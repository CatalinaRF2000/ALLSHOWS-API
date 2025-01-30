<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;

class ProductoController extends Controller
{
    // Mostrar todos los productos (público)
    public function index(Request $request)
    {
        // Si no está autenticado, devolver productos sin precio
        if (!$request->user()) {
            return $this->indexWithoutPrice();
        }
    
        // Si está autenticado, devolver productos completos según el rol
        $user = $request->user();
        if ($user->role === 'admin' || $user->role === 'cliente') {
            return $this->indexWithPrice();
        }
    
        // Para otros roles, devolver una respuesta vacía
        return response()->json([], 403);
    }
    

    // Crear un nuevo producto (solo admin)
    public function store(Request $request)
    {
        // Verificar si el usuario tiene el rol de admin
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403); // Solo admin puede crear productos
        }

        // Validar los datos del producto
        // ProductoController.php
$request->validate([
    'nombre' => 'required|string',
    'descripcion' => 'required|string',
    'tags' => 'required|array', // Validar que sea un array
    'precio' => 'required|numeric',
    'imagen' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // Validación de la imagen
]);


        // Crear el producto
        $producto = Producto::create($request->all());

        return response()->json($producto, 201);
    }

    // Mostrar un producto específico
    public function show($id)
    {
        return Producto::findOrFail($id);
    }

    // Actualizar un producto (solo admin)
    public function update(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);

        // Verificar si el usuario tiene el rol de admin
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403); // Solo admin puede actualizar productos
        }

        // Actualizar el producto
        $producto->update($request->all());

        return response()->json($producto);
    }

    // Eliminar un producto (solo admin)
    public function destroy(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);

        // Verificar si el usuario tiene el rol de admin
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403); // Solo admin puede eliminar productos
        }

        // Eliminar el producto
        $producto->delete();

        return response()->json(['message' => 'Producto eliminado']);
    }

    

    public function indexWithPrice()
{
    return response()->json(Producto::all(), 200);
}

public function showWithPrice($id)
{
    $producto = Producto::findOrFail($id);
    return response()->json($producto, 200);
}

public function indexWithoutPrice()
{
    $productos = Producto::all()->map(function ($producto) {
        return [
            'nombre' => $producto->nombre,
            'descripcion' => $producto->descripcion,
            'tags' => $producto->tags,
        ];
    });

    return response()->json($productos, 200);
}

public function showWithoutPrice($id)
{
    $producto = Producto::findOrFail($id);
    return response()->json([
        'nombre' => $producto->nombre,
        'descripcion' => $producto->descripcion,
        'tags' => $producto->tags,
    ], 200);
}


public function downloadCatalog()
{
    // Obtener los productos
    $productos = Producto::all();

    // Crear el PDF con la vista `catalogo_pdf` (debes crear esta vista en resources/views)
    $pdf = PDF::loadView('catalogo_pdf', compact('productos'));

    // Descargar el PDF con el nombre "catalogo.pdf"
    return $pdf->download('catalogo.pdf');
}
}
