<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWorkerRequest;
use App\Models\Category;
use App\Models\Worker;
use App\Models\WorkerPhoto;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class WorkerController extends Controller
{
    public function index()
    {
        $query = Worker::with('categories')->latest();

        if (request('estado') === 'pendiente') {
            $query->where('is_active', false);
        }

        $workers = $query->paginate(20);
        return view('admin.workers.index', compact('workers'));
    }

    public function create()
    {
        $categories = Category::ordered()->get();
        return view('admin.workers.create', compact('categories'));
    }

    public function store(StoreWorkerRequest $request)
    {
        $data              = $request->validated();
        $data['slug']      = $this->uniqueSlug($request->name);
        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('workers/photos');
        }

        unset($data['photo'], $data['category_ids']);
        $worker = Worker::create($data);

        $this->syncCategories($worker, $request->input('category_ids', []));
        $this->syncWorkPhotos($request, $worker);

        return redirect()->route('admin.workers.index')->with('success', 'Trabajador creado correctamente.');
    }

    public function edit(Worker $worker)
    {
        $worker->load('categories');
        $categories = Category::ordered()->get();
        return view('admin.workers.edit', compact('worker', 'categories'));
    }

    public function update(StoreWorkerRequest $request, Worker $worker)
    {
        $data              = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('photo')) {
            if ($worker->photo_path) {
                Storage::delete($worker->photo_path);
            }
            $data['photo_path'] = $request->file('photo')->store('workers/photos');
        }

        unset($data['photo'], $data['category_ids']);
        $worker->update($data);

        $this->syncCategories($worker, $request->input('category_ids', []));

        // Eliminar fotos marcadas para borrar
        $deleteIds = array_filter(explode(',', request('delete_photos', '')));
        if ($deleteIds) {
            $toDelete = WorkerPhoto::whereIn('id', $deleteIds)->where('worker_id', $worker->id)->get();
            foreach ($toDelete as $photo) {
                Storage::delete($photo->path);
                $photo->delete();
            }
        }

        $this->syncWorkPhotos(request(), $worker);

        return redirect()->route('admin.workers.index')->with('success', 'Trabajador actualizado correctamente.');
    }

    public function approve(Worker $worker)
    {
        $worker->update(['is_active' => true]);
        return back()->with('success', "\"$worker->name\" aprobado y visible en el directorio.");
    }

    public function destroy(Worker $worker)
    {
        if ($worker->photo_path) {
            Storage::delete($worker->photo_path);
        }
        $worker->delete();

        return back()->with('success', 'Trabajador eliminado.');
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function syncCategories(Worker $worker, array $categoryIds): void
    {
        if (empty($categoryIds)) return;

        // Primer ID = categoría primaria
        $pivot = [];
        foreach ($categoryIds as $i => $id) {
            $pivot[(int)$id] = ['is_primary' => ($i === 0)];
        }
        $worker->categories()->sync($pivot);
    }

    private function syncWorkPhotos($request, Worker $worker): void
    {
        $existing = $worker->photos()->count();
        $slots    = 2 - $existing;
        if ($slots <= 0 || !$request->hasFile('work_photos')) return;

        foreach (array_slice($request->file('work_photos'), 0, $slots) as $i => $file) {
            $path = $file->store('workers/work-photos');
            WorkerPhoto::create([
                'worker_id' => $worker->id,
                'path'      => $path,
                'order'     => $existing + $i,
            ]);
        }
    }

    private function uniqueSlug(string $name): string
    {
        $slug  = Str::slug($name);
        $count = Worker::where('slug', 'like', $slug . '%')->count();
        return $count ? $slug . '-' . ($count + 1) : $slug;
    }
}
