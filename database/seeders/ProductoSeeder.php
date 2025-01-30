<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Producto;

class ProductoSeeder extends Seeder
{
    public function run()
    {
        $productos = json_decode(file_get_contents(database_path('seeders/productos.json')), true);
        Producto::insert($productos);
    }
}
