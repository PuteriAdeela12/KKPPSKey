<?php
header('Content-Type: application/json');
include "db.php";

// Check if a scan happened in the last 15 seconds for more stability
$stmt = $conn->query("SELECT uid FROM pending_scans WHERE id = 1 AND updated_at > NOW() - INTERVAL 15 SECOND LIMIT 1");
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row && !empty($row['uid'])) {
    $scannedUid = trim($row['uid']);
    $clear = $conn->prepare("UPDATE pending_scans SET uid = '', updated_at = NOW() WHERE id = 1");
    // Clear immediately to prevent accidental double-reads
    
    $clear->execute();
    
    echo json_encode([
        'status' => 'success', 
        'uid' => $scannedUid
    ]);
} else {
    echo json_encode([
        'status' => 'waiting'
    ]);
}
?>

