<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Support\Journal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Changement de mot de passe par l'utilisateur, depuis « Mon compte ». Ferme ses autres sessions
     * (autres postes) par sécurité et inscrit l'événement au journal d'activité.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ], [
            'current_password.required' => 'Saisissez votre mot de passe actuel.',
            'current_password.current_password' => 'Le mot de passe actuel est incorrect.',
            'password.required' => 'Saisissez le nouveau mot de passe.',
            'password.confirmed' => 'La confirmation ne correspond pas au nouveau mot de passe.',
        ]);

        $request->user()->forceFill([
            'password' => Hash::make($validated['password']),
            'mot_de_passe_modifie_le' => now(),
        ])->save();

        Auth::logoutOtherDevices($validated['password']);

        Journal::noter('session', 'mot_de_passe_modifie', 'Mot de passe modifié depuis « Mon compte ».', $request->user());

        return back()->with('status', 'password-updated');
    }
}
