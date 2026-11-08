<?php

namespace App\Support;

use App\Models\Direction;
use App\Models\Fonction;
use App\Models\JournalActivite;
use App\Models\MandatResponsable;
use App\Models\Personnel;
use App\Models\Service;
use App\Models\SousDirection;
use Carbon\Carbon;
use DomainException;

/**
 * Affectation d'un agent à un poste de l'organigramme.
 *
 * Même principe que les mandats : poste à titulaire unique (DG, DGA, directeur, sous-directeur, chef de service),
 * « titulaire » ou « intérim », historique conservé. Quand le poste a déjà un occupant, l'affectation n'a lieu
 * qu'APRÈS CONFIRMATION : le nouveau devient titulaire et l'ancien est remplacé (mandat fermé la veille).
 * Les autres fonctions (agent, chargé d'études, conseiller, technicien...) fixent seulement le rattachement :
 * un service, ou la Direction Générale.
 */
class AffectationPoste
{
    /** Données embarquées dans le formulaire : fonctions, unités et occupants actuels des postes. */
    public static function donneesFormulaire(): array
    {
        $fonctions = Fonction::where('actif', true)->orderBy('ordre')->get()->map(fn ($f) => [
            'id' => $f->id, 'libelle' => $f->libelle, 'categorie' => $f->categorie, 'niveau' => $f->niveau_mandat,
        ])->all();

        $unites = [
            'direction' => Direction::orderBy('nom')->get(['id', 'nom'])->map(fn ($d) => ['id' => $d->id, 'libelle' => $d->nom])->all(),
            'sous_direction' => SousDirection::with('direction:id,nom')->orderBy('nom')->get()->map(fn ($s) => ['id' => $s->id, 'libelle' => $s->nom.($s->direction ? ' ('.$s->direction->nom.')' : '')])->all(),
            'service' => Service::orderBy('nom')->get(['id', 'nom'])->map(fn ($s) => ['id' => $s->id, 'libelle' => $s->nom])->all(),
        ];

        $occupants = [];
        foreach (MandatResponsable::actifA()->with('personnel')->get() as $m) {
            $cle = $m->niveau.':'.self::uniteDuMandat($m);
            $occupants[$cle][$m->type] = ['id' => $m->personnel_id, 'nom' => $m->personnel?->nomComplet(), 'depuis' => $m->date_debut->format('d/m/Y')];
        }

        // Postes à titulaire unique occupés par chaque agent (pour « Mettre fin »)
        $mandatsAgents = [];
        foreach (MandatResponsable::actifA()->with(['direction', 'sousDirection', 'service'])->get() as $m) {
            $mandatsAgents[$m->personnel_id][] = [
                'id' => $m->id, 'libelle' => $m->libellePoste(), 'type' => $m->type,
                'debut' => $m->date_debut->format('d/m/Y'), 'debut_iso' => $m->date_debut->format('Y-m-d'),
            ];
        }

        return ['fonctions' => $fonctions, 'unites' => $unites, 'occupants' => $occupants, 'mandatsAgents' => $mandatsAgents];
    }

    private static function uniteDuMandat(MandatResponsable $m): int
    {
        $colonne = MandatResponsable::cleUnite($m->niveau);

        return $colonne ? (int) $m->{$colonne} : 0;
    }

    /** Valeur du champ « fonction » de l'agent : « Directeur Général », « Sous-directeur - Sous-direction X », « Agent »... */
    public static function libellePoste(Fonction $f, ?int $uniteId): string
    {
        $nom = match ($f->niveau_mandat) {
            MandatResponsable::NIVEAU_DIRECTION => Direction::find($uniteId)?->nom,
            MandatResponsable::NIVEAU_SOUS_DIRECTION => SousDirection::find($uniteId)?->nom,
            MandatResponsable::NIVEAU_SERVICE => Service::find($uniteId)?->nom,
            default => null,
        };

        return $nom ? "{$f->libelle} - {$nom}" : $f->libelle;
    }

    /** Met à jour la fonction enregistrée selon le mandat actif ou la fonction non hiérarchique choisie. */
    public static function synchroniserFonction(Personnel $agent, ?Carbon $date = null): void
    {
        $date ??= today();
        $mandat = MandatResponsable::query()
            ->where('personnel_id', $agent->id)
            ->actifA($date)
            ->reorder()
            ->prioritaire()
            ->with(['direction', 'sousDirection', 'service'])
            ->first();

        if ($mandat) {
            $fonction = Fonction::query()
                ->where('categorie', Fonction::CAT_MANDAT)
                ->where('niveau_mandat', $mandat->niveau)
                ->where('actif', true)
                ->first();
            $libelle = $fonction
                ? self::libellePoste($fonction, self::uniteDuMandat($mandat))
                : $mandat->libellePoste();

            $agent->update([
                'fonction_id' => $fonction?->id,
                'fonction' => $libelle,
                'service_id' => $mandat->niveau === MandatResponsable::NIVEAU_SERVICE ? $mandat->service_id : null,
            ]);

            return;
        }

        $fonction = $agent->fonctionDef;
        if ($fonction && ! $fonction->estMandat()) {
            if ($agent->fonction !== $fonction->libelle) {
                $agent->update(['fonction' => $fonction->libelle]);
            }

            return;
        }

        if ($agent->fonction_id !== null || $agent->fonction !== 'Agent') {
            $agent->update(['fonction_id' => null, 'fonction' => 'Agent']);
        }
    }

    /**
     * @return array{statut: string, message: string, occupants?: list<string>}
     *
     * @throws DomainException
     */
    public static function affecter(Personnel $agent, Fonction $f, ?int $uniteId, bool $interim, Carbon $date, ?string $acte, bool $confirmer, ?int $parUserId): array
    {
        $estMandat = $f->categorie === Fonction::CAT_MANDAT;
        $niveau = $f->niveau_mandat;
        $colonne = $estMandat ? MandatResponsable::cleUnite($niveau) : null;
        $type = $interim ? MandatResponsable::INTERIM : MandatResponsable::TITULAIRE;

        // --- Contrôles du poste visé ---
        if ($estMandat) {
            if ($colonne && ! $uniteId) {
                throw new DomainException('Choisissez le poste concerné.');
            }
            if ($colonne && ! self::uniteExiste($niveau, $uniteId)) {
                throw new DomainException('Le poste choisi est introuvable.');
            }
        } elseif ($f->categorie === Fonction::CAT_SERVICE && (! $uniteId || ! self::uniteExiste('service', $uniteId))) {
            throw new DomainException('Choisissez le service de rattachement.');
        }

        $existants = $estMandat
            ? MandatResponsable::where('niveau', $niveau)->when($colonne, fn ($q) => $q->where($colonne, $uniteId))->actifA($date)->with('personnel')->get()
            : collect();

        if ($estMandat && $existants->contains(fn ($m) => $m->personnel_id === $agent->id && $m->type === $type)) {
            throw new DomainException($agent->nomComplet().($interim ? " assure déjà l'intérim de" : ' est déjà titulaire de').' ce poste.');
        }

        // --- Ce que l'affectation fait cesser (à confirmer) ---
        // 1. les occupants actuels du poste : le titulaire et/ou l'intérim (un nouveau titulaire met fin à l'intérim)
        $aRemplacer = $existants->filter(fn ($m) => $m->personnel_id !== $agent->id && (! $interim || $m->type === MandatResponsable::INTERIM));
        // 2. les autres postes de titulaire que l'agent occupe déjà (changement de poste) : une fonction = un poste
        $autresPostes = $interim ? collect() : MandatResponsable::where('personnel_id', $agent->id)->where('type', MandatResponsable::TITULAIRE)
            ->actifA($date)->with(['direction', 'sousDirection', 'service'])->get()
            ->reject(fn ($m) => $estMandat && $m->niveau === $niveau && (! $colonne || (int) $m->{$colonne} === (int) $uniteId))->values();

        if (($aRemplacer->isNotEmpty() || $autresPostes->isNotEmpty()) && ! $confirmer) {
            $liste = $aRemplacer->map(fn ($m) => ($m->personnel?->nomComplet() ?? '—').' ('.($m->type === MandatResponsable::INTERIM ? 'intérim' : 'titulaire').')')->values()->all();
            $phrases = [];
            if ($liste) {
                $phrases[] = 'Ce poste est déjà occupé : '.implode(', ', $liste).'. '.($interim ? "L'intérimaire actuel sera remplacé." : "Ils seront remplacés : {$agent->nomComplet()} devient titulaire.");
            }
            if ($autresPostes->isNotEmpty()) {
                $phrases[] = "{$agent->nomComplet()} quitte son poste actuel : ".$autresPostes->map->libellePoste()->implode(', ').'.';
            }

            return ['statut' => 'conflit', 'occupants' => $liste, 'message' => implode(' ', $phrases)];
        }

        // --- Application ---
        $titulairesRemplaces = $aRemplacer->where('type', MandatResponsable::TITULAIRE)->map->personnel->filter();

        foreach ($autresPostes as $mandat) {
            $fin = $date->copy()->subDay();
            $mandat->update(['date_fin' => $fin->lt($mandat->date_debut) ? $mandat->date_debut->copy() : $fin, 'motif_fin' => 'changement de poste']);
        }

        if ($estMandat) {
            MandatResponsable::nommer($niveau, $colonne ? $uniteId : null, $agent->id, $date, $type, $acte, $parUserId);
            $libelle = self::libellePoste($f, $uniteId);
        } elseif ($f->categorie === Fonction::CAT_SERVICE) {
            $agent->update(['fonction_id' => $f->id, 'fonction' => $f->libelle, 'service_id' => $uniteId]);
            $libelle = $f->libelle.' (service : '.Service::find($uniteId)->nom.')';
        } else {
            $agent->update(['fonction_id' => $f->id, 'fonction' => $f->libelle, 'service_id' => null]);
            $libelle = $f->libelle.' (Direction Générale)';
        }

        self::synchroniserFonction($agent, $date);
        // Les personnes sorties de leur mandat retrouvent leur autre poste actif ou le statut Agent.
        foreach ($titulairesRemplaces as $ancien) {
            self::synchroniserFonction($ancien, $date);
        }
        $details = [];
        if ($titulairesRemplaces->isNotEmpty()) {
            $details[] = 'remplace '.$titulairesRemplaces->map->nomComplet()->implode(', ');
        }
        if ($autresPostes->isNotEmpty()) {
            $details[] = 'quitte '.$autresPostes->map->libellePoste()->implode(', ');
        }
        Journal::noter('personnel', 'affectation', "{$agent->nomComplet()} : ".($estMandat ? ($interim ? 'intérim — ' : 'titulaire — ') : '').$libelle.($details ? ' ('.implode(' ; ', $details).')' : ''), $agent, $agent, [
            'fonction' => $f->libelle, 'poste' => $libelle, 'type' => $estMandat ? $type : null, 'date' => $date->toDateString(), 'acte' => $acte,
        ]);

        $message = "{$agent->nomComplet()} : ".($estMandat ? ($interim ? 'intérim — ' : 'titulaire — ') : '').$libelle.'.';
        if ($titulairesRemplaces->isNotEmpty()) {
            $message .= ' '.$titulairesRemplaces->map->nomComplet()->implode(', ').' n\'a plus de poste : pensez à l\'affecter.';
        }

        return ['statut' => 'ok', 'message' => $message];
    }

    /** Mouvements de poste d'un agent : mandats (titulaire / intérim) et historique des affectations. */
    public static function mouvements(Personnel $agent): array
    {
        return [
            'mandats' => MandatResponsable::where('personnel_id', $agent->id)->with(['direction', 'sousDirection', 'service', 'auteur'])
                ->orderByDesc('date_debut')->orderByDesc('id')->get(),
            'historique' => JournalActivite::where('parent_type', $agent->getMorphClass())->where('parent_id', $agent->id)
                ->where('module', 'personnel')->whereIn('evenement', ['affectation', 'fin_affectation'])
                ->orderByDesc('created_at')->orderByDesc('id')->limit(100)->get(),
        ];
    }

    private static function uniteExiste(string $niveau, int $id): bool
    {
        return match ($niveau) {
            MandatResponsable::NIVEAU_DIRECTION => Direction::whereKey($id)->exists(),
            MandatResponsable::NIVEAU_SOUS_DIRECTION => SousDirection::whereKey($id)->exists(),
            default => Service::whereKey($id)->exists(),
        };
    }
}
