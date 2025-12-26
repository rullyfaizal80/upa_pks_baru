<?php

if (!function_exists('hitungPeriodeMingguan')) {
    /**
     * Menghitung tanggal Senin (Mulai) dan Ahad (Selesai)
     * berdasarkan tanggal input tertentu.
     * * @param string $tanggalInput Format Y-m-d (Misal: 2025-12-25)
     * @return array ['mulai' => '...', 'selesai' => '...']
     */
    function hitungPeriodeMingguan($tanggalInput = null)
    {
        // 1. Jika tidak ada input, pakai hari ini
        if (!$tanggalInput) {
            $tanggalInput = date('Y-m-d');
        }

        $timestamp = strtotime($tanggalInput);

        // 2. Cek hari apa tanggal tersebut (1=Senin, 7=Minggu)
        $hariKe = date('N', $timestamp);

        // 3. Logika Mencari Senin
        if ($hariKe == 1) {
            // Jika hari ini sudah Senin, maka ini tanggal mulainya
            $start = date('Y-m-d', $timestamp);
        } else {
            // Jika bukan Senin, cari "Last Monday" (Senin terakhir)
            $start = date('Y-m-d', strtotime('last monday', $timestamp));
        }

        // 4. Logika Mencari Ahad (Senin + 6 hari)
        $end = date('Y-m-d', strtotime($start . ' +6 days'));

        return [
            'mulai'   => $start,
            'selesai' => $end
        ];
    }
}