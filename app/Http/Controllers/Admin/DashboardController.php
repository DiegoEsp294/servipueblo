<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rating;
use App\Models\Worker;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_workers'  => Worker::count(),
            'active_workers' => Worker::where('is_active', true)->count(),
            'total_ratings'  => Rating::count(),
        ];

        $latestWorkers = Worker::with('categories')->latest()->limit(5)->get();

        return view('admin.dashboard', compact('stats', 'latestWorkers'));
    }
}
