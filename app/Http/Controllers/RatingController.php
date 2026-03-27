<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRatingRequest;
use App\Models\Worker;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function store(StoreRatingRequest $request, Worker $worker)
    {
        abort_unless($worker->is_active, 404);

        $cookieKey = 'rated_worker_' . $worker->id;

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
            // Anónimo: verificar por cookie Y por IP
            if ($request->cookie($cookieKey)) {
                return back()->with('error', 'Ya calificaste a este trabajador desde este dispositivo.');
            }
            if ($worker->ratings()->whereNotNull('ip_address')->where('ip_address', $request->ip())->exists()) {
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

        // Cookie que dura 1 año
        return back()
            ->with('success', '¡Gracias por tu calificación!')
            ->withCookie(cookie($cookieKey, '1', 60 * 24 * 365));
    }
}
