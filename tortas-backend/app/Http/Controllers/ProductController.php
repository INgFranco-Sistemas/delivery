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
        $products = Product::with('category')
            ->where('is_active', true)
            ->get();

        return response()->json($products);
    }

    // Ver una torta específica
    public function show(Product $product)
    {
        $product->load('category');

        return response()->json($product);
    }

    // Crear nueva torta (admin)
    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'is_active'   => 'boolean',
            'image_path'  => 'nullable|string', // luego la cambiaremos a upload real
        ]);

        $product = Product::create($data);

        return response()->json($product, 201);
    }

    // Actualizar torta (admin)
    public function update(Request $request, Product $product)
    {
        $this->authorizeAdmin();

        $data = $request->validate([
            'category_id' => 'sometimes|exists:categories,id',
            'name'        => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'sometimes|numeric|min:0',
            'stock'       => 'sometimes|integer|min:0',
            'is_active'   => 'boolean',
            'image_path'  => 'nullable|string',
        ]);

        $product->update($data);

        return response()->json($product);
    }

    // Eliminar torta (admin)
    public function destroy(Product $product)
    {
        $this->authorizeAdmin();

        $product->delete();

        return response()->json(['message' => 'Producto eliminado']);
    }
}
