<?php
date_default_timezone_set('Asia/Kuala_Lumpur');
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include "db.php";
include "topbar.php";

/* =========================
   GET TODAY BOOKINGS
========================= */
$stmt = $conn->prepare("
    SELECT 
        b.booking_id, b.room_id, b.borrower_type, b.staff_id,
        b.borrower_name, b.booking_type, b.booking_date,
        b.start_time, b.end_time, b.purpose, b.overdue_notified,
        k.key_code, k.room_name
    FROM bookings b
    JOIN key_list k ON k.key_id = b.room_id
    WHERE b.booking_date = CURDATE()
      AND b.status != 'returned'
    ORDER BY b.start_time ASC
");
$stmt->execute();
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* =========================
   AUTO-UPDATE OVERDUE STATUS
========================= */
$currentTime = date('H:i:s');
$toUpdate = [];

foreach ($bookings as $b) {
    if ($b['end_time'] < $currentTime && $b['overdue_notified'] == 0) {
        $toUpdate[] = $b['booking_id'];
    }
}

if (!empty($toUpdate)) {
    $placeholders = implode(',', array_fill(0, count($toUpdate), '?'));
    $upd = $conn->prepare("UPDATE bookings SET overdue_notified = 1 WHERE booking_id IN ($placeholders)");
    $upd->execute($toUpdate);
}

function getBookingStatus(array $b): string {
    if ($b['end_time'] < date('H:i:s')) {
        return 'OVERDUE';
    }
    return 'BOOKED';
}
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Today - KKPPSKey</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: #f8fafc; 
        }
        .navbar-gradient {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
            border-bottom: 4px solid #fbbf24;
        }
        .loader {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #2563eb;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            animation: spin 1s linear infinite;
        }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
</head>
<body class="flex flex-col min-h-screen">

<main class="container mx-auto px-4 max-w-7xl py-8 pb-12">
    
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h2 class="text-3xl font-black text-blue-900">📅 Tempahan Hari Ini</h2>
            <p class="text-slate-500">Senarai penggunaan bilik pada <?= date('d/m/Y') ?></p>
        </div>
        
        <div class="flex flex-wrap gap-2">
            <a href="kunci_index.php" class="bg-slate-100 text-slate-700 px-4 py-2 rounded-xl text-sm font-bold hover:bg-slate-200 transition">Back to List</a>
            <button onclick="window.location.reload()" class="bg-blue-100 text-blue-700 px-4 py-2 rounded-xl text-sm font-bold hover:bg-blue-200 transition">Refresh</button>
        </div>
    </div>

    <!-- Desktop Table View -->
    <div class="hidden md:block overflow-hidden bg-white rounded-[2rem] shadow-sm border border-slate-100">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 uppercase text-xs font-bold tracking-wider">
                    <th class="px-6 py-4 text-center">Masa</th>
                    <th class="px-6 py-4">Nama Bilik</th>
                    <th class="px-6 py-4">Peminjam</th>
                    <th class="px-6 py-4">Tujuan</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-center">Tindakan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (!$bookings): ?>
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-slate-400 italic">Tiada tempahan aktif ditemui untuk hari ini.</td>
                </tr>
                <?php endif; ?>

                <?php foreach ($bookings as $b): ?>
                <?php
                    $status = getBookingStatus($b);
                    $bolehReturn = $_SESSION['role'] === 'admin' || ($b['borrower_type'] === 'staff' && $b['staff_id'] == ($_SESSION['staff_id'] ?? 0));
                ?>
                <tr class="hover:bg-blue-50/50 transition">
                    <td class="px-6 py-4 text-center">
                        <div class="bg-slate-100 rounded-lg py-1 px-2 text-xs font-bold text-slate-600">
                            <?= substr($b['start_time'],0,5) ?> – <?= substr($b['end_time'],0,5) ?>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-bold text-blue-900"><?= htmlspecialchars($b['room_name']) ?></div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-semibold text-slate-700"><?= htmlspecialchars($b['borrower_name']) ?></div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-slate-500 truncate max-w-xs"><?= htmlspecialchars($b['purpose']) ?></div>
                    </td>
                    <td class="px-6 py-4">
                        <?php if ($status === 'OVERDUE'): ?>
                            <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full font-black text-[10px] tracking-tight uppercase">OVERDUE</span>
                        <?php else: ?>
                            <span class="bg-orange-100 text-orange-700 px-3 py-1 rounded-full font-black text-[10px] tracking-tight uppercase">BOOKED</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <?php if ($bolehReturn): ?>
                            <button onclick="startReturnFlow(<?= $b['booking_id'] ?>)" 
                                    class="bg-blue-600 text-white px-4 py-1.5 rounded-full font-extrabold text-xs tracking-tight hover:bg-blue-700 transition shadow-md shadow-blue-100">
                                RETURN
                            </button>
                        <?php else: ?>
                            <span class="text-slate-300">—</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Mobile Card View -->
    <div class="md:hidden space-y-4">
        <?php if (!$bookings): ?>
            <div class="bg-white p-8 rounded-3xl text-center text-slate-400 italic shadow-sm border border-slate-100">
                Tiada tempahan aktif hari ini.
            </div>
        <?php endif; ?>

        <?php foreach ($bookings as $b): ?>
        <?php
            $status = getBookingStatus($b);
            $bolehReturn = $_SESSION['role'] === 'admin' || ($b['borrower_type'] === 'staff' && $b['staff_id'] == ($_SESSION['staff_id'] ?? 0));
        ?>
        <div class="bg-white p-5 rounded-3xl shadow-sm border border-slate-100">
            <div class="flex justify-between items-start mb-3">
                <div class="bg-blue-50 text-blue-700 px-2 py-1 rounded-lg text-[10px] font-black tracking-widest">
                    <?= substr($b['start_time'],0,5) ?> – <?= substr($b['end_time'],0,5) ?>
                </div>
                <span class="bg-<?= ($status === 'OVERDUE' ? 'red' : 'orange') ?>-100 text-<?= ($status === 'OVERDUE' ? 'red' : 'orange') ?>-700 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-tight">
                    <?= $status ?>
                </span>
            </div>
            
            <div class="mb-4">
                <div class="text-xl font-black text-blue-900"><?= htmlspecialchars($b['room_name']) ?></div>
                <div class="flex items-center gap-2 text-sm mt-2">
                    <span class="text-slate-400 italic">Peminjam:</span>
                    <span class="font-bold text-slate-700"><?= htmlspecialchars($b['borrower_name']) ?></span>
                </div>
            </div>

            <?php if ($bolehReturn): ?>
                <button onclick="startReturnFlow(<?= $b['booking_id'] ?>)" 
                        class="w-full bg-blue-600 text-white py-3 rounded-2xl font-bold shadow-lg shadow-blue-100 active:scale-95 transition">
                    Pulangkan Kunci
                </button>
            <?php else: ?>
                <div class="text-center py-2 text-xs font-bold text-slate-300 uppercase tracking-widest border-t border-slate-50 mt-2 pt-3">
                    Tiada akses pulangan
                </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
</main>

<!-- Return Modal Overlay (Synced with your original multi-step logic) -->
<div id="returnModal" class="hidden fixed inset-0 bg-blue-900/40 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-[2.5rem] p-10 max-w-sm w-full text-center shadow-2xl border-t-8 border-yellow-400">
        <div class="relative inline-block mb-6">
            <div id="modalIcon" class="text-6xl animate-bounce">🪪</div>
            <div class="absolute -bottom-1 -right-1 bg-green-500 w-6 h-6 rounded-full border-4 border-white"></div>
        </div>
        <h3 id="modalTitle" class="text-2xl font-black text-blue-900 mb-2">Step 1: Scan ID Card</h3>
        <p id="modalMsg" class="text-slate-500 mb-8 text-sm leading-relaxed">Please tap your Student/Staff card on the RFID reader.</p>
        
        <div class="bg-blue-50 rounded-3xl p-6 mb-6">
            <div class="loader mx-auto"></div>
            <div class="text-xs font-bold text-blue-600 animate-pulse uppercase tracking-widest mt-4">Waiting for scan...</div>
        </div>
        
        <button onclick="closeModal()" class="w-full text-slate-400 font-bold text-sm hover:text-slate-600 transition">Batalkan</button>
    </div>
</div>

<footer class="mt-auto text-center py-8 text-slate-400 text-[10px] font-bold uppercase tracking-widest">
    &copy; <?= date('Y') ?> KKPPS System • Secured Management
</footer>

<script>
let currentStep = 1; 
let activeBookingId = null;
let pollTimer = null;

function startReturnFlow(bookingId) {
    activeBookingId = bookingId;
    currentStep = 1;
    
    // UI Reset
    document.getElementById('modalTitle').innerText = "Step 1: Scan ID Card";
    document.getElementById('modalIcon').innerText = "🪪";
    document.getElementById('modalMsg').innerText = "Please tap your Student/Staff card on the RFID reader.";
    document.getElementById('returnModal').classList.remove('hidden');

    // Start checking for hardware scans
    pollTimer = setInterval(checkHardwareScan, 1500);
}

function closeModal() {
    document.getElementById('returnModal').classList.add('hidden');
    clearInterval(pollTimer);
}

function checkHardwareScan() {
    fetch('check_scan.php')
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            if (currentStep === 1) {
                // Step 1 Complete (ID Scanned)
                currentStep = 2;
                document.getElementById('modalTitle').innerText = "Step 2: Scan Key";
                document.getElementById('modalIcon').innerText = "🔑";
                document.getElementById('modalMsg').innerText = "Success! Now scan the physical key to finish.";
            } else if (currentStep === 2) {
                // Step 2 Complete (Key Scanned)
                clearInterval(pollTimer);
                // Redirect to the actual return processing script
                window.location.href = "booking_return.php?id=" + activeBookingId + "&uid=" + data.uid;
            }
        }
    })
    .catch(err => console.error("Error checking scan:", err));
}
</script>

</body>
</html>