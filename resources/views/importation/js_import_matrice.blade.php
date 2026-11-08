<script type="text/javascript">

	/* ============================================================
	   IMPORT DE LA MATRICE : analyse (prévisualisation) puis import
	   JavaScript natif (aucune dépendance à jQuery) : le script démarre après le chargement de la page,
	   quel que soit l'ordre de chargement des scripts du gabarit. Seul SweetAlert (Swal) est utilisé s'il est présent.
	   En cas de problème : détail dans la console du navigateur (F12).
	   ============================================================ */
	(function () {
		const URL_ANALYSE = "{{ route('feuille_de_route.import_matrice.previsualiser') }}";
		const URL_IMPORT = "{{ route('feuille_de_route.import_matrice.importer') }}";
		const URL_TACHES = "{{ route('feuille_de_route.tache') }}";
		const URL_PROGRESSION = "{{ route('feuille_de_route.import_matrice.progression', ':id') }}";
		const TOKEN_CSRF = "{{ csrf_token() }}";

		const ETATS = {
			erreur:    ['Erreur', 'imp-pill--erreur'],
			nouvelle:  ['Nouvelle', 'imp-pill--nouvelle'],
			existante: ['Existante', 'imp-pill--existante']
		};

		let token = null;        // fichier analysé, conservé côté serveur
		let exerciceAnalyse = null;
		let lignes = [];

		const el = function (id) { return document.getElementById(id); };

		function esc(texte) {
			return String(texte == null ? '' : texte)
				.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
		}

		// Boîtes de dialogue : SweetAlert si disponible, sinon alert()/confirm() du navigateur
		function alerte(options) {
			if (window.Swal) { return window.Swal.fire(options); }
			const brut = (options.title || '') + '\n' + String(options.html || options.text || '').replace(/<br\s*\/?>/gi, '\n').replace(/<[^>]+>/g, '');
			if (options.showCancelButton) { return Promise.resolve({ isConfirmed: window.confirm(brut) }); }
			window.alert(brut);
			return Promise.resolve({ isConfirmed: true });
		}

		// POST (FormData) -> JSON ; lève { statut, json } si la réponse n'est pas un succès. XMLHttpRequest pour mesurer l'envoi du fichier.
		function envoyer(url, donnees, surEnvoi) {
			return new Promise(function (resolve, reject) {
				const xhr = new XMLHttpRequest();
				xhr.open('POST', url);
				xhr.setRequestHeader('Accept', 'application/json');
				xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
				if (surEnvoi && xhr.upload) {
					xhr.upload.onprogress = function (e) { if (e.lengthComputable) { surEnvoi(e.loaded, e.total); } };
				}
				xhr.onload = function () {
					let json = null;
					try { json = JSON.parse(xhr.responseText); } catch (e) { /* réponse non JSON */ }
					if (xhr.status >= 200 && xhr.status < 300 && json && json.status !== false) {
						resolve(json);
					} else {
						reject({ statut: xhr.status, json: json });
					}
				};
				xhr.timeout = 300000; // 5 minutes : au-delà, on rend la main avec un message clair
				xhr.onerror = function () { reject({ reseau: true }); };
				xhr.ontimeout = function () { reject({ delai: true }); };
				xhr.send(donnees);
			});
		}

		function messageErreur(erreur) {
			if (erreur && erreur.delai) {
				return "Le serveur met trop de temps à répondre (plus de 5 minutes). Vérifiez le serveur ou découpez le fichier en plusieurs imports.";
			}
			if (!erreur || erreur.statut === undefined) {
				console.error('[import matrice]', erreur);
				return "Impossible de joindre le serveur ou erreur inattendue (voir la console du navigateur).";
			}
			const j = erreur.json || {};
			if (j.erreurs_globales && j.erreurs_globales.length) { return j.erreurs_globales.map(esc).join('<br>'); }
			if (j.errors) { return Object.keys(j.errors).map(function (k) { return j.errors[k].map(esc).join('<br>'); }).join('<br>'); }
			if (j.message) { return esc(j.message); }
			if (erreur.statut === 419) { return 'Votre session a expiré : rechargez la page puis recommencez.'; }
			if (erreur.statut === 403) { return "Vous n'avez pas le droit d'effectuer cette action."; }
			if (erreur.statut === 413) { return 'Le fichier est trop volumineux pour le serveur.'; }
			if (erreur.statut === 500) { return "Erreur du serveur (détail dans storage/logs/laravel.log)."; }
			return 'Réponse inattendue du serveur (code ' + erreur.statut + '). Si votre session a expiré, reconnectez-vous.';
		}

		// sous : ligne de précision sous le libellé (ex. « 1 existante réutilisée »)
		function tuile(libelle, valeur, ton, icone, sous) {
			return '<div class="imp-kpi imp-kpi--' + ton + '"><span class="imp-kpi__icone"><i class="fa-solid ' + icone + '"></i></span>'
				+ '<span class="imp-kpi__valeur">' + valeur + '</span><span class="imp-kpi__libelle">' + esc(libelle) + '</span>'
				+ '<span class="imp-kpi__sous" style="display:block; min-height:16px; font-size:11.5px; opacity:.7;">' + (sous ? esc(sous) : '&nbsp;') + '</span></div>';
		}

		// « 1 existant réutilisé » / « 2 existantes réutilisées » (rien si 0)
		function reutilise(n, feminin) {
			if (!n) { return ''; }
			return n + ' existant' + (feminin ? 'e' : '') + (n > 1 ? 's' : '') + ' réutilisé' + (feminin ? 'e' : '') + (n > 1 ? 's' : '');
		}

		function afficherResume(r) {
			el('resume').innerHTML =
				tuile('Lignes lues', r.lignes, 'neutre', 'fa-table-list') +
				tuile('Lignes en erreur', r.lignes_erreur, r.lignes_erreur ? 'rouge' : 'neutre', 'fa-circle-xmark') +
				tuile('Axes nouveaux', r.axes_nouveaux, 'bleu', 'fa-sitemap', reutilise(r.axes_existants, false)) +
				tuile('Actions clés nouvelles', r.actions_cles_nouvelles, 'bleu', 'fa-bullseye', reutilise(r.actions_cles_existantes, true)) +
				tuile('Actions mesurables nouvelles', r.actions_mesurables_nouvelles, 'bleu', 'fa-list-check', reutilise(r.actions_mesurables_existantes, true)) +
				tuile('Tâches nouvelles', r.taches_nouvelles, 'vert', 'fa-circle-plus') +
				tuile('Tâches déjà existantes (ignorées)', r.taches_existantes, 'neutre', 'fa-box-archive');
		}

		function verdict(ton, icone, titre, texte) {
			return '<div class="imp-verdict imp-verdict--' + ton + '"><span class="imp-verdict__icone"><i class="fa-solid ' + icone + '" style="padding-top:10px; padding-left:10px"></i></span>'
				+ '<div><b>' + titre + '</b><br><span>' + texte + '</span></div></div>';
		}

		function afficherVerdict(donnees) {
			const r = donnees.resume;
			let html;
			if (r.lignes_erreur > 0) {
				html = verdict('erreur', 'fa-circle-xmark', r.lignes_erreur + ' ligne(s) en erreur',
					"Corrigez le fichier puis relancez l'analyse : rien ne sera importé tant qu'il reste une erreur.");
			} else if (r.taches_nouvelles === 0) {
				html = verdict('info', 'fa-circle-info', 'Aucune nouvelle tâche', 'Tout existe déjà dans cet exercice : il n\'y a rien à importer.');
			} else {
				html = verdict('ok', 'fa-circle-check', 'Le fichier est valide',
					'<b>' + r.taches_nouvelles + " tâche(s)</b> seront créées dans l'exercice " + esc(donnees.exercice) + '. Vérifiez le détail ci-dessous puis cliquez sur « Importer ».');
			}
			el('verdict').innerHTML = html;
		}

		function afficherLignes() {
			const filtre = el('filtre_lignes').value;

			// Compteurs et bouton actif du sélecteur de filtre
			const nb = { toutes: lignes.length, erreur: 0, nouvelle: 0, existante: 0 };
			lignes.forEach(function (l) { if (nb[l.etat] !== undefined) { nb[l.etat]++; } });
			document.querySelectorAll('[data-nb]').forEach(function (n) { n.textContent = nb[n.getAttribute('data-nb')] || 0; });
			document.querySelectorAll('.imp-seg__btn').forEach(function (b) { b.classList.toggle('imp-actif', b.getAttribute('data-filtre') === filtre); });

			const visibles = lignes.filter(function (l) { return filtre === 'toutes' || l.etat === filtre; });

			if (!visibles.length) {
				el('corps_lignes').innerHTML = '<tr><td colspan="9" class="imp-vide"><i class="fa-regular fa-folder-open"></i>Aucune ligne dans cette catégorie.</td></tr>';
				return;
			}

			el('corps_lignes').innerHTML = visibles.map(function (l) {
				const e = ETATS[l.etat] || ['', ''];
				const messages = l.erreurs.map(function (m) { return '<div class="imp-msg imp-msg--erreur"><i class="fa-solid fa-circle-exclamation"></i><span>' + esc(m) + '</span></div>'; })
					.concat(l.avertissements.map(function (m) { return '<div class="imp-msg imp-msg--info"><i class="fa-solid fa-circle-info"></i><span>' + esc(m) + '</span></div>'; })).join('');
				return '<tr class="imp-ligne--' + esc(l.etat) + '">'
					+ '<td class="imp-num-ligne">' + l.ligne + '</td>'
					+ '<td><span class="imp-pill ' + e[1] + '">' + e[0] + '</span></td>'
					+ '<td>' + esc(l.axe) + '</td>'
					+ '<td>' + esc(l.action_cle) + '</td>'
					+ '<td>' + esc(l.action_mesurable) + '</td>'
					+ '<td>' + esc(l.tache) + '</td>'
					+ '<td>' + esc(l.responsable) + '</td>'
					+ '<td style="white-space:nowrap;">' + esc(l.date_fin) + '</td>'
					+ '<td>' + (messages || '—') + '</td>'
					+ '</tr>';
			}).join('');
		}

		/* ---------- Étapes (bandeau du haut), zone de dépôt du fichier ---------- */
		function etape(n) {
			const s = el('imp_stepper');
			if (s) { s.setAttribute('data-etape', n); }
		}

		function majDepot() {
			const depot = el('depot');
			if (!depot) { return; }
			const f = el('fichier').files[0];
			const valide = !f || /\.xlsx$/i.test(f.name);
			depot.classList.toggle('imp-depot--rempli', !!f && valide);
			depot.classList.toggle('imp-depot--erreur', !!f && !valide);
			el('depot_icone').className = !f ? 'fa-solid fa-cloud-arrow-up' : (valide ? 'fa-solid fa-file-circle-check' : 'fa-solid fa-file-circle-xmark');
			if (!f) {
				el('depot_titre').innerHTML = 'Glissez votre fichier ici ou <u>parcourez</u>';
				el('depot_sous').textContent = 'Format .xlsx · 5 Mo maximum · onglet « Matrice »';
				return;
			}
			el('depot_titre').textContent = f.name;
			el('depot_sous').textContent = valide ? formaterOctets(f.size) + ' · prêt à être analysé' : 'Format non pris en charge : un fichier .xlsx est attendu';
		}

		function selectionnerFiltre(filtre) {
			el('filtre_lignes').value = filtre;
			afficherLignes();
		}

		function invalider() {
			token = null;
			el('btn_importer').disabled = true;
			el('zone_resultat').hidden = true;
			etape(el('fichier').files.length ? 2 : 1);
		}


		/* ---------- Barre de progression ----------
		   Chaque étape a un poids dans la barre globale. Le serveur indique l'étape en cours et le nombre
		   d'éléments traités (route « progression », interrogée toutes les 350 ms) ; l'envoi du fichier est mesuré
		   par le navigateur. La barre ne recule jamais. */
		const ETAPES_ANALYSE = [
			['envoi',        'Envoi du fichier',                                       10],
			['lecture',      'Lecture du fichier Excel',                               20],
			['referentiel',  "Chargement des agents, directions et de l'existant",     10],
			['verification', 'Vérification des lignes',                                50],
			['finalisation', 'Préparation du résultat',                                10]
		];
		const ETAPES_IMPORT = [
			['lecture',            'Relecture du fichier',                 10],
			['referentiel',        'Chargement du référentiel',             5],
			['verification',       'Contrôle final des lignes',            20],
			['axes',               'Création des axes',                     5],
			['actions_cles',       'Création des actions clés',             5],
			['actions_mesurables', 'Création des actions mesurables',       7],
			['taches',             'Création des tâches et des affectations', 38],
			['enregistrement',     'Enregistrement en base de données',    10]
		];

		const suivi = { etapes: [], courante: 0, fait: 0, total: 0, octets: false, pourcent: 0, minuteur: null, debut: 0, bouton: null, texteBouton: '' };

		function secondesEcoulees() { return Math.max(0, Math.round((Date.now() - suivi.debut) / 1000)); }

		function identifiantSuivi() {
			const octets = new Uint8Array(20);
			(window.crypto || window.msCrypto).getRandomValues(octets);
			return Array.prototype.map.call(octets, function (b) { return ('0' + b.toString(16)).slice(-2); }).join('');
		}

		function formaterOctets(n) {
			if (n >= 1048576) { return (n / 1048576).toFixed(1).replace('.', ',') + ' Mo'; }
			return Math.max(1, Math.round(n / 1024)) + ' Ko';
		}

		function pourcentageGlobal() {
			const total = suivi.etapes.reduce(function (s, e) { return s + e[2]; }, 0);
			let acquis = 0;
			for (let i = 0; i < suivi.courante; i++) { acquis += suivi.etapes[i][2]; }
			const courante = suivi.etapes[suivi.courante];
			if (courante && suivi.total > 0) { acquis += courante[2] * Math.min(1, suivi.fait / suivi.total); }
			return Math.min(99, Math.round(acquis / total * 100)); // 100 % seulement quand l'opération est terminée
		}

		function progressionPresente() { return !!(el('zone_progression') && el('prog_barre')); }

		function dessinerProgression(etat) {
			// etat : undefined (en cours), 'fini' ou 'erreur'
			if (!progressionPresente()) { return; }
			const pct = etat === 'fini' ? 100 : Math.max(suivi.pourcent, pourcentageGlobal());
			suivi.pourcent = pct;

			const barre = el('prog_barre');
			barre.style.width = pct + '%';
			barre.classList.toggle('imp-fini', etat === 'fini');
			barre.classList.toggle('imp-erreur', etat === 'erreur');
			el('prog_pourcent').textContent = pct + ' %';
			el('prog_aria').setAttribute('aria-valuenow', pct);

			const courante = suivi.etapes[suivi.courante];
			el('prog_libelle').textContent = etat === 'fini' ? 'Terminé' : (etat === 'erreur' ? 'Opération interrompue' : (courante ? courante[1] : ''));
			let compteur = '';
			if (!etat && suivi.total > 0) {
				compteur = suivi.octets
					? ' — ' + formaterOctets(suivi.fait) + ' / ' + formaterOctets(suivi.total)
					: ' — ' + suivi.fait + ' / ' + suivi.total;
			}
			if (!etat && suivi.debut) { compteur += (compteur ? ' · ' : ' — ') + secondesEcoulees() + ' s'; }
			el('prog_compteur').textContent = compteur;

			el('prog_etapes').innerHTML = suivi.etapes.map(function (e, i) {
				const fait = etat === 'fini' || i < suivi.courante;
				const encours = !etat && i === suivi.courante;
				const icone = fait ? '<i class="fa-solid fa-circle-check"></i>'
					: (encours ? '<i class="fa-solid fa-spinner fa-spin"></i>' : '<i class="fa-regular fa-circle"></i>');
				const classe = fait ? 'imp-etape--fait' : (encours ? 'imp-etape--cours' : '');
				return '<li class="imp-etape ' + classe + '"><span class="imp-etape__icone">' + icone + '</span><span>' + esc(e[1]) + '</span>'
					+ (encours && suivi.total > 0 && !suivi.octets ? '<span class="imp-compteur">' + suivi.fait + ' / ' + suivi.total + '</span>' : '') + '</li>';
			}).join('');
		}

		function ouvrirProgression(titre, etapes) {
			suivi.etapes = etapes;
			suivi.courante = 0;
			suivi.fait = 0;
			suivi.total = 0;
			suivi.octets = false;
			suivi.pourcent = 0;
			suivi.debut = Date.now();
			if (!progressionPresente()) { return; } // vue sans carte de progression : l'opération continue sans barre
			el('prog_titre').textContent = titre;
			el('zone_resultat').hidden = true;
			el('zone_progression').hidden = false;
			dessinerProgression();
			el('zone_progression').scrollIntoView({ behavior: 'smooth', block: 'center' });
		}

		function appliquerEtape(cle, fait, total, octets) {
			const i = suivi.etapes.findIndex(function (e) { return e[0] === cle; });
			if (i < 0 || i < suivi.courante) { return; } // étape inconnue ou déjà dépassée
			suivi.courante = i;
			suivi.fait = fait || 0;
			suivi.total = total || 0;
			suivi.octets = !!octets;
			dessinerProgression();
		}

		function surveiller(id) {
			arreterSurveillance();
			const url = URL_PROGRESSION.replace(':id', id);
			suivi.minuteur = setInterval(async function () {
				try {
					const reponse = await fetch(url, { headers: { 'Accept': 'application/json' }, credentials: 'same-origin', cache: 'no-store' });
					if (!reponse.ok) { return; }
					const etat = await reponse.json();
					if (etat && etat.etape) { appliquerEtape(etat.etape, etat.fait, etat.total, false); }
				} catch (e) { /* le suivi est facultatif : l'opération continue */ }
			}, 350);
			// Le temps écoulé progresse même si le serveur ne répond pas au suivi
			suivi.chrono = setInterval(function () {
				if (suivi.bouton) { suivi.bouton.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> ' + suivi.texteBouton + ' (' + secondesEcoulees() + ' s)'; }
				dessinerProgression();
			}, 1000);
		}

		function arreterSurveillance() {
			if (suivi.minuteur) { clearInterval(suivi.minuteur); suivi.minuteur = null; }
			if (suivi.chrono) { clearInterval(suivi.chrono); suivi.chrono = null; }
		}

		const pause = function (ms) { return new Promise(function (r) { setTimeout(r, ms); }); };

		// Termine la barre : 100 % en vert (succès) ou rouge (échec), puis la referme
		async function cloreProgression(etat) {
			arreterSurveillance();
			dessinerProgression(etat);
			if (!progressionPresente()) { return; }
			await pause(etat === 'fini' ? 700 : 900);
			el('zone_progression').hidden = true;
		}

		function protegerDepart(actif) {
			window.onbeforeunload = actif ? function (e) { e.preventDefault(); e.returnValue = ''; return ''; } : null;
		}

		/* ---------- Analyse ---------- */
		async function analyser() {
			const fichier = el('fichier').files[0];
			if (!fichier) {
				alerte({ icon: 'warning', title: 'Fichier manquant', text: 'Choisissez un fichier Excel (.xlsx).' });
				return;
			}

			const bouton = el('btn_analyser');
			bouton.disabled = true;
			bouton.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Analyse en cours…';
			el('btn_importer').disabled = true;

			try {
				const idSuivi = identifiantSuivi();
				const donnees = new FormData();
				donnees.append('_token', TOKEN_CSRF);
				donnees.append('exercice_id', el('exercice_id').value);
				donnees.append('progression', idSuivi);
				donnees.append('fichier', fichier);

				ouvrirProgression('Analyse du fichier', ETAPES_ANALYSE);
				suivi.bouton = bouton; suivi.texteBouton = 'Analyse en cours…';
				surveiller(idSuivi);

				const r = await envoyer(URL_ANALYSE, donnees, function (envoye, total) { appliquerEtape('envoi', envoye, total, true); });
				await cloreProgression('fini');
				token = r.token;
				exerciceAnalyse = el('exercice_id').value;
				lignes = r.lignes;
				afficherVerdict(r);
				afficherResume(r.resume);
				el('filtre_lignes').value = r.resume.lignes_erreur > 0 ? 'erreur' : 'toutes';
				afficherLignes();
				el('note_troncature').hidden = !r.tronque;
				el('zone_resultat').hidden = false;
				el('btn_importer').disabled = !r.importable;
				etape(3);
				el('zone_resultat').scrollIntoView({ behavior: 'smooth', block: 'start' });
			} catch (erreur) {
				console.error('[import matrice] analyse', erreur);
				try { await cloreProgression('erreur'); } catch (e) { arreterSurveillance(); }
				invalider();
				alerte({ icon: 'error', title: 'Analyse impossible', html: messageErreur(erreur) });
			} finally {
				arreterSurveillance();
				suivi.bouton = null;
				bouton.disabled = false;
				bouton.innerHTML = '<i class="fa-solid fa-magnifying-glass"></i> Analyser le fichier';
			}
		}

		/* ---------- Import ---------- */
		async function importer() {
			if (!token) { return; }

			const choix = await alerte({
				title: 'Importer la matrice ?',
				text: "Les éléments seront créés dans l'exercice sélectionné. Cette opération est « tout ou rien ».",
				icon: 'question',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				cancelButtonText: 'Annuler',
				confirmButtonText: 'Oui, importer'
			});
			if (!choix.isConfirmed) { return; }

			const bouton = el('btn_importer');
			bouton.disabled = true;
			bouton.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Import en cours…';
			el('btn_analyser').disabled = true;

			try {
				const idSuivi = identifiantSuivi();
				const donnees = new FormData();
				donnees.append('_token', TOKEN_CSRF);
				donnees.append('exercice_id', exerciceAnalyse);
				donnees.append('token', token);
				donnees.append('progression', idSuivi);

				ouvrirProgression('Import de la matrice', ETAPES_IMPORT);
				suivi.bouton = bouton; suivi.texteBouton = 'Import en cours…';
				surveiller(idSuivi);
				protegerDepart(true); // avertit si l'on quitte la page pendant l'import

				const r = await envoyer(URL_IMPORT, donnees);
				await cloreProgression('fini');
				invalider();
				el('fichier').value = '';
				majDepot();
				etape(4);
				const c = r.cree;
				const suite = await alerte({
					icon: 'success',
					title: 'Import terminé',
					html: c.axes + ' axe(s), ' + c.actions_cles + ' action(s) clé(s), ' + c.actions_mesurables + ' action(s) mesurable(s) et <b>'
						+ c.taches + ' tâche(s)</b> créés.' + (r.ignorees ? '<br>' + r.ignorees + ' tâche(s) déjà existante(s) ignorée(s).' : ''),
					showCancelButton: true,
					confirmButtonText: 'Voir les tâches',
					cancelButtonText: 'Fermer'
				});
				if (suite.isConfirmed) { window.location.href = URL_TACHES; }
			} catch (erreur) {
				console.error('[import matrice] import', erreur);
				try { await cloreProgression('erreur'); } catch (e) { arreterSurveillance(); }
				el('zone_resultat').hidden = !token; // l'analyse reste affichée : le fichier peut être corrigé puis relancé
				alerte({ icon: 'error', title: 'Import refusé', html: messageErreur(erreur) });
			} finally {
				arreterSurveillance();
				suivi.bouton = null;
				protegerDepart(false);
				el('btn_analyser').disabled = false;
				bouton.innerHTML = '<i class="fa-solid fa-file-import"></i> Importer';
				bouton.disabled = !token;
			}
		}

		function demarrer() {
			if (!el('btn_analyser')) { console.error("[import matrice] bouton « Analyser » introuvable : page incomplète."); return; }
			el('btn_analyser').addEventListener('click', analyser);
			el('btn_importer').addEventListener('click', importer);
			el('filtre_lignes').addEventListener('change', afficherLignes);
			document.querySelectorAll('.imp-seg__btn').forEach(function (b) {
				b.addEventListener('click', function () { selectionnerFiltre(b.getAttribute('data-filtre')); });
			});

			// Changer l'exercice ou le fichier invalide l'analyse affichée
			el('exercice_id').addEventListener('change', invalider);
			el('fichier').addEventListener('change', function () { majDepot(); invalider(); });

			// Glisser-déposer du fichier sur la zone de dépôt
			const depot = el('depot');
			if (depot) {
				['dragenter', 'dragover'].forEach(function (nom) {
					depot.addEventListener(nom, function (e) { e.preventDefault(); depot.classList.add('imp-depot--survol'); });
				});
				['dragleave', 'drop'].forEach(function (nom) {
					depot.addEventListener(nom, function (e) { e.preventDefault(); depot.classList.remove('imp-depot--survol'); });
				});
				depot.addEventListener('drop', function (e) {
					const fichiers = e.dataTransfer && e.dataTransfer.files;
					if (fichiers && fichiers.length) {
						el('fichier').files = fichiers;
						el('fichier').dispatchEvent(new Event('change', { bubbles: true }));
					}
				});
			}
			majDepot();
			etape(el('fichier').files.length ? 2 : 1);
		}

		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', demarrer);
		} else {
			demarrer();
		}
	})();

</script>
