<?php

function canReturn(array $booking): bool
{
    // Admin boleh return semua
    if ($_SESSION['role'] === 'admin') {
        return true;
    }

    // Staff hanya boleh return booking STAFF sendiri
    if (
        $booking['borrower_type'] === 'staff' &&
        isset($_SESSION['staff_id']) &&
        $_SESSION['staff_id'] !== null &&
        $booking['staff_id'] == $_SESSION['staff_id']
    ) {
        return true;
    }

    return false;
}
