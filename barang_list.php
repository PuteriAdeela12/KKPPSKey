<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include "db.php";

$stmt = $conn->query("SELECT * FROM barang_list ORDER BY created_at DESC");
$barangs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Senarai Barang</title>

<style>
*{box-sizing:border-box;}

body{
    margin:0;
    font-family:Arial;
    background:#f1f5f9;
}

/* NAV */
.navbar{
    background:#0f172a;
    padding:14px 20px;
    color:white;
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.navbar a{
    color:white;
    text-decoration:none;
    margin-right:15px;
    font-weight:bold;
}

.container{
    padding:20px;
}

/* HEADER */
.header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
}

.btn{
    padding:10px 18px;
    border-radius:10px;
    background:#2563eb;
    color:white;
    text-decoration:none;
    font-weight:bold;
    display:inline-block;
}

/* TABLE DESKTOP */
table{
    width:100%;
    border-collapse:collapse;
    background:white;
    border-radius:12px;
    overflow:hidden;
}

th{
    background:#1e293b;
    color:white;
    padding:12px;
    text-align:left;
}

td{
    padding:12px;
    border-bottom:1px solid #e2e8f0;
}

/* MOBILE CARD */
.cards{
    display:none;
}

.card{
    background:white;
    padding:16px;
    border-radius:16px;
    margin-bottom:14px;
    box-shadow:0 2px 6px rgba(0,0,0,0.06);
}

.card-title{
    font-weight:bold;
    font-size:16px;
    margin-bottom:6px;
}

.card-sub{
    font-size:14px;
    color:#475569;
    margin-bottom:8px;
}

/* RESPONSIVE */
@media(max-width:768px){

    table{display:none;}
    .cards{display:block;}

    .header{
        flex-direction:column;
        gap:12px;
        align-items:flex-start;
    }

    .btn{
        width:100%;
        text-align:center;
        padding:14px;
        font-size:16px;
    }
}
</style>
</head>

<body>

<div class="navbar">
    <div>
        <a href="barang_dashboard.php">← Dashboard</a>
    </div>
    <div>
        <?= $_SESSION['full_name'] ?>
    </div>
</div>

<div class="container">

<div class="header">
    <h2>📦 Senarai Barang</h2>

    <?php if($_SESSION['role']==='admin'): ?>
        <a href="barang_add.php" class="btn">+ Tambah Barang</a>
    <?php endif; ?>
</div>

<!-- DESKTOP TABLE -->
<table>
<tr>
    <th>Nama</th>
    <th>Kategori</th>
    <th>Kod Aset</th>
    <th>Kuantiti</th>
    <th>Lokasi</th>
</tr>

<?php foreach($barangs as $b): ?>
<tr>
    <td><?= htmlspecialchars($b['barang_nama']) ?></td>
    <td><?= htmlspecialchars($b['kategori']) ?></td>
    <td><?= htmlspecialchars($b['kod_aset']) ?></td>
    <td><?= htmlspecialchars($b['kuantiti']) ?></td>
    <td><?= htmlspecialchars($b['lokasi']) ?></td>
</tr>
<?php endforeach; ?>
</table>

<!-- MOBILE CARDS -->
<div class="cards">
<?php foreach($barangs as $b): ?>
    <div class="card">
        <div class="card-title"><?= htmlspecialchars($b['barang_nama']) ?></div>
        <div class="card-sub">Kategori: <?= htmlspecialchars($b['kategori']) ?></div>
        <div class="card-sub">Kod: <?= htmlspecialchars($b['kod_aset']) ?></div>
        <div class="card-sub">Kuantiti: <?= htmlspecialchars($b['kuantiti']) ?></div>
        <div class="card-sub">Lokasi: <?= htmlspecialchars($b['lokasi']) ?></div>
    </div>
<?php endforeach; ?>
</div>

</div>
</body>
</html>