<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Login | KKPPSKey</title>

<style>
body{
    margin:0;
    font-family: "Segoe UI", Arial, sans-serif;
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    background:
        linear-gradient(rgba(15,23,42,0.75), rgba(15,23,42,0.75)),
  
    background-size:cover;
    background-position:center;
}

.login-box{
    background:#ffffff;
    width:360px;
    padding:35px;
    border-radius:14px;
    box-shadow:0 20px 40px rgba(0,0,0,0.25);
    text-align:center;
}

.login-box h1{
    margin:0;
    font-size:26px;
    color:#0f172a;
}

.login-box p{
    margin:10px 0 25px;
    color:#475569;
    font-size:14px;
}

.login-box input{
    background-image: url("assets/kkppskeynobg.png");
    width:100%;
    padding:12px 14px;
    margin-bottom:14px;
    border-radius:8px;
    border:1px solid #cbd5e1;
    font-size:14px;
}

.login-box input:focus{
    outline:none;
    border-color:#2563eb;
    box-shadow:0 0 0 2px rgba(37,99,235,0.15);
}

.login-box button{
    width:100%;
    padding:12px;
    border:none;
    border-radius:8px;
    background:#2563eb;
    color:white;
    font-weight:600;
    font-size:15px;
    cursor:pointer;
}

.login-box button:hover{
    background:#1e40af;
}

.error{
    background:#fee2e2;
    color:#991b1b;
    padding:8px;
    border-radius:6px;
    font-size:13px;
    margin-bottom:12px;
}

.footer{
    margin-top:18px;
    font-size:12px;
    color:#64748b;
}
   
  

</style>
</head>

<body>

<div class="login-box">
    <h1>🔐 KKPPSKey</h1>
    <p>Selamat datang ke Sistem Pengurusan Kunci KKPPS.<br>
       Sila login untuk bermula.</p>

    <?php if (isset($_GET['error'])): ?>
        <div class="error">Username atau password tidak sah</div>
    <?php endif; ?>

    <form method="POST" action="login_process.php">
        <input type="text" name="username" placeholder="Username / No Telefon" required>
	<input type="password" name="password" placeholder="Password" required>

        <button type="submit">Login</button>
    </form>


    <div class="footer">
        © <?= date('Y') ?> KKPPSKey
    </div>
</div>

</body>
</html>
