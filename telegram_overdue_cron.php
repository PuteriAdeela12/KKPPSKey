<?php
/**********************************************************
 * KKPPSKey – Telegram Overdue Reminder (FINAL + GAS PROXY)
 **********************************************************/

error_reporting(E_ALL);
ini_set('display_errors', 1);

date_default_timezone_set('Asia/Kuala_Lumpur');

include "db.php";

/* =========================
   SECURITY KEY
========================= */
$SECRET_KEY = 'kkppskey2026';

if (!isset($_GET['key']) || $_GET['key'] !== $SECRET_KEY) {
    http_response_code(403);
    exit('Forbidden');
}

/* =========================
   GET OVERDUE BOOKINGS
========================= */
$sql = "
SELECT
    b.booking_id,
    b.borrower_name,
    b.phone,
    b.end_datetime,
    k.key_code,
    k.room_name,
    s.telegram_chat_id
FROM bookings b
JOIN key_list k ON k.key_id = b.room_id
LEFT JOIN staff_name s ON s.staff_id = b.staff_id
WHERE b.status = 'BOOKED'
  AND b.overdue_notified = 0
  AND b.end_datetime IS NOT NULL
  AND TIMESTAMPDIFF(MINUTE, b.end_datetime, NOW()) >= 15
  AND s.telegram_chat_id IS NOT NULL
  AND b.overdue_notified < 6
";

$rows = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
$sent = 0;

/* =========================
   SEND VIA GOOGLE APPS SCRIPT
========================= */
$GAS_URL = "https://script.google.com/macros/s/AKfycbwpfK3kRHO9Ia31sV_OV6II7cry9Vu4up8rYjNWr-yCkCiZ_2x0rvmsBGCzWKerCcufhQ/exec"; // 🔴 TUKAR URL INI

foreach ($rows as $r) {

    $msg =
        "⏰ REMINDER KUNCI OVERDUE\n\n" .
        "🔑 Kod Kunci : {$r['key_code']}\n" .
        "🏢 Bilik     : {$r['room_name']}\n" .
        "👤 Peminjam  : {$r['borrower_name']}\n" .
        "📞 Telefon  : {$r['phone']}\n" .
        "⏱ Tamat     : {$r['end_datetime']}\n\n" .
        "⚠️ Sila pulangkan kunci segera.";

    $payload = json_encode([
        'chat_id' => $r['telegram_chat_id'],
        'text'    => $msg
    ]);

    $ch = curl_init($GAS_URL);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_SSL_VERIFYPEER => false
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    if ($response !== false) {
        $conn->prepare("
            UPDATE bookings
            SET overdue_notified = overdue_notified + 1
            WHERE booking_id = ?
        ")->execute([$r['booking_id']]);

        $sent++;
    }
}

echo "Done. {$sent} reminder sent.";
