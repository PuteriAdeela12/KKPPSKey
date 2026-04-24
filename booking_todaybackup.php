<?php

date_default_timezone_set('Asia/Kuala_Lumpur');

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include "db.php";
include "topbar.php";

/* =========================
   STATUS FUNCTION (SAMA DGN INDEX)
========================= */
function getBookingStatus(array $b): string
{
    // overdue jika masa tamat sudah lepas
    if ($b['end_time'] < date('H:i:s')) {
        return 'OVERDUE';
    }
    return 'BOOKED';
}

/* =========================
   GET TODAY BOOKINGS
========================= */
$stmt = $conn->prepare("
    SELECT 
        b.booking_id,
        b.room_id,
        b.borrower_type,
        b.staff_id,
        b.borrower_name,
        b.booking_type,
        b.booking_date,
        b.start_time,
        b.end_time,
        b.purpose,
        k.key_code,
        k.room_name
    FROM bookings b
    JOIN key_list k ON k.key_id = b.room_id
    WHERE b.booking_date = CURDATE()
      AND b.status != 'returned'
    ORDER BY b.start_time ASC
");
$stmt->execute();
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Booking Today</title>
<style>
/* ===== TOP BAR ===== */
.topbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:16px;
}

.topbar-left{
    font-size:13px;
    color:#475569;
}

.btn-logout{
    padding:6px 14px;
    background:#ef4444;
    color:#fff;
    border-radius:6px;
    text-decoration:none;
    font-weight:bold;
    font-size:13px;
}

.btn-logout:hover{
    background:#dc2626;
}

/* SEMBUNYI MASA PRINT */
@media print{
    .topbar{display:none;}
}

body{font-family:Arial;background:#f5f7fa;padding:20px;}
table{width:100%;border-collapse:collapse;background:#fff;border-radius:8px;overflow:hidden;}
th{background:#1e293b;color:#fff;padding:10px;text-align:left;}
td{padding:9px;border-bottom:1px solid #e5e7eb;}
.status{padding:4px 10px;border-radius:12px;font-size:13px;font-weight:bold;}
.BOOKED{background:#fff7ed;color:#9a3412;}
.OVERDUE{background:#fee2e2;color:#991b1b;}
.btn-return{
    background:#2563eb;
    color:#fff;
    padding:6px 12px;
    border-radius:6px;
    text-decoration:none;
    font-weight:bold;
    font-size:13px;
}
</style>
</head>
<body>

<h2>📅 Booking Today (<?= date('d/m/Y') ?>)</h2>

<table>
<tr>
    <th>Masa</th>
    <th>Kod Kunci</th>
    <th>Nama Bilik</th>
    <th>Peminjam</th>
    <th>Tujuan</th>
    <th>Status</th>
    <th>Tindakan</th>
</tr>

<?php if (!$bookings): ?>
<tr>
    <td colspan="7">Tiada tempahan hari ini</td>
</tr>
<?php endif; ?>

<?php foreach ($bookings as $b): ?>
<?php
$status = getBookingStatus($b);

/* boleh return jika:
   - admin
   - staff yg pinjam
*/
$bolehReturn =
    $_SESSION['role'] === 'admin' ||
    (
        $b['borrower_type'] === 'staff' &&
        $b['staff_id'] == ($_SESSION['staff_id'] ?? 0)
    );
?>
<tr>
    <td><?= substr($b['start_time'],0,5) ?> – <?= substr($b['end_time'],0,5) ?></td>
    <td><?= htmlspecialchars($b['key_code']) ?></td>
    <td><?= htmlspecialchars($b['room_name']) ?></td>
    <td><?= htmlspecialchars($b['borrower_name']) ?></td>
    <td><?= htmlspecialchars($b['purpose']) ?></td>
    <td><span class="status <?= $status ?>"><?= $status ?></span></td>
    <td>
        <?php if ($bolehReturn): ?>
            <a class="btn-return"
               href="booking_return.php?id=<?= $b['booking_id'] ?>"
               onclick="return confirm('Pulangkan kunci ini?')">
               RETURN
            </a>
        <?php else: ?>
            —
        <?php endif; ?>
    </td>
</tr>
<?php endforeach; ?>

</table>

</body>
</html>
