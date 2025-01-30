<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre', 'descripcion', 'tags', 'precio', 'imagen'
    ];

    protected $casts = [
        'tags' => 'array', // Convertir tags automáticamente a array
        ];
        
}
