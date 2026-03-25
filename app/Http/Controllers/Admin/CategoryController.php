<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::ordered()->withCount('workers')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:80', 'unique:categories,name'],
            'icon' => ['nullable', 'string', 'max:60'],
        ]);

        Category::create([
            'name'       => $request->name,
            'slug'       => Str::slug($request->name),
            'icon'       => $request->icon,
            'sort_order' => Category::max('sort_order') + 1,
        ]);

        return back()->with('success', 'Categoría creada correctamente.');
    }

    public function destroy(Category $category)
    {
        if ($category->workers()->exists()) {
            return back()->with('error', 'No se puede eliminar una categoría con trabajadores asignados.');
        }
        $category->delete();
        return back()->with('success', 'Categoría eliminada.');
    }
}
