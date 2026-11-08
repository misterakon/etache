<?php

namespace App\Support;

use App\Models\Courrier;
use App\Models\DelaiCourrier;
use App\Models\Diligence;
use App\Models\Imputation;
use App\Models\ImputationDestinataire;
use App\Models\PosteImputation;
use App\Models\User;
use App\Notifications\DiligenceNotification;
use Carbon\Carbon;
use DomainException;
use Illuminate\Support\Facades\DB;

/**
 * Imputation d'un courrier : chaque destinataire reçoit une diligence (numérotée DL26-0001...), notifiée et à prendre en charge.
 * Quand l'imputation vise directement un agent, son supérieur hiérarchique est informé (réglable).
 */
class ImputationCourrier
{
    /**
     * @param  list<array{type: string, id: int, role: string}>  $destinataires
     * @param  list<int>  $instructionIds
     * @return array{imputation: Imputation, diligences: list<Diligence>, sans_responsable: list<string>}
     *
     * @throws DomainException
     */
    public static function imputer(Courrier $courrier, User $auteur, array $destinataires, ?DelaiCourrier $delai, Carbon $echeance, Carbon $dateImputation, array $instructionIds, ?string $particulieres, ?string $observations, ?Diligence $diligenceParente = null): array
    {
        $sansResponsable = [];
        $destinatairesResolus = [];
        foreach ($destinataires as $destinataire) {
            if ($destinataire['type'] === 'poste' && ! PosteImputation::whereKey($destinataire['id'])->where('actif', true)->whereNotNull('niveau')->exists()) {
                throw new DomainException('Ce poste destinataire est désactivé ou ne correspond plus à l’organigramme.');
            }
            $cible = DestinatairesImputation::resoudre($destinataire['type'], $destinataire['id']);
            if (! $cible) {
                throw new DomainException('Un destinataire sélectionné est introuvable ou inactif.');
            }
            if (! $cible['responsable']) {
                $sansResponsable[] = $cible['libelle'];
            }
            $destinatairesResolus[$destinataire['type'].':'.$destinataire['id']] = $destinataire + ['cible' => $cible];
        }

        $resultat = DB::transaction(function () use ($courrier, $auteur, $destinatairesResolus, $delai, $echeance, $dateImputation, $instructionIds, $particulieres, $observations, $diligenceParente, &$sansResponsable) {
            $imputation = Imputation::create([
                'courrier_id' => $courrier->id, 'parent_diligence_id' => $diligenceParente?->id, 'imputee_par' => $auteur->id, 'date_imputation' => $dateImputation,
                'delai_id' => $delai?->id, 'delai_libelle' => $delai?->libelle, 'echeance' => $echeance,
                'instructions_particulieres' => $particulieres, 'observations' => $observations,
            ]);
            $imputation->instructions()->sync($instructionIds);

            $diligences = [];
            $diligencesParDestinataire = [];
            foreach ($destinatairesResolus as $cle => $d) {
                if ($d['role'] !== 'principal') {
                    continue;
                }
                [$code] = NumerotationCourrier::prochain('diligence', (int) now()->year);
                $diligence = Diligence::create([
                    'code' => $code, 'courrier_id' => $courrier->id, 'imputation_id' => $imputation->id,
                    'parent_diligence_id' => $diligenceParente?->id,
                    'destinataire_type' => $d['type'], 'destinataire_id' => $d['id'], 'destinataire_libelle' => $d['cible']['libelle'],
                    'responsable_personnel_id' => $d['cible']['responsable']?->id, 'role' => 'principal',
                    'echeance' => $echeance, 'statut' => 'a_prendre_en_charge',
                ]);
                $diligences[] = $diligence;
                $diligencesParDestinataire[$cle] = $diligence;
            }

            foreach ($destinatairesResolus as $cle => $d) {
                if ($d['role'] === 'principal') {
                    continue;
                }
                $diligenceAssociee = null;
                if ($d['role'] === 'avis') {
                    $diligenceAssociee = $diligencesParDestinataire[$d['associe_a'] ?? ''] ?? null;
                    if (! $diligenceAssociee) {
                        throw new DomainException('Chaque destinataire en avis/aide doit être associé à une diligence principale.');
                    }
                }
                ImputationDestinataire::create([
                    'imputation_id' => $imputation->id, 'diligence_id' => $diligenceAssociee?->id,
                    'destinataire_type' => $d['type'], 'destinataire_id' => $d['id'],
                    'destinataire_libelle' => $d['cible']['libelle'], 'role' => $d['role'],
                ]);
            }

            if ($diligenceParente) {
                $diligenceParente->update(['statut' => 'reimputee']);
            }

            if ($courrier->statut === 'enregistre') {
                $courrier->update(['statut' => 'impute']);
            }

            return ['imputation' => $imputation, 'diligences' => $diligences];
        });

        self::notifier($resultat['diligences'], $auteur);

        $resume = collect($resultat['diligences'])->map(fn ($dl) => $dl->destinataire_libelle.' ('.$dl->libelleRole().')')->implode(', ');
        Journal::noter('courrier', 'imputation', "Courrier {$courrier->numero} imputé : {$resume} — échéance ".$echeance->format('d/m/Y'), $resultat['imputation'], $courrier, [
            'diligences' => collect($resultat['diligences'])->pluck('code')->all(), 'delai' => $delai?->libelle, 'echeance' => $echeance->toDateString(),
        ]);

        return $resultat + ['sans_responsable' => $sansResponsable];
    }

    /** « Archiver » / « Pour information » seuls : courrier classé, sans diligence ; les destinataires sont informés (copie). */
    public static function classer(Courrier $courrier, User $auteur, array $destinataires, Carbon $date, array $instructionIds, ?string $particulieres, ?string $observations): Imputation
    {
        $imputation = DB::transaction(function () use ($courrier, $auteur, $destinataires, $date, $instructionIds, $particulieres, $observations) {
            $imputation = Imputation::create([
                'courrier_id' => $courrier->id, 'imputee_par' => $auteur->id, 'date_imputation' => $date, 'echeance' => $date,
                'instructions_particulieres' => $particulieres, 'observations' => $observations,
            ]);
            $imputation->instructions()->sync($instructionIds);
            foreach (array_unique($destinataires) as $cle) {
                [$type, $id] = explode(':', $cle);
                $cible = DestinatairesImputation::resoudre($type, (int) $id);
                if (! $cible) {
                    throw new DomainException('Un destinataire sélectionné est introuvable ou inactif.');
                }
                ImputationDestinataire::create([
                    'imputation_id' => $imputation->id, 'diligence_id' => null, 'destinataire_type' => $type, 'destinataire_id' => (int) $id,
                    'destinataire_libelle' => $cible['libelle'], 'role' => 'copie',
                ]);
            }
            $courrier->update(['statut' => 'archive']);

            return $imputation;
        });
        Journal::noter('courrier', 'classement', "Courrier {$courrier->numero} classé sur instruction du DG (aucune diligence)", $imputation, $courrier);

        return $imputation;
    }

    /** Cloche : le responsable de chaque diligence, puis le supérieur hiérarchique en cas d'imputation directe à un agent. */
    private static function notifier(array $diligences, User $auteur): void
    {
        $informer = Reglages::get('courrier.informer_superieur');

        foreach ($diligences as $diligence) {
            try {
                $deja = [];
                if ($diligence->responsable_personnel_id) {
                    $deja[] = $diligence->responsable_personnel_id;
                    foreach (User::where('actif', true)->where('personnel_id', $diligence->responsable_personnel_id)->where('id', '!=', $auteur->id)->get() as $user) {
                        $user->notify(new DiligenceNotification($diligence, DiligenceNotification::IMPUTATION));
                    }
                }

                if ($informer && $diligence->destinataire_type === 'agent' && $diligence->responsable) {
                    foreach (DestinatairesImputation::superieursDe($diligence->responsable) as $superieur) {
                        if (in_array($superieur->id, $deja, true)) {
                            continue;
                        }
                        $deja[] = $superieur->id;
                        foreach (User::where('actif', true)->where('personnel_id', $superieur->id)->where('id', '!=', $auteur->id)->get() as $user) {
                            $user->notify(new DiligenceNotification($diligence, DiligenceNotification::INFORMATION));
                        }
                    }
                }

                foreach (ImputationDestinataire::where('imputation_id', $diligence->imputation_id)->get() as $associe) {
                    $p = DestinatairesImputation::resoudre($associe->destinataire_type, (int) $associe->destinataire_id)['responsable'] ?? null;
                    if (! $p || in_array($p->id, $deja, true)) {
                        continue;
                    }
                    $deja[] = $p->id;
                    foreach (User::where('actif', true)->where('personnel_id', $p->id)->where('id', '!=', $auteur->id)->get() as $user) {
                        $user->notify(new DiligenceNotification($diligence, DiligenceNotification::INFORMATION));
                    }
                }

                // Associés (avis, copie) : informés eux aussi
                foreach (ImputationDestinataire::where('imputation_id', $diligence->imputation_id)->get() as $associe) {
                    $p = DestinatairesImputation::resoudre($associe->destinataire_type, (int) $associe->destinataire_id)['responsable'] ?? null;
                    if (! $p || in_array($p->id, $deja, true)) {
                        continue;
                    }
                    $deja[] = $p->id;
                    foreach (User::where('actif', true)->where('personnel_id', $p->id)->where('id', '!=', $auteur->id)->get() as $user) {
                        $user->notify(new DiligenceNotification($diligence, DiligenceNotification::INFORMATION));
                    }
                }
            } catch (\Throwable $e) {
                report($e); // la cloche ne doit jamais bloquer l'imputation
            }
        }
    }
}
