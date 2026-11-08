<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Protège une route côté serveur selon le profil d'accès : ->middleware(ExigerProfil::class.':ADMIN,DG').
 * Un utilisateur qui connaît l'URL mais n'a pas le profil reçoit une erreur 403.
 */
class ExigerProfil
{
    public function handle(Request $request, Closure $next, string ...$profils)
    {
        abort_unless($request->user()?->hasRole($profils), 403, "Vous n'avez pas accès à cette page.");

        return $next($request);
    }
}
