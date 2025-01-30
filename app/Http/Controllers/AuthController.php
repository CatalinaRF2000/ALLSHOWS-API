<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Calendario;  // Asegúrate de importar el modelo Calendario
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // Registro de usuario
    public function register(Request $request)
{
    // Validación de los datos enviados en el registro
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email',
        'password' => 'required|string|min:8|confirmed',
        // El rol es opcional, pero no permite 'admin' por defecto
        'role' => 'nullable|in:cliente,artista,socorrista,animador', // Roles permitidos
    ]);

    // Si hay errores en la validación
    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    // Determinar el rol: 'cliente' por defecto, o el rol proporcionado por el administrador
    $role = $request->role ?? 'cliente';  // 'cliente' es el rol predeterminado

    // Si el usuario autenticado es admin, permitir roles personalizados
    if ($request->user() && $request->user()->role === 'admin') {
        // Si es admin, el rol asignado será el proporcionado o 'cliente' si no hay uno
        $role = $request->role ?? 'cliente';
    } elseif ($role !== 'cliente') {
        // Si no es admin, impedir asignar un rol distinto de 'cliente'
        return response()->json(['message' => 'No tienes permiso para asignar roles'], 403);
    }

    // Crear un nuevo usuario con los datos validados
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => $role,  // Asignamos el rol especificado o 'cliente' por defecto
    ]);

    // Crear el token de acceso solo si es un registro normal
    $token = null;
    if (!$request->user()) {
        $token = $user->createToken('allshows')->plainTextToken;
    }

    // Respuesta exitosa
    return response()->json([
        'message' => 'Usuario registrado exitosamente',
        'token' => $token, // Solo para usuarios normales
        'user' => $user,
    ], 201);
}


    // Login de usuario
    public function login(Request $request)
    {
        // Validar las credenciales de login
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        // Verificar si el usuario existe y las credenciales son correctas
        $user = User::where('email', $request->email)->first();

        // Si el usuario no existe o la contraseña no es correcta
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Credenciales incorrectas'], 401);
        }

        // Crear un nuevo token para el usuario autenticado
        $token = $user->createToken('allshows')->plainTextToken;

        return response()->json([
            'message' => 'Login exitoso',
            'token' => $token,
            'user' => $user,
        ]);
    }

    // Logout de usuario
    public function logout(Request $request)
    {
        // Eliminar todos los tokens del usuario actual
        $request->user()->tokens->each(function ($token) {
            $token->delete();
        });

        return response()->json(['message' => 'Logout exitoso']);
    }
}
