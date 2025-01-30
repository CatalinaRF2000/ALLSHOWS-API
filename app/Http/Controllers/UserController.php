<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    // Ver el perfil del usuario o todos los usuarios si el usuario autenticado es admin
    public function show(Request $request)
    {
        // Si el usuario autenticado es admin, devolver todos los usuarios
        if ($request->user()->role == 'admin') {
            return User::all();  // Devuelve todos los usuarios
        }

        // Si no es admin, devolver solo el perfil del usuario autenticado
        return $request->user();  // Devuelve el usuario autenticado
    }

    // Actualizar el correo o contraseña del usuario
    public function update(Request $request, $id = null)
{
    // Identificar si es admin o usuario normal
    $user = $request->user();

    if ($user->role !== 'admin') {
        // Usuario no admin solo puede actualizar su propio perfil
        $userToUpdate = $user;
    } else {
        // Admin puede actualizar a cualquier usuario
        $userToUpdate = User::findOrFail($id);
    }

    // Validación de los datos enviados
    $request->validate([
        'name' => 'nullable|string|max:255',
        'email' => 'nullable|email|unique:users,email,' . $userToUpdate->id,
        'password' => 'nullable|string|min:8|confirmed', // Validar contraseña solo si está presente
        'role' => 'nullable|string|in:admin,artista,socorrista,animacion', // Solo si deseas que el admin pueda cambiar roles
    ]);

    // Actualizar los datos del usuario
    if ($request->has('name')) {
        $userToUpdate->name = $request->name;
    }

    if ($request->has('email')) {
        $userToUpdate->email = $request->email;
    }

    if ($request->has('password')) {
        $userToUpdate->password = Hash::make($request->password);
    }

    if ($request->has('role') && $user->role === 'admin') {
        $userToUpdate->role = $request->role; // Solo admins pueden cambiar roles
    }

    $userToUpdate->save();

    return response()->json(['message' => 'Usuario actualizado correctamente', 'user' => $userToUpdate]);
}


    // Cambiar la contraseña del usuario
    public function changePassword(Request $request)
    {
        // Validar los datos enviados
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validación fallida', 'errors' => $validator->errors()], 422);
        }

        // Obtener el usuario autenticado
        $user = $request->user();

        // Verificar que la contraseña actual sea correcta
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'La contraseña actual no es correcta'], 403);
        }

        // Actualizar la contraseña
        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['message' => 'La contraseña se ha cambiado correctamente']);
    }



    public function destroy($id)
{
    // Verificar si el usuario autenticado es admin
    if (auth()->user()->role !== 'admin') {
        return response()->json(['message' => 'No autorizado'], 403);
    }

    // Buscar el usuario por ID
    $user = User::find($id);

    // Verificar si el usuario existe
    if (!$user) {
        return response()->json(['message' => 'Usuario no encontrado'], 404);
    }

    // Eliminar el usuario
    $user->delete();

    return response()->json(['message' => 'Usuario eliminado correctamente']);
}




    
}
