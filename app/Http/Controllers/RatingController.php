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

        $userId = auth()->id();

        // Una sola calificación por usuario por trabajador
        $existing = $worker->ratings()->where('user_id', $userId)->first();
        if ($existing) {
            return back()->with('error', 'Ya calificaste a este trabajador.');
        }

        $worker->ratings()->create([
            'user_id'       => $userId,
            'score'         => $request->score,
            'comment'       => $request->comment,
            'reviewer_name' => $request->reviewer_name ?: auth()->user()->name,
            'ip_address'    => $request->ip(),
        ]);

        // Forzar recálculo (respaldo por si el model event falla)
        $worker->recalculateRating();

        return back()->with('success', '¡Gracias por tu calificación!');
    }
}
