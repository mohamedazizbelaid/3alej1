<?php
session_start();
// If already logged in, redirect to dashboard
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
    <title>MediRendez-vous · Récupération de mot de passe</title>
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

        .form-header p {
            color: #4a6a7f;
            margin-top: 0.5rem;
            font-size: 0.95rem;
        }

        .recovery-options {
            display: flex;
            flex-direction: column;
            gap: 1.2rem;
            margin-bottom: 2rem;
        }

        .recovery-option {
            background: #f8fafd;
            border: 2px solid #dee7ef;
            border-radius: 24px;
            padding: 1.5rem;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: flex-start;
            gap: 1.2rem;
        }

        .recovery-option:hover {
            border-color: #1e7b6f;
            background: #f0f7fa;
        }

        .recovery-option.selected {
            border-color: #1e7b6f;
            background: #e3f2ed;
            box-shadow: 0 0 0 4px rgba(30, 123, 111, 0.15);
        }

        .option-radio {
            margin-top: 0.3rem;
        }

        .option-radio input[type="radio"] {
            width: 20px;
            height: 20px;
            accent-color: #1e7b6f;
            cursor: pointer;
        }

        .option-content {
            flex: 1;
        }

        .option-content h3 {
            font-size: 1.2rem;
            font-weight: 600;
            color: #1c3f5c;
            margin-bottom: 0.3rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .option-content h3 i {
            color: #1e7b6f;
        }

        .option-content p {
            color: #4f6f87;
            font-size: 0.9rem;
            margin-bottom: 0.8rem;
        }

        .option-detail {
            margin-top: 0.5rem;
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

        .btn-secondary {
            width: 100%;
            background: #6c757d;
            border: none;
            padding: 0.8rem;
            border-radius: 40px;
            color: white;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            margin-top: 0.5rem;
        }

        .btn-link {
            background: none;
            border: none;
            color: #1e7b6f;
            font-weight: 600;
            cursor: pointer;
            text-decoration: underline;
            font-size: 0.95rem;
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

        .back-to-login {
            text-align: center;
            margin-top: 1.5rem;
        }

        .back-to-login a {
            color: #1e7b6f;
            text-decoration: none;
            font-weight: 500;
        }

        .confirmation-screen {
            text-align: center;
            padding: 1rem 0;
        }

        .confirmation-screen i {
            font-size: 4rem;
            color: #1e7b6f;
            margin-bottom: 1rem;
        }

        .confirmation-screen h3 {
            font-size: 1.5rem;
            color: #1c3f5c;
            margin-bottom: 0.5rem;
        }

        .confirmation-screen p {
            color: #4f6f87;
            margin-bottom: 1.5rem;
        }

        .hidden {
            display: none;
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

        @media (max-width: 680px) {
            .main-card {
                flex-direction: column;
            }
            .brand-panel {
                min-height: auto;
                padding: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="main-card">
        <div class="brand-panel">
            <div class="brand-header">
                <h1><i class="fas fa-notes-medical"></i> MediRendez-vous</h1>
                <div class="tagline">Récupération de mot de passe</div>
            </div>
            <div class="brand-features">
                <div class="feature-item"><i class="fas fa-shield-alt"></i> Processus sécurisé</div>
                <div class="feature-item"><i class="fas fa-clock"></i> Lien valable 1 heure</div>
                <div class="feature-item"><i class="fas fa-sms"></i> Code par SMS possible</div>
            </div>
            <div class="brand-footer">
                <span>Support 24/7</span>
            </div>
        </div>

        <div class="form-panel">
            <div class="form-header">
                <h2><i class="fas fa-key"></i> Récupération</h2>
                <p>Choisissez votre méthode de récupération</p>
            </div>

            <!-- Écran de choix -->
            <div id="choiceScreen">
                <div class="recovery-options">
                    <!-- Option Email -->
                    <div class="recovery-option" id="optionEmail">
                        <div class="option-radio">
                            <input type="radio" name="recoveryMethod" id="radioEmail" value="email" checked>
                        </div>
                        <div class="option-content">
                            <h3><i class="fas fa-envelope"></i> Par email</h3>
                            <p>Recevez un lien de réinitialisation</p>
                            <div class="option-detail" id="emailDetail">
                                <div class="input-group">
                                    <label>Votre email</label>
                                    <input type="email" class="input-field" id="emailInput" placeholder="exemple@cabinet.fr" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Option SMS -->
                    <div class="recovery-option" id="optionSms">
                        <div class="option-radio">
                            <input type="radio" name="recoveryMethod" id="radioSms" value="sms">
                        </div>
                        <div class="option-content">
                            <h3><i class="fas fa-phone-alt"></i> Par SMS</h3>
                            <p>Recevez un code à 6 chiffres</p>
                            <div class="option-detail hidden" id="smsDetail">
                                <div class="input-group">
                                    <label>Numéro de téléphone</label>
                                    <input type="tel" class="input-field" id="phoneInput" placeholder="06 12 34 56 78" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <button class="btn-primary" id="continueBtn">
                    <i class="fas fa-arrow-right"></i> Continuer
                </button>
                
                <div class="back-to-login">
                    <a href="login.php">← Retour à la connexion</a>
                </div>

                <div class="info-message" id="choiceMessage" style="display: none;"></div>
            </div>

            <!-- Écran de confirmation -->
            <div id="confirmationScreen" class="hidden">
                <div class="confirmation-screen">
                    <i class="fas fa-paper-plane" id="confirmationIcon"></i>
                    <h3 id="confirmationTitle">Vérifiez votre email</h3>
                    <p id="confirmationText">Un lien de réinitialisation a été envoyé</p>
                    <button class="btn-primary" id="resendBtn">
                        <i class="fas fa-redo"></i> Renvoyer
                    </button>
                    <div class="back-to-login">
                        <a href="login.php">← Retour à la connexion</a>
                    </div>
                </div>
                <div class="info-message" id="confirmationMessage" style="display: none;"></div>
            </div>

            <!-- Écran de réinitialisation (quand on clique sur le token) -->
            <div id="resetScreen" class="hidden">
                <div class="input-group">
                    <label><i class="fas fa-lock"></i> Nouveau mot de passe</label>
                    <input type="password" class="input-field" id="newPassword" placeholder="Min. 6 caractères" required>
                </div>
                <div class="input-group">
                    <label><i class="fas fa-check-circle"></i> Confirmer le mot de passe</label>
                    <input type="password" class="input-field" id="confirmPassword" placeholder="Identique" required>
                </div>
                <button class="btn-primary" id="resetPasswordBtn">
                    <i class="fas fa-save"></i> Réinitialiser
                </button>
                <div class="back-to-login">
                    <a href="login.php">← Retour à la connexion</a>
                </div>
                <div class="info-message" id="resetMessage" style="display: none;"></div>
            </div>
        </div>
    </div>

    <script>
        (function() {
            const choiceScreen = document.getElementById('choiceScreen');
            const confirmationScreen = document.getElementById('confirmationScreen');
            const resetScreen = document.getElementById('resetScreen');
            
            const optionEmail = document.getElementById('optionEmail');
            const optionSms = document.getElementById('optionSms');
            const radioEmail = document.getElementById('radioEmail');
            const radioSms = document.getElementById('radioSms');
            const emailDetail = document.getElementById('emailDetail');
            const smsDetail = document.getElementById('smsDetail');
            
            const emailInput = document.getElementById('emailInput');
            const phoneInput = document.getElementById('phoneInput');
            
            const continueBtn = document.getElementById('continueBtn');
            const resendBtn = document.getElementById('resendBtn');
            const resetPasswordBtn = document.getElementById('resetPasswordBtn');
            
            const choiceMessage = document.getElementById('choiceMessage');
            const resetMessage = document.getElementById('resetMessage');
            const confirmationMessage = document.getElementById('confirmationMessage');
            const confirmationIcon = document.getElementById('confirmationIcon');
            const confirmationTitle = document.getElementById('confirmationTitle');
            const confirmationText = document.getElementById('confirmationText');

            const newPassword = document.getElementById('newPassword');
            const confirmPassword = document.getElementById('confirmPassword');

            let selectedMethod = 'email';
            let currentEmail = null;
            let currentToken = null;

            // Vérifier s'il y a un token dans l'URL (quand l'utilisateur clique sur le lien email)
            const urlParams = new URLSearchParams(window.location.search);
            const token = urlParams.get('token');
            
            if (token) {
                currentToken = token;
                verifyToken(token);
            }

            function showMessage(element, message, isSuccess = false) {
                element.style.display = 'flex';
                element.innerHTML = message;
                element.classList.remove('error', 'success');
                element.classList.add(isSuccess ? 'success' : 'error');
            }

            function showChoiceScreen() {
                choiceScreen.classList.remove('hidden');
                confirmationScreen.classList.add('hidden');
                resetScreen.classList.add('hidden');
                choiceMessage.style.display = 'none';
            }

            function showConfirmationScreen(method, contact) {
                choiceScreen.classList.add('hidden');
                confirmationScreen.classList.remove('hidden');
                resetScreen.classList.add('hidden');
                
                if (method === 'email') {
                    confirmationIcon.className = 'fas fa-envelope-open-text';
                    confirmationTitle.textContent = 'Vérifiez votre email';
                    confirmationText.innerHTML = `Lien envoyé à <strong>${contact}</strong>`;
                } else {
                    confirmationIcon.className = 'fas fa-sms';
                    confirmationTitle.textContent = 'Code envoyé par SMS';
                    confirmationText.innerHTML = `Code envoyé au <strong>${contact}</strong>`;
                }
            }

            function showResetScreen() {
                choiceScreen.classList.add('hidden');
                confirmationScreen.classList.add('hidden');
                resetScreen.classList.remove('hidden');
            }

            function selectOption(method) {
                selectedMethod = method;
                radioEmail.checked = (method === 'email');
                radioSms.checked = (method === 'sms');
                
                if (method === 'email') {
                    optionEmail.classList.add('selected');
                    optionSms.classList.remove('selected');
                    emailDetail.classList.remove('hidden');
                    smsDetail.classList.add('hidden');
                } else {
                    optionEmail.classList.remove('selected');
                    optionSms.classList.add('selected');
                    emailDetail.classList.add('hidden');
                    smsDetail.classList.remove('hidden');
                }
            }

            // Vérifier le token reçu par email
            async function verifyToken(token) {
                try {
                    const formData = new FormData();
                    formData.append('action', 'verify_token');
                    formData.append('token', token);

                    const response = await fetch('api/auth.php', {
                        method: 'POST',
                        body: formData
                    });

                    const data = await response.json();

                    if (data.success) {
                        currentEmail = data.email;
                        showResetScreen();
                    } else {
                        showMessage(resetMessage, '<i class="fas fa-exclamation-triangle"></i> ' + data.message, false);
                        setTimeout(() => {
                            window.location.href = 'forgot_password.php';
                        }, 3000);
                    }
                } catch (error) {
                    console.error('Erreur:', error);
                    alert('Erreur de vérification du token');
                    window.location.href = 'forgot_password.php';
                }
            }

            // Demande de récupération
            async function requestRecovery() {
                let contact = '';
                let email = '';
                
                if (selectedMethod === 'email') {
                    contact = emailInput.value.trim();
                    email = contact;
                    if (!contact || !contact.includes('@')) {
                        showMessage(choiceMessage, '<i class="fas fa-exclamation-triangle"></i> Email valide requis');
                        return false;
                    }
                } else {
                    contact = phoneInput.value.trim();
                    const phoneDigits = contact.replace(/\D/g, '');
                    if (phoneDigits.length !== 8 && phoneDigits.length !== 10) {
                        showMessage(choiceMessage, '<i class="fas fa-exclamation-triangle"></i> Téléphone invalide (8 ou 10 chiffres)');
                        return false;
                    }
                    // Pour SMS, demander l'email associé
                    email = prompt("Pour la récupération par SMS, veuillez entrer votre email associé au compte :");
                    if (!email || !email.includes('@')) {
                        showMessage(choiceMessage, '<i class="fas fa-exclamation-triangle"></i> Email requis');
                        return false;
                    }
                }

                // Afficher chargement
                const originalText = continueBtn.innerHTML;
                continueBtn.innerHTML = '<span class="loading"></span> Envoi...';
                continueBtn.disabled = true;

                try {
                    const formData = new FormData();
                    formData.append('action', 'forgot_password');
                    formData.append('email', email);
                    formData.append('methode', selectedMethod);

                    const response = await fetch('api/auth.php', {
                        method: 'POST',
                        body: formData
                    });

                    const data = await response.json();

                    if (data.success) {
                        currentEmail = email;
                        showConfirmationScreen(selectedMethod, contact);
                        return true;
                    } else {
                        showMessage(choiceMessage, '<i class="fas fa-exclamation-triangle"></i> ' + data.message);
                        return false;
                    }
                } catch (error) {
                    console.error('Erreur:', error);
                    showMessage(choiceMessage, '<i class="fas fa-exclamation-triangle"></i> Erreur de connexion au serveur');
                    return false;
                } finally {
                    continueBtn.innerHTML = originalText;
                    continueBtn.disabled = false;
                }
            }

            // Renvoyer le lien/code
            async function resendRecovery() {
                const originalText = resendBtn.innerHTML;
                resendBtn.innerHTML = '<span class="loading"></span> Renvoi...';
                resendBtn.disabled = true;

                try {
                    const formData = new FormData();
                    formData.append('action', 'forgot_password');
                    formData.append('email', currentEmail);
                    formData.append('methode', selectedMethod);

                    const response = await fetch('api/auth.php', {
                        method: 'POST',
                        body: formData
                    });

                    const data = await response.json();

                    if (data.success) {
                        showMessage(confirmationMessage, '<i class="fas fa-check-circle"></i> Nouveau message envoyé !', true);
                        setTimeout(() => {
                            confirmationMessage.style.display = 'none';
                        }, 3000);
                    } else {
                        showMessage(confirmationMessage, '<i class="fas fa-exclamation-triangle"></i> ' + data.message);
                    }
                } catch (error) {
                    console.error('Erreur:', error);
                    showMessage(confirmationMessage, '<i class="fas fa-exclamation-triangle"></i> Erreur de connexion');
                } finally {
                    resendBtn.innerHTML = originalText;
                    resendBtn.disabled = false;
                }
            }

            // Réinitialisation du mot de passe
            async function resetPassword() {
                const password = newPassword.value.trim();
                const confirm = confirmPassword.value.trim();

                if (!password || !confirm) {
                    showMessage(resetMessage, '<i class="fas fa-exclamation-triangle"></i> Remplissez tous les champs');
                    return;
                }

                if (password !== confirm) {
                    showMessage(resetMessage, '<i class="fas fa-times-circle"></i> Les mots de passe ne correspondent pas');
                    return;
                }

                if (password.length < 6) {
                    showMessage(resetMessage, '<i class="fas fa-info-circle"></i> Minimum 6 caractères');
                    return;
                }

                const originalText = resetPasswordBtn.innerHTML;
                resetPasswordBtn.innerHTML = '<span class="loading"></span> Réinitialisation...';
                resetPasswordBtn.disabled = true;

                try {
                    const formData = new FormData();
                    formData.append('action', 'reset_password');
                    formData.append('token', currentToken);
                    formData.append('new_password', password);

                    const response = await fetch('api/auth.php', {
                        method: 'POST',
                        body: formData
                    });

                    const data = await response.json();

                    if (data.success) {
                        showMessage(resetMessage, '<i class="fas fa-check-circle"></i> Mot de passe réinitialisé ! Redirection vers la connexion...', true);
                        
                        setTimeout(() => {
                            window.location.href = 'login.php';
                        }, 2000);
                    } else {
                        showMessage(resetMessage, '<i class="fas fa-exclamation-triangle"></i> ' + data.message);
                    }
                } catch (error) {
                    console.error('Erreur:', error);
                    showMessage(resetMessage, '<i class="fas fa-exclamation-triangle"></i> Erreur de connexion');
                } finally {
                    resetPasswordBtn.innerHTML = originalText;
                    resetPasswordBtn.disabled = false;
                }
            }

            // Event Listeners
            optionEmail.addEventListener('click', (e) => {
                if (e.target.tagName !== 'INPUT') selectOption('email');
            });

            optionSms.addEventListener('click', (e) => {
                if (e.target.tagName !== 'INPUT') selectOption('sms');
            });

            radioEmail.addEventListener('change', () => {
                if (radioEmail.checked) selectOption('email');
            });

            radioSms.addEventListener('change', () => {
                if (radioSms.checked) selectOption('sms');
            });

            continueBtn.addEventListener('click', (e) => {
                e.preventDefault();
                requestRecovery();
            });

            resendBtn.addEventListener('click', (e) => {
                e.preventDefault();
                resendRecovery();
            });

            resetPasswordBtn.addEventListener('click', (e) => {
                e.preventDefault();
                resetPassword();
            });

            // Allow Enter key on password fields
            newPassword.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') resetPassword();
            });
            confirmPassword.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') resetPassword();
            });

            // Initialisation
            selectOption('email');
        })();
    </script>
</body>
</html>