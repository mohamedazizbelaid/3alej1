<?php
// api/auth.php avec sécurité renforcée
require_once __DIR__ . '/../includes/security.php';

// Activer la protection XSS
Security::xssProtection();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-CSRF-Token');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../includes/user_functions.php';

$userManager = new UserManager();
$response = ['success' => false, 'message' => 'Action non valide'];

// Vérification du token CSRF pour les actions sensibles
$csrf_token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? $_POST['csrf_token'] ?? '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    // Actions qui nécessitent une protection CSRF
    $protectedActions = ['login', 'register', 'reset_password'];
    
    if(in_array($action, $protectedActions)) {
        if(!Security::verifyCSRFToken($csrf_token)) {
            echo json_encode(['success' => false, 'message' => 'Token CSRF invalide']);
            exit();
        }
    }
    
    // Rate limiting
    $clientIP = $_SERVER['REMOTE_ADDR'];
    if(!Security::checkRateLimit($clientIP . '_' . $action)) {
        echo json_encode(['success' => false, 'message' => 'Trop de tentatives, réessayez plus tard']);
        exit();
    }
    
    switch($action) {
        case 'login':
            $email = Security::sanitizeInput($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            
            if(!Security::validateEmail($email)) {
                $response = ['success' => false, 'message' => 'Format email invalide'];
                break;
            }
            
            $response = $userManager->login($email, $password);
            break;
            
        case 'register':
            $nom = Security::sanitizeInput($_POST['nom'] ?? '');
            $email = Security::sanitizeInput($_POST['email'] ?? '');
            $telephone = Security::sanitizeInput($_POST['telephone'] ?? '');
            $password = $_POST['password'] ?? '';
            $role = $_POST['role'] ?? 'patient';
            
            if(!Security::validateEmail($email)) {
                $response = ['success' => false, 'message' => 'Format email invalide'];
                break;
            }
            
            if(!Security::validatePhone($telephone)) {
                $response = ['success' => false, 'message' => 'Format téléphone invalide (ex: 0612345678)'];
                break;
            }
            
            if(!Security::validatePassword($password)) {
                $response = ['success' => false, 'message' => 'Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule et un chiffre'];
                break;
            }
            
            $response = $userManager->register($nom, $email, $telephone, $password, $role);
            break;
            
        // ... autres actions
    }
} elseif($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Pour obtenir un token CSRF
    if(isset($_GET['action']) && $_GET['action'] === 'get_csrf') {
        $response = [
            'success' => true, 
            'csrf_token' => Security::generateCSRFToken()
        ];
    }
}

echo json_encode($response);
?>