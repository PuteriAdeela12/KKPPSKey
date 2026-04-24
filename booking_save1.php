<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "db.php";

/* =========================
   GET POST DATA
========================= */
$room_id       = (int)($_POST['room_id'] ?? 0);
$borrower_sel  = $_POST['borrower_select'] ?? '';
$booking_type  = $_POST['booking_type'] ?? '';
$booking_date  = $_POST['booking_date'] ?? '';
$start_time    = $_POST['start_time'] ?? null;
$end_time      = $_POST['end_time'] ?? null;
$purpose       = trim($_POST['purpose'] ?? '');
$phone         = trim($_POST['phone'] ?? '');

if ($room_id === 0 || $booking_date === '' || $booking_type === '') {
    die("Data tidak lengkap");
}

/* =========================
   GET KEY INFO (AUTO)
========================= */
$stmt = $conn->prepare("
    SELECT key_code, room_name
    FROM key_list
    WHERE key_id = ?
");
$stmt->execute([$room_id]);
$key = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$key) {
    die("Maklumat kunci tidak dijumpai");
}

$key_code  = $key['key_code'];
$room_name = $key['room_name'];

/* =========================
   RESOLVE BORROWER
========================= */
$borrower_type = '';
$staff_id = null;
$borrower_name = '';

if ($borrower_sel === 'others') {

    $borrower_type = 'others';
    $borrower_name = trim($_POST['borrower_name_others'] ?? '');

    if ($borrower_name === '') {
        die("Nama peminjam (Others) wajib diisi");
    }

} else {

    $borrower_type = 'staff';
    $staff_id = (int) str_replace('staff_', '', $borrower_sel);

    $stmt = $conn->prepare("
        SELECT staff_name, phone
        FROM staff_name
        WHERE staff_id = ?
    ");
    $stmt->execute([$staff_id]);
    $staff = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$staff) {
        die("Staff tidak dijumpai");
    }

    $borrower_name = $staff['staff_name'];

    // auto ambil phone staff jika kosong
    if ($phone === '') {
        $phone = $staff['phone'];
    }
}

/* =========================
   TIME HANDLING
========================= */
if ($booking_type === 'whole_day') {
    $start_time = '00:00:00';
    $end_time   = '23:59:59';
} else {
    if (!$start_time || !$end_time || $start_time >= $end_time) {
        die("Masa tidak sah");
    }
}

/* =========================
   CONFLICT CHECK (BOOKED SAHAJA)
========================= */

// WHOLE DAY
if ($booking_type === 'whole_day') {
    $stmt = $conn->prepare("
        SELECT COUNT(*)
        FROM bookings
        WHERE room_id=? AND booking_date=? AND status='BOOKED'
    ");
    $stmt->execute([$room_id, $booking_date]);
    if ($stmt->fetchColumn() > 0) {
        die("Tarikh ini sudah dibooking");
    }
}

// TIME SLOT
if ($booking_type === 'time_slot') {

    // Existing whole day
    $stmt = $conn->prepare("
        SELECT COUNT(*)
        FROM bookings
        WHERE room_id=? AND booking_date=?
          AND booking_type='whole_day'
          AND status='BOOKED'
    ");
    $stmt->execute([$room_id, $booking_date]);
    if ($stmt->fetchColumn() > 0) {
        die("Tarikh ini sudah ada Whole Day booking");
    }

    // Overlap
    $stmt = $conn->prepare("
        SELECT COUNT(*)
        FROM bookings
        WHERE room_id=? AND booking_date=?
          AND booking_type='time_slot'
          AND status='BOOKED'
          AND start_time < ?
          AND end_time > ?
    ");
    $stmt->execute([$room_id, $booking_date, $end_time, $start_time]);
    if ($stmt->fetchColumn() > 0) {
        die("Masa bertindih dengan booking sedia ada");
    }
    if ($booking_type === 'time_slot') {
    if (strtotime($end_time) <= strtotime($start_time)) {
        die('Masa tamat mesti lebih lewat daripada masa mula');
    }
}

}

/* =========================
   INSERT BOOKING (FINAL)
========================= */
$stmt = $conn->prepare("
    INSERT INTO bookings
    (
        room_id,
        key_code,
        room_name,
        borrower_type,
        staff_id,
        borrower_name,
        phone,
        booking_type,
        booking_date,
        start_time,
        end_time,
        purpose,
        status,
        overdue_notified
    )
    VALUES (?,?,?,?,?,?,?,?,?,?,?,?, 'BOOKED', 0)
");


$stmt->execute([
    $room_id,
    $key_code,
    $room_name,
    $borrower_type,
    $staff_id,
    $borrower_name,
    $phone,
    $booking_type,
    $booking_date,
    $start_time,
    $end_time,
    $purpose
]);

header("Location: booking_today.php");
exit;
