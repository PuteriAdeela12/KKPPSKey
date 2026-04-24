<?php
session_start();
include "db.php";
include "topbar.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$month = $_GET['month'] ?? date('Y-m');
$startDate = $month.'-01';
$endDate = date('Y-m-t', strtotime($startDate));

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
    WHERE b.booking_date BETWEEN ? AND ?
    ORDER BY b.booking_date, b.start_time
");
$stmt->execute([$startDate,$endDate]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Monthly Booking Log</title>

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
h2{margin-bottom:12px;}
form{margin-bottom:15px;}

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
}
td{
    padding:9px;
    border-bottom:1px solid #e5e7eb;
}
tr:nth-child(even){background:#f8fafc;}

.status{
    padding:4px 10px;
    border-radius:12px;
    font-size:13px;
    font-weight:bold;
}
.BOOKED{background:#fff7ed;color:#9a3412;}
.OVERDUE{background:#fee2e2;color:#991b1b;}
.RETURNED{background:#dcfce7;color:#166534;}

.actions{margin-bottom:15px;}
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
.btn-print{background:#16a34a;}

@media print{
    .no-print{display:none;}
    body{background:white;padding:0;}
}
.print-header{
    display:none;
    text-align:center;
    margin-bottom:20px;
}
.print-header .logo{
    height:70px;
    margin-bottom:8px;
}
.print-header .title{
    font-size:14px;
    letter-spacing:0.5px;
}

@media print{
    .print-header{display:block;}
    h2{margin-top:0;}
}

</style>
</head>

<body>
<div class="print-header">
    <img src="assets/logo.png" class="logo">
    <div class="title">
        <strong>SISTEM PENGURUSAN KUNCI KKPPS</strong><br>
        Laporan Penggunaan Kunci(Monthly)
    </div>
</div>


<h2>📆 Monthly Booking Log</h2>

<div class="actions no-print">
<form method="GET" style="display:inline-block;">
    <label>Pilih Bulan:</label>
    <input type="month" name="month" value="<?= htmlspecialchars($month) ?>">
    <button class="btn">View</button>
</form>

<a href="#" onclick="window.print()" class="btn btn-print">🖨 Print</a>
</div>

<table>
<tr>
<th>Tarikh</th>
<th>Masa</th>
<th>Kod Kunci</th>
<th>Nama Bilik</th>
<th>Peminjam</th>
<th>Tujuan</th>
<th>Status</th>
</tr>

<?php if(!$rows): ?>
<tr><td colspan="7">Tiada rekod</td></tr>
<?php endif; ?>

<?php foreach($rows as $r): ?>
<?php
$masa = ($r['booking_type']==='whole_day')
    ? 'Whole Day'
    : substr($r['start_time'],0,5).' – '.substr($r['end_time'],0,5);

$paparStatus = $r['status'];
if (
    $r['status']==='BOOKED' &&
    $r['booking_date']===date('Y-m-d') &&
    $r['end_time'] < date('H:i:s')
) {
    $paparStatus = 'OVERDUE';
}
?>
<tr>
<td><?= date('d/m/Y',strtotime($r['booking_date'])) ?></td>
<td><?= $masa ?></td>
<td><?= htmlspecialchars($r['key_code']) ?></td>
<td><?= htmlspecialchars($r['room_name']) ?></td>
<td><?= htmlspecialchars($r['borrower_name']) ?></td>
<td><?= htmlspecialchars($r['purpose']) ?></td>
<td><span class="status <?= $paparStatus ?>"><?= $paparStatus ?></span></td>
</tr>
<?php endforeach; ?>
</table>

</body>
</html>
