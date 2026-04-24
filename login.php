<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: kunci_index.php"); // Updated destination
    exit;
}
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KKPPSKey</title>
    <!-- Using the same Tailwind and Font configuration as your example -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: #f0f4f8; 
            min-height: 100vh; 
        }
        .navbar-gradient {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
            border-bottom: 4px solid #fbbf24;
        }
        .loader { 
            border: 4px solid #f3f3f3; 
            border-top: 4px solid #fbbf24; 
            border-radius: 50%; 
            width: 48px; 
            height: 48px; 
            animation: spin 1s linear infinite; 
            margin: 20px auto; 
        }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        
        .login-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
    </style>
</head>
<body class="flex flex-col">

<!-- Navigation Bar matching the example -->
<nav class="navbar-gradient py-4 px-6 shadow-lg">
    <div class="max-w-6xl mx-auto flex justify-between items-center">
        <div class="flex items-center gap-3">
            <div class="bg-white p-2 rounded-lg shadow-sm">
                <img src="assets/kkppskeynobg.png" alt="Logo" class="h-8 w-auto" onerror="this.src='https://cdn-icons-png.flaticon.com/512/2830/2830305.png'">
            </div>
            <div class="flex flex-col">
                <span class="text-white font-extrabold text-lg tracking-tight leading-none">KKPPSKEY</span>
                <span class="text-yellow-400 text-[10px] font-bold tracking-widest uppercase">Admin Portal</span>
            </div>
        </div>
        <div class="hidden md:block">
            <span class="text-blue-100 text-sm font-medium">Sistem Peminjaman Kolej</span>
        </div>
    </div>
</nav>

<div class="container mx-auto px-4 flex-grow flex items-center justify-center py-12">
    <div class="login-card bg-white rounded-[2.5rem] p-10 max-w-md w-full text-center shadow-2xl border-t-8 border-yellow-400">

        <div class="relative inline-block mb-6">
            <div class="text-7xl animate-bounce">🔐</div>
            <div class="absolute -bottom-1 -right-1 bg-green-500 w-6 h-6 rounded-full border-4 border-white"></div>
        </div>

        <h1 class="text-3xl font-black text-blue-900 mb-2">Log Masuk Admin</h1>
        <p class="text-gray-500 mb-8 leading-relaxed">Sila imbas kad ID pada scanner untuk meneruskan akses.</p>
        
        <div class="bg-blue-50 rounded-3xl p-6 mb-8">
            <div class="loader"></div>
            <div id="message" class="text-sm font-bold text-blue-600 animate-pulse uppercase tracking-widest mt-2">
                Menunggu imbasan kad...
            </div>
        </div>

        <div class="flex items-center justify-center gap-2 text-blue-900/40 text-[10px] font-bold uppercase tracking-[0.2em]">
            <span class="w-8 h-px bg-blue-100"></span>
            Secured Connection
            <span class="w-8 h-px bg-blue-100"></span>
        </div>
    </div>
</div>

<footer class="text-center py-8 text-blue-900/40 text-xs font-medium uppercase tracking-widest">
    &copy; <?= date('Y') ?> KKPPS System • Developed for Efficiency
</footer>

<script>
/** * Keep original function names and logic 
 * as requested without modification 
 */
function checkRFID() {
    fetch('check_scan.php')
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            const messageEl = document.getElementById('message');
            messageEl.innerText = "Kad dikesan! Memproses...";
            messageEl.style.color = "#059669"; // Tailwind green-600
            
            // Redirect to a processing page to set session
            window.location.href = "login_process_rfid.php?uid=" + data.uid;
        }
    })
    .catch(error => console.error('Error:', error));
}

// Check every 2 seconds - Original timing maintained
setInterval(checkRFID, 2000);
</script>

</body>
</html>