<?php

namespace App\Support;

use App\Models\Axe;
use App\Models\Direction;
use App\Models\Exercice;
use App\Models\Personnel;
use App\Models\Priorite;
use App\Models\RoleAffectation;
use App\Models\SituationTemporelle;
use App\Models\StatutExecution;

/**
 * Données de la base nécessaires à l'analyse d'un import (lues une seule fois par analyse).
 */
class ReferentielImport
{
    /** Directions ayant un code : seules celles-ci peuvent être citées dans le fichier. */
    public static function directionsAvecCode(): array
    {
        return Direction::whereNotNull('code')->where('code', '!=', '')->orderBy('nom')->get(['code', 'nom'])
            ->map(fn ($d) => ['code' => $d->code, 'nom' => $d->nom])->all();
    }

    public static function directionsSansCode(): array
    {
        return Direction::where(fn ($q) => $q->whereNull('code')->orWhere('code', ''))->orderBy('nom')->pluck('nom')->all();
    }

    public static function statuts(): array
    {
        return StatutExecution::where('actif', true)->orderBy('code')->pluck('libelle')->all();
    }

    public static function priorites(): array
    {
        return Priorite::where('actif', true)->orderBy('libelle')->pluck('libelle')->all();
    }

    public static function construire(Exercice $exercice): array
    {
        // --- Agents : matricule (unique) et email (non unique : plusieurs agents possibles) ---
        $agents = ['par_matricule' => [], 'par_email' => [], 'actifs' => [], 'par_matricule_sans_zero' => []];
        $sansZero = [];
        foreach (Personnel::get(['id', 'matricule', 'email', 'actif']) as $p) {
            $agents['actifs'][$p->id] = (bool) $p->actif;
            if (filled($p->matricule)) {
                $cle = ImportMatrice::norm($p->matricule);
                $agents['par_matricule'][$cle] = $p->id;
                if (ctype_digit($cle)) {
                    $sansZero[ltrim($cle, '0')][] = $p->id;
                }
            }
            if (filled($p->email)) {
                $agents['par_email'][mb_strtolower(trim($p->email))][] = $p->id;
            }
        }
        foreach ($sansZero as $cle => $ids) {
            if (count($ids) === 1) { // ambigu s'il y a plusieurs matricules équivalents : on ne devine pas
                $agents['par_matricule_sans_zero'][$cle] = $ids[0];
            }
        }

        // --- Directions (par code), priorités et statuts (par libellé) ---
        $directions = [];
        foreach (Direction::whereNotNull('code')->where('code', '!=', '')->get(['id', 'code']) as $d) {
            $directions[ImportMatrice::norm($d->code)] = $d->id;
        }

        $priorites = [];
        foreach (Priorite::where('actif', true)->get(['id', 'libelle']) as $p) {
            $priorites[ImportMatrice::norm($p->libelle)] = $p->id;
        }

        $parLibelle = [];
        $parCode = [];
        foreach (StatutExecution::where('actif', true)->get(['id', 'code', 'libelle']) as $s) {
            $parLibelle[ImportMatrice::norm($s->libelle)] = $s->id;
            $parCode[$s->code] = $s->id;
        }

        // --- Structure déjà présente dans l'exercice (pour ne rien recréer) ---
        $existant = ['axes' => [], 'axes_autres_exercices' => []];
        $axes = Axe::where('exercice_id', $exercice->id)
            ->with('actionsCles.actionsMesurables.taches:id,action_mesurable_id,libelle')->get();
        foreach ($axes as $axe) {
            $acs = [];
            foreach ($axe->actionsCles as $ac) {
                if (! filled($ac->code)) {
                    continue; // sans code : impossible à rapprocher
                }
                $ams = [];
                foreach ($ac->actionsMesurables as $am) {
                    $taches = [];
                    foreach ($am->taches as $t) {
                        $taches[ImportMatrice::norm($t->libelle)] = $t->id;
                    }
                    $ams[ImportMatrice::norm($am->libelle)] = ['id' => $am->id, 'taches' => $taches];
                }
                $acs[ImportMatrice::norm($ac->code)] = ['id' => $ac->id, 'libelle' => $ac->libelle, 'ams' => $ams];
            }
            $existant['axes'][ImportMatrice::norm($axe->libelle)] = ['id' => $axe->id, 'acs' => $acs];
        }
        foreach (Axe::where('exercice_id', '!=', $exercice->id)->pluck('libelle') as $libelle) {
            $existant['axes_autres_exercices'][ImportMatrice::norm($libelle)] = true;
        }

        return [
            'agents' => $agents,
            'directions' => $directions,
            'priorites' => $priorites,
            'statuts' => [
                'par_libelle' => $parLibelle,
                'par_role' => [
                    'non_demarre' => $parCode[StatutExecution::NON_DEMARREE] ?? null,
                    'en_cours' => $parCode[StatutExecution::EN_COURS] ?? null,
                    'termine' => $parCode[StatutExecution::TERMINEE] ?? null,
                ],
            ],
            'existant' => $existant,
            'mode_avancement' => $exercice->mode_avancement, // exercice "par sous-tâches" : l'avancement du fichier est ignoré
        ];
    }

    /** Identifiants nécessaires à la création des tâches et de leurs affectations. */
    public static function idsCreation(): array
    {
        $roles = RoleAffectation::pluck('id', 'code');

        return [
            'responsable' => $roles[RoleAffectation::RESPONSABLE] ?? null,
            'pilote' => $roles[RoleAffectation::PILOTE] ?? null,
            'contributeur' => $roles[RoleAffectation::CONTRIBUTEUR] ?? null,
            'situation_normale' => SituationTemporelle::where('code', SituationTemporelle::NORMAL)->value('id'),
        ];
    }
}
