<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include "db.php";
include "topbar.php";

$room_id = $_GET['room_id'] ?? 0;
$scanned_uid = $_GET['uid'] ?? '';

if ($room_id == 0 || $scanned_uid == '') {
    die("Sila imbas kad pelajar terlebih dahulu.");
}

/* =========================
AUTO-FETCH STAFF BY UID
========================= */
$stmt = $conn->prepare("SELECT * FROM staff_name WHERE card_uid = ? LIMIT 1");
$stmt->execute([$scanned_uid]);
$staff = $stmt->fetch(PDO::FETCH_ASSOC);

$is_known = $staff ? true : false;
$borrower_name = $is_known ? $staff['staff_name'] : "";
$phone         = $is_known ? $staff['phone'] : "";
$dept          = $is_known ? $staff['department'] : "OTHERS / GUEST";
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Form</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f0f4f8; min-height: 100vh; }
        .glass-card { background: #ffffff; border-top: 6px solid #fbbf24; box-shadow: 0 10px 25px -5px rgba(30, 58, 138, 0.1); }
        .input-focus:focus { border-color: #3b82f6; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15); outline: none; }
        .custom-radio:checked + div { border-color: #3b82f6; background-color: #eff6ff; color: #1e3a8a; }
        .loader { border: 4px solid #f3f3f3; border-top: 4px solid #fbbf24; border-radius: 50%; width: 40px; height: 40px; animation: spin 1s linear infinite; margin: 15px auto; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
</head>
<body class="py-12 px-4">

<div class="max-w-2xl mx-auto">
    <div class="glass-card rounded-3xl overflow-hidden">
        <!-- Header Section -->
        <div class="bg-blue-900 pt-10 pb-8 px-8 text-center">
            <div class="inline-block p-4 bg-white rounded-2xl mb-4 shadow-sm">
                <img src="assets/kkppskeynobg.png" alt="Logo" class="h-16 w-auto mx-auto" onerror="this.src='https://cdn-icons-png.flaticon.com/512/3064/3064155.png'">
            </div>
            <h1 class="text-3xl font-extrabold text-white">Booking Form</h1>
            <p class="text-blue-200 mt-2 font-medium">Sila lengkapkan butiran permohonan anda</p>
        </div>

        <!-- Recognition Badge -->
        <div class="px-8 pt-6">
            <?php if ($is_known): ?>
                <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-blue-50 border border-blue-100 text-blue-800 shadow-sm">
                    <span class="text-lg">👋</span>
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wider opacity-70">Selamat Datang</p>
                        <p class="text-base font-bold"><?= htmlspecialchars($borrower_name) ?></p>
                    </div>
                </div>
            <?php else: ?>
                <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-orange-50 border border-orange-100 text-orange-800 shadow-sm">
                    <span class="text-lg">🔍</span>
                    <div>
                        <p class="text-sm font-semibold">Kad Belum Didaftarkan</p>
                        <p class="text-xs opacity-80">Sila lengkapkan maklumat di bawah untuk pendaftaran (ID: <?=$scanned_uid?>)</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Main Form -->
        <form action="booking_save.php" method="POST" id="bookingForm" class="p-8 space-y-6">
            <input type="hidden" name="room_id" value="<?= $room_id ?>">
            <input type="hidden" name="student_uid" value="<?= htmlspecialchars($scanned_uid) ?>">
            <input type="hidden" name="key_uid" id="key_uid_input">
            
            <!-- Personal Info Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-blue-900 mb-2">Nama Peminjam</label>
                    <input type="text" name="borrower_name" 
                        value="<?= htmlspecialchars($borrower_name) ?>" 
                        <?= $is_known ? 'readonly' : '' ?> 
                        required 
                        placeholder="Nama Penuh"
                        class="w-full px-4 py-3 rounded-xl border-2 border-gray-100 input-focus <?= $is_known ? 'bg-gray-100 cursor-not-allowed' : 'bg-gray-50/50' ?>">
                </div>
                <div>
                    <label class="block text-sm font-bold text-blue-900 mb-2">No. Telefon</label>
                    <input type="tel" name="phone" 
                        value="<?= htmlspecialchars($phone) ?>" 
                        <?= $is_known ? 'readonly' : '' ?> 
                        required 
                        placeholder="e.g. 0123456789"
                        class="w-full px-4 py-3 rounded-xl border-2 border-gray-100 input-focus <?= $is_known ? 'bg-gray-100 cursor-not-allowed' : 'bg-gray-50/50' ?>">
                </div>
            </div>

            <!-- Booking Type Selection -->
            <div>
                <label class="block text-sm font-bold text-blue-900 mb-3">Jenis Tempahan</label>
                <div class="grid grid-cols-2 gap-4">
                    <label class="cursor-pointer">
                        <input type="radio" name="booking_type" value="time_slot" checked class="hidden custom-radio peer" onchange="toggleTimeInputs()">
                        <div class="p-4 border-2 border-gray-100 rounded-2xl text-center font-bold text-gray-500 transition-all peer-checked:border-blue-600 peer-checked:bg-blue-50 peer-checked:text-blue-700">TIME SLOT</div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="booking_type" value="whole_day" class="hidden custom-radio peer" onchange="toggleTimeInputs()">
                        <div class="p-4 border-2 border-gray-100 rounded-2xl text-center font-bold text-gray-500 transition-all peer-checked:border-blue-600 peer-checked:bg-blue-50 peer-checked:text-blue-700">WHOLE DAY</div>
                    </label>
                </div>
            </div>

            <!-- Date & Time Container -->
            <div class="bg-blue-50/50 p-6 rounded-2xl space-y-6 border border-blue-100">
                <div>
                    <label class="block text-sm font-bold text-blue-900 mb-2">Tarikh Tempahan</label>
                    <input type="date" name="booking_date" id="booking_date" required class="w-full px-4 py-3 rounded-xl border-2 border-white input-focus shadow-sm">
                </div>
                
                <div id="timeInputs" class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-blue-900 mb-2">Masa Mula</label>
                        <input type="time" name="start_time" id="start_time" class="w-full px-4 py-3 rounded-xl border-2 border-white input-focus shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-blue-900 mb-2">Masa Tamat</label>
                        <input type="time" name="end_time" id="end_time" class="w-full px-4 py-3 rounded-xl border-2 border-white input-focus shadow-sm">
                    </div>
                </div>
            </div>

            <!-- Purpose Textarea -->
            <div>
                <label class="block text-sm font-bold text-blue-900 mb-2">Tujuan / Catatan</label>
                <textarea name="purpose" rows="3" required placeholder="Nyatakan tujuan tempahan anda..." class="w-full px-4 py-3 rounded-xl border-2 border-gray-100 input-focus bg-gray-50/50"></textarea>
            </div>

            <!-- Submit Button -->
            <button type="submit" id="submitBtn" class="w-full bg-blue-700 hover:bg-blue-800 text-white font-bold py-4 rounded-2xl shadow-lg flex items-center justify-center gap-2 transition-all active:scale-[0.98]">
                <span>Confirm & Scan Key 🔑</span>
                <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>
        </form>
    </div>
</div>

<!-- Scanning Modal Overlay -->
<div id="keyModal" class="fixed inset-0 bg-blue-900/90 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl p-8 max-w-sm w-full text-center shadow-2xl border-t-8 border-yellow-400">
        <div class="text-5xl mb-4">🔑</div>
        <h2 class="text-2xl font-bold text-blue-900 mb-2">Imbas Kunci Fizikal</h2>
        <p class="text-gray-600 mb-6">Sila imbas <strong>Kunci</strong> pada scanner.</p>
        <div class="loader"></div>
        <p class="text-xs font-bold text-blue-500 animate-pulse uppercase">Menunggu Kunci...</p>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        // Default date/time setup
        const now = new Date();
        const today = now.toISOString().split("T")[0];
        document.getElementById("booking_date").value = today;

        // Helper to format time as HH:mm
        const pad = n => n.toString().padStart(2, '0');
        
        // Masa Mula (Current Real Time)
        const currentHours = now.getHours();
        const currentMinutes = now.getMinutes();
        const currentTime = `${pad(currentHours)}:${pad(currentMinutes)}`;
        
        // Masa Tamat (One hour after real time)
        let endHours = currentHours + 1;
        let endMinutes = currentMinutes;
        
        // Handle midnight wrap-around or cap at end of day
        if (endHours >= 24) {
            endHours = 23;
            endMinutes = 59;
        }
        
        const endTime = `${pad(endHours)}:${pad(endMinutes)}`;

        document.getElementById("start_time").value = currentTime;
        document.getElementById("end_time").value = endTime;
    });

    function toggleTimeInputs() {
        const isWhole = document.querySelector('input[name="booking_type"]:checked').value === 'whole_day';
        const timeWrap = document.getElementById('timeInputs');
        timeWrap.style.opacity = isWhole ? '0.3' : '1';
        timeWrap.style.pointerEvents = isWhole ? 'none' : 'auto';
        
        // Set values if whole day
        if(isWhole) {
            document.getElementById("start_time").value = "08:00";
            document.getElementById("end_time").value = "17:00";
        } else {
            // Re-run the real-time logic if they switch back to Time Slot
            const now = new Date();
            const pad = n => n.toString().padStart(2, '0');
            const h = now.getHours();
            const m = now.getMinutes();
            document.getElementById("start_time").value = `${pad(h)}:${pad(m)}`;
            let eh = h + 1;
            if(eh >= 24) eh = 23;
            document.getElementById("end_time").value = `${pad(eh)}:${pad(m)}`;
        }
    }

    document.getElementById("bookingForm").addEventListener("submit", function(e) {
        e.preventDefault(); 
        document.getElementById("keyModal").classList.remove("hidden");
        
        let poll = setInterval(() => {
            fetch('check_scan.php')
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    clearInterval(poll);
                    document.getElementById("key_uid_input").value = data.uid;
                    
                    const btn = document.getElementById('submitBtn');
                    btn.disabled = true;
                    btn.innerHTML = `<span>Memproses Data...</span>`;
                    
                    document.getElementById("bookingForm").submit();
                }
            })
            .catch(err => console.error("Error polling:", err));
        }, 1000);
    });
</script>

</body>
</html>