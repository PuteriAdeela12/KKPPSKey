<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$booking_id = (int)($_GET['id'] ?? 0);
if ($booking_id === 0) {
    die("Booking tidak sah");
}

/* GET BOOKING (BOOKED SAHAJA) */
$stmt = $conn->prepare("
    SELECT * FROM bookings
    WHERE booking_id=? AND status='BOOKED'
");
$stmt->execute([$booking_id]);
$booking = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$booking) {
    die("Rekod booking tidak dijumpai atau sudah dipulangkan");
}

/* PERMISSION */
$bolehReturn =
    $_SESSION['role'] === 'admin' ||
    (
        $booking['borrower_type'] === 'staff' &&
        $booking['staff_id'] == ($_SESSION['staff_id'] ?? 0)
    );

if (!$bolehReturn) {
    die("Anda tidak dibenarkan memulangkan kunci ini");
}

/* RETURN */
$stmt = $conn->prepare("
    UPDATE bookings
    SET 
        status = 'RETURNED',
        overdue_notified = 0
    WHERE booking_id = ?
");
$stmt->execute([$booking_id]);

?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; }
        /* Matching your modal style */
        .swal2-popup {
            border-radius: 2.5rem !important;
            padding: 2rem !important;
            border-top: 8px solid #fbbf24 !important; /* Yellow border like your scan modal */
        }
        .swal2-title {
            color: #1e3a8a !important; /* Blue-900 */
            font-weight: 800 !important;
        }
        .swal2-confirm {
            background-color: #2563eb !important; /* Blue-600 */
            border-radius: 1rem !important;
            padding: 0.75rem 2rem !important;
            font-weight: 700 !important;
        }
    </style>
</head>
<body>
    <script>
        Swal.fire({
            title: 'Berjaya!',
            text: 'Kunci telah berjaya dipulangkan ke sistem.',
            icon: 'success',
            iconColor: '#22c55e',
            confirmButtonText: 'KEMBALI KE SENARAI',
            allowOutsideClick: false
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirect back to the main management index
                window.location.href = 'kunci_index.php';
            }
        });
    </script>
</body>
</html>
<?php
exit;