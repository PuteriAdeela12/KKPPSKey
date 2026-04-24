<?php
session_start();
include "db.php";

/* =========================
   GET INPUT
========================= */
$login    = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

if ($login === '' || $password === '') {
    header("Location: login.php?error=1");
    exit;
}

/* =========================
   LOGIN VIA:
   - username (users)
   - phone (staff_name)
========================= */
$stmt = $conn->prepare("
    SELECT 
        u.user_id,
        u.username,
        u.full_name,
        u.role,
        s.staff_id,
        s.phone
    FROM users u
    LEFT JOIN staff_name s 
        ON s.staff_name = u.full_name
    WHERE (u.username = ? OR s.phone = ?)
      AND u.password = ?
    LIMIT 1
");
$stmt->execute([$login, $login, $password]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: login.php?error=1");
    exit;
}

/* =========================
   SET SESSION
========================= */

$_SESSION['user_id']  = $user['user_id'];
$_SESSION['username'] = $user['username'];
$_SESSION['role']     = $user['role'];
$_SESSION['staff_id'] = $user['staff_id'] ?? null;

/* ======================
   SUPER ADMIN (BY PHONE)
====================== */
$_SESSION['is_super_admin'] = false;

if ($login === '0138682409') {
    $_SESSION['is_super_admin'] = true;
    $_SESSION['role'] = 'admin'; // force admin
}


/* =========================
   STAFF ID (UNTUK RETURN KEY)
========================= */
if ($user['role'] === 'staff') {
    $_SESSION['staff_id'] = $user['staff_id'] ?? null;
} else {
    $_SESSION['staff_id'] = null;
}

/* =========================
   REDIRECT
========================= */
header("Location: kunci_index.php");
exit;
