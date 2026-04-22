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
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(145deg, #e9f0f9 0%, #dbe5f0 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .main-card {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(14px);
            border-radius: 2.5rem;
            box-shadow: 0 25px 50px -10px rgba(0,50,80,0.25);
            width: 100%;
            max-width: 1000px;
            display: flex;
            flex-wrap: wrap;
            overflow: hidden;
        }

        .brand-panel {
            flex: 1 1 280px;
            background: linear-gradient(135deg, #1c3f5c, #143649);
            color: white;
            padding: 2.5rem 2rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 500px;
        }

        .brand-header h1 {
            font-size: 2.2rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .brand-header h1 i {
            color: #5fc1b0;
            margin-right: 8px;
        }

        .brand-header .tagline {
            font-size: 1rem;
            opacity: 0.75;
            border-left: 3px solid #5fc1b0;
            padding-left: 1rem;
            margin-top: 1rem;
        }

        .brand-features {
            margin: 2rem 0;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            margin-bottom: 1.2rem;
            opacity: 0.9;
        }

        .feature-item i {
            font-size: 1.2rem;
            width: 28px;
            color: #7fd9c8;
        }

        .brand-footer {
            font-size: 0.85rem;
            opacity: 0.5;
            display: flex;
            gap: 1rem;
        }

        .form-panel {
            flex: 1.2 1 400px;
            background: white;
            padding: 2.5rem 2.2rem;
        }

        .form-header {
            margin-bottom: 2rem;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 0.8rem;
        }

        .form-header h2 {
            font-size: 1.8rem;
            font-weight: 600;
            color: #1c3f5c;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-header h2 i {
            color: #1e7b6f;
        }

        .form-card {
            display: block;
        }

        .form-card.hidden {
            display: none;
        }

        .input-group {
            margin-bottom: 1.4rem;
        }

        .input-group label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            color: #1f3a4b;
            margin-bottom: 6px;
        }

        .input-group label i {
            color: #1e7b6f;
            width: 18px;
        }

        .input-field {
            width: 100%;
            padding: 0.9rem 1rem;
            border: 1.5px solid #dee7ef;
            border-radius: 18px;
            font-size: 0.95rem;
            transition: 0.15s;
            background: #f9fcff;
        }

        .input-field:focus {
            border-color: #1e7b6f;
            outline: none;
            background: white;
            box-shadow: 0 0 0 4px rgba(30, 123, 111, 0.1);
        }

        .checkbox-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.9rem;
            margin: 1rem 0 1.5rem;
        }

        .checkbox-row label {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #2c4a5e;
        }

        .forgot-link {
            background: none;
            border: none;
            color: #1e7b6f;
            font-weight: 500;
            cursor: pointer;
            text-decoration: underline dotted;
        }

        .btn-primary {
            width: 100%;
            background: #1e3b4f;
            border: none;
            padding: 1rem;
            border-radius: 40px;
            color: white;
            font-weight: 600;
            font-size: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            cursor: pointer;
            transition: 0.2s;
            box-shadow: 0 8px 18px -6px rgba(20, 60, 80, 0.3);
        }

        .btn-primary:hover {
            background: #143649;
            transform: scale(1.01);
        }

        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .btn-link {
            background: none;
            border: none;
            color: #1e7b6f;
            font-weight: 600;
            cursor: pointer;
            text-decoration: underline;
            font-size: 0.95rem;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .info-message {
            background: #f0f9ff;
            padding: 1rem;
            border-radius: 24px;
            font-size: 0.9rem;
            border: 1px solid #cde3f0;
            color: #1f4b6e;
            margin: 1.5rem 0 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-message.error {
            background: #fff0f0;
            border-color: #ffcdd2;
            color: #b33c3c;
        }

        .info-message.success {
            background: #e1f7ed;
            border-color: #c0e6d3;
            color: #13543e;
        }

        .switch-container {
            text-align: center;
            margin-top: 1.5rem;
        }

        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        @media (max-width: 768px) {
            .main-card {
                flex-direction: column;
            }
            .brand-panel {
                min-height: auto;
            }
        }
    </style>
</head>
<body>
    <div class="main-card">
        <!-- Panneau gauche -->
        <div class="brand-panel">
            <div class="brand-header">
                <h1><i class="fas fa-notes-medical"></i> MediRendez-vous</h1>
                <div class="tagline">Votre santé, simplifiée</div>
            </div>
            <div class="brand-features">
                <div class="feature-item"><i class="fas fa-calendar-check"></i> Prise de RDV 24h/24</div>
                <div class="feature-item"><i class="fas fa-user-md"></i> Planning médecins temps réel</div>
                <div class="feature-item"><i class="fas fa-bell"></i> Rappels automatiques</div>
                <div class="feature-item"><i class="fas fa-lock"></i> Données sécurisées</div>
            </div>
            <div class="brand-footer">
                <span><i class="far fa-clock"></i> Application médicale v1.0</span>
            </div>
        </div>

        <!-- Panneau droit -->
        <div class="form-panel">
            <div class="form-header">
                <h2 id="formTitle"><i class="fas fa-sign-in-alt"></i> Connexion</h2>
            </div>

            <!-- Formulaire de connexion -->
            <div id="loginForm" class="form-card">
                <div class="input-group">
                    <label><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" class="input-field" id="loginEmail" placeholder="exemple@cabinet.fr" required>
                </div>
                <div class="input-group">
                    <label><i class="fas fa-lock"></i> Mot de passe</label>
                    <input type="password" class="input-field" id="loginPassword" placeholder="••••••••" required>
                </div>
                <div class="checkbox-row">
                    <label><input type="checkbox" id="rememberMe"> Se souvenir de moi</label>
                    <button class="forgot-link" id="forgotPasswordBtn">Mot de passe oublié ?</button>
                </div>
                <button class="btn-primary" id="doLoginBtn">
                    <i class="fas fa-arrow-right-to-bracket"></i> Connexion
                </button>
                
                <div class="switch-container">
                    <button class="btn-link" id="switchToSignupBtn">
                        <i class="fas fa-user-plus"></i> Créer un compte
                    </button>
                </div>
                
                <div class="info-message" id="loginMessage" style="display: none;"></div>
            </div>

            <!-- Formulaire d'inscription -->
            <div id="signupForm" class="form-card hidden">
                <div class="input-group">
                    <label><i class="fas fa-user"></i> Nom complet</label>
                    <input type="text" class="input-field" id="signupName" placeholder="Dr. Sophie Martin" required>
                </div>
                <div class="input-group">
                    <label><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" class="input-field" id="signupEmail" placeholder="contact@domaine.com" required>
                </div>
                <div class="input-group">
                    <label><i class="fas fa-phone-alt"></i> Téléphone</label>
                    <input type="tel" class="input-field" id="signupPhone" placeholder="06 12 34 56 78" required>
                </div>
                <div class="input-group">
                    <label><i class="fas fa-lock"></i> Mot de passe</label>
                    <input type="password" class="input-field" id="signupPassword" placeholder="Min. 6 caractères" required>
                </div>
                <div class="input-group">
                    <label><i class="fas fa-check-circle"></i> Confirmer</label>
                    <input type="password" class="input-field" id="signupConfirm" placeholder="Identique" required>
                </div>
                <div class="input-group">
                    <label><i class="fas fa-stethoscope"></i> Type de compte</label>
                    <select class="input-field" id="signupRole" required>
                        <option value="patient">Patient</option>
                        <option value="medecin">Médecin</option>
                    </select>
                </div>
                <div class="checkbox-row">
                    <label><input type="checkbox" id="acceptCgu" required> J'accepte les conditions d'utilisation</label>
                </div>
                <button class="btn-primary" id="doSignupBtn">
                    <i class="fas fa-check"></i> S'inscrire
                </button>
                
                <div class="switch-container">
                    <button class="btn-link" id="switchToLoginBtn">
                        <i class="fas fa-arrow-left"></i> Déjà un compte
                    </button>
                </div>
                
                <div class="info-message" id="signupMessage" style="display: none;"></div>
            </div>
        </div>
    </div>

    <script>
        (function() {
            // Éléments DOM
            const loginForm = document.getElementById('loginForm');
            const signupForm = document.getElementById('signupForm');
            const formTitle = document.getElementById('formTitle');
            
            const loginMsg = document.getElementById('loginMessage');
            const signupMsg = document.getElementById('signupMessage');

            const switchToSignup = document.getElementById('switchToSignupBtn');
            const switchToLogin = document.getElementById('switchToLoginBtn');
            const forgotBtn = document.getElementById('forgotPasswordBtn');

            const doLogin = document.getElementById('doLoginBtn');
            const doSignup = document.getElementById('doSignupBtn');

            // Champs
            const loginEmail = document.getElementById('loginEmail');
            const loginPass = document.getElementById('loginPassword');
            const signupName = document.getElementById('signupName');
            const signupEmail = document.getElementById('signupEmail');
            const signupPhone = document.getElementById('signupPhone');
            const signupPass = document.getElementById('signupPassword');
            const signupConfirm = document.getElementById('signupConfirm');
            const signupRole = document.getElementById('signupRole');
            const acceptCgu = document.getElementById('acceptCgu');
            const rememberMe = document.getElementById('rememberMe');

            function showMessage(element, message, isSuccess = false) {
                element.style.display = 'flex';
                element.innerHTML = message;
                element.classList.remove('error', 'success');
                element.classList.add(isSuccess ? 'success' : 'error');
            }

            function showLogin() {
                loginForm.classList.remove('hidden');
                signupForm.classList.add('hidden');
                formTitle.innerHTML = '<i class="fas fa-sign-in-alt"></i> Connexion';
                loginMsg.style.display = 'none';
            }

            function showSignup() {
                loginForm.classList.add('hidden');
                signupForm.classList.remove('hidden');
                formTitle.innerHTML = '<i class="fas fa-user-plus"></i> Créer un compte';
                signupMsg.style.display = 'none';
            }

            // Navigation
            switchToSignup.addEventListener('click', (e) => {
                e.preventDefault();
                showSignup();
            });

            switchToLogin.addEventListener('click', (e) => {
                e.preventDefault();
                showLogin();
            });

            forgotBtn.addEventListener('click', (e) => {
                e.preventDefault();
                window.location.href = 'forgot_password.php';
            });

            // ===== CONNEXION =====
            doLogin.addEventListener('click', async (e) => {
                e.preventDefault();
                
                const email = loginEmail.value.trim();
                const password = loginPass.value.trim();
                
                if (!email || !password) {
                    showMessage(loginMsg, '<i class="fas fa-exclamation-triangle"></i> Veuillez remplir tous les champs');
                    return;
                }

                const originalText = doLogin.innerHTML;
                doLogin.innerHTML = '<span class="loading"></span> Connexion...';
                doLogin.disabled = true;

                try {
                    const formData = new FormData();
                    formData.append('action', 'login');
                    formData.append('email', email);
                    formData.append('password', password);

                    const response = await fetch('api/auth.php', {
                        method: 'POST',
                        body: formData
                    });

                    const data = await response.json();

                    if (data.success) {
                        sessionStorage.setItem('user', JSON.stringify(data.user));
                        
                        if(rememberMe.checked) {
                            localStorage.setItem('savedEmail', email);
                        }
                        
                        showMessage(loginMsg, '<i class="fas fa-circle-check"></i> Connexion réussie ! Redirection...', true);
                        
                        setTimeout(() => {
                            window.location.href = 'dashboard.php';
                        }, 1000);
                    } else {
                        showMessage(loginMsg, '<i class="fas fa-exclamation-triangle"></i> ' + data.message);
                        doLogin.innerHTML = originalText;
                        doLogin.disabled = false;
                    }
                } catch (error) {
                    console.error('Erreur:', error);
                    showMessage(loginMsg, '<i class="fas fa-exclamation-triangle"></i> Erreur de connexion au serveur');
                    doLogin.innerHTML = originalText;
                    doLogin.disabled = false;
                }
            });

            // ===== INSCRIPTION =====
            doSignup.addEventListener('click', async (e) => {
                e.preventDefault();
                
                const name = signupName.value.trim();
                const email = signupEmail.value.trim();
                const phone = signupPhone.value.trim();
                const password = signupPass.value.trim();
                const confirm = signupConfirm.value.trim();
                const role = signupRole.value;

                if (!name || !email || !phone || !password || !confirm) {
                    showMessage(signupMsg, '<i class="fas fa-exclamation-triangle"></i> Tous les champs sont obligatoires');
                    return;
                }

                if (!email.includes('@') || !email.includes('.')) {
                    showMessage(signupMsg, '<i class="fas fa-exclamation-triangle"></i> Email invalide');
                    return;
                }

                const phoneDigits = phone.replace(/\D/g, '');
                if (phoneDigits.length !== 8 && phoneDigits.length !== 10) {
                    showMessage(signupMsg, '<i class="fas fa-exclamation-triangle"></i> Téléphone invalide (8 ou 10 chiffres)');
                    return;
                }

                if (password !== confirm) {
                    showMessage(signupMsg, '<i class="fas fa-times-circle"></i> Les mots de passe ne correspondent pas');
                    return;
                }

                if (password.length < 6) {
                    showMessage(signupMsg, '<i class="fas fa-info-circle"></i> Mot de passe trop court (minimum 6 caractères)');
                    return;
                }

                if (!acceptCgu.checked) {
                    showMessage(signupMsg, '<i class="fas fa-gavel"></i> Vous devez accepter les conditions');
                    return;
                }

                const originalText = doSignup.innerHTML;
                doSignup.innerHTML = '<span class="loading"></span> Inscription...';
                doSignup.disabled = true;

                try {
                    const formData = new FormData();
                    formData.append('action', 'register');
                    formData.append('nom', name);
                    formData.append('email', email);
                    formData.append('telephone', phone);
                    formData.append('password', password);
                    formData.append('role', role);

                    const response = await fetch('api/auth.php', {
                        method: 'POST',
                        body: formData
                    });

                    const data = await response.json();

                    if (data.success) {
                        showMessage(signupMsg, '<i class="fas fa-check-circle"></i> ' + data.message, true);
                        
                        setTimeout(() => {
                            showLogin();
                            loginEmail.value = email;
                        }, 2000);
                    } else {
                        showMessage(signupMsg, '<i class="fas fa-exclamation-triangle"></i> ' + data.message);
                        doSignup.innerHTML = originalText;
                        doSignup.disabled = false;
                    }
                } catch (error) {
                    console.error('Erreur:', error);
                    showMessage(signupMsg, '<i class="fas fa-exclamation-triangle"></i> Erreur de connexion au serveur');
                    doSignup.innerHTML = originalText;
                    doSignup.disabled = false;
                }
            });

            // Charger email sauvegardé
            const savedEmail = localStorage.getItem('savedEmail');
            if (savedEmail) {
                loginEmail.value = savedEmail;
                rememberMe.checked = true;
            }

            // Vérifier si déjà connecté via sessionStorage
            if (sessionStorage.getItem('user')) {
                window.location.href = 'dashboard.php';
            }

            // Initialisation
            showLogin();
        })();
    </script>
</body>
</html>