<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>KKPPSKey</title>
<style>
body{
    margin:0;
    font-family:Arial, sans-serif;
    background:#f5f7fa;
}
.navbar{
    background:#1e293b;
    color:white;
    padding:14px 20px;
    display:flex;
    justify-content:space-between;
    align-items:center;
}
.navbar a{
    color:white;
    text-decoration:none;
    margin-right:18px;
    font-weight:bold;
}
.navbar a:hover{
    text-decoration:underline;
}
.container{
    padding:20px;
}
.user{
    font-size:14px;
}
</style>
</head>

<body>

<div class="navbar">
    <div>
        <a href="kunci_index.php">🔑 Senarai Kunci</a>
        <a href="#">📋 Senarai Tempahan</a>
        <a href="booking_today.php">• Hari Ini</a>
        <a href="booking_weekly.php">• Minggu Ini</a>
        <a href="booking_monthly.php">• Bulan Ini</a>
    </div>
    <div class="user">
        <?= $_SESSION['full_name'] ?> |
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="container">
