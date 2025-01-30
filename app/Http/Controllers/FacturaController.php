<?php
namespace App\Http\Controllers;

use App\Models\Factura;
use Illuminate\Http\Request;

class FacturaController extends Controller
{
    
    // Mostrar las facturas de un usuario específico
    public function index(Request $request, $userId)
    {
        // Verificar si el usuario autenticado es admin
        if ($request->user()->role === 'admin') {
            // Si es admin, mostramos las nóminas de cualquier usuario
            $facturas = Factura::where('user_id', $userId)->get();
        } else {
            // Si no es admin, mostramos solo las nóminas del usuario autenticado
            $facturas = $request->user()->facturas; // Suponiendo que el usuario tiene una relación 'facturas'
        }

        // Verificar si se encontraron nóminas
        if ($facturas->isEmpty()) {
            return response()->json(['message' => 'No se encontraron facturas para este usuario.'], 404);
        }

        return response()->json($facturas);
    }

    // Mostrar una nómina específica
    public function show(Request $request, $id)
    {
        // Si el usuario es admin, puede ver cualquier nómina
        if ($request->user()->role == 'admin') {
            return Factura::findOrFail($id);  // Admin puede ver todas las nóminas
        }

        // Si no es admin, solo puede ver sus propias nóminas
        $factura = Factura::where('usuario_id', $request->user()->id)->findOrFail($id);
        return response()->json($factura);
    }


    // Método para manejar la carga del archivo
    public function upload(Request $request, $userId)
    {
        // Verificar si el usuario autenticado es admin o el mismo usuario
        if ($request->user()->role !== 'admin' && $request->user()->id != $userId) {
            return response()->json(['message' => 'Unauthorized'], 403);  // Solo admin o el mismo usuario pueden subir la nómina
        }

        // Validar que se haya subido un archivo
        $request->validate([
            'file' => 'required|file|mimes:csv,pdf,txt|max:2048',  // Limitar tipos y tamaño
        ]);

        // Obtener el archivo subido
        $file = $request->file('file');

        // Verificar si el archivo fue recibido correctamente
        if (!$file) {
            return response()->json(['message' => 'No file uploaded.'], 400);
        }

        // Subir el archivo al almacenamiento público o privado
        $filePath = $file->storeAs('facturas', $file->getClientOriginalName(), 'public');

        // Guardar los detalles del archivo en la base de datos
        Factura::create([
            'user_id' => $userId, // Asociamos el archivo con el usuario
            'file_path' => $filePath,  // Ruta del archivo
            'file_name' => $file->getClientOriginalName(),  // Nombre del archivo
        ]);

        return response()->json([
            'message' => 'Archivo subido y guardado exitosamente',
            'file_path' => $filePath,
        ], 201);
    }



    
    public function download($userId, $fileName)
    {
        $user = auth()->user();
    
        // Verificar permisos del usuario
        if ($user->id != $userId && $user->role !== 'admin') {
            return response()->json(['message' => 'No tienes permiso para acceder a este archivo.'], 403);
        }
    
        // Buscar el archivo en la base de datos
        $file = \App\Models\Factura::where('user_id', $userId)
            ->where('file_name', $fileName)
            ->first();
    
        if (!$file) {
            return response()->json(['message' => 'Archivo no encontrado en la base de datos.'], 404);
        }
    
        // Ruta al archivo en public/storage
        $filePath = public_path("storage/" . $file->file_path);
    
        // Verificar si el archivo existe físicamente
        if (!file_exists($filePath)) {
            return response()->json(['message' => 'Archivo no encontrado en el servidor.'], 404);
        }
    
        // Forzar descarga del archivo
        return response()->download($filePath, $file->file_name, [
            'Content-Type' => mime_content_type($filePath),
        ]);
    }
}

