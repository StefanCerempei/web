<?php
// Conectare la baza de date
$servername = "localhost";
$username = "u708054497_ion";
$password = "Stefan30052006.";
$dbname = "u708054497_topclimat";

// Creează conexiunea
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifică conexiunea
if ($conn->connect_error) {
    die("Conexiunea a eșuat: " . $conn->connect_error);
}
?>
