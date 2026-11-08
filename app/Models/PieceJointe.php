<?php

namespace App\Models;

use App\Models\Concerns\ProtegeParCloture;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'attachable_type', 'attachable_id', 'categorie', 'nom_original', 'chemin', 'extension',
    'mime', 'taille', 'version', 'racine_id', 'actuelle', 'depose_par',
])]
class PieceJointe extends Model
{
    use ProtegeParCloture, SoftDeletes;

    protected $table = 'pieces_jointes';

    protected function casts(): array
    {
        return ['actuelle' => 'boolean', 'taille' => 'integer', 'version' => 'integer'];
    }

    public function exercicesProteges(): array
    {
        return [$this->tache()?->exerciceTache()];
    }

    public function attachable()
    {
        return $this->morphTo();
    }

    public function deposePar()
    {
        return $this->belongsTo(User::class, 'depose_par');
    }

    /** Identifiant de la 1re version du fichier (commun à toute la chaîne des versions). */
    public function racine(): int
    {
        return $this->racine_id ?? $this->id;
    }

    public function chaine()
    {
        return static::where(fn ($q) => $q->where('id', $this->racine())->orWhere('racine_id', $this->racine()));
    }

    public function versionsAnterieures()
    {
        return $this->chaine()->where('actuelle', false)->with('deposePar')->orderByDesc('version')->get();
    }

    /** 'tache' ou 'sous_tache' (valeurs acceptées par le formulaire de dépôt). */
    public function typeCourt(): string
    {
        return $this->attachable_type === (new SousTache)->getMorphClass() ? 'sous_tache' : 'tache';
    }

    public function tache(): ?Tache
    {
        $a = $this->attachable;

        return match (true) {
            $a instanceof SousTache => $a->tache,
            $a instanceof Tache => $a,
            default => null, // ex. scan d'un courrier : pas de tâche
        };
    }

    public function estApercevable(): bool
    {
        return in_array($this->extension, config('pieces_jointes.apercu'), true);
    }

    public function tailleLisible(): string
    {
        $o = (int) $this->taille;

        return $o >= 1048576 ? number_format($o / 1048576, 1, ',', ' ').' Mo' : max(1, (int) round($o / 1024)).' Ko';
    }

    public function icone(): string
    {
        return match ($this->extension) {
            'pdf' => 'fa-regular fa-file-pdf',
            'doc', 'docx' => 'fa-regular fa-file-word',
            'xls', 'xlsx' => 'fa-regular fa-file-excel',
            'ppt', 'pptx' => 'fa-regular fa-file-powerpoint',
            'jpg', 'jpeg', 'png' => 'fa-regular fa-file-image',
            'zip' => 'fa-regular fa-file-zipper',
            default => 'fa-regular fa-file',
        };
    }
}
