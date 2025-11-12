<?php
/**
 * API GESTION DES BUREAUX SR (Secrétariats Régionaux) - VERSION 2
 * Structure : Un responsable peut créer un bureau et y ajouter des membres
 * Seuls SR, Présidents de sous-comités ou Présidents de section peuvent accéder
 */

// === HEADERS CORS - DOIT ÊTRE EN PREMIER ===
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

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'CORS preflight OK']);
    exit();
}

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// === CONFIG DB ===
$dbname = 'soget2616263';
$username = 'soget2616263';
$password = '0Objectif-';
$pdo = null;

$tries = [
    "mysql:host=localhost;dbname=$dbname;charset=utf8mb4",
    "mysql:host=127.0.0.1;dbname=$dbname;charset=utf8mb4"
];

foreach ($tries as $dsn) {
    try {
        $pdo = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 30
        ]);
        break;
    } catch (PDOException $e) {
        continue;
    }
}

if (!$pdo) {
    http_response_code(500);
    echo json_encode(['success'=>false,'error'=>'Impossible de se connecter à la base de données']);
    exit();
}

function sendSuccess($data=null, $message=null){
    $response = ['success'=>true];
    if ($message) $response['message'] = $message;
    if ($data !== null) $response['data'] = $data;
    $response['timestamp'] = date('Y-m-d H:i:s');
    echo json_encode($response);
    exit();
}

function sendError($msg, $code=400){
    http_response_code($code);
    echo json_encode(['success'=>false,'error'=>$msg,'code'=>$code,'timestamp'=>date('Y-m-d H:i:s')]);
    exit();
}

// Fonction helper pour détecter la colonne matricule dans sr_bureaux
function getMatriculeColumn($pdo) {
    static $cachedCol = null;
    if ($cachedCol !== null) {
        return $cachedCol;
    }
    
    try {
        $colsResult = $pdo->query("DESCRIBE sr_bureaux");
        $cols = $colsResult->fetchAll(PDO::FETCH_COLUMN);
        
        if (in_array('matricule_responsable', $cols)) {
            $cachedCol = 'matricule_responsable';
        } elseif (in_array('matricule_membre', $cols)) {
            $cachedCol = 'matricule_membre';
        } else {
            $cachedCol = 'matricule_responsable'; // Par défaut
        }
    } catch(Throwable $e) {
        error_log('Erreur détection colonne matricule: '.$e->getMessage());
        $cachedCol = 'matricule_responsable'; // Par défaut
    }
    
    return $cachedCol;
}

// Rôles autorisés pour gérer les bureaux SR
$authorizedRoles = [
    'SR', 'Secrétaire Régional', 'Secretaire Regional', 'Secrétaire régional',
    'Président', 'President', 'Président de sous-comité', 'President de sous-comite',
    'Président sous-comité', 'President sous-comite', 'Président Sous-Comité',
    'Président de section', 'President de section', 'Président Section'
];

function isAuthorizedRole($qualite) {
    global $authorizedRoles;
    if (empty($qualite)) return false;
    
    $qualite = trim($qualite);
    $qualiteLower = mb_strtolower($qualite);
    
    // Vérification exacte
    foreach ($authorizedRoles as $role) {
        if (mb_strtolower(trim($role)) === $qualiteLower) {
            return true;
        }
    }
    
    // Vérification partielle
    if (stripos($qualite, 'secrétaire') !== false && stripos($qualite, 'régional') !== false) return true;
    if (stripos($qualite, 'secretaire') !== false && stripos($qualite, 'regional') !== false) return true;
    if (trim($qualite) === 'SR' || preg_match('/\bSR\b/i', $qualite)) return true;
    if (stripos($qualite, 'président') !== false && stripos($qualite, 'sous-comité') !== false) return true;
    if (stripos($qualite, 'president') !== false && stripos($qualite, 'sous-comite') !== false) return true;
    if (stripos($qualite, 'président') !== false && stripos($qualite, 'section') !== false) return true;
    if (stripos($qualite, 'president') !== false && stripos($qualite, 'section') !== false) return true;
    
    return false;
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';
if(empty($action)){
    $raw = file_get_contents('php://input');
    $input = json_decode($raw,true);
    $action = $input['action'] ?? '';
}

error_log('🟢 Action bureaux SR v2 reçue: '.$action);

try {
    switch($action){
        case 'ping':
            sendSuccess(['status'=>'online','message'=>'API Bureaux SR v2 opérationnelle']);
            break;
            
        case 'db_check':
            // Diagnostic de la base de données
            $checks = [];
            try {
                $r = $pdo->query("SELECT 1")->fetchColumn();
                $checks['db_connection'] = (int)$r === 1;
            } catch(Throwable $e) {
                $checks['db_connection'] = false;
                $checks['db_error'] = $e->getMessage();
            }
            
            // Vérifier si les tables existent
            try {
                $tables = $pdo->query("SHOW TABLES LIKE 'sr_bureaux'")->fetchAll(PDO::FETCH_COLUMN);
                $checks['sr_bureaux_exists'] = !empty($tables);
            } catch(Throwable $e) {
                $checks['sr_bureaux_exists'] = false;
                $checks['sr_bureaux_error'] = $e->getMessage();
            }
            
            try {
                $tables = $pdo->query("SHOW TABLES LIKE 'sr_bureaux_membres'")->fetchAll(PDO::FETCH_COLUMN);
                $checks['sr_bureaux_membres_exists'] = !empty($tables);
            } catch(Throwable $e) {
                $checks['sr_bureaux_membres_exists'] = false;
                $checks['sr_bureaux_membres_error'] = $e->getMessage();
            }
            
            // Vérifier la structure de sr_bureaux si elle existe
            if ($checks['sr_bureaux_exists']) {
                try {
                    $cols = $pdo->query("DESCRIBE sr_bureaux")->fetchAll(PDO::FETCH_COLUMN);
                    $checks['sr_bureaux_columns'] = $cols;
                } catch(Throwable $e) {
                    $checks['sr_bureaux_columns_error'] = $e->getMessage();
                }
            }
            
            sendSuccess($checks);
            break;
            
        case 'check_authorization':
            // Vérifier si l'utilisateur peut gérer les bureaux
            $matricule = $_GET['matricule'] ?? '';
            if (!$matricule) sendError('Matricule requis');
            
            try {
                $stmt = $pdo->prepare("SELECT qualite_membre FROM aeemciste_carte_membre WHERE matricule_gen = ? LIMIT 1");
                $stmt->execute([$matricule]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$user) {
                    sendError('Utilisateur non trouvé', 404);
                }
                
                $isAuthorized = isAuthorizedRole($user['qualite_membre'] ?? '');
                sendSuccess([
                    'authorized' => $isAuthorized,
                    'qualite_membre' => $user['qualite_membre'] ?? '',
                    'message' => $isAuthorized ? 'Accès autorisé' : 'Accès non autorisé. Seuls les SR, Présidents de sous-comités et Présidents de section peuvent gérer les bureaux.'
                ]);
            } catch(Throwable $e) {
                sendError('Erreur lors de la vérification: '.$e->getMessage(), 500);
            }
            break;
            
        case 'get_bureaux':
            // Récupérer tous les bureaux d'un responsable avec leurs membres
            $matricule = $_GET['matricule'] ?? '';
            if (!$matricule) sendError('Matricule requis');
            
            try {
                // Vérifier si les tables existent
                try {
                    $checkTables = $pdo->query("SHOW TABLES LIKE 'sr_bureaux'")->fetch();
                    if (!$checkTables) {
                        sendError('La table sr_bureaux n\'existe pas encore. Veuillez exécuter le script SQL create_sr_bureaux_structure.sql', 500);
                    }
                } catch(Throwable $e) {
                    error_log('Erreur vérification table sr_bureaux: '.$e->getMessage());
                    sendError('Erreur lors de la vérification des tables: '.$e->getMessage(), 500);
                }
                
                // Vérifier l'autorisation
                try {
                    $stmt = $pdo->prepare("SELECT qualite_membre FROM aeemciste_carte_membre WHERE matricule_gen = ? LIMIT 1");
                    $stmt->execute([$matricule]);
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                    if (!$user) {
                        sendError('Utilisateur non trouvé dans la base de données', 404);
                    }
                    if (!isAuthorizedRole($user['qualite_membre'] ?? '')) {
                        sendError('Accès non autorisé. Votre rôle: '.($user['qualite_membre'] ?? 'Non défini'), 403);
                    }
                } catch(Throwable $e) {
                    error_log('Erreur vérification autorisation: '.$e->getMessage());
                    sendError('Erreur lors de la vérification de l\'autorisation: '.$e->getMessage(), 500);
                }
                
                // Récupérer les bureaux (tous les bureaux pour les utilisateurs autorisés)
                try {
                    // Déterminer la colonne de tri (created_at ou id)
                    $cols = [];
                    try {
                        $colsResult = $pdo->query("DESCRIBE sr_bureaux");
                        $cols = $colsResult->fetchAll(PDO::FETCH_COLUMN);
                    } catch(Throwable $e) {
                        error_log('Erreur détection colonnes sr_bureaux: '.$e->getMessage());
                    }
                    
                    $orderCol = in_array('created_at', $cols) ? 'created_at' : 'id';
                    
                    // Récupérer tous les bureaux (pas de filtre par matricule)
                    $stmt = $pdo->prepare("SELECT * FROM sr_bureaux ORDER BY $orderCol DESC");
                    $stmt->execute();
                    $bureaux = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    // Log pour déboguer
                    error_log('🔍 Nombre de bureaux récupérés: '.count($bureaux));
                    if (count($bureaux) > 0) {
                        error_log('🔍 IDs des bureaux: '.implode(', ', array_column($bureaux, 'id')));
                    }
                } catch(PDOException $e) {
                    $errorMsg = $e->getMessage();
                    error_log('Erreur récupération bureaux (PDO): '.$errorMsg);
                    
                    // Détecter si c'est une erreur de table inexistante
                    if (strpos($errorMsg, "doesn't exist") !== false || strpos($errorMsg, "Table") !== false || strpos($errorMsg, "Unknown table") !== false || strpos($errorMsg, "Unknown column") !== false) {
                        sendError('La table sr_bureaux n\'existe pas ou a une structure incorrecte. Veuillez exécuter le script SQL create_sr_bureaux_structure.sql ou migrate_sr_bureaux_to_new_structure.sql. Erreur: '.$errorMsg, 500);
                    } else {
                        sendError('Erreur lors de la récupération des bureaux: '.$errorMsg, 500);
                    }
                } catch(Throwable $e) {
                    error_log('Erreur récupération bureaux: '.$e->getMessage());
                    sendError('Erreur lors de la récupération des bureaux: '.$e->getMessage(), 500);
                }
                
                // Pour chaque bureau, récupérer ses membres
                foreach ($bureaux as &$bureau) {
                    try {
                        $membresStmt = $pdo->prepare("SELECT * FROM sr_bureaux_membres WHERE bureau_id = ? ORDER BY created_at ASC");
                        $membresStmt->execute([$bureau['id']]);
                        $bureau['membres'] = $membresStmt->fetchAll(PDO::FETCH_ASSOC);
                    } catch(Throwable $e2) {
                        error_log('Erreur récupération membres pour bureau '.$bureau['id'].': '.$e2->getMessage());
                        $bureau['membres'] = [];
                    }
                }
                
                // Log final pour déboguer
                error_log('✅ Envoi de '.count($bureaux).' bureaux au client');
                
                sendSuccess($bureaux);
            } catch(Throwable $e) {
                error_log('Erreur get_bureaux (catch général): '.$e->getMessage());
                error_log('Stack trace: '.$e->getTraceAsString());
                sendError('Erreur inattendue lors de la récupération: '.$e->getMessage(), 500);
            }
            break;
            
        case 'create_bureau':
            // Créer un nouveau bureau
            $input = json_decode(file_get_contents('php://input'), true);
            $matricule = $input['matricule_responsable'] ?? '';
            $nom_bureau = $input['nom_bureau'] ?? '';
            $description = $input['description'] ?? null;
            $email = $input['email'] ?? null;
            $telephone = $input['telephone'] ?? null;
            $adresse = $input['adresse'] ?? null;
            
            if (!$matricule || !$nom_bureau) {
                sendError('matricule_responsable et nom_bureau requis');
            }
            
            try {
                // Vérifier l'autorisation
                $stmt = $pdo->prepare("SELECT qualite_membre FROM aeemciste_carte_membre WHERE matricule_gen = ? LIMIT 1");
                $stmt->execute([$matricule]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$user) {
                    sendError('Utilisateur non trouvé dans la base de données', 404);
                }
                $qualite = $user['qualite_membre'] ?? '';
                if (!isAuthorizedRole($qualite)) {
                    sendError('Accès non autorisé. Votre rôle actuel: "'.($qualite ?: 'Non défini').'". Seuls les SR, Présidents de sous-comités et Présidents de section peuvent créer des bureaux.', 403);
                }
                
                // Détecter dynamiquement les colonnes disponibles
                $cols = [];
                try {
                    $colsResult = $pdo->query("DESCRIBE sr_bureaux");
                    $cols = $colsResult->fetchAll(PDO::FETCH_COLUMN);
                } catch(Throwable $e) {
                    error_log('Erreur détection colonnes: '.$e->getMessage());
                }
                
                // Déterminer la colonne matricule
                $matriculeCol = getMatriculeColumn($pdo);
                
                // Construire la requête dynamiquement
                $insertCols = [$matriculeCol, 'nom_bureau'];
                $insertVals = [$matricule, $nom_bureau];
                $placeholders = ['?', '?'];
                
                if (in_array('description', $cols)) {
                    $insertCols[] = 'description';
                    $insertVals[] = $description;
                    $placeholders[] = '?';
                }
                if (in_array('email', $cols)) {
                    $insertCols[] = 'email';
                    $insertVals[] = $email;
                    $placeholders[] = '?';
                }
                if (in_array('telephone', $cols)) {
                    $insertCols[] = 'telephone';
                    $insertVals[] = $telephone;
                    $placeholders[] = '?';
                }
                if (in_array('adresse', $cols)) {
                    $insertCols[] = 'adresse';
                    $insertVals[] = $adresse;
                    $placeholders[] = '?';
                }
                
                // Créer le bureau
                $sql = "INSERT INTO sr_bureaux (".implode(', ', $insertCols).") VALUES (".implode(', ', $placeholders).")";
                $stmt = $pdo->prepare($sql);
                $stmt->execute($insertVals);
                $bureau_id = $pdo->lastInsertId();
                
                // Récupérer le bureau créé
                $getStmt = $pdo->prepare("SELECT * FROM sr_bureaux WHERE id = ? LIMIT 1");
                $getStmt->execute([$bureau_id]);
                $bureau = $getStmt->fetch(PDO::FETCH_ASSOC);
                $bureau['membres'] = [];
                
                sendSuccess($bureau, 'Bureau créé avec succès');
            } catch(Throwable $e) {
                error_log('Erreur create_bureau: '.$e->getMessage());
                sendError('Erreur lors de la création: '.$e->getMessage(), 500);
            }
            break;
            
        case 'add_membre':
            // Ajouter un membre à un bureau
            $input = json_decode(file_get_contents('php://input'), true);
            $bureau_id = $input['bureau_id'] ?? null;
            $matricule_membre = $input['matricule_membre'] ?? '';
            $poste = $input['poste'] ?? '';
            $matricule_responsable = $input['matricule_responsable'] ?? '';
            
            if (!$bureau_id || !$matricule_membre || !$poste || !$matricule_responsable) {
                sendError('bureau_id, matricule_membre, poste et matricule_responsable requis');
            }
            
            try {
                // Vérifier que le responsable a le bon rôle
                $roleStmt = $pdo->prepare("SELECT qualite_membre FROM aeemciste_carte_membre WHERE matricule_gen = ? LIMIT 1");
                $roleStmt->execute([$matricule_responsable]);
                $roleUser = $roleStmt->fetch(PDO::FETCH_ASSOC);
                if (!$roleUser || !isAuthorizedRole($roleUser['qualite_membre'] ?? '')) {
                    sendError('Accès non autorisé. Seuls les SR, Présidents de sous-comités et Présidents de section peuvent gérer les bureaux.', 403);
                }
                
                // Vérifier que le bureau appartient au responsable
                $matriculeCol = getMatriculeColumn($pdo);
                $checkStmt = $pdo->prepare("SELECT id FROM sr_bureaux WHERE id = ? AND $matriculeCol = ? LIMIT 1");
                $checkStmt->execute([$bureau_id, $matricule_responsable]);
                if (!$checkStmt->fetch()) {
                    sendError('Bureau non trouvé ou accès non autorisé', 403);
                }
                
                // Récupérer les infos du membre
                $userStmt = $pdo->prepare("SELECT prenom, nom, qualite_membre, email, contact FROM aeemciste_carte_membre WHERE matricule_gen = ? LIMIT 1");
                $userStmt->execute([$matricule_membre]);
                $membre = $userStmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$membre) {
                    sendError('Membre non trouvé', 404);
                }
                
                // Vérifier si le membre n'est pas déjà dans ce bureau
                $existingStmt = $pdo->prepare("SELECT id FROM sr_bureaux_membres WHERE bureau_id = ? AND matricule_membre = ? LIMIT 1");
                $existingStmt->execute([$bureau_id, $matricule_membre]);
                if ($existingStmt->fetch()) {
                    sendError('Ce membre est déjà dans ce bureau', 409);
                }
                
                // Ajouter le membre
                $nom_complet = trim(($membre['prenom'] ?? '') . ' ' . ($membre['nom'] ?? ''));
                
                // Détecter dynamiquement les colonnes disponibles dans sr_bureaux_membres
                $membreCols = [];
                try {
                    $membreColsResult = $pdo->query("DESCRIBE sr_bureaux_membres");
                    $membreCols = $membreColsResult->fetchAll(PDO::FETCH_COLUMN);
                } catch(Throwable $e) {
                    error_log('Erreur détection colonnes sr_bureaux_membres: '.$e->getMessage());
                }
                
                // Construire la requête dynamiquement
                $insertCols = ['bureau_id', 'matricule_membre', 'poste'];
                $insertVals = [$bureau_id, $matricule_membre, $poste];
                $placeholders = ['?', '?', '?'];
                
                if (in_array('poste_actuel', $membreCols)) {
                    $insertCols[] = 'poste_actuel';
                    $insertVals[] = $membre['qualite_membre'] ?? null;
                    $placeholders[] = '?';
                }
                if (in_array('nom_complet', $membreCols) && $nom_complet) {
                    $insertCols[] = 'nom_complet';
                    $insertVals[] = $nom_complet;
                    $placeholders[] = '?';
                }
                if (in_array('email', $membreCols) && isset($membre['email'])) {
                    $insertCols[] = 'email';
                    $insertVals[] = $membre['email'] ?? null;
                    $placeholders[] = '?';
                }
                if (in_array('telephone', $membreCols) && isset($membre['contact'])) {
                    $insertCols[] = 'telephone';
                    $insertVals[] = $membre['contact'] ?? null;
                    $placeholders[] = '?';
                }
                
                $insertSql = "INSERT INTO sr_bureaux_membres (".implode(', ', $insertCols).") VALUES (".implode(', ', $placeholders).")";
                $insertStmt = $pdo->prepare($insertSql);
                $insertStmt->execute($insertVals);
                
                $membre_id = $pdo->lastInsertId();
                
                // Récupérer le membre ajouté
                $getStmt = $pdo->prepare("SELECT * FROM sr_bureaux_membres WHERE id = ? LIMIT 1");
                $getStmt->execute([$membre_id]);
                $membreAdded = $getStmt->fetch(PDO::FETCH_ASSOC);
                
                sendSuccess($membreAdded, 'Membre ajouté avec succès');
            } catch(PDOException $e) {
                $errorMsg = $e->getMessage();
                error_log('Erreur add_membre (PDO): '.$errorMsg);
                
                // Détecter les erreurs spécifiques
                if (strpos($errorMsg, 'Duplicate entry') !== false || strpos($errorMsg, 'UNIQUE constraint') !== false) {
                    sendError('Ce membre est déjà dans ce bureau', 409);
                } elseif (strpos($errorMsg, "doesn't exist") !== false || strpos($errorMsg, "Unknown table") !== false) {
                    sendError('La table sr_bureaux_membres n\'existe pas encore. Veuillez exécuter le script SQL create_sr_bureaux_structure.sql', 500);
                } else {
                    sendError('Erreur lors de l\'ajout: '.$errorMsg, 500);
                }
            } catch(Throwable $e) {
                error_log('Erreur add_membre: '.$e->getMessage());
                sendError('Erreur lors de l\'ajout: '.$e->getMessage(), 500);
            }
            break;
            
        case 'update_membre_poste':
            // Modifier le poste d'un membre
            $input = json_decode(file_get_contents('php://input'), true);
            $membre_id = $input['membre_id'] ?? null;
            $nouveau_poste = $input['poste'] ?? '';
            $matricule_responsable = $input['matricule_responsable'] ?? '';
            
            if (!$membre_id || !$nouveau_poste || !$matricule_responsable) {
                sendError('membre_id, poste et matricule_responsable requis');
            }
            
            try {
                // Vérifier que le responsable a le bon rôle
                $roleStmt = $pdo->prepare("SELECT qualite_membre FROM aeemciste_carte_membre WHERE matricule_gen = ? LIMIT 1");
                $roleStmt->execute([$matricule_responsable]);
                $roleUser = $roleStmt->fetch(PDO::FETCH_ASSOC);
                if (!$roleUser || !isAuthorizedRole($roleUser['qualite_membre'] ?? '')) {
                    sendError('Accès non autorisé. Seuls les SR, Présidents de sous-comités et Présidents de section peuvent gérer les bureaux.', 403);
                }
                
                // Vérifier que le membre appartient à un bureau du responsable
                $matriculeCol = getMatriculeColumn($pdo);
                $checkStmt = $pdo->prepare("
                    SELECT m.id 
                    FROM sr_bureaux_membres m
                    INNER JOIN sr_bureaux b ON m.bureau_id = b.id
                    WHERE m.id = ? AND b.$matriculeCol = ?
                    LIMIT 1
                ");
                $checkStmt->execute([$membre_id, $matricule_responsable]);
                if (!$checkStmt->fetch()) {
                    sendError('Membre non trouvé ou accès non autorisé', 403);
                }
                
                // Mettre à jour le poste
                $updateStmt = $pdo->prepare("UPDATE sr_bureaux_membres SET poste = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
                $updateStmt->execute([$nouveau_poste, $membre_id]);
                
                // Récupérer le membre mis à jour
                $getStmt = $pdo->prepare("SELECT * FROM sr_bureaux_membres WHERE id = ? LIMIT 1");
                $getStmt->execute([$membre_id]);
                $membre = $getStmt->fetch(PDO::FETCH_ASSOC);
                
                sendSuccess($membre, 'Poste modifié avec succès');
            } catch(Throwable $e) {
                error_log('Erreur update_membre_poste: '.$e->getMessage());
                sendError('Erreur lors de la modification: '.$e->getMessage(), 500);
            }
            break;
            
        case 'remove_membre':
            // Retirer un membre d'un bureau
            $input = json_decode(file_get_contents('php://input'), true);
            $membre_id = $input['membre_id'] ?? null;
            $matricule_responsable = $input['matricule_responsable'] ?? '';
            
            if (!$membre_id || !$matricule_responsable) {
                sendError('membre_id et matricule_responsable requis');
            }
            
            try {
                // Vérifier que le responsable a le bon rôle
                $roleStmt = $pdo->prepare("SELECT qualite_membre FROM aeemciste_carte_membre WHERE matricule_gen = ? LIMIT 1");
                $roleStmt->execute([$matricule_responsable]);
                $roleUser = $roleStmt->fetch(PDO::FETCH_ASSOC);
                if (!$roleUser || !isAuthorizedRole($roleUser['qualite_membre'] ?? '')) {
                    sendError('Accès non autorisé. Seuls les SR, Présidents de sous-comités et Présidents de section peuvent gérer les bureaux.', 403);
                }
                
                // Vérifier que le membre appartient à un bureau du responsable
                $matriculeCol = getMatriculeColumn($pdo);
                $checkStmt = $pdo->prepare("
                    SELECT m.id 
                    FROM sr_bureaux_membres m
                    INNER JOIN sr_bureaux b ON m.bureau_id = b.id
                    WHERE m.id = ? AND b.$matriculeCol = ?
                    LIMIT 1
                ");
                $checkStmt->execute([$membre_id, $matricule_responsable]);
                if (!$checkStmt->fetch()) {
                    sendError('Membre non trouvé ou accès non autorisé', 403);
                }
                
                // Supprimer le membre
                $deleteStmt = $pdo->prepare("DELETE FROM sr_bureaux_membres WHERE id = ?");
                $deleteStmt->execute([$membre_id]);
                
                sendSuccess(null, 'Membre retiré avec succès');
            } catch(Throwable $e) {
                error_log('Erreur remove_membre: '.$e->getMessage());
                sendError('Erreur lors de la suppression: '.$e->getMessage(), 500);
            }
            break;
            
        case 'delete_bureau':
            // Supprimer un bureau (et tous ses membres)
            $input = json_decode(file_get_contents('php://input'), true);
            $bureau_id = $input['bureau_id'] ?? null;
            $matricule = $input['matricule_responsable'] ?? '';
            
            if (!$bureau_id || !$matricule) {
                sendError('bureau_id et matricule_responsable requis');
            }
            
            try {
                // Vérifier que le responsable a le bon rôle
                $roleStmt = $pdo->prepare("SELECT qualite_membre FROM aeemciste_carte_membre WHERE matricule_gen = ? LIMIT 1");
                $roleStmt->execute([$matricule]);
                $roleUser = $roleStmt->fetch(PDO::FETCH_ASSOC);
                if (!$roleUser || !isAuthorizedRole($roleUser['qualite_membre'] ?? '')) {
                    sendError('Accès non autorisé. Seuls les SR, Présidents de sous-comités et Présidents de section peuvent gérer les bureaux.', 403);
                }
                
                // Vérifier que le bureau appartient au responsable
                $matriculeCol = getMatriculeColumn($pdo);
                $checkStmt = $pdo->prepare("SELECT id FROM sr_bureaux WHERE id = ? AND $matriculeCol = ? LIMIT 1");
                $checkStmt->execute([$bureau_id, $matricule]);
                if (!$checkStmt->fetch()) {
                    sendError('Bureau non trouvé ou accès non autorisé', 403);
                }
                
                // Supprimer le bureau (les membres seront supprimés automatiquement par CASCADE)
                $deleteStmt = $pdo->prepare("DELETE FROM sr_bureaux WHERE id = ?");
                $deleteStmt->execute([$bureau_id]);
                
                sendSuccess(null, 'Bureau supprimé avec succès');
            } catch(Throwable $e) {
                error_log('Erreur delete_bureau: '.$e->getMessage());
                sendError('Erreur lors de la suppression: '.$e->getMessage(), 500);
            }
            break;
            
        case 'get_user_by_matricule':
            // Récupérer les infos d'un utilisateur par son matricule
            $matricule = $_GET['matricule'] ?? '';
            if (!$matricule) sendError('Matricule requis');
            
            try {
                // Détecter dynamiquement les colonnes disponibles
                $cols = [];
                try {
                    $colsResult = $pdo->query("DESCRIBE aeemciste_carte_membre");
                    $cols = $colsResult->fetchAll(PDO::FETCH_COLUMN);
                } catch(Throwable $e) {
                    error_log('Erreur détection colonnes: '.$e->getMessage());
                }
                
                // Construire la requête avec les colonnes disponibles
                $selectCols = ['matricule_gen', 'prenom', 'nom', 'qualite_membre'];
                if (in_array('email', $cols)) $selectCols[] = 'email';
                if (in_array('contact', $cols)) $selectCols[] = 'contact';
                if (in_array('telephone', $cols)) $selectCols[] = 'telephone';
                
                $stmt = $pdo->prepare("SELECT ".implode(', ', $selectCols)." FROM aeemciste_carte_membre WHERE matricule_gen = ? LIMIT 1");
                $stmt->execute([$matricule]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$user) {
                    sendError('Utilisateur non trouvé', 404);
                }
                
                // Déterminer le téléphone (priorité: contact > telephone)
                $telephone = '';
                if (isset($user['contact']) && !empty($user['contact'])) {
                    $telephone = $user['contact'];
                } elseif (isset($user['telephone']) && !empty($user['telephone'])) {
                    $telephone = $user['telephone'];
                }
                
                sendSuccess([
                    'matricule' => $user['matricule_gen'] ?? $matricule,
                    'prenom' => $user['prenom'] ?? '',
                    'nom' => $user['nom'] ?? '',
                    'qualite_membre' => $user['qualite_membre'] ?? '',
                    'email' => $user['email'] ?? '',
                    'telephone' => $telephone
                ]);
            } catch(Throwable $e) {
                error_log('Erreur get_user_by_matricule: '.$e->getMessage());
                sendError('Erreur lors de la récupération: '.$e->getMessage(), 500);
            }
            break;
            
        default:
            sendError('Action non reconnue: '.$action, 400);
    }
} catch(Throwable $e) {
    error_log('❌ Erreur API bureaux SR v2: '.$e->getMessage());
    sendError('Erreur serveur: '.$e->getMessage(), 500);
}

