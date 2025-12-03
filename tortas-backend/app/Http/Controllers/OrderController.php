<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    // 🔐 Solo admin
    protected function authorizeAdmin()
    {
        $user = auth('api')->user();
        if (!$user || $user->role !== 'admin') {
            abort(403, 'No autorizado');
        }
    }

    // 👤 Usuario autenticado
    protected function user()
    {
        return auth('api')->user();
    }

    // 🟢 CLIENTE: crear pedido con items
    public function store(Request $request)
    {
        $user = $this->user();

        $data = $request->validate([
            'delivery_address'       => 'required|string|max:255',
            'delivery_phone'         => 'required|string|max:20',
            'notes'                  => 'nullable|string',
            'items'                  => 'required|array|min:1',
            'items.*.product_id'     => 'required|exists:products,id',
            'items.*.quantity'       => 'required|integer|min:1',
        ]);

        // Usamos transacción para que todo se grabe correctamente o nada
        $order = DB::transaction(function () use ($data, $user) {
            $order = Order::create([
                'user_id'          => $user->id,
                'status'           => 'pending',
                'total_amount'     => 0, // se calcula luego
                'delivery_address' => $data['delivery_address'],
                'delivery_phone'   => $data['delivery_phone'],
                'notes'            => $data['notes'] ?? null,
            ]);

            $total = 0;

            // Traemos todos los productos involucrados
            $products = Product::whereIn('id', collect($data['items'])->pluck('product_id'))->get();

            foreach ($data['items'] as $itemData) {
                $product = $products->firstWhere('id', $itemData['product_id']);

                if (!$product) {
                    abort(400, 'Producto no encontrado.');
                }

                // Validar stock (simple)
                if ($product->stock < $itemData['quantity']) {
                    abort(400, "Stock insuficiente para el producto {$product->name}");
                }

                $unitPrice = $product->price;
                $quantity  = $itemData['quantity'];
                $subtotal  = $unitPrice * $quantity;

                // Crear detalle
                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $product->id,
                    'quantity'   => $quantity,
                    'unit_price' => $unitPrice,
                    'subtotal'   => $subtotal,
                ]);

                // Descontar stock
                $product->decrement('stock', $quantity);

                $total += $subtotal;
            }

            // Actualizar total del pedido
            $order->update([
                'total_amount' => $total,
            ]);

            return $order;
        });

        // Cargamos relaciones para responder bonito
        $order->load(['items.product', 'user']);

        return response()->json([
            'message' => 'Pedido creado correctamente',
            'order'   => $order,
        ], 201);
    }

    // 🟢 CLIENTE: ver SOLO sus pedidos
    public function myOrders()
    {
        $user = $this->user();

        $orders = Order::with(['items.product'])
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->get();

        return response()->json($orders);
    }

    // 🟡 ADMIN: ver TODOS los pedidos
    public function index()
    {
        $this->authorizeAdmin();

        $orders = Order::with(['user', 'items.product'])
            ->orderByDesc('created_at')
            ->get();

        return response()->json($orders);
    }

    // 👁 Ver un pedido en detalle (admin o dueño)
    public function show(Order $order)
    {
        $user = $this->user();

        if ($user->role !== 'admin' && $order->user_id !== $user->id) {
            abort(403, 'No autorizado');
        }

        $order->load(['user', 'items.product']);

        return response()->json($order);
    }

    // 🟡 ADMIN: cambiar estado del pedido
    public function updateStatus(Request $request, Order $order)
    {
        $this->authorizeAdmin();

        $data = $request->validate([
            'status' => 'required|in:pending,confirmed,in_delivery,delivered,cancelled',
        ]);

        $order->update([
            'status' => $data['status'],
        ]);

        return response()->json([
            'message' => 'Estado actualizado correctamente',
            'order'   => $order,
        ]);
    }
}
