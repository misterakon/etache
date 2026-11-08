<?php

namespace App\Notifications;

use App\Models\SousTache;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

/** Cloche : circuit de validation des sous-tâches (à valider, validée, refusée, confiée). */
class SousTacheNotification extends Notification
{
    public const A_VALIDER = 'sous_tache_a_valider';
    public const VALIDEE = 'sous_tache_validee';
    public const REFUSEE = 'sous_tache_refusee';
    public const CONFIEE = 'sous_tache_confiee';

    public function __construct(public SousTache $sousTache, public string $evenement, public ?string $detail = null)
    {
        $this->locale('fr');
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $tache = $this->sousTache->tache;
        $libelle = '« '.Str::limit($this->sousTache->libelle, 70).' »';
        $detail = $this->detail ? ' '.Str::limit($this->detail, 110) : '';

        [$titre, $message, $ton] = match ($this->evenement) {
            self::A_VALIDER => ['Sous-tâche à valider', "La sous-tâche {$libelle} de la tâche {$tache->code} attend votre validation.", 'attente'],
            self::VALIDEE => ['Sous-tâche validée', "Votre sous-tâche {$libelle} (tâche {$tache->code}) a été validée.", 'avenir'],
            self::REFUSEE => ['Sous-tâche refusée', "Votre sous-tâche {$libelle} (tâche {$tache->code}) a été refusée.{$detail}", 'retard'],
            default => ['Sous-tâche qui vous est confiée', "La sous-tâche {$libelle} de la tâche {$tache->code} vous est confiée.", 'attente'],
        };

        return [
            'type' => $this->evenement, 'tache_id' => $tache->id, 'libelle' => $tache->code, 'titre' => $titre,
            'message' => $message, 'ton' => $ton, 'lien' => route('feuille_de_route.tache.details', $tache->id),
        ];
    }
}
