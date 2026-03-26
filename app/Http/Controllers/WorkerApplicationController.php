<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WorkerApplicationController extends Controller
{
    public function create()
    {
        $categories = \App\Models\Category::ordered()->get();
        return view('workers.apply', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type'        => 'nullable|in:worker,entrepreneur',
            'name'        => 'required|string|max:100',
            'category_id' => 'required|exists:categories,id',
            'phone'       => 'required|string|max:20',
            'email'       => 'nullable|email|max:150',
            'town'        => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
        ]);

        $slug = \Illuminate\Support\Str::slug($data['name']);
        $count = \App\Models\Worker::where('slug', 'like', $slug . '%')->count();
        $data['slug']      = $count ? $slug . '-' . ($count + 1) : $slug;
        $data['is_active'] = false;

        \App\Models\Worker::create($data);

        return redirect()->route('workers.apply')->with('success',
            '¡Gracias! Tu solicitud fue enviada. El equipo de ServiPueblo la revisará y te contactará pronto.'
        );
    }
}
