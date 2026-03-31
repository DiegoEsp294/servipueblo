<?php

namespace App\Http\Controllers;

use App\Mail\WorkerContactNotification;
use App\Models\Category;
use App\Models\Worker;
use App\Models\WorkerEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class WorkerController extends Controller
{
    public function index(Request $request)
    {
        $tipo = $request->input('tipo'); // 'worker', 'entrepreneur', o null = todos

        $categoriesQuery = Category::ordered();
        if ($tipo === 'worker') {
            $categoriesQuery->whereIn('for_type', ['worker', 'all']);
        } elseif ($tipo === 'entrepreneur') {
            $categoriesQuery->whereIn('for_type', ['entrepreneur', 'all']);
        }
        $categories = $categoriesQuery->withCount(['workers as workers_count' => function ($q) use ($tipo) {
            $q->active()->when($tipo, fn($q2) => $q2->where('type', $tipo));
        }])->get();

        $townsCacheKey = 'towns_list_' . ($tipo ?? 'all');
        $towns = Cache::remember($townsCacheKey, 300, fn() =>
            Worker::active()
                ->when($tipo, fn($q) => $q->where('type', $tipo))
                ->distinct()->orderBy('town')->pluck('town')
        );

        // Pueblo desde GET, cookie, o vacío
        $pueblo = $request->filled('pueblo')
            ? $request->pueblo
            : ($request->hasCookie('sp_pueblo') ? $request->cookie('sp_pueblo') : '');

        $clearCookie = $request->has('limpiar');
        if ($clearCookie) $pueblo = '';

        $query = Worker::active()->with('categories', 'tags')->orderByDesc('average_rating');

        if ($tipo) {
            $query->where('type', $tipo);
        }
        if ($request->filled('nombre')) {
            $query->where('name', 'ilike', '%' . $request->nombre . '%');
        }
        if ($request->filled('categoria')) {
            $query->whereHas('categories', fn($q) => $q->where('slug', $request->categoria));
        }
        if ($pueblo) {
            $query->where('town', 'ilike', '%' . $pueblo . '%');
        }

        $workers = $query->paginate(12)->withQueryString();

        $response = response()->view('workers.index', compact('workers', 'categories', 'towns', 'pueblo', 'tipo'));

        if ($clearCookie) {
            $response->withCookie(cookie()->forget('sp_pueblo'));
        } elseif ($pueblo) {
            $response->withCookie(cookie('sp_pueblo', $pueblo, 60 * 24 * 30));
        }

        return $response;
    }

    public function show(Worker $worker, Request $request)
    {
        abort_unless($worker->is_active, 404);

        // Redirigir si la URL no corresponde al tipo
        $isEntrepreneurRoute = $request->routeIs('entrepreneurs.*');
        if ($isEntrepreneurRoute && !$worker->is_entrepreneur) {
            return redirect($worker->profile_url, 301);
        }
        if (!$isEntrepreneurRoute && $worker->is_entrepreneur) {
            return redirect($worker->profile_url, 301);
        }

        $worker->load([
            'categories',
            'tags',
            'ratings'       => fn($q) => $q->latest()->limit(10),
            'photos',
            'businessHours',
            'posts'         => fn($q) => $q->active()->latest()->limit(5),
        ]);

        WorkerEvent::record($worker, WorkerEvent::TYPE_VIEW);

        return view('workers.show', compact('worker'));
    }

    public function trackWhatsapp(Worker $worker)
    {
        abort_unless($worker->is_active, 404);
        WorkerEvent::record($worker, WorkerEvent::TYPE_WHATSAPP, 10);

        // Notificar al trabajador (máx 1 email por hora por trabajador)
        if ($worker->email) {
            $cacheKey = 'worker_contact_notif_' . $worker->id;
            if (!Cache::has($cacheKey)) {
                Cache::put($cacheKey, true, now()->addHour());
                try {
                    Mail::to($worker->email)->send(new WorkerContactNotification($worker));
                } catch (\Exception $e) {
                    // No interrumpir el redirect si falla el mail
                }
            }
        }

        return redirect($worker->whatsapp_url);
    }

    public function trackShare(Worker $worker)
    {
        abort_unless($worker->is_active, 404);
        WorkerEvent::record($worker, WorkerEvent::TYPE_SHARE, 30);
        return response()->json(['ok' => true]);
    }
}
