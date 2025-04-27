-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql201.infinityfree.com
-- Generation Time: Apr 18, 2025 at 09:59 AM
-- Server version: 10.6.19-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_38776708_db_absensi`
--

-- --------------------------------------------------------

--
-- Table structure for table `absensi`
--

CREATE TABLE `absensi` (
  `id` int(11) NOT NULL,
  `siswa_id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `status` enum('hadir','izin','sakit','alpha') NOT NULL,
  `keterangan` text DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `guru`
--

CREATE TABLE `guru` (
  `id` int(11) NOT NULL,
  `nip` varchar(20) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `jenis_kelamin` enum('L','P') NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('superadmin','guru') NOT NULL DEFAULT 'guru',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `guru`
--

INSERT INTO `guru` (`id`, `nip`, `nama`, `jenis_kelamin`, `username`, `password`, `role`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'Administrator', 'L', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'superadmin', '2025-04-18 03:27:25', '2025-04-18 03:27:25'),
(13, '197812072007012013', 'SALMAWATI, S.Pd', 'P', 'Salmawati', '$2y$10$knK4kkIsV.f/OMAkuZpb9uuvyOjly0rYLPG2tqMUKIrN.sk6IRGju', 'guru', '2025-04-18 13:40:59', '2025-04-18 13:40:59'),
(14, '198012112014122002', 'SUNARNI, S.Pd', 'P', 'Sunarni', '$2y$10$QJ.7pOmgEE8CSJNDYkckguKAVpgBYAjaSKIRRIT8eWBEdcApdbMUW', 'guru', '2025-04-18 13:42:12', '2025-04-18 13:42:12'),
(15, '198307172011012008', 'SUKMAWATI, S.Pd', 'P', 'Sukmawati', '$2y$10$fdI91azWmUF92Oxt0AJvBulCxP4JEAdwlxrcyF.eqKk.dZ4mPCHNm', 'guru', '2025-04-18 13:42:53', '2025-04-18 13:42:53'),
(16, '199005082024212008', 'HERMAWATI. H, S.Pd', 'P', 'Herma', '$2y$10$BD38.JArmY73gGUJ.w.J7.rTgk8u9mxLG0EgxBiaXMKkwUPG8AAFK', 'guru', '2025-04-18 13:43:32', '2025-04-18 13:43:32'),
(17, '-', 'KHAERUL BARIAH, S.Pd', 'P', 'Khaerul', '$2y$10$oDzY3VIILyLgRJE1aUnVbea285C/SWpKVLeSmYYVLKzaCWiwmszFy', 'guru', '2025-04-18 13:44:03', '2025-04-18 13:44:03'),
(18, '--', 'USMAN PESTY, S.Pd', 'L', 'Usman', '$2y$10$kXcV3iQFgkoJUssrVrrq9OKVHf8mTL9v0jtjDoLLhgaRDFrgs.ulG', 'guru', '2025-04-18 13:44:51', '2025-04-18 13:47:15');

-- --------------------------------------------------------

--
-- Table structure for table `kelas`
--

CREATE TABLE `kelas` (
  `id` int(11) NOT NULL,
  `nama_kelas` varchar(20) NOT NULL,
  `guru_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kelas`
--

INSERT INTO `kelas` (`id`, `nama_kelas`, `guru_id`, `created_at`, `updated_at`) VALUES
(4, 'Kelas 1', 13, '2025-04-18 06:20:45', '2025-04-18 13:46:53'),
(5, 'Kelas 4', 14, '2025-04-18 06:23:54', '2025-04-18 13:46:25'),
(6, 'Kelas 3', 18, '2025-04-18 06:26:56', '2025-04-18 13:46:36'),
(7, 'Kelas 5', 16, '2025-04-18 13:10:15', '2025-04-18 13:46:15'),
(8, 'Kelas 2', 17, '2025-04-18 13:10:39', '2025-04-18 13:46:44'),
(9, 'Kelas 6', 15, '2025-04-18 13:10:49', '2025-04-18 13:45:21');

-- --------------------------------------------------------

--
-- Table structure for table `siswa`
--

CREATE TABLE `siswa` (
  `id` int(11) NOT NULL,
  `nis` varchar(20) NOT NULL,
  `nisn` varchar(20) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `jenis_kelamin` enum('L','P') NOT NULL,
  `kelas_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `siswa`
--

INSERT INTO `siswa` (`id`, `nis`, `nisn`, `nama`, `jenis_kelamin`, `kelas_id`, `created_at`, `updated_at`) VALUES
(4, '00012022', '3169255537', 'A. Muh. Syukri Ikhsan', 'L', 6, '2025-04-18 13:26:20', '2025-04-18 13:26:20'),
(5, '00022022', '3158430992', 'Adam Nurwahid', 'L', 6, '2025-04-18 13:27:51', '2025-04-18 13:27:51'),
(6, '0032022', '3154702459', 'Agung Darmawan', 'L', 6, '2025-04-18 13:28:36', '2025-04-18 13:28:36'),
(7, '0042022', '3159726456', 'Aiyla Azizah', 'P', 6, '2025-04-18 13:29:11', '2025-04-18 13:29:11'),
(8, '0052022', '3156398793', 'Almira Ayudhia Inara', 'P', 6, '2025-04-18 13:29:59', '2025-04-18 13:29:59'),
(9, '0072022', '0144962917', 'Defhy Amelia', 'P', 6, '2025-04-18 13:30:57', '2025-04-18 13:30:57'),
(10, '0092022', '3158984633', 'Fatin Mahbubah', 'P', 6, '2025-04-18 13:31:27', '2025-04-18 13:31:27'),
(11, '0102022', '0149638138', 'Filza Defina Aira', 'P', 6, '2025-04-18 13:32:23', '2025-04-18 13:32:23');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `absensi`
--
ALTER TABLE `absensi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `siswa_tanggal` (`siswa_id`,`tanggal`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `guru`
--
ALTER TABLE `guru`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nip` (`nip`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `kelas`
--
ALTER TABLE `kelas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `guru_id` (`guru_id`);

--
-- Indexes for table `siswa`
--
ALTER TABLE `siswa`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nis` (`nis`),
  ADD UNIQUE KEY `nisn` (`nisn`),
  ADD KEY `kelas_id` (`kelas_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `absensi`
--
ALTER TABLE `absensi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `guru`
--
ALTER TABLE `guru`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `kelas`
--
ALTER TABLE `kelas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `siswa`
--
ALTER TABLE `siswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `absensi`
--
ALTER TABLE `absensi`
  ADD CONSTRAINT `absensi_ibfk_1` FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`id`),
  ADD CONSTRAINT `absensi_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `guru` (`id`);

--
-- Constraints for table `kelas`
--
ALTER TABLE `kelas`
  ADD CONSTRAINT `kelas_ibfk_1` FOREIGN KEY (`guru_id`) REFERENCES `guru` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `siswa`
--
ALTER TABLE `siswa`
  ADD CONSTRAINT `siswa_ibfk_1` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
