<?php
// Database configuration (procedural, no PDO)
$host = 'localhost';
$db   = 'Nume_baza_de_date';
$user = 'Utilizator_baza_de_date';
$pass = 'Parola_baza_de_date';
$n8n_webhook_url = 'URL_DIN_WEBHOOK'; //modifică URL-ul din test în Live dacă treci de la Test Workflow în Live Workflow / producție 

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die('Connection failed: ' . mysqli_connect_error());
}
?>
