<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // Solo admin
    protected function authorizeAdmin()
    {
        $user = auth('api')->user();
        if (!$user || $user->role !== 'admin') {
            abort(403, 'No autorizado');
        }
    }

    // Listar todas las categorías (visible para todos los logueados)
    public function index()
    {
        $categories = Category::where('is_active', true)->get();
        return response()->json($categories);
    }

    // Crear categoría (admin)
    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active'   => 'boolean',
        ]);

        $category = Category::create($data);

        return response()->json($category, 201);
    }

    // Ver una categoría
    public function show(Category $category)
    {
        return response()->json($category);
    }

    // Actualizar categoría (admin)
    public function update(Request $request, Category $category)
    {
        $this->authorizeAdmin();

        $data = $request->validate([
            'name'        => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'is_active'   => 'boolean',
        ]);

        $category->update($data);

        return response()->json($category);
    }

    // Eliminar categoría (admin)
    public function destroy(Category $category)
    {
        $this->authorizeAdmin();

        $category->delete();

        return response()->json(['message' => 'Categoría eliminada']);
    }
}
