-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 19, 2026 at 12:28 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `Key_Management`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `key_code` varchar(50) DEFAULT NULL,
  `room_name` varchar(100) DEFAULT NULL,
  `borrower_type` enum('staff','others') NOT NULL,
  `staff_id` int(11) DEFAULT NULL,
  `borrower_name` varchar(150) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `booking_type` enum('time_slot','whole_day') NOT NULL,
  `booking_date` date NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `purpose` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('BOOKED','RETURNED') NOT NULL DEFAULT 'BOOKED',
  `overdue_notified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `room_id`, `key_code`, `room_name`, `borrower_type`, `staff_id`, `borrower_name`, `phone`, `booking_type`, `booking_date`, `start_time`, `end_time`, `purpose`, `created_at`, `status`, `overdue_notified`) VALUES
(1, 1, '1C001', 'INCUBATOR', 'staff', 2, 'DAILY BINTI TAYOK', '0138682409', 'whole_day', '2026-01-18', '00:00:00', '23:59:59', 'testing lg', '2026-01-18 05:38:26', 'RETURNED', NULL),
(2, 39, '2A039', 'PENGARAH', 'staff', 2, 'DAILY BINTI TAYOK', '0138682409', 'time_slot', '2026-01-18', '14:30:00', '14:32:00', 'testing overdue button', '2026-01-18 06:30:59', 'RETURNED', NULL),
(3, 2, '1C002', 'INCUBATOR', 'staff', 2, 'DAILY BINTI TAYOK', '0138682409', 'time_slot', '2026-01-18', '15:02:00', '15:05:00', 'testing key code n room name', '2026-01-18 07:02:54', 'RETURNED', NULL),
(4, 3, '1D003', 'INCUBATOR', 'staff', 2, 'DAILY BINTI TAYOK', '0138682409', 'whole_day', '2026-01-18', '00:00:00', '23:59:59', 'testing phone no', '2026-01-18 07:07:11', 'RETURNED', NULL),
(5, 4, '1D004', 'INCUBATOR', 'staff', 2, 'DAILY BINTI TAYOK', '0138682409', 'time_slot', '2026-01-18', '15:15:00', '15:17:00', 'testing save phone', '2026-01-18 07:15:32', 'BOOKED', NULL),
(6, 5, '1D005', 'INCUBATOR', 'others', NULL, 'doyenne', '0138681307', 'time_slot', '2026-01-18', '16:10:00', '16:12:00', 'testing peminjam others', '2026-01-18 08:11:08', 'BOOKED', NULL),
(7, 6, '1C006', 'BILIK KAWALAN', 'staff', 2, 'DAILY BINTI TAYOK', '0138682409', 'time_slot', '2026-01-18', '16:14:00', '16:16:00', 'testing pengguna lain', '2026-01-18 08:15:47', 'BOOKED', NULL),
(8, 7, '1B007', 'BILIK KAWALAN', 'staff', 10, 'FARHANA BINTI KAMIUS', '0138771700', 'time_slot', '2026-01-18', '16:16:00', '16:18:00', 'testing penguna lain book under nama sendiri', '2026-01-18 08:17:12', 'BOOKED', NULL),
(9, 1, '1C001', 'INCUBATOR', 'staff', 2, 'DAILY BINTI TAYOK', '0138682409', 'whole_day', '2026-01-18', '00:00:00', '23:59:59', 'testing lg', '2026-01-18 12:16:23', 'RETURNED', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `key_list`
--

CREATE TABLE `key_list` (
  `key_id` int(11) NOT NULL,
  `key_code` varchar(20) NOT NULL,
  `room_name` varchar(100) NOT NULL,
  `remarks` varchar(50) NOT NULL,
  `status` enum('AVAILABLE','BOOKED','OVERDUE','') NOT NULL DEFAULT 'AVAILABLE'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `key_list`
--

INSERT INTO `key_list` (`key_id`, `key_code`, `room_name`, `remarks`, `status`) VALUES
(1, '1C001', 'INCUBATOR', 'FD-1', 'AVAILABLE'),
(2, '1C002', 'INCUBATOR', 'FD-2', 'AVAILABLE'),
(3, '1D003', 'INCUBATOR', 'MD-1', 'AVAILABLE'),
(4, '1D004', 'INCUBATOR', 'MD-2', 'AVAILABLE'),
(5, '1D005', 'INCUBATOR', 'BD', 'AVAILABLE'),
(6, '1C006', 'BILIK KAWALAN', 'FD', 'AVAILABLE'),
(7, '1B007', 'BILIK KAWALAN', 'BD', 'AVAILABLE'),
(8, '1A008', 'BILIK MSB', 'MD', 'AVAILABLE'),
(9, '1D009', 'LOBBY', 'FD-L-1', 'AVAILABLE'),
(10, '1D010', 'LOBBY', 'FD-L-2', 'AVAILABLE'),
(11, '1E011', 'LOBBY', 'FD-R-1', 'AVAILABLE'),
(12, '1E012', 'LOBBY', 'FD-R-2', 'AVAILABLE'),
(13, '1E013', 'STOR HEP', 'FD', 'AVAILABLE'),
(14, '1E014', 'STOR HEP', 'BD', 'AVAILABLE'),
(15, '1D015', 'BILIK SDF', 'MD', 'AVAILABLE'),
(16, '1D016', 'BILIK HCRDB', 'MD', 'AVAILABLE'),
(17, '1D017', 'RUANG SERBAGUNA', 'MD', 'AVAILABLE'),
(18, '1D018', 'PINTU RUANG SERBAGUNA', 'MD', 'AVAILABLE'),
(19, '1F019', 'STUDENT CENTRE', 'FD-L-1', 'AVAILABLE'),
(20, '1F020', 'STUDENT CENTRE', 'FD-L-2', 'AVAILABLE'),
(21, '1G021', 'STUDENT CENTRE', 'FD-R-1', 'AVAILABLE'),
(22, '1G022', 'STUDENT CENTRE', 'FD-R-2', 'AVAILABLE'),
(23, '1G023', 'STUDENT CENTRE', 'MD-L-1', 'AVAILABLE'),
(24, '1G024', 'STUDENT CENTRE', 'MD-L-2', 'AVAILABLE'),
(25, '1G025', 'STUDENT CENTRE', 'BD', 'AVAILABLE'),
(26, '1F026', 'STUDENT CENTRE', 'MD-R-1', 'AVAILABLE'),
(27, '1F027', 'STUDENT CENTRE', 'MD-R-2', 'AVAILABLE'),
(28, '1H028', 'SIMULATION AREA', 'MD', 'AVAILABLE'),
(29, '1H029', 'SIMULATION AREA', 'MD', 'AVAILABLE'),
(30, '1H030', 'PREP AREA', 'MD', 'AVAILABLE'),
(31, '1I031', 'PSH', 'FD-1', 'AVAILABLE'),
(32, '1I032', 'PSH', 'FD-2', 'AVAILABLE'),
(33, '1I033', 'PSH', 'BD', 'AVAILABLE'),
(34, '1J034', 'CAFE', 'FD-1', 'AVAILABLE'),
(35, '1J035', 'CAFE', 'FD-2', 'AVAILABLE'),
(36, '1J036', 'CAFE', 'BD', 'AVAILABLE'),
(37, '1J037', 'KOPERASI', 'FD', 'AVAILABLE'),
(38, '1J038', 'KOPERASI', 'BD', 'AVAILABLE'),
(39, '2A039', 'PENGARAH', 'FD-1', 'AVAILABLE'),
(40, '2A040', 'PENGARAH', 'FD-2', 'AVAILABLE'),
(41, '2A041', 'PENGARAH', 'BD', 'AVAILABLE'),
(42, '2A042', 'PENO. PEG. TADBIR', 'MD', 'AVAILABLE'),
(43, '2A043', 'KEWANGAN', 'MD', 'AVAILABLE'),
(44, '2A044', 'BILIK CETAK', 'MD', 'AVAILABLE'),
(45, '2A045', 'BILIK SSB', 'MD', 'AVAILABLE'),
(46, '2A046', 'BILIK UTILITI', 'MD', 'AVAILABLE'),
(47, '2B047', 'RUANG MENUNGGU', 'MD-1', 'AVAILABLE'),
(48, '2B048', 'RUANG MENUNGGU', 'MD-2', 'AVAILABLE'),
(49, '2B049', 'ADMIN', 'FD', 'AVAILABLE'),
(50, '2D050', 'ADMIN', 'MD', 'AVAILABLE'),
(51, '2B051', 'ADMIN', 'BD', 'AVAILABLE'),
(52, '2C052', 'TIM. PEG. 2', 'FD', 'AVAILABLE'),
(53, '2C053', 'TIM. PEG. 3', 'BD', 'AVAILABLE'),
(54, '2C054', 'TIM. PEG. 1', 'MD', 'AVAILABLE'),
(55, '2D055', 'BILIK FAIL', 'MD', 'AVAILABLE'),
(56, '2D056', 'PINTU LALUAN ADMIN', 'MD', 'AVAILABLE'),
(57, '2D057', 'BILIK MESYUARAT', 'FD-L-1', 'AVAILABLE'),
(58, '2D058', 'BILIK MESYUARAT', 'FD-L-2', 'AVAILABLE'),
(59, '2E059', 'BILIK MESYUARAT', 'FD-R-1', 'AVAILABLE'),
(60, '2E060', 'BILIK MESYUARAT', 'FD-R-2', 'AVAILABLE'),
(61, '2E061', 'BILIK MESYUARAT', 'BD-R', 'AVAILABLE'),
(62, '2D062', 'BILIK MESYUARAT', 'BD-L', 'AVAILABLE'),
(63, '2D063', 'BILIK UTILITI 1', 'L', 'AVAILABLE'),
(64, '2E064', 'BILIK UTILITI 2', 'R', 'AVAILABLE'),
(65, '2D065', 'BILIK TCR', 'MD', 'AVAILABLE'),
(66, '2F066', 'DEWAN KULIAH', 'FD-L-1', 'AVAILABLE'),
(67, '2F067', 'DEWAN KULIAH', 'FD-L-2', 'AVAILABLE'),
(68, '2G068', 'DEWAN KULIAH', 'FD-R-1', 'AVAILABLE'),
(69, '2G069', 'DEWAN KULIAH', 'FD-R-2', 'AVAILABLE'),
(70, '2G070', 'DEWAN KULIAH', 'BD-R', 'AVAILABLE'),
(71, '2F071', 'DEWAN KULIAH', 'BD-L', 'AVAILABLE'),
(72, '2F072', 'B. PERSEDIAAN 2 (STOR)', 'L', 'AVAILABLE'),
(73, '2G073', 'B. PERSEDIAAN 2 (STOR)', 'R', 'AVAILABLE'),
(74, '2H074', 'SURAU', 'FD', 'AVAILABLE'),
(75, '2H075', 'SURAU', 'BD', 'AVAILABLE'),
(76, '2H076', 'SURAU', 'GIRL', 'AVAILABLE'),
(77, '2H077', 'SURAU', 'BOY', 'AVAILABLE'),
(78, '2H078', 'PINTU LALUAN HEP', 'MD', 'AVAILABLE'),
(79, '2I079', 'BILIK FAIL EXAM', 'MD', 'AVAILABLE'),
(80, '2I080', 'BILIK PEG. EXAM', 'MD', 'AVAILABLE'),
(81, '2I081', 'BILIK PEG. PENGAMBILAN', 'MD', 'AVAILABLE'),
(82, '2I082', 'BILIK PEG. LI', 'MD', 'AVAILABLE'),
(83, '2J083', 'RUANG HEP', 'FD', 'AVAILABLE'),
(84, '2J084', 'RUANG HEP', '', 'AVAILABLE'),
(85, '2J085', 'RUANG HEP', 'MD', 'AVAILABLE'),
(86, '2J086', 'RUANG HEP', 'BD', 'AVAILABLE'),
(87, '2J087', 'BILIK FAIL HEP', 'MD', 'AVAILABLE'),
(88, '2J088', 'BILIK PEG. PP', 'MD', 'AVAILABLE'),
(89, '3A089', 'BILIK PENSYARAH', 'FD-L-1', 'AVAILABLE'),
(90, '3A090', 'BILIK PENSYARAH', 'FD-L-2', 'AVAILABLE'),
(91, '3B091', 'BILIK PENSYARAH', 'FD -R', 'AVAILABLE'),
(92, '3B092', 'BILIK PENSYARAH', 'BD-R', 'AVAILABLE'),
(93, '3A093', 'BILIK PENSYARAH', 'BD-L', 'AVAILABLE'),
(94, '3A094', 'BILIK DB', 'MD', 'AVAILABLE'),
(95, '3A095', 'BILIK UTILITI', 'MD', 'AVAILABLE'),
(96, '3B096', 'P.LALUAN PENSYARAH', 'MD', 'AVAILABLE'),
(97, '3C097', 'MAKMAL MULTIMEDIA', 'FD', 'AVAILABLE'),
(98, '3C098', 'MAKMAL MULTIMEDIA', 'BD', 'AVAILABLE'),
(99, '3C099', 'P.LALUAN M.MMEDIA', 'MD', 'AVAILABLE'),
(100, '3D100', 'BILIK ICT', 'MD', 'AVAILABLE'),
(101, '3D101', 'BILIK MAIN TCR', 'MD', 'AVAILABLE'),
(102, '3D102', 'BILIK TCR', 'MD', 'AVAILABLE'),
(103, '3E103', 'M.PENGATUCARAAN 1', 'FD', 'AVAILABLE'),
(104, '3E104', 'M.PENGATUCARAAN 2', 'BD', 'AVAILABLE'),
(105, '3E105', 'B.PERSEDIAAN 3', 'WR', 'AVAILABLE'),
(106, '3F106', 'M.K.LOGISTIK 1', 'FD', 'AVAILABLE'),
(107, '3F107', 'M.K.LOGISTIK 1', 'BD', 'AVAILABLE'),
(108, '3F108', 'B.PERSEDIAAN 4 (PSH)', 'L', 'AVAILABLE'),
(109, '3G109', 'B.PERSEDIAAN 4 (PSH)', 'R', 'AVAILABLE'),
(110, '3G110', 'M.K.LOGISTIK 2', 'FD', 'AVAILABLE'),
(111, '3G111', 'M.K.LOGISTIK 2', 'BD', 'AVAILABLE'),
(112, '3H112', 'M.PENGATUCARAAN 2', 'FD', 'AVAILABLE'),
(113, '3H113', 'M.PENGATUCARAAN 2', 'BD', 'AVAILABLE'),
(114, '3H114', 'P.LALUAN M.P2', 'MD', 'AVAILABLE'),
(115, '3I115', 'MAKMAL BAHASA', 'FD', 'AVAILABLE'),
(116, '3I116', 'MAKMAL BAHASA', 'BD', 'AVAILABLE'),
(117, '3J117', 'LIBRARY', 'FD-1', 'AVAILABLE'),
(118, '3J118', 'LIBRARY', 'FD-2', 'AVAILABLE'),
(119, '3J119', 'LIBRARY', 'BD', 'AVAILABLE'),
(120, '4A120', 'MAK.PENDAWAIAN RANGKAIAN', 'FD-1', 'AVAILABLE'),
(121, '4A121', 'MAK.PENDAWAIAN RANGKAIAN', 'FD-2', 'AVAILABLE'),
(122, '4A122', 'MAK.PENDAWAIAN RANGKAIAN', 'BD', 'AVAILABLE'),
(123, '4A123', 'BILIK DB', 'MD', 'AVAILABLE'),
(124, '4A124', 'BILIK UTILITI', 'MD', 'AVAILABLE'),
(125, '4B125', 'MAK. RANGKAIAN KOMPUTER', 'FD', 'AVAILABLE'),
(126, '4B126', 'MAK. RANGKAIAN KOMPUTER', 'BD', 'AVAILABLE'),
(127, '4B127', 'P.LALUAN MRK', 'MD', 'AVAILABLE'),
(128, '4C128', 'MAKMAL BAIK PULIH KOMPUTER', 'FD', 'AVAILABLE'),
(129, '4C129', 'MAKMAL BAIK PULIH KOMPUTER', 'BD', 'AVAILABLE'),
(130, '4C130', 'P.LALUAN MBPK', 'MD', 'AVAILABLE'),
(131, '4D131', 'TEC1', 'FD', 'AVAILABLE'),
(132, '4D132', 'TEC1', 'BD', 'AVAILABLE'),
(133, '4D133', 'BILIK TCR', 'MD', 'AVAILABLE'),
(134, '4E134', 'MAKMAL IOT', 'FD', 'AVAILABLE'),
(135, '4E135', 'MAKMAL IOT', 'BD', 'AVAILABLE'),
(136, '4E136', 'B. PERSEDIAAN 5', 'MD', 'AVAILABLE'),
(137, '4F137', 'TECC2', 'FD-L', 'AVAILABLE'),
(138, '4G138', 'TECC2', 'FD-R', 'AVAILABLE'),
(139, '4G139', 'TECC2', 'BD-R', 'AVAILABLE'),
(140, '4F140', 'TECC2', 'BD-L', 'AVAILABLE'),
(141, '4F141', 'B.PERSEDIAAN 6', 'R', 'AVAILABLE'),
(142, '4G142', 'B.PERSEDIAAN 6', 'L', 'AVAILABLE'),
(143, '4H143', 'MAKMAL AI', 'FD', 'AVAILABLE'),
(144, '4H144', 'MAKMAL AI', 'BD', 'AVAILABLE'),
(145, '4H145', 'P.LALUAN M.AI', 'MD', 'AVAILABLE'),
(146, '4I146', 'TEC 2', 'FD', 'AVAILABLE'),
(147, '4I147', 'TEC 2', 'BD', 'AVAILABLE'),
(148, '4I148', 'P.LALUAN TEC2', 'MD', 'AVAILABLE'),
(149, '4J149', 'TECC 1', 'FD-L', 'AVAILABLE'),
(150, '4J150', 'TECC 1', 'FD-R', 'AVAILABLE'),
(151, '4J151', 'TECC 1', 'BD', 'AVAILABLE'),
(152, '1CD152', 'INCUBATOR(SK)', 'S.Key', 'AVAILABLE'),
(153, '1CB153', 'BILIK KAWALAN(SK)', 'S.Key', 'AVAILABLE'),
(154, '1A154', 'BILIK MSB(SK)', 'S.Key', 'AVAILABLE'),
(155, '1DE155', 'LOBBY(SK)', 'S.Key', 'AVAILABLE'),
(156, '1E156', 'STOR HEP(SK)', 'S.Key', 'AVAILABLE'),
(157, '1D157', 'BILIK SDF(SK)', 'S.Key', 'AVAILABLE'),
(158, '1D158', 'BILIK HCRDB(SK)', 'S.Key', 'AVAILABLE'),
(159, '1D159', 'RUANG SERBAGUNA(SK)', 'S.Key', 'AVAILABLE'),
(160, '1D160', 'PINTU RUANG SERBAGUNA(SK)', 'S.Key', 'AVAILABLE'),
(161, '1FG161', 'STUDENT CENTRE(SK)', 'S.Key', 'AVAILABLE'),
(162, '1H162', 'SIMULATION AREA(SK)', 'S.Key', 'AVAILABLE'),
(163, '1H163', 'PREP AREA(SK)', 'S.Key', 'AVAILABLE'),
(164, '1I164', 'PSH(SK)', 'S.Key', 'AVAILABLE'),
(165, 'J165', 'CAFE(SK)', 'S.Key', 'AVAILABLE'),
(166, '1J166', 'KOPERASI(SK)', 'S.Key', 'AVAILABLE'),
(167, '2A167', 'PENGARAH(SK)', 'S.Key', 'AVAILABLE'),
(168, '2A168', 'PENO. PEG. TADBIR(SK)', 'S.Key', 'AVAILABLE'),
(169, '2A169', 'KEWANGAN(SK)', 'S.Key', 'AVAILABLE'),
(170, '2A170', 'BILIK CETAK(SK)', 'S.Key', 'AVAILABLE'),
(171, '2A171', 'BILIK SSB(SK)', 'S.Key', 'AVAILABLE'),
(172, '2A172', 'BILIK UTILITI(SK)', 'S.Key', 'AVAILABLE'),
(173, '2B173', 'RUANG MENUNGGU(SK)', 'S.Key', 'AVAILABLE'),
(174, '2BD174', 'ADMIN(SK)', 'S.Key', 'AVAILABLE'),
(175, '2C175', 'TIM. PEG. 2(SK)', 'S.Key', 'AVAILABLE'),
(176, '2C176', 'TIM. PEG. 1(SK)', 'S.Key', 'AVAILABLE'),
(177, '2D177', 'BILIK FAIL(SK)', 'S.Key', 'AVAILABLE'),
(178, '2D178', 'PINTU LALUAN ADMIN(SK)', 'S.Key', 'AVAILABLE'),
(179, '2D179/80', 'BILIK MESYUARAT(SK)', 'S.Key', 'AVAILABLE'),
(180, '2D181', 'BILIK UTILITI 1(SK)', 'S.Key', 'AVAILABLE'),
(181, '2D182', 'BILIK UTILITI 2(SK)', 'S.Key', 'AVAILABLE'),
(182, '2D183', 'BILIK TCR(SK)', 'S.Key', 'AVAILABLE'),
(183, '2FG184', 'DEWAN KULIAH(SK)', 'S.Key', 'AVAILABLE'),
(184, '2FG185', 'B. PERSEDIAAN 2 (STOR)(SK)', 'S.Key', 'AVAILABLE'),
(185, '2H186', 'SURAU(SK)', 'S.Key', 'AVAILABLE'),
(186, '2H187', 'PINTU LALUAN HEP(SK)', 'S.Key', 'AVAILABLE'),
(187, '2I188', 'BILIK FAIL EXAM(SK)', 'S.Key', 'AVAILABLE'),
(188, '2I189', 'BILIK PEG. EXAM(SK)', 'S.Key', 'AVAILABLE'),
(189, '2I190', 'BILIK PEG. PENGAMBILAN(SK)', 'S.Key', 'AVAILABLE'),
(190, '2I191', 'BILIK PEG. LI(SK)', 'S.Key', 'AVAILABLE'),
(191, '2J192', 'RUANG HEP(SK)', 'S.Key', 'AVAILABLE'),
(192, '2J193', 'BILIK FAIL HEP(SK)', 'S.Key', 'AVAILABLE'),
(193, '2J194', 'BILIK PEG. PP(SK)', 'S.Key', 'AVAILABLE'),
(194, '3AB195', 'BILIK PENSYARAH(SK)', 'S.Key', 'AVAILABLE'),
(195, '3A196', 'BILIK DB(SK)', 'S.Key', 'AVAILABLE'),
(196, '3A197', 'BILIK UTILITI(SK)', 'S.Key', 'AVAILABLE'),
(197, '3B198', 'P.LALUAN PENSYARAH(SK)', 'S.Key', 'AVAILABLE'),
(198, '3C199', 'MAKMAL MULTIMEDIA(SK)', 'S.Key', 'AVAILABLE'),
(199, '3C200', 'P.LALUAN M.MMEDIA(SK)', 'S.Key', 'AVAILABLE'),
(200, '3D201', 'BILIK ICT(SK)', 'S.Key', 'AVAILABLE'),
(201, '3D202', 'BILIK MAIN TCR(SK)', 'S.Key', 'AVAILABLE'),
(202, '3D203', 'BILIK TCR(SK)', 'S.Key', 'AVAILABLE'),
(203, '3E204', 'M.PENGATUCARAAN 1(SK)', 'S.Key', 'AVAILABLE'),
(204, '3E205', 'B.PERSEDIAAN 4 (PSH)(SK)', 'S.Key', 'AVAILABLE'),
(205, '3G206', 'M.K.LOGISTIK 2(SK)', 'S.Key', 'AVAILABLE'),
(206, '3H207', 'M.PENGATUCARAAN 2(SK)', 'S.Key', 'AVAILABLE'),
(207, '3H208', 'P.LALUAN M.P2(SK)', 'S.Key', 'AVAILABLE'),
(208, '3I209', 'MAKMAL BAHASA(SK)', 'S.Key', 'AVAILABLE'),
(209, '3J210', 'LIBRARY(SK)', 'S.Key', 'AVAILABLE'),
(210, '4A211', 'MAK.PENDAWAIAN RANGKAIAN(SK)', 'S.Key', 'AVAILABLE'),
(211, '4A212', 'BILIK DB(SK)', 'S.Key', 'AVAILABLE'),
(212, '4A213', 'BILIK UTILITI(SK)', 'S.Key', 'AVAILABLE'),
(213, '4B214', 'MAK. RANGKAIAN KOMPUTER(SK)', 'S.Key', 'AVAILABLE'),
(214, '4B215', 'P.LALUAN MRK(SK)', 'S.Key', 'AVAILABLE'),
(215, '4C216', 'MAKMAL BAIK PULIH KOMPUTER(SK)', 'S.Key', 'AVAILABLE'),
(216, '4C217', 'P.LALUAN MBPK(SK)', 'S.Key', 'AVAILABLE'),
(217, '4D218', 'TEC1(SK)', 'S.Key', 'AVAILABLE'),
(218, '4D219', 'BILIK TCR(SK)', 'S.Key', 'AVAILABLE'),
(219, '4E220', 'MAKMAL IOT(SK)', 'S.Key', 'AVAILABLE'),
(220, '4E221', 'B. PERSEDIAAN 5(SK)', 'S.Key', 'AVAILABLE'),
(221, '4FG222', 'TECC2(SK)', 'S.Key', 'AVAILABLE'),
(222, '4FG223', 'B.PERSEDIAAN 6(SK)', 'S.Key', 'AVAILABLE'),
(223, '4H224', 'MAKMAL AI(SK)', 'S.Key', 'AVAILABLE'),
(224, '4H225', 'P.LALUAN M.AI(SK)', 'S.Key', 'AVAILABLE'),
(225, '4I226', 'TEC 2(SK)', 'S.Key', 'AVAILABLE'),
(226, '4I227', 'P.LALUAN TEC2(SK)', 'S.Key', 'AVAILABLE'),
(227, '4J228', 'TECC 1(SK)', 'S.Key', 'AVAILABLE');

-- --------------------------------------------------------

--
-- Table structure for table `staff_name`
--

CREATE TABLE `staff_name` (
  `staff_id` int(11) NOT NULL,
  `staff_name` varchar(100) NOT NULL,
  `department` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `telegram_chat_id` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff_name`
--

INSERT INTO `staff_name` (`staff_id`, `staff_name`, `department`, `phone`, `created_at`, `telegram_chat_id`) VALUES
(1, 'DARNI BINTI MOHAMED YUSOFF', 'PENGARAH DH12', '0194167878', '2026-01-17 08:02:24', NULL),
(2, 'DAILY BINTI TAYOK', 'TIMBALAN PENGARAH AKADEMIK', '0138682409', '2026-01-17 08:02:24', '1817118'),
(3, 'MOHAMMAD NOR IHSAN BIN MD ZIN', 'TIMBALAN PENGARAH PENGURUSAN', '0138768007', '2026-01-17 08:02:24', '3407492'),
(4, 'MOHD AZLAN BIN HASHIM', 'PEGAWAI PEMBANGUNAN PELAJAR DH9', '0109361519', '2026-01-17 08:02:24', '1048876798'),
(5, 'NURUL HUZAIFAH BINTI GIMSUN', 'PEGAWAI PERHUBUNGAN INDUSTRI DAN ALUMNI DH9', '0135282984', '2026-01-17 08:02:24', NULL),
(6, 'TS. NURATIKA ASYURAH BINTI ABDULLAH', 'PEGAWAI PEPERIKSAAN DH10', '0168067759', '2026-01-17 08:02:24', NULL),
(7, 'NADIRAH BINTI MOHAMAD', 'PEGAWAI PENGAMBILAN DH9', '0143735344', '2026-01-17 08:02:24', '868970855'),
(8, 'DAYANG NOORLIZAH BT AWANG MAHMOOD', 'PENSYARAH PEMBELAJARAN SEPANJANG HAYAT DH10', '0178948428', '2026-01-17 08:02:24', '56936490'),
(9, 'TIFFANY THU PEI YING', 'KETUA UNIT PENGAJIAN AWAM DH10', '0146577683', '2026-01-17 08:02:24', '512734656'),
(10, 'FARHANA BINTI KAMIUS', 'KETUA UNIT SIJIL PERKHIDMATAN LOGISTIK DH9', '0138771700', '2026-01-17 08:02:24', NULL),
(11, 'DG NUR AFIAH BINTI AWANG AHMAD		', 'PEGAWAI KUALITI DH9', '0138771700', '2026-01-17 08:02:24', NULL),
(12, 'DG NURSHAZANA		', 'PENSYARAH SIJIL PERKHIDMATAN LOGISTIK', '0198420524', '2026-01-17 08:02:24', NULL),
(13, 'WAN HILMI		', 'PENSYARAH UNIT PENGAJIAN AM', '0162750348', '2026-01-17 08:02:24', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `role` enum('admin','staff') DEFAULT 'staff'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `full_name`, `role`) VALUES
(1, 'daily', 'kkppskey2026', 'DAILY BINTI TAYOK', 'staff'),
(2, 'darni', 'kkppskey2026', 'DARNI BINTI MOHAMED YUSOFF', 'staff'),
(3, 'ihsan', 'kkppskey2026', 'MOHAMMAD NOR IHSAN BIN MD ZIN', 'staff'),
(4, 'azlan', 'kkppskey2026', 'MOHD AZLAN BIN HASHIM', 'staff'),
(5, 'huzaifah', 'kkppskey2026', 'NURUL HUZAIFAH BINTI GIMSUN', 'staff'),
(6, 'syurah', 'kkppskey2026', 'TS. NURATIKA ASYURAH BINTI ABDULLAH', 'staff'),
(7, 'nadirah', 'kkppskey2026', 'NADIRAH BINTI MOHAMAD', 'staff'),
(8, 'dayang', 'kkppskey2026', 'DAYANG NOORLIZAH BT AWANG MAHMOOD', 'staff'),
(9, 'tiffany', 'kkppskey2026', 'TIFFANY THU PEI YING', 'staff'),
(10, 'farhana', 'kkppskey2026', 'FARHANA BINTI KAMIUS', 'staff'),
(11, 'afiah	', 'kkppskey2026', 'DG NUR AFIAH BINTI AWANG AHMAD		', 'staff'),
(17, 'shazana	', 'kkppskey2026', 'DG NURSHAZANA		', 'staff'),
(18, 'wanhilmi', 'kkppskey2026', 'WAN HILMI		', 'staff');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`);

--
-- Indexes for table `key_list`
--
ALTER TABLE `key_list`
  ADD PRIMARY KEY (`key_id`);

--
-- Indexes for table `staff_name`
--
ALTER TABLE `staff_name`
  ADD PRIMARY KEY (`staff_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `key_list`
--
ALTER TABLE `key_list`
  MODIFY `key_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=913;

--
-- AUTO_INCREMENT for table `staff_name`
--
ALTER TABLE `staff_name`
  MODIFY `staff_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
