-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 14, 2026 at 01:08 AM
-- Server version: 10.11.10-MariaDB-log
-- PHP Version: 8.3.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `galihtotiilhamprayoga_lsp_smkn1mejayan_sch_id`
--

-- --------------------------------------------------------

--
-- Table structure for table `tb_ukk_galih_area_parkir`
--

CREATE TABLE `tb_ukk_galih_area_parkir` (
  `id_area` int(11) NOT NULL,
  `nama_area` varchar(50) DEFAULT NULL,
  `kapasitas` int(11) DEFAULT NULL,
  `terisi` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tb_ukk_galih_kendaraan`
--

CREATE TABLE `tb_ukk_galih_kendaraan` (
  `id_kendaraan` int(11) NOT NULL,
  `plat_nomor` varchar(15) DEFAULT NULL,
  `jenis_kendaraan` varchar(20) DEFAULT NULL,
  `warna` varchar(20) DEFAULT NULL,
  `pemilik` varchar(100) DEFAULT NULL,
  `id_user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tb_ukk_galih_log_aktivitas`
--

CREATE TABLE `tb_ukk_galih_log_aktivitas` (
  `id_log` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `aktivitas` varchar(100) DEFAULT NULL,
  `waktu_aktivitas` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_ukk_galih_log_aktivitas`
--

INSERT INTO `tb_ukk_galih_log_aktivitas` (`id_log`, `id_user`, `aktivitas`, `waktu_aktivitas`) VALUES
(26, 1, 'User Login', '2026-04-12 09:29:59'),
(27, 2, 'User Login', '2026-04-12 09:30:29'),
(28, 1, 'User Login', '2026-04-12 09:30:38'),
(29, 2, 'User Login', '2026-04-12 09:31:14'),
(30, 1, 'User Login', '2026-04-12 09:40:19'),
(31, 2, 'User Login', '2026-04-12 09:41:19');

-- --------------------------------------------------------

--
-- Table structure for table `tb_ukk_galih_tarif`
--

CREATE TABLE `tb_ukk_galih_tarif` (
  `id_tarif` int(11) NOT NULL,
  `jenis_kendaraan` enum('motor','mobil','lainnya','') DEFAULT NULL,
  `tarif_per_jam` decimal(10,0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tb_ukk_galih_transaksi`
--

CREATE TABLE `tb_ukk_galih_transaksi` (
  `id_parkir` int(11) NOT NULL,
  `id_kendaraan` int(11) DEFAULT NULL,
  `waktu_masuk` datetime DEFAULT NULL,
  `waktu_keluar` datetime DEFAULT NULL,
  `id_tarif` int(11) DEFAULT NULL,
  `durasi_jam` int(11) DEFAULT NULL,
  `biaya_total` decimal(10,0) DEFAULT NULL,
  `status` enum('masuk','keluar','') DEFAULT NULL,
  `id_user` int(11) DEFAULT NULL,
  `id_area` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tb_ukk_galih_user`
--

CREATE TABLE `tb_ukk_galih_user` (
  `id_user` int(11) NOT NULL,
  `nama_lengkap` varchar(50) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `role` enum('admin','petugas','owner','') DEFAULT NULL,
  `status_aktif` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_ukk_galih_user`
--

INSERT INTO `tb_ukk_galih_user` (`id_user`, `nama_lengkap`, `username`, `password`, `role`, `status_aktif`) VALUES
(1, 'petugas1', 'petugas1', '123', 'petugas', 1),
(2, 'admin1', 'admin1', '123', 'admin', 1),
(3, 'owner1', 'owner1', '123', 'owner', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_ukk_galih_area_parkir`
--
ALTER TABLE `tb_ukk_galih_area_parkir`
  ADD PRIMARY KEY (`id_area`);

--
-- Indexes for table `tb_ukk_galih_kendaraan`
--
ALTER TABLE `tb_ukk_galih_kendaraan`
  ADD PRIMARY KEY (`id_kendaraan`),
  ADD KEY `fk_kendaraan_user` (`id_user`);

--
-- Indexes for table `tb_ukk_galih_log_aktivitas`
--
ALTER TABLE `tb_ukk_galih_log_aktivitas`
  ADD PRIMARY KEY (`id_log`),
  ADD KEY `fk_log_user` (`id_user`);

--
-- Indexes for table `tb_ukk_galih_tarif`
--
ALTER TABLE `tb_ukk_galih_tarif`
  ADD PRIMARY KEY (`id_tarif`);

--
-- Indexes for table `tb_ukk_galih_transaksi`
--
ALTER TABLE `tb_ukk_galih_transaksi`
  ADD PRIMARY KEY (`id_parkir`),
  ADD KEY `fk_transaksi_kendaraan` (`id_kendaraan`),
  ADD KEY `fk_transaksi_tarif` (`id_tarif`),
  ADD KEY `fk_transaksi_user` (`id_user`),
  ADD KEY `fk_transaksi_area` (`id_area`);

--
-- Indexes for table `tb_ukk_galih_user`
--
ALTER TABLE `tb_ukk_galih_user`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tb_ukk_galih_area_parkir`
--
ALTER TABLE `tb_ukk_galih_area_parkir`
  MODIFY `id_area` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tb_ukk_galih_kendaraan`
--
ALTER TABLE `tb_ukk_galih_kendaraan`
  MODIFY `id_kendaraan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tb_ukk_galih_log_aktivitas`
--
ALTER TABLE `tb_ukk_galih_log_aktivitas`
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `tb_ukk_galih_tarif`
--
ALTER TABLE `tb_ukk_galih_tarif`
  MODIFY `id_tarif` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tb_ukk_galih_transaksi`
--
ALTER TABLE `tb_ukk_galih_transaksi`
  MODIFY `id_parkir` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tb_ukk_galih_user`
--
ALTER TABLE `tb_ukk_galih_user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tb_ukk_galih_kendaraan`
--
ALTER TABLE `tb_ukk_galih_kendaraan`
  ADD CONSTRAINT `fk_kendaraan_user` FOREIGN KEY (`id_user`) REFERENCES `tb_ukk_galih_user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tb_ukk_galih_log_aktivitas`
--
ALTER TABLE `tb_ukk_galih_log_aktivitas`
  ADD CONSTRAINT `fk_log_user` FOREIGN KEY (`id_user`) REFERENCES `tb_ukk_galih_user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tb_ukk_galih_transaksi`
--
ALTER TABLE `tb_ukk_galih_transaksi`
  ADD CONSTRAINT `fk_transaksi_area` FOREIGN KEY (`id_area`) REFERENCES `tb_ukk_galih_area_parkir` (`id_area`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_transaksi_kendaraan` FOREIGN KEY (`id_kendaraan`) REFERENCES `tb_ukk_galih_kendaraan` (`id_kendaraan`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_transaksi_tarif` FOREIGN KEY (`id_tarif`) REFERENCES `tb_ukk_galih_tarif` (`id_tarif`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_transaksi_user` FOREIGN KEY (`id_user`) REFERENCES `tb_ukk_galih_user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
