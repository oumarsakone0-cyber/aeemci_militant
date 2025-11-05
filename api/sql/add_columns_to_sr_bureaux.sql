-- ============================================
-- SCRIPT SQL POUR AJOUTER LES COLONNES À sr_bureaux
-- Base de données: soget2616263
-- ============================================
-- Exécutez ce script dans votre base de données MySQL
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

-- Ajouter description
ALTER TABLE `sr_bureaux` 
ADD COLUMN `description` TEXT NULL DEFAULT NULL COMMENT 'Description optionnelle du bureau' AFTER `adresse`;

-- ============================================
-- VÉRIFICATION (optionnel - décommentez pour vérifier)
-- ============================================
-- DESCRIBE sr_bureaux;

