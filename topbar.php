<?php
if (session_status() === PHP_SESSION_NONE) {
session_start();
}
?>

<!-- Import Tailwind and Fonts -->

<script src="https://cdn.tailwindcss.com"></script>

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
body {
padding-top: 76px; /* Adjusted space for the fixed topbar */
}

.topbar-font { 
    font-family: &#39;Plus Jakarta Sans&#39;, sans-serif; 
}

/* Ensure topbar doesn&#39;t affect print */
@media print {
    .no-print { display: none !important; }
    body { padding-top: 0; }
}

.btn-action {
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-action:hover {
    transform: translateY(-1px);
    filter: brightness(110%);
}


</style>

<!-- Fixed Topbar Container -->

<div class="topbar no-print topbar-font fixed top-0 left-0 right-0 z-50 bg-blue-900 border-b-4 border-yellow-400 px-6 py-3 flex items-center justify-between shadow-xl">

<!-- Left Section: Logo & User Info -->
<div class="flex items-center gap-6">
    <!-- Website Logo -->
    <div class="flex items-center gap-3 border-r border-white/20 pr-6">
        <div class="bg-white p-1.5 rounded-lg shadow-sm">
            <img src="assets/kkppskeynobg.png" alt="Logo" class="h-8 w-auto" onerror="this.src='https://cdn-icons-png.flaticon.com/512/2830/2830305.png'">
        </div>
        <span class="text-white font-extrabold text-lg tracking-tight">KKPPSKEY</span>
    </div>

    <!-- User Info -->
    <div class="flex items-center gap-3">
        <div class="bg-white/10 p-2 rounded-xl text-white flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-100" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
        </div>
        <div class="flex flex-col">
            <span class="text-[9px] uppercase tracking-widest text-blue-300 font-bold leading-none mb-1">Authenticated USer</span>
            <span class="text-white font-bold text-sm leading-none uppercase"><?= htmlspecialchars($_SESSION['username'] ?? 'Guest User') ?></span>
        </div>
    </div>
</div>

<!-- Right Section: Navigation Buttons -->
<div class="flex items-center gap-3">
    <a href="kunci_index.php" class="btn-action bg-emerald-600 text-white px-4 py-2.5 rounded-xl font-bold text-xs shadow-md hover:shadow-lg">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Back to List
    </a>

</div>


</div>

<script>
/* =========================
AUTO LOGOUT (IDLE)
========================= */
let idleTime = 0;
const idleLimit = 900; // 15 minit (dalam saat)

function resetIdle(){
idleTime = 0;
}

setInterval(() => {
idleTime++;
if (idleTime >= idleLimit) {
alert("Anda telah logout secara automatik kerana tidak aktif.");
window.location.href = "logout.php";
}
}, 1000);

["mousemove","keydown","click","scroll","touchstart"].forEach(evt => {
document.addEventListener(evt, resetIdle, true);
});
</script>