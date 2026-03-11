<?php
// includes/security.php

class Security {
    
    // Protection CSRF
    public static function generateCSRFToken() {
        if(session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $token;
        $_SESSION['csrf_token_time'] = time();
        return $token;
    }
    
    public static function verifyCSRFToken($token) {
        if(session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if(!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_time'])) {
            return false;
        }
        // Token expire après 1 heure
        if(time() - $_SESSION['csrf_token_time'] > 3600) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }
    
    // Validation et nettoyage des entrées
    public static function sanitizeInput($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        return $data;
    }
    
    // Validation email
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    // Validation téléphone (format français)
    public static function validatePhone($phone) {
        $phone = preg_replace('/\s+/', '', $phone);
        return preg_match('/^0[1-9][0-9]{8}$/', $phone) === 1;
    }
    
    // Validation mot de passe fort
    public static function validatePassword($password) {
        // Au moins 8 caractères, 1 majuscule, 1 minuscule, 1 chiffre
        return strlen($password) >= 8 && 
               preg_match('/[A-Z]/', $password) && 
               preg_match('/[a-z]/', $password) && 
               preg_match('/[0-9]/', $password);
    }
    
    // Protection contre les attaques XSS
    public static function xssProtection() {
        header('X-XSS-Protection: 1; mode=block');
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: DENY');
    }
    
    // Rate limiting (prévention brute force)
    public static function checkRateLimit($key, $maxAttempts = 5, $timeWindow = 900) {
        if(session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $now = time();
        if(!isset($_SESSION['rate_limit'][$key])) {
            $_SESSION['rate_limit'][$key] = [
                'attempts' => 1,
                'first_attempt' => $now
            ];
            return true;
        }
        
        $attempts = $_SESSION['rate_limit'][$key]['attempts'];
        $firstAttempt = $_SESSION['rate_limit'][$key]['first_attempt'];
        
        if($now - $firstAttempt > $timeWindow) {
            // Réinitialiser après la fenêtre de temps
            $_SESSION['rate_limit'][$key] = [
                'attempts' => 1,
                'first_attempt' => $now
            ];
            return true;
        }
        
        if($attempts >= $maxAttempts) {
            return false;
        }
        
        $_SESSION['rate_limit'][$key]['attempts']++;
        return true;
    }
}
?>