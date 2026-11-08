{{-- Formulaire modal + configuration JSON du script ponctuelles.js. À placer hors des onglets (pour que le modal ne soit pas piégé dans un bloc masqué). --}}
@php
  $utilisateurPt = auth()->user();
  $priorites = $priorites ?? \App\Models\Priorite::where('actif', true)->orderBy('id')->get(['id', 'libelle', 'couleur']);
  $agents = $agents ?? \App\Support\PonctuellesDroits::agentsConfiables($utilisateurPt)->map(fn ($p) => ['id' => $p->id, 'nom' => $p->nomComplet(), 'moi' => (int) $p->id === (int) $utilisateurPt->personnel_id])->values();
  $supervise = $supervise ?? \App\Support\PonctuellesDroits::supervise($utilisateurPt);
  $controle = $controle ?? \App\Support\Reglages::get('taches_ponctuelles.controle');
  $aPersonnel = $aPersonnel ?? (bool) $utilisateurPt->personnel_id;
@endphp
<div class="pt-toasts" id="pt_toasts" aria-live="polite"></div>
@include('ponctuelles.form_ponctuelle')
<script type="application/json" id="pt_config">{!! json_encode([
    'base' => route('ponctuelle.index'), 'donnees' => route('ponctuelle.donnees'), 'jeton' => csrf_token(), 'ouvrir' => $ouvrir ?? null,
    'supervise' => $supervise, 'agents' => $agents, 'controle' => $controle, 'agentFiltre' => $agentFiltre ?? null,
    'priorites' => $priorites->map(fn ($p) => ['id' => $p->id, 'libelle' => $p->libelle, 'couleur' => $p->couleur ?: '#2B6CB0'])->values(),
], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>
