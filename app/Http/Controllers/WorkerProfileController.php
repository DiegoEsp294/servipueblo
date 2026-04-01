<?php

namespace App\Http\Controllers;

use App\Models\BusinessHour;
use App\Models\Tag;
use App\Models\WorkerPhoto;
use App\Models\WorkerPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WorkerProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['showPost', 'postShareImage']);
    }

    private function workerOrAbort()
    {
        $user = auth()->user();
        if (!$user->isWorker() || !$user->worker) abort(403);
        return $user->worker;
    }

    // ── Perfil ────────────────────────────────────────────────────────────────

    public function edit()
    {
        $worker = $this->workerOrAbort()->load('categories', 'tags', 'photos', 'businessHours');
        $tagsGrouped = Tag::allGrouped();
        $days        = BusinessHour::$days;

        // Indexar horarios por día para facilitar el acceso en la vista
        $hoursByDay = $worker->businessHours->keyBy('day_of_week');

        return view('worker-portal.edit', compact('worker', 'tagsGrouped', 'days', 'hoursByDay'));
    }

    public function update(Request $request)
    {
        $worker = $this->workerOrAbort();

        $data = $request->validate([
            'description'      => ['nullable', 'string', 'max:500'],
            'phone'            => ['required', 'string', 'max:20'],
            'email'            => ['nullable', 'email', 'max:150', 'unique:users,email,' . auth()->id()],
            'rate_info'        => ['nullable', 'string', 'max:100'],
            'years_experience' => ['nullable', 'integer', 'min:1', 'max:60'],
            'availability'     => ['nullable', 'in:available,on_request,unavailable'],
            'photo'            => ['nullable', 'image', 'max:10240'],
            'tags'             => ['nullable', 'array', 'max:6'],
            'tags.*'           => ['integer', 'exists:tags,id'],
        ]);

        if ($request->hasFile('photo')) {
            try {
                if ($worker->photo_path) Storage::delete($worker->photo_path);
                $data['photo_path'] = $request->file('photo')->store('workers/photos');
            } catch (\Throwable $e) {
                return back()->withErrors(['photo' => 'No se pudo subir la foto: ' . $e->getMessage()]);
            }
        }

        $tagIds = $data['tags'] ?? [];
        unset($data['tags']);

        $worker->update($data);
        $worker->tags()->sync($tagIds);

        // Fotos de trabajos — eliminar
        $deleteIds = array_filter(explode(',', $request->input('delete_photos', '')));
        if ($deleteIds) {
            $toDelete = WorkerPhoto::whereIn('id', $deleteIds)->where('worker_id', $worker->id)->get();
            foreach ($toDelete as $photo) {
                try { Storage::delete($photo->path); } catch (\Throwable $e) {}
                $photo->delete();
            }
        }

        // Fotos de trabajos — agregar
        $existing = $worker->photos()->count();
        $slots    = 5 - $existing;
        if ($slots > 0 && $request->hasFile('work_photos')) {
            foreach (array_slice($request->file('work_photos'), 0, $slots) as $i => $file) {
                try {
                    $path = $file->store('workers/work-photos');
                    WorkerPhoto::create(['worker_id' => $worker->id, 'path' => $path, 'order' => $existing + $i]);
                } catch (\Throwable $e) {}
            }
        }

        // Actualizar email del usuario si cambió
        if ($request->filled('email') && $request->email !== auth()->user()->email) {
            auth()->user()->update(['email' => $request->email]);
        }

        // Guardar horarios
        $hours = $request->input('hours', []);
        foreach (BusinessHour::$days as $day => $name) {
            $isClosed = isset($hours[$day]['closed']);
            BusinessHour::updateOrCreate(
                ['worker_id' => $worker->id, 'day_of_week' => $day],
                [
                    'is_closed'  => $isClosed,
                    'open_time'  => $isClosed ? null : ($hours[$day]['open']  ?? null),
                    'close_time' => $isClosed ? null : ($hours[$day]['close'] ?? null),
                ]
            );
        }

        return back()->with('success', '¡Perfil actualizado correctamente!');
    }

    // ── Novedades (muro) ──────────────────────────────────────────────────────

    public function posts()
    {
        $worker = $this->workerOrAbort();
        $posts  = WorkerPost::where('worker_id', $worker->id)->latest()->paginate(20);

        return view('worker-portal.posts', compact('worker', 'posts'));
    }

    public function storePost(Request $request)
    {
        $worker = $this->workerOrAbort();

        $data = $request->validate([
            'content'  => ['required', 'string', 'max:500'],
            'photo'    => ['nullable', 'image', 'max:5120'],
            'duration' => ['nullable', 'in:today,3days,7days,none'],
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            try {
                $photoPath = $request->file('photo')->store('workers/posts');
            } catch (\Throwable $e) {
                return back()->withErrors(['photo' => 'No se pudo subir la foto.']);
            }
        }

        $duration  = $data['duration'] ?? '7days';
        if ($duration === 'today')      $expiresAt = now()->endOfDay();
        elseif ($duration === '3days')  $expiresAt = now()->addDays(3);
        elseif ($duration === 'none')   $expiresAt = null;
        else                            $expiresAt = now()->addDays(7);

        WorkerPost::create([
            'worker_id'  => $worker->id,
            'content'    => $data['content'],
            'photo_path' => $photoPath,
            'expires_at' => $expiresAt,
        ]);

        return back()->with('success', '¡Novedad publicada!');
    }

    public function toggleSoldOut(WorkerPost $post)
    {
        $worker = $this->workerOrAbort();
        if ($post->worker_id !== $worker->id) abort(403);

        $post->update(['is_sold_out' => !$post->is_sold_out]);

        return back();
    }

    public function destroyPost(WorkerPost $post)
    {
        $worker = $this->workerOrAbort();
        if ($post->worker_id !== $worker->id) abort(403);

        if ($post->photo_path) {
            try { Storage::delete($post->photo_path); } catch (\Throwable $e) {}
        }
        $post->delete();

        return back()->with('success', 'Novedad eliminada.');
    }

    // ── Página pública de novedad (para compartir) ────────────────────────────

    public function showPost(\App\Models\Worker $worker, WorkerPost $post)
    {
        abort_if($post->worker_id !== $worker->id, 404);
        $worker->load(['categories']);
        return view('workers.post-show', compact('worker', 'post'));
    }

    public function postShareImage(\App\Models\Worker $worker, WorkerPost $post)
    {
        abort_if($post->worker_id !== $worker->id, 404);
        $worker->load(['categories']);

        $w = 1080; $h = 1080;
        $img = imagecreatetruecolor($w, $h);

        $cWhite     = imagecolorallocate($img, 255, 255, 255);
        $cGreen     = imagecolorallocate($img, 22, 163, 74);
        $cDark      = imagecolorallocate($img, 17, 24, 39);
        $cGray      = imagecolorallocate($img, 107, 114, 128);
        $cLightGray = imagecolorallocate($img, 229, 231, 235);

        imagefill($img, 0, 0, $cWhite);
        imagefilledrectangle($img, 0, 0, $w, 130, $cGreen);
        imagefilledrectangle($img, 0, $h - 90, $w, $h, $cGreen);

        $fontBold = null; $fontRegular = null;
        foreach ([
            '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf',
            '/usr/share/fonts/truetype/liberation/LiberationSans-Bold.ttf',
            '/usr/share/fonts/truetype/freefont/FreeSansBold.ttf',
        ] as $f) { if (file_exists($f)) { $fontBold = $f; break; } }
        foreach ([
            '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf',
            '/usr/share/fonts/truetype/liberation/LiberationSans-Regular.ttf',
            '/usr/share/fonts/truetype/freefont/FreeSans.ttf',
        ] as $f) { if (file_exists($f)) { $fontRegular = $f; break; } }

        $fb = $fontBold ?? $fontRegular;
        $fr = $fontRegular ?? $fontBold;

        if ($fb) {
            imagettftext($img, 44, 0, 50, 88, $cWhite, $fb, 'ServiPueblo');
            imagettftext($img, 46, 0, 60, 220, $cDark, $fb, $worker->name);
            if ($fr) {
                $cats = $worker->categories->pluck('name')->join(' · ');
                imagettftext($img, 28, 0, 62, 270, $cGray, $fr, $cats);
            }
            imagefilledrectangle($img, 60, 298, $w - 60, 301, $cLightGray);
            if ($fr) $this->gdDrawWrapped($img, $fr, 32, $cDark, $post->content, 60, 350, $w - 120, 48);
            if ($fr) imagettftext($img, 26, 0, 50, $h - 28, $cWhite, $fr, 'servipueblo.onrender.com');
        } else {
            imagestring($img, 5, 50, 50, 'ServiPueblo', $cWhite);
            imagestring($img, 5, 60, 180, $worker->name, $cDark);
            $lines = explode("\n", wordwrap($post->content, 55, "\n", true));
            $y = 270;
            foreach (array_slice($lines, 0, 15) as $line) {
                imagestring($img, 4, 60, $y, $line, $cDark); $y += 26;
            }
            imagestring($img, 3, 50, $h - 50, 'servipueblo.onrender.com', $cWhite);
        }

        if ($post->photo_path) {
            try {
                $data = @file_get_contents($post->photo_url);
                if ($data) {
                    $src = @imagecreatefromstring($data);
                    if ($src) {
                        $sw = imagesx($src); $sh = imagesy($src);
                        $side = min($sw, $sh);
                        $destSize = 400;
                        $destX = ($w - $destSize) / 2;
                        $destY = $h - 90 - $destSize - 30;
                        imagecopyresampled($img, $src, $destX, $destY, ($sw - $side) / 2, ($sh - $side) / 2, $destSize, $destSize, $side, $side);
                        imagedestroy($src);
                    }
                }
            } catch (\Throwable $e) {}
        }

        ob_start();
        imagepng($img);
        $png = ob_get_clean();
        imagedestroy($img);

        return response($png, 200)
            ->header('Content-Type', 'image/png')
            ->header('Cache-Control', 'public, max-age=3600');
    }

    private function gdDrawWrapped($img, $font, $size, $color, $text, $x, $y, $maxWidth, $lineH): void
    {
        $words = preg_split('/\s+/u', $text);
        $line  = '';
        $curY  = $y;
        $count = 0;

        foreach ($words as $word) {
            $test = $line ? "$line $word" : $word;
            $bbox = imagettfbbox($size, 0, $font, $test);
            if (($bbox[2] - $bbox[0]) > $maxWidth && $line !== '') {
                imagettftext($img, $size, 0, $x, $curY, $color, $font, $line);
                $line = $word; $curY += $lineH; $count++;
                if ($count >= 12) { $line = ''; break; }
            } else { $line = $test; }
        }
        if ($line) imagettftext($img, $size, 0, $x, $curY, $color, $font, $line);
    }
}
