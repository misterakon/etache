<?php

namespace App\Support;

use App\Models\Diligence;
use App\Models\MandatResponsable;
use App\Models\ReponseCourrier;
use App\Models\ReponseVisa;
use App\Models\User;
use App\Notifications\DiligenceNotification;
use DomainException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

/**
 * Réponse à un courrier : brouillon avec projet joint, circuit de visas qui monte la hiérarchie,
 * puis signature du Directeur Général (signature scannée déposée). Chaque étape est tracée dans le journal.
 */
class ReponseWorkflow
{
    public const EXTENSIONS_PROJET = ['pdf', 'doc', 'docx', 'odt'];

    public const EXTENSIONS_SIGNATURE = ['pdf', 'jpg', 'jpeg', 'png'];

    /** Crée un brouillon ou modifie un brouillon / une réponse à corriger. */
    public static function enregistrer(Diligence $diligence, ?ReponseCourrier $reponse, array $donnees, ?UploadedFile $projet, User $user): ReponseCourrier
    {
        if ($reponse && ! $reponse->estModifiable()) {
            throw new DomainException("Cette réponse n'est plus modifiable.");
        }

        $reponse = DB::transaction(function () use ($diligence, $reponse, $donnees, $projet, $user) {
            if ($reponse) {
                $reponse->update(['objet' => trim($donnees['objet']), 'commentaire' => $donnees['commentaire'] ?? null]);
            } else {
                $reponse = ReponseCourrier::create([
                    'courrier_id' => $diligence->courrier_id, 'diligence_id' => $diligence->id, 'objet' => trim($donnees['objet']),
                    'commentaire' => $donnees['commentaire'] ?? null, 'statut' => 'brouillon', 'cycle' => 1, 'cree_par' => $user->id,
                ]);
            }
            if ($projet) {
                $piece = FichiersCourrier::deposer($reponse, $projet, 'projet', self::EXTENSIONS_PROJET, $user, 'courriers/reponses');
                Journal::noter('courrier', 'reponse', "Projet de réponse déposé (v{$piece->version}) : {$piece->nom_original}", $piece, $diligence->courrier);
            }

            return $reponse;
        });
        $reponse->unsetRelation('fichiers');

        return $reponse;
    }

    /** Circuit de visa : la hiérarchie du responsable, le Directeur Général signant en dernier. */
    public static function circuit(Diligence $diligence): array
    {
        $dg = MandatResponsable::directeurGeneral();
        if (! $dg) {
            throw new DomainException('Aucun Directeur Général en poste : impossible de constituer le circuit de signature.');
        }
        $etapeDg = ['personnel' => $dg, 'libelle' => 'Directeur Général'];

        $chaine = ($diligence->responsable && Reglages::get('courrier.reponse_visa_chaine'))
            ? ChaineHierarchique::auDessus($diligence->responsable)
            : [];
        if ($chaine === [] || (int) end($chaine)['personnel']->id !== (int) $dg->id) {
            $chaine[] = $etapeDg;
        }

        $dernier = array_key_last($chaine);
        foreach ($chaine as $i => $etape) {
            $chaine[$i]['signataire'] = $i === $dernier;
        }

        return $chaine;
    }

    public static function soumettre(ReponseCourrier $reponse, User $user): void
    {
        if (! $reponse->estModifiable()) {
            throw new DomainException('Cette réponse est déjà dans le circuit ou terminée.');
        }
        $reponse->load('diligence.responsable', 'diligence.courrier', 'fichiers');
        if (! $reponse->projet()) {
            throw new DomainException('Joignez le projet de réponse avant de le soumettre au visa.');
        }
        $etapes = self::circuit($reponse->diligence);

        DB::transaction(function () use ($reponse, $etapes) {
            $cycle = $reponse->statut === 'a_corriger' ? $reponse->cycle + 1 : $reponse->cycle;
            foreach ($etapes as $i => $etape) {
                ReponseVisa::create([
                    'reponse_id' => $reponse->id, 'cycle' => $cycle, 'ordre' => $i + 1, 'personnel_id' => $etape['personnel']->id,
                    'libelle' => $etape['libelle'], 'signataire' => $etape['signataire'], 'statut' => 'en_attente',
                ]);
            }
            $reponse->update(['statut' => 'en_visa', 'cycle' => $cycle, 'soumis_le' => now()]);
        });

        $d = $reponse->diligence;
        Journal::noter('courrier', 'reponse', "Réponse au courrier {$d->courrier->numero} soumise au circuit de visa ({$reponse->objet}) : ".count($etapes).' étape(s)', $reponse, $d->courrier);
        $premiere = $etapes[0];
        NotifDiligence::versPersonnel($premiere['personnel']->id, $d, $premiere['signataire'] ? DiligenceNotification::REPONSE_A_SIGNER : DiligenceNotification::REPONSE_A_VISER, null, $user->id);
    }

    public static function viser(ReponseVisa $visa, User $user, ?string $commentaire): void
    {
        self::verifier($visa, $user, false);
        $reponse = $visa->reponse;
        $visa->update(['statut' => 'vise', 'decide_par' => $user->id, 'decide_le' => now(), 'commentaire' => $commentaire]);

        $d = $reponse->diligence()->with('courrier')->first();
        Journal::noter('courrier', 'reponse', "Réponse au courrier {$d->courrier->numero} visée par {$visa->libelle}", $reponse, $d->courrier, ['visa' => $visa->id]);

        $reponse->load('visas');
        if ($suivant = $reponse->visaCourant()) {
            NotifDiligence::versPersonnel($suivant->personnel_id, $d, $suivant->signataire ? DiligenceNotification::REPONSE_A_SIGNER : DiligenceNotification::REPONSE_A_VISER, null, $user->id);
        }
    }

    public static function refuser(ReponseVisa $visa, User $user, string $motif): void
    {
        self::verifier($visa, $user, null);
        $reponse = $visa->reponse;

        DB::transaction(function () use ($visa, $reponse, $user, $motif) {
            $visa->update(['statut' => 'refuse', 'decide_par' => $user->id, 'decide_le' => now(), 'commentaire' => $motif]);
            ReponseVisa::where('reponse_id', $reponse->id)->where('cycle', $reponse->cycle)->where('statut', 'en_attente')->update(['statut' => 'annule']);
            $reponse->update(['statut' => 'a_corriger']);
        });

        $d = $reponse->diligence()->with('courrier')->first();
        Journal::noter('courrier', 'reponse', "Réponse au courrier {$d->courrier->numero} refusée par {$visa->libelle} : {$motif}", $reponse, $d->courrier, ['motif' => $motif]);
        NotifDiligence::versPersonnel($d->responsable_personnel_id, $d, DiligenceNotification::REPONSE_REFUSEE, $motif, $user->id);
        NotifDiligence::versUtilisateur($reponse->cree_par, $d, DiligenceNotification::REPONSE_REFUSEE, $motif, $user->id);
    }

    /** Signature du Directeur Général : la réponse signée et scannée est déposée, puis le départ est éventuellement créé. */
    public static function signer(ReponseVisa $visa, User $user, UploadedFile $scan): array
    {
        self::verifier($visa, $user, true);
        $reponse = $visa->reponse;

        DB::transaction(function () use ($visa, $reponse, $user, $scan) {
            FichiersCourrier::deposer($reponse, $scan, 'signature', self::EXTENSIONS_SIGNATURE, $user, 'courriers/reponses');
            $visa->update(['statut' => 'vise', 'decide_par' => $user->id, 'decide_le' => now()]);
            $reponse->update(['statut' => 'signee', 'signe_le' => now(), 'signe_par' => $user->id]);
        });
        $reponse->unsetRelation('fichiers');

        $d = $reponse->diligence()->with('courrier')->first();
        Journal::noter('courrier', 'reponse', "Réponse au courrier {$d->courrier->numero} signée par {$visa->libelle} ({$reponse->objet})", $reponse, $d->courrier);
        NotifDiligence::versPersonnel($d->responsable_personnel_id, $d, DiligenceNotification::REPONSE_SIGNEE, null, $user->id);

        return Reglages::get('courrier.depart_auto') ? DepartService::depuisReponse($reponse, $user) : [];
    }

    /** Personnes déjà concernées par un circuit : celles qui ont visé, plus celle dont c'est le tour. */
    private static function concernes(ReponseCourrier $reponse): array
    {
        $reponse->loadMissing('visas');
        $ids = $reponse->visasDuCycle()->where('statut', 'vise')->pluck('personnel_id')->all();
        if ($courant = $reponse->visaCourant()) {
            $ids[] = $courant->personnel_id;
        }

        return array_values(array_unique(array_map('intval', $ids)));
    }

    /**
     * Annulation d'une réponse.
     *  - brouillon ou à corriger : immédiate ;
     *  - en circuit, personne n'a encore visé (ou demandeur = dernier visa, ADMIN, DG) : immédiate, avec motif ;
     *  - en circuit avec au moins un visa : demande soumise à l'accord du dernier acteur qui a visé ; le circuit est suspendu.
     *
     * @return string 'annulee' ou 'demandee'
     */
    public static function annuler(ReponseCourrier $reponse, User $user, ?string $motif = null): string
    {
        if (in_array($reponse->statut, ['signee', 'annulee'], true)) {
            throw new DomainException('Une réponse signée ou déjà annulée ne peut pas être annulée.');
        }
        if ($reponse->annulation_demandee_le) {
            throw new DomainException("Une demande d'annulation est déjà en cours pour cette réponse.");
        }

        if ($reponse->statut !== 'en_visa') { // brouillon ou à corriger : personne n'a encore été sollicité
            self::finaliserAnnulation($reponse, $user, null, []);

            return 'annulee';
        }

        $motif = trim((string) $motif);
        if (mb_strlen($motif) < 5) {
            throw new DomainException("Précisez le motif de l'annulation (5 caractères minimum).");
        }
        $reponse->loadMissing('visas.personnel', 'diligence.courrier');
        $concernes = self::concernes($reponse);
        $dernier = $reponse->visasDuCycle()->where('statut', 'vise')->sortBy('ordre')->last();
        $autorite = $user->hasRole('ADMIN|DG');
        $estDernier = $dernier && $user->personnel_id && (int) $dernier->personnel_id === (int) $user->personnel_id;

        if (! $dernier || $autorite || $estDernier) {
            self::finaliserAnnulation($reponse, $user, $motif, $concernes);

            return 'annulee';
        }

        // Au moins un visa donné : le dernier acteur décide, le circuit est suspendu
        $reponse->forceFill([
            'annulation_demandee_par' => $user->id, 'annulation_demandee_le' => now(), 'annulation_motif' => $motif, 'annulation_decideur_id' => $dernier->personnel_id,
        ])->save();
        $d = $reponse->diligence;
        $nomDernier = $dernier->personnel?->nomComplet() ?? $dernier->libelle;
        Journal::noter('courrier', 'reponse', "Annulation de la réponse au courrier {$d->courrier->numero} demandée par {$user->name}, soumise à l'accord de {$nomDernier} ({$dernier->libelle}) : {$motif}", $reponse, $d->courrier);

        NotifDiligence::versPersonnel($dernier->personnel_id, $d, DiligenceNotification::REPONSE_ANNULATION_DEMANDEE, $motif, $user->id);
        foreach ($concernes as $pid) {
            if ($pid === (int) $dernier->personnel_id || ($user->personnel_id && $pid === (int) $user->personnel_id)) {
                continue;
            }
            NotifDiligence::versPersonnel($pid, $d, DiligenceNotification::REPONSE_SUSPENDUE, "En attente de l'accord de {$nomDernier}.", $user->id);
        }

        return 'demandee';
    }

    /** Décision du dernier visa (ou de l'administrateur, ou du DG) sur une demande d'annulation. */
    public static function trancherAnnulation(ReponseCourrier $reponse, User $user, bool $accepte, ?string $motif = null): void
    {
        if ($reponse->statut !== 'en_visa' || ! $reponse->annulation_demandee_le) {
            throw new DomainException("Aucune demande d'annulation n'est en attente pour cette réponse.");
        }
        $estDecideur = $user->personnel_id && (int) $reponse->annulation_decideur_id === (int) $user->personnel_id;
        if (! $estDecideur && ! $user->hasRole('ADMIN|DG')) {
            throw new DomainException("Seule la personne qui a visé en dernier, l'administrateur ou le DG peut décider de cette annulation.");
        }
        $reponse->loadMissing('visas', 'diligence.courrier');
        $concernes = self::concernes($reponse);
        $courant = $reponse->visaCourant();
        $demandeur = $reponse->annulation_demandee_par ? (int) $reponse->annulation_demandee_par : null;

        if ($accepte) {
            self::finaliserAnnulation($reponse, $user, (string) $reponse->annulation_motif, $concernes, $demandeur);

            return;
        }

        $motif = trim((string) $motif);
        if (mb_strlen($motif) < 5) {
            throw new DomainException('Le motif du refus est obligatoire (5 caractères minimum).');
        }
        $reponse->forceFill(['annulation_demandee_par' => null, 'annulation_demandee_le' => null, 'annulation_motif' => null, 'annulation_decideur_id' => null])->save();
        $d = $reponse->diligence;
        Journal::noter('courrier', 'reponse', "Annulation de la réponse au courrier {$d->courrier->numero} refusée par {$user->name} : {$motif} — le circuit de visa reprend", $reponse, $d->courrier);

        if ($demandeur) {
            NotifDiligence::versUtilisateur($demandeur, $d, DiligenceNotification::REPONSE_ANNULATION_REFUSEE, $motif, $user->id);
        }
        if ($courant && ! ($user->personnel_id && (int) $courant->personnel_id === (int) $user->personnel_id)) {
            NotifDiligence::versPersonnel($courant->personnel_id, $d, DiligenceNotification::REPONSE_ANNULATION_REFUSEE, $motif, $user->id);
        }
    }

    private static function finaliserAnnulation(ReponseCourrier $reponse, User $user, ?string $motif, array $concernes, ?int $demandeurUserId = null): void
    {
        DB::transaction(function () use ($reponse) {
            ReponseVisa::where('reponse_id', $reponse->id)->where('cycle', $reponse->cycle)->where('statut', 'en_attente')->update(['statut' => 'annule']);
            $reponse->forceFill(['statut' => 'annulee', 'annulation_demandee_par' => null, 'annulation_demandee_le' => null, 'annulation_decideur_id' => null])->save();
        });
        $d = $reponse->diligence()->with('courrier')->first();
        Journal::noter('courrier', 'reponse', "Réponse au courrier {$d->courrier->numero} annulée ({$reponse->objet})".($motif ? " — motif : {$motif}" : ''), $reponse, $d->courrier);

        foreach ($concernes as $pid) {
            if ($user->personnel_id && $pid === (int) $user->personnel_id) {
                continue;
            }
            NotifDiligence::versPersonnel($pid, $d, DiligenceNotification::REPONSE_ANNULEE, $motif, $user->id);
        }
        if ($demandeurUserId && $demandeurUserId !== (int) $user->id) {
            NotifDiligence::versUtilisateur($demandeurUserId, $d, DiligenceNotification::REPONSE_ANNULEE, 'Votre demande d\'annulation est acceptée.', $user->id);
        }
    }

    /** Contrôle d'une étape : c'est bien son tour, c'est bien la bonne personne, et le bon type d'acte (visa ou signature). */
    private static function verifier(ReponseVisa $visa, User $user, ?bool $signature): void
    {
        $reponse = $visa->reponse()->with('visas')->first();
        $visa->setRelation('reponse', $reponse);

        if ($reponse->annulation_demandee_le) {
            throw new DomainException("Une demande d'annulation de cette réponse est en cours : le circuit est suspendu jusqu'à la décision.");
        }

        if ($reponse->statut !== 'en_visa' || (int) $visa->cycle !== (int) $reponse->cycle || $visa->statut !== 'en_attente') {
            throw new DomainException("Cette étape n'est plus en attente.");
        }
        if (($reponse->visaCourant()?->id) !== $visa->id) {
            throw new DomainException("Ce n'est pas encore votre tour : une étape précédente est en attente.");
        }
        if ($signature !== null && $visa->signataire !== $signature) {
            throw new DomainException($signature ? 'Cette étape est un visa : la signature intervient en fin de circuit.' : 'Cette étape est la signature : déposez la réponse signée.');
        }
        if (! $user->hasRole('ADMIN') && (int) $visa->personnel_id !== (int) $user->personnel_id) {
            throw new DomainException("Cette étape est réservée à : {$visa->libelle}.");
        }
    }
}
