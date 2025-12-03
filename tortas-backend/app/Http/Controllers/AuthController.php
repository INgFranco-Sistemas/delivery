<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // Registrar un nuevo cliente
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed', // password_confirmation
            'phone'    => 'nullable|string|max:20',
            'address'  => 'nullable|string|max:255',
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'phone'    => $data['phone'] ?? null,
            'address'  => $data['address'] ?? null,
            'role'     => 'cliente', // por defecto todos son clientes
        ]);

        $token = auth('api')->login($user);

        return response()->json([
            'message' => 'Registro exitoso',
            'user'    => $user,
            'token'   => $token,
        ], 201);
    }

    // Login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (!$token = auth('api')->attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['Credenciales inválidas.'],
            ]);
        }

        return response()->json([
            'message' => 'Login exitoso',
            'user'    => auth('api')->user(),
            'token'   => $token,
        ]);
    }

    // Datos del usuario autenticado
    public function me()
    {
        return response()->json(auth('api')->user());
    }

    // Cerrar sesión (invalidar token)
    public function logout()
    {
        auth('api')->logout();

        return response()->json(['message' => 'Sesión cerrada correctamente']);
    }

    // Refrescar token
    public function refresh()
    {
        return response()->json([
            'token' => auth('api')->refresh(),
        ]);
    }
}
