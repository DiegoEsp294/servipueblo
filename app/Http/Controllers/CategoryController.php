<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Worker;

class CategoryController extends Controller
{
    public function show(Category $category)
    {
        $workers = Worker::active()
            ->whereHas('categories', fn($q) => $q->where('categories.id', $category->id))
            ->with('categories')
            ->orderByDesc('average_rating')
            ->paginate(12);

        $categories = Category::ordered()->get();

        return view('categories.show', compact('category', 'workers', 'categories'));
    }
}
