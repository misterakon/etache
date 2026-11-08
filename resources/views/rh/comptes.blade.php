@extends('layout.master')
@section('title', 'Comptes des agents')

@section('css')
  <link rel="stylesheet" href="{{ asset('assets/css/courrier.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/courrier-suite.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/rh.css') }}">
@endsection

@section('main-content')
  <div class="page-body">
    <div class="container">

      <div class="cr-hero cr-hero--ambre">
        <div>
          <p class="cr-hero__sur">Personnel</p>
          <h1 class="cr-hero__titre">Comptes des agents</h1>
          <p class="cr-hero__sous">Créez en une fois les comptes des agents qui n'en ont pas (profil Agent, mot de passe provisoire à changer à la première connexion) et désactivez ceux des agents partis.</p>
        </div>
      </div>
      @include('rh._nav')
      @if ($errors->any())
        <div class="cr-alerte-conf"><i class="fa-solid fa-circle-exclamation"></i><div>@foreach ($errors->all() as $e) {{ $e }}<br> @endforeach</div></div>
      @endif

      @if ($resultat)
        <div class="rh-secret">
          <i class="fa-solid fa-key"></i>
          <div style="flex:1;">
            <strong>{{ $resultat['crees'] }} compte(s) créé(s).</strong>
            <div class="cs-aide">Les mots de passe provisoires ne sont <b>jamais enregistrés en clair</b> : téléchargez le fichier maintenant, il ne sera plus disponible ensuite. Chaque agent devra changer son mot de passe à sa première connexion.</div>
            @if (! empty($resultat['ignores']))
              <details class="cs-historique" style="margin-top:8px;"><summary>{{ count($resultat['ignores']) }} agent(s) ignoré(s)</summary><ul>@foreach ($resultat['ignores'] as $i)<li><b>{{ $i['nom'] }}</b> : {{ $i['raison'] }}</li>@endforeach</ul></details>
            @endif
          </div>
          @if ($avecMotDePasse)<a class="cr-btn cr-btn--primaire" href="{{ route('rh.comptes.telecharger') }}"><i class="fa-solid fa-file-excel"></i> Télécharger les mots de passe</a>@endif
        </div>
      @endif

      <form method="post" action="{{ route('rh.comptes.creer') }}" class="cr-carte" id="form_comptes">
        @csrf
        <div class="cr-carte__tete"><h2 class="cr-carte__titre"><i class="fa-solid fa-user-plus"></i> Agents sans compte <span class="cs-compte cs-compte--neutre">{{ $sansCompte->count() }}</span></h2></div>
        @if ($sansCompte->isEmpty())
          <div class="cr-vide"><i class="fa-regular fa-circle-check"></i><strong>Tous les agents actifs ont un compte</strong>Rien à créer.</div>
        @else
          <div class="rh-selection">
            <label style="display:flex;align-items:center;gap:8px;margin:0;font-weight:600;"><input type="checkbox" class="rh-case" id="rh_tous" onclick="rh_tout_cocher(this)"> Tout sélectionner (agents avec e-mail)</label>
            @if ($peutCreer)
              <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                <label for="rh_profil" class="cs-aide" style="margin:0;">Profil</label>
                <select class="form-select form-select-sm" id="rh_profil" name="profil_id" style="width:auto;">@foreach ($profils->where('slug', '!=', 'ADMIN') as $p)<option value="{{ $p->id }}" @selected($p->id === $profilDefaut)>{{ $p->libelle }}</option>@endforeach</select>
                <button class="cr-btn cr-btn--primaire cr-btn--petit" type="submit"><i class="fa-solid fa-user-plus"></i> Créer les comptes cochés (<span id="rh_nb">0</span>)</button>
              </div>
            @else
              <span class="cs-aide">Seul l'administrateur peut créer des comptes.</span>
            @endif
          </div>
          <div class="overflow-x-auto" style="max-height:560px;overflow-y:auto;">
            <table class="cr-table">
              <thead><tr><th style="width:40px;"></th><th>Agent</th><th>E-mail</th><th>Service</th><th>Type</th></tr></thead>
              <tbody>
                @foreach ($sansCompte as $a)
                  <tr style="cursor:default;">
                    <td><input type="checkbox" class="rh-case rh-ligne" name="ids[]" value="{{ $a->id }}" @disabled(blank($a->email) || ! $peutCreer) onchange="rh_compter()"></td>
                    <td><div class="rh-agent"><span class="rh-avatar">{{ $a->initiales() }}</span><span><b>{{ $a->nomComplet() }}</b><small>{{ $a->matricule ?: 'Sans matricule' }}</small></span></div></td>
                    <td>@if (filled($a->email)){{ $a->email }}@else<span class="rh-sans-email"><i class="fa-solid fa-circle-exclamation"></i> Pas d'e-mail : à renseigner sur la fiche</span>@endif</td>
                    <td>{{ $a->service?->nom ?? '—' }}</td><td>{{ $a->typeAgent?->libelle ?? '—' }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif
      </form>

      @if ($adesactiver->isNotEmpty())
        <div class="cr-carte">
          <div class="cr-carte__tete"><h2 class="cr-carte__titre"><i class="fa-solid fa-user-slash"></i> Comptes à désactiver <span class="cs-compte">{{ $adesactiver->count() }}</span></h2>
            @role('ADMIN')<form method="post" action="{{ route('rh.comptes.desactiver') }}" onsubmit="return cs_confirmer2(this, 'Désactiver ces {{ $adesactiver->count() }} compte(s) ?')">@csrf<button class="cr-btn cr-btn--danger cr-btn--petit" type="submit"><i class="fa-solid fa-user-slash"></i> Tout désactiver</button></form>@endrole
          </div>
          <div class="cr-carte__corps"><p class="cs-aide" style="margin-bottom:10px;">Ces comptes sont encore actifs alors que l'agent est inactif ou archivé.</p>
            @foreach ($adesactiver as $u)<div class="cs-afaire__ligne" style="{{ $loop->first ? 'border-top:0;' : '' }}"><div><b>{{ $u->name }}</b><small>{{ $u->email }} · {{ $u->personnel?->archive_le ? 'agent archivé' : 'agent inactif' }}</small></div></div>@endforeach
          </div>
        </div>
      @endif

      <script type="text/javascript">
		function rh_compter() { document.getElementById('rh_nb').textContent = document.querySelectorAll('.rh-ligne:checked').length; }
		function rh_tout_cocher(m) { document.querySelectorAll('.rh-ligne:not(:disabled)').forEach(function (c) { c.checked = m.checked; }); rh_compter(); }
		function cs_confirmer2(form, texte) {
			Swal.fire({ title: 'e-Tâche', text: texte, icon: 'warning', showCancelButton: true, confirmButtonText: 'Oui', cancelButtonText: 'Annuler' }).then(function (r) { if (r.isConfirmed) { form.submit(); } });
			return false;
		}
      </script>
    </div>
  </div>
@endsection
