<?php

namespace App\Notifications;

use App\Models\TacheJustification;
use App\Support\JustificationRetard;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

/**
 * Cloche de l'application : demande de justification / de report reçue (décideur) ou décision rendue (demandeur).
 * Canal "database" uniquement. Le champ "lien" dit où ouvrir la notification (NotificationController::ouvrir).
 */
class JustificationNotification extends Notification
{
    public const DEMANDE = 'justification_demande';
    public const DECISION = 'justification_decision';

    public function __construct(public TacheJustification $justification, public string $evenement)
    {
        $this->locale('fr');
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $j = $this->justification->loadMissing('tache', 'demandeur.personnel', 'decideur.personnel');
        $tache = $j->tache;
        $report = $j->estReport();
        $objet = $report ? 'report' : 'justification';
        $cible = "la tâche {$tache->code} « ".Str::limit($tache->libelle, 70).' »';
        $date = fn ($d) => $d?->format('d/m/Y') ?? '—';
        $nom = fn ($user) => $user?->personnel?->nomComplet() ?: ($user?->name ?: 'Un agent');

        if ($this->evenement === self::DEMANDE) {
            $titre = $report ? 'Demande de report à valider' : 'Justification de retard à valider';
            $message = $report
                ? "{$nom($j->demandeur)} demande un report pour {$cible} : échéance du {$date($j->echeance_actuelle)} au {$date($j->echeance_proposee)}."
                : "{$nom($j->demandeur)} justifie le retard de {$cible} (motif : ".(JustificationRetard::MOTIFS[$j->motif] ?? $j->motif).').';
            $lien = route('feuille_de_route.justification.index');
        } else {
            $approuvee = $j->statut === TacheJustification::APPROUVEE;
            $titre = $approuvee ? 'Demande approuvée' : 'Demande refusée';
            $message = "Votre demande de {$objet} pour {$cible} a été ".($approuvee ? 'approuvée' : 'refusée')." par {$nom($j->decideur)}."
                .($approuvee && $report ? " Nouvelle échéance : {$date($j->echeance_proposee)}." : '')
                .(! $approuvee && $j->decision_commentaire ? ' Motif : '.Str::limit($j->decision_commentaire, 160) : '');
            $lien = route('feuille_de_route.tache.details', $tache->id);
        }

        return [
            'type' => $this->evenement,
            'justification_id' => $j->id,
            'tache_id' => $tache->id,
            'code' => $tache->code,
            'libelle' => Str::limit($tache->libelle, 120),
            'titre' => $titre,
            'message' => $message,
            'ton' => 'justification', // inconnu des tons d'alerte : bordure neutre et badge "Info"
            'lien' => $lien,
        ];
    }
}
