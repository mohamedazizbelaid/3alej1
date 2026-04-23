-- Create database
CREATE DATABASE IF NOT EXISTS medirendezvous;
USE medirendezvous;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    telephone VARCHAR(20) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('patient', 'medecin', 'admin') DEFAULT 'patient',
    status ENUM('active', 'inactive', 'pending') DEFAULT 'active',
    reset_token VARCHAR(255) NULL,
    reset_expires DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Appointments table
CREATE TABLE IF NOT EXISTS appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    medecin_id INT NOT NULL,
    date DATE NOT NULL,
    heure TIME NOT NULL,
    motif TEXT,
    status ENUM('en-attente', 'confirme', 'termine', 'annule') DEFAULT 'en-attente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES users(id),
    FOREIGN KEY (medecin_id) REFERENCES users(id)
);

-- Medicaments table (inventory)
CREATE TABLE IF NOT EXISTS medicaments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    description TEXT,
    stock INT DEFAULT 0,
    seuil_alerte INT DEFAULT 10,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Prescriptions table
CREATE TABLE IF NOT EXISTS prescriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    medecin_id INT NOT NULL,
    medicament_id INT,
    medicament_nom VARCHAR(100),
    posologie VARCHAR(200),
    date_prescription DATE,
    FOREIGN KEY (patient_id) REFERENCES users(id),
    FOREIGN KEY (medecin_id) REFERENCES users(id)
);

-- Messages table (secure messaging)
CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    subject VARCHAR(200),
    message TEXT,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id),
    FOREIGN KEY (receiver_id) REFERENCES users(id)
);

-- Insert sample data
INSERT INTO users (nom, email, telephone, password, role) VALUES
('Dr. Sophie Martin', 'sophie@cabinet.fr', '0612345678', '$2y$10$YourHashedPasswordHere', 'medecin'),
('Jean Dupont', 'jean@patient.fr', '0698765432', '$2y$10$YourHashedPasswordHere', 'patient'),
('Admin System', 'admin@medirendezvous.com', '0712345678', '$2y$10$YourHashedPasswordHere', 'admin');

INSERT INTO medicaments (nom, description, stock, seuil_alerte) VALUES
('Paracétamol 500mg', 'Antalgique', 150, 20),
('Amoxicilline 1g', 'Antibiotique', 45, 10),
('Ibuprofène 400mg', 'Anti-inflammatoire', 80, 15),
('Ventoline', 'Bronchodilatateur', 30, 5);