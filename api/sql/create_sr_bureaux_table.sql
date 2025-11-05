-- ============================================
-- SCRIPT SQL POUR CRÉER LA TABLE sr_bureaux
-- Base de données: soget2616263
-- ============================================

-- Table principale pour les bureaux SR (Secrétariats Régionaux)
CREATE TABLE IF NOT EXISTS `sr_bureaux` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `matricule_membre` VARCHAR(50) NOT NULL COMMENT 'Matricule du membre qui crée le bureau',
  `nom_bureau` VARCHAR(255) NOT NULL COMMENT 'Nom du bureau (ex: Bureau de Coordination)',
  `poste` VARCHAR(255) NOT NULL COMMENT 'Poste occupé (ex: Secrétaire Régional)',
  `nom_president` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Nom du président du bureau',
  `nom_vice_president` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Nom du vice-président',
  `nom_tresorier` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Nom du trésorier',
  `nom_secretaire` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Nom du secrétaire',
  `email` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Email de contact du bureau',
  `telephone` VARCHAR(50) NULL DEFAULT NULL COMMENT 'Téléphone de contact',
  `adresse` TEXT NULL DEFAULT NULL COMMENT 'Adresse du bureau',
  `description` TEXT NULL DEFAULT NULL COMMENT 'Description optionnelle du bureau',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Date de création',
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Date de mise à jour',
  PRIMARY KEY (`id`),
  INDEX `idx_matricule_membre` (`matricule_membre`),
  INDEX `idx_poste` (`poste`),
  INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bureaux des Secrétariats Régionaux';

-- ============================================
-- NOTES IMPORTANTES:
-- ============================================
-- 1. Cette table utilise la même base de données que les posts (soget2616263)
-- 2. Les champs obligatoires sont : matricule_membre, nom_bureau, poste, nom_president
-- 3. Tous les autres champs sont optionnels
-- 4. Les index sont créés pour optimiser les requêtes de recherche
-- 5. La table stocke les informations complètes des bureaux SR
-- ============================================

