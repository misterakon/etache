<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\GenereCodeSequentiel;

#[Fillable(['code', 'libelle', 'actif'])]
class TypeAgent extends Model
{
    use GenereCodeSequentiel;

    protected $table = 'types_agent';

    // Codes seedés par TypeAgentSeeder
    const FONCTIONNAIRE = '001';
    const CONTRACTUEL = '002';
    const STAGIAIRE = '003';
    const CONSULTANT = '004';

    protected function casts(): array
    {
        return ['actif' => 'boolean'];
    }

    public function personnel()
    {
        return $this->hasMany(Personnel::class, 'type_agent_id');
    }
}
