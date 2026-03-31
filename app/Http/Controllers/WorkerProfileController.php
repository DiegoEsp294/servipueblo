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
        $this->middleware('auth');
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
}
