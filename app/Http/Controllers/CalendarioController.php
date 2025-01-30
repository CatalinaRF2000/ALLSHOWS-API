<?php
namespace App\Http\Controllers;

use App\Models\Calendario;
use Illuminate\Http\Request;

class CalendarioController extends Controller
{
    // Obtener todos los calendarios del usuario autenticado
        public function index(Request $request)
    {
        $user = $request->user();

        // Si el usuario es administrador, devolver todos los calendarios
        if ($user->role === 'admin') {
            return Calendario::all(); // Devuelve todos los calendarios
        }

        // Si no es administrador, devolver solo los calendarios asociados al usuario autenticado
        return Calendario::where('user_id', $user->id)->get();
    }


    public function getUserCalendars(Request $request)
{
    return response()->json(Calendario::where('user_id', auth()->id())->get(), 200)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
}


    // Crear un nuevo calendario para el usuario autenticado
    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string',
            'fecha' => 'required|date',
        ]);

        // Crear el calendario y asociarlo al usuario autenticado
        $calendario = new Calendario($request->all());
        $calendario->user_id = $request->user()->id;  // Asociar al usuario autenticado
        $calendario->save();

        return response()->json($calendario, 201);
    }

    // Mostrar un calendario específico asociado al usuario autenticado 
    public function show(Request $request, $userId, $calendarId)
    {
        $user = $request->user();
    
        // Si el usuario autenticado es admin, puede ver cualquier calendario
        if ($user->role === 'admin') {
            $calendario = Calendario::with('user') // Carga la relación con el usuario
                ->where('id', $calendarId)
                ->where('user_id', $userId)
                ->firstOrFail();
        } else {
            // Un usuario normal solo puede ver sus propios calendarios
            if ($user->id != $userId) {
                return response()->json(['message' => 'No tienes permiso para ver este calendario.'], 403);
            }
    
            $calendario = Calendario::with('user') // Carga la relación con el usuario
                ->where('id', $calendarId)
                ->where('user_id', $user->id)
                ->firstOrFail();
        }
    
        return response()->json($calendario);
    }
    

    // Actualizar un calendario específico asociado al usuario autenticado
    public function update(Request $request, $user_id, $calendario_id)
    {
        // Validar que el calendario pertenece al usuario
        $calendario = Calendario::where('user_id', $user_id)->where('id', $calendario_id)->firstOrFail();
    
        // Validar los datos enviados
        $request->validate([
            'html' => 'required|string',
        ]);
    
        // Actualizar el calendario
        $calendario->html = $request->html;
        $calendario->save();
    
        return response()->json(['message' => 'Calendario actualizado correctamente', 'calendario' => $calendario]);
    }
    

    // Eliminar un calendario específico asociado al usuario autenticado
    public function destroy(Request $request, $id)
    {
        $calendario = Calendario::where('user_id', $request->user()->id)
                                ->findOrFail($id);

        $calendario->delete();

        return response()->json(['message' => 'Calendario eliminado']);
    }
}
