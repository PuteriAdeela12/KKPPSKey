<?php
session_start();
include "db.php";

/* =========================
   AUTH CHECK
========================= */
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

/* =========================
   GET ROOM ID
========================= */
$room_id = (int)($_GET['room_id'] ?? 0);
if ($room_id <= 0) {
    die("Rekod tidak sah");
}

/* =========================
   GET BOOKING AKTIF (HARI INI)
========================= */
$stmt = $conn->prepare("
SELECT 
    b.booking_id,
    b.borrower_type,
    b.staff_id,
    b.borrower_name,
    b.phone,
    b.booking_type,
    b.booking_date,
    b.start_time,
    b.end_time,
    b.purpose,
    k.key_code,
    k.room_name
FROM bookings b
JOIN key_list k ON k.key_id = b.room_id
WHERE b.room_id = ?
  AND b.status = 'BOOKED'
ORDER BY b.booking_date DESC, b.start_time DESC
LIMIT 1

");
$stmt->execute([$room_id]);
$booking = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$booking) {
    die("Tiada booking aktif untuk kunci ini");
}

/* =========================
   PERMISSION RETURN
========================= */
$bolehReturn =
    ($_SESSION['is_super_admin'] ?? false) === true
    || ($_SESSION['role'] ?? '') === 'admin'
    || (
        $booking['borrower_type'] === 'staff'
        && $booking['staff_id'] == ($_SESSION['staff_id'] ?? 0)
    );

/* =========================
   STATUS LOGIC
========================= */
$status = (
    $booking['end_datetime'] <= date('Y-m-d H:i:s')
    ? 'OVERDUE'
    : 'BOOKED'
);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Booking Detail</title>

<style>
body{
    font-family: Arial, sans-serif;
    background: linear-gradient(135deg,#e2e8f0,#f8fafc);
    min-height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
}

/* CARD */
.card{
    background:#ffffff;
    width:420px;
    max-width:92%;
    padding:30px;
    border-radius:16px;
    box-shadow:0 25px 50px rgba(0,0,0,0.15);
}

/* TITLE */
.card h2{
    margin:0 0 22px;
    text-align:center;
    font-size:20px;
    color:#0f172a;
}

/* INFO ROW */
.row{
    display:flex;
    margin-bottom:14px;
}
.label{
    width:130px;
    font-size:13px;
    color:#64748b;
}
.value{
    font-weight:600;
    color:#0f172a;
    word-break:break-word;
}

/* PHONE */
.value a{
    color:#2563eb;
    text-decoration:none;
}
.value a:hover{
    text-decoration:underline;
}

/* STATUS */
.status{
    display:inline-block;
    padding:6px 16px;
    border-radius:999px;
    font-size:12px;
    font-weight:700;
    letter-spacing:0.4px;
}
.BOOKED{background:#fff7ed;color:#9a3412;}
.OVERDUE{background:#fee2e2;color:#991b1b;}

/* ACTIONS */
.actions{
    margin-top:26px;
    display:flex;
    gap:12px;
}
.btn{
    flex:1;
    padding:12px;
    border-radius:10px;
    font-weight:700;
    text-align:center;
    text-decoration:none;
    font-size:14px;
}
.btn-back{
    background:#e5e7eb;
    color:#0f172a;
}
.btn-return{
    background:#2563eb;
    color:#ffffff;
}
.btn:hover{opacity:0.9;}
</style>
</head>

<body>

<div class="card">

<h2>🔑 Booking Detail</h2>

<div class="row">
    <div class="label">Kod Kunci</div>
    <div class="value"><?= htmlspecialchars($booking['key_code']) ?></div>
</div>

<div class="row">
    <div class="label">Nama Bilik</div>
    <div class="value"><?= htmlspecialchars($booking['room_name']) ?></div>
</div>

<div class="row">
    <div class="label">Peminjam</div>
    <div class="value"><?= htmlspecialchars($booking['borrower_name']) ?></div>
</div>

<?php if (!empty($booking['phone'])): ?>
<div class="row">
    <div class="label">No. Telefon</div>
    <div class="value">
        <a href="tel:<?= htmlspecialchars($booking['phone']) ?>">
            <?= htmlspecialchars($booking['phone']) ?>
        </a>
    </div>
</div>
<?php endif; ?>

<div class="row">
    <div class="label">Tarikh</div>
    <div class="value"><?= date('d/m/Y', strtotime($booking['booking_date'])) ?></div>
</div>

<div class="row">
    <div class="label">Masa</div>
    <div class="value">
        <?= $booking['booking_type'] === 'whole_day'
            ? 'Whole Day'
            : substr($booking['start_time'],0,5).' – '.substr($booking['end_time'],0,5)
        ?>
    </div>
</div>

<div class="row">
    <div class="label">Tujuan</div>
    <div class="value"><?= htmlspecialchars($booking['purpose']) ?></div>
</div>

<div class="row">
    <div class="label">Status</div>
    <div class="value">
        <span class="status <?= $status ?>"><?= $status ?></span>
    </div>
</div>

<div class="actions">
    <a href="index.php" class="btn btn-back">⬅ Back</a>

    <?php if ($bolehReturn): ?>
        <a href="booking_return.php?id=<?= $booking['booking_id'] ?>"
           class="btn btn-return"
           onclick="return confirm('Pulangkan kunci ini?')">
           RETURN
        </a>
    <?php endif; ?>
</div>

</div>

</body>
</html>
