<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRatingRequest;
use App\Models\Worker;

class RatingController extends Controller
{
    public function store(StoreRatingRequest $request, Worker $worker)
    {
        abort_unless($worker->is_active, 404);

        if (auth()->check()) {
            // Usuario logueado: una calificación por cuenta
            if ($worker->ratings()->where('user_id', auth()->id())->exists()) {
                return back()->with('error', 'Ya calificaste a este trabajador.');
            }
            $worker->ratings()->create([
                'user_id'       => auth()->id(),
                'score'         => $request->score,
                'comment'       => $request->comment,
                'reviewer_name' => $request->reviewer_name ?: auth()->user()->name,
                'ip_address'    => $request->ip(),
            ]);
        } else {
            // Anónimo: una calificación por IP
            if ($worker->ratings()->where('ip_address', $request->ip())->exists()) {
                return back()->with('error', 'Ya calificaste a este trabajador desde este dispositivo.');
            }
            $worker->ratings()->create([
                'score'         => $request->score,
                'comment'       => $request->comment,
                'reviewer_name' => $request->reviewer_name ?: 'Anónimo',
                'ip_address'    => $request->ip(),
            ]);
        }

        $worker->recalculateRating();

        return back()->with('success', '¡Gracias por tu calificación!');
    }
}
