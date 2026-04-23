<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediRendez-vous · Connexion</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { min-height: 100vh; background: linear-gradient(135deg, #1c3f5c, #0a2a3a); display: flex; align-items: center; justify-content: center; padding: 1rem; }
        .main-card { background: white; border-radius: 2rem; display: flex; flex-wrap: wrap; max-width: 1000px; width: 100%; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); }
        .brand-panel { flex: 1; background: linear-gradient(135deg, #1c3f5c, #143649); color: white; padding: 2.5rem; }
        .brand-panel h1 { font-size: 2rem; margin-bottom: 1rem; }
        .brand-panel h1 i { color: #5fc1b0; }
        .form-panel { flex: 1.2; padding: 2.5rem; }
        .form-panel h2 { color: #1c3f5c; margin-bottom: 1.5rem; }
        .input-group { margin-bottom: 1rem; }
        .input-group label { display: block; margin-bottom: 0.5rem; font-weight: 500; color: #1c3f5c; }
        .input-field { width: 100%; padding: 0.75rem; border: 1px solid #dee7ef; border-radius: 16px; font-size: 1rem; }
        .btn-primary { width: 100%; background: #1e7b6f; color: white; border: none; padding: 0.75rem; border-radius: 40px; font-size: 1rem; font-weight: 600; cursor: pointer; margin-top: 1rem; }
        .switch-link { text-align: center; margin-top: 1rem; }
        .switch-link a { color: #1e7b6f; text-decoration: none; }
        .error-message { background: #fee; color: #c33; padding: 0.75rem; border-radius: 16px; margin-top: 1rem; text-align: center; }
        .success-message { background: #e1f7ed; color: #13543e; padding: 0.75rem; border-radius: 16px; margin-top: 1rem; text-align: center; }
        @media (max-width: 768px) { .main-card { flex-direction: column; } }
    </style>
</head>
<body>
    <div class="main-card">
        <div class="brand-panel">
            <h1><i class="fas fa-notes-medical"></i> MediRendez-vous</h1>
            <p style="margin-top: 1rem;">Votre santé, simplifiée</p>
            <div style="margin-top: 2rem;">
                <div><i class="fas fa-calendar-check"></i> Prise RDV 24h/24</div>
                <div style="margin-top: 1rem;"><i class="fas fa-user-md"></i> Planning temps réel</div>
                <div style="margin-top: 1rem;"><i class="fas fa-bell"></i> Rappels automatiques</div>
            </div>
        </div>
        <div class="form-panel">
            <div id="loginForm">
                <h2><i class="fas fa-sign-in-alt"></i> Connexion</h2>
                <div class="input-group"><label>Email</label><input type="email" id="loginEmail" class="input-field" placeholder="exemple@cabinet.fr"></div>
                <div class="input-group"><label>Mot de passe</label><input type="password" id="loginPassword" class="input-field" placeholder="••••••"></div>
                <button class="btn-primary" onclick="login()"><i class="fas fa-arrow-right-to-bracket"></i> Se connecter</button>
                <div class="switch-link"><a href="#" onclick="showSignup()">Créer un compte</a> | <a href="#" onclick="showForgot()">Mot de passe oublié</a></div>
                <div id="loginMessage"></div>
            </div>
            
            <div id="signupForm" style="display: none;">
                <h2><i class="fas fa-user-plus"></i> Inscription</h2>
                <div class="input-group"><label>Nom complet</label><input type="text" id="signupName" class="input-field"></div>
                <div class="input-group"><label>Email</label><input type="email" id="signupEmail" class="input-field"></div>
                <div class="input-group"><label>Téléphone</label><input type="tel" id="signupPhone" class="input-field"></div>
                <div class="input-group"><label>Mot de passe</label><input type="password" id="signupPassword" class="input-field"></div>
                <div class="input-group"><label>Rôle</label><select id="signupRole" class="input-field"><option value="patient">Patient</option><option value="medecin">Médecin</option></select></div>
                <button class="btn-primary" onclick="register()"><i class="fas fa-check"></i> S'inscrire</button>
                <div class="switch-link"><a href="#" onclick="showLogin()">Déjà un compte</a></div>
                <div id="signupMessage"></div>
            </div>
            
            <div id="forgotForm" style="display: none;">
                <h2><i class="fas fa-key"></i> Récupération</h2>
                <div class="input-group"><label>Email</label><input type="email" id="forgotEmail" class="input-field"></div>
                <button class="btn-primary" onclick="forgotPassword()"><i class="fas fa-paper-plane"></i> Envoyer</button>
                <div class="switch-link"><a href="#" onclick="showLogin()">Retour à la connexion</a></div>
                <div id="forgotMessage"></div>
            </div>
        </div>
    </div>

    <script>
        async function login() {
            const email = document.getElementById('loginEmail').value;
            const password = document.getElementById('loginPassword').value;
            
            const formData = new FormData();
            formData.append('action', 'login');
            formData.append('email', email);
            formData.append('password', password);
            
            const response = await fetch('api/auth.php', { method: 'POST', body: formData });
            const data = await response.json();
            
            if (data.success) {
                window.location.href = 'dashboard.php';
            } else {
                document.getElementById('loginMessage').innerHTML = `<div class="error-message">${data.message}</div>`;
            }
        }
        
        async function register() {
            const nom = document.getElementById('signupName').value;
            const email = document.getElementById('signupEmail').value;
            const telephone = document.getElementById('signupPhone').value;
            const password = document.getElementById('signupPassword').value;
            const role = document.getElementById('signupRole').value;
            
            const formData = new FormData();
            formData.append('action', 'register');
            formData.append('nom', nom);
            formData.append('email', email);
            formData.append('telephone', telephone);
            formData.append('password', password);
            formData.append('role', role);
            
            const response = await fetch('api/auth.php', { method: 'POST', body: formData });
            const data = await response.json();
            
            if (data.success) {
                document.getElementById('signupMessage').innerHTML = `<div class="success-message">${data.message} Vous pouvez maintenant vous connecter</div>`;
                setTimeout(() => showLogin(), 2000);
            } else {
                document.getElementById('signupMessage').innerHTML = `<div class="error-message">${data.message}</div>`;
            }
        }
        
        async function forgotPassword() {
            const email = document.getElementById('forgotEmail').value;
            
            const formData = new FormData();
            formData.append('action', 'forgot_password');
            formData.append('email', email);
            formData.append('methode', 'email');
            
            const response = await fetch('api/auth.php', { method: 'POST', body: formData });
            const data = await response.json();
            
            if (data.success) {
                document.getElementById('forgotMessage').innerHTML = `<div class="success-message">${data.message}</div>`;
            } else {
                document.getElementById('forgotMessage').innerHTML = `<div class="error-message">${data.message}</div>`;
            }
        }
        
        function showSignup() {
            document.getElementById('loginForm').style.display = 'none';
            document.getElementById('signupForm').style.display = 'block';
            document.getElementById('forgotForm').style.display = 'none';
        }
        
        function showLogin() {
            document.getElementById('loginForm').style.display = 'block';
            document.getElementById('signupForm').style.display = 'none';
            document.getElementById('forgotForm').style.display = 'none';
        }
        
        function showForgot() {
            document.getElementById('loginForm').style.display = 'none';
            document.getElementById('signupForm').style.display = 'none';
            document.getElementById('forgotForm').style.display = 'block';
        }
    </script>
</body>
</html>