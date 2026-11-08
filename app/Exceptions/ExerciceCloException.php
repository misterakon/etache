<?php

namespace App\Exceptions;

use App\Models\Exercice;
use RuntimeException;

/** Levée quand on tente d'écrire dans un exercice clos (création, modification, suppression). */
class ExerciceCloException extends RuntimeException
{
    public function __construct(public readonly Exercice $exercice)
    {
        parent::__construct("L'exercice {$exercice->annee} est clos : aucune création, modification ou suppression n'est possible. Seul un administrateur peut le rouvrir.");
    }

    /** Refus normal, pas une erreur applicative : inutile de le consigner dans les logs. */
    public function report(): bool
    {
        return false;
    }

    public function render($request)
    {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['status' => false, 'message' => $this->getMessage()], 423);
        }

        return redirect()->back()->with('error', $this->getMessage())->withErrors(['exercice_clos' => $this->getMessage()]);
    }
}
