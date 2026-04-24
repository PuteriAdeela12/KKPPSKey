<?php

function generateAssetCode(PDO $conn): string
{
    $prefix = "KKPPS";
    $year   = date('Y');

    // Cari kod terbesar tahun semasa
    $stmt = $conn->prepare("
        SELECT kod_aset 
        FROM barang_list 
        WHERE kod_aset LIKE ?
        ORDER BY barang_id DESC 
        LIMIT 1
    ");

    $like = $prefix . "-" . $year . "-%";
    $stmt->execute([$like]);

    $last = $stmt->fetchColumn();

    if ($last) {
        $parts = explode('-', $last);
        $number = (int)end($parts);
        $number++;
    } else {
        $number = 1;
    }

    return sprintf("%s-%s-%04d", $prefix, $year, $number);
}