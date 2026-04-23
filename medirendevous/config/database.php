<?php
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'medirendezvous';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Erreur de connexion à la base de données']));
}

$conn->set_charset("utf8mb4");
?>