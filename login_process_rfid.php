<?php
session_start();
include "db.php";

if (isset($_GET['uid'])) {
    // Sanitize input
    $uid = trim($_GET['uid']);

    try {
        // Double check: Your table has user_id, username, and card_uid
        $stmt = $conn->prepare("SELECT user_id, username, full_name, role, staff_id FROM users WHERE card_uid = ? LIMIT 1");
        $stmt->execute([$uid]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Success: Set session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name']= $user['full_name'];
            $_SESSION['role']     = $user['role'];
            $_SESSION['staff_id'] = $user['staff_id'];
            
            // Redirect to dashboard
            header("Location: kunci_index.php");
            exit;
        } else {
            // UID not found in database
            header("Location: login.php?error=unregistered");
            exit;
        }
    } catch (PDOException $e) {
        // If there is still a 'column not found' error, this will show it clearly
        die("Database Error: " . $e->getMessage());
    }
} else {
    header("Location: login.php");
    exit;
}
?>