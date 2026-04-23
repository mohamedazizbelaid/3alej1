<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediRendez-vous · Tableau de bord</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { background: #f0f5fa; }
        .navbar { background: white; padding: 1rem 2rem; box-shadow: 0 2px 10px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; }
        .logo { font-size: 1.5rem; font-weight: 600; color: #1c3f5c; }
        .logo i { color: #1e7b6f; margin-right: 10px; }
        .user-info { display: flex; align-items: center; gap: 1rem; }
        .avatar { width: 40px; height: 40px; background: #1e7b6f; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; }
        .logout-btn { background: none; border: 1px solid #1e7b6f; color: #1e7b6f; padding: 0.5rem 1.2rem; border-radius: 20px; cursor: pointer; }
        .container { max-width: 1200px; margin: 2rem auto; padding: 0 2rem; }
        .welcome-card { background: white; border-radius: 24px; padding: 2rem; margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
        .stat-card { background: white; border-radius: 20px; padding: 1.5rem; text-align: center; }
        .stat-number { font-size: 2rem; font-weight: 600; color: #1e7b6f; }
        .appointments-card { background: white; border-radius: 24px; padding: 2rem; }
        .appointment-filters { display: flex; gap: 1rem; margin: 1rem 0; }
        .filter-btn { padding: 0.5rem 1.5rem; border-radius: 30px; border: none; cursor: pointer; background: #eef2f5; }
        .filter-btn.active { background: #1e7b6f; color: white; }
        .appointment-item { display: flex; justify-content: space-between; align-items: center; padding: 1rem; border-bottom: 1px solid #edf2f7; }
        .status { padding: 0.25rem 1rem; border-radius: 20px; font-size: 0.8rem; }
        .status-confirme { background: #e1f7ed; color: #13543e; }
        .status-en-attente { background: #fff3e0; color: #b45b0a; }
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); justify-content: center; align-items: center; z-index: 1000; }
        .modal.active { display: flex; }
        .modal-content { background: white; border-radius: 24px; padding: 2rem; max-width: 500px; width: 90%; }
        .input-group { margin-bottom: 1rem; }
        .input-group label { display: block; margin-bottom: 0.5rem; font-weight: 500; }
        .input-field { width: 100%; padding: 0.75rem; border: 1px solid #dee7ef; border-radius: 16px; }
        .btn-primary { background: #1e7b6f; color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 40px; cursor: pointer; width: 100%; }
        .btn-secondary { background: #1c3f5c; color: white; border: none; padding: 0.5rem 1rem; border-radius: 20px; cursor: pointer; }
        .toast { position: fixed; bottom: 20px; right: 20px; background: #1c3f5c; color: white; padding: 12px 24px; border-radius: 40px; z-index: 2000; animation: fadeIn 0.3s; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="logo"><i class="fas fa-notes-medical"></i> MediRendez-vous</div>
        <div class="user-info">
            <div class="avatar" id="userAvatar"></div>
            <span id="userName"></span>
            <button class="logout-btn" onclick="logout()"><i class="fas fa-sign-out-alt"></i> Déconnexion</button>
        </div>
    </nav>

    <div class="container">
        <div class="welcome-card">
            <div><h1 id="welcomeMessage"></h1><p>Voici un résumé de vos rendez-vous</p></div>
            <div class="date-badge" id="currentDate"></div>
        </div>

        <div class="stats-grid">
            <div class="stat-card"><h3>Rendez-vous à venir</h3><div class="stat-number" id="upcomingCount">-</div></div>
            <div class="stat-card"><h3>En attente</h3><div class="stat-number" id="pendingCount">-</div></div>
            <div class="stat-card"><h3>Confirmés</h3><div class="stat-number" id="confirmedCount">-</div></div>
            <div class="stat-card"><h3>Total</h3><div class="stat-number" id="totalCount">-</div></div>
        </div>

        <div class="appointments-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h2><i class="fas fa-calendar-alt"></i> Mes rendez-vous</h2>
                <button class="btn-secondary" onclick="showNewAppointmentModal()"><i class="fas fa-plus"></i> Nouveau RDV</button>
            </div>
            <div class="appointment-filters">
                <button class="filter-btn active" data-filter="all">Tous</button>
                <button class="filter-btn" data-filter="upcoming">À venir</button>
                <button class="filter-btn" data-filter="pending">En attente</button>
                <button class="filter-btn" data-filter="past">Passés</button>
            </div>
            <div id="appointmentsList">Chargement...</div>
        </div>
    </div>

    <div id="modalContainer"></div>

    <script>
        const userId = <?php echo $_SESSION['user_id']; ?>;
        const userRole = '<?php echo $_SESSION['user_role']; ?>';
        const userName = '<?php echo addslashes($_SESSION['user_name']); ?>';
        
        document.getElementById('userName').innerText = userName;
        document.getElementById('userAvatar').innerText = userName.charAt(0);
        document.getElementById('welcomeMessage').innerHTML = `Bonjour, ${userName.split(' ')[0]}`;
        document.getElementById('currentDate').innerText = new Date().toLocaleDateString('fr-FR', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        
        let allAppointments = [];
        
        function showToast(message) {
            const toast = document.createElement('div');
            toast.className = 'toast';
            toast.innerHTML = `<i class="fas fa-info-circle"></i> ${message}`;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        }
        
        async function loadAppointments() {
            const formData = new FormData();
            formData.append('action', 'get_appointments');
            formData.append('user_id', userId);
            formData.append('role', userRole);
            
            const response = await fetch('api/auth.php', { method: 'POST', body: formData });
            const data = await response.json();
            
            if (data.success) {
                allAppointments = data.appointments;
                updateStats();
                applyFilter();
            }
        }
        
        function updateStats() {
            const now = new Date();
            const upcoming = allAppointments.filter(a => new Date(a.date) >= now && a.status !== 'termine' && a.status !== 'annule');
            const pending = allAppointments.filter(a => a.status === 'en-attente');
            const confirmed = allAppointments.filter(a => a.status === 'confirme');
            
            document.getElementById('upcomingCount').innerText = upcoming.length;
            document.getElementById('pendingCount').innerText = pending.length;
            document.getElementById('confirmedCount').innerText = confirmed.length;
            document.getElementById('totalCount').innerText = allAppointments.length;
        }
        
        function applyFilter() {
            const activeFilter = document.querySelector('.filter-btn.active').dataset.filter;
            const now = new Date();
            let filtered = [...allAppointments];
            
            if (activeFilter === 'upcoming') {
                filtered = filtered.filter(a => new Date(a.date) >= now && a.status !== 'termine');
            } else if (activeFilter === 'pending') {
                filtered = filtered.filter(a => a.status === 'en-attente');
            } else if (activeFilter === 'past') {
                filtered = filtered.filter(a => new Date(a.date) < now || a.status === 'termine');
            }
            
            const container = document.getElementById('appointmentsList');
            if (filtered.length === 0) {
                container.innerHTML = '<p style="text-align:center; padding:2rem;">Aucun rendez-vous trouvé</p>';
                return;
            }
            
            container.innerHTML = filtered.map(appt => {
                const doctorName = appt.medecin_nom || 'Médecin';
                const patientName = appt.patient_nom || 'Patient';
                let statusClass = 'status-en-attente';
                if (appt.status === 'confirme') statusClass = 'status-confirme';
                
                return `
                    <div class="appointment-item">
                        <div>
                            <strong>${appt.date} à ${appt.heure}</strong><br>
                            <small>${userRole === 'patient' ? 'Dr. ' + doctorName : 'Patient: ' + patientName}</small><br>
                            <small>${appt.motif || 'Consultation'}</small>
                        </div>
                        <div>
                            <span class="status ${statusClass}">${appt.status}</span>
                            ${userRole === 'medecin' && appt.status === 'en-attente' ? `<button class="btn-secondary" style="margin-left:10px; padding:5px 10px;" onclick="updateStatus(${appt.id}, 'confirme')">Confirmer</button>` : ''}
                            ${userRole === 'patient' && appt.status === 'en-attente' ? `<button class="btn-secondary" style="margin-left:10px; background:#b33c3c;" onclick="updateStatus(${appt.id}, 'annule')">Annuler</button>` : ''}
                        </div>
                    </div>
                `;
            }).join('');
        }
        
        async function updateStatus(appointmentId, status) {
            const formData = new FormData();
            formData.append('action', 'update_appointment_status');
            formData.append('appointment_id', appointmentId);
            formData.append('status', status);
            
            const response = await fetch('api/auth.php', { method: 'POST', body: formData });
            const data = await response.json();
            
            if (data.success) {
                showToast('Rendez-vous ' + (status === 'confirme' ? 'confirmé' : 'annulé'));
                loadAppointments();
            }
        }
        
        async function showNewAppointmentModal() {
            // Get doctors list
            const formData = new FormData();
            formData.append('action', 'get_doctors');
            const response = await fetch('api/auth.php', { method: 'POST', body: formData });
            const data = await response.json();
            
            const modalHtml = `
                <div class="modal active" id="appointmentModal">
                    <div class="modal-content">
                        <h3>Nouveau rendez-vous</h3>
                        <div class="input-group">
                            <label>Médecin</label>
                            <select id="modalMedecinId" class="input-field">
                                ${data.doctors?.map(d => `<option value="${d.id}">${d.nom}</option>`).join('') || '<option>Chargement...</option>'}
                            </select>
                        </div>
                        <div class="input-group">
                            <label>Date</label>
                            <input type="date" id="modalDate" class="input-field" min="${new Date().toISOString().split('T')[0]}">
                        </div>
                        <div class="input-group">
                            <label>Heure</label>
                            <input type="time" id="modalHeure" class="input-field">
                        </div>
                        <div class="input-group">
                            <label>Motif</label>
                            <textarea id="modalMotif" class="input-field" rows="3"></textarea>
                        </div>
                        <button class="btn-primary" onclick="createAppointment()">Prendre rendez-vous</button>
                        <button class="btn-secondary" style="margin-top:10px;" onclick="closeModal()">Annuler</button>
                    </div>
                </div>
            `;
            document.getElementById('modalContainer').innerHTML = modalHtml;
        }
        
        async function createAppointment() {
            const medecinId = document.getElementById('modalMedecinId').value;
            const date = document.getElementById('modalDate').value;
            const heure = document.getElementById('modalHeure').value;
            const motif = document.getElementById('modalMotif').value;
            
            if (!date || !heure) {
                showToast('Veuillez remplir tous les champs');
                return;
            }
            
            const formData = new FormData();
            formData.append('action', 'create_appointment');
            formData.append('patient_id', userId);
            formData.append('medecin_id', medecinId);
            formData.append('date', date);
            formData.append('heure', heure);
            formData.append('motif', motif);
            
            const response = await fetch('api/auth.php', { method: 'POST', body: formData });
            const data = await response.json();
            
            if (data.success) {
                showToast('Rendez-vous créé avec succès');
                closeModal();
                loadAppointments();
            } else {
                showToast('Erreur: ' + data.message);
            }
        }
        
        function closeModal() {
            document.getElementById('modalContainer').innerHTML = '';
        }
        
        function logout() {
            window.location.href = 'logout.php';
        }
        
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                applyFilter();
            });
        });
        
        loadAppointments();
    </script>
</body>
</html>