<?php

namespace App\Support;

use App\Models\ClasseAgent;
use App\Models\Direction;
use App\Models\Echelon;
use App\Models\Emploi;
use App\Models\Fonction;
use App\Models\Grade;
use App\Models\ListeValeur;
use App\Models\MandatResponsable;
use App\Models\Personnel;
use App\Models\ProfilAcces;
use App\Models\Service;
use App\Models\SousDirection;
use App\Models\TypeAgent;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Import Excel du personnel : analyse (aucune écriture) puis application en une seule transaction.
 * « Tout ou rien » : une seule erreur bloque tout. Matricule connu = mise à jour (les cellules vides sont ignorées),
 * matricule inconnu = création. Les valeurs de référence (direction, service, emploi…) doivent exister : jamais créées en silence.
 */
class ImportPersonnel
{
    public const MAX_LIGNES = 500;

    private const LIBELLES = [
        'service_id' => 'Service', 'fonction' => 'Fonction', 'fonction_id' => 'Fonction', 'grade_id' => 'Grade', 'classe_id' => 'Classe', 'echelon_id' => 'Échelon',
        'date_premiere_prise_service' => '1re prise de service', 'date_prise_service' => 'Prise de service', 'date_depart_retraite' => 'Départ à la retraite',
    ];

    private static function n(?string $t): string
    {
        return DoublonsPersonnel::norme($t);
    }

    /** @return array<string, Collection> */
    private static function referentiels(): array
    {
        $parNom = fn (Collection $c, callable $cle) => $c->groupBy(fn ($x) => self::n($cle($x)));
        $liste = fn (string $nom) => ListeValeur::where('liste', $nom)->get()->flatMap(fn ($v) => [[self::n($v->libelle), $v], [self::n($v->code), $v]])->groupBy(0)->map(fn ($g) => $g->first()[1]);

        return [
            'directions' => $parNom(Direction::all(), fn ($x) => $x->nom),
            'sous_directions' => $parNom(SousDirection::with('direction')->get(), fn ($x) => $x->nom),
            'services' => $parNom(Service::with('sousDirection.direction', 'directionDirecte')->get(), fn ($x) => $x->nom),
            'types' => $parNom(TypeAgent::all(), fn ($x) => $x->libelle),
            'emplois' => $parNom(Emploi::all(), fn ($x) => $x->libelle),
            'grades' => $parNom(Grade::all(), fn ($x) => $x->code),
            'classes' => $parNom(ClasseAgent::all(), fn ($x) => $x->libelle),
            'echelons' => $parNom(Echelon::all(), fn ($x) => (string) $x->numero),
            'civilite' => $liste('civilite'),
            'genre' => $liste('genre'),
            'statut_matrimonial' => $liste('statut_matrimonial'),
            'fonctions' => $parNom(Fonction::where('actif', true)->get(), fn ($x) => $x->libelle),
        ];
    }

    /**
     * @return array{erreurs_fichier: list<string>, lignes: list<array>, resume: array<string, int>}
     */
    public static function analyser(string $chemin): array
    {
        $lecture = PersonnelExcel::lire($chemin);
        if ($lecture['erreurs']) {
            return ['erreurs_fichier' => $lecture['erreurs'], 'lignes' => [], 'resume' => []];
        }
        $source = collect($lecture['lignes'])->reject(fn ($l) => str_starts_with(self::n($l['valeurs']['matricule'] ?? ''), 'exemple'))->values();
        if ($source->isEmpty()) {
            return ['erreurs_fichier' => ["Le fichier ne contient aucune ligne d'agent à importer."], 'lignes' => [], 'resume' => []];
        }
        if ($source->count() > self::MAX_LIGNES) {
            return ['erreurs_fichier' => ['Le fichier dépasse '.self::MAX_LIGNES.' agents : découpez-le en plusieurs fichiers.'], 'lignes' => [], 'resume' => []];
        }

        $ref = self::referentiels();
        $index = DoublonsPersonnel::index();
        $existants = Personnel::all()->keyBy(fn ($a) => self::n($a->matricule));
        $emailsUsers = User::pluck('personnel_id', 'email')->mapWithKeys(fn ($pid, $email) => [mb_strtolower($email) => $pid]);
        $vusMatricule = [];
        $vusEmail = [];
        $lignes = [];

        foreach ($source as $l) {
            $v = $l['valeurs'];
            $erreurs = [];
            $avert = [];
            $d = []; // colonnes de la table personnel (valeurs renseignées uniquement)

            // --- identité obligatoire
            foreach (['matricule' => 'Matricule', 'nom' => 'Nom', 'prenom' => 'Prénom'] as $cle => $titre) {
                if (blank($v[$cle] ?? null)) {
                    $erreurs[] = "{$titre} obligatoire.";
                }
            }
            $matricule = trim((string) ($v['matricule'] ?? ''));
            $cleMat = self::n($matricule);
            if ($cleMat !== '') {
                if (isset($vusMatricule[$cleMat])) {
                    $erreurs[] = "Matricule en double dans le fichier (déjà ligne {$vusMatricule[$cleMat]}).";
                }
                $vusMatricule[$cleMat] = $l['num'];
            }
            $agent = $cleMat !== '' ? ($existants[$cleMat] ?? null) : null;
            if ($agent?->estArchive()) {
                $erreurs[] = "L'agent {$agent->nomComplet()} est archivé : restaurez-le avant de le mettre à jour.";
            }

            foreach (['matricule', 'nom', 'prenom', 'lieu_naissance', 'nom_pere', 'nom_mere', 'lieu_habitation'] as $cle) {
                if (filled($v[$cle] ?? null)) {
                    $d[$cle] = trim($v[$cle]);
                }
            }
            if (isset($d['nom'])) {
                $d['nom'] = mb_strtoupper($d['nom']);
            }

            // --- dates
            foreach (['date_naissance', 'date_premiere_prise_service', 'date_prise_service', 'date_depart_retraite'] as $cle) {
                $x = $v[$cle] ?? null;
                if ($x === null) {
                    continue;
                }
                if (str_starts_with($x, '!')) {
                    $erreurs[] = PersonnelExcel::COLONNES[$cle][0].' « '.substr($x, 1).' » : date illisible (format attendu jj/mm/aaaa).';
                } else {
                    $d[$cle] = $x;
                }
            }
            if (isset($d['date_naissance'])) {
                $age = Carbon::parse($d['date_naissance'])->age;
                if ($age < 16 || $age > 90) {
                    $erreurs[] = "Date de naissance invraisemblable ({$age} ans).";
                }
            }
            if (isset($d['date_naissance'], $d['date_prise_service']) && Carbon::parse($d['date_prise_service'])->lt(Carbon::parse($d['date_naissance'])->addYears(14))) {
                $erreurs[] = 'La prise de service ne peut pas précéder les 14 ans de l\'agent.';
            }
            if (isset($d['date_naissance'], $d['date_depart_retraite']) && Carbon::parse($d['date_depart_retraite'])->lte(Carbon::parse($d['date_naissance']))) {
                $erreurs[] = 'La date de départ à la retraite est antérieure à la naissance.';
            }

            // --- contacts
            if (filled($v['email'] ?? null)) {
                $email = mb_strtolower(trim($v['email']));
                if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $erreurs[] = "E-mail « {$v['email']} » invalide.";
                } else {
                    $d['email'] = $email;
                    if (isset($vusEmail[$email])) {
                        $erreurs[] = "E-mail déjà utilisé ligne {$vusEmail[$email]} du fichier.";
                    }
                    $vusEmail[$email] = $l['num'];
                    $autre = $index->first(fn ($a) => mb_strtolower((string) $a->email) === $email && (! $agent || $a->id !== $agent->id));
                    if ($autre) {
                        $erreurs[] = "E-mail déjà utilisé par {$autre->prenom} {$autre->nom} (matricule {$autre->matricule}).";
                    }
                }
            }
            foreach (['telephone', 'telephone_2'] as $cle) {
                if (filled($v[$cle] ?? null)) {
                    if (! preg_match('/^[+0-9 ().\-]{8,20}$/', trim($v[$cle]))) {
                        $erreurs[] = PersonnelExcel::COLONNES[$cle][0].' « '.$v[$cle].' » invalide.';
                    } else {
                        $d[$cle] = trim($v[$cle]);
                    }
                }
            }
            if (filled($v['nombre_enfants'] ?? null)) {
                if (! ctype_digit((string) $v['nombre_enfants']) || (int) $v['nombre_enfants'] > 30) {
                    $erreurs[] = "Nombre d'enfants « {$v['nombre_enfants']} » invalide (0 à 30).";
                } else {
                    $d['nombre_enfants'] = (int) $v['nombre_enfants'];
                }
            }

            // --- listes de valeurs et référentiels
            $resoudre = function (string $cle, string $refCle, string $colonne) use (&$v, &$d, &$erreurs, $ref) {
                if (blank($v[$cle] ?? null)) {
                    return;
                }
                $trouve = $ref[$refCle]->get(self::n($v[$cle]));
                $objet = $trouve instanceof Collection ? ($trouve->count() === 1 ? $trouve->first() : null) : $trouve;
                if (! $objet && $refCle === 'genre') {
                    $objet = $ref['genre']->first(fn ($g, $k) => str_starts_with($k, self::n($v[$cle]) === 'm' ? 'masc' : (self::n($v[$cle]) === 'f' ? 'fem' : '#')));
                }
                if (! $objet) {
                    $erreurs[] = PersonnelExcel::COLONNES[$cle][0].' « '.$v[$cle].' » inconnu'.($trouve instanceof Collection && $trouve->count() > 1 ? ' (plusieurs correspondances)' : '').'.';

                    return;
                }
                $d[$colonne] = $objet->id;
            };
            $resoudre('civilite', 'civilite', 'civilite_id');
            $resoudre('genre', 'genre', 'genre_id');
            $resoudre('statut_matrimonial', 'statut_matrimonial', 'statut_matrimonial_id');
            $resoudre('type_agent', 'types', 'type_agent_id');
            $resoudre('emploi', 'emplois', 'emploi_id');
            $resoudre('grade', 'grades', 'grade_id');
            $resoudre('classe', 'classes', 'classe_id');
            $resoudre('echelon', 'echelons', 'echelon_id');

            // --- rattachement : service (avec contrôle de cohérence direction / sous-direction)
            $direction = filled($v['direction'] ?? null) ? $ref['directions']->get(self::n($v['direction']))?->first() : null;
            $sousDirection = filled($v['sous_direction'] ?? null) ? $ref['sous_directions']->get(self::n($v['sous_direction']))?->first() : null;
            if (filled($v['direction'] ?? null) && ! $direction) {
                $erreurs[] = "Direction « {$v['direction']} » inconnue.";
            }
            if (filled($v['sous_direction'] ?? null) && ! $sousDirection) {
                $erreurs[] = "Sous-direction « {$v['sous_direction']} » inconnue.";
            }
            if (filled($v['service'] ?? null)) {
                $candidats = $ref['services']->get(self::n($v['service']), collect());
                if ($direction) {
                    $candidats = $candidats->filter(fn ($s) => $s->direction()?->id === $direction->id);
                }
                if ($sousDirection) {
                    $candidats = $candidats->filter(fn ($s) => $s->sous_direction_id === $sousDirection->id);
                }
                if ($candidats->count() === 1) {
                    $d['service_id'] = $candidats->first()->id;
                } else {
                    $erreurs[] = $candidats->isEmpty()
                        ? "Service « {$v['service']} » inconnu".($direction || $sousDirection ? ' dans cette direction ou sous-direction' : '').'.'
                        : "Service « {$v['service']} » ambigu : précisez la direction ou la sous-direction.";
                }
            } elseif ($direction || $sousDirection) {
                $avert[] = 'Direction / sous-direction sans service : le rattachement n\'est pas enregistré (un agent se rattache à un service).';
            }

            // --- fonction : texte libre, sauf les postes à titulaire unique (qui passent par « Affecter à un poste »)
            if (filled($v['fonction'] ?? null)) {
                $f = $ref['fonctions']->get(self::n($v['fonction']))?->first();
                if ($f && $f->categorie === Fonction::CAT_MANDAT) {
                    $avert[] = "« {$f->libelle} » est un poste à titulaire unique : utilisez « Affecter à un poste » ; la fonction n'est pas importée.";
                } elseif ($agent && MandatResponsable::where('personnel_id', $agent->id)->actifA()->exists()) {
                    $avert[] = 'Cet agent occupe un poste de responsable : sa fonction n\'est pas modifiée.';
                } else {
                    $d['fonction'] = $f?->libelle ?? trim($v['fonction']);
                    if ($f) {
                        $d['fonction_id'] = $f->id;
                    }
                }
            }

            // --- compte utilisateur
            $creerCompte = in_array(self::n($v['creer_compte'] ?? null), ['oui', 'o', 'yes', 'y', '1', 'x'], true);
            if ($creerCompte) {
                $emailCompte = $d['email'] ?? $agent?->email;
                if (blank($emailCompte)) {
                    $erreurs[] = "Compte demandé mais l'agent n'a pas d'e-mail.";
                } elseif ($agent && User::where('personnel_id', $agent->id)->exists()) {
                    $avert[] = 'Cet agent a déjà un compte : aucun compte créé.';
                    $creerCompte = false;
                } elseif ($emailsUsers->has(mb_strtolower($emailCompte))) {
                    $erreurs[] = "L'e-mail {$emailCompte} est déjà utilisé par un compte existant.";
                }
            }

            // --- doublon probable (autre fiche très ressemblante)
            if ($cleMat !== '' && ! $erreurs) {
                $dup = DoublonsPersonnel::trouver(
                    ['nom' => $d['nom'] ?? $agent?->nom, 'prenom' => $d['prenom'] ?? $agent?->prenom, 'date_naissance' => $d['date_naissance'] ?? $agent?->date_naissance?->toDateString(), 'telephone' => $d['telephone'] ?? null],
                    $agent?->id, $index
                );
                if ($dup && self::n($dup['agent']->matricule) !== $cleMat) {
                    $erreurs[] = "Doublon probable de {$dup['agent']->prenom} {$dup['agent']->nom} (matricule {$dup['agent']->matricule}) : {$dup['raison']}.";
                }
            }

            // --- changements (mise à jour)
            $changements = [];
            if ($agent) {
                foreach ($d as $cle => $valeur) {
                    $actuel = $agent->getAttribute($cle);
                    $actuel = $actuel instanceof \DateTimeInterface ? $actuel->format('Y-m-d') : (string) $actuel;
                    if ((string) $valeur !== $actuel) {
                        $changements[] = $cle;
                    }
                }
                if (! $changements && ! $creerCompte && ! $erreurs) {
                    $avert[] = 'Aucun changement par rapport à la fiche existante.';
                }
            }

            $lignes[] = [
                'num' => $l['num'], 'matricule' => $matricule, 'nom' => $d['nom'] ?? ($v['nom'] ?? ''), 'prenom' => $d['prenom'] ?? ($v['prenom'] ?? ''),
                'action' => $agent ? 'maj' : 'creer', 'agent_id' => $agent?->id, 'donnees' => $d, 'creer_compte' => $creerCompte,
                'changements' => array_values(array_unique(array_map(fn ($c) => self::LIBELLES[$c] ?? (JournalFiche::CHAMPS[$c] ?? $c), $changements))),
                'erreurs' => $erreurs, 'avertissements' => $avert,
            ];
        }

        $collection = collect($lignes);

        return [
            'erreurs_fichier' => [],
            'lignes' => $lignes,
            'resume' => [
                'lignes' => $collection->count(),
                'creations' => $collection->where('action', 'creer')->where('erreurs', [])->count(),
                'mises_a_jour' => $collection->where('action', 'maj')->where('erreurs', [])->count(),
                'erreurs' => $collection->filter(fn ($l) => $l['erreurs'] !== [])->count(),
                'avertissements' => $collection->filter(fn ($l) => $l['avertissements'] !== [])->count(),
                'comptes' => $collection->where('creer_compte', true)->count(),
            ],
        ];
    }

    /**
     * Applique une analyse sans erreur (ré-analysée juste avant : la prévisualisation n'est jamais considérée comme fiable).
     *
     * @return array{crees: int, maj: int, comptes: list<array>}
     */
    public static function appliquer(array $analyse, User $par): array
    {
        abort_if($analyse['erreurs_fichier'] || ($analyse['resume']['erreurs'] ?? 1) > 0, 422, "L'import contient des erreurs : rien n'a été importé.");
        $profil = ProfilAcces::bySlug(ProfilAcces::AGENT) ?? ProfilAcces::bySlug(ProfilAcces::RESPONSABLE_TACHE);
        $crees = 0;
        $maj = 0;
        $comptes = [];

        DB::transaction(function () use ($analyse, $profil, &$crees, &$maj, &$comptes) {
            foreach ($analyse['lignes'] as $l) {
                if ($l['action'] === 'creer') {
                    $agent = Personnel::create($l['donnees'] + ['actif' => true] + (isset($l['donnees']['fonction']) ? [] : ['fonction' => 'Agent']));
                    JournalFiche::creation($agent);
                    $crees++;
                } else {
                    $agent = Personnel::findOrFail($l['agent_id']);
                    $avant = JournalFiche::avant($agent);
                    $agent->update($l['donnees']);
                    JournalFiche::modification($agent, $avant);
                    $maj++;
                }
                if ($l['creer_compte'] && $profil) {
                    $r = CreationComptes::pour($agent->fresh(), $profil);
                    if (is_array($r)) {
                        $comptes[] = $r;
                    }
                }
            }
        });

        Journal::noter('personnel', 'import', "Import Excel du personnel : {$crees} fiche(s) créée(s), {$maj} mise(s) à jour, ".count($comptes).' compte(s) créé(s)', null, null, ['crees' => $crees, 'maj' => $maj, 'comptes' => count($comptes)]);

        return ['crees' => $crees, 'maj' => $maj, 'comptes' => $comptes];
    }
}
