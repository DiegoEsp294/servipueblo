<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRatingRequest;
use App\Models\Worker;

class RatingController extends Controller
{
    public function store(StoreRatingRequest $request, Worker $worker)
    {
        abort_unless($worker->is_active, 404);

        if (!auth()->check()) {
            return redirect()->route('auth.google', [
                'redirect' => route('workers.show', $worker->slug),
            ])->with('error', 'Necesitás iniciar sesión para calificar.');
        }

        $worker->ratings()->create([
            'score'         => $request->score,
            'comment'       => $request->comment,
            'reviewer_name' => $request->reviewer_name ?: auth()->user()->name,
            'ip_address'    => $request->ip(),
        ]);

        return back()->with('success', '¡Gracias por tu calificación!');
    }
}
