<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MetricsController extends Controller
{
    public function index()
    {
        $workers = \App\Models\Worker::with('category')
            ->withCount([
                'events as views_total'     => fn($q) => $q->where('type', 'view'),
                'events as views_month'     => fn($q) => $q->where('type', 'view')->where('created_at', '>=', now()->startOfMonth()),
                'events as whatsapp_total'  => fn($q) => $q->where('type', 'whatsapp_click'),
                'events as whatsapp_month'  => fn($q) => $q->where('type', 'whatsapp_click')->where('created_at', '>=', now()->startOfMonth()),
                'events as shares_total'    => fn($q) => $q->where('type', 'share_click'),
            ])
            ->where('is_active', true)
            ->orderByDesc('views_month')
            ->get();

        // Totales globales del mes
        $totals = [
            'views'    => \App\Models\WorkerEvent::where('type', 'view')->where('created_at', '>=', now()->startOfMonth())->count(),
            'whatsapp' => \App\Models\WorkerEvent::where('type', 'whatsapp_click')->where('created_at', '>=', now()->startOfMonth())->count(),
            'shares'   => \App\Models\WorkerEvent::where('type', 'share_click')->where('created_at', '>=', now()->startOfMonth())->count(),
        ];

        // Actividad diaria últimos 30 días (para el gráfico)
        $daily = \App\Models\WorkerEvent::selectRaw("DATE(created_at) as day, type, COUNT(*) as total")
            ->where('created_at', '>=', now()->subDays(29)->startOfDay())
            ->groupBy('day', 'type')
            ->orderBy('day')
            ->get()
            ->groupBy('day');

        return view('admin.metrics.index', compact('workers', 'totals', 'daily'));
    }

    public function worker(\App\Models\Worker $worker)
    {
        $daily = \App\Models\WorkerEvent::selectRaw("DATE(created_at) as day, type, COUNT(*) as total")
            ->where('worker_id', $worker->id)
            ->where('created_at', '>=', now()->subDays(29)->startOfDay())
            ->groupBy('day', 'type')
            ->orderBy('day')
            ->get()
            ->groupBy('day');

        $totals = [
            'views'    => $worker->events()->where('type', 'view')->count(),
            'whatsapp' => $worker->events()->where('type', 'whatsapp_click')->count(),
            'shares'   => $worker->events()->where('type', 'share_click')->count(),
            'views_month'    => $worker->events()->where('type', 'view')->where('created_at', '>=', now()->startOfMonth())->count(),
            'whatsapp_month' => $worker->events()->where('type', 'whatsapp_click')->where('created_at', '>=', now()->startOfMonth())->count(),
        ];

        return view('admin.metrics.worker', compact('worker', 'daily', 'totals'));
    }
}
