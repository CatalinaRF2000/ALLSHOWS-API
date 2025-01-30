<?php

namespace App\Policies;

use App\Models\Calendario;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CalendarPolicy
{
    use HandlesAuthorization;

    /**
     * Verificar si el usuario puede ver su calendario.
     */
    public function view(User $user, Calendario $calendar)
    {
        return in_array($user->role, ['artista', 'socorrista', 'animador']) && $user->id === $calendar->user_id;
    }
}
