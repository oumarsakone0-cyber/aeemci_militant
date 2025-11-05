<?php
/**
 * API GESTION DES BUREAUX SR (Secrétariats Régionaux)
 * Utilise la même base de données que post_api.php
 */

// === HEADERS CORS - DOIT ÊTRE EN PREMIER ===
// Permettre les deux ports de développement courants
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
    // Permettre tous les localhost pour le développement
    header('Access-Control-Allow-Origin: ' . $origin);
} else {
    // Fallback pour le développement local
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

// Désactiver l'affichage des erreurs pour éviter le HTML dans la réponse JSON
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Configuration pour les gros fichiers
ini_set('upload_max_filesize', '1024M');
ini_set('post_max_size', '1024M');
ini_set('max_execution_time', 300); // 5 minutes
ini_set('max_input_time', 300);
ini_set('memory_limit', '512M');

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
    http_response_code(500);
    echo json_encode(['success'=>false,'error'=>'Impossible de se connecter à la base de données','details'=>$errors]);
    exit();
}

// Fonction pour envoyer les erreurs avec les headers CORS déjà définis
function sendErrorWithCors($msg, $code=400) {
    http_response_code($code);
    echo json_encode(['success'=>false,'error'=>$msg,'code'=>$code,'timestamp'=>date('Y-m-d H:i:s')]);
    exit();
}

// === FONCTIONS UTILITAIRES ===
function sendSuccess($data=null, $message=null){
    $response = ['success'=>true];
    if ($message) $response['message'] = $message;
    if ($data !== null) {
        // Si $data est déjà un tableau avec 'data', l'utiliser directement
        if (is_array($data) && isset($data['data'])) {
            $response = array_merge($response, $data);
        } else {
            $response['data'] = $data;
        }
    }
    $response['timestamp'] = date('Y-m-d H:i:s');
    echo json_encode($response);
    exit();
}

function sendError($msg, $code=400){
    http_response_code($code);
    echo json_encode(['success'=>false,'error'=>$msg,'code'=>$code,'timestamp'=>date('Y-m-d H:i:s')]);
    exit();
}

function formatTimestamp($timestamp) {
    $date = new DateTime($timestamp);
    $now = new DateTime();
    $diff = $now->getTimestamp() - $date->getTimestamp();
    $minutes = floor($diff / 60);
    $hours = floor($diff / 3600);
    $days = floor($diff / 86400);
    
    if ($minutes < 1) return 'À l\'instant';
    if ($minutes < 60) return "Il y a {$minutes} min";
    if ($hours < 24) return "Il y a {$hours}h";
    if ($days < 7) return "Il y a {$days} jour" . ($days > 1 ? 's' : '');
    return $date->format('d/m/Y');
}

// === ROUTAGE ===
$action = $_GET['action'] ?? $_POST['action'] ?? '';
if(empty($action)){
    $raw = file_get_contents('php://input');
    $input = json_decode($raw,true);
    $action = $input['action'] ?? '';
}

error_log('🟢 Action bureaux SR reçue: '.$action);

try {
    switch($action){
        case 'ping':
            sendSuccess(['status'=>'online','message'=>'API Bureaux SR opérationnelle','time'=>date('Y-m-d H:i:s')]);
            break;
            
        case 'get_bureaux':
            $matricule = $_GET['matricule'] ?? '';
            if (!$matricule) {
                sendError('Matricule requis');
            }
            
            try {
                // Vérifier si la table existe
                $tableExists = $pdo->query("SHOW TABLES LIKE 'sr_bureaux'")->fetch();
                if (!$tableExists) {
                    sendError('Table sr_bureaux non trouvée. Veuillez exécuter le script SQL de création.', 404);
                }
                
                // Récupérer les bureaux du membre
                $stmt = $pdo->prepare("SELECT * FROM sr_bureaux WHERE matricule_membre = ? ORDER BY created_at DESC");
                $stmt->execute([$matricule]);
                $bureaux = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                
                sendSuccess($bureaux);
            } catch(Throwable $e) {
                error_log('Erreur get_bureaux: '.$e->getMessage());
                sendError('Erreur lors de la récupération des bureaux: '.$e->getMessage(), 500);
            }
            break;
            
        case 'create_bureau':
            $input = json_decode(file_get_contents('php://input'), true);
            $matricule = $input['matricule_membre'] ?? '';
            $nom_bureau = $input['nom_bureau'] ?? '';
            $poste = $input['poste'] ?? '';
            $nom_president = $input['nom_president'] ?? '';
            $nom_vice_president = $input['nom_vice_president'] ?? null;
            $nom_tresorier = $input['nom_tresorier'] ?? null;
            $nom_secretaire = $input['nom_secretaire'] ?? null;
            $email = $input['email'] ?? null;
            $telephone = $input['telephone'] ?? null;
            $adresse = $input['adresse'] ?? null;
            $description = $input['description'] ?? null;
            
            if (!$matricule || !$nom_bureau || !$poste || !$nom_president) {
                sendError('matricule_membre, nom_bureau, poste et nom_president requis');
            }
            
            try {
                // Vérifier si la table existe
                $tableExists = $pdo->query("SHOW TABLES LIKE 'sr_bureaux'")->fetch();
                if (!$tableExists) {
                    sendError('Table sr_bureaux non trouvée. Veuillez exécuter le script SQL de création.', 404);
                }
                
                // Insérer le bureau
                $stmt = $pdo->prepare("INSERT INTO sr_bureaux (matricule_membre, nom_bureau, poste, nom_president, nom_vice_president, nom_tresorier, nom_secretaire, email, telephone, adresse, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $matricule, 
                    $nom_bureau, 
                    $poste, 
                    $nom_president,
                    $nom_vice_president,
                    $nom_tresorier,
                    $nom_secretaire,
                    $email,
                    $telephone,
                    $adresse,
                    $description
                ]);
                $bureau_id = $pdo->lastInsertId();
                
                // Récupérer le bureau créé
                $getStmt = $pdo->prepare("SELECT * FROM sr_bureaux WHERE id = ? LIMIT 1");
                $getStmt->execute([$bureau_id]);
                $bureau = $getStmt->fetch(PDO::FETCH_ASSOC);
                
                sendSuccess($bureau, 'Bureau créé avec succès');
            } catch(Throwable $e) {
                error_log('Erreur create_bureau: '.$e->getMessage());
                sendError('Erreur lors de la création du bureau: '.$e->getMessage(), 500);
            }
            break;
            
        case 'update_bureau':
            $input = json_decode(file_get_contents('php://input'), true);
            $id = $input['id'] ?? '';
            $matricule = $input['matricule_membre'] ?? '';
            $nom_bureau = $input['nom_bureau'] ?? '';
            $poste = $input['poste'] ?? '';
            $nom_president = $input['nom_president'] ?? '';
            $nom_vice_president = $input['nom_vice_president'] ?? null;
            $nom_tresorier = $input['nom_tresorier'] ?? null;
            $nom_secretaire = $input['nom_secretaire'] ?? null;
            $email = $input['email'] ?? null;
            $telephone = $input['telephone'] ?? null;
            $adresse = $input['adresse'] ?? null;
            $description = $input['description'] ?? null;
            
            if (!$id || !$matricule || !$nom_bureau || !$poste || !$nom_president) {
                sendError('id, matricule_membre, nom_bureau, poste et nom_president requis');
            }
            
            try {
                // Vérifier que le bureau appartient au membre
                $check = $pdo->prepare("SELECT id FROM sr_bureaux WHERE id = ? AND matricule_membre = ? LIMIT 1");
                $check->execute([$id, $matricule]);
                if (!$check->fetch()) {
                    sendError('Bureau non trouvé ou vous n\'êtes pas autorisé à le modifier', 403);
                }
                
                // Mettre à jour le bureau
                $stmt = $pdo->prepare("UPDATE sr_bureaux SET nom_bureau = ?, poste = ?, nom_president = ?, nom_vice_president = ?, nom_tresorier = ?, nom_secretaire = ?, email = ?, telephone = ?, adresse = ?, description = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ? AND matricule_membre = ?");
                $stmt->execute([
                    $nom_bureau, 
                    $poste, 
                    $nom_president,
                    $nom_vice_president,
                    $nom_tresorier,
                    $nom_secretaire,
                    $email,
                    $telephone,
                    $adresse,
                    $description,
                    $id, 
                    $matricule
                ]);
                
                // Récupérer le bureau mis à jour
                $getStmt = $pdo->prepare("SELECT * FROM sr_bureaux WHERE id = ? LIMIT 1");
                $getStmt->execute([$id]);
                $bureau = $getStmt->fetch(PDO::FETCH_ASSOC);
                
                sendSuccess($bureau, 'Bureau modifié avec succès');
            } catch(Throwable $e) {
                error_log('Erreur update_bureau: '.$e->getMessage());
                sendError('Erreur lors de la modification du bureau: '.$e->getMessage(), 500);
            }
            break;
            
        case 'delete_bureau':
            $input = json_decode(file_get_contents('php://input'), true);
            $id = $input['id'] ?? '';
            $matricule = $_GET['matricule'] ?? $input['matricule_membre'] ?? '';
            
            if (!$id) {
                sendError('id requis');
            }
            
            try {
                // Si un matricule est fourni, vérifier que le bureau appartient au membre
                if ($matricule) {
                    $check = $pdo->prepare("SELECT id FROM sr_bureaux WHERE id = ? AND matricule_membre = ? LIMIT 1");
                    $check->execute([$id, $matricule]);
                    if (!$check->fetch()) {
                        sendError('Bureau non trouvé ou vous n\'êtes pas autorisé à le supprimer', 403);
                    }
                    
                    $stmt = $pdo->prepare("DELETE FROM sr_bureaux WHERE id = ? AND matricule_membre = ?");
                    $stmt->execute([$id, $matricule]);
                } else {
                    // Sinon, supprimer sans vérification (pour les admins)
                    $stmt = $pdo->prepare("DELETE FROM sr_bureaux WHERE id = ?");
                    $stmt->execute([$id]);
                }
                
                sendSuccess(null, 'Bureau supprimé avec succès');
            } catch(Throwable $e) {
                error_log('Erreur delete_bureau: '.$e->getMessage());
                sendError('Erreur lors de la suppression du bureau: '.$e->getMessage(), 500);
            }
            break;
            
        case 'get_bureau':
            // Récupérer un bureau spécifique par ID
            $id = $_GET['id'] ?? '';
            if (!$id) {
                sendError('id requis');
            }
            
            try {
                $stmt = $pdo->prepare("SELECT * FROM sr_bureaux WHERE id = ? LIMIT 1");
                $stmt->execute([$id]);
                $bureau = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$bureau) {
                    sendError('Bureau non trouvé', 404);
                }
                
                
                sendSuccess($bureau);
            } catch(Throwable $e) {
                error_log('Erreur get_bureau: '.$e->getMessage());
                sendError('Erreur lors de la récupération du bureau: '.$e->getMessage(), 500);
            }
            break;
            
        default:
            if (empty($action)) {
                // Réponse par défaut si aucune action n'est fournie
                sendSuccess([
                    'message' => 'API Bureaux SR - Service actif',
                    'version' => '1.0',
                    'endpoints' => [
                        'GET ?action=ping' => 'Vérifier le statut de l\'API',
                        'GET ?action=get_bureaux&matricule=X' => 'Récupérer les bureaux d\'un membre',
                        'GET ?action=get_bureau&id=X' => 'Récupérer un bureau spécifique',
                        'POST ?action=create_bureau' => 'Créer un nouveau bureau',
                        'POST ?action=update_bureau' => 'Modifier un bureau',
                        'POST ?action=delete_bureau' => 'Supprimer un bureau'
                    ]
                ]);
            } else {
                sendError('Action non reconnue: '.$action);
            }
    }
    
} catch(Throwable $e){
    error_log('🔥 Exception: '.$e->getMessage());
    // Utiliser sendError qui utilise les headers CORS déjà définis
    http_response_code(500);
    echo json_encode([
        'success'=>false,
        'error'=>'Erreur serveur: '.$e->getMessage(),
        'code'=>500,
        'timestamp'=>date('Y-m-d H:i:s')
    ]);
    exit();
}

