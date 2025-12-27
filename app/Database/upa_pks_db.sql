-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 27, 2025 at 12:40 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `upa_pks`
--

-- --------------------------------------------------------

--
-- Table structure for table `anggota_kelompok`
--

CREATE TABLE `anggota_kelompok` (
  `id` int(11) NOT NULL,
  `kelompok_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `anggota_kelompok`
--

INSERT INTO `anggota_kelompok` (`id`, `kelompok_id`, `user_id`) VALUES
(1, 2, 2),
(2, 2, 6),
(3, 1, 7),
(5, 1, 8);

-- --------------------------------------------------------

--
-- Table structure for table `kelompok`
--

CREATE TABLE `kelompok` (
  `id` int(11) NOT NULL,
  `nama_kelompok` varchar(100) NOT NULL,
  `pembina_id` int(11) NOT NULL,
  `sekertaris_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kelompok`
--

INSERT INTO `kelompok` (`id`, `nama_kelompok`, `pembina_id`, `sekertaris_id`, `created_at`, `updated_at`) VALUES
(1, 'Kelompok 1', 5, 22, '2025-12-26 00:55:16', '2025-12-27 10:25:31'),
(2, 'Kelompok 2', 5, 3, '2025-12-26 00:55:35', '2025-12-27 10:25:19'),
(3, 'Kelompok 3', 12, 22, '2025-12-27 01:31:13', '2025-12-27 01:31:13');

-- --------------------------------------------------------

--
-- Table structure for table `laporan_amalan`
--

CREATE TABLE `laporan_amalan` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `kelompok_id` int(11) NOT NULL,
  `tanggal_upa` date NOT NULL COMMENT 'Tanggal pertemuan UPA',
  `periode_mulai` date NOT NULL COMMENT 'Senin',
  `periode_selesai` date NOT NULL COMMENT 'Ahad',
  `amalan_1` int(11) DEFAULT 0 COMMENT 'Sholat Jamaah',
  `amalan_2` int(11) DEFAULT 0 COMMENT 'Qiyamul Lail',
  `amalan_3` float DEFAULT 0 COMMENT 'Tilawah (Juz)',
  `amalan_4` int(11) DEFAULT 0 COMMENT 'Shaum',
  `amalan_5` int(11) DEFAULT 0 COMMENT 'Matsurat',
  `amalan_6` int(11) DEFAULT 0 COMMENT 'Dhuha',
  `amalan_7` int(11) DEFAULT 0 COMMENT 'Olahraga',
  `amalan_8` int(11) DEFAULT 0 COMMENT 'Istighfar',
  `amalan_9` int(11) DEFAULT 0 COMMENT 'Shalawat',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `laporan_amalan`
--

INSERT INTO `laporan_amalan` (`id`, `user_id`, `kelompok_id`, `tanggal_upa`, `periode_mulai`, `periode_selesai`, `amalan_1`, `amalan_2`, `amalan_3`, `amalan_4`, `amalan_5`, `amalan_6`, `amalan_7`, `amalan_8`, `amalan_9`, `created_at`, `updated_at`) VALUES
(2, 6, 2, '2025-12-01', '2025-12-01', '2025-12-07', 1, 4, 3, 2, 4, 3, 4, 5, 5, '2025-12-26 11:12:37', '2025-12-26 11:12:37'),
(4, 6, 2, '2026-01-08', '2026-01-05', '2026-01-11', 5, 4, 3, 2, 7, 5, 3, 4, 6, '2025-12-26 11:40:28', '2025-12-26 11:40:28'),
(5, 6, 2, '2025-11-17', '2025-11-17', '2025-11-23', 5, 4, 3, 2, 7, 5, 3, 4, 6, '2025-12-26 11:40:28', '2025-12-26 11:40:28'),
(6, 6, 2, '2025-12-22', '2025-12-22', '2025-12-28', 5, 5, 3, 0, 6, 2, 3, 4, 4, '2025-12-26 13:29:11', '2025-12-26 13:29:11'),
(7, 6, 2, '2025-12-10', '2025-12-08', '2025-12-14', 35, 4, 3.5, 2, 5, 3, 3, 3, 3, '2025-12-27 03:40:12', '2025-12-27 03:40:12');

-- --------------------------------------------------------

--
-- Table structure for table `laporan_upa`
--

CREATE TABLE `laporan_upa` (
  `id` int(11) NOT NULL,
  `kelompok_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT 'ID User yang mengisi laporan (Reporter)',
  `pembina_id` int(11) NOT NULL COMMENT 'Snapshot ID Pembina saat laporan dibuat',
  `sekertaris_id` int(11) NOT NULL COMMENT 'Snapshot ID Sekertaris saat laporan dibuat',
  `tanggal` date NOT NULL,
  `teknis_pelaksanaan` enum('Offline','Online','Hybrid') NOT NULL DEFAULT 'Offline',
  `is_pembina_hadir` tinyint(1) NOT NULL DEFAULT 1,
  `total_anggota` int(5) NOT NULL,
  `total_hadir` int(5) NOT NULL,
  `nama_tidak_hadir` text DEFAULT NULL,
  `materi` text NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `laporan_upa`
--

INSERT INTO `laporan_upa` (`id`, `kelompok_id`, `user_id`, `pembina_id`, `sekertaris_id`, `tanggal`, `teknis_pelaksanaan`, `is_pembina_hadir`, `total_anggota`, `total_hadir`, `nama_tidak_hadir`, `materi`, `created_at`, `updated_at`) VALUES
(4, 2, 3, 5, 3, '2025-12-01', 'Online', 1, 2, 2, '', 'cvb', '2025-12-27 08:55:13', '2025-12-27 09:13:48'),
(5, 1, 5, 5, 22, '2025-12-05', 'Offline', 1, 2, 2, '', 'dd', '2025-12-27 09:16:12', '2025-12-27 09:16:12'),
(6, 2, 3, 5, 3, '2025-12-17', 'Offline', 1, 2, 2, '', 'z', '2025-12-27 09:19:29', '2025-12-27 09:19:29');

-- --------------------------------------------------------

--
-- Table structure for table `pengumuman`
--

CREATE TABLE `pengumuman` (
  `id` int(11) UNSIGNED NOT NULL,
  `judul` varchar(255) NOT NULL,
  `isi` text NOT NULL,
  `tanggal` datetime DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengumuman`
--

INSERT INTO `pengumuman` (`id`, `judul`, `isi`, `tanggal`, `created_by`) VALUES
(2, '1. Undangan Mabit Gabungan Akhir Tahun', 'Assalamu’alaikum Warahmatullahi Wabarakatuh,\r\n\r\nIkhwah/akhwat sekalian, insya Allah kita akan mengadakan Mabit (Malam Bina Iman dan Taqwa) Gabungan pada:\r\n\r\nHari/Tgl: Sabtu, 30 Desember 2025\r\nWaktu: 16.00 WIB s/d Selesai\r\nTempat: Masjid Agung Al-Ukhuwah\r\n\r\nAgenda: Qiyamul Lail & Muhasabah Akhir Tahun.\r\nMohon kehadirannya tepat waktu dan membawa perlengkapan sholat serta obat-obatan pribadi.', '2025-12-27 08:00:00', 0),
(3, 'Perubahan Jadwal Liqo Pekanan', 'Diberitahukan kepada seluruh anggota Kelompok 03 (Pak Budi), dikarenakan ada agenda Daurah Wilayah, maka jadwal Liqo pekan ini digeser menjadi:\n\nHari: Ahad Sore\nJam: 16.00 WIB\nTempat: Rumah Pak Budi\n\nSyukron atas perhatiannya.', '2025-12-26 19:30:00', 0),
(4, 'Rihlah Keluarga & Outbound', 'Untuk mempererat ukhuwah antar anggota dan keluarga, kita akan mengadakan Rihlah ke Kebun Raya.\n\nBiaya kontribusi: Rp 50.000/orang (Subsidi Kas).\nPendaftaran terakhir tanggal 5 Januari 2026 melalui ketua kelompok masing-masing.\n\nJangan lupa ajak keluarga!', '2025-12-25 10:15:00', 0),
(5, 'Update Fitur Aplikasi UPA v2.0', 'Alhamdulillah, aplikasi UPA telah diupdate. Sekarang antum bisa melihat grafik kehadiran (Statistik) dan melakukan pengisian laporan yaumiyah dengan tampilan yang lebih ramah di HP.\n\nJika ada kendala saat login atau error, silakan lapor ke Admin IT.', '2025-12-24 09:00:00', 0),
(6, 'Himbauan Infaq Palestina', 'Mengingat kondisi saudara kita yang sedang membutuhkan bantuan, kami membuka dompet donasi khusus.\n\nBagi ikhwah yang memiliki kelebihan rezeki, bisa menyalurkannya via Bendahara Kelompok atau transfer ke Rekening Yayasan dengan kode unik 001.\n\nJazakumullah Khairan Katsiran.', '2025-12-23 14:45:00', 0),
(9, '🚀 Selamat Datang di Aplikasi UPA Digital!', 'Assalamu’alaikum Warahmatullahi Wabarakatuh,\n\nAhlan wa Sahlan! Alhamdulillah, mulai hari ini kita resmi menggunakan **Aplikasi UPA Digital** untuk menunjang kegiatan pembinaan kita.\n\nAplikasi ini hadir untuk memudahkan antum dalam:\n✅ **Mengisi Laporan Yaumiyah** secara online (bisa lewat HP).\n✅ **Monitoring Kelompok** bagi Ketua dan Pembina secara real-time.\n✅ **Melihat Statistik** perkembangan keaktifan.\n\nSilakan mulai dengan melengkapi **Profil** antum dan mencoba fitur **Laporan**. Jika menemukan kendala atau error, mohon segera hubungi Tim Admin.\n\nSemoga aplikasi ini membawa keberkahan dan semangat baru bagi kita semua. Selamat beraktivitas!\n\nWassalamu’alaikum,\nTim Pengembang', '2025-12-27 18:34:17', 0);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `role_name`, `description`) VALUES
(1, 'admin', 'Administrator Sistem'),
(2, 'ketua', 'Ketua Cabang'),
(3, 'sekertaris', 'Sekertaris Kelompok'),
(4, 'pembina', 'Ketua Kelompok'),
(5, 'anggota', 'Anggota Kelompok');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `gender` enum('L','P') NOT NULL,
  `jenjang` enum('Muda','Pratama') NOT NULL DEFAULT 'Muda',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `nama`, `gender`, `jenjang`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$cVtG6BLd1HyBClJgPy1AjesyfHp5iRfXjoF26VloHuez3mzgISmLC', 'Administrator', 'L', 'Muda', '2025-12-15 15:20:32', '2025-12-16 09:17:06'),
(2, 'anggota', '$2y$10$wf1SFn6EtxUxdsnpgqbAneUDPE0TaRE5yJ3aWdzNEi7KK1cnRh4wC', 'Anggota', 'L', 'Muda', '2025-12-15 15:20:32', '2025-12-26 16:18:56'),
(3, 'sekertaris', '$2y$10$cVtG6BLd1HyBClJgPy1AjesyfHp5iRfXjoF26VloHuez3mzgISmLC', 'Sekertaris', 'L', 'Pratama', '2025-12-15 15:20:32', '2025-12-17 13:38:43'),
(4, 'ketua', '$2y$10$cVtG6BLd1HyBClJgPy1AjesyfHp5iRfXjoF26VloHuez3mzgISmLC', 'Ketua', 'P', 'Pratama', '2025-12-15 15:20:32', '2025-12-26 07:36:13'),
(5, 'pembina', '$2y$10$MLWPeMqJ7eSU456k8KRJ8OHQxWfd7JMi/NU21wJqVYHRS2oRCRWCW', 'Pembina', 'L', 'Pratama', '2025-12-15 15:20:32', '2025-12-26 08:49:17'),
(6, 'anggota2', '$2y$10$WQ14hOhPP7i.BqzSy7DM2.aSoCAs0aP0CAnKHZPBawHpGXWa088GO', 'Anggota2', 'L', 'Pratama', '2025-12-16 13:22:08', '2025-12-27 09:39:36'),
(7, 'anggota3', '$2y$10$rjVavbZrwhcnJJ9lONtj5.DJJNCO3TbGDurAnnlJ753ddVvo5kopq', 'Anggota3', 'L', 'Muda', '2025-12-17 13:27:35', '2025-12-26 07:36:56'),
(8, 'tes2', '$2y$10$eJATbIqcBReXmUWu/KFPcuMTmhk6NxiNQK2MpfGsBEEVVoUDVWR2G', 'tes2', 'P', 'Muda', '2025-12-19 07:56:47', '2025-12-19 07:56:47'),
(10, 'tes4', '$2y$10$8B.DdJT9EPFWFAaL9mZjiOjuBNW1vWNdcGRHdkANYe0M3Uj0PiYVq', 'tes4', 'P', 'Pratama', '2025-12-19 10:50:25', '2025-12-19 10:50:25'),
(12, 'pembina1', '$2y$10$/fGQSARxdLoEScfsAyoEieI94t1S8QZ9aF8aDYPX53.chXzI7/OXC', 'Pembina 1', 'L', 'Muda', '2025-12-19 11:06:43', '2025-12-19 11:06:43'),
(13, 'pembina2', '$2y$10$PSDf9WEHxi0e2A7mniQB7uMjE9E10/07qvEv6pFBWCEPLeyeh7EuC', 'Pembina 2', 'L', 'Muda', '2025-12-19 11:07:01', '2025-12-19 11:07:01'),
(14, 'pembina3', '$2y$10$HPw7KtFmC5CxZiyLmVCQPOETyTIZ2yJGoWUlK05WpW1vslszpIRHa', 'Pembina 3', 'P', 'Muda', '2025-12-19 11:07:17', '2025-12-19 11:07:17'),
(15, 'pembina4', '$2y$10$W0eamL/jw.rC5CngUP78t.Gyd7Qz4TGozR7aMVvCjFx53qt.a8TDi', 'Pembina 4', 'L', 'Muda', '2025-12-19 11:07:36', '2025-12-19 11:07:36'),
(16, 'pembina5', '$2y$10$cbVFXaYsC9PeCKdxQqIoAeLyif1EpoqnvcHWgYO8Mw.XUbjGWH8Cm', 'Pembina 5', 'P', 'Pratama', '2025-12-19 11:08:17', '2025-12-19 11:08:17'),
(17, 'pembina6', '$2y$10$GxGBZFLAtAMuXHInSdaDaev0ndvYQBSYojQeO/c4.mk1TH8HCwNli', 'Pembina 6', 'L', 'Muda', '2025-12-19 11:08:33', '2025-12-19 11:08:33'),
(18, 'pembina7', '$2y$10$pEyCqPFLU1iLbpAtQpFlh.6wtLidZ6W50uCv.mf0u9uSxkEzAmSCa', 'Pembina 7', 'L', 'Pratama', '2025-12-19 11:08:48', '2025-12-19 11:08:48'),
(19, 'pembina8', '$2y$10$biMeqdc.OlESoN3JQkHyY.iJdAGMqYU1ErYS/kfFdMaw8jjUaPvFu', 'Pembina 8', 'P', 'Muda', '2025-12-19 11:09:12', '2025-12-19 11:09:12'),
(22, 'rullyfaizal1', '$2y$10$.rIX/CfBRXiRLozh3X71KOwmu63F8vcYYl0jKNOFBoC.RujGejSnm', 'Rully Faizal', 'L', 'Muda', '2025-12-25 23:35:25', '2025-12-26 22:46:14');

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE `user_roles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`id`, `user_id`, `role_id`) VALUES
(1, 1, 1),
(2, 2, 5),
(3, 3, 3),
(8, 8, 5),
(10, 10, 5),
(12, 12, 4),
(13, 13, 4),
(14, 14, 4),
(15, 15, 4),
(16, 16, 4),
(17, 17, 4),
(18, 18, 4),
(19, 19, 4),
(31, 7, 5),
(33, 4, 2),
(34, 22, 3),
(39, 5, 4),
(40, 6, 5);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `anggota_kelompok`
--
ALTER TABLE `anggota_kelompok`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_anggotakelompok_kelompok` (`kelompok_id`),
  ADD KEY `fk_anggotakelompok_user` (`user_id`);

--
-- Indexes for table `kelompok`
--
ALTER TABLE `kelompok`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_kelompok_pembina` (`pembina_id`),
  ADD KEY `fk_kelompok_sekertaris` (`sekertaris_id`);

--
-- Indexes for table `laporan_amalan`
--
ALTER TABLE `laporan_amalan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_laporan_user` (`user_id`),
  ADD KEY `fk_laporan_kelompok` (`kelompok_id`);

--
-- Indexes for table `laporan_upa`
--
ALTER TABLE `laporan_upa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `laporan_kelompok_foreign` (`kelompok_id`),
  ADD KEY `laporan_user_foreign` (`user_id`),
  ADD KEY `laporan_pembina_foreign` (`pembina_id`),
  ADD KEY `laporan_sekertaris_foreign` (`sekertaris_id`);

--
-- Indexes for table `pengumuman`
--
ALTER TABLE `pengumuman`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_roles_user` (`user_id`),
  ADD KEY `fk_user_roles_role` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `anggota_kelompok`
--
ALTER TABLE `anggota_kelompok`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `kelompok`
--
ALTER TABLE `kelompok`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `laporan_amalan`
--
ALTER TABLE `laporan_amalan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `laporan_upa`
--
ALTER TABLE `laporan_upa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `pengumuman`
--
ALTER TABLE `pengumuman`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `user_roles`
--
ALTER TABLE `user_roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `anggota_kelompok`
--
ALTER TABLE `anggota_kelompok`
  ADD CONSTRAINT `fk_anggotakelompok_kelompok` FOREIGN KEY (`kelompok_id`) REFERENCES `kelompok` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_anggotakelompok_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `kelompok`
--
ALTER TABLE `kelompok`
  ADD CONSTRAINT `fk_kelompok_pembina` FOREIGN KEY (`pembina_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_kelompok_sekertaris` FOREIGN KEY (`sekertaris_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `laporan_amalan`
--
ALTER TABLE `laporan_amalan`
  ADD CONSTRAINT `fk_laporan_kelompok` FOREIGN KEY (`kelompok_id`) REFERENCES `kelompok` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_laporan_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `laporan_upa`
--
ALTER TABLE `laporan_upa`
  ADD CONSTRAINT `laporan_kelompok_foreign` FOREIGN KEY (`kelompok_id`) REFERENCES `kelompok` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `laporan_pembina_foreign` FOREIGN KEY (`pembina_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `laporan_sekertaris_foreign` FOREIGN KEY (`sekertaris_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `laporan_user_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD CONSTRAINT `fk_user_roles_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_user_roles_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
