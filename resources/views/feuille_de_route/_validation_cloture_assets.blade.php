{{-- Styles et fonctions des boutons de validation de clôture (chargés une seule fois par page) --}}
@once
  <style>
    .ct-banniere { display: flex !important; align-items: center; gap: 16px; flex-wrap: wrap; margin: 0 0 18px; padding: 14px 18px; border-radius: 14px; border: 1px solid var(--ct-bord); background: var(--ct-fond); }
    .ct-banniere--attente { --ct-c: #C77D0A; --ct-fond: #FDF3E1; --ct-bord: #F0D9A8; }
    .ct-banniere--refus { --ct-c: #A73B34; --ct-fond: #FBEDEC; --ct-bord: #EBC3C0; }
    .ct-icone { display: flex !important; align-items: center; justify-content: center; flex: 0 0 44px; width: 44px; height: 44px; border-radius: 50%; border: 2px solid var(--ct-c); background: #fff; color: var(--ct-c); font-size: 18px; }
    .ct-texte { display: block !important; flex: 1 1 320px; min-width: 0; font-size: 13.5px; line-height: 1.5; color: #31445E; }
    .ct-texte strong { display: block !important; margin-bottom: 2px; font-size: 15px; color: var(--ct-c); }
    .ct-actions { display: flex !important; gap: 8px; flex-wrap: wrap; }
    .ct-bouton { display: inline-flex !important; align-items: center; gap: 7px; padding: 8px 16px; border: 0; border-radius: 10px; font-size: 13.5px; font-weight: 700; color: #fff; cursor: pointer; }
    .ct-bouton--ok { background: #2E7D5B; } .ct-bouton--ko { background: #A73B34; } .ct-bouton--neutre { background: #2B6CB0; }
    .ct-bouton:hover { filter: brightness(1.08); }
    .ct-liste { display: block !important; }
    .ct-ligne { display: flex !important; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; padding: 10px 0; border-top: 1px solid #F0D9A8; }
    .ct-ligne:first-of-type { border-top: 0; }
    .ct-ligne b { display: block !important; font-size: 13.5px; color: #14304F; } .ct-ligne small { display: block !important; font-size: 12px; color: #52637C; }
  </style>
  <script type="text/javascript">
    function ct_poster(url, motif) {
      const f = document.createElement('form'); f.method = 'POST'; f.action = url;
      const t = document.createElement('input'); t.type = 'hidden'; t.name = '_token'; t.value = "{{ csrf_token() }}"; f.appendChild(t);
      if (motif !== undefined) { const m = document.createElement('input'); m.type = 'hidden'; m.name = 'motif'; m.value = motif; f.appendChild(m); }
      document.body.appendChild(f); f.submit();
    }
    function ct_valider(url) {
      Swal.fire({ title: 'Valider la clôture ?', text: 'La tâche passera à « Terminée » (100 %).', icon: 'question', showCancelButton: true, confirmButtonText: 'Valider', cancelButtonText: 'Annuler' })
        .then(function (r) { if (r.isConfirmed) { ct_poster(url); } });
    }
    function ct_refuser(url) {
      Swal.fire({ title: 'Refuser la clôture', input: 'textarea', inputLabel: 'Motif du refus (obligatoire, renvoyé au responsable)', showCancelButton: true, confirmButtonText: 'Refuser', cancelButtonText: 'Annuler',
        inputValidator: function (v) { if (!v || v.trim().length < 5) { return 'Précisez le motif (5 caractères minimum).'; } } })
        .then(function (r) { if (r.isConfirmed) { ct_poster(url, r.value); } });
    }
    function ct_soumettre(url) { ct_poster(url); }
  </script>
@endonce
