<?php
// tests/test_auth.php
require_once __DIR__ . '/../includes/user_functions.php';
require_once __DIR__ . '/../includes/security.php';

class TestAuth {
    private $userManager;
    private $testsPassed = 0;
    private $testsFailed = 0;
    
    public function __construct() {
        $this->userManager = new UserManager();
    }
    
    public function runAllTests() {
        echo "=== Tests d'authentification ===\n\n";
        
        $this->testEmailValidation();
        $this->testPhoneValidation();
        $this->testPasswordValidation();
        $this->testRegistration();
        $this->testLogin();
        $this->testPasswordReset();
        
        echo "\n=== Résultats ===\n";
        echo "Tests réussis: {$this->testsPassed}\n";
        echo "Tests échoués: {$this->testsFailed}\n";
    }
    
    private function assertTrue($condition, $message) {
        if($condition) {
            echo "✅ $message\n";
            $this->testsPassed++;
        } else {
            echo "❌ $message\n";
            $this->testsFailed++;
        }
    }
    
    private function testEmailValidation() {
        $this->assertTrue(Security::validateEmail('test@example.com'), "Email valide");
        $this->assertTrue(!Security::validateEmail('test@'), "Email invalide sans domaine");
        $this->assertTrue(!Security::validateEmail('test.com'), "Email invalide sans @");
    }
    
    private function testPhoneValidation() {
        $this->assertTrue(Security::validatePhone('0612345678'), "Téléphone valide");
        $this->assertTrue(!Security::validatePhone('123'), "Téléphone trop court");
        $this->assertTrue(!Security::validatePhone('06123456789'), "Téléphone trop long");
    }
    
    private function testPasswordValidation() {
        $this->assertTrue(Security::validatePassword('Password123'), "Mot de passe fort");
        $this->assertTrue(!Security::validatePassword('pass'), "Mot de passe trop court");
        $this->assertTrue(!Security::validatePassword('password'), "Pas de majuscule");
    }
    
    private function testRegistration() {
        // Test avec email déjà existant
        $result = $this->userManager->register('Test', 'test@example.com', '0612345678', 'Password123');
        $this->assertTrue(!$result['success'], "Inscription avec email existant");
    }
    
    private function testLogin() {
        // Test avec mauvais mot de passe
        $result = $this->userManager->login('test@example.com', 'WrongPassword');
        $this->assertTrue(!$result['success'], "Connexion avec mauvais mot de passe");
    }
    
    private function testPasswordReset() {
        $result = $this->userManager->requestPasswordReset('nonexistent@email.com');
        $this->assertTrue(!$result['success'], "Récupération email inexistant");
    }
}

$test = new TestAuth();
$test->runAllTests();
?>