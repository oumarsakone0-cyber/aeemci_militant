-- ============================================
-- SCRIPT SQL POUR MIGRER LA TABLE sr_bureaux VERS LA NOUVELLE STRUCTURE
-- Base de données: soget2616263
-- ============================================
-- Ce script vérifie et migre la table sr_bureaux si elle existe avec l'ancienne structure
-- ============================================

USE soget2616263;

-- Vérifier si la table existe avec l'ancienne structure (matricule_membre au lieu de matricule_responsable)
-- et la migrer si nécessaire

-- 1. Vérifier si la colonne matricule_responsable existe
SET @col_exists = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = 'soget2616263' 
    AND TABLE_NAME = 'sr_bureaux' 
    AND COLUMN_NAME = 'matricule_responsable'
);

-- 2. Si la colonne n'existe pas mais que matricule_membre existe, renommer
SET @old_col_exists = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = 'soget2616263' 
    AND TABLE_NAME = 'sr_bureaux' 
    AND COLUMN_NAME = 'matricule_membre'
);

-- Si la table existe avec l'ancienne structure, la migrer
-- Note: Cette migration doit être exécutée manuellement car MySQL ne supporte pas les IF dynamiques dans les scripts

-- ÉTAPE 1: Vérifier si la table existe
-- Si elle existe avec matricule_membre, renommer la colonne
ALTER TABLE `sr_bureaux` 
CHANGE COLUMN `matricule_membre` `matricule_responsable` VARCHAR(50) NOT NULL COMMENT 'Matricule du responsable (SR, Président sous-comité, Président section)';

-- ÉTAPE 2: Vérifier si la table sr_bureaux_membres existe, sinon la créer
CREATE TABLE IF NOT EXISTS `sr_bureaux_membres` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `bureau_id` INT(11) NOT NULL COMMENT 'ID du bureau',
  `matricule_membre` VARCHAR(50) NOT NULL COMMENT 'Matricule du membre',
  `poste` VARCHAR(255) NOT NULL COMMENT 'Poste du membre dans le bureau',
  `poste_actuel` VARCHAR(255) DEFAULT NULL COMMENT 'Poste actuel du membre dans la base (qualite_membre)',
  `nom_complet` VARCHAR(255) DEFAULT NULL COMMENT 'Nom complet du membre',
  `email` VARCHAR(255) DEFAULT NULL COMMENT 'Email du membre',
  `telephone` VARCHAR(50) DEFAULT NULL COMMENT 'Téléphone du membre',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Date d ajout',
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Date de mise à jour',
  PRIMARY KEY (`id`),
  KEY `idx_bureau_id` (`bureau_id`),
  KEY `idx_matricule_membre` (`matricule_membre`),
  UNIQUE KEY `unique_bureau_membre` (`bureau_id`, `matricule_membre`),
  CONSTRAINT `fk_bureau_membre` FOREIGN KEY (`bureau_id`) REFERENCES `sr_bureaux` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ÉTAPE 3: Vérifier que toutes les colonnes nécessaires existent dans sr_bureaux
-- Ajouter les colonnes manquantes si elles n'existent pas

-- Vérifier et ajouter description si elle n'existe pas
SET @desc_exists = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = 'soget2616263' 
    AND TABLE_NAME = 'sr_bureaux' 
    AND COLUMN_NAME = 'description'
);
-- Note: L'ajout de colonnes doit être fait manuellement ou via un script PHP

-- Pour ajouter les colonnes manquantes, exécutez ces commandes si nécessaire:
-- ALTER TABLE `sr_bureaux` ADD COLUMN IF NOT EXISTS `description` TEXT DEFAULT NULL COMMENT 'Description du bureau';
-- ALTER TABLE `sr_bureaux` ADD COLUMN IF NOT EXISTS `email` VARCHAR(255) DEFAULT NULL COMMENT 'Email du bureau';
-- ALTER TABLE `sr_bureaux` ADD COLUMN IF NOT EXISTS `telephone` VARCHAR(50) DEFAULT NULL COMMENT 'Téléphone du bureau';
-- ALTER TABLE `sr_bureaux` ADD COLUMN IF NOT EXISTS `adresse` VARCHAR(500) DEFAULT NULL COMMENT 'Adresse du bureau';
-- ALTER TABLE `sr_bureaux` ADD COLUMN IF NOT EXISTS `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Date de création';
-- ALTER TABLE `sr_bureaux` ADD COLUMN IF NOT EXISTS `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Date de mise à jour';


