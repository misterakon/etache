<?php

namespace App\Notifications;

use App\Models\TachePonctuelle;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

/** Cloche : tâches ponctuelles (créée, confiée, à valider, validée, refusée, annulée, en retard). */
class PonctuelleNotification extends Notification
{
    public const CREEE = 'ponctuelle_creee';

    public const CONFIEE = 'ponctuelle_confiee';

    public const A_VALIDER = 'ponctuelle_a_valider';

    public const VALIDEE = 'ponctuelle_validee';

    public const REFUSEE = 'ponctuelle_refusee';

    public const ANNULEE = 'ponctuelle_annulee';

    public const RETARD = 'ponctuelle_retard';

    public function __construct(public TachePonctuelle $tache, public string $evenement, public ?string $detail = null)
    {
        $this->locale('fr');
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $t = $this->tache->loadMissing('responsable');
        $qui = $t->responsable?->nomComplet() ?? 'Un agent';
        $libelle = '« '.Str::limit($t->libelle, 70).' »';
        $detail = $this->detail ? ' Motif : '.Str::limit($this->detail, 110) : '';

        [$titre, $message, $ton] = match ($this->evenement) {
            self::CREEE => ['Nouvelle tâche ponctuelle', "{$qui} a créé la tâche ponctuelle {$libelle}.", 'avenir'],
            self::CONFIEE => ['Une tâche ponctuelle vous est confiée', "La tâche {$libelle} vous est confiée".($t->date_fin ? ' pour le '.$t->date_fin->format('d/m/Y') : '').'.', 'attente'],
            self::A_VALIDER => ['Tâche ponctuelle à valider', "{$qui} soumet la tâche ponctuelle {$libelle} à votre validation.", 'attente'],
            self::VALIDEE => ['Tâche ponctuelle validée', "Votre tâche ponctuelle {$libelle} a été validée.", 'avenir'],
            self::REFUSEE => ['Tâche ponctuelle refusée', "Votre tâche ponctuelle {$libelle} a été refusée.{$detail}", 'retard'],
            self::ANNULEE => ['Tâche ponctuelle annulée', "La tâche ponctuelle {$libelle} a été annulée.{$detail}", 'retard'],
            default => ['Tâche ponctuelle en retard', "La tâche ponctuelle {$libelle} de {$qui} a dépassé son échéance.", 'retard'],
        };

        return [
            'type' => $this->evenement, 'tache_id' => $t->id, 'libelle' => $t->code, 'titre' => $titre,
            'message' => $message, 'ton' => $ton, 'lien' => route('ponctuelle.index', ['ouvrir' => $t->id]),
        ];
    }
}
