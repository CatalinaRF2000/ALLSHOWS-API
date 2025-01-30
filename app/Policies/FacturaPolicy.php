<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Factura;
use Illuminate\Auth\Access\HandlesAuthorization;

class FacturaPolicy
{
    use HandlesAuthorization;

    /**
     * Verificar si el usuario puede ver sus facturas.
     */
    public function view(User $user, Factura $factura)
    {
        return $user->role === 'cliente' && $user->id === $factura->user_id;
    }
}
