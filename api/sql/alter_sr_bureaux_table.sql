-- ============================================
-- SCRIPT SQL POUR AJOUTER LES COLONNES
-- Base de données: soget2616263
-- ============================================
-- Ce script ajoute les nouvelles colonnes à la table sr_bureaux existante
-- Si la table n'existe pas, utilisez create_sr_bureaux_table.sql
-- ============================================

-- Ajouter la colonne nom_president
ALTER TABLE `sr_bureaux` 
ADD COLUMN IF NOT EXISTS `nom_president` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Nom du président du bureau' AFTER `poste`;

-- Ajouter la colonne nom_vice_president
ALTER TABLE `sr_bureaux` 
ADD COLUMN IF NOT EXISTS `nom_vice_president` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Nom du vice-président' AFTER `nom_president`;

-- Ajouter la colonne nom_tresorier
ALTER TABLE `sr_bureaux` 
ADD COLUMN IF NOT EXISTS `nom_tresorier` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Nom du trésorier' AFTER `nom_vice_president`;

-- Ajouter la colonne nom_secretaire
ALTER TABLE `sr_bureaux` 
ADD COLUMN IF NOT EXISTS `nom_secretaire` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Nom du secrétaire' AFTER `nom_tresorier`;

-- Ajouter la colonne contact_email
ALTER TABLE `sr_bureaux` 
ADD COLUMN IF NOT EXISTS `contact_email` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Email de contact du bureau' AFTER `nom_secretaire`;

-- Ajouter la colonne contact_telephone
ALTER TABLE `sr_bureaux` 
ADD COLUMN IF NOT EXISTS `contact_telephone` VARCHAR(50) NULL DEFAULT NULL COMMENT 'Téléphone de contact' AFTER `contact_email`;

-- Ajouter la colonne adresse
ALTER TABLE `sr_bureaux` 
ADD COLUMN IF NOT EXISTS `adresse` TEXT NULL DEFAULT NULL COMMENT 'Adresse du bureau' AFTER `contact_telephone`;

-- Ajouter la colonne description (si elle n'existe pas déjà)
ALTER TABLE `sr_bureaux` 
ADD COLUMN IF NOT EXISTS `description` TEXT NULL DEFAULT NULL COMMENT 'Description optionnelle du bureau' AFTER `adresse`;

-- ============================================
-- NOTES IMPORTANTES:
-- ============================================
-- 1. Ce script utilise "ADD COLUMN IF NOT EXISTS" pour éviter les erreurs si la colonne existe déjà
-- 2. Si votre version de MySQL ne supporte pas "IF NOT EXISTS", supprimez-le et exécutez chaque ALTER TABLE séparément
-- 3. Les colonnes sont ajoutées avec NULL par défaut pour ne pas casser les données existantes
-- 4. Pour MySQL < 5.7, utilisez la version alternative ci-dessous
-- ============================================

-- ============================================
-- VERSION ALTERNATIVE (sans IF NOT EXISTS)
-- Pour MySQL < 5.7 ou MariaDB < 10.2
-- ============================================
/*
-- Vérifier et ajouter nom_president
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = 'soget2616263' 
  AND TABLE_NAME = 'sr_bureaux' 
  AND COLUMN_NAME = 'nom_president';
  
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE sr_bureaux ADD COLUMN nom_president VARCHAR(255) NULL DEFAULT NULL COMMENT ''Nom du président du bureau'' AFTER poste',
    'SELECT ''Column nom_president already exists'' AS message');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Répéter pour chaque colonne...
-- (Pour simplifier, utilisez plutôt la version ci-dessous qui ignore les erreurs)
*/

-- ============================================
-- VERSION SIMPLIFIÉE (ignore les erreurs si la colonne existe)
-- ============================================
-- Exécutez chaque commande séparément et ignorez les erreurs "Duplicate column name"

ALTER TABLE `sr_bureaux` 
ADD COLUMN `nom_president` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Nom du président du bureau' AFTER `poste`;

ALTER TABLE `sr_bureaux` 
ADD COLUMN `nom_vice_president` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Nom du vice-président' AFTER `nom_president`;

ALTER TABLE `sr_bureaux` 
ADD COLUMN `nom_tresorier` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Nom du trésorier' AFTER `nom_vice_president`;

ALTER TABLE `sr_bureaux` 
ADD COLUMN `nom_secretaire` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Nom du secrétaire' AFTER `nom_tresorier`;

ALTER TABLE `sr_bureaux` 
ADD COLUMN `contact_email` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Email de contact du bureau' AFTER `nom_secretaire`;

ALTER TABLE `sr_bureaux` 
ADD COLUMN `contact_telephone` VARCHAR(50) NULL DEFAULT NULL COMMENT 'Téléphone de contact' AFTER `contact_email`;

ALTER TABLE `sr_bureaux` 
ADD COLUMN `adresse` TEXT NULL DEFAULT NULL COMMENT 'Adresse du bureau' AFTER `contact_telephone`;

ALTER TABLE `sr_bureaux` 
ADD COLUMN `description` TEXT NULL DEFAULT NULL COMMENT 'Description optionnelle du bureau' AFTER `adresse`;

-- ============================================
-- SUPPRIMER LA COLONNE post_id SI ELLE EXISTE (optionnel)
-- ============================================
-- Si vous voulez supprimer la colonne post_id qui n'est plus utilisée :
-- ALTER TABLE `sr_bureaux` DROP COLUMN IF EXISTS `post_id`;
-- ALTER TABLE `sr_bureaux` DROP FOREIGN KEY IF EXISTS `fk_sr_bureaux_post`;
-- ALTER TABLE `sr_bureaux` DROP INDEX IF EXISTS `idx_post_id`;

-- ============================================
-- VÉRIFICATION
-- ============================================
-- Pour vérifier que toutes les colonnes ont été ajoutées :
-- DESCRIBE sr_bureaux;
-- ou
-- SHOW COLUMNS FROM sr_bureaux;

