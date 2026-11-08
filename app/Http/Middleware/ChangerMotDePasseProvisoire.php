<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/** Un compte créé avec un mot de passe provisoire doit le changer avant tout autre usage. */
class ChangerMotDePasseProvisoire
{
    public function handle(Request $request, Closure $next)
    {
        $utilisateur = $request->user();
        if ($utilisateur && $utilisateur->mot_de_passe_provisoire && ! $request->routeIs('rh.mot_de_passe', 'rh.mot_de_passe.store', 'logout')) {
            return redirect()->route('rh.mot_de_passe');
        }

        return $next($request);
    }
}
