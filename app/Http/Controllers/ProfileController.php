<?php

namespace App\Http\Controllers;

use App\Models\Personnel;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * « Mon compte » : la page personnelle de chaque utilisateur connecté — identité, profil d'accès,
 * rattachement RH s'il existe, et sécurité (changement de mot de passe). Distincte de la fiche agent
 * (annuaire RH, réservée à l'administrateur et à l'agent concerné) : ici, tout utilisateur se retrouve,
 * même un compte ADMIN ou COURRIER non relié à un agent.
 */
class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $moi = $request->user();

        /** @var Personnel|null $agent */
        $agent = $moi->personnel_id ? Personnel::with(['service.sousDirection.direction', 'service.directionDirecte'])->find($moi->personnel_id) : null;

        return view('profile.edit', [
            'moi' => $moi,
            'agent' => $agent,
        ]);
    }
}
