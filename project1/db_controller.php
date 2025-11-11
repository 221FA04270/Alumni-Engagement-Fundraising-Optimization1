<?php
// DB server connection info
$conn_address = "127.0.0.1:3306";
$conn_username = "root";
$conn_password = "";

// Try connecting to the DB server, redirects to maintenance page if fails
try {
    $conn = new mysqli($conn_address, $conn_username, $conn_password);
    $conn->select_db('alumni_portal'); // <-- Your database name
} catch (Exception $e){
    header('Location: maintenance.php');
    die();
}
?>
