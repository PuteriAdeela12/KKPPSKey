<?php
session_start();

include "db.php";

/* =========================
   STATUS FUNCTION (Includes Holder Validation Data)
========================= */
function getKeyInfo(PDO $conn, int $key_id): array
{
    // Fetch the most recent active booking for this key
    $stmt = $conn->prepare("
        SELECT booking_id, end_datetime, borrower_name, card_uid
        FROM bookings
        WHERE room_id = ?
          AND status = 'BOOKED'
        ORDER BY end_datetime ASC
        LIMIT 1
    ");
    $stmt->execute([$key_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        return [
            'status' => 'AVAILABLE',
            'borrower' => null,
            'booking_id' => null,
            'holder_id' => null
        ];
    }

    // Determine if it's Overdue or Booked
    $currentTime = new DateTime();
    $endTime = new DateTime($row['end_datetime']);
    
    $status = ($currentTime > $endTime) ? 'OVERDUE' : 'BOOKED';

    return [
        'status' => $status,
        'borrower' => $row['borrower_name'],
        'booking_id' => $row['booking_id'],
        'holder_id' => $row['card_uid'] // This is the ID that must match during return
    ];
}

/* =========================
   SEARCH
========================= */
$search = $_GET['search'] ?? '';

if ($search !== '') {
    $stmt = $conn->prepare("
        SELECT * FROM key_list
        WHERE key_code LIKE :s OR room_name LIKE :s
        ORDER BY key_id ASC
    ");
    $stmt->execute(['s' => "%$search%"]);
} else {
    $stmt = $conn->query("SELECT * FROM key_list ORDER BY key_id ASC");
}
$keys = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* =========================
   AUTOCOMPLETE
========================= */
$suggestStmt = $conn->query("
    SELECT DISTINCT key_code AS val FROM key_list
    UNION
    SELECT DISTINCT room_name FROM key_list
    ORDER BY val
");
$suggestions = $suggestStmt->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KKPPSKey - Management</title>
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
        .suggest-box {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            z-index: 50;
            display: none;
        }
        .suggest-item {
            padding: 0.75rem 1rem;
            cursor: pointer;
            transition: background 0.2s;
        }
        .suggest-item:hover { background: #f1f5f9; }
        
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

<nav class="navbar-gradient py-4 px-6 shadow-lg mb-8">
    <div class="max-w-7xl mx-auto flex justify-between items-center">
        <div class="flex items-center gap-3">
            <div class="bg-white p-2 rounded-lg shadow-sm">
                <img src="assets/kkppskeynobg.png" alt="Logo" class="h-8 w-auto" onerror="this.src='https://cdn-icons-png.flaticon.com/512/2830/2830305.png'">
            </div>
            <span class="text-white font-extrabold text-lg tracking-tight">KKPPSKEY</span>
        </div>
    </div>
</nav>

<main class="container mx-auto px-4 max-w-7xl pb-12">
    
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h2 class="text-3xl font-black text-blue-900">Senarai Kunci</h2>
            <p class="text-slate-500">Pantau status kunci setiap bilik kolej</p>
        </div>
        
        <div class="flex flex-wrap gap-2">
            <a href="booking_today.php" class="bg-blue-100 text-blue-700 px-4 py-2 rounded-xl text-sm font-bold hover:bg-blue-200 transition">Today</a>
            <a href="booking_daily_log.php" class="bg-slate-100 text-slate-700 px-4 py-2 rounded-xl text-sm font-bold hover:bg-slate-200 transition">Daily</a>
            <a href="booking_weekly.php" class="bg-slate-100 text-slate-700 px-4 py-2 rounded-xl text-sm font-bold hover:bg-slate-200 transition">Weekly</a>
            <a href="booking_monthly.php" class="bg-slate-100 text-slate-700 px-4 py-2 rounded-xl text-sm font-bold hover:bg-slate-200 transition">Monthly</a>
        </div>
    </div>

    <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 mb-8">
        <form method="GET" class="flex flex-col md:flex-row gap-3 relative" autocomplete="off">
            <div class="relative flex-grow">
                <input type="text" id="searchInput" name="search" 
                       placeholder="Cari Nama Bilik..." 
                       value="<?= htmlspecialchars($search) ?>"
                       class="w-full pl-4 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                <div id="suggestBox" class="suggest-box"></div>
            </div>
            <button class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-2xl font-bold shadow-lg shadow-blue-200 transition">Search</button>
            <a href="kunci_index.php" class="bg-slate-100 hover:bg-slate-200 text-slate-600 px-6 py-3 rounded-2xl font-bold text-center transition">Reset</a>
        </form>
    </div>

    <!-- Table -->
    <div class="hidden md:block overflow-hidden bg-white rounded-[2rem] shadow-sm border border-slate-100">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50 border-bottom border-slate-100 text-slate-500 uppercase text-xs font-bold tracking-wider">
                    <th class="px-6 py-4">ID</th>
                    <th class="px-6 py-4">Nama Bilik</th>
                    <th class="px-6 py-4">Remarks</th>
                    <th class="px-6 py-4">Status & Peminjam</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php foreach ($keys as $k): ?>
                <?php 
                    $info = getKeyInfo($conn, (int)$k['key_id']); 
                    $status = $info['status'];
                    $borrower = $info['borrower'];
                    $b_id = $info['booking_id'];
                    $h_id = $info['holder_id'];
                ?>
                <tr class="hover:bg-blue-50/50 transition">
                    <td class="px-6 py-4 text-slate-400 font-medium text-sm">#<?= $k['key_id'] ?></td>
                    <td class="px-6 py-4 font-bold text-blue-900"><?= htmlspecialchars($k['room_name']) ?></td>
                    <td class="px-6 py-4 text-slate-400 text-sm italic"><?= htmlspecialchars($k['remarks'] ?: '-') ?></td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col items-start gap-2">
                            <?php if ($status === 'AVAILABLE'): ?>
                                <button onclick="openBorrowModal(<?= $k['key_id'] ?>)" 
                                        class="bg-green-100 text-green-700 px-4 py-1.5 rounded-full font-extrabold text-[10px] uppercase tracking-wider hover:bg-green-200 transition">
                                    AVAILABLE
                                </button>
                            <?php else: ?>
                                <?php 
                                    $badgeColor = ($status === 'OVERDUE') ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-orange-100 text-orange-700 hover:bg-orange-200';
                                    $textColor = ($status === 'OVERDUE') ? 'text-red-600' : 'text-orange-600';
                                ?>
                                <button onclick="openReturnModal(<?= $b_id ?>, '<?= $h_id ?>')" 
                                   class="<?= $badgeColor ?> px-4 py-1.5 rounded-full font-extrabold text-[10px] uppercase tracking-wider transition">
                                    <?= $status ?>
                                </button>
                                <div class="flex items-center gap-1.5 px-2 py-1 bg-slate-50 rounded-lg border border-slate-100">
                                    <svg class="w-3 h-3 <?= $textColor ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <span class="text-[11px] font-bold <?= $textColor ?> truncate max-w-[150px]">
                                        <?= htmlspecialchars($borrower) ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Mobile View -->
    <div class="md:hidden space-y-4">
        <?php foreach ($keys as $k): ?>
        <?php 
            $info = getKeyInfo($conn, (int)$k['key_id']); 
            $status = $info['status'];
            $borrower = $info['borrower'];
            $b_id = $info['booking_id'];
            $h_id = $info['holder_id'];
        ?>
        <div class="bg-white p-5 rounded-3xl shadow-sm border border-slate-100">
            <div class="flex justify-between items-start mb-3">
                <div>
                    <div class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">ID #<?= $k['key_id'] ?></div>
                    <div class="text-xl font-black text-blue-900"><?= htmlspecialchars($k['room_name']) ?></div>
                </div>
                <span class="<?= ($status === 'AVAILABLE') ? 'bg-green-100 text-green-700' : (($status === 'OVERDUE') ? 'bg-red-100 text-red-700' : 'bg-orange-100 text-orange-700') ?> px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider"><?= $status ?></span>
            </div>
            
            <div class="text-slate-400 text-sm mb-4"><?= htmlspecialchars($k['remarks'] ?: 'No remarks') ?></div>
            
            <?php if ($borrower): ?>
                <div class="mb-5 bg-blue-50/50 p-3 rounded-2xl border border-blue-100 flex items-center gap-3">
                    <div class="<?= ($status === 'OVERDUE') ? 'bg-red-100 text-red-600' : 'bg-blue-100 text-blue-600' ?> p-2 rounded-xl">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-tight leading-none mb-1">Peminjam Terkini</p>
                        <p class="text-sm font-bold text-blue-900 leading-tight"><?= htmlspecialchars($borrower) ?></p>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if ($status === 'AVAILABLE'): ?>
                <button onclick="openBorrowModal(<?= $k['key_id'] ?>)" 
                        class="w-full bg-blue-600 text-white py-3.5 rounded-2xl font-bold shadow-lg shadow-blue-100 transition">
                    Pinjam Kunci
                </button>
            <?php else: ?>
                <button onclick="openReturnModal(<?= $b_id ?>, '<?= $h_id ?>')" 
                        class="w-full bg-slate-100 text-slate-700 py-3.5 rounded-2xl font-bold transition">
                    Pulangkan Kunci
                </button>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
</main>

<!-- Universal Scan Modal -->
<div id="scanModal" class="hidden fixed inset-0 bg-blue-900/40 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-[2.5rem] p-10 max-w-sm w-full text-center shadow-2xl border-t-8 border-yellow-400">
        <div class="relative inline-block mb-6">
            <div id="modalIcon" class="text-6xl animate-bounce">💳</div>
            <div id="statusDot" class="absolute -bottom-1 -right-1 bg-green-500 w-6 h-6 rounded-full border-4 border-white"></div>
        </div>
        <h3 id="modalTitle" class="text-2xl font-black text-blue-900 mb-2">Scan Your ID Card</h3>
        <p id="modalMsg" class="text-slate-500 mb-8 text-sm leading-relaxed">Sila imbas kad ID anda pada scanner untuk meneruskan.</p>
        
        <div class="bg-blue-50 rounded-3xl p-6 mb-6">
            <div class="loader mx-auto"></div>
            <div id="modalPulse" class="text-xs font-bold text-blue-600 animate-pulse uppercase tracking-widest mt-4">Menunggu imbasan...</div>
        </div>
        
        <button onclick="closeModal()" class="w-full text-slate-400 font-bold text-sm hover:text-slate-600 transition">Batalkan</button>
    </div>
</div>

<footer class="mt-auto text-center py-8 text-slate-400 text-[10px] font-bold uppercase tracking-widest">
    &copy; <?= date('Y') ?> KKPPS System • Developed for Efficiency
</footer>

<script>
/* =========================
   AUTOCOMPLETE LOGIC
========================= */
const suggestions = <?= json_encode($suggestions) ?>;
const input = document.getElementById("searchInput");
const box = document.getElementById("suggestBox");

if (input) {
    input.addEventListener("input", () => {
        const val = input.value.toLowerCase();
        box.innerHTML = "";
        if (!val) { box.style.display="none"; return; }
        const matches = suggestions.filter(s => s.toLowerCase().includes(val)).slice(0,8);
        if (!matches.length) { box.style.display="none"; return; }
        matches.forEach(item=>{
            const d=document.createElement("div");
            d.className="suggest-item";
            d.textContent=item;
            d.onclick=()=>{ input.value=item; box.style.display="none"; };
            box.appendChild(d);
        });
        box.style.display="block";
    });
}

/* =========================
   SCAN MODAL LOGIC (BORROW & RETURN)
========================= */
let scanTimer = null;
let currentRoomId = null;
let activeBookingId = null;
let expectedHolderId = null;
let currentStep = 1; // 1: Scan ID, 2: Scan Key
let mode = 'borrow'; // 'borrow' or 'return'

function openBorrowModal(roomId) {
    mode = 'borrow';
    currentRoomId = roomId;
    resetModalUI("Scan ID Card", "💳", "Sila imbas kad ID anda pada scanner untuk meminjam kunci.");
    document.getElementById('scanModal').classList.remove('hidden');
    scanTimer = setInterval(pollScan, 1500);
}

function openReturnModal(bookingId, holderId) {
    mode = 'return';
    activeBookingId = bookingId;
    expectedHolderId = holderId;
    currentStep = 1;
    resetModalUI("Return: Step 1", "🪪", "Sila imbas kad ID anda (Peminjam Asal) untuk pemulangan.");
    document.getElementById('scanModal').classList.remove('hidden');
    scanTimer = setInterval(pollScan, 1500);
}

function resetModalUI(title, icon, msg) {
    document.getElementById('modalTitle').innerText = title;
    document.getElementById('modalIcon').innerText = icon;
    document.getElementById('modalMsg').innerText = msg;
    document.getElementById('modalPulse').innerText = "Menunggu imbasan...";
    document.getElementById('statusDot').className = "absolute -bottom-1 -right-1 bg-green-500 w-6 h-6 rounded-full border-4 border-white";
}

function closeModal() {
    document.getElementById('scanModal').classList.add('hidden');
    if (scanTimer) clearInterval(scanTimer);
}

function pollScan() {
    fetch('check_scan.php')
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success' && data.uid) {
            if (mode === 'borrow') {
                clearInterval(scanTimer);
                window.location.href = `booking_form.php?room_id=${currentRoomId}&uid=${data.uid}`;
            } else {
                handleReturnSteps(data.uid);
            }
        }
    })
    .catch(err => console.log("Waiting for scanner..."));
}

function handleReturnSteps(scannedUid) {
    const normalizedScanned = String(scannedUid).trim().toLowerCase();
    const normalizedExpected = String(expectedHolderId).trim().toLowerCase();

    console.log("Current Step:", currentStep);
    console.log("Scanned:", normalizedScanned, "Expected:", normalizedExpected);

    if (currentStep === 1) {
        if (normalizedScanned === normalizedExpected) {
            // SUCCESS: ID Matches
            currentStep = 2;
            document.getElementById('modalTitle').innerText = "Return: Step 2";
            document.getElementById('modalIcon').innerText = "🔑";
            document.getElementById('modalMsg').innerText = "ID Sah! Sekarang, sila imbas KUNCI fizikal.";
            document.getElementById('modalPulse').innerText = "Menunggu imbasan KUNCI...";
            
            // Critical: We stop the timer for 2 seconds to allow the user 
            // to swap the ID card for the Key on the reader.
            clearInterval(scanTimer);
            setTimeout(() => {
                scanTimer = setInterval(pollScan, 1500);
            }, 2000);

        } else {
            // FAIL: Wrong Card
            showScanError("ID tidak sepadan dengan peminjam asal (" + scannedUid + ")");
        }
    } else if (currentStep === 2) {
        // FINAL: Key is scanned
        // We assume the second scan is the key. 
        // You might want to check if scannedUid != expectedHolderId to ensure it's a different tag
        clearInterval(scanTimer);
        window.location.href = `booking_return.php?id=${activeBookingId}&uid=${scannedUid}`;
    }
}

function showScanError(msg) {
    const msgEl = document.getElementById('modalMsg');
    const originalMsg = msgEl.innerText;
    msgEl.innerText = msg;
    msgEl.classList.add('text-red-500');
    
    setTimeout(() => {
        msgEl.innerText = originalMsg;
        msgEl.classList.remove('text-red-500');
    }, 3000);
}

document.addEventListener("click",e=>{
    if(box && !box.contains(e.target) && e.target !== input){
        box.style.display="none";
    }
});
</script>

</body>
</html>