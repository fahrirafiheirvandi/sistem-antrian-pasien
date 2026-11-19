-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 19 Nov 2026 pada 06.31
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_antrian_pasien`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `pasien`
--

CREATE TABLE `pasien` (
  `id` int(11) NOT NULL,
  `no_tiket` varchar(20) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `usia` int(11) NOT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `keluhan` varchar(255) DEFAULT NULL,
  `kategori` enum('darurat','prioritas','reguler') NOT NULL DEFAULT 'reguler',
  `status` enum('menunggu','dipanggil','selesai') NOT NULL DEFAULT 'menunggu',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pasien`
--

INSERT INTO `pasien` (`id`, `no_tiket`, `nama`, `usia`, `no_hp`, `keluhan`, `kategori`, `status`, `created_at`, `updated_at`) VALUES
(1, 'TKT-20260926-001', 'Sumarni', 82, '089873123123', 'Control dengan Dr.Azri dengan resep obat juga', 'prioritas', 'selesai', '2026-09-26 02:52:09', '2026-09-26 02:52:12'),
(2, 'TKT-20260926-002', 'Sumarni', 85, '08912831231', 'Control dengan Dr.Azri', 'prioritas', 'selesai', '2026-09-26 02:52:42', '2026-09-26 02:53:12'),
(3, 'TKT-20260926-003', 'Fahri', 21, '08989485615', 'Control ke Dr.Hamdan', 'reguler', 'selesai', '2026-09-26 02:56:00', '2026-09-26 02:59:29'),
(4, 'TKT-20260926-004', 'Reza', 85, '08912932131', 'Control ke Dr.Azri', 'prioritas', 'selesai', '2026-09-26 02:58:25', '2026-09-26 02:59:13'),
(5, 'TKT-20260926-005', 'Dani', 32, '0891239123', 'Batuk, Pilek', 'reguler', 'selesai', '2026-09-26 02:58:44', '2026-09-26 02:59:53'),
(7, 'TKT-20260926-006', 'Fahri', 23, '08918239123', 'Sakit batuk flu', 'reguler', 'selesai', '2026-09-26 03:22:08', '2026-09-26 03:22:21'),
(8, 'TKT-20260926-007', 'Fahri', 25, '08993281313', 'Kepala pusing, batuk pilek', 'reguler', 'selesai', '2026-09-26 03:41:45', '2026-09-26 03:44:01'),
(9, 'TKT-20260926-008', 'Sumarni', 89, '0999123123', 'Control ke dr.azri', 'prioritas', 'selesai', '2026-09-26 03:43:17', '2026-09-26 03:59:57'),
(10, 'TKT-20260926-009', 'Dean', 34, '088123123123', 'Lambung, magh', 'reguler', 'selesai', '2026-09-26 03:43:42', '2026-09-26 04:00:16'),
(11, 'TKT-20260926-010', 'Bagys', 21, '08755634242', 'batuk pilek', 'reguler', 'selesai', '2026-09-26 03:54:14', '2026-09-26 03:55:18'),
(12, 'TKT-20260926-011', 'Bagus Panca rian', 21, '0892131231231', 'Batuk dan pilek', 'reguler', 'selesai', '2026-09-26 03:59:16', '2026-09-26 04:00:40'),
(13, 'TKT-20260930-001', 'Fahri', 21, '08921391823', 'Sakit lambung', 'darurat', 'selesai', '2026-09-30 03:13:41', '2026-09-30 03:13:55'),
(14, 'TKT-20260930-002', 'Dimas', 21, '08912312312', 'Sakit tenggorokan', 'reguler', 'selesai', '2026-09-30 03:54:07', '2026-09-30 04:00:53'),
(15, 'TKT-20261009-001', 'Reza gimank', 25, '08981923141', 'Pasien control usus buntu', 'prioritas', 'selesai', '2026-10-09 03:45:15', '2026-10-09 03:47:02'),
(16, 'TKT-20261009-002', 'Fahri', 22, '089141924124', 'Sakit magh', 'reguler', 'selesai', '2026-10-09 03:57:39', '2026-10-12 13:36:29'),
(17, 'TKT-20261012-001', 'Fahri', 42, '089123123', 'Sakit magh', 'reguler', 'selesai', '2026-10-12 13:39:36', '2026-10-12 13:44:41'),
(18, 'TKT-20261019-001', 'Fahri', 21, '08912931923', 'Sakit lambung', 'reguler', 'selesai', '2026-10-19 03:06:34', '2026-10-19 05:06:10'),
(19, 'TKT-20261113-001', 'Fahri', 23, '087192931923', 'Sakit magh', 'reguler', 'selesai', '2026-11-13 04:15:40', '2026-11-13 04:16:04'),
(20, 'TKT-20261113-002', 'Reno', 24, '0098148912930', 'Sakit gigi', 'reguler', 'selesai', '2026-11-13 08:10:30', '2026-11-13 08:10:56'),
(21, 'TKT-20261113-003', 'Vandi', 32, '00981489123', 'Batuk dan flu', 'reguler', 'selesai', '2026-11-13 08:11:26', '2026-11-13 08:11:52'),
(22, 'TKT-20261118-001', 'Fahri', 23, '089894781723', 'Sakit magh', 'reguler', 'selesai', '2026-11-18 11:54:13', '2026-11-18 11:54:51'),
(23, 'TKT-20261118-002', 'Bagu Panca Rian', 23, '0819239123', 'Sakit batuk dan flu', 'reguler', 'menunggu', '2026-11-18 11:56:00', NULL),
(24, 'TKT-20261118-003', 'Sumarni', 87, '08912312414', 'Control ke Poli Jantung', 'prioritas', 'menunggu', '2026-11-18 11:56:35', NULL),
(25, 'TKT-20261118-004', 'Abizar', 22, '08982319231', 'Kecelakaan Lalu Lintas, IGD', 'darurat', 'dipanggil', '2026-11-18 11:57:38', '2026-11-18 11:58:47');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `role` enum('superadmin','admin') NOT NULL DEFAULT 'admin',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `nama_lengkap`, `role`, `created_at`) VALUES
(1, 'admin', '$2y$10$PtX8bgV6Yw98l1Ot8HBBMuM3ZVrsmhYdo72AE9cBR7Mt4/rxBVdBO', 'Administrator Klinik', 'superadmin', '2026-09-25 14:23:09'),
(2, 'vandi', '$2y$10$T3mt6KdoucphDWUmoUm9UO4y9oNEVTAXHL8b3okuj58ZPtcf87Teq', 'Fahri Rafi Heirvandi', 'admin', '2026-11-14 16:47:39');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `pasien`
--
ALTER TABLE `pasien`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `no_tiket` (`no_tiket`),
  ADD KEY `idx_status_kategori` (`status`,`kategori`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `pasien`
--
ALTER TABLE `pasien`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
