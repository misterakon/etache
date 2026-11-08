<?php

namespace App\Http\Controllers;

use App\Models\EchelleTraitement;
use App\Models\Personnel;
use App\Support\Journal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/** Carrière de l'agent (grade, classe, échelon, dates de service, retraite, décès) : réservé à l'administrateur. L'état civil se saisit dans le formulaire agent. */
class PersonnelCarriereController extends Controller
{
    public function update(Request $request, $id)
    {
        abort_unless($request->user()?->hasRole('ADMIN'), 403, "Seul l'administrateur peut modifier ces informations.");
        $agent = Personnel::findOrFail($id);

        $naissance = $agent->date_naissance?->toDateString();

        $validator = Validator::make($request->all(), [
            'grade_id' => ['nullable', 'exists:grades,id'],
            'classe_id' => ['nullable', 'exists:classes_agent,id'],
            'echelon_id' => ['nullable', 'exists:echelons,id'],
            'date_premiere_prise_service' => ['nullable', 'date', $naissance ? 'after:'.$naissance : 'date'],
            'date_prise_service' => ['nullable', 'date', 'after_or_equal:date_premiere_prise_service'],
            'date_depart_retraite' => ['nullable', 'date', $naissance ? 'after:'.$naissance : 'date'],
            'date_deces' => ['nullable', 'date', $naissance ? 'after_or_equal:'.$naissance : 'date'],
        ], [
            'date_premiere_prise_service.after' => 'La première prise de service doit être postérieure à la naissance.',
            'date_prise_service.after_or_equal' => 'La date de prise de service ne peut pas précéder la première prise de service.',
            'date_depart_retraite.after' => 'Le départ à la retraite doit être postérieur à la naissance.',
            'date_deces.after_or_equal' => 'Le décès ne peut pas précéder la naissance.',
        ]);

        // Grade, classe et échelon vont ensemble, et la combinaison doit exister dans l'échelle de traitement
        $validator->after(function ($v) use ($request) {
            $trois = [$request->input('grade_id'), $request->input('classe_id'), $request->input('echelon_id')];
            $remplis = count(array_filter($trois, fn ($x) => filled($x)));
            if ($remplis === 0 || $v->errors()->hasAny(['grade_id', 'classe_id', 'echelon_id'])) {
                return;
            }
            if ($remplis < 3) {
                $v->errors()->add('grade_id', 'Renseignez ensemble le grade, la classe et l\'échelon (ou aucun des trois).');

                return;
            }
            if (EchelleTraitement::indiceDe((int) $trois[0], (int) $trois[1], (int) $trois[2]) === null) {
                $v->errors()->add('grade_id', "Cette combinaison grade, classe et échelon n'existe pas dans l'échelle de traitement.");
            }
        });

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $donnees = $validator->validated();
        foreach ($donnees as $cle => $valeur) {
            $donnees[$cle] = $valeur === '' ? null : $valeur;
        }

        $agent->fill($donnees);
        $modifies = array_keys($agent->getDirty());
        $agent->save();

        if ($modifies) {
            Journal::noter('personnel', 'modification', "Informations administratives de {$agent->nomComplet()} modifiées (".implode(', ', $modifies).')', $agent, $agent, ['champs' => $modifies]);
        }

        return redirect()->back()->with('success', 'Informations administratives enregistrées.');
    }
}
