<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Catalogo;

class CatalogoController extends Controller
{
    public function uploadCatalog(Request $request)
    {
        $request->validate([
            'archivo_pdf' => 'required|mimes:pdf|max:2048',
            'nombre' => 'required|string|max:255'
        ]);

        $path = $request->file('archivo_pdf')->store('public/catalogos');

        $catalogo = Catalogo::create([
            'nombre' => $request->nombre,
            'archivo_pdf' => str_replace('public/', '', $path)
        ]);

        return response()->json([
            'message' => 'Catálogo subido con éxito',
            'catalogo' => $catalogo
        ], 201);
    }

    public function downloadCatalog()
    {
        $catalogo = Catalogo::latest()->first();
        if (!$catalogo) {
            return response()->json(['message' => 'No hay catálogos disponibles'], 404);
        }

        return response()->download(storage_path("app/public/{$catalogo->archivo_pdf}"));
    }
}

