<?php
// includes/user_functions.php
require_once 'config/database.php';

class UserManager {
    private $conn;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    // Inscription d'un nouvel utilisateur
    public function register($nom, $email, $telephone, $password, $role = 'patient') {
        try {
            // Vérifier si l'email existe déjà
            $checkQuery = "SELECT id FROM users WHERE email = :email";
            $checkStmt = $this->conn->prepare($checkQuery);
            $checkStmt->bindParam(':email', $email);
            $checkStmt->execute();
            
            if($checkStmt->rowCount() > 0) {
                return ['success' => false, 'message' => 'Cet email est déjà utilisé'];
            }
            
            // Hasher le mot de passe
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            // Insérer l'utilisateur
            $query = "INSERT INTO users (nom_complet, email, telephone, mot_de_passe, role) 
                      VALUES (:nom, :email, :telephone, :password, :role)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':telephone', $telephone);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':role', $role);
            
            if($stmt->execute()) {
                $userId = $this->conn->lastInsertId();
                
                // Si c'est un médecin, créer aussi une entrée dans la table medecins
                if($role === 'medecin') {
                    $medQuery = "INSERT INTO medecins (user_id) VALUES (:user_id)";
                    $medStmt = $this->conn->prepare($medQuery);
                    $medStmt->bindParam(':user_id', $userId);
                    $medStmt->execute();
                }
                
                return ['success' => true, 'message' => 'Inscription réussie', 'user_id' => $userId];
            }
            
            return ['success' => false, 'message' => 'Erreur lors de l\'inscription'];
            
        } catch(PDOException $e) {
            return ['success' => false, 'message' => 'Erreur: ' . $e->getMessage()];
        }
    }
    
    // Connexion utilisateur
    public function login($email, $password) {
        try {
            $query = "SELECT * FROM users WHERE email = :email AND est_actif = 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            if($stmt->rowCount() == 1) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if(password_verify($password, $user['mot_de_passe'])) {
                    // Mettre à jour la dernière connexion
                    $updateQuery = "UPDATE users SET derniere_connexion = NOW() WHERE id = :id";
                    $updateStmt = $this->conn->prepare($updateQuery);
                    $updateStmt->bindParam(':id', $user['id']);
                    $updateStmt->execute();
                    
                    // Démarrer la session
                    session_start();
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_nom'] = $user['nom_complet'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_role'] = $user['role'];
                    
                    return ['success' => true, 'message' => 'Connexion réussie', 'user' => $user];
                }
            }
            
            return ['success' => false, 'message' => 'Email ou mot de passe incorrect'];
            
        } catch(PDOException $e) {
            return ['success' => false, 'message' => 'Erreur: ' . $e->getMessage()];
        }
    }
    
    // Demande de récupération de mot de passe
    public function requestPasswordReset($email, $methode = 'email') {
        try {
            // Vérifier si l'utilisateur existe
            $query = "SELECT id, nom_complet, telephone FROM users WHERE email = :email";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            if($stmt->rowCount() == 0) {
                return ['success' => false, 'message' => 'Aucun compte trouvé avec cet email'];
            }
            
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Générer un token unique
            $token = bin2hex(random_bytes(32));
            $code_sms = null;
            $contact = $email;
            
            if($methode === 'sms') {
                // Générer un code à 6 chiffres pour SMS
                $code_sms = sprintf("%06d", mt_rand(1, 999999));
                $contact = $user['telephone'];
            }
            
            // Date d'expiration (1 heure)
            $expiration = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Supprimer les anciens tokens pour cet email
            $deleteQuery = "DELETE FROM password_resets WHERE email = :email";
            $deleteStmt = $this->conn->prepare($deleteQuery);
            $deleteStmt->bindParam(':email', $email);
            $deleteStmt->execute();
            
            // Insérer le nouveau token
            $insertQuery = "INSERT INTO password_resets (email, token, code_sms, methode, date_expiration) 
                           VALUES (:email, :token, :code_sms, :methode, :expiration)";
            $insertStmt = $this->conn->prepare($insertQuery);
            $insertStmt->bindParam(':email', $email);
            $insertStmt->bindParam(':token', $token);
            $insertStmt->bindParam(':code_sms', $code_sms);
            $insertStmt->bindParam(':methode', $methode);
            $insertStmt->bindParam(':expiration', $expiration);
            $insertStmt->execute();
            
            // Ici, vous enverriez un email ou un SMS
            // Pour la démo, on retourne les infos
            return [
                'success' => true, 
                'message' => 'Instructions envoyées', 
                'methode' => $methode,
                'contact' => $contact,
                'token' => $token,
                'code_sms' => $code_sms
            ];
            
        } catch(PDOException $e) {
            return ['success' => false, 'message' => 'Erreur: ' . $e->getMessage()];
        }
    }
    
    // Vérifier le token de récupération
    public function verifyResetToken($token, $code_sms = null) {
        try {
            $query = "SELECT * FROM password_resets WHERE token = :token AND utilise = 0 AND date_expiration > NOW()";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':token', $token);
            $stmt->execute();
            
            if($stmt->rowCount() == 0) {
                return ['success' => false, 'message' => 'Token invalide ou expiré'];
            }
            
            $reset = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Si c'est une récupération par SMS, vérifier le code
            if($reset['methode'] === 'sms' && $code_sms != $reset['code_sms']) {
                return ['success' => false, 'message' => 'Code SMS incorrect'];
            }
            
            return ['success' => true, 'message' => 'Token valide', 'email' => $reset['email']];
            
        } catch(PDOException $e) {
            return ['success' => false, 'message' => 'Erreur: ' . $e->getMessage()];
        }
    }
    
    // Réinitialiser le mot de passe
    public function resetPassword($token, $new_password) {
        try {
            // Vérifier le token
            $verify = $this->verifyResetToken($token);
            
            if(!$verify['success']) {
                return $verify;
            }
            
            $email = $verify['email'];
            
            // Hasher le nouveau mot de passe
            $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);
            
            // Mettre à jour le mot de passe
            $updateQuery = "UPDATE users SET mot_de_passe = :password WHERE email = :email";
            $updateStmt = $this->conn->prepare($updateQuery);
            $updateStmt->bindParam(':password', $hashedPassword);
            $updateStmt->bindParam(':email', $email);
            $updateStmt->execute();
            
            // Marquer le token comme utilisé
            $tokenQuery = "UPDATE password_resets SET utilise = 1 WHERE token = :token";
            $tokenStmt = $this->conn->prepare($tokenQuery);
            $tokenStmt->bindParam(':token', $token);
            $tokenStmt->execute();
            
            return ['success' => true, 'message' => 'Mot de passe réinitialisé avec succès'];
            
        } catch(PDOException $e) {
            return ['success' => false, 'message' => 'Erreur: ' . $e->getMessage()];
        }
    }
    
    // Déconnexion
    public function logout() {
        session_start();
        session_destroy();
        return ['success' => true, 'message' => 'Déconnexion réussie'];
    }
}
?>