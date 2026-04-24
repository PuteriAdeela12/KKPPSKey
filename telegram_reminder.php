<?php
// 1️⃣ TIMEZONE — WAJIB PALING ATAS
date_default_timezone_set('Asia/Kuala_Lumpur');

$secret = 'kkppskey2026';
if (!isset($_GET['key']) || $_GET['key'] !== $secret) {
    http_response_code(403);
    exit('Forbidden');
}

include "db.php";

/* =========================
   TELEGRAM CONFIG
========================= */
$BOT_TOKEN = "8313249719:AAGUW3isYt914_FzbS8_PfGuPz2d6IlSawU";
$API_URL  = "https://api.telegram.org/bot{$BOT_TOKEN}/sendMessage";

/* =========================
   GET OVERDUE (EVERY 30 MIN)
========================= */
$stmt = $conn->prepare("
    SELECT 
        b.booking_id,
        b.borrower_name,
        b.phone,
        b.end_time,
        k.key_code,
        k.room_name,
        s.telegram_chat_id,
        b.overdue_notified
    FROM bookings b
    JOIN key_list k ON k.key_id = b.room_id
    LEFT JOIN staff_name s ON s.staff_id = b.staff_id
    WHERE b.status = 'BOOKED'
      AND b.booking_date = CURDATE()
      AND b.end_time < CONVERT_TZ(NOW(), '+00:00', '+08:00')
      AND s.telegram_chat_id IS NOT NULL
      AND (
            b.overdue_notified IS NULL
            OR b.overdue_notified <= DATE_SUB(NOW(), INTERVAL 30 MINUTE)
          )
");
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$count = 0;

foreach ($rows as $r) {

    // ❗ GUNA TEXT BIASA (NO MARKDOWN)
    $message =
        "⏰ PERINGATAN PULANG KUNCI\n\n"
      . "Kod Kunci : {$r['key_code']}\n"
      . "Bilik     : {$r['room_name']}\n"
      . "Peminjam  : {$r['borrower_name']}\n"
      . "Telefon  : {$r['phone']}\n"
      . "Masa Tamat: {$r['end_time']}\n\n"
      . "⚠️ Kunci masih belum dipulangkan.\n"
      . "Sila pulangkan dengan segera.";

    $payload = [
        'chat_id' => $r['telegram_chat_id'],
        'text'    => $message
    ];

    $ch = curl_init($API_URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    $response = curl_exec($ch);
    curl_close($ch);

    // OPTIONAL: log jika error
    if ($response !== false) {
        $count++;

        // UPDATE LAST NOTIFY TIME
        $upd = $conn->prepare("
            UPDATE bookings
            SET overdue_notified = NOW()
            WHERE booking_id = ?
        ");
        $upd->execute([$r['booking_id']]);
    }
}

echo "Telegram overdue reminder sent: {$count}";
