<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Nomina;
use Illuminate\Auth\Access\HandlesAuthorization;

class NominaPolicy
{
    use HandlesAuthorization;

    /**
     * Verificar si el usuario puede ver sus nóminas.
     */
    public function view(User $user, Nomina $nomina)
    {
        return in_array($user->role, ['artista', 'socorrista', 'animador']) && $user->id === $nomina->user_id;
    }
}
