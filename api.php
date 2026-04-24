<?php
// api.php
include "db.php";

if (isset($_GET['uid'])) {
    $uid = $_GET['uid'];
    // Update the record to tell the website a new scan happened
    // We use id=1 as a global "last scan" placeholder
    $stmt = $conn->prepare("UPDATE pending_scans SET uid = ?, updated_at = NOW() WHERE id = 1");
    $stmt->execute([$uid]);
    echo "SUCCESS";
}
?>