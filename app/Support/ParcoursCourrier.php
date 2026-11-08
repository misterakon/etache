<?php

namespace App\Support;

use App\Models\Courrier;
use Carbon\Carbon;
use Illuminate\Support\Str;

/**
 * « Où en est ce courrier ? » : qui détient le dossier, par qui il est passé, circuit de la réponse, avancement.
 * Lecture seule : tout est déduit des diligences, des réimputations, des réponses et des visas existants.
 */
class ParcoursCourrier
{
    private static function date($valeur, string $format = 'd/m · H:i'): ?string
    {
        return $valeur ? Carbon::parse($valeur)->format($format) : null;
    }

    /** Vrai si les deux libellés diffèrent (évite d'afficher deux fois le même nom). */
    private static function different(?string $a, ?string $b): bool
    {
        return Str::lower(trim((string) $a)) !== Str::lower(trim((string) $b));
    }

    private static function etape(string $etat, string $titre, ?string $nom, ?string $detail = null, ?string $icone = null, ?string $unite = null, ?string $note = null, int $autres = 0): array
    {
        return ['etat' => $etat, 'titre' => $titre, 'nom' => $nom, 'detail' => $detail, 'icone' => $icone, 'unite' => $unite, 'note' => $note, 'autres' => $autres];
    }

    public static function pour(Courrier $courrier): array
    {
        $courrier->loadMissing('enregistrePar');
        $racines = $courrier->diligences()->whereNull('parent_diligence_id')->with(['responsable', 'imputation.auteur'])->orderBy('id')->get();
        $principales = $racines->where('role', 'principal');
        $racine = $principales->first(fn ($d) => $d->statut !== 'annule') ?? $principales->last();

        $etapes = [self::etape('ok', 'Enregistré', $courrier->enregistrePar?->name ?? 'Service courrier', self::date($courrier->created_at), 'fa-inbox')];
        $detenteur = null;
        $echeance = null;
        $message = null;
        $avancement = null;

        if (! $racine) {
            if (in_array($courrier->statut, ['archive', 'solde'], true)) {
                $etapes[] = self::etape('ok', $courrier->statut === 'archive' ? 'Classé' : 'Traité', null, $courrier->numero_archives ? 'n° '.$courrier->numero_archives : null, 'fa-box-archive');
                $message = ['texte' => $courrier->statut === 'archive' ? 'Courrier classé, sans diligence.' : 'Courrier traité.', 'couleur' => '#2E7D5B', 'icone' => 'fa-circle-check'];
            } else {
                $etapes[] = self::etape('cur', "En attente d'imputation", 'Direction Générale', 'à imputer', 'fa-paper-plane');
                $message = ['texte' => "Ce courrier attend d'être imputé.", 'couleur' => '#C77D0A', 'icone' => 'fa-hourglass-half'];
            }
        } else {
            $imp = $racine->imputation;
            $etapes[] = self::etape('ok', 'Imputé par', $imp?->auteur?->name ?? 'Direction Générale', self::date($imp?->created_at ?? $racine->created_at), 'fa-paper-plane');

            $d = $racine;
            $niveau = 0;
            while ($d && $niveau < 8) {
                $nom = $d->responsable?->nomComplet() ?? $d->destinataire_libelle;
                $unite = $d->responsable && self::different($d->destinataire_libelle, $nom) ? $d->destinataire_libelle : null;
                $retard = $d->estEnRetard();
                [$etat, $detail, $icone] = match ($d->statut) {
                    'solde' => ['ok', 'traitée le '.self::date($d->solde_le, 'd/m'), 'fa-check'],
                    'reimputee' => ['ok', 'a réimputé', 'fa-share'],
                    'en_cours' => [$retard ? 'late' : 'cur', 'pris en charge le '.self::date($d->pris_en_charge_le), $retard ? 'fa-triangle-exclamation' : 'fa-user'],
                    'a_prendre_en_charge' => [$retard ? 'late' : 'cur', 'à prendre en charge', $retard ? 'fa-triangle-exclamation' : 'fa-hand-holding'],
                    default => ['off', 'annulée', 'fa-rotate-left'],
                };

                $enfants = $d->diligencesEnfants()->with('responsable')->orderBy('id')->get();
                $renvoi = $enfants->where('statut', 'annule')->last();
                $note = null;
                if ($renvoi) {
                    $note = (str_starts_with((string) $renvoi->motif_annulation, 'Renvoy') ? 'Renvoi reçu de ' : 'Annulée : ').($renvoi->responsable?->nomComplet() ?? $renvoi->destinataire_libelle)
                        .' — '.Str::limit(Str::after((string) $renvoi->motif_annulation, ' : '), 70);
                }
                $suivants = $enfants->where('statut', '!=', 'annule');
                $etapes[] = self::etape($etat, $niveau === 0 ? 'Chef de file' : 'Réimputé à', $nom, $detail, $icone, $unite, $note, max(0, $suivants->count() - 1));

                $d = $suivants->first(fn ($e) => in_array($e->statut, ['a_prendre_en_charge', 'en_cours', 'reimputee'], true)) ?? $suivants->first();
                $niveau++;
            }

            if ($racine->statut === 'annule') {
                $etapes[] = self::etape('cur', 'À ré-imputer', 'Direction Générale', 'dossier renvoyé', 'fa-paper-plane');
                $message = ['texte' => "Le dossier a été renvoyé à l'imputeur : il attend une nouvelle imputation.", 'couleur' => '#C77D0A', 'icone' => 'fa-rotate-left'];
            } else {
                $suivi = $racine->suivi();
                $bout = $suivi['bout'];
                if ($bout->estOuverte()) {
                    $echeance = $suivi['echeance'];
                    $depuis = $bout->pris_en_charge_le ?? $bout->created_at;
                    $nomBout = $bout->responsable?->nomComplet() ?? $bout->destinataire_libelle;
                    $detenteur = [
                        'nom' => $nomBout,
                        'role' => $bout->parent_diligence_id ? 'Réimputé' : 'Chef de file',
                        'unite' => $bout->responsable && self::different($bout->destinataire_libelle, $nomBout) ? $bout->destinataire_libelle : null,
                        'initiales' => $bout->responsable?->initiales() ?? '?',
                        'depuis' => self::date($depuis),
                        'duree' => $depuis ? Carbon::parse($depuis)->locale('fr')->diffForHumans(null, true) : null,
                        'retard' => $bout->estEnRetard(),
                        'a_prendre' => $bout->statut === 'a_prendre_en_charge',
                    ];
                    $avancement = $bout->avancementActions();
                } elseif ($courrier->statut === 'archive') {
                    $message = ['texte' => 'Courrier classé'.($courrier->numero_archives ? ' · n° '.$courrier->numero_archives : '').'.', 'couleur' => '#2E7D5B', 'icone' => 'fa-box-archive'];
                } else {
                    $message = ['texte' => 'Toutes les diligences sont traitées.', 'couleur' => '#2E7D5B', 'icone' => 'fa-circle-check'];
                }
            }
        }

        // Associés pour avis ou pour copie
        $associes = $racines->whereIn('role', ['avis', 'copie'])->map(fn ($a) => [
            'nom' => $a->responsable?->nomComplet() ?? $a->destinataire_libelle, 'role' => $a->libelleRole(), 'statut' => $a->libelleStatut(), 'couleur' => $a->couleurStatut(),
        ])->values()->all();

        // Circuit de la réponse
        $reponse = null;
        if ($racine || $courrier->reponses()->exists()) {
            $rep = $courrier->reponses()->where('statut', '!=', 'annulee')->with(['visas.personnel', 'depart'])->latest('id')->first();
            $r = [];
            if (! $rep) {
                $r[] = self::etape('next', 'Projet de réponse', null, 'à rédiger', 'fa-pencil');
                $r[] = self::etape('nx', 'Visas', 'chaîne hiérarchique', null, 'fa-clock');
                $r[] = self::etape('nx', 'Signature', 'Directeur Général', null, 'fa-signature');
            } else {
                $r[] = self::etape(match ($rep->statut) {
                    'brouillon' => 'cur', 'a_corriger' => 'late', default => 'ok',
                }, 'Projet de réponse', null, match ($rep->statut) {
                    'brouillon' => 'en rédaction', 'a_corriger' => 'à corriger', default => 'soumis'
                }, $rep->statut === 'a_corriger' ? 'fa-triangle-exclamation' : ($rep->statut === 'brouillon' ? 'fa-pencil' : 'fa-check'));
                $visas = $rep->visasDuCycle();
                $premierAttente = $visas->firstWhere('statut', 'en_attente');
                foreach ($visas as $v) {
                    $etat = match ($v->statut) {
                        'vise' => 'ok', 'refuse' => 'late', 'annule' => 'off',
                        default => ($premierAttente && $premierAttente->id === $v->id && $rep->statut === 'en_visa') ? 'cur' : 'nx',
                    };
                    $r[] = self::etape($etat, $v->signataire ? 'Signature' : 'Visa', $v->personnel?->nomComplet() ?? $v->libelle,
                        $v->statut === 'vise' ? self::date($v->decide_le) : ($v->statut === 'refuse' ? 'refusé' : ($etat === 'cur' && $rep->annulation_demandee_le ? 'suspendu · annulation demandée' : null)), $v->statut === 'vise' ? 'fa-check' : ($v->statut === 'refuse' ? 'fa-xmark' : ($etat === 'cur' ? 'fa-user' : 'fa-clock')));
                }
                if ($visas->isEmpty()) {
                    $r[] = self::etape($rep->statut === 'signee' ? 'ok' : 'nx', 'Signature', 'Directeur Général', null, $rep->statut === 'signee' ? 'fa-check' : 'fa-clock');
                }
            }
            $depart = $rep?->depart;
            $r[] = self::etape($depart ? 'ok' : 'nx', 'Départ', null, $depart ? ($depart->numero ?? 'expédié') : null, $depart ? 'fa-check' : 'fa-paper-plane');
            $r[] = self::etape($courrier->statut === 'archive' ? 'ok' : 'nx', 'Classement', null, $courrier->numero_archives ? 'n° '.$courrier->numero_archives : null, $courrier->statut === 'archive' ? 'fa-check' : 'fa-box-archive');
            $reponse = $r;
        }

        $jours = $echeance ? (int) today()->diffInDays($echeance, false) : null;

        return [
            'statut' => $courrier->libelleStatut(), 'statut_couleur' => $courrier->couleurStatut(),
            'echeance' => $echeance,
            'echeance_texte' => $jours === null ? null : ($jours < 0 ? 'en retard de '.abs($jours).' jour'.(abs($jours) > 1 ? 's' : '') : ($jours === 0 ? "aujourd'hui" : 'dans '.$jours.' jour'.($jours > 1 ? 's' : ''))),
            'echeance_couleur' => $jours === null ? '#2B6CB0' : ($jours < 0 ? '#A73B34' : ($jours <= 2 ? '#C77D0A' : '#2B6CB0')),
            'detenteur' => $detenteur, 'message' => $message, 'etapes' => $etapes, 'associes' => $associes, 'reponse' => $reponse, 'avancement' => $avancement,
        ];
    }
}
