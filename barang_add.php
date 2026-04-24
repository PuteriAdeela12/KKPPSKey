<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION['role'] !== 'admin') {
    die("Akses Admin Sahaja");
}

include "db.php";
include "barang_helper.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nama     = trim($_POST['barang_nama']);
    $kategori = trim($_POST['kategori']);
    $kod      = trim($_POST['kod_aset']);
    $qty      = (int)$_POST['kuantiti'];
    $lokasi   = trim($_POST['lokasi']);

    if (!$nama) {
        $error = "Nama barang wajib diisi";
    } else {
$kod = generateAssetCode($conn);


$kod = generateAssetCode($conn);

$stmt = $conn->prepare("
    INSERT INTO barang_list 
    (barang_nama, kategori, kod_aset, kuantiti, lokasi)
    VALUES (?,?,?,?,?)
");
$stmt->execute([$nama,$kategori,$kod,$qty,$lokasi]);

        header("Location: barang_list.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tambah Barang</title>

<style>
*{box-sizing:border-box;}

body{
    margin:0;
    font-family:Arial;
    background:#f1f5f9;
}

.navbar{
    background:#0f172a;
    color:white;
    padding:14px 16px;
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.navbar a{
    color:white;
    text-decoration:none;
    font-weight:bold;
}

.container{
    max-width:600px;
    margin:30px auto;
    background:white;
    padding:24px;
    border-radius:16px;
    box-shadow:0 4px 10px rgba(0,0,0,0.05);
}

h2{
    margin-top:0;
    margin-bottom:20px;
}

label{
    display:block;
    margin-top:16px;
    font-weight:bold;
    font-size:14px;
}

input{
    width:100%;
    height:48px;
    padding:12px 14px;
    margin-top:6px;
    border:1px solid #cbd5e1;
    border-radius:10px;
    font-size:15px;
}

button{
    margin-top:24px;
    width:100%;
    height:50px;
    border:none;
    border-radius:12px;
    background:#2563eb;
    color:white;
    font-size:16px;
    font-weight:bold;
    cursor:pointer;
}

button:hover{
    opacity:0.9;
}

.error{
    background:#fee2e2;
    color:#991b1b;
    padding:10px;
    border-radius:8px;
    margin-bottom:10px;
}

/* MOBILE */
@media(max-width:768px){
    .container{
        margin:16px;
        padding:20px;
    }

    input{
        font-size:16px;
    }
}
</style>
</head>

<body>

<div class="navbar">
    <a href="barang_list.php">← Senarai Barang</a>
    <div><?= $_SESSION['full_name'] ?></div>
</div>

<div class="container">

<h2>➕ Tambah Barang</h2>

<?php if(isset($error)): ?>
<div class="error"><?= $error ?></div>
<?php endif; ?>

<form method="post">

<label>Nama Barang</label>
<input type="text" name="barang_nama" required>

<label>Kategori</label>
<input type="text" name="kategori">


<label>Kuantiti</label>
<input type="number" name="kuantiti" value="1" min="1">

<label>Lokasi</label>
<input type="text" name="lokasi">

<button type="submit">Simpan Barang</button>

</form>

</div>
</body>
</html>