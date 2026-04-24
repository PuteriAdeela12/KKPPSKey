<?php
$host = "localhost"; // Use localhost for XAMPP
$db   = "if0_40933067_kkppskey"; 
$user = "root";       // Default XAMPP username
$pass = "";           // Default XAMPP password is empty

try {
    $conn = new PDO(
        "mysql:host=$host;dbname=$db;charset=utf8",
        $user,
        $pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    $conn->exec("SET time_zone = '+08:00'");
} catch (PDOException $e) {
    die("DB Error: " . $e->getMessage());
}
?>