<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include "db.php";
include "topbar.php";

/* =========================
   PILIH TARIKH
========================= */
$date = $_GET['date'] ?? date('Y-m-d');

/* =========================
   GET DAILY LOG (SEMUA STATUS)
========================= */
$stmt = $conn->prepare("
    SELECT
        b.booking_date,
        b.booking_type,
        b.start_time,
        b.end_time,
        b.borrower_name,
        b.purpose,
        b.status,
        k.key_code,
        k.room_name
    FROM bookings b
    JOIN key_list k ON k.key_id = b.room_id
    WHERE b.booking_date = ?
    ORDER BY b.start_time ASC
");
$stmt->execute([$date]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* =========================
   STATUS LOGIC (KONSISTEN)
========================= */
function getDailyStatus(array $b): string
{
    if ($b['status'] === 'RETURNED') {
        return 'RETURNED';
    }

    if (
        $b['status'] === 'BOOKED' &&
        $b['booking_date'] === date('Y-m-d') &&
        $b['end_time'] < date('H:i:s')
    ) {
        return 'OVERDUE';
    }

    return 'BOOKED';
}
?>
<!DOCTYPE html>
<html>
<head>
<head>
    <meta charset="UTF-8">

    <title>Daily Booking Log</title>

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

/* =========================
   PRINT HEADER (PRINT ONLY)
========================= */
.print-header{
    display:none;
    text-align:center;
    margin-bottom:15px;
}

.print-header img{
    height:65px;
    margin-bottom:6px;
}

.print-header .title{
    font-size:13px;
    font-weight:bold;
    letter-spacing:0.4px;
}

/* =========================
   BASE STYLE
========================= */
body{
    font-family:Arial, sans-serif;
    background:#f5f7fa;
    padding:20px;
}

h2{margin-bottom:12px;}

.actions{margin-bottom:15px;}

table{
    width:100%;
    border-collapse:collapse;
    background:#fff;
    border-radius:8px;
    overflow:hidden;
}

th{
    background:#1e293b;
    color:#fff;
    padding:10px;
    text-align:left;
}

td{
    padding:9px;
    border-bottom:1px solid #e5e7eb;
}

tr:nth-child(even){background:#f8fafc;}

/* =========================
   STATUS BADGE
========================= */
.status{
    padding:4px 10px;
    border-radius:12px;
    font-size:13px;
    font-weight:bold;
    display:inline-block;
}
.BOOKED{background:#fff7ed;color:#9a3412;}
.OVERDUE{background:#fee2e2;color:#991b1b;}
.RETURNED{background:#dcfce7;color:#166534;}

/* =========================
   BUTTON
========================= */
.btn{
    padding:8px 14px;
    background:#2563eb;
    color:#fff;
    border-radius:6px;
    text-decoration:none;
    font-weight:bold;
    border:none;
    cursor:pointer;
}

.btn-print{
    background:#16a34a;
}

/* =========================
   PRINT RULE
========================= */
@media print{
    .print-header{display:block;}
    .no-print{display:none;}
    body{background:#fff;padding:0;}
}
</style>

</head>

<body>

<!-- =========================
     PRINT HEADER (HIDDEN)
========================= -->
<div class="print-header">
    <img src="assets/logo.png">
    <div class="title">
        SISTEM PENGURUSAN KUNCI KKPPS<br>
        LAPORAN PENGGUNAAN KUNCI (HARIAN)
    </div>
</div>

<h2 class="no-print">📋 Daily Booking Log</h2>

<div class="actions no-print">
    <form method="GET" style="display:inline-block;">
        <label>Tarikh:</label>
        <input type="date" name="date" value="<?= htmlspecialchars($date) ?>">
        <button class="btn">View</button>
    </form>

    <button onclick="window.print()" class="btn btn-print">
        🖨 Print
    </button>
</div>
<table>
<tr>
    <th>Masa</th>
    <th>Kod Kunci</th>
    <th>Nama Bilik</th>
    <th>Peminjam</th>
    <th>Tujuan</th>
    <th>Status</th>
</tr>

<?php if (!$rows): ?>
<tr>
    <td colspan="6">Tiada rekod untuk tarikh ini</td>
</tr>
<?php endif; ?>

<?php foreach ($rows as $r): ?>
<?php
$masa = ($r['booking_type'] === 'whole_day')
    ? 'Whole Day'
    : substr($r['start_time'],0,5).' – '.substr($r['end_time'],0,5);

$status = getDailyStatus($r);
?>
<tr>
    <td><?= $masa ?></td>
    <td><?= htmlspecialchars($r['key_code']) ?></td>
    <td><?= htmlspecialchars($r['room_name']) ?></td>
    <td><?= htmlspecialchars($r['borrower_name']) ?></td>
    <td><?= htmlspecialchars($r['purpose']) ?></td>
    <td>
        <span class="status <?= $status ?>"><?= $status ?></span>
    </td>
</tr>
<?php endforeach; ?>

</table>

</body>
</html>
