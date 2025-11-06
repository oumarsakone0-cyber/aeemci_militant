-- ============================================
-- SCRIPT SQL POUR CRÉER LA TABLE cima_registrations
-- Base de données: soget2616263
-- ============================================
-- Cette table stocke les inscriptions CIMA (Certificat d'Inscription aux Modules d'Apprentissage)
-- Seules les personnes avec les rôles suivants peuvent s'inscrire :
-- - Délégué culturel
-- - SR (Secrétaire Régional)
-- - Formateur
-- - Imam
-- ============================================

USE soget2616263;

CREATE TABLE IF NOT EXISTS `cima_registrations` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `matricule_membre` VARCHAR(50) NOT NULL COMMENT 'Matricule du membre inscrit',
  `prenom` VARCHAR(255) NOT NULL COMMENT 'Prénom du membre',
  `nom` VARCHAR(255) NOT NULL COMMENT 'Nom du membre',
  `qualite_membre` VARCHAR(255) DEFAULT NULL COMMENT 'Qualité/rôle du membre (Délégué culturel, SR, Formateur, Imam)',
  `email` VARCHAR(255) DEFAULT NULL COMMENT 'Email du membre',
  `telephone` VARCHAR(50) DEFAULT NULL COMMENT 'Téléphone du membre',
  `ville_ou_commune` VARCHAR(255) DEFAULT NULL COMMENT 'Ville ou commune du membre',
  `photo_membre` VARCHAR(500) DEFAULT NULL COMMENT 'URL de la photo du membre',
  
  -- Informations de paiement
  `somme_payee` DECIMAL(10, 2) DEFAULT 0.00 COMMENT 'Somme payée en F CFA',
  `statut_paiement` ENUM('non_paye', 'partiel', 'paye') DEFAULT 'non_paye' COMMENT 'Statut du paiement',
  `reference_paiement` VARCHAR(255) DEFAULT NULL COMMENT 'Référence du paiement',
  `contact_paiement` VARCHAR(255) DEFAULT NULL COMMENT 'Contact utilisé pour le paiement (téléphone, email, etc.)',
  `montant_inscription` DECIMAL(10, 2) DEFAULT 3000.00 COMMENT 'Montant de l\'inscription (3000 F CFA)',
  
  -- Métadonnées
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Date de création de l\'inscription',
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Date de mise à jour',
  
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_matricule` (`matricule_membre`),
  KEY `idx_matricule` (`matricule_membre`),
  KEY `idx_statut_paiement` (`statut_paiement`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Table des inscriptions CIMA';

-- ============================================
-- VÉRIFICATION (optionnel - décommentez pour vérifier)
-- ============================================
-- DESCRIBE cima_registrations;
-- SHOW INDEX FROM cima_registrations;

