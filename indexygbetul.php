<?php


session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include "db.php";
include "topbar_index.php";


/* =========================
   STATUS FUNCTION
========================= */
function getKeyStatus(PDO $conn, int $key_id): string
{
    // Ambil booking BOOKED yang PALING LAMA belum RETURN
    $stmt = $conn->prepare("
        SELECT MIN(end_datetime) AS first_end
        FROM bookings
        WHERE room_id = ?
          AND status = 'BOOKED'
    ");
    $stmt->execute([$key_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    // Tiada booking aktif
    if (!$row || !$row['first_end']) {
        return 'AVAILABLE';
    }

    // Banding guna masa MySQL sahaja
    $stmt = $conn->prepare("
        SELECT
            CASE
                WHEN ? <= CURRENT_TIMESTAMP THEN 'OVERDUE'
                ELSE 'BOOKED'
            END AS status
    ");
    $stmt->execute([$row['first_end']]);
    $res = $stmt->fetch(PDO::FETCH_ASSOC);

    return $res['status'];
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
<html>
<head>
<meta charset="UTF-8">
<title>Key Management</title>

<style>
/* ===== TOP BAR ===== */
.topbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:16px;
}

.topbar-left{
    font-size:13px;
    color:#475569;
}

.btn-logout{
    padding:6px 14px;
    background:#ef4444;
    color:#fff;
    border-radius:6px;
    text-decoration:none;
    font-weight:bold;
    font-size:13px;
}

.btn-logout:hover{
    background:#dc2626;
}

/* SEMBUNYI MASA PRINT */
@media print{
    .topbar{display:none;}
}

body { font-family: Arial; background:#f5f7fa; padding:20px; }

/* MENU */
.menu {
    display:flex;
    gap:10px;
    margin-bottom:20px;
}
.menu a {
    padding:10px 16px;
    background:#1e293b;
    color:#fff;
    border-radius:6px;
    text-decoration:none;
    font-weight:bold;
}
.menu a:hover { background:#334155; }

/* SEARCH */
.search-box {
    display:flex;
    gap:10px;
    margin-bottom:15px;
}
.search-wrapper { position:relative; }
.search-wrapper input {
    padding:10px;
    width:280px;
    border:1px solid #cbd5e1;
    border-radius:6px;
}
.btn-search {
    background:#2563eb;
    color:#fff;
    border:none;
    padding:10px 18px;
    border-radius:6px;
    font-weight:bold;
    cursor:pointer;
}
.btn-reset {
    background:#64748b;
    color:#fff;
    padding:10px 18px;
    border-radius:6px;
    text-decoration:none;
    font-weight:bold;
}

/* AUTOCOMPLETE */
.suggest-box {
    position:absolute;
    top:42px;
    left:0;
    right:0;
    background:#fff;
    border:1px solid #cbd5e1;
    border-radius:6px;
    max-height:180px;
    overflow-y:auto;
    display:none;
    z-index:10;
}
.suggest-item {
    padding:8px 10px;
    cursor:pointer;
}
.suggest-item:hover { background:#e0e7ff; }

/* TABLE */
table {
    width:100%;
    border-collapse:collapse;
    background:#fff;
    border-radius:8px;
    overflow:hidden;
}
th {
    background:#1e293b;
    color:#fff;
    padding:10px;
}
td {
    padding:9px;
    border-bottom:1px solid #e5e7eb;
}
tr:nth-child(even){ background:#f8fafc; }
tr:hover{ background:#eef2ff; }

/* STATUS */
.status {
    padding:5px 14px;
    border-radius:14px;
    font-weight:bold;
    display:inline-block;
}
.AVAILABLE{background:#dcfce7;color:#166534;}
.BOOKED{background:#fff7ed;color:#9a3412;}
.OVERDUE{background:#fee2e2;color:#991b1b;}
a.status { text-decoration:none; }
</style>
</head>

<body>

<h2>🔑 Senarai Kunci</h2>

<!-- MENU -->
<div class="menu">
    	<a href="booking_today.php">Booking Today</a>
	<a href="booking_daily_log.php">Daily Log</a>
	<a href="booking_weekly.php">Weekly Log</a>
	<a href="booking_monthly.php">Monthly Log</a>

</div>

<!-- SEARCH -->
<form method="GET" class="search-box" autocomplete="off">
    <div class="search-wrapper">
        <input type="text" id="searchInput" name="search"
               placeholder="Cari Kod Kunci / Nama Bilik"
               value="<?= htmlspecialchars($search) ?>">
        <div id="suggestBox" class="suggest-box"></div>
    </div>
    <button class="btn-search">Search</button>
    <a href="index.php" class="btn-reset">Reset</a>
</form>

<!-- TABLE -->
<table>
<tr>
    <th>ID</th>
    <th>Kod Kunci</th>
    <th>Nama Bilik</th>
    <th>Remarks</th>
    <th>Status</th>
</tr>

<?php foreach ($keys as $k): ?>
<?php $status = getKeyStatus($conn, (int)$k['key_id']); ?>
<tr>
<td><?= $k['key_id'] ?></td>
<td><?= htmlspecialchars($k['key_code']) ?></td>
<td><?= htmlspecialchars($k['room_name']) ?></td>
<td><?= htmlspecialchars($k['remarks']) ?></td>
<td>
<?php if ($status === 'AVAILABLE'): ?>
<a class="status AVAILABLE"
   href="booking_form.php?room_id=<?= $k['key_id'] ?>">
   AVAILABLE
</a>
<?php else: ?>
<a class="status <?= $status ?>"
   href="booking_detail.php?room_id=<?= $k['key_id'] ?>">
   <?= $status ?>
</a>
<?php endif; ?>
</td>
</tr>
<?php endforeach; ?>
</table>

<script>
const suggestions = <?= json_encode($suggestions) ?>;
const input = document.getElementById("searchInput");
const box = document.getElementById("suggestBox");

input.addEventListener("input", () => {
    const val = input.value.toLowerCase();
    box.innerHTML = "";
    if (!val) { box.style.display="none"; return; }

    const matches = suggestions.filter(s =>
        s.toLowerCase().includes(val)
    ).slice(0,8);

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

document.addEventListener("click",e=>{
    if(!box.contains(e.target)&&e.target!==input){
        box.style.display="none";
    }
});
</script>

</body>
</html>
