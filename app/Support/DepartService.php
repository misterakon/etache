<?php

namespace App\Support;

use App\Models\Courrier;
use App\Models\CourrierDepart;
use App\Models\ReponseCourrier;
use App\Models\User;
use DomainException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/** Création des courriers départ : un numéro D26-0001 par destinataire, objet saisi une seule fois. */
class DepartService
{
    /**
     * @param  array{objet: string, date_depart: string, mode_envoi?: ?string, mode_envoi_precision?: ?string, nombre_pieces?: int, confidentialite?: ?string, observations?: ?string, courrier_id?: ?int, reponse_id?: ?int, diligence_id?: ?int}  $donnees
     * @param  list<array{nom: string, precision?: ?string}>  $destinataires
     * @return list<CourrierDepart>
     */
    public static function creer(array $donnees, array $destinataires, User $user, ?UploadedFile $scan = null): array
    {
        if ($destinataires === []) {
            throw new DomainException('Indiquez au moins un destinataire.');
        }

        $groupe = (string) Str::uuid();
        $annee = (int) now()->year;

        $departs = DB::transaction(function () use ($donnees, $destinataires, $user, $scan, $groupe, $annee) {
            $liste = [];
            foreach ($destinataires as $destinataire) {
                [$numero, $sequence] = NumerotationCourrier::prochain('depart', $annee);
                $liste[] = CourrierDepart::create([
                    'groupe' => $groupe, 'numero' => $numero, 'annee' => $annee, 'sequence' => $sequence,
                    'courrier_id' => $donnees['courrier_id'] ?? null, 'reponse_id' => $donnees['reponse_id'] ?? null, 'diligence_id' => $donnees['diligence_id'] ?? null,
                    'destinataire' => trim($destinataire['nom']), 'destinataire_precision' => $destinataire['precision'] ?? null,
                    'objet' => trim($donnees['objet']), 'date_depart' => $donnees['date_depart'],
                    'mode_envoi' => $donnees['mode_envoi'] ?? null, 'mode_envoi_precision' => ($donnees['mode_envoi'] ?? null) === 'autre' ? ($donnees['mode_envoi_precision'] ?? null) : null,
                    'nombre_pieces' => (int) ($donnees['nombre_pieces'] ?? 0), 'confidentialite' => $donnees['confidentialite'] ?? Courrier::NORMAL,
                    'observations' => $donnees['observations'] ?? null, 'statut' => 'enregistre', 'enregistre_par' => $user->id,
                ]);
            }
            if ($scan) {
                FichiersCourrier::deposer($liste[0], $scan, 'scan', ['pdf', 'jpg', 'jpeg', 'png'], $user, 'courriers/departs');
            }

            return $liste;
        });

        $origine = ! empty($donnees['courrier_id']) ? Courrier::find($donnees['courrier_id']) : null;
        foreach ($departs as $depart) {
            Journal::noter('courrier', 'depart', "Courrier départ {$depart->numero} enregistré pour {$depart->destinataire} : {$depart->objet}", $depart, $origine, ['numero' => $depart->numero, 'groupe' => $groupe]);
        }

        return $departs;
    }

    /** Départ créé à partir d'une réponse signée : adressé à l'expéditeur du courrier d'origine. */
    public static function depuisReponse(ReponseCourrier $reponse, User $user, ?array $surcharge = null): array
    {
        if ($reponse->depart()->exists()) {
            return [$reponse->depart];
        }
        $reponse->loadMissing('courrier');
        $courrier = $reponse->courrier;

        return self::creer(($surcharge ?? []) + [
            'objet' => $reponse->objet, 'date_depart' => now()->toDateString(), 'nombre_pieces' => 1,
            'confidentialite' => $courrier->confidentialite, 'courrier_id' => $courrier->id, 'reponse_id' => $reponse->id, 'diligence_id' => $reponse->diligence_id,
        ], [['nom' => $courrier->expediteur]], $user);
    }
}
