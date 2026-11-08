<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

/** Changement obligatoire du mot de passe provisoire (comptes créés en masse ou par import). */
class MotDePasseController extends Controller
{
    public function show(Request $request)
    {
        if (! $request->user()->mot_de_passe_provisoire) {
            return redirect()->route('dashboard');
        }

        return view('rh.mot_de_passe');
    }

    public function store(Request $request)
    {
        $d = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8), 'different:current_password'],
        ], [
            'current_password.required' => 'Saisissez votre mot de passe provisoire.', 'current_password.current_password' => 'Le mot de passe provisoire est incorrect.',
            'password.confirmed' => 'La confirmation ne correspond pas au nouveau mot de passe.', 'password.min' => 'Le nouveau mot de passe doit contenir au moins 8 caractères.',
            'password.different' => 'Choisissez un mot de passe différent du mot de passe provisoire.',
        ]);

        $request->user()->forceFill(['password' => Hash::make($d['password']), 'mot_de_passe_provisoire' => false])->save();

        return redirect()->route('dashboard')->with('success', 'Votre mot de passe a été changé. Bienvenue !');
    }
}
