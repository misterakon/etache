<?php

namespace App\Http\Controllers;

use App\Models\Direction;
use App\Models\MandatResponsable;
use App\Models\Personnel;
use App\Models\Service;
use App\Models\SousDirection;
use App\Support\OrganigrammeResponsables;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Ecran "Responsables" : qui dirige la Direction Generale, chaque direction, sous-direction et service,
 * avec l'historique des nominations (table mandats_responsables).
 *
 * Consultation : ADMIN, DG, RD. Modification : ADMIN (voir routes/web.php).
 */
class ResponsableController extends Controller
{
    private const NIVEAUX = [
        MandatResponsable::NIVEAU_DG,
        MandatResponsable::NIVEAU_DIRECTION,
        MandatResponsable::NIVEAU_SOUS_DIRECTION,
        MandatResponsable::NIVEAU_SERVICE,
    ];

    public function index()
    {
        $donnees = OrganigrammeResponsables::construire();

        // Agents proposes pour une nomination : agents actifs
        $personnels = Personnel::where('actif', true)->orderBy('nom')->orderBy('prenom')->get();

        return view('dgepn.responsable', [
            'lignes' => $donnees['lignes'],
            'synthese' => $donnees['synthese'],
            'personnels' => $personnels,
        ]);
    }

    /** Nomination : ouvre un nouveau mandat (le precedent est ferme automatiquement). */
    public function store(Request $request)
    {
        abort(403, "Les affectations se gèrent désormais depuis la page Personnel (« Affecter à un poste »).");

        $niveau = $request->input('niveau');
        $tables = ['direction' => 'directions', 'sous_direction' => 'sous_directions', 'service' => 'services'];

        $validator = Validator::make($request->all(), [
            'niveau' => ['required', Rule::in(self::NIVEAUX)],
            'unite_id' => array_filter([
                $niveau === MandatResponsable::NIVEAU_DG ? 'nullable' : 'required',
                'integer',
                isset($tables[$niveau]) ? Rule::exists($tables[$niveau], 'id') : null,
            ]),
            'personnel_id' => ['required', Rule::exists('personnel', 'id')->where('actif', true)],
            'type' => ['required', Rule::in([MandatResponsable::TITULAIRE, MandatResponsable::INTERIM])],
            'date_debut' => ['required', 'date'],
            'date_fin' => ['nullable', 'date', 'after_or_equal:date_debut'],
            'reference_acte' => ['nullable', 'string', 'max:255'],
        ], [
            'unite_id.required' => "Choisissez l'unité concernée.",
            'personnel_id.required' => 'Choisissez un agent.',
            'personnel_id.exists' => 'Cet agent est introuvable ou inactif.',
            'date_debut.required' => 'La date de prise de fonction est obligatoire.',
            'date_fin.after_or_equal' => 'La date de fin doit être postérieure à la date de prise de fonction.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $donnees = $validator->validated();
        $uniteId = $niveau === MandatResponsable::NIVEAU_DG ? null : (int) $donnees['unite_id'];
        $cle = MandatResponsable::cleUnite($niveau);

        // Meme agent, meme poste, meme type, deja en fonction a cette date : rien a faire
        $dejaEnPoste = MandatResponsable::where('niveau', $niveau)
            ->when($cle, fn ($q) => $q->where($cle, $uniteId))
            ->where('type', $donnees['type'])
            ->where('personnel_id', $donnees['personnel_id'])
            ->actifA(Carbon::parse($donnees['date_debut']))
            ->exists();

        if ($dejaEnPoste) {
            return redirect()->back()
                ->withErrors(['personnel_id' => 'Cet agent occupe déjà ce poste à cette date.'])
                ->withInput();
        }

        $mandat = MandatResponsable::nommer(
            $niveau,
            $uniteId,
            (int) $donnees['personnel_id'],
            $donnees['date_debut'],
            $donnees['type'],
            $donnees['reference_acte'] ?? null,
            auth()->id(),
            $donnees['date_fin'] ?? null,
        );

        $agent = Personnel::find($mandat->personnel_id);

        return redirect()->back()->with('success', sprintf(
            '%s est désormais %s de « %s » à partir du %s.',
            $agent?->nomComplet() ?? 'L\'agent',
            $mandat->type === MandatResponsable::INTERIM ? 'responsable par intérim' : 'responsable',
            $this->libelleUnite($niveau, $uniteId),
            $mandat->date_debut->format('d/m/Y')
        ));
    }

    /** Fin de mandat sans successeur (depart, mutation...). */
    public function fin(Request $request, $id)
    {
        abort(403, "Les affectations se gèrent désormais depuis la page Personnel (« Affecter à un poste »).");

        $mandat = MandatResponsable::findOrFail($id);

        $cle = MandatResponsable::cleUnite($mandat->niveau);
        $enPoste = MandatResponsable::enPoste($mandat->niveau, $cle ? (int) $mandat->{$cle} : null);
        if (! $enPoste || $enPoste->id !== $mandat->id) {
            return redirect()->back()->withErrors([
                'date_fin' => "Seul le responsable en poste peut voir son mandat clos. Pour une erreur de saisie, supprimez la ligne.",
            ]);
        }

        $validator = Validator::make($request->all(), [
            'date_fin' => ['required', 'date', 'after_or_equal:'.$mandat->date_debut->toDateString()],
            'motif_fin' => ['required', 'string', 'max:255'],
        ], [
            'date_fin.required' => 'La date de fin est obligatoire.',
            'date_fin.after_or_equal' => 'La date de fin ne peut pas précéder la prise de fonction ('.$mandat->date_debut->format('d/m/Y').').',
            'motif_fin.required' => 'Indiquez le motif de la fin de mandat.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $mandat->update($validator->validated());

        return redirect()->back()->with('success', 'Le mandat a pris fin le '.$mandat->date_fin->format('d/m/Y').'.');
    }

    /** Historique d'une unite (JSON, affiche dans le modal). */
    public function historique(Request $request)
    {
        $niveau = $request->query('niveau');
        abort_unless(in_array($niveau, self::NIVEAUX, true), 422, 'Niveau inconnu.');

        $uniteId = $niveau === MandatResponsable::NIVEAU_DG ? null : (int) $request->query('unite_id');
        $cle = MandatResponsable::cleUnite($niveau);

        $mandats = MandatResponsable::with(['personnel', 'auteur'])
            ->where('niveau', $niveau)
            ->when($cle, fn ($q) => $q->where($cle, $uniteId))
            ->orderByDesc('date_debut')
            ->orderByDesc('id')
            ->get();

        $enPosteId = MandatResponsable::enPoste($niveau, $uniteId)?->id;
        $aujourdhui = today();

        return response()->json([
            'unite' => $this->libelleUnite($niveau, $uniteId),
            'mandats' => $mandats->map(function ($m) use ($enPosteId, $aujourdhui) {
                $enCours = $m->date_debut->lte($aujourdhui) && (! $m->date_fin || $m->date_fin->gte($aujourdhui));
                $aVenir = $m->date_debut->gt($aujourdhui);

                $etat = match (true) {
                    $m->id === $enPosteId => 'en_poste',
                    $enCours => 'supplee',   // en cours mais couvert par un intérim
                    $aVenir => 'a_venir',
                    in_array($m->motif_fin, ['remplacement', "fin d'intérim"], true) => 'remplace',
                    default => 'termine',
                };

                return [
                    'id' => $m->id,
                    'personne' => $m->personnel?->nomComplet() ?? '—',
                    'initiales' => $m->personnel?->initiales() ?? '?',
                    'photo' => $m->personnel?->photo_url,
                    'fiche' => $m->personnel ? route('dgepn.personnel.fiche', $m->personnel_id) : null,
                    'type' => $m->type,
                    'debut' => $m->date_debut->format('d/m/Y'),
                    'debut_iso' => $m->date_debut->toDateString(),
                    'fin' => $m->date_fin?->format('d/m/Y'),
                    'etat' => $etat,
                    'acte' => $m->reference_acte,
                    'motif' => $m->motif_fin,
                    'par' => $m->auteur?->name,
                ];
            })->values(),
        ]);
    }

    /** Suppression d'une ligne saisie par erreur (correction). L'historique normal se ferme avec "fin" ou "nommer". */
    public function destroy($id)
    {
        abort(403, "Les affectations se gèrent désormais depuis la page Personnel (« Affecter à un poste »).");
        
        MandatResponsable::findOrFail($id)->delete();

        return response()->json(['status' => true, 'message' => 'La ligne a été supprimée.']);
    }

    private function libelleUnite(string $niveau, ?int $uniteId): string
    {
        return match ($niveau) {
            MandatResponsable::NIVEAU_DIRECTION => Direction::find($uniteId)?->nom ?? 'Direction',
            MandatResponsable::NIVEAU_SOUS_DIRECTION => SousDirection::find($uniteId)?->nom ?? 'Sous-direction',
            MandatResponsable::NIVEAU_SERVICE => Service::find($uniteId)?->nom ?? 'Service',
            default => 'Direction Générale',
        };
    }
}
