<?php
/**
 * API GESTION DES INSCRIPTIONS CIMA
 * Certificat d'Inscription aux Modules d'Apprentissage
 * Utilise la même base de données que post_api.php
 * 
 * Rôles autorisés pour l'inscription :
 * - Délégué culturel
 * - SR (Secrétaire Régional)
 * - Formateur
 * - Imam
 */

// === HEADERS CORS - DOIT ÊTRE EN PREMIER (AVANT TOUT) ===
// Désactiver l'affichage des erreurs AVANT les headers pour éviter les warnings
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Fonction pour envoyer les headers CORS
function sendCorsHeaders() {
    $allowedOrigins = [
        'http://localhost:5173',
        'http://localhost:5174',
        'http://127.0.0.1:5173',
        'http://127.0.0.1:5174'
    ];
    
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    if (in_array($origin, $allowedOrigins)) {
        header('Access-Control-Allow-Origin: ' . $origin);
    } elseif (!empty($origin) && (strpos($origin, 'localhost') !== false || strpos($origin, '127.0.0.1') !== false)) {
        header('Access-Control-Allow-Origin: ' . $origin);
    } else {
        header('Access-Control-Allow-Origin: http://localhost:5174');
    }
    
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Accept, Origin');
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');
    header('Content-Type: application/json; charset=utf-8');
}

// Envoyer les headers CORS immédiatement
sendCorsHeaders();

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'CORS preflight OK']);
    exit();
}

// === CONFIG DB / CONNEXION (même que post_api.php) ===
$dbname = 'soget2616263';
$username = 'soget2616263';
$password = '0Objectif-';
$pdo = null;
$errors = [];

$tries = [
    "mysql:host=localhost;dbname=$dbname;charset=utf8mb4",
    "mysql:host=127.0.0.1;dbname=$dbname;charset=utf8mb4",
    "mysql:unix_socket=/var/run/mysqld/mysqld.sock;dbname=$dbname;charset=utf8mb4",
    "mysql:unix_socket=/var/lib/mysql/mysql.sock;dbname=$dbname;charset=utf8mb4"
];

foreach ($tries as $dsn) {
    try {
        $pdo = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 30
        ]);
        break;
    } catch (PDOException $e) {
        $errors[] = $dsn.' => '.$e->getMessage();
    }
}

if (!$pdo) {
    error_log('❌ DB connection errors: '.implode(' | ', $errors));
    sendCorsHeaders();
    http_response_code(500);
    echo json_encode(['success'=>false,'error'=>'Impossible de se connecter à la base de données','details'=>$errors]);
    exit();
}

function sendSuccess($data=null, $message=null) {
    $response = ['success' => true];
    if ($message !== null) {
        $response['message'] = $message;
    }
    if ($data !== null) {
        if (is_array($data) && isset($data[0])) {
            $response['data'] = $data;
        } else {
            $response = array_merge($response, is_array($data) ? $data : ['data' => $data]);
        }
    }
    $response['timestamp'] = date('Y-m-d H:i:s');
    echo json_encode($response);
    exit();
}

function sendError($msg, $code=400) {
    http_response_code($code);
    // Si $msg est un tableau, l'utiliser directement, sinon créer un objet avec message
    $errorData = is_array($msg) ? $msg : ['message' => $msg];
    echo json_encode([
        'success' => false,
        'error' => $errorData,
        'code' => $code,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    exit();
}

function sendErrorWithCors($msg, $code=400) {
    // S'assurer que les headers CORS sont envoyés même en cas d'erreur
    sendCorsHeaders();
    sendError($msg, $code);
}

// Rôles autorisés pour l'inscription CIMA
// UNIQUEMENT : Délégué culturel, SR (Secrétaire Régional), Formateur, Imam
$authorizedRoles = [
    'Délégué culturel',
    'Délégué Culturel',
    'délégué culturel',
    'delegue culturel',
    'Delegue culturel',
    'Délégué Culturel AEEMCI',
    'SR',
    'Secrétaire Régional',
    'Secretaire Regional',
    'Secrétaire régional',
    'Secrétaire Régional AEEMCI',
    'Formateur',
    'formateur',
    'Formateur AEEMCI',
    'Imam',
    'imam',
    'Imam AEEMCI'
];

function isAuthorizedRole($qualite) {
    global $authorizedRoles;
    if (empty($qualite)) {
        error_log('⚠️ Qualité vide');
        return false;
    }
    
    $qualite = trim($qualite);
    $qualiteLower = mb_strtolower($qualite);
    
    error_log('🔍 Vérification rôle: "'.$qualite.'" (lowercase: "'.$qualiteLower.'")');
    
    // Vérification exacte dans la liste
    foreach ($authorizedRoles as $role) {
        $roleLower = mb_strtolower(trim($role));
        if ($roleLower === $qualiteLower) {
            error_log('✅ Rôle autorisé trouvé (exact): "'.$role.'"');
            return true;
        }
    }
    
    // Vérification partielle pour les variations (plus flexible)
    // Délégué culturel
    if (stripos($qualite, 'délégué') !== false && stripos($qualite, 'culturel') !== false) {
        error_log('✅ Rôle autorisé trouvé (partiel): Délégué culturel');
        return true;
    }
    if (stripos($qualite, 'delegue') !== false && stripos($qualite, 'culturel') !== false) {
        error_log('✅ Rôle autorisé trouvé (partiel): delegue culturel');
        return true;
    }
    
    // Secrétaire Régional / SR
    if (stripos($qualite, 'secrétaire') !== false && stripos($qualite, 'régional') !== false) {
        error_log('✅ Rôle autorisé trouvé (partiel): Secrétaire Régional');
        return true;
    }
    if (stripos($qualite, 'secretaire') !== false && stripos($qualite, 'regional') !== false) {
        error_log('✅ Rôle autorisé trouvé (partiel): Secretaire Regional');
        return true;
    }
    // SR seul ou dans une chaîne
    if (trim($qualite) === 'SR' || 
        preg_match('/\bSR\b/i', $qualite) || 
        stripos($qualite, ' sr ') !== false || 
        stripos($qualite, ' sr,') !== false ||
        stripos($qualite, ',sr') !== false) {
        error_log('✅ Rôle autorisé trouvé (partiel): SR');
        return true;
    }
    
    // Formateur
    if (stripos($qualite, 'formateur') !== false) {
        error_log('✅ Rôle autorisé trouvé (partiel): Formateur');
        return true;
    }
    
    // Imam
    if (stripos($qualite, 'imam') !== false) {
        error_log('✅ Rôle autorisé trouvé (partiel): Imam');
        return true;
    }
    
    error_log('❌ Rôle non autorisé: "'.$qualite.'"');
    return false;
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';
if(empty($action)) {
    $raw = file_get_contents('php://input');
    $input = json_decode($raw, true);
    $action = $input['action'] ?? '';
}

error_log('🟢 Action CIMA reçue: '.$action);

try {
    switch($action) {
        case 'ping':
            sendSuccess(['status'=>'online','message'=>'API CIMA opérationnelle','time'=>date('Y-m-d H:i:s')]);
            break;
            
        case 'debug_matricule':
            // Endpoint de debug pour voir le rôle exact dans la base
            $matricule = $_GET['matricule'] ?? $_POST['matricule'] ?? '';
            if (empty($matricule)) {
                sendError('Matricule requis');
            }
            
            $matriculeClean = str_replace('-', '', strtoupper(trim($matricule)));
            
            // Utiliser la même logique de détection de colonnes que verify_matricule
            $user = null;
            // La colonne s'appelle "contact" dans la base de données
            $possibleContactColumns = ['contact', 'telephone', 'contact_membre', 'telephone_membre', 'phone', 'tel', 'mobile'];
            $possibleEmailColumns = ['email', 'email_membre', 'courriel'];
            
            try {
                // Construire la requête dynamiquement en testant les colonnes disponibles
                $baseColumns = ['matricule_gen', 'prenom', 'nom', 'qualite_membre', 'ville_ou_commune', 'photo_membre'];
                $selectedColumns = $baseColumns;
                $contactCol = null;
                $emailCol = null;
                
                // Tester quelles colonnes de contact existent
                $descStmt = $pdo->query("DESCRIBE aeemciste_carte_membre");
                $allColumns = $descStmt->fetchAll(PDO::FETCH_COLUMN);
                
                foreach ($possibleContactColumns as $col) {
                    if (in_array($col, $allColumns, true)) {
                        $contactCol = $col;
                        $selectedColumns[] = $col;
                        break;
                    }
                }
                
                foreach ($possibleEmailColumns as $col) {
                    if (in_array($col, $allColumns, true)) {
                        $emailCol = $col;
                        $selectedColumns[] = $col;
                        break;
                    }
                }
                
                $sql = "SELECT " . implode(', ', $selectedColumns) . " 
                        FROM aeemciste_carte_membre 
                        WHERE matricule_gen = ? OR REPLACE(UPPER(matricule_gen), '-', '') = ?
                        LIMIT 1";
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$matricule, $matriculeClean]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Normaliser les noms de colonnes pour le retour
                if ($user) {
                    $user['telephone'] = $user[$contactCol] ?? $user['telephone'] ?? '';
                    $user['email'] = $user[$emailCol] ?? $user['email'] ?? '';
                }
            } catch (PDOException $e) {
                // Si la méthode dynamique échoue, essayer la méthode simple
                error_log('⚠️ Erreur avec colonnes dynamiques (debug_matricule), tentative simple: '.$e->getMessage());
                try {
                    $stmt = $pdo->prepare("
                        SELECT 
                            matricule_gen,
                            prenom,
                            nom,
                            qualite_membre,
                            ville_ou_commune,
                            photo_membre
                        FROM aeemciste_carte_membre 
                        WHERE matricule_gen = ? OR REPLACE(UPPER(matricule_gen), '-', '') = ?
                        LIMIT 1
                    ");
                    $stmt->execute([$matricule, $matriculeClean]);
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($user) {
                        $user['email'] = '';
                        $user['telephone'] = '';
                    }
                } catch (PDOException $e2) {
                    sendError('Erreur SQL: '.$e2->getMessage(), 500);
                }
            }
            
            if (!$user) {
                sendError('Matricule non trouvé', 404);
            }
            
            $qualite = $user['qualite_membre'] ?? '';
            $isAuthorized = isAuthorizedRole($qualite);
            
            sendSuccess([
                'user' => $user,
                'qualite_membre' => $qualite,
                'qualite_membre_length' => strlen($qualite),
                'qualite_membre_hex' => bin2hex($qualite), // Pour voir les caractères cachés
                'is_authorized' => $isAuthorized,
                'authorized_roles_list' => $authorizedRoles
            ], 'Informations de debug');
            break;
            
        case 'verify_matricule':
            $matricule = $_GET['matricule'] ?? $_POST['matricule'] ?? '';
            if (empty($matricule)) {
                sendError('Matricule requis');
            }
            
            // Nettoyer le matricule (enlever les tirets pour la recherche)
            $matriculeClean = str_replace('-', '', strtoupper(trim($matricule)));
            
            // Chercher dans aeemciste_carte_membre
            // Essayer d'abord avec toutes les colonnes possibles
            $user = null;
            // La colonne s'appelle "contact" dans la base de données
            $possibleContactColumns = ['contact', 'telephone', 'contact_membre', 'telephone_membre', 'phone', 'tel', 'mobile'];
            $possibleEmailColumns = ['email', 'email_membre', 'courriel'];
            
            try {
                // Construire la requête dynamiquement en testant les colonnes disponibles
                $baseColumns = ['matricule_gen', 'prenom', 'nom', 'qualite_membre', 'ville_ou_commune', 'photo_membre'];
                $selectedColumns = $baseColumns;
                $contactCol = null;
                $emailCol = null;
                
                // Tester quelles colonnes de contact existent
                $descStmt = $pdo->query("DESCRIBE aeemciste_carte_membre");
                $allColumns = $descStmt->fetchAll(PDO::FETCH_COLUMN);
                
                foreach ($possibleContactColumns as $col) {
                    if (in_array($col, $allColumns, true)) {
                        $contactCol = $col;
                        $selectedColumns[] = $col;
                        break;
                    }
                }
                
                foreach ($possibleEmailColumns as $col) {
                    if (in_array($col, $allColumns, true)) {
                        $emailCol = $col;
                        $selectedColumns[] = $col;
                        break;
                    }
                }
                
                $sql = "SELECT " . implode(', ', $selectedColumns) . " 
                        FROM aeemciste_carte_membre 
                        WHERE matricule_gen = ? OR REPLACE(UPPER(matricule_gen), '-', '') = ?
                        LIMIT 1";
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$matricule, $matriculeClean]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Normaliser les noms de colonnes pour le retour
                if ($user) {
                    $user['telephone'] = $user[$contactCol] ?? $user['telephone'] ?? '';
                    $user['email'] = $user[$emailCol] ?? $user['email'] ?? '';
                }
            } catch (PDOException $e) {
                // Si la méthode dynamique échoue, essayer la méthode simple
                error_log('⚠️ Erreur avec colonnes dynamiques, tentative simple: '.$e->getMessage());
                try {
                    $stmt = $pdo->prepare("
                        SELECT 
                            matricule_gen,
                            prenom,
                            nom,
                            qualite_membre,
                            ville_ou_commune,
                            photo_membre
                        FROM aeemciste_carte_membre 
                        WHERE matricule_gen = ? OR REPLACE(UPPER(matricule_gen), '-', '') = ?
                        LIMIT 1
                    ");
                    $stmt->execute([$matricule, $matriculeClean]);
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($user) {
                        $user['email'] = '';
                        $user['telephone'] = '';
                    }
                } catch (PDOException $e2) {
                    error_log('❌ Erreur SQL verify_matricule: '.$e2->getMessage());
                    sendError('Erreur lors de la recherche du matricule: '.$e2->getMessage(), 500);
                }
            }
            
            if (!$user) {
                sendError('Matricule non trouvé dans la base de données', 404);
            }
            
            // Vérifier si le rôle est autorisé
            $qualite = $user['qualite_membre'] ?? '';
            
            // Log pour debug
            error_log('🔍 Vérification rôle CIMA - Matricule: '.$matricule.', Qualité: "'.$qualite.'"');
            
            if (!isAuthorizedRole($qualite)) {
                $currentRole = !empty($qualite) ? $qualite : 'Aucun rôle défini';
                // Message d'erreur clair indiquant que l'utilisateur ne peut pas s'inscrire
                $errorMsg = 'Vous ne pouvez pas vous inscrire. Votre rôle actuel est : "'.$currentRole.'". Seuls les Délégués culturels, SR (Secrétaire Régional), Formateurs et Imams peuvent s\'inscrire.';
                
                // Retourner aussi les infos utilisateur pour debug dans la réponse
                sendError([
                    'message' => $errorMsg,
                    'current_role' => $currentRole,
                    'qualite_membre_raw' => $qualite,
                    'qualite_membre_length' => strlen($qualite),
                    'user_info' => [
                        'matricule' => $user['matricule_gen'],
                        'prenom' => $user['prenom'] ?? '',
                        'nom' => $user['nom'] ?? '',
                        'qualite_membre' => $qualite
                    ]
                ], 403);
            }
            
            // Vérifier si déjà inscrit (si la table existe)
            $existing = false;
            try {
                $checkStmt = $pdo->prepare("SELECT id FROM cima_registrations WHERE matricule_membre = ? LIMIT 1");
                $checkStmt->execute([$user['matricule_gen']]);
                $existing = $checkStmt->fetch(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                // Si la table n'existe pas encore, on continue quand même
                error_log('⚠️ Table cima_registrations non trouvée (normal si pas encore créée): '.$e->getMessage());
                $existing = false;
            }
            
            // Log pour debug
            error_log('📋 Données utilisateur avant envoi: email='.($user['email'] ?? 'VIDE').', telephone='.($user['telephone'] ?? 'VIDE'));
            
            sendSuccess([
                'user' => [
                    'matricule' => $user['matricule_gen'],
                    'prenom' => $user['prenom'] ?? '',
                    'nom' => $user['nom'] ?? '',
                    'qualite_membre' => $qualite,
                    'email' => $user['email'] ?? '',
                    'telephone' => $user['telephone'] ?? '',
                    'ville_ou_commune' => $user['ville_ou_commune'] ?? '',
                    'photo_membre' => $user['photo_membre'] ?? ''
                ],
                'already_registered' => $existing !== false,
                'registration_id' => $existing ? $existing['id'] : null
            ], 'Matricule vérifié avec succès');
            break;
            
        case 'create_registration':
            $input = json_decode(file_get_contents('php://input'), true);
            
            $matricule = $input['matricule_membre'] ?? '';
            // Le montant est fixe à 3000 F CFA, sera mis à jour après paiement
            $sommePayee = floatval($input['somme_payee'] ?? 0);
            // Le statut sera calculé automatiquement, mais par défaut 'non_paye'
            $statutPaiement = $input['statut_paiement'] ?? 'non_paye';
            // La référence sera remplie après paiement
            $referencePaiement = $input['reference_paiement'] ?? '';
            $contactPaiement = $input['contact_paiement'] ?? '';
            
            // Calculer automatiquement le statut de paiement basé sur la somme payée
            $montantInscription = 3000.00;
            if ($sommePayee >= $montantInscription) {
                $statutPaiement = 'paye';
            } else if ($sommePayee > 0) {
                $statutPaiement = 'partiel';
            } else {
                $statutPaiement = 'non_paye';
            }
            
            if (empty($matricule)) {
                sendError('Matricule requis');
            }
            
            // Vérifier à nouveau le matricule et le rôle
            $matriculeClean = str_replace('-', '', strtoupper(trim($matricule)));
            
            // Utiliser la même logique de détection de colonnes que verify_matricule
            $user = null;
            // La colonne s'appelle "contact" dans la base de données
            $possibleContactColumns = ['contact', 'telephone', 'contact_membre', 'telephone_membre', 'phone', 'tel', 'mobile'];
            $possibleEmailColumns = ['email', 'email_membre', 'courriel'];
            
            try {
                // Construire la requête dynamiquement en testant les colonnes disponibles
                $baseColumns = ['matricule_gen', 'prenom', 'nom', 'qualite_membre', 'ville_ou_commune', 'photo_membre'];
                $selectedColumns = $baseColumns;
                $contactCol = null;
                $emailCol = null;
                
                // Tester quelles colonnes de contact existent
                $descStmt = $pdo->query("DESCRIBE aeemciste_carte_membre");
                $allColumns = $descStmt->fetchAll(PDO::FETCH_COLUMN);
                
                foreach ($possibleContactColumns as $col) {
                    if (in_array($col, $allColumns, true)) {
                        $contactCol = $col;
                        $selectedColumns[] = $col;
                        break;
                    }
                }
                
                foreach ($possibleEmailColumns as $col) {
                    if (in_array($col, $allColumns, true)) {
                        $emailCol = $col;
                        $selectedColumns[] = $col;
                        break;
                    }
                }
                
                $sql = "SELECT " . implode(', ', $selectedColumns) . " 
                        FROM aeemciste_carte_membre 
                        WHERE matricule_gen = ? OR REPLACE(UPPER(matricule_gen), '-', '') = ?
                        LIMIT 1";
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$matricule, $matriculeClean]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Normaliser les noms de colonnes pour le retour
                if ($user) {
                    $user['telephone'] = $user[$contactCol] ?? $user['telephone'] ?? '';
                    $user['email'] = $user[$emailCol] ?? $user['email'] ?? '';
                }
            } catch (PDOException $e) {
                // Si la méthode dynamique échoue, essayer la méthode simple
                error_log('⚠️ Erreur avec colonnes dynamiques (create_registration), tentative simple: '.$e->getMessage());
                try {
                    $stmt = $pdo->prepare("
                        SELECT 
                            matricule_gen,
                            prenom,
                            nom,
                            qualite_membre,
                            ville_ou_commune,
                            photo_membre
                        FROM aeemciste_carte_membre 
                        WHERE matricule_gen = ? OR REPLACE(UPPER(matricule_gen), '-', '') = ?
                        LIMIT 1
                    ");
                    $stmt->execute([$matricule, $matriculeClean]);
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($user) {
                        $user['email'] = '';
                        $user['telephone'] = '';
                    }
                } catch (PDOException $e2) {
                    error_log('❌ Erreur SQL create_registration: '.$e2->getMessage());
                    sendError('Erreur lors de la recherche du matricule: '.$e2->getMessage(), 500);
                }
            }
            
            if (!$user) {
                sendError('Matricule non trouvé', 404);
            }
            
            $qualite = $user['qualite_membre'] ?? '';
            if (!isAuthorizedRole($qualite)) {
                $currentRole = !empty($qualite) ? $qualite : 'Aucun rôle défini';
                sendError('Vous ne pouvez pas vous inscrire. Votre rôle actuel est : "'.$currentRole.'". Seuls les Délégués culturels, SR (Secrétaire Régional), Formateurs et Imams peuvent s\'inscrire.', 403);
            }
            
            // Vérifier si déjà inscrit (si la table existe)
            try {
                $checkStmt = $pdo->prepare("SELECT id FROM cima_registrations WHERE matricule_membre = ? LIMIT 1");
                $checkStmt->execute([$user['matricule_gen']]);
                if ($checkStmt->fetch()) {
                    sendError('Ce matricule est déjà inscrit', 409);
                }
            } catch (PDOException $e) {
                // Si la table n'existe pas encore, on continue quand même
                error_log('⚠️ Table cima_registrations non trouvée (normal si pas encore créée): '.$e->getMessage());
            }
            
            // Déterminer le statut de paiement en fonction de la somme payée
            $montantInscription = 3000.00;
            if ($sommePayee >= $montantInscription) {
                $statutPaiement = 'paye';
            } elseif ($sommePayee > 0) {
                $statutPaiement = 'partiel';
            } else {
                $statutPaiement = 'non_paye';
            }
            
            // Insérer l'inscription
            $insertStmt = $pdo->prepare("
                INSERT INTO cima_registrations (
                    matricule_membre,
                    prenom,
                    nom,
                    qualite_membre,
                    email,
                    telephone,
                    ville_ou_commune,
                    photo_membre,
                    somme_payee,
                    statut_paiement,
                    reference_paiement,
                    contact_paiement,
                    montant_inscription
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $insertStmt->execute([
                $user['matricule_gen'],
                $user['prenom'] ?? '',
                $user['nom'] ?? '',
                $qualite,
                '', // Email vide car toutes les cases email sont vides
                $user['telephone'] ?? '',
                $user['ville_ou_commune'] ?? '',
                $user['photo_membre'] ?? '',
                $sommePayee,
                $statutPaiement,
                $referencePaiement,
                $contactPaiement,
                $montantInscription
            ]);
            
            $registrationId = $pdo->lastInsertId();
            
            // Récupérer l'inscription créée
            $getStmt = $pdo->prepare("SELECT * FROM cima_registrations WHERE id = ? LIMIT 1");
            $getStmt->execute([$registrationId]);
            $registration = $getStmt->fetch(PDO::FETCH_ASSOC);
            
            sendSuccess($registration, 'Inscription CIMA créée avec succès');
            break;
            
        case 'get_registrations':
            $matricule = $_GET['matricule'] ?? '';
            if (empty($matricule)) {
                sendError('Matricule requis');
            }
            
            $stmt = $pdo->prepare("SELECT * FROM cima_registrations WHERE matricule_membre = ? ORDER BY created_at DESC");
            $stmt->execute([$matricule]);
            $registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            sendSuccess($registrations);
            break;
            
        case 'get_all_registrations':
            // Pour l'administration (optionnel)
            $limit = (int)($_GET['limit'] ?? 50);
            $offset = (int)($_GET['offset'] ?? 0);
            
            $stmt = $pdo->prepare("SELECT * FROM cima_registrations ORDER BY created_at DESC LIMIT ? OFFSET ?");
            $stmt->bindValue(1, $limit, PDO::PARAM_INT);
            $stmt->bindValue(2, $offset, PDO::PARAM_INT);
            $stmt->execute();
            $registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            sendSuccess($registrations);
            break;
            
        case 'update_registration':
            $input = json_decode(file_get_contents('php://input'), true);
            
            $id = $input['id'] ?? 0;
            $matricule = $input['matricule_membre'] ?? '';
            
            if (empty($id) || empty($matricule)) {
                sendError('ID et matricule requis');
            }
            
            // Vérifier que l'inscription appartient au matricule
            $checkStmt = $pdo->prepare("SELECT id FROM cima_registrations WHERE id = ? AND matricule_membre = ? LIMIT 1");
            $checkStmt->execute([$id, $matricule]);
            if (!$checkStmt->fetch()) {
                sendError('Inscription non trouvée ou non autorisée', 404);
            }
            
            $sommePayee = floatval($input['somme_payee'] ?? 0);
            $referencePaiement = $input['reference_paiement'] ?? '';
            $contactPaiement = $input['contact_paiement'] ?? '';
            
            // Déterminer le statut de paiement
            $montantInscription = 3000.00;
            if ($sommePayee >= $montantInscription) {
                $statutPaiement = 'paye';
            } elseif ($sommePayee > 0) {
                $statutPaiement = 'partiel';
            } else {
                $statutPaiement = 'non_paye';
            }
            
            $updateStmt = $pdo->prepare("
                UPDATE cima_registrations 
                SET 
                    somme_payee = ?,
                    statut_paiement = ?,
                    reference_paiement = ?,
                    contact_paiement = ?,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = ? AND matricule_membre = ?
            ");
            
            $updateStmt->execute([
                $sommePayee,
                $statutPaiement,
                $referencePaiement,
                $contactPaiement,
                $id,
                $matricule
            ]);
            
            // Récupérer l'inscription mise à jour
            $getStmt = $pdo->prepare("SELECT * FROM cima_registrations WHERE id = ? LIMIT 1");
            $getStmt->execute([$id]);
            $registration = $getStmt->fetch(PDO::FETCH_ASSOC);
            
            sendSuccess($registration, 'Inscription mise à jour avec succès');
            break;
            
        default:
            sendError('Action non reconnue', 400);
            break;
    }
} catch (PDOException $e) {
    error_log('❌ Erreur SQL CIMA: '.$e->getMessage());
    sendCorsHeaders();
    sendError('Erreur base de données: '.$e->getMessage(), 500);
} catch (Exception $e) {
    error_log('❌ Erreur CIMA: '.$e->getMessage());
    sendCorsHeaders();
    sendError('Erreur: '.$e->getMessage(), 500);
} catch (Throwable $e) {
    error_log('❌ Erreur fatale CIMA: '.$e->getMessage());
    sendCorsHeaders();
    sendError('Erreur fatale: '.$e->getMessage(), 500);
}

