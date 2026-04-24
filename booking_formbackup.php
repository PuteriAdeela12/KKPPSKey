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

/* =========================
   VALIDATE ROOM ID
========================= */
$room_id = $_GET['room_id'] ?? 0;
if ($room_id == 0) {
  die("Room tidak sah");
}

/* =========================
   GET STAFF LIST
========================= */
$stmt = $conn->query("
  SELECT staff_id, staff_name, department, phone
  FROM staff_name
  ORDER BY staff_name
");
$staffs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Booking Form</title>

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

body {
  font-family: Arial, sans-serif;
  background:#f5f5f5;
}

.container {
  max-width:600px;
  margin:30px auto;
  background:#fff;
  padding:24px;
  border-radius:8px;
}

label {
  margin-top:14px;
  display:block;
  font-weight:bold;
}

input,
select,
textarea {
  width:100%;
  box-sizing:border-box;
  padding:10px;
  margin-top:6px;
  font-size:14px;
}

textarea {
  min-height:90px;
  resize:vertical;
}

input[readonly] {
  background:#eee;
}

button {
  margin-top:24px;
  padding:12px;
  width:100%;
  background:#2e7d32;
  color:white;
  border:none;
  font-size:16px;
  border-radius:4px;
  cursor:pointer;
}

button:hover {
  background:#256428;
}
</style>
</head>

<body>

<div class="container">
<h2>Booking Form</h2>

<form method="post" action="booking_save.php" id="bookingForm">

  <!-- ROOM -->
  <input type="hidden" name="room_id" value="<?= $room_id ?>">

  <!-- NAMA PEMINJAM -->
  <label>Nama Peminjam</label>
  <select name="borrower_select" id="borrower_select" required>
    <option value="">-- Pilih --</option>

    <?php foreach ($staffs as $s): ?>
      <option value="staff_<?= $s['staff_id'] ?>"
              data-phone="<?= htmlspecialchars($s['phone']) ?>">
        <?= htmlspecialchars($s['staff_name']) ?>
        <?= $s['department'] ? "({$s['department']})" : "" ?>
      </option>
    <?php endforeach; ?>

    <option value="others">Others</option>
  </select>

  <!-- OTHERS NAME -->
  <div id="others_name_wrap" style="display:none;">
    <label>Nama Peminjam (Others)</label>
    <input type="text" name="borrower_name_others" id="borrower_name_others">
  </div>

  <!-- PHONE -->
  <label>No. Telefon</label>
  <input type="text" name="phone" id="phone" readonly>

  <!-- JENIS TEMPAHAN -->
  <label>Jenis Tempahan</label>
  <select name="booking_type" id="booking_type">
    <option value="time_slot">Time Slot</option>
    <option value="whole_day">Whole Day</option>
  </select>

  <!-- TARIKH -->
  <label>Tarikh Tempahan</label>
  <input type="date" name="booking_date" id="booking_date" required>

  <!-- MASA -->
  <div id="time_wrap">
    <label>Masa Mula</label>
    <input type="time" name="start_time" id="start_time">

    <label>Masa Tamat</label>
    <input type="time" name="end_time" id="end_time">
  </div>

  <!-- TUJUAN -->
  <label>Tujuan</label>
  <textarea name="purpose" required></textarea>

  <button type="submit">Simpan Booking</button>

</form>
</div>

<script>
/* =========================
   DEFAULT DATE & TIME
========================= */
document.addEventListener("DOMContentLoaded", () => {
  const today = new Date();
  const yyyyMmDd = today.toISOString().split("T")[0];

  document.getElementById("booking_date").value = yyyyMmDd;
  document.getElementById("booking_date").min = yyyyMmDd;

  const pad = n => n.toString().padStart(2, '0');
  const h = today.getHours();
  const m = today.getMinutes();

  document.getElementById("start_time").value = `${pad(h)}:${pad(m)}`;
  document.getElementById("end_time").value   = `${pad(h+1)}:${pad(m)}`;
});

/* =========================
   BORROWER CHANGE
========================= */
document.getElementById("borrower_select").addEventListener("change", e => {
  const othersWrap = document.getElementById("others_name_wrap");
  const othersName = document.getElementById("borrower_name_others");
  const phoneInput = document.getElementById("phone");

  if (e.target.value === "others") {
    othersWrap.style.display = "block";
    othersName.required = true;

    phoneInput.value = "";
    phoneInput.readOnly = false;
    phoneInput.required = true;
  } else {
    othersWrap.style.display = "none";
    othersName.required = false;
    othersName.value = "";

    const phone = e.target.selectedOptions[0].dataset.phone || "";
    phoneInput.value = phone;
    phoneInput.readOnly = true;
    phoneInput.required = true;
  }
});

/* =========================
   TOGGLE WHOLE DAY
========================= */
document.getElementById("booking_type").addEventListener("change", e => {
  const timeWrap = document.getElementById("time_wrap");
  timeWrap.style.display = (e.target.value === "whole_day") ? "none" : "block";
});

/* =========================
   BASIC VALIDATION
========================= */
document.getElementById("bookingForm").addEventListener("submit", e => {
  const type = document.getElementById("booking_type").value;

  if (type === "time_slot") {
    const start = document.getElementById("start_time").value;
    const end   = document.getElementById("end_time").value;

    if (!start || !end || start >= end) {
      e.preventDefault();
      alert("Masa tamat mesti selepas masa mula");
    }
  }
});
</script>

</body>
</html>
