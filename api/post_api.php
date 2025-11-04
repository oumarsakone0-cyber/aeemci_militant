<?php
/**
 * API SYSTÈME DE POSTS AEEMCI - VERSION SIMPLIFIÉE ET ROBUSTE
 */

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

// === HEADERS CORS ===
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
} else {
    // Fallback pour le développement local
    header('Access-Control-Allow-Origin: http://localhost:5173');
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

// === CONFIG DB / CONNEXION (conservé) ===
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

function sendSuccess($data=null){ echo json_encode(['success'=>true,'data'=>$data,'timestamp'=>date('Y-m-d H:i:s')]); exit(); }
function sendError($msg, $code=400){ http_response_code($code); echo json_encode(['success'=>false,'error'=>$msg,'code'=>$code,'timestamp'=>date('Y-m-d H:i:s')]); exit(); }

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

$action = $_GET['action'] ?? $_POST['action'] ?? '';
if(empty($action)){
    $raw = file_get_contents('php://input');
    $input = json_decode($raw,true);
    $action = $input['action'] ?? '';
}

error_log('🟢 Action reçue: '.$action);

try {
    switch($action){

        case 'ping':
            sendSuccess(['status'=>'online','message'=>'Serveur API opérationnel','time'=>date('Y-m-d H:i:s')]);
            break;

        case 'db_check':
            $checks = [];
            $r = $pdo->query("SELECT 1")->fetchColumn();
            $checks['select_1'] = (int)$r === 1;
            try { 
                $count = $pdo->query("SELECT COUNT(*) FROM p_posts")->fetchColumn(); 
                $checks['p_posts'] = ['exists' => true, 'count' => $count]; 
            } catch(Throwable $e){ 
                $checks['p_posts'] = ['exists' => false, 'error' => $e->getMessage()]; 
            }
            try { 
                $count = $pdo->query("SELECT COUNT(*) FROM p_comments")->fetchColumn(); 
                $checks['p_comments'] = ['exists' => true, 'count' => $count]; 
                
                // Vérifier la structure de p_comments
                $cols = $pdo->query("DESCRIBE p_comments")->fetchAll(PDO::FETCH_COLUMN);
                $checks['p_comments']['columns'] = $cols;
            } catch(Throwable $e){ 
                $checks['p_comments'] = ['exists' => false, 'error' => $e->getMessage()]; 
            }
            sendSuccess(['db_ok'=>true,'checks'=>$checks]);
            break;
            
        // PROFIL UTILISATEUR — adapté au schéma fourni
        case 'get_user_profile':
            $matricule = $_GET['matricule'] ?? '';
            if (!$matricule) sendError('Matricule requis');

            // Le matricule est stocké dans matricule_gen
            $stmt = $pdo->prepare("SELECT matricule_gen, prenom, nom, photo_membre, ville_ou_commune, qualite_membre, email FROM aeemciste_carte_membre WHERE matricule_gen = ? LIMIT 1");
            try {
                $stmt->execute([$matricule]);
                $u = $stmt->fetch(PDO::FETCH_ASSOC);
            } catch(Throwable $e){
                // si certaines colonnes comme email n'existent pas, retente sans
                $stmt = $pdo->prepare("SELECT matricule_gen, prenom, nom, photo_membre, ville_ou_commune, qualite_membre FROM aeemciste_carte_membre WHERE matricule_gen = ? LIMIT 1");
                $stmt->execute([$matricule]);
                $u = $stmt->fetch(PDO::FETCH_ASSOC);
            }

            if(!$u){
                sendError('Utilisateur non trouvé',404);
            }

            $fullName = trim(($u['prenom'] ?? '').' '.($u['nom'] ?? ''));
            $role = $u['qualite_membre'] ?? 'Membre AEEMCI';
            $photo = $u['photo_membre'] ?? 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/1200px-User_icon_2.svg.png';
            $ville = $u['ville_ou_commune'] ?? '';

            sendSuccess([
                'matricule' => $u['matricule_gen'] ?? $matricule,
                'full_name' => $fullName !== '' ? $fullName : ($u['matricule_gen'] ?? $matricule),
                'photo_url' => $photo,
                'role' => $role,
                'email' => $u['email'] ?? '',
                'ville' => $ville
            ]);
            break;
            
        case 'get_posts':
            $matricule = $_GET['matricule'] ?? '';
            $limit = (int)($_GET['limit'] ?? 20);
            $offset = (int)($_GET['offset'] ?? 0);
            
            // Détecter schéma p_posts et choisir ORDER BY sûr
            $postsCols = [];
            try { $postsCols = $pdo->query("DESCRIBE p_posts")->fetchAll(PDO::FETCH_COLUMN); } catch(Throwable $e) {}
            $orderCol = in_array('created_at',$postsCols,true) ? 'created_at' : (in_array('id',$postsCols,true) ? 'id' : null);
            $orderSql = $orderCol ? " ORDER BY $orderCol DESC" : '';

            try {
                $sql = "SELECT * FROM p_posts".$orderSql." LIMIT ? OFFSET ?";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(1, $limit, PDO::PARAM_INT);
                $stmt->bindValue(2, $offset, PDO::PARAM_INT);
                $stmt->execute();
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (Throwable $e) {
                sendError('Erreur get_posts: '.$e->getMessage(),500);
            }

            $posts = [];
            foreach($rows as $r){
                // Auteur via aeemciste_carte_membre
                $au = ['prenom'=>'','nom'=>'','ville_ou_commune'=>'','photo_membre'=>null];
                if (!empty($r['author_matricule'])){
                    try {
                        $s = $pdo->prepare("SELECT prenom, nom, ville_ou_commune, photo_membre FROM aeemciste_carte_membre WHERE matricule_gen = ? LIMIT 1");
                        $s->execute([$r['author_matricule']]);
                        $tmp = $s->fetch(PDO::FETCH_ASSOC);
                        if ($tmp) $au = $tmp;
                    } catch(Throwable $e){}
                }
                // Compteurs robustes
                $reactions_count = 0; $comments_count = 0; $ur = 0;
                try { $rc = $pdo->prepare("SELECT COUNT(*) FROM p_comments WHERE post_id=? AND type='reaction'"); $rc->execute([$r['id']]); $reactions_count = (int)$rc->fetchColumn(); } catch(Throwable $e){}
                try { $cc = $pdo->prepare("SELECT COUNT(*) FROM p_comments WHERE post_id=? AND type='comment'"); $cc->execute([$r['id']]); $comments_count = (int)$cc->fetchColumn(); } catch(Throwable $e){}
                if ($matricule){ try { $urq = $pdo->prepare("SELECT COUNT(*) FROM p_comments WHERE post_id=? AND type='reaction' AND author_matricule=?"); $urq->execute([$r['id'],$matricule]); $ur = (int)$urq->fetchColumn(); } catch(Throwable $e){} }

                $posts[] = [
                    'id'=>$r['id'] ?? null,
                    'content'=>$r['content'] ?? ($r['post_content'] ?? ''),
                    'author_name'=>trim(($au['prenom']??'').' '.($au['nom']??'')) ?: ($r['author_matricule'] ?? ''),
                    'author_ville'=>$au['ville_ou_commune'] ?? '',
                    'author_photo'=>$au['photo_membre'] ?? 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/1200px-User_icon_2.svg.png',
                    'created_at'=>$r['created_at'] ?? ($r['date_creation'] ?? null),
                    'reactions_count'=>$reactions_count,
                    'comments_count'=>$comments_count,
                    'user_has_reacted'=>$ur>0,
                    // Ajouter les champs média
                    'media_url'=>$r['media_url'] ?? null,
                    'media_type'=>$r['media_type'] ?? 'none',
                    // Pour compatibilité avec le front-end
                    'image_url'=>($r['media_type'] === 'image') ? $r['media_url'] : null,
                    'video_url'=>($r['media_type'] === 'video') ? $r['media_url'] : null,
                    // Debug: vérifier si l'URL est tronquée
                    'media_url_length'=>$r['media_url'] ? strlen($r['media_url']) : 0,
                    'is_base64_truncated'=>($r['media_url'] && strpos($r['media_url'], 'data:') === 0 && !preg_match('/[A-Za-z0-9+\/]+=*$/', $r['media_url']))
                ];
            }
            sendSuccess($posts);
            break;
            
        case 'upload_media':
            // Nouveau endpoint pour upload de fichiers volumineux
            if (!isset($_FILES['media'])) {
                sendError('Aucun fichier fourni');
            }
            
            $file = $_FILES['media'];
            $matricule = $_POST['matricule'] ?? '';
            
            if (!$matricule) {
                sendError('Matricule requis');
            }
            
            // Vérifications de sécurité
            $allowedTypes = ['video/mp4', 'video/avi', 'video/mov', 'video/wmv', 'image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($file['type'], $allowedTypes)) {
                sendError('Type de fichier non autorisé');
            }
            
            // Limite de taille : 1GB
            $maxSize = 1024 * 1024 * 1024; // 1GB
            if ($file['size'] > $maxSize) {
                sendError('Fichier trop volumineux (max 1GB)');
            }
            
            // Créer le dossier uploads s'il n'existe pas (dans le même répertoire que l'API)
            $uploadDir = __DIR__ . '/uploads/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            // Générer un nom de fichier unique
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $fileName = $matricule . '_' . time() . '_' . uniqid() . '.' . $extension;
            $filePath = $uploadDir . $fileName;
            
            // Déplacer le fichier uploadé
            if (move_uploaded_file($file['tmp_name'], $filePath)) {
                // URL absolue pour accéder au fichier via le serveur distant
                $fileUrl = 'http://sogetrag.com/apistage/uploads/' . $fileName;
                
                sendSuccess([
                    'url' => $fileUrl,
                    'filename' => $fileName,
                    'size' => $file['size'],
                    'type' => $file['type']
                ]);
            } else {
                sendError('Erreur lors de l\'upload du fichier');
            }
            break;

        case 'create_post':
            $input = json_decode(file_get_contents('php://input'),true);
            $matricule = $input['matricule'] ?? '';
            $content = $input['content'] ?? '';
            $image_url = $input['image_url'] ?? null;
            $video_url = $input['video_url'] ?? null;
            $media_json = $input['media_json'] ?? null;
            $location = $input['location'] ?? null;
            $privacy = $input['privacy_level'] ?? 'public';
            
            // Nouveaux champs pour les médias multiples
            $images = $input['images'] ?? [];
            $videos = $input['videos'] ?? [];
            $captions = $input['captions'] ?? [];
            $media = $input['media'] ?? [];
            
            // Utiliser les colonnes existantes de la table
            $media_url = null;
            $media_type = 'none';
            
            // Si des médias sont fournis, utiliser le premier média
            if (!empty($media)) {
                $firstMedia = $media[0];
                $media_url = $firstMedia['url'] ?? null;
                $media_type = $firstMedia['type'] ?? 'none';
            } elseif (!empty($images)) {
                $media_url = $images[0];
                $media_type = 'image';
            } elseif (!empty($videos)) {
                $media_url = $videos[0];
                $media_type = 'video';
            }
            
            // Log pour debug
            $urlLength = $media_url ? strlen($media_url) : 0;
            error_log("Média sauvegardé: URL=" . ($media_url ? "présente ({$urlLength} chars)" : 'null') . ", Type=" . $media_type);
            if ($media_url && $urlLength > 1000) {
                error_log("URL début: " . substr($media_url, 0, 100));
                error_log("URL fin: " . substr($media_url, -100));
            }
            if(!$matricule || !$content) sendError('Matricule et content requis');
            // Détecter les colonnes existantes
            $cols = [];
            try { $cols = $pdo->query("DESCRIBE p_posts")->fetchAll(PDO::FETCH_COLUMN); } catch (Throwable $e) {}
            $fields = [];
            $placeholders = [];
            $values = [];
            $add = function(string $col, $val) use (&$fields, &$placeholders, &$values, $cols) {
                if (in_array($col, $cols, true)) { $fields[] = $col; $placeholders[] = '?'; $values[] = $val; }
            };
            // Colonnes minimales
            $add('author_matricule', $matricule);
            $add('content', $content);
            // user_profile_id si présent: le résoudre depuis p_user_profiles
            $needUserProfileId = in_array('user_profile_id', $cols, true);
            $user_profile_id = null;
            if ($needUserProfileId) {
                // 1) tenter de trouver un profil existant
                $tryCols = ['matricule_gen','matricule','user_matricule'];
                foreach ($tryCols as $c) {
                    try {
                        $q = $pdo->prepare("SELECT id FROM p_user_profiles WHERE $c = ? LIMIT 1");
                        $q->execute([$matricule]);
                        $found = $q->fetchColumn();
                        if ($found) { $user_profile_id = (int)$found; break; }
                    } catch (Throwable $e2) { }
                }
                // 2) si non trouvé, créer un profil minimal si la table existe
                if ($user_profile_id === null) {
                    try {
                        $colsProf = $pdo->query("DESCRIBE p_user_profiles")->fetchAll(PDO::FETCH_COLUMN);
                        // Récupérer infos utilisateur de aeemciste_carte_membre
                        $prenom = $nom = $photo_membre = '';
                        try {
                            $uStmt = $pdo->prepare("SELECT prenom, nom, photo_membre FROM aeemciste_carte_membre WHERE matricule_gen = ? LIMIT 1");
                            $uStmt->execute([$matricule]);
                            $u = $uStmt->fetch(PDO::FETCH_ASSOC);
                            if ($u) { $prenom = $u['prenom'] ?? ''; $nom = $u['nom'] ?? ''; $photo_membre = $u['photo_membre'] ?? ''; }
                        } catch (Throwable $e3) { }
                        $full_name = trim(($prenom?:'').' '.($nom?:''));
                        // Construire insertion dynamique minimale
                        $pfFields = [];
                        $pfPlace = [];
                        $pfValues = [];
                        $push = function($c,$v) use (&$pfFields,&$pfPlace,&$pfValues,$colsProf){ if (in_array($c,$colsProf,true)) { $pfFields[]=$c; $pfPlace[]='?'; $pfValues[]=$v; } };
                        // Matricule
                        if (in_array('matricule_gen',$colsProf,true)) $push('matricule_gen',$matricule);
                        elseif (in_array('matricule',$colsProf,true)) $push('matricule',$matricule);
                        elseif (in_array('user_matricule',$colsProf,true)) $push('user_matricule',$matricule);
                        // Nom complet
                        if (in_array('full_name',$colsProf,true) && $full_name!=='') $push('full_name',$full_name);
                        // Photo
                        if (in_array('photo_url',$colsProf,true) && $photo_membre!=='') $push('photo_url',$photo_membre);
                        // Timestamps
                        if (in_array('created_at',$colsProf,true)) { $pfFields[]='created_at'; $pfPlace[]='NOW()'; }
                        if (in_array('updated_at',$colsProf,true)) { $pfFields[]='updated_at'; $pfPlace[]='NOW()'; }
                        if (!empty($pfFields)) {
                            $sqlP = "INSERT INTO p_user_profiles (".implode(',', $pfFields).") VALUES (".implode(',', array_map(function($p){return $p==='NOW()'?'NOW()':$p;}, $pfPlace)).")";
                            $stmtP = $pdo->prepare($sqlP);
                            $bindVals = [];
                            foreach ($pfPlace as $i => $p) if ($p==='?') $bindVals[] = $pfValues[$i] ?? null;
                            $stmtP->execute($bindVals);
                            $user_profile_id = (int)$pdo->lastInsertId();
                        }
                    } catch (Throwable $e4) { }
                }
                // 3) pousser la valeur (NULL si malgré tout non trouvé/creé -> laisser la BD refuser si NOT NULL)
                $fields[] = 'user_profile_id';
                if ($user_profile_id === null) { $placeholders[] = 'NULL'; } else { $placeholders[] = '?'; $values[] = $user_profile_id; }
            }
            // Optionnelles - utiliser les colonnes existantes
            $add('media_url', $media_url);
            $add('media_type', $media_type);
            $add('location', $location);
            $add('privacy_level', $privacy);
            if (in_array('created_at', $cols, true)) { $fields[] = 'created_at'; $placeholders[] = 'NOW()'; }
            if (in_array('updated_at', $cols, true)) { $fields[] = 'updated_at'; $placeholders[] = 'NOW()'; }
            if (count($fields) < 2) { sendError('Schéma p_posts incomplet: colonnes minimales manquantes'); }
            $ph = implode(',', array_map(function($p){ return $p === 'NOW()' || $p === 'NULL' ? $p : $p; }, $placeholders));
            $sql = "INSERT INTO p_posts (".implode(',', $fields).") VALUES (".$ph.")";
            $stmt = $pdo->prepare($sql);
            $paramValues = [];
            foreach ($placeholders as $i => $p) { if ($p === '?') { $paramValues[] = $values[$i] ?? null; } }
            $stmt->execute($paramValues);
            sendSuccess(['post_id'=>$pdo->lastInsertId(),'message'=>'Post créé']);
            break;
            
        case 'add_comment':
            $input = json_decode(file_get_contents('php://input'),true);
            $post_id = $input['post_id'] ?? '';
            $matricule = $input['matricule'] ?? '';
            $content = $input['content'] ?? '';
            $parent_comment_id = $input['parent_comment_id'] ?? null;
            
            if(!$post_id || !$matricule || !$content) sendError('post_id, matricule et content requis');
            
            $textContent = strip_tags($content);
            
            // Détecter schéma p_comments
            $cCols = [];
            try { 
                $cCols = $pdo->query("DESCRIBE p_comments")->fetchAll(PDO::FETCH_COLUMN); 
            } catch(Throwable $e) {
                error_log('Erreur DESCRIBE p_comments: '.$e->getMessage());
                sendError('Table p_comments non accessible: '.$e->getMessage(), 500);
            }
            
            // Résoudre user_profile_id si nécessaire
            $user_profile_id = null;
            $needUserProfileId = in_array('user_profile_id', $cCols, true);
            if ($needUserProfileId) {
                // Tenter de trouver un profil existant
                $tryCols = ['matricule_gen','matricule','user_matricule'];
                foreach ($tryCols as $c) {
                    try {
                        $q = $pdo->prepare("SELECT id FROM p_user_profiles WHERE $c = ? LIMIT 1");
                        $q->execute([$matricule]);
                        $found = $q->fetchColumn();
                        if ($found) { $user_profile_id = (int)$found; break; }
                    } catch (Throwable $e2) { }
                }
                
                // Si non trouvé, créer un profil minimal
                if ($user_profile_id === null) {
                    try {
                        $colsProf = $pdo->query("DESCRIBE p_user_profiles")->fetchAll(PDO::FETCH_COLUMN);
                        // Récupérer infos utilisateur de aeemciste_carte_membre
                        $prenom = $nom = $photo_membre = '';
                        try {
                            $uStmt = $pdo->prepare("SELECT prenom, nom, photo_membre FROM aeemciste_carte_membre WHERE matricule_gen = ? LIMIT 1");
                            $uStmt->execute([$matricule]);
                            $u = $uStmt->fetch(PDO::FETCH_ASSOC);
                            if ($u) { $prenom = $u['prenom'] ?? ''; $nom = $u['nom'] ?? ''; $photo_membre = $u['photo_membre'] ?? ''; }
                        } catch (Throwable $e3) { }
                        $full_name = trim(($prenom?:'').' '.($nom?:''));
                        
                        // Construire insertion dynamique minimale
                        $pfFields = [];
                        $pfPlace = [];
                        $pfValues = [];
                        $push = function($c,$v) use (&$pfFields,&$pfPlace,&$pfValues,$colsProf){ if (in_array($c,$colsProf,true)) { $pfFields[]=$c; $pfPlace[]='?'; $pfValues[]=$v; } };
                        
                        // Matricule
                        if (in_array('matricule_gen',$colsProf,true)) $push('matricule_gen',$matricule);
                        elseif (in_array('matricule',$colsProf,true)) $push('matricule',$matricule);
                        elseif (in_array('user_matricule',$colsProf,true)) $push('user_matricule',$matricule);
                        
                        // Nom complet
                        if (in_array('full_name',$colsProf,true) && $full_name!=='') $push('full_name',$full_name);
                        
                        // Photo
                        if (in_array('photo_url',$colsProf,true) && $photo_membre!=='') $push('photo_url',$photo_membre);
                        
                        // Timestamps
                        if (in_array('created_at',$colsProf,true)) { $pfFields[]='created_at'; $pfPlace[]='NOW()'; }
                        if (in_array('updated_at',$colsProf,true)) { $pfFields[]='updated_at'; $pfPlace[]='NOW()'; }
                        
                        if (!empty($pfFields)) {
                            $sqlP = "INSERT INTO p_user_profiles (".implode(',', $pfFields).") VALUES (".implode(',', array_map(function($p){return $p==='NOW()'?'NOW()':$p;}, $pfPlace)).")";
                            $stmtP = $pdo->prepare($sqlP);
                            $bindVals = [];
                            foreach ($pfPlace as $i => $p) if ($p==='?') $bindVals[] = $pfValues[$i] ?? null;
                            $stmtP->execute($bindVals);
                            $user_profile_id = (int)$pdo->lastInsertId();
                        }
                    } catch (Throwable $e4) { 
                        error_log('Erreur création profil utilisateur: '.$e4->getMessage());
                    }
                }
            }
            
            // Insérer le commentaire
            try {
                $fields = ['post_id', 'type', 'content'];
                $values = [$post_id, 'comment', $content];
                $placeholders = ['?', '?', '?'];
                
                // Ajouter l'utilisateur
                if ($needUserProfileId && $user_profile_id) {
                    $fields[] = 'user_profile_id';
                    $values[] = $user_profile_id;
                    $placeholders[] = '?';
                } elseif (in_array('author_matricule', $cCols, true)) {
                    $fields[] = 'author_matricule';
                    $values[] = $matricule;
                    $placeholders[] = '?';
                }
                
                if ($parent_comment_id !== null && in_array('parent_comment_id', $cCols, true)) {
                    $fields[] = 'parent_comment_id';
                    $values[] = $parent_comment_id;
                    $placeholders[] = '?';
                }
                
                if (in_array('created_at', $cCols, true)) {
                    $fields[] = 'created_at';
                    $placeholders[] = 'NOW()';
                }
                
                if (in_array('updated_at', $cCols, true)) {
                    $fields[] = 'updated_at';
                    $placeholders[] = 'NOW()';
                }
                
                $sqlC = "INSERT INTO p_comments (" . implode(',', $fields) . ") VALUES (" . implode(',', $placeholders) . ")";
                $stmt = $pdo->prepare($sqlC);
                $stmt->execute($values);
                
                $comment_id = $pdo->lastInsertId();
                
                // Retourner le commentaire créé avec les infos utilisateur
                $newComment = [
                    'id' => (int)$comment_id,
                    'text' => $textContent,
                    'author' => [
                        'name' => 'Utilisateur AEEMCI', // Sera amélioré plus tard
                        'avatar' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/1200px-User_icon_2.svg.png'
                    ],
                    'timestamp' => 'À l\'instant',
                    'created_at' => date('Y-m-d H:i:s')
                ];
                
                // Essayer de récupérer le nom de l'utilisateur
                if (!empty($matricule)) {
                    try {
                        $userStmt = $pdo->prepare("SELECT prenom, nom, photo_membre FROM aeemciste_carte_membre WHERE matricule_gen = ? LIMIT 1");
                        $userStmt->execute([$matricule]);
                        $userInfo = $userStmt->fetch(PDO::FETCH_ASSOC);
                        if ($userInfo) {
                            $prenom = $userInfo['prenom'] ?? '';
                            $nom = $userInfo['nom'] ?? '';
                            $fullName = trim($prenom . ' ' . $nom) ?: $matricule;
                            $newComment['author']['name'] = $fullName;
                            if (!empty($userInfo['photo_membre'])) {
                                $newComment['author']['avatar'] = $userInfo['photo_membre'];
                            }
                        } else {
                            // Si pas trouvé dans aeemciste_carte_membre, utiliser le matricule
                            $newComment['author']['name'] = $matricule;
                        }
                    } catch (Throwable $e) {
                        // En cas d'erreur, utiliser le matricule
                        $newComment['author']['name'] = $matricule;
                        error_log('Erreur récupération utilisateur: ' . $e->getMessage());
                    }
                }
                
                sendSuccess([
                    'comment_id' => $comment_id,
                    'message' => 'Commentaire ajouté',
                    'comment' => $newComment
                ]);
                
            } catch(Throwable $e) {
                error_log('Erreur ajout commentaire: '.$e->getMessage());
                sendError('Erreur ajout commentaire: '.$e->getMessage(), 500);
            }
            break;

        case 'toggle_reaction':
            $input = json_decode(file_get_contents('php://input'),true);
            $post_id = $input['post_id'] ?? '';
            $matricule = $input['matricule'] ?? '';
            $reaction_type = $input['reaction_type'] ?? 'like';
            
            if(!$post_id || !$matricule) sendError('post_id et matricule requis');
            
            // Détecter schéma p_comments
            $cCols = [];
            try { 
                $cCols = $pdo->query("DESCRIBE p_comments")->fetchAll(PDO::FETCH_COLUMN); 
            } catch(Throwable $e) {
                error_log('Erreur DESCRIBE p_comments: '.$e->getMessage());
                sendError('Table p_comments non accessible: '.$e->getMessage(), 500);
            }
            
            // Résoudre user_profile_id si nécessaire
            $user_profile_id = null;
            $needUserProfileId = in_array('user_profile_id', $cCols, true);
            if ($needUserProfileId) {
                // Tenter de trouver un profil existant
                $tryCols = ['matricule_gen','matricule','user_matricule'];
                foreach ($tryCols as $c) {
                    try {
                        $q = $pdo->prepare("SELECT id FROM p_user_profiles WHERE $c = ? LIMIT 1");
                        $q->execute([$matricule]);
                        $found = $q->fetchColumn();
                        if ($found) { $user_profile_id = (int)$found; break; }
                    } catch (Throwable $e2) { }
                }
                
                // Si non trouvé, créer un profil minimal
                if ($user_profile_id === null) {
                    try {
                        $colsProf = $pdo->query("DESCRIBE p_user_profiles")->fetchAll(PDO::FETCH_COLUMN);
                        // Récupérer infos utilisateur de aeemciste_carte_membre
                        $prenom = $nom = $photo_membre = '';
                        try {
                            $uStmt = $pdo->prepare("SELECT prenom, nom, photo_membre FROM aeemciste_carte_membre WHERE matricule_gen = ? LIMIT 1");
                            $uStmt->execute([$matricule]);
                            $u = $uStmt->fetch(PDO::FETCH_ASSOC);
                            if ($u) { $prenom = $u['prenom'] ?? ''; $nom = $u['nom'] ?? ''; $photo_membre = $u['photo_membre'] ?? ''; }
                        } catch (Throwable $e3) { }
                        $full_name = trim(($prenom?:'').' '.($nom?:''));
                        
                        // Construire insertion dynamique minimale
                        $pfFields = [];
                        $pfPlace = [];
                        $pfValues = [];
                        $push = function($c,$v) use (&$pfFields,&$pfPlace,&$pfValues,$colsProf){ if (in_array($c,$colsProf,true)) { $pfFields[]=$c; $pfPlace[]='?'; $pfValues[]=$v; } };
                        
                        // Matricule
                        if (in_array('matricule_gen',$colsProf,true)) $push('matricule_gen',$matricule);
                        elseif (in_array('matricule',$colsProf,true)) $push('matricule',$matricule);
                        elseif (in_array('user_matricule',$colsProf,true)) $push('user_matricule',$matricule);
                        
                        // Nom complet
                        if (in_array('full_name',$colsProf,true) && $full_name!=='') $push('full_name',$full_name);
                        
                        // Photo
                        if (in_array('photo_url',$colsProf,true) && $photo_membre!=='') $push('photo_url',$photo_membre);
                        
                        // Timestamps
                        if (in_array('created_at',$colsProf,true)) { $pfFields[]='created_at'; $pfPlace[]='NOW()'; }
                        if (in_array('updated_at',$colsProf,true)) { $pfFields[]='updated_at'; $pfPlace[]='NOW()'; }
                        
                        if (!empty($pfFields)) {
                            $sqlP = "INSERT INTO p_user_profiles (".implode(',', $pfFields).") VALUES (".implode(',', array_map(function($p){return $p==='NOW()'?'NOW()':$p;}, $pfPlace)).")";
                            $stmtP = $pdo->prepare($sqlP);
                            $bindVals = [];
                            foreach ($pfPlace as $i => $p) if ($p==='?') $bindVals[] = $pfValues[$i] ?? null;
                            $stmtP->execute($bindVals);
                            $user_profile_id = (int)$pdo->lastInsertId();
                        }
                    } catch (Throwable $e4) { 
                        error_log('Erreur création profil utilisateur: '.$e4->getMessage());
                    }
                }
            }
            
            // Vérifier existence de la réaction
            $ex = null;
            try {
                $sqlChk = "SELECT id FROM p_comments WHERE post_id=? AND type='reaction'";
                $params = [$post_id];
                
                // Ajouter condition sur l'utilisateur
                if ($needUserProfileId && $user_profile_id) {
                    $sqlChk .= " AND user_profile_id=?";
                    $params[] = $user_profile_id;
                } elseif (in_array('author_matricule', $cCols, true)) {
                    $sqlChk .= " AND author_matricule=?";
                    $params[] = $matricule;
                }
                
                if (in_array('reaction_type', $cCols, true)) {
                    $sqlChk .= " AND reaction_type=?";
                    $params[] = $reaction_type;
                }
                $sqlChk .= " LIMIT 1";
                
                $chk = $pdo->prepare($sqlChk); 
                $chk->execute($params); 
                $ex = $chk->fetch(PDO::FETCH_ASSOC);
            } catch(Throwable $e) { 
                error_log('Erreur check réaction: '.$e->getMessage());
                sendError('Erreur lors de la vérification de la réaction: '.$e->getMessage(), 500);
            }
            
            $actionDone = '';
            
            if($ex){
                // Supprimer la réaction existante
                try { 
                    $del = $pdo->prepare("DELETE FROM p_comments WHERE id=?"); 
                    $del->execute([$ex['id']]); 
                    $actionDone = 'removed'; 
                } catch(Throwable $e){ 
                    error_log('Erreur suppression réaction: '.$e->getMessage());
                    sendError('Erreur suppression réaction: '.$e->getMessage(), 500); 
                }
            } else {
                // Ajouter une nouvelle réaction
                try {
                    $fields = ['post_id', 'type'];
                    $values = [$post_id, 'reaction'];
                    $placeholders = ['?', '?'];
                    
                    // Ajouter l'utilisateur
                    if ($needUserProfileId && $user_profile_id) {
                        $fields[] = 'user_profile_id';
                        $values[] = $user_profile_id;
                        $placeholders[] = '?';
                    } elseif (in_array('author_matricule', $cCols, true)) {
                        $fields[] = 'author_matricule';
                        $values[] = $matricule;
                        $placeholders[] = '?';
                    }
                    
                    if (in_array('reaction_type', $cCols, true)) {
                        $fields[] = 'reaction_type';
                        $values[] = $reaction_type;
                        $placeholders[] = '?';
                    }
                    
                    if (in_array('created_at', $cCols, true)) {
                        $fields[] = 'created_at';
                        $placeholders[] = 'NOW()';
                    }
                    
                    if (in_array('updated_at', $cCols, true)) {
                        $fields[] = 'updated_at';
                        $placeholders[] = 'NOW()';
                    }
                    
                    $sqlR = "INSERT INTO p_comments (" . implode(',', $fields) . ") VALUES (" . implode(',', $placeholders) . ")";
                    $ins = $pdo->prepare($sqlR);
                    $ins->execute($values);
                    $actionDone = 'added';
                    
                } catch(Throwable $e){ 
                    error_log('Erreur ajout réaction: '.$e->getMessage());
                    sendError('Erreur ajout réaction: '.$e->getMessage(), 500); 
                }
            }
            
            sendSuccess(['action'=>$actionDone,'post_id'=>$post_id,'reaction_type'=>$reaction_type,'message'=>'Réaction '.($actionDone === 'added' ? 'ajoutée' : 'supprimée')]);
            break;

        case 'get_comments':
            $post_id = $_GET['post_id'] ?? '';
            
            if (empty($post_id)) {
                sendError('post_id requis');
            }
            
            try {
                // Vérifier que la table p_comments existe
                $tableCheck = $pdo->query("SHOW TABLES LIKE 'p_comments'")->fetch();
                if (!$tableCheck) {
                    sendError('Table p_comments non trouvée', 500);
                }
                
                // Vérifier les colonnes disponibles
                $columns = $pdo->query("DESCRIBE p_comments")->fetchAll(PDO::FETCH_COLUMN);
                
                // Construire la requête selon les colonnes disponibles
                $selectFields = ['id', 'post_id', 'type'];
                if (in_array('content', $columns)) $selectFields[] = 'content';
                if (in_array('author_matricule', $columns)) $selectFields[] = 'author_matricule';
                if (in_array('user_profile_id', $columns)) $selectFields[] = 'user_profile_id';
                if (in_array('created_at', $columns)) $selectFields[] = 'created_at';
                
                $sql = "SELECT " . implode(', ', $selectFields) . " FROM p_comments WHERE post_id = ?";
                
                // Ajouter la condition type si la colonne existe
                if (in_array('type', $columns)) {
                    $sql .= " AND type = 'comment'";
                }
                
                $sql .= " ORDER BY " . (in_array('created_at', $columns) ? 'created_at' : 'id') . " ASC";
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$post_id]);
                $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Formater les commentaires
                $formattedComments = [];
                foreach ($comments as $comment) {
                    $authorName = 'Utilisateur AEEMCI';
                    $authorAvatar = 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/1200px-User_icon_2.svg.png';
                    
                    // Récupérer le nom réel de l'utilisateur depuis aeemciste_carte_membre
                    if (isset($comment['author_matricule']) && !empty($comment['author_matricule'])) {
                        try {
                            $userStmt = $pdo->prepare("SELECT prenom, nom, photo_membre FROM aeemciste_carte_membre WHERE matricule_gen = ? LIMIT 1");
                            $userStmt->execute([$comment['author_matricule']]);
                            $userInfo = $userStmt->fetch(PDO::FETCH_ASSOC);
                            if ($userInfo) {
                                $prenom = $userInfo['prenom'] ?? '';
                                $nom = $userInfo['nom'] ?? '';
                                $fullName = trim($prenom . ' ' . $nom) ?: $comment['author_matricule'];
                                $authorName = $fullName;
                                if (!empty($userInfo['photo_membre'])) {
                                    $authorAvatar = $userInfo['photo_membre'];
                                }
                            } else {
                                // Si pas trouvé, utiliser le matricule
                                $authorName = $comment['author_matricule'];
                            }
                        } catch (Throwable $e) {
                            // En cas d'erreur, utiliser le matricule
                            $authorName = $comment['author_matricule'] ?? 'Utilisateur AEEMCI';
                            error_log('Erreur récupération utilisateur commentaire: ' . $e->getMessage());
                        }
                    }
                    
                    $formattedComments[] = [
                        'id' => (int)$comment['id'],
                        'text' => $comment['content'] ?? '',
                        'author' => [
                            'name' => $authorName,
                            'avatar' => $authorAvatar
                        ],
                        'timestamp' => isset($comment['created_at']) && $comment['created_at'] ? formatTimestamp($comment['created_at']) : 'À l\'instant',
                        'created_at' => $comment['created_at'] ?? null
                    ];
                }
                
                sendSuccess(['comments' => $formattedComments, 'count' => count($formattedComments)]);
                
            } catch (Throwable $e) {
                error_log('🔥 Erreur get_comments: ' . $e->getMessage());
                sendError('Erreur lors de la récupération des commentaires: ' . $e->getMessage(), 500);
            }
            break;

        case 'get_comments_count':
            $post_id = $_GET['post_id'] ?? '';
            
            if (empty($post_id)) {
                sendError('post_id requis');
            }
            
            try {
                $sql = "SELECT COUNT(*) FROM p_comments WHERE post_id = ? AND type = 'comment'";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$post_id]);
                $count = (int)$stmt->fetchColumn();
                
                sendSuccess(['count' => $count]);
                
            } catch (Throwable $e) {
                error_log('🔥 Erreur get_comments_count: ' . $e->getMessage());
                sendError('Erreur lors du comptage des commentaires: ' . $e->getMessage(), 500);
            }
            break;

        case 'toggle_comment_like':
            $input = json_decode(file_get_contents('php://input'),true);
            $comment_id = $input['comment_id'] ?? '';
            $matricule = $input['matricule'] ?? '';
            $post_id = $input['post_id'] ?? '';
            
            if(!$comment_id || !$matricule) sendError('comment_id et matricule requis');
            
            // Détecter schéma p_comments
            $cCols = [];
            try { 
                $cCols = $pdo->query("DESCRIBE p_comments")->fetchAll(PDO::FETCH_COLUMN); 
            } catch(Throwable $e) {
                sendError('Table p_comments non accessible: '.$e->getMessage(), 500);
            }
            
            // Vérifier si une table p_comment_likes existe, sinon utiliser p_comments
            $useLikesTable = false;
            try {
                $pdo->query("DESCRIBE p_comment_likes");
                $useLikesTable = true;
            } catch(Throwable $e) {
                // Table p_comment_likes n'existe pas, utiliser p_comments avec type='comment_like'
            }
            
            if ($useLikesTable) {
                // Utiliser la table dédiée p_comment_likes
                try {
                    $checkStmt = $pdo->prepare("SELECT id FROM p_comment_likes WHERE comment_id = ? AND user_matricule = ? LIMIT 1");
                    $checkStmt->execute([$comment_id, $matricule]);
                    $existing = $checkStmt->fetch();
                    
                    if ($existing) {
                        // Supprimer le like
                        $deleteStmt = $pdo->prepare("DELETE FROM p_comment_likes WHERE id = ?");
                        $deleteStmt->execute([$existing['id']]);
                        $action = 'removed';
                    } else {
                        // Ajouter le like
                        $insertStmt = $pdo->prepare("INSERT INTO p_comment_likes (comment_id, user_matricule, created_at) VALUES (?, ?, NOW())");
                        $insertStmt->execute([$comment_id, $matricule]);
                        $action = 'added';
                    }
                } catch(Throwable $e) {
                    sendError('Erreur toggle comment like: '.$e->getMessage(), 500);
                }
            } else {
                // Utiliser p_comments avec type='comment_like'
                // Résoudre user_profile_id si nécessaire (même logique que add_comment)
                $user_profile_id = null;
                $needUserProfileId = in_array('user_profile_id', $cCols, true);
                if ($needUserProfileId) {
                    // Tenter de trouver un profil existant
                    $tryCols = ['matricule_gen','matricule','user_matricule'];
                    foreach ($tryCols as $c) {
                        try {
                            $q = $pdo->prepare("SELECT id FROM p_user_profiles WHERE $c = ? LIMIT 1");
                            $q->execute([$matricule]);
                            $found = $q->fetchColumn();
                            if ($found) { $user_profile_id = (int)$found; break; }
                        } catch (Throwable $e2) { }
                    }
                    
                    // Si non trouvé, créer un profil minimal
                    if ($user_profile_id === null) {
                        try {
                            $colsProf = $pdo->query("DESCRIBE p_user_profiles")->fetchAll(PDO::FETCH_COLUMN);
                            // Récupérer infos utilisateur de aeemciste_carte_membre
                            $prenom = $nom = $photo_membre = '';
                            try {
                                $uStmt = $pdo->prepare("SELECT prenom, nom, photo_membre FROM aeemciste_carte_membre WHERE matricule_gen = ? LIMIT 1");
                                $uStmt->execute([$matricule]);
                                $u = $uStmt->fetch(PDO::FETCH_ASSOC);
                                if ($u) { $prenom = $u['prenom'] ?? ''; $nom = $u['nom'] ?? ''; $photo_membre = $u['photo_membre'] ?? ''; }
                            } catch (Throwable $e3) { }
                            $full_name = trim(($prenom?:'').' '.($nom?:''));
                            
                            // Construire insertion dynamique minimale
                            $pfFields = [];
                            $pfPlace = [];
                            $pfValues = [];
                            $push = function($c,$v) use (&$pfFields,&$pfPlace,&$pfValues,$colsProf){ if (in_array($c,$colsProf,true)) { $pfFields[]=$c; $pfPlace[]='?'; $pfValues[]=$v; } };
                            
                            // Matricule
                            if (in_array('matricule_gen',$colsProf,true)) $push('matricule_gen',$matricule);
                            elseif (in_array('matricule',$colsProf,true)) $push('matricule',$matricule);
                            elseif (in_array('user_matricule',$colsProf,true)) $push('user_matricule',$matricule);
                            
                            // Nom complet
                            if (in_array('full_name',$colsProf,true) && $full_name!=='') $push('full_name',$full_name);
                            
                            // Photo
                            if (in_array('photo_url',$colsProf,true) && $photo_membre!=='') $push('photo_url',$photo_membre);
                            
                            // Timestamps
                            if (in_array('created_at',$colsProf,true)) { $pfFields[]='created_at'; $pfPlace[]='NOW()'; }
                            if (in_array('updated_at',$colsProf,true)) { $pfFields[]='updated_at'; $pfPlace[]='NOW()'; }
                            
                            if (!empty($pfFields)) {
                                $sqlP = "INSERT INTO p_user_profiles (".implode(',', $pfFields).") VALUES (".implode(',', array_map(function($p){return $p==='NOW()'?'NOW()':$p;}, $pfPlace)).")";
                                $stmtP = $pdo->prepare($sqlP);
                                $bindVals = [];
                                foreach ($pfPlace as $i => $p) if ($p==='?') $bindVals[] = $pfValues[$i] ?? null;
                                $stmtP->execute($bindVals);
                                $user_profile_id = (int)$pdo->lastInsertId();
                            }
                        } catch (Throwable $e4) { 
                            error_log('Erreur création profil utilisateur pour like commentaire: '.$e4->getMessage());
                        }
                    }
                }
                
                try {
                    $checkSql = "SELECT id FROM p_comments WHERE type='comment_like' AND content = ?";
                    $checkParams = [$comment_id];
                    
                    // Ajouter condition sur l'utilisateur
                    if ($needUserProfileId && $user_profile_id) {
                        $checkSql .= " AND user_profile_id = ?";
                        $checkParams[] = $user_profile_id;
                    } elseif (in_array('author_matricule', $cCols, true)) {
                        $checkSql .= " AND author_matricule = ?";
                        $checkParams[] = $matricule;
                    }
                    $checkSql .= " LIMIT 1";
                    
                    $checkStmt = $pdo->prepare($checkSql);
                    $checkStmt->execute($checkParams);
                    $existing = $checkStmt->fetch();
                    
                    if ($existing) {
                        // Supprimer le like
                        $deleteStmt = $pdo->prepare("DELETE FROM p_comments WHERE id = ?");
                        $deleteStmt->execute([$existing['id']]);
                        $action = 'removed';
                    } else {
                        // Ajouter le like - utiliser content au lieu de parent_comment_id
                        $fields = ['type', 'content'];
                        $values = ['comment_like', $comment_id];
                        $placeholders = ['?', '?'];
                        
                        if (in_array('post_id', $cCols, true)) {
                            $fields[] = 'post_id';
                            $values[] = $post_id;
                            $placeholders[] = '?';
                        }
                        
                        // Ajouter l'utilisateur
                        if ($needUserProfileId && $user_profile_id) {
                            $fields[] = 'user_profile_id';
                            $values[] = $user_profile_id;
                            $placeholders[] = '?';
                        } elseif (in_array('author_matricule', $cCols, true)) {
                            $fields[] = 'author_matricule';
                            $values[] = $matricule;
                            $placeholders[] = '?';
                        }
                        
                        if (in_array('created_at', $cCols, true)) {
                            $fields[] = 'created_at';
                            $placeholders[] = 'NOW()';
                        }
                        
                        $insertSql = "INSERT INTO p_comments (" . implode(',', $fields) . ") VALUES (" . implode(',', $placeholders) . ")";
                        $insertStmt = $pdo->prepare($insertSql);
                        $insertStmt->execute($values);
                        $action = 'added';
                    }
                } catch(Throwable $e) {
                    error_log('Erreur toggle comment like: '.$e->getMessage());
                    sendError('Erreur toggle comment like: '.$e->getMessage(), 500);
                }
            }
            
            sendSuccess(['action' => $action, 'comment_id' => $comment_id, 'message' => 'Like commentaire ' . ($action === 'added' ? 'ajouté' : 'supprimé')]);
            break;

        case 'add_reply':
            $input = json_decode(file_get_contents('php://input'),true);
            $comment_id = $input['comment_id'] ?? '';
            $matricule = $input['matricule'] ?? '';
            $post_id = $input['post_id'] ?? '';
            $text = $input['text'] ?? '';
            
            if(!$comment_id || !$matricule || !$text) sendError('comment_id, matricule et text requis');
            
            // Détecter schéma p_comments
            $cCols = [];
            try { 
                $cCols = $pdo->query("DESCRIBE p_comments")->fetchAll(PDO::FETCH_COLUMN); 
            } catch(Throwable $e) {
                sendError('Table p_comments non accessible: '.$e->getMessage(), 500);
            }
            
            // Résoudre user_profile_id si nécessaire (même logique que add_comment)
            $user_profile_id = null;
            $needUserProfileId = in_array('user_profile_id', $cCols, true);
            if ($needUserProfileId) {
                // Tenter de trouver un profil existant
                $tryCols = ['matricule_gen','matricule','user_matricule'];
                foreach ($tryCols as $c) {
                    try {
                        $q = $pdo->prepare("SELECT id FROM p_user_profiles WHERE $c = ? LIMIT 1");
                        $q->execute([$matricule]);
                        $found = $q->fetchColumn();
                        if ($found) { $user_profile_id = (int)$found; break; }
                    } catch (Throwable $e2) { }
                }
                
                // Si non trouvé, créer un profil minimal
                if ($user_profile_id === null) {
                    try {
                        $colsProf = $pdo->query("DESCRIBE p_user_profiles")->fetchAll(PDO::FETCH_COLUMN);
                        // Récupérer infos utilisateur de aeemciste_carte_membre
                        $prenom = $nom = $photo_membre = '';
                        try {
                            $uStmt = $pdo->prepare("SELECT prenom, nom, photo_membre FROM aeemciste_carte_membre WHERE matricule_gen = ? LIMIT 1");
                            $uStmt->execute([$matricule]);
                            $u = $uStmt->fetch(PDO::FETCH_ASSOC);
                            if ($u) { $prenom = $u['prenom'] ?? ''; $nom = $u['nom'] ?? ''; $photo_membre = $u['photo_membre'] ?? ''; }
                        } catch (Throwable $e3) { }
                        $full_name = trim(($prenom?:'').' '.($nom?:''));
                        
                        // Construire insertion dynamique minimale
                        $pfFields = [];
                        $pfPlace = [];
                        $pfValues = [];
                        $push = function($c,$v) use (&$pfFields,&$pfPlace,&$pfValues,$colsProf){ if (in_array($c,$colsProf,true)) { $pfFields[]=$c; $pfPlace[]='?'; $pfValues[]=$v; } };
                        
                        // Matricule
                        if (in_array('matricule_gen',$colsProf,true)) $push('matricule_gen',$matricule);
                        elseif (in_array('matricule',$colsProf,true)) $push('matricule',$matricule);
                        elseif (in_array('user_matricule',$colsProf,true)) $push('user_matricule',$matricule);
                        
                        // Nom complet
                        if (in_array('full_name',$colsProf,true) && $full_name!=='') $push('full_name',$full_name);
                        
                        // Photo
                        if (in_array('photo_url',$colsProf,true) && $photo_membre!=='') $push('photo_url',$photo_membre);
                        
                        // Timestamps
                        if (in_array('created_at',$colsProf,true)) { $pfFields[]='created_at'; $pfPlace[]='NOW()'; }
                        if (in_array('updated_at',$colsProf,true)) { $pfFields[]='updated_at'; $pfPlace[]='NOW()'; }
                        
                        if (!empty($pfFields)) {
                            $sqlP = "INSERT INTO p_user_profiles (".implode(',', $pfFields).") VALUES (".implode(',', array_map(function($p){return $p==='NOW()'?'NOW()':$p;}, $pfPlace)).")";
                            $stmtP = $pdo->prepare($sqlP);
                            $bindVals = [];
                            foreach ($pfPlace as $i => $p) if ($p==='?') $bindVals[] = $pfValues[$i] ?? null;
                            $stmtP->execute($bindVals);
                            $user_profile_id = (int)$pdo->lastInsertId();
                        }
                    } catch (Throwable $e4) { 
                        error_log('Erreur création profil utilisateur pour réponse: '.$e4->getMessage());
                    }
                }
            }
            
            try {
                $fields = ['type', 'content', 'parent_comment_id'];
                $values = ['reply', $text, $comment_id];
                $placeholders = ['?', '?', '?'];
                
                if (in_array('post_id', $cCols, true)) {
                    $fields[] = 'post_id';
                    $values[] = $post_id;
                    $placeholders[] = '?';
                }
                
                // Ajouter l'utilisateur
                if ($needUserProfileId && $user_profile_id) {
                    $fields[] = 'user_profile_id';
                    $values[] = $user_profile_id;
                    $placeholders[] = '?';
                } elseif (in_array('author_matricule', $cCols, true)) {
                    $fields[] = 'author_matricule';
                    $values[] = $matricule;
                    $placeholders[] = '?';
                }
                
                if (in_array('created_at', $cCols, true)) {
                    $fields[] = 'created_at';
                    $placeholders[] = 'NOW()';
                }
                
                if (in_array('updated_at', $cCols, true)) {
                    $fields[] = 'updated_at';
                    $placeholders[] = 'NOW()';
                }
                
                // Vérifier si le parent_comment_id existe dans la base
                if (in_array('parent_comment_id', $fields)) {
                    $parentExists = false;
                    try {
                        $checkParent = $pdo->prepare("SELECT id FROM p_comments WHERE id = ? LIMIT 1");
                        $checkParent->execute([$comment_id]);
                        $parentExists = $checkParent->fetch() !== false;
                    } catch (Throwable $e) {
                        error_log('Erreur vérification parent: '.$e->getMessage());
                    }
                    
                    if (!$parentExists) {
                        // Si le parent n'existe pas, ne pas mettre parent_comment_id
                        $parentIndex = array_search('parent_comment_id', $fields);
                        if ($parentIndex !== false) {
                            unset($fields[$parentIndex]);
                            unset($values[$parentIndex]);
                            unset($placeholders[$parentIndex]);
                            // Réindexer les tableaux
                            $fields = array_values($fields);
                            $values = array_values($values);
                            $placeholders = array_values($placeholders);
                        }
                        error_log('Parent comment_id '.$comment_id.' n\'existe pas, création sans parent');
                    }
                }
                
                $insertSql = "INSERT INTO p_comments (" . implode(',', $fields) . ") VALUES (" . implode(',', $placeholders) . ")";
                $stmt = $pdo->prepare($insertSql);
                $stmt->execute($values);
                
                $reply_id = $pdo->lastInsertId();
                
                sendSuccess(['reply_id' => $reply_id, 'message' => 'Réponse ajoutée']);
                
            } catch(Throwable $e) {
                error_log('Erreur ajout réponse: '.$e->getMessage());
                sendError('Erreur ajout réponse: '.$e->getMessage(), 500);
            }
            break;

        case 'toggle_reply_like':
            $input = json_decode(file_get_contents('php://input'),true);
            $reply_id = $input['reply_id'] ?? '';
            $matricule = $input['matricule'] ?? '';
            $comment_id = $input['comment_id'] ?? '';
            $post_id = $input['post_id'] ?? '';
            
            if(!$reply_id || !$matricule) sendError('reply_id et matricule requis');
            
            // Détecter schéma p_comments
            $cCols = [];
            try { 
                $cCols = $pdo->query("DESCRIBE p_comments")->fetchAll(PDO::FETCH_COLUMN); 
            } catch(Throwable $e) {
                sendError('Table p_comments non accessible: '.$e->getMessage(), 500);
            }
            
            // Résoudre user_profile_id si nécessaire (même logique que add_comment)
            $user_profile_id = null;
            $needUserProfileId = in_array('user_profile_id', $cCols, true);
            if ($needUserProfileId) {
                // Tenter de trouver un profil existant
                $tryCols = ['matricule_gen','matricule','user_matricule'];
                foreach ($tryCols as $c) {
                    try {
                        $q = $pdo->prepare("SELECT id FROM p_user_profiles WHERE $c = ? LIMIT 1");
                        $q->execute([$matricule]);
                        $found = $q->fetchColumn();
                        if ($found) { $user_profile_id = (int)$found; break; }
                    } catch (Throwable $e2) { }
                }
                
                // Si non trouvé, créer un profil minimal
                if ($user_profile_id === null) {
                    try {
                        $colsProf = $pdo->query("DESCRIBE p_user_profiles")->fetchAll(PDO::FETCH_COLUMN);
                        // Récupérer infos utilisateur de aeemciste_carte_membre
                        $prenom = $nom = $photo_membre = '';
                        try {
                            $uStmt = $pdo->prepare("SELECT prenom, nom, photo_membre FROM aeemciste_carte_membre WHERE matricule_gen = ? LIMIT 1");
                            $uStmt->execute([$matricule]);
                            $u = $uStmt->fetch(PDO::FETCH_ASSOC);
                            if ($u) { $prenom = $u['prenom'] ?? ''; $nom = $u['nom'] ?? ''; $photo_membre = $u['photo_membre'] ?? ''; }
                        } catch (Throwable $e3) { }
                        $full_name = trim(($prenom?:'').' '.($nom?:''));
                        
                        // Construire insertion dynamique minimale
                        $pfFields = [];
                        $pfPlace = [];
                        $pfValues = [];
                        $push = function($c,$v) use (&$pfFields,&$pfPlace,&$pfValues,$colsProf){ if (in_array($c,$colsProf,true)) { $pfFields[]=$c; $pfPlace[]='?'; $pfValues[]=$v; } };
                        
                        // Matricule
                        if (in_array('matricule_gen',$colsProf,true)) $push('matricule_gen',$matricule);
                        elseif (in_array('matricule',$colsProf,true)) $push('matricule',$matricule);
                        elseif (in_array('user_matricule',$colsProf,true)) $push('user_matricule',$matricule);
                        
                        // Nom complet
                        if (in_array('full_name',$colsProf,true) && $full_name!=='') $push('full_name',$full_name);
                        
                        // Photo
                        if (in_array('photo_url',$colsProf,true) && $photo_membre!=='') $push('photo_url',$photo_membre);
                        
                        // Timestamps
                        if (in_array('created_at',$colsProf,true)) { $pfFields[]='created_at'; $pfPlace[]='NOW()'; }
                        if (in_array('updated_at',$colsProf,true)) { $pfFields[]='updated_at'; $pfPlace[]='NOW()'; }
                        
                        if (!empty($pfFields)) {
                            $sqlP = "INSERT INTO p_user_profiles (".implode(',', $pfFields).") VALUES (".implode(',', array_map(function($p){return $p==='NOW()'?'NOW()':$p;}, $pfPlace)).")";
                            $stmtP = $pdo->prepare($sqlP);
                            $bindVals = [];
                            foreach ($pfPlace as $i => $p) if ($p==='?') $bindVals[] = $pfValues[$i] ?? null;
                            $stmtP->execute($bindVals);
                            $user_profile_id = (int)$pdo->lastInsertId();
                        }
                    } catch (Throwable $e4) { 
                        error_log('Erreur création profil utilisateur pour like réponse: '.$e4->getMessage());
                    }
                }
            }
            
            try {
                // Vérifier si un like existe déjà pour cette réponse
                // Utiliser le champ content au lieu de parent_comment_id pour éviter les contraintes
                $checkSql = "SELECT id FROM p_comments WHERE type='reply_like' AND content = ?";
                $checkParams = [$reply_id];
                
                // Ajouter condition sur l'utilisateur
                if ($needUserProfileId && $user_profile_id) {
                    $checkSql .= " AND user_profile_id = ?";
                    $checkParams[] = $user_profile_id;
                } elseif (in_array('author_matricule', $cCols, true)) {
                    $checkSql .= " AND author_matricule = ?";
                    $checkParams[] = $matricule;
                }
                $checkSql .= " LIMIT 1";
                
                $checkStmt = $pdo->prepare($checkSql);
                $checkStmt->execute($checkParams);
                $existing = $checkStmt->fetch();
                
                if ($existing) {
                    // Supprimer le like
                    $deleteStmt = $pdo->prepare("DELETE FROM p_comments WHERE id = ?");
                    $deleteStmt->execute([$existing['id']]);
                    $action = 'removed';
                } else {
                    // Ajouter le like - utiliser content au lieu de parent_comment_id
                    $fields = ['type', 'content'];
                    $values = ['reply_like', $reply_id];
                    $placeholders = ['?', '?'];
                    
                    if (in_array('post_id', $cCols, true)) {
                        $fields[] = 'post_id';
                        $values[] = $post_id;
                        $placeholders[] = '?';
                    }
                    
                    // Ajouter l'utilisateur
                    if ($needUserProfileId && $user_profile_id) {
                        $fields[] = 'user_profile_id';
                        $values[] = $user_profile_id;
                        $placeholders[] = '?';
                    } elseif (in_array('author_matricule', $cCols, true)) {
                        $fields[] = 'author_matricule';
                        $values[] = $matricule;
                        $placeholders[] = '?';
                    }
                    
                    if (in_array('created_at', $cCols, true)) {
                        $fields[] = 'created_at';
                        $placeholders[] = 'NOW()';
                    }
                    
                    $insertSql = "INSERT INTO p_comments (" . implode(',', $fields) . ") VALUES (" . implode(',', $placeholders) . ")";
                    $insertStmt = $pdo->prepare($insertSql);
                    $insertStmt->execute($values);
                    $action = 'added';
                }
                
                sendSuccess(['action' => $action, 'reply_id' => $reply_id, 'message' => 'Like réponse ' . ($action === 'added' ? 'ajouté' : 'supprimé')]);
                
            } catch(Throwable $e) {
                error_log('Erreur toggle reply like: '.$e->getMessage());
                sendError('Erreur toggle reply like: '.$e->getMessage(), 500);
            }
            break;

        case 'get_comments_with_replies':
            $post_id = $_GET['post_id'] ?? '';
            
            if (empty($post_id)) {
                sendError('post_id requis');
            }
            
            try {
                // Récupérer tous les commentaires et réponses pour ce post (exclure les likes)
                $sql = "SELECT * FROM p_comments WHERE post_id = ? AND (type = 'comment' OR type = 'reply' OR type IS NULL OR type = '') ORDER BY created_at ASC";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$post_id]);
                $allComments = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Organiser les commentaires en structure hiérarchique
                $comments = [];
                $repliesMap = [];
                
                foreach ($allComments as $comment) {
                    // Récupérer les infos utilisateur
                    $authorName = 'Utilisateur AEEMCI';
                    $authorAvatar = 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/1200px-User_icon_2.svg.png';
                    
                    if (!empty($comment['author_matricule'])) {
                        try {
                            $userStmt = $pdo->prepare("SELECT prenom, nom, photo_membre FROM aeemciste_carte_membre WHERE matricule_gen = ? LIMIT 1");
                            $userStmt->execute([$comment['author_matricule']]);
                            $userInfo = $userStmt->fetch(PDO::FETCH_ASSOC);
                            if ($userInfo) {
                                $prenom = $userInfo['prenom'] ?? '';
                                $nom = $userInfo['nom'] ?? '';
                                $fullName = trim($prenom . ' ' . $nom) ?: $comment['author_matricule'];
                                $authorName = $fullName;
                                if (!empty($userInfo['photo_membre'])) {
                                    $authorAvatar = $userInfo['photo_membre'];
                                }
                            }
                        } catch (Throwable $e) {
                            error_log('Erreur récupération utilisateur: ' . $e->getMessage());
                        }
                    }
                    
                    $formattedComment = [
                        'id' => (int)$comment['id'],
                        'text' => $comment['content'] ?? '',
                        'author' => [
                            'name' => $authorName,
                            'avatar' => $authorAvatar
                        ],
                        'timestamp' => isset($comment['created_at']) && $comment['created_at'] ? formatTimestamp($comment['created_at']) : 'À l\'instant',
                        'created_at' => $comment['created_at'] ?? null,
                        'likes' => 0,
                        'isLiked' => false,
                        'replies' => []
                    ];
                    
                    if (empty($comment['parent_comment_id'])) {
                        // C'est un commentaire principal
                        $comments[] = $formattedComment;
                    } else {
                        // C'est une réponse
                        if (!isset($repliesMap[$comment['parent_comment_id']])) {
                            $repliesMap[$comment['parent_comment_id']] = [];
                        }
                        $repliesMap[$comment['parent_comment_id']][] = $formattedComment;
                    }
                }
                
                // Attacher les réponses aux commentaires
                foreach ($comments as &$comment) {
                    if (isset($repliesMap[$comment['id']])) {
                        $comment['replies'] = $repliesMap[$comment['id']];
                        
                        // Attacher les sous-réponses
                        foreach ($comment['replies'] as &$reply) {
                            if (isset($repliesMap[$reply['id']])) {
                                $reply['replies'] = $repliesMap[$reply['id']];
                            }
                        }
                    }
                }
                
                sendSuccess(['comments' => $comments, 'count' => count($comments)]);
                
            } catch (Throwable $e) {
                error_log('🔥 Erreur get_comments_with_replies: ' . $e->getMessage());
                sendError('Erreur lors de la récupération des commentaires: ' . $e->getMessage(), 500);
            }
            break;

        case 'delete_post':
            $input = json_decode(file_get_contents('php://input'),true);
            $post_id = $input['post_id'] ?? '';
            $matricule = $input['matricule'] ?? '';
            
            if(!$post_id || !$matricule) sendError('post_id et matricule requis');
            
            try {
                // Vérifier que l'utilisateur est l'auteur du post
                $checkStmt = $pdo->prepare("SELECT author_matricule FROM p_posts WHERE id = ? LIMIT 1");
                $checkStmt->execute([$post_id]);
                $post = $checkStmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$post) {
                    sendError('Post non trouvé', 404);
                }
                
                if ($post['author_matricule'] !== $matricule) {
                    sendError('Vous n\'êtes pas autorisé à supprimer ce post', 403);
                }
                
                // Supprimer d'abord tous les commentaires et réponses liés
                $deleteComments = $pdo->prepare("DELETE FROM p_comments WHERE post_id = ?");
                $deleteComments->execute([$post_id]);
                
                // Supprimer le post
                $deletePost = $pdo->prepare("DELETE FROM p_posts WHERE id = ?");
                $deletePost->execute([$post_id]);
                
                sendSuccess(['message' => 'Post supprimé avec succès']);
                
            } catch(Throwable $e) {
                error_log('Erreur suppression post: '.$e->getMessage());
                sendError('Erreur suppression post: '.$e->getMessage(), 500);
            }
            break;

        default:
            sendError('Action non reconnue: '.$action);
    }

} catch(Throwable $e){
    error_log('🔥 Exception: '.$e->getMessage());
    sendError('Erreur serveur: '.$e->getMessage(),500);
}
