<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static function boot()
    {
        parent::boot();

        // Crear automáticamente un calendario al crear un usuario
        static::created(function ($user) {
            $user->calendarios()->create([
                'titulo' => $user->name, // Usar el nombre del usuario como título
                'fecha' => now(),
                'html' => 'Añadir calendario', // Inicialmente vacío
            ]);
        });
    }

    public function nominas()
    {
        return $this->hasMany(Nomina::class);
    }

    public function facturas()
    {
        return $this->hasMany(Factura::class);
    }

    public function calendarios()
    {
        return $this->hasMany(Calendario::class);
    }
}
