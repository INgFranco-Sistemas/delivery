<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected function authorizeAdmin()
    {
        $user = auth('api')->user();
        if (!$user || $user->role !== 'admin') {
            abort(403, 'No autorizado');
        }
    }

    // Lista de tortas (visible para todos los logueados: cliente/admin)
    public function index()
    {
        $products = Product::where('is_active', true)
            ->with('category')
            ->orderBy('name')
            ->get();

        return response()->json($products);
    }


    // Ver una torta específica
    public function show(Product $product)
    {
        $product->load('category');

        return response()->json($product);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'is_active'   => 'boolean',
            'image'       => 'nullable|image|max:2048', // 2MB
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $data['image_path'] = $path;
        }

        $product = Product::create($data);

        return response()->json($product->load('category'), 201);
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name'        => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'sometimes|required|numeric|min:0',
            'stock'       => 'sometimes|required|integer|min:0',
            'category_id' => 'sometimes|required|exists:categories,id',
            'is_active'   => 'boolean',
            'image'       => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // opcional: borrar imagen anterior
            // if ($product->image_path) Storage::disk('public')->delete($product->image_path);

            $path = $request->file('image')->store('products', 'public');
            $data['image_path'] = $path;
        }

        $product->update($data);

        return response()->json($product->fresh()->load('category'));
    }

    // Eliminar torta (admin)
    public function destroy(Product $product)
    {
        $this->authorizeAdmin();

        $product->delete();

        return response()->json(['message' => 'Producto eliminado']);
    }
}
