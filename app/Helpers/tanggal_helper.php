<?php

if (!function_exists('hitungPeriodeMingguan')) {
    function hitungPeriodeMingguan($tanggalInput)
    {
        // Ubah ke timestamp
        $timestamp = strtotime($tanggalInput);
        
        // Cari hari Senin pada minggu tersebut (Monday this week)
        // Jika input hari minggu, 'monday this week' akan mundur ke senin sebelumnya
        // Logic: Kita anggap pekan dimulai Senin.
        
        $dayOfWeek = date('N', $timestamp); // 1 (Senin) - 7 (Minggu)
        
        // Hitung mundur ke Senin
        $selisihKeSenin = $dayOfWeek - 1; 
        $timestampSenin = strtotime("-$selisihKeSenin days", $timestamp);
        
        // Hitung maju ke Ahad (Senin + 6 hari)
        $timestampAhad = strtotime("+6 days", $timestampSenin);
        
        return [
            'mulai'   => date('Y-m-d', $timestampSenin),
            'selesai' => date('Y-m-d', $timestampAhad)
        ];
    }
}

if (!function_exists('formatTanggalIndo')) {
    function formatTanggalIndo($tanggal)
    {
        $bulanIndo = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        $pecahkan = explode('-', $tanggal); // yyyy-mm-dd
        
        return $pecahkan[2] . ' ' . $bulanIndo[ (int)$pecahkan[1] ] . ' ' . $pecahkan[0];
    }
}