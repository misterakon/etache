<?php

namespace App\Support;

use App\Models\ActionCle;
use App\Models\ActionMesurable;
use App\Models\Axe;
use App\Models\Tache;
use DateTimeImmutable;
use DateTimeInterface;
use Illuminate\Support\Facades\DB;

/**
 * Import de la matrice (tranche 5) : analyse et validation ligne par ligne, puis création en une transaction.
 *
 * Une ligne du fichier = une tâche. Les cellules d'Axe, Résultat attendu, Action clé, Directions et Action
 * mesurable peuvent être vides (ou fusionnées) : elles reprennent alors la valeur de la ligne du dessus tant
 * qu'un niveau supérieur ne change pas. Les valeurs répétées sur chaque ligne sont aussi acceptées.
 *
 * analyser() est pur (aucun accès base) : il reçoit les lignes lues et un référentiel (voir ReferentielImport).
 */
class ImportMatrice
{
    /** Colonnes du modèle : [clé, titre, largeur, obligatoire]. Source unique (modèle, lecture, validation). */
    public const COLONNES = [
        ['axe', 'Axe', 38, true],
        ['resultat', 'Résultat attendu', 34, true],
        ['ac_code', 'Code action clé', 11, true],
        ['ac', 'Action clé', 38, true],
        ['dir_porteuse', 'Direction porteuse (code)', 16, false],
        ['dir_associees', 'Directions associées (codes, séparés par ;)', 22, false],
        ['am', 'Action mesurable', 38, true],
        ['tache', 'Tâche', 38, true],
        ['indicateur', 'Indicateur', 30, true],
        ['livrable', 'Livrable', 30, true],
        ['responsable', 'Responsable (matricule ou email)', 24, true],
        ['pilote', 'Pilote (matricule ou email)', 22, false],
        ['contributeurs', 'Contributeurs (matricules ou emails, séparés par ;)', 28, false],
        ['date_debut', 'Date début', 12, false],
        ['date_fin', 'Date limite', 12, true],
        ['priorite', 'Priorité', 12, false],
        ['statut', 'Statut', 14, false],
        ['avancement', 'Avancement (%)', 12, false],
        ['budget', 'Budget estimé (FCFA)', 16, false],
        ['observations', 'Observations', 28, false],
    ];

    public const MAX_LIGNES = 3000;

    /** Libellés de statut acceptés en plus de ceux de la base : alias => rôle. */
    private const ALIAS_STATUTS = [
        'realise' => 'termine', 'realisee' => 'termine', 'termine' => 'termine', 'terminee' => 'termine',
        'fait' => 'termine', 'faite' => 'termine', 'acheve' => 'termine', 'achevee' => 'termine',
        'non demarre' => 'non_demarre', 'non demarree' => 'non_demarre', 'a demarrer' => 'non_demarre',
        'en cours' => 'en_cours',
    ];

    /* ================================================================== */
    /* Utilitaires */
    /* ================================================================== */

    public static function titre(string $cle): string
    {
        foreach (self::COLONNES as [$k, $titre]) {
            if ($k === $cle) {
                return $titre;
            }
        }

        return $cle;
    }

    /** Texte comparable : minuscules, sans accents, espaces, tirets et ponctuation finale normalisés. */
    public static function norm(?string $s): string
    {
        $s = str_replace(["\u{00A0}", '’', '‘', '`', '´', '—', '–', '‑'], [' ', "'", "'", "'", "'", '-', '-', '-'], (string) $s);
        $s = mb_strtolower($s);
        $s = strtr($s, [
            'à' => 'a', 'â' => 'a', 'ä' => 'a', 'á' => 'a', 'ã' => 'a', 'ç' => 'c',
            'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e', 'î' => 'i', 'ï' => 'i', 'í' => 'i',
            'ô' => 'o', 'ö' => 'o', 'ó' => 'o', 'õ' => 'o', 'ù' => 'u', 'û' => 'u', 'ü' => 'u', 'ú' => 'u',
            'ÿ' => 'y', 'œ' => 'oe', 'æ' => 'ae',
        ]);

        // Une ponctuation finale (point, virgule, point-virgule, deux-points) ne distingue pas deux libellés
        return trim(rtrim(trim((string) preg_replace('/\s+/u', ' ', $s)), ' .,;:'));
    }

    /** Valeur de cellule -> texte nettoyé, ou null si vide. */
    public static function texte($valeur): ?string
    {
        if ($valeur === null || is_bool($valeur)) {
            return null;
        }
        if ($valeur instanceof DateTimeInterface) {
            return $valeur->format('d/m/Y');
        }
        if (is_int($valeur)) {
            return (string) $valeur;
        }
        if (is_float($valeur)) {
            return floor($valeur) == $valeur ? (string) (int) $valeur : rtrim(rtrim(number_format($valeur, 6, '.', ''), '0'), '.');
        }

        $s = trim(str_replace("\u{00A0}", ' ', (string) $valeur));

        return $s === '' ? null : $s;
    }

    /** Retire « Axe 1 — » devant le libellé d'un axe (le plan source l'écrit ainsi, la base non). */
    private static function nettoyerAxe(string $libelle): string
    {
        $net = preg_replace('/^\s*axe\s*\d+\s*[—–:\-]+\s*/iu', '', $libelle);

        return trim($net) !== '' ? trim($net) : $libelle;
    }

    /** Retire « R06 — » devant le libellé d'une action clé quand c'est son propre code. */
    private static function nettoyerActionCle(string $libelle, ?string $code): string
    {
        if ($code !== null && $code !== '') {
            $net = preg_replace('/^\s*'.preg_quote($code, '/').'\s*[—–:\-]+\s*/iu', '', $libelle);
            if (trim((string) $net) !== '') {
                return trim($net);
            }
        }

        return $libelle;
    }

    private static function liste(string $s): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/[;\n]+/u', $s)), fn ($x) => $x !== ''));
    }

    /** @return DateTimeImmutable|false|null  null = vide, false = invalide */
    private static function date($valeur)
    {
        if ($valeur instanceof DateTimeInterface) {
            return DateTimeImmutable::createFromInterface($valeur)->setTime(0, 0);
        }
        $s = self::texte($valeur);
        if ($s === null) {
            return null;
        }
        foreach (['j/n/Y', 'j-n-Y', 'j.n.Y', 'Y-m-d', 'j/n/y'] as $format) {
            $d = DateTimeImmutable::createFromFormat('!'.$format, $s);
            $erreurs = DateTimeImmutable::getLastErrors();
            if ($d && (! $erreurs || ($erreurs['warning_count'] === 0 && $erreurs['error_count'] === 0))) {
                $annee = (int) $d->format('Y');

                return $annee >= 2000 && $annee <= 2100 ? $d : false;
            }
        }

        return false;
    }

    /** @return array{0: ?float, 1: bool}  [valeur, valide] */
    private static function nombre($valeur): array
    {
        if ($valeur === null || $valeur === '') {
            return [null, true];
        }
        if (is_int($valeur) || is_float($valeur)) {
            return [(float) $valeur, true];
        }
        $s = str_replace([' ', "\u{00A0}", 'FCFA', 'fcfa', 'F CFA', '%'], '', trim((string) $valeur));
        if ($s === '') {
            return [null, true];
        }
        if (substr_count($s, ',') === 1 && ! str_contains($s, '.')) {
            $s = str_replace(',', '.', $s);
        }

        return preg_match('/^-?\d+(\.\d+)?$/', $s) ? [(float) $s, true] : [null, false];
    }

    private static function agent(string $jeton, array $ref, string $colonne, array &$err): ?int
    {
        $jeton = trim($jeton);
        if (str_contains($jeton, '@')) {
            $ids = $ref['agents']['par_email'][mb_strtolower($jeton)] ?? [];
            if (count($ids) > 1) {
                $err[] = "{$colonne} : l'email « {$jeton} » est partagé par plusieurs agents, utilisez le matricule.";

                return null;
            }
            $id = $ids[0] ?? null;
        } else {
            $cle = self::norm($jeton);
            $id = $ref['agents']['par_matricule'][$cle] ?? null;
            if ($id === null && ctype_digit($cle)) { // matricule numérique : zéros de tête perdus par Excel
                $id = $ref['agents']['par_matricule_sans_zero'][ltrim($cle, '0')] ?? null;
            }
        }

        if ($id === null) {
            $err[] = "{$colonne} : agent introuvable (« {$jeton} »). Indiquez son matricule ou son email.";

            return null;
        }
        if (empty($ref['agents']['actifs'][$id])) {
            $err[] = "{$colonne} : l'agent « {$jeton} » est inactif.";

            return null;
        }

        return (int) $id;
    }

    private static function direction(string $code, array $ref, string $colonne, array &$err): ?int
    {
        $id = $ref['directions'][self::norm($code)] ?? null;
        if ($id === null) {
            $err[] = "{$colonne} : code introuvable « {$code} » (utilisez le code d'une direction).";
        }

        return $id !== null ? (int) $id : null;
    }

    /* ================================================================== */
    /* Analyse */
    /* ================================================================== */

    /**
     * @param  array  $lignes  [['ligne' => n° de ligne Excel, 'v' => [valeurs indexées de 0 à 19]], ...]
     * @param  array  $ref  voir ReferentielImport::construire()
     * @param  callable|null  $progression  ($etape, $fait, $total) appelée pendant la vérification des lignes
     * @return array{lignes: array, plan: array, resume: array, nb_erreurs: int}
     */
    public static function analyser(array $lignes, array $ref, ?callable $progression = null): array
    {
        $cles = array_column(self::COLONNES, 0);
        $sortie = [];
        $plan = ['axes' => [], 'acs' => [], 'ams' => [], 'taches' => []];
        $existantes = 0;
        $vus = [];
        $ctx = ['axe' => null, 'ac' => null, 'am' => null];

        $nbLignes = count($lignes);
        $pas = max(1, (int) floor($nbLignes / 60));
        $numero = 0;

        foreach ($lignes as $l) {
            $numero++;
            if ($progression && ($numero === $nbLignes || $numero % $pas === 0)) {
                $progression('verification', $numero, $nbLignes);
            }

            $v = [];
            foreach ($cles as $i => $cle) {
                $v[$cle] = $l['v'][$i] ?? null;
            }
            $dates = ['date_debut' => $v['date_debut'], 'date_fin' => $v['date_fin']];
            $v = array_map(fn ($x) => $x instanceof DateTimeInterface ? $x : self::texte($x), $v);
            $bruts = $l['v'];
            if (count(array_filter($v, fn ($x) => $x !== null)) === 0) {
                continue; // ligne entièrement vide
            }

            $err = [];
            $warn = [];

            /* ---------- Axe ---------- */
            if ($v['axe'] !== null) {
                $axeLib = self::nettoyerAxe($v['axe']);
                $axeCle = self::norm($axeLib);
                if (! $ctx['axe'] || $ctx['axe']['cle'] !== $axeCle) {
                    $ctx['axe'] = ['cle' => $axeCle, 'libelle' => $axeLib, 'resultat' => null];
                    $ctx['ac'] = $ctx['am'] = null;
                }
            }
            if (! $ctx['axe']) {
                $err[] = 'Colonne « '.self::titre('axe').' » obligatoire.';
            } else {
                $res = $v['resultat'];
                if ($res !== null) {
                    if ($ctx['axe']['resultat'] === null) {
                        $ctx['axe']['resultat'] = $res;
                    } elseif (self::norm($res) !== self::norm($ctx['axe']['resultat'])) {
                        $err[] = "Résultat attendu différent de celui déjà indiqué pour l'axe « {$ctx['axe']['libelle']} ».";
                    }
                }
                if ($ctx['axe']['resultat'] === null) {
                    $err[] = 'Colonne « '.self::titre('resultat').' » obligatoire.';
                }
                if (mb_strlen($ctx['axe']['libelle']) > 255) {
                    $err[] = "Libellé de l'axe trop long (255 caractères maximum).";
                }
            }

            /* ---------- Action clé ---------- */
            $code = $v['ac_code'];
            if ($code !== null || $v['ac'] !== null) {
                $acLib = $v['ac'] !== null ? self::nettoyerActionCle($v['ac'], $code) : null;
                $codeCle = $code !== null ? self::norm($code) : null;
                $meme = $ctx['ac'] && (
                    ($codeCle !== null && $ctx['ac']['code_cle'] === $codeCle)
                    || ($codeCle === null && $acLib !== null && self::norm($acLib) === self::norm((string) $ctx['ac']['libelle']))
                );

                if ($meme) {
                    if ($acLib !== null) {
                        if ($ctx['ac']['libelle'] === null) {
                            $ctx['ac']['libelle'] = $acLib;
                        } elseif (self::norm($acLib) !== self::norm($ctx['ac']['libelle'])) {
                            $err[] = "Libellé différent pour l'action clé « {$ctx['ac']['code']} » (déjà indiqué : « {$ctx['ac']['libelle']} »).";
                        }
                    }
                } else {
                    $ctx['ac'] = ['code' => $code, 'code_cle' => $codeCle, 'libelle' => $acLib, 'porteuse' => null, 'associees' => []];
                    $ctx['am'] = null;
                }
            }
            if (! $ctx['ac']) {
                $err[] = 'Colonne « '.self::titre('ac').' » obligatoire.';
            } else {
                if ($ctx['ac']['code'] === null) {
                    $err[] = 'Colonne « '.self::titre('ac_code').' » obligatoire.';
                } elseif (mb_strlen($ctx['ac']['code']) > 20) {
                    $err[] = "Code de l'action clé trop long (20 caractères maximum).";
                }
                if ($ctx['ac']['libelle'] === null) {
                    $err[] = 'Colonne « '.self::titre('ac').' » obligatoire.';
                }
            }

            /* ---------- Directions (facultatives) ---------- */
            $dp = $v['dir_porteuse'] !== null ? self::direction($v['dir_porteuse'], $ref, 'Direction porteuse', $err) : null;
            $da = [];
            if ($v['dir_associees'] !== null) {
                foreach (self::liste($v['dir_associees']) as $codeDir) {
                    $idDir = self::direction($codeDir, $ref, 'Direction associée', $err);
                    if ($idDir) {
                        $da[] = $idDir;
                    }
                }
                $da = array_values(array_unique($da));
                sort($da);
            }
            if ($ctx['ac']) {
                if ($dp) {
                    if ($ctx['ac']['porteuse'] === null) {
                        $ctx['ac']['porteuse'] = $dp;
                    } elseif ($ctx['ac']['porteuse'] !== $dp) {
                        $err[] = "Direction porteuse différente de celle déjà indiquée pour l'action clé.";
                    }
                }
                if ($da) {
                    if (! $ctx['ac']['associees']) {
                        $ctx['ac']['associees'] = $da;
                    } elseif ($ctx['ac']['associees'] !== $da) {
                        $err[] = "Directions associées différentes de celles déjà indiquées pour l'action clé.";
                    }
                }
                $assocSansPorteuse = array_values(array_diff($ctx['ac']['associees'], [$ctx['ac']['porteuse']]));
                if ($assocSansPorteuse && $ctx['ac']['porteuse'] === null) {
                    $err[] = 'Direction porteuse obligatoire lorsque des directions associées sont indiquées.';
                }
            }

            /* ---------- Action mesurable ---------- */
            if ($v['am'] !== null) {
                $amCle = self::norm($v['am']);
                if (! $ctx['am'] || $ctx['am']['cle'] !== $amCle) {
                    $ctx['am'] = ['cle' => $amCle, 'libelle' => $v['am']];
                }
            }
            if (! $ctx['am']) {
                $err[] = 'Colonne « '.self::titre('am').' » obligatoire.';
            }

            /* ---------- Tâche, indicateur, livrable ---------- */
            foreach (['tache', 'indicateur', 'livrable'] as $cle) {
                if ($v[$cle] === null) {
                    $err[] = 'Colonne « '.self::titre($cle).' » obligatoire.';
                }
            }

            /* ---------- Personnes ---------- */
            $responsables = $pilotes = $contributeurs = [];
            if ($v['responsable'] === null) {
                $err[] = 'Colonne « '.self::titre('responsable').' » obligatoire.';
            } elseif (count(self::liste($v['responsable'])) > 1) {
                $err[] = 'Un seul responsable par ligne (colonne « '.self::titre('responsable').' »).';
            } elseif ($id = self::agent($v['responsable'], $ref, 'Responsable', $err)) {
                $responsables[] = $id;
            }
            if ($v['pilote'] !== null) {
                if (count(self::liste($v['pilote'])) > 1) {
                    $err[] = 'Un seul pilote par ligne (colonne « '.self::titre('pilote').' »).';
                } elseif ($id = self::agent($v['pilote'], $ref, 'Pilote', $err)) {
                    $pilotes[] = $id;
                }
            }
            if ($v['contributeurs'] !== null) {
                foreach (self::liste($v['contributeurs']) as $jeton) {
                    if ($id = self::agent($jeton, $ref, 'Contributeur', $err)) {
                        $contributeurs[] = $id;
                    }
                }
                $contributeurs = array_values(array_unique($contributeurs));
            }

            /* ---------- Dates ---------- */
            $debut = self::date($dates['date_debut']);
            $fin = self::date($dates['date_fin']);
            if ($debut === false) {
                $err[] = 'Date début invalide (format JJ/MM/AAAA).';
                $debut = null;
            }
            if ($v['date_fin'] === null) {
                $err[] = 'Colonne « '.self::titre('date_fin').' » obligatoire.';
            } elseif ($fin === false) {
                $err[] = 'Date limite invalide (format JJ/MM/AAAA).';
                $fin = null;
            }
            if ($debut && $fin && $debut > $fin) {
                $err[] = 'La date de début est postérieure à la date limite.';
            }

            /* ---------- Priorité, statut, avancement, budget ---------- */
            $prioriteId = null;
            if ($v['priorite'] !== null) {
                $prioriteId = $ref['priorites'][self::norm($v['priorite'])] ?? null;
                if ($prioriteId === null) {
                    $err[] = "Priorité inconnue : « {$v['priorite']} » (voir l'onglet Listes).";
                }
            }

            $roles = $ref['statuts']['par_role'] ?? [];
            $statutId = $roles['non_demarre'] ?? null;
            if ($v['statut'] !== null) {
                $cleStatut = self::norm($v['statut']);
                $statutId = $ref['statuts']['par_libelle'][$cleStatut]
                    ?? (isset(self::ALIAS_STATUTS[$cleStatut]) ? ($roles[self::ALIAS_STATUTS[$cleStatut]] ?? null) : null);
                if ($statutId === null) {
                    $err[] = "Statut inconnu : « {$v['statut']} » (voir l'onglet Listes).";
                }
            } elseif ($statutId === null) {
                $err[] = "Le statut « Non démarrée » est introuvable dans l'application.";
            }

            [$avancement, $avOk] = self::nombre($bruts[array_search('avancement', $cles, true)] ?? null);
            if (! $avOk || ($avancement !== null && ($avancement < 0 || $avancement > 100 || floor($avancement) != $avancement))) {
                $err[] = 'Avancement invalide : un entier de 0 à 100 est attendu.';
                $avancement = null;
            }
            $avancement = (int) ($avancement ?? 0);
            if (($ref['mode_avancement'] ?? 'manuel') === 'sous_taches') {
                // exercice "par sous-tâches" : l'avancement du fichier est ignoré (calculé à partir des sous-tâches)
                if ($avancement > 0) {
                    $warn[] = "Avancement ignoré : l'exercice est en mode « par sous-tâches » (la tâche démarrera à 0 %).";
                }
                $avancement = 0;
            } elseif ($statutId !== null && $statutId === ($roles['termine'] ?? null)) {
                $avancement = 100; // une tâche terminée est toujours à 100 %
            }

            [$budget, $buOk] = self::nombre($bruts[array_search('budget', $cles, true)] ?? null);
            if (! $buOk || ($budget !== null && $budget < 0)) {
                $err[] = 'Budget estimé invalide : un nombre positif est attendu.';
                $budget = null;
            }

            /* ---------- Rapprochement avec l'existant et doublons ---------- */
            $axeEx = $acEx = $amEx = $tacheEx = null;
            $acKey = $amKey = $tacheCle = null;
            if ($ctx['axe'] && $ctx['ac'] && $ctx['am'] && $v['tache'] !== null && $ctx['ac']['code'] !== null) {
                $axeEx = $ref['existant']['axes'][$ctx['axe']['cle']] ?? null;
                if (! $axeEx && isset($ref['existant']['axes_autres_exercices'][$ctx['axe']['cle']])) {
                    $err[] = "L'axe « {$ctx['axe']['libelle']} » existe déjà dans un autre exercice (le libellé d'un axe doit être unique).";
                }
                $acEx = $axeEx['acs'][self::norm($ctx['ac']['code'])] ?? null;
                if ($acEx && $ctx['ac']['libelle'] !== null && self::norm($acEx['libelle']) !== self::norm($ctx['ac']['libelle'])) {
                    $err[] = "Le code « {$ctx['ac']['code']} » existe déjà dans cet axe avec un autre libellé (« {$acEx['libelle']} »).";
                }
                if ($acEx && ($ctx['ac']['porteuse'] || $ctx['ac']['associees']) && empty($ctx['ac']['avertie'])) {
                    $warn[] = 'Action clé déjà existante : les directions du fichier sont ignorées.';
                    $ctx['ac']['avertie'] = true; // une seule fois par action clé
                }
                $amEx = $acEx['ams'][$ctx['am']['cle']] ?? null;
                $tacheEx = $amEx['taches'][self::norm($v['tache'])] ?? null;

                $acKey = $ctx['axe']['cle'].'|'.self::norm($ctx['ac']['code']);
                $amKey = $acKey.'|'.$ctx['am']['cle'];
                $tacheCle = $amKey.'|'.self::norm($v['tache']);
                if (isset($vus[$tacheCle])) {
                    $err[] = "Tâche en double (déjà présente à la ligne {$vus[$tacheCle]} du fichier).";
                }
                $vus[$tacheCle] ??= $l['ligne'];
            }

            if ($tacheEx) {
                $warn[] = 'Tâche déjà existante : ignorée (elle n\'est pas modifiée).';
            }

            $etat = $err ? 'erreur' : ($tacheEx ? 'existante' : 'nouvelle');

            /* ---------- Plan de création (lignes valides uniquement) ---------- */
            if (! $err) {
                $plan['axes'][$ctx['axe']['cle']] ??= [
                    'libelle' => $ctx['axe']['libelle'], 'resultat' => $ctx['axe']['resultat'], 'id' => $axeEx['id'] ?? null,
                ];
                $plan['acs'][$acKey] = [
                    'axe' => $ctx['axe']['cle'], 'code' => $ctx['ac']['code'], 'libelle' => $ctx['ac']['libelle'],
                    'direction_id' => $ctx['ac']['porteuse'],
                    'associees' => array_values(array_diff($ctx['ac']['associees'], [$ctx['ac']['porteuse']])),
                    'id' => $acEx['id'] ?? null,
                ];
                $plan['ams'][$amKey] ??= ['ac' => $acKey, 'libelle' => $ctx['am']['libelle'], 'id' => $amEx['id'] ?? null];

                if ($tacheEx) {
                    $existantes++;
                } else {
                    $plan['taches'][] = [
                        'ligne' => $l['ligne'], 'am' => $amKey, 'libelle' => $v['tache'], 'indicateur' => $v['indicateur'],
                        'livrable' => $v['livrable'], 'date_debut' => $debut ? $debut->format('Y-m-d') : null, 'date_fin' => $fin ? $fin->format('Y-m-d') : null,
                        'priorite_id' => $prioriteId, 'statut_id' => $statutId, 'avancement' => $avancement,
                        'budget' => $budget, 'observations' => $v['observations'],
                        'responsables' => $responsables, 'pilotes' => $pilotes, 'contributeurs' => $contributeurs,
                    ];
                }
            }

            $sortie[] = [
                'ligne' => $l['ligne'],
                'axe' => $ctx['axe']['libelle'] ?? null,
                'action_cle' => $ctx['ac'] ? trim(($ctx['ac']['code'] ?? '').' — '.($ctx['ac']['libelle'] ?? ''), ' —') : null,
                'action_mesurable' => $ctx['am']['libelle'] ?? null,
                'tache' => $v['tache'],
                'responsable' => $v['responsable'],
                'date_fin' => $fin instanceof DateTimeInterface ? $fin->format('d/m/Y') : null,
                'etat' => $etat,
                'erreurs' => $err,
                'avertissements' => $warn,
            ];
        }

        $nbErreurs = count(array_filter($sortie, fn ($x) => $x['etat'] === 'erreur'));

        return [
            'lignes' => $sortie,
            'plan' => $plan,
            'nb_erreurs' => $nbErreurs,
            'resume' => self::resume($sortie, $plan, $existantes, $nbErreurs),
        ];
    }

    private static function resume(array $sortie, array $plan, int $existantes, int $nbErreurs): array
    {
        $nouveaux = fn (array $niveau) => count(array_filter($niveau, fn ($x) => $x['id'] === null));

        return [
            'lignes' => count($sortie),
            'lignes_erreur' => $nbErreurs,
            'lignes_valides' => count($sortie) - $nbErreurs,
            'axes_nouveaux' => $nouveaux($plan['axes']),
            'axes_existants' => count($plan['axes']) - $nouveaux($plan['axes']),
            'actions_cles_nouvelles' => $nouveaux($plan['acs']),
            'actions_cles_existantes' => count($plan['acs']) - $nouveaux($plan['acs']),
            'actions_mesurables_nouvelles' => $nouveaux($plan['ams']),
            'actions_mesurables_existantes' => count($plan['ams']) - $nouveaux($plan['ams']),
            'taches_nouvelles' => count($plan['taches']),
            'taches_existantes' => $existantes,
        ];
    }

    /* ================================================================== */
    /* Exécution (une transaction : tout ou rien) */
    /* ================================================================== */

    /**
     * @param  array  $plan  plan issu de analyser() (sans aucune erreur)
     * @param  array  $ids  ['responsable' => id, 'pilote' => id, 'contributeur' => id, 'situation_normale' => id]
     * @return array<string, int> éléments créés
     */
    public static function executer(array $plan, int $exerciceId, array $ids, ?callable $progression = null): array
    {
        $signaler = $progression ?? fn () => null;
        $nb = [
            'axes' => count(array_filter($plan['axes'], fn ($x) => $x['id'] === null)),
            'actions_cles' => count(array_filter($plan['acs'], fn ($x) => $x['id'] === null)),
            'actions_mesurables' => count(array_filter($plan['ams'], fn ($x) => $x['id'] === null)),
            'taches' => count($plan['taches']),
        ];

        return DB::transaction(function () use ($plan, $exerciceId, $ids, $signaler, $nb) {
            $cree = ['axes' => 0, 'actions_cles' => 0, 'actions_mesurables' => 0, 'taches' => 0];

            $signaler('axes', 0, $nb['axes']);
            $ordreAxe = (int) Axe::where('exercice_id', $exerciceId)->max('ordre');
            $axeIds = [];
            foreach ($plan['axes'] as $cle => $a) {
                if ($a['id']) {
                    $axeIds[$cle] = $a['id'];

                    continue;
                }
                $axe = Axe::create([
                    'exercice_id' => $exerciceId, 'libelle' => $a['libelle'],
                    'resultat_attendu' => $a['resultat'], 'ordre' => ++$ordreAxe,
                ]);
                $axeIds[$cle] = $axe->id;
                $cree['axes']++;
                $signaler('axes', $cree['axes'], $nb['axes']);
            }

            $signaler('actions_cles', 0, $nb['actions_cles']);
            $ordreAc = [];
            $acIds = [];
            foreach ($plan['acs'] as $cle => $a) {
                if ($a['id']) {
                    $acIds[$cle] = $a['id'];

                    continue;
                }
                $axeId = $axeIds[$a['axe']];
                $ordreAc[$axeId] ??= (int) ActionCle::where('axe_id', $axeId)->max('ordre');
                $ac = ActionCle::create([
                    'axe_id' => $axeId, 'direction_id' => $a['direction_id'], 'code' => $a['code'],
                    'libelle' => $a['libelle'], 'ordre' => ++$ordreAc[$axeId],
                ]);
                if ($a['associees']) {
                    $ac->directionsAssociees()->sync($a['associees']);
                }
                $acIds[$cle] = $ac->id;
                $cree['actions_cles']++;
                $signaler('actions_cles', $cree['actions_cles'], $nb['actions_cles']);
            }

            $signaler('actions_mesurables', 0, $nb['actions_mesurables']);
            $ordreAm = [];
            $amIds = [];
            foreach ($plan['ams'] as $cle => $a) {
                if ($a['id']) {
                    $amIds[$cle] = $a['id'];

                    continue;
                }
                $acId = $acIds[$a['ac']];
                $ordreAm[$acId] ??= (int) ActionMesurable::where('action_cle_id', $acId)->max('ordre');
                $am = ActionMesurable::create([
                    'action_cle_id' => $acId, 'libelle' => $a['libelle'], 'ordre' => ++$ordreAm[$acId],
                ]);
                $amIds[$cle] = $am->id;
                $cree['actions_mesurables']++;
                $signaler('actions_mesurables', $cree['actions_mesurables'], $nb['actions_mesurables']);
            }

            $signaler('taches', 0, $nb['taches']);
            $ordreTache = [];
            foreach ($plan['taches'] as $t) {
                $amId = $amIds[$t['am']];
                $ordreTache[$amId] ??= (int) Tache::where('action_mesurable_id', $amId)->max('ordre');
                $tache = Tache::create([
                    'action_mesurable_id' => $amId, 'libelle' => $t['libelle'], 'indicateur' => $t['indicateur'],
                    'livrable' => $t['livrable'], 'ordre' => ++$ordreTache[$amId],
                    'date_debut' => $t['date_debut'], 'date_fin' => $t['date_fin'],
                    'priorite_id' => $t['priorite_id'], 'statut_execution_id' => $t['statut_id'],
                    'avancement_pourcentage' => $t['avancement'], 'budget_estime' => $t['budget'],
                    'observations' => $t['observations'],
                    'situation_temporelle_id' => $ids['situation_normale'] ?? null, // ensuite gérée par le moteur d'alertes
                    'derniere_mise_a_jour_le' => now(),
                ]);

                AvancementTache::recalculer($tache); // exercice "par sous-tâches" : aligne avancement et statut

                foreach ([
                    $ids['responsable'] => $t['responsables'],
                    $ids['pilote'] => $t['pilotes'],
                    $ids['contributeur'] => $t['contributeurs'],
                ] as $roleId => $personnes) {
                    foreach ($personnes as $personnelId) {
                        $tache->affectations()->create(['personnel_id' => $personnelId, 'role_affectation_id' => $roleId]);
                    }
                }
                $cree['taches']++;
                $signaler('taches', $cree['taches'], $nb['taches']);
            }

            $signaler('enregistrement', 0, 0); // validation de la transaction juste après

            return $cree;
        });
    }
}
