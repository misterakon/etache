<?php

namespace App\Notifications;

use App\Models\Tache;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

/** Cloche : validation de la clôture d'une tâche (à valider, validée, refusée, relance). */
class ClotureTacheNotification extends Notification
{
    public const A_VALIDER = 'tache_cloture_a_valider';
    public const VALIDEE = 'tache_cloture_validee';
    public const REFUSEE = 'tache_cloture_refusee';
    public const RELANCE = 'tache_cloture_relance';

    public function __construct(public Tache $tache, public string $evenement, public ?string $detail = null)
    {
        $this->locale('fr');
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $t = $this->tache;
        $libelle = '« '.Str::limit($t->libelle, 70).' »';
        $detail = $this->detail ? ' '.Str::limit($this->detail, 110) : '';

        [$titre, $message, $ton] = match ($this->evenement) {
            self::A_VALIDER => ['Clôture de tâche à valider', "La clôture de la tâche {$t->code} {$libelle} attend votre validation.", 'attente'],
            self::VALIDEE => ['Clôture validée', "La clôture de la tâche {$t->code} {$libelle} est validée : la tâche est terminée.", 'avenir'],
            self::REFUSEE => ['Clôture refusée', "La clôture de la tâche {$t->code} {$libelle} est refusée.{$detail}", 'retard'],
            default => ['Relance : clôture en attente', "La clôture de la tâche {$t->code} {$libelle} attend toujours votre validation.{$detail}", 'retard'],
        };

        return [
            'type' => $this->evenement, 'tache_id' => $t->id, 'libelle' => $t->code, 'code' => $t->code, 'titre' => $titre, 'message' => $message,
            'ton' => $ton, 'lien' => route('feuille_de_route.tache.details', $t->id).'#validation',
        ];
    }
}
