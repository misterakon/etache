<?php

namespace App\Support;

use App\Models\Exercice;
use App\Models\InstantanePilotage;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/** Photo quotidienne des chiffres (global, par axe, par direction) : prise chaque soir par le planificateur, ou à la première visite du jour. */
class InstantanesPilotage
{
    public static function capturerSiNecessaire(): void
    {
        if (Cache::add('pilotage.instantane.'.today()->toDateString(), 1, now()->addHours(8))) {
            try {
                self::capturer();
            } catch (\Throwable $e) {
                report($e);
            }
        }
    }

    /** @return int nombre de lignes écrites (zéro si la photo du jour existe déjà) */
    public static function capturer(?Exercice $exercice = null): int
    {
        $n = 0;
        $jour = today()->toDateString();
        $exercices = $exercice ? collect([$exercice]) : Exercice::whereNull('cloture_le')->get();

        foreach ($exercices as $e) {
            $a = AnalysesPilotage::calculer($e, AnalysesPilotage::periode('annee', null, $e));
            $ligne = fn (array $r, ?int $axe, ?int $dir) => [
                'cle' => "{$jour}|{$e->id}|a".($axe ?? 0).'|d'.($dir ?? 0), 'jour' => $jour, 'exercice_id' => $e->id, 'axe_id' => $axe, 'direction_id' => $dir,
                'taches' => $r['total'], 'terminees' => $r['terminees'], 'en_cours' => $r['en_cours'], 'en_retard' => $r['en_retard'], 'non_justifies' => $r['non_justifies'],
                'avancement_moyen' => $r['avancement'], 'created_at' => now(), 'updated_at' => now(),
            ];
            $lignes = [$ligne($a['situation'], null, null)];
            foreach ($a['par_axe'] as $r) {
                $lignes[] = $ligne($r, $r['id'], null);
            }
            foreach ($a['par_direction'] as $r) {
                if ($r['id'] > 0) {
                    $lignes[] = $ligne($r, null, $r['id']);
                }
            }
            $n += DB::table('instantanes_pilotage')->insertOrIgnore($lignes);
        }

        return $n;
    }

    /** @return Collection<int, InstantanePilotage> */
    public static function serie(int $exerciceId, ?int $axeId, ?int $directionId): Collection
    {
        return InstantanePilotage::where('exercice_id', $exerciceId)->where('axe_id', $axeId)->where('direction_id', $directionId)->orderBy('jour')->get();
    }
}
