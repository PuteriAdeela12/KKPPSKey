<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "db.php";

/* =========================
   GET POST DATA
========================= */
$room_id       = (int)($_POST['room_id'] ?? 0);
$booking_type  = $_POST['booking_type'] ?? '';
$booking_date  = $_POST['booking_date'] ?? '';
$start_time    = $_POST['start_time'] ?? null;
$end_time      = $_POST['end_time'] ?? null;
$purpose       = trim($_POST['purpose'] ?? '');

// User info from form
$borrower_name = trim($_POST['borrower_name'] ?? 'Unknown');
$phone         = trim($_POST['phone'] ?? '');

// UID data captured from the two-step scan process
$student_uid   = $_POST['student_uid'] ?? 'N/A'; // Card Scan
$key_uid       = $_POST['key_uid'] ?? 'N/A';     // Key Scan

if ($room_id === 0 || !$booking_date || !$booking_type) {
    die("Data tidak lengkap. Sila cuba lagi.");
}

/* =========================
   GET ROOM/KEY INFO
========================= */
$stmt = $conn->prepare("SELECT key_code, room_name FROM key_list WHERE key_id=?");
$stmt->execute([$room_id]);
$key = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$key) die("Kunci tidak dijumpai dalam sistem.");

$key_code  = $key['key_code'];
$room_name = $key['room_name'];

/* =========================
   IDENTIFY STAFF_ID (INTERNAL TRACKING)
========================= */
$stmt = $conn->prepare("SELECT staff_id FROM staff_name WHERE card_uid = ? LIMIT 1");
$stmt->execute([$student_uid]);
$staff = $stmt->fetch(PDO::FETCH_ASSOC);
$staff_id = $staff ? $staff['staff_id'] : null;
$borrower_type = $staff ? 'staff' : 'others';

/* =========================
   TIME CALCULATION
========================= */
if ($booking_type === 'whole_day') {
    $start_time = '08:00:00';
    $end_time   = '17:00:00';
} else {
    // Ensure format is HH:MM:SS
    $start_time = $start_time . ":00";
    $end_time   = $end_time . ":00";
}

if (!$start_time || !$end_time || $start_time >= $end_time) {
    die("Masa tempahan tidak sah.");
}

$end_datetime = $booking_date . ' ' . $end_time;

/* =========================
   INSERT INTO DATABASE
========================= */
// Added card_uid and key_uid to the columns and values
$stmt = $conn->prepare("
INSERT INTO bookings (
    room_id, key_code, room_name,
    borrower_type, staff_id, borrower_name, phone, 
    card_uid, key_uid,
    booking_type, booking_date, start_time, end_time,
    end_datetime, purpose, status, overdue_notified
) VALUES (
    ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?, 'BOOKED', 0
)");

$result = $stmt->execute([
    $room_id,
    $key_code,
    $room_name,
    $borrower_type,
    $staff_id,
    $borrower_name,
    $phone,
    $student_uid, // Matches card_uid column
    $key_uid,     // Matches key_uid column
    $booking_type,
    $booking_date,
    $start_time,
    $end_time,
    $end_datetime,
    $purpose
]);

/* =========================
   TELEGRAM NOTIFICATION
========================= */
if ($result) {
    $botToken = "8721861065:AAGa60R22Yj0SMoF_kPhGGFBwxk6kdb9VX4";
    $chatID   = "1006171429";

    // Format message
    $message = "🔑 *NEW KEY BOOKING CONFIRMED*\n";
    $message .= "------------------------------------------\n";
    $message .= "📍 *Room:* " . $room_name . " (" . $key_code . ")\n";
    $message .= "👤 *Borrower:* " . $borrower_name . "\n";
    $message .= "📞 *Phone:* " . $phone . "\n";
    $message .= "📅 *Date:* " . $booking_date . "\n";
    $message .= "⏰ *Time:* " . $start_time . " - " . $end_time . "\n";
    $message .= "📝 *Purpose:* " . $purpose . "\n";
    $message .= "------------------------------------------\n";
    $message .= "🆔 *Card UID:* `" . $student_uid . "`\n";
    $message .= "🔑 *Key UID:* `" . $key_uid . "`\n";
    $message .= "✅ *Status:* Key Handed Over";

    $url = "https://api.telegram.org/bot$botToken/sendMessage";
    
    // Using cURL for more reliable Telegram delivery
    $data = [
        'chat_id' => $chatID,
        'text' => $message,
        'parse_mode' => 'Markdown'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, count($data));
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);
}

/* =========================
   REDIRECT
========================= */
header("Location: booking_today.php?msg=success");
exit;