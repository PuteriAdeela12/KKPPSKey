<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<div class="topbar no-print">
    <div class="topbar-left">
        👤 <?= htmlspecialchars($_SESSION['username'] ?? 'User') ?>
    </div>
    <div class="topbar-right">
        <a href="logout.php" class="btn-logout"
           onclick="return confirm('Logout dari sistem?')">
           Logout
        </a>
    </div>
</div>

<script>
/* =========================
   AUTO LOGOUT (IDLE)
========================= */
let idleTime = 0;
const idleLimit = 600; // 10 minit (dalam saat)

// reset bila ada aktiviti
function resetIdle(){
    idleTime = 0;
}

// kira idle setiap 1 saat
setInterval(() => {
    idleTime++;
    if (idleTime >= idleLimit) {
        alert("Anda telah logout secara automatik kerana tidak aktif.");
        window.location.href = "logout.php";
    }
}, 1000);

// aktiviti yang dikira
["mousemove","keydown","click","scroll","touchstart"].forEach(evt => {
    document.addEventListener(evt, resetIdle, true);
});
</script>
