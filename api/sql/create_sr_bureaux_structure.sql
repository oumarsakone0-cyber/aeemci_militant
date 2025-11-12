-- ============================================
-- SCRIPT SQL POUR CRÉER LA STRUCTURE DES BUREAUX SR
-- Base de données: soget2616263
-- ============================================
-- Structure : Un responsable (SR, Président sous-comité, Président section) 
-- peut créer un bureau et y ajouter des membres
-- Chaque membre a un poste dans le bureau
-- ============================================

USE soget2616263;

-- Table des bureaux SR (un par responsable)
CREATE TABLE IF NOT EXISTS `sr_bureaux` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `matricule_responsable` VARCHAR(50) NOT NULL COMMENT 'Matricule du responsable (SR, Président sous-comité, Président section)',
  `nom_bureau` VARCHAR(255) NOT NULL COMMENT 'Nom du bureau',
  `description` TEXT DEFAULT NULL COMMENT 'Description du bureau',
  `email` VARCHAR(255) DEFAULT NULL COMMENT 'Email du bureau',
  `telephone` VARCHAR(50) DEFAULT NULL COMMENT 'Téléphone du bureau',
  `adresse` VARCHAR(500) DEFAULT NULL COMMENT 'Adresse du bureau',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Date de création',
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Date de mise à jour',
  PRIMARY KEY (`id`),
  KEY `idx_matricule_responsable` (`matricule_responsable`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des membres des bureaux (plusieurs membres par bureau)
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

