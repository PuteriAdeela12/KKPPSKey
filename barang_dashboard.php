<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard Barang</title>

<style>
*{box-sizing:border-box;}

body{
    margin:0;
    font-family:Arial;
    background:#f1f5f9;
}

/* NAVBAR */
.navbar{
    background:#0f172a;
    color:white;
    padding:14px 16px;
    font-size:14px;
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.navbar a{
    color:white;
    text-decoration:none;
    font-weight:bold;
}

/* CONTAINER */
.container{
    padding:20px;
}

/* TITLE */
h2{
    margin-bottom:20px;
}

/* CARDS GRID */
.cards{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
    gap:16px;
}

/* CARD */
.card{
    background:white;
    padding:22px;
    border-radius:16px;
    box-shadow:0 4px 10px rgba(0,0,0,0.05);
    text-align:center;
    transition:0.2s;
}

.card:hover{
    transform:translateY(-3px);
}

.card a{
    text-decoration:none;
    color:#1e293b;
    font-weight:bold;
    font-size:15px;
}

.icon{
    font-size:36px;
    margin-bottom:8px;
}

/* MOBILE POLISH */
@media(max-width:768px){

    .navbar{
        font-size:13px;
        padding:12px;
    }

    .cards{
        grid-template-columns:1fr;
    }

    .card{
        padding:20px;
    }

    .card a{
        font-size:16px;
    }
}
</style>
</head>

<body>

<div class="navbar">
    <div>
        <a href="index.php">🏠 Menu</a>
    </div>
    <div>
        <?= $_SESSION['full_name'] ?>
    </div>
</div>

<div class="container">

<h2>📦 Dashboard Peminjaman Barang</h2>

<div class="cards">

    <div class="card">
        <div class="icon">📦</div>
        <a href="barang_list.php">Lihat Senarai Barang</a>
    </div>

    <div class="card">
        <div class="icon">➕</div>
        <a href="barang_booking_form.php">Mohon Pinjam Barang</a>
    </div>

    <?php if($_SESSION['role']==='admin'): ?>
    <div class="card">
        <div class="icon">✅</div>
        <a href="barang_approval.php">Approval Permohonan</a>
    </div>
    <?php endif; ?>

</div>

</div>
</body>
</html>