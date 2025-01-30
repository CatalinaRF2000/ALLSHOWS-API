<?php

namespace App\Policies;


use App\Models\User;
use App\Models\Producto;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductoPolicy
{
    use HandlesAuthorization;

    /**
     * Verificar si el usuario puede crear un producto.
     */
    public function create(User $user)
    {
        return $user->role === 'admin';
    }

    /**
     * Verificar si el usuario puede actualizar un producto.
     */
    public function update(User $user, Producto $producto)
    {
        return $user->role === 'admin';
    }

    /**
     * Verificar si el usuario puede eliminar un producto.
     */
    public function delete(User $user, Producto $producto)
    {
        return $user->role === 'admin';
    }

    /**
     * Verificar si el usuario puede ver un producto.
     */
    public function view(User $user, Producto $producto)
    {
        return true;  // Todos pueden ver los productos
    }
}
