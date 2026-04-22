<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

$response = ['success' => false, 'message' => 'Action non reconnue'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'login':
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            
            if (empty($email) || empty($password)) {
                $response = ['success' => false, 'message' => 'Email et mot de passe requis'];
                break;
            }
            
            $stmt = $conn->prepare("SELECT id, nom, email, telephone, password, role, status FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($user = $result->fetch_assoc()) {
                if (password_verify($password, $user['password'])) {
                    if ($user['status'] !== 'active') {
                        $response = ['success' => false, 'message' => 'Compte désactivé. Veuillez contacter l\'administrateur.'];
                    } else {
                        // Start session for PHP backend
                        session_start();
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['user_name'] = $user['nom'];
                        $_SESSION['user_email'] = $user['email'];
                        $_SESSION['user_role'] = $user['role'];
                        
                        $response = [
                            'success' => true,
                            'message' => 'Connexion réussie',
                            'user' => [
                                'id' => $user['id'],
                                'nom' => $user['nom'],
                                'email' => $user['email'],
                                'role' => $user['role'],
                                'telephone' => $user['telephone']
                            ]
                        ];
                    }
                } else {
                    $response = ['success' => false, 'message' => 'Mot de passe incorrect'];
                }
            } else {
                $response = ['success' => false, 'message' => 'Aucun compte trouvé avec cet email'];
            }
            $stmt->close();
            break;
            
        case 'register':
            $nom = $_POST['nom'] ?? '';
            $email = $_POST['email'] ?? '';
            $telephone = $_POST['telephone'] ?? '';
            $password = $_POST['password'] ?? '';
            $role = $_POST['role'] ?? 'patient';
            
            if (empty($nom) || empty($email) || empty($password)) {
                $response = ['success' => false, 'message' => 'Tous les champs sont requis'];
                break;
            }
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $response = ['success' => false, 'message' => 'Email invalide'];
                break;
            }
            
            if (strlen($password) < 6) {
                $response = ['success' => false, 'message' => 'Le mot de passe doit contenir au moins 6 caractères'];
                break;
            }
            
            // Check if email exists
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            if ($stmt->get_result()->num_rows > 0) {
                $response = ['success' => false, 'message' => 'Cet email est déjà utilisé'];
                $stmt->close();
                break;
            }
            $stmt->close();
            
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (nom, email, telephone, password, role, status) VALUES (?, ?, ?, ?, ?, 'active')");
            $stmt->bind_param("sssss", $nom, $email, $telephone, $hashed_password, $role);
            
            if ($stmt->execute()) {
                $response = ['success' => true, 'message' => 'Inscription réussie ! Vous pouvez maintenant vous connecter.'];
            } else {
                $response = ['success' => false, 'message' => 'Erreur lors de l\'inscription: ' . $stmt->error];
            }
            $stmt->close();
            break;
            
        case 'forgot_password':
            $email = $_POST['email'] ?? '';
            $methode = $_POST['methode'] ?? 'email';
            
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $response = ['success' => false, 'message' => 'Email valide requis'];
                break;
            }
            
            // Check if user exists
            $stmt = $conn->prepare("SELECT id, nom FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($user = $result->fetch_assoc()) {
                // Generate unique token
                $token = bin2hex(random_bytes(32));
                $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
                
                // Save token in database
                $update = $conn->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE email = ?");
                $update->bind_param("sss", $token, $expires, $email);
                $update->execute();
                $update->close();
                
                if ($methode === 'email') {
                    // In a real application, send email here
                    // For demo purposes, we'll show the reset link
                    $resetLink = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']) . "/../forgot_password.php?token=" . $token;
                    
                    $response = [
                        'success' => true, 
                        'message' => 'Un lien de réinitialisation a été envoyé à votre email.',
                        'reset_link' => $resetLink // For demo - remove in production
                    ];
                } else {
                    // SMS method - in production, send SMS here
                    $response = [
                        'success' => true, 
                        'message' => 'Un code de réinitialisation a été envoyé par SMS.'
                    ];
                }
            } else {
                $response = ['success' => false, 'message' => 'Aucun compte trouvé avec cet email'];
            }
            $stmt->close();
            break;
            
        case 'verify_token':
            $token = $_POST['token'] ?? '';
            
            if (empty($token)) {
                $response = ['success' => false, 'message' => 'Token manquant'];
                break;
            }
            
            $stmt = $conn->prepare("SELECT email FROM users WHERE reset_token = ? AND reset_expires > NOW()");
            $stmt->bind_param("s", $token);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($user = $result->fetch_assoc()) {
                $response = ['success' => true, 'email' => $user['email']];
            } else {
                $response = ['success' => false, 'message' => 'Lien invalide ou expiré. Veuillez refaire une demande de réinitialisation.'];
            }
            $stmt->close();
            break;
            
        case 'reset_password':
            $token = $_POST['token'] ?? '';
            $new_password = $_POST['new_password'] ?? '';
            
            if (empty($token) || empty($new_password)) {
                $response = ['success' => false, 'message' => 'Token et nouveau mot de passe requis'];
                break;
            }
            
            if (strlen($new_password) < 6) {
                $response = ['success' => false, 'message' => 'Le mot de passe doit contenir au moins 6 caractères'];
                break;
            }
            
            // Verify token is valid
            $stmt = $conn->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_expires > NOW()");
            $stmt->bind_param("s", $token);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $update = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE reset_token = ?");
                $update->bind_param("ss", $hashed_password, $token);
                $update->execute();
                $update->close();
                $response = ['success' => true, 'message' => 'Mot de passe réinitialisé avec succès !'];
            } else {
                $response = ['success' => false, 'message' => 'Token invalide ou expiré'];
            }
            $stmt->close();
            break;
            
        case 'get_appointments':
            $user_id = $_POST['user_id'] ?? 0;
            $role = $_POST['role'] ?? '';
            
            if ($role === 'patient') {
                $stmt = $conn->prepare("
                    SELECT a.*, u.nom as medecin_nom 
                    FROM appointments a 
                    JOIN users u ON a.medecin_id = u.id 
                    WHERE a.patient_id = ? 
                    ORDER BY a.date DESC, a.heure DESC
                ");
                $stmt->bind_param("i", $user_id);
            } elseif ($role === 'medecin') {
                $stmt = $conn->prepare("
                    SELECT a.*, u.nom as patient_nom 
                    FROM appointments a 
                    JOIN users u ON a.patient_id = u.id 
                    WHERE a.medecin_id = ? 
                    ORDER BY a.date DESC, a.heure DESC
                ");
                $stmt->bind_param("i", $user_id);
            } else {
                $stmt = $conn->prepare("
                    SELECT a.*, p.nom as patient_nom, m.nom as medecin_nom 
                    FROM appointments a 
                    JOIN users p ON a.patient_id = p.id 
                    JOIN users m ON a.medecin_id = m.id 
                    ORDER BY a.date DESC
                ");
            }
            
            $stmt->execute();
            $result = $stmt->get_result();
            $appointments = [];
            while ($row = $result->fetch_assoc()) {
                $appointments[] = $row;
            }
            $response = ['success' => true, 'appointments' => $appointments];
            $stmt->close();
            break;
            
        case 'create_appointment':
            $patient_id = $_POST['patient_id'] ?? 0;
            $medecin_id = $_POST['medecin_id'] ?? 0;
            $date = $_POST['date'] ?? '';
            $heure = $_POST['heure'] ?? '';
            $motif = $_POST['motif'] ?? '';
            
            if (empty($date) || empty($heure)) {
                $response = ['success' => false, 'message' => 'Date et heure requises'];
                break;
            }
            
            // Check if slot is available
            $stmt = $conn->prepare("SELECT id FROM appointments WHERE medecin_id = ? AND date = ? AND heure = ? AND status != 'annule'");
            $stmt->bind_param("iss", $medecin_id, $date, $heure);
            $stmt->execute();
            if ($stmt->get_result()->num_rows > 0) {
                $response = ['success' => false, 'message' => 'Ce créneau est déjà pris'];
                $stmt->close();
                break;
            }
            $stmt->close();
            
            $stmt = $conn->prepare("INSERT INTO appointments (patient_id, medecin_id, date, heure, motif, status) VALUES (?, ?, ?, ?, ?, 'en-attente')");
            $stmt->bind_param("iisss", $patient_id, $medecin_id, $date, $heure, $motif);
            
            if ($stmt->execute()) {
                $response = ['success' => true, 'message' => 'Rendez-vous créé avec succès', 'id' => $stmt->insert_id];
            } else {
                $response = ['success' => false, 'message' => 'Erreur lors de la création du rendez-vous'];
            }
            $stmt->close();
            break;
            
        case 'update_appointment_status':
            $appointment_id = $_POST['appointment_id'] ?? 0;
            $status = $_POST['status'] ?? '';
            
            $allowed_status = ['confirme', 'annule', 'termine'];
            if (!in_array($status, $allowed_status)) {
                $response = ['success' => false, 'message' => 'Statut invalide'];
                break;
            }
            
            $stmt = $conn->prepare("UPDATE appointments SET status = ? WHERE id = ?");
            $stmt->bind_param("si", $status, $appointment_id);
            
            if ($stmt->execute()) {
                $response = ['success' => true, 'message' => 'Statut mis à jour avec succès'];
            } else {
                $response = ['success' => false, 'message' => 'Erreur lors de la mise à jour'];
            }
            $stmt->close();
            break;
            
        case 'get_doctors':
            $stmt = $conn->prepare("SELECT id, nom, email, telephone FROM users WHERE role = 'medecin' AND status = 'active' ORDER BY nom");
            $stmt->execute();
            $result = $stmt->get_result();
            $doctors = [];
            while ($row = $result->fetch_assoc()) {
                $doctors[] = $row;
            }
            $response = ['success' => true, 'doctors' => $doctors];
            $stmt->close();
            break;
            
        case 'get_medicaments':
            $stmt = $conn->prepare("SELECT * FROM medicaments ORDER BY nom");
            $stmt->execute();
            $result = $stmt->get_result();
            $medicaments = [];
            while ($row = $result->fetch_assoc()) {
                $medicaments[] = $row;
            }
            $response = ['success' => true, 'medicaments' => $medicaments];
            $stmt->close();
            break;
            
        case 'update_stock':
            $medicament_id = $_POST['medicament_id'] ?? 0;
            $quantity = $_POST['quantity'] ?? 0;
            
            $stmt = $conn->prepare("UPDATE medicaments SET stock = stock - ? WHERE id = ? AND stock >= ?");
            $stmt->bind_param("iii", $quantity, $medicament_id, $quantity);
            
            if ($stmt->execute() && $stmt->affected_rows > 0) {
                $response = ['success' => true, 'message' => 'Stock mis à jour'];
            } else {
                $response = ['success' => false, 'message' => 'Stock insuffisant ou médicament non trouvé'];
            }
            $stmt->close();
            break;
            
        case 'get_prescriptions':
            $patient_id = $_POST['patient_id'] ?? 0;
            
            $stmt = $conn->prepare("
                SELECT p.*, u.nom as medecin_nom 
                FROM prescriptions p 
                JOIN users u ON p.medecin_id = u.id 
                WHERE p.patient_id = ? 
                ORDER BY p.date_prescription DESC
            ");
            $stmt->bind_param("i", $patient_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $prescriptions = [];
            while ($row = $result->fetch_assoc()) {
                $prescriptions[] = $row;
            }
            $response = ['success' => true, 'prescriptions' => $prescriptions];
            $stmt->close();
            break;
            
        default:
            $response = ['success' => false, 'message' => 'Action non reconnue: ' . $action];
            break;
    }
}

echo json_encode($response);
$conn->close();
?>