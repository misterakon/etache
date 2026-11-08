<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * En-têtes de sécurité ajoutés à toutes les réponses de l'application :
 * anti-clickjacking, anti-détection de type MIME, politique de référent, fonctions du navigateur inutiles coupées,
 * HSTS dès que le site est servi en HTTPS. La CSP se limite aux directives qui ne gênent pas les scripts du thème.
 */
class EnTetesSecurite
{
    public function handle(Request $request, Closure $next): Response
    {
        $reponse = $next($request);

        $entetes = [
            'X-Frame-Options' => 'SAMEORIGIN',
            'X-Content-Type-Options' => 'nosniff',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Permissions-Policy' => 'camera=(), microphone=(), geolocation=(), payment=(), usb=()',
            'Cross-Origin-Opener-Policy' => 'same-origin',
            'Content-Security-Policy' => "frame-ancestors 'self'; base-uri 'self'; form-action 'self'; object-src 'none'",
        ];

        if ($request->isSecure()) {
            $entetes['Strict-Transport-Security'] = 'max-age=31536000; includeSubDomains';
        }

        foreach ($entetes as $nom => $valeur) {
            if (! $reponse->headers->has($nom)) {
                $reponse->headers->set($nom, $valeur);
            }
        }

        $reponse->headers->remove('X-Powered-By');

        return $reponse;
    }
}
