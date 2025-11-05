-- ============================================
-- SCRIPT SQL POUR AJOUTER LES COLONNES À sr_bureaux
-- Base de données: soget2616263
-- Compatible avec toutes les versions de MySQL/MariaDB
-- ============================================
-- 
-- INSTRUCTIONS:
-- 1. Connectez-vous à votre base de données MySQL
-- 2. Exécutez ce script
-- 3. Si une colonne existe déjà, vous obtiendrez une erreur "Duplicate column name" - ignorez-la
-- 4. Vérifiez avec: DESCRIBE sr_bureaux;
-- ============================================

USE soget2616263;

-- Ajouter nom_president
ALTER TABLE `sr_bureaux` 
ADD COLUMN `nom_president` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Nom du président du bureau' AFTER `poste`;

-- Ajouter nom_vice_president
ALTER TABLE `sr_bureaux` 
ADD COLUMN `nom_vice_president` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Nom du vice-président' AFTER `nom_president`;

-- Ajouter nom_tresorier
ALTER TABLE `sr_bureaux` 
ADD COLUMN `nom_tresorier` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Nom du trésorier' AFTER `nom_vice_president`;

-- Ajouter nom_secretaire
ALTER TABLE `sr_bureaux` 
ADD COLUMN `nom_secretaire` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Nom du secrétaire' AFTER `nom_tresorier`;

-- Ajouter email
ALTER TABLE `sr_bureaux` 
ADD COLUMN `email` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Email de contact du bureau' AFTER `nom_secretaire`;

-- Ajouter telephone
ALTER TABLE `sr_bureaux` 
ADD COLUMN `telephone` VARCHAR(50) NULL DEFAULT NULL COMMENT 'Téléphone de contact' AFTER `email`;

-- Ajouter adresse
ALTER TABLE `sr_bureaux` 
ADD COLUMN `adresse` TEXT NULL DEFAULT NULL COMMENT 'Adresse du bureau' AFTER `telephone`;

-- Ajouter description (si elle n'existe pas déjà)
ALTER TABLE `sr_bureaux` 
ADD COLUMN `description` TEXT NULL DEFAULT NULL COMMENT 'Description optionnelle du bureau' AFTER `adresse`;

-- ============================================
-- SUPPRIMER LA COLONNE post_id (optionnel)
-- Décommentez les lignes suivantes si vous voulez supprimer post_id
-- ============================================
-- ALTER TABLE `sr_bureaux` DROP FOREIGN KEY IF EXISTS `fk_sr_bureaux_post`;
-- ALTER TABLE `sr_bureaux` DROP INDEX IF EXISTS `idx_post_id`;
-- ALTER TABLE `sr_bureaux` DROP COLUMN IF EXISTS `post_id`;

-- ============================================
-- VÉRIFICATION
-- ============================================
-- Exécutez cette commande pour voir toutes les colonnes :
-- DESCRIBE sr_bureaux;

