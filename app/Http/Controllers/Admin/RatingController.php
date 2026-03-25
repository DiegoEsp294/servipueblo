<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rating;

class RatingController extends Controller
{
    public function index()
    {
        $ratings = Rating::with('worker')->latest()->paginate(30);
        return view('admin.ratings.index', compact('ratings'));
    }

    public function destroy(Rating $rating)
    {
        $rating->delete(); // El modelo dispara el evento que recalcula el promedio
        return back()->with('success', 'Calificación eliminada.');
    }
}
