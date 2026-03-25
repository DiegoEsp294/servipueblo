<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Worker;
use App\Models\WorkerEvent;
use Illuminate\Http\Request;

class WorkerController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::ordered()->get()->each(function ($cat) {
            $cat->workers_count = $cat->workers()->active()->count();
        });

        $towns = Worker::active()->distinct()->orderBy('town')->pluck('town');

        // Pueblo desde GET, cookie, o vacío
        $pueblo = $request->filled('pueblo')
            ? $request->pueblo
            : ($request->hasCookie('sp_pueblo') ? $request->cookie('sp_pueblo') : '');

        // "Limpiar" limpia también la cookie
        $clearCookie = $request->has('limpiar');
        if ($clearCookie) {
            $pueblo = '';
        }

        $query = Worker::active()->with('categories')->orderByDesc('average_rating');

        if ($request->filled('categoria')) {
            $query->whereHas('categories', fn($q) => $q->where('slug', $request->categoria));
        }

        if ($pueblo) {
            $query->where('town', 'ilike', '%' . $pueblo . '%');
        }

        $workers = $query->paginate(12)->withQueryString();

        $response = response()->view('workers.index', compact('workers', 'categories', 'towns', 'pueblo'));

        if ($clearCookie) {
            $response->withCookie(cookie()->forget('sp_pueblo'));
        } elseif ($pueblo) {
            $response->withCookie(cookie('sp_pueblo', $pueblo, 60 * 24 * 30)); // 30 días
        }

        return $response;
    }

    public function show(Worker $worker)
    {
        abort_unless($worker->is_active, 404);

        $worker->load(['categories', 'ratings' => fn($q) => $q->latest()->limit(10), 'photos']);

        WorkerEvent::record($worker, WorkerEvent::TYPE_VIEW);

        return view('workers.show', compact('worker'));
    }

    public function trackWhatsapp(Worker $worker)
    {
        abort_unless($worker->is_active, 404);
        WorkerEvent::record($worker, WorkerEvent::TYPE_WHATSAPP, 10);
        return redirect($worker->whatsapp_url);
    }

    public function trackShare(Worker $worker)
    {
        abort_unless($worker->is_active, 404);
        WorkerEvent::record($worker, WorkerEvent::TYPE_SHARE, 30);
        return response()->json(['ok' => true]);
    }
}
