/* Page « Pilotage et analyses » : graphiques interactifs (ApexCharts).
 * Les données viennent du serveur (<script type="application/json" id="pl_donnees">) ; chaque emplacement
 * est un <div class="pl-apex" data-g="nom">. Survol, infobulles, légende cliquable, zoom (évolution),
 * export PNG / SVG / CSV par la barre d'outils, animation à l'ouverture. */
(function () {
	'use strict';

	var noeud = document.getElementById('pl_donnees');
	if (!noeud || typeof ApexCharts === 'undefined') { return; }

	var D;
	try { D = JSON.parse(noeud.textContent); } catch (e) { return; }
	if (!D) { return; }

	var POLICE = "'Rubik', system-ui, sans-serif";
	var TEXTE = '#1F2937';
	var GRILLE = '#E8EDF5';

	var fr = {
		name: 'fr',
		options: {
			toolbar: { exportToSVG: 'Télécharger en SVG', exportToPNG: 'Télécharger en PNG', exportToCSV: 'Télécharger en CSV', menu: 'Menu', selection: 'Sélection', selectionZoom: 'Zoom', zoomIn: 'Agrandir', zoomOut: 'Réduire', pan: 'Déplacer', reset: 'Réinitialiser' }
		}
	};

	function base(type, hauteur, extra) {
		var o = {
			chart: {
				type: type, height: hauteur, fontFamily: POLICE, foreColor: TEXTE, locales: [fr], defaultLocale: 'fr',
				toolbar: { show: true, tools: { download: true, selection: false, zoom: false, zoomin: false, zoomout: false, pan: false, reset: false } },
				animations: { enabled: true, easing: 'easeinout', speed: 900, animateGradually: { enabled: true, delay: 120 }, dynamicAnimation: { enabled: true, speed: 350 } },
				dropShadow: { enabled: false }
			},
			grid: { borderColor: GRILLE, strokeDashArray: 4, padding: { left: 6, right: 12 } },
			legend: { fontFamily: POLICE, fontSize: '13px', fontWeight: 500, labels: { colors: TEXTE }, markers: { radius: 99 }, itemMargin: { horizontal: 10, vertical: 4 } },
			tooltip: { style: { fontFamily: POLICE, fontSize: '13px' } },
			dataLabels: { enabled: false },
			noData: { text: 'Aucune donnée à afficher', style: { fontFamily: POLICE, fontSize: '14px' } }
		};
		return fusion(o, extra || {});
	}

	function fusion(a, b) {
		Object.keys(b).forEach(function (k) {
			if (b[k] && typeof b[k] === 'object' && !Array.isArray(b[k]) && a[k] && typeof a[k] === 'object' && !Array.isArray(a[k])) { fusion(a[k], b[k]); } else { a[k] = b[k]; }
		});
		return a;
	}

	function somme(t) { return t.reduce(function (x, y) { return x + y; }, 0); }
	function court(s, n) { s = String(s); return s.length > n ? s.slice(0, n - 1) + '…' : s; }
	function vide(el, texte) { el.innerHTML = '<div class="cs-vide" style="padding:28px 8px;text-align:center;color:#374151">' + texte + '</div>'; }

	var constructeurs = {
		courbe: function (d) {
			return base('area', 330, {
				series: [{ name: 'Attendu (tâches échues)', data: d.attendu }, { name: 'Réalisé (tâches terminées)', data: d.realise }],
				colors: ['#6B7280', '#0F8B8D'],
				stroke: { curve: 'smooth', width: [3, 4], dashArray: [7, 0] },
				fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: .05, opacityTo: .02, stops: [0, 100] } },
				markers: { size: 4, strokeWidth: 2, strokeColors: '#fff', hover: { size: 7 } },
				xaxis: { categories: d.labels, axisBorder: { show: false }, axisTicks: { show: false }, labels: { style: { fontSize: '12px' } } },
				yaxis: { min: 0, max: 100, tickAmount: 5, labels: { formatter: function (v) { return Math.round(v) + ' %'; } } },
				tooltip: { shared: true, intersect: false, y: { formatter: function (v) { return v + ' %'; } } },
				legend: { position: 'top', horizontalAlign: 'left' }
			});
		},

		statuts: function (d) {
			var total = somme(d.map(function (x) { return x.valeur; }));
			if (total <= 0) { return null; }
			return base('donut', 330, {
				series: d.map(function (x) { return x.valeur; }), labels: d.map(function (x) { return x.libelle; }), colors: d.map(function (x) { return x.couleur; }),
				stroke: { width: 3, colors: ['#fff'] },
				plotOptions: { pie: { expandOnClick: true, donut: { size: '68%', labels: { show: true, name: { fontSize: '14px', offsetY: 18 }, value: { fontSize: '30px', fontWeight: 700, offsetY: -14, formatter: function (v) { return v; } },
					total: { show: true, showAlways: true, label: 'Tâches', fontSize: '14px', color: '#374151', formatter: function () { return total; } } } } } },
				dataLabels: { enabled: false },
				legend: { position: 'right', offsetY: 10, formatter: function (nom, o) { return nom + '  <b>' + o.w.globals.series[o.seriesIndex] + '</b>'; } },
				tooltip: { y: { formatter: function (v) { return v + ' tâche(s) · ' + Math.round(v / total * 100) + ' %'; } } },
				responsive: [{ breakpoint: 600, options: { legend: { position: 'bottom' } } }]
			});
		},

		axes: function (d) {
			if (!d.length) { return null; }
			return base('bar', Math.max(240, d.length * 62 + 60), {
				series: [{ name: 'Avancement', data: d.map(function (x) { return x.avancement; }) }],
				colors: d.map(function (x) { return x.couleur; }),
				plotOptions: { bar: { horizontal: true, distributed: true, borderRadius: 8, barHeight: '58%', dataLabels: { position: 'top' } } },
				dataLabels: { enabled: true, textAnchor: 'start', offsetX: 8, formatter: function (v) { return String(v).replace('.', ',') + ' %'; }, style: { fontSize: '13px', fontWeight: 700, colors: [TEXTE] }, background: { enabled: false } },
				xaxis: { categories: d.map(function (x) { return court(x.libelle, 46); }), max: 100, labels: { formatter: function (v) { return Math.round(v) + ' %'; } } },
				yaxis: { labels: { maxWidth: 260, style: { fontSize: '12.5px' } } },
				legend: { show: false },
				tooltip: { custom: function (o) { var x = d[o.dataPointIndex]; return '<div class="pl-bulle"><b>' + x.libelle + '</b><br>Avancement : <b>' + String(x.avancement).replace('.', ',') + ' %</b><br>' + x.terminees + ' tâche(s) terminée(s) sur ' + x.total + '</div>'; } }
			});
		},

		directions: function (d) {
			if (!d.length) { return null; }
			return base('bar', Math.max(260, d.length * 64 + 90), {
				series: [
					{ name: 'Terminées', data: d.map(function (x) { return x.terminees; }) },
					{ name: 'En cours', data: d.map(function (x) { return x.en_cours; }) },
					{ name: 'Non démarrées', data: d.map(function (x) { return x.non_demarrees; }) },
					{ name: 'Bloquées / suspendues', data: d.map(function (x) { return x.autres; }) }
				],
				colors: ['#1F8A5B', '#2B6CB0', '#B8C0CC', '#C77D0A'],
				chart: { stacked: true },
				plotOptions: { bar: { horizontal: true, borderRadius: 6, borderRadiusApplication: 'end', barHeight: '56%' } },
				stroke: { width: 2, colors: ['#fff'] },
				dataLabels: { enabled: true, style: { fontSize: '12px', fontWeight: 700, colors: ['#fff'] }, formatter: function (v) { return v > 0 ? v : ''; } },
				xaxis: { categories: d.map(function (x) { return court(x.libelle, 40); }), labels: { formatter: function (v) { return Math.round(v); } } },
				yaxis: { labels: { maxWidth: 240, style: { fontSize: '12.5px' } } },
				legend: { position: 'top', horizontalAlign: 'left' },
				tooltip: { shared: false, y: { formatter: function (v) { return v + ' tâche(s)'; } } }
			});
		},

		retards: function (d) {
			if (!d.length) { return null; }
			return base('bar', Math.max(240, d.length * 58 + 60), {
				series: [{ name: 'En retard', data: d.map(function (x) { return x.valeur; }) }],
				colors: ['#C2372E'],
				fill: { type: 'gradient', gradient: { shade: 'light', type: 'horizontal', gradientToColors: ['#F0877F'], opacityFrom: 1, opacityTo: 1, stops: [0, 100] } },
				plotOptions: { bar: { horizontal: true, borderRadius: 8, barHeight: '56%', dataLabels: { position: 'top' } } },
				dataLabels: { enabled: true, textAnchor: 'start', offsetX: 8, style: { fontSize: '13px', fontWeight: 700, colors: [TEXTE] } },
				xaxis: { categories: d.map(function (x) { return court(x.libelle, 36); }), labels: { formatter: function (v) { return Math.round(v); } } },
				yaxis: { labels: { maxWidth: 220, style: { fontSize: '12.5px' } } },
				legend: { show: false },
				tooltip: { custom: function (o) { var x = d[o.dataPointIndex]; return '<div class="pl-bulle"><b>' + x.libelle + '</b><br>' + x.valeur + ' tâche(s) en retard' + (x.non_justifies ? '<br><span style="color:#C2372E">dont ' + x.non_justifies + ' non justifiée(s)</span>' : '') + '</div>'; } }
			});
		},

		semaines: function (d) {
			return base('bar', 300, {
				series: [{ name: 'Tâches à échéance', data: d.valeurs }],
				colors: ['#6D5BD0'],
				fill: { type: 'gradient', gradient: { shade: 'light', type: 'vertical', gradientToColors: ['#B9AEF2'], opacityFrom: 1, opacityTo: 1, stops: [0, 100] } },
				plotOptions: { bar: { borderRadius: 8, columnWidth: '46%', dataLabels: { position: 'top' } } },
				dataLabels: { enabled: true, offsetY: -22, style: { fontSize: '13px', fontWeight: 700, colors: [TEXTE] } },
				xaxis: { categories: d.labels, axisBorder: { show: false }, axisTicks: { show: false } },
				yaxis: { labels: { formatter: function (v) { return Math.round(v); } } },
				legend: { show: false },
				tooltip: { y: { formatter: function (v) { return v + ' tâche(s)'; } } }
			});
		},

		evolution: function (d) {
			return base('area', 320, {
				chart: { zoom: { enabled: true, type: 'x', autoScaleYaxis: true }, toolbar: { show: true, autoSelected: 'zoom', tools: { download: true, selection: false, zoom: true, zoomin: true, zoomout: true, pan: true, reset: true } } },
				series: [{ name: 'En retard', data: d.retards }, { name: 'Retards non justifiés', data: d.non_justifies }],
				colors: ['#C2372E', '#C77D0A'],
				stroke: { curve: 'smooth', width: [3.5, 3], dashArray: [0, 6] },
				fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: .35, opacityTo: .02, stops: [0, 95] } },
				markers: { size: 0, hover: { size: 6 } },
				xaxis: { categories: d.labels, tickAmount: Math.min(10, d.labels.length), axisBorder: { show: false }, axisTicks: { show: false } },
				yaxis: { min: 0, forceNiceScale: true, labels: { formatter: function (v) { return Math.round(v); } } },
				tooltip: { shared: true, intersect: false },
				legend: { position: 'top', horizontalAlign: 'left' }
			});
		},

		courrier: function (d) {
			return base('bar', 320, {
				series: [{ name: 'Courriers reçus', data: d.recus }, { name: 'Courriers expédiés', data: d.departs }],
				colors: ['#2B6CB0', '#1F8A5B'],
				plotOptions: { bar: { borderRadius: 6, columnWidth: '62%' } },
				stroke: { width: 2, colors: ['transparent'] },
				xaxis: { categories: d.labels, axisBorder: { show: false }, axisTicks: { show: false } },
				yaxis: { labels: { formatter: function (v) { return Math.round(v); } } },
				legend: { position: 'top', horizontalAlign: 'left' },
				tooltip: { shared: true, intersect: false }
			});
		},

		diligences: function (d) {
			var total = somme(d.map(function (x) { return x.valeur; }));
			if (total <= 0) { return null; }
			return base('donut', 300, {
				series: d.map(function (x) { return x.valeur; }), labels: d.map(function (x) { return x.libelle; }), colors: d.map(function (x) { return x.couleur; }),
				stroke: { width: 3, colors: ['#fff'] },
				plotOptions: { pie: { donut: { size: '66%', labels: { show: true, name: { fontSize: '13px', offsetY: 16 }, value: { fontSize: '28px', fontWeight: 700, offsetY: -12 }, total: { show: true, showAlways: true, label: 'Diligences', fontSize: '13px', color: '#374151', formatter: function () { return total; } } } } } },
				legend: { position: 'bottom' },
				tooltip: { y: { formatter: function (v) { return v + ' diligence(s)'; } } }
			});
		},

		charge: function (d) {
			if (!d.length) { return null; }
			return base('bar', Math.max(240, d.length * 48 + 60), {
				series: [{ name: 'Charge', data: d.map(function (x) { return x.valeur; }) }],
				colors: d.map(function (x) { return x.surcharge ? '#C2372E' : '#2B6CB0'; }),
				plotOptions: { bar: { horizontal: true, distributed: true, borderRadius: 7, barHeight: '58%', dataLabels: { position: 'top' } } },
				dataLabels: { enabled: true, textAnchor: 'start', offsetX: 8, style: { fontSize: '13px', fontWeight: 700, colors: [TEXTE] } },
				xaxis: { categories: d.map(function (x) { return court(x.libelle, 30); }), labels: { formatter: function (v) { return Math.round(v); } } },
				yaxis: { labels: { maxWidth: 200, style: { fontSize: '12.5px' } } },
				legend: { show: false },
				tooltip: { y: { formatter: function (v) { return v + ' élément(s) en charge'; } } }
			});
		}
	};

	var cle = { courbe: 'courbe', statuts: 'statuts', axes: 'axes', directions: 'directions', retards: 'retards', semaines: 'semaines', evolution: 'evolution', courrier: 'courrier', diligences: 'diligences', charge: 'charge' };

	function tracer() {
		document.querySelectorAll('.pl-apex[data-g]').forEach(function (el) {
			var nom = el.getAttribute('data-g');
			var donnees = D[cle[nom]];
			if (!constructeurs[nom] || donnees === null || donnees === undefined || el.dataset.trace) { return; }
			var options = constructeurs[nom](donnees);
			if (!options) { vide(el, 'Aucune donnée à afficher pour ce périmètre.'); return; }
			el.dataset.trace = '1';
			el.innerHTML = '';
			new ApexCharts(el, options).render();
		});
	}

	if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', tracer); } else { tracer(); }
})();
