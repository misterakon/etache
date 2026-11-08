/* Tâches ponctuelles (public/assets/js/ponctuelles.js)
 * Tout passe par des appels JSON : ajout rapide, tableau par glisser-déposer, détail dans le formulaire modal,
 * livrables par glisser-déposer, décision de la chaîne hiérarchique. Aucun rechargement de page. */
(function () {
	'use strict';

	var racine = document.getElementById('pt');
	var cfgEl = document.getElementById('pt_config');
	if (!racine || !cfgEl) { return; }
	var cfg = JSON.parse(cfgEl.textContent);
	var zone = document.getElementById('pt_zone');
	var reduit = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	var etat = { portee: 'mes', etat: 'toutes', q: '', taches: [], kpis: {}, prio: null, courante: null };
	var STATUTS = [['a_faire', 'À faire', 'fa-hourglass-half'], ['en_cours', 'En cours', 'fa-bars-progress'], ['terminee', 'Terminées', 'fa-circle-check']];
	var minuterie = null;
	var numeroRequete = 0;

	function esc(s) { return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]; }); }

	/* ---------------------------------------------------------------- réseau */
	function api(url, options) {
		options = options || {};
		var entetes = { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': cfg.jeton };
		var corps = options.corps;
		if (corps && !(corps instanceof FormData)) { entetes['Content-Type'] = 'application/json'; corps = JSON.stringify(corps); }
		return fetch(url, { method: options.methode || 'GET', credentials: 'same-origin', headers: entetes, body: corps }).then(function (r) {
			return r.json().catch(function () { return {}; }).then(function (j) {
				if (!r.ok) { var e = new Error(j.message || 'Une erreur est survenue (code ' + r.status + ').'); e.statut = r.status; throw e; }
				return j;
			});
		});
	}

	function toast(message, type) {
		var t = document.createElement('div');
		t.className = 'pt-toast pt-toast--' + (type || 'ok');
		t.innerHTML = '<i class="fa-solid ' + (type === 'erreur' ? 'fa-circle-exclamation' : 'fa-circle-check') + '"></i><span>' + esc(message) + '</span>';
		document.getElementById('pt_toasts').appendChild(t);
		setTimeout(function () { t.classList.add('sort'); setTimeout(function () { t.remove(); }, 300); }, type === 'erreur' ? 5000 : 2600);
	}

	/* ---------------------------------------------------------------- chargement */
	function parametres() {
		var p = new URLSearchParams({ portee: etat.portee, q: etat.q });
		if (cfg.agentFiltre) { p.set('agent', cfg.agentFiltre.id); }
		return p;
	}

	function charger(options) {
		options = options || {};
		var n = ++numeroRequete;
		var p = parametres();
		if (!options.silencieux) { zone.classList.add('charge'); }
		return api(cfg.donnees + '?' + p.toString()).then(function (j) {
			if (n !== numeroRequete) { return; }
			etat.taches = j.taches; etat.kpis = j.kpis;
			zone.classList.remove('charge');
			majKpis(j.kpis);
			dessiner(options.nouveau);
			if (options.apres) { options.apres(); }
		}).catch(function (e) {
			zone.classList.remove('charge');
			zone.innerHTML = '<div class="pt-vide"><i class="fa-solid fa-triangle-exclamation"></i><strong>Chargement impossible</strong><span>' + esc(e.message) + '</span><button type="button" class="pt-btn" id="pt_reessayer">Réessayer</button></div>';
			document.getElementById('pt_reessayer').addEventListener('click', function () { charger(); });
		});
	}

	function animer(el, vers) {
		var de = parseInt(el.dataset.v || el.textContent, 10) || 0;
		el.dataset.v = vers;
		if (reduit || de === vers) { el.textContent = vers; return; }
		var debut = null;
		function pas(t) { if (debut === null) { debut = t; } var k = Math.min(1, (t - debut) / 500); el.textContent = Math.round(de + (vers - de) * (1 - Math.pow(1 - k, 3))); if (k < 1) { requestAnimationFrame(pas); } }
		requestAnimationFrame(pas);
	}

	function majKpis(k) {
		var vals = racine.querySelectorAll('.ep-kpi__val');
		[k.a_faire, k.en_cours, k.en_retard, k.terminees].forEach(function (v, i) { if (vals[i]) { animer(vals[i], v || 0); } });
		var bouton = document.getElementById('pt_a_valider');
		if (k.a_valider > 0) { bouton.hidden = false; bouton.querySelector('b').textContent = k.a_valider; } else { bouton.hidden = true; }
		var nb = racine.querySelector('[data-nb="' + etat.portee + '"]');
		if (nb) { nb.textContent = etat.taches.length; }
	}

	/* ---------------------------------------------------------------- rendu */
	function echeance(t) {
		if (!t.date_fin) { return '<span class="pt-ech pt-ech--aucune"><i class="fa-regular fa-calendar"></i> Sans échéance</span>'; }
		if (t.statut === 'terminee') { return '<span class="pt-ech"><i class="fa-regular fa-calendar-check"></i> ' + esc(t.date_fin_fr) + '</span>'; }
		var j = t.jours;
		var txt = j < 0 ? (-j) + ' j de retard' : (j === 0 ? 'Aujourd’hui' : (j === 1 ? 'Demain' : esc(t.date_fin_fr)));
		return '<span class="pt-ech ' + (j < 0 ? 'pt-ech--retard' : (j <= 2 ? 'pt-ech--proche' : '')) + '"><i class="fa-regular fa-calendar"></i> ' + txt + '</span>';
	}

	function avatar(r) {
		if (!r) { return ''; }
		return r.photo ? '<img class="pt-av" src="' + esc(r.photo) + '" alt="" title="' + esc(r.nom) + '">' : '<span class="pt-av" title="' + esc(r.nom) + '">' + esc(r.initiales) + '</span>';
	}

	function badges(t) {
		var b = '';
		if (t.priorite) { b += '<span class="pt-badge" style="--c:' + esc(t.priorite.couleur || '#2B6CB0') + '">' + esc(t.priorite.libelle) + '</span>'; }
		if (t.validation === 'en_attente') { b += '<span class="pt-badge pt-badge--attente"><i class="fa-solid fa-hourglass-half"></i> À valider</span>'; }
		if (t.statut === 'annulee') { b += '<span class="pt-badge pt-badge--gris">' + (t.validation === 'refusee' ? 'Refusée' : 'Annulée') + '</span>'; }
		if (t.origine === 'responsable' && t.responsable && t.responsable.moi) { b += '<span class="pt-badge pt-badge--confiee"><i class="fa-solid fa-hand-holding-hand"></i> Confiée</span>'; }
		return b;
	}

	var MOIS = ['JAN', 'FÉV', 'MAR', 'AVR', 'MAI', 'JUIN', 'JUIL', 'AOÛT', 'SEP', 'OCT', 'NOV', 'DÉC'];
	var ETATS = [['toutes', 'Toutes', 'fa-layer-group'], ['a_faire', 'À faire', 'fa-hourglass-half'], ['en_cours', 'En cours', 'fa-bars-progress'], ['retard', 'En retard', 'fa-triangle-exclamation'], ['terminee', 'Terminées', 'fa-circle-check'], ['annulee', 'Annulées', 'fa-ban']];

	function ton(t) {
		if (t.statut === 'annulee') { return 'neutre'; }
		if (t.statut === 'terminee') { return 'terminee'; }
		if (t.retard) { return 'retard'; }
		return t.statut === 'en_cours' ? 'encours' : 'attente';
	}

	function dansEtat(t, e) {
		if (e === 'toutes') { return t.statut !== 'annulee'; }
		if (e === 'retard') { return !!t.retard; }
		return t.statut === e;
	}

	function dessinerEtats() {
		var conteneur = document.getElementById('pt_etats');
		conteneur.innerHTML = ETATS.map(function (e) {
			var n = etat.taches.filter(function (t) { return dansEtat(t, e[0]); }).length;
			if (e[0] === 'annulee' && !n) { return ''; }
			return '<button type="button" role="tab" data-etat="' + e[0] + '" class="' + (etat.etat === e[0] ? 'actif' : '') + ' pt-etat--' + e[0] + '"><i class="fa-solid ' + e[2] + '"></i> ' + e[1] + ' <em>' + n + '</em></button>';
		}).join('');
	}

	function ligne(t) {
		var d = t.date_fin ? t.date_fin.split('-') : null;
		var date = d ? '<strong>' + d[2] + '</strong><span>' + MOIS[parseInt(d[1], 10) - 1] + '</span><small>' + d[0] + '</small>' : '<i class="fa-regular fa-calendar-xmark"></i><small>Sans échéance</small>';
		var tn = ton(t);
		var qui = (etat.portee !== 'mes' && t.responsable) ? '<span class="pt-ech"><i class="fa-regular fa-user"></i> ' + esc(t.responsable.nom) + '</span>' : '';
		var retard = (t.retard && t.statut !== 'terminee' && t.statut !== 'annulee') ? '<span class="tl-badge tl-retard tl-solid">' + (t.jours < 0 ? (-t.jours) + ' j de retard' : 'En retard') + '</span>' : '';
		var actions = '';
		if (t.validation === 'en_attente' && t.peut.valider) {
			actions += '<button type="button" class="tl-btn tl-btn--ok" data-act="valider" title="Valider"><i class="fa-solid fa-check"></i></button><button type="button" class="tl-btn tl-btn--suppr" data-act="refuser" title="Refuser"><i class="fa-solid fa-xmark"></i></button>';
		}
		actions += '<button type="button" class="tl-btn tl-btn--voir" data-act="voir" title="Voir"><i class="fa-regular fa-eye"></i></button>';
		if (t.peut.modifier) { actions += '<button type="button" class="tl-btn tl-btn--edit" data-act="modifier" title="Modifier"><i class="fa-regular fa-pen-to-square"></i></button>'; }
		if (t.peut.supprimer) { actions += '<button type="button" class="tl-btn tl-btn--suppr" data-act="supprimer" title="Supprimer"><i class="fa-solid fa-trash-can"></i></button>'; }
		return '<div class="pt-item"><article class="tk tl-' + tn + '" data-id="' + t.id + '" tabindex="0">'
			+ '<div class="tk__date" title="Échéance">' + date + '</div>'
			+ '<div class="tk__corps"><div class="tk__titre"><a href="#" data-act="voir">' + esc(t.code) + '</a>' + badges(t) + '</div>'
			+ '<p class="tk__libelle" title="' + esc(t.libelle) + '">' + esc(t.libelle) + '</p>'
			+ '<div class="tk__meta"><span class="tl-badge tl-' + (tn === 'retard' ? 'neutre' : tn) + '">' + esc(t.statut_lib) + '</span>' + retard
			+ '<span class="tk__prog" title="Avancement ' + t.avancement + ' %"><i><b style="width:' + t.avancement + '%"></b></i>' + t.avancement + '%</span>'
			+ (t.livrables.length ? '<span class="pt-ech"><i class="fa-solid fa-paperclip"></i> ' + t.livrables.length + '</span>' : '') + qui + '</div></div>'
			+ '<div class="tk__droite"><ul class="tk__agents"><li>' + avatar(t.responsable) + '</li></ul><div class="tk__actions">' + actions + '</div></div>'
			+ '</article></div>';
	}

	function dessiner(nouveau) {
		dessinerEtats();
		var liste = etat.taches.filter(function (t) { return dansEtat(t, etat.etat); });
		if (!liste.length) {
			var textes = { mes: ['Aucune tâche ponctuelle', 'Cliquez sur « Nouvelle tâche » : un titre suffit pour commencer.'], confiees: ['Rien confié pour l’instant', 'Les tâches que vous confiez à vos agents apparaissent ici.'], equipe: ['Rien dans votre équipe', 'Les tâches ponctuelles de vos agents apparaissent ici.'] }[etat.portee];
			var filtre = etat.q || (etat.taches.length && etat.etat !== 'toutes');
			zone.innerHTML = '<div class="pt-vide"><i class="fa-solid fa-' + (filtre ? 'magnifying-glass' : 'mug-hot') + '"></i><strong>' + (filtre ? 'Aucune tâche ici' : textes[0]) + '</strong><span>' + (filtre ? 'Essayez un autre onglet ou un autre mot.' : textes[1]) + '</span></div>';
			return;
		}
		zone.innerHTML = '<div class="pt-liste">' + liste.map(ligne).join('') + '</div>';
		if (nouveau) {
			var c = zone.querySelector('[data-id="' + nouveau + '"]');
			if (c) { c.classList.add('pt-nouvelle'); c.scrollIntoView({ block: 'nearest', behavior: reduit ? 'auto' : 'smooth' }); }
		}
	}

	/* ---------------------------------------------------------------- boîtes de dialogue (SweetAlert, déjà chargé par l'application) */
	function confirmer(titre, texte, bouton) {
		if (!window.Swal) { return Promise.resolve(window.confirm(titre)); }
		return Swal.fire({ title: titre, text: texte || '', icon: 'warning', showCancelButton: true, confirmButtonText: bouton || 'Confirmer', cancelButtonText: 'Annuler', confirmButtonColor: '#BE123C', reverseButtons: true, focusCancel: true }).then(function (r) { return r.isConfirmed; });
	}

	function demanderMotif(titre) {
		if (!window.Swal) { var m = window.prompt(titre); return Promise.resolve(m ? m.trim() : null); }
		return Swal.fire({ title: titre, input: 'textarea', inputPlaceholder: 'Motif (communiqué à l’agent)', inputAttributes: { maxlength: 1000 }, showCancelButton: true, confirmButtonText: 'Confirmer', cancelButtonText: 'Annuler', confirmButtonColor: '#033074', reverseButtons: true,
			inputValidator: function (v) { if (!v || !v.trim()) { return 'Le motif est obligatoire.'; } } }).then(function (r) { return r.isConfirmed ? r.value.trim() : null; });
	}

	function trouver(id) { return etat.taches.filter(function (t) { return t.id === id; })[0]; }

	function changerStatut(t, statut) {
		var corps = { statut: statut };
		return api(cfg.base + '/' + t.id, { methode: 'POST', corps: corps }).then(function (maj) {
			remplacer(maj);
			dessiner(maj.id);
			rafraichirKpis();
			if (statut === 'terminee') { toast('Bravo : « ' + maj.libelle + ' » est terminée.'); }
			return maj;
		}).catch(function (e) { toast(e.message, 'erreur'); dessiner(); });
	}

	function remplacer(maj) {
		var i = etat.taches.findIndex(function (t) { return t.id === maj.id; });
		if (i >= 0) { etat.taches[i] = maj; } else { etat.taches.unshift(maj); }
		if (etat.courante && etat.courante.id === maj.id) { etat.courante = maj; }
	}

	function rafraichirKpis() {
		api(cfg.donnees + '?' + parametres().toString()).then(function (j) { etat.kpis = j.kpis; majKpis(j.kpis); }).catch(function () { /* indicateurs non critiques */ });
	}

	/* ---------------------------------------------------------------- formulaire modal « Nouvelle tâche » */
	var modal = document.getElementById('modal_ponctuelle');
	var formulaire = document.getElementById('form_ponctuelle');

	function afficherModal(montrer) {
		if (window.jQuery && jQuery.fn.modal) { jQuery(modal).modal(montrer ? 'show' : 'hide'); }
		else if (window.bootstrap) { var m = bootstrap.Modal.getOrCreateInstance(modal); if (montrer) { m.show(); } else { m.hide(); } }
	}

	function ouvrirFormulaire() {
		formulaire.reset();
		preparerCreation();
		var champLibelle = document.getElementById('pt_libelle');
		champLibelle.classList.remove('is-invalid');
		var moi = (cfg.agents.filter(function (a) { return a.moi; })[0] || {}).id;
		var pour = document.getElementById('pt_pour');
		if (pour && moi) { pour.value = moi; if (window.jQuery) { jQuery(pour).val(String(moi)).trigger('change'); } }
		var bouton = document.getElementById('BtnEnregistrerPonctuelle');
		bouton.disabled = false; bouton.textContent = 'ENREGISTRER';
		afficherModal(true);
		setTimeout(function () { champLibelle.focus(); }, 400);
	}

	// Liste « Pour » : recherche dans la liste des agents (Select2, comme dans les autres formulaires)
	if (window.jQuery && jQuery.fn.select2 && document.getElementById('pt_pour')) {
		jQuery('#pt_pour').select2({ dropdownParent: jQuery(modal), width: '100%', placeholder: 'Rechercher un agent…', language: { noResults: function () { return 'Aucun agent trouvé'; }, searching: function () { return 'Recherche…'; } } });
	}

	formulaire.addEventListener('submit', function (e) {
		e.preventDefault();
		if (document.getElementById('pt_id').value) { enregistrerDetail(); return; }
		var champLibelle = document.getElementById('pt_libelle');
		var libelle = champLibelle.value.trim();
		if (libelle.length < 3) { champLibelle.classList.add('is-invalid'); champLibelle.focus(); return; }
		var pour = document.getElementById('pt_pour');
		var corps = {
			libelle: libelle, description: document.getElementById('pt_description').value.trim() || null,
			date_debut: document.getElementById('pt_date_debut').value || null, date_fin: document.getElementById('pt_date_fin').value || null,
			priorite_id: document.getElementById('pt_priorite').value || null
		};
		if (pour) { corps.responsable_personnel_id = pour.value; }
		var bouton = document.getElementById('BtnEnregistrerPonctuelle');
		bouton.disabled = true; bouton.textContent = 'Enregistrement…';
		api(cfg.base, { methode: 'POST', corps: corps }).then(function (t) {
			afficherModal(false);
			var pourAutre = t.responsable && !t.responsable.moi;
			toast(pourAutre ? 'Tâche confiée à ' + t.responsable.nom + '.' : (t.validation === 'en_attente' ? 'Tâche envoyée à votre supérieur pour validation.' : 'Tâche ajoutée.'));
			changerPortee(pourAutre ? 'confiees' : 'mes', t.id);
		}).catch(function (er) { toast(er.message, 'erreur'); bouton.disabled = false; bouton.textContent = 'ENREGISTRER'; });
	});
	document.getElementById('pt_libelle').addEventListener('input', function () { this.classList.remove('is-invalid'); });
	document.getElementById('pt_nouvelle').addEventListener('click', ouvrirFormulaire);
	/* ---------------------------------------------------------------- barre d'outils */
	function changerPortee(p, nouveau) {
		etat.portee = p;
		document.querySelectorAll('#pt_portees button').forEach(function (b) { b.classList.toggle('actif', b.dataset.portee === p); });
		return charger({ nouveau: nouveau });
	}
	document.getElementById('pt_portees').addEventListener('click', function (e) { var b = e.target.closest('button'); if (b) { changerPortee(b.dataset.portee); } });
	document.getElementById('pt_etats').addEventListener('click', function (e) {
		var b = e.target.closest('button');
		if (b) { etat.etat = b.dataset.etat; dessiner(); }
	});
	document.getElementById('pt_q').addEventListener('input', function () { clearTimeout(minuterie); var v = this.value.trim(); minuterie = setTimeout(function () { etat.q = v; charger({ silencieux: true }); }, 250); });
	document.getElementById('pt_a_valider').addEventListener('click', function () {
		var cible = cfg.supervise ? 'equipe' : 'mes';
		changerPortee(cible, null).then(function () { var t = etat.taches.filter(function (x) { return x.validation === 'en_attente'; })[0]; if (t) { ouvrirDetail(t.id); } });
	});

	zone.addEventListener('click', function (e) {
		var c = e.target.closest('[data-id]');
		if (!c) { return; }
		e.preventDefault();
		var t = trouver(parseInt(c.dataset.id, 10));
		var b = e.target.closest('[data-act]');
		var act = b ? b.dataset.act : 'voir';
		if (!t) { return; }
		if (act === 'valider' || act === 'refuser') { demander(t, act); }
		else if (act === 'supprimer') { supprimerTache(t); }
		else { ouvrirDetail(t.id, act === 'voir' && !!b); }
	});
	zone.addEventListener('keydown', function (e) { if (e.key === 'Enter') { var c = e.target.closest('[data-id]'); if (c) { ouvrirDetail(parseInt(c.dataset.id, 10)); } } });

	/* ---------------------------------------------------------------- même modal en mode consultation / modification */
	var champsSaisie = ['pt_libelle', 'pt_description', 'pt_date_debut', 'pt_date_fin', 'pt_priorite'];
	var statutChoisi = null;
	var boutonEnregistrer = document.getElementById('BtnEnregistrerPonctuelle');

	function basculerMode(edition) {
		modal.querySelectorAll('.pt-edit').forEach(function (el) { el.hidden = !edition; });
		modal.querySelectorAll('.pt-creation').forEach(function (el) { el.hidden = edition; });
		document.getElementById('pt_supprimer').hidden = true;
	}

	function preparerCreation() {
		etat.courante = null; statutChoisi = null;
		document.getElementById('pt_id').value = '';
		document.getElementById('titre_modal_ponctuelle').textContent = 'Nouvelle tâche ponctuelle';
		basculerMode(false);
		champsSaisie.forEach(function (id) { document.getElementById(id).disabled = false; });
		boutonEnregistrer.hidden = false;
	}

	function dessinerLivrables(t) {
		var m = t.peut.modifier;
		document.getElementById('pt_nb_livrables').textContent = t.livrables.length;
		document.getElementById('pt_livrables').innerHTML = t.livrables.map(function (l) {
			return '<li><i class="fa-solid fa-file"></i><a href="' + esc(l.url) + '">' + esc(l.nom) + '</a><small>' + esc(l.taille) + '</small>' + (l.supprimable ? '<button type="button" data-liv="' + l.id + '" aria-label="Retirer"><i class="fa-solid fa-trash-can"></i></button>' : '') + '</li>';
		}).join('') || '<li class="pt-livrables__vide">Aucun livrable déposé.</li>';
		var depot = document.getElementById('pt_depot');
		depot.hidden = !m; depot.classList.remove('charge');
		document.getElementById('pt_fichier').value = '';
	}

	function marquerStatut(statut) {
		document.querySelectorAll('#pt_statuts button').forEach(function (b) { b.classList.toggle('actif', b.dataset.statut === statut); });
	}

	function remplirDetail(t, lecture) {
		var m = t.peut.modifier && !lecture;
		etat.courante = t; statutChoisi = null;
		basculerMode(true);
		document.getElementById('pt_id').value = t.id;
		document.getElementById('titre_modal_ponctuelle').textContent = t.code + ' — ' + t.statut_lib;
		document.getElementById('pt_libelle').value = t.libelle || '';
		document.getElementById('pt_description').value = t.description || '';
		document.getElementById('pt_date_debut').value = t.date_debut || '';
		document.getElementById('pt_date_fin').value = t.date_fin || '';
		document.getElementById('pt_priorite').value = t.priorite ? t.priorite.id : '';
		champsSaisie.forEach(function (id) { document.getElementById(id).disabled = !m; });
		document.getElementById('pt_libelle').classList.remove('is-invalid');

		var alerte = '';
		if (t.validation === 'en_attente') { alerte = '<div class="pt-alerte pt-alerte--attente"><i class="fa-solid fa-hourglass-half"></i><div><b>En attente de validation</b><span>Cette tâche ne peut pas démarrer avant l’accord du supérieur.</span></div></div>'; }
		if (t.validation === 'refusee' || t.statut === 'annulee') { alerte = '<div class="pt-alerte pt-alerte--refus"><i class="fa-solid fa-ban"></i><div><b>' + (t.validation === 'refusee' ? 'Tâche refusée' : 'Tâche annulée') + '</b><span>' + esc(t.motif_decision || 'Aucun motif indiqué.') + '</span></div></div>'; }
		var zoneEtat = document.getElementById('pt_etat');
		zoneEtat.innerHTML = alerte; zoneEtat.hidden = !alerte;

		document.getElementById('pt_qui').innerHTML = '<div class="pt-t__qui">' + avatar(t.responsable) + '<div><b>' + esc(t.responsable ? t.responsable.nom : '—') + '</b><small>' + (t.origine === 'responsable' ? 'Tâche confiée par ' + esc(t.createur || 'un responsable') : 'Créée par ' + esc(t.createur || 'l’agent')) + ' · ' + esc(t.cree_le) + '</small></div></div>';

		var r = document.getElementById('pt_range');
		r.value = t.avancement; r.disabled = !m; r.style.setProperty('--p', t.avancement + '%');
		document.getElementById('pt_pct').textContent = t.avancement + ' %';
		marquerStatut(t.statut);
		document.querySelectorAll('#pt_statuts button').forEach(function (b) { b.disabled = !m; });
		dessinerLivrables(t);

		var decision = document.getElementById('pt_decision');
		decision.hidden = !t.peut.decider;
		decision.querySelector('[data-decision="valider"]').hidden = !t.peut.valider;
		decision.querySelector('[data-decision="refuser"]').hidden = !t.peut.valider;
		decision.querySelector('[data-decision="annuler"]').hidden = !!t.peut.valider;

		document.getElementById('pt_supprimer').hidden = !t.peut.supprimer || !!lecture;
		boutonEnregistrer.hidden = !m; boutonEnregistrer.disabled = false; boutonEnregistrer.textContent = 'ENREGISTRER';
	}

	function ouvrirDetail(id, lecture) {
		var t = trouver(id);
		(t ? Promise.resolve(t) : api(cfg.base + '/' + id)).then(function (fiche) {
			formulaire.reset();
			remplirDetail(fiche, lecture);
			afficherModal(true);
		}).catch(function (e) { toast(e.message, 'erreur'); });
	}

	function enregistrerDetail() {
		var t = etat.courante;
		var champLibelle = document.getElementById('pt_libelle');
		var libelle = champLibelle.value.trim();
		if (libelle.length < 3) { champLibelle.classList.add('is-invalid'); champLibelle.focus(); return; }
		var avancement = parseInt(document.getElementById('pt_range').value, 10);
		var corps = {
			libelle: libelle, description: document.getElementById('pt_description').value.trim() || null,
			date_debut: document.getElementById('pt_date_debut').value || null, date_fin: document.getElementById('pt_date_fin').value || null,
			priorite_id: document.getElementById('pt_priorite').value || null
		};
		if (avancement !== t.avancement) { corps.avancement_pourcentage = avancement; }
		if (statutChoisi && statutChoisi !== t.statut) { corps.statut = statutChoisi; }
		boutonEnregistrer.disabled = true; boutonEnregistrer.textContent = 'Enregistrement…';
		api(cfg.base + '/' + t.id, { methode: 'POST', corps: corps }).then(function (maj) {
			remplacer(maj); dessiner(); rafraichirKpis();
			afficherModal(false); toast('Tâche enregistrée.');
		}).catch(function (er) { toast(er.message, 'erreur'); boutonEnregistrer.disabled = false; boutonEnregistrer.textContent = 'ENREGISTRER'; });
	}

	document.getElementById('pt_range').addEventListener('input', function () {
		document.getElementById('pt_pct').textContent = this.value + ' %';
		this.style.setProperty('--p', this.value + '%');
	});

	modal.addEventListener('click', function (e) {
		var t = etat.courante;
		if (!t || !document.getElementById('pt_id').value) { return; }
		var s = e.target.closest('#pt_statuts button');
		if (s) {
			statutChoisi = s.dataset.statut; marquerStatut(statutChoisi);
			if (statutChoisi === 'terminee') { var r = document.getElementById('pt_range'); r.value = 100; r.style.setProperty('--p', '100%'); document.getElementById('pt_pct').textContent = '100 %'; }
			return;
		}
		var l = e.target.closest('[data-liv]');
		if (l) {
			confirmer('Retirer ce livrable ?', '', 'Retirer').then(function (ok) {
				if (!ok) { return; }
				api(cfg.base + '/' + t.id + '/livrables/' + l.dataset.liv, { methode: 'DELETE' }).then(function (m) { remplacer(m); etat.courante = m; dessinerLivrables(m); dessiner(); }).catch(function (er) { toast(er.message, 'erreur'); });
			});
			return;
		}
		if (e.target.closest('#pt_supprimer')) { supprimerTache(t); return; }
		var d = e.target.closest('[data-decision]');
		if (d) { demander(t, d.dataset.decision); }
	});

	/* valider directement ; refuser / annuler demandent un motif */
	function demander(t, decision) {
		if (decision === 'valider') { decider(t, 'valider', ''); return; }
		demanderMotif(decision === 'refuser' ? 'Refuser « ' + t.code + ' » ?' : 'Annuler « ' + t.code + ' » ?').then(function (motif) { if (motif) { decider(t, decision, motif); } });
	}

	function supprimerTache(t) {
		confirmer('Supprimer ' + t.code + ' ?', 'Cette action est définitive.', 'Supprimer').then(function (ok) {
			if (!ok) { return; }
			api(cfg.base + '/' + t.id, { methode: 'DELETE' }).then(function () { etat.taches = etat.taches.filter(function (x) { return x.id !== t.id; }); afficherModal(false); dessiner(); rafraichirKpis(); toast('Tâche supprimée.'); }).catch(function (er) { toast(er.message, 'erreur'); });
		});
	}

	function decider(t, decision, motif) {
		api(cfg.base + '/' + t.id + '/decision', { methode: 'POST', corps: { decision: decision, motif: motif } }).then(function (m) {
			remplacer(m); dessiner(); rafraichirKpis(); afficherModal(false);
			toast({ valider: 'Tâche validée.', refuser: 'Tâche refusée : l’agent est prévenu.', annuler: 'Tâche annulée : l’agent est prévenu.' }[decision]);
		}).catch(function (e) { toast(e.message, 'erreur'); });
	}

	function deposer(fichier) {
		var t = etat.courante;
		var d = new FormData();
		d.set('fichier', fichier);
		var zoneDepot = document.getElementById('pt_depot');
		zoneDepot.classList.add('charge');
		api(cfg.base + '/' + t.id + '/livrables', { methode: 'POST', corps: d }).then(function (m) { remplacer(m); etat.courante = m; dessinerLivrables(m); dessiner(); toast('Livrable déposé.'); })
			.catch(function (e) { toast(e.message, 'erreur'); zoneDepot.classList.remove('charge'); });
	}
	modal.addEventListener('change', function (e) { if (e.target.id === 'pt_fichier' && e.target.files[0] && etat.courante) { deposer(e.target.files[0]); } });
	modal.addEventListener('dragover', function (e) { var z = e.target.closest('#pt_depot'); if (z) { e.preventDefault(); z.classList.add('survol'); } });
	modal.addEventListener('dragleave', function (e) { var z = e.target.closest('#pt_depot'); if (z) { z.classList.remove('survol'); } });
	modal.addEventListener('drop', function (e) { var z = e.target.closest('#pt_depot'); if (z) { e.preventDefault(); z.classList.remove('survol'); if (e.dataTransfer.files[0] && etat.courante) { deposer(e.dataTransfer.files[0]); } } });

	/* ---------------------------------------------------------------- clavier */
	document.addEventListener('keydown', function (e) {
		var saisie = /^(INPUT|TEXTAREA|SELECT)$/.test((document.activeElement || {}).tagName || '');
		if ((e.key === 'n' || e.key === 'N') && racine.offsetParent !== null && !saisie && !e.ctrlKey && !e.metaKey && !e.altKey && !modal.classList.contains('show')) { e.preventDefault(); ouvrirFormulaire(); }
	});


	/* ---------------------------------------------------------------- démarrage */
	charger({ apres: function () { if (cfg.ouvrir) { ouvrirDetail(cfg.ouvrir); } } });
})();
