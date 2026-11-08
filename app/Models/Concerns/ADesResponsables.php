<?php

namespace App\Models\Concerns;

use App\Models\MandatResponsable;
use App\Models\Personnel;
use Carbon\Carbon;
use Illuminate\Support\Str;

/**
 * Pour les unites qui ont un responsable historise : Direction, SousDirection, Service.
 *
 *   $service->responsable()                 // responsable aujourd'hui (Personnel ou null)
 *   $service->responsable('2025-03-01')     // responsable a une date passee
 *   $service->mandats                       // tout l'historique, du plus recent au plus ancien
 */
trait ADesResponsables
{
    /** Historique des responsables de l'unite (colonne : direction_id, sous_direction_id ou service_id). */
    public function mandats()
    {
        return $this->hasMany(MandatResponsable::class, Str::snake(class_basename($this)).'_id')
            ->orderByDesc('date_debut');
    }

    /**
     * Responsable de l'unite a une date : l'interim d'abord, sinon le titulaire.
     * Un agent inactif est ignore : on passe au mandat suivant, ou a null (la chaine hierarchique remonte alors d'un niveau).
     */
    public function responsable($date = null): ?Personnel
    {
        $date = $date ? Carbon::parse($date) : today();

        return $this->mandats()->getQuery()->reorder()
            ->actifA($date)->prioritaire()->with('personnel')->get()
            ->pluck('personnel')
            ->first(fn ($p) => $p && $p->actif);
    }
}
