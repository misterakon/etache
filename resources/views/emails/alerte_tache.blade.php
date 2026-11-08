<x-mail::message>
# Bonjour {{ $destinataire->nomComplet() }},

{{ $message }}

<x-mail::panel>
**Alerte :** {{ $regle->libelle }}  
**Tâche :** {{ $tache->code }} — {{ $tache->libelle }}  
**Échéance :** {{ $tache->date_fin->format('d/m/Y') }}  
@if ($statut)
**Statut :** {{ $statut }}  
@endif
**Avancement :** {{ (int) $tache->avancement_pourcentage }} %
</x-mail::panel>

<x-mail::button :url="$url" color="primary">
Consulter la tâche
</x-mail::button>

Si l'échéance ne peut pas être tenue, le responsable ou le pilote de la tâche peut demander une justification ou un report depuis la fiche de la tâche.

Cordialement,<br>
L'équipe e-Tache (DGEPN)
</x-mail::message>
