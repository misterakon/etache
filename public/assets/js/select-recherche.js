/* =====================================================================
   Listes déroulantes avec RECHERCHE (agents, destinataires…), sans dépendance.
   - liste simple  : devient un champ où l'on tape pour filtrer (accents et majuscules ignorés), flèches + Entrée au clavier ;
   - liste multiple: un champ de recherche s'ajoute au-dessus et masque les lignes qui ne correspondent pas (les lignes cochées restent visibles).
   Les <select> d'origine restent dans la page : formulaires, valeurs et évènements « change » fonctionnent comme avant.
   Activation : classe .js-select-recherche (ou attribut data-recherche) ; sont aussi traités automatiquement :
   personnel_id, affectations[...][], #imp_autre. Chargement multiple sans effet (idempotent).
   ===================================================================== */
(function () {
	if (window.SelectRecherche) { return; }

	var SELECTEURS = 'select.js-select-recherche, select[data-recherche], select[name="personnel_id"], select[name^="affectations["], select#imp_autre';
	var instances = [];
	var minuteur = null;

	function norme(s) { return (s || '').normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase(); }
	function mots(q) { return norme(q).split(/\s+/).filter(Boolean); }
	function correspond(texte, m) { var c = norme(texte); return m.every(function (x) { return c.indexOf(x) >= 0; }); }

	/* ---------- liste simple ---------- */
	function combo(select) {
		var champ = document.createElement('input');
		champ.type = 'text'; champ.autocomplete = 'off'; champ.spellcheck = false;
		champ.className = 'sr__champ form-control' + (select.classList.contains('form-select-sm') ? ' form-control-sm' : '');
		champ.setAttribute('role', 'combobox'); champ.setAttribute('aria-expanded', 'false'); champ.setAttribute('aria-autocomplete', 'list');
		champ.placeholder = select.getAttribute('data-placeholder') || 'Rechercher…';
		var liste = document.createElement('ul');
		liste.className = 'sr__liste'; liste.setAttribute('role', 'listbox');

		var requis = select.required;
		if (requis) { select.required = false; champ.required = true; }
		select.insertAdjacentElement('afterend', champ);
		select.style.display = 'none';
		select.setAttribute('tabindex', '-1');

		var ouvert = false; var actif = -1; var entrees = [];
		var derniere = { index: -2, nb: -1, desactive: null };

		function libelleCourant() {
			var o = select.options[select.selectedIndex];
			return o && o.value !== '' ? o.text.trim() : '';
		}
		function maj() {
			if (!ouvert) { champ.value = libelleCourant(); }
			champ.disabled = select.disabled;
			derniere = { index: select.selectedIndex, nb: select.options.length, desactive: select.disabled };
		}

		function rendre(q) {
			var m = mots(q); liste.innerHTML = ''; entrees = []; actif = -1;
			var groupe = null; var n = 0;
			Array.prototype.forEach.call(select.options, function (o) {
				if (o.disabled) { return; }
				var g = o.parentElement && o.parentElement.tagName === 'OPTGROUP' ? o.parentElement.label : null;
				if (o.value === '') { if (m.length === 0) { ajouter(o, null, true); n++; } return; }
				if (!correspond(o.text + ' ' + (g || ''), m)) { return; }
				if (g !== groupe) { groupe = g; if (g) { var h = document.createElement('li'); h.className = 'sr__groupe'; h.textContent = g; liste.appendChild(h); } }
				ajouter(o, g, false); n++;
			});
			if (!n) { var v = document.createElement('li'); v.className = 'sr__vide'; v.textContent = 'Aucun résultat'; liste.appendChild(v); }
			var sel = entrees.findIndex(function (e) { return e.o.index === select.selectedIndex && e.o.value !== ''; });
			if (q === '' && sel >= 0) { activer(sel); }
		}
		function ajouter(o, g, vide) {
			var li = document.createElement('li');
			li.className = 'sr__option' + (vide ? ' sr__option--vide' : '') + (o.index === select.selectedIndex && !vide ? ' sr__option--sel' : '');
			li.setAttribute('role', 'option'); li.textContent = vide ? (o.text.trim() || '— Aucun —') : o.text.trim();
			li.addEventListener('mousedown', function (e) { e.preventDefault(); choisir(o.index); });
			liste.appendChild(li); entrees.push({ o: o, li: li });
		}
		function activer(i) {
			if (!entrees.length) { return; }
			actif = (i + entrees.length) % entrees.length;
			entrees.forEach(function (e, k) { e.li.classList.toggle('sr__option--active', k === actif); });
			var li = entrees[actif].li; if (li.scrollIntoView) { li.scrollIntoView({ block: 'nearest' }); }
		}
		function positionner() {
			var r = champ.getBoundingClientRect();
			var bas = window.innerHeight - r.bottom - 14; var haut = r.top - 14;
			var placeBas = bas >= 180 || bas >= haut;
			liste.style.left = r.left + 'px'; liste.style.width = Math.max(r.width, 240) + 'px';
			liste.style.maxHeight = Math.max(140, Math.min(300, placeBas ? bas : haut)) + 'px';
			if (placeBas) { liste.style.top = (r.bottom + 4) + 'px'; liste.style.bottom = 'auto'; }
			else { liste.style.bottom = (window.innerHeight - r.top + 4) + 'px'; liste.style.top = 'auto'; }
		}
		function ouvrir() {
			if (ouvert || select.disabled) { return; }
			ouvert = true; champ.setAttribute('aria-expanded', 'true');
			rendre(''); document.body.appendChild(liste); liste.classList.add('sr__liste--ouverte'); positionner();
			champ.select();
		}
		function fermer(restaurer) {
			if (!ouvert) { return; }
			ouvert = false; champ.setAttribute('aria-expanded', 'false');
			if (liste.parentNode) { liste.parentNode.removeChild(liste); }
			liste.classList.remove('sr__liste--ouverte');
			if (restaurer !== false) { champ.value = libelleCourant(); }
		}
		function choisir(index) {
			select.selectedIndex = index; fermer(false); maj();
			select.dispatchEvent(new Event('change', { bubbles: true }));
		}

		champ.addEventListener('focus', ouvrir);
		champ.addEventListener('click', ouvrir);
		champ.addEventListener('input', function () { if (!ouvert) { ouvrir(); } rendre(champ.value); if (champ.value.trim() !== '') { activer(entrees.findIndex(function (e) { return e.o.value !== ''; })); } positionner(); });
		champ.addEventListener('blur', function () { fermer(true); });
		champ.addEventListener('keydown', function (e) {
			if (e.key === 'ArrowDown') { e.preventDefault(); if (!ouvert) { ouvrir(); } else { activer(actif + 1); } }
			else if (e.key === 'ArrowUp') { e.preventDefault(); if (!ouvert) { ouvrir(); } else { activer(actif - 1); } }
			else if (e.key === 'Enter') { if (ouvert) { e.preventDefault(); if (actif >= 0 && entrees[actif]) { choisir(entrees[actif].o.index); } } }
			else if (e.key === 'Escape') { if (ouvert) { e.stopPropagation(); fermer(true); } }
			else if (e.key === 'Tab') { fermer(true); }
		});
		window.addEventListener('resize', function () { if (ouvert) { positionner(); } });
		document.addEventListener('scroll', function (e) { if (ouvert && e.target !== liste) { positionner(); } }, true);

		/* affectations programmatiques (select.value = …, select.selectedIndex = …, form.reset()) */
		['value', 'selectedIndex'].forEach(function (prop) {
			var d = Object.getOwnPropertyDescriptor(HTMLSelectElement.prototype, prop);
			if (!d || !d.set) { return; }
			Object.defineProperty(select, prop, { configurable: true, get: function () { return d.get.call(select); }, set: function (v) { d.set.call(select, v); maj(); } });
		});
		if (select.form) { select.form.addEventListener('reset', function () { setTimeout(maj, 0); }); }

		maj();
		instances.push({ select: select, champ: champ, maj: maj, etat: function () { return derniere; } });
	}

	/* ---------- liste multiple : champ de filtre au-dessus ---------- */
	function multiple(select) {
		var filtre = document.createElement('input');
		filtre.type = 'search'; filtre.autocomplete = 'off';
		filtre.className = 'sr__filtre form-control form-control-sm'; filtre.placeholder = select.getAttribute('data-placeholder') || 'Rechercher dans la liste…';
		select.insertAdjacentElement('beforebegin', filtre);
		function appliquer() {
			var m = mots(filtre.value);
			Array.prototype.forEach.call(select.options, function (o) {
				var g = o.parentElement && o.parentElement.tagName === 'OPTGROUP' ? o.parentElement.label : '';
				o.hidden = m.length > 0 && !o.selected && !correspond(o.text + ' ' + g, m);
			});
		}
		filtre.addEventListener('input', appliquer);
		select.addEventListener('change', appliquer);
		instances.push({ select: select, champ: filtre, maj: appliquer, etat: function () { return { index: -3, nb: select.options.length, desactive: null }; } });
	}

	function appliquer(racine) {
		Array.prototype.forEach.call((racine || document).querySelectorAll(SELECTEURS), function (select) {
			if (select.dataset.srOk) { return; }
			select.dataset.srOk = '1';
			if (select.multiple) { multiple(select); } else { combo(select); }
		});
	}

	function rafraichir() { instances.forEach(function (i) { i.maj(); }); }

	function demarrer() {
		appliquer(document);
		if (window.MutationObserver) {
			new MutationObserver(function () { clearTimeout(minuteur); minuteur = setTimeout(function () { appliquer(document); }, 60); })
				.observe(document.body, { childList: true, subtree: true });
		}
		/* jQuery .val() modifie les options sans passer par select.value : on resynchronise l'affichage régulièrement */
		setInterval(function () {
			instances.forEach(function (i) {
				var e = i.etat(); var s = i.select;
				if (e.index === -3) { return; }
				if (s.selectedIndex !== e.index || s.options.length !== e.nb || s.disabled !== e.desactive) { i.maj(); }
			});
		}, 400);
	}

	window.SelectRecherche = { appliquer: appliquer, rafraichir: rafraichir };
	if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', demarrer); } else { demarrer(); }
})();
