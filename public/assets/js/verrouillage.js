/* Verrouillage de session après inactivité (public/assets/js/verrouillage.js).
 *
 *  - suit l'activité de l'utilisateur (souris, clavier, défilement, toucher), partagée entre les onglets ouverts ;
 *  - quelques secondes avant le délai, affiche une fenêtre avec compte à rebours (« Rester connecté ») ;
 *  - à l'échéance, demande le verrouillage au serveur puis affiche l'écran de verrouillage ;
 *  - signale régulièrement au serveur que l'utilisateur est actif (sinon le serveur verrouillerait trop tôt) ;
 *  - si le serveur répond « verrouillé » (423) à un appel Ajax, affiche l'écran de verrouillage.
 *
 * Configuration : attributs data-* de l'élément #verrou-config (voir layout/script.blade.php).
 */
(function () {
	'use strict';

	var conf = document.getElementById('verrou-config');
	if (!conf) { return; }

	var LIMITE = (parseInt(conf.dataset.minutes, 10) || 20) * 60 * 1000;
	var AVERT = Math.min((parseInt(conf.dataset.avertissement, 10) || 0) * 1000, LIMITE - 5000);
	var URL_VERROU = conf.dataset.urlVerrou;
	var URL_PING = conf.dataset.urlPing;
	var URL_LOGIN = conf.dataset.urlLogin;
	var JETON = conf.dataset.jeton;
	var PING_TOUS = Math.min(5 * 60 * 1000, Math.max(30 * 1000, Math.floor(LIMITE / 3)));
	var CLE = 'etache_derniere_activite';   // partagée entre les onglets ouverts

	var local = Date.now();
	var dernierePing = Date.now();
	var verrouille = false;
	var voile = null;
	var nbSecondes = null;
	var anneau = null;
	var carte = null;
	var texteSec = null;
	var CIRCONF = 2 * Math.PI * 54;

	function lire() { try { return parseInt(localStorage.getItem(CLE), 10) || local; } catch (e) { return local; } }
	function noter() { local = Date.now(); try { localStorage.setItem(CLE, String(local)); } catch (e) { /* stockage indisponible */ } }
	function limiter(f, ms) { var t = 0; return function () { var n = Date.now(); if (n - t > ms) { t = n; f(); } }; }

	// Pendant l'avertissement, seule une action explicite (bouton) prolonge la session.
	var surActivite = limiter(function () { if (!voile || voile.hidden) { noter(); } }, 3000);
	['mousemove', 'mousedown', 'keydown', 'scroll', 'touchstart', 'click'].forEach(function (ev) {
		window.addEventListener(ev, surActivite, { passive: true, capture: true });
	});
	noter();

	function aller(url) { verrouille = true; window.location.href = url; }

	function enteteAjax() {
		return { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': JETON };
	}

	function verrouiller(auto) {
		if (verrouille) { return; }
		verrouille = true;
		var donnees = new FormData();
		donnees.append('_token', JETON);
		donnees.append('retour', window.location.href);
		if (auto) { donnees.append('auto', '1'); }
		fetch(URL_VERROU, { method: 'POST', body: donnees, credentials: 'same-origin', headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
			.then(function (r) { return r.json(); })
			.then(function (j) { window.location.href = j.redirect; })
			.catch(function () { window.location.reload(); });
	}

	function signaler(force) {
		var n = Date.now();
		if (!force && (n - lire() > PING_TOUS || n - dernierePing < PING_TOUS)) { return; }   // inactif, ou signal déjà envoyé récemment
		dernierePing = n;
		fetch(URL_PING, { method: 'POST', credentials: 'same-origin', headers: enteteAjax() });
	}

	/* ---------- Fenêtre d'avertissement ---------- */
	function construire() {
		voile = document.createElement('div');
		voile.className = 'vr-voile';
		voile.hidden = true;
		voile.setAttribute('role', 'alertdialog');
		voile.setAttribute('aria-modal', 'true');
		voile.setAttribute('aria-labelledby', 'vr-titre');
		voile.setAttribute('aria-describedby', 'vr-texte');
		voile.innerHTML =
			'<div class="vr-carte">' +
				'<div class="vr-anneau">' +
					'<svg viewBox="0 0 120 120" aria-hidden="true">' +
						'<circle class="vr-anneau__fond" cx="60" cy="60" r="54" fill="none" stroke-width="9"></circle>' +
						'<circle class="vr-anneau__reste" cx="60" cy="60" r="54" fill="none" stroke-width="9" stroke-linecap="round" stroke-dasharray="' + CIRCONF.toFixed(2) + '" stroke-dashoffset="0"></circle>' +
					'</svg>' +
					'<div class="vr-anneau__nombre"><strong>0</strong><span>secondes</span></div>' +
				'</div>' +
				'<h3 id="vr-titre">Êtes-vous toujours là ?</h3>' +
				'<p id="vr-texte">Faute d’activité, votre session va être verrouillée dans quelques secondes. Cliquez pour continuer à travailler.</p>' +
				'<div class="vr-actions">' +
					'<button type="button" class="vr-btn vr-btn--prim" data-vr="rester"><i class="fa-solid fa-hand-pointer"></i> Rester connecté</button>' +
					'<button type="button" class="vr-btn vr-btn--sec" data-vr="verrouiller"><i class="fa-solid fa-lock"></i> Verrouiller maintenant</button>' +
				'</div>' +
			'</div>';
		document.body.appendChild(voile);

		carte = voile.querySelector('.vr-carte');
		anneau = voile.querySelector('.vr-anneau__reste');
		texteSec = voile.querySelector('.vr-anneau__nombre strong');

		voile.querySelector('[data-vr="rester"]').addEventListener('click', function () {
			noter();
			signaler(true);
			masquer();
		});
		voile.querySelector('[data-vr="verrouiller"]').addEventListener('click', function () { verrouiller(false); });
		voile.addEventListener('keydown', function (e) { if (e.key === 'Escape') { noter(); signaler(true); masquer(); } });
	}

	function afficher() {
		if (!voile) { construire(); }
		if (voile.hidden) {
			voile.hidden = false;
			var b = voile.querySelector('[data-vr="rester"]');
			if (b) { b.focus(); }
		}
	}

	function masquer() {
		if (voile && !voile.hidden) { voile.hidden = true; }
	}

	function majCompteARebours(resteMs) {
		var s = Math.max(0, Math.ceil(resteMs / 1000));
		if (s === nbSecondes) { return; }
		nbSecondes = s;
		texteSec.textContent = String(s);
		anneau.style.strokeDashoffset = String(CIRCONF * (1 - Math.min(1, resteMs / AVERT)));
		carte.classList.toggle('vr-urgent', s <= 10);
	}

	/* ---------- Boucle principale (chaque seconde) ---------- */
	setInterval(function () {
		if (verrouille) { return; }
		var reste = LIMITE - (Date.now() - lire());

		if (reste <= 0) { verrouiller(true); return; }

		if (AVERT > 0 && reste <= AVERT) {
			afficher();
			majCompteARebours(reste);
		} else {
			masquer();
			nbSecondes = null;
			signaler(false);
		}
	}, 1000);

	/* ---------- Le serveur répond « verrouillé » (423) ou « session expirée » (401) ---------- */
	if (window.jQuery) {
		window.jQuery(document).ajaxError(function (e, xhr) {
			if (xhr.status === 423 && xhr.responseJSON && xhr.responseJSON.redirect) { aller(xhr.responseJSON.redirect); }
			else if (xhr.status === 401) { aller(URL_LOGIN); }
		});
	}
	var fetchOrigine = window.fetch;
	window.fetch = function () {
		return fetchOrigine.apply(this, arguments).then(function (r) {
			if (r.status === 423) {
				r.clone().json().then(function (j) { aller(j.redirect); }).catch(function () { aller(URL_VERROU.replace('verrouiller', 'verrouille')); });
			}
			return r;
		});
	};
})();
