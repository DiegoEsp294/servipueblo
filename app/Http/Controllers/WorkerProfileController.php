<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\WorkerPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WorkerProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function edit()
    {
        $user = auth()->user();

        if (!$user->isWorker() || !$user->worker) {
            abort(403);
        }

        $worker = $user->worker->load('categories', 'tags', 'photos');
        $tagsGrouped = Tag::allGrouped();

        return view('worker-portal.edit', compact('worker', 'tagsGrouped'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        if (!$user->isWorker() || !$user->worker) {
            abort(403);
        }

        $worker = $user->worker;

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

        // Foto de perfil
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

        return back()->with('success', '¡Perfil actualizado correctamente!');
    }
}
