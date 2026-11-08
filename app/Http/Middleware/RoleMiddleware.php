<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Protege une route par profil d'acces :
 *
 *   Route::get(...)->middleware(['auth', 'role:ADMIN']);
 *   Route::get(...)->middleware(['auth', 'role:ADMIN,DG']);     // l'un OU l'autre
 *
 * Enregistrement de l'alias : voir bootstrap/app.php.
 */
class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $utilisateur = $request->user();

        abort_unless(
            $utilisateur && $utilisateur->hasAnyRole(...$roles),
            403,
            'Accès réservé aux profils : '.implode(', ', $roles).'.'
        );

        return $next($request);
    }
}
