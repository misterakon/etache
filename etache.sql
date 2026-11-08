-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : dim. 08 nov. 2026 à 22:41
-- Version du serveur : 8.4.7
-- Version de PHP : 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `etache`
--

-- --------------------------------------------------------

--
-- Structure de la table `actions_cles`
--

DROP TABLE IF EXISTS `actions_cles`;
CREATE TABLE IF NOT EXISTS `actions_cles` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `axe_id` bigint UNSIGNED NOT NULL,
  `direction_id` bigint UNSIGNED DEFAULT NULL,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ordre` smallint UNSIGNED NOT NULL DEFAULT '0',
  `libelle` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `actions_cles_axe_id_foreign` (`axe_id`),
  KEY `actions_cles_direction_id_foreign` (`direction_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `actions_cles`
--

INSERT INTO `actions_cles` (`id`, `axe_id`, `direction_id`, `code`, `ordre`, `libelle`, `description`, `created_at`, `updated_at`) VALUES
(2, 2, NULL, 'R06', 1, 'Régime des jetons de présence et indemnités de session des organes de gouvernance des EPN et EPH', NULL, '2026-09-26 11:34:58', '2026-09-26 11:34:58'),
(3, 2, NULL, 'R07', 2, 'Révision du cadre législatif et réglementaire applicable aux EPN et EPH', NULL, '2026-09-26 11:34:58', '2026-09-26 11:34:58'),
(4, 2, NULL, 'R08', 3, 'Réforme des critères de désignation des membres des organes de gouvernance des EPN,EPH et EPAST', NULL, '2026-09-26 11:34:58', '2026-09-26 11:34:58'),
(5, 2, NULL, 'R09', 4, 'Rationalisation du Portefeuille des EPN', NULL, '2026-09-26 11:34:58', '2026-09-26 11:34:58'),
(6, 2, NULL, 'R10', 5, 'Réorganisation de la tutelle financière et du contrôle budgétaire', NULL, '2026-09-26 11:34:58', '2026-09-26 11:34:58'),
(7, 3, NULL, NULL, 1, 'A02 — Production du Rapport national de performance et de gestion des EPN (exercice 2025/ exercice 2026)', NULL, '2026-09-26 11:34:58', '2026-09-26 11:34:58'),
(8, 3, NULL, NULL, 2, 'A03 — Restitution des résultats de l\'étude de diagnostic global de la situation des EPN', NULL, '2026-09-26 11:34:58', '2026-09-26 11:34:58'),
(9, 3, NULL, NULL, 3, 'C02 — Mise en place d\'un cadre de suivi des Contrats d\'Objectifs et de Performance (COP)', NULL, '2026-09-26 11:34:58', '2026-09-26 11:34:58'),
(10, 3, NULL, NULL, 4, 'C03 — Travaux préparatoires du programme national d\'audit des EPN', NULL, '2026-09-26 11:34:58', '2026-09-26 11:34:58'),
(11, 4, NULL, NULL, 1, 'A04 — Mise en œuvre du Programme National de Renforcement des Capacités des organes de gouvernance des EPN (PRGG-EPN)', NULL, '2026-09-26 11:34:58', '2026-09-26 11:34:58'),
(12, 4, 4, NULL, 2, 'B01 — Déploiement du Portail numérique e-EPN', NULL, '2026-09-26 11:34:58', '2026-10-02 19:04:13');

-- --------------------------------------------------------

--
-- Structure de la table `actions_mesurables`
--

DROP TABLE IF EXISTS `actions_mesurables`;
CREATE TABLE IF NOT EXISTS `actions_mesurables` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action_cle_id` bigint UNSIGNED NOT NULL,
  `libelle` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `ordre` smallint UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `actions_mesurables_code_unique` (`code`),
  KEY `actions_mesurables_action_cle_id_foreign` (`action_cle_id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `actions_mesurables`
--

INSERT INTO `actions_mesurables` (`id`, `code`, `action_cle_id`, `libelle`, `description`, `ordre`, `created_at`, `updated_at`) VALUES
(1, 'AM26-001', 2, 'Réaliser une étude comparative des barèmes de jetons de présence et indemnités de session appliqués dans des structures publiques comparables (nationales et régionales)', NULL, 1, '2026-09-26 11:34:58', '2026-09-26 11:34:58'),
(2, 'AM26-002', 2, 'Chiffrer l\'impact budgétaire du nouveau régime de jetons de présence pour les finances publiques et pour chaque EPN/EPH', NULL, 2, '2026-09-26 11:34:58', '2026-09-26 11:34:58'),
(3, 'AM26-003', 2, 'Concerter les EPN/EPH et organes de tutelle sur le projet de barème avant finalisation du décret', NULL, 3, '2026-09-26 11:34:58', '2026-09-26 11:34:58'),
(4, 'AM26-004', 3, 'Réaliser l\'analyse comparative des pratiques régionales et internationales (benchmarking) et concerter les administrations concernées', NULL, 1, '2026-09-26 11:34:58', '2026-09-26 11:34:58'),
(5, 'AM26-005', 3, 'Établir le diagnostic du cadre législatif et réglementaire applicable aux EPN ,EPH, EPAST et SA', NULL, 2, '2026-09-26 11:34:58', '2026-09-26 11:34:58'),
(6, 'AM26-006', 3, 'Élaborer les projets de loi et de décrets d\'application issus du diagnostic du cadre législatif et réglementaire', NULL, 3, '2026-09-26 11:34:58', '2026-09-26 11:34:58'),
(7, 'AM26-007', 3, 'Transmettre les projets de loi et de décrets d\'application au Cabinet du MPEEP', NULL, 4, '2026-09-26 11:34:58', '2026-09-26 11:34:58'),
(8, 'AM26-008', 4, 'Définir le référentiel de compétences requis pour les membres des organes de gouvernance des EPN,EPH et EPAST', NULL, 1, '2026-09-26 11:34:58', '2026-09-26 11:34:58'),
(9, 'AM26-009', 4, 'Définir les paramètres organiques de la réforme des critères de désignation (durée des mandats, limite d\'âge, encadrement du nombre de mandats successifs, modalités de renouvellement)', NULL, 2, '2026-09-26 11:34:58', '2026-09-26 11:34:58'),
(10, 'AM26-010', 4, 'Élaborer l\'avant-projet de décret portant réforme des critères de désignation des membres des organes de gouvernance', NULL, 3, '2026-09-26 11:34:58', '2026-09-26 11:34:58'),
(11, 'AM26-011', 4, 'Transmettre le projet de décret portant réforme des critères de désignation au Cabinet du MPEEP', NULL, 4, '2026-09-26 11:34:58', '2026-09-26 11:34:58'),
(12, 'AM26-012', 5, 'Lancement de l\'étude de rationalisation du portefeuille', NULL, 1, '2026-09-26 11:34:58', '2026-09-26 11:34:58'),
(13, 'AM26-013', 5, 'Analyse des missions similaires ou connexes', NULL, 2, '2026-09-26 11:34:58', '2026-09-26 11:34:58'),
(14, 'AM26-014', 5, 'Élaboration de scénarios de fusion / regroupement / mutualisation', NULL, 3, '2026-09-26 11:34:58', '2026-09-26 11:34:58'),
(15, 'AM26-015', 6, 'Alignement de la tutelle financière des EPN sur le régime des sociétés d\'État', NULL, 1, '2026-09-26 11:34:58', '2026-09-26 11:34:58'),
(16, 'AM26-016', 6, 'Rattachement du contrôle budgétaire à la DGEPN', NULL, 2, '2026-09-26 11:34:58', '2026-09-26 11:34:58'),
(17, 'AM26-017', 6, 'Mise en place d\'un contrôle budgétaire orienté performance', NULL, 3, '2026-09-26 11:34:58', '2026-09-26 11:34:58'),
(18, 'AM26-018', 6, 'Définition du cadre réglementaire du contrôle a posteriori des EPIC', NULL, 4, '2026-09-26 11:34:58', '2026-09-26 11:34:58'),
(19, 'AM26-019', 6, 'Identification des EPIC éligibles au contrôle a posteriori', NULL, 5, '2026-09-26 11:34:58', '2026-09-26 11:34:58'),
(20, 'AM26-020', 6, 'Mise en œuvre progressive du contrôle a posteriori pour les EPIC', NULL, 6, '2026-09-26 11:34:58', '2026-09-26 11:34:58'),
(21, 'AM26-021', 6, 'Évaluation et ajustement du dispositif', NULL, 7, '2026-09-26 11:34:58', '2026-09-26 11:34:58'),
(22, 'AM26-022', 7, 'Collecter et consolider les données de performance et de gestion 2025 des EPN auprès des tutelles techniques', NULL, 1, '2026-09-26 11:34:58', '2026-09-26 11:34:58'),
(23, 'AM26-023', 7, 'Exploiter les données consolidées selon une grille d\'analyse harmonisée', NULL, 2, '2026-09-26 11:34:58', '2026-09-26 11:34:58'),
(24, 'AM26-024', 7, 'Mettre en forme et diffuser le Rapport national de performance et de gestion des EPN (exercice 2025)', NULL, 3, '2026-09-26 11:34:58', '2026-09-26 11:34:58'),
(25, 'AM26-025', 8, 'Poursuivre les travaux de restitution de l\'étude de diagnostic global des EPN, sur la base des 70 % déjà exécutés à fin décembre 2025', NULL, 1, '2026-09-26 11:34:58', '2026-09-26 11:34:58'),
(26, 'AM26-026', 8, 'Poursuivre la restitution des résultats de l\'étude de diagnostic global des EPN et du cadre stratégique de développement associé', NULL, 2, '2026-09-26 11:34:58', '2026-09-26 11:34:58'),
(27, 'AM26-027', 8, 'Finaliser et restituer les résultats de l\'étude « Diagnostic global de la situation des EPN et proposition d\'un cadre stratégique de développement »', NULL, 3, '2026-09-26 11:34:58', '2026-09-26 11:34:58'),
(28, 'AM26-028', 9, 'Définir le cadre méthodologique de la contractualisation par Contrats d\'Objectifs et de Performance (COP) entre l\'État et les EPN', NULL, 1, '2026-09-26 11:34:58', '2026-09-26 11:34:58'),
(29, 'AM26-029', 9, 'Définir le cadre organisationnel nécessaire à la mise en œuvre de la contractualisation par COP', NULL, 2, '2026-09-26 11:34:58', '2026-09-26 11:34:58'),
(30, 'AM26-030', 10, 'Définir le référentiel méthodologique du dispositif d\'audit des EPN', NULL, 1, '2026-09-26 11:34:58', '2026-09-26 11:34:58'),
(31, 'AM26-031', 10, 'Engager les travaux préparatoires du programme national d\'audit des EPN (cadrage, priorisation des EPN à auditer)', NULL, 2, '2026-09-26 11:34:58', '2026-09-26 11:34:58'),
(32, 'AM26-032', 11, 'Déployer la seconde phase des sessions de formation des directeurs exécutifs et présidents d\'organes de gouvernance (PRGG-EPN)', NULL, 1, '2026-09-26 11:34:58', '2026-09-26 11:34:58'),
(33, 'AM26-033', 11, 'Poursuivre le déploiement des sessions de formation des directeurs exécutifs et présidents d\'organes de gouvernance', NULL, 2, '2026-09-26 11:34:58', '2026-09-26 11:34:58'),
(34, 'AM26-034', 11, 'Achever le programme de formation des 200 directeurs exécutifs et 200 présidents d\'organes de gouvernance', NULL, 3, '2026-09-26 11:34:58', '2026-09-26 11:34:58'),
(35, 'AM26-035', 12, 'Élaborer les Termes de Référence (TDR) pour le recrutement d\'un consultant ou d\'une structure chargée de la rédaction du manuel de procédures du portail e-EPN', NULL, 1, '2026-09-26 11:34:59', '2026-09-26 11:34:59'),
(36, 'AM26-036', 12, 'Élaborer le manuel de procédures pour le déploiement, l\'exploitation et la sécurité des données du portail e-EPN (rôles, processus, contrôles)', NULL, 2, '2026-09-26 11:34:59', '2026-09-26 11:34:59'),
(37, 'AM26-037', 12, 'Valider le manuel de procédures en comité de pilotage et le diffuser en amont du lancement du portail', NULL, 3, '2026-09-26 11:34:59', '2026-09-26 11:34:59'),
(38, 'AM26-038', 12, 'Procéder au lancement officiel du portail numérique e-EPN', NULL, 4, '2026-09-26 11:34:59', '2026-09-26 11:34:59'),
(39, 'AM26-039', 12, 'Former les utilisateurs des EPN pilotes à l\'application du manuel de procédures, en lien avec l\'exploitation progressive du portail', NULL, 5, '2026-09-26 11:34:59', '2026-09-26 11:34:59'),
(40, 'AM26-040', 12, 'Mettre en œuvre la phase pilote d\'exploitation auprès des EPN pilotes', NULL, 6, '2026-09-26 11:34:59', '2026-09-26 11:34:59');

-- --------------------------------------------------------

--
-- Structure de la table `action_cle_direction`
--

DROP TABLE IF EXISTS `action_cle_direction`;
CREATE TABLE IF NOT EXISTS `action_cle_direction` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `action_cle_id` bigint UNSIGNED NOT NULL,
  `direction_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `action_cle_direction_action_cle_id_direction_id_unique` (`action_cle_id`,`direction_id`),
  KEY `action_cle_direction_direction_id_foreign` (`direction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `affectations`
--

DROP TABLE IF EXISTS `affectations`;
CREATE TABLE IF NOT EXISTS `affectations` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `personnel_id` bigint UNSIGNED NOT NULL,
  `role_affectation_id` bigint UNSIGNED NOT NULL,
  `affectable_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `affectable_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `affectation_unique` (`personnel_id`,`role_affectation_id`,`affectable_type`,`affectable_id`),
  KEY `affectations_role_affectation_id_foreign` (`role_affectation_id`),
  KEY `affectations_affectable_type_affectable_id_index` (`affectable_type`,`affectable_id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `affectations`
--

INSERT INTO `affectations` (`id`, `personnel_id`, `role_affectation_id`, `affectable_type`, `affectable_id`, `created_at`, `updated_at`) VALUES
(8, 3, 2, 'tache', 7, '2026-09-30 09:14:01', '2026-09-30 09:14:01'),
(9, 1, 1, 'tache', 7, '2026-09-30 09:14:01', '2026-09-30 09:14:01'),
(10, 3, 1, 'tache', 7, '2026-09-30 09:14:01', '2026-09-30 09:14:01'),
(15, 2, 2, 'tache', 9, '2026-10-02 08:18:21', '2026-10-02 08:18:21'),
(16, 1, 1, 'tache', 9, '2026-10-02 08:18:21', '2026-10-02 08:18:21'),
(17, 1, 2, 'tache', 5, '2026-10-02 15:23:02', '2026-10-02 15:23:02'),
(18, 3, 1, 'tache', 5, '2026-10-02 15:23:02', '2026-10-02 15:23:02'),
(19, 8, 1, 'action_cle', 12, '2026-10-02 19:04:13', '2026-10-02 19:04:13'),
(22, 8, 2, 'tache', 10, '2026-10-03 17:58:27', '2026-10-03 17:58:27'),
(23, 9, 1, 'tache', 10, '2026-10-03 17:58:27', '2026-10-03 17:58:27'),
(24, 13, 1, 'tache', 8, '2026-10-11 17:07:13', '2026-10-11 17:07:13'),
(25, 11, 1, 'tache', 4, '2026-10-11 17:08:06', '2026-10-11 17:08:06');

-- --------------------------------------------------------

--
-- Structure de la table `alertes_envoyees`
--

DROP TABLE IF EXISTS `alertes_envoyees`;
CREATE TABLE IF NOT EXISTS `alertes_envoyees` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tache_id` bigint UNSIGNED NOT NULL,
  `regle_alerte_id` bigint UNSIGNED NOT NULL,
  `canal_notification_id` bigint UNSIGNED NOT NULL,
  `personnel_id` bigint UNSIGNED NOT NULL,
  `echeance_au_envoi` date DEFAULT NULL,
  `envoye_le` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `alerte_unique` (`tache_id`,`regle_alerte_id`,`canal_notification_id`,`personnel_id`),
  UNIQUE KEY `alertes_envoyees_anti_doublon` (`tache_id`,`regle_alerte_id`,`canal_notification_id`,`personnel_id`,`echeance_au_envoi`),
  KEY `alertes_envoyees_regle_alerte_id_foreign` (`regle_alerte_id`),
  KEY `alertes_envoyees_canal_notification_id_foreign` (`canal_notification_id`),
  KEY `alertes_envoyees_personnel_id_foreign` (`personnel_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `alertes_envoyees`
--

INSERT INTO `alertes_envoyees` (`id`, `tache_id`, `regle_alerte_id`, `canal_notification_id`, `personnel_id`, `echeance_au_envoi`, `envoye_le`, `created_at`, `updated_at`) VALUES
(1, 10, 4, 1, 9, '2026-10-06', '2026-10-03 19:13:56', '2026-10-03 19:13:56', '2026-10-03 19:13:56'),
(2, 10, 4, 1, 8, '2026-10-06', '2026-10-03 19:13:56', '2026-10-03 19:13:56', '2026-10-03 19:13:56'),
(3, 10, 4, 2, 9, '2026-10-06', '2026-10-03 19:14:12', '2026-10-03 19:14:12', '2026-10-03 19:14:12'),
(4, 10, 4, 2, 8, '2026-10-06', '2026-10-03 19:14:15', '2026-10-03 19:14:15', '2026-10-03 19:14:15'),
(5, 10, 7, 1, 9, '2026-10-06', '2026-10-07 13:38:20', '2026-10-07 13:38:20', '2026-10-07 13:38:20'),
(6, 10, 7, 1, 8, '2026-10-06', '2026-10-07 13:38:20', '2026-10-07 13:38:20', '2026-10-07 13:38:20'),
(7, 10, 7, 2, 9, '2026-10-06', '2026-10-07 13:38:37', '2026-10-07 13:38:37', '2026-10-07 13:38:37'),
(8, 10, 7, 2, 8, '2026-10-06', '2026-10-07 13:38:39', '2026-10-07 13:38:39', '2026-10-07 13:38:39'),
(9, 109, 8, 1, 8, '2026-09-30', '2026-10-07 13:38:39', '2026-10-07 13:38:39', '2026-10-07 13:38:39'),
(10, 109, 8, 2, 8, '2026-09-30', '2026-10-07 13:38:41', '2026-10-07 13:38:41', '2026-10-07 13:38:41');

-- --------------------------------------------------------

--
-- Structure de la table `axes`
--

DROP TABLE IF EXISTS `axes`;
CREATE TABLE IF NOT EXISTS `axes` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `exercice_id` bigint UNSIGNED DEFAULT NULL,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `libelle` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `resultat_attendu` text COLLATE utf8mb4_unicode_ci,
  `description` text COLLATE utf8mb4_unicode_ci,
  `ordre` smallint UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `axes_code_unique` (`code`),
  UNIQUE KEY `axes_exercice_libelle_unique` (`exercice_id`,`libelle`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `axes`
--

INSERT INTO `axes` (`id`, `exercice_id`, `code`, `libelle`, `resultat_attendu`, `description`, `ordre`, `created_at`, `updated_at`) VALUES
(2, 1, 'A001', 'Harmonisation du cadre juridique et de la gouvernance des organes des EPN / EPH / EPAST /SA', 'Cadre juridique et statutaire des organes de gouvernance des EPN/EPH modernisé et sécurisé', NULL, 1, '2026-09-26 11:34:58', '2026-09-26 12:16:05'),
(3, 1, 'A002', 'Pilotage, performance et contrôle des EPN', 'Dispositif de pilotage,de mesure de la performance et de contrôle des EPN structuré et opérationnel', NULL, 2, '2026-09-26 11:34:58', '2026-09-26 12:16:05'),
(4, 1, 'A003', 'Renforcement des capacités et digitalisation des outils de gestion', 'Capacités des dirigeants et outils numériques de gestion des EPN renforcés', NULL, 3, '2026-09-26 11:34:58', '2026-09-26 12:16:05');

-- --------------------------------------------------------

--
-- Structure de la table `cache`
--

DROP TABLE IF EXISTS `cache`;
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('e-tache-cache-05f93c5dcd622e26994f48355a6f2396', 'i:1;', 1794177164),
('e-tache-cache-05f93c5dcd622e26994f48355a6f2396:timer', 'i:1794177164;', 1794177164),
('e-tache-cache-356a192b7913b04c54574d18c28d46e6395428ab', 'i:1;', 1794092193),
('e-tache-cache-356a192b7913b04c54574d18c28d46e6395428ab:timer', 'i:1794092193;', 1794092193),
('e-tache-cache-admin@dgepn.ciadmin2026|127.0.0.1', 'i:1;', 1793802308),
('e-tache-cache-admin@dgepn.ciadmin2026|127.0.0.1:timer', 'i:1793802308;', 1793802308),
('e-tache-cache-akonperso@gmail.com|127.0.0.1', 'i:1;', 1792673411),
('e-tache-cache-akonperso@gmail.com|127.0.0.1:timer', 'i:1792673410;', 1792673410),
('e-tache-cache-courrier.alertes_diligences', 'i:1;', 1794100582),
('e-tache-cache-courrier.suivi_responsables', 'i:1;', 1794092316),
('e-tache-cache-pilotage.instantane.2026-10-19', 'i:1;', 1792446685),
('e-tache-cache-pilotage.instantane.2026-10-22', 'i:1;', 1792702496),
('e-tache-cache-pilotage.instantane.2026-11-07', 'i:1;', 1794099573),
('e-tache-cache-pilotage.instantane.2026-11-08', 'i:1;', 1794205912),
('e-tache-cache-reglages.valeurs', 'a:2:{s:18:\"synthese.ia_active\";s:4:\"true\";s:27:\"securite.inactivite_minutes\";s:2:\"15\";}', 2109441448),
('e-tache-cache-rh.alertes', 'i:1;', 1794158175);
INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('e-tache-cache-roster:project:v3:3ab11c8e2c715697a3a45751f0d6134e', 'O:26:\"Laravel\\Roster\\ProjectScan\":8:{s:8:\"basePath\";s:21:\"C:\\wamp64\\www\\etache\\\";s:3:\"php\";O:35:\"Laravel\\Roster\\Ecosystems\\Ecosystem\":2:{s:9:\"\0*\0byName\";a:127:{s:23:\"barryvdh/laravel-dompdf\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"barryvdh/laravel-dompdf\";s:10:\"\0*\0version\";s:5:\"3.1.2\";s:9:\"\0*\0source\";E:43:\"Laravel\\Roster\\Enums\\PackageSource:Composer\";s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:4:\"^3.1\";s:7:\"\0*\0path\";s:51:\"C:\\wamp64\\www\\etache\\vendor\\barryvdh\\laravel-dompdf\";}s:10:\"brick/math\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"brick/math\";s:10:\"\0*\0version\";s:5:\"1.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:38:\"C:\\wamp64\\www\\etache\\vendor\\brick\\math\";}s:31:\"carbonphp/carbon-doctrine-types\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:31:\"carbonphp/carbon-doctrine-types\";s:10:\"\0*\0version\";s:5:\"3.2.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:59:\"C:\\wamp64\\www\\etache\\vendor\\carbonphp\\carbon-doctrine-types\";}s:13:\"composer/pcre\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"composer/pcre\";s:10:\"\0*\0version\";s:5:\"3.4.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:41:\"C:\\wamp64\\www\\etache\\vendor\\composer\\pcre\";}s:23:\"dflydev/dot-access-data\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"dflydev/dot-access-data\";s:10:\"\0*\0version\";s:5:\"3.0.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"C:\\wamp64\\www\\etache\\vendor\\dflydev\\dot-access-data\";}s:18:\"doctrine/inflector\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"doctrine/inflector\";s:10:\"\0*\0version\";s:5:\"2.1.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"C:\\wamp64\\www\\etache\\vendor\\doctrine\\inflector\";}s:14:\"doctrine/lexer\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"doctrine/lexer\";s:10:\"\0*\0version\";s:5:\"3.0.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:42:\"C:\\wamp64\\www\\etache\\vendor\\doctrine\\lexer\";}s:13:\"dompdf/dompdf\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"dompdf/dompdf\";s:10:\"\0*\0version\";s:5:\"3.1.6\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:41:\"C:\\wamp64\\www\\etache\\vendor\\dompdf\\dompdf\";}s:19:\"dompdf/php-font-lib\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"dompdf/php-font-lib\";s:10:\"\0*\0version\";s:5:\"1.0.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"C:\\wamp64\\www\\etache\\vendor\\dompdf\\php-font-lib\";}s:18:\"dompdf/php-svg-lib\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"dompdf/php-svg-lib\";s:10:\"\0*\0version\";s:5:\"1.0.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"C:\\wamp64\\www\\etache\\vendor\\dompdf\\php-svg-lib\";}s:29:\"dragonmantank/cron-expression\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:29:\"dragonmantank/cron-expression\";s:10:\"\0*\0version\";s:5:\"3.6.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:57:\"C:\\wamp64\\www\\etache\\vendor\\dragonmantank\\cron-expression\";}s:23:\"egulias/email-validator\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"egulias/email-validator\";s:10:\"\0*\0version\";s:5:\"4.0.4\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"C:\\wamp64\\www\\etache\\vendor\\egulias\\email-validator\";}s:18:\"fruitcake/php-cors\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"fruitcake/php-cors\";s:10:\"\0*\0version\";s:5:\"1.4.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"C:\\wamp64\\www\\etache\\vendor\\fruitcake\\php-cors\";}s:27:\"graham-campbell/result-type\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:27:\"graham-campbell/result-type\";s:10:\"\0*\0version\";s:5:\"1.2.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:55:\"C:\\wamp64\\www\\etache\\vendor\\graham-campbell\\result-type\";}s:17:\"guzzlehttp/guzzle\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"guzzlehttp/guzzle\";s:10:\"\0*\0version\";s:5:\"8.2.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\vendor\\guzzlehttp\\guzzle\";}s:19:\"guzzlehttp/promises\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"guzzlehttp/promises\";s:10:\"\0*\0version\";s:5:\"3.0.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"C:\\wamp64\\www\\etache\\vendor\\guzzlehttp\\promises\";}s:15:\"guzzlehttp/psr7\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"guzzlehttp/psr7\";s:10:\"\0*\0version\";s:5:\"3.1.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"C:\\wamp64\\www\\etache\\vendor\\guzzlehttp\\psr7\";}s:23:\"guzzlehttp/uri-template\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"guzzlehttp/uri-template\";s:10:\"\0*\0version\";s:5:\"2.0.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"C:\\wamp64\\www\\etache\\vendor\\guzzlehttp\\uri-template\";}s:17:\"laravel/framework\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"laravel/framework\";s:10:\"\0*\0version\";s:7:\"13.33.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^13.17\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\vendor\\laravel\\framework\";}s:15:\"laravel/prompts\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"laravel/prompts\";s:10:\"\0*\0version\";s:6:\"0.3.24\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"C:\\wamp64\\www\\etache\\vendor\\laravel\\prompts\";}s:28:\"laravel/serializable-closure\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:28:\"laravel/serializable-closure\";s:10:\"\0*\0version\";s:5:\"2.1.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:56:\"C:\\wamp64\\www\\etache\\vendor\\laravel\\serializable-closure\";}s:14:\"laravel/tinker\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"laravel/tinker\";s:10:\"\0*\0version\";s:5:\"3.0.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:4:\"^3.0\";s:7:\"\0*\0path\";s:42:\"C:\\wamp64\\www\\etache\\vendor\\laravel\\tinker\";}s:17:\"league/commonmark\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"league/commonmark\";s:10:\"\0*\0version\";s:6:\"2.10.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\vendor\\league\\commonmark\";}s:13:\"league/config\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"league/config\";s:10:\"\0*\0version\";s:5:\"1.2.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:41:\"C:\\wamp64\\www\\etache\\vendor\\league\\config\";}s:16:\"league/flysystem\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"league/flysystem\";s:10:\"\0*\0version\";s:6:\"3.36.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\wamp64\\www\\etache\\vendor\\league\\flysystem\";}s:22:\"league/flysystem-local\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"league/flysystem-local\";s:10:\"\0*\0version\";s:6:\"3.35.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"C:\\wamp64\\www\\etache\\vendor\\league\\flysystem-local\";}s:26:\"league/mime-type-detection\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:26:\"league/mime-type-detection\";s:10:\"\0*\0version\";s:6:\"1.17.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:54:\"C:\\wamp64\\www\\etache\\vendor\\league\\mime-type-detection\";}s:10:\"league/uri\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"league/uri\";s:10:\"\0*\0version\";s:5:\"7.8.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:38:\"C:\\wamp64\\www\\etache\\vendor\\league\\uri\";}s:21:\"league/uri-interfaces\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"league/uri-interfaces\";s:10:\"\0*\0version\";s:5:\"7.8.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:49:\"C:\\wamp64\\www\\etache\\vendor\\league\\uri-interfaces\";}s:23:\"maennchen/zipstream-php\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"maennchen/zipstream-php\";s:10:\"\0*\0version\";s:5:\"3.2.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"C:\\wamp64\\www\\etache\\vendor\\maennchen\\zipstream-php\";}s:17:\"markbaker/complex\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"markbaker/complex\";s:10:\"\0*\0version\";s:5:\"3.0.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\vendor\\markbaker\\complex\";}s:16:\"markbaker/matrix\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"markbaker/matrix\";s:10:\"\0*\0version\";s:5:\"3.0.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\wamp64\\www\\etache\\vendor\\markbaker\\matrix\";}s:17:\"masterminds/html5\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"masterminds/html5\";s:10:\"\0*\0version\";s:6:\"2.11.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\vendor\\masterminds\\html5\";}s:15:\"monolog/monolog\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"monolog/monolog\";s:10:\"\0*\0version\";s:6:\"3.12.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"C:\\wamp64\\www\\etache\\vendor\\monolog\\monolog\";}s:13:\"nesbot/carbon\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"nesbot/carbon\";s:10:\"\0*\0version\";s:6:\"3.14.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:41:\"C:\\wamp64\\www\\etache\\vendor\\nesbot\\carbon\";}s:12:\"nette/schema\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"nette/schema\";s:10:\"\0*\0version\";s:5:\"1.3.6\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:40:\"C:\\wamp64\\www\\etache\\vendor\\nette\\schema\";}s:11:\"nette/utils\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"nette/utils\";s:10:\"\0*\0version\";s:5:\"4.1.5\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:39:\"C:\\wamp64\\www\\etache\\vendor\\nette\\utils\";}s:16:\"nikic/php-parser\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"nikic/php-parser\";s:10:\"\0*\0version\";s:5:\"5.9.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\wamp64\\www\\etache\\vendor\\nikic\\php-parser\";}s:19:\"nunomaduro/termwind\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"nunomaduro/termwind\";s:10:\"\0*\0version\";s:5:\"2.4.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"C:\\wamp64\\www\\etache\\vendor\\nunomaduro\\termwind\";}s:24:\"phpoffice/phpspreadsheet\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:24:\"phpoffice/phpspreadsheet\";s:10:\"\0*\0version\";s:6:\"5.10.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:5:\"^5.10\";s:7:\"\0*\0path\";s:52:\"C:\\wamp64\\www\\etache\\vendor\\phpoffice\\phpspreadsheet\";}s:19:\"phpoption/phpoption\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"phpoption/phpoption\";s:10:\"\0*\0version\";s:6:\"1.10.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"C:\\wamp64\\www\\etache\\vendor\\phpoption\\phpoption\";}s:9:\"psr/clock\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"psr/clock\";s:10:\"\0*\0version\";s:5:\"1.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:37:\"C:\\wamp64\\www\\etache\\vendor\\psr\\clock\";}s:13:\"psr/container\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"psr/container\";s:10:\"\0*\0version\";s:5:\"2.0.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:41:\"C:\\wamp64\\www\\etache\\vendor\\psr\\container\";}s:20:\"psr/event-dispatcher\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"psr/event-dispatcher\";s:10:\"\0*\0version\";s:5:\"1.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"C:\\wamp64\\www\\etache\\vendor\\psr\\event-dispatcher\";}s:15:\"psr/http-client\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"psr/http-client\";s:10:\"\0*\0version\";s:5:\"1.0.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"C:\\wamp64\\www\\etache\\vendor\\psr\\http-client\";}s:16:\"psr/http-factory\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"psr/http-factory\";s:10:\"\0*\0version\";s:5:\"1.1.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\wamp64\\www\\etache\\vendor\\psr\\http-factory\";}s:16:\"psr/http-message\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"psr/http-message\";s:10:\"\0*\0version\";s:3:\"2.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\wamp64\\www\\etache\\vendor\\psr\\http-message\";}s:7:\"psr/log\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:7:\"psr/log\";s:10:\"\0*\0version\";s:5:\"3.0.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:35:\"C:\\wamp64\\www\\etache\\vendor\\psr\\log\";}s:16:\"psr/simple-cache\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"psr/simple-cache\";s:10:\"\0*\0version\";s:5:\"3.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\wamp64\\www\\etache\\vendor\\psr\\simple-cache\";}s:9:\"psy/psysh\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"psy/psysh\";s:10:\"\0*\0version\";s:7:\"0.12.24\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:37:\"C:\\wamp64\\www\\etache\\vendor\\psy\\psysh\";}s:17:\"ramsey/collection\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"ramsey/collection\";s:10:\"\0*\0version\";s:5:\"2.1.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\vendor\\ramsey\\collection\";}s:11:\"ramsey/uuid\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"ramsey/uuid\";s:10:\"\0*\0version\";s:5:\"4.9.4\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:39:\"C:\\wamp64\\www\\etache\\vendor\\ramsey\\uuid\";}s:25:\"sabberworm/php-css-parser\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"sabberworm/php-css-parser\";s:10:\"\0*\0version\";s:5:\"9.5.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"C:\\wamp64\\www\\etache\\vendor\\sabberworm\\php-css-parser\";}s:13:\"symfony/clock\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"symfony/clock\";s:10:\"\0*\0version\";s:6:\"7.4.21\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:41:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\clock\";}s:15:\"symfony/console\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"symfony/console\";s:10:\"\0*\0version\";s:6:\"7.4.23\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\console\";}s:20:\"symfony/css-selector\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"symfony/css-selector\";s:10:\"\0*\0version\";s:6:\"7.4.22\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\css-selector\";}s:29:\"symfony/deprecation-contracts\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:29:\"symfony/deprecation-contracts\";s:10:\"\0*\0version\";s:5:\"3.8.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:57:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\deprecation-contracts\";}s:21:\"symfony/error-handler\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"symfony/error-handler\";s:10:\"\0*\0version\";s:6:\"7.4.22\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:49:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\error-handler\";}s:24:\"symfony/event-dispatcher\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:24:\"symfony/event-dispatcher\";s:10:\"\0*\0version\";s:6:\"7.4.17\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\event-dispatcher\";}s:34:\"symfony/event-dispatcher-contracts\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:34:\"symfony/event-dispatcher-contracts\";s:10:\"\0*\0version\";s:5:\"3.8.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:62:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\event-dispatcher-contracts\";}s:14:\"symfony/finder\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"symfony/finder\";s:10:\"\0*\0version\";s:6:\"7.4.23\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:42:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\finder\";}s:23:\"symfony/http-foundation\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"symfony/http-foundation\";s:10:\"\0*\0version\";s:6:\"7.4.23\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\http-foundation\";}s:19:\"symfony/http-kernel\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"symfony/http-kernel\";s:10:\"\0*\0version\";s:6:\"7.4.23\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\http-kernel\";}s:14:\"symfony/mailer\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"symfony/mailer\";s:10:\"\0*\0version\";s:6:\"7.4.22\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:42:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\mailer\";}s:12:\"symfony/mime\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"symfony/mime\";s:10:\"\0*\0version\";s:6:\"7.4.22\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:40:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\mime\";}s:22:\"symfony/polyfill-ctype\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"symfony/polyfill-ctype\";s:10:\"\0*\0version\";s:6:\"1.37.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\polyfill-ctype\";}s:30:\"symfony/polyfill-intl-grapheme\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:30:\"symfony/polyfill-intl-grapheme\";s:10:\"\0*\0version\";s:6:\"1.43.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:58:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\polyfill-intl-grapheme\";}s:25:\"symfony/polyfill-intl-idn\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"symfony/polyfill-intl-idn\";s:10:\"\0*\0version\";s:6:\"1.43.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\polyfill-intl-idn\";}s:32:\"symfony/polyfill-intl-normalizer\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:32:\"symfony/polyfill-intl-normalizer\";s:10:\"\0*\0version\";s:6:\"1.43.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:60:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\polyfill-intl-normalizer\";}s:25:\"symfony/polyfill-mbstring\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"symfony/polyfill-mbstring\";s:10:\"\0*\0version\";s:6:\"1.43.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\polyfill-mbstring\";}s:22:\"symfony/polyfill-php80\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"symfony/polyfill-php80\";s:10:\"\0*\0version\";s:6:\"1.43.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\polyfill-php80\";}s:22:\"symfony/polyfill-php82\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"symfony/polyfill-php82\";s:10:\"\0*\0version\";s:6:\"1.43.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\polyfill-php82\";}s:22:\"symfony/polyfill-php83\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"symfony/polyfill-php83\";s:10:\"\0*\0version\";s:6:\"1.43.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\polyfill-php83\";}s:22:\"symfony/polyfill-php84\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"symfony/polyfill-php84\";s:10:\"\0*\0version\";s:6:\"1.43.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\polyfill-php84\";}s:22:\"symfony/polyfill-php85\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"symfony/polyfill-php85\";s:10:\"\0*\0version\";s:6:\"1.43.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\polyfill-php85\";}s:22:\"symfony/polyfill-php86\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"symfony/polyfill-php86\";s:10:\"\0*\0version\";s:6:\"1.43.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\polyfill-php86\";}s:21:\"symfony/polyfill-uuid\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"symfony/polyfill-uuid\";s:10:\"\0*\0version\";s:6:\"1.43.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:49:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\polyfill-uuid\";}s:15:\"symfony/process\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"symfony/process\";s:10:\"\0*\0version\";s:6:\"7.4.22\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\process\";}s:15:\"symfony/routing\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"symfony/routing\";s:10:\"\0*\0version\";s:6:\"7.4.23\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\routing\";}s:25:\"symfony/service-contracts\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"symfony/service-contracts\";s:10:\"\0*\0version\";s:5:\"3.8.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\service-contracts\";}s:14:\"symfony/string\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"symfony/string\";s:10:\"\0*\0version\";s:6:\"7.4.22\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:42:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\string\";}s:19:\"symfony/translation\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"symfony/translation\";s:10:\"\0*\0version\";s:6:\"7.4.23\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\translation\";}s:29:\"symfony/translation-contracts\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:29:\"symfony/translation-contracts\";s:10:\"\0*\0version\";s:5:\"3.8.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:57:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\translation-contracts\";}s:11:\"symfony/uid\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"symfony/uid\";s:10:\"\0*\0version\";s:6:\"7.4.21\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:39:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\uid\";}s:18:\"symfony/var-dumper\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"symfony/var-dumper\";s:10:\"\0*\0version\";s:6:\"7.4.22\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\var-dumper\";}s:33:\"tijsverkoyen/css-to-inline-styles\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:33:\"tijsverkoyen/css-to-inline-styles\";s:10:\"\0*\0version\";s:5:\"2.4.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:61:\"C:\\wamp64\\www\\etache\\vendor\\tijsverkoyen\\css-to-inline-styles\";}s:16:\"vlucas/phpdotenv\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"vlucas/phpdotenv\";s:10:\"\0*\0version\";s:5:\"5.7.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\wamp64\\www\\etache\\vendor\\vlucas\\phpdotenv\";}s:19:\"voku/portable-ascii\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"voku/portable-ascii\";s:10:\"\0*\0version\";s:5:\"2.1.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"C:\\wamp64\\www\\etache\\vendor\\voku\\portable-ascii\";}s:15:\"composer/semver\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"composer/semver\";s:10:\"\0*\0version\";s:5:\"3.5.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"C:\\wamp64\\www\\etache\\vendor\\composer\\semver\";}s:14:\"fakerphp/faker\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"fakerphp/faker\";s:10:\"\0*\0version\";s:6:\"1.24.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:5:\"^1.23\";s:7:\"\0*\0path\";s:42:\"C:\\wamp64\\www\\etache\\vendor\\fakerphp\\faker\";}s:11:\"filp/whoops\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"filp/whoops\";s:10:\"\0*\0version\";s:6:\"2.18.5\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:39:\"C:\\wamp64\\www\\etache\\vendor\\filp\\whoops\";}s:21:\"hamcrest/hamcrest-php\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"hamcrest/hamcrest-php\";s:10:\"\0*\0version\";s:5:\"3.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:49:\"C:\\wamp64\\www\\etache\\vendor\\hamcrest\\hamcrest-php\";}s:22:\"laravel/agent-detector\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"laravel/agent-detector\";s:10:\"\0*\0version\";s:5:\"2.0.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"C:\\wamp64\\www\\etache\\vendor\\laravel\\agent-detector\";}s:13:\"laravel/boost\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"laravel/boost\";s:10:\"\0*\0version\";s:6:\"2.10.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:5:\"^2.10\";s:7:\"\0*\0path\";s:41:\"C:\\wamp64\\www\\etache\\vendor\\laravel\\boost\";}s:14:\"laravel/breeze\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"laravel/breeze\";s:10:\"\0*\0version\";s:5:\"2.4.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:1:\"*\";s:7:\"\0*\0path\";s:42:\"C:\\wamp64\\www\\etache\\vendor\\laravel\\breeze\";}s:11:\"laravel/mcp\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"laravel/mcp\";s:10:\"\0*\0version\";s:5:\"1.0.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:39:\"C:\\wamp64\\www\\etache\\vendor\\laravel\\mcp\";}s:12:\"laravel/pail\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"laravel/pail\";s:10:\"\0*\0version\";s:5:\"1.2.7\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^1.2.5\";s:7:\"\0*\0path\";s:40:\"C:\\wamp64\\www\\etache\\vendor\\laravel\\pail\";}s:11:\"laravel/pao\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"laravel/pao\";s:10:\"\0*\0version\";s:5:\"1.1.5\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^1.0.6\";s:7:\"\0*\0path\";s:39:\"C:\\wamp64\\www\\etache\\vendor\\laravel\\pao\";}s:12:\"laravel/pint\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"laravel/pint\";s:10:\"\0*\0version\";s:6:\"1.32.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:5:\"^1.27\";s:7:\"\0*\0path\";s:40:\"C:\\wamp64\\www\\etache\\vendor\\laravel\\pint\";}s:14:\"laravel/roster\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"laravel/roster\";s:10:\"\0*\0version\";s:5:\"1.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:42:\"C:\\wamp64\\www\\etache\\vendor\\laravel\\roster\";}s:15:\"mockery/mockery\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"mockery/mockery\";s:10:\"\0*\0version\";s:6:\"1.6.15\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:4:\"^1.6\";s:7:\"\0*\0path\";s:43:\"C:\\wamp64\\www\\etache\\vendor\\mockery\\mockery\";}s:17:\"myclabs/deep-copy\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"myclabs/deep-copy\";s:10:\"\0*\0version\";s:6:\"1.14.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\vendor\\myclabs\\deep-copy\";}s:20:\"nunomaduro/collision\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"nunomaduro/collision\";s:10:\"\0*\0version\";s:5:\"8.9.5\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:4:\"^8.6\";s:7:\"\0*\0path\";s:48:\"C:\\wamp64\\www\\etache\\vendor\\nunomaduro\\collision\";}s:16:\"phar-io/manifest\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"phar-io/manifest\";s:10:\"\0*\0version\";s:5:\"2.0.4\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\wamp64\\www\\etache\\vendor\\phar-io\\manifest\";}s:15:\"phar-io/version\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"phar-io/version\";s:10:\"\0*\0version\";s:5:\"3.2.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"C:\\wamp64\\www\\etache\\vendor\\phar-io\\version\";}s:25:\"phpunit/php-code-coverage\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"phpunit/php-code-coverage\";s:10:\"\0*\0version\";s:6:\"12.5.7\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"C:\\wamp64\\www\\etache\\vendor\\phpunit\\php-code-coverage\";}s:25:\"phpunit/php-file-iterator\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"phpunit/php-file-iterator\";s:10:\"\0*\0version\";s:5:\"6.0.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"C:\\wamp64\\www\\etache\\vendor\\phpunit\\php-file-iterator\";}s:19:\"phpunit/php-invoker\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"phpunit/php-invoker\";s:10:\"\0*\0version\";s:5:\"6.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"C:\\wamp64\\www\\etache\\vendor\\phpunit\\php-invoker\";}s:25:\"phpunit/php-text-template\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"phpunit/php-text-template\";s:10:\"\0*\0version\";s:5:\"5.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"C:\\wamp64\\www\\etache\\vendor\\phpunit\\php-text-template\";}s:17:\"phpunit/php-timer\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"phpunit/php-timer\";s:10:\"\0*\0version\";s:5:\"8.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\vendor\\phpunit\\php-timer\";}s:15:\"phpunit/phpunit\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"phpunit/phpunit\";s:10:\"\0*\0version\";s:7:\"12.5.35\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:8:\"^12.5.12\";s:7:\"\0*\0path\";s:43:\"C:\\wamp64\\www\\etache\\vendor\\phpunit\\phpunit\";}s:20:\"sebastian/cli-parser\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"sebastian/cli-parser\";s:10:\"\0*\0version\";s:5:\"4.2.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"C:\\wamp64\\www\\etache\\vendor\\sebastian\\cli-parser\";}s:20:\"sebastian/comparator\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"sebastian/comparator\";s:10:\"\0*\0version\";s:5:\"7.1.8\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"C:\\wamp64\\www\\etache\\vendor\\sebastian\\comparator\";}s:20:\"sebastian/complexity\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"sebastian/complexity\";s:10:\"\0*\0version\";s:5:\"5.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"C:\\wamp64\\www\\etache\\vendor\\sebastian\\complexity\";}s:14:\"sebastian/diff\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"sebastian/diff\";s:10:\"\0*\0version\";s:5:\"7.0.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:42:\"C:\\wamp64\\www\\etache\\vendor\\sebastian\\diff\";}s:21:\"sebastian/environment\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"sebastian/environment\";s:10:\"\0*\0version\";s:5:\"8.1.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:49:\"C:\\wamp64\\www\\etache\\vendor\\sebastian\\environment\";}s:18:\"sebastian/exporter\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"sebastian/exporter\";s:10:\"\0*\0version\";s:5:\"7.0.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"C:\\wamp64\\www\\etache\\vendor\\sebastian\\exporter\";}s:22:\"sebastian/global-state\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"sebastian/global-state\";s:10:\"\0*\0version\";s:5:\"8.0.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"C:\\wamp64\\www\\etache\\vendor\\sebastian\\global-state\";}s:23:\"sebastian/lines-of-code\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"sebastian/lines-of-code\";s:10:\"\0*\0version\";s:5:\"4.0.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"C:\\wamp64\\www\\etache\\vendor\\sebastian\\lines-of-code\";}s:27:\"sebastian/object-enumerator\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:27:\"sebastian/object-enumerator\";s:10:\"\0*\0version\";s:5:\"7.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:55:\"C:\\wamp64\\www\\etache\\vendor\\sebastian\\object-enumerator\";}s:26:\"sebastian/object-reflector\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:26:\"sebastian/object-reflector\";s:10:\"\0*\0version\";s:5:\"5.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:54:\"C:\\wamp64\\www\\etache\\vendor\\sebastian\\object-reflector\";}s:27:\"sebastian/recursion-context\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:27:\"sebastian/recursion-context\";s:10:\"\0*\0version\";s:5:\"7.0.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:55:\"C:\\wamp64\\www\\etache\\vendor\\sebastian\\recursion-context\";}s:14:\"sebastian/type\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"sebastian/type\";s:10:\"\0*\0version\";s:5:\"6.0.4\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:42:\"C:\\wamp64\\www\\etache\\vendor\\sebastian\\type\";}s:17:\"sebastian/version\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"sebastian/version\";s:10:\"\0*\0version\";s:5:\"6.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\vendor\\sebastian\\version\";}s:28:\"staabm/side-effects-detector\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:28:\"staabm/side-effects-detector\";s:10:\"\0*\0version\";s:5:\"1.0.5\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:56:\"C:\\wamp64\\www\\etache\\vendor\\staabm\\side-effects-detector\";}s:12:\"symfony/yaml\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"symfony/yaml\";s:10:\"\0*\0version\";s:6:\"7.4.23\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:40:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\yaml\";}s:17:\"theseer/tokenizer\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"theseer/tokenizer\";s:10:\"\0*\0version\";s:5:\"2.0.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\vendor\\theseer\\tokenizer\";}}s:11:\"\0*\0packages\";O:32:\"Laravel\\Roster\\PackageCollection\":2:{s:8:\"\0*\0items\";a:127:{i:0;r:5;i:1;r:13;i:2;r:21;i:3;r:29;i:4;r:37;i:5;r:45;i:6;r:53;i:7;r:61;i:8;r:69;i:9;r:77;i:10;r:85;i:11;r:93;i:12;r:101;i:13;r:109;i:14;r:117;i:15;r:125;i:16;r:133;i:17;r:141;i:18;r:149;i:19;r:157;i:20;r:165;i:21;r:173;i:22;r:181;i:23;r:189;i:24;r:197;i:25;r:205;i:26;r:213;i:27;r:221;i:28;r:229;i:29;r:237;i:30;r:245;i:31;r:253;i:32;r:261;i:33;r:269;i:34;r:277;i:35;r:285;i:36;r:293;i:37;r:301;i:38;r:309;i:39;r:317;i:40;r:325;i:41;r:333;i:42;r:341;i:43;r:349;i:44;r:357;i:45;r:365;i:46;r:373;i:47;r:381;i:48;r:389;i:49;r:397;i:50;r:405;i:51;r:413;i:52;r:421;i:53;r:429;i:54;r:437;i:55;r:445;i:56;r:453;i:57;r:461;i:58;r:469;i:59;r:477;i:60;r:485;i:61;r:493;i:62;r:501;i:63;r:509;i:64;r:517;i:65;r:525;i:66;r:533;i:67;r:541;i:68;r:549;i:69;r:557;i:70;r:565;i:71;r:573;i:72;r:581;i:73;r:589;i:74;r:597;i:75;r:605;i:76;r:613;i:77;r:621;i:78;r:629;i:79;r:637;i:80;r:645;i:81;r:653;i:82;r:661;i:83;r:669;i:84;r:677;i:85;r:685;i:86;r:693;i:87;r:701;i:88;r:709;i:89;r:717;i:90;r:725;i:91;r:733;i:92;r:741;i:93;r:749;i:94;r:757;i:95;r:765;i:96;r:773;i:97;r:781;i:98;r:789;i:99;r:797;i:100;r:805;i:101;r:813;i:102;r:821;i:103;r:829;i:104;r:837;i:105;r:845;i:106;r:853;i:107;r:861;i:108;r:869;i:109;r:877;i:110;r:885;i:111;r:893;i:112;r:901;i:113;r:909;i:114;r:917;i:115;r:925;i:116;r:933;i:117;r:941;i:118;r:949;i:119;r:957;i:120;r:965;i:121;r:973;i:122;r:981;i:123;r:989;i:124;r:997;i:125;r:1005;i:126;r:1013;}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}s:2:\"js\";O:37:\"Laravel\\Roster\\Ecosystems\\JsEcosystem\":3:{s:9:\"\0*\0byName\";a:189:{s:24:\"@alcalzone/ansi-tokenize\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:24:\"@alcalzone/ansi-tokenize\";s:10:\"\0*\0version\";s:5:\"0.3.1\";s:9:\"\0*\0source\";E:38:\"Laravel\\Roster\\Enums\\PackageSource:Npm\";s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:58:\"C:\\wamp64\\www\\etache\\node_modules\\@alcalzone\\ansi-tokenize\";}s:18:\"@laravel/multiplex\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"@laravel/multiplex\";s:10:\"\0*\0version\";s:5:\"0.4.4\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^0.4.1\";s:7:\"\0*\0path\";s:52:\"C:\\wamp64\\www\\etache\\node_modules\\@laravel\\multiplex\";}s:12:\"ansi-escapes\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"ansi-escapes\";s:10:\"\0*\0version\";s:5:\"7.3.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"C:\\wamp64\\www\\etache\\node_modules\\ansi-escapes\";}s:10:\"ansi-regex\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"ansi-regex\";s:10:\"\0*\0version\";s:5:\"6.3.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\wamp64\\www\\etache\\node_modules\\ansi-regex\";}s:11:\"ansi-styles\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"ansi-styles\";s:10:\"\0*\0version\";s:5:\"7.0.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\node_modules\\ansi-styles\";}s:9:\"auto-bind\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"auto-bind\";s:10:\"\0*\0version\";s:5:\"5.0.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"C:\\wamp64\\www\\etache\\node_modules\\auto-bind\";}s:5:\"chalk\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:5:\"chalk\";s:10:\"\0*\0version\";s:5:\"5.6.2\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:39:\"C:\\wamp64\\www\\etache\\node_modules\\chalk\";}s:9:\"cli-boxes\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"cli-boxes\";s:10:\"\0*\0version\";s:5:\"4.0.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"C:\\wamp64\\www\\etache\\node_modules\\cli-boxes\";}s:10:\"cli-cursor\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"cli-cursor\";s:10:\"\0*\0version\";s:5:\"4.0.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\wamp64\\www\\etache\\node_modules\\cli-cursor\";}s:12:\"cli-truncate\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"cli-truncate\";s:10:\"\0*\0version\";s:5:\"6.1.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"C:\\wamp64\\www\\etache\\node_modules\\cli-truncate\";}s:12:\"code-excerpt\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"code-excerpt\";s:10:\"\0*\0version\";s:5:\"4.0.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"C:\\wamp64\\www\\etache\\node_modules\\code-excerpt\";}s:9:\"commander\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"commander\";s:10:\"\0*\0version\";s:6:\"15.0.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"C:\\wamp64\\www\\etache\\node_modules\\commander\";}s:17:\"convert-to-spaces\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"convert-to-spaces\";s:10:\"\0*\0version\";s:5:\"2.0.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"C:\\wamp64\\www\\etache\\node_modules\\convert-to-spaces\";}s:11:\"environment\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"environment\";s:10:\"\0*\0version\";s:5:\"1.1.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\node_modules\\environment\";}s:10:\"es-toolkit\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"es-toolkit\";s:10:\"\0*\0version\";s:6:\"1.52.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\wamp64\\www\\etache\\node_modules\\es-toolkit\";}s:20:\"escape-string-regexp\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"escape-string-regexp\";s:10:\"\0*\0version\";s:5:\"2.0.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:54:\"C:\\wamp64\\www\\etache\\node_modules\\escape-string-regexp\";}s:20:\"get-east-asian-width\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"get-east-asian-width\";s:10:\"\0*\0version\";s:5:\"1.7.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:54:\"C:\\wamp64\\www\\etache\\node_modules\\get-east-asian-width\";}s:13:\"indent-string\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"indent-string\";s:10:\"\0*\0version\";s:5:\"5.0.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"C:\\wamp64\\www\\etache\\node_modules\\indent-string\";}s:3:\"ink\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:3:\"ink\";s:10:\"\0*\0version\";s:5:\"7.1.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:37:\"C:\\wamp64\\www\\etache\\node_modules\\ink\";}s:23:\"is-fullwidth-code-point\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"is-fullwidth-code-point\";s:10:\"\0*\0version\";s:5:\"5.1.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:57:\"C:\\wamp64\\www\\etache\\node_modules\\is-fullwidth-code-point\";}s:8:\"is-in-ci\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"is-in-ci\";s:10:\"\0*\0version\";s:5:\"2.0.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:42:\"C:\\wamp64\\www\\etache\\node_modules\\is-in-ci\";}s:8:\"mimic-fn\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"mimic-fn\";s:10:\"\0*\0version\";s:5:\"2.1.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:42:\"C:\\wamp64\\www\\etache\\node_modules\\mimic-fn\";}s:7:\"onetime\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:7:\"onetime\";s:10:\"\0*\0version\";s:5:\"5.1.2\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:41:\"C:\\wamp64\\www\\etache\\node_modules\\onetime\";}s:13:\"patch-console\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"patch-console\";s:10:\"\0*\0version\";s:5:\"2.0.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"C:\\wamp64\\www\\etache\\node_modules\\patch-console\";}s:5:\"react\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:5:\"react\";s:10:\"\0*\0version\";s:6:\"19.3.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:39:\"C:\\wamp64\\www\\etache\\node_modules\\react\";}s:16:\"react-reconciler\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"react-reconciler\";s:10:\"\0*\0version\";s:6:\"0.33.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"C:\\wamp64\\www\\etache\\node_modules\\react-reconciler\";}s:14:\"restore-cursor\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"restore-cursor\";s:10:\"\0*\0version\";s:5:\"4.0.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"C:\\wamp64\\www\\etache\\node_modules\\restore-cursor\";}s:9:\"scheduler\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"scheduler\";s:10:\"\0*\0version\";s:6:\"0.27.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"C:\\wamp64\\www\\etache\\node_modules\\scheduler\";}s:11:\"signal-exit\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"signal-exit\";s:10:\"\0*\0version\";s:5:\"3.0.7\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\node_modules\\signal-exit\";}s:10:\"slice-ansi\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"slice-ansi\";s:10:\"\0*\0version\";s:5:\"9.0.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\wamp64\\www\\etache\\node_modules\\slice-ansi\";}s:11:\"stack-utils\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"stack-utils\";s:10:\"\0*\0version\";s:5:\"2.0.6\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\node_modules\\stack-utils\";}s:12:\"string-width\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"string-width\";s:10:\"\0*\0version\";s:5:\"8.3.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"C:\\wamp64\\www\\etache\\node_modules\\string-width\";}s:10:\"strip-ansi\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"strip-ansi\";s:10:\"\0*\0version\";s:5:\"7.2.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\wamp64\\www\\etache\\node_modules\\strip-ansi\";}s:10:\"tagged-tag\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"tagged-tag\";s:10:\"\0*\0version\";s:5:\"1.0.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\wamp64\\www\\etache\\node_modules\\tagged-tag\";}s:13:\"terminal-size\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"terminal-size\";s:10:\"\0*\0version\";s:5:\"4.0.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"C:\\wamp64\\www\\etache\\node_modules\\terminal-size\";}s:9:\"type-fest\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"type-fest\";s:10:\"\0*\0version\";s:6:\"5.10.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"C:\\wamp64\\www\\etache\\node_modules\\type-fest\";}s:11:\"widest-line\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"widest-line\";s:10:\"\0*\0version\";s:5:\"6.0.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\node_modules\\widest-line\";}s:9:\"wrap-ansi\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"wrap-ansi\";s:10:\"\0*\0version\";s:6:\"10.0.2\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"C:\\wamp64\\www\\etache\\node_modules\\wrap-ansi\";}s:2:\"ws\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:2:\"ws\";s:10:\"\0*\0version\";s:6:\"8.21.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:36:\"C:\\wamp64\\www\\etache\\node_modules\\ws\";}s:11:\"yoga-layout\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"yoga-layout\";s:10:\"\0*\0version\";s:5:\"3.2.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\node_modules\\yoga-layout\";}s:16:\"@alloc/quick-lru\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"@alloc/quick-lru\";s:10:\"\0*\0version\";s:5:\"5.3.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"C:\\wamp64\\www\\etache\\node_modules\\@alloc\\quick-lru\";}s:23:\"@jridgewell/gen-mapping\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"@jridgewell/gen-mapping\";s:10:\"\0*\0version\";s:6:\"0.3.13\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:57:\"C:\\wamp64\\www\\etache\\node_modules\\@jridgewell\\gen-mapping\";}s:21:\"@jridgewell/remapping\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"@jridgewell/remapping\";s:10:\"\0*\0version\";s:5:\"2.3.5\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:55:\"C:\\wamp64\\www\\etache\\node_modules\\@jridgewell\\remapping\";}s:23:\"@jridgewell/resolve-uri\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"@jridgewell/resolve-uri\";s:10:\"\0*\0version\";s:5:\"3.1.2\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:57:\"C:\\wamp64\\www\\etache\\node_modules\\@jridgewell\\resolve-uri\";}s:27:\"@jridgewell/sourcemap-codec\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:27:\"@jridgewell/sourcemap-codec\";s:10:\"\0*\0version\";s:5:\"1.6.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:61:\"C:\\wamp64\\www\\etache\\node_modules\\@jridgewell\\sourcemap-codec\";}s:25:\"@jridgewell/trace-mapping\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"@jridgewell/trace-mapping\";s:10:\"\0*\0version\";s:6:\"0.3.31\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:59:\"C:\\wamp64\\www\\etache\\node_modules\\@jridgewell\\trace-mapping\";}s:19:\"@nodelib/fs.scandir\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"@nodelib/fs.scandir\";s:10:\"\0*\0version\";s:5:\"2.1.5\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"C:\\wamp64\\www\\etache\\node_modules\\@nodelib\\fs.scandir\";}s:16:\"@nodelib/fs.stat\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"@nodelib/fs.stat\";s:10:\"\0*\0version\";s:5:\"2.0.5\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"C:\\wamp64\\www\\etache\\node_modules\\@nodelib\\fs.stat\";}s:16:\"@nodelib/fs.walk\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"@nodelib/fs.walk\";s:10:\"\0*\0version\";s:5:\"1.2.8\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"C:\\wamp64\\www\\etache\\node_modules\\@nodelib\\fs.walk\";}s:18:\"@oxc-project/types\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"@oxc-project/types\";s:10:\"\0*\0version\";s:7:\"0.151.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"C:\\wamp64\\www\\etache\\node_modules\\@oxc-project\\types\";}s:34:\"@rolldown/binding-android-arm-eabi\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:34:\"@rolldown/binding-android-arm-eabi\";s:10:\"\0*\0version\";s:6:\"1.2.11\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:68:\"C:\\wamp64\\www\\etache\\node_modules\\@rolldown\\binding-android-arm-eabi\";}s:31:\"@rolldown/binding-android-arm64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:31:\"@rolldown/binding-android-arm64\";s:10:\"\0*\0version\";s:6:\"1.2.11\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:65:\"C:\\wamp64\\www\\etache\\node_modules\\@rolldown\\binding-android-arm64\";}s:30:\"@rolldown/binding-darwin-arm64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:30:\"@rolldown/binding-darwin-arm64\";s:10:\"\0*\0version\";s:6:\"1.2.11\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:64:\"C:\\wamp64\\www\\etache\\node_modules\\@rolldown\\binding-darwin-arm64\";}s:28:\"@rolldown/binding-darwin-x64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:28:\"@rolldown/binding-darwin-x64\";s:10:\"\0*\0version\";s:6:\"1.2.11\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:62:\"C:\\wamp64\\www\\etache\\node_modules\\@rolldown\\binding-darwin-x64\";}s:29:\"@rolldown/binding-freebsd-x64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:29:\"@rolldown/binding-freebsd-x64\";s:10:\"\0*\0version\";s:6:\"1.2.11\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:63:\"C:\\wamp64\\www\\etache\\node_modules\\@rolldown\\binding-freebsd-x64\";}s:37:\"@rolldown/binding-linux-arm-gnueabihf\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:37:\"@rolldown/binding-linux-arm-gnueabihf\";s:10:\"\0*\0version\";s:6:\"1.2.11\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:71:\"C:\\wamp64\\www\\etache\\node_modules\\@rolldown\\binding-linux-arm-gnueabihf\";}s:33:\"@rolldown/binding-linux-arm64-gnu\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:33:\"@rolldown/binding-linux-arm64-gnu\";s:10:\"\0*\0version\";s:6:\"1.2.11\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:67:\"C:\\wamp64\\www\\etache\\node_modules\\@rolldown\\binding-linux-arm64-gnu\";}s:34:\"@rolldown/binding-linux-arm64-musl\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:34:\"@rolldown/binding-linux-arm64-musl\";s:10:\"\0*\0version\";s:6:\"1.2.11\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:68:\"C:\\wamp64\\www\\etache\\node_modules\\@rolldown\\binding-linux-arm64-musl\";}s:33:\"@rolldown/binding-linux-ppc64-gnu\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:33:\"@rolldown/binding-linux-ppc64-gnu\";s:10:\"\0*\0version\";s:6:\"1.2.11\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:67:\"C:\\wamp64\\www\\etache\\node_modules\\@rolldown\\binding-linux-ppc64-gnu\";}s:33:\"@rolldown/binding-linux-s390x-gnu\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:33:\"@rolldown/binding-linux-s390x-gnu\";s:10:\"\0*\0version\";s:6:\"1.2.11\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:67:\"C:\\wamp64\\www\\etache\\node_modules\\@rolldown\\binding-linux-s390x-gnu\";}s:31:\"@rolldown/binding-linux-x64-gnu\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:31:\"@rolldown/binding-linux-x64-gnu\";s:10:\"\0*\0version\";s:6:\"1.2.11\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:65:\"C:\\wamp64\\www\\etache\\node_modules\\@rolldown\\binding-linux-x64-gnu\";}s:32:\"@rolldown/binding-linux-x64-musl\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:32:\"@rolldown/binding-linux-x64-musl\";s:10:\"\0*\0version\";s:6:\"1.2.11\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:66:\"C:\\wamp64\\www\\etache\\node_modules\\@rolldown\\binding-linux-x64-musl\";}s:35:\"@rolldown/binding-openharmony-arm64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:35:\"@rolldown/binding-openharmony-arm64\";s:10:\"\0*\0version\";s:6:\"1.2.11\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:69:\"C:\\wamp64\\www\\etache\\node_modules\\@rolldown\\binding-openharmony-arm64\";}s:34:\"@rolldown/binding-win32-arm64-msvc\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:34:\"@rolldown/binding-win32-arm64-msvc\";s:10:\"\0*\0version\";s:6:\"1.2.11\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:68:\"C:\\wamp64\\www\\etache\\node_modules\\@rolldown\\binding-win32-arm64-msvc\";}s:32:\"@rolldown/binding-win32-x64-msvc\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:32:\"@rolldown/binding-win32-x64-msvc\";s:10:\"\0*\0version\";s:6:\"1.2.11\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:66:\"C:\\wamp64\\www\\etache\\node_modules\\@rolldown\\binding-win32-x64-msvc\";}s:21:\"@rolldown/pluginutils\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"@rolldown/pluginutils\";s:10:\"\0*\0version\";s:5:\"1.0.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:55:\"C:\\wamp64\\www\\etache\\node_modules\\@rolldown\\pluginutils\";}s:18:\"@tailwindcss/forms\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"@tailwindcss/forms\";s:10:\"\0*\0version\";s:6:\"0.5.11\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^0.5.2\";s:7:\"\0*\0path\";s:52:\"C:\\wamp64\\www\\etache\\node_modules\\@tailwindcss\\forms\";}s:17:\"@tailwindcss/node\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"@tailwindcss/node\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"C:\\wamp64\\www\\etache\\node_modules\\@tailwindcss\\node\";}s:18:\"@tailwindcss/oxide\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"@tailwindcss/oxide\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"C:\\wamp64\\www\\etache\\node_modules\\@tailwindcss\\oxide\";}s:32:\"@tailwindcss/oxide-android-arm64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:32:\"@tailwindcss/oxide-android-arm64\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:66:\"C:\\wamp64\\www\\etache\\node_modules\\@tailwindcss\\oxide-android-arm64\";}s:31:\"@tailwindcss/oxide-darwin-arm64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:31:\"@tailwindcss/oxide-darwin-arm64\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:65:\"C:\\wamp64\\www\\etache\\node_modules\\@tailwindcss\\oxide-darwin-arm64\";}s:29:\"@tailwindcss/oxide-darwin-x64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:29:\"@tailwindcss/oxide-darwin-x64\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:63:\"C:\\wamp64\\www\\etache\\node_modules\\@tailwindcss\\oxide-darwin-x64\";}s:30:\"@tailwindcss/oxide-freebsd-x64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:30:\"@tailwindcss/oxide-freebsd-x64\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:64:\"C:\\wamp64\\www\\etache\\node_modules\\@tailwindcss\\oxide-freebsd-x64\";}s:38:\"@tailwindcss/oxide-linux-arm-gnueabihf\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:38:\"@tailwindcss/oxide-linux-arm-gnueabihf\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:72:\"C:\\wamp64\\www\\etache\\node_modules\\@tailwindcss\\oxide-linux-arm-gnueabihf\";}s:34:\"@tailwindcss/oxide-linux-arm64-gnu\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:34:\"@tailwindcss/oxide-linux-arm64-gnu\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:68:\"C:\\wamp64\\www\\etache\\node_modules\\@tailwindcss\\oxide-linux-arm64-gnu\";}s:35:\"@tailwindcss/oxide-linux-arm64-musl\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:35:\"@tailwindcss/oxide-linux-arm64-musl\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:69:\"C:\\wamp64\\www\\etache\\node_modules\\@tailwindcss\\oxide-linux-arm64-musl\";}s:32:\"@tailwindcss/oxide-linux-x64-gnu\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:32:\"@tailwindcss/oxide-linux-x64-gnu\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:66:\"C:\\wamp64\\www\\etache\\node_modules\\@tailwindcss\\oxide-linux-x64-gnu\";}s:33:\"@tailwindcss/oxide-linux-x64-musl\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:33:\"@tailwindcss/oxide-linux-x64-musl\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:67:\"C:\\wamp64\\www\\etache\\node_modules\\@tailwindcss\\oxide-linux-x64-musl\";}s:30:\"@tailwindcss/oxide-wasm32-wasi\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:30:\"@tailwindcss/oxide-wasm32-wasi\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:64:\"C:\\wamp64\\www\\etache\\node_modules\\@tailwindcss\\oxide-wasm32-wasi\";}s:35:\"@tailwindcss/oxide-win32-arm64-msvc\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:35:\"@tailwindcss/oxide-win32-arm64-msvc\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:69:\"C:\\wamp64\\www\\etache\\node_modules\\@tailwindcss\\oxide-win32-arm64-msvc\";}s:33:\"@tailwindcss/oxide-win32-x64-msvc\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:33:\"@tailwindcss/oxide-win32-x64-msvc\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:67:\"C:\\wamp64\\www\\etache\\node_modules\\@tailwindcss\\oxide-win32-x64-msvc\";}s:17:\"@tailwindcss/vite\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"@tailwindcss/vite\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^4.0.0\";s:7:\"\0*\0path\";s:51:\"C:\\wamp64\\www\\etache\\node_modules\\@tailwindcss\\vite\";}s:15:\"@vue/reactivity\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"@vue/reactivity\";s:10:\"\0*\0version\";s:6:\"3.5.43\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:49:\"C:\\wamp64\\www\\etache\\node_modules\\@vue\\reactivity\";}s:11:\"@vue/shared\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"@vue/shared\";s:10:\"\0*\0version\";s:6:\"3.5.43\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\node_modules\\@vue\\shared\";}s:8:\"alpinejs\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"alpinejs\";s:10:\"\0*\0version\";s:6:\"3.17.4\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^3.4.2\";s:7:\"\0*\0path\";s:42:\"C:\\wamp64\\www\\etache\\node_modules\\alpinejs\";}s:11:\"any-promise\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"any-promise\";s:10:\"\0*\0version\";s:5:\"1.3.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\node_modules\\any-promise\";}s:8:\"anymatch\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"anymatch\";s:10:\"\0*\0version\";s:5:\"3.1.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:42:\"C:\\wamp64\\www\\etache\\node_modules\\anymatch\";}s:3:\"arg\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:3:\"arg\";s:10:\"\0*\0version\";s:5:\"5.0.2\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:37:\"C:\\wamp64\\www\\etache\\node_modules\\arg\";}s:12:\"autoprefixer\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"autoprefixer\";s:10:\"\0*\0version\";s:6:\"10.6.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:7:\"^10.4.2\";s:7:\"\0*\0path\";s:46:\"C:\\wamp64\\www\\etache\\node_modules\\autoprefixer\";}s:24:\"baseline-browser-mapping\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:24:\"baseline-browser-mapping\";s:10:\"\0*\0version\";s:7:\"2.11.26\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:58:\"C:\\wamp64\\www\\etache\\node_modules\\baseline-browser-mapping\";}s:17:\"binary-extensions\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"binary-extensions\";s:10:\"\0*\0version\";s:5:\"2.3.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"C:\\wamp64\\www\\etache\\node_modules\\binary-extensions\";}s:6:\"braces\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:6:\"braces\";s:10:\"\0*\0version\";s:5:\"3.0.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:40:\"C:\\wamp64\\www\\etache\\node_modules\\braces\";}s:12:\"browserslist\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"browserslist\";s:10:\"\0*\0version\";s:6:\"4.29.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"C:\\wamp64\\www\\etache\\node_modules\\browserslist\";}s:13:\"camelcase-css\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"camelcase-css\";s:10:\"\0*\0version\";s:5:\"2.0.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"C:\\wamp64\\www\\etache\\node_modules\\camelcase-css\";}s:12:\"caniuse-lite\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"caniuse-lite\";s:10:\"\0*\0version\";s:12:\"1.0.30001812\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"C:\\wamp64\\www\\etache\\node_modules\\caniuse-lite\";}s:8:\"chokidar\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"chokidar\";s:10:\"\0*\0version\";s:5:\"3.6.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:42:\"C:\\wamp64\\www\\etache\\node_modules\\chokidar\";}s:5:\"cliui\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:5:\"cliui\";s:10:\"\0*\0version\";s:5:\"9.0.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:39:\"C:\\wamp64\\www\\etache\\node_modules\\cliui\";}s:12:\"concurrently\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"concurrently\";s:10:\"\0*\0version\";s:6:\"10.0.5\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:7:\"^10.0.3\";s:7:\"\0*\0path\";s:46:\"C:\\wamp64\\www\\etache\\node_modules\\concurrently\";}s:6:\"cssesc\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:6:\"cssesc\";s:10:\"\0*\0version\";s:5:\"3.0.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:40:\"C:\\wamp64\\www\\etache\\node_modules\\cssesc\";}s:11:\"detect-libc\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"detect-libc\";s:10:\"\0*\0version\";s:5:\"2.1.2\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\node_modules\\detect-libc\";}s:10:\"didyoumean\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"didyoumean\";s:10:\"\0*\0version\";s:5:\"1.2.2\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\wamp64\\www\\etache\\node_modules\\didyoumean\";}s:3:\"dlv\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:3:\"dlv\";s:10:\"\0*\0version\";s:5:\"1.1.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:37:\"C:\\wamp64\\www\\etache\\node_modules\\dlv\";}s:20:\"electron-to-chromium\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"electron-to-chromium\";s:10:\"\0*\0version\";s:7:\"1.5.439\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:54:\"C:\\wamp64\\www\\etache\\node_modules\\electron-to-chromium\";}s:11:\"emoji-regex\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"emoji-regex\";s:10:\"\0*\0version\";s:6:\"10.6.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\node_modules\\emoji-regex\";}s:16:\"enhanced-resolve\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"enhanced-resolve\";s:10:\"\0*\0version\";s:6:\"5.25.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"C:\\wamp64\\www\\etache\\node_modules\\enhanced-resolve\";}s:9:\"es-errors\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"es-errors\";s:10:\"\0*\0version\";s:5:\"1.3.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"C:\\wamp64\\www\\etache\\node_modules\\es-errors\";}s:8:\"escalade\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"escalade\";s:10:\"\0*\0version\";s:5:\"3.2.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:42:\"C:\\wamp64\\www\\etache\\node_modules\\escalade\";}s:9:\"fast-glob\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"fast-glob\";s:10:\"\0*\0version\";s:5:\"3.3.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"C:\\wamp64\\www\\etache\\node_modules\\fast-glob\";}s:5:\"fastq\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:5:\"fastq\";s:10:\"\0*\0version\";s:6:\"1.20.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:39:\"C:\\wamp64\\www\\etache\\node_modules\\fastq\";}s:10:\"fill-range\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"fill-range\";s:10:\"\0*\0version\";s:5:\"7.1.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\wamp64\\www\\etache\\node_modules\\fill-range\";}s:11:\"fraction.js\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"fraction.js\";s:10:\"\0*\0version\";s:5:\"5.3.4\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\node_modules\\fraction.js\";}s:8:\"fsevents\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"fsevents\";s:10:\"\0*\0version\";s:5:\"2.3.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:42:\"C:\\wamp64\\www\\etache\\node_modules\\fsevents\";}s:13:\"function-bind\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"function-bind\";s:10:\"\0*\0version\";s:5:\"1.1.2\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"C:\\wamp64\\www\\etache\\node_modules\\function-bind\";}s:15:\"get-caller-file\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"get-caller-file\";s:10:\"\0*\0version\";s:5:\"2.0.5\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:49:\"C:\\wamp64\\www\\etache\\node_modules\\get-caller-file\";}s:11:\"glob-parent\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"glob-parent\";s:10:\"\0*\0version\";s:5:\"6.0.2\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\node_modules\\glob-parent\";}s:11:\"graceful-fs\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"graceful-fs\";s:10:\"\0*\0version\";s:6:\"4.2.11\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\node_modules\\graceful-fs\";}s:6:\"hasown\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:6:\"hasown\";s:10:\"\0*\0version\";s:5:\"2.0.4\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:40:\"C:\\wamp64\\www\\etache\\node_modules\\hasown\";}s:14:\"is-binary-path\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"is-binary-path\";s:10:\"\0*\0version\";s:5:\"2.1.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"C:\\wamp64\\www\\etache\\node_modules\\is-binary-path\";}s:14:\"is-core-module\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"is-core-module\";s:10:\"\0*\0version\";s:6:\"2.17.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"C:\\wamp64\\www\\etache\\node_modules\\is-core-module\";}s:10:\"is-extglob\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"is-extglob\";s:10:\"\0*\0version\";s:5:\"2.1.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\wamp64\\www\\etache\\node_modules\\is-extglob\";}s:7:\"is-glob\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:7:\"is-glob\";s:10:\"\0*\0version\";s:5:\"4.0.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:41:\"C:\\wamp64\\www\\etache\\node_modules\\is-glob\";}s:9:\"is-number\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"is-number\";s:10:\"\0*\0version\";s:5:\"7.0.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"C:\\wamp64\\www\\etache\\node_modules\\is-number\";}s:4:\"jiti\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:4:\"jiti\";s:10:\"\0*\0version\";s:5:\"2.7.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:38:\"C:\\wamp64\\www\\etache\\node_modules\\jiti\";}s:19:\"laravel-vite-plugin\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"laravel-vite-plugin\";s:10:\"\0*\0version\";s:5:\"3.2.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:4:\"^3.1\";s:7:\"\0*\0path\";s:53:\"C:\\wamp64\\www\\etache\\node_modules\\laravel-vite-plugin\";}s:12:\"lightningcss\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"lightningcss\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"C:\\wamp64\\www\\etache\\node_modules\\lightningcss\";}s:26:\"lightningcss-android-arm64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:26:\"lightningcss-android-arm64\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:60:\"C:\\wamp64\\www\\etache\\node_modules\\lightningcss-android-arm64\";}s:25:\"lightningcss-darwin-arm64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"lightningcss-darwin-arm64\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:59:\"C:\\wamp64\\www\\etache\\node_modules\\lightningcss-darwin-arm64\";}s:23:\"lightningcss-darwin-x64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"lightningcss-darwin-x64\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:57:\"C:\\wamp64\\www\\etache\\node_modules\\lightningcss-darwin-x64\";}s:24:\"lightningcss-freebsd-x64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:24:\"lightningcss-freebsd-x64\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:58:\"C:\\wamp64\\www\\etache\\node_modules\\lightningcss-freebsd-x64\";}s:32:\"lightningcss-linux-arm-gnueabihf\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:32:\"lightningcss-linux-arm-gnueabihf\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:66:\"C:\\wamp64\\www\\etache\\node_modules\\lightningcss-linux-arm-gnueabihf\";}s:28:\"lightningcss-linux-arm64-gnu\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:28:\"lightningcss-linux-arm64-gnu\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:62:\"C:\\wamp64\\www\\etache\\node_modules\\lightningcss-linux-arm64-gnu\";}s:29:\"lightningcss-linux-arm64-musl\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:29:\"lightningcss-linux-arm64-musl\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:63:\"C:\\wamp64\\www\\etache\\node_modules\\lightningcss-linux-arm64-musl\";}s:26:\"lightningcss-linux-x64-gnu\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:26:\"lightningcss-linux-x64-gnu\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:60:\"C:\\wamp64\\www\\etache\\node_modules\\lightningcss-linux-x64-gnu\";}s:27:\"lightningcss-linux-x64-musl\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:27:\"lightningcss-linux-x64-musl\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:61:\"C:\\wamp64\\www\\etache\\node_modules\\lightningcss-linux-x64-musl\";}s:29:\"lightningcss-win32-arm64-msvc\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:29:\"lightningcss-win32-arm64-msvc\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:63:\"C:\\wamp64\\www\\etache\\node_modules\\lightningcss-win32-arm64-msvc\";}s:27:\"lightningcss-win32-x64-msvc\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:27:\"lightningcss-win32-x64-msvc\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:61:\"C:\\wamp64\\www\\etache\\node_modules\\lightningcss-win32-x64-msvc\";}s:9:\"lilconfig\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"lilconfig\";s:10:\"\0*\0version\";s:5:\"3.1.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"C:\\wamp64\\www\\etache\\node_modules\\lilconfig\";}s:17:\"lines-and-columns\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"lines-and-columns\";s:10:\"\0*\0version\";s:5:\"1.2.4\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"C:\\wamp64\\www\\etache\\node_modules\\lines-and-columns\";}s:12:\"magic-string\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"magic-string\";s:10:\"\0*\0version\";s:7:\"0.30.21\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"C:\\wamp64\\www\\etache\\node_modules\\magic-string\";}s:6:\"merge2\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:6:\"merge2\";s:10:\"\0*\0version\";s:5:\"1.4.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:40:\"C:\\wamp64\\www\\etache\\node_modules\\merge2\";}s:10:\"micromatch\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"micromatch\";s:10:\"\0*\0version\";s:5:\"4.0.8\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\wamp64\\www\\etache\\node_modules\\micromatch\";}s:17:\"mini-svg-data-uri\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"mini-svg-data-uri\";s:10:\"\0*\0version\";s:5:\"1.4.4\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"C:\\wamp64\\www\\etache\\node_modules\\mini-svg-data-uri\";}s:2:\"mz\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:2:\"mz\";s:10:\"\0*\0version\";s:5:\"2.7.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:36:\"C:\\wamp64\\www\\etache\\node_modules\\mz\";}s:6:\"nanoid\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:6:\"nanoid\";s:10:\"\0*\0version\";s:6:\"3.3.19\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:40:\"C:\\wamp64\\www\\etache\\node_modules\\nanoid\";}s:13:\"node-releases\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"node-releases\";s:10:\"\0*\0version\";s:6:\"2.0.57\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"C:\\wamp64\\www\\etache\\node_modules\\node-releases\";}s:14:\"normalize-path\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"normalize-path\";s:10:\"\0*\0version\";s:5:\"3.0.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"C:\\wamp64\\www\\etache\\node_modules\\normalize-path\";}s:13:\"object-assign\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"object-assign\";s:10:\"\0*\0version\";s:5:\"4.1.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"C:\\wamp64\\www\\etache\\node_modules\\object-assign\";}s:11:\"object-hash\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"object-hash\";s:10:\"\0*\0version\";s:5:\"3.0.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\node_modules\\object-hash\";}s:10:\"path-parse\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"path-parse\";s:10:\"\0*\0version\";s:5:\"1.0.7\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\wamp64\\www\\etache\\node_modules\\path-parse\";}s:10:\"picocolors\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"picocolors\";s:10:\"\0*\0version\";s:5:\"1.1.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\wamp64\\www\\etache\\node_modules\\picocolors\";}s:9:\"picomatch\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"picomatch\";s:10:\"\0*\0version\";s:5:\"2.3.2\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"C:\\wamp64\\www\\etache\\node_modules\\picomatch\";}s:7:\"pirates\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:7:\"pirates\";s:10:\"\0*\0version\";s:5:\"4.0.7\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:41:\"C:\\wamp64\\www\\etache\\node_modules\\pirates\";}s:7:\"postcss\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:7:\"postcss\";s:10:\"\0*\0version\";s:6:\"8.5.28\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:7:\"^8.4.31\";s:7:\"\0*\0path\";s:41:\"C:\\wamp64\\www\\etache\\node_modules\\postcss\";}s:14:\"postcss-import\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"postcss-import\";s:10:\"\0*\0version\";s:6:\"15.1.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"C:\\wamp64\\www\\etache\\node_modules\\postcss-import\";}s:10:\"postcss-js\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"postcss-js\";s:10:\"\0*\0version\";s:5:\"4.1.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\wamp64\\www\\etache\\node_modules\\postcss-js\";}s:19:\"postcss-load-config\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"postcss-load-config\";s:10:\"\0*\0version\";s:5:\"6.0.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"C:\\wamp64\\www\\etache\\node_modules\\postcss-load-config\";}s:14:\"postcss-nested\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"postcss-nested\";s:10:\"\0*\0version\";s:5:\"6.2.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"C:\\wamp64\\www\\etache\\node_modules\\postcss-nested\";}s:23:\"postcss-selector-parser\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"postcss-selector-parser\";s:10:\"\0*\0version\";s:5:\"6.1.4\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:57:\"C:\\wamp64\\www\\etache\\node_modules\\postcss-selector-parser\";}s:20:\"postcss-value-parser\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"postcss-value-parser\";s:10:\"\0*\0version\";s:5:\"4.2.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:54:\"C:\\wamp64\\www\\etache\\node_modules\\postcss-value-parser\";}s:15:\"queue-microtask\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"queue-microtask\";s:10:\"\0*\0version\";s:5:\"1.2.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:49:\"C:\\wamp64\\www\\etache\\node_modules\\queue-microtask\";}s:10:\"read-cache\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"read-cache\";s:10:\"\0*\0version\";s:5:\"1.0.2\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\wamp64\\www\\etache\\node_modules\\read-cache\";}s:8:\"readdirp\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"readdirp\";s:10:\"\0*\0version\";s:5:\"3.6.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:42:\"C:\\wamp64\\www\\etache\\node_modules\\readdirp\";}s:7:\"resolve\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:7:\"resolve\";s:10:\"\0*\0version\";s:7:\"1.22.12\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:41:\"C:\\wamp64\\www\\etache\\node_modules\\resolve\";}s:7:\"reusify\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:7:\"reusify\";s:10:\"\0*\0version\";s:5:\"1.1.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:41:\"C:\\wamp64\\www\\etache\\node_modules\\reusify\";}s:8:\"rolldown\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"rolldown\";s:10:\"\0*\0version\";s:6:\"1.2.11\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:42:\"C:\\wamp64\\www\\etache\\node_modules\\rolldown\";}s:12:\"run-parallel\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"run-parallel\";s:10:\"\0*\0version\";s:5:\"1.2.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"C:\\wamp64\\www\\etache\\node_modules\\run-parallel\";}s:4:\"rxjs\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:4:\"rxjs\";s:10:\"\0*\0version\";s:5:\"7.8.2\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:38:\"C:\\wamp64\\www\\etache\\node_modules\\rxjs\";}s:11:\"shell-quote\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"shell-quote\";s:10:\"\0*\0version\";s:5:\"1.9.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\node_modules\\shell-quote\";}s:13:\"source-map-js\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"source-map-js\";s:10:\"\0*\0version\";s:5:\"1.2.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"C:\\wamp64\\www\\etache\\node_modules\\source-map-js\";}s:7:\"sucrase\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:7:\"sucrase\";s:10:\"\0*\0version\";s:6:\"3.35.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:41:\"C:\\wamp64\\www\\etache\\node_modules\\sucrase\";}s:14:\"supports-color\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"supports-color\";s:10:\"\0*\0version\";s:6:\"10.2.2\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"C:\\wamp64\\www\\etache\\node_modules\\supports-color\";}s:31:\"supports-preserve-symlinks-flag\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:31:\"supports-preserve-symlinks-flag\";s:10:\"\0*\0version\";s:5:\"1.0.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:65:\"C:\\wamp64\\www\\etache\\node_modules\\supports-preserve-symlinks-flag\";}s:11:\"tailwindcss\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"tailwindcss\";s:10:\"\0*\0version\";s:6:\"3.4.19\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^3.1.0\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\node_modules\\tailwindcss\";}s:7:\"tapable\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:7:\"tapable\";s:10:\"\0*\0version\";s:5:\"2.3.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:41:\"C:\\wamp64\\www\\etache\\node_modules\\tapable\";}s:7:\"thenify\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:7:\"thenify\";s:10:\"\0*\0version\";s:5:\"3.3.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:41:\"C:\\wamp64\\www\\etache\\node_modules\\thenify\";}s:11:\"thenify-all\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"thenify-all\";s:10:\"\0*\0version\";s:5:\"1.6.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\node_modules\\thenify-all\";}s:10:\"tinyglobby\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"tinyglobby\";s:10:\"\0*\0version\";s:6:\"0.2.17\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\wamp64\\www\\etache\\node_modules\\tinyglobby\";}s:14:\"to-regex-range\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"to-regex-range\";s:10:\"\0*\0version\";s:5:\"5.0.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"C:\\wamp64\\www\\etache\\node_modules\\to-regex-range\";}s:9:\"tree-kill\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"tree-kill\";s:10:\"\0*\0version\";s:5:\"1.2.2\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"C:\\wamp64\\www\\etache\\node_modules\\tree-kill\";}s:20:\"ts-interface-checker\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"ts-interface-checker\";s:10:\"\0*\0version\";s:6:\"0.1.13\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:54:\"C:\\wamp64\\www\\etache\\node_modules\\ts-interface-checker\";}s:5:\"tslib\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:5:\"tslib\";s:10:\"\0*\0version\";s:5:\"2.8.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:39:\"C:\\wamp64\\www\\etache\\node_modules\\tslib\";}s:22:\"update-browserslist-db\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"update-browserslist-db\";s:10:\"\0*\0version\";s:5:\"1.3.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:56:\"C:\\wamp64\\www\\etache\\node_modules\\update-browserslist-db\";}s:14:\"util-deprecate\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"util-deprecate\";s:10:\"\0*\0version\";s:5:\"1.0.2\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"C:\\wamp64\\www\\etache\\node_modules\\util-deprecate\";}s:4:\"vite\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:4:\"vite\";s:10:\"\0*\0version\";s:5:\"8.3.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^8.0.0\";s:7:\"\0*\0path\";s:38:\"C:\\wamp64\\www\\etache\\node_modules\\vite\";}s:23:\"vite-plugin-full-reload\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"vite-plugin-full-reload\";s:10:\"\0*\0version\";s:5:\"1.2.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:57:\"C:\\wamp64\\www\\etache\\node_modules\\vite-plugin-full-reload\";}s:4:\"y18n\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:4:\"y18n\";s:10:\"\0*\0version\";s:5:\"5.0.8\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:38:\"C:\\wamp64\\www\\etache\\node_modules\\y18n\";}s:5:\"yargs\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:5:\"yargs\";s:10:\"\0*\0version\";s:6:\"18.0.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:39:\"C:\\wamp64\\www\\etache\\node_modules\\yargs\";}s:12:\"yargs-parser\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"yargs-parser\";s:10:\"\0*\0version\";s:6:\"22.0.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"C:\\wamp64\\www\\etache\\node_modules\\yargs-parser\";}s:4:\"fdir\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:4:\"fdir\";s:10:\"\0*\0version\";s:5:\"6.5.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:38:\"C:\\wamp64\\www\\etache\\node_modules\\fdir\";}}s:11:\"\0*\0packages\";O:32:\"Laravel\\Roster\\PackageCollection\":2:{s:8:\"\0*\0items\";a:189:{i:0;r:1153;i:1;r:1161;i:2;r:1169;i:3;r:1177;i:4;r:1185;i:5;r:1193;i:6;r:1201;i:7;r:1209;i:8;r:1217;i:9;r:1225;i:10;r:1233;i:11;r:1241;i:12;r:1249;i:13;r:1257;i:14;r:1265;i:15;r:1273;i:16;r:1281;i:17;r:1289;i:18;r:1297;i:19;r:1305;i:20;r:1313;i:21;r:1321;i:22;r:1329;i:23;r:1337;i:24;r:1345;i:25;r:1353;i:26;r:1361;i:27;r:1369;i:28;r:1377;i:29;r:1385;i:30;r:1393;i:31;r:1401;i:32;r:1409;i:33;r:1417;i:34;r:1425;i:35;r:1433;i:36;r:1441;i:37;r:1449;i:38;r:1457;i:39;r:1465;i:40;r:1473;i:41;r:1481;i:42;r:1489;i:43;r:1497;i:44;r:1505;i:45;r:1513;i:46;r:1521;i:47;r:1529;i:48;r:1537;i:49;r:1545;i:50;r:1553;i:51;r:1561;i:52;r:1569;i:53;r:1577;i:54;r:1585;i:55;r:1593;i:56;r:1601;i:57;r:1609;i:58;r:1617;i:59;r:1625;i:60;r:1633;i:61;r:1641;i:62;r:1649;i:63;r:1657;i:64;r:1665;i:65;r:1673;i:66;r:1681;i:67;r:1689;i:68;r:1697;i:69;r:1705;i:70;r:1713;i:71;r:1721;i:72;r:1729;i:73;r:1737;i:74;r:1745;i:75;r:1753;i:76;r:1761;i:77;r:1769;i:78;r:1777;i:79;r:1785;i:80;r:1793;i:81;r:1801;i:82;r:1809;i:83;r:1817;i:84;r:1825;i:85;r:1833;i:86;r:1841;i:87;r:1849;i:88;r:1857;i:89;r:1865;i:90;r:1873;i:91;r:1881;i:92;r:1889;i:93;r:1897;i:94;r:1905;i:95;r:1913;i:96;r:1921;i:97;r:1929;i:98;r:1937;i:99;r:1945;i:100;r:1953;i:101;r:1961;i:102;r:1969;i:103;r:1977;i:104;r:1985;i:105;r:1993;i:106;r:2001;i:107;r:2009;i:108;r:2017;i:109;r:2025;i:110;r:2033;i:111;r:2041;i:112;r:2049;i:113;r:2057;i:114;r:2065;i:115;r:2073;i:116;r:2081;i:117;r:2089;i:118;r:2097;i:119;r:2105;i:120;r:2113;i:121;r:2121;i:122;r:2129;i:123;r:2137;i:124;r:2145;i:125;r:2153;i:126;r:2161;i:127;r:2169;i:128;r:2177;i:129;r:2185;i:130;r:2193;i:131;r:2201;i:132;r:2209;i:133;r:2217;i:134;r:2225;i:135;r:2233;i:136;r:2241;i:137;r:2249;i:138;r:2257;i:139;r:2265;i:140;r:2273;i:141;r:2281;i:142;r:2289;i:143;r:2297;i:144;r:2305;i:145;r:2313;i:146;r:2321;i:147;r:2329;i:148;r:2337;i:149;r:2345;i:150;r:2353;i:151;r:2361;i:152;r:2369;i:153;r:2377;i:154;r:2385;i:155;r:2393;i:156;r:2401;i:157;r:2409;i:158;r:2417;i:159;r:2425;i:160;r:2433;i:161;r:2441;i:162;r:2449;i:163;r:2457;i:164;r:2465;i:165;r:2473;i:166;r:2481;i:167;r:2489;i:168;r:2497;i:169;r:2505;i:170;r:2513;i:171;r:2521;i:172;r:2529;i:173;r:2537;i:174;r:2545;i:175;r:2553;i:176;r:2561;i:177;r:2569;i:178;r:2577;i:179;r:2585;i:180;r:2593;i:181;r:2601;i:182;r:2609;i:183;r:2617;i:184;r:2625;i:185;r:2633;i:186;r:2641;i:187;r:2649;i:188;r:2657;}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}s:17:\"\0*\0packageManager\";E:41:\"Laravel\\Roster\\Enums\\JsPackageManager:Npm\";}s:6:\"stacks\";O:30:\"Laravel\\Roster\\Support\\EnumSet\":1:{s:8:\"\0*\0cases\";a:1:{i:0;E:32:\"Laravel\\Roster\\Enums\\Stack:Blade\";}}s:21:\"browserTestFrameworks\";O:30:\"Laravel\\Roster\\Support\\EnumSet\":1:{s:8:\"\0*\0cases\";a:0:{}}s:9:\"frontends\";O:30:\"Laravel\\Roster\\Support\\EnumSet\":1:{s:8:\"\0*\0cases\";a:0:{}}s:6:\"agents\";O:30:\"Laravel\\Roster\\Support\\EnumSet\":1:{s:8:\"\0*\0cases\";a:2:{i:0;E:37:\"Laravel\\Roster\\Enums\\Agent:ClaudeCode\";i:1;E:32:\"Laravel\\Roster\\Enums\\Agent:Codex\";}}s:7:\"editors\";O:30:\"Laravel\\Roster\\Support\\EnumSet\":1:{s:8:\"\0*\0cases\";a:0:{}}}', 1794177062);
INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('e-tache-cache-roster:project:v3:506313f1ae15934dcf9e25d68c08f41f', 'O:26:\"Laravel\\Roster\\ProjectScan\":8:{s:8:\"basePath\";s:21:\"C:\\wamp64\\www\\etache\\\";s:3:\"php\";O:35:\"Laravel\\Roster\\Ecosystems\\Ecosystem\":2:{s:9:\"\0*\0byName\";a:127:{s:23:\"barryvdh/laravel-dompdf\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"barryvdh/laravel-dompdf\";s:10:\"\0*\0version\";s:5:\"3.1.2\";s:9:\"\0*\0source\";E:43:\"Laravel\\Roster\\Enums\\PackageSource:Composer\";s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:4:\"^3.1\";s:7:\"\0*\0path\";s:51:\"C:\\wamp64\\www\\etache\\vendor\\barryvdh\\laravel-dompdf\";}s:10:\"brick/math\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"brick/math\";s:10:\"\0*\0version\";s:5:\"1.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:38:\"C:\\wamp64\\www\\etache\\vendor\\brick\\math\";}s:31:\"carbonphp/carbon-doctrine-types\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:31:\"carbonphp/carbon-doctrine-types\";s:10:\"\0*\0version\";s:5:\"3.2.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:59:\"C:\\wamp64\\www\\etache\\vendor\\carbonphp\\carbon-doctrine-types\";}s:13:\"composer/pcre\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"composer/pcre\";s:10:\"\0*\0version\";s:5:\"3.4.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:41:\"C:\\wamp64\\www\\etache\\vendor\\composer\\pcre\";}s:23:\"dflydev/dot-access-data\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"dflydev/dot-access-data\";s:10:\"\0*\0version\";s:5:\"3.0.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"C:\\wamp64\\www\\etache\\vendor\\dflydev\\dot-access-data\";}s:18:\"doctrine/inflector\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"doctrine/inflector\";s:10:\"\0*\0version\";s:5:\"2.1.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"C:\\wamp64\\www\\etache\\vendor\\doctrine\\inflector\";}s:14:\"doctrine/lexer\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"doctrine/lexer\";s:10:\"\0*\0version\";s:5:\"3.0.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:42:\"C:\\wamp64\\www\\etache\\vendor\\doctrine\\lexer\";}s:13:\"dompdf/dompdf\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"dompdf/dompdf\";s:10:\"\0*\0version\";s:5:\"3.1.6\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:41:\"C:\\wamp64\\www\\etache\\vendor\\dompdf\\dompdf\";}s:19:\"dompdf/php-font-lib\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"dompdf/php-font-lib\";s:10:\"\0*\0version\";s:5:\"1.0.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"C:\\wamp64\\www\\etache\\vendor\\dompdf\\php-font-lib\";}s:18:\"dompdf/php-svg-lib\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"dompdf/php-svg-lib\";s:10:\"\0*\0version\";s:5:\"1.0.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"C:\\wamp64\\www\\etache\\vendor\\dompdf\\php-svg-lib\";}s:29:\"dragonmantank/cron-expression\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:29:\"dragonmantank/cron-expression\";s:10:\"\0*\0version\";s:5:\"3.6.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:57:\"C:\\wamp64\\www\\etache\\vendor\\dragonmantank\\cron-expression\";}s:23:\"egulias/email-validator\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"egulias/email-validator\";s:10:\"\0*\0version\";s:5:\"4.0.4\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"C:\\wamp64\\www\\etache\\vendor\\egulias\\email-validator\";}s:18:\"fruitcake/php-cors\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"fruitcake/php-cors\";s:10:\"\0*\0version\";s:5:\"1.4.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"C:\\wamp64\\www\\etache\\vendor\\fruitcake\\php-cors\";}s:27:\"graham-campbell/result-type\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:27:\"graham-campbell/result-type\";s:10:\"\0*\0version\";s:5:\"1.2.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:55:\"C:\\wamp64\\www\\etache\\vendor\\graham-campbell\\result-type\";}s:17:\"guzzlehttp/guzzle\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"guzzlehttp/guzzle\";s:10:\"\0*\0version\";s:5:\"8.2.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\vendor\\guzzlehttp\\guzzle\";}s:19:\"guzzlehttp/promises\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"guzzlehttp/promises\";s:10:\"\0*\0version\";s:5:\"3.0.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"C:\\wamp64\\www\\etache\\vendor\\guzzlehttp\\promises\";}s:15:\"guzzlehttp/psr7\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"guzzlehttp/psr7\";s:10:\"\0*\0version\";s:5:\"3.1.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"C:\\wamp64\\www\\etache\\vendor\\guzzlehttp\\psr7\";}s:23:\"guzzlehttp/uri-template\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"guzzlehttp/uri-template\";s:10:\"\0*\0version\";s:5:\"2.0.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"C:\\wamp64\\www\\etache\\vendor\\guzzlehttp\\uri-template\";}s:17:\"laravel/framework\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"laravel/framework\";s:10:\"\0*\0version\";s:7:\"13.33.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^13.17\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\vendor\\laravel\\framework\";}s:15:\"laravel/prompts\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"laravel/prompts\";s:10:\"\0*\0version\";s:6:\"0.3.24\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"C:\\wamp64\\www\\etache\\vendor\\laravel\\prompts\";}s:28:\"laravel/serializable-closure\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:28:\"laravel/serializable-closure\";s:10:\"\0*\0version\";s:5:\"2.1.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:56:\"C:\\wamp64\\www\\etache\\vendor\\laravel\\serializable-closure\";}s:14:\"laravel/tinker\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"laravel/tinker\";s:10:\"\0*\0version\";s:5:\"3.0.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:4:\"^3.0\";s:7:\"\0*\0path\";s:42:\"C:\\wamp64\\www\\etache\\vendor\\laravel\\tinker\";}s:17:\"league/commonmark\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"league/commonmark\";s:10:\"\0*\0version\";s:6:\"2.10.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\vendor\\league\\commonmark\";}s:13:\"league/config\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"league/config\";s:10:\"\0*\0version\";s:5:\"1.2.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:41:\"C:\\wamp64\\www\\etache\\vendor\\league\\config\";}s:16:\"league/flysystem\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"league/flysystem\";s:10:\"\0*\0version\";s:6:\"3.36.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\wamp64\\www\\etache\\vendor\\league\\flysystem\";}s:22:\"league/flysystem-local\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"league/flysystem-local\";s:10:\"\0*\0version\";s:6:\"3.35.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"C:\\wamp64\\www\\etache\\vendor\\league\\flysystem-local\";}s:26:\"league/mime-type-detection\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:26:\"league/mime-type-detection\";s:10:\"\0*\0version\";s:6:\"1.17.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:54:\"C:\\wamp64\\www\\etache\\vendor\\league\\mime-type-detection\";}s:10:\"league/uri\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"league/uri\";s:10:\"\0*\0version\";s:5:\"7.8.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:38:\"C:\\wamp64\\www\\etache\\vendor\\league\\uri\";}s:21:\"league/uri-interfaces\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"league/uri-interfaces\";s:10:\"\0*\0version\";s:5:\"7.8.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:49:\"C:\\wamp64\\www\\etache\\vendor\\league\\uri-interfaces\";}s:23:\"maennchen/zipstream-php\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"maennchen/zipstream-php\";s:10:\"\0*\0version\";s:5:\"3.2.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"C:\\wamp64\\www\\etache\\vendor\\maennchen\\zipstream-php\";}s:17:\"markbaker/complex\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"markbaker/complex\";s:10:\"\0*\0version\";s:5:\"3.0.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\vendor\\markbaker\\complex\";}s:16:\"markbaker/matrix\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"markbaker/matrix\";s:10:\"\0*\0version\";s:5:\"3.0.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\wamp64\\www\\etache\\vendor\\markbaker\\matrix\";}s:17:\"masterminds/html5\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"masterminds/html5\";s:10:\"\0*\0version\";s:6:\"2.11.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\vendor\\masterminds\\html5\";}s:15:\"monolog/monolog\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"monolog/monolog\";s:10:\"\0*\0version\";s:6:\"3.12.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"C:\\wamp64\\www\\etache\\vendor\\monolog\\monolog\";}s:13:\"nesbot/carbon\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"nesbot/carbon\";s:10:\"\0*\0version\";s:6:\"3.14.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:41:\"C:\\wamp64\\www\\etache\\vendor\\nesbot\\carbon\";}s:12:\"nette/schema\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"nette/schema\";s:10:\"\0*\0version\";s:5:\"1.3.6\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:40:\"C:\\wamp64\\www\\etache\\vendor\\nette\\schema\";}s:11:\"nette/utils\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"nette/utils\";s:10:\"\0*\0version\";s:5:\"4.1.5\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:39:\"C:\\wamp64\\www\\etache\\vendor\\nette\\utils\";}s:16:\"nikic/php-parser\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"nikic/php-parser\";s:10:\"\0*\0version\";s:5:\"5.9.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\wamp64\\www\\etache\\vendor\\nikic\\php-parser\";}s:19:\"nunomaduro/termwind\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"nunomaduro/termwind\";s:10:\"\0*\0version\";s:5:\"2.4.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"C:\\wamp64\\www\\etache\\vendor\\nunomaduro\\termwind\";}s:24:\"phpoffice/phpspreadsheet\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:24:\"phpoffice/phpspreadsheet\";s:10:\"\0*\0version\";s:6:\"5.10.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:5:\"^5.10\";s:7:\"\0*\0path\";s:52:\"C:\\wamp64\\www\\etache\\vendor\\phpoffice\\phpspreadsheet\";}s:19:\"phpoption/phpoption\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"phpoption/phpoption\";s:10:\"\0*\0version\";s:6:\"1.10.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"C:\\wamp64\\www\\etache\\vendor\\phpoption\\phpoption\";}s:9:\"psr/clock\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"psr/clock\";s:10:\"\0*\0version\";s:5:\"1.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:37:\"C:\\wamp64\\www\\etache\\vendor\\psr\\clock\";}s:13:\"psr/container\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"psr/container\";s:10:\"\0*\0version\";s:5:\"2.0.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:41:\"C:\\wamp64\\www\\etache\\vendor\\psr\\container\";}s:20:\"psr/event-dispatcher\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"psr/event-dispatcher\";s:10:\"\0*\0version\";s:5:\"1.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"C:\\wamp64\\www\\etache\\vendor\\psr\\event-dispatcher\";}s:15:\"psr/http-client\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"psr/http-client\";s:10:\"\0*\0version\";s:5:\"1.0.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"C:\\wamp64\\www\\etache\\vendor\\psr\\http-client\";}s:16:\"psr/http-factory\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"psr/http-factory\";s:10:\"\0*\0version\";s:5:\"1.1.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\wamp64\\www\\etache\\vendor\\psr\\http-factory\";}s:16:\"psr/http-message\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"psr/http-message\";s:10:\"\0*\0version\";s:3:\"2.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\wamp64\\www\\etache\\vendor\\psr\\http-message\";}s:7:\"psr/log\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:7:\"psr/log\";s:10:\"\0*\0version\";s:5:\"3.0.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:35:\"C:\\wamp64\\www\\etache\\vendor\\psr\\log\";}s:16:\"psr/simple-cache\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"psr/simple-cache\";s:10:\"\0*\0version\";s:5:\"3.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\wamp64\\www\\etache\\vendor\\psr\\simple-cache\";}s:9:\"psy/psysh\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"psy/psysh\";s:10:\"\0*\0version\";s:7:\"0.12.24\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:37:\"C:\\wamp64\\www\\etache\\vendor\\psy\\psysh\";}s:17:\"ramsey/collection\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"ramsey/collection\";s:10:\"\0*\0version\";s:5:\"2.1.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\vendor\\ramsey\\collection\";}s:11:\"ramsey/uuid\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"ramsey/uuid\";s:10:\"\0*\0version\";s:5:\"4.9.4\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:39:\"C:\\wamp64\\www\\etache\\vendor\\ramsey\\uuid\";}s:25:\"sabberworm/php-css-parser\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"sabberworm/php-css-parser\";s:10:\"\0*\0version\";s:5:\"9.5.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"C:\\wamp64\\www\\etache\\vendor\\sabberworm\\php-css-parser\";}s:13:\"symfony/clock\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"symfony/clock\";s:10:\"\0*\0version\";s:6:\"7.4.21\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:41:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\clock\";}s:15:\"symfony/console\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"symfony/console\";s:10:\"\0*\0version\";s:6:\"7.4.23\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\console\";}s:20:\"symfony/css-selector\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"symfony/css-selector\";s:10:\"\0*\0version\";s:6:\"7.4.22\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\css-selector\";}s:29:\"symfony/deprecation-contracts\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:29:\"symfony/deprecation-contracts\";s:10:\"\0*\0version\";s:5:\"3.8.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:57:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\deprecation-contracts\";}s:21:\"symfony/error-handler\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"symfony/error-handler\";s:10:\"\0*\0version\";s:6:\"7.4.22\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:49:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\error-handler\";}s:24:\"symfony/event-dispatcher\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:24:\"symfony/event-dispatcher\";s:10:\"\0*\0version\";s:6:\"7.4.17\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\event-dispatcher\";}s:34:\"symfony/event-dispatcher-contracts\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:34:\"symfony/event-dispatcher-contracts\";s:10:\"\0*\0version\";s:5:\"3.8.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:62:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\event-dispatcher-contracts\";}s:14:\"symfony/finder\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"symfony/finder\";s:10:\"\0*\0version\";s:6:\"7.4.23\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:42:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\finder\";}s:23:\"symfony/http-foundation\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"symfony/http-foundation\";s:10:\"\0*\0version\";s:6:\"7.4.23\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\http-foundation\";}s:19:\"symfony/http-kernel\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"symfony/http-kernel\";s:10:\"\0*\0version\";s:6:\"7.4.23\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\http-kernel\";}s:14:\"symfony/mailer\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"symfony/mailer\";s:10:\"\0*\0version\";s:6:\"7.4.22\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:42:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\mailer\";}s:12:\"symfony/mime\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"symfony/mime\";s:10:\"\0*\0version\";s:6:\"7.4.22\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:40:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\mime\";}s:22:\"symfony/polyfill-ctype\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"symfony/polyfill-ctype\";s:10:\"\0*\0version\";s:6:\"1.37.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\polyfill-ctype\";}s:30:\"symfony/polyfill-intl-grapheme\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:30:\"symfony/polyfill-intl-grapheme\";s:10:\"\0*\0version\";s:6:\"1.43.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:58:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\polyfill-intl-grapheme\";}s:25:\"symfony/polyfill-intl-idn\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"symfony/polyfill-intl-idn\";s:10:\"\0*\0version\";s:6:\"1.43.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\polyfill-intl-idn\";}s:32:\"symfony/polyfill-intl-normalizer\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:32:\"symfony/polyfill-intl-normalizer\";s:10:\"\0*\0version\";s:6:\"1.43.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:60:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\polyfill-intl-normalizer\";}s:25:\"symfony/polyfill-mbstring\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"symfony/polyfill-mbstring\";s:10:\"\0*\0version\";s:6:\"1.43.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\polyfill-mbstring\";}s:22:\"symfony/polyfill-php80\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"symfony/polyfill-php80\";s:10:\"\0*\0version\";s:6:\"1.43.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\polyfill-php80\";}s:22:\"symfony/polyfill-php82\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"symfony/polyfill-php82\";s:10:\"\0*\0version\";s:6:\"1.43.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\polyfill-php82\";}s:22:\"symfony/polyfill-php83\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"symfony/polyfill-php83\";s:10:\"\0*\0version\";s:6:\"1.43.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\polyfill-php83\";}s:22:\"symfony/polyfill-php84\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"symfony/polyfill-php84\";s:10:\"\0*\0version\";s:6:\"1.43.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\polyfill-php84\";}s:22:\"symfony/polyfill-php85\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"symfony/polyfill-php85\";s:10:\"\0*\0version\";s:6:\"1.43.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\polyfill-php85\";}s:22:\"symfony/polyfill-php86\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"symfony/polyfill-php86\";s:10:\"\0*\0version\";s:6:\"1.43.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\polyfill-php86\";}s:21:\"symfony/polyfill-uuid\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"symfony/polyfill-uuid\";s:10:\"\0*\0version\";s:6:\"1.43.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:49:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\polyfill-uuid\";}s:15:\"symfony/process\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"symfony/process\";s:10:\"\0*\0version\";s:6:\"7.4.22\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\process\";}s:15:\"symfony/routing\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"symfony/routing\";s:10:\"\0*\0version\";s:6:\"7.4.23\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\routing\";}s:25:\"symfony/service-contracts\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"symfony/service-contracts\";s:10:\"\0*\0version\";s:5:\"3.8.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\service-contracts\";}s:14:\"symfony/string\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"symfony/string\";s:10:\"\0*\0version\";s:6:\"7.4.22\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:42:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\string\";}s:19:\"symfony/translation\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"symfony/translation\";s:10:\"\0*\0version\";s:6:\"7.4.23\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\translation\";}s:29:\"symfony/translation-contracts\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:29:\"symfony/translation-contracts\";s:10:\"\0*\0version\";s:5:\"3.8.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:57:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\translation-contracts\";}s:11:\"symfony/uid\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"symfony/uid\";s:10:\"\0*\0version\";s:6:\"7.4.21\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:39:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\uid\";}s:18:\"symfony/var-dumper\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"symfony/var-dumper\";s:10:\"\0*\0version\";s:6:\"7.4.22\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\var-dumper\";}s:33:\"tijsverkoyen/css-to-inline-styles\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:33:\"tijsverkoyen/css-to-inline-styles\";s:10:\"\0*\0version\";s:5:\"2.4.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:61:\"C:\\wamp64\\www\\etache\\vendor\\tijsverkoyen\\css-to-inline-styles\";}s:16:\"vlucas/phpdotenv\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"vlucas/phpdotenv\";s:10:\"\0*\0version\";s:5:\"5.7.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\wamp64\\www\\etache\\vendor\\vlucas\\phpdotenv\";}s:19:\"voku/portable-ascii\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"voku/portable-ascii\";s:10:\"\0*\0version\";s:5:\"2.1.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"C:\\wamp64\\www\\etache\\vendor\\voku\\portable-ascii\";}s:15:\"composer/semver\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"composer/semver\";s:10:\"\0*\0version\";s:5:\"3.5.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"C:\\wamp64\\www\\etache\\vendor\\composer\\semver\";}s:14:\"fakerphp/faker\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"fakerphp/faker\";s:10:\"\0*\0version\";s:6:\"1.24.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:5:\"^1.23\";s:7:\"\0*\0path\";s:42:\"C:\\wamp64\\www\\etache\\vendor\\fakerphp\\faker\";}s:11:\"filp/whoops\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"filp/whoops\";s:10:\"\0*\0version\";s:6:\"2.18.5\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:39:\"C:\\wamp64\\www\\etache\\vendor\\filp\\whoops\";}s:21:\"hamcrest/hamcrest-php\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"hamcrest/hamcrest-php\";s:10:\"\0*\0version\";s:5:\"3.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:49:\"C:\\wamp64\\www\\etache\\vendor\\hamcrest\\hamcrest-php\";}s:22:\"laravel/agent-detector\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"laravel/agent-detector\";s:10:\"\0*\0version\";s:5:\"2.0.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"C:\\wamp64\\www\\etache\\vendor\\laravel\\agent-detector\";}s:13:\"laravel/boost\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"laravel/boost\";s:10:\"\0*\0version\";s:6:\"2.10.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:5:\"^2.10\";s:7:\"\0*\0path\";s:41:\"C:\\wamp64\\www\\etache\\vendor\\laravel\\boost\";}s:14:\"laravel/breeze\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"laravel/breeze\";s:10:\"\0*\0version\";s:5:\"2.4.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:1:\"*\";s:7:\"\0*\0path\";s:42:\"C:\\wamp64\\www\\etache\\vendor\\laravel\\breeze\";}s:11:\"laravel/mcp\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"laravel/mcp\";s:10:\"\0*\0version\";s:5:\"1.0.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:39:\"C:\\wamp64\\www\\etache\\vendor\\laravel\\mcp\";}s:12:\"laravel/pail\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"laravel/pail\";s:10:\"\0*\0version\";s:5:\"1.2.7\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^1.2.5\";s:7:\"\0*\0path\";s:40:\"C:\\wamp64\\www\\etache\\vendor\\laravel\\pail\";}s:11:\"laravel/pao\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"laravel/pao\";s:10:\"\0*\0version\";s:5:\"1.1.5\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^1.0.6\";s:7:\"\0*\0path\";s:39:\"C:\\wamp64\\www\\etache\\vendor\\laravel\\pao\";}s:12:\"laravel/pint\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"laravel/pint\";s:10:\"\0*\0version\";s:6:\"1.32.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:5:\"^1.27\";s:7:\"\0*\0path\";s:40:\"C:\\wamp64\\www\\etache\\vendor\\laravel\\pint\";}s:14:\"laravel/roster\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"laravel/roster\";s:10:\"\0*\0version\";s:5:\"1.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:42:\"C:\\wamp64\\www\\etache\\vendor\\laravel\\roster\";}s:15:\"mockery/mockery\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"mockery/mockery\";s:10:\"\0*\0version\";s:6:\"1.6.15\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:4:\"^1.6\";s:7:\"\0*\0path\";s:43:\"C:\\wamp64\\www\\etache\\vendor\\mockery\\mockery\";}s:17:\"myclabs/deep-copy\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"myclabs/deep-copy\";s:10:\"\0*\0version\";s:6:\"1.14.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\vendor\\myclabs\\deep-copy\";}s:20:\"nunomaduro/collision\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"nunomaduro/collision\";s:10:\"\0*\0version\";s:5:\"8.9.5\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:4:\"^8.6\";s:7:\"\0*\0path\";s:48:\"C:\\wamp64\\www\\etache\\vendor\\nunomaduro\\collision\";}s:16:\"phar-io/manifest\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"phar-io/manifest\";s:10:\"\0*\0version\";s:5:\"2.0.4\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\wamp64\\www\\etache\\vendor\\phar-io\\manifest\";}s:15:\"phar-io/version\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"phar-io/version\";s:10:\"\0*\0version\";s:5:\"3.2.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"C:\\wamp64\\www\\etache\\vendor\\phar-io\\version\";}s:25:\"phpunit/php-code-coverage\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"phpunit/php-code-coverage\";s:10:\"\0*\0version\";s:6:\"12.5.7\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"C:\\wamp64\\www\\etache\\vendor\\phpunit\\php-code-coverage\";}s:25:\"phpunit/php-file-iterator\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"phpunit/php-file-iterator\";s:10:\"\0*\0version\";s:5:\"6.0.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"C:\\wamp64\\www\\etache\\vendor\\phpunit\\php-file-iterator\";}s:19:\"phpunit/php-invoker\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"phpunit/php-invoker\";s:10:\"\0*\0version\";s:5:\"6.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"C:\\wamp64\\www\\etache\\vendor\\phpunit\\php-invoker\";}s:25:\"phpunit/php-text-template\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"phpunit/php-text-template\";s:10:\"\0*\0version\";s:5:\"5.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"C:\\wamp64\\www\\etache\\vendor\\phpunit\\php-text-template\";}s:17:\"phpunit/php-timer\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"phpunit/php-timer\";s:10:\"\0*\0version\";s:5:\"8.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\vendor\\phpunit\\php-timer\";}s:15:\"phpunit/phpunit\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"phpunit/phpunit\";s:10:\"\0*\0version\";s:7:\"12.5.35\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:8:\"^12.5.12\";s:7:\"\0*\0path\";s:43:\"C:\\wamp64\\www\\etache\\vendor\\phpunit\\phpunit\";}s:20:\"sebastian/cli-parser\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"sebastian/cli-parser\";s:10:\"\0*\0version\";s:5:\"4.2.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"C:\\wamp64\\www\\etache\\vendor\\sebastian\\cli-parser\";}s:20:\"sebastian/comparator\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"sebastian/comparator\";s:10:\"\0*\0version\";s:5:\"7.1.8\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"C:\\wamp64\\www\\etache\\vendor\\sebastian\\comparator\";}s:20:\"sebastian/complexity\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"sebastian/complexity\";s:10:\"\0*\0version\";s:5:\"5.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"C:\\wamp64\\www\\etache\\vendor\\sebastian\\complexity\";}s:14:\"sebastian/diff\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"sebastian/diff\";s:10:\"\0*\0version\";s:5:\"7.0.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:42:\"C:\\wamp64\\www\\etache\\vendor\\sebastian\\diff\";}s:21:\"sebastian/environment\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"sebastian/environment\";s:10:\"\0*\0version\";s:5:\"8.1.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:49:\"C:\\wamp64\\www\\etache\\vendor\\sebastian\\environment\";}s:18:\"sebastian/exporter\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"sebastian/exporter\";s:10:\"\0*\0version\";s:5:\"7.0.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"C:\\wamp64\\www\\etache\\vendor\\sebastian\\exporter\";}s:22:\"sebastian/global-state\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"sebastian/global-state\";s:10:\"\0*\0version\";s:5:\"8.0.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"C:\\wamp64\\www\\etache\\vendor\\sebastian\\global-state\";}s:23:\"sebastian/lines-of-code\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"sebastian/lines-of-code\";s:10:\"\0*\0version\";s:5:\"4.0.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"C:\\wamp64\\www\\etache\\vendor\\sebastian\\lines-of-code\";}s:27:\"sebastian/object-enumerator\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:27:\"sebastian/object-enumerator\";s:10:\"\0*\0version\";s:5:\"7.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:55:\"C:\\wamp64\\www\\etache\\vendor\\sebastian\\object-enumerator\";}s:26:\"sebastian/object-reflector\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:26:\"sebastian/object-reflector\";s:10:\"\0*\0version\";s:5:\"5.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:54:\"C:\\wamp64\\www\\etache\\vendor\\sebastian\\object-reflector\";}s:27:\"sebastian/recursion-context\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:27:\"sebastian/recursion-context\";s:10:\"\0*\0version\";s:5:\"7.0.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:55:\"C:\\wamp64\\www\\etache\\vendor\\sebastian\\recursion-context\";}s:14:\"sebastian/type\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"sebastian/type\";s:10:\"\0*\0version\";s:5:\"6.0.4\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:42:\"C:\\wamp64\\www\\etache\\vendor\\sebastian\\type\";}s:17:\"sebastian/version\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"sebastian/version\";s:10:\"\0*\0version\";s:5:\"6.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\vendor\\sebastian\\version\";}s:28:\"staabm/side-effects-detector\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:28:\"staabm/side-effects-detector\";s:10:\"\0*\0version\";s:5:\"1.0.5\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:56:\"C:\\wamp64\\www\\etache\\vendor\\staabm\\side-effects-detector\";}s:12:\"symfony/yaml\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"symfony/yaml\";s:10:\"\0*\0version\";s:6:\"7.4.23\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:40:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\yaml\";}s:17:\"theseer/tokenizer\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"theseer/tokenizer\";s:10:\"\0*\0version\";s:5:\"2.0.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\vendor\\theseer\\tokenizer\";}}s:11:\"\0*\0packages\";O:32:\"Laravel\\Roster\\PackageCollection\":2:{s:8:\"\0*\0items\";a:127:{i:0;r:5;i:1;r:13;i:2;r:21;i:3;r:29;i:4;r:37;i:5;r:45;i:6;r:53;i:7;r:61;i:8;r:69;i:9;r:77;i:10;r:85;i:11;r:93;i:12;r:101;i:13;r:109;i:14;r:117;i:15;r:125;i:16;r:133;i:17;r:141;i:18;r:149;i:19;r:157;i:20;r:165;i:21;r:173;i:22;r:181;i:23;r:189;i:24;r:197;i:25;r:205;i:26;r:213;i:27;r:221;i:28;r:229;i:29;r:237;i:30;r:245;i:31;r:253;i:32;r:261;i:33;r:269;i:34;r:277;i:35;r:285;i:36;r:293;i:37;r:301;i:38;r:309;i:39;r:317;i:40;r:325;i:41;r:333;i:42;r:341;i:43;r:349;i:44;r:357;i:45;r:365;i:46;r:373;i:47;r:381;i:48;r:389;i:49;r:397;i:50;r:405;i:51;r:413;i:52;r:421;i:53;r:429;i:54;r:437;i:55;r:445;i:56;r:453;i:57;r:461;i:58;r:469;i:59;r:477;i:60;r:485;i:61;r:493;i:62;r:501;i:63;r:509;i:64;r:517;i:65;r:525;i:66;r:533;i:67;r:541;i:68;r:549;i:69;r:557;i:70;r:565;i:71;r:573;i:72;r:581;i:73;r:589;i:74;r:597;i:75;r:605;i:76;r:613;i:77;r:621;i:78;r:629;i:79;r:637;i:80;r:645;i:81;r:653;i:82;r:661;i:83;r:669;i:84;r:677;i:85;r:685;i:86;r:693;i:87;r:701;i:88;r:709;i:89;r:717;i:90;r:725;i:91;r:733;i:92;r:741;i:93;r:749;i:94;r:757;i:95;r:765;i:96;r:773;i:97;r:781;i:98;r:789;i:99;r:797;i:100;r:805;i:101;r:813;i:102;r:821;i:103;r:829;i:104;r:837;i:105;r:845;i:106;r:853;i:107;r:861;i:108;r:869;i:109;r:877;i:110;r:885;i:111;r:893;i:112;r:901;i:113;r:909;i:114;r:917;i:115;r:925;i:116;r:933;i:117;r:941;i:118;r:949;i:119;r:957;i:120;r:965;i:121;r:973;i:122;r:981;i:123;r:989;i:124;r:997;i:125;r:1005;i:126;r:1013;}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}s:2:\"js\";O:37:\"Laravel\\Roster\\Ecosystems\\JsEcosystem\":3:{s:9:\"\0*\0byName\";a:189:{s:24:\"@alcalzone/ansi-tokenize\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:24:\"@alcalzone/ansi-tokenize\";s:10:\"\0*\0version\";s:5:\"0.3.1\";s:9:\"\0*\0source\";E:38:\"Laravel\\Roster\\Enums\\PackageSource:Npm\";s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:58:\"C:\\wamp64\\www\\etache\\node_modules\\@alcalzone\\ansi-tokenize\";}s:18:\"@laravel/multiplex\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"@laravel/multiplex\";s:10:\"\0*\0version\";s:5:\"0.4.4\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^0.4.1\";s:7:\"\0*\0path\";s:52:\"C:\\wamp64\\www\\etache\\node_modules\\@laravel\\multiplex\";}s:12:\"ansi-escapes\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"ansi-escapes\";s:10:\"\0*\0version\";s:5:\"7.3.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"C:\\wamp64\\www\\etache\\node_modules\\ansi-escapes\";}s:10:\"ansi-regex\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"ansi-regex\";s:10:\"\0*\0version\";s:5:\"6.3.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\wamp64\\www\\etache\\node_modules\\ansi-regex\";}s:11:\"ansi-styles\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"ansi-styles\";s:10:\"\0*\0version\";s:5:\"7.0.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\node_modules\\ansi-styles\";}s:9:\"auto-bind\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"auto-bind\";s:10:\"\0*\0version\";s:5:\"5.0.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"C:\\wamp64\\www\\etache\\node_modules\\auto-bind\";}s:5:\"chalk\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:5:\"chalk\";s:10:\"\0*\0version\";s:5:\"5.6.2\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:39:\"C:\\wamp64\\www\\etache\\node_modules\\chalk\";}s:9:\"cli-boxes\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"cli-boxes\";s:10:\"\0*\0version\";s:5:\"4.0.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"C:\\wamp64\\www\\etache\\node_modules\\cli-boxes\";}s:10:\"cli-cursor\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"cli-cursor\";s:10:\"\0*\0version\";s:5:\"4.0.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\wamp64\\www\\etache\\node_modules\\cli-cursor\";}s:12:\"cli-truncate\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"cli-truncate\";s:10:\"\0*\0version\";s:5:\"6.1.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"C:\\wamp64\\www\\etache\\node_modules\\cli-truncate\";}s:12:\"code-excerpt\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"code-excerpt\";s:10:\"\0*\0version\";s:5:\"4.0.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"C:\\wamp64\\www\\etache\\node_modules\\code-excerpt\";}s:9:\"commander\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"commander\";s:10:\"\0*\0version\";s:6:\"15.0.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"C:\\wamp64\\www\\etache\\node_modules\\commander\";}s:17:\"convert-to-spaces\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"convert-to-spaces\";s:10:\"\0*\0version\";s:5:\"2.0.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"C:\\wamp64\\www\\etache\\node_modules\\convert-to-spaces\";}s:11:\"environment\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"environment\";s:10:\"\0*\0version\";s:5:\"1.1.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\node_modules\\environment\";}s:10:\"es-toolkit\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"es-toolkit\";s:10:\"\0*\0version\";s:6:\"1.52.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\wamp64\\www\\etache\\node_modules\\es-toolkit\";}s:20:\"escape-string-regexp\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"escape-string-regexp\";s:10:\"\0*\0version\";s:5:\"2.0.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:54:\"C:\\wamp64\\www\\etache\\node_modules\\escape-string-regexp\";}s:20:\"get-east-asian-width\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"get-east-asian-width\";s:10:\"\0*\0version\";s:5:\"1.7.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:54:\"C:\\wamp64\\www\\etache\\node_modules\\get-east-asian-width\";}s:13:\"indent-string\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"indent-string\";s:10:\"\0*\0version\";s:5:\"5.0.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"C:\\wamp64\\www\\etache\\node_modules\\indent-string\";}s:3:\"ink\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:3:\"ink\";s:10:\"\0*\0version\";s:5:\"7.1.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:37:\"C:\\wamp64\\www\\etache\\node_modules\\ink\";}s:23:\"is-fullwidth-code-point\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"is-fullwidth-code-point\";s:10:\"\0*\0version\";s:5:\"5.1.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:57:\"C:\\wamp64\\www\\etache\\node_modules\\is-fullwidth-code-point\";}s:8:\"is-in-ci\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"is-in-ci\";s:10:\"\0*\0version\";s:5:\"2.0.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:42:\"C:\\wamp64\\www\\etache\\node_modules\\is-in-ci\";}s:8:\"mimic-fn\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"mimic-fn\";s:10:\"\0*\0version\";s:5:\"2.1.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:42:\"C:\\wamp64\\www\\etache\\node_modules\\mimic-fn\";}s:7:\"onetime\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:7:\"onetime\";s:10:\"\0*\0version\";s:5:\"5.1.2\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:41:\"C:\\wamp64\\www\\etache\\node_modules\\onetime\";}s:13:\"patch-console\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"patch-console\";s:10:\"\0*\0version\";s:5:\"2.0.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"C:\\wamp64\\www\\etache\\node_modules\\patch-console\";}s:5:\"react\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:5:\"react\";s:10:\"\0*\0version\";s:6:\"19.3.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:39:\"C:\\wamp64\\www\\etache\\node_modules\\react\";}s:16:\"react-reconciler\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"react-reconciler\";s:10:\"\0*\0version\";s:6:\"0.33.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"C:\\wamp64\\www\\etache\\node_modules\\react-reconciler\";}s:14:\"restore-cursor\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"restore-cursor\";s:10:\"\0*\0version\";s:5:\"4.0.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"C:\\wamp64\\www\\etache\\node_modules\\restore-cursor\";}s:9:\"scheduler\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"scheduler\";s:10:\"\0*\0version\";s:6:\"0.27.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"C:\\wamp64\\www\\etache\\node_modules\\scheduler\";}s:11:\"signal-exit\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"signal-exit\";s:10:\"\0*\0version\";s:5:\"3.0.7\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\node_modules\\signal-exit\";}s:10:\"slice-ansi\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"slice-ansi\";s:10:\"\0*\0version\";s:5:\"9.0.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\wamp64\\www\\etache\\node_modules\\slice-ansi\";}s:11:\"stack-utils\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"stack-utils\";s:10:\"\0*\0version\";s:5:\"2.0.6\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\node_modules\\stack-utils\";}s:12:\"string-width\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"string-width\";s:10:\"\0*\0version\";s:5:\"8.3.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"C:\\wamp64\\www\\etache\\node_modules\\string-width\";}s:10:\"strip-ansi\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"strip-ansi\";s:10:\"\0*\0version\";s:5:\"7.2.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\wamp64\\www\\etache\\node_modules\\strip-ansi\";}s:10:\"tagged-tag\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"tagged-tag\";s:10:\"\0*\0version\";s:5:\"1.0.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\wamp64\\www\\etache\\node_modules\\tagged-tag\";}s:13:\"terminal-size\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"terminal-size\";s:10:\"\0*\0version\";s:5:\"4.0.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"C:\\wamp64\\www\\etache\\node_modules\\terminal-size\";}s:9:\"type-fest\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"type-fest\";s:10:\"\0*\0version\";s:6:\"5.10.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"C:\\wamp64\\www\\etache\\node_modules\\type-fest\";}s:11:\"widest-line\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"widest-line\";s:10:\"\0*\0version\";s:5:\"6.0.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\node_modules\\widest-line\";}s:9:\"wrap-ansi\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"wrap-ansi\";s:10:\"\0*\0version\";s:6:\"10.0.2\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"C:\\wamp64\\www\\etache\\node_modules\\wrap-ansi\";}s:2:\"ws\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:2:\"ws\";s:10:\"\0*\0version\";s:6:\"8.21.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:36:\"C:\\wamp64\\www\\etache\\node_modules\\ws\";}s:11:\"yoga-layout\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"yoga-layout\";s:10:\"\0*\0version\";s:5:\"3.2.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\node_modules\\yoga-layout\";}s:16:\"@alloc/quick-lru\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"@alloc/quick-lru\";s:10:\"\0*\0version\";s:5:\"5.3.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"C:\\wamp64\\www\\etache\\node_modules\\@alloc\\quick-lru\";}s:23:\"@jridgewell/gen-mapping\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"@jridgewell/gen-mapping\";s:10:\"\0*\0version\";s:6:\"0.3.13\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:57:\"C:\\wamp64\\www\\etache\\node_modules\\@jridgewell\\gen-mapping\";}s:21:\"@jridgewell/remapping\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"@jridgewell/remapping\";s:10:\"\0*\0version\";s:5:\"2.3.5\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:55:\"C:\\wamp64\\www\\etache\\node_modules\\@jridgewell\\remapping\";}s:23:\"@jridgewell/resolve-uri\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"@jridgewell/resolve-uri\";s:10:\"\0*\0version\";s:5:\"3.1.2\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:57:\"C:\\wamp64\\www\\etache\\node_modules\\@jridgewell\\resolve-uri\";}s:27:\"@jridgewell/sourcemap-codec\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:27:\"@jridgewell/sourcemap-codec\";s:10:\"\0*\0version\";s:5:\"1.6.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:61:\"C:\\wamp64\\www\\etache\\node_modules\\@jridgewell\\sourcemap-codec\";}s:25:\"@jridgewell/trace-mapping\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"@jridgewell/trace-mapping\";s:10:\"\0*\0version\";s:6:\"0.3.31\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:59:\"C:\\wamp64\\www\\etache\\node_modules\\@jridgewell\\trace-mapping\";}s:19:\"@nodelib/fs.scandir\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"@nodelib/fs.scandir\";s:10:\"\0*\0version\";s:5:\"2.1.5\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"C:\\wamp64\\www\\etache\\node_modules\\@nodelib\\fs.scandir\";}s:16:\"@nodelib/fs.stat\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"@nodelib/fs.stat\";s:10:\"\0*\0version\";s:5:\"2.0.5\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"C:\\wamp64\\www\\etache\\node_modules\\@nodelib\\fs.stat\";}s:16:\"@nodelib/fs.walk\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"@nodelib/fs.walk\";s:10:\"\0*\0version\";s:5:\"1.2.8\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"C:\\wamp64\\www\\etache\\node_modules\\@nodelib\\fs.walk\";}s:18:\"@oxc-project/types\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"@oxc-project/types\";s:10:\"\0*\0version\";s:7:\"0.151.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"C:\\wamp64\\www\\etache\\node_modules\\@oxc-project\\types\";}s:34:\"@rolldown/binding-android-arm-eabi\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:34:\"@rolldown/binding-android-arm-eabi\";s:10:\"\0*\0version\";s:6:\"1.2.11\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:68:\"C:\\wamp64\\www\\etache\\node_modules\\@rolldown\\binding-android-arm-eabi\";}s:31:\"@rolldown/binding-android-arm64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:31:\"@rolldown/binding-android-arm64\";s:10:\"\0*\0version\";s:6:\"1.2.11\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:65:\"C:\\wamp64\\www\\etache\\node_modules\\@rolldown\\binding-android-arm64\";}s:30:\"@rolldown/binding-darwin-arm64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:30:\"@rolldown/binding-darwin-arm64\";s:10:\"\0*\0version\";s:6:\"1.2.11\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:64:\"C:\\wamp64\\www\\etache\\node_modules\\@rolldown\\binding-darwin-arm64\";}s:28:\"@rolldown/binding-darwin-x64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:28:\"@rolldown/binding-darwin-x64\";s:10:\"\0*\0version\";s:6:\"1.2.11\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:62:\"C:\\wamp64\\www\\etache\\node_modules\\@rolldown\\binding-darwin-x64\";}s:29:\"@rolldown/binding-freebsd-x64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:29:\"@rolldown/binding-freebsd-x64\";s:10:\"\0*\0version\";s:6:\"1.2.11\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:63:\"C:\\wamp64\\www\\etache\\node_modules\\@rolldown\\binding-freebsd-x64\";}s:37:\"@rolldown/binding-linux-arm-gnueabihf\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:37:\"@rolldown/binding-linux-arm-gnueabihf\";s:10:\"\0*\0version\";s:6:\"1.2.11\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:71:\"C:\\wamp64\\www\\etache\\node_modules\\@rolldown\\binding-linux-arm-gnueabihf\";}s:33:\"@rolldown/binding-linux-arm64-gnu\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:33:\"@rolldown/binding-linux-arm64-gnu\";s:10:\"\0*\0version\";s:6:\"1.2.11\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:67:\"C:\\wamp64\\www\\etache\\node_modules\\@rolldown\\binding-linux-arm64-gnu\";}s:34:\"@rolldown/binding-linux-arm64-musl\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:34:\"@rolldown/binding-linux-arm64-musl\";s:10:\"\0*\0version\";s:6:\"1.2.11\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:68:\"C:\\wamp64\\www\\etache\\node_modules\\@rolldown\\binding-linux-arm64-musl\";}s:33:\"@rolldown/binding-linux-ppc64-gnu\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:33:\"@rolldown/binding-linux-ppc64-gnu\";s:10:\"\0*\0version\";s:6:\"1.2.11\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:67:\"C:\\wamp64\\www\\etache\\node_modules\\@rolldown\\binding-linux-ppc64-gnu\";}s:33:\"@rolldown/binding-linux-s390x-gnu\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:33:\"@rolldown/binding-linux-s390x-gnu\";s:10:\"\0*\0version\";s:6:\"1.2.11\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:67:\"C:\\wamp64\\www\\etache\\node_modules\\@rolldown\\binding-linux-s390x-gnu\";}s:31:\"@rolldown/binding-linux-x64-gnu\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:31:\"@rolldown/binding-linux-x64-gnu\";s:10:\"\0*\0version\";s:6:\"1.2.11\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:65:\"C:\\wamp64\\www\\etache\\node_modules\\@rolldown\\binding-linux-x64-gnu\";}s:32:\"@rolldown/binding-linux-x64-musl\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:32:\"@rolldown/binding-linux-x64-musl\";s:10:\"\0*\0version\";s:6:\"1.2.11\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:66:\"C:\\wamp64\\www\\etache\\node_modules\\@rolldown\\binding-linux-x64-musl\";}s:35:\"@rolldown/binding-openharmony-arm64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:35:\"@rolldown/binding-openharmony-arm64\";s:10:\"\0*\0version\";s:6:\"1.2.11\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:69:\"C:\\wamp64\\www\\etache\\node_modules\\@rolldown\\binding-openharmony-arm64\";}s:34:\"@rolldown/binding-win32-arm64-msvc\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:34:\"@rolldown/binding-win32-arm64-msvc\";s:10:\"\0*\0version\";s:6:\"1.2.11\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:68:\"C:\\wamp64\\www\\etache\\node_modules\\@rolldown\\binding-win32-arm64-msvc\";}s:32:\"@rolldown/binding-win32-x64-msvc\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:32:\"@rolldown/binding-win32-x64-msvc\";s:10:\"\0*\0version\";s:6:\"1.2.11\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:66:\"C:\\wamp64\\www\\etache\\node_modules\\@rolldown\\binding-win32-x64-msvc\";}s:21:\"@rolldown/pluginutils\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"@rolldown/pluginutils\";s:10:\"\0*\0version\";s:5:\"1.0.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:55:\"C:\\wamp64\\www\\etache\\node_modules\\@rolldown\\pluginutils\";}s:18:\"@tailwindcss/forms\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"@tailwindcss/forms\";s:10:\"\0*\0version\";s:6:\"0.5.11\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^0.5.2\";s:7:\"\0*\0path\";s:52:\"C:\\wamp64\\www\\etache\\node_modules\\@tailwindcss\\forms\";}s:17:\"@tailwindcss/node\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"@tailwindcss/node\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"C:\\wamp64\\www\\etache\\node_modules\\@tailwindcss\\node\";}s:18:\"@tailwindcss/oxide\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"@tailwindcss/oxide\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"C:\\wamp64\\www\\etache\\node_modules\\@tailwindcss\\oxide\";}s:32:\"@tailwindcss/oxide-android-arm64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:32:\"@tailwindcss/oxide-android-arm64\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:66:\"C:\\wamp64\\www\\etache\\node_modules\\@tailwindcss\\oxide-android-arm64\";}s:31:\"@tailwindcss/oxide-darwin-arm64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:31:\"@tailwindcss/oxide-darwin-arm64\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:65:\"C:\\wamp64\\www\\etache\\node_modules\\@tailwindcss\\oxide-darwin-arm64\";}s:29:\"@tailwindcss/oxide-darwin-x64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:29:\"@tailwindcss/oxide-darwin-x64\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:63:\"C:\\wamp64\\www\\etache\\node_modules\\@tailwindcss\\oxide-darwin-x64\";}s:30:\"@tailwindcss/oxide-freebsd-x64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:30:\"@tailwindcss/oxide-freebsd-x64\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:64:\"C:\\wamp64\\www\\etache\\node_modules\\@tailwindcss\\oxide-freebsd-x64\";}s:38:\"@tailwindcss/oxide-linux-arm-gnueabihf\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:38:\"@tailwindcss/oxide-linux-arm-gnueabihf\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:72:\"C:\\wamp64\\www\\etache\\node_modules\\@tailwindcss\\oxide-linux-arm-gnueabihf\";}s:34:\"@tailwindcss/oxide-linux-arm64-gnu\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:34:\"@tailwindcss/oxide-linux-arm64-gnu\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:68:\"C:\\wamp64\\www\\etache\\node_modules\\@tailwindcss\\oxide-linux-arm64-gnu\";}s:35:\"@tailwindcss/oxide-linux-arm64-musl\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:35:\"@tailwindcss/oxide-linux-arm64-musl\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:69:\"C:\\wamp64\\www\\etache\\node_modules\\@tailwindcss\\oxide-linux-arm64-musl\";}s:32:\"@tailwindcss/oxide-linux-x64-gnu\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:32:\"@tailwindcss/oxide-linux-x64-gnu\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:66:\"C:\\wamp64\\www\\etache\\node_modules\\@tailwindcss\\oxide-linux-x64-gnu\";}s:33:\"@tailwindcss/oxide-linux-x64-musl\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:33:\"@tailwindcss/oxide-linux-x64-musl\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:67:\"C:\\wamp64\\www\\etache\\node_modules\\@tailwindcss\\oxide-linux-x64-musl\";}s:30:\"@tailwindcss/oxide-wasm32-wasi\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:30:\"@tailwindcss/oxide-wasm32-wasi\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:64:\"C:\\wamp64\\www\\etache\\node_modules\\@tailwindcss\\oxide-wasm32-wasi\";}s:35:\"@tailwindcss/oxide-win32-arm64-msvc\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:35:\"@tailwindcss/oxide-win32-arm64-msvc\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:69:\"C:\\wamp64\\www\\etache\\node_modules\\@tailwindcss\\oxide-win32-arm64-msvc\";}s:33:\"@tailwindcss/oxide-win32-x64-msvc\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:33:\"@tailwindcss/oxide-win32-x64-msvc\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:67:\"C:\\wamp64\\www\\etache\\node_modules\\@tailwindcss\\oxide-win32-x64-msvc\";}s:17:\"@tailwindcss/vite\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"@tailwindcss/vite\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^4.0.0\";s:7:\"\0*\0path\";s:51:\"C:\\wamp64\\www\\etache\\node_modules\\@tailwindcss\\vite\";}s:15:\"@vue/reactivity\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"@vue/reactivity\";s:10:\"\0*\0version\";s:6:\"3.5.43\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:49:\"C:\\wamp64\\www\\etache\\node_modules\\@vue\\reactivity\";}s:11:\"@vue/shared\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"@vue/shared\";s:10:\"\0*\0version\";s:6:\"3.5.43\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\node_modules\\@vue\\shared\";}s:8:\"alpinejs\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"alpinejs\";s:10:\"\0*\0version\";s:6:\"3.17.4\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^3.4.2\";s:7:\"\0*\0path\";s:42:\"C:\\wamp64\\www\\etache\\node_modules\\alpinejs\";}s:11:\"any-promise\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"any-promise\";s:10:\"\0*\0version\";s:5:\"1.3.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\node_modules\\any-promise\";}s:8:\"anymatch\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"anymatch\";s:10:\"\0*\0version\";s:5:\"3.1.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:42:\"C:\\wamp64\\www\\etache\\node_modules\\anymatch\";}s:3:\"arg\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:3:\"arg\";s:10:\"\0*\0version\";s:5:\"5.0.2\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:37:\"C:\\wamp64\\www\\etache\\node_modules\\arg\";}s:12:\"autoprefixer\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"autoprefixer\";s:10:\"\0*\0version\";s:6:\"10.6.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:7:\"^10.4.2\";s:7:\"\0*\0path\";s:46:\"C:\\wamp64\\www\\etache\\node_modules\\autoprefixer\";}s:24:\"baseline-browser-mapping\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:24:\"baseline-browser-mapping\";s:10:\"\0*\0version\";s:7:\"2.11.26\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:58:\"C:\\wamp64\\www\\etache\\node_modules\\baseline-browser-mapping\";}s:17:\"binary-extensions\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"binary-extensions\";s:10:\"\0*\0version\";s:5:\"2.3.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"C:\\wamp64\\www\\etache\\node_modules\\binary-extensions\";}s:6:\"braces\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:6:\"braces\";s:10:\"\0*\0version\";s:5:\"3.0.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:40:\"C:\\wamp64\\www\\etache\\node_modules\\braces\";}s:12:\"browserslist\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"browserslist\";s:10:\"\0*\0version\";s:6:\"4.29.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"C:\\wamp64\\www\\etache\\node_modules\\browserslist\";}s:13:\"camelcase-css\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"camelcase-css\";s:10:\"\0*\0version\";s:5:\"2.0.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"C:\\wamp64\\www\\etache\\node_modules\\camelcase-css\";}s:12:\"caniuse-lite\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"caniuse-lite\";s:10:\"\0*\0version\";s:12:\"1.0.30001812\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"C:\\wamp64\\www\\etache\\node_modules\\caniuse-lite\";}s:8:\"chokidar\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"chokidar\";s:10:\"\0*\0version\";s:5:\"3.6.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:42:\"C:\\wamp64\\www\\etache\\node_modules\\chokidar\";}s:5:\"cliui\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:5:\"cliui\";s:10:\"\0*\0version\";s:5:\"9.0.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:39:\"C:\\wamp64\\www\\etache\\node_modules\\cliui\";}s:12:\"concurrently\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"concurrently\";s:10:\"\0*\0version\";s:6:\"10.0.5\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:7:\"^10.0.3\";s:7:\"\0*\0path\";s:46:\"C:\\wamp64\\www\\etache\\node_modules\\concurrently\";}s:6:\"cssesc\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:6:\"cssesc\";s:10:\"\0*\0version\";s:5:\"3.0.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:40:\"C:\\wamp64\\www\\etache\\node_modules\\cssesc\";}s:11:\"detect-libc\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"detect-libc\";s:10:\"\0*\0version\";s:5:\"2.1.2\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\node_modules\\detect-libc\";}s:10:\"didyoumean\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"didyoumean\";s:10:\"\0*\0version\";s:5:\"1.2.2\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\wamp64\\www\\etache\\node_modules\\didyoumean\";}s:3:\"dlv\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:3:\"dlv\";s:10:\"\0*\0version\";s:5:\"1.1.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:37:\"C:\\wamp64\\www\\etache\\node_modules\\dlv\";}s:20:\"electron-to-chromium\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"electron-to-chromium\";s:10:\"\0*\0version\";s:7:\"1.5.439\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:54:\"C:\\wamp64\\www\\etache\\node_modules\\electron-to-chromium\";}s:11:\"emoji-regex\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"emoji-regex\";s:10:\"\0*\0version\";s:6:\"10.6.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\node_modules\\emoji-regex\";}s:16:\"enhanced-resolve\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"enhanced-resolve\";s:10:\"\0*\0version\";s:6:\"5.25.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"C:\\wamp64\\www\\etache\\node_modules\\enhanced-resolve\";}s:9:\"es-errors\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"es-errors\";s:10:\"\0*\0version\";s:5:\"1.3.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"C:\\wamp64\\www\\etache\\node_modules\\es-errors\";}s:8:\"escalade\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"escalade\";s:10:\"\0*\0version\";s:5:\"3.2.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:42:\"C:\\wamp64\\www\\etache\\node_modules\\escalade\";}s:9:\"fast-glob\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"fast-glob\";s:10:\"\0*\0version\";s:5:\"3.3.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"C:\\wamp64\\www\\etache\\node_modules\\fast-glob\";}s:5:\"fastq\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:5:\"fastq\";s:10:\"\0*\0version\";s:6:\"1.20.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:39:\"C:\\wamp64\\www\\etache\\node_modules\\fastq\";}s:10:\"fill-range\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"fill-range\";s:10:\"\0*\0version\";s:5:\"7.1.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\wamp64\\www\\etache\\node_modules\\fill-range\";}s:11:\"fraction.js\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"fraction.js\";s:10:\"\0*\0version\";s:5:\"5.3.4\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\node_modules\\fraction.js\";}s:8:\"fsevents\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"fsevents\";s:10:\"\0*\0version\";s:5:\"2.3.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:42:\"C:\\wamp64\\www\\etache\\node_modules\\fsevents\";}s:13:\"function-bind\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"function-bind\";s:10:\"\0*\0version\";s:5:\"1.1.2\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"C:\\wamp64\\www\\etache\\node_modules\\function-bind\";}s:15:\"get-caller-file\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"get-caller-file\";s:10:\"\0*\0version\";s:5:\"2.0.5\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:49:\"C:\\wamp64\\www\\etache\\node_modules\\get-caller-file\";}s:11:\"glob-parent\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"glob-parent\";s:10:\"\0*\0version\";s:5:\"6.0.2\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\node_modules\\glob-parent\";}s:11:\"graceful-fs\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"graceful-fs\";s:10:\"\0*\0version\";s:6:\"4.2.11\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\node_modules\\graceful-fs\";}s:6:\"hasown\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:6:\"hasown\";s:10:\"\0*\0version\";s:5:\"2.0.4\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:40:\"C:\\wamp64\\www\\etache\\node_modules\\hasown\";}s:14:\"is-binary-path\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"is-binary-path\";s:10:\"\0*\0version\";s:5:\"2.1.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"C:\\wamp64\\www\\etache\\node_modules\\is-binary-path\";}s:14:\"is-core-module\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"is-core-module\";s:10:\"\0*\0version\";s:6:\"2.17.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"C:\\wamp64\\www\\etache\\node_modules\\is-core-module\";}s:10:\"is-extglob\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"is-extglob\";s:10:\"\0*\0version\";s:5:\"2.1.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\wamp64\\www\\etache\\node_modules\\is-extglob\";}s:7:\"is-glob\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:7:\"is-glob\";s:10:\"\0*\0version\";s:5:\"4.0.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:41:\"C:\\wamp64\\www\\etache\\node_modules\\is-glob\";}s:9:\"is-number\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"is-number\";s:10:\"\0*\0version\";s:5:\"7.0.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"C:\\wamp64\\www\\etache\\node_modules\\is-number\";}s:4:\"jiti\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:4:\"jiti\";s:10:\"\0*\0version\";s:5:\"2.7.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:38:\"C:\\wamp64\\www\\etache\\node_modules\\jiti\";}s:19:\"laravel-vite-plugin\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"laravel-vite-plugin\";s:10:\"\0*\0version\";s:5:\"3.2.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:4:\"^3.1\";s:7:\"\0*\0path\";s:53:\"C:\\wamp64\\www\\etache\\node_modules\\laravel-vite-plugin\";}s:12:\"lightningcss\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"lightningcss\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"C:\\wamp64\\www\\etache\\node_modules\\lightningcss\";}s:26:\"lightningcss-android-arm64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:26:\"lightningcss-android-arm64\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:60:\"C:\\wamp64\\www\\etache\\node_modules\\lightningcss-android-arm64\";}s:25:\"lightningcss-darwin-arm64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"lightningcss-darwin-arm64\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:59:\"C:\\wamp64\\www\\etache\\node_modules\\lightningcss-darwin-arm64\";}s:23:\"lightningcss-darwin-x64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"lightningcss-darwin-x64\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:57:\"C:\\wamp64\\www\\etache\\node_modules\\lightningcss-darwin-x64\";}s:24:\"lightningcss-freebsd-x64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:24:\"lightningcss-freebsd-x64\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:58:\"C:\\wamp64\\www\\etache\\node_modules\\lightningcss-freebsd-x64\";}s:32:\"lightningcss-linux-arm-gnueabihf\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:32:\"lightningcss-linux-arm-gnueabihf\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:66:\"C:\\wamp64\\www\\etache\\node_modules\\lightningcss-linux-arm-gnueabihf\";}s:28:\"lightningcss-linux-arm64-gnu\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:28:\"lightningcss-linux-arm64-gnu\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:62:\"C:\\wamp64\\www\\etache\\node_modules\\lightningcss-linux-arm64-gnu\";}s:29:\"lightningcss-linux-arm64-musl\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:29:\"lightningcss-linux-arm64-musl\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:63:\"C:\\wamp64\\www\\etache\\node_modules\\lightningcss-linux-arm64-musl\";}s:26:\"lightningcss-linux-x64-gnu\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:26:\"lightningcss-linux-x64-gnu\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:60:\"C:\\wamp64\\www\\etache\\node_modules\\lightningcss-linux-x64-gnu\";}s:27:\"lightningcss-linux-x64-musl\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:27:\"lightningcss-linux-x64-musl\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:61:\"C:\\wamp64\\www\\etache\\node_modules\\lightningcss-linux-x64-musl\";}s:29:\"lightningcss-win32-arm64-msvc\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:29:\"lightningcss-win32-arm64-msvc\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:63:\"C:\\wamp64\\www\\etache\\node_modules\\lightningcss-win32-arm64-msvc\";}s:27:\"lightningcss-win32-x64-msvc\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:27:\"lightningcss-win32-x64-msvc\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:61:\"C:\\wamp64\\www\\etache\\node_modules\\lightningcss-win32-x64-msvc\";}s:9:\"lilconfig\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"lilconfig\";s:10:\"\0*\0version\";s:5:\"3.1.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"C:\\wamp64\\www\\etache\\node_modules\\lilconfig\";}s:17:\"lines-and-columns\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"lines-and-columns\";s:10:\"\0*\0version\";s:5:\"1.2.4\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"C:\\wamp64\\www\\etache\\node_modules\\lines-and-columns\";}s:12:\"magic-string\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"magic-string\";s:10:\"\0*\0version\";s:7:\"0.30.21\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"C:\\wamp64\\www\\etache\\node_modules\\magic-string\";}s:6:\"merge2\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:6:\"merge2\";s:10:\"\0*\0version\";s:5:\"1.4.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:40:\"C:\\wamp64\\www\\etache\\node_modules\\merge2\";}s:10:\"micromatch\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"micromatch\";s:10:\"\0*\0version\";s:5:\"4.0.8\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\wamp64\\www\\etache\\node_modules\\micromatch\";}s:17:\"mini-svg-data-uri\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"mini-svg-data-uri\";s:10:\"\0*\0version\";s:5:\"1.4.4\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"C:\\wamp64\\www\\etache\\node_modules\\mini-svg-data-uri\";}s:2:\"mz\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:2:\"mz\";s:10:\"\0*\0version\";s:5:\"2.7.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:36:\"C:\\wamp64\\www\\etache\\node_modules\\mz\";}s:6:\"nanoid\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:6:\"nanoid\";s:10:\"\0*\0version\";s:6:\"3.3.19\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:40:\"C:\\wamp64\\www\\etache\\node_modules\\nanoid\";}s:13:\"node-releases\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"node-releases\";s:10:\"\0*\0version\";s:6:\"2.0.57\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"C:\\wamp64\\www\\etache\\node_modules\\node-releases\";}s:14:\"normalize-path\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"normalize-path\";s:10:\"\0*\0version\";s:5:\"3.0.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"C:\\wamp64\\www\\etache\\node_modules\\normalize-path\";}s:13:\"object-assign\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"object-assign\";s:10:\"\0*\0version\";s:5:\"4.1.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"C:\\wamp64\\www\\etache\\node_modules\\object-assign\";}s:11:\"object-hash\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"object-hash\";s:10:\"\0*\0version\";s:5:\"3.0.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\node_modules\\object-hash\";}s:10:\"path-parse\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"path-parse\";s:10:\"\0*\0version\";s:5:\"1.0.7\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\wamp64\\www\\etache\\node_modules\\path-parse\";}s:10:\"picocolors\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"picocolors\";s:10:\"\0*\0version\";s:5:\"1.1.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\wamp64\\www\\etache\\node_modules\\picocolors\";}s:9:\"picomatch\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"picomatch\";s:10:\"\0*\0version\";s:5:\"2.3.2\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"C:\\wamp64\\www\\etache\\node_modules\\picomatch\";}s:7:\"pirates\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:7:\"pirates\";s:10:\"\0*\0version\";s:5:\"4.0.7\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:41:\"C:\\wamp64\\www\\etache\\node_modules\\pirates\";}s:7:\"postcss\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:7:\"postcss\";s:10:\"\0*\0version\";s:6:\"8.5.28\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:7:\"^8.4.31\";s:7:\"\0*\0path\";s:41:\"C:\\wamp64\\www\\etache\\node_modules\\postcss\";}s:14:\"postcss-import\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"postcss-import\";s:10:\"\0*\0version\";s:6:\"15.1.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"C:\\wamp64\\www\\etache\\node_modules\\postcss-import\";}s:10:\"postcss-js\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"postcss-js\";s:10:\"\0*\0version\";s:5:\"4.1.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\wamp64\\www\\etache\\node_modules\\postcss-js\";}s:19:\"postcss-load-config\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"postcss-load-config\";s:10:\"\0*\0version\";s:5:\"6.0.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"C:\\wamp64\\www\\etache\\node_modules\\postcss-load-config\";}s:14:\"postcss-nested\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"postcss-nested\";s:10:\"\0*\0version\";s:5:\"6.2.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"C:\\wamp64\\www\\etache\\node_modules\\postcss-nested\";}s:23:\"postcss-selector-parser\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"postcss-selector-parser\";s:10:\"\0*\0version\";s:5:\"6.1.4\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:57:\"C:\\wamp64\\www\\etache\\node_modules\\postcss-selector-parser\";}s:20:\"postcss-value-parser\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"postcss-value-parser\";s:10:\"\0*\0version\";s:5:\"4.2.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:54:\"C:\\wamp64\\www\\etache\\node_modules\\postcss-value-parser\";}s:15:\"queue-microtask\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"queue-microtask\";s:10:\"\0*\0version\";s:5:\"1.2.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:49:\"C:\\wamp64\\www\\etache\\node_modules\\queue-microtask\";}s:10:\"read-cache\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"read-cache\";s:10:\"\0*\0version\";s:5:\"1.0.2\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\wamp64\\www\\etache\\node_modules\\read-cache\";}s:8:\"readdirp\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"readdirp\";s:10:\"\0*\0version\";s:5:\"3.6.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:42:\"C:\\wamp64\\www\\etache\\node_modules\\readdirp\";}s:7:\"resolve\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:7:\"resolve\";s:10:\"\0*\0version\";s:7:\"1.22.12\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:41:\"C:\\wamp64\\www\\etache\\node_modules\\resolve\";}s:7:\"reusify\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:7:\"reusify\";s:10:\"\0*\0version\";s:5:\"1.1.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:41:\"C:\\wamp64\\www\\etache\\node_modules\\reusify\";}s:8:\"rolldown\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"rolldown\";s:10:\"\0*\0version\";s:6:\"1.2.11\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:42:\"C:\\wamp64\\www\\etache\\node_modules\\rolldown\";}s:12:\"run-parallel\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"run-parallel\";s:10:\"\0*\0version\";s:5:\"1.2.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"C:\\wamp64\\www\\etache\\node_modules\\run-parallel\";}s:4:\"rxjs\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:4:\"rxjs\";s:10:\"\0*\0version\";s:5:\"7.8.2\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:38:\"C:\\wamp64\\www\\etache\\node_modules\\rxjs\";}s:11:\"shell-quote\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"shell-quote\";s:10:\"\0*\0version\";s:5:\"1.9.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\node_modules\\shell-quote\";}s:13:\"source-map-js\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"source-map-js\";s:10:\"\0*\0version\";s:5:\"1.2.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"C:\\wamp64\\www\\etache\\node_modules\\source-map-js\";}s:7:\"sucrase\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:7:\"sucrase\";s:10:\"\0*\0version\";s:6:\"3.35.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:41:\"C:\\wamp64\\www\\etache\\node_modules\\sucrase\";}s:14:\"supports-color\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"supports-color\";s:10:\"\0*\0version\";s:6:\"10.2.2\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"C:\\wamp64\\www\\etache\\node_modules\\supports-color\";}s:31:\"supports-preserve-symlinks-flag\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:31:\"supports-preserve-symlinks-flag\";s:10:\"\0*\0version\";s:5:\"1.0.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:65:\"C:\\wamp64\\www\\etache\\node_modules\\supports-preserve-symlinks-flag\";}s:11:\"tailwindcss\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"tailwindcss\";s:10:\"\0*\0version\";s:6:\"3.4.19\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^3.1.0\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\node_modules\\tailwindcss\";}s:7:\"tapable\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:7:\"tapable\";s:10:\"\0*\0version\";s:5:\"2.3.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:41:\"C:\\wamp64\\www\\etache\\node_modules\\tapable\";}s:7:\"thenify\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:7:\"thenify\";s:10:\"\0*\0version\";s:5:\"3.3.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:41:\"C:\\wamp64\\www\\etache\\node_modules\\thenify\";}s:11:\"thenify-all\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"thenify-all\";s:10:\"\0*\0version\";s:5:\"1.6.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\node_modules\\thenify-all\";}s:10:\"tinyglobby\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"tinyglobby\";s:10:\"\0*\0version\";s:6:\"0.2.17\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\wamp64\\www\\etache\\node_modules\\tinyglobby\";}s:14:\"to-regex-range\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"to-regex-range\";s:10:\"\0*\0version\";s:5:\"5.0.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"C:\\wamp64\\www\\etache\\node_modules\\to-regex-range\";}s:9:\"tree-kill\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"tree-kill\";s:10:\"\0*\0version\";s:5:\"1.2.2\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"C:\\wamp64\\www\\etache\\node_modules\\tree-kill\";}s:20:\"ts-interface-checker\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"ts-interface-checker\";s:10:\"\0*\0version\";s:6:\"0.1.13\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:54:\"C:\\wamp64\\www\\etache\\node_modules\\ts-interface-checker\";}s:5:\"tslib\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:5:\"tslib\";s:10:\"\0*\0version\";s:5:\"2.8.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:39:\"C:\\wamp64\\www\\etache\\node_modules\\tslib\";}s:22:\"update-browserslist-db\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"update-browserslist-db\";s:10:\"\0*\0version\";s:5:\"1.3.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:56:\"C:\\wamp64\\www\\etache\\node_modules\\update-browserslist-db\";}s:14:\"util-deprecate\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"util-deprecate\";s:10:\"\0*\0version\";s:5:\"1.0.2\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"C:\\wamp64\\www\\etache\\node_modules\\util-deprecate\";}s:4:\"vite\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:4:\"vite\";s:10:\"\0*\0version\";s:5:\"8.3.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^8.0.0\";s:7:\"\0*\0path\";s:38:\"C:\\wamp64\\www\\etache\\node_modules\\vite\";}s:23:\"vite-plugin-full-reload\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"vite-plugin-full-reload\";s:10:\"\0*\0version\";s:5:\"1.2.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:57:\"C:\\wamp64\\www\\etache\\node_modules\\vite-plugin-full-reload\";}s:4:\"y18n\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:4:\"y18n\";s:10:\"\0*\0version\";s:5:\"5.0.8\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:38:\"C:\\wamp64\\www\\etache\\node_modules\\y18n\";}s:5:\"yargs\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:5:\"yargs\";s:10:\"\0*\0version\";s:6:\"18.0.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:39:\"C:\\wamp64\\www\\etache\\node_modules\\yargs\";}s:12:\"yargs-parser\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"yargs-parser\";s:10:\"\0*\0version\";s:6:\"22.0.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"C:\\wamp64\\www\\etache\\node_modules\\yargs-parser\";}s:4:\"fdir\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:4:\"fdir\";s:10:\"\0*\0version\";s:5:\"6.5.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:38:\"C:\\wamp64\\www\\etache\\node_modules\\fdir\";}}s:11:\"\0*\0packages\";O:32:\"Laravel\\Roster\\PackageCollection\":2:{s:8:\"\0*\0items\";a:189:{i:0;r:1153;i:1;r:1161;i:2;r:1169;i:3;r:1177;i:4;r:1185;i:5;r:1193;i:6;r:1201;i:7;r:1209;i:8;r:1217;i:9;r:1225;i:10;r:1233;i:11;r:1241;i:12;r:1249;i:13;r:1257;i:14;r:1265;i:15;r:1273;i:16;r:1281;i:17;r:1289;i:18;r:1297;i:19;r:1305;i:20;r:1313;i:21;r:1321;i:22;r:1329;i:23;r:1337;i:24;r:1345;i:25;r:1353;i:26;r:1361;i:27;r:1369;i:28;r:1377;i:29;r:1385;i:30;r:1393;i:31;r:1401;i:32;r:1409;i:33;r:1417;i:34;r:1425;i:35;r:1433;i:36;r:1441;i:37;r:1449;i:38;r:1457;i:39;r:1465;i:40;r:1473;i:41;r:1481;i:42;r:1489;i:43;r:1497;i:44;r:1505;i:45;r:1513;i:46;r:1521;i:47;r:1529;i:48;r:1537;i:49;r:1545;i:50;r:1553;i:51;r:1561;i:52;r:1569;i:53;r:1577;i:54;r:1585;i:55;r:1593;i:56;r:1601;i:57;r:1609;i:58;r:1617;i:59;r:1625;i:60;r:1633;i:61;r:1641;i:62;r:1649;i:63;r:1657;i:64;r:1665;i:65;r:1673;i:66;r:1681;i:67;r:1689;i:68;r:1697;i:69;r:1705;i:70;r:1713;i:71;r:1721;i:72;r:1729;i:73;r:1737;i:74;r:1745;i:75;r:1753;i:76;r:1761;i:77;r:1769;i:78;r:1777;i:79;r:1785;i:80;r:1793;i:81;r:1801;i:82;r:1809;i:83;r:1817;i:84;r:1825;i:85;r:1833;i:86;r:1841;i:87;r:1849;i:88;r:1857;i:89;r:1865;i:90;r:1873;i:91;r:1881;i:92;r:1889;i:93;r:1897;i:94;r:1905;i:95;r:1913;i:96;r:1921;i:97;r:1929;i:98;r:1937;i:99;r:1945;i:100;r:1953;i:101;r:1961;i:102;r:1969;i:103;r:1977;i:104;r:1985;i:105;r:1993;i:106;r:2001;i:107;r:2009;i:108;r:2017;i:109;r:2025;i:110;r:2033;i:111;r:2041;i:112;r:2049;i:113;r:2057;i:114;r:2065;i:115;r:2073;i:116;r:2081;i:117;r:2089;i:118;r:2097;i:119;r:2105;i:120;r:2113;i:121;r:2121;i:122;r:2129;i:123;r:2137;i:124;r:2145;i:125;r:2153;i:126;r:2161;i:127;r:2169;i:128;r:2177;i:129;r:2185;i:130;r:2193;i:131;r:2201;i:132;r:2209;i:133;r:2217;i:134;r:2225;i:135;r:2233;i:136;r:2241;i:137;r:2249;i:138;r:2257;i:139;r:2265;i:140;r:2273;i:141;r:2281;i:142;r:2289;i:143;r:2297;i:144;r:2305;i:145;r:2313;i:146;r:2321;i:147;r:2329;i:148;r:2337;i:149;r:2345;i:150;r:2353;i:151;r:2361;i:152;r:2369;i:153;r:2377;i:154;r:2385;i:155;r:2393;i:156;r:2401;i:157;r:2409;i:158;r:2417;i:159;r:2425;i:160;r:2433;i:161;r:2441;i:162;r:2449;i:163;r:2457;i:164;r:2465;i:165;r:2473;i:166;r:2481;i:167;r:2489;i:168;r:2497;i:169;r:2505;i:170;r:2513;i:171;r:2521;i:172;r:2529;i:173;r:2537;i:174;r:2545;i:175;r:2553;i:176;r:2561;i:177;r:2569;i:178;r:2577;i:179;r:2585;i:180;r:2593;i:181;r:2601;i:182;r:2609;i:183;r:2617;i:184;r:2625;i:185;r:2633;i:186;r:2641;i:187;r:2649;i:188;r:2657;}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}s:17:\"\0*\0packageManager\";E:41:\"Laravel\\Roster\\Enums\\JsPackageManager:Npm\";}s:6:\"stacks\";O:30:\"Laravel\\Roster\\Support\\EnumSet\":1:{s:8:\"\0*\0cases\";a:1:{i:0;E:32:\"Laravel\\Roster\\Enums\\Stack:Blade\";}}s:21:\"browserTestFrameworks\";O:30:\"Laravel\\Roster\\Support\\EnumSet\":1:{s:8:\"\0*\0cases\";a:0:{}}s:9:\"frontends\";O:30:\"Laravel\\Roster\\Support\\EnumSet\":1:{s:8:\"\0*\0cases\";a:0:{}}s:6:\"agents\";O:30:\"Laravel\\Roster\\Support\\EnumSet\":1:{s:8:\"\0*\0cases\";a:2:{i:0;E:37:\"Laravel\\Roster\\Enums\\Agent:ClaudeCode\";i:1;E:32:\"Laravel\\Roster\\Enums\\Agent:Codex\";}}s:7:\"editors\";O:30:\"Laravel\\Roster\\Support\\EnumSet\":1:{s:8:\"\0*\0cases\";a:0:{}}}', 1794163208);
INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('e-tache-cache-roster:project:v3:9f7e665cc1b0adeb5e50be33d73f6e32', 'O:26:\"Laravel\\Roster\\ProjectScan\":8:{s:8:\"basePath\";s:21:\"C:\\wamp64\\www\\etache\\\";s:3:\"php\";O:35:\"Laravel\\Roster\\Ecosystems\\Ecosystem\":2:{s:9:\"\0*\0byName\";a:127:{s:23:\"barryvdh/laravel-dompdf\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"barryvdh/laravel-dompdf\";s:10:\"\0*\0version\";s:5:\"3.1.2\";s:9:\"\0*\0source\";E:43:\"Laravel\\Roster\\Enums\\PackageSource:Composer\";s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:4:\"^3.1\";s:7:\"\0*\0path\";s:51:\"C:\\wamp64\\www\\etache\\vendor\\barryvdh\\laravel-dompdf\";}s:10:\"brick/math\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"brick/math\";s:10:\"\0*\0version\";s:5:\"1.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:38:\"C:\\wamp64\\www\\etache\\vendor\\brick\\math\";}s:31:\"carbonphp/carbon-doctrine-types\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:31:\"carbonphp/carbon-doctrine-types\";s:10:\"\0*\0version\";s:5:\"3.2.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:59:\"C:\\wamp64\\www\\etache\\vendor\\carbonphp\\carbon-doctrine-types\";}s:13:\"composer/pcre\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"composer/pcre\";s:10:\"\0*\0version\";s:5:\"3.4.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:41:\"C:\\wamp64\\www\\etache\\vendor\\composer\\pcre\";}s:23:\"dflydev/dot-access-data\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"dflydev/dot-access-data\";s:10:\"\0*\0version\";s:5:\"3.0.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"C:\\wamp64\\www\\etache\\vendor\\dflydev\\dot-access-data\";}s:18:\"doctrine/inflector\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"doctrine/inflector\";s:10:\"\0*\0version\";s:5:\"2.1.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"C:\\wamp64\\www\\etache\\vendor\\doctrine\\inflector\";}s:14:\"doctrine/lexer\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"doctrine/lexer\";s:10:\"\0*\0version\";s:5:\"3.0.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:42:\"C:\\wamp64\\www\\etache\\vendor\\doctrine\\lexer\";}s:13:\"dompdf/dompdf\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"dompdf/dompdf\";s:10:\"\0*\0version\";s:5:\"3.1.6\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:41:\"C:\\wamp64\\www\\etache\\vendor\\dompdf\\dompdf\";}s:19:\"dompdf/php-font-lib\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"dompdf/php-font-lib\";s:10:\"\0*\0version\";s:5:\"1.0.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"C:\\wamp64\\www\\etache\\vendor\\dompdf\\php-font-lib\";}s:18:\"dompdf/php-svg-lib\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"dompdf/php-svg-lib\";s:10:\"\0*\0version\";s:5:\"1.0.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"C:\\wamp64\\www\\etache\\vendor\\dompdf\\php-svg-lib\";}s:29:\"dragonmantank/cron-expression\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:29:\"dragonmantank/cron-expression\";s:10:\"\0*\0version\";s:5:\"3.6.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:57:\"C:\\wamp64\\www\\etache\\vendor\\dragonmantank\\cron-expression\";}s:23:\"egulias/email-validator\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"egulias/email-validator\";s:10:\"\0*\0version\";s:5:\"4.0.4\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"C:\\wamp64\\www\\etache\\vendor\\egulias\\email-validator\";}s:18:\"fruitcake/php-cors\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"fruitcake/php-cors\";s:10:\"\0*\0version\";s:5:\"1.4.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"C:\\wamp64\\www\\etache\\vendor\\fruitcake\\php-cors\";}s:27:\"graham-campbell/result-type\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:27:\"graham-campbell/result-type\";s:10:\"\0*\0version\";s:5:\"1.2.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:55:\"C:\\wamp64\\www\\etache\\vendor\\graham-campbell\\result-type\";}s:17:\"guzzlehttp/guzzle\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"guzzlehttp/guzzle\";s:10:\"\0*\0version\";s:5:\"8.2.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\vendor\\guzzlehttp\\guzzle\";}s:19:\"guzzlehttp/promises\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"guzzlehttp/promises\";s:10:\"\0*\0version\";s:5:\"3.0.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"C:\\wamp64\\www\\etache\\vendor\\guzzlehttp\\promises\";}s:15:\"guzzlehttp/psr7\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"guzzlehttp/psr7\";s:10:\"\0*\0version\";s:5:\"3.1.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"C:\\wamp64\\www\\etache\\vendor\\guzzlehttp\\psr7\";}s:23:\"guzzlehttp/uri-template\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"guzzlehttp/uri-template\";s:10:\"\0*\0version\";s:5:\"2.0.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"C:\\wamp64\\www\\etache\\vendor\\guzzlehttp\\uri-template\";}s:17:\"laravel/framework\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"laravel/framework\";s:10:\"\0*\0version\";s:7:\"13.33.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^13.17\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\vendor\\laravel\\framework\";}s:15:\"laravel/prompts\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"laravel/prompts\";s:10:\"\0*\0version\";s:6:\"0.3.24\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"C:\\wamp64\\www\\etache\\vendor\\laravel\\prompts\";}s:28:\"laravel/serializable-closure\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:28:\"laravel/serializable-closure\";s:10:\"\0*\0version\";s:5:\"2.1.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:56:\"C:\\wamp64\\www\\etache\\vendor\\laravel\\serializable-closure\";}s:14:\"laravel/tinker\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"laravel/tinker\";s:10:\"\0*\0version\";s:5:\"3.0.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:4:\"^3.0\";s:7:\"\0*\0path\";s:42:\"C:\\wamp64\\www\\etache\\vendor\\laravel\\tinker\";}s:17:\"league/commonmark\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"league/commonmark\";s:10:\"\0*\0version\";s:6:\"2.10.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\vendor\\league\\commonmark\";}s:13:\"league/config\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"league/config\";s:10:\"\0*\0version\";s:5:\"1.2.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:41:\"C:\\wamp64\\www\\etache\\vendor\\league\\config\";}s:16:\"league/flysystem\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"league/flysystem\";s:10:\"\0*\0version\";s:6:\"3.36.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\wamp64\\www\\etache\\vendor\\league\\flysystem\";}s:22:\"league/flysystem-local\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"league/flysystem-local\";s:10:\"\0*\0version\";s:6:\"3.35.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"C:\\wamp64\\www\\etache\\vendor\\league\\flysystem-local\";}s:26:\"league/mime-type-detection\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:26:\"league/mime-type-detection\";s:10:\"\0*\0version\";s:6:\"1.17.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:54:\"C:\\wamp64\\www\\etache\\vendor\\league\\mime-type-detection\";}s:10:\"league/uri\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"league/uri\";s:10:\"\0*\0version\";s:5:\"7.8.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:38:\"C:\\wamp64\\www\\etache\\vendor\\league\\uri\";}s:21:\"league/uri-interfaces\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"league/uri-interfaces\";s:10:\"\0*\0version\";s:5:\"7.8.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:49:\"C:\\wamp64\\www\\etache\\vendor\\league\\uri-interfaces\";}s:23:\"maennchen/zipstream-php\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"maennchen/zipstream-php\";s:10:\"\0*\0version\";s:5:\"3.2.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"C:\\wamp64\\www\\etache\\vendor\\maennchen\\zipstream-php\";}s:17:\"markbaker/complex\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"markbaker/complex\";s:10:\"\0*\0version\";s:5:\"3.0.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\vendor\\markbaker\\complex\";}s:16:\"markbaker/matrix\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"markbaker/matrix\";s:10:\"\0*\0version\";s:5:\"3.0.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\wamp64\\www\\etache\\vendor\\markbaker\\matrix\";}s:17:\"masterminds/html5\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"masterminds/html5\";s:10:\"\0*\0version\";s:6:\"2.11.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\vendor\\masterminds\\html5\";}s:15:\"monolog/monolog\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"monolog/monolog\";s:10:\"\0*\0version\";s:6:\"3.12.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"C:\\wamp64\\www\\etache\\vendor\\monolog\\monolog\";}s:13:\"nesbot/carbon\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"nesbot/carbon\";s:10:\"\0*\0version\";s:6:\"3.14.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:41:\"C:\\wamp64\\www\\etache\\vendor\\nesbot\\carbon\";}s:12:\"nette/schema\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"nette/schema\";s:10:\"\0*\0version\";s:5:\"1.3.6\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:40:\"C:\\wamp64\\www\\etache\\vendor\\nette\\schema\";}s:11:\"nette/utils\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"nette/utils\";s:10:\"\0*\0version\";s:5:\"4.1.5\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:39:\"C:\\wamp64\\www\\etache\\vendor\\nette\\utils\";}s:16:\"nikic/php-parser\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"nikic/php-parser\";s:10:\"\0*\0version\";s:5:\"5.9.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\wamp64\\www\\etache\\vendor\\nikic\\php-parser\";}s:19:\"nunomaduro/termwind\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"nunomaduro/termwind\";s:10:\"\0*\0version\";s:5:\"2.4.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"C:\\wamp64\\www\\etache\\vendor\\nunomaduro\\termwind\";}s:24:\"phpoffice/phpspreadsheet\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:24:\"phpoffice/phpspreadsheet\";s:10:\"\0*\0version\";s:6:\"5.10.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:5:\"^5.10\";s:7:\"\0*\0path\";s:52:\"C:\\wamp64\\www\\etache\\vendor\\phpoffice\\phpspreadsheet\";}s:19:\"phpoption/phpoption\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"phpoption/phpoption\";s:10:\"\0*\0version\";s:6:\"1.10.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"C:\\wamp64\\www\\etache\\vendor\\phpoption\\phpoption\";}s:9:\"psr/clock\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"psr/clock\";s:10:\"\0*\0version\";s:5:\"1.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:37:\"C:\\wamp64\\www\\etache\\vendor\\psr\\clock\";}s:13:\"psr/container\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"psr/container\";s:10:\"\0*\0version\";s:5:\"2.0.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:41:\"C:\\wamp64\\www\\etache\\vendor\\psr\\container\";}s:20:\"psr/event-dispatcher\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"psr/event-dispatcher\";s:10:\"\0*\0version\";s:5:\"1.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"C:\\wamp64\\www\\etache\\vendor\\psr\\event-dispatcher\";}s:15:\"psr/http-client\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"psr/http-client\";s:10:\"\0*\0version\";s:5:\"1.0.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"C:\\wamp64\\www\\etache\\vendor\\psr\\http-client\";}s:16:\"psr/http-factory\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"psr/http-factory\";s:10:\"\0*\0version\";s:5:\"1.1.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\wamp64\\www\\etache\\vendor\\psr\\http-factory\";}s:16:\"psr/http-message\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"psr/http-message\";s:10:\"\0*\0version\";s:3:\"2.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\wamp64\\www\\etache\\vendor\\psr\\http-message\";}s:7:\"psr/log\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:7:\"psr/log\";s:10:\"\0*\0version\";s:5:\"3.0.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:35:\"C:\\wamp64\\www\\etache\\vendor\\psr\\log\";}s:16:\"psr/simple-cache\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"psr/simple-cache\";s:10:\"\0*\0version\";s:5:\"3.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\wamp64\\www\\etache\\vendor\\psr\\simple-cache\";}s:9:\"psy/psysh\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"psy/psysh\";s:10:\"\0*\0version\";s:7:\"0.12.24\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:37:\"C:\\wamp64\\www\\etache\\vendor\\psy\\psysh\";}s:17:\"ramsey/collection\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"ramsey/collection\";s:10:\"\0*\0version\";s:5:\"2.1.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\vendor\\ramsey\\collection\";}s:11:\"ramsey/uuid\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"ramsey/uuid\";s:10:\"\0*\0version\";s:5:\"4.9.4\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:39:\"C:\\wamp64\\www\\etache\\vendor\\ramsey\\uuid\";}s:25:\"sabberworm/php-css-parser\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"sabberworm/php-css-parser\";s:10:\"\0*\0version\";s:5:\"9.5.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"C:\\wamp64\\www\\etache\\vendor\\sabberworm\\php-css-parser\";}s:13:\"symfony/clock\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"symfony/clock\";s:10:\"\0*\0version\";s:5:\"7.4.8\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:41:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\clock\";}s:15:\"symfony/console\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"symfony/console\";s:10:\"\0*\0version\";s:6:\"7.4.19\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\console\";}s:20:\"symfony/css-selector\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"symfony/css-selector\";s:10:\"\0*\0version\";s:6:\"7.4.18\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\css-selector\";}s:29:\"symfony/deprecation-contracts\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:29:\"symfony/deprecation-contracts\";s:10:\"\0*\0version\";s:5:\"3.7.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:57:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\deprecation-contracts\";}s:21:\"symfony/error-handler\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"symfony/error-handler\";s:10:\"\0*\0version\";s:6:\"7.4.17\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:49:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\error-handler\";}s:24:\"symfony/event-dispatcher\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:24:\"symfony/event-dispatcher\";s:10:\"\0*\0version\";s:6:\"7.4.17\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\event-dispatcher\";}s:34:\"symfony/event-dispatcher-contracts\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:34:\"symfony/event-dispatcher-contracts\";s:10:\"\0*\0version\";s:5:\"3.7.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:62:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\event-dispatcher-contracts\";}s:14:\"symfony/finder\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"symfony/finder\";s:10:\"\0*\0version\";s:6:\"7.4.19\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:42:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\finder\";}s:23:\"symfony/http-foundation\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"symfony/http-foundation\";s:10:\"\0*\0version\";s:6:\"7.4.19\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\http-foundation\";}s:19:\"symfony/http-kernel\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"symfony/http-kernel\";s:10:\"\0*\0version\";s:6:\"7.4.19\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\http-kernel\";}s:14:\"symfony/mailer\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"symfony/mailer\";s:10:\"\0*\0version\";s:6:\"7.4.19\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:42:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\mailer\";}s:12:\"symfony/mime\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"symfony/mime\";s:10:\"\0*\0version\";s:6:\"7.4.19\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:40:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\mime\";}s:22:\"symfony/polyfill-ctype\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"symfony/polyfill-ctype\";s:10:\"\0*\0version\";s:6:\"1.37.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\polyfill-ctype\";}s:30:\"symfony/polyfill-intl-grapheme\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:30:\"symfony/polyfill-intl-grapheme\";s:10:\"\0*\0version\";s:6:\"1.41.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:58:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\polyfill-intl-grapheme\";}s:25:\"symfony/polyfill-intl-idn\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"symfony/polyfill-intl-idn\";s:10:\"\0*\0version\";s:6:\"1.42.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\polyfill-intl-idn\";}s:32:\"symfony/polyfill-intl-normalizer\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:32:\"symfony/polyfill-intl-normalizer\";s:10:\"\0*\0version\";s:6:\"1.42.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:60:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\polyfill-intl-normalizer\";}s:25:\"symfony/polyfill-mbstring\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"symfony/polyfill-mbstring\";s:10:\"\0*\0version\";s:6:\"1.38.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\polyfill-mbstring\";}s:22:\"symfony/polyfill-php80\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"symfony/polyfill-php80\";s:10:\"\0*\0version\";s:6:\"1.37.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\polyfill-php80\";}s:22:\"symfony/polyfill-php82\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"symfony/polyfill-php82\";s:10:\"\0*\0version\";s:6:\"1.38.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\polyfill-php82\";}s:22:\"symfony/polyfill-php83\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"symfony/polyfill-php83\";s:10:\"\0*\0version\";s:6:\"1.41.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\polyfill-php83\";}s:22:\"symfony/polyfill-php84\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"symfony/polyfill-php84\";s:10:\"\0*\0version\";s:6:\"1.38.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\polyfill-php84\";}s:22:\"symfony/polyfill-php85\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"symfony/polyfill-php85\";s:10:\"\0*\0version\";s:6:\"1.41.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\polyfill-php85\";}s:22:\"symfony/polyfill-php86\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"symfony/polyfill-php86\";s:10:\"\0*\0version\";s:6:\"1.41.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\polyfill-php86\";}s:21:\"symfony/polyfill-uuid\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"symfony/polyfill-uuid\";s:10:\"\0*\0version\";s:6:\"1.37.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:49:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\polyfill-uuid\";}s:15:\"symfony/process\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"symfony/process\";s:10:\"\0*\0version\";s:6:\"7.4.19\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\process\";}s:15:\"symfony/routing\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"symfony/routing\";s:10:\"\0*\0version\";s:6:\"7.4.18\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\routing\";}s:25:\"symfony/service-contracts\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"symfony/service-contracts\";s:10:\"\0*\0version\";s:5:\"3.7.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\service-contracts\";}s:14:\"symfony/string\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"symfony/string\";s:10:\"\0*\0version\";s:6:\"7.4.19\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:42:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\string\";}s:19:\"symfony/translation\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"symfony/translation\";s:10:\"\0*\0version\";s:6:\"7.4.17\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\translation\";}s:29:\"symfony/translation-contracts\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:29:\"symfony/translation-contracts\";s:10:\"\0*\0version\";s:5:\"3.7.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:57:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\translation-contracts\";}s:11:\"symfony/uid\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"symfony/uid\";s:10:\"\0*\0version\";s:6:\"7.4.17\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:39:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\uid\";}s:18:\"symfony/var-dumper\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"symfony/var-dumper\";s:10:\"\0*\0version\";s:6:\"7.4.18\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\var-dumper\";}s:33:\"tijsverkoyen/css-to-inline-styles\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:33:\"tijsverkoyen/css-to-inline-styles\";s:10:\"\0*\0version\";s:5:\"2.4.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:61:\"C:\\wamp64\\www\\etache\\vendor\\tijsverkoyen\\css-to-inline-styles\";}s:16:\"vlucas/phpdotenv\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"vlucas/phpdotenv\";s:10:\"\0*\0version\";s:5:\"5.7.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\wamp64\\www\\etache\\vendor\\vlucas\\phpdotenv\";}s:19:\"voku/portable-ascii\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"voku/portable-ascii\";s:10:\"\0*\0version\";s:5:\"2.1.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"C:\\wamp64\\www\\etache\\vendor\\voku\\portable-ascii\";}s:15:\"composer/semver\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"composer/semver\";s:10:\"\0*\0version\";s:5:\"3.5.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"C:\\wamp64\\www\\etache\\vendor\\composer\\semver\";}s:14:\"fakerphp/faker\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"fakerphp/faker\";s:10:\"\0*\0version\";s:6:\"1.24.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:5:\"^1.23\";s:7:\"\0*\0path\";s:42:\"C:\\wamp64\\www\\etache\\vendor\\fakerphp\\faker\";}s:11:\"filp/whoops\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"filp/whoops\";s:10:\"\0*\0version\";s:6:\"2.18.5\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:39:\"C:\\wamp64\\www\\etache\\vendor\\filp\\whoops\";}s:21:\"hamcrest/hamcrest-php\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"hamcrest/hamcrest-php\";s:10:\"\0*\0version\";s:5:\"3.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:49:\"C:\\wamp64\\www\\etache\\vendor\\hamcrest\\hamcrest-php\";}s:22:\"laravel/agent-detector\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"laravel/agent-detector\";s:10:\"\0*\0version\";s:5:\"2.0.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"C:\\wamp64\\www\\etache\\vendor\\laravel\\agent-detector\";}s:13:\"laravel/boost\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"laravel/boost\";s:10:\"\0*\0version\";s:6:\"2.10.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:5:\"^2.10\";s:7:\"\0*\0path\";s:41:\"C:\\wamp64\\www\\etache\\vendor\\laravel\\boost\";}s:14:\"laravel/breeze\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"laravel/breeze\";s:10:\"\0*\0version\";s:5:\"2.4.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:1:\"*\";s:7:\"\0*\0path\";s:42:\"C:\\wamp64\\www\\etache\\vendor\\laravel\\breeze\";}s:11:\"laravel/mcp\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"laravel/mcp\";s:10:\"\0*\0version\";s:5:\"1.0.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:39:\"C:\\wamp64\\www\\etache\\vendor\\laravel\\mcp\";}s:12:\"laravel/pail\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"laravel/pail\";s:10:\"\0*\0version\";s:5:\"1.2.7\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^1.2.5\";s:7:\"\0*\0path\";s:40:\"C:\\wamp64\\www\\etache\\vendor\\laravel\\pail\";}s:11:\"laravel/pao\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"laravel/pao\";s:10:\"\0*\0version\";s:5:\"1.1.5\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^1.0.6\";s:7:\"\0*\0path\";s:39:\"C:\\wamp64\\www\\etache\\vendor\\laravel\\pao\";}s:12:\"laravel/pint\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"laravel/pint\";s:10:\"\0*\0version\";s:6:\"1.32.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:5:\"^1.27\";s:7:\"\0*\0path\";s:40:\"C:\\wamp64\\www\\etache\\vendor\\laravel\\pint\";}s:14:\"laravel/roster\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"laravel/roster\";s:10:\"\0*\0version\";s:5:\"1.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:42:\"C:\\wamp64\\www\\etache\\vendor\\laravel\\roster\";}s:15:\"mockery/mockery\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"mockery/mockery\";s:10:\"\0*\0version\";s:6:\"1.6.15\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:4:\"^1.6\";s:7:\"\0*\0path\";s:43:\"C:\\wamp64\\www\\etache\\vendor\\mockery\\mockery\";}s:17:\"myclabs/deep-copy\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"myclabs/deep-copy\";s:10:\"\0*\0version\";s:6:\"1.14.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\vendor\\myclabs\\deep-copy\";}s:20:\"nunomaduro/collision\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"nunomaduro/collision\";s:10:\"\0*\0version\";s:5:\"8.9.5\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:4:\"^8.6\";s:7:\"\0*\0path\";s:48:\"C:\\wamp64\\www\\etache\\vendor\\nunomaduro\\collision\";}s:16:\"phar-io/manifest\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"phar-io/manifest\";s:10:\"\0*\0version\";s:5:\"2.0.4\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\wamp64\\www\\etache\\vendor\\phar-io\\manifest\";}s:15:\"phar-io/version\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"phar-io/version\";s:10:\"\0*\0version\";s:5:\"3.2.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"C:\\wamp64\\www\\etache\\vendor\\phar-io\\version\";}s:25:\"phpunit/php-code-coverage\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"phpunit/php-code-coverage\";s:10:\"\0*\0version\";s:6:\"12.5.7\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"C:\\wamp64\\www\\etache\\vendor\\phpunit\\php-code-coverage\";}s:25:\"phpunit/php-file-iterator\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"phpunit/php-file-iterator\";s:10:\"\0*\0version\";s:5:\"6.0.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"C:\\wamp64\\www\\etache\\vendor\\phpunit\\php-file-iterator\";}s:19:\"phpunit/php-invoker\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"phpunit/php-invoker\";s:10:\"\0*\0version\";s:5:\"6.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"C:\\wamp64\\www\\etache\\vendor\\phpunit\\php-invoker\";}s:25:\"phpunit/php-text-template\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"phpunit/php-text-template\";s:10:\"\0*\0version\";s:5:\"5.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"C:\\wamp64\\www\\etache\\vendor\\phpunit\\php-text-template\";}s:17:\"phpunit/php-timer\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"phpunit/php-timer\";s:10:\"\0*\0version\";s:5:\"8.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\vendor\\phpunit\\php-timer\";}s:15:\"phpunit/phpunit\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"phpunit/phpunit\";s:10:\"\0*\0version\";s:7:\"12.5.35\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:8:\"^12.5.12\";s:7:\"\0*\0path\";s:43:\"C:\\wamp64\\www\\etache\\vendor\\phpunit\\phpunit\";}s:20:\"sebastian/cli-parser\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"sebastian/cli-parser\";s:10:\"\0*\0version\";s:5:\"4.2.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"C:\\wamp64\\www\\etache\\vendor\\sebastian\\cli-parser\";}s:20:\"sebastian/comparator\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"sebastian/comparator\";s:10:\"\0*\0version\";s:5:\"7.1.8\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"C:\\wamp64\\www\\etache\\vendor\\sebastian\\comparator\";}s:20:\"sebastian/complexity\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"sebastian/complexity\";s:10:\"\0*\0version\";s:5:\"5.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"C:\\wamp64\\www\\etache\\vendor\\sebastian\\complexity\";}s:14:\"sebastian/diff\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"sebastian/diff\";s:10:\"\0*\0version\";s:5:\"7.0.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:42:\"C:\\wamp64\\www\\etache\\vendor\\sebastian\\diff\";}s:21:\"sebastian/environment\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"sebastian/environment\";s:10:\"\0*\0version\";s:5:\"8.1.2\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:49:\"C:\\wamp64\\www\\etache\\vendor\\sebastian\\environment\";}s:18:\"sebastian/exporter\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"sebastian/exporter\";s:10:\"\0*\0version\";s:5:\"7.0.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"C:\\wamp64\\www\\etache\\vendor\\sebastian\\exporter\";}s:22:\"sebastian/global-state\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"sebastian/global-state\";s:10:\"\0*\0version\";s:5:\"8.0.3\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"C:\\wamp64\\www\\etache\\vendor\\sebastian\\global-state\";}s:23:\"sebastian/lines-of-code\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"sebastian/lines-of-code\";s:10:\"\0*\0version\";s:5:\"4.0.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"C:\\wamp64\\www\\etache\\vendor\\sebastian\\lines-of-code\";}s:27:\"sebastian/object-enumerator\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:27:\"sebastian/object-enumerator\";s:10:\"\0*\0version\";s:5:\"7.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:55:\"C:\\wamp64\\www\\etache\\vendor\\sebastian\\object-enumerator\";}s:26:\"sebastian/object-reflector\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:26:\"sebastian/object-reflector\";s:10:\"\0*\0version\";s:5:\"5.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:54:\"C:\\wamp64\\www\\etache\\vendor\\sebastian\\object-reflector\";}s:27:\"sebastian/recursion-context\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:27:\"sebastian/recursion-context\";s:10:\"\0*\0version\";s:5:\"7.0.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:55:\"C:\\wamp64\\www\\etache\\vendor\\sebastian\\recursion-context\";}s:14:\"sebastian/type\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"sebastian/type\";s:10:\"\0*\0version\";s:5:\"6.0.4\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:42:\"C:\\wamp64\\www\\etache\\vendor\\sebastian\\type\";}s:17:\"sebastian/version\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"sebastian/version\";s:10:\"\0*\0version\";s:5:\"6.0.0\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\vendor\\sebastian\\version\";}s:28:\"staabm/side-effects-detector\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:28:\"staabm/side-effects-detector\";s:10:\"\0*\0version\";s:5:\"1.0.5\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:56:\"C:\\wamp64\\www\\etache\\vendor\\staabm\\side-effects-detector\";}s:12:\"symfony/yaml\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"symfony/yaml\";s:10:\"\0*\0version\";s:6:\"7.4.20\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:40:\"C:\\wamp64\\www\\etache\\vendor\\symfony\\yaml\";}s:17:\"theseer/tokenizer\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"theseer/tokenizer\";s:10:\"\0*\0version\";s:5:\"2.0.1\";s:9:\"\0*\0source\";r:8;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\vendor\\theseer\\tokenizer\";}}s:11:\"\0*\0packages\";O:32:\"Laravel\\Roster\\PackageCollection\":2:{s:8:\"\0*\0items\";a:127:{i:0;r:5;i:1;r:13;i:2;r:21;i:3;r:29;i:4;r:37;i:5;r:45;i:6;r:53;i:7;r:61;i:8;r:69;i:9;r:77;i:10;r:85;i:11;r:93;i:12;r:101;i:13;r:109;i:14;r:117;i:15;r:125;i:16;r:133;i:17;r:141;i:18;r:149;i:19;r:157;i:20;r:165;i:21;r:173;i:22;r:181;i:23;r:189;i:24;r:197;i:25;r:205;i:26;r:213;i:27;r:221;i:28;r:229;i:29;r:237;i:30;r:245;i:31;r:253;i:32;r:261;i:33;r:269;i:34;r:277;i:35;r:285;i:36;r:293;i:37;r:301;i:38;r:309;i:39;r:317;i:40;r:325;i:41;r:333;i:42;r:341;i:43;r:349;i:44;r:357;i:45;r:365;i:46;r:373;i:47;r:381;i:48;r:389;i:49;r:397;i:50;r:405;i:51;r:413;i:52;r:421;i:53;r:429;i:54;r:437;i:55;r:445;i:56;r:453;i:57;r:461;i:58;r:469;i:59;r:477;i:60;r:485;i:61;r:493;i:62;r:501;i:63;r:509;i:64;r:517;i:65;r:525;i:66;r:533;i:67;r:541;i:68;r:549;i:69;r:557;i:70;r:565;i:71;r:573;i:72;r:581;i:73;r:589;i:74;r:597;i:75;r:605;i:76;r:613;i:77;r:621;i:78;r:629;i:79;r:637;i:80;r:645;i:81;r:653;i:82;r:661;i:83;r:669;i:84;r:677;i:85;r:685;i:86;r:693;i:87;r:701;i:88;r:709;i:89;r:717;i:90;r:725;i:91;r:733;i:92;r:741;i:93;r:749;i:94;r:757;i:95;r:765;i:96;r:773;i:97;r:781;i:98;r:789;i:99;r:797;i:100;r:805;i:101;r:813;i:102;r:821;i:103;r:829;i:104;r:837;i:105;r:845;i:106;r:853;i:107;r:861;i:108;r:869;i:109;r:877;i:110;r:885;i:111;r:893;i:112;r:901;i:113;r:909;i:114;r:917;i:115;r:925;i:116;r:933;i:117;r:941;i:118;r:949;i:119;r:957;i:120;r:965;i:121;r:973;i:122;r:981;i:123;r:989;i:124;r:997;i:125;r:1005;i:126;r:1013;}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}s:2:\"js\";O:37:\"Laravel\\Roster\\Ecosystems\\JsEcosystem\":3:{s:9:\"\0*\0byName\";a:189:{s:24:\"@alcalzone/ansi-tokenize\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:24:\"@alcalzone/ansi-tokenize\";s:10:\"\0*\0version\";s:5:\"0.3.1\";s:9:\"\0*\0source\";E:38:\"Laravel\\Roster\\Enums\\PackageSource:Npm\";s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:58:\"C:\\wamp64\\www\\etache\\node_modules\\@alcalzone\\ansi-tokenize\";}s:18:\"@laravel/multiplex\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"@laravel/multiplex\";s:10:\"\0*\0version\";s:5:\"0.4.4\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^0.4.1\";s:7:\"\0*\0path\";s:52:\"C:\\wamp64\\www\\etache\\node_modules\\@laravel\\multiplex\";}s:12:\"ansi-escapes\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"ansi-escapes\";s:10:\"\0*\0version\";s:5:\"7.3.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"C:\\wamp64\\www\\etache\\node_modules\\ansi-escapes\";}s:10:\"ansi-regex\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"ansi-regex\";s:10:\"\0*\0version\";s:5:\"6.3.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\wamp64\\www\\etache\\node_modules\\ansi-regex\";}s:11:\"ansi-styles\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"ansi-styles\";s:10:\"\0*\0version\";s:5:\"7.0.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\node_modules\\ansi-styles\";}s:9:\"auto-bind\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"auto-bind\";s:10:\"\0*\0version\";s:5:\"5.0.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"C:\\wamp64\\www\\etache\\node_modules\\auto-bind\";}s:5:\"chalk\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:5:\"chalk\";s:10:\"\0*\0version\";s:5:\"5.6.2\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:39:\"C:\\wamp64\\www\\etache\\node_modules\\chalk\";}s:9:\"cli-boxes\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"cli-boxes\";s:10:\"\0*\0version\";s:5:\"4.0.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"C:\\wamp64\\www\\etache\\node_modules\\cli-boxes\";}s:10:\"cli-cursor\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"cli-cursor\";s:10:\"\0*\0version\";s:5:\"4.0.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\wamp64\\www\\etache\\node_modules\\cli-cursor\";}s:12:\"cli-truncate\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"cli-truncate\";s:10:\"\0*\0version\";s:5:\"6.1.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"C:\\wamp64\\www\\etache\\node_modules\\cli-truncate\";}s:12:\"code-excerpt\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"code-excerpt\";s:10:\"\0*\0version\";s:5:\"4.0.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"C:\\wamp64\\www\\etache\\node_modules\\code-excerpt\";}s:9:\"commander\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"commander\";s:10:\"\0*\0version\";s:6:\"15.0.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"C:\\wamp64\\www\\etache\\node_modules\\commander\";}s:17:\"convert-to-spaces\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"convert-to-spaces\";s:10:\"\0*\0version\";s:5:\"2.0.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"C:\\wamp64\\www\\etache\\node_modules\\convert-to-spaces\";}s:11:\"environment\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"environment\";s:10:\"\0*\0version\";s:5:\"1.1.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\node_modules\\environment\";}s:10:\"es-toolkit\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"es-toolkit\";s:10:\"\0*\0version\";s:6:\"1.52.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\wamp64\\www\\etache\\node_modules\\es-toolkit\";}s:20:\"escape-string-regexp\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"escape-string-regexp\";s:10:\"\0*\0version\";s:5:\"2.0.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:54:\"C:\\wamp64\\www\\etache\\node_modules\\escape-string-regexp\";}s:20:\"get-east-asian-width\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"get-east-asian-width\";s:10:\"\0*\0version\";s:5:\"1.7.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:54:\"C:\\wamp64\\www\\etache\\node_modules\\get-east-asian-width\";}s:13:\"indent-string\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"indent-string\";s:10:\"\0*\0version\";s:5:\"5.0.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"C:\\wamp64\\www\\etache\\node_modules\\indent-string\";}s:3:\"ink\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:3:\"ink\";s:10:\"\0*\0version\";s:5:\"7.1.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:37:\"C:\\wamp64\\www\\etache\\node_modules\\ink\";}s:23:\"is-fullwidth-code-point\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"is-fullwidth-code-point\";s:10:\"\0*\0version\";s:5:\"5.1.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:57:\"C:\\wamp64\\www\\etache\\node_modules\\is-fullwidth-code-point\";}s:8:\"is-in-ci\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"is-in-ci\";s:10:\"\0*\0version\";s:5:\"2.0.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:42:\"C:\\wamp64\\www\\etache\\node_modules\\is-in-ci\";}s:8:\"mimic-fn\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"mimic-fn\";s:10:\"\0*\0version\";s:5:\"2.1.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:42:\"C:\\wamp64\\www\\etache\\node_modules\\mimic-fn\";}s:7:\"onetime\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:7:\"onetime\";s:10:\"\0*\0version\";s:5:\"5.1.2\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:41:\"C:\\wamp64\\www\\etache\\node_modules\\onetime\";}s:13:\"patch-console\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"patch-console\";s:10:\"\0*\0version\";s:5:\"2.0.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"C:\\wamp64\\www\\etache\\node_modules\\patch-console\";}s:5:\"react\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:5:\"react\";s:10:\"\0*\0version\";s:6:\"19.3.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:39:\"C:\\wamp64\\www\\etache\\node_modules\\react\";}s:16:\"react-reconciler\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"react-reconciler\";s:10:\"\0*\0version\";s:6:\"0.33.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"C:\\wamp64\\www\\etache\\node_modules\\react-reconciler\";}s:14:\"restore-cursor\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"restore-cursor\";s:10:\"\0*\0version\";s:5:\"4.0.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"C:\\wamp64\\www\\etache\\node_modules\\restore-cursor\";}s:9:\"scheduler\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"scheduler\";s:10:\"\0*\0version\";s:6:\"0.27.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"C:\\wamp64\\www\\etache\\node_modules\\scheduler\";}s:11:\"signal-exit\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"signal-exit\";s:10:\"\0*\0version\";s:5:\"3.0.7\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\node_modules\\signal-exit\";}s:10:\"slice-ansi\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"slice-ansi\";s:10:\"\0*\0version\";s:5:\"9.0.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\wamp64\\www\\etache\\node_modules\\slice-ansi\";}s:11:\"stack-utils\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"stack-utils\";s:10:\"\0*\0version\";s:5:\"2.0.6\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\node_modules\\stack-utils\";}s:12:\"string-width\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"string-width\";s:10:\"\0*\0version\";s:5:\"8.3.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"C:\\wamp64\\www\\etache\\node_modules\\string-width\";}s:10:\"strip-ansi\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"strip-ansi\";s:10:\"\0*\0version\";s:5:\"7.2.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\wamp64\\www\\etache\\node_modules\\strip-ansi\";}s:10:\"tagged-tag\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"tagged-tag\";s:10:\"\0*\0version\";s:5:\"1.0.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\wamp64\\www\\etache\\node_modules\\tagged-tag\";}s:13:\"terminal-size\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"terminal-size\";s:10:\"\0*\0version\";s:5:\"4.0.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"C:\\wamp64\\www\\etache\\node_modules\\terminal-size\";}s:9:\"type-fest\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"type-fest\";s:10:\"\0*\0version\";s:6:\"5.10.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"C:\\wamp64\\www\\etache\\node_modules\\type-fest\";}s:11:\"widest-line\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"widest-line\";s:10:\"\0*\0version\";s:5:\"6.0.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\node_modules\\widest-line\";}s:9:\"wrap-ansi\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"wrap-ansi\";s:10:\"\0*\0version\";s:6:\"10.0.2\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"C:\\wamp64\\www\\etache\\node_modules\\wrap-ansi\";}s:2:\"ws\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:2:\"ws\";s:10:\"\0*\0version\";s:6:\"8.21.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:36:\"C:\\wamp64\\www\\etache\\node_modules\\ws\";}s:11:\"yoga-layout\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"yoga-layout\";s:10:\"\0*\0version\";s:5:\"3.2.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:0;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\node_modules\\yoga-layout\";}s:16:\"@alloc/quick-lru\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"@alloc/quick-lru\";s:10:\"\0*\0version\";s:5:\"5.3.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"C:\\wamp64\\www\\etache\\node_modules\\@alloc\\quick-lru\";}s:23:\"@jridgewell/gen-mapping\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"@jridgewell/gen-mapping\";s:10:\"\0*\0version\";s:6:\"0.3.13\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:57:\"C:\\wamp64\\www\\etache\\node_modules\\@jridgewell\\gen-mapping\";}s:21:\"@jridgewell/remapping\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"@jridgewell/remapping\";s:10:\"\0*\0version\";s:5:\"2.3.5\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:55:\"C:\\wamp64\\www\\etache\\node_modules\\@jridgewell\\remapping\";}s:23:\"@jridgewell/resolve-uri\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"@jridgewell/resolve-uri\";s:10:\"\0*\0version\";s:5:\"3.1.2\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:57:\"C:\\wamp64\\www\\etache\\node_modules\\@jridgewell\\resolve-uri\";}s:27:\"@jridgewell/sourcemap-codec\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:27:\"@jridgewell/sourcemap-codec\";s:10:\"\0*\0version\";s:5:\"1.6.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:61:\"C:\\wamp64\\www\\etache\\node_modules\\@jridgewell\\sourcemap-codec\";}s:25:\"@jridgewell/trace-mapping\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"@jridgewell/trace-mapping\";s:10:\"\0*\0version\";s:6:\"0.3.31\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:59:\"C:\\wamp64\\www\\etache\\node_modules\\@jridgewell\\trace-mapping\";}s:19:\"@nodelib/fs.scandir\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"@nodelib/fs.scandir\";s:10:\"\0*\0version\";s:5:\"2.1.5\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"C:\\wamp64\\www\\etache\\node_modules\\@nodelib\\fs.scandir\";}s:16:\"@nodelib/fs.stat\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"@nodelib/fs.stat\";s:10:\"\0*\0version\";s:5:\"2.0.5\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"C:\\wamp64\\www\\etache\\node_modules\\@nodelib\\fs.stat\";}s:16:\"@nodelib/fs.walk\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"@nodelib/fs.walk\";s:10:\"\0*\0version\";s:5:\"1.2.8\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"C:\\wamp64\\www\\etache\\node_modules\\@nodelib\\fs.walk\";}s:18:\"@oxc-project/types\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"@oxc-project/types\";s:10:\"\0*\0version\";s:7:\"0.151.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"C:\\wamp64\\www\\etache\\node_modules\\@oxc-project\\types\";}s:34:\"@rolldown/binding-android-arm-eabi\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:34:\"@rolldown/binding-android-arm-eabi\";s:10:\"\0*\0version\";s:6:\"1.2.11\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:68:\"C:\\wamp64\\www\\etache\\node_modules\\@rolldown\\binding-android-arm-eabi\";}s:31:\"@rolldown/binding-android-arm64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:31:\"@rolldown/binding-android-arm64\";s:10:\"\0*\0version\";s:6:\"1.2.11\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:65:\"C:\\wamp64\\www\\etache\\node_modules\\@rolldown\\binding-android-arm64\";}s:30:\"@rolldown/binding-darwin-arm64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:30:\"@rolldown/binding-darwin-arm64\";s:10:\"\0*\0version\";s:6:\"1.2.11\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:64:\"C:\\wamp64\\www\\etache\\node_modules\\@rolldown\\binding-darwin-arm64\";}s:28:\"@rolldown/binding-darwin-x64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:28:\"@rolldown/binding-darwin-x64\";s:10:\"\0*\0version\";s:6:\"1.2.11\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:62:\"C:\\wamp64\\www\\etache\\node_modules\\@rolldown\\binding-darwin-x64\";}s:29:\"@rolldown/binding-freebsd-x64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:29:\"@rolldown/binding-freebsd-x64\";s:10:\"\0*\0version\";s:6:\"1.2.11\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:63:\"C:\\wamp64\\www\\etache\\node_modules\\@rolldown\\binding-freebsd-x64\";}s:37:\"@rolldown/binding-linux-arm-gnueabihf\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:37:\"@rolldown/binding-linux-arm-gnueabihf\";s:10:\"\0*\0version\";s:6:\"1.2.11\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:71:\"C:\\wamp64\\www\\etache\\node_modules\\@rolldown\\binding-linux-arm-gnueabihf\";}s:33:\"@rolldown/binding-linux-arm64-gnu\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:33:\"@rolldown/binding-linux-arm64-gnu\";s:10:\"\0*\0version\";s:6:\"1.2.11\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:67:\"C:\\wamp64\\www\\etache\\node_modules\\@rolldown\\binding-linux-arm64-gnu\";}s:34:\"@rolldown/binding-linux-arm64-musl\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:34:\"@rolldown/binding-linux-arm64-musl\";s:10:\"\0*\0version\";s:6:\"1.2.11\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:68:\"C:\\wamp64\\www\\etache\\node_modules\\@rolldown\\binding-linux-arm64-musl\";}s:33:\"@rolldown/binding-linux-ppc64-gnu\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:33:\"@rolldown/binding-linux-ppc64-gnu\";s:10:\"\0*\0version\";s:6:\"1.2.11\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:67:\"C:\\wamp64\\www\\etache\\node_modules\\@rolldown\\binding-linux-ppc64-gnu\";}s:33:\"@rolldown/binding-linux-s390x-gnu\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:33:\"@rolldown/binding-linux-s390x-gnu\";s:10:\"\0*\0version\";s:6:\"1.2.11\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:67:\"C:\\wamp64\\www\\etache\\node_modules\\@rolldown\\binding-linux-s390x-gnu\";}s:31:\"@rolldown/binding-linux-x64-gnu\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:31:\"@rolldown/binding-linux-x64-gnu\";s:10:\"\0*\0version\";s:6:\"1.2.11\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:65:\"C:\\wamp64\\www\\etache\\node_modules\\@rolldown\\binding-linux-x64-gnu\";}s:32:\"@rolldown/binding-linux-x64-musl\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:32:\"@rolldown/binding-linux-x64-musl\";s:10:\"\0*\0version\";s:6:\"1.2.11\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:66:\"C:\\wamp64\\www\\etache\\node_modules\\@rolldown\\binding-linux-x64-musl\";}s:35:\"@rolldown/binding-openharmony-arm64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:35:\"@rolldown/binding-openharmony-arm64\";s:10:\"\0*\0version\";s:6:\"1.2.11\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:69:\"C:\\wamp64\\www\\etache\\node_modules\\@rolldown\\binding-openharmony-arm64\";}s:34:\"@rolldown/binding-win32-arm64-msvc\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:34:\"@rolldown/binding-win32-arm64-msvc\";s:10:\"\0*\0version\";s:6:\"1.2.11\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:68:\"C:\\wamp64\\www\\etache\\node_modules\\@rolldown\\binding-win32-arm64-msvc\";}s:32:\"@rolldown/binding-win32-x64-msvc\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:32:\"@rolldown/binding-win32-x64-msvc\";s:10:\"\0*\0version\";s:6:\"1.2.11\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:66:\"C:\\wamp64\\www\\etache\\node_modules\\@rolldown\\binding-win32-x64-msvc\";}s:21:\"@rolldown/pluginutils\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:21:\"@rolldown/pluginutils\";s:10:\"\0*\0version\";s:5:\"1.0.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:55:\"C:\\wamp64\\www\\etache\\node_modules\\@rolldown\\pluginutils\";}s:18:\"@tailwindcss/forms\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"@tailwindcss/forms\";s:10:\"\0*\0version\";s:6:\"0.5.11\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^0.5.2\";s:7:\"\0*\0path\";s:52:\"C:\\wamp64\\www\\etache\\node_modules\\@tailwindcss\\forms\";}s:17:\"@tailwindcss/node\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"@tailwindcss/node\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"C:\\wamp64\\www\\etache\\node_modules\\@tailwindcss\\node\";}s:18:\"@tailwindcss/oxide\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:18:\"@tailwindcss/oxide\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:52:\"C:\\wamp64\\www\\etache\\node_modules\\@tailwindcss\\oxide\";}s:32:\"@tailwindcss/oxide-android-arm64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:32:\"@tailwindcss/oxide-android-arm64\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:66:\"C:\\wamp64\\www\\etache\\node_modules\\@tailwindcss\\oxide-android-arm64\";}s:31:\"@tailwindcss/oxide-darwin-arm64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:31:\"@tailwindcss/oxide-darwin-arm64\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:65:\"C:\\wamp64\\www\\etache\\node_modules\\@tailwindcss\\oxide-darwin-arm64\";}s:29:\"@tailwindcss/oxide-darwin-x64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:29:\"@tailwindcss/oxide-darwin-x64\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:63:\"C:\\wamp64\\www\\etache\\node_modules\\@tailwindcss\\oxide-darwin-x64\";}s:30:\"@tailwindcss/oxide-freebsd-x64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:30:\"@tailwindcss/oxide-freebsd-x64\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:64:\"C:\\wamp64\\www\\etache\\node_modules\\@tailwindcss\\oxide-freebsd-x64\";}s:38:\"@tailwindcss/oxide-linux-arm-gnueabihf\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:38:\"@tailwindcss/oxide-linux-arm-gnueabihf\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:72:\"C:\\wamp64\\www\\etache\\node_modules\\@tailwindcss\\oxide-linux-arm-gnueabihf\";}s:34:\"@tailwindcss/oxide-linux-arm64-gnu\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:34:\"@tailwindcss/oxide-linux-arm64-gnu\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:68:\"C:\\wamp64\\www\\etache\\node_modules\\@tailwindcss\\oxide-linux-arm64-gnu\";}s:35:\"@tailwindcss/oxide-linux-arm64-musl\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:35:\"@tailwindcss/oxide-linux-arm64-musl\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:69:\"C:\\wamp64\\www\\etache\\node_modules\\@tailwindcss\\oxide-linux-arm64-musl\";}s:32:\"@tailwindcss/oxide-linux-x64-gnu\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:32:\"@tailwindcss/oxide-linux-x64-gnu\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:66:\"C:\\wamp64\\www\\etache\\node_modules\\@tailwindcss\\oxide-linux-x64-gnu\";}s:33:\"@tailwindcss/oxide-linux-x64-musl\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:33:\"@tailwindcss/oxide-linux-x64-musl\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:67:\"C:\\wamp64\\www\\etache\\node_modules\\@tailwindcss\\oxide-linux-x64-musl\";}s:30:\"@tailwindcss/oxide-wasm32-wasi\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:30:\"@tailwindcss/oxide-wasm32-wasi\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:64:\"C:\\wamp64\\www\\etache\\node_modules\\@tailwindcss\\oxide-wasm32-wasi\";}s:35:\"@tailwindcss/oxide-win32-arm64-msvc\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:35:\"@tailwindcss/oxide-win32-arm64-msvc\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:69:\"C:\\wamp64\\www\\etache\\node_modules\\@tailwindcss\\oxide-win32-arm64-msvc\";}s:33:\"@tailwindcss/oxide-win32-x64-msvc\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:33:\"@tailwindcss/oxide-win32-x64-msvc\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:67:\"C:\\wamp64\\www\\etache\\node_modules\\@tailwindcss\\oxide-win32-x64-msvc\";}s:17:\"@tailwindcss/vite\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"@tailwindcss/vite\";s:10:\"\0*\0version\";s:5:\"4.3.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^4.0.0\";s:7:\"\0*\0path\";s:51:\"C:\\wamp64\\www\\etache\\node_modules\\@tailwindcss\\vite\";}s:15:\"@vue/reactivity\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"@vue/reactivity\";s:10:\"\0*\0version\";s:6:\"3.5.43\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:49:\"C:\\wamp64\\www\\etache\\node_modules\\@vue\\reactivity\";}s:11:\"@vue/shared\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"@vue/shared\";s:10:\"\0*\0version\";s:6:\"3.5.43\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\node_modules\\@vue\\shared\";}s:8:\"alpinejs\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"alpinejs\";s:10:\"\0*\0version\";s:6:\"3.17.4\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^3.4.2\";s:7:\"\0*\0path\";s:42:\"C:\\wamp64\\www\\etache\\node_modules\\alpinejs\";}s:11:\"any-promise\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"any-promise\";s:10:\"\0*\0version\";s:5:\"1.3.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\node_modules\\any-promise\";}s:8:\"anymatch\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"anymatch\";s:10:\"\0*\0version\";s:5:\"3.1.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:42:\"C:\\wamp64\\www\\etache\\node_modules\\anymatch\";}s:3:\"arg\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:3:\"arg\";s:10:\"\0*\0version\";s:5:\"5.0.2\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:37:\"C:\\wamp64\\www\\etache\\node_modules\\arg\";}s:12:\"autoprefixer\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"autoprefixer\";s:10:\"\0*\0version\";s:6:\"10.6.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:7:\"^10.4.2\";s:7:\"\0*\0path\";s:46:\"C:\\wamp64\\www\\etache\\node_modules\\autoprefixer\";}s:24:\"baseline-browser-mapping\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:24:\"baseline-browser-mapping\";s:10:\"\0*\0version\";s:7:\"2.11.26\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:58:\"C:\\wamp64\\www\\etache\\node_modules\\baseline-browser-mapping\";}s:17:\"binary-extensions\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"binary-extensions\";s:10:\"\0*\0version\";s:5:\"2.3.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"C:\\wamp64\\www\\etache\\node_modules\\binary-extensions\";}s:6:\"braces\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:6:\"braces\";s:10:\"\0*\0version\";s:5:\"3.0.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:40:\"C:\\wamp64\\www\\etache\\node_modules\\braces\";}s:12:\"browserslist\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"browserslist\";s:10:\"\0*\0version\";s:6:\"4.29.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"C:\\wamp64\\www\\etache\\node_modules\\browserslist\";}s:13:\"camelcase-css\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"camelcase-css\";s:10:\"\0*\0version\";s:5:\"2.0.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"C:\\wamp64\\www\\etache\\node_modules\\camelcase-css\";}s:12:\"caniuse-lite\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"caniuse-lite\";s:10:\"\0*\0version\";s:12:\"1.0.30001812\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"C:\\wamp64\\www\\etache\\node_modules\\caniuse-lite\";}s:8:\"chokidar\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"chokidar\";s:10:\"\0*\0version\";s:5:\"3.6.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:42:\"C:\\wamp64\\www\\etache\\node_modules\\chokidar\";}s:5:\"cliui\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:5:\"cliui\";s:10:\"\0*\0version\";s:5:\"9.0.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:39:\"C:\\wamp64\\www\\etache\\node_modules\\cliui\";}s:12:\"concurrently\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"concurrently\";s:10:\"\0*\0version\";s:6:\"10.0.5\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:7:\"^10.0.3\";s:7:\"\0*\0path\";s:46:\"C:\\wamp64\\www\\etache\\node_modules\\concurrently\";}s:6:\"cssesc\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:6:\"cssesc\";s:10:\"\0*\0version\";s:5:\"3.0.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:40:\"C:\\wamp64\\www\\etache\\node_modules\\cssesc\";}s:11:\"detect-libc\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"detect-libc\";s:10:\"\0*\0version\";s:5:\"2.1.2\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\node_modules\\detect-libc\";}s:10:\"didyoumean\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"didyoumean\";s:10:\"\0*\0version\";s:5:\"1.2.2\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\wamp64\\www\\etache\\node_modules\\didyoumean\";}s:3:\"dlv\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:3:\"dlv\";s:10:\"\0*\0version\";s:5:\"1.1.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:37:\"C:\\wamp64\\www\\etache\\node_modules\\dlv\";}s:20:\"electron-to-chromium\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"electron-to-chromium\";s:10:\"\0*\0version\";s:7:\"1.5.439\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:54:\"C:\\wamp64\\www\\etache\\node_modules\\electron-to-chromium\";}s:11:\"emoji-regex\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"emoji-regex\";s:10:\"\0*\0version\";s:6:\"10.6.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\node_modules\\emoji-regex\";}s:16:\"enhanced-resolve\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:16:\"enhanced-resolve\";s:10:\"\0*\0version\";s:6:\"5.25.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:50:\"C:\\wamp64\\www\\etache\\node_modules\\enhanced-resolve\";}s:9:\"es-errors\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"es-errors\";s:10:\"\0*\0version\";s:5:\"1.3.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"C:\\wamp64\\www\\etache\\node_modules\\es-errors\";}s:8:\"escalade\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"escalade\";s:10:\"\0*\0version\";s:5:\"3.2.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:42:\"C:\\wamp64\\www\\etache\\node_modules\\escalade\";}s:9:\"fast-glob\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"fast-glob\";s:10:\"\0*\0version\";s:5:\"3.3.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"C:\\wamp64\\www\\etache\\node_modules\\fast-glob\";}s:5:\"fastq\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:5:\"fastq\";s:10:\"\0*\0version\";s:6:\"1.20.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:39:\"C:\\wamp64\\www\\etache\\node_modules\\fastq\";}s:10:\"fill-range\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"fill-range\";s:10:\"\0*\0version\";s:5:\"7.1.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\wamp64\\www\\etache\\node_modules\\fill-range\";}s:11:\"fraction.js\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"fraction.js\";s:10:\"\0*\0version\";s:5:\"5.3.4\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\node_modules\\fraction.js\";}s:8:\"fsevents\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"fsevents\";s:10:\"\0*\0version\";s:5:\"2.3.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:42:\"C:\\wamp64\\www\\etache\\node_modules\\fsevents\";}s:13:\"function-bind\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"function-bind\";s:10:\"\0*\0version\";s:5:\"1.1.2\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"C:\\wamp64\\www\\etache\\node_modules\\function-bind\";}s:15:\"get-caller-file\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"get-caller-file\";s:10:\"\0*\0version\";s:5:\"2.0.5\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:49:\"C:\\wamp64\\www\\etache\\node_modules\\get-caller-file\";}s:11:\"glob-parent\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"glob-parent\";s:10:\"\0*\0version\";s:5:\"6.0.2\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\node_modules\\glob-parent\";}s:11:\"graceful-fs\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"graceful-fs\";s:10:\"\0*\0version\";s:6:\"4.2.11\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\node_modules\\graceful-fs\";}s:6:\"hasown\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:6:\"hasown\";s:10:\"\0*\0version\";s:5:\"2.0.4\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:40:\"C:\\wamp64\\www\\etache\\node_modules\\hasown\";}s:14:\"is-binary-path\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"is-binary-path\";s:10:\"\0*\0version\";s:5:\"2.1.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"C:\\wamp64\\www\\etache\\node_modules\\is-binary-path\";}s:14:\"is-core-module\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"is-core-module\";s:10:\"\0*\0version\";s:6:\"2.17.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"C:\\wamp64\\www\\etache\\node_modules\\is-core-module\";}s:10:\"is-extglob\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"is-extglob\";s:10:\"\0*\0version\";s:5:\"2.1.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\wamp64\\www\\etache\\node_modules\\is-extglob\";}s:7:\"is-glob\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:7:\"is-glob\";s:10:\"\0*\0version\";s:5:\"4.0.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:41:\"C:\\wamp64\\www\\etache\\node_modules\\is-glob\";}s:9:\"is-number\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"is-number\";s:10:\"\0*\0version\";s:5:\"7.0.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"C:\\wamp64\\www\\etache\\node_modules\\is-number\";}s:4:\"jiti\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:4:\"jiti\";s:10:\"\0*\0version\";s:5:\"2.7.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:38:\"C:\\wamp64\\www\\etache\\node_modules\\jiti\";}s:19:\"laravel-vite-plugin\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"laravel-vite-plugin\";s:10:\"\0*\0version\";s:5:\"3.2.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:4:\"^3.1\";s:7:\"\0*\0path\";s:53:\"C:\\wamp64\\www\\etache\\node_modules\\laravel-vite-plugin\";}s:12:\"lightningcss\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"lightningcss\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"C:\\wamp64\\www\\etache\\node_modules\\lightningcss\";}s:26:\"lightningcss-android-arm64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:26:\"lightningcss-android-arm64\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:60:\"C:\\wamp64\\www\\etache\\node_modules\\lightningcss-android-arm64\";}s:25:\"lightningcss-darwin-arm64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:25:\"lightningcss-darwin-arm64\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:59:\"C:\\wamp64\\www\\etache\\node_modules\\lightningcss-darwin-arm64\";}s:23:\"lightningcss-darwin-x64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"lightningcss-darwin-x64\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:57:\"C:\\wamp64\\www\\etache\\node_modules\\lightningcss-darwin-x64\";}s:24:\"lightningcss-freebsd-x64\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:24:\"lightningcss-freebsd-x64\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:58:\"C:\\wamp64\\www\\etache\\node_modules\\lightningcss-freebsd-x64\";}s:32:\"lightningcss-linux-arm-gnueabihf\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:32:\"lightningcss-linux-arm-gnueabihf\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:66:\"C:\\wamp64\\www\\etache\\node_modules\\lightningcss-linux-arm-gnueabihf\";}s:28:\"lightningcss-linux-arm64-gnu\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:28:\"lightningcss-linux-arm64-gnu\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:62:\"C:\\wamp64\\www\\etache\\node_modules\\lightningcss-linux-arm64-gnu\";}s:29:\"lightningcss-linux-arm64-musl\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:29:\"lightningcss-linux-arm64-musl\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:63:\"C:\\wamp64\\www\\etache\\node_modules\\lightningcss-linux-arm64-musl\";}s:26:\"lightningcss-linux-x64-gnu\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:26:\"lightningcss-linux-x64-gnu\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:60:\"C:\\wamp64\\www\\etache\\node_modules\\lightningcss-linux-x64-gnu\";}s:27:\"lightningcss-linux-x64-musl\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:27:\"lightningcss-linux-x64-musl\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:61:\"C:\\wamp64\\www\\etache\\node_modules\\lightningcss-linux-x64-musl\";}s:29:\"lightningcss-win32-arm64-msvc\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:29:\"lightningcss-win32-arm64-msvc\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:63:\"C:\\wamp64\\www\\etache\\node_modules\\lightningcss-win32-arm64-msvc\";}s:27:\"lightningcss-win32-x64-msvc\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:27:\"lightningcss-win32-x64-msvc\";s:10:\"\0*\0version\";s:6:\"1.32.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:61:\"C:\\wamp64\\www\\etache\\node_modules\\lightningcss-win32-x64-msvc\";}s:9:\"lilconfig\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"lilconfig\";s:10:\"\0*\0version\";s:5:\"3.1.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"C:\\wamp64\\www\\etache\\node_modules\\lilconfig\";}s:17:\"lines-and-columns\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"lines-and-columns\";s:10:\"\0*\0version\";s:5:\"1.2.4\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"C:\\wamp64\\www\\etache\\node_modules\\lines-and-columns\";}s:12:\"magic-string\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"magic-string\";s:10:\"\0*\0version\";s:7:\"0.30.21\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"C:\\wamp64\\www\\etache\\node_modules\\magic-string\";}s:6:\"merge2\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:6:\"merge2\";s:10:\"\0*\0version\";s:5:\"1.4.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:40:\"C:\\wamp64\\www\\etache\\node_modules\\merge2\";}s:10:\"micromatch\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"micromatch\";s:10:\"\0*\0version\";s:5:\"4.0.8\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\wamp64\\www\\etache\\node_modules\\micromatch\";}s:17:\"mini-svg-data-uri\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:17:\"mini-svg-data-uri\";s:10:\"\0*\0version\";s:5:\"1.4.4\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:51:\"C:\\wamp64\\www\\etache\\node_modules\\mini-svg-data-uri\";}s:2:\"mz\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:2:\"mz\";s:10:\"\0*\0version\";s:5:\"2.7.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:36:\"C:\\wamp64\\www\\etache\\node_modules\\mz\";}s:6:\"nanoid\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:6:\"nanoid\";s:10:\"\0*\0version\";s:6:\"3.3.19\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:40:\"C:\\wamp64\\www\\etache\\node_modules\\nanoid\";}s:13:\"node-releases\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"node-releases\";s:10:\"\0*\0version\";s:6:\"2.0.57\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"C:\\wamp64\\www\\etache\\node_modules\\node-releases\";}s:14:\"normalize-path\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"normalize-path\";s:10:\"\0*\0version\";s:5:\"3.0.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"C:\\wamp64\\www\\etache\\node_modules\\normalize-path\";}s:13:\"object-assign\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"object-assign\";s:10:\"\0*\0version\";s:5:\"4.1.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"C:\\wamp64\\www\\etache\\node_modules\\object-assign\";}s:11:\"object-hash\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"object-hash\";s:10:\"\0*\0version\";s:5:\"3.0.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\node_modules\\object-hash\";}s:10:\"path-parse\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"path-parse\";s:10:\"\0*\0version\";s:5:\"1.0.7\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\wamp64\\www\\etache\\node_modules\\path-parse\";}s:10:\"picocolors\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"picocolors\";s:10:\"\0*\0version\";s:5:\"1.1.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\wamp64\\www\\etache\\node_modules\\picocolors\";}s:9:\"picomatch\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"picomatch\";s:10:\"\0*\0version\";s:5:\"2.3.2\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"C:\\wamp64\\www\\etache\\node_modules\\picomatch\";}s:7:\"pirates\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:7:\"pirates\";s:10:\"\0*\0version\";s:5:\"4.0.7\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:41:\"C:\\wamp64\\www\\etache\\node_modules\\pirates\";}s:7:\"postcss\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:7:\"postcss\";s:10:\"\0*\0version\";s:6:\"8.5.28\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:7:\"^8.4.31\";s:7:\"\0*\0path\";s:41:\"C:\\wamp64\\www\\etache\\node_modules\\postcss\";}s:14:\"postcss-import\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"postcss-import\";s:10:\"\0*\0version\";s:6:\"15.1.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"C:\\wamp64\\www\\etache\\node_modules\\postcss-import\";}s:10:\"postcss-js\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"postcss-js\";s:10:\"\0*\0version\";s:5:\"4.1.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\wamp64\\www\\etache\\node_modules\\postcss-js\";}s:19:\"postcss-load-config\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:19:\"postcss-load-config\";s:10:\"\0*\0version\";s:5:\"6.0.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:53:\"C:\\wamp64\\www\\etache\\node_modules\\postcss-load-config\";}s:14:\"postcss-nested\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"postcss-nested\";s:10:\"\0*\0version\";s:5:\"6.2.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"C:\\wamp64\\www\\etache\\node_modules\\postcss-nested\";}s:23:\"postcss-selector-parser\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"postcss-selector-parser\";s:10:\"\0*\0version\";s:5:\"6.1.4\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:57:\"C:\\wamp64\\www\\etache\\node_modules\\postcss-selector-parser\";}s:20:\"postcss-value-parser\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"postcss-value-parser\";s:10:\"\0*\0version\";s:5:\"4.2.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:54:\"C:\\wamp64\\www\\etache\\node_modules\\postcss-value-parser\";}s:15:\"queue-microtask\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:15:\"queue-microtask\";s:10:\"\0*\0version\";s:5:\"1.2.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:49:\"C:\\wamp64\\www\\etache\\node_modules\\queue-microtask\";}s:10:\"read-cache\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"read-cache\";s:10:\"\0*\0version\";s:5:\"1.0.2\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\wamp64\\www\\etache\\node_modules\\read-cache\";}s:8:\"readdirp\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"readdirp\";s:10:\"\0*\0version\";s:5:\"3.6.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:42:\"C:\\wamp64\\www\\etache\\node_modules\\readdirp\";}s:7:\"resolve\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:7:\"resolve\";s:10:\"\0*\0version\";s:7:\"1.22.12\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:41:\"C:\\wamp64\\www\\etache\\node_modules\\resolve\";}s:7:\"reusify\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:7:\"reusify\";s:10:\"\0*\0version\";s:5:\"1.1.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:41:\"C:\\wamp64\\www\\etache\\node_modules\\reusify\";}s:8:\"rolldown\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:8:\"rolldown\";s:10:\"\0*\0version\";s:6:\"1.2.11\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:42:\"C:\\wamp64\\www\\etache\\node_modules\\rolldown\";}s:12:\"run-parallel\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"run-parallel\";s:10:\"\0*\0version\";s:5:\"1.2.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"C:\\wamp64\\www\\etache\\node_modules\\run-parallel\";}s:4:\"rxjs\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:4:\"rxjs\";s:10:\"\0*\0version\";s:5:\"7.8.2\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:38:\"C:\\wamp64\\www\\etache\\node_modules\\rxjs\";}s:11:\"shell-quote\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"shell-quote\";s:10:\"\0*\0version\";s:5:\"1.9.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\node_modules\\shell-quote\";}s:13:\"source-map-js\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:13:\"source-map-js\";s:10:\"\0*\0version\";s:5:\"1.2.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:47:\"C:\\wamp64\\www\\etache\\node_modules\\source-map-js\";}s:7:\"sucrase\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:7:\"sucrase\";s:10:\"\0*\0version\";s:6:\"3.35.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:41:\"C:\\wamp64\\www\\etache\\node_modules\\sucrase\";}s:14:\"supports-color\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"supports-color\";s:10:\"\0*\0version\";s:6:\"10.2.2\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"C:\\wamp64\\www\\etache\\node_modules\\supports-color\";}s:31:\"supports-preserve-symlinks-flag\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:31:\"supports-preserve-symlinks-flag\";s:10:\"\0*\0version\";s:5:\"1.0.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:65:\"C:\\wamp64\\www\\etache\\node_modules\\supports-preserve-symlinks-flag\";}s:11:\"tailwindcss\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"tailwindcss\";s:10:\"\0*\0version\";s:6:\"3.4.19\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^3.1.0\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\node_modules\\tailwindcss\";}s:7:\"tapable\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:7:\"tapable\";s:10:\"\0*\0version\";s:5:\"2.3.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:41:\"C:\\wamp64\\www\\etache\\node_modules\\tapable\";}s:7:\"thenify\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:7:\"thenify\";s:10:\"\0*\0version\";s:5:\"3.3.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:41:\"C:\\wamp64\\www\\etache\\node_modules\\thenify\";}s:11:\"thenify-all\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:11:\"thenify-all\";s:10:\"\0*\0version\";s:5:\"1.6.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:45:\"C:\\wamp64\\www\\etache\\node_modules\\thenify-all\";}s:10:\"tinyglobby\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:10:\"tinyglobby\";s:10:\"\0*\0version\";s:6:\"0.2.17\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:44:\"C:\\wamp64\\www\\etache\\node_modules\\tinyglobby\";}s:14:\"to-regex-range\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"to-regex-range\";s:10:\"\0*\0version\";s:5:\"5.0.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"C:\\wamp64\\www\\etache\\node_modules\\to-regex-range\";}s:9:\"tree-kill\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:9:\"tree-kill\";s:10:\"\0*\0version\";s:5:\"1.2.2\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:43:\"C:\\wamp64\\www\\etache\\node_modules\\tree-kill\";}s:20:\"ts-interface-checker\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:20:\"ts-interface-checker\";s:10:\"\0*\0version\";s:6:\"0.1.13\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:54:\"C:\\wamp64\\www\\etache\\node_modules\\ts-interface-checker\";}s:5:\"tslib\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:5:\"tslib\";s:10:\"\0*\0version\";s:5:\"2.8.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:39:\"C:\\wamp64\\www\\etache\\node_modules\\tslib\";}s:22:\"update-browserslist-db\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:22:\"update-browserslist-db\";s:10:\"\0*\0version\";s:5:\"1.3.3\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:56:\"C:\\wamp64\\www\\etache\\node_modules\\update-browserslist-db\";}s:14:\"util-deprecate\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:14:\"util-deprecate\";s:10:\"\0*\0version\";s:5:\"1.0.2\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:48:\"C:\\wamp64\\www\\etache\\node_modules\\util-deprecate\";}s:4:\"vite\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:4:\"vite\";s:10:\"\0*\0version\";s:5:\"8.3.1\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:1;s:13:\"\0*\0constraint\";s:6:\"^8.0.0\";s:7:\"\0*\0path\";s:38:\"C:\\wamp64\\www\\etache\\node_modules\\vite\";}s:23:\"vite-plugin-full-reload\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:23:\"vite-plugin-full-reload\";s:10:\"\0*\0version\";s:5:\"1.2.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:57:\"C:\\wamp64\\www\\etache\\node_modules\\vite-plugin-full-reload\";}s:4:\"y18n\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:4:\"y18n\";s:10:\"\0*\0version\";s:5:\"5.0.8\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:38:\"C:\\wamp64\\www\\etache\\node_modules\\y18n\";}s:5:\"yargs\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:5:\"yargs\";s:10:\"\0*\0version\";s:6:\"18.0.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:39:\"C:\\wamp64\\www\\etache\\node_modules\\yargs\";}s:12:\"yargs-parser\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:12:\"yargs-parser\";s:10:\"\0*\0version\";s:6:\"22.0.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:46:\"C:\\wamp64\\www\\etache\\node_modules\\yargs-parser\";}s:4:\"fdir\";O:22:\"Laravel\\Roster\\Package\":7:{s:7:\"\0*\0name\";s:4:\"fdir\";s:10:\"\0*\0version\";s:5:\"6.5.0\";s:9:\"\0*\0source\";r:1156;s:6:\"\0*\0dev\";b:1;s:9:\"\0*\0direct\";b:0;s:13:\"\0*\0constraint\";s:0:\"\";s:7:\"\0*\0path\";s:38:\"C:\\wamp64\\www\\etache\\node_modules\\fdir\";}}s:11:\"\0*\0packages\";O:32:\"Laravel\\Roster\\PackageCollection\":2:{s:8:\"\0*\0items\";a:189:{i:0;r:1153;i:1;r:1161;i:2;r:1169;i:3;r:1177;i:4;r:1185;i:5;r:1193;i:6;r:1201;i:7;r:1209;i:8;r:1217;i:9;r:1225;i:10;r:1233;i:11;r:1241;i:12;r:1249;i:13;r:1257;i:14;r:1265;i:15;r:1273;i:16;r:1281;i:17;r:1289;i:18;r:1297;i:19;r:1305;i:20;r:1313;i:21;r:1321;i:22;r:1329;i:23;r:1337;i:24;r:1345;i:25;r:1353;i:26;r:1361;i:27;r:1369;i:28;r:1377;i:29;r:1385;i:30;r:1393;i:31;r:1401;i:32;r:1409;i:33;r:1417;i:34;r:1425;i:35;r:1433;i:36;r:1441;i:37;r:1449;i:38;r:1457;i:39;r:1465;i:40;r:1473;i:41;r:1481;i:42;r:1489;i:43;r:1497;i:44;r:1505;i:45;r:1513;i:46;r:1521;i:47;r:1529;i:48;r:1537;i:49;r:1545;i:50;r:1553;i:51;r:1561;i:52;r:1569;i:53;r:1577;i:54;r:1585;i:55;r:1593;i:56;r:1601;i:57;r:1609;i:58;r:1617;i:59;r:1625;i:60;r:1633;i:61;r:1641;i:62;r:1649;i:63;r:1657;i:64;r:1665;i:65;r:1673;i:66;r:1681;i:67;r:1689;i:68;r:1697;i:69;r:1705;i:70;r:1713;i:71;r:1721;i:72;r:1729;i:73;r:1737;i:74;r:1745;i:75;r:1753;i:76;r:1761;i:77;r:1769;i:78;r:1777;i:79;r:1785;i:80;r:1793;i:81;r:1801;i:82;r:1809;i:83;r:1817;i:84;r:1825;i:85;r:1833;i:86;r:1841;i:87;r:1849;i:88;r:1857;i:89;r:1865;i:90;r:1873;i:91;r:1881;i:92;r:1889;i:93;r:1897;i:94;r:1905;i:95;r:1913;i:96;r:1921;i:97;r:1929;i:98;r:1937;i:99;r:1945;i:100;r:1953;i:101;r:1961;i:102;r:1969;i:103;r:1977;i:104;r:1985;i:105;r:1993;i:106;r:2001;i:107;r:2009;i:108;r:2017;i:109;r:2025;i:110;r:2033;i:111;r:2041;i:112;r:2049;i:113;r:2057;i:114;r:2065;i:115;r:2073;i:116;r:2081;i:117;r:2089;i:118;r:2097;i:119;r:2105;i:120;r:2113;i:121;r:2121;i:122;r:2129;i:123;r:2137;i:124;r:2145;i:125;r:2153;i:126;r:2161;i:127;r:2169;i:128;r:2177;i:129;r:2185;i:130;r:2193;i:131;r:2201;i:132;r:2209;i:133;r:2217;i:134;r:2225;i:135;r:2233;i:136;r:2241;i:137;r:2249;i:138;r:2257;i:139;r:2265;i:140;r:2273;i:141;r:2281;i:142;r:2289;i:143;r:2297;i:144;r:2305;i:145;r:2313;i:146;r:2321;i:147;r:2329;i:148;r:2337;i:149;r:2345;i:150;r:2353;i:151;r:2361;i:152;r:2369;i:153;r:2377;i:154;r:2385;i:155;r:2393;i:156;r:2401;i:157;r:2409;i:158;r:2417;i:159;r:2425;i:160;r:2433;i:161;r:2441;i:162;r:2449;i:163;r:2457;i:164;r:2465;i:165;r:2473;i:166;r:2481;i:167;r:2489;i:168;r:2497;i:169;r:2505;i:170;r:2513;i:171;r:2521;i:172;r:2529;i:173;r:2537;i:174;r:2545;i:175;r:2553;i:176;r:2561;i:177;r:2569;i:178;r:2577;i:179;r:2585;i:180;r:2593;i:181;r:2601;i:182;r:2609;i:183;r:2617;i:184;r:2625;i:185;r:2633;i:186;r:2641;i:187;r:2649;i:188;r:2657;}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}s:17:\"\0*\0packageManager\";E:41:\"Laravel\\Roster\\Enums\\JsPackageManager:Npm\";}s:6:\"stacks\";O:30:\"Laravel\\Roster\\Support\\EnumSet\":1:{s:8:\"\0*\0cases\";a:1:{i:0;E:32:\"Laravel\\Roster\\Enums\\Stack:Blade\";}}s:21:\"browserTestFrameworks\";O:30:\"Laravel\\Roster\\Support\\EnumSet\":1:{s:8:\"\0*\0cases\";a:0:{}}s:9:\"frontends\";O:30:\"Laravel\\Roster\\Support\\EnumSet\":1:{s:8:\"\0*\0cases\";a:0:{}}s:6:\"agents\";O:30:\"Laravel\\Roster\\Support\\EnumSet\":1:{s:8:\"\0*\0cases\";a:2:{i:0;E:37:\"Laravel\\Roster\\Enums\\Agent:ClaudeCode\";i:1;E:32:\"Laravel\\Roster\\Enums\\Agent:Codex\";}}s:7:\"editors\";O:30:\"Laravel\\Roster\\Support\\EnumSet\":1:{s:8:\"\0*\0cases\";a:0:{}}}', 1794103594);

-- --------------------------------------------------------

--
-- Structure de la table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `canaux_notification`
--

DROP TABLE IF EXISTS `canaux_notification`;
CREATE TABLE IF NOT EXISTS `canaux_notification` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `libelle` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `canaux_notification_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `canaux_notification`
--

INSERT INTO `canaux_notification` (`id`, `code`, `libelle`, `actif`, `created_at`, `updated_at`) VALUES
(1, '001', 'Notification application', 1, '2026-09-26 07:00:25', '2026-09-26 07:00:25'),
(2, '002', 'Email', 1, '2026-09-26 07:00:25', '2026-09-26 07:00:25'),
(3, '003', 'SMS', 0, '2026-09-26 07:00:25', '2026-09-27 15:57:32');

-- --------------------------------------------------------

--
-- Structure de la table `classes_agent`
--

DROP TABLE IF EXISTS `classes_agent`;
CREATE TABLE IF NOT EXISTS `classes_agent` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `libelle` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ordre` smallint UNSIGNED NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `classes_agent_libelle_unique` (`libelle`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `classes_agent`
--

INSERT INTO `classes_agent` (`id`, `libelle`, `ordre`, `created_at`, `updated_at`) VALUES
(1, '2ème classe', 1, '2026-10-10 07:49:56', '2026-10-10 07:49:56'),
(2, '1ère classe', 2, '2026-10-10 07:49:56', '2026-10-10 07:49:56'),
(3, 'Classe principale', 3, '2026-10-10 07:49:56', '2026-10-10 07:49:56'),
(4, 'Classe exceptionnelle', 4, '2026-10-10 07:49:56', '2026-10-10 07:49:56');

-- --------------------------------------------------------

--
-- Structure de la table `courriers`
--

DROP TABLE IF EXISTS `courriers`;
CREATE TABLE IF NOT EXISTS `courriers` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `sens` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'arrivee',
  `numero` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `annee` smallint UNSIGNED NOT NULL,
  `sequence` int UNSIGNED NOT NULL,
  `date_reception` date NOT NULL,
  `date_courrier` date DEFAULT NULL,
  `reference_courrier` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expediteur` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `objet` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type_courrier_id` bigint UNSIGNED NOT NULL,
  `urgence_id` bigint UNSIGNED NOT NULL,
  `confidentialite` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'normal',
  `origine` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'service_courrier',
  `mode_transmission` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mode_transmission_precision` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nombre_pieces` smallint UNSIGNED NOT NULL DEFAULT '0',
  `observations` text COLLATE utf8mb4_unicode_ci,
  `statut` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'enregistre',
  `enregistre_par` bigint UNSIGNED DEFAULT NULL,
  `a_conserver_jusqu_au` date DEFAULT NULL,
  `archive_le` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `numero_archives` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `archive_par` bigint UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `courriers_sens_annee_sequence_unique` (`sens`,`annee`,`sequence`),
  UNIQUE KEY `courriers_numero_unique` (`numero`),
  KEY `courriers_type_courrier_id_foreign` (`type_courrier_id`),
  KEY `courriers_urgence_id_foreign` (`urgence_id`),
  KEY `courriers_enregistre_par_foreign` (`enregistre_par`),
  KEY `courriers_date_reception_index` (`date_reception`),
  KEY `courriers_statut_index` (`statut`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `courriers`
--

INSERT INTO `courriers` (`id`, `sens`, `numero`, `annee`, `sequence`, `date_reception`, `date_courrier`, `reference_courrier`, `expediteur`, `objet`, `type_courrier_id`, `urgence_id`, `confidentialite`, `origine`, `mode_transmission`, `mode_transmission_precision`, `nombre_pieces`, `observations`, `statut`, `enregistre_par`, `a_conserver_jusqu_au`, `archive_le`, `created_at`, `updated_at`, `numero_archives`, `archive_par`) VALUES
(1, 'arrivee', 'A26-0001', 2026, 1, '2026-10-09', '2026-10-06', '0412/ANADT/DG/2026', 'Agence Nationale d’Appui au Développement des Territoire', 'Désignation d’un point focal et de son suppléant pour le suivi de la plateforme nationale de reporting des établissements publics', 1, 2, 'normal', 'service_courrier', 'porteur_coursier', NULL, 1, NULL, 'impute', 7, NULL, NULL, '2026-10-09 16:16:30', '2026-10-12 04:43:32', NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `courrier_sequences`
--

DROP TABLE IF EXISTS `courrier_sequences`;
CREATE TABLE IF NOT EXISTS `courrier_sequences` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `sens` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `annee` smallint UNSIGNED NOT NULL,
  `dernier` int UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `courrier_sequences_sens_annee_unique` (`sens`,`annee`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `courrier_sequences`
--

INSERT INTO `courrier_sequences` (`id`, `sens`, `annee`, `dernier`) VALUES
(1, 'arrivee', 2026, 1),
(2, 'diligence', 2026, 3);

-- --------------------------------------------------------

--
-- Structure de la table `delais_courrier`
--

DROP TABLE IF EXISTS `delais_courrier`;
CREATE TABLE IF NOT EXISTS `delais_courrier` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `libelle` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `delai_jours` smallint UNSIGNED DEFAULT NULL,
  `saisie_libre` tinyint(1) NOT NULL DEFAULT '0',
  `ordre` smallint UNSIGNED NOT NULL DEFAULT '1',
  `actif` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `delais_courrier_code_unique` (`code`),
  UNIQUE KEY `delais_courrier_libelle_unique` (`libelle`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `delais_courrier`
--

INSERT INTO `delais_courrier` (`id`, `code`, `libelle`, `delai_jours`, `saisie_libre`, `ordre`, `actif`, `created_at`, `updated_at`) VALUES
(1, 'URGENT', 'Urgent', 1, 0, 1, 1, '2026-10-09 10:48:41', '2026-10-09 10:48:41'),
(2, 'H24', '24 h', 1, 0, 2, 1, '2026-10-09 10:48:41', '2026-10-09 10:48:41'),
(3, 'H48', '48 h', 2, 0, 3, 1, '2026-10-09 10:48:41', '2026-10-09 10:48:41'),
(4, 'AUTRE', 'Autre', NULL, 1, 4, 1, '2026-10-09 10:48:41', '2026-10-09 10:48:41');

-- --------------------------------------------------------

--
-- Structure de la table `departs`
--

DROP TABLE IF EXISTS `departs`;
CREATE TABLE IF NOT EXISTS `departs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `groupe` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `numero` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `annee` smallint UNSIGNED NOT NULL,
  `sequence` int UNSIGNED NOT NULL,
  `courrier_id` bigint UNSIGNED DEFAULT NULL,
  `reponse_id` bigint UNSIGNED DEFAULT NULL,
  `diligence_id` bigint UNSIGNED DEFAULT NULL,
  `destinataire` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `destinataire_precision` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `objet` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_depart` date NOT NULL,
  `mode_envoi` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mode_envoi_precision` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nombre_pieces` smallint UNSIGNED NOT NULL DEFAULT '0',
  `confidentialite` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'normal',
  `observations` text COLLATE utf8mb4_unicode_ci,
  `numero_archives` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_conserver_jusqu_au` date DEFAULT NULL,
  `archive_le` timestamp NULL DEFAULT NULL,
  `archive_par` bigint UNSIGNED DEFAULT NULL,
  `statut` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'enregistre',
  `enregistre_par` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `departs_annee_sequence_unique` (`annee`,`sequence`),
  UNIQUE KEY `departs_numero_unique` (`numero`),
  KEY `departs_groupe_index` (`groupe`),
  KEY `departs_courrier_id_index` (`courrier_id`),
  KEY `departs_reponse_id_index` (`reponse_id`),
  KEY `departs_statut_index` (`statut`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `diligences`
--

DROP TABLE IF EXISTS `diligences`;
CREATE TABLE IF NOT EXISTS `diligences` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `courrier_id` bigint UNSIGNED NOT NULL,
  `imputation_id` bigint UNSIGNED NOT NULL,
  `parent_diligence_id` bigint UNSIGNED DEFAULT NULL,
  `destinataire_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `destinataire_id` bigint UNSIGNED NOT NULL,
  `destinataire_libelle` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `responsable_personnel_id` bigint UNSIGNED DEFAULT NULL,
  `role` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'principal',
  `echeance` date NOT NULL,
  `statut` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'a_prendre_en_charge',
  `pris_en_charge_le` timestamp NULL DEFAULT NULL,
  `pris_en_charge_par` bigint UNSIGNED DEFAULT NULL,
  `solde_le` timestamp NULL DEFAULT NULL,
  `solde_par` bigint UNSIGNED DEFAULT NULL,
  `commentaire_solde` text COLLATE utf8mb4_unicode_ci,
  `annule_le` timestamp NULL DEFAULT NULL,
  `motif_annulation` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `diligences_code_unique` (`code`),
  KEY `diligences_courrier_id_foreign` (`courrier_id`),
  KEY `diligences_imputation_id_foreign` (`imputation_id`),
  KEY `diligences_pris_en_charge_par_foreign` (`pris_en_charge_par`),
  KEY `diligences_solde_par_foreign` (`solde_par`),
  KEY `diligences_statut_index` (`statut`),
  KEY `diligences_echeance_index` (`echeance`),
  KEY `diligences_responsable_personnel_id_index` (`responsable_personnel_id`),
  KEY `diligences_parent_diligence_id_index` (`parent_diligence_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `diligences`
--

INSERT INTO `diligences` (`id`, `code`, `courrier_id`, `imputation_id`, `parent_diligence_id`, `destinataire_type`, `destinataire_id`, `destinataire_libelle`, `responsable_personnel_id`, `role`, `echeance`, `statut`, `pris_en_charge_le`, `pris_en_charge_par`, `solde_le`, `solde_par`, `commentaire_solde`, `annule_le`, `motif_annulation`, `created_at`, `updated_at`) VALUES
(3, 'DL26-0003', 1, 2, NULL, 'agent', 13, 'Antonio Chapman KOUADIO', 13, 'principal', '2026-10-14', 'en_cours', '2026-10-12 05:10:55', 10, NULL, NULL, NULL, NULL, NULL, '2026-10-12 04:43:32', '2026-10-12 05:10:55');

-- --------------------------------------------------------

--
-- Structure de la table `diligence_actions`
--

DROP TABLE IF EXISTS `diligence_actions`;
CREATE TABLE IF NOT EXISTS `diligence_actions` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `diligence_id` bigint UNSIGNED NOT NULL,
  `libelle` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `personnel_id` bigint UNSIGNED DEFAULT NULL,
  `echeance` date DEFAULT NULL,
  `modalites` text COLLATE utf8mb4_unicode_ci,
  `conditions` text COLLATE utf8mb4_unicode_ci,
  `ordre` int UNSIGNED NOT NULL DEFAULT '0',
  `faite` tinyint(1) NOT NULL DEFAULT '0',
  `faite_le` timestamp NULL DEFAULT NULL,
  `faite_par` bigint UNSIGNED DEFAULT NULL,
  `statut_validation` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'a_valider',
  `valide_par` bigint UNSIGNED DEFAULT NULL,
  `valide_le` timestamp NULL DEFAULT NULL,
  `motif_refus` text COLLATE utf8mb4_unicode_ci,
  `cree_par` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `diligence_actions_diligence_id_index` (`diligence_id`),
  KEY `diligence_actions_statut_validation_index` (`statut_validation`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `diligence_actions`
--

INSERT INTO `diligence_actions` (`id`, `diligence_id`, `libelle`, `description`, `personnel_id`, `echeance`, `modalites`, `conditions`, `ordre`, `faite`, `faite_le`, `faite_par`, `statut_validation`, `valide_par`, `valide_le`, `motif_refus`, `cree_par`, `created_at`, `updated_at`) VALUES
(1, 3, 'Etablissement du courrier de désignation à envoyer aux EPN', NULL, NULL, '2026-10-12', NULL, NULL, 1, 1, '2026-10-12 16:31:01', 10, 'validee', 9, '2026-10-12 16:30:10', NULL, 10, '2026-10-12 13:03:28', '2026-10-12 16:31:01'),
(2, 3, 'Signature du courrier par le DG', NULL, NULL, '2026-10-12', NULL, NULL, 2, 1, '2026-10-12 16:31:14', 10, 'validee', 9, '2026-10-12 16:30:18', NULL, 10, '2026-10-12 13:04:24', '2026-10-12 16:31:14'),
(3, 3, 'Envoie du courrier aux EPN via mail et physiquement.', 'Distribution du courrier', NULL, '2026-10-13', NULL, NULL, 3, 0, NULL, NULL, 'validee', 9, '2026-10-12 16:30:25', NULL, 10, '2026-10-12 13:06:24', '2026-10-12 16:30:25');

-- --------------------------------------------------------

--
-- Structure de la table `diligence_alertes`
--

DROP TABLE IF EXISTS `diligence_alertes`;
CREATE TABLE IF NOT EXISTS `diligence_alertes` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `diligence_id` bigint UNSIGNED NOT NULL,
  `type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cle` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `envoye_le` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `diligence_alertes_diligence_id_type_cle_unique` (`diligence_id`,`type`,`cle`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `diligence_alertes`
--

INSERT INTO `diligence_alertes` (`id`, `diligence_id`, `type`, `cle`, `envoye_le`) VALUES
(1, 2, 'echeance', '2026-10-11', '2026-10-11 06:09:48'),
(2, 1, 'echeance', '2026-10-11', '2026-10-11 06:09:48'),
(3, 3, 'echeance', '2026-10-14', '2026-10-12 04:45:11'),
(12, 3, 'retard', '20261014-r0', '2026-10-15 05:00:02'),
(14, 3, 'retard', '20261014-r1', '2026-10-19 13:53:40'),
(15, 3, 'retard', '20261014-r2', '2026-10-22 12:51:14'),
(16, 3, 'retard', '20261014-r6', '2026-11-04 14:25:30'),
(17, 3, 'retard', '20261014-r7', '2026-11-07 18:01:49');

-- --------------------------------------------------------

--
-- Structure de la table `diligence_avis`
--

DROP TABLE IF EXISTS `diligence_avis`;
CREATE TABLE IF NOT EXISTS `diligence_avis` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `imputation_destinataire_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `commentaire` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `diligence_avis_user_id_foreign` (`user_id`),
  KEY `diligence_avis_historique_index` (`imputation_destinataire_id`,`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `diligence_delais`
--

DROP TABLE IF EXISTS `diligence_delais`;
CREATE TABLE IF NOT EXISTS `diligence_delais` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `diligence_id` bigint UNSIGNED NOT NULL,
  `demande_par` bigint UNSIGNED DEFAULT NULL,
  `echeance_actuelle` date NOT NULL,
  `echeance_souhaitee` date NOT NULL,
  `motif` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `statut` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en_attente',
  `decide_par` bigint UNSIGNED DEFAULT NULL,
  `decide_le` timestamp NULL DEFAULT NULL,
  `echeance_accordee` date DEFAULT NULL,
  `motif_decision` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `diligence_delais_diligence_id_index` (`diligence_id`),
  KEY `diligence_delais_statut_index` (`statut`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `directions`
--

DROP TABLE IF EXISTS `directions`;
CREATE TABLE IF NOT EXISTS `directions` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nom` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `directions_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `directions`
--

INSERT INTO `directions` (`id`, `code`, `nom`, `description`, `created_at`, `updated_at`) VALUES
(1, 'DSPQ', 'Direction du Suivi de la Performance et de la Qualité', 'Assure le suivi et l\'évaluation de la performance économique et opérationnelle et de la qualité du service public des établissements publics nationaux et structures assimilées (Article 22).', '2026-09-27 17:01:02', '2026-09-27 17:01:02'),
(2, 'DSAR', 'Direction de la Stratégie, de l\'Audit et des Risques', 'Définit et suit la stratégie de gestion du portefeuille, organise les missions d\'audit et assure le suivi des risques liés aux établissements publics nationaux (Article 23).', '2026-09-27 17:01:02', '2026-09-27 17:01:02'),
(3, 'DAJ', 'Direction des Affaires Juridiques', 'Élabore les projets de textes législatifs et réglementaires et assure l\'interprétation uniforme des textes relatifs aux établissements publics nationaux (Article 24).', '2026-09-27 17:01:02', '2026-09-27 17:01:02'),
(4, 'DI', 'Direction de l\'Informatique', 'Conçoit, développe et administre les applications, systèmes d\'information et outils numériques de suivi des établissements publics nationaux (Article 25).', '2026-09-27 17:01:02', '2026-09-27 17:01:02'),
(5, 'DPC', 'Direction du Personnel et de la Communication', 'Assure la gestion du personnel, la planification des besoins en formation et la communication interne et externe de la DGEPN (Article 26).', '2026-09-27 17:01:02', '2026-09-27 17:01:02');

-- --------------------------------------------------------

--
-- Structure de la table `dossier_infos`
--

DROP TABLE IF EXISTS `dossier_infos`;
CREATE TABLE IF NOT EXISTS `dossier_infos` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `piece_jointe_id` bigint UNSIGNED NOT NULL,
  `date_document` date DEFAULT NULL,
  `valide_jusqu_au` date DEFAULT NULL,
  `observation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `modifie_par` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `dossier_infos_piece_jointe_id_unique` (`piece_jointe_id`),
  KEY `dossier_infos_valide_jusqu_au_index` (`valide_jusqu_au`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `echelle_traitements`
--

DROP TABLE IF EXISTS `echelle_traitements`;
CREATE TABLE IF NOT EXISTS `echelle_traitements` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `grade_id` bigint UNSIGNED NOT NULL,
  `classe_id` bigint UNSIGNED NOT NULL,
  `echelon_id` bigint UNSIGNED NOT NULL,
  `indice` smallint UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `echelle_traitements_grade_id_classe_id_echelon_id_unique` (`grade_id`,`classe_id`,`echelon_id`),
  KEY `echelle_traitements_classe_id_foreign` (`classe_id`),
  KEY `echelle_traitements_echelon_id_foreign` (`echelon_id`)
) ENGINE=InnoDB AUTO_INCREMENT=154 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `echelle_traitements`
--

INSERT INTO `echelle_traitements` (`id`, `grade_id`, `classe_id`, `echelon_id`, `indice`) VALUES
(1, 1, 1, 1, 1960),
(2, 2, 1, 1, 1315),
(3, 3, 1, 1, 965),
(4, 4, 1, 1, 895),
(5, 5, 1, 1, 830),
(6, 6, 1, 1, 680),
(7, 7, 1, 1, 565),
(8, 8, 1, 1, 485),
(9, 9, 1, 1, 475),
(10, 10, 1, 1, 460),
(11, 11, 1, 1, 440),
(12, 12, 1, 1, 415),
(13, 1, 1, 2, 2015),
(14, 2, 1, 2, 1360),
(15, 3, 1, 2, 1005),
(16, 4, 1, 2, 940),
(17, 5, 1, 2, 870),
(18, 6, 1, 2, 730),
(19, 7, 1, 2, 595),
(20, 8, 1, 2, 510),
(21, 9, 1, 2, 485),
(22, 10, 1, 2, 470),
(23, 11, 1, 2, 450),
(24, 12, 1, 2, 420),
(25, 1, 1, 3, 2090),
(26, 2, 1, 3, 1410),
(27, 3, 1, 3, 1070),
(28, 4, 1, 3, 1000),
(29, 5, 1, 3, 915),
(30, 6, 1, 3, 785),
(31, 7, 1, 3, 625),
(32, 8, 1, 3, 535),
(33, 9, 1, 3, 495),
(34, 10, 1, 3, 480),
(35, 11, 1, 3, 460),
(36, 12, 1, 3, 425),
(37, 1, 1, 4, 2135),
(38, 2, 1, 4, 1485),
(39, 3, 1, 4, 1165),
(40, 4, 1, 4, 1100),
(41, 5, 1, 4, 990),
(42, 6, 1, 4, 840),
(43, 7, 1, 4, 640),
(44, 8, 1, 4, 560),
(45, 9, 1, 4, 515),
(46, 10, 1, 4, 490),
(47, 11, 1, 4, 470),
(48, 12, 1, 4, 435),
(49, 1, 2, 1, 2170),
(50, 2, 2, 1, 1580),
(51, 3, 2, 1, 1295),
(52, 4, 2, 1, 1220),
(53, 5, 2, 1, 1100),
(54, 6, 2, 1, 890),
(55, 7, 2, 1, 695),
(56, 8, 2, 1, 605),
(57, 9, 2, 1, 550),
(58, 10, 2, 1, 515),
(59, 11, 2, 1, 485),
(60, 12, 2, 1, 400),
(61, 1, 2, 2, 2255),
(62, 2, 2, 2, 1655),
(63, 3, 2, 2, 1390),
(64, 4, 2, 2, 1315),
(65, 5, 2, 2, 1190),
(66, 6, 2, 2, 950),
(67, 7, 2, 2, 715),
(68, 8, 2, 2, 630),
(69, 9, 2, 2, 570),
(70, 10, 2, 2, 525),
(71, 11, 2, 2, 495),
(72, 12, 2, 2, 465),
(73, 1, 2, 3, 2295),
(74, 2, 2, 3, 1765),
(75, 3, 2, 3, 1520),
(76, 4, 2, 3, 1440),
(77, 5, 2, 3, 1255),
(78, 6, 2, 3, 1000),
(79, 7, 2, 3, 740),
(80, 8, 2, 3, 650),
(81, 9, 2, 3, 580),
(82, 10, 2, 3, 545),
(83, 11, 2, 3, 510),
(84, 12, 2, 3, 475),
(85, 1, 3, 1, 2350),
(86, 2, 3, 1, 1880),
(87, 3, 3, 1, 1680),
(88, 4, 3, 1, 1605),
(89, 5, 3, 1, 1315),
(90, 6, 3, 1, 1085),
(91, 7, 3, 1, 815),
(92, 8, 3, 1, 705),
(93, 9, 3, 1, 625),
(94, 10, 3, 1, 580),
(95, 11, 3, 1, 535),
(96, 12, 3, 1, 495),
(97, 1, 3, 2, 2350),
(98, 2, 3, 2, 1955),
(99, 3, 3, 2, 1810),
(100, 4, 3, 2, 1735),
(101, 5, 3, 2, 1380),
(102, 6, 3, 2, 1135),
(103, 7, 3, 2, 835),
(104, 8, 3, 2, 725),
(105, 9, 3, 2, 640),
(106, 10, 3, 2, 590),
(107, 11, 3, 2, 550),
(108, 12, 3, 2, 510),
(109, 1, 3, 3, 2350),
(110, 2, 3, 3, 1980),
(111, 3, 3, 3, 1905),
(112, 4, 3, 3, 1890),
(113, 5, 3, 3, 1575),
(114, 6, 3, 3, 1185),
(115, 7, 3, 3, 860),
(116, 8, 3, 3, 755),
(117, 9, 3, 3, 650),
(118, 10, 3, 3, 600),
(119, 11, 3, 3, 560),
(120, 12, 3, 3, 525),
(121, 2, 4, 1, 2055),
(122, 3, 4, 1, 2040),
(123, 4, 4, 1, 2015),
(124, 5, 4, 1, 1735),
(125, 6, 4, 1, 1270),
(126, 7, 4, 1, 880),
(127, 8, 4, 1, 775),
(128, 9, 4, 1, 665),
(129, 10, 4, 1, 610),
(130, 11, 4, 1, 575),
(131, 12, 4, 1, 545),
(132, 2, 4, 2, 2130),
(133, 3, 4, 2, 2105),
(134, 4, 4, 2, 2065),
(135, 5, 4, 2, 1760),
(136, 6, 4, 2, 1300),
(137, 7, 4, 2, 900),
(138, 8, 4, 2, 790),
(139, 9, 4, 2, 680),
(140, 10, 4, 2, 625),
(141, 11, 4, 2, 585),
(142, 12, 4, 2, 550),
(143, 2, 4, 3, 2145),
(144, 3, 4, 3, 2115),
(145, 4, 4, 3, 2095),
(146, 5, 4, 3, 1790),
(147, 6, 4, 3, 1325),
(148, 7, 4, 3, 925),
(149, 8, 4, 3, 805),
(150, 9, 4, 3, 695),
(151, 10, 4, 3, 640),
(152, 11, 4, 3, 590),
(153, 12, 4, 3, 555);

-- --------------------------------------------------------

--
-- Structure de la table `echelons`
--

DROP TABLE IF EXISTS `echelons`;
CREATE TABLE IF NOT EXISTS `echelons` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `numero` tinyint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `echelons_numero_unique` (`numero`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `echelons`
--

INSERT INTO `echelons` (`id`, `numero`, `created_at`, `updated_at`) VALUES
(1, 1, '2026-10-10 07:49:56', '2026-10-10 07:49:56'),
(2, 2, '2026-10-10 07:49:56', '2026-10-10 07:49:56'),
(3, 3, '2026-10-10 07:49:56', '2026-10-10 07:49:56'),
(4, 4, '2026-10-10 07:49:56', '2026-10-10 07:49:56');

-- --------------------------------------------------------

--
-- Structure de la table `emplois`
--

DROP TABLE IF EXISTS `emplois`;
CREATE TABLE IF NOT EXISTS `emplois` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `famille_emploi_id` bigint UNSIGNED NOT NULL,
  `libelle` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `emplois_famille_emploi_id_foreign` (`famille_emploi_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `emplois`
--

INSERT INTO `emplois` (`id`, `famille_emploi_id`, `libelle`, `actif`, `created_at`, `updated_at`) VALUES
(1, 1, 'Ingénieur Informaticien option Génie Logiciel', 1, '2026-09-27 14:47:53', '2026-09-27 14:47:53'),
(2, 1, 'Ingénieur Informaticien option Réseaux Télécommunication', 1, '2026-09-27 14:48:51', '2026-09-27 14:48:51'),
(3, 1, 'Technicien Supérieur Informatique option Gestion', 1, '2026-09-27 14:49:35', '2026-09-27 14:49:35'),
(4, 1, 'Technicien Supérieur Informatique option Maintenance', 1, '2026-09-27 14:50:04', '2026-09-27 14:50:04');

-- --------------------------------------------------------

--
-- Structure de la table `exercices`
--

DROP TABLE IF EXISTS `exercices`;
CREATE TABLE IF NOT EXISTS `exercices` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `annee` smallint UNSIGNED NOT NULL,
  `libelle` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT '0',
  `mode_avancement` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'manuel',
  `cloture_le` timestamp NULL DEFAULT NULL,
  `cloture_par` bigint UNSIGNED DEFAULT NULL,
  `cloture_archive` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `exercices_annee_unique` (`annee`),
  KEY `exercices_cloture_par_foreign` (`cloture_par`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `exercices`
--

INSERT INTO `exercices` (`id`, `annee`, `libelle`, `date_debut`, `date_fin`, `actif`, `mode_avancement`, `cloture_le`, `cloture_par`, `cloture_archive`, `created_at`, `updated_at`) VALUES
(1, 2026, 'Feuille de route 2026', '2026-01-01', '2026-12-31', 1, 'sous_taches', NULL, NULL, NULL, '2026-09-26 12:15:57', '2026-10-08 11:27:05');

-- --------------------------------------------------------

--
-- Structure de la table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `familles_emploi`
--

DROP TABLE IF EXISTS `familles_emploi`;
CREATE TABLE IF NOT EXISTS `familles_emploi` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `libelle` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `familles_emploi_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `familles_emploi`
--

INSERT INTO `familles_emploi` (`id`, `code`, `libelle`, `actif`, `created_at`, `updated_at`) VALUES
(1, '001', 'Informatique', 1, '2026-09-26 07:00:25', '2026-09-26 07:00:25'),
(2, '002', 'Secrétariat', 1, '2026-09-26 07:00:25', '2026-09-26 07:00:25'),
(3, '003', 'Juridique', 1, '2026-09-26 07:00:25', '2026-09-27 14:45:39'),
(4, '004', 'Finance', 1, '2026-09-26 07:00:25', '2026-09-26 07:00:25'),
(5, '005', 'Communication', 1, '2026-09-26 07:00:25', '2026-09-26 07:00:25'),
(6, '006', 'Ressources Humaines', 1, '2026-09-26 07:00:25', '2026-09-26 07:00:25'),
(7, '007', 'Administration Générale', 1, '2026-09-26 07:00:25', '2026-09-26 07:00:25'),
(8, '008', 'Chargé d\'étude', 1, '2026-09-27 14:45:58', '2026-09-27 14:45:58');

-- --------------------------------------------------------

--
-- Structure de la table `fonctions`
--

DROP TABLE IF EXISTS `fonctions`;
CREATE TABLE IF NOT EXISTS `fonctions` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `libelle` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `categorie` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `niveau_mandat` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `titulaire_unique` tinyint(1) NOT NULL DEFAULT '0',
  `systeme` tinyint(1) NOT NULL DEFAULT '0',
  `ordre` smallint UNSIGNED NOT NULL DEFAULT '1',
  `actif` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fonctions_code_unique` (`code`),
  UNIQUE KEY `fonctions_libelle_unique` (`libelle`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `fonctions`
--

INSERT INTO `fonctions` (`id`, `code`, `libelle`, `categorie`, `niveau_mandat`, `titulaire_unique`, `systeme`, `ordre`, `actif`, `created_at`, `updated_at`) VALUES
(1, 'DG', 'Directeur Général', 'mandat', 'direction_generale', 1, 1, 1, 1, '2026-10-10 06:07:50', '2026-10-10 06:07:50'),
(2, 'DGA', 'Directeur Général Adjoint', 'mandat', 'dga', 1, 1, 2, 1, '2026-10-10 06:07:50', '2026-10-10 06:07:50'),
(3, 'DIRECTEUR', 'Directeur', 'mandat', 'direction', 1, 1, 3, 1, '2026-10-10 06:07:50', '2026-10-10 06:07:50'),
(4, 'SOUS_DIRECTEUR', 'Sous-directeur', 'mandat', 'sous_direction', 1, 1, 4, 1, '2026-10-10 06:07:50', '2026-10-10 06:07:50'),
(5, 'CHEF_SERVICE', 'Chef de service', 'mandat', 'service', 1, 1, 5, 1, '2026-10-10 06:07:50', '2026-10-10 06:07:50'),
(6, 'CHARGE_ETUDES', 'Chargé d\'études', 'direction_generale', NULL, 0, 1, 6, 1, '2026-10-10 06:07:50', '2026-10-10 06:07:50'),
(7, 'CONSEILLER', 'Conseiller', 'direction_generale', NULL, 0, 1, 7, 1, '2026-10-10 06:07:50', '2026-10-10 06:07:50'),
(8, 'AGENT', 'Agent', 'service', NULL, 0, 1, 8, 1, '2026-10-10 06:07:50', '2026-10-10 06:07:50'),
(9, 'TECH_SURFACE', 'Technicien de surface', 'service', NULL, 0, 0, 9, 1, '2026-10-10 06:07:50', '2026-10-10 06:07:50');

-- --------------------------------------------------------

--
-- Structure de la table `grades`
--

DROP TABLE IF EXISTS `grades`;
CREATE TABLE IF NOT EXISTS `grades` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ordre` smallint UNSIGNED NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `grades_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `grades`
--

INSERT INTO `grades` (`id`, `code`, `ordre`, `created_at`, `updated_at`) VALUES
(1, 'A7', 1, '2026-10-10 07:49:56', '2026-10-10 07:49:56'),
(2, 'A6', 2, '2026-10-10 07:49:56', '2026-10-10 07:49:56'),
(3, 'A5', 3, '2026-10-10 07:49:56', '2026-10-10 07:49:56'),
(4, 'A4', 4, '2026-10-10 07:49:56', '2026-10-10 07:49:56'),
(5, 'A3', 5, '2026-10-10 07:49:56', '2026-10-10 07:49:56'),
(6, 'B3', 6, '2026-10-10 07:49:56', '2026-10-10 07:49:56'),
(7, 'B1', 7, '2026-10-10 07:49:56', '2026-10-10 07:49:56'),
(8, 'C3', 8, '2026-10-10 07:49:56', '2026-10-10 07:49:56'),
(9, 'C2', 9, '2026-10-10 07:49:56', '2026-10-10 07:49:56'),
(10, 'C1', 10, '2026-10-10 07:49:56', '2026-10-10 07:49:56'),
(11, 'D2', 11, '2026-10-10 07:49:56', '2026-10-10 07:49:56'),
(12, 'D1', 12, '2026-10-10 07:49:56', '2026-10-10 07:49:56');

-- --------------------------------------------------------

--
-- Structure de la table `imputations`
--

DROP TABLE IF EXISTS `imputations`;
CREATE TABLE IF NOT EXISTS `imputations` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `courrier_id` bigint UNSIGNED NOT NULL,
  `parent_diligence_id` bigint UNSIGNED DEFAULT NULL,
  `imputee_par` bigint UNSIGNED DEFAULT NULL,
  `date_imputation` date NOT NULL,
  `delai_id` bigint UNSIGNED DEFAULT NULL,
  `delai_libelle` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `echeance` date NOT NULL,
  `instructions_particulieres` text COLLATE utf8mb4_unicode_ci,
  `observations` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `imputations_imputee_par_foreign` (`imputee_par`),
  KEY `imputations_delai_id_foreign` (`delai_id`),
  KEY `imputations_courrier_id_index` (`courrier_id`),
  KEY `imputations_parent_diligence_fk` (`parent_diligence_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `imputations`
--

INSERT INTO `imputations` (`id`, `courrier_id`, `parent_diligence_id`, `imputee_par`, `date_imputation`, `delai_id`, `delai_libelle`, `echeance`, `instructions_particulieres`, `observations`, `created_at`, `updated_at`) VALUES
(2, 1, NULL, 5, '2026-10-12', 3, '48 h', '2026-10-14', 'Merci de le traiter avec diligence.', NULL, '2026-10-12 04:43:32', '2026-10-12 04:43:32');

-- --------------------------------------------------------

--
-- Structure de la table `imputation_destinataires`
--

DROP TABLE IF EXISTS `imputation_destinataires`;
CREATE TABLE IF NOT EXISTS `imputation_destinataires` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `imputation_id` bigint UNSIGNED NOT NULL,
  `diligence_id` bigint UNSIGNED DEFAULT NULL,
  `destinataire_type` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `destinataire_id` bigint UNSIGNED NOT NULL,
  `destinataire_libelle` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `imputation_destinataires_imputation_id_foreign` (`imputation_id`),
  KEY `imputation_destinataires_diligence_id_foreign` (`diligence_id`),
  KEY `imputation_destinataires_cible_index` (`destinataire_type`,`destinataire_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `imputation_instruction`
--

DROP TABLE IF EXISTS `imputation_instruction`;
CREATE TABLE IF NOT EXISTS `imputation_instruction` (
  `imputation_id` bigint UNSIGNED NOT NULL,
  `instruction_id` bigint UNSIGNED NOT NULL,
  PRIMARY KEY (`imputation_id`,`instruction_id`),
  KEY `imputation_instruction_instruction_id_foreign` (`instruction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `imputation_instruction`
--

INSERT INTO `imputation_instruction` (`imputation_id`, `instruction_id`) VALUES
(2, 7);

-- --------------------------------------------------------

--
-- Structure de la table `instantanes_pilotage`
--

DROP TABLE IF EXISTS `instantanes_pilotage`;
CREATE TABLE IF NOT EXISTS `instantanes_pilotage` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `cle` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jour` date NOT NULL,
  `exercice_id` bigint UNSIGNED NOT NULL,
  `axe_id` bigint UNSIGNED DEFAULT NULL,
  `direction_id` bigint UNSIGNED DEFAULT NULL,
  `taches` int UNSIGNED NOT NULL DEFAULT '0',
  `terminees` int UNSIGNED NOT NULL DEFAULT '0',
  `en_cours` int UNSIGNED NOT NULL DEFAULT '0',
  `en_retard` int UNSIGNED NOT NULL DEFAULT '0',
  `non_justifies` int UNSIGNED NOT NULL DEFAULT '0',
  `avancement_moyen` decimal(5,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `instantanes_pilotage_cle_unique` (`cle`),
  KEY `instantanes_pilotage_jour_index` (`jour`),
  KEY `instantanes_pilotage_exercice_id_index` (`exercice_id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `instantanes_pilotage`
--

INSERT INTO `instantanes_pilotage` (`id`, `cle`, `jour`, `exercice_id`, `axe_id`, `direction_id`, `taches`, `terminees`, `en_cours`, `en_retard`, `non_justifies`, `avancement_moyen`, `created_at`, `updated_at`) VALUES
(1, '2026-10-11|1|a0|d0', '2026-10-11', 1, NULL, NULL, 118, 3, 2, 44, 42, 0.40, '2026-10-11 12:54:51', '2026-10-11 12:54:51'),
(2, '2026-10-11|1|a2|d0', '2026-10-11', 1, 2, NULL, 51, 3, 2, 5, 3, 1.00, '2026-10-11 12:54:51', '2026-10-11 12:54:51'),
(3, '2026-10-11|1|a3|d0', '2026-10-11', 1, 3, NULL, 36, 0, 0, 9, 9, 0.00, '2026-10-11 12:54:51', '2026-10-11 12:54:51'),
(4, '2026-10-11|1|a4|d0', '2026-10-11', 1, 4, NULL, 31, 0, 0, 30, 30, 0.00, '2026-10-11 12:54:51', '2026-10-11 12:54:51'),
(5, '2026-10-11|1|a0|d4', '2026-10-11', 1, NULL, 4, 19, 0, 0, 19, 19, 0.00, '2026-10-11 12:54:51', '2026-10-11 12:54:51'),
(6, '2026-10-12|1|a0|d0', '2026-10-12', 1, NULL, NULL, 118, 3, 3, 43, 42, 0.70, '2026-10-12 06:55:23', '2026-10-12 06:55:23'),
(7, '2026-10-12|1|a2|d0', '2026-10-12', 1, 2, NULL, 51, 3, 3, 4, 3, 1.60, '2026-10-12 06:55:23', '2026-10-12 06:55:23'),
(8, '2026-10-12|1|a3|d0', '2026-10-12', 1, 3, NULL, 36, 0, 0, 9, 9, 0.00, '2026-10-12 06:55:23', '2026-10-12 06:55:23'),
(9, '2026-10-12|1|a4|d0', '2026-10-12', 1, 4, NULL, 31, 0, 0, 30, 30, 0.00, '2026-10-12 06:55:23', '2026-10-12 06:55:23'),
(10, '2026-10-12|1|a0|d4', '2026-10-12', 1, NULL, 4, 19, 0, 0, 19, 19, 0.00, '2026-10-12 06:55:23', '2026-10-12 06:55:23'),
(12, '2026-10-19|1|a0|d0', '2026-10-19', 1, NULL, NULL, 118, 3, 3, 45, 44, 0.70, '2026-10-19 13:51:26', '2026-10-19 13:51:26'),
(13, '2026-10-19|1|a2|d0', '2026-10-19', 1, 2, NULL, 51, 3, 3, 5, 4, 1.60, '2026-10-19 13:51:26', '2026-10-19 13:51:26'),
(14, '2026-10-19|1|a3|d0', '2026-10-19', 1, 3, NULL, 36, 0, 0, 9, 9, 0.00, '2026-10-19 13:51:26', '2026-10-19 13:51:26'),
(15, '2026-10-19|1|a4|d0', '2026-10-19', 1, 4, NULL, 31, 0, 0, 31, 31, 0.00, '2026-10-19 13:51:26', '2026-10-19 13:51:26'),
(16, '2026-10-19|1|a0|d4', '2026-10-19', 1, NULL, 4, 19, 0, 0, 19, 19, 0.00, '2026-10-19 13:51:26', '2026-10-19 13:51:26'),
(17, '2026-10-22|1|a0|d0', '2026-10-22', 1, NULL, NULL, 118, 3, 3, 45, 44, 0.70, '2026-10-22 12:54:56', '2026-10-22 12:54:56'),
(18, '2026-10-22|1|a2|d0', '2026-10-22', 1, 2, NULL, 51, 3, 3, 5, 4, 1.60, '2026-10-22 12:54:56', '2026-10-22 12:54:56'),
(19, '2026-10-22|1|a3|d0', '2026-10-22', 1, 3, NULL, 36, 0, 0, 9, 9, 0.00, '2026-10-22 12:54:56', '2026-10-22 12:54:56'),
(20, '2026-10-22|1|a4|d0', '2026-10-22', 1, 4, NULL, 31, 0, 0, 31, 31, 0.00, '2026-10-22 12:54:56', '2026-10-22 12:54:56'),
(21, '2026-10-22|1|a0|d4', '2026-10-22', 1, NULL, 4, 19, 0, 0, 19, 19, 0.00, '2026-10-22 12:54:56', '2026-10-22 12:54:56'),
(22, '2026-11-07|1|a0|d0', '2026-11-07', 1, NULL, NULL, 118, 3, 3, 46, 45, 0.70, '2026-11-07 16:59:34', '2026-11-07 16:59:34'),
(23, '2026-11-07|1|a2|d0', '2026-11-07', 1, 2, NULL, 51, 3, 3, 5, 4, 1.60, '2026-11-07 16:59:34', '2026-11-07 16:59:34'),
(24, '2026-11-07|1|a3|d0', '2026-11-07', 1, 3, NULL, 36, 0, 0, 10, 10, 0.00, '2026-11-07 16:59:34', '2026-11-07 16:59:34'),
(25, '2026-11-07|1|a4|d0', '2026-11-07', 1, 4, NULL, 31, 0, 0, 31, 31, 0.00, '2026-11-07 16:59:34', '2026-11-07 16:59:34'),
(26, '2026-11-07|1|a0|d4', '2026-11-07', 1, NULL, 4, 19, 0, 0, 19, 19, 0.00, '2026-11-07 16:59:34', '2026-11-07 16:59:34'),
(27, '2026-11-08|1|a0|d0', '2026-11-08', 1, NULL, NULL, 118, 3, 3, 46, 45, 0.70, '2026-11-08 00:10:23', '2026-11-08 00:10:23'),
(28, '2026-11-08|1|a2|d0', '2026-11-08', 1, 2, NULL, 51, 3, 3, 5, 4, 1.60, '2026-11-08 00:10:23', '2026-11-08 00:10:23'),
(29, '2026-11-08|1|a3|d0', '2026-11-08', 1, 3, NULL, 36, 0, 0, 10, 10, 0.00, '2026-11-08 00:10:23', '2026-11-08 00:10:23'),
(30, '2026-11-08|1|a4|d0', '2026-11-08', 1, 4, NULL, 31, 0, 0, 31, 31, 0.00, '2026-11-08 00:10:23', '2026-11-08 00:10:23'),
(31, '2026-11-08|1|a0|d4', '2026-11-08', 1, NULL, 4, 19, 0, 0, 19, 19, 0.00, '2026-11-08 00:10:23', '2026-11-08 00:10:23');

-- --------------------------------------------------------

--
-- Structure de la table `instructions_courrier`
--

DROP TABLE IF EXISTS `instructions_courrier`;
CREATE TABLE IF NOT EXISTS `instructions_courrier` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `libelle` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ordre` smallint UNSIGNED NOT NULL DEFAULT '1',
  `actif` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `instructions_courrier_libelle_unique` (`libelle`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `instructions_courrier`
--

INSERT INTO `instructions_courrier` (`id`, `libelle`, `ordre`, `actif`, `created_at`, `updated_at`) VALUES
(1, 'Pour instruction', 1, 1, '2026-10-09 10:48:41', '2026-10-09 10:48:41'),
(2, 'Me représenter', 2, 1, '2026-10-09 10:48:41', '2026-10-09 10:48:41'),
(3, 'Allocution à préparer', 3, 1, '2026-10-09 10:48:41', '2026-10-09 10:48:41'),
(4, 'Participation / Invitation', 4, 1, '2026-10-09 10:48:41', '2026-10-09 10:48:41'),
(5, 'M\'en parler', 5, 1, '2026-10-09 10:48:41', '2026-10-09 10:48:41'),
(6, 'Courrier réponse à rédiger', 6, 1, '2026-10-09 10:48:41', '2026-10-09 10:48:41'),
(7, 'Nécessaire à faire', 7, 1, '2026-10-09 10:48:41', '2026-10-09 10:48:41'),
(8, 'Me voir', 8, 1, '2026-10-09 10:48:41', '2026-10-09 10:48:41'),
(9, 'Accord', 9, 1, '2026-10-09 10:48:41', '2026-10-09 10:48:41'),
(10, 'Pour information', 10, 1, '2026-10-09 10:48:41', '2026-10-09 10:48:41'),
(11, 'Pour suivi', 11, 1, '2026-10-09 10:48:41', '2026-10-09 10:48:41'),
(12, 'Archiver', 12, 1, '2026-10-09 10:48:41', '2026-10-09 10:48:41');

-- --------------------------------------------------------

--
-- Structure de la table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `journal_activites`
--

DROP TABLE IF EXISTS `journal_activites`;
CREATE TABLE IF NOT EXISTS `journal_activites` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `acteur_nom` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `module` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `evenement` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sujet_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sujet_id` bigint UNSIGNED DEFAULT NULL,
  `parent_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_id` bigint UNSIGNED DEFAULT NULL,
  `resume` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `details` json DEFAULT NULL,
  `ip` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `journal_activites_sujet_type_sujet_id_index` (`sujet_type`,`sujet_id`),
  KEY `journal_activites_parent_type_parent_id_index` (`parent_type`,`parent_id`),
  KEY `journal_activites_module_evenement_index` (`module`,`evenement`),
  KEY `journal_activites_created_at_index` (`created_at`),
  KEY `journal_activites_user_id_index` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=151 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `journal_activites`
--

INSERT INTO `journal_activites` (`id`, `created_at`, `user_id`, `acteur_nom`, `module`, `evenement`, `sujet_type`, `sujet_id`, `parent_type`, `parent_id`, `resume`, `details`, `ip`) VALUES
(1, '2026-10-08 17:57:43', 1, 'Paul AKON', 'fichiers', 'depot', 'piece_jointe', 1, 'tache', 9, 'Fichier déposé : axe.xlsx (sous-tâche « Recensement »)', '{\"taille\": 12361, \"fichier\": \"axe.xlsx\", \"version\": 1}', '127.0.0.1'),
(2, '2026-10-09 16:16:30', 7, 'Yao Serge', 'courrier', 'enregistrement', 'courrier', 1, 'courrier', 1, 'Courrier A26-0001 enregistré : Agence Nationale d’Appui au Développement des Territoire — Désignation d’un point focal et de son suppléant pour le suivi de la plateforme nationale de reporting des établissements publics', '{\"numero\": \"A26-0001\", \"origine\": \"service_courrier\", \"confidentiel\": false}', '127.0.0.1'),
(3, '2026-10-09 16:16:30', 7, 'Yao Serge', 'courrier', 'scan', 'piece_jointe', 2, 'courrier', 1, 'Scan du courrier A26-0001 déposé : Courrier_test_ANADT_0412.pdf', NULL, '127.0.0.1'),
(4, '2026-10-09 16:23:23', 7, 'Yao Serge', 'courrier', 'telechargement', NULL, NULL, 'courrier', 1, 'Fiche de transmission du courrier A26-0001 générée', NULL, '127.0.0.1'),
(5, '2026-10-09 16:24:21', 7, 'Yao Serge', 'courrier', 'telechargement', 'piece_jointe', 2, 'courrier', 1, 'Scan du courrier A26-0001 téléchargé', NULL, '127.0.0.1'),
(6, '2026-10-09 16:40:52', 5, 'KAMAGATE Daouda', 'courrier', 'imputation', 'imputation', 1, 'courrier', 1, 'Courrier A26-0001 imputé : Direction de l\'Informatique (Associé pour copie), Jean-Baptiste AKON (Responsable principal) — échéance 11/10/2026', '{\"delai\": \"48 h\", \"echeance\": \"2026-10-11\", \"diligences\": [\"DL26-0001\", \"DL26-0002\"]}', '127.0.0.1'),
(7, '2026-10-09 16:43:11', 5, 'KAMAGATE Daouda', 'courrier', 'telechargement', NULL, NULL, 'courrier', 1, 'Fiche de transmission du courrier A26-0001 générée', NULL, '127.0.0.1'),
(8, '2026-10-10 06:24:22', 1, 'Paul AKON', 'personnel', 'affectation', 'personnel', 11, NULL, NULL, 'Mamadou Diabaté : intérim — Directeur - Direction de l\'Informatique', '{\"acte\": null, \"date\": \"2026-10-10\", \"type\": \"interim\", \"poste\": \"Directeur - Direction de l\'Informatique\", \"fonction\": \"Directeur\"}', '127.0.0.1'),
(9, '2026-10-10 06:55:35', 1, 'Paul AKON', 'personnel', 'fin_affectation', 'personnel', 11, 'personnel', 11, 'Fin de l\'intérim de Mamadou Diabaté : Directeur — Direction de l\'Informatique (10/10/2026) — Fin d\'intérim', '{\"type\": \"interim\", \"motif\": \"Fin d\'intérim\", \"mandat\": 10, \"date_fin\": \"2026-10-10\"}', '127.0.0.1'),
(10, '2026-10-10 07:02:26', 1, 'Paul AKON', 'personnel', 'fin_affectation', 'personnel', 11, 'personnel', 11, 'Fin de l\'intérim de Mamadou Diabaté : Directeur — Direction de l\'Informatique (10/10/2026) — Fin d\'intérim', '{\"type\": \"interim\", \"motif\": \"Fin d\'intérim\", \"mandat\": 10, \"date_fin\": \"2026-10-10\"}', '127.0.0.1'),
(11, '2026-10-10 08:50:26', 1, 'Paul AKON', 'personnel', 'dossier', 'piece_jointe', 3, 'personnel', 6, 'Document « CV » déposé dans le dossier de Ange BLETCHI : Courrier_test_ANADT_0412.pdf', NULL, '127.0.0.1'),
(12, '2026-10-10 08:50:53', 1, 'Paul AKON', 'personnel', 'dossier', 'piece_jointe', 4, 'personnel', 6, 'Document « Carte nationale d\'identité » déposé dans le dossier de Ange BLETCHI : Certificat de residence.pdf', NULL, '127.0.0.1'),
(13, '2026-10-10 08:51:58', 1, 'Paul AKON', 'personnel', 'dossier', 'piece_jointe', 5, 'personnel', 6, 'Document « Diplôme » déposé dans le dossier de Ange BLETCHI : Courrier_test_ANADT_0412.pdf', NULL, '127.0.0.1'),
(14, '2026-10-10 10:50:23', 2, 'N\'GUESSAN YVES', 'personnel', 'dossier', 'piece_jointe', 6, 'personnel', 3, 'Document « CV » déposé dans le dossier de Yves N\'Guessan : A26-0001.pdf', NULL, '127.0.0.1'),
(15, '2026-10-11 08:26:04', 1, 'Paul AKON', 'personnel', 'creation', 'personnel', 10, 'personnel', 10, 'Compte utilisateur créé pour Marie-Thérèse Aka (profil Agent)', '{\"profil\": \"AGENT\"}', '127.0.0.1'),
(16, '2026-10-11 08:26:04', 1, 'Paul AKON', 'personnel', 'creation', NULL, NULL, NULL, NULL, 'Création de comptes en masse : 1 créé(s), 0 ignoré(s) (profil Agent)', '{\"profil\": \"AGENT\"}', '127.0.0.1'),
(17, '2026-10-11 08:26:35', 1, 'Paul AKON', 'exports', 'export', NULL, NULL, NULL, NULL, 'Fichier des comptes créés (mots de passe provisoires) téléchargé : 1 compte(s)', NULL, '127.0.0.1'),
(18, '2026-10-11 12:53:51', 1, 'Paul AKON', 'reglages', 'modification', NULL, NULL, NULL, NULL, 'Réglage « Rédiger la synthèse avec l\'IA » : Non → Oui', '{\"cle\": \"synthese.ia_active\", \"apres\": true, \"avant\": false}', '127.0.0.1'),
(19, '2026-10-11 12:56:23', 1, 'Paul AKON', 'pilotage', 'synthese', 'synthese_pilotage', 1, NULL, NULL, 'Synthèse du pilotage générée (Toute la structure · Exercice 2026) : Modèles de phrases', NULL, '127.0.0.1'),
(20, '2026-10-11 13:06:03', 1, 'Paul AKON', 'pilotage', 'synthese', 'synthese_pilotage', 2, NULL, NULL, 'Synthèse du pilotage générée (Toute la structure · Exercice 2026) : Modèles de phrases', NULL, '127.0.0.1'),
(21, '2026-10-11 13:27:59', 1, 'Paul AKON', 'pilotage', 'synthese', 'synthese_pilotage', 3, NULL, NULL, 'Synthèse du pilotage générée (Toute la structure · Exercice 2026) : Rédigée par IA', NULL, '127.0.0.1'),
(22, '2026-10-11 13:30:25', 1, 'Paul AKON', 'exports', 'export', NULL, NULL, NULL, NULL, 'Note de synthèse PDF du pilotage (Toute la structure · Exercice 2026)', '{\"filtres\": {\"type\": \"annee\", \"portee\": \"global\", \"exercice_id\": \"1\", \"synthese_id\": \"3\"}}', '127.0.0.1'),
(23, '2026-10-11 13:42:49', 1, 'Paul AKON', 'pilotage', 'synthese', 'synthese_pilotage', 4, NULL, NULL, 'Synthèse du pilotage générée (Toute la structure · Exercice 2026) : Rédigée par IA', NULL, '127.0.0.1'),
(24, '2026-10-11 13:48:10', 1, 'Paul AKON', 'exports', 'export', NULL, NULL, NULL, NULL, 'Note de synthèse PDF du pilotage (Toute la structure · Exercice 2026)', '{\"filtres\": {\"type\": \"annee\", \"portee\": \"global\", \"exercice_id\": \"1\", \"synthese_id\": \"4\"}}', '127.0.0.1'),
(25, '2026-10-11 16:34:32', 1, 'Paul AKON', 'personnel', 'creation', 'personnel', 16, 'personnel', 16, 'Agent Mohamed FOFANA créé (matricule 900320M)', '{\"matricule\": \"900320M\"}', '127.0.0.1'),
(26, '2026-10-11 16:41:00', 1, 'Paul AKON', 'personnel', 'affectation', 'personnel', 13, 'personnel', 13, 'Chapman Bamba : Agent (service : Service Développement numérique)', '{\"acte\": \"RDGEPN-26-001\", \"date\": \"2026-10-11\", \"type\": null, \"poste\": \"Agent (service : Service Développement numérique)\", \"fonction\": \"Agent\"}', '127.0.0.1'),
(27, '2026-10-11 16:42:14', 1, 'Paul AKON', 'personnel', 'affectation', 'personnel', 11, 'personnel', 11, 'Mamadou Diabaté : titulaire — Chef de service - Service Développement numérique', '{\"acte\": \"RDGEPN-26-002\", \"date\": \"2026-10-11\", \"type\": \"titulaire\", \"poste\": \"Chef de service - Service Développement numérique\", \"fonction\": \"Chef de service\"}', '127.0.0.1'),
(28, '2026-10-11 16:50:46', 1, 'Paul AKON', 'personnel', 'modification', 'personnel', 11, 'personnel', 11, 'Fiche de Mamadou Diabaté modifiée : Civilité, Genre, Statut matrimonial, Nombre d\'enfants, Nom du père, Nom de la mère', '{\"champs\": {\"Genre\": \"∅ → 1\", \"Civilité\": \"∅ → 3\", \"Nom du père\": \"modifié\", \"Nom de la mère\": \"modifié\", \"Nombre d\'enfants\": \"∅ → 1\", \"Statut matrimonial\": \"∅ → 9\"}}', '127.0.0.1'),
(29, '2026-10-11 16:53:15', 1, 'Paul AKON', 'personnel', 'modification', 'personnel', 11, 'personnel', 11, 'Informations administratives de Mamadou Diabaté modifiées (grade_id, classe_id, echelon_id, date_premiere_prise_service, date_prise_service)', '{\"champs\": [\"grade_id\", \"classe_id\", \"echelon_id\", \"date_premiere_prise_service\", \"date_prise_service\"]}', '127.0.0.1'),
(30, '2026-10-11 16:54:25', 1, 'Paul AKON', 'personnel', 'modification', 'personnel', 11, 'personnel', 11, 'Fiche de Mamadou Diabaté modifiée : Type d\'agent', '{\"champs\": {\"Type d\'agent\": \"∅ → 1\"}}', '127.0.0.1'),
(31, '2026-10-11 16:57:49', 1, 'Paul AKON', 'personnel', 'modification', 'personnel', 13, 'personnel', 13, 'Fiche de Antonio Chapman KOUADIO modifiée : Nom, Prénom, Civilité, Genre, Statut matrimonial, Nombre d\'enfants, Nom du père, Nom de la mère, Type d\'agent', '{\"champs\": {\"Nom\": \"Bamba → KOUADIO\", \"Genre\": \"∅ → 1\", \"Prénom\": \"Chapman → Antonio Chapman\", \"Civilité\": \"∅ → 3\", \"Nom du père\": \"modifié\", \"Type d\'agent\": \"∅ → 1\", \"Nom de la mère\": \"modifié\", \"Nombre d\'enfants\": \"∅ → 0\", \"Statut matrimonial\": \"∅ → 9\"}}', '127.0.0.1'),
(32, '2026-10-11 16:58:10', 1, 'Paul AKON', 'personnel', 'modification', 'personnel', 13, 'personnel', 13, 'Fiche de Antonio Chapman KOUADIO modifiée : Emploi', '{\"champs\": {\"Emploi\": \"∅ → 4\"}}', '127.0.0.1'),
(33, '2026-10-11 16:59:50', 1, 'Paul AKON', 'personnel', 'modification', 'personnel', 13, 'personnel', 13, 'Informations administratives de Antonio Chapman KOUADIO modifiées (grade_id, classe_id, echelon_id, date_premiere_prise_service, date_prise_service)', '{\"champs\": [\"grade_id\", \"classe_id\", \"echelon_id\", \"date_premiere_prise_service\", \"date_prise_service\"]}', '127.0.0.1'),
(34, '2026-10-11 17:02:53', 1, 'Paul AKON', 'personnel', 'modification', 'personnel', 13, 'personnel', 13, 'Informations administratives de Antonio Chapman KOUADIO modifiées (date_premiere_prise_service, date_prise_service)', '{\"champs\": [\"date_premiere_prise_service\", \"date_prise_service\"]}', '127.0.0.1'),
(35, '2026-10-11 17:16:00', 10, 'Chapman Bamba', 'sous_taches', 'creation', 'sous_tache', 4, 'tache', 8, 'Sous-tâche ajoutée : Réunion préalable de restitution', NULL, '127.0.0.1'),
(36, '2026-10-11 17:24:55', 9, 'Mamadou Diabaté', 'sous_taches', 'validation', 'sous_tache', 4, 'tache', 8, 'Sous-tâche validée : Réunion préalable de restitution', NULL, '127.0.0.1'),
(37, '2026-10-11 17:27:55', 10, 'Chapman Bamba', 'sous_taches', 'creation', 'sous_tache', 5, 'tache', 8, 'Sous-tâche ajoutée : Elaboration partiel du document de restitution', NULL, '127.0.0.1'),
(38, '2026-10-11 17:29:37', 10, 'Chapman Bamba', 'sous_taches', 'creation', 'sous_tache', 6, 'tache', 8, 'Sous-tâche ajoutée : Restitution des enseignements', NULL, '127.0.0.1'),
(39, '2026-10-11 17:31:44', 10, 'Chapman Bamba', 'sous_taches', 'coche', 'sous_tache', 4, 'tache', 8, 'Sous-tâche faite : Réunion préalable de restitution', NULL, '127.0.0.1'),
(40, '2026-10-11 17:33:58', 9, 'Mamadou Diabaté', 'sous_taches', 'validation', 'sous_tache', 5, 'tache', 8, 'Sous-tâche validée : Elaboration partiel du document de restitution', NULL, '127.0.0.1'),
(41, '2026-10-11 17:34:01', 9, 'Mamadou Diabaté', 'sous_taches', 'validation', 'sous_tache', 6, 'tache', 8, 'Sous-tâche validée : Restitution des enseignements', NULL, '127.0.0.1'),
(42, '2026-10-12 04:43:32', 5, 'KAMAGATE Daouda', 'courrier', 'imputation', 'imputation', 2, 'courrier', 1, 'Courrier A26-0001 imputé : Antonio Chapman KOUADIO (Responsable principal) — échéance 14/10/2026', '{\"delai\": \"48 h\", \"echeance\": \"2026-10-14\", \"diligences\": [\"DL26-0003\"]}', '127.0.0.1'),
(43, '2026-10-12 05:10:55', 10, 'Chapman Bamba', 'courrier', 'prise_en_charge', 'diligence', 3, 'courrier', 1, 'Diligence DL26-0003 prise en charge (courrier A26-0001)', NULL, '127.0.0.1'),
(44, '2026-10-12 06:56:05', 5, 'KAMAGATE Daouda', 'pilotage', 'synthese', 'synthese_pilotage', 5, NULL, NULL, 'Synthèse du pilotage générée (Toute la structure · Exercice 2026) : Rédigée par IA', NULL, '127.0.0.1'),
(45, '2026-10-12 13:03:29', 10, 'Chapman Bamba', 'courrier', 'traitement', 'diligence_action', 1, 'courrier', 1, 'Action ajoutée sur la diligence DL26-0003 : Etablissement du courrier de désignation à envoyer aux EPN', NULL, '127.0.0.1'),
(46, '2026-10-12 13:04:24', 10, 'Chapman Bamba', 'courrier', 'traitement', 'diligence_action', 2, 'courrier', 1, 'Action ajoutée sur la diligence DL26-0003 : Envoie du courrier aux Epn', NULL, '127.0.0.1'),
(47, '2026-10-12 13:05:17', 10, 'Chapman Bamba', 'courrier', 'traitement', 'diligence_action', 2, 'courrier', 1, 'Action modifiée sur la diligence DL26-0003 : Signature du courrier par le DG', NULL, '127.0.0.1'),
(48, '2026-10-12 13:06:24', 10, 'Chapman Bamba', 'courrier', 'traitement', 'diligence_action', 3, 'courrier', 1, 'Action ajoutée sur la diligence DL26-0003 : Envoie du courrier aux EPN via mail et physiquement.', NULL, '127.0.0.1'),
(49, '2026-10-12 16:30:10', 9, 'Mamadou Diabaté', 'courrier', 'traitement', 'diligence_action', 1, 'courrier', 1, 'Action validée : Etablissement du courrier de désignation à envoyer aux EPN (diligence DL26-0003)', NULL, '127.0.0.1'),
(50, '2026-10-12 16:30:18', 9, 'Mamadou Diabaté', 'courrier', 'traitement', 'diligence_action', 2, 'courrier', 1, 'Action validée : Signature du courrier par le DG (diligence DL26-0003)', NULL, '127.0.0.1'),
(51, '2026-10-12 16:30:25', 9, 'Mamadou Diabaté', 'courrier', 'traitement', 'diligence_action', 3, 'courrier', 1, 'Action validée : Envoie du courrier aux EPN via mail et physiquement. (diligence DL26-0003)', NULL, '127.0.0.1'),
(52, '2026-10-12 16:31:01', 10, 'Chapman Bamba', 'courrier', 'traitement', 'diligence_action', 1, 'courrier', 1, 'Action faite : Etablissement du courrier de désignation à envoyer aux EPN (diligence DL26-0003)', NULL, '127.0.0.1'),
(53, '2026-10-12 16:31:14', 10, 'Chapman Bamba', 'courrier', 'traitement', 'diligence_action', 2, 'courrier', 1, 'Action faite : Signature du courrier par le DG (diligence DL26-0003)', NULL, '127.0.0.1'),
(54, '2026-10-13 06:23:07', 10, 'Chapman Bamba', 'courrier', 'reponse', 'piece_jointe', 7, 'courrier', 1, 'Projet de réponse déposé (v1) : note_synthese_pilotage_2026.pdf', NULL, '127.0.0.1'),
(55, '2026-10-13 06:23:08', 10, 'Chapman Bamba', 'courrier', 'reponse', 'reponse_courrier', 1, 'courrier', 1, 'Réponse au courrier A26-0001 soumise au circuit de visa (Réponse à votre courrier réf. 0412/ANADT/DG/2026 — Désignation d’un point focal et de son suppléant pour le suivi de la plateforme nationale de reporting des établissement...) : 4 étape(s)', NULL, '127.0.0.1'),
(56, '2026-10-13 15:51:48', 9, 'Mamadou Diabaté', 'courrier', 'reponse', 'reponse_courrier', 1, 'courrier', 1, 'Réponse au courrier A26-0001 visée par Chef de service — Service Développement numérique', '{\"visa\": 1}', '127.0.0.1'),
(57, '2026-10-13 16:45:13', 3, 'Paul AKON', 'courrier', 'reponse', 'reponse_courrier', 1, 'courrier', 1, 'Réponse au courrier A26-0001 visée par Sous-directeur — Sous-Direction des Développements et des Etudes Informatiques', '{\"visa\": 2}', '127.0.0.1'),
(58, '2026-10-19 13:52:35', 1, 'Paul AKON', 'pilotage', 'synthese', 'synthese_pilotage', 6, NULL, NULL, 'Synthèse du pilotage générée (Toute la structure · Exercice 2026) : Rédigée par IA', NULL, '127.0.0.1'),
(59, '2026-10-22 12:50:28', 1, 'Paul AKON', 'exports', 'export', NULL, NULL, NULL, NULL, 'Export du registre du courrier arrivée : 1 courrier(s)', '{\"filtres\": []}', '127.0.0.1'),
(60, '2026-10-22 12:52:21', 1, 'Paul AKON', 'exports', 'export', NULL, NULL, NULL, NULL, 'Export « Toutes les tâches » (PDF) : 118 tâche(s)', '{\"type\": \"toutes\", \"format\": \"pdf\", \"lignes\": 118, \"filtres\": \"Aucun\", \"exercice\": \"Exercice 2026\"}', '127.0.0.1'),
(61, '2026-10-22 12:54:33', 1, 'Paul AKON', 'exports', 'export', NULL, NULL, NULL, NULL, 'Export « Tâches en cours » (PDF) : 3 tâche(s)', '{\"type\": \"en_cours\", \"format\": \"pdf\", \"lignes\": 3, \"filtres\": \"Aucun\", \"exercice\": \"Exercice 2026\"}', '127.0.0.1'),
(62, '2026-10-22 12:55:02', 1, 'Paul AKON', 'exports', 'export', NULL, NULL, NULL, NULL, 'Note de synthèse PDF du pilotage (Toute la structure · Exercice 2026)', '{\"filtres\": {\"type\": \"annee\", \"portee\": \"global\", \"exercice_id\": \"1\", \"synthese_id\": \"6\"}}', '127.0.0.1'),
(63, '2026-10-22 13:04:12', 1, 'Paul AKON', 'exports', 'export', NULL, NULL, NULL, NULL, 'Note de synthèse PDF du pilotage (Toute la structure · Exercice 2026)', '{\"filtres\": {\"type\": \"annee\", \"portee\": \"global\", \"exercice_id\": \"1\", \"synthese_id\": \"6\"}}', '127.0.0.1'),
(64, '2026-10-22 13:06:13', 1, 'Paul AKON', 'exports', 'export', NULL, NULL, NULL, NULL, 'Note de synthèse PDF du pilotage (Toute la structure · Exercice 2026)', '{\"filtres\": {\"type\": \"annee\", \"portee\": \"global\", \"exercice_id\": \"1\", \"synthese_id\": \"6\"}}', '127.0.0.1'),
(65, '2026-10-22 13:06:30', 1, 'Paul AKON', 'exports', 'export', NULL, NULL, NULL, NULL, 'Note de synthèse PDF du pilotage (Toute la structure · Exercice 2026)', '{\"filtres\": {\"type\": \"annee\", \"portee\": \"global\", \"exercice_id\": \"1\", \"synthese_id\": \"6\"}}', '127.0.0.1'),
(66, '2026-10-22 13:07:28', 1, 'Paul AKON', 'exports', 'export', NULL, NULL, NULL, NULL, 'Note de synthèse PDF du pilotage (Toute la structure · Exercice 2026)', '{\"filtres\": {\"type\": \"annee\", \"portee\": \"global\", \"exercice_id\": \"1\", \"synthese_id\": \"6\"}}', '127.0.0.1'),
(67, '2026-10-22 13:31:30', 1, 'Paul AKON', 'exports', 'export', NULL, NULL, NULL, NULL, 'Note de synthèse PDF du pilotage (Toute la structure · Exercice 2026)', '{\"filtres\": {\"type\": \"annee\", \"portee\": \"global\", \"exercice_id\": \"1\", \"synthese_id\": \"6\"}}', '127.0.0.1'),
(68, '2026-10-22 13:32:01', 1, 'Paul AKON', 'exports', 'export', NULL, NULL, NULL, NULL, 'Export « Tâches en retard » (PDF) : 45 tâche(s)', '{\"type\": \"en_retard\", \"format\": \"pdf\", \"lignes\": 45, \"filtres\": \"Aucun\", \"exercice\": \"Exercice 2026\"}', '127.0.0.1'),
(69, '2026-10-22 13:35:34', 1, 'Paul AKON', 'exports', 'export', NULL, NULL, NULL, NULL, 'Export « Tâches en retard » (PDF) : 45 tâche(s)', '{\"type\": \"en_retard\", \"format\": \"pdf\", \"lignes\": 45, \"filtres\": \"Aucun\", \"exercice\": \"Exercice 2026\"}', '127.0.0.1'),
(70, '2026-10-22 13:38:39', 1, 'Paul AKON', 'exports', 'export', NULL, NULL, NULL, NULL, 'Export « Tâches en retard » (PDF) : 45 tâche(s)', '{\"type\": \"en_retard\", \"format\": \"pdf\", \"lignes\": 45, \"filtres\": \"Aucun\", \"exercice\": \"Exercice 2026\"}', '127.0.0.1'),
(71, '2026-11-07 15:38:24', 1, 'Paul AKON', 'reglages', 'retablissement', NULL, NULL, NULL, NULL, 'Réglage « Rédiger la synthèse avec l\'IA » rétabli à sa valeur par défaut : Oui → Non', '{\"cle\": \"synthese.ia_active\", \"apres\": false, \"avant\": true}', '127.0.0.1'),
(72, '2026-11-07 15:38:37', NULL, NULL, 'reglages', 'modification', NULL, NULL, NULL, NULL, 'Réglage « Rédiger la synthèse avec l\'IA » : Non → Oui', '{\"cle\": \"synthese.ia_active\", \"apres\": true, \"avant\": false}', '127.0.0.1'),
(79, '2026-11-07 17:11:57', 1, 'Paul AKON', 'reglages', 'modification', NULL, NULL, NULL, NULL, 'Réglage « Délai d\'inactivité avant verrouillage » : 20 minutes → 2 minutes', '{\"cle\": \"securite.inactivite_minutes\", \"apres\": 2, \"avant\": 20}', '127.0.0.1'),
(80, '2026-11-07 17:14:11', 1, 'Paul AKON', 'reglages', 'modification', NULL, NULL, NULL, NULL, 'Réglage « Délai d\'inactivité avant verrouillage » : 2 minutes → 15 minutes', '{\"cle\": \"securite.inactivite_minutes\", \"apres\": 15, \"avant\": 2}', '127.0.0.1'),
(81, '2026-11-07 17:14:25', 1, 'Paul AKON', 'reglages', 'modification', NULL, NULL, NULL, NULL, 'Réglage « Délai d\'inactivité avant verrouillage » : 15 minutes → 1 minutes', '{\"cle\": \"securite.inactivite_minutes\", \"apres\": 1, \"avant\": 15}', '127.0.0.1'),
(82, '2026-11-07 17:15:12', 1, 'Paul AKON', 'session', 'verrouillage', 'user', 1, NULL, NULL, 'Session verrouillée : demandé par l\'utilisateur', NULL, '127.0.0.1'),
(83, '2026-11-07 17:20:11', 1, 'Paul AKON', 'session', 'deverrouillage', 'user', 1, NULL, NULL, 'Session déverrouillée', NULL, '127.0.0.1'),
(84, '2026-11-07 17:20:25', 1, 'Paul AKON', 'reglages', 'modification', NULL, NULL, NULL, NULL, 'Réglage « Délai d\'inactivité avant verrouillage » : 1 minutes → 15 minutes', '{\"cle\": \"securite.inactivite_minutes\", \"apres\": 15, \"avant\": 1}', '127.0.0.1'),
(85, '2026-11-07 18:07:31', 1, 'Paul AKON', 'exports', 'export', NULL, NULL, NULL, NULL, 'Export Excel du pilotage (Toute la structure · Exercice 2026)', '{\"filtres\": {\"type\": \"annee\", \"portee\": \"global\", \"exercice_id\": \"1\", \"synthese_id\": \"6\"}}', '127.0.0.1'),
(86, '2026-11-07 22:05:28', 1, 'Paul AKON', 'session', 'verrouillage', 'user', 1, NULL, NULL, 'Session verrouillée : inactivité détectée par le navigateur', NULL, '127.0.0.1'),
(87, '2026-11-07 22:08:34', 1, 'Paul AKON', 'session', 'deverrouillage', 'user', 1, NULL, NULL, 'Session déverrouillée', NULL, '127.0.0.1'),
(88, '2026-11-07 23:15:28', 1, 'Paul AKON', 'session', 'verrouillage', 'user', 1, NULL, NULL, 'Session verrouillée : inactivité détectée par le navigateur', NULL, '127.0.0.1'),
(102, '2026-11-07 23:20:59', 1, 'Paul AKON', 'session', 'deverrouillage', 'user', 1, NULL, NULL, 'Session déverrouillée', NULL, '127.0.0.1'),
(111, '2026-11-07 23:54:25', 1, 'Paul AKON', 'session', 'verrouillage', 'user', 1, NULL, NULL, 'Session verrouillée : inactivité détectée par le navigateur', NULL, '127.0.0.1'),
(112, '2026-11-07 23:57:27', 1, 'Paul AKON', 'session', 'deverrouillage', 'user', 1, NULL, NULL, 'Session déverrouillée', NULL, '127.0.0.1'),
(121, '2026-11-08 00:39:34', 1, 'Paul AKON', 'session', 'verrouillage', 'user', 1, NULL, NULL, 'Session verrouillée : inactivité détectée par le navigateur', NULL, '127.0.0.1'),
(122, '2026-11-08 00:40:04', 1, 'Paul AKON', 'session', 'deverrouillage', 'user', 1, NULL, NULL, 'Session déverrouillée', NULL, '127.0.0.1'),
(123, '2026-11-08 00:46:39', 1, 'Paul AKON', 'taches_ponctuelles', 'creation', 'tache_ponctuelle', 19, NULL, NULL, 'Tâche ponctuelle créée : TP26-0001 — Mise en place du comité de cadrage', NULL, '127.0.0.1'),
(124, '2026-11-08 00:55:52', 1, 'Paul AKON', 'fichiers', 'depot', 'piece_jointe', 8, 'tache_ponctuelle', 19, 'Livrable déposé (tâche ponctuelle TP26-0001) : pilotage_2026_exercice_2026_20261107_180736.xlsx', NULL, '127.0.0.1'),
(125, '2026-11-08 00:56:27', 1, 'Paul AKON', 'fichiers', 'suppression', 'piece_jointe', 8, 'tache_ponctuelle', 19, 'Livrable retiré (tâche ponctuelle TP26-0001) : pilotage_2026_exercice_2026_20261107_180736.xlsx', NULL, '127.0.0.1'),
(126, '2026-11-08 01:04:52', 1, 'Paul AKON', 'fichiers', 'depot', 'piece_jointe', 9, 'tache_ponctuelle', 19, 'Livrable déposé (tâche ponctuelle TP26-0001) : pilotage_2026_exercice_2026_20261107_180736.xlsx', NULL, '127.0.0.1'),
(127, '2026-11-08 01:05:59', 1, 'Paul AKON', 'fichiers', 'suppression', 'piece_jointe', 9, 'tache_ponctuelle', 19, 'Livrable retiré (tâche ponctuelle TP26-0001) : pilotage_2026_exercice_2026_20261107_180736.xlsx', NULL, '127.0.0.1'),
(128, '2026-11-08 01:11:40', 1, 'Paul AKON', 'fichiers', 'depot', 'piece_jointe', 10, 'tache_ponctuelle', 19, 'Livrable déposé (tâche ponctuelle TP26-0001) : pilotage_2026_exercice_2026_20261107_180736.xlsx', NULL, '127.0.0.1'),
(129, '2026-11-08 01:11:50', 1, 'Paul AKON', 'fichiers', 'suppression', 'piece_jointe', 10, 'tache_ponctuelle', 19, 'Livrable retiré (tâche ponctuelle TP26-0001) : pilotage_2026_exercice_2026_20261107_180736.xlsx', NULL, '127.0.0.1'),
(130, '2026-11-08 01:11:59', 1, 'Paul AKON', 'fichiers', 'depot', 'piece_jointe', 11, 'tache_ponctuelle', 19, 'Livrable déposé (tâche ponctuelle TP26-0001) : pilotage_2026_exercice_2026_20261107_180736.xlsx', NULL, '127.0.0.1'),
(131, '2026-11-08 01:12:02', 1, 'Paul AKON', 'taches_ponctuelles', 'modification', 'tache_ponctuelle', 19, NULL, NULL, 'Tâche ponctuelle mise à jour : TP26-0001 (à faire, 0 %)', NULL, '127.0.0.1'),
(132, '2026-11-08 03:08:48', 1, 'Paul AKON', 'session', 'verrouillage', 'user', 1, NULL, NULL, 'Session verrouillée : inactivité détectée par le navigateur', NULL, '127.0.0.1'),
(133, '2026-11-08 08:47:24', 1, 'Paul AKON', 'session', 'verrouillage', 'user', 1, NULL, NULL, 'Session verrouillée : inactivité détectée par le navigateur', NULL, '127.0.0.1'),
(134, '2026-11-08 09:06:25', 1, 'Paul AKON', 'session', 'deverrouillage', 'user', 1, NULL, NULL, 'Session déverrouillée', NULL, '127.0.0.1'),
(135, '2026-11-08 10:00:41', 1, 'Paul AKON', 'session', 'verrouillage', 'user', 1, NULL, NULL, 'Session verrouillée : inactivité détectée par le navigateur', NULL, '127.0.0.1'),
(136, '2026-11-08 13:07:24', 1, 'Paul AKON', 'session', 'verrouillage', 'user', 1, NULL, NULL, 'Session verrouillée : inactivité détectée par le navigateur', NULL, '127.0.0.1'),
(137, '2026-11-08 13:10:19', 1, 'Paul AKON', 'session', 'deverrouillage', 'user', 1, NULL, NULL, 'Session déverrouillée', NULL, '127.0.0.1'),
(138, '2026-11-08 13:52:24', 1, 'Paul AKON', 'session', 'verrouillage', 'user', 1, NULL, NULL, 'Session verrouillée : inactivité détectée par le navigateur', NULL, '127.0.0.1'),
(139, '2026-11-08 14:08:50', 1, 'Paul AKON', 'session', 'deverrouillage', 'user', 1, NULL, NULL, 'Session déverrouillée', NULL, '127.0.0.1'),
(140, '2026-11-08 15:42:24', 1, 'Paul AKON', 'session', 'verrouillage', 'user', 1, NULL, NULL, 'Session verrouillée : inactivité détectée par le navigateur', NULL, '127.0.0.1'),
(141, '2026-11-08 19:44:28', 1, 'Paul AKON', 'session', 'verrouillage', 'user', 1, NULL, NULL, 'Session verrouillée : inactivité détectée par le navigateur', NULL, '127.0.0.1'),
(142, '2026-11-08 19:45:04', 1, 'Paul AKON', 'session', 'deverrouillage', 'user', 1, NULL, NULL, 'Session déverrouillée', NULL, '127.0.0.1'),
(143, '2026-11-08 20:04:16', 1, 'Paul AKON', 'session', 'verrouillage', 'user', 1, NULL, NULL, 'Session verrouillée : inactivité détectée par le navigateur', NULL, '127.0.0.1'),
(144, '2026-11-08 20:05:05', 1, 'Paul AKON', 'session', 'deverrouillage', 'user', 1, NULL, NULL, 'Session déverrouillée', NULL, '127.0.0.1'),
(145, '2026-11-08 21:05:01', 1, 'Paul AKON', 'session', 'verrouillage', 'user', 1, NULL, NULL, 'Session verrouillée : inactivité détectée par le navigateur', NULL, '127.0.0.1'),
(146, '2026-11-08 21:05:07', 1, 'Paul AKON', 'session', 'deverrouillage', 'user', 1, NULL, NULL, 'Session déverrouillée', NULL, '127.0.0.1'),
(147, '2026-11-08 21:41:10', 1, 'Paul AKON', 'session', 'verrouillage', 'user', 1, NULL, NULL, 'Session verrouillée : inactivité détectée par le navigateur', NULL, '127.0.0.1'),
(148, '2026-11-08 21:42:45', 1, 'Paul AKON', 'session', 'deverrouillage', 'user', 1, NULL, NULL, 'Session déverrouillée', NULL, '127.0.0.1'),
(149, '2026-11-08 22:16:10', 1, 'Paul AKON', 'session', 'verrouillage', 'user', 1, NULL, NULL, 'Session verrouillée : inactivité détectée par le navigateur', NULL, '127.0.0.1'),
(150, '2026-11-08 22:31:45', 1, 'Paul AKON', 'session', 'deverrouillage', 'user', 1, NULL, NULL, 'Session déverrouillée', NULL, '127.0.0.1');

-- --------------------------------------------------------

--
-- Structure de la table `listes_valeurs`
--

DROP TABLE IF EXISTS `listes_valeurs`;
CREATE TABLE IF NOT EXISTS `listes_valeurs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `liste` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `libelle` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ordre` smallint UNSIGNED NOT NULL DEFAULT '1',
  `actif` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `listes_valeurs_liste_libelle_unique` (`liste`,`libelle`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `listes_valeurs`
--

INSERT INTO `listes_valeurs` (`id`, `liste`, `code`, `libelle`, `ordre`, `actif`, `created_at`, `updated_at`) VALUES
(1, 'genre', NULL, 'Masculin', 1, 1, '2026-10-10 07:49:56', '2026-10-10 07:49:56'),
(2, 'genre', NULL, 'Féminin', 2, 1, '2026-10-10 07:49:56', '2026-10-10 07:49:56'),
(3, 'civilite', NULL, 'M.', 1, 1, '2026-10-10 07:49:56', '2026-10-10 07:49:56'),
(4, 'civilite', NULL, 'Mme', 2, 1, '2026-10-10 07:49:56', '2026-10-10 07:49:56'),
(5, 'civilite', NULL, 'Mlle', 3, 1, '2026-10-10 07:49:56', '2026-10-10 07:49:56'),
(6, 'civilite', NULL, 'Dr', 4, 1, '2026-10-10 07:49:56', '2026-10-10 07:49:56'),
(7, 'civilite', NULL, 'Pr', 5, 1, '2026-10-10 07:49:56', '2026-10-10 07:49:56'),
(8, 'civilite', NULL, 'Me', 6, 1, '2026-10-10 07:49:56', '2026-10-10 07:49:56'),
(9, 'statut_matrimonial', NULL, 'Célibataire', 1, 1, '2026-10-10 07:49:56', '2026-10-10 07:49:56'),
(10, 'statut_matrimonial', NULL, 'Marié(e)', 2, 1, '2026-10-10 07:49:56', '2026-10-10 07:49:56'),
(11, 'statut_matrimonial', NULL, 'Divorcé(e)', 3, 1, '2026-10-10 07:49:56', '2026-10-10 07:49:56'),
(12, 'statut_matrimonial', NULL, 'Veuf(ve)', 4, 1, '2026-10-10 07:49:56', '2026-10-10 07:49:56'),
(13, 'type_document', 'cv', 'CV', 1, 1, '2026-10-10 07:49:56', '2026-10-10 07:49:56'),
(14, 'type_document', 'cni', 'Carte nationale d\'identité', 2, 1, '2026-10-10 07:49:56', '2026-10-10 07:49:56'),
(15, 'type_document', 'diplome', 'Diplôme', 3, 1, '2026-10-10 07:49:56', '2026-10-10 07:49:56'),
(16, 'type_document', 'acte_naissance', 'Acte de naissance', 4, 1, '2026-10-10 07:49:56', '2026-10-10 07:49:56'),
(17, 'type_document', 'attestation', 'Attestation', 5, 1, '2026-10-10 07:49:56', '2026-10-10 07:49:56'),
(18, 'type_document', 'autre', 'Autre document', 6, 1, '2026-10-10 07:49:56', '2026-10-10 07:49:56');

-- --------------------------------------------------------

--
-- Structure de la table `mandats_responsables`
--

DROP TABLE IF EXISTS `mandats_responsables`;
CREATE TABLE IF NOT EXISTS `mandats_responsables` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `niveau` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `direction_id` bigint UNSIGNED DEFAULT NULL,
  `sous_direction_id` bigint UNSIGNED DEFAULT NULL,
  `service_id` bigint UNSIGNED DEFAULT NULL,
  `personnel_id` bigint UNSIGNED NOT NULL,
  `type` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'titulaire',
  `date_debut` date NOT NULL,
  `date_fin` date DEFAULT NULL,
  `reference_acte` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `motif_fin` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cree_par` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mandats_responsables_direction_id_foreign` (`direction_id`),
  KEY `mandats_responsables_sous_direction_id_foreign` (`sous_direction_id`),
  KEY `mandats_responsables_service_id_foreign` (`service_id`),
  KEY `mandats_responsables_cree_par_foreign` (`cree_par`),
  KEY `mandats_unite_index` (`niveau`,`direction_id`,`sous_direction_id`,`service_id`,`date_debut`),
  KEY `mandats_responsables_personnel_id_date_fin_index` (`personnel_id`,`date_fin`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `mandats_responsables`
--

INSERT INTO `mandats_responsables` (`id`, `niveau`, `direction_id`, `sous_direction_id`, `service_id`, `personnel_id`, `type`, `date_debut`, `date_fin`, `reference_acte`, `motif_fin`, `cree_par`, `created_at`, `updated_at`) VALUES
(1, 'sous_direction', NULL, 9, NULL, 1, 'titulaire', '2026-10-02', '2026-10-02', NULL, 'Fin de mandat', 1, '2026-10-02 07:48:21', '2026-10-02 18:58:48'),
(2, 'sous_direction', NULL, 9, NULL, 9, 'titulaire', '2026-10-02', NULL, NULL, NULL, 1, '2026-10-02 18:56:11', '2026-10-02 18:56:11'),
(4, 'sous_direction', NULL, 5, NULL, 6, 'titulaire', '2026-10-02', NULL, NULL, NULL, 1, '2026-10-02 19:01:02', '2026-10-02 19:01:02'),
(5, 'direction', 4, NULL, NULL, 10, 'interim', '2026-10-04', '2026-10-04', NULL, 'fin d\'intérim', 1, '2026-10-04 09:56:21', '2026-10-05 10:06:32'),
(6, 'direction', 4, NULL, NULL, 8, 'titulaire', '2026-10-05', '2026-10-08', NULL, 'remplacement', 1, '2026-10-05 10:06:32', '2026-10-09 15:22:36'),
(7, 'direction', 4, NULL, NULL, 15, 'titulaire', '2026-10-09', NULL, NULL, NULL, 1, '2026-10-09 15:22:36', '2026-10-09 15:22:36'),
(8, 'direction', 2, NULL, NULL, 8, 'titulaire', '2026-10-09', NULL, NULL, NULL, 1, '2026-10-09 15:23:45', '2026-10-09 15:23:45'),
(9, 'direction_generale', NULL, NULL, NULL, 14, 'titulaire', '2026-10-09', NULL, NULL, NULL, 1, '2026-10-09 15:24:38', '2026-10-09 15:24:38'),
(10, 'direction', 4, NULL, NULL, 11, 'interim', '2026-10-10', '2026-10-10', NULL, 'Fin d\'intérim', 1, '2026-10-10 06:24:22', '2026-10-10 06:55:35'),
(11, 'service', NULL, NULL, 2, 11, 'titulaire', '2026-10-11', NULL, 'RDGEPN-26-002', NULL, 1, '2026-10-11 16:42:14', '2026-10-11 16:42:14');

-- --------------------------------------------------------

--
-- Structure de la table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=75 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_09_26_000001_create_directions_table', 1),
(5, '2026_09_26_000002_create_sous_directions_table', 1),
(6, '2026_09_26_000003_create_services_table', 1),
(7, '2026_09_26_000004_create_types_agent_table', 1),
(8, '2026_09_26_000005_create_familles_emploi_table', 1),
(9, '2026_09_26_000006_create_emplois_table', 1),
(10, '2026_09_26_000006_create_profils_acces_table', 1),
(11, '2026_09_26_000007_create_personnel_table', 1),
(12, '2026_09_26_000008_create_axes_table', 1),
(13, '2026_09_26_000009_add_profil_fields_to_users_table', 1),
(14, '2026_09_26_000009_create_actions_cles_table', 1),
(15, '2026_09_26_000010_create_actions_mesurables_table', 1),
(16, '2026_09_26_000011_create_statuts_execution_table', 1),
(17, '2026_09_26_000012_create_situations_temporelles_table', 1),
(18, '2026_09_26_000013_create_priorites_table', 1),
(19, '2026_09_26_000014_create_taches_table', 1),
(20, '2026_09_26_000015_create_roles_affectation_table', 1),
(21, '2026_09_26_000016_create_affectations_table', 1),
(22, '2026_09_26_000017_create_tache_dependance_table', 1),
(23, '2026_09_26_000018_create_tache_historiques_table', 1),
(24, '2026_09_26_000019_create_canaux_notification_table', 1),
(25, '2026_09_26_000020_create_regles_alerte_table', 1),
(26, '2026_09_26_000021_create_regle_alerte_canal_table', 1),
(27, '2026_09_26_000022_create_alertes_envoyees_table', 1),
(28, '2026_09_26_000025_add_code_to_actions_mesurables_and_taches_tables', 2),
(29, '2026_09_26_000026_create_exercices_table', 3),
(30, '2026_09_26_000027_add_exercice_id_to_axes_table', 3),
(31, '2026_09_26_000028_add_slug_to_profils_acces_table', 4),
(32, '2026_09_26_000029_make_sous_direction_nullable_add_direction_to_services', 5),
(33, '2026_09_26_000030_add_photo_to_personnel_table', 6),
(34, '2026_10_02_120000_add_destinataires_to_regles_alerte_table', 7),
(35, '2026_10_03_100000_create_mandats_responsables_table', 8),
(36, '2026_10_02_100000_create_tache_justifications_table', 9),
(37, '2026_10_03_100000_add_echeance_au_envoi_to_alertes_envoyees_table', 10),
(38, '2026_10_03_100100_create_notifications_table', 10),
(39, '2026_10_04_100000_create_action_cle_direction_table', 11),
(40, '2026_10_08_000028_add_mode_avancement_to_exercices_table', 12),
(41, '2026_10_08_000029_create_sous_taches_table', 12),
(42, '2026_10_08_000030_create_pieces_jointes_table', 13),
(43, '2026_10_08_000031_create_journal_activites_table', 13),
(44, '2026_10_09_000032_add_cloture_to_exercices_table', 14),
(45, '2026_10_09_000033_add_reconduction_to_taches_table', 14),
(46, '2026_10_09_000034_axes_libelle_unique_par_exercice', 14),
(47, '2026_10_09_000035_create_reglages_table', 14),
(48, '2026_10_10_000036_create_types_courrier_table', 15),
(49, '2026_10_10_000037_create_urgences_courrier_table', 15),
(50, '2026_10_10_000038_create_courriers_table', 15),
(51, '2026_10_10_000039_add_profil_courrier', 15),
(52, '2026_10_10_000040_add_champs_fiche_to_courriers_table', 16),
(53, '2026_10_11_000041_create_referentiels_imputation_tables', 17),
(54, '2026_10_11_000042_create_imputations_diligences_tables', 17),
(55, '2026_10_12_000043_create_fonctions_table', 18),
(56, '2026_10_13_000044_create_listes_personnel_tables', 19),
(57, '2026_10_13_000045_add_champs_carriere_to_personnel_table', 19),
(58, '2026_10_13_000046_create_courrier_destinataires_associes_table', 20),
(59, '2026_10_14_000047_add_validation_to_sous_taches', 21),
(60, '2026_10_15_000048_create_diligence_traitement_tables', 21),
(61, '2026_10_15_000049_create_reponses_courrier_tables', 21),
(62, '2026_10_15_000050_create_departs_et_archivage', 21),
(63, '2026_10_16_000051_f10_archivage_comptes', 22),
(64, '2026_10_16_000052_f10_dossier_competences_alertes', 22),
(65, '2026_10_17_000053_pilotage_syntheses_instantanes', 23),
(66, '2026_10_18_000054_annulation_reponse_courrier', 24),
(67, '2026_10_19_000055_validation_cloture_taches', 25),
(68, '2026_11_07_220130_ajoute_visite_guidee_aux_users', 26),
(69, '2026_11_08_100000_create_taches_ponctuelles_table', 27),
(70, '2026_11_08_091537_ajoute_mot_de_passe_modifie_le_aux_users', 28),
(71, '2026_11_08_123920_ajoute_droits_aux_profils_acces', 29),
(72, '2026_11_08_125830_peuple_droits_profils_existants', 30),
(73, '2026_11_08_141138_complete_droits_dg_rd_modules_restants', 31),
(74, '2026_11_08_214206_etend_droits_rd_lecture_feuille_de_route', 32);

-- --------------------------------------------------------

--
-- Structure de la table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` bigint UNSIGNED NOT NULL,
  `data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `notifications`
--

INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES
('040052b7-4bcf-4c4c-ba2d-f5fbfb550fc5', 'App\\Notifications\\DiligenceNotification', 'user', 3, '{\"type\":\"diligence_reponse_a_viser\",\"diligence_id\":3,\"code\":\"DL26-0003\",\"libelle\":\"A26-0001\",\"titre\":\"R\\u00e9ponse \\u00e0 viser\",\"message\":\"La r\\u00e9ponse au courrier A26-0001 (\\u00ab D\\u00e9signation d\\u2019un point focal et de son suppl\\u00e9ant pour le suivi de la plateforme... \\u00bb) attend votre visa.\",\"ton\":\"attente\",\"lien\":\"http:\\/\\/127.0.0.1\\/etache\\/courrier\\/diligence\\/3#reponses\"}', '2026-10-13 16:44:30', '2026-10-13 15:51:48', '2026-10-13 16:44:30'),
('066b16cf-38e5-49a8-9e8c-083e4be6515b', 'App\\Notifications\\DiligenceNotification', 'user', 9, '{\"type\":\"diligence_retard\",\"diligence_id\":3,\"code\":\"DL26-0003\",\"libelle\":\"A26-0001\",\"titre\":\"Diligence en retard\",\"message\":\"La diligence DL26-0003 (courrier A26-0001, \\u00ab D\\u00e9signation d\\u2019un point focal et de son suppl\\u00e9ant pour le suivi de la plateforme... \\u00bb) est en retard : \\u00e9ch\\u00e9ance d\\u00e9pass\\u00e9e le 14\\/10\\/2026. Responsable : Antonio Chapman KOUADIO (5 jour(s) de retard).\",\"ton\":\"retard\",\"lien\":\"http:\\/\\/127.0.0.1\\/etache\\/courrier\\/diligence\\/3\"}', NULL, '2026-10-19 13:53:41', '2026-10-19 13:53:41'),
('10ba8fba-4635-4637-8909-051556b20e14', 'App\\Notifications\\DiligenceNotification', 'user', 10, '{\"type\":\"diligence_action_validee\",\"diligence_id\":3,\"code\":\"DL26-0003\",\"libelle\":\"A26-0001\",\"titre\":\"Action valid\\u00e9e\",\"message\":\"Votre action sur la diligence DL26-0003 a \\u00e9t\\u00e9 valid\\u00e9e : \\u00ab Envoie du courrier aux EPN via mail et physiquement. \\u00bb.\",\"ton\":\"avenir\",\"lien\":\"http:\\/\\/127.0.0.1\\/etache\\/courrier\\/diligence\\/3#traitement\"}', NULL, '2026-10-12 16:30:25', '2026-10-12 16:30:25'),
('140e2d6c-c70a-44ac-899e-33e46e8364dc', 'App\\Notifications\\SousTacheNotification', 'user', 10, '{\"type\":\"sous_tache_validee\",\"tache_id\":8,\"libelle\":\"TA26-008\",\"titre\":\"Sous-t\\u00e2che valid\\u00e9e\",\"message\":\"Votre sous-t\\u00e2che \\u00ab Elaboration partiel du document de restitution \\u00bb (t\\u00e2che TA26-008) a \\u00e9t\\u00e9 valid\\u00e9e.\",\"ton\":\"avenir\",\"lien\":\"http:\\/\\/127.0.0.1\\/etache\\/feuille-de-route\\/tache\\/8\\/details\"}', NULL, '2026-10-11 17:33:58', '2026-10-11 17:33:58'),
('38f5dc11-4dbb-434e-9126-f0b78b17447c', 'App\\Notifications\\DiligenceNotification', 'user', 9, '{\"type\":\"diligence_retard\",\"diligence_id\":3,\"code\":\"DL26-0003\",\"libelle\":\"A26-0001\",\"titre\":\"Diligence en retard\",\"message\":\"La diligence DL26-0003 (courrier A26-0001, \\u00ab D\\u00e9signation d\\u2019un point focal et de son suppl\\u00e9ant pour le suivi de la plateforme... \\u00bb) est en retard : \\u00e9ch\\u00e9ance d\\u00e9pass\\u00e9e le 14\\/10\\/2026. Responsable : Antonio Chapman KOUADIO (24 jour(s) de retard).\",\"ton\":\"retard\",\"lien\":\"http:\\/\\/127.0.0.1\\/etache\\/courrier\\/diligence\\/3\"}', NULL, '2026-11-07 18:01:49', '2026-11-07 18:01:49'),
('3a0083e3-3617-4826-9db7-9b199ed8ef4f', 'App\\Notifications\\DiligenceNotification', 'user', 9, '{\"type\":\"diligence_action_a_valider\",\"diligence_id\":3,\"code\":\"DL26-0003\",\"libelle\":\"A26-0001\",\"titre\":\"Action \\u00e0 valider\",\"message\":\"Une action du plan de traitement de la diligence DL26-0003 attend votre validation : \\u00ab Signature du courrier par le DG \\u00bb.\",\"ton\":\"attente\",\"lien\":\"http:\\/\\/127.0.0.1\\/etache\\/courrier\\/diligence\\/3#traitement\"}', NULL, '2026-10-12 13:05:17', '2026-10-12 13:05:17'),
('42f0da72-b12b-40a3-b6ce-36578fcb1b3d', 'App\\Notifications\\JustificationNotification', 'user', 1, '{\"type\":\"justification_decision\",\"justification_id\":1,\"tache_id\":9,\"code\":\"TA26-009\",\"libelle\":\"R\\u00e9diger le rapport de diagnostic consolidant le recensement et le benchmarking\",\"titre\":\"Demande approuv\\u00e9e\",\"message\":\"Votre demande de report pour la t\\u00e2che TA26-009 \\u00ab R\\u00e9diger le rapport de diagnostic consolidant le recensement et le benc... \\u00bb a \\u00e9t\\u00e9 approuv\\u00e9e par KAMAGATE DAOUDA. Nouvelle \\u00e9ch\\u00e9ance : 15\\/10\\/2026.\",\"ton\":\"justification\",\"lien\":\"http:\\/\\/127.0.0.1\\/etache\\/feuille-de-route\\/tache\\/9\\/details\"}', NULL, '2026-10-11 17:43:08', '2026-10-11 17:43:08'),
('444ff015-7973-4562-90ff-ce45c707bfc2', 'App\\Notifications\\DiligenceNotification', 'user', 9, '{\"type\":\"diligence_retard\",\"diligence_id\":3,\"code\":\"DL26-0003\",\"libelle\":\"A26-0001\",\"titre\":\"Diligence en retard\",\"message\":\"La diligence DL26-0003 (courrier A26-0001, \\u00ab D\\u00e9signation d\\u2019un point focal et de son suppl\\u00e9ant pour le suivi de la plateforme... \\u00bb) est en retard : \\u00e9ch\\u00e9ance d\\u00e9pass\\u00e9e le 14\\/10\\/2026. Responsable : Antonio Chapman KOUADIO (21 jour(s) de retard).\",\"ton\":\"retard\",\"lien\":\"http:\\/\\/127.0.0.1\\/etache\\/courrier\\/diligence\\/3\"}', NULL, '2026-11-04 14:25:31', '2026-11-04 14:25:31'),
('44636715-0a0c-4801-87dd-1b17f33ca564', 'App\\Notifications\\DiligenceNotification', 'user', 6, '{\"type\":\"diligence_information\",\"diligence_id\":3,\"code\":\"DL26-0003\",\"libelle\":\"A26-0001\",\"titre\":\"Courrier imput\\u00e9 \\u00e0 un de vos agents\",\"message\":\"Le courrier A26-0001 (\\u00ab D\\u00e9signation d\\u2019un point focal et de son suppl\\u00e9ant pour le suivi de la plateforme... \\u00bb) a \\u00e9t\\u00e9 imput\\u00e9 directement \\u00e0 Antonio Chapman KOUADIO. \\u00c9ch\\u00e9ance : 14\\/10\\/2026.\",\"ton\":\"justification\",\"lien\":\"http:\\/\\/127.0.0.1\\/etache\\/courrier\\/diligence\\/3\"}', NULL, '2026-10-12 04:43:32', '2026-10-12 04:43:32'),
('4c935ff5-0000-48fd-9c32-0b29234f17de', 'App\\Notifications\\DiligenceNotification', 'user', 10, '{\"type\":\"diligence_action_validee\",\"diligence_id\":3,\"code\":\"DL26-0003\",\"libelle\":\"A26-0001\",\"titre\":\"Action valid\\u00e9e\",\"message\":\"Votre action sur la diligence DL26-0003 a \\u00e9t\\u00e9 valid\\u00e9e : \\u00ab Signature du courrier par le DG \\u00bb.\",\"ton\":\"avenir\",\"lien\":\"http:\\/\\/127.0.0.1\\/etache\\/courrier\\/diligence\\/3#traitement\"}', NULL, '2026-10-12 16:30:18', '2026-10-12 16:30:18'),
('4d269de0-a9cf-4980-bd14-493484e99a3f', 'App\\Notifications\\DiligenceNotification', 'user', 10, '{\"type\":\"diligence_retard\",\"diligence_id\":3,\"code\":\"DL26-0003\",\"libelle\":\"A26-0001\",\"titre\":\"Diligence en retard\",\"message\":\"La diligence DL26-0003 (courrier A26-0001, \\u00ab D\\u00e9signation d\\u2019un point focal et de son suppl\\u00e9ant pour le suivi de la plateforme... \\u00bb) est en retard : \\u00e9ch\\u00e9ance d\\u00e9pass\\u00e9e le 14\\/10\\/2026. Retard : 24 jour(s).\",\"ton\":\"retard\",\"lien\":\"http:\\/\\/127.0.0.1\\/etache\\/courrier\\/diligence\\/3\"}', NULL, '2026-11-07 18:01:49', '2026-11-07 18:01:49'),
('4dce5fd5-d231-4ce9-9842-f3cea29fc0d7', 'App\\Notifications\\DiligenceNotification', 'user', 10, '{\"type\":\"diligence_retard\",\"diligence_id\":3,\"code\":\"DL26-0003\",\"libelle\":\"A26-0001\",\"titre\":\"Diligence en retard\",\"message\":\"La diligence DL26-0003 (courrier A26-0001, \\u00ab D\\u00e9signation d\\u2019un point focal et de son suppl\\u00e9ant pour le suivi de la plateforme... \\u00bb) est en retard : \\u00e9ch\\u00e9ance d\\u00e9pass\\u00e9e le 14\\/10\\/2026. Retard : 5 jour(s).\",\"ton\":\"retard\",\"lien\":\"http:\\/\\/127.0.0.1\\/etache\\/courrier\\/diligence\\/3\"}', NULL, '2026-10-19 13:53:41', '2026-10-19 13:53:41'),
('516905d7-53c3-4eaa-95c5-931e2e1b049a', 'App\\Notifications\\AlerteTacheNotification', 'user', 4, '{\"type\":\"alerte_tache\",\"tache_id\":10,\"code\":\"TA26-010\",\"libelle\":\"Soumettre le rapport du diagnostic au cabinet du  MPEEP pour validation\",\"titre\":\"\\u00c9ch\\u00e9ance imminente\",\"regle\":\"J-3\",\"message\":\"La t\\u00e2che TA26-010 \\u00ab Soumettre le rapport du diagnostic au cabinet du  MPEEP pour validation \\u00bb arrive \\u00e0 \\u00e9ch\\u00e9ance dans 3 jours (\\u00e9ch\\u00e9ance : 06\\/10\\/2026).\",\"ton\":\"avenir\",\"date_fin\":\"2026-10-06\",\"url\":\"http:\\/\\/127.0.0.1\\/etache\\/feuille-de-route\\/tache\\/10\\/details\"}', NULL, '2026-10-03 17:58:51', '2026-10-03 17:58:51'),
('51d00fbc-2e9a-48a7-84d2-c0a62ba1cfab', 'App\\Notifications\\AlerteTacheNotification', 'user', 3, '{\"type\":\"alerte_tache\",\"tache_id\":10,\"code\":\"TA26-010\",\"libelle\":\"Soumettre le rapport du diagnostic au cabinet du  MPEEP pour validation\",\"titre\":\"\\u00c9ch\\u00e9ance imminente\",\"regle\":\"J-3\",\"message\":\"La t\\u00e2che TA26-010 \\u00ab Soumettre le rapport du diagnostic au cabinet du  MPEEP pour validation \\u00bb arrive \\u00e0 \\u00e9ch\\u00e9ance dans 3 jours (\\u00e9ch\\u00e9ance : 05\\/10\\/2026).\",\"ton\":\"avenir\",\"date_fin\":\"2026-10-05\",\"url\":\"http:\\/\\/127.0.0.1\\/etache\\/feuille-de-route\\/tache\\/10\\/details\"}', '2026-10-03 18:36:34', '2026-10-02 19:55:33', '2026-10-03 18:36:34'),
('57c29c9d-9916-42b0-bae9-db667d2ab2b3', 'App\\Notifications\\AlerteTacheNotification', 'user', 4, '{\"type\":\"alerte_tache\",\"tache_id\":10,\"code\":\"TA26-010\",\"libelle\":\"Soumettre le rapport du diagnostic au cabinet du  MPEEP pour validation\",\"titre\":\"\\u00c9ch\\u00e9ance d\\u00e9pass\\u00e9e\",\"regle\":\"J+1\",\"message\":\"La t\\u00e2che TA26-010 \\u00ab Soumettre le rapport du diagnostic au cabinet du  MPEEP pour validation \\u00bb a d\\u00e9pass\\u00e9 son \\u00e9ch\\u00e9ance de 1 jour (\\u00e9ch\\u00e9ance : 06\\/10\\/2026).\",\"ton\":\"retard\",\"date_fin\":\"2026-10-06\",\"url\":\"http:\\/\\/127.0.0.1\\/etache\\/feuille-de-route\\/tache\\/10\\/details\"}', NULL, '2026-10-07 13:38:20', '2026-10-07 13:38:20'),
('57fa6186-148d-44bf-9e7e-157ab4bd1bf7', 'App\\Notifications\\SousTacheNotification', 'user', 9, '{\"type\":\"sous_tache_a_valider\",\"tache_id\":8,\"libelle\":\"TA26-008\",\"titre\":\"Sous-t\\u00e2che \\u00e0 valider\",\"message\":\"La sous-t\\u00e2che \\u00ab R\\u00e9union pr\\u00e9alable de restitution \\u00bb de la t\\u00e2che TA26-008 attend votre validation.\",\"ton\":\"attente\",\"lien\":\"http:\\/\\/127.0.0.1\\/etache\\/feuille-de-route\\/tache\\/8\\/details\"}', '2026-10-11 17:21:07', '2026-10-11 17:16:01', '2026-10-11 17:21:07'),
('5e1abbd5-4c83-4531-b9b5-b5bd1c890e7a', 'App\\Notifications\\AlerteTacheNotification', 'user', 3, '{\"type\":\"alerte_tache\",\"tache_id\":10,\"code\":\"TA26-010\",\"libelle\":\"Soumettre le rapport du diagnostic au cabinet du  MPEEP pour validation\",\"titre\":\"\\u00c9ch\\u00e9ance imminente\",\"regle\":\"J-3\",\"message\":\"La t\\u00e2che TA26-010 \\u00ab Soumettre le rapport du diagnostic au cabinet du  MPEEP pour validation \\u00bb arrive \\u00e0 \\u00e9ch\\u00e9ance dans 3 jours (\\u00e9ch\\u00e9ance : 05\\/10\\/2026).\",\"ton\":\"avenir\",\"date_fin\":\"2026-10-05\",\"url\":\"http:\\/\\/127.0.0.1\\/etache\\/feuille-de-route\\/tache\\/10\\/details\"}', '2026-10-03 18:36:34', '2026-10-02 19:49:01', '2026-10-03 18:36:34'),
('6a652efe-14d4-4f02-bf96-289270576061', 'App\\Notifications\\SousTacheNotification', 'user', 9, '{\"type\":\"sous_tache_a_valider\",\"tache_id\":8,\"libelle\":\"TA26-008\",\"titre\":\"Sous-t\\u00e2che \\u00e0 valider\",\"message\":\"La sous-t\\u00e2che \\u00ab Elaboration partiel du document de restitution \\u00bb de la t\\u00e2che TA26-008 attend votre validation.\",\"ton\":\"attente\",\"lien\":\"http:\\/\\/127.0.0.1\\/etache\\/feuille-de-route\\/tache\\/8\\/details\"}', '2026-10-11 17:32:37', '2026-10-11 17:27:55', '2026-10-11 17:32:37'),
('70975eec-ff42-4b01-aa72-3727c00a3713', 'App\\Notifications\\DiligenceNotification', 'user', 9, '{\"type\":\"diligence_retard\",\"diligence_id\":3,\"code\":\"DL26-0003\",\"libelle\":\"A26-0001\",\"titre\":\"Diligence en retard\",\"message\":\"La diligence DL26-0003 (courrier A26-0001, \\u00ab D\\u00e9signation d\\u2019un point focal et de son suppl\\u00e9ant pour le suivi de la plateforme... \\u00bb) est en retard : \\u00e9ch\\u00e9ance d\\u00e9pass\\u00e9e le 14\\/10\\/2026. Responsable : Antonio Chapman KOUADIO (8 jour(s) de retard).\",\"ton\":\"retard\",\"lien\":\"http:\\/\\/127.0.0.1\\/etache\\/courrier\\/diligence\\/3\"}', NULL, '2026-10-22 12:51:14', '2026-10-22 12:51:14'),
('72561596-c621-4af9-8a0f-f4bfd30756ef', 'App\\Notifications\\DiligenceNotification', 'user', 9, '{\"type\":\"diligence_action_a_valider\",\"diligence_id\":3,\"code\":\"DL26-0003\",\"libelle\":\"A26-0001\",\"titre\":\"Action \\u00e0 valider\",\"message\":\"Une action du plan de traitement de la diligence DL26-0003 attend votre validation : \\u00ab Etablissement du courrier de d\\u00e9signation \\u00e0 envoyer aux EPN \\u00bb.\",\"ton\":\"attente\",\"lien\":\"http:\\/\\/127.0.0.1\\/etache\\/courrier\\/diligence\\/3#traitement\"}', '2026-10-12 16:29:57', '2026-10-12 13:03:29', '2026-10-12 16:29:57'),
('7414ff23-5e38-4104-b084-28062496dcc9', 'App\\Notifications\\DiligenceNotification', 'user', 9, '{\"type\":\"diligence_information\",\"diligence_id\":3,\"code\":\"DL26-0003\",\"libelle\":\"A26-0001\",\"titre\":\"Courrier imput\\u00e9 \\u00e0 un de vos agents\",\"message\":\"Le courrier A26-0001 (\\u00ab D\\u00e9signation d\\u2019un point focal et de son suppl\\u00e9ant pour le suivi de la plateforme... \\u00bb) a \\u00e9t\\u00e9 imput\\u00e9 directement \\u00e0 Antonio Chapman KOUADIO. \\u00c9ch\\u00e9ance : 14\\/10\\/2026.\",\"ton\":\"justification\",\"lien\":\"http:\\/\\/127.0.0.1\\/etache\\/courrier\\/diligence\\/3\"}', NULL, '2026-10-12 04:43:32', '2026-10-12 04:43:32'),
('7623d538-ba53-4e07-8fb9-0d0cc55002cb', 'App\\Notifications\\DiligenceNotification', 'user', 10, '{\"type\":\"diligence_retard\",\"diligence_id\":3,\"code\":\"DL26-0003\",\"libelle\":\"A26-0001\",\"titre\":\"Diligence en retard\",\"message\":\"La diligence DL26-0003 (courrier A26-0001, \\u00ab D\\u00e9signation d\\u2019un point focal et de son suppl\\u00e9ant pour le suivi de la plateforme... \\u00bb) est en retard : \\u00e9ch\\u00e9ance d\\u00e9pass\\u00e9e le 14\\/10\\/2026. Retard : 8 jour(s).\",\"ton\":\"retard\",\"lien\":\"http:\\/\\/127.0.0.1\\/etache\\/courrier\\/diligence\\/3\"}', NULL, '2026-10-22 12:51:14', '2026-10-22 12:51:14'),
('7d753d37-7b42-4dc1-8928-23a83fa52c08', 'App\\Notifications\\DiligenceNotification', 'user', 6, '{\"type\":\"diligence_reponse_a_viser\",\"diligence_id\":3,\"code\":\"DL26-0003\",\"libelle\":\"A26-0001\",\"titre\":\"R\\u00e9ponse \\u00e0 viser\",\"message\":\"La r\\u00e9ponse au courrier A26-0001 (\\u00ab D\\u00e9signation d\\u2019un point focal et de son suppl\\u00e9ant pour le suivi de la plateforme... \\u00bb) attend votre visa.\",\"ton\":\"attente\",\"lien\":\"http:\\/\\/127.0.0.1\\/etache\\/courrier\\/diligence\\/3#reponses\"}', NULL, '2026-10-13 16:45:13', '2026-10-13 16:45:13'),
('7f01a5de-adeb-41c2-8142-7e2d8099c42c', 'App\\Notifications\\DiligenceNotification', 'user', 10, '{\"type\":\"diligence_action_validee\",\"diligence_id\":3,\"code\":\"DL26-0003\",\"libelle\":\"A26-0001\",\"titre\":\"Action valid\\u00e9e\",\"message\":\"Votre action sur la diligence DL26-0003 a \\u00e9t\\u00e9 valid\\u00e9e : \\u00ab Etablissement du courrier de d\\u00e9signation \\u00e0 envoyer aux EPN \\u00bb.\",\"ton\":\"avenir\",\"lien\":\"http:\\/\\/127.0.0.1\\/etache\\/courrier\\/diligence\\/3#traitement\"}', NULL, '2026-10-12 16:30:10', '2026-10-12 16:30:10'),
('82ecc4b0-d520-4ea3-981b-c66baaee6829', 'App\\Notifications\\AlerteTacheNotification', 'user', 4, '{\"type\":\"alerte_tache\",\"tache_id\":10,\"code\":\"TA26-010\",\"libelle\":\"Soumettre le rapport du diagnostic au cabinet du  MPEEP pour validation\",\"titre\":\"\\u00c9ch\\u00e9ance imminente\",\"regle\":\"J-3\",\"message\":\"La t\\u00e2che TA26-010 \\u00ab Soumettre le rapport du diagnostic au cabinet du  MPEEP pour validation \\u00bb arrive \\u00e0 \\u00e9ch\\u00e9ance dans 3 jours (\\u00e9ch\\u00e9ance : 05\\/10\\/2026).\",\"ton\":\"avenir\",\"date_fin\":\"2026-10-05\",\"url\":\"http:\\/\\/127.0.0.1\\/etache\\/feuille-de-route\\/tache\\/10\\/details\"}', NULL, '2026-10-02 19:55:33', '2026-10-02 19:55:33'),
('8baa4131-7496-488e-8739-92ffd0be6baa', 'App\\Notifications\\AlerteTacheNotification', 'user', 3, '{\"type\":\"alerte_tache\",\"tache_id\":10,\"code\":\"TA26-010\",\"libelle\":\"Soumettre le rapport du diagnostic au cabinet du  MPEEP pour validation\",\"titre\":\"\\u00c9ch\\u00e9ance imminente\",\"regle\":\"J-3\",\"message\":\"La t\\u00e2che TA26-010 \\u00ab Soumettre le rapport du diagnostic au cabinet du  MPEEP pour validation \\u00bb arrive \\u00e0 \\u00e9ch\\u00e9ance dans 3 jours (\\u00e9ch\\u00e9ance : 06\\/10\\/2026).\",\"ton\":\"avenir\",\"date_fin\":\"2026-10-06\",\"url\":\"http:\\/\\/127.0.0.1\\/etache\\/feuille-de-route\\/tache\\/10\\/details\"}', NULL, '2026-10-03 19:13:56', '2026-10-03 19:13:56'),
('8bb934f4-ad32-4ad4-bcd5-51c38c400bf3', 'App\\Notifications\\SousTacheNotification', 'user', 10, '{\"type\":\"sous_tache_validee\",\"tache_id\":8,\"libelle\":\"TA26-008\",\"titre\":\"Sous-t\\u00e2che valid\\u00e9e\",\"message\":\"Votre sous-t\\u00e2che \\u00ab Restitution des enseignements \\u00bb (t\\u00e2che TA26-008) a \\u00e9t\\u00e9 valid\\u00e9e.\",\"ton\":\"avenir\",\"lien\":\"http:\\/\\/127.0.0.1\\/etache\\/feuille-de-route\\/tache\\/8\\/details\"}', NULL, '2026-10-11 17:34:01', '2026-10-11 17:34:01'),
('9390e200-5840-4a9a-b7e4-b9fdb93c015c', 'App\\Notifications\\DiligenceNotification', 'user', 10, '{\"type\":\"diligence_imputation\",\"diligence_id\":3,\"code\":\"DL26-0003\",\"libelle\":\"A26-0001\",\"titre\":\"Courrier \\u00e0 traiter\",\"message\":\"Le courrier A26-0001 (\\u00ab D\\u00e9signation d\\u2019un point focal et de son suppl\\u00e9ant pour le suivi de la plateforme... \\u00bb) vous est imput\\u00e9 (Responsable principal). \\u00c9ch\\u00e9ance : 14\\/10\\/2026.\",\"ton\":\"justification\",\"lien\":\"http:\\/\\/127.0.0.1\\/etache\\/courrier\\/diligence\\/3\"}', NULL, '2026-10-12 04:43:32', '2026-10-12 04:43:32'),
('9b22845b-42b5-484e-8f26-193be39cc2bb', 'App\\Notifications\\AlerteTacheNotification', 'user', 4, '{\"type\":\"alerte_tache\",\"tache_id\":10,\"code\":\"TA26-010\",\"libelle\":\"Soumettre le rapport du diagnostic au cabinet du  MPEEP pour validation\",\"titre\":\"\\u00c9ch\\u00e9ance imminente\",\"regle\":\"J-3\",\"message\":\"La t\\u00e2che TA26-010 \\u00ab Soumettre le rapport du diagnostic au cabinet du  MPEEP pour validation \\u00bb arrive \\u00e0 \\u00e9ch\\u00e9ance dans 3 jours (\\u00e9ch\\u00e9ance : 05\\/10\\/2026).\",\"ton\":\"avenir\",\"date_fin\":\"2026-10-05\",\"url\":\"http:\\/\\/127.0.0.1\\/etache\\/feuille-de-route\\/tache\\/10\\/details\"}', NULL, '2026-10-02 20:50:01', '2026-10-02 20:50:01'),
('9ca9b295-feec-4895-9b36-850c8bf32f6b', 'App\\Notifications\\JustificationNotification', 'user', 2, '{\"type\":\"justification_decision\",\"justification_id\":3,\"tache_id\":5,\"code\":\"TA26-005\",\"libelle\":\"Consolider une matrice des marges d\'am\\u00e9lioration par th\\u00e9matique\",\"titre\":\"Demande approuv\\u00e9e\",\"message\":\"Votre demande de justification pour la t\\u00e2che TA26-005 \\u00ab Consolider une matrice des marges d\'am\\u00e9lioration par th\\u00e9matique \\u00bb a \\u00e9t\\u00e9 approuv\\u00e9e par KAMAGATE DAOUDA.\",\"ton\":\"justification\",\"lien\":\"http:\\/\\/127.0.0.1\\/etache\\/feuille-de-route\\/tache\\/5\\/details\"}', NULL, '2026-10-11 17:42:50', '2026-10-11 17:42:50'),
('9e4cb67d-5109-40be-979e-da98db8dba29', 'App\\Notifications\\SousTacheNotification', 'user', 9, '{\"type\":\"sous_tache_a_valider\",\"tache_id\":8,\"libelle\":\"TA26-008\",\"titre\":\"Sous-t\\u00e2che \\u00e0 valider\",\"message\":\"La sous-t\\u00e2che \\u00ab Restitution des enseignements \\u00bb de la t\\u00e2che TA26-008 attend votre validation.\",\"ton\":\"attente\",\"lien\":\"http:\\/\\/127.0.0.1\\/etache\\/feuille-de-route\\/tache\\/8\\/details\"}', NULL, '2026-10-11 17:29:37', '2026-10-11 17:29:37'),
('a5f9f482-9a8a-4141-8673-103c1da86905', 'App\\Notifications\\AlerteTacheNotification', 'user', 4, '{\"type\":\"alerte_tache\",\"tache_id\":109,\"code\":\"TA26-109\",\"libelle\":\"Diffuser le manuel aupr\\u00e8s des parties prenantes en amont du lancement\",\"titre\":\"Retard persistant\",\"regle\":\"J+7\",\"message\":\"La t\\u00e2che TA26-109 \\u00ab Diffuser le manuel aupr\\u00e8s des parties prenantes en amont du lancement \\u00bb a d\\u00e9pass\\u00e9 son \\u00e9ch\\u00e9ance de 7 jours (\\u00e9ch\\u00e9ance : 30\\/09\\/2026).\",\"ton\":\"retard\",\"date_fin\":\"2026-09-30\",\"url\":\"http:\\/\\/127.0.0.1\\/etache\\/feuille-de-route\\/tache\\/109\\/details\"}', NULL, '2026-10-07 13:38:39', '2026-10-07 13:38:39'),
('a69fc21f-610e-4fab-8726-366f2903d339', 'App\\Notifications\\DiligenceNotification', 'user', 10, '{\"type\":\"diligence_retard\",\"diligence_id\":3,\"code\":\"DL26-0003\",\"libelle\":\"A26-0001\",\"titre\":\"Diligence en retard\",\"message\":\"La diligence DL26-0003 (courrier A26-0001, \\u00ab D\\u00e9signation d\\u2019un point focal et de son suppl\\u00e9ant pour le suivi de la plateforme... \\u00bb) est en retard : \\u00e9ch\\u00e9ance d\\u00e9pass\\u00e9e le 14\\/10\\/2026. Retard : 1 jour(s).\",\"ton\":\"retard\",\"lien\":\"http:\\/\\/127.0.0.1\\/etache\\/courrier\\/diligence\\/3\"}', NULL, '2026-10-15 05:00:04', '2026-10-15 05:00:04'),
('a9f738aa-8bfd-4b05-9182-ffa7fd2dbead', 'App\\Notifications\\DiligenceNotification', 'user', 9, '{\"type\":\"diligence_action_a_valider\",\"diligence_id\":3,\"code\":\"DL26-0003\",\"libelle\":\"A26-0001\",\"titre\":\"Action \\u00e0 valider\",\"message\":\"Une action du plan de traitement de la diligence DL26-0003 attend votre validation : \\u00ab Envoie du courrier aux Epn \\u00bb.\",\"ton\":\"attente\",\"lien\":\"http:\\/\\/127.0.0.1\\/etache\\/courrier\\/diligence\\/3#traitement\"}', NULL, '2026-10-12 13:04:24', '2026-10-12 13:04:24'),
('b1fa3338-96e7-4690-89e4-e08c2765cef5', 'App\\Notifications\\DiligenceNotification', 'user', 9, '{\"type\":\"diligence_retard\",\"diligence_id\":3,\"code\":\"DL26-0003\",\"libelle\":\"A26-0001\",\"titre\":\"Diligence en retard\",\"message\":\"La diligence DL26-0003 (courrier A26-0001, \\u00ab D\\u00e9signation d\\u2019un point focal et de son suppl\\u00e9ant pour le suivi de la plateforme... \\u00bb) est en retard : \\u00e9ch\\u00e9ance d\\u00e9pass\\u00e9e le 14\\/10\\/2026. Responsable : Antonio Chapman KOUADIO (1 jour(s) de retard).\",\"ton\":\"retard\",\"lien\":\"http:\\/\\/127.0.0.1\\/etache\\/courrier\\/diligence\\/3\"}', NULL, '2026-10-15 05:00:05', '2026-10-15 05:00:05'),
('bdcf4044-5efd-456e-8c48-a68979905e59', 'App\\Notifications\\AlerteTacheNotification', 'user', 3, '{\"type\":\"alerte_tache\",\"tache_id\":10,\"code\":\"TA26-010\",\"libelle\":\"Soumettre le rapport du diagnostic au cabinet du  MPEEP pour validation\",\"titre\":\"\\u00c9ch\\u00e9ance d\\u00e9pass\\u00e9e\",\"regle\":\"J+1\",\"message\":\"La t\\u00e2che TA26-010 \\u00ab Soumettre le rapport du diagnostic au cabinet du  MPEEP pour validation \\u00bb a d\\u00e9pass\\u00e9 son \\u00e9ch\\u00e9ance de 1 jour (\\u00e9ch\\u00e9ance : 06\\/10\\/2026).\",\"ton\":\"retard\",\"date_fin\":\"2026-10-06\",\"url\":\"http:\\/\\/127.0.0.1\\/etache\\/feuille-de-route\\/tache\\/10\\/details\"}', NULL, '2026-10-07 13:38:20', '2026-10-07 13:38:20'),
('c00e1ce1-51d7-492d-a2ea-2fe6696a0a51', 'App\\Notifications\\AlerteTacheNotification', 'user', 4, '{\"type\":\"alerte_tache\",\"tache_id\":10,\"code\":\"TA26-010\",\"libelle\":\"Soumettre le rapport du diagnostic au cabinet du  MPEEP pour validation\",\"titre\":\"\\u00c9ch\\u00e9ance imminente\",\"regle\":\"J-3\",\"message\":\"La t\\u00e2che TA26-010 \\u00ab Soumettre le rapport du diagnostic au cabinet du  MPEEP pour validation \\u00bb arrive \\u00e0 \\u00e9ch\\u00e9ance dans 3 jours (\\u00e9ch\\u00e9ance : 06\\/10\\/2026).\",\"ton\":\"avenir\",\"date_fin\":\"2026-10-06\",\"url\":\"http:\\/\\/127.0.0.1\\/etache\\/feuille-de-route\\/tache\\/10\\/details\"}', NULL, '2026-10-03 19:13:56', '2026-10-03 19:13:56'),
('db491601-430f-4a79-bc7f-f1a36c86e68c', 'App\\Notifications\\SousTacheNotification', 'user', 10, '{\"type\":\"sous_tache_validee\",\"tache_id\":8,\"libelle\":\"TA26-008\",\"titre\":\"Sous-t\\u00e2che valid\\u00e9e\",\"message\":\"Votre sous-t\\u00e2che \\u00ab R\\u00e9union pr\\u00e9alable de restitution \\u00bb (t\\u00e2che TA26-008) a \\u00e9t\\u00e9 valid\\u00e9e.\",\"ton\":\"avenir\",\"lien\":\"http:\\/\\/127.0.0.1\\/etache\\/feuille-de-route\\/tache\\/8\\/details\"}', NULL, '2026-10-11 17:24:55', '2026-10-11 17:24:55'),
('db7723e0-2b14-4fb6-88e4-37ffe000a551', 'App\\Notifications\\DiligenceNotification', 'user', 9, '{\"type\":\"diligence_reponse_a_viser\",\"diligence_id\":3,\"code\":\"DL26-0003\",\"libelle\":\"A26-0001\",\"titre\":\"R\\u00e9ponse \\u00e0 viser\",\"message\":\"La r\\u00e9ponse au courrier A26-0001 (\\u00ab D\\u00e9signation d\\u2019un point focal et de son suppl\\u00e9ant pour le suivi de la plateforme... \\u00bb) attend votre visa.\",\"ton\":\"attente\",\"lien\":\"http:\\/\\/127.0.0.1\\/etache\\/courrier\\/diligence\\/3#reponses\"}', '2026-10-13 15:51:20', '2026-10-13 06:23:08', '2026-10-13 15:51:20'),
('dd1b1524-afd8-4b7b-9190-66b2b7309e78', 'App\\Notifications\\AlerteTacheNotification', 'user', 3, '{\"type\":\"alerte_tache\",\"tache_id\":10,\"code\":\"TA26-010\",\"libelle\":\"Soumettre le rapport du diagnostic au cabinet du  MPEEP pour validation\",\"titre\":\"\\u00c9ch\\u00e9ance imminente\",\"regle\":\"J-3\",\"message\":\"La t\\u00e2che TA26-010 \\u00ab Soumettre le rapport du diagnostic au cabinet du  MPEEP pour validation \\u00bb arrive \\u00e0 \\u00e9ch\\u00e9ance dans 3 jours (\\u00e9ch\\u00e9ance : 05\\/10\\/2026).\",\"ton\":\"avenir\",\"date_fin\":\"2026-10-05\",\"url\":\"http:\\/\\/127.0.0.1\\/etache\\/feuille-de-route\\/tache\\/10\\/details\"}', '2026-10-03 18:36:34', '2026-10-02 20:50:01', '2026-10-03 18:36:34'),
('de0408e7-b04e-4f4a-bfd9-6b3551b04a6a', 'App\\Notifications\\AlerteTacheNotification', 'user', 3, '{\"type\":\"alerte_tache\",\"tache_id\":10,\"code\":\"TA26-010\",\"libelle\":\"Soumettre le rapport du diagnostic au cabinet du  MPEEP pour validation\",\"titre\":\"\\u00c9ch\\u00e9ance imminente\",\"regle\":\"J-3\",\"message\":\"La t\\u00e2che TA26-010 \\u00ab Soumettre le rapport du diagnostic au cabinet du  MPEEP pour validation \\u00bb arrive \\u00e0 \\u00e9ch\\u00e9ance dans 3 jours (\\u00e9ch\\u00e9ance : 06\\/10\\/2026).\",\"ton\":\"avenir\",\"date_fin\":\"2026-10-06\",\"url\":\"http:\\/\\/127.0.0.1\\/etache\\/feuille-de-route\\/tache\\/10\\/details\"}', '2026-10-03 18:20:38', '2026-10-03 17:58:51', '2026-10-03 18:20:38'),
('dfd96070-d477-4b42-842a-5efb5fc63eff', 'App\\Notifications\\PonctuelleNotification', 'user', 5, '{\"type\":\"ponctuelle_creee\",\"tache_id\":19,\"libelle\":\"TP26-0001\",\"titre\":\"Nouvelle t\\u00e2che ponctuelle\",\"message\":\"Jean-Baptiste AKON a cr\\u00e9\\u00e9 la t\\u00e2che ponctuelle \\u00ab Mise en place du comit\\u00e9 de cadrage \\u00bb.\",\"ton\":\"avenir\",\"lien\":\"http:\\/\\/127.0.0.1\\/etache\\/feuille-de-route\\/ponctuelles?ouvrir=19\"}', NULL, '2026-11-08 00:46:39', '2026-11-08 00:46:39'),
('e0d809f5-7ffe-4218-aa53-9d5667333854', 'App\\Notifications\\DiligenceNotification', 'user', 10, '{\"type\":\"diligence_echeance\",\"diligence_id\":3,\"code\":\"DL26-0003\",\"libelle\":\"A26-0001\",\"titre\":\"\\u00c9ch\\u00e9ance proche\",\"message\":\"La diligence DL26-0003 (courrier A26-0001, \\u00ab D\\u00e9signation d\\u2019un point focal et de son suppl\\u00e9ant pour le suivi de la plateforme... \\u00bb) arrive \\u00e0 \\u00e9ch\\u00e9ance le 14\\/10\\/2026.\",\"ton\":\"avenir\",\"lien\":\"http:\\/\\/127.0.0.1\\/etache\\/courrier\\/diligence\\/3\"}', NULL, '2026-10-12 04:45:11', '2026-10-12 04:45:11'),
('f336dd24-d32b-4075-9b76-5900a08528e0', 'App\\Notifications\\DiligenceNotification', 'user', 10, '{\"type\":\"diligence_retard\",\"diligence_id\":3,\"code\":\"DL26-0003\",\"libelle\":\"A26-0001\",\"titre\":\"Diligence en retard\",\"message\":\"La diligence DL26-0003 (courrier A26-0001, \\u00ab D\\u00e9signation d\\u2019un point focal et de son suppl\\u00e9ant pour le suivi de la plateforme... \\u00bb) est en retard : \\u00e9ch\\u00e9ance d\\u00e9pass\\u00e9e le 14\\/10\\/2026. Retard : 21 jour(s).\",\"ton\":\"retard\",\"lien\":\"http:\\/\\/127.0.0.1\\/etache\\/courrier\\/diligence\\/3\"}', NULL, '2026-11-04 14:25:31', '2026-11-04 14:25:31'),
('f827b194-dc78-4bfe-b61b-51fb60dcaf6c', 'App\\Notifications\\DiligenceNotification', 'user', 9, '{\"type\":\"diligence_action_a_valider\",\"diligence_id\":3,\"code\":\"DL26-0003\",\"libelle\":\"A26-0001\",\"titre\":\"Action \\u00e0 valider\",\"message\":\"Une action du plan de traitement de la diligence DL26-0003 attend votre validation : \\u00ab Envoie du courrier aux EPN via mail et physiquement. \\u00bb.\",\"ton\":\"attente\",\"lien\":\"http:\\/\\/127.0.0.1\\/etache\\/courrier\\/diligence\\/3#traitement\"}', '2026-10-13 15:50:59', '2026-10-12 13:06:24', '2026-10-13 15:50:59'),
('ffe08180-f8b0-43ca-ab48-9cde67948e2e', 'App\\Notifications\\AlerteTacheNotification', 'user', 4, '{\"type\":\"alerte_tache\",\"tache_id\":10,\"code\":\"TA26-010\",\"libelle\":\"Soumettre le rapport du diagnostic au cabinet du  MPEEP pour validation\",\"titre\":\"\\u00c9ch\\u00e9ance imminente\",\"regle\":\"J-3\",\"message\":\"La t\\u00e2che TA26-010 \\u00ab Soumettre le rapport du diagnostic au cabinet du  MPEEP pour validation \\u00bb arrive \\u00e0 \\u00e9ch\\u00e9ance dans 3 jours (\\u00e9ch\\u00e9ance : 05\\/10\\/2026).\",\"ton\":\"avenir\",\"date_fin\":\"2026-10-05\",\"url\":\"http:\\/\\/127.0.0.1\\/etache\\/feuille-de-route\\/tache\\/10\\/details\"}', NULL, '2026-10-02 19:49:01', '2026-10-02 19:49:01');

-- --------------------------------------------------------

--
-- Structure de la table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `personnel`
--

DROP TABLE IF EXISTS `personnel`;
CREATE TABLE IF NOT EXISTS `personnel` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `service_id` bigint UNSIGNED DEFAULT NULL,
  `type_agent_id` bigint UNSIGNED DEFAULT NULL,
  `emploi_id` bigint UNSIGNED DEFAULT NULL,
  `fonction_id` bigint UNSIGNED DEFAULT NULL,
  `matricule` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nom` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_naissance` date DEFAULT NULL,
  `lieu_naissance` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fonction` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telephone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lieu_habitation` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `genre_id` bigint UNSIGNED DEFAULT NULL,
  `civilite_id` bigint UNSIGNED DEFAULT NULL,
  `statut_matrimonial_id` bigint UNSIGNED DEFAULT NULL,
  `nombre_enfants` tinyint UNSIGNED DEFAULT NULL,
  `nom_pere` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nom_mere` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telephone_2` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `grade_id` bigint UNSIGNED DEFAULT NULL,
  `classe_id` bigint UNSIGNED DEFAULT NULL,
  `echelon_id` bigint UNSIGNED DEFAULT NULL,
  `date_premiere_prise_service` date DEFAULT NULL,
  `date_prise_service` date DEFAULT NULL,
  `date_depart_retraite` date DEFAULT NULL,
  `date_deces` date DEFAULT NULL,
  `archive_le` timestamp NULL DEFAULT NULL,
  `archive_motif` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `archive_par` bigint UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personnel_matricule_unique` (`matricule`),
  KEY `personnel_service_id_foreign` (`service_id`),
  KEY `personnel_type_agent_id_foreign` (`type_agent_id`),
  KEY `personnel_emploi_id_foreign` (`emploi_id`),
  KEY `personnel_fonction_id_foreign` (`fonction_id`),
  KEY `personnel_genre_id_foreign` (`genre_id`),
  KEY `personnel_civilite_id_foreign` (`civilite_id`),
  KEY `personnel_statut_matrimonial_id_foreign` (`statut_matrimonial_id`),
  KEY `personnel_grade_id_foreign` (`grade_id`),
  KEY `personnel_classe_id_foreign` (`classe_id`),
  KEY `personnel_echelon_id_foreign` (`echelon_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `personnel`
--

INSERT INTO `personnel` (`id`, `service_id`, `type_agent_id`, `emploi_id`, `fonction_id`, `matricule`, `nom`, `prenom`, `date_naissance`, `lieu_naissance`, `fonction`, `email`, `telephone`, `lieu_habitation`, `photo`, `actif`, `created_at`, `updated_at`, `genre_id`, `civilite_id`, `statut_matrimonial_id`, `nombre_enfants`, `nom_pere`, `nom_mere`, `telephone_2`, `grade_id`, `classe_id`, `echelon_id`, `date_premiere_prise_service`, `date_prise_service`, `date_depart_retraite`, `date_deces`, `archive_le`, `archive_motif`, `archive_par`) VALUES
(1, NULL, 1, 1, NULL, '835374V', 'AKON', 'Jean-Baptiste', NULL, NULL, 'Agent', 'jb.kouassi@dgepn.ci', '+225 07 00 00 00 01', 'Songon Institut Pasteur', 'ef3058ba-025b-424d-b3d6-4a9aa9b703ee.png', 1, '2026-09-27 17:07:46', '2026-10-10 10:29:49', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, NULL, NULL, NULL, NULL, 'TEST-002', 'Traoré', 'Aminata', NULL, NULL, 'Agent', 'a.traore@dgepn.ci', '+225 07 00 00 00 02', NULL, NULL, 1, '2026-09-27 17:07:46', '2026-10-10 10:29:49', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, NULL, NULL, NULL, NULL, 'TEST-003', 'N\'Guessan', 'Yves', NULL, NULL, 'Agent', 'y.nguessan@dgepn.ci', '+225 07 00 00 00 03', NULL, NULL, 1, '2026-09-27 17:07:46', '2026-10-10 10:29:49', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, NULL, 1, 4, NULL, '328565C', 'Koné', 'Aminata', '1987-03-14', 'Abidjan', 'Agent', 'aminata.kone@dgepn.ci', '+225 07 73 50 44 71', 'Cocody', '242e4519-555d-4126-89cc-ee525de313ae.jpg', 1, '2026-10-02 20:38:21', '2026-10-10 10:29:49', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(5, NULL, NULL, NULL, NULL, '507346T', 'Yao', 'Serge', '1982-11-02', 'Bouaké', 'Agent', 'serge.yao@dgepn.ci', '+225 01 75 91 24 12', 'Yopougon', '3ffa541c-b17c-45cc-b92d-f6daa0a8bf9c.jpg', 1, '2026-10-02 20:38:21', '2026-10-10 10:29:49', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(6, NULL, 1, 2, 4, '412510E', 'BLETCHI', 'Ange', '1990-07-25', 'Korhogo', 'Sous-directeur - Sous-direction de l\'Audit, du Contrôle et des Risques', 'paul.akon@uvci.edu.ci', '+225 07 68 64 43 51', 'Marcory', '86a3548c-4528-4f22-b39e-00f8715c356a.jpg', 1, '2026-10-02 20:38:21', '2026-10-10 10:29:49', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(7, NULL, NULL, NULL, NULL, '893766F', 'Kouadio', 'Emmanuel', '1979-01-19', 'Yamoussoukro', 'Agent', 'emmanuel.kouadio@dgepn.ci', '+225 05 97 34 16 85', 'Cocody', NULL, 1, '2026-10-02 20:38:21', '2026-10-10 10:29:49', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(8, NULL, 1, NULL, 3, '475042N', 'TEBILI', 'Bolou Maxime', '1993-09-08', 'Daloa', 'Directeur - Direction de la Stratégie, de l\'Audit et des Risques', 'mister.akonnano@gmail.com', '+225 01 54 83 79 35', 'Abobo', 'a771f5c5-3545-4901-b0ce-8ef3a0a4b1ef.jpg', 1, '2026-10-02 20:38:22', '2026-10-10 10:29:49', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(9, NULL, 1, 1, 4, '134584P', 'AKON', 'Paul-Landry', '1985-05-30', 'Attinguié', 'Sous-directeur - Sous-Direction des Développements et des Etudes Informatiques', 'akonperso@gmail.com', '+225 05 33 92 64 70', 'Plateau', '92b95f6d-da92-4f98-8f08-caa0bca9b294.jpg', 1, '2026-10-02 20:38:22', '2026-10-10 10:29:49', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(10, NULL, NULL, NULL, NULL, '221120U', 'Aka', 'Marie-Thérèse', '1988-12-12', 'Abidjan', 'Agent', 'marietherese.aka@dgepn.ci', '+225 07 17 26 41 17', 'Treichville', '385c4946-90e8-4476-b745-1a90b1c0191a.jpg', 1, '2026-10-02 20:38:22', '2026-10-10 10:29:49', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(11, 2, 1, NULL, 5, '917261L', 'Diabaté', 'Mamadou', '1976-04-03', 'Man', 'Chef de service - Service Développement numérique', 'mamadou.diabate@dgepn.ci', '+225 05 69 47 14 59', 'Riviera', 'cd2a2702-d0e3-47ff-be95-92af7fc6bbd3.jpg', 1, '2026-10-02 20:38:22', '2026-10-11 16:54:25', 1, 3, 9, 1, 'DIABATE SALIFOU', 'SANKO MAIMOUNA', NULL, 5, 1, 2, '2025-03-05', '2025-03-05', NULL, NULL, NULL, NULL, NULL),
(12, NULL, NULL, NULL, NULL, '829982N', 'N\'Guessan', 'Sylvie', '1991-08-21', 'San-Pédro', 'Agent', 'sylvie.nguessan@dgepn.ci', '+225 05 43 58 43 64', 'Koumassi', NULL, 1, '2026-10-02 20:38:22', '2026-10-10 10:29:49', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(13, 2, 1, 4, 8, '868560T', 'KOUADIO', 'Antonio Chapman', '1984-02-27', 'Abengourou', 'Agent', 'yves.bamba@dgepn.ci', '+225 07 31 74 72 68', 'Adjamé', '31c3965f-6479-486d-92b8-15edaf897792.jpg', 1, '2026-10-02 20:38:22', '2026-10-11 17:02:52', 1, 3, 9, 0, 'KOUADIO Franck', 'CHAYE Aline', NULL, 6, 1, 1, '2026-03-08', '2026-03-08', NULL, NULL, NULL, NULL, NULL),
(14, NULL, 1, NULL, 1, '251790R', 'DAOUDA', 'KAMAGATE', '1966-02-15', NULL, 'Directeur Général', 'akonperso@gmail.com', NULL, 'COCODY', '238b53f0-7ec7-4959-8dd1-e31546a3f8d5.jpg', 1, '2026-10-09 12:40:55', '2026-10-10 10:29:49', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(15, NULL, 1, NULL, 3, '348749J', 'CLARI', 'DELAFACE KANGOU', '1978-08-18', NULL, 'Directeur - Direction de l\'Informatique', 'akonperso@gmail.com', NULL, NULL, 'b0121583-763e-4845-927b-03af3d7a4fbf.jpg', 1, '2026-10-09 12:43:26', '2026-10-10 10:29:49', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(16, NULL, 1, NULL, NULL, '900320M', 'FOFANA', 'Mohamed', '1985-01-01', 'ABIDJAN', 'Agent', 'm.fofana@dgepn.ci', '0735454321', 'Yopougon Millionnaire', 'c346f458-083c-47d6-84a7-60cfada8a55a.jpg', 1, '2026-10-11 16:34:32', '2026-10-11 16:34:34', 1, 3, 10, 3, 'FOFANA MALICK', 'KONE AICHATA', '0509362311', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `personnel_competences`
--

DROP TABLE IF EXISTS `personnel_competences`;
CREATE TABLE IF NOT EXISTS `personnel_competences` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `personnel_id` bigint UNSIGNED NOT NULL,
  `libelle` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `niveau` tinyint UNSIGNED NOT NULL DEFAULT '2',
  `commentaire` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cree_par` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `personnel_competences_personnel_id_index` (`personnel_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `personnel_formations`
--

DROP TABLE IF EXISTS `personnel_formations`;
CREATE TABLE IF NOT EXISTS `personnel_formations` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `personnel_id` bigint UNSIGNED NOT NULL,
  `intitule` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `organisme` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `duree_heures` smallint UNSIGNED DEFAULT NULL,
  `certifiante` tinyint(1) NOT NULL DEFAULT '0',
  `observations` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cree_par` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `personnel_formations_personnel_id_index` (`personnel_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `pieces_jointes`
--

DROP TABLE IF EXISTS `pieces_jointes`;
CREATE TABLE IF NOT EXISTS `pieces_jointes` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `attachable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `attachable_id` bigint UNSIGNED NOT NULL,
  `categorie` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'livrable',
  `nom_original` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `chemin` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `extension` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mime` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `taille` bigint UNSIGNED NOT NULL,
  `version` smallint UNSIGNED NOT NULL DEFAULT '1',
  `racine_id` bigint UNSIGNED DEFAULT NULL,
  `actuelle` tinyint(1) NOT NULL DEFAULT '1',
  `depose_par` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pieces_jointes_attachable_type_attachable_id_index` (`attachable_type`,`attachable_id`),
  KEY `pieces_jointes_depose_par_foreign` (`depose_par`),
  KEY `pj_attachable_actuelle` (`attachable_type`,`attachable_id`,`actuelle`),
  KEY `pieces_jointes_racine_id_index` (`racine_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `pieces_jointes`
--

INSERT INTO `pieces_jointes` (`id`, `attachable_type`, `attachable_id`, `categorie`, `nom_original`, `chemin`, `extension`, `mime`, `taille`, `version`, `racine_id`, `actuelle`, `depose_par`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'sous_tache', 2, 'livrable', 'axe.xlsx', 'pieces_jointes/2026/10/9lGBviAZFI22Ayis4jVj3qKUJRJ2LBdJzGZ4XtrA.xlsx', 'xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 12361, 1, NULL, 1, 1, '2026-10-08 17:57:43', '2026-10-08 17:57:43', NULL),
(2, 'courrier', 1, 'scan', 'Courrier_test_ANADT_0412.pdf', 'courriers/2026/j0PwmLwYxUu9Z7inNqpD50I5Wo4blvoQRAsD5D6g.pdf', 'pdf', 'application/pdf', 3542, 1, NULL, 1, 7, '2026-10-09 16:16:30', '2026-10-09 16:16:30', NULL),
(3, 'personnel', 6, 'dossier_cv', 'Courrier_test_ANADT_0412.pdf', 'dossiers_agents/6/AQCJBViQhwvwBCoCmTvnKe4ETHWEQwQ4S3nSK0qY.pdf', 'pdf', 'application/pdf', 3542, 1, NULL, 1, 1, '2026-10-10 08:50:26', '2026-10-10 08:50:26', NULL),
(4, 'personnel', 6, 'dossier_cni', 'Certificat de residence.pdf', 'dossiers_agents/6/Ymfdr1IxnxDeWfwJvGAVLvfxY93rsV6gGX0JFBbq.pdf', 'pdf', 'application/pdf', 151227, 1, NULL, 1, 1, '2026-10-10 08:50:53', '2026-10-10 08:50:53', NULL),
(5, 'personnel', 6, 'dossier_diplome', 'Courrier_test_ANADT_0412.pdf', 'dossiers_agents/6/M8BzqgAAzxC4CLUo60CPKwoCdXP18rUk2oAQYIhL.pdf', 'pdf', 'application/pdf', 3542, 1, NULL, 1, 1, '2026-10-10 08:51:58', '2026-10-10 08:51:58', NULL),
(6, 'personnel', 3, 'dossier_cv', 'A26-0001.pdf', 'dossiers_agents/3/t5S9KTntLaAjQ5LBG8QkB3nzWYGaG4NrdHWp6BJi.pdf', 'pdf', 'application/pdf', 3542, 1, NULL, 1, 2, '2026-10-10 10:50:23', '2026-10-10 10:50:23', NULL),
(7, 'reponse_courrier', 1, 'projet', 'note_synthese_pilotage_2026.pdf', 'courriers/reponses/2026/uI2FMknsAVU0YsNJClV8srAt5zdHFaFNeROwADbG.pdf', 'pdf', 'application/pdf', 891727, 1, NULL, 1, 10, '2026-10-13 06:23:07', '2026-10-13 06:23:07', NULL),
(8, 'tache_ponctuelle', 19, 'livrable', 'pilotage_2026_exercice_2026_20261107_180736.xlsx', 'pieces_jointes/2026/11/RVdhPa3EbUlDcGwayZSBk5UHRJJnAZg3Bct3UoQp.xlsx', 'xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 19129, 1, NULL, 1, 1, '2026-11-08 00:55:52', '2026-11-08 00:56:27', '2026-11-08 00:56:27'),
(9, 'tache_ponctuelle', 19, 'livrable', 'pilotage_2026_exercice_2026_20261107_180736.xlsx', 'pieces_jointes/2026/11/EfSghLfgW0JWzJVb5XiLNHvLJGQ3TRdN6ZMOdhXi.xlsx', 'xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 19129, 1, NULL, 1, 1, '2026-11-08 01:04:52', '2026-11-08 01:05:59', '2026-11-08 01:05:59'),
(10, 'tache_ponctuelle', 19, 'livrable', 'pilotage_2026_exercice_2026_20261107_180736.xlsx', 'pieces_jointes/2026/11/HcHl01WAuVSzOIHuC3xGrngXK9mckuDz3I1iFT1J.xlsx', 'xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 19129, 1, NULL, 1, 1, '2026-11-08 01:11:40', '2026-11-08 01:11:50', '2026-11-08 01:11:50'),
(11, 'tache_ponctuelle', 19, 'livrable', 'pilotage_2026_exercice_2026_20261107_180736.xlsx', 'pieces_jointes/2026/11/5LHpHLVurlrIHcEHDDp5YEOM7FVZAgPGG1EMRqzf.xlsx', 'xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 19129, 1, NULL, 1, 1, '2026-11-08 01:11:59', '2026-11-08 01:11:59', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `postes_imputation`
--

DROP TABLE IF EXISTS `postes_imputation`;
CREATE TABLE IF NOT EXISTS `postes_imputation` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `libelle` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `niveau` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unite_type` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unite_id` bigint UNSIGNED DEFAULT NULL,
  `personnel_id` bigint UNSIGNED DEFAULT NULL,
  `peut_reimputer` tinyint(1) NOT NULL DEFAULT '0',
  `ordre` smallint UNSIGNED NOT NULL DEFAULT '1',
  `actif` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `postes_imputation_libelle_unique` (`libelle`),
  KEY `postes_imputation_personnel_id_foreign` (`personnel_id`),
  KEY `postes_imputation_position_index` (`niveau`,`unite_type`,`unite_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `postes_imputation`
--

INSERT INTO `postes_imputation` (`id`, `libelle`, `niveau`, `unite_type`, `unite_id`, `personnel_id`, `peut_reimputer`, `ordre`, `actif`, `created_at`, `updated_at`) VALUES
(1, 'Directeur Général Adjoint', 'dga', NULL, NULL, NULL, 1, 2, 1, '2026-10-09 10:48:41', '2026-10-10 11:57:27'),
(2, 'Service courrier', NULL, NULL, NULL, 5, 0, 2, 1, '2026-10-09 10:48:41', '2026-10-09 11:30:48'),
(3, 'Chargé d\'études Direction Générale', NULL, NULL, NULL, 8, 0, 3, 1, '2026-10-09 10:48:41', '2026-10-09 15:30:17'),
(4, 'Directeur Général', 'direction_generale', NULL, NULL, NULL, 1, 1, 1, '2026-10-10 11:54:54', '2026-10-10 11:54:54'),
(5, 'Directeur — Direction de l\'Informatique', 'direction', 'direction', 4, NULL, 1, 3, 1, '2026-10-10 11:59:05', '2026-10-10 11:59:22'),
(6, 'Directeur — Direction de la Stratégie, de l\'Audit et des Risques', 'direction', 'direction', 2, NULL, 1, 4, 1, '2026-10-11 06:12:54', '2026-10-11 17:50:42'),
(7, 'Directeur — Direction des Affaires Juridiques', 'direction', 'direction', 3, NULL, 1, 6, 1, '2026-10-11 17:51:03', '2026-10-11 17:51:38'),
(8, 'Directeur — Direction du Personnel et de la Communication', 'direction', 'direction', 5, NULL, 1, 5, 1, '2026-10-11 17:51:21', '2026-10-11 17:54:53'),
(9, 'Directeur — Direction du Suivi de la Performance et de la Qualité', 'direction', 'direction', 1, NULL, 1, 7, 1, '2026-10-11 17:51:59', '2026-10-11 17:51:59'),
(10, 'Chef de service — Service Financier et des Moyens Généraux', 'service', 'service', 1, NULL, 1, 8, 1, '2026-10-11 17:53:18', '2026-10-11 17:53:18');

-- --------------------------------------------------------

--
-- Structure de la table `priorites`
--

DROP TABLE IF EXISTS `priorites`;
CREATE TABLE IF NOT EXISTS `priorites` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `libelle` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `couleur` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `priorites_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `priorites`
--

INSERT INTO `priorites` (`id`, `code`, `libelle`, `couleur`, `actif`, `created_at`, `updated_at`) VALUES
(1, '001', 'Haute', '#A73B34', 1, '2026-09-26 07:00:25', '2026-09-26 07:00:25'),
(2, '002', 'Moyenne', '#B8790F', 1, '2026-09-26 07:00:25', '2026-09-26 07:00:25'),
(3, '003', 'Basse', '#5B6B84', 1, '2026-09-26 07:00:25', '2026-09-26 07:00:25');

-- --------------------------------------------------------

--
-- Structure de la table `profils_acces`
--

DROP TABLE IF EXISTS `profils_acces`;
CREATE TABLE IF NOT EXISTS `profils_acces` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `libelle` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `droits` json DEFAULT NULL,
  `systeme` tinyint(1) NOT NULL DEFAULT '0',
  `actif` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `profils_acces_code_unique` (`code`),
  UNIQUE KEY `profils_acces_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `profils_acces`
--

INSERT INTO `profils_acces` (`id`, `code`, `slug`, `libelle`, `droits`, `systeme`, `actif`, `created_at`, `updated_at`) VALUES
(1, '001', 'DG', 'Direction Générale', '[\"direction.voir\", \"direction.creer\", \"direction.modifier\", \"direction.supprimer\", \"sous_direction.voir\", \"sous_direction.creer\", \"sous_direction.modifier\", \"sous_direction.supprimer\", \"service.voir\", \"service.creer\", \"service.modifier\", \"service.supprimer\", \"responsable.voir\", \"personnel.voir\", \"axe.voir\", \"axe.creer\", \"axe.modifier\", \"axe.supprimer\", \"action_cle.voir\", \"action_cle.creer\", \"action_cle.modifier\", \"action_cle.supprimer\", \"action_mesurable.voir\", \"action_mesurable.creer\", \"action_mesurable.modifier\", \"action_mesurable.supprimer\", \"tache.voir\", \"tache.creer\", \"tache.modifier\", \"tache.supprimer\", \"import_matrice.voir\", \"import_matrice.importer\", \"alerte.voir\", \"justification.voir\", \"pilotage.voir\", \"pilotage.exporter\", \"pilotage.synthese\", \"pilotage.corriger_synthese\", \"cockpit.voir\", \"rh_tableau_de_bord.voir\", \"rh_organigramme.voir\", \"rh_qualite.voir\", \"rh_qualite.exporter\", \"rh_doublons.voir\", \"rh_dossiers.voir\", \"rh_comptes.voir\", \"rh_archives.voir\", \"rh_charge.voir\", \"exercice.voir\", \"exercice.creer\", \"exercice.modifier\", \"exercice.supprimer\", \"exercice.cloturer\", \"type_agent.voir\", \"type_agent.creer\", \"type_agent.modifier\", \"type_agent.supprimer\", \"famille_emploi.voir\", \"famille_emploi.creer\", \"famille_emploi.modifier\", \"famille_emploi.supprimer\", \"emploi.voir\", \"emploi.creer\", \"emploi.modifier\", \"emploi.supprimer\", \"profils_acces.voir\", \"profils_acces.creer\", \"profils_acces.modifier\", \"profils_acces.supprimer\", \"role_affectation.voir\", \"role_affectation.creer\", \"role_affectation.modifier\", \"role_affectation.supprimer\", \"priorite.voir\", \"priorite.creer\", \"priorite.modifier\", \"priorite.supprimer\", \"statut_execution.voir\", \"statut_execution.creer\", \"statut_execution.modifier\", \"statut_execution.supprimer\", \"situation_temporelle.voir\", \"situation_temporelle.creer\", \"situation_temporelle.modifier\", \"situation_temporelle.supprimer\", \"canal_notification.voir\", \"canal_notification.creer\", \"canal_notification.modifier\", \"canal_notification.supprimer\", \"regle_alerte.voir\", \"regle_alerte.creer\", \"regle_alerte.modifier\", \"regle_alerte.supprimer\", \"comptes_utilisateurs.voir\", \"comptes_utilisateurs.creer\", \"comptes_utilisateurs.modifier\", \"comptes_utilisateurs.supprimer\", \"journal.voir\"]', 0, 1, '2026-09-27 15:18:39', '2026-11-08 14:16:01'),
(2, '002', 'RD', 'Responsable de direction', '[\"responsable.voir\", \"axe.voir\", \"action_cle.voir\", \"action_mesurable.voir\", \"tache.voir\", \"vues_taches.voir\", \"vue_ensemble.voir\", \"justification.voir\", \"pilotage.voir\", \"pilotage.exporter\", \"pilotage.synthese\", \"cockpit.voir\", \"rh_organigramme.voir\"]', 0, 1, '2026-09-27 15:18:39', '2026-11-08 21:43:56'),
(3, '003', 'RA', 'Responsable de tâche', NULL, 0, 1, '2026-09-27 15:18:39', '2026-09-27 15:18:39'),
(4, '004', 'ADMIN', 'Administrateur', '[]', 1, 1, '2026-09-27 15:18:39', '2026-11-08 12:40:24'),
(5, '005', 'COURRIER', 'Service courrier', NULL, 0, 1, '2026-10-09 09:41:26', '2026-10-09 09:41:26'),
(6, '006', 'AGENT', 'Agent', NULL, 0, 1, '2026-10-11 07:37:07', '2026-10-11 07:37:07');

-- --------------------------------------------------------

--
-- Structure de la table `reglages`
--

DROP TABLE IF EXISTS `reglages`;
CREATE TABLE IF NOT EXISTS `reglages` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `cle` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `valeur` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `modifie_par` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reglages_cle_unique` (`cle`),
  KEY `reglages_modifie_par_foreign` (`modifie_par`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `reglages`
--

INSERT INTO `reglages` (`id`, `cle`, `valeur`, `modifie_par`, `created_at`, `updated_at`) VALUES
(2, 'synthese.ia_active', 'true', NULL, '2026-11-07 15:38:37', '2026-11-07 15:38:37'),
(3, 'securite.inactivite_minutes', '15', 1, '2026-11-07 17:11:57', '2026-11-07 17:20:25');

-- --------------------------------------------------------

--
-- Structure de la table `regles_alerte`
--

DROP TABLE IF EXISTS `regles_alerte`;
CREATE TABLE IF NOT EXISTS `regles_alerte` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `libelle` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `decalage_jours` int NOT NULL,
  `destinataires` json DEFAULT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `regles_alerte_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `regles_alerte`
--

INSERT INTO `regles_alerte` (`id`, `code`, `libelle`, `decalage_jours`, `destinataires`, `actif`, `created_at`, `updated_at`) VALUES
(1, 'J-30', 'Échéance à venir', -30, '[\"responsable\"]', 1, '2026-10-01 18:47:00', '2026-10-01 18:47:00'),
(2, 'J-15', 'Échéance proche', -15, '[\"responsable\", \"pilote\"]', 1, '2026-10-01 20:55:08', '2026-10-01 20:55:08'),
(3, 'J-7', 'À surveiller', -7, '[\"responsable\", \"pilote\"]', 1, '2026-10-01 20:55:08', '2026-10-01 20:55:08'),
(4, 'J-3', 'Échéance imminente', -3, '[\"responsable\", \"pilote\"]', 1, '2026-10-01 20:55:08', '2026-10-01 20:55:08'),
(5, 'J-1', 'Échéance le lendemain', -1, '[\"responsable\", \"pilote\"]', 1, '2026-10-01 20:55:08', '2026-10-01 20:55:08'),
(6, 'J0', 'Échéance aujourd\'hui', 0, '[\"responsable\", \"pilote\"]', 1, '2026-10-01 20:55:08', '2026-10-01 20:55:08'),
(7, 'J+1', 'Échéance dépassée', 1, '[\"responsable\", \"pilote\", \"responsable_direction\"]', 1, '2026-10-01 20:55:08', '2026-10-01 20:55:08'),
(8, 'J+7', 'Retard persistant', 7, '[\"responsable\", \"pilote\", \"responsable_direction\"]', 1, '2026-10-01 20:55:08', '2026-10-01 20:55:08'),
(9, 'J+15', 'Retard grave', 15, '[\"responsable\", \"pilote\", \"responsable_direction\", \"dg\"]', 1, '2026-10-01 20:55:08', '2026-10-01 20:55:08'),
(10, 'J+30', 'Retard critique', 30, '[\"responsable\", \"pilote\", \"responsable_direction\", \"dg\"]', 0, '2026-10-01 20:55:08', '2026-10-01 20:55:08');

-- --------------------------------------------------------

--
-- Structure de la table `regle_alerte_canal`
--

DROP TABLE IF EXISTS `regle_alerte_canal`;
CREATE TABLE IF NOT EXISTS `regle_alerte_canal` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `regle_alerte_id` bigint UNSIGNED NOT NULL,
  `canal_notification_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `regle_alerte_canal_unique` (`regle_alerte_id`,`canal_notification_id`),
  KEY `regle_alerte_canal_canal_notification_id_foreign` (`canal_notification_id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `regle_alerte_canal`
--

INSERT INTO `regle_alerte_canal` (`id`, `regle_alerte_id`, `canal_notification_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2026-10-01 18:47:00', '2026-10-01 18:47:00'),
(2, 5, 1, '2026-10-01 20:55:08', '2026-10-01 20:55:08'),
(3, 2, 1, '2026-10-01 20:55:08', '2026-10-01 20:55:08'),
(4, 4, 1, '2026-10-01 20:55:08', '2026-10-01 20:55:08'),
(5, 3, 1, '2026-10-01 20:55:08', '2026-10-01 20:55:08'),
(6, 7, 1, '2026-10-01 20:55:08', '2026-10-01 20:55:08'),
(7, 9, 1, '2026-10-01 20:55:08', '2026-10-01 20:55:08'),
(8, 10, 1, '2026-10-01 20:55:08', '2026-10-01 20:55:08'),
(9, 8, 1, '2026-10-01 20:55:08', '2026-10-01 20:55:08'),
(10, 6, 1, '2026-10-01 20:55:08', '2026-10-01 20:55:08'),
(17, 5, 2, '2026-10-01 20:55:08', '2026-10-01 20:55:08'),
(18, 2, 2, '2026-10-01 20:55:08', '2026-10-01 20:55:08'),
(19, 4, 2, '2026-10-01 20:55:08', '2026-10-01 20:55:08'),
(20, 3, 2, '2026-10-01 20:55:08', '2026-10-01 20:55:08'),
(21, 7, 2, '2026-10-01 20:55:08', '2026-10-01 20:55:08'),
(22, 9, 2, '2026-10-01 20:55:08', '2026-10-01 20:55:08'),
(23, 10, 2, '2026-10-01 20:55:08', '2026-10-01 20:55:08'),
(24, 8, 2, '2026-10-01 20:55:08', '2026-10-01 20:55:08'),
(25, 6, 2, '2026-10-01 20:55:08', '2026-10-01 20:55:08');

-- --------------------------------------------------------

--
-- Structure de la table `reponses_courrier`
--

DROP TABLE IF EXISTS `reponses_courrier`;
CREATE TABLE IF NOT EXISTS `reponses_courrier` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `courrier_id` bigint UNSIGNED NOT NULL,
  `diligence_id` bigint UNSIGNED NOT NULL,
  `objet` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `commentaire` text COLLATE utf8mb4_unicode_ci,
  `statut` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'brouillon',
  `cycle` tinyint UNSIGNED NOT NULL DEFAULT '1',
  `soumis_le` timestamp NULL DEFAULT NULL,
  `signe_le` timestamp NULL DEFAULT NULL,
  `signe_par` bigint UNSIGNED DEFAULT NULL,
  `cree_par` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `annulation_demandee_par` bigint UNSIGNED DEFAULT NULL,
  `annulation_demandee_le` timestamp NULL DEFAULT NULL,
  `annulation_motif` text COLLATE utf8mb4_unicode_ci,
  `annulation_decideur_id` bigint UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reponses_courrier_courrier_id_index` (`courrier_id`),
  KEY `reponses_courrier_diligence_id_index` (`diligence_id`),
  KEY `reponses_courrier_statut_index` (`statut`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `reponses_courrier`
--

INSERT INTO `reponses_courrier` (`id`, `courrier_id`, `diligence_id`, `objet`, `commentaire`, `statut`, `cycle`, `soumis_le`, `signe_le`, `signe_par`, `cree_par`, `created_at`, `updated_at`, `annulation_demandee_par`, `annulation_demandee_le`, `annulation_motif`, `annulation_decideur_id`) VALUES
(1, 1, 3, 'Réponse à votre courrier réf. 0412/ANADT/DG/2026 — Désignation d’un point focal et de son suppléant pour le suivi de la plateforme nationale de reporting des établissement...', 'Dans le cadre du déploiement et du suivi opérationnel de la plateforme nationale de reporting des établissements publics, il est procédé à la désignation des agents ci-dessous mentionnés pour assurer la liaison et la transmission des données', 'en_visa', 1, '2026-10-13 06:23:08', NULL, NULL, 10, '2026-10-13 06:23:07', '2026-10-13 06:23:08', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `reponse_visas`
--

DROP TABLE IF EXISTS `reponse_visas`;
CREATE TABLE IF NOT EXISTS `reponse_visas` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `reponse_id` bigint UNSIGNED NOT NULL,
  `cycle` tinyint UNSIGNED NOT NULL DEFAULT '1',
  `ordre` smallint UNSIGNED NOT NULL,
  `personnel_id` bigint UNSIGNED NOT NULL,
  `libelle` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `signataire` tinyint(1) NOT NULL DEFAULT '0',
  `statut` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en_attente',
  `decide_par` bigint UNSIGNED DEFAULT NULL,
  `decide_le` timestamp NULL DEFAULT NULL,
  `commentaire` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reponse_visas_reponse_id_index` (`reponse_id`),
  KEY `reponse_visas_personnel_id_index` (`personnel_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `reponse_visas`
--

INSERT INTO `reponse_visas` (`id`, `reponse_id`, `cycle`, `ordre`, `personnel_id`, `libelle`, `signataire`, `statut`, `decide_par`, `decide_le`, `commentaire`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 11, 'Chef de service — Service Développement numérique', 0, 'vise', 9, '2026-10-13 15:51:48', 'Il est bon à mon niveau.', '2026-10-13 06:23:07', '2026-10-13 15:51:48'),
(2, 1, 1, 2, 9, 'Sous-directeur — Sous-Direction des Développements et des Etudes Informatiques', 0, 'vise', 3, '2026-10-13 16:45:13', 'RAS', '2026-10-13 06:23:07', '2026-10-13 16:45:13'),
(3, 1, 1, 3, 15, 'Directeur — Direction de l\'Informatique', 0, 'en_attente', NULL, NULL, NULL, '2026-10-13 06:23:08', '2026-10-13 06:23:08'),
(4, 1, 1, 4, 14, 'Directeur Général', 1, 'en_attente', NULL, NULL, NULL, '2026-10-13 06:23:08', '2026-10-13 06:23:08');

-- --------------------------------------------------------

--
-- Structure de la table `rh_alertes`
--

DROP TABLE IF EXISTS `rh_alertes`;
CREATE TABLE IF NOT EXISTS `rh_alertes` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `cle` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `envoye_le` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `rh_alertes_cle_unique` (`cle`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `roles_affectation`
--

DROP TABLE IF EXISTS `roles_affectation`;
CREATE TABLE IF NOT EXISTS `roles_affectation` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `libelle` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_affectation_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `roles_affectation`
--

INSERT INTO `roles_affectation` (`id`, `code`, `libelle`, `actif`, `created_at`, `updated_at`) VALUES
(1, '001', 'Responsable', 1, '2026-09-26 07:00:25', '2026-09-26 07:00:25'),
(2, '002', 'Pilote (coordination)', 1, '2026-09-26 07:00:25', '2026-09-26 07:00:25'),
(3, '003', 'Contributeur', 1, '2026-09-26 07:00:25', '2026-09-26 07:00:25');

-- --------------------------------------------------------

--
-- Structure de la table `services`
--

DROP TABLE IF EXISTS `services`;
CREATE TABLE IF NOT EXISTS `services` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `sous_direction_id` bigint UNSIGNED DEFAULT NULL,
  `direction_id` bigint UNSIGNED DEFAULT NULL,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nom` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `services_sous_direction_id_code_unique` (`sous_direction_id`,`code`),
  KEY `services_direction_id_foreign` (`direction_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `services`
--

INSERT INTO `services` (`id`, `sous_direction_id`, `direction_id`, `code`, `nom`, `description`, `created_at`, `updated_at`) VALUES
(1, NULL, NULL, 'SFMG', 'Service Financier et des Moyens Généraux', 'Gère le patrimoine mobilier et immobilier, l\'entretien du parc automobile, la programmation des besoins en fournitures et équipements de bureau et l\'exécution du budget de la DGEPN (Article 27).', '2026-09-27 17:01:22', '2026-09-27 17:01:22'),
(2, 9, NULL, 'SDN', 'Service Développement numérique', 'Service chargé de développer les solutions informatiques', '2026-10-11 16:37:13', '2026-10-11 16:37:13');

-- --------------------------------------------------------

--
-- Structure de la table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('7T2ujg5NURrbYZHr46bl8qQVZHbBdU0bKduadrxR', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJHVXhZR2J6S3lPZEJvdjFoTUVGREFEbWRSczhmN0ZHT0hmaVo2bEJ1IiwidXJsIjpbXSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTI3LjAuMC4xXC9ldGFjaGVcL3ZlcnJvdWlsbGUiLCJyb3V0ZSI6InZlcnJvdWlsbGFnZS5zaG93In0sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjoxLCJwYXNzd29yZF9oYXNoX3dlYiI6ImQwYjRkODI0YTg5ZmFmZWE1YjBkNTI2ZDkyOWU3OGU0OWI5YWU3ZWFhOWE4ZmMyNzk5NzMwOTY3MTM5MTc3ZDEiLCJkZXJuaWVyZV9hY3Rpdml0ZSI6MTc5NDE3MDk5MywiZWNyYW5fdmVycm91aWxsZSI6eyJkZXB1aXMiOjE3OTQxNzE5MDEsImVzc2FpcyI6MCwicmV0b3VyIjoiaHR0cDpcL1wvMTI3LjAuMC4xXC9ldGFjaGVcL2Rhc2hib2FyZCJ9fQ==', 1794171902),
('DyEvoU3IOt3eIjnTye6GY6xxwVwoptnayDHJOYvf', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJ5ME5uWFQyWGladkRxNnlxSTdQNkFJQkpPVmlrT1lSMlpIVjhJbFFIIiwidXJsIjpbXSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTI3LjAuMC4xXC9ldGFjaGVcL2NvY2twaXQiLCJyb3V0ZSI6ImNvY2twaXQuaW5kZXgifSwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjEsInBhc3N3b3JkX2hhc2hfd2ViIjoiZDBiNGQ4MjRhODlmYWZlYTViMGQ1MjZkOTI5ZTc4ZTQ5YjlhZTdlYWE5YThmYzI3OTk3MzA5NjcxMzkxNzdkMSIsImRlcm5pZXJlX2FjdGl2aXRlIjoxNzk0MTc3NDMwfQ==', 1794177430),
('g8XBknyRjFEPHgeBri6PYDrO73R6NkcfemN9Ztku', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJwd0tROGRNWmJ4cGNPcllTVU1oYWpkWHVYZGdqSzRValhGUVVNeDFyIiwidXJsIjpbXSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTI3LjAuMC4xXC9ldGFjaGVcL3ZlcnJvdWlsbGUiLCJyb3V0ZSI6InZlcnJvdWlsbGFnZS5zaG93In0sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjoxLCJwYXNzd29yZF9oYXNoX3dlYiI6ImQwYjRkODI0YTg5ZmFmZWE1YjBkNTI2ZDkyOWU3OGU0OWI5YWU3ZWFhOWE4ZmMyNzk5NzMwOTY3MTM5MTc3ZDEiLCJkZXJuaWVyZV9hY3Rpdml0ZSI6MTc5NDE3MjIyNCwiZWNyYW5fdmVycm91aWxsZSI6eyJkZXB1aXMiOjE3OTQxNzQwNzAsImVzc2FpcyI6MCwicmV0b3VyIjoiaHR0cDpcL1wvMTI3LjAuMC4xXC9ldGFjaGVcL3BhcmFtZXRyZVwvcHJvZmlsLWFjY2VzIn19', 1794174071),
('QNd2lPxkNJSfs9iXeynE1c0nW3IbpnIK8K5pf5yS', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJhNTViTnk1SlAxMnBxdkhIa2hkYWttWERnTkNXZVFsR1VMWVNpblJLIiwidXJsIjpbXSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTI3LjAuMC4xXC9ldGFjaGVcL3ZlcnJvdWlsbGUiLCJyb3V0ZSI6InZlcnJvdWlsbGFnZS5zaG93In0sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjoxLCJwYXNzd29yZF9oYXNoX3dlYiI6ImQwYjRkODI0YTg5ZmFmZWE1YjBkNTI2ZDkyOWU3OGU0OWI5YWU3ZWFhOWE4ZmMyNzk5NzMwOTY3MTM5MTc3ZDEiLCJkZXJuaWVyZV9hY3Rpdml0ZSI6MTc5NDE2NzEwNCwiZWNyYW5fdmVycm91aWxsZSI6eyJkZXB1aXMiOjE3OTQxNjgyNTYsImVzc2FpcyI6MCwicmV0b3VyIjoiaHR0cDpcL1wvMTI3LjAuMC4xXC9ldGFjaGVcL2Rhc2hib2FyZCJ9fQ==', 1794168256),
('wNfJN0XDkL7jCXByakEEgkoEZ48D7llo20bnDLIq', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJhYnQ5WXp6ZE15am44SWtvbU1oWFd5ZGN2S29RaGpWWHhRTFFLaGF1IiwidXJsIjpbXSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTI3LjAuMC4xXC9ldGFjaGVcL3ZlcnJvdWlsbGUiLCJyb3V0ZSI6InZlcnJvdWlsbGFnZS5zaG93In0sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjoxLCJwYXNzd29yZF9oYXNoX3dlYiI6ImQwYjRkODI0YTg5ZmFmZWE1YjBkNTI2ZDkyOWU3OGU0OWI5YWU3ZWFhOWE4ZmMyNzk5NzMwOTY3MTM5MTc3ZDEiLCJkZXJuaWVyZV9hY3Rpdml0ZSI6MTc5NDE3NTUwNywiZWNyYW5fdmVycm91aWxsZSI6eyJkZXB1aXMiOjE3OTQxNzYxNzAsImVzc2FpcyI6MCwicmV0b3VyIjoiaHR0cDpcL1wvMTI3LjAuMC4xXC9ldGFjaGVcL3BhcmFtZXRyZVwvcHJvZmlsLWFjY2VzIn19', 1794176170);

-- --------------------------------------------------------

--
-- Structure de la table `situations_temporelles`
--

DROP TABLE IF EXISTS `situations_temporelles`;
CREATE TABLE IF NOT EXISTS `situations_temporelles` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `libelle` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `couleur` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `situations_temporelles_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `situations_temporelles`
--

INSERT INTO `situations_temporelles` (`id`, `code`, `libelle`, `couleur`, `actif`, `created_at`, `updated_at`) VALUES
(1, '001', 'Normal', '#5B6B84', 1, '2026-09-26 07:00:25', '2026-09-26 07:00:25'),
(2, '002', 'Échéance proche', '#B8790F', 1, '2026-09-26 07:00:25', '2026-09-26 07:00:25'),
(3, '003', 'À risque', '#C2410C', 1, '2026-09-26 07:00:25', '2026-09-26 07:00:25'),
(4, '004', 'En retard', '#A73B34', 1, '2026-09-26 07:00:25', '2026-09-26 07:00:25'),
(5, '005', 'Terminée', '#2E7D5B', 1, '2026-09-26 07:00:25', '2026-09-26 07:00:25');

-- --------------------------------------------------------

--
-- Structure de la table `sous_directions`
--

DROP TABLE IF EXISTS `sous_directions`;
CREATE TABLE IF NOT EXISTS `sous_directions` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `direction_id` bigint UNSIGNED NOT NULL,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nom` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sous_directions_direction_id_code_unique` (`direction_id`,`code`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `sous_directions`
--

INSERT INTO `sous_directions` (`id`, `direction_id`, `code`, `nom`, `description`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 'Sous-direction du Suivi des EPN des Secteurs de la Santé, de l\'Education et de la Formation', NULL, '2026-09-27 17:01:14', '2026-09-27 17:01:14'),
(2, 1, NULL, 'Sous-direction du Suivi des EPN des Secteurs Economiques, Techniques et Environnementaux', NULL, '2026-09-27 17:01:14', '2026-09-27 17:01:14'),
(3, 1, NULL, 'Sous-direction du Suivi des EPN des Secteurs Administratifs, Sociaux et assimilés', NULL, '2026-09-27 17:01:14', '2026-09-27 17:01:14'),
(4, 2, NULL, 'Sous-direction de la Stratégie', NULL, '2026-09-27 17:01:14', '2026-09-27 17:01:14'),
(5, 2, NULL, 'Sous-direction de l\'Audit, du Contrôle et des Risques', NULL, '2026-09-27 17:01:14', '2026-09-27 17:01:14'),
(6, 3, NULL, 'Sous-direction des Affaires Juridiques des EPN des Secteurs de la Santé, de l\'Education et de la Formation', NULL, '2026-09-27 17:01:14', '2026-09-27 17:01:14'),
(7, 3, NULL, 'Sous-direction des Affaires Juridiques des EPN des Secteurs Economiques, Techniques et Environnementaux', NULL, '2026-09-27 17:01:14', '2026-09-27 17:01:14'),
(8, 3, NULL, 'Sous-direction des Affaires Juridiques des EPN des Secteurs Administratifs, Sociaux et Assimilés', NULL, '2026-09-27 17:01:14', '2026-09-27 17:01:14'),
(9, 4, NULL, 'Sous-Direction des Développements et des Etudes Informatiques', NULL, '2026-09-27 17:01:14', '2026-09-27 17:01:14'),
(10, 4, NULL, 'Sous-Direction de l\'Exploitation et du Réseau', NULL, '2026-09-27 17:01:14', '2026-09-27 17:01:14'),
(11, 4, NULL, 'Sous-Direction du Support et de la Production', NULL, '2026-09-27 17:01:14', '2026-09-27 17:01:14'),
(12, 5, NULL, 'Sous-direction du Personnel et de la Formation', NULL, '2026-09-27 17:01:14', '2026-09-27 17:01:14'),
(13, 5, NULL, 'Sous-direction de la Communication', NULL, '2026-09-27 17:01:14', '2026-09-27 17:01:14');

-- --------------------------------------------------------

--
-- Structure de la table `sous_taches`
--

DROP TABLE IF EXISTS `sous_taches`;
CREATE TABLE IF NOT EXISTS `sous_taches` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tache_id` bigint UNSIGNED NOT NULL,
  `personnel_id` bigint UNSIGNED DEFAULT NULL,
  `libelle` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `echeance` date DEFAULT NULL,
  `ordre` smallint UNSIGNED NOT NULL DEFAULT '1',
  `cochee` tinyint(1) NOT NULL DEFAULT '0',
  `cochee_le` timestamp NULL DEFAULT NULL,
  `cochee_par` bigint UNSIGNED DEFAULT NULL,
  `cree_par` bigint UNSIGNED DEFAULT NULL,
  `poids` smallint UNSIGNED NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `modalites` text COLLATE utf8mb4_unicode_ci,
  `conditions` text COLLATE utf8mb4_unicode_ci,
  `statut_validation` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'validee',
  `valide_par` bigint UNSIGNED DEFAULT NULL,
  `valide_le` timestamp NULL DEFAULT NULL,
  `motif_refus` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `sous_taches_cochee_par_foreign` (`cochee_par`),
  KEY `sous_taches_cree_par_foreign` (`cree_par`),
  KEY `sous_taches_tache_id_ordre_index` (`tache_id`,`ordre`),
  KEY `sous_taches_statut_validation_index` (`statut_validation`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `sous_taches`
--

INSERT INTO `sous_taches` (`id`, `tache_id`, `personnel_id`, `libelle`, `description`, `echeance`, `ordre`, `cochee`, `cochee_le`, `cochee_par`, `cree_par`, `poids`, `created_at`, `updated_at`, `modalites`, `conditions`, `statut_validation`, `valide_par`, `valide_le`, `motif_refus`) VALUES
(2, 9, NULL, 'Recensement', 'Recenser les rapports', '2026-09-15', 1, 1, '2026-10-08 11:57:53', 1, 1, 1, '2026-10-08 10:26:19', '2026-10-08 11:57:53', NULL, NULL, 'validee', NULL, NULL, NULL),
(3, 9, NULL, 'benchmarking', NULL, '2026-09-24', 2, 0, NULL, NULL, 1, 1, '2026-10-08 10:31:04', '2026-10-08 11:17:04', NULL, NULL, 'validee', NULL, NULL, NULL),
(4, 8, NULL, 'Réunion préalable de restitution', NULL, '2026-10-22', 1, 1, '2026-10-11 17:31:44', 10, 10, 1, '2026-10-11 17:16:00', '2026-10-11 17:31:44', NULL, NULL, 'validee', 9, '2026-10-11 17:24:55', NULL),
(5, 8, NULL, 'Elaboration partiel du document de restitution', NULL, '2026-10-29', 2, 0, NULL, NULL, 10, 1, '2026-10-11 17:27:55', '2026-10-11 17:33:58', NULL, NULL, 'validee', 9, '2026-10-11 17:33:58', NULL),
(6, 8, NULL, 'Restitution des enseignements', NULL, '2026-10-30', 3, 0, NULL, NULL, 10, 1, '2026-10-11 17:29:37', '2026-10-11 17:34:01', NULL, NULL, 'validee', 9, '2026-10-11 17:34:01', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `statuts_execution`
--

DROP TABLE IF EXISTS `statuts_execution`;
CREATE TABLE IF NOT EXISTS `statuts_execution` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `libelle` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `couleur` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `statuts_execution_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `statuts_execution`
--

INSERT INTO `statuts_execution` (`id`, `code`, `libelle`, `couleur`, `actif`, `created_at`, `updated_at`) VALUES
(1, '001', 'Non démarrée', '#5B6B84', 1, '2026-09-26 07:00:25', '2026-09-26 07:00:25'),
(2, '002', 'En cours', '#2B6CB0', 1, '2026-09-26 07:00:25', '2026-09-26 07:00:25'),
(3, '003', 'Bloquée', '#A73B34', 1, '2026-09-26 07:00:25', '2026-09-26 07:00:25'),
(4, '004', 'Suspendue', '#B8790F', 1, '2026-09-26 07:00:25', '2026-09-26 07:00:25'),
(5, '005', 'Terminée', '#2E7D5B', 1, '2026-09-26 07:00:25', '2026-09-26 07:00:25'),
(6, '006', 'Annulée', '#ff0000', 1, '2026-09-26 07:00:25', '2026-09-27 15:35:21');

-- --------------------------------------------------------

--
-- Structure de la table `syntheses_pilotage`
--

DROP TABLE IF EXISTS `syntheses_pilotage`;
CREATE TABLE IF NOT EXISTS `syntheses_pilotage` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `exercice_id` bigint UNSIGNED NOT NULL,
  `periode_type` varchar(12) COLLATE utf8mb4_unicode_ci NOT NULL,
  `periode_valeur` tinyint UNSIGNED DEFAULT NULL,
  `portee_type` varchar(12) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'global',
  `portee_id` bigint UNSIGNED DEFAULT NULL,
  `contenu` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `source` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `modele` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `empreinte` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `auteur_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_synthese_cle` (`exercice_id`,`periode_type`,`periode_valeur`,`portee_type`,`portee_id`),
  KEY `syntheses_pilotage_exercice_id_index` (`exercice_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `syntheses_pilotage`
--

INSERT INTO `syntheses_pilotage` (`id`, `exercice_id`, `periode_type`, `periode_valeur`, `portee_type`, `portee_id`, `contenu`, `source`, `modele`, `empreinte`, `auteur_id`, `created_at`, `updated_at`) VALUES
(1, 1, 'annee', NULL, 'global', NULL, '## Situation d\'ensemble\nAu 11/10/2026, le périmètre « Toute la structure » compte 118 tâches pour l\'exercice 2026. 3 tâche(s) terminée(s) (2,5 %), 2 en cours et 113 non démarrée(s). L\'avancement moyen s\'établit à 0,4 %. À fin Oct, 39,0 % des tâches devaient être achevées pour 2,5 % effectivement terminées (retard de 36,5 points). Sur la période « Exercice 2026 », 50 échéances étaient prévues ; 3 tâche(s) terminée(s) sur la période, dont 3 dans les délais (taux de respect des échéances : 0,0 %). 44 tâches restent en retard à la fin de la période.\n\n## Points forts\n- L\'axe « Harmonisation du cadre juridique et de la gouvernance des organes des EPN / EPH / EPAST /SA » est le plus avancé : 5,9 % de tâches terminées pour un avancement moyen de 1,0 %.\n\n## Points d\'attention\n- 44 tâches en retard, dont 42 sans justification valable.\n- Directions les plus concernées par les retards : Non affectée (25), Direction de l\'Informatique (19).\n- L\'axe « Pilotage, performance et contrôle des EPN » progresse lentement : 0,0 % d\'avancement moyen.\n- L\'axe « Renforcement des capacités et digitalisation des outils de gestion » progresse lentement : 0,0 % d\'avancement moyen.\n\n## Recommandations\n- Relancer les responsables pour justifier ou reporter les 42 retard(s) non justifié(s).\n- Organiser un point de recadrage avec la direction « Non affectée », la plus concernée par les retards.\n- Revoir la planification ou la charge des équipes : moins de 70 % des échéances de la période ont été tenues.\n- Examiner les moyens alloués à l\'axe « Pilotage, performance et contrôle des EPN ».\n- Demander la mise à jour de l\'avancement des tâches avant le prochain point de suivi.', 'modele', NULL, '498870cdefeb5675a3dabd584e42c6ea63599ff9', 1, '2026-10-11 12:56:23', '2026-10-11 12:56:23'),
(2, 1, 'annee', NULL, 'global', NULL, '## Situation d\'ensemble\nAu 11/10/2026, le périmètre « Toute la structure » compte 118 tâches pour l\'exercice 2026. 3 tâche(s) terminée(s) (2,5 %), 2 en cours et 113 non démarrée(s). L\'avancement moyen s\'établit à 0,4 %. À fin Oct, 39,0 % des tâches devaient être achevées pour 2,5 % effectivement terminées (retard de 36,5 points). Sur la période « Exercice 2026 », 50 échéances étaient prévues ; 3 tâche(s) terminée(s) sur la période, dont 3 dans les délais (taux de respect des échéances : 0,0 %). 44 tâches restent en retard à la fin de la période.\n\n## Points forts\n- L\'axe « Harmonisation du cadre juridique et de la gouvernance des organes des EPN / EPH / EPAST /SA » est le plus avancé : 5,9 % de tâches terminées pour un avancement moyen de 1,0 %.\n\n## Points d\'attention\n- 44 tâches en retard, dont 42 sans justification valable.\n- Directions les plus concernées par les retards : Non affectée (25), Direction de l\'Informatique (19).\n- L\'axe « Pilotage, performance et contrôle des EPN » progresse lentement : 0,0 % d\'avancement moyen.\n- L\'axe « Renforcement des capacités et digitalisation des outils de gestion » progresse lentement : 0,0 % d\'avancement moyen.\n\n## Recommandations\n- Relancer les responsables pour justifier ou reporter les 42 retard(s) non justifié(s).\n- Organiser un point de recadrage avec la direction « Non affectée », la plus concernée par les retards.\n- Revoir la planification ou la charge des équipes : moins de 70 % des échéances de la période ont été tenues.\n- Examiner les moyens alloués à l\'axe « Pilotage, performance et contrôle des EPN ».\n- Demander la mise à jour de l\'avancement des tâches avant le prochain point de suivi.', 'modele', NULL, '498870cdefeb5675a3dabd584e42c6ea63599ff9', 1, '2026-10-11 13:06:03', '2026-10-11 13:06:03'),
(3, 1, 'annee', NULL, 'global', NULL, '## Situation d\'ensemble\nAu 11/10/2026, sur l\'ensemble de la structure, le plan 2026 compte 118 tâches : 3 terminées, 2 en cours et 113 non démarrées (95,8 %). Le taux de réalisation est de 2,5 % et l\'avancement moyen de 0,4 %, contre 39,0 % attendus pour octobre, soit un écart de 36,5 points. Le réalisé est resté nul de janvier à septembre. 44 tâches (37,3 %) sont en retard, dont 42 sans justification. Le budget de 425 000 (unité non précisée) n\'affiche aucune réalisation.\n\n## Points forts\n- 3 tâches sont terminées, toutes dans l\'axe « Harmonisation du cadre juridique et de la gouvernance des organes », qui affiche un taux de réalisation de 5,9 % et un avancement moyen de 1,0 %.\n- Aucune tâche n\'est bloquée, suspendue ou annulée, et 2 tâches sont en cours.\n- Aucune diligence de courrier n\'est ouverte', 'ia', 'claude-sonnet-5-5', '498870cdefeb5675a3dabd584e42c6ea63599ff9', 1, '2026-10-11 13:27:59', '2026-10-11 13:27:59'),
(4, 1, 'annee', NULL, 'global', NULL, '## Situation d\'ensemble\n\nAu 11 octobre 2026, l\'exécution de la feuille de route 2026 reste très en deçà de l\'attendu. Le réalisé est de 2,5 % contre 39,0 % attendus pour octobre, soit un écart de 36,5 points. Il est resté nul de janvier à septembre : le seul mouvement observé date d\'octobre, avec 3 tâches terminées sur 118. Sur les 50 échéances de la période, 3 seulement ont été honorées, et 44 tâches sont en retard à ce jour. L\'attendu atteint 45,8 % en décembre, ce qui laisse peu de marge pour combler l\'écart.\n\n## Points forts\n\n- Aucun point fort notable sur la période.\n\n## Points d\'attention\n\n- Écart massif avec l\'attendu : 113 tâches sur 118 (95,8 %) ne sont pas démarrées, l\'avancement moyen est de 0,4 % et le budget réalisé est de 0 FCFA sur 425 000 FCFA.\n- Retards non justifiés : 42 des 44 retards (95,5 %) n\'ont aucune justification. L\'axe « Renforcement des capacités et digitalisation des outils de gestion » concentre 30 retards sur 31 tâches, tous non justifiés. Les 19 tâches de la Direction de l\'Informatique sont toutes en retard et non justifiées.\n- Qualité des données : 99 tâches sur 118 (83,9 %) n\'ont pas de direction porteuse, ce qui empêche toute redevabilité. Les indicateurs sont par ailleurs contradictoires : 3 tâches sont déclarées terminées à temps, mais aucune échéance n\'est comptée comme respectée (taux de respect de 0 %). Le budget réalisé nul alors que 3 tâches sont terminées appelle aussi une vérification.\n- Axe « Pilotage, performance et contrôle des EPN » : aucune des 36 tâches n\'est terminée, l\'avancement est nul et 9 retards ne sont pas justifiés.\n\n## Recommandations\n\n- Affecter une direction porteuse aux 99 tâches non affectées, afin de rendre chaque retard imputable et de fiabiliser le suivi par direction.\n- Exiger une justification ou une replanification des 42 retards non justifiés, en commençant par les 30 de l\'axe « Renforcement des capacités et digitalisation ».\n- Convoquer la Direction de l\'Informatique pour établir un plan de rattrapage des 19 tâches en retard.\n- Réexaminer la planification des 113 tâches non démarrées au regard des cibles de 41,5 % en novembre et 45,8 % en décembre, et réviser ces cibles si elles sont inatteignables.\n- Contrôler la saisie des indicateurs : concilier les 3 tâches à temps avec les 0 échéance respectée, et vérifier le budget réalisé de 0 FCFA sur 425 000 FCFA.', 'ia', 'claude-sonnet-5-5', '369e18bffb24be9e61e726ace4e73d6a4230a7e4', 1, '2026-10-11 13:42:49', '2026-10-11 13:42:49'),
(5, 1, 'annee', NULL, 'global', NULL, '## Situation d\'ensemble\n\nAu 12/10/2026, l\'exécution de la feuille de route est très en deçà de la trajectoire prévue. Le taux de réalisation est de 2,5 % (3 tâches terminées sur 118), contre 39,0 % attendus pour octobre, soit un écart de 36,5 points. Aucune avancée n\'est enregistrée de janvier à septembre ; le premier résultat n\'apparaît qu\'en octobre. 112 tâches ne sont pas démarrées et 43 sont en retard, dont 42 sans justification. Sur 50 échéances arrivées à terme, le taux de respect affiché est de 0,0 %. Le budget réalisé est nul sur 425 000 FCFA.\n\n## Points forts\n\n- Aucun point fort notable sur la période.\n\n## Points d\'attention\n\n- L\'avancée est nulle pendant neuf mois consécutifs (janvier à septembre) et l\'écart avec l\'attendu atteint 36,5 points. L\'attendu de décembre (45,8 %) paraît hors de portée au rythme actuel.\n- 42 des 43 retards ne sont pas justifiés. Ils se concentrent sur l\'axe « Renforcement des capacités et digitalisation des outils de gestion » (30 retards sur 31 tâches, aucune terminée) et sur la Direction de l\'Informatique (19 tâches sur 19 en retard, avancement nul).\n- Qualité des données : 99 tâches sur 118 (83,9 %) n\'ont pas de direction porteuse, dont 23 retards non justifiés. La responsabilité de ces tâches ne peut donc pas être établie.\n- L\'axe « Pilotage, performance et contrôle des EPN » (36 tâches) n\'affiche aucune tâche terminée, un avancement de 0,0 % et 9 retards, tous non justifiés.\n- Des incohérences sont à vérifier : 3 tâches terminées « à temps » pour un taux de respect des échéances de 0,0 %, et un budget réalisé nul alors que 3 tâches sont terminées. Le délai moyen de traitement du courrier n\'est pas disponible (1 courrier reçu, 1 diligence ouverte).\n\n## Recommandations\n\n- Affecter une direction porteuse aux 99 tâches non affectées, afin de fiabiliser le suivi et d\'identifier les responsables.\n- Exiger des directions concernées la justification ou la replanification des 42 retards non justifiés, en commençant par les 30 de l\'axe « Renforcement des capacités et digitalisation des outils de gestion ».\n- Convoquer la Direction de l\'Informatique pour établir un plan de rattrapage des 19 tâches en retard, avec des échéances datées.\n- Lancer sans délai les 112 tâches non démarrées, en priorisant les 43 tâches en retard, et réviser la trajectoire de fin d\'exercice au regard de l\'attendu de 45,8 % en décembre.\n- Contrôler la saisie du budget réalisé (0 FCFA sur 425 000 FCFA) et celle des échéances respectées (0 pour 3 tâches terminées à temps).', 'ia', 'claude-sonnet-5-5', 'b2e01d2455dfe6de5fab6b27d493c957b5e02aeb', 5, '2026-10-12 06:56:05', '2026-10-12 06:56:05'),
(6, 1, 'annee', NULL, 'global', NULL, '## Situation d\'ensemble\n\nÀ la date du 19 octobre 2026, la feuille de route est très en deçà de la trajectoire prévue. L\'avancement réalisé atteint 2,5 % contre 39,0 % attendus, soit un écart de 36,5 points. Aucune progression n\'a été enregistrée de janvier à septembre, et le premier mouvement n\'apparaît qu\'en octobre. Sur 118 tâches, 112 (94,9 %) ne sont pas démarrées et 45 sont en retard, dont 44 sans justification. L\'attendu atteint 45,8 % en décembre, ce qui laisse peu de temps pour résorber l\'écart.\n\n## Points forts\n\n- Aucun point fort notable sur la période.\n\n## Points d\'attention\n\n- Le retard est massif et quasi non justifié : 45 tâches en retard, dont 44 (97,8 %) sans justification. Sur 50 échéances de la période, seules 3 tâches ont été terminées.\n- L\'axe « Renforcement des capacités et digitalisation des outils de gestion » est entièrement en défaut : ses 31 tâches sont en retard, sans justification et sans avancement. La Direction de l\'Informatique est dans le même cas, avec 19 tâches sur 19 en retard et aucune terminée.\n- L\'axe « Pilotage, performance et contrôle des EPN » n\'a aucun avancement (0 %), avec 9 retards non justifiés sur 36 tâches.\n- La qualité des données est insuffisante :\n  - 99 tâches sur 118 (83,9 %) n\'ont pas de direction porteuse.\n  - Le budget réalisé est nul (0 FCFA sur 425 000 FCFA) alors que 3 tâches sont terminées.\n  - Le taux de respect des échéances est de 0 % alors que 3 tâches sont déclarées terminées à temps.\n- Au courrier, l\'unique diligence ouverte est en retard et aucune n\'a été traitée. Le délai moyen de traitement n\'est pas renseigné.\n\n## Recommandations\n\n- Affecter une direction porteuse aux 99 tâches « Non affectée » (83,9 % du total), en commençant par les 26 en retard.\n- Exiger des justifications ou une replanification pour les 44 retards non justifiés, en priorité pour les 31 tâches de l\'axe « Renforcement des capacités et digitalisation ».\n- Tenir une revue avec la Direction de l\'Informatique sur ses 19 tâches en retard, avec un calendrier de démarrage daté.\n- Réviser le calendrier des 112 tâches non démarrées au regard de l\'attendu de 45,8 % en décembre, et arbitrer les priorités.\n- Contrôler la saisie du budget réalisé (0 FCFA sur 425 000 FCFA) et du taux de respect des échéances (0 % pour 3 tâches terminées à temps), puis traiter la diligence en retard (1 ouverte, 0 traitée).', 'ia', 'claude-sonnet-5-5', '9e8e88d8ec9e53fe1d4a58fd9761226b0a1fbca1', 1, '2026-10-19 13:52:35', '2026-10-19 13:52:35');

-- --------------------------------------------------------

--
-- Structure de la table `taches`
--

DROP TABLE IF EXISTS `taches`;
CREATE TABLE IF NOT EXISTS `taches` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action_mesurable_id` bigint UNSIGNED NOT NULL,
  `libelle` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `indicateur` text COLLATE utf8mb4_unicode_ci,
  `livrable` text COLLATE utf8mb4_unicode_ci,
  `ordre` smallint UNSIGNED NOT NULL DEFAULT '0',
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `duree_jours` smallint UNSIGNED DEFAULT NULL,
  `priorite_id` bigint UNSIGNED DEFAULT NULL,
  `budget_estime` decimal(14,2) DEFAULT NULL,
  `statut_execution_id` bigint UNSIGNED NOT NULL,
  `situation_temporelle_id` bigint UNSIGNED DEFAULT NULL,
  `avancement_pourcentage` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `observations` text COLLATE utf8mb4_unicode_ci,
  `derniere_mise_a_jour_le` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `reconduite_de_id` bigint UNSIGNED DEFAULT NULL,
  `reconduite_vers_exercice_id` bigint UNSIGNED DEFAULT NULL,
  `cloture_validation` varchar(12) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cloture_soumise_le` timestamp NULL DEFAULT NULL,
  `cloture_soumise_par` bigint UNSIGNED DEFAULT NULL,
  `cloture_validee_le` timestamp NULL DEFAULT NULL,
  `cloture_validee_par` bigint UNSIGNED DEFAULT NULL,
  `cloture_motif_refus` text COLLATE utf8mb4_unicode_ci,
  `cloture_relance_le` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `taches_code_unique` (`code`),
  KEY `taches_action_mesurable_id_foreign` (`action_mesurable_id`),
  KEY `taches_priorite_id_foreign` (`priorite_id`),
  KEY `taches_statut_execution_id_foreign` (`statut_execution_id`),
  KEY `taches_situation_temporelle_id_foreign` (`situation_temporelle_id`),
  KEY `taches_date_fin_index` (`date_fin`),
  KEY `taches_reconduite_de_id_foreign` (`reconduite_de_id`),
  KEY `taches_reconduite_vers_exercice_id_foreign` (`reconduite_vers_exercice_id`),
  KEY `taches_cloture_validation_index` (`cloture_validation`)
) ENGINE=InnoDB AUTO_INCREMENT=119 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `taches`
--

INSERT INTO `taches` (`id`, `code`, `action_mesurable_id`, `libelle`, `indicateur`, `livrable`, `ordre`, `date_debut`, `date_fin`, `duree_jours`, `priorite_id`, `budget_estime`, `statut_execution_id`, `situation_temporelle_id`, `avancement_pourcentage`, `observations`, `derniere_mise_a_jour_le`, `created_at`, `updated_at`, `reconduite_de_id`, `reconduite_vers_exercice_id`, `cloture_validation`, `cloture_soumise_le`, `cloture_soumise_par`, `cloture_validee_le`, `cloture_validee_par`, `cloture_motif_refus`, `cloture_relance_le`) VALUES
(1, 'TA26-001', 1, 'Réaliser une étude comparative des barèmes de jetons de présence et indemnités de session appliqués dans des structures publiques comparables (nationales et régionales)', 'Étude comparative réalisée et validée (Oui/Non)', 'Note de benchmarking des barèmes de jetons de présence', 1, NULL, NULL, NULL, NULL, NULL, 5, 5, 0, 'Responsable indiqué dans la matrice source : M. Tebili Maxime', '2026-10-08 11:27:05', '2026-09-26 11:34:58', '2026-10-08 11:27:05', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 'TA26-002', 2, 'Chiffrer l\'impact budgétaire du nouveau régime de jetons de présence pour les finances publiques et pour chaque EPN/EPH', 'Note de chiffrage budgétaire produite et validée par les Finances (Oui/Non)', 'Note d\'impact budgétaire', 1, NULL, NULL, NULL, NULL, NULL, 5, 5, 0, 'Responsable indiqué dans la matrice source : M. Tebili Maxime', '2026-10-08 11:27:05', '2026-09-26 11:34:58', '2026-10-08 11:27:05', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 'TA26-003', 3, 'Concerter les EPN/EPH et organes de tutelle sur le projet de barème avant finalisation du décret', 'Compte rendu de concertation produit, avis recueillis auprès d\'un échantillon représentatif d\'EPN/EPH (Oui/Non)', 'Compte rendu de concertation avec les EPN/EPH', 1, NULL, NULL, NULL, NULL, NULL, 5, 5, 0, 'Responsable indiqué dans la matrice source : M. Coulibaly Seydou', '2026-10-08 11:27:05', '2026-09-26 11:34:58', '2026-10-08 11:27:05', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 'TA26-004', 4, 'Recenser exhaustivement les textes législatifs et réglementaires en vigueur applicables aux EPN/EPH et identifier les marges d\'amélioration', 'Nombre de textes recensés ; taux de couverture du recensement (100%)', 'Note d\'analyse des insuffisances des textes soumises au Directeur Général', 1, NULL, '2026-03-30', NULL, NULL, NULL, 1, 4, 0, 'Responsable indiqué dans la matrice source : M. Fofana Mohamed', '2026-10-11 17:08:06', '2026-09-26 11:34:58', '2026-10-11 17:08:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(5, 'TA26-005', 4, 'Consolider une matrice des marges d\'amélioration par thématique', 'Matrice des marges d’amélioration élaborée et consolidée (Oui/Non)', 'Note de transmission de la Matrice des marges d\'amélioration par thématique, au Directeur Général', 2, NULL, '2026-04-15', NULL, 2, 300000.00, 1, 4, 0, 'Responsable indiqué dans la matrice source : M. Coulibaly Seydou', '2026-10-08 11:27:05', '2026-09-26 11:34:58', '2026-10-08 11:27:05', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(6, 'TA26-006', 4, 'Collecter et analyser les cadres juridiques applicables aux opérateurs publics dans ces juridictions', 'Nombre de juridictions collectées et analysées  nombre de cadres juridiques comparés', 'Note d\'analyse comparative (benchmarking)', 3, NULL, '2026-09-15', NULL, NULL, NULL, 1, 4, 0, 'Responsable indiqué dans la matrice source : M. Tebili Maxime', '2026-10-08 11:27:05', '2026-09-26 11:34:58', '2026-10-08 11:27:05', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(7, 'TA26-007', 4, 'Organiser les concertations avec les administrations concernées (DGBF, DGTCP, SGG, ministères sectoriels)', 'taux de participation des administrations concernées (80%)', 'Comptes rendus des séances de concertation', 4, NULL, '2026-12-07', NULL, 1, NULL, 2, 1, 0, 'Responsable indiqué dans la matrice source : M. Tebili Maxime', '2026-10-08 11:27:05', '2026-09-26 11:34:58', '2026-10-08 11:27:05', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(8, 'TA26-008', 4, 'Restituer les enseignements du benchmarking', 'Rapport de benchmarking produit et validé (Oui/Non)', 'Rapport de benchmarking', 5, NULL, '2026-11-30', NULL, NULL, NULL, 2, 1, 33, 'Responsable indiqué dans la matrice source : M. Fofana Mohamed', '2026-10-11 17:34:01', '2026-09-26 11:34:58', '2026-10-11 17:34:01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(9, 'TA26-009', 5, 'Rédiger le rapport de diagnostic consolidant le recensement et le benchmarking', 'Rapport de diagnostic produit et validé (Oui/Non)', 'Rapport de diagnostic du cadre législatif et réglementaire', 1, NULL, '2026-10-15', NULL, 2, 100000.00, 2, 4, 50, 'Responsable indiqué dans la matrice source : M. Tebili Maxime', '2026-10-11 17:43:08', '2026-09-26 11:34:58', '2026-10-11 17:43:08', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(10, 'TA26-010', 5, 'Soumettre le rapport du diagnostic au cabinet du  MPEEP pour validation', 'Rapport de diagnostic trasmis au Cabinet (Oui/Non)', 'Note de transmission du rapport', 2, NULL, '2026-10-15', NULL, 1, 25000.00, 1, 4, 0, 'Responsable indiqué dans la matrice source : M. Tebili Maxime', '2026-10-08 11:27:05', '2026-09-26 11:34:58', '2026-10-08 11:27:05', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(11, 'TA26-011', 5, 'Recruter un cabinet pour l\'élaboration de l\'avant-projet de loi', 'Cabinet recruté et sélectionné (Oui/Non)', 'Le rapport de selection du cabinet', 3, NULL, '2027-04-01', NULL, NULL, NULL, 1, 1, 0, 'Responsable indiqué dans la matrice source : M. Bamba Arnaud', '2026-10-08 11:27:05', '2026-09-26 11:34:58', '2026-10-08 11:27:05', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(12, 'TA26-012', 6, 'Rédiger l\'avant-projet de loi portant réforme du cadre législatif applicable aux EPN ,EPH, EPAST et SA', 'Avant-projet de loi élaboré et finalisé (Oui/Non)', 'Avant-projet de loi', 1, NULL, '2027-07-31', NULL, NULL, NULL, 1, 1, 0, 'Responsable indiqué dans la matrice source : M. Tebili Maxime', '2026-10-08 11:27:05', '2026-09-26 11:34:58', '2026-10-08 11:27:05', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(13, 'TA26-013', 6, 'Rédiger les projets de décrets d\'application afférents', 'Nombre de projets de décrets d’application élaborés', 'Avant-projets de décrets d\'application', 2, NULL, '2027-07-31', NULL, NULL, NULL, 1, 1, 0, 'Responsable indiqué dans la matrice source : M. Tebili Maxime', '2026-10-08 11:27:05', '2026-09-26 11:34:58', '2026-10-08 11:27:05', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(14, 'TA26-014', 6, 'validation de l\'avant-projet de loi par les parties prenantes', 'Taux de participation des parties prenantes consultées (80%)', 'Rapport de validation de l\'avant-projet de loi.', 3, NULL, '2027-09-30', NULL, NULL, NULL, 1, 1, 0, 'Responsable indiqué dans la matrice source : M. Bamba Arnaud', '2026-10-08 11:27:05', '2026-09-26 11:34:58', '2026-10-08 11:27:05', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(15, 'TA26-015', 7, 'Finaliser le dossier de transmission (exposé des motifs, fiches d\'impact)', 'Dossier de transmission complet et finalisé (Oui/Non)', 'Note de transmission de l\'avant-projet de loi', 1, NULL, '2027-10-15', NULL, NULL, NULL, 1, 1, 0, 'Responsable indiqué dans la matrice source : M. Coulibaly Seydou', '2026-10-08 11:27:05', '2026-09-26 11:34:58', '2026-10-08 11:27:05', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(16, 'TA26-016', 7, 'Assurer le suivi de la validation par le Cabinet et répondre aux éventuelles observations', 'Taux de traitement des observations (100%)', 'Dossier de relance (Emeraude, Mail et Appels)', 2, NULL, '2027-11-01', NULL, NULL, NULL, 1, 1, 0, 'Responsable indiqué dans la matrice source : M. Fofana Mohamed', '2026-10-08 11:27:05', '2026-09-26 11:34:58', '2026-10-08 11:27:05', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(17, 'TA26-017', 8, 'Recenser les compétences requises par type de mandat (présidence, membres)', 'Nombre de compétences recensées par type de mandat', 'Note de cadrage des compétences requises', 1, NULL, '2028-01-15', NULL, NULL, NULL, 1, 1, 0, 'Responsable indiqué dans la matrice source : M. Tebili Maxime', '2026-10-08 11:27:05', '2026-09-26 11:34:58', '2026-10-08 11:27:05', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(18, 'TA26-018', 8, 'Consulter les parties prenantes concernées (DGEPN, EPN pilotes, experts)', 'Nombre de parties prenantes consultées', 'Comptes rendus des consultations', 2, NULL, '2028-02-01', NULL, NULL, NULL, 1, 1, 0, 'Responsable indiqué dans la matrice source : M. Coulibaly Seydou', '2026-10-08 11:27:05', '2026-09-26 11:34:58', '2026-10-08 11:27:05', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(19, 'TA26-019', 8, 'Rédiger et valider le référentiel de compétences', 'Référentiel de compétences validé (Oui/Non)', 'Référentiel de compétences des membres des organes de gouvernance', 3, NULL, '2028-02-15', NULL, NULL, NULL, 1, 1, 0, 'Responsable indiqué dans la matrice source : M. Fofana Mohamed', '2026-10-08 11:27:05', '2026-09-26 11:34:58', '2026-10-08 11:27:05', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(20, 'TA26-020', 9, 'Analyser les pratiques comparées en matière de gouvernance des conseils (durée, limite d\'âge, cumul de mandats)', 'Nombre de juridictions / organismes comparés ; nombre de pratiques analysées', 'Note d’analyse comparative des pratiques', 1, NULL, '2028-02-20', NULL, NULL, NULL, 1, 1, 0, 'Responsable indiqué dans la matrice source : M. Tebili Maxime', '2026-10-08 11:27:05', '2026-09-26 11:34:58', '2026-10-08 11:27:05', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(21, 'TA26-021', 9, 'Arrêter les paramètres organiques de la réforme (durée, limite d\'âge, cumul, renouvellement)', 'Nombre de paramètres organiques définis', 'Note de synthèse des paramètres organiques retenus', 2, NULL, '2028-03-01', NULL, NULL, NULL, 1, 1, 0, 'Responsable indiqué dans la matrice source : M. Coulibaly Seydou', '2026-10-08 11:27:05', '2026-09-26 11:34:58', '2026-10-08 11:27:05', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(22, 'TA26-022', 9, 'Valider les paramètres organiques avec la hiérarchie', 'Paramètres organiques validés (Oui/Non)', 'PV de validation des paramètres organiques', 3, NULL, '2028-03-10', NULL, NULL, NULL, 1, 1, 0, 'Responsable indiqué dans la matrice source : M. Coulibaly Seydou', '2026-10-08 11:27:05', '2026-09-26 11:34:58', '2026-10-08 11:27:05', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(23, 'TA26-023', 10, 'Rédiger l\'avant-projet de décret intégrant le référentiel de compétences et les paramètres organiques', 'Avant-projet de décret élaboré et finalisé (Oui/Non)', 'Avant-projet de décret', 1, NULL, '2028-03-18', NULL, NULL, NULL, 1, 1, 0, 'Responsable indiqué dans la matrice source : M. Tebili Maxime', '2026-10-08 11:27:05', '2026-09-26 11:34:58', '2026-10-08 11:27:05', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(24, 'TA26-024', 10, 'Finaliser le dossier de transmission (exposé des motifs, fiche d\'impact)', 'Dossier de transmission complet et finalisé (Oui/Non)', 'Dossier de transmission', 2, NULL, '2028-03-25', NULL, NULL, NULL, 1, 1, 0, 'Responsable indiqué dans la matrice source : M. Tebili Maxime', '2026-10-08 11:27:05', '2026-09-26 11:34:58', '2026-10-08 11:27:05', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(25, 'TA26-025', 11, 'Transmettre formellement le projet de décret au Cabinet du MPEEP', 'Projet de décret transmis au Cabinet (Oui/Non)', 'Bordereau / lettre de transmission au Cabinet du MPEEP', 1, NULL, '2028-03-30', NULL, NULL, NULL, 1, 1, 0, 'Responsable indiqué dans la matrice source : M. Fofana Mohamed', '2026-10-08 11:27:05', '2026-09-26 11:34:58', '2026-10-08 11:27:05', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(26, 'TA26-026', 11, 'Assurer le suivi de la validation par le Cabinet et répondre aux éventuelles observations', 'Nombre d’observations du Cabinet traitées ; dossier validé (Oui/Non)', 'Dossier validé par le Cabinet (observations traitées)', 2, NULL, '2028-04-10', NULL, NULL, NULL, 1, 1, 0, 'Responsable indiqué dans la matrice source : M. Coulibaly Seydou', '2026-10-08 11:27:05', '2026-09-26 11:34:58', '2026-10-08 11:27:05', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(27, 'TA26-027', 12, 'Élaborer les termes de référence de l\'étude', 'TDR de l’étude élaborés (Oui/Non)', 'Projet de TDR de l’étude', 1, NULL, '2028-06-01', NULL, NULL, NULL, 1, 1, 0, 'Responsable indiqué dans la matrice source : M. Coulibaly Seydou', '2026-10-08 11:27:05', '2026-09-26 11:34:58', '2026-10-08 11:27:05', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(28, 'TA26-028', 12, 'Valider les termes de référence de l\'étude', 'TDR de l’étude validés (Oui/Non)', 'TDR validés', 2, NULL, '2028-06-10', NULL, NULL, NULL, 1, 1, 0, 'Responsable indiqué dans la matrice source : M. BAMBA Arnaud', '2026-10-08 11:27:05', '2026-09-26 11:34:58', '2026-10-08 11:27:05', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(29, 'TA26-029', 12, 'Sélectionner le cabinet en charge de l\'étude', 'Cabinet sélectionné et contrat/marché signé (Oui/Non)', 'Contrat / marché signé', 3, NULL, '2028-06-15', NULL, NULL, NULL, 1, 1, 0, 'Responsable indiqué dans la matrice source : M. BAMBA Arnaud', '2026-10-08 11:27:05', '2026-09-26 11:34:58', '2026-10-08 11:27:05', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(30, 'TA26-030', 13, 'Cartographier les missions de l\'ensemble des EPN du portefeuille', 'Taux d\'EPN dont la cartographie des missions a été réalisée (100%)', 'Rapport de cartographie des missions des EPN', 1, NULL, '2028-07-01', NULL, NULL, NULL, 1, 1, 0, 'Responsable indiqué dans la matrice source : M. Coulibaly Seydou', '2026-10-08 11:27:05', '2026-09-26 11:34:58', '2026-10-08 11:27:05', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(31, 'TA26-031', 13, 'Cartographier les secteurs d\'activités de l\'ensemble des EPN du portefeuille', 'Nombre de secteurs d’activités recensés', 'Liste des secteurs d’activités', 2, NULL, '2028-07-02', NULL, NULL, NULL, 1, 1, 0, 'Responsable indiqué dans la matrice source : M. Tebili Maxime', '2026-10-08 11:27:05', '2026-09-26 11:34:58', '2026-10-08 11:27:05', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(32, 'TA26-032', 13, 'Identifier les chevauchements et les redondances de missions', 'Nombre de chevauchements et redondances identifiés', 'Rapport d’identification des chevauchements et redondances', 3, NULL, '2028-07-03', NULL, NULL, NULL, 1, 1, 0, 'Responsable indiqué dans la matrice source : M. Fofana Mohamed', '2026-10-08 11:27:05', '2026-09-26 11:34:58', '2026-10-08 11:27:05', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(33, 'TA26-033', 13, 'Produire les scénarios de fusion, regroupement ou mutualisation', 'Nombre de scénarios de fusion, regroupement ou mutualisation élaborés', 'Matrice des scénarios de fusion, regroupement ou mutualisation', 4, NULL, '2028-07-04', NULL, NULL, NULL, 1, 1, 0, 'Responsable indiqué dans la matrice source : M. Coulibaly Seydou', '2026-10-08 11:27:05', '2026-09-26 11:34:58', '2026-10-08 11:27:05', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(34, 'TA26-034', 13, 'Definir les modalités de fusion, de regroupement ou de mutualisation pour les EPN concernés', 'Nombre de modalités opérationnelles définies par scénario', 'Note de cadrage des modalités', 5, NULL, '2028-07-05', NULL, NULL, NULL, 1, 1, 0, 'Responsable indiqué dans la matrice source : M. Tebili Maxime', '2026-10-08 11:27:05', '2026-09-26 11:34:58', '2026-10-08 11:27:05', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(35, 'TA26-035', 13, 'Évaluer les impacts juridiques, financiers et RH de chaque scénario', 'Nombre de scénarios ayant fait l’objet d’une analyse juridique', 'Rapport d’analyse des impacts juridiques', 6, NULL, '2028-07-06', NULL, NULL, NULL, 1, 1, 0, 'Responsable indiqué dans la matrice source : M. Tebili Maxime', '2026-10-08 11:27:06', '2026-09-26 11:34:58', '2026-10-08 11:27:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(36, 'TA26-036', 14, 'Évaluer les impacts financiers de chaque scénario', 'Nombre de scénarios ayant fait l’objet d’une analyse financière', 'Rapport d’analyse des impacts financiers', 1, NULL, '2028-07-07', NULL, NULL, NULL, 1, 1, 0, 'Responsable indiqué dans la matrice source : M. Fofana Mohamed', '2026-10-08 11:27:06', '2026-09-26 11:34:58', '2026-10-08 11:27:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(37, 'TA26-037', 14, 'Évaluer les impacts sur les RH de chaque scénario', 'Nombre de scénarios ayant fait l’objet d’une analyse RH', 'Rapport d’analyse des impacts RH', 2, NULL, '2028-07-08', NULL, NULL, NULL, 1, 1, 0, 'Responsable indiqué dans la matrice source : M. Coulibaly Seydou', '2026-10-08 11:27:06', '2026-09-26 11:34:58', '2026-10-08 11:27:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(38, 'TA26-038', 14, 'Consolider les resultats de l\'analyse des impacts de chaque scénario', 'Taux de scénarios évalués sur les 3 dimensions ( Juridiques, financiers et RH)', 'Matrice comparative des impacts', 3, NULL, '2028-07-09', NULL, NULL, NULL, 1, 1, 0, 'Responsable indiqué dans la matrice source : M. Tebili Maxime', '2026-10-08 11:27:06', '2026-09-26 11:34:58', '2026-10-08 11:27:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(39, 'TA26-039', 15, 'Rédiger le projet de texte de rattachement de la tutelle financière', 'Projet de texte élaboré et finalisé (Oui/Non)', 'Projet de texte de rattachement de la tutelle financière', 1, NULL, '2028-07-10', NULL, NULL, NULL, 1, 1, 0, 'Responsable indiqué dans la matrice source : M. Fofana Mohamed', '2026-10-08 11:27:06', '2026-09-26 11:34:58', '2026-10-08 11:27:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(40, 'TA26-040', 15, 'Concerter DGBF, DGTCP et SGG sur le projet de texte', 'Nombre de structures ayant pris part aux séances de concertation', 'Compte rendu de concertation', 2, NULL, '2028-07-11', NULL, NULL, NULL, 1, 1, 0, 'Responsable indiqué dans la matrice source : M. Coulibaly Seydou', '2026-10-08 11:27:06', '2026-09-26 11:34:58', '2026-10-08 11:27:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(41, 'TA26-041', 15, 'Faire adopter le texte', 'Texte adopté et publié (Oui/Non)', 'Texte adopté et publié', 3, NULL, '2028-07-12', NULL, NULL, NULL, 1, 1, 0, 'Responsable indiqué dans la matrice source : M. Tebili Maxime', '2026-10-08 11:27:06', '2026-09-26 11:34:58', '2026-10-08 11:27:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(42, 'TA26-042', 16, 'Rédiger le texte de rattachement fonctionnel et organique', 'Projet de texte élaboré et finalisé (Oui/Non)', 'Projet de texte de rattachement fonctionnel et organique', 1, NULL, '2028-07-13', NULL, NULL, NULL, 1, 1, 0, 'Responsable indiqué dans la matrice source : M. Coulibaly Seydou', '2026-10-08 11:27:06', '2026-09-26 11:34:58', '2026-10-08 11:27:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(43, 'TA26-043', 16, 'Faire adopter le texte et opérationnaliser le rattachement', 'Texte adopté et dispositif opérationnel mis en place (Oui/Non)', 'Texte adopté et dispositif opérationnel', 2, NULL, '2028-07-14', NULL, NULL, NULL, 1, 1, 0, 'Responsable indiqué dans la matrice source : M. Fofana Mohamed', '2026-10-08 11:27:06', '2026-09-26 11:34:58', '2026-10-08 11:27:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(44, 'TA26-044', 17, 'Élaborer la méthodologie de contrôle budgétaire différencié', 'Méthodologie élaborée et validée (Oui/Non)', 'Méthodologie de contrôle budgétaire différencié validée', 1, NULL, '2028-07-15', NULL, NULL, NULL, 1, 1, 0, 'Responsable indiqué dans la matrice source : M. Tebili Maxime', '2026-10-08 11:27:06', '2026-09-26 11:34:58', '2026-10-08 11:27:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(45, 'TA26-045', 17, 'Déployer la méthodologie auprès des EPN', 'Nombre d’EPN accompagnés', 'Rapport de déploiement de la méthodologie', 2, NULL, '2028-07-16', NULL, NULL, NULL, 1, 1, 0, 'Responsable indiqué dans la matrice source : M. Coulibaly Seydou', '2026-10-08 11:27:06', '2026-09-26 11:34:58', '2026-10-08 11:27:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(46, 'TA26-046', 18, 'Rédiger le projet de texte réglementaire', 'Projet de texte réglementaire élaboré et finalisé (Oui/Non)', 'Projet de texte réglementaire', 1, NULL, '2028-07-17', NULL, NULL, NULL, 1, 1, 0, 'Responsable indiqué dans la matrice source : M. Tebili Maxime', '2026-10-08 11:27:06', '2026-09-26 11:34:58', '2026-10-08 11:27:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(47, 'TA26-047', 18, 'Faire adopter le texte', 'Texte réglementaire adopté et publié (Oui/Non)', 'Texte réglementaire publié', 2, NULL, '2028-07-18', NULL, NULL, NULL, 1, 1, 0, 'Responsable indiqué dans la matrice source : M. Fofana Mohamed', '2026-10-08 11:27:06', '2026-09-26 11:34:58', '2026-10-08 11:27:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(48, 'TA26-048', 19, 'Définir les critères d\'éligibilité au régime a posteriori', 'Critères d’éligibilité définis et validés (Oui/Non)', 'Grille de critères d’éligibilité', 1, NULL, '2028-07-19', NULL, NULL, NULL, 1, 1, 0, 'Responsable indiqué dans la matrice source : M. Tebili Maxime', '2026-10-08 11:27:06', '2026-09-26 11:34:58', '2026-10-08 11:27:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(49, 'TA26-049', 19, 'Évaluer les EPIC candidats selon les critères', 'Nombre d’EPIC éligibles', 'Liste des EPIC éligibles', 2, NULL, '2028-07-20', NULL, NULL, NULL, 1, 1, 0, 'Responsable indiqué dans la matrice source : M. Coulibaly Seydou', '2026-10-08 11:27:06', '2026-09-26 11:34:58', '2026-10-08 11:27:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(50, 'TA26-050', 20, 'Accompagner les EPIC retenus dans le passage au régime de contrôle a posteriori', 'Nombre d’EPIC passés au régime a posteriori', 'Conventions / actes de passage au régime a posteriori', 1, NULL, '2028-07-21', NULL, NULL, NULL, 1, 1, 0, 'Responsable indiqué dans la matrice source : M. Fofana Mohamed', '2026-10-08 11:27:06', '2026-09-26 11:34:58', '2026-10-08 11:27:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(51, 'TA26-051', 21, 'Réaliser un bilan d\'étape du dispositif de contrôle a posteriori', 'Rapport de bilan produit et validé (Oui/Non)', 'Rapport de bilan', 1, NULL, '2028-07-22', NULL, NULL, NULL, 1, 1, 0, 'Responsable indiqué dans la matrice source : M. Coulibaly Seydou', '2026-10-08 11:27:06', '2026-09-26 11:34:58', '2026-10-08 11:27:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(52, 'TA26-052', 22, 'Élaborer le canevas/la fiche de collecte des données de performance et de gestion', 'Canevas de collecte élaboré et validé (Oui/Non)', 'Canevas / fiche de collecte des données', 1, NULL, '2025-09-01', NULL, NULL, NULL, 1, 4, 0, 'Responsable indiqué dans la matrice source : M. Coulibaly Seydou', '2026-10-08 11:27:06', '2026-09-26 11:34:58', '2026-10-08 11:27:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(53, 'TA26-053', 22, 'Diffuser le canevas de collecte aux tutelles techniques et aux EPN', 'Taux de diffusion du canevas auprès des tutelles et EPN (100%)', 'Note de diffusion du canevas de collecte', 2, NULL, '2026-07-31', NULL, NULL, NULL, 1, 4, 0, 'Responsable indiqué dans la matrice source : M. Fofana Mohamed', '2026-10-08 11:27:06', '2026-09-26 11:34:58', '2026-10-08 11:27:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(54, 'TA26-054', 22, 'Collecter les données transmises par les tutelles techniques et les EPN', 'Taux de collecte des données auprès des EPN (75%) ', 'Base de données des données collectées par EPN', 3, NULL, '2026-12-31', NULL, NULL, NULL, 1, 1, 0, 'Responsable indiqué dans la matrice source : M. Tebili Maxime', '2026-10-08 11:27:06', '2026-09-26 11:34:58', '2026-10-08 11:27:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(55, 'TA26-055', 22, 'Consolider et fiabiliser les données collectées (vérification, relances)', 'Taux de données vérifiées et fiabilisées (80%) ', 'Base de données consolidée et fiabilisée', 4, NULL, '2026-12-31', NULL, NULL, NULL, 1, 1, 0, 'Responsable indiqué dans la matrice source : M. Fofana Mohamed', '2026-10-08 11:27:06', '2026-09-26 11:34:58', '2026-10-08 11:27:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(56, 'TA26-056', 23, 'Élaborer ou actualiser la grille d\'analyse harmonisée de performance et de gestion', 'Grille d’analyse harmonisée élaborée et validée (Oui/Non)', 'Grille d’analyse harmonisée de performance et de gestion', 1, NULL, '2026-12-31', NULL, NULL, NULL, 1, 1, 0, 'Responsable indiqué dans la matrice source : M. Tebili Maxime', '2026-10-08 11:27:06', '2026-09-26 11:34:58', '2026-10-08 11:27:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(57, 'TA26-057', 23, 'Traiter et analyser les données consolidées selon la grille d\'analyse', 'Taux de couverture de l’analyse (100%)', 'Résultats d’analyse par EPN et consolidés', 2, NULL, '2027-02-28', NULL, NULL, NULL, 1, 1, 0, 'Responsable indiqué dans la matrice source : M. Coulibaly Seydou', '2026-10-08 11:27:06', '2026-09-26 11:34:58', '2026-10-08 11:27:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(58, 'TA26-058', 23, 'Identifier les tendances, écarts et enseignements clés', 'Nombre de tendances, écarts et enseignements identifiés', 'Note de synthèse des principaux constats', 3, NULL, '2027-02-28', NULL, NULL, NULL, 1, 1, 0, 'Responsable indiqué dans la matrice source : M. Coulibaly Seydou', '2026-10-08 11:27:06', '2026-09-26 11:34:58', '2026-10-08 11:27:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(59, 'TA26-059', 24, 'Rédiger le projet de rapport national de performance et de gestion', 'Projet de rapport national élaboré (Oui/Non)', 'Projet de rapport national de performance et de gestion', 1, NULL, '2027-03-31', NULL, NULL, NULL, 1, 1, 0, 'Responsable indiqué dans la matrice source : M. Fofana Mohamed', '2026-10-08 11:27:06', '2026-09-26 11:34:58', '2026-10-08 11:27:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(60, 'TA26-060', 24, 'Mettre en forme et éditer le rapport (maquette, graphiques, synthèse exécutive)', 'Rapport final édité et mis en forme (Oui/Non)', 'Rapport national de performance et de gestion des EPN — version finale', 2, NULL, '2027-04-30', NULL, NULL, NULL, 1, 1, 0, 'Responsable indiqué dans la matrice source : M. Coulibaly Seydou', '2026-10-08 11:27:06', '2026-09-26 11:34:58', '2026-10-08 11:27:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(61, 'TA26-061', 24, 'Diffuser le rapport aux parties prenantes (Cabinet du MPEEP, tutelles techniques, EPN)', 'Taux de diffusion du rapport aux parties prenantes (100%)', 'Bordereau de diffusion / liste des destinataires', 3, NULL, '2027-04-30', NULL, NULL, NULL, 1, 1, 0, 'Responsable indiqué dans la matrice source : M. Tebili Maxime', '2026-10-08 11:27:06', '2026-09-26 11:34:58', '2026-10-08 11:27:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(62, 'TA26-062', 25, 'Faire le point sur l\'état d\'avancement des travaux de restitution (recensement des livrables produits à 70 %)', 'Taux d’avancement des travaux de restitution (70%)', 'Note d’état d’avancement de l’étude', 1, NULL, '2026-02-01', NULL, NULL, NULL, 1, 4, 0, 'Responsable indiqué dans la matrice source : M. Tebili Maxime', '2026-10-08 11:27:06', '2026-09-26 11:34:58', '2026-10-08 11:27:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(63, 'TA26-063', 25, 'Planifier les travaux restants avec le cabinet en charge de l\'étude pour atteindre le jalon de 80 % au 2e trimestre', 'Plan de travail actualisé et validé (Oui/Non)', 'Plan de travail actualisé', 2, NULL, '2026-06-01', NULL, NULL, NULL, 1, 4, 0, 'Responsable indiqué dans la matrice source : M. Fofana Mohamed', '2026-10-08 11:27:06', '2026-09-26 11:34:58', '2026-10-08 11:27:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(64, 'TA26-064', 25, 'Poursuivre les séances de restitution avec le cabinet en charge de l\'étude', 'Nombre de séances de restitution réalisées', 'Comptes rendus des séances de restitution', 3, NULL, '2026-07-01', NULL, NULL, NULL, 1, 4, 0, 'Responsable indiqué dans la matrice source : M. Tebili Maxime', '2026-10-08 11:27:06', '2026-09-26 11:34:58', '2026-10-08 11:27:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(65, 'TA26-065', 25, 'Consolider les éléments de restitution reçus', 'Taux de consolidation des éléments de restitution (100%)', 'Dossier de restitution consolidé', 4, NULL, '2026-07-15', NULL, NULL, NULL, 1, 4, 0, 'Responsable indiqué dans la matrice source : M. Tebili Maxime', '2026-10-08 11:27:06', '2026-09-26 11:34:58', '2026-10-08 11:27:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(66, 'TA26-066', 26, 'Organiser les séances de restitution du diagnostic auprès des parties prenantes (Cabinet du MPEEP, ministères techniques, EPN)', 'Taux de participation des parties prenantes (80%)', 'Comptes rendus des séances de restitution du diagnostic', 1, NULL, '2026-07-15', NULL, NULL, NULL, 1, 4, 0, 'Responsable indiqué dans la matrice source : M. Tebili Maxime', '2026-10-08 11:27:06', '2026-09-26 11:34:58', '2026-10-08 11:27:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(67, 'TA26-067', 26, 'Recueillir les observations et avis des parties prenantes sur le diagnostic et le cadre stratégique proposé', 'Taux des parties prenantes ayant formulé des observations (60%)', 'Synthèse des observations recueillies', 2, NULL, '2026-09-30', NULL, NULL, NULL, 1, 4, 0, 'Responsable indiqué dans la matrice source : M. Fofana Mohamed', '2026-10-08 11:27:06', '2026-09-26 11:34:58', '2026-10-08 11:27:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(68, 'TA26-068', 26, 'Intégrer les observations recueillies dans le cadre stratégique de développement, avec le cabinet en charge de l\'étude', 'Taux d’intégration des observations retenues (100%)', 'Cadre stratégique de développement amendé', 3, NULL, '2026-10-10', NULL, NULL, NULL, 1, 2, 0, 'Responsable indiqué dans la matrice source : M. Fofana Mohamed', '2026-10-08 11:27:06', '2026-09-26 11:34:58', '2026-10-08 11:27:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(69, 'TA26-069', 27, 'Finaliser, avec le cabinet en charge de l\'étude, le rapport définitif (diagnostic global et cadre stratégique de développement)', 'Rapport définitif finalisé (Oui/Non)', 'Rapport final de l’étude (diagnostic global et cadre stratégique)', 1, NULL, '2026-10-31', NULL, NULL, NULL, 1, 1, 0, 'Responsable indiqué dans la matrice source : M. Tebili Maxime', '2026-10-08 11:27:06', '2026-09-26 11:34:58', '2026-10-08 11:27:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(70, 'TA26-070', 27, 'Faire valider le rapport final par la hiérarchie (Directeur Général, Cabinet du MPEEP, Ministre)', 'Rapport final validé (Oui/Non)', 'Rapport final validé', 2, NULL, '2026-11-10', NULL, NULL, NULL, 1, 1, 0, 'Responsable indiqué dans la matrice source : M. Tebili Maxime', '2026-10-08 11:27:06', '2026-09-26 11:34:58', '2026-10-08 11:27:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(71, 'TA26-071', 27, 'Organiser la restitution officielle des résultats (atelier de restitution finale)', 'Atelier de restitution finale organisé (Oui/Non)', 'Rapport d’atelier de restitution finale', 3, NULL, '2026-11-30', NULL, NULL, NULL, 1, 1, 0, 'Responsable indiqué dans la matrice source : M. Tebili Maxime', '2026-10-08 11:27:06', '2026-09-26 11:34:58', '2026-10-08 11:27:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(72, 'TA26-072', 27, 'Diffuser le rapport final aux parties prenantes concernées', 'Taux de diffusion du rapport final (100%)', 'Note de diffusion / liste des destinataires', 4, NULL, '2026-12-15', NULL, NULL, NULL, 1, 1, 0, 'Responsable indiqué dans la matrice source : M. Tebili Maxime', '2026-10-08 11:27:06', '2026-09-26 11:34:58', '2026-10-08 11:27:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(73, 'TA26-073', 28, 'Elaboration d\'un texte portant encadrement des COP ', 'Projet de texte élaboré et finalisé (Oui/Non)', 'Projet de texte encadrant les COP', 1, NULL, '2028-02-27', NULL, NULL, NULL, 1, 1, 0, 'Responsable indiqué dans la matrice source : M. Coulibaly Seydou', '2026-10-08 11:27:06', '2026-09-26 11:34:58', '2026-10-08 11:27:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(74, 'TA26-074', 28, 'Recenser les expériences et pratiques existantes de contractualisation (contrats de performance actuels, Rapports Annuels de Performance: Benchmarking)', 'Nombre de références analysées', 'Note de recensement des pratiques existantes', 2, NULL, '2028-03-05', NULL, NULL, NULL, 1, 1, 0, 'Responsable indiqué dans la matrice source : M. Coulibaly Seydou', '2026-10-08 11:27:06', '2026-09-26 11:34:58', '2026-10-08 11:27:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(75, 'TA26-075', 28, 'Définir la méthodologie d\'élaboration des COP (structure du contrat, indicateurs, processus de négociation)', 'Méthodologie d’élaboration des COP définie et validée (Oui/Non)', 'Guide méthodologique d’élaboration des COP', 3, NULL, '2028-03-15', NULL, NULL, NULL, 1, 1, 0, 'Responsable indiqué dans la matrice source : M. Coulibaly Seydou', '2026-10-08 11:27:06', '2026-09-26 11:34:58', '2026-10-08 11:27:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(76, 'TA26-076', 28, 'Élaborer les outils méthodologiques associés (modèle-type de COP, matrice d\'indicateurs, canevas de suivi)', 'Nombre d’outils méthodologiques élaborés et validés', 'Modèle-type de COP, matrice d’indicateurs et canevas de suivi', 4, NULL, '2028-03-20', NULL, NULL, NULL, 1, 1, 0, 'Responsable indiqué dans la matrice source : M. Coulibaly Seydou', '2026-10-08 11:27:06', '2026-09-26 11:34:58', '2026-10-08 11:27:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(77, 'TA26-077', 28, 'Valider le cadre méthodologique avec la hiérarchie', 'Cadre méthodologique validé (Oui/Non)', 'Cadre méthodologique validé', 5, NULL, '2028-03-24', NULL, NULL, NULL, 1, 1, 0, 'Responsable indiqué dans la matrice source : M. Coulibaly Seydou', '2026-10-08 11:27:06', '2026-09-26 11:34:58', '2026-10-08 11:27:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(78, 'TA26-078', 29, 'Définir les rôles et responsabilités des acteurs impliqués (DGEPN, ministères techniques, EPN) dans le processus de contractualisation', 'Nombre de rôles et responsabilités définis', 'Note de répartition des rôles et responsabilités', 1, NULL, '2028-03-28', NULL, NULL, NULL, 1, 1, 0, 'Responsable indiqué dans la matrice source : M. Fofana Mohamed', '2026-10-08 11:27:06', '2026-09-26 11:34:58', '2026-10-08 11:27:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(79, 'TA26-079', 29, 'Définir le circuit de validation et de signature des COP', 'Circuit de validation et de signature formalisé (Oui/Non)', 'Circuit de validation et de signature des COP', 2, NULL, '2028-04-05', NULL, NULL, NULL, 1, 1, 0, 'Responsable indiqué dans la matrice source : M. Fofana Mohamed', '2026-10-08 11:27:06', '2026-09-26 11:34:58', '2026-10-08 11:27:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(80, 'TA26-080', 29, 'Définir les modalités de suivi-évaluation de la mise en œuvre des COP (fréquence des revues, indicateurs de suivi)', 'Dispositif de suivi-évaluation défini et validé (Oui/Non)', 'Dispositif de suivi-évaluation des COP', 3, NULL, '2028-04-15', NULL, NULL, NULL, 1, 1, 0, 'Responsable indiqué dans la matrice source : M. Fofana Mohamed', '2026-10-08 11:27:06', '2026-09-26 11:34:58', '2026-10-08 11:27:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(81, 'TA26-081', 29, 'Valider le cadre organisationnel avec la hiérarchie', 'Cadre organisationnel validé (Oui/Non)', 'Cadre organisationnel validé', 4, NULL, '2028-04-20', NULL, NULL, NULL, 1, 1, 0, 'Responsable indiqué dans la matrice source : M. Fofana Mohamed', '2026-10-08 11:27:06', '2026-09-26 11:34:58', '2026-10-08 11:27:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(82, 'TA26-082', 30, 'Définir le périmètre des audits (gouvernance, conformité réglementaire, contrôle interne, mise en œuvre des COP)', 'Périmètre des audits défini et validé (Oui/Non)', 'Note de cadrage du périmètre des audits', 1, NULL, '2028-04-25', NULL, NULL, NULL, 1, 1, 0, 'Responsable indiqué dans la matrice source : M. Tebili Maxime', '2026-10-08 11:27:06', '2026-09-26 11:34:58', '2026-10-08 11:27:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(83, 'TA26-083', 30, 'Recrutement d\'un cabinet pour l\'élaboration d\'un référentiel d\'audit des EPN', 'Cabinet recruté (Oui/Non)', 'Contrat / marché de recrutement du cabinet', 2, NULL, '2028-04-30', NULL, NULL, NULL, 1, 1, 0, 'Responsable indiqué dans la matrice source : M. BAMBA Arnaud', '2026-10-08 11:27:06', '2026-09-26 11:34:58', '2026-10-08 11:27:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(84, 'TA26-084', 30, 'Élaborer le référentiel méthodologique des audits (guide méthodologique, grille d\'évaluation)', 'Référentiel méthodologique élaboré (Oui/Non)', 'Référentiel méthodologique des audits des EPN', 3, NULL, '2028-05-05', NULL, NULL, NULL, 1, 1, 0, 'Responsable indiqué dans la matrice source : M. Fofana Mohamed', '2026-10-08 11:27:06', '2026-09-26 11:34:58', '2026-10-08 11:27:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(85, 'TA26-085', 30, 'Valider le référentiel méthodologique avec la hiérarchie', 'Référentiel méthodologique validé (Oui/Non)', 'Référentiel méthodologique validé', 4, NULL, '2028-05-10', NULL, NULL, NULL, 1, 1, 0, 'Responsable indiqué dans la matrice source : M. Fofana Mohamed', '2026-10-08 11:27:06', '2026-09-26 11:34:58', '2026-10-08 11:27:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(86, 'TA26-086', 31, 'Définir les critères de priorisation des EPN à auditer, selon une approche fondée sur le risque et les enjeux budgétaires', 'Critères de priorisation définis et validés (Oui/Non)', 'Grille de critères de priorisation', 1, NULL, '2028-05-15', NULL, NULL, NULL, 1, 1, 0, 'Responsable indiqué dans la matrice source : M. Fofana Mohamed', '2026-10-08 11:27:06', '2026-09-26 11:34:58', '2026-10-08 11:27:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(87, 'TA26-087', 31, 'Recenser et prioriser les EPN à auditer selon ces critères', 'Nombre d’EPN prioritaires retenus', 'Liste des EPN prioritaires à auditer', 2, NULL, '2028-05-20', NULL, NULL, NULL, 1, 1, 0, 'Responsable indiqué dans la matrice source : M. Fofana Mohamed', '2026-10-08 11:27:06', '2026-09-26 11:34:58', '2026-10-08 11:27:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(88, 'TA26-088', 32, 'Actualiser le calendrier des groupes restants (directeurs exécutifs et présidents/membres d\'organes de gouvernance)', 'Calendrier actualisé et validé (Oui/Non)', 'Calendrier actualisé des sessions de formation', 1, NULL, '2026-07-31', NULL, NULL, NULL, 1, 4, 0, 'Responsable indiqué dans la matrice source : M. BAMBA Arnaud', '2026-10-08 11:27:06', '2026-09-26 11:34:58', '2026-10-08 11:27:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(89, 'TA26-089', 32, 'Mobiliser les formateurs et l\'INAD-CI pour l\'animation des sessions restantes', 'Nombre de formateurs mobilisés', 'Liste des formateurs mobilisés', 2, NULL, '2026-06-29', NULL, NULL, NULL, 1, 4, 0, 'Responsable indiqué dans la matrice source : M. BAMBA Arnaud', '2026-10-08 11:27:06', '2026-09-26 11:34:58', '2026-10-08 11:27:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(90, 'TA26-090', 32, 'Assurer la logistique des sessions (salle, supports pédagogiques, restauration)', 'Dispositif logistique opérationnel (Oui/Non)', 'Dispositif logistique opérationnel', 3, NULL, '2026-06-30', NULL, NULL, NULL, 1, 4, 0, 'Responsable indiqué dans la matrice source : M. BAMBA Arnaud', '2026-10-08 11:27:07', '2026-09-26 11:34:58', '2026-10-08 11:27:07', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(91, 'TA26-091', 32, 'Dérouler les sessions de formation des groupes concernés', 'Nombre de participants formés', 'Comptes rendus et listes de présence des sessions', 4, NULL, '2026-07-31', NULL, NULL, NULL, 1, 4, 0, 'Responsable indiqué dans la matrice source : M. BAMBA Arnaud', '2026-10-08 11:27:07', '2026-09-26 11:34:58', '2026-10-08 11:27:07', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(92, 'TA26-092', 33, 'Suivre le taux de participation et de satisfaction par session', 'Taux de participation (90%)', 'Tableau de suivi des participations et de satisfaction', 1, NULL, '2026-07-31', NULL, NULL, NULL, 1, 4, 0, 'Responsable indiqué dans la matrice source : M. BAMBA Arnaud', '2026-10-08 11:27:07', '2026-09-26 11:34:58', '2026-10-08 11:27:07', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(93, 'TA26-093', 33, 'Gérer les rattrapages pour les participants absents ou empêchés', 'Nombre de participants ayant bénéficié d’une session de rattrapage', 'Calendrier et comptes rendus des sessions de rattrapage', 2, NULL, '2026-09-30', NULL, NULL, NULL, 1, 4, 0, 'Responsable indiqué dans la matrice source : M. BAMBA Arnaud', '2026-10-08 11:27:07', '2026-09-26 11:34:58', '2026-10-08 11:27:07', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(94, 'TA26-094', 33, 'Consolider les résultats intermédiaires du programme', 'Taux d’avancement du programme (80%)', 'Note d’étape sur l’avancement du programme', 3, NULL, '2026-09-30', NULL, NULL, NULL, 1, 4, 0, 'Responsable indiqué dans la matrice source : M. BAMBA Arnaud', '2026-10-08 11:27:07', '2026-09-26 11:34:58', '2026-10-08 11:27:07', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(95, 'TA26-095', 34, 'Finaliser les dernières sessions de formation restantes', 'Nombre de participants formés', 'Comptes rendus des dernières sessions', 1, NULL, '2026-09-30', NULL, NULL, NULL, 1, 4, 0, 'Responsable indiqué dans la matrice source : M. BAMBA Arnaud', '2026-10-08 11:27:07', '2026-09-26 11:34:58', '2026-10-08 11:27:07', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(96, 'TA26-096', 34, 'Accompagner les EPN dans l\'élaboration des manuels des procedures de fonctionnement', 'Nombre d’EPN accompagnés', 'Manuels de procédures élaborés', 2, NULL, '2026-09-20', NULL, NULL, NULL, 1, 4, 0, 'Responsable indiqué dans la matrice source : M. Tebili Maxime', '2026-10-08 11:27:07', '2026-09-26 11:34:58', '2026-10-08 11:27:07', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(97, 'TA26-097', 34, 'Consolider les données finales de participation et de satisfaction', 'Taux de participation final (90%) ', 'Rapport de synthèse de participation et de satisfaction', 3, NULL, '2026-09-30', NULL, NULL, NULL, 1, 4, 0, 'Responsable indiqué dans la matrice source : M. Tebili Maxime', '2026-10-08 11:27:07', '2026-09-26 11:34:58', '2026-10-08 11:27:07', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(98, 'TA26-098', 34, 'Organiser la cérémonie de clôture du PRGG-EPN 2026', 'Cérémonie de clôture organisée (Oui/Non)', 'Rapport de clôture du PRGG-EPN 2026', 4, NULL, '2026-09-30', NULL, NULL, NULL, 1, 4, 0, 'Responsable indiqué dans la matrice source : M. BAMBA Arnaud', '2026-10-08 11:27:07', '2026-09-26 11:34:58', '2026-10-08 11:27:07', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(99, 'TA26-099', 34, 'Élaborer le rapport final du PRGG-EPN 2026', 'Rapport final élaboré et validé (Oui/Non)', 'Rapport final du PRGG-EPN 2026', 5, NULL, '2026-10-15', NULL, NULL, NULL, 1, 1, 0, 'Responsable indiqué dans la matrice source : M. Tebili Maxime', '2026-10-08 11:27:07', '2026-09-26 11:34:58', '2026-10-08 11:27:07', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(100, 'TA26-100', 35, 'Définir le périmètre et les attentes de la mission (élaboration du manuel de procédures)', 'Périmètre et attentes de la mission définis et validés (Oui/Non)', 'Note de cadrage de la mission', 1, NULL, '2025-09-30', NULL, NULL, NULL, 1, 4, 0, 'Responsable indiqué dans la matrice source : M. CLARI Delaface', '2026-10-08 11:27:07', '2026-09-26 11:34:59', '2026-10-08 11:27:07', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(101, 'TA26-101', 35, 'Rédiger les termes de référence (TDR) de recrutement du consultant/de la structure', 'TDR élaborés et finalisés (Oui/Non)', 'TDR de recrutement du consultant / de la structure', 2, NULL, '2025-12-30', NULL, NULL, NULL, 1, 4, 0, 'Responsable indiqué dans la matrice source : M. CLARI Delaface', '2026-10-08 11:27:07', '2026-09-26 11:34:59', '2026-10-08 11:27:07', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(102, 'TA26-102', 35, 'Faire valider les TDR par la hiérarchie', 'TDR validés (Oui/Non)', 'TDR validés', 3, NULL, '2025-12-30', NULL, NULL, NULL, 1, 4, 0, 'Responsable indiqué dans la matrice source : M. CLARI Delaface', '2026-10-08 11:27:07', '2026-09-26 11:34:59', '2026-10-08 11:27:07', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(103, 'TA26-103', 36, 'Recenser les processus métier et les flux de données du portail e-EPN', 'Nombre de processus métier et flux de données recensés', 'Cartographie des processus et flux de données', 1, NULL, '2026-01-31', NULL, NULL, NULL, 1, 4, 0, 'Responsable indiqué dans la matrice source : M. AKON Akpossan', '2026-10-08 11:27:07', '2026-09-26 11:34:59', '2026-10-08 11:27:07', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(104, 'TA26-104', 36, 'Définir les rôles et responsabilités des utilisateurs (DGEPN, EPN, DSI)', 'Nombre de rôles et responsabilités définis', 'Matrice des rôles et responsabilités', 2, NULL, '2026-01-31', NULL, NULL, NULL, 1, 4, 0, 'Responsable indiqué dans la matrice source : M. AKON Akpossan', '2026-10-08 11:27:07', '2026-09-26 11:34:59', '2026-10-08 11:27:07', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(105, 'TA26-105', 36, 'Définir les procédures de sécurité et de contrôle des données', 'Nombre de procédures de sécurité et de contrôle définies', 'Procédures de sécurité et de contrôle des données', 3, NULL, '2026-03-10', NULL, NULL, NULL, 1, 4, 0, 'Responsable indiqué dans la matrice source : M. AKON Akpossan', '2026-10-08 11:27:07', '2026-09-26 11:34:59', '2026-10-08 11:27:07', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(106, 'TA26-106', 36, 'Rédiger le manuel de procédures consolidé', 'Manuel de procédures consolidé élaboré (Oui/Non)', 'Manuel de procédures du portail e-EPN — projet', 4, NULL, '2026-03-30', NULL, NULL, NULL, 1, 4, 0, 'Responsable indiqué dans la matrice source : M. CLARI Delaface', '2026-10-08 11:27:07', '2026-09-26 11:34:59', '2026-10-08 11:27:07', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(107, 'TA26-107', 37, 'Organiser la session du comité de pilotage pour l\'examen du manuel de procédures', 'Session du comité de pilotage tenue (Oui/Non)', 'Compte rendu du comité de pilotage', 1, NULL, '2026-04-30', NULL, NULL, NULL, 1, 4, 0, 'Responsable indiqué dans la matrice source : M. CLARI Delaface', '2026-10-08 11:27:07', '2026-09-26 11:34:59', '2026-10-08 11:27:07', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(108, 'TA26-108', 37, 'Intégrer les observations du comité et finaliser le manuel', 'Nombre d’observations pertinantes intégrées', 'Manuel de procédures validé', 2, NULL, '2026-05-15', NULL, NULL, NULL, 1, 4, 0, 'Responsable indiqué dans la matrice source : M. CLARI Delaface', '2026-10-08 11:27:07', '2026-09-26 11:34:59', '2026-10-08 11:27:07', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(109, 'TA26-109', 37, 'Diffuser le manuel auprès des parties prenantes en amont du lancement', 'Taux de diffusion du manuel auprès des parties prenantes (100%)', 'Note de diffusion du manuel de procédures', 3, NULL, '2026-09-30', NULL, NULL, NULL, 1, 4, 0, 'Responsable indiqué dans la matrice source : M. CLARI Delaface', '2026-10-08 11:27:07', '2026-09-26 11:34:59', '2026-10-08 11:27:07', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(110, 'TA26-110', 38, 'Finaliser les aspects techniques et logistiques du lancement (plateforme, communication)', 'Dispositif technique et logistique finalisé (Oui/Non)', 'Dispositif de lancement finalisé', 1, NULL, '2026-06-29', NULL, NULL, NULL, 1, 4, 0, 'Responsable indiqué dans la matrice source : M. GNANGBO Kouadio', '2026-10-08 11:27:07', '2026-09-26 11:34:59', '2026-10-08 11:27:07', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(111, 'TA26-111', 38, 'Organiser la cérémonie de lancement officiel', 'Cérémonie de lancement organisée (Oui/Non)', 'Rapport de cérémonie de lancement', 2, NULL, '2026-06-29', NULL, NULL, NULL, 1, 4, 0, 'Responsable indiqué dans la matrice source : M. CLARI Delaface', '2026-10-08 11:27:07', '2026-09-26 11:34:59', '2026-10-08 11:27:07', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(112, 'TA26-112', 38, 'Communiquer sur le lancement auprès des EPN et des parties prenantes', 'Nombre d’EPN et parties prenantes informés', 'Supports de communication / note d’information', 3, NULL, '2026-07-15', NULL, NULL, NULL, 1, 4, 0, 'Responsable indiqué dans la matrice source : M. GNANGBO Kouadio', '2026-10-08 11:27:07', '2026-09-26 11:34:59', '2026-10-08 11:27:07', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(113, 'TA26-113', 39, 'Élaborer le programme et les supports de formation des utilisateurs', 'Programme et supports de formation élaborés et validés (Oui/Non)', 'Programme et supports de formation', 1, NULL, '2026-04-30', NULL, NULL, NULL, 1, 4, 0, 'Responsable indiqué dans la matrice source : M. CLARI Delaface', '2026-10-08 11:27:07', '2026-09-26 11:34:59', '2026-10-08 11:27:07', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `taches` (`id`, `code`, `action_mesurable_id`, `libelle`, `indicateur`, `livrable`, `ordre`, `date_debut`, `date_fin`, `duree_jours`, `priorite_id`, `budget_estime`, `statut_execution_id`, `situation_temporelle_id`, `avancement_pourcentage`, `observations`, `derniere_mise_a_jour_le`, `created_at`, `updated_at`, `reconduite_de_id`, `reconduite_vers_exercice_id`, `cloture_validation`, `cloture_soumise_le`, `cloture_soumise_par`, `cloture_validee_le`, `cloture_validee_par`, `cloture_motif_refus`, `cloture_relance_le`) VALUES
(114, 'TA26-114', 39, 'Organiser les sessions de formation des utilisateurs des EPN pilotes', 'Nombre d’utilisateurs formés', 'Comptes rendus des sessions de formation', 2, NULL, '2026-07-30', NULL, NULL, NULL, 1, 4, 0, 'Responsable indiqué dans la matrice source : M. CLARI Delaface', '2026-10-08 11:27:07', '2026-09-26 11:34:59', '2026-10-08 11:27:07', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(115, 'TA26-115', 39, 'Évaluer le niveau d\'appropriation des utilisateurs formés', 'Taux d’appropriation des utilisateurs formés (70%)', 'Rapport d’évaluation de la formation', 3, NULL, '2026-07-30', NULL, NULL, NULL, 1, 4, 0, 'Responsable indiqué dans la matrice source : M. AKON Akpossan', '2026-10-08 11:27:07', '2026-09-26 11:34:59', '2026-10-08 11:27:07', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(116, 'TA26-116', 40, 'Sélectionner l\'échantillon d\'EPN pilotes', 'Nombre d’EPN pilotes sélectionnés', 'Liste des EPN pilotes retenus', 1, NULL, '2026-07-30', NULL, NULL, NULL, 1, 4, 0, 'Responsable indiqué dans la matrice source : M. CLARI Delaface', '2026-10-08 11:27:07', '2026-09-26 11:34:59', '2026-10-08 11:27:07', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(117, 'TA26-117', 40, 'Assurer l\'exploitation du portail auprès des EPN pilotes', 'Nombre d’EPN pilotes effectivement déployés / nombre d’EPN pilotes prévus', 'Portail déployé auprès des EPN pilotes', 2, NULL, '2026-08-11', NULL, NULL, NULL, 1, 4, 0, 'Responsable indiqué dans la matrice source : M. GNANGBO Kouadio', '2026-10-08 11:27:07', '2026-09-26 11:34:59', '2026-10-08 11:27:07', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(118, 'TA26-118', 40, 'Assurer le suivi et l\'assistance technique post-déploiement', 'Nombre de demandes d’assistance traitées', 'Rapport de suivi et d’assistance technique post-déploiement', 3, NULL, '2026-08-15', NULL, NULL, NULL, 1, 4, 0, 'Responsable indiqué dans la matrice source : M. GNANGBO Kouadio', '2026-10-08 11:27:07', '2026-09-26 11:34:59', '2026-10-08 11:27:07', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `taches_ponctuelles`
--

DROP TABLE IF EXISTS `taches_ponctuelles`;
CREATE TABLE IF NOT EXISTS `taches_ponctuelles` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `libelle` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `priorite_id` bigint UNSIGNED DEFAULT NULL,
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `avancement_pourcentage` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `statut` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'a_faire',
  `validation` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aucune',
  `origine` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'agent',
  `responsable_personnel_id` bigint UNSIGNED NOT NULL,
  `cree_par_user_id` bigint UNSIGNED DEFAULT NULL,
  `direction_id` bigint UNSIGNED DEFAULT NULL,
  `superieurs` json DEFAULT NULL,
  `motif_decision` text COLLATE utf8mb4_unicode_ci,
  `decide_par_user_id` bigint UNSIGNED DEFAULT NULL,
  `decide_le` timestamp NULL DEFAULT NULL,
  `terminee_le` timestamp NULL DEFAULT NULL,
  `annulee_le` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `taches_ponctuelles_code_unique` (`code`),
  KEY `taches_ponctuelles_priorite_id_foreign` (`priorite_id`),
  KEY `taches_ponctuelles_cree_par_user_id_foreign` (`cree_par_user_id`),
  KEY `taches_ponctuelles_decide_par_user_id_foreign` (`decide_par_user_id`),
  KEY `taches_ponctuelles_responsable_personnel_id_statut_index` (`responsable_personnel_id`,`statut`),
  KEY `taches_ponctuelles_date_fin_index` (`date_fin`),
  KEY `taches_ponctuelles_direction_id_index` (`direction_id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `taches_ponctuelles`
--

INSERT INTO `taches_ponctuelles` (`id`, `code`, `libelle`, `description`, `priorite_id`, `date_debut`, `date_fin`, `avancement_pourcentage`, `statut`, `validation`, `origine`, `responsable_personnel_id`, `cree_par_user_id`, `direction_id`, `superieurs`, `motif_decision`, `decide_par_user_id`, `decide_le`, `terminee_le`, `annulee_le`, `created_at`, `updated_at`) VALUES
(19, 'TP26-0001', 'Mise en place du comité de cadrage', 'Réflexion sur les axes stratégiques', 2, '2026-11-09', '2026-11-09', 0, 'a_faire', 'aucune', 'agent', 1, 1, NULL, '[14]', NULL, NULL, NULL, NULL, NULL, '2026-11-08 00:46:39', '2026-11-08 00:46:39');

-- --------------------------------------------------------

--
-- Structure de la table `tache_dependance`
--

DROP TABLE IF EXISTS `tache_dependance`;
CREATE TABLE IF NOT EXISTS `tache_dependance` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tache_id` bigint UNSIGNED NOT NULL,
  `depend_de_tache_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tache_dependance_unique` (`tache_id`,`depend_de_tache_id`),
  KEY `tache_dependance_depend_de_tache_id_foreign` (`depend_de_tache_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tache_historiques`
--

DROP TABLE IF EXISTS `tache_historiques`;
CREATE TABLE IF NOT EXISTS `tache_historiques` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tache_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `champ` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ancienne_valeur` text COLLATE utf8mb4_unicode_ci,
  `nouvelle_valeur` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `tache_historiques_user_id_foreign` (`user_id`),
  KEY `tache_historiques_tache_id_created_at_index` (`tache_id`,`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `tache_historiques`
--

INSERT INTO `tache_historiques` (`id`, `tache_id`, `user_id`, `champ`, `ancienne_valeur`, `nouvelle_valeur`, `created_at`) VALUES
(1, 7, 1, 'priorite_id', NULL, 'Haute', '2026-09-29 20:28:19'),
(2, 7, 1, 'statut_execution_id', 'Non démarrée', 'En cours', '2026-09-29 20:28:19'),
(3, 7, 1, 'avancement_pourcentage', '0 %', '5 %', '2026-09-29 20:28:19'),
(4, 9, 1, 'priorite_id', NULL, 'Moyenne', '2026-10-02 08:15:46'),
(5, 9, 1, 'statut_execution_id', 'Non démarrée', 'En cours', '2026-10-02 08:15:46'),
(6, 9, 1, 'avancement_pourcentage', '0 %', '60 %', '2026-10-02 08:15:46'),
(7, 9, 1, 'date_fin', '2026-09-30', '2026-10-02', '2026-10-02 08:16:54'),
(8, 9, 1, 'date_fin', '2026-10-02', '2026-09-30', '2026-10-02 08:18:21'),
(9, 7, 1, 'date_fin', '2026-11-30', '2026-12-07', '2026-10-02 14:23:42'),
(10, 5, 1, 'priorite_id', NULL, 'Moyenne', '2026-10-02 15:23:02'),
(11, 5, 1, 'responsable', NULL, 'Yves N\'Guessan', '2026-10-02 15:23:02'),
(12, 5, 1, 'pilote', NULL, 'Jean-Baptiste AKON', '2026-10-02 15:23:02'),
(13, 10, 1, 'priorite_id', NULL, 'Haute', '2026-10-02 19:06:33'),
(14, 10, 1, 'responsable', NULL, 'Paul-Landry AKON', '2026-10-02 19:06:33'),
(15, 10, 1, 'pilote', NULL, 'Bolou Maxime TEBILI', '2026-10-02 19:06:33'),
(16, 10, 1, 'date_fin', '2026-10-05', '2026-10-06', '2026-10-03 17:58:27'),
(17, 1, 1, 'avancement_pourcentage', '100 %', '0 %', '2026-10-08 10:07:17'),
(18, 2, 1, 'avancement_pourcentage', '100 %', '0 %', '2026-10-08 10:07:17'),
(19, 3, 1, 'avancement_pourcentage', '100 %', '0 %', '2026-10-08 10:07:17'),
(20, 7, 1, 'avancement_pourcentage', '15 %', '0 %', '2026-10-08 10:07:17'),
(21, 9, 1, 'avancement_pourcentage', '60 %', '0 %', '2026-10-08 10:07:17'),
(22, 9, 1, 'sous_tache', NULL, 'Ajoutée : Recensement', '2026-10-08 10:11:07'),
(23, 9, 1, 'sous_tache', 'Recensement', 'Supprimée', '2026-10-08 10:24:01'),
(24, 9, 1, 'sous_tache', NULL, 'Ajoutée : Recensement', '2026-10-08 10:26:19'),
(25, 9, 1, 'sous_tache', NULL, 'Ajoutée : benchmarking', '2026-10-08 10:31:04'),
(26, 9, 1, 'sous_tache', 'Recensement (à faire)', 'Recensement (faite)', '2026-10-08 10:31:26'),
(27, 9, 1, 'avancement_pourcentage', '0 %', '50 %', '2026-10-08 10:33:50'),
(28, 9, 1, 'sous_tache', 'benchmarking (à faire)', 'benchmarking (faite)', '2026-10-08 11:16:57'),
(29, 9, 1, 'statut_execution_id', 'En cours', 'Terminée', '2026-10-08 11:16:57'),
(30, 9, 1, 'avancement_pourcentage', '50 %', '100 %', '2026-10-08 11:16:57'),
(31, 9, 1, 'sous_tache', 'benchmarking (faite)', 'benchmarking (à faire)', '2026-10-08 11:17:04'),
(32, 9, 1, 'statut_execution_id', 'Terminée', 'En cours', '2026-10-08 11:17:04'),
(33, 9, 1, 'avancement_pourcentage', '100 %', '50 %', '2026-10-08 11:17:04'),
(34, 9, 1, 'sous_tache', 'Recensement (faite)', 'Recensement (à faire)', '2026-10-08 11:57:50'),
(35, 9, 1, 'statut_execution_id', 'En cours', 'Non démarrée', '2026-10-08 11:57:50'),
(36, 9, 1, 'avancement_pourcentage', '50 %', '0 %', '2026-10-08 11:57:50'),
(37, 9, 1, 'sous_tache', 'Recensement (à faire)', 'Recensement (faite)', '2026-10-08 11:57:53'),
(38, 9, 1, 'statut_execution_id', 'Non démarrée', 'En cours', '2026-10-08 11:57:53'),
(39, 9, 1, 'avancement_pourcentage', '0 %', '50 %', '2026-10-08 11:57:53'),
(40, 8, 1, 'responsable', NULL, 'Antonio Chapman KOUADIO', '2026-10-11 17:07:13'),
(41, 8, 1, 'responsable', NULL, 'Antonio Chapman KOUADIO', '2026-10-11 17:07:13'),
(42, 4, 1, 'responsable', NULL, 'Mamadou Diabaté', '2026-10-11 17:08:06'),
(43, 4, 1, 'responsable', NULL, 'Mamadou Diabaté', '2026-10-11 17:08:06'),
(44, 8, 10, 'statut_execution_id', 'Non démarrée', 'Terminée', '2026-10-11 17:31:44'),
(45, 8, 10, 'avancement_pourcentage', '0 %', '100 %', '2026-10-11 17:31:44'),
(46, 8, 9, 'statut_execution_id', 'Terminée', 'En cours', '2026-10-11 17:33:58'),
(47, 8, 9, 'avancement_pourcentage', '100 %', '50 %', '2026-10-11 17:33:58'),
(48, 8, 9, 'avancement_pourcentage', '50 %', '33 %', '2026-10-11 17:34:01'),
(49, 9, 5, 'date_fin', '2026-09-30', '2026-10-15', '2026-10-11 17:43:08');

-- --------------------------------------------------------

--
-- Structure de la table `tache_justifications`
--

DROP TABLE IF EXISTS `tache_justifications`;
CREATE TABLE IF NOT EXISTS `tache_justifications` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tache_id` bigint UNSIGNED NOT NULL,
  `demandeur_id` bigint UNSIGNED NOT NULL,
  `type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `motif` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `commentaire` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `echeance_actuelle` date DEFAULT NULL,
  `echeance_proposee` date DEFAULT NULL,
  `statut` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en_attente',
  `niveau_decision` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `decideur_personnel_id` bigint UNSIGNED DEFAULT NULL,
  `decide_par` bigint UNSIGNED DEFAULT NULL,
  `decision_commentaire` text COLLATE utf8mb4_unicode_ci,
  `decide_le` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tache_justifications_demandeur_id_foreign` (`demandeur_id`),
  KEY `tache_justifications_decideur_personnel_id_foreign` (`decideur_personnel_id`),
  KEY `tache_justifications_decide_par_foreign` (`decide_par`),
  KEY `tache_justifications_tache_id_statut_index` (`tache_id`,`statut`),
  KEY `tache_justifications_statut_niveau_decision_index` (`statut`,`niveau_decision`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `tache_justifications`
--

INSERT INTO `tache_justifications` (`id`, `tache_id`, `demandeur_id`, `type`, `motif`, `commentaire`, `echeance_actuelle`, `echeance_proposee`, `statut`, `niveau_decision`, `decideur_personnel_id`, `decide_par`, `decision_commentaire`, `decide_le`, `created_at`, `updated_at`) VALUES
(1, 9, 1, 'report', 'manque_ressources', 'Bonjour M. le Directeur, je viens par cette demande justifier le retard de la tâche, je souhaiterais un report à cause du manque de ressource humaine.', '2026-09-30', '2026-10-15', 'approuvee', 'DG', NULL, 5, NULL, '2026-10-11 17:43:08', '2026-10-02 13:51:48', '2026-10-11 17:43:08'),
(2, 7, 2, 'report', 'blocage_administratif', 'juste un test', '2026-11-30', '2026-12-07', 'approuvee', 'DG', NULL, 1, 'Demande accordé, veuillez terminer cette tâche dans le délai demandé.', '2026-10-02 14:23:42', '2026-10-02 14:02:15', '2026-10-02 14:23:42'),
(3, 5, 2, 'justification', 'dependance_externe', 'juste un test', '2026-04-15', NULL, 'approuvee', 'DG', NULL, 5, NULL, '2026-10-11 17:42:50', '2026-10-02 15:34:25', '2026-10-11 17:42:50');

-- --------------------------------------------------------

--
-- Structure de la table `types_agent`
--

DROP TABLE IF EXISTS `types_agent`;
CREATE TABLE IF NOT EXISTS `types_agent` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `libelle` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `types_agent_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `types_agent`
--

INSERT INTO `types_agent` (`id`, `code`, `libelle`, `actif`, `created_at`, `updated_at`) VALUES
(1, '001', 'Fonctionnaire', 1, '2026-09-26 07:00:25', '2026-09-27 14:36:01'),
(2, '002', 'Contractuel', 1, '2026-09-26 07:00:25', '2026-09-26 07:00:25'),
(3, '003', 'Stagiaire', 1, '2026-09-26 07:00:25', '2026-09-26 07:00:25'),
(4, '004', 'Consultant', 1, '2026-09-26 07:00:25', '2026-09-26 07:00:25');

-- --------------------------------------------------------

--
-- Structure de la table `types_courrier`
--

DROP TABLE IF EXISTS `types_courrier`;
CREATE TABLE IF NOT EXISTS `types_courrier` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `libelle` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ordre` smallint UNSIGNED NOT NULL DEFAULT '1',
  `actif` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `types_courrier_code_unique` (`code`),
  UNIQUE KEY `types_courrier_libelle_unique` (`libelle`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `types_courrier`
--

INSERT INTO `types_courrier` (`id`, `code`, `libelle`, `ordre`, `actif`, `created_at`, `updated_at`) VALUES
(1, 'LETTRE', 'Lettre', 1, 1, '2026-10-09 09:41:25', '2026-10-09 09:41:25'),
(2, 'NOTE', 'Note de service', 2, 1, '2026-10-09 09:41:25', '2026-10-09 09:41:25'),
(3, 'DEMANDE', 'Demande', 3, 1, '2026-10-09 09:41:25', '2026-10-09 09:41:25'),
(4, 'INVITATION', 'Invitation', 4, 1, '2026-10-09 09:41:25', '2026-10-09 09:41:25'),
(5, 'RAPPORT', 'Rapport', 5, 1, '2026-10-09 09:41:25', '2026-10-09 09:41:25'),
(6, 'AUTRE', 'Autre', 6, 1, '2026-10-09 09:41:25', '2026-10-09 09:41:25');

-- --------------------------------------------------------

--
-- Structure de la table `urgences_courrier`
--

DROP TABLE IF EXISTS `urgences_courrier`;
CREATE TABLE IF NOT EXISTS `urgences_courrier` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `libelle` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `delai_jours` smallint UNSIGNED NOT NULL,
  `couleur` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#2B6CB0',
  `ordre` smallint UNSIGNED NOT NULL DEFAULT '1',
  `actif` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `urgences_courrier_code_unique` (`code`),
  UNIQUE KEY `urgences_courrier_libelle_unique` (`libelle`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `urgences_courrier`
--

INSERT INTO `urgences_courrier` (`id`, `code`, `libelle`, `delai_jours`, `couleur`, `ordre`, `actif`, `created_at`, `updated_at`) VALUES
(1, 'NORMAL', 'Normal', 15, '#2B6CB0', 1, 1, '2026-10-09 09:41:25', '2026-10-09 09:41:25'),
(2, 'URGENT', 'Urgent', 7, '#C77D0A', 2, 1, '2026-10-09 09:41:25', '2026-10-09 09:41:25'),
(3, 'TRES_URGENT', 'Très urgent', 2, '#A73B34', 3, 1, '2026-10-09 09:41:25', '2026-10-09 09:41:25');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `personnel_id` bigint UNSIGNED DEFAULT NULL,
  `profil_acces_id` bigint UNSIGNED DEFAULT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT '1',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `mot_de_passe_provisoire` tinyint(1) NOT NULL DEFAULT '0',
  `mot_de_passe_modifie_le` timestamp NULL DEFAULT NULL,
  `visite_guidee_le` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_personnel_id_unique` (`personnel_id`),
  KEY `users_profil_acces_id_foreign` (`profil_acces_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `personnel_id`, `profil_acces_id`, `actif`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `mot_de_passe_provisoire`, `mot_de_passe_modifie_le`, `visite_guidee_le`) VALUES
(1, 'Paul AKON', 'admin@dgepn.ci', 1, 4, 1, NULL, '$2y$12$d7q5iBS3CoAAeJM5hKhvauB8JRZajmZlw3rceGp55yMKOtdr/lx76', NULL, '2026-09-26 07:53:38', '2026-11-07 22:10:00', 0, NULL, '2026-11-07 22:10:00'),
(2, 'N\'GUESSAN YVES', 'y.nguessan@dgepn.ci', 3, 3, 1, NULL, '$2y$12$ateDAqaJ.aP9qXGS/dxaeeb.dHKXQRlw/9IK29grWIUcG4lq10U12', NULL, '2026-10-01 11:22:24', '2026-10-01 11:22:24', 0, NULL, NULL),
(3, 'Paul AKON', 'akonperso@gmail.com', 9, 3, 1, NULL, '$2y$12$fombRgEHJgGeGiFypsO.DuwQzhFecz0Jx7QOffD.755pkTNxNRi5e', NULL, '2026-10-02 19:08:26', '2026-10-02 19:08:26', 0, NULL, NULL),
(4, 'TEBILI Maxime', 'mister.akonnano@gmail.com', 8, 2, 1, NULL, '$2y$12$LWncoWZ0lNVy72jdE9ChAO.2SOItfg79HDX5JJUnf.r26Dh8GsQKK', NULL, '2026-10-02 19:10:00', '2026-10-02 19:10:00', 0, NULL, NULL),
(5, 'KAMAGATE Daouda', 'akonperso1@gmail.com', 14, 1, 1, NULL, '$2y$12$fg6BFCiXq7HxAuct/6rHk.j3bFtAcjbNCVXEH3bdYy6kmOx3GD69q', NULL, '2026-10-09 15:36:20', '2026-10-09 16:00:49', 0, NULL, NULL),
(6, 'DelaFace', 'clarikdelaface@gmail.com', 15, 2, 1, NULL, '$2y$12$ICsDwX8W2r.ONqCDlEjpEeEqxgug2C7HgE6wqxc9VneBh7yv0nz7S', NULL, '2026-10-09 16:00:28', '2026-10-09 16:00:28', 0, NULL, NULL),
(7, 'Yao Serge', 'paul.akon@uvci.edu.ci', 5, 5, 1, NULL, '$2y$12$GXW1V7dSjeyETEjVVGCSSejo.4/SgqUhgDAz.ctA471Cs8IsYe2lK', NULL, '2026-10-09 16:03:30', '2026-10-09 16:03:30', 0, NULL, NULL),
(8, 'Marie-Thérèse Aka', 'marietherese.aka@dgepn.ci', 10, 6, 1, NULL, '$2y$12$pUrgnDPpSSmLa8OU0theluu2V48LWROYi2dOk1lCo0Wupysy643SG', NULL, '2026-10-11 08:26:04', '2026-10-11 08:26:04', 1, NULL, NULL),
(9, 'Mamadou Diabaté', 'mamadou.diabate@dgepn.ci', 11, 3, 1, NULL, '$2y$12$z44UaV624NZlBHgXbDtUd.wVLwMh0ahQTShH/8JTuT9FtcK7pG/3S', NULL, '2026-10-11 16:45:13', '2026-10-11 16:45:13', 0, NULL, NULL),
(10, 'Chapman Bamba', 'yves.bamba@dgepn.ci', 13, 3, 1, NULL, '$2y$12$uwNmhlEBDY4HAz3In9JTVumGdQvjwON.YIxQe25WuwE3rNrCUGVpW', NULL, '2026-10-11 16:47:04', '2026-10-11 16:47:04', 0, NULL, NULL);

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `actions_cles`
--
ALTER TABLE `actions_cles`
  ADD CONSTRAINT `actions_cles_axe_id_foreign` FOREIGN KEY (`axe_id`) REFERENCES `axes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `actions_cles_direction_id_foreign` FOREIGN KEY (`direction_id`) REFERENCES `directions` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `actions_mesurables`
--
ALTER TABLE `actions_mesurables`
  ADD CONSTRAINT `actions_mesurables_action_cle_id_foreign` FOREIGN KEY (`action_cle_id`) REFERENCES `actions_cles` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `action_cle_direction`
--
ALTER TABLE `action_cle_direction`
  ADD CONSTRAINT `action_cle_direction_action_cle_id_foreign` FOREIGN KEY (`action_cle_id`) REFERENCES `actions_cles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `action_cle_direction_direction_id_foreign` FOREIGN KEY (`direction_id`) REFERENCES `directions` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `affectations`
--
ALTER TABLE `affectations`
  ADD CONSTRAINT `affectations_personnel_id_foreign` FOREIGN KEY (`personnel_id`) REFERENCES `personnel` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `affectations_role_affectation_id_foreign` FOREIGN KEY (`role_affectation_id`) REFERENCES `roles_affectation` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `alertes_envoyees`
--
ALTER TABLE `alertes_envoyees`
  ADD CONSTRAINT `alertes_envoyees_canal_notification_id_foreign` FOREIGN KEY (`canal_notification_id`) REFERENCES `canaux_notification` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `alertes_envoyees_personnel_id_foreign` FOREIGN KEY (`personnel_id`) REFERENCES `personnel` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `alertes_envoyees_regle_alerte_id_foreign` FOREIGN KEY (`regle_alerte_id`) REFERENCES `regles_alerte` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `alertes_envoyees_tache_id_foreign` FOREIGN KEY (`tache_id`) REFERENCES `taches` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `axes`
--
ALTER TABLE `axes`
  ADD CONSTRAINT `axes_exercice_id_foreign` FOREIGN KEY (`exercice_id`) REFERENCES `exercices` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `courriers`
--
ALTER TABLE `courriers`
  ADD CONSTRAINT `courriers_enregistre_par_foreign` FOREIGN KEY (`enregistre_par`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `courriers_type_courrier_id_foreign` FOREIGN KEY (`type_courrier_id`) REFERENCES `types_courrier` (`id`),
  ADD CONSTRAINT `courriers_urgence_id_foreign` FOREIGN KEY (`urgence_id`) REFERENCES `urgences_courrier` (`id`);

--
-- Contraintes pour la table `diligences`
--
ALTER TABLE `diligences`
  ADD CONSTRAINT `diligences_courrier_id_foreign` FOREIGN KEY (`courrier_id`) REFERENCES `courriers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `diligences_imputation_id_foreign` FOREIGN KEY (`imputation_id`) REFERENCES `imputations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `diligences_parent_diligence_fk` FOREIGN KEY (`parent_diligence_id`) REFERENCES `diligences` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `diligences_pris_en_charge_par_foreign` FOREIGN KEY (`pris_en_charge_par`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `diligences_responsable_personnel_id_foreign` FOREIGN KEY (`responsable_personnel_id`) REFERENCES `personnel` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `diligences_solde_par_foreign` FOREIGN KEY (`solde_par`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `diligence_avis`
--
ALTER TABLE `diligence_avis`
  ADD CONSTRAINT `diligence_avis_imputation_destinataire_id_foreign` FOREIGN KEY (`imputation_destinataire_id`) REFERENCES `imputation_destinataires` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `diligence_avis_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `echelle_traitements`
--
ALTER TABLE `echelle_traitements`
  ADD CONSTRAINT `echelle_traitements_classe_id_foreign` FOREIGN KEY (`classe_id`) REFERENCES `classes_agent` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `echelle_traitements_echelon_id_foreign` FOREIGN KEY (`echelon_id`) REFERENCES `echelons` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `echelle_traitements_grade_id_foreign` FOREIGN KEY (`grade_id`) REFERENCES `grades` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `emplois`
--
ALTER TABLE `emplois`
  ADD CONSTRAINT `emplois_famille_emploi_id_foreign` FOREIGN KEY (`famille_emploi_id`) REFERENCES `familles_emploi` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `exercices`
--
ALTER TABLE `exercices`
  ADD CONSTRAINT `exercices_cloture_par_foreign` FOREIGN KEY (`cloture_par`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `imputations`
--
ALTER TABLE `imputations`
  ADD CONSTRAINT `imputations_courrier_id_foreign` FOREIGN KEY (`courrier_id`) REFERENCES `courriers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `imputations_delai_id_foreign` FOREIGN KEY (`delai_id`) REFERENCES `delais_courrier` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `imputations_imputee_par_foreign` FOREIGN KEY (`imputee_par`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `imputations_parent_diligence_fk` FOREIGN KEY (`parent_diligence_id`) REFERENCES `diligences` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `imputation_destinataires`
--
ALTER TABLE `imputation_destinataires`
  ADD CONSTRAINT `imputation_destinataires_diligence_id_foreign` FOREIGN KEY (`diligence_id`) REFERENCES `diligences` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `imputation_destinataires_imputation_id_foreign` FOREIGN KEY (`imputation_id`) REFERENCES `imputations` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `imputation_instruction`
--
ALTER TABLE `imputation_instruction`
  ADD CONSTRAINT `imputation_instruction_imputation_id_foreign` FOREIGN KEY (`imputation_id`) REFERENCES `imputations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `imputation_instruction_instruction_id_foreign` FOREIGN KEY (`instruction_id`) REFERENCES `instructions_courrier` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `journal_activites`
--
ALTER TABLE `journal_activites`
  ADD CONSTRAINT `journal_activites_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `mandats_responsables`
--
ALTER TABLE `mandats_responsables`
  ADD CONSTRAINT `mandats_responsables_cree_par_foreign` FOREIGN KEY (`cree_par`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `mandats_responsables_direction_id_foreign` FOREIGN KEY (`direction_id`) REFERENCES `directions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `mandats_responsables_personnel_id_foreign` FOREIGN KEY (`personnel_id`) REFERENCES `personnel` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `mandats_responsables_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `mandats_responsables_sous_direction_id_foreign` FOREIGN KEY (`sous_direction_id`) REFERENCES `sous_directions` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `personnel`
--
ALTER TABLE `personnel`
  ADD CONSTRAINT `personnel_civilite_id_foreign` FOREIGN KEY (`civilite_id`) REFERENCES `listes_valeurs` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `personnel_classe_id_foreign` FOREIGN KEY (`classe_id`) REFERENCES `classes_agent` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `personnel_echelon_id_foreign` FOREIGN KEY (`echelon_id`) REFERENCES `echelons` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `personnel_emploi_id_foreign` FOREIGN KEY (`emploi_id`) REFERENCES `emplois` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `personnel_fonction_id_foreign` FOREIGN KEY (`fonction_id`) REFERENCES `fonctions` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `personnel_genre_id_foreign` FOREIGN KEY (`genre_id`) REFERENCES `listes_valeurs` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `personnel_grade_id_foreign` FOREIGN KEY (`grade_id`) REFERENCES `grades` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `personnel_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `personnel_statut_matrimonial_id_foreign` FOREIGN KEY (`statut_matrimonial_id`) REFERENCES `listes_valeurs` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `personnel_type_agent_id_foreign` FOREIGN KEY (`type_agent_id`) REFERENCES `types_agent` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `pieces_jointes`
--
ALTER TABLE `pieces_jointes`
  ADD CONSTRAINT `pieces_jointes_depose_par_foreign` FOREIGN KEY (`depose_par`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `postes_imputation`
--
ALTER TABLE `postes_imputation`
  ADD CONSTRAINT `postes_imputation_personnel_id_foreign` FOREIGN KEY (`personnel_id`) REFERENCES `personnel` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `reglages`
--
ALTER TABLE `reglages`
  ADD CONSTRAINT `reglages_modifie_par_foreign` FOREIGN KEY (`modifie_par`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `regle_alerte_canal`
--
ALTER TABLE `regle_alerte_canal`
  ADD CONSTRAINT `regle_alerte_canal_canal_notification_id_foreign` FOREIGN KEY (`canal_notification_id`) REFERENCES `canaux_notification` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `regle_alerte_canal_regle_alerte_id_foreign` FOREIGN KEY (`regle_alerte_id`) REFERENCES `regles_alerte` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `services`
--
ALTER TABLE `services`
  ADD CONSTRAINT `services_direction_id_foreign` FOREIGN KEY (`direction_id`) REFERENCES `directions` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `services_sous_direction_id_foreign` FOREIGN KEY (`sous_direction_id`) REFERENCES `sous_directions` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `sous_directions`
--
ALTER TABLE `sous_directions`
  ADD CONSTRAINT `sous_directions_direction_id_foreign` FOREIGN KEY (`direction_id`) REFERENCES `directions` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `sous_taches`
--
ALTER TABLE `sous_taches`
  ADD CONSTRAINT `sous_taches_cochee_par_foreign` FOREIGN KEY (`cochee_par`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `sous_taches_cree_par_foreign` FOREIGN KEY (`cree_par`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `sous_taches_tache_id_foreign` FOREIGN KEY (`tache_id`) REFERENCES `taches` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `taches`
--
ALTER TABLE `taches`
  ADD CONSTRAINT `taches_action_mesurable_id_foreign` FOREIGN KEY (`action_mesurable_id`) REFERENCES `actions_mesurables` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `taches_priorite_id_foreign` FOREIGN KEY (`priorite_id`) REFERENCES `priorites` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `taches_reconduite_de_id_foreign` FOREIGN KEY (`reconduite_de_id`) REFERENCES `taches` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `taches_reconduite_vers_exercice_id_foreign` FOREIGN KEY (`reconduite_vers_exercice_id`) REFERENCES `exercices` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `taches_situation_temporelle_id_foreign` FOREIGN KEY (`situation_temporelle_id`) REFERENCES `situations_temporelles` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `taches_statut_execution_id_foreign` FOREIGN KEY (`statut_execution_id`) REFERENCES `statuts_execution` (`id`);

--
-- Contraintes pour la table `taches_ponctuelles`
--
ALTER TABLE `taches_ponctuelles`
  ADD CONSTRAINT `taches_ponctuelles_cree_par_user_id_foreign` FOREIGN KEY (`cree_par_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `taches_ponctuelles_decide_par_user_id_foreign` FOREIGN KEY (`decide_par_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `taches_ponctuelles_priorite_id_foreign` FOREIGN KEY (`priorite_id`) REFERENCES `priorites` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `taches_ponctuelles_responsable_personnel_id_foreign` FOREIGN KEY (`responsable_personnel_id`) REFERENCES `personnel` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `tache_dependance`
--
ALTER TABLE `tache_dependance`
  ADD CONSTRAINT `tache_dependance_depend_de_tache_id_foreign` FOREIGN KEY (`depend_de_tache_id`) REFERENCES `taches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tache_dependance_tache_id_foreign` FOREIGN KEY (`tache_id`) REFERENCES `taches` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `tache_historiques`
--
ALTER TABLE `tache_historiques`
  ADD CONSTRAINT `tache_historiques_tache_id_foreign` FOREIGN KEY (`tache_id`) REFERENCES `taches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tache_historiques_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `tache_justifications`
--
ALTER TABLE `tache_justifications`
  ADD CONSTRAINT `tache_justifications_decide_par_foreign` FOREIGN KEY (`decide_par`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tache_justifications_decideur_personnel_id_foreign` FOREIGN KEY (`decideur_personnel_id`) REFERENCES `personnel` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tache_justifications_demandeur_id_foreign` FOREIGN KEY (`demandeur_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `tache_justifications_tache_id_foreign` FOREIGN KEY (`tache_id`) REFERENCES `taches` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_personnel_id_foreign` FOREIGN KEY (`personnel_id`) REFERENCES `personnel` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `users_profil_acces_id_foreign` FOREIGN KEY (`profil_acces_id`) REFERENCES `profils_acces` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
