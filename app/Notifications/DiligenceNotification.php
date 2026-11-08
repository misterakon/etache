<?php

namespace App\Notifications;

use App\Models\Diligence;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

/**
 * Cloche de l'application pour le courrier : imputation, échéances, plan de traitement, délais, réponses.
 * Canal « database » uniquement. L'objet d'un courrier confidentiel ne figure jamais dans la notification.
 */
class DiligenceNotification extends Notification
{
    public const IMPUTATION = 'diligence_imputation';
    public const INFORMATION = 'diligence_information';
    public const ECHEANCE = 'diligence_echeance';
    public const RETARD = 'diligence_retard';
    public const ACTION_A_VALIDER = 'diligence_action_a_valider';
    public const ACTION_ASSIGNEE = 'diligence_action_assignee';
    public const ACTION_VALIDEE = 'diligence_action_validee';
    public const ACTION_REFUSEE = 'diligence_action_refusee';
    public const RENVOI = 'diligence_renvoi_imputeur';
    public const DELAI_DEMANDE = 'diligence_delai_demande';
    public const DELAI_ACCORDE = 'diligence_delai_accorde';
    public const DELAI_REFUSE = 'diligence_delai_refuse';
    public const REPONSE_A_VISER = 'diligence_reponse_a_viser';
    public const REPONSE_A_SIGNER = 'diligence_reponse_a_signer';
    public const REPONSE_REFUSEE = 'diligence_reponse_refusee';
    public const REPONSE_SIGNEE = 'diligence_reponse_signee';
    public const REPONSE_ANNULATION_DEMANDEE = 'diligence_reponse_annulation_demandee';
    public const REPONSE_SUSPENDUE = 'diligence_reponse_suspendue';
    public const REPONSE_ANNULEE = 'diligence_reponse_annulee';
    public const REPONSE_ANNULATION_REFUSEE = 'diligence_reponse_annulation_refusee';

    public function __construct(public Diligence $diligence, public string $evenement, public ?string $detail = null)
    {
        $this->locale('fr');
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $d = $this->diligence->loadMissing('courrier', 'responsable');
        $c = $d->courrier;
        $objet = $c->estConfidentiel() ? 'courrier confidentiel' : '« '.Str::limit($c->objet, 80).' »';
        $echeance = $d->echeance->format('d/m/Y');
        $det = $this->detail ? Str::limit($this->detail, 110) : '';
        $ancre = '';

        [$titre, $message, $ton] = match ($this->evenement) {
            self::IMPUTATION => ['Courrier à traiter', "Le courrier {$c->numero} ({$objet}) vous est imputé ({$d->libelleRole()}). Échéance : {$echeance}.", 'justification'],
            self::ECHEANCE => ['Échéance proche', "La diligence {$d->code} (courrier {$c->numero}, {$objet}) arrive à échéance le {$echeance}.", 'avenir'],
            self::RETARD => ['Diligence en retard', "La diligence {$d->code} (courrier {$c->numero}, {$objet}) est en retard : échéance dépassée le {$echeance}.".($det ? " {$det}" : ''), 'retard'],
            self::ACTION_A_VALIDER => ['Action à valider', "Une action du plan de traitement de la diligence {$d->code} attend votre validation : « {$det} ».", 'attente'],
            self::ACTION_ASSIGNEE => ['Action qui vous est confiée', "Une action vous est confiée sur la diligence {$d->code} (courrier {$c->numero}) : « {$det} ».", 'attente'],
            self::ACTION_VALIDEE => ['Action validée', "Votre action sur la diligence {$d->code} a été validée : « {$det} ».", 'avenir'],
            self::ACTION_REFUSEE => ['Action refusée', "Votre action sur la diligence {$d->code} a été refusée. {$det}", 'retard'],
            self::RENVOI => ['Dossier renvoyé', "Le courrier {$c->numero} ({$objet}) vous est renvoyé par le responsable de la diligence {$d->code}. {$det}", 'attente'],
            self::REPONSE_ANNULATION_DEMANDEE => ['Annulation de réponse à décider', "L'annulation de la réponse au courrier {$c->numero} ({$objet}) est demandée. Vous avez visé cette réponse : votre accord est requis. {$det}", 'attente'],
            self::REPONSE_SUSPENDUE => ['Circuit de visa suspendu', "L'annulation de la réponse au courrier {$c->numero} est demandée : le circuit est suspendu jusqu'à la décision. {$det}", 'attente'],
            self::REPONSE_ANNULEE => ['Réponse annulée', "La réponse au courrier {$c->numero} ({$objet}) a été annulée. {$det}", 'retard'],
            self::REPONSE_ANNULATION_REFUSEE => ['Annulation refusée', "L'annulation de la réponse au courrier {$c->numero} est refusée : le circuit de visa reprend. {$det}", 'avenir'],
            self::DELAI_DEMANDE => ['Demande de délai', "Un report d'échéance est demandé pour la diligence {$d->code} (courrier {$c->numero}). {$det}", 'attente'],
            self::DELAI_ACCORDE => ['Délai accordé', "Votre demande de délai pour la diligence {$d->code} est accordée. {$det}", 'avenir'],
            self::DELAI_REFUSE => ['Délai refusé', "Votre demande de délai pour la diligence {$d->code} est refusée. {$det}", 'retard'],
            self::REPONSE_A_VISER => ['Réponse à viser', "La réponse au courrier {$c->numero} ({$objet}) attend votre visa.", 'attente'],
            self::REPONSE_A_SIGNER => ['Réponse à signer', "La réponse au courrier {$c->numero} ({$objet}) attend votre signature.", 'attente'],
            self::REPONSE_REFUSEE => ['Réponse à corriger', "La réponse au courrier {$c->numero} a été refusée au visa. {$det}", 'retard'],
            self::REPONSE_SIGNEE => ['Réponse signée', "La réponse au courrier {$c->numero} ({$objet}) a été signée.", 'avenir'],
            default => ['Courrier imputé à un de vos agents', "Le courrier {$c->numero} ({$objet}) a été imputé directement à ".($d->responsable?->nomComplet() ?? $d->destinataire_libelle).". Échéance : {$echeance}.", 'justification'],
        };

        if (str_starts_with($this->evenement, 'diligence_action')) {
            $ancre = '#traitement';
        } elseif (str_starts_with($this->evenement, 'diligence_delai')) {
            $ancre = '#delais';
        } elseif (str_starts_with($this->evenement, 'diligence_reponse')) {
            $ancre = '#reponses';
        }

        return [
            'type' => $this->evenement,
            'diligence_id' => $d->id,
            'code' => $d->code,
            'libelle' => $c->numero,
            'titre' => $titre,
            'message' => $message,
            'ton' => $ton,
            'lien' => $this->evenement === self::RENVOI ? route('courrier.show', $c->id) : route('courrier.diligence.show', $d->id).$ancre,
        ];
    }
}
