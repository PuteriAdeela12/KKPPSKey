<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include "db.php";


/* =========================
   PILIH TARIKH
========================= */
$date = $_GET['date'] ?? date('Y-m-d');

/* =========================
   DAILY DATA
========================= */
$stmt = $conn->prepare("
    SELECT 
        b.booking_date,
        b.booking_type,
        b.start_time,
        b.end_time,
        b.borrower_name,
        b.purpose,
        b.status AS booking_status,
        k.key_code,
        k.room_name
    FROM bookings b
    JOIN key_list k ON k.key_id = b.room_id
    WHERE b.booking_date = ?
    ORDER BY b.start_time ASC
");
$stmt->execute([$date]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Daily Booking Log</title>

<style>
body {
    font-family: Arial, sans-serif;
    padding: 20px;
    background: #f5f7fa;
}
h2 { margin-bottom: 15px; }
form { margin-bottom: 15px; }

table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    border-radius: 8px;
}
th {
    background: #1e293b;
    color: white;
    padding: 10px;
}
td {
    padding: 9px;
    border-bottom: 1px solid #e5e7eb;
}
tr:nth-child(even) { background: #f8fafc; }

.status {
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 13px;
    font-weight: bold;
}
.active   { background: #fff7ed; color: #9a3412; }
.returned { background: #dcfce7; color: #166534; }
</style>
</head>

<body>

<h2>📅 Daily Booking Log</h2>

<form method="GET">
    <label>Pilih Tarikh:</label>
    <input type="date" name="date" value="<?= htmlspecialchars($date) ?>">
    <button>View</button>
</form>

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
<tr><td colspan="6">Tiada rekod untuk tarikh ini</td></tr>
<?php endif; ?>

<?php foreach ($rows as $r): ?>
<tr>
    <td>
        <?php if ($r['booking_type'] === 'whole_day'): ?>
            Whole Day
        <?php else: ?>
            <?= substr($r['start_time'],0,5) ?> – <?= substr($r['end_time'],0,5) ?>
        <?php endif; ?>
    </td>
    <td><?= htmlspecialchars($r['key_code']) ?></td>
    <td><?= htmlspecialchars($r['room_name']) ?></td>
    <td><?= htmlspecialchars($r['borrower_name']) ?></td>
    <td><?= htmlspecialchars($r['purpose']) ?></td>
    <td>
        <span class="status <?= $r['booking_status'] ?>">
            <?= strtoupper($r['booking_status']) ?>
        </span>
    </td>
</tr>
<?php endforeach; ?>
</table>

</body>
</html>
