-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 21, 2026 at 07:57 AM
-- Server version: 8.0.30
-- PHP Version: 8.2.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mengobrolmengobati`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` bigint UNSIGNED NOT NULL,
  `email_token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `psikiater_id` bigint UNSIGNED DEFAULT NULL,
  `psikolog_id` bigint UNSIGNED DEFAULT NULL,
  `type` enum('psikiater','psikolog') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'psikiater',
  `service` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `scheduled_at` datetime NOT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `email_token`, `user_id`, `psikiater_id`, `psikolog_id`, `type`, `service`, `status`, `scheduled_at`, `notes`, `created_at`, `updated_at`) VALUES
(54, NULL, 18, 12, NULL, 'psikiater', 'ingin memutihkan kulit', 'rejected', '2025-12-22 15:00:00', 'TOLONG SAYAA', '2025-12-21 04:37:58', '2025-12-21 04:43:10'),
(55, NULL, 19, NULL, 12, 'psikolog', 'SAYA INGIN CERDAS', 'finished', '2025-12-24 12:00:00', 'OKEE', '2025-12-21 04:39:06', '2025-12-21 04:44:33'),
(56, NULL, 18, 12, NULL, 'psikiater', 'sakit mental', 'confirmed', '2025-12-23 17:00:00', 'TOLONGGGGG', '2025-12-21 04:41:21', '2026-01-21 07:13:55'),
(57, NULL, 19, NULL, 12, 'psikolog', 'saya kurang cerdas', 'rejected', '2026-01-01 18:00:00', 'WLEEEEE', '2025-12-21 04:42:10', '2026-01-21 07:13:55'),
(58, NULL, 20, 12, NULL, 'psikiater', 'Takut tambah dewasa', 'finished', '2025-12-22 07:00:00', NULL, '2025-12-21 08:22:47', '2025-12-29 02:31:09'),
(59, NULL, 20, NULL, 12, 'psikolog', 'tes', 'confirmed', '2025-12-29 17:00:00', NULL, '2025-12-29 02:43:18', '2025-12-29 02:43:49');

-- --------------------------------------------------------

--
-- Table structure for table `conversations`
--

CREATE TABLE `conversations` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `psychologist_user_id` bigint UNSIGNED NOT NULL,
  `starts_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `group_members`
--

CREATE TABLE `group_members` (
  `id` bigint UNSIGNED NOT NULL,
  `therapy_group_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `pseudonym` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `group_members`
--

INSERT INTO `group_members` (`id`, `therapy_group_id`, `user_id`, `pseudonym`, `created_at`, `updated_at`) VALUES
(7, 1, 19, 'Anon-81EBC46C', '2025-12-21 04:44:52', '2025-12-21 04:44:52'),
(8, 1, 18, 'Anon-376D3E7D', '2025-12-21 04:45:14', '2025-12-21 04:45:14'),
(9, 1, 20, 'Anon-0B31B4AC', '2025-12-21 08:26:20', '2025-12-21 08:26:20'),
(10, 2, 20, 'Anon-DB6DA796', '2025-12-22 08:30:57', '2025-12-22 08:30:57'),
(11, 3, 20, 'Anon-E9C67B37', '2025-12-22 08:31:02', '2025-12-22 08:31:02'),
(12, 1, 22, 'Anon-7D3673FA', '2025-12-23 00:44:52', '2025-12-23 00:44:52'),
(13, 2, 22, 'Anon-DBBAFB8D', '2025-12-23 00:45:06', '2025-12-23 00:45:06'),
(14, 3, 22, 'Anon-848413CB', '2025-12-23 00:45:11', '2025-12-23 00:45:11');

-- --------------------------------------------------------

--
-- Table structure for table `group_messages`
--

CREATE TABLE `group_messages` (
  `id` bigint UNSIGNED NOT NULL,
  `therapy_group_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `group_messages`
--

INSERT INTO `group_messages` (`id`, `therapy_group_id`, `user_id`, `message`, `created_at`, `updated_at`) VALUES
(47, 1, 20, 'hi', '2026-01-21 00:46:30', '2026-01-21 00:46:30');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` bigint UNSIGNED NOT NULL,
  `booking_id` bigint UNSIGNED NOT NULL,
  `sender_type` enum('user','psikolog') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sender_id` bigint UNSIGNED DEFAULT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `booking_id`, `sender_type`, `sender_id`, `content`, `created_at`, `updated_at`) VALUES
(22, 59, 'psikolog', 17, 'halo, ada yang bisa saya bantu?', '2026-01-21 00:47:54', '2026-01-21 00:47:54'),
(23, 59, 'user', 20, 'halo dok, saya ingin konsultasi', '2026-01-21 00:48:38', '2026-01-21 00:48:38');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint UNSIGNED NOT NULL,
  `booking_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `method` varchar(50) NOT NULL,
  `status` varchar(30) NOT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `channel` varchar(50) DEFAULT NULL,
  `proof` varchar(255) DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `booking_id`, `user_id`, `amount`, `method`, `status`, `paid_at`, `created_at`, `updated_at`, `channel`, `proof`, `verified_at`) VALUES
(21, 54, 18, 300000.00, 'transfer', 'waiting_verification', NULL, '2025-12-21 04:37:58', '2025-12-21 04:37:58', 'bca', 'payments/K4BvUZWDiv6uZsV1IUyNZxEJA75Ll8sISBoSl8lC.jpg', NULL),
(22, 55, 19, 500000.00, 'ewallet', 'waiting_verification', NULL, '2025-12-21 04:39:06', '2025-12-21 04:39:06', 'gopay', 'payments/AEVxahStzzeuHwhAOE2xhaRculSUFgHLRQMlPo2z.jpg', NULL),
(23, 56, 18, 300000.00, 'transfer', 'waiting_verification', NULL, '2025-12-21 04:41:21', '2025-12-21 04:41:21', 'mandiri', 'payments/lNJiy3U9y5R8QLZ1FZ2wbUsf5LcgGLhGZUpmsfj2.jpg', NULL),
(24, 57, 19, 500000.00, 'qris', 'waiting_verification', NULL, '2025-12-21 04:42:10', '2025-12-21 04:42:10', 'qris', 'payments/yYvUMJtSkupNmpCqIYKAByjCoJ4w4f2uoh3KVeqI.jpg', NULL),
(25, 58, 20, 300000.00, 'transfer', 'waiting_verification', NULL, '2025-12-21 08:22:47', '2025-12-21 08:22:47', 'bca', 'payments/riceDbiDdqQpQSa7kXJEa5ltHkjUnNQis7qKlwQU.png', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `psikiaters`
--

CREATE TABLE `psikiaters` (
  `id` int UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `hospital` varchar(150) DEFAULT NULL,
  `work_start` time DEFAULT NULL,
  `work_end` time DEFAULT NULL,
  `description` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `psikiaters`
--

INSERT INTO `psikiaters` (`id`, `user_id`, `name`, `photo`, `hospital`, `work_start`, `work_end`, `description`, `created_at`, `updated_at`) VALUES
(12, 16, 'Dr. Hendra Saputra, Sp.KJ', 'psikiaters/c8vpKiUWRfLZo564cbn4.png', 'RSUD Sejahtera, Surabaya', '08:00:00', '13:00:00', 'Fokus pada gangguan psikotik, skizofrenia, dan rehabilitasi pasien jangka panjang.', '2025-12-21 04:34:59', '2026-01-20 05:07:34');

-- --------------------------------------------------------

--
-- Table structure for table `psikologs`
--

CREATE TABLE `psikologs` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `photo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `work_start` time DEFAULT NULL,
  `work_end` time DEFAULT NULL,
  `hospital` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `psikologs`
--

INSERT INTO `psikologs` (`id`, `user_id`, `name`, `photo`, `description`, `work_start`, `work_end`, `hospital`, `created_at`, `updated_at`) VALUES
(12, 17, 'Dr. Andi Pratama, M.Psi., Psikolog', 'psikologs/vX2nyN6QUDdtxJ2tPkCG.png', 'Psikolog klinis dengan fokus pada kecemasan, stres kerja, dan konseling dewasa. Berpengalaman lebih dari 8 tahun dalam terapi individual.', '09:00:00', '16:00:00', 'Klinik Psikologi Sejahtera, Bandung', '2025-12-21 04:36:25', '2026-01-20 05:07:11');

-- --------------------------------------------------------

--
-- Table structure for table `therapy_groups`
--

CREATE TABLE `therapy_groups` (
  `id` bigint UNSIGNED NOT NULL,
  `slug` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `therapy_groups`
--

INSERT INTO `therapy_groups` (`id`, `slug`, `title`, `description`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'anxiety-support', 'Anxiety Support', 'Tempat berbagi pengalaman dan coping untuk kecemasan.', NULL, '2025-11-19 04:18:44', '2025-11-19 04:18:44'),
(2, 'self-love', 'Self-Love', 'Diskusi dan tips membangun self-worth.', NULL, '2025-11-19 04:18:44', '2025-11-19 04:18:44'),
(3, 'overthinking-corner', 'Overthinking Corner', 'Ceritakan pengalaman mengatasi overthinking.', NULL, '2025-11-19 04:18:44', '2025-11-19 04:18:44');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','psikiater','psikolog','user') NOT NULL DEFAULT 'user',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`, `updated_at`) VALUES
(1, 'Admin Utama', 'admin@example.com', '$2y$12$h98aYTRb95KE7HHXEe05X.GyuOMoIPtlnojVtD1Ly7QG5g3XjpYBy', 'admin', '2025-11-15 16:28:51', '2025-11-15 09:32:53'),
(16, 'Dr. Hendra Saputra, Sp.KJ', 'randtwn@gmail.com', '$2y$12$tAcwltObrBkjECLIB1qy8.dxRPfcj9ExdIpRvSAvFYmxaj59C3iLK', 'psikiater', '2025-12-21 04:34:59', '2025-12-21 04:34:59'),
(17, 'Dr. Andi Pratama, M.Psi., Psikolog', 'radtwn@gmail.com', '$2y$12$mUFNzUIDi9MAgofYnDVBSODDNiufbh6xK5RGGmU20hz5onj/yH5pu', 'psikolog', '2025-12-21 04:36:25', '2025-12-21 04:36:25'),
(18, 'Iwan Kurniawan', 'akunff2370@gmail.com', '$2y$12$hy/5d.Tpw1pKBjQJo91NmOXALK7FVVePwP2G2zq6d5SChy162Er4.', 'user', '2025-12-21 04:36:58', '2025-12-21 04:36:58'),
(19, 'Gibran Fahmi', 'gibran@gmail.com', '$2y$12$zizv//dQQYhvFzN6Ky8D6.y/gqfqvho0F/GPogEBI6Wd1p8iL6A2G', 'user', '2025-12-21 04:38:29', '2025-12-21 04:38:29'),
(20, 'Doo', '1966deez@gmail.com', '$2y$12$hs3KyhfSCdBIWI0TMPWikOrNtE4ekBgoTLUS9xSRAmzmGo9Codt9m', 'user', '2025-12-21 08:21:22', '2025-12-21 08:21:22'),
(22, 'rev', 'dooaldodoo023@gmail.com', '$2y$12$x3/E9KyDeH3xNQQcA3ETj.AgcATRfs5aplTu6PFpCvGbpYvV1ExIC', 'user', '2025-12-23 00:44:46', '2025-12-23 00:44:46');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_bookings_email_token` (`email_token`),
  ADD KEY `idx_bookings_user_id` (`user_id`),
  ADD KEY `idx_bookings_psikiater_id` (`psikiater_id`);

--
-- Indexes for table `conversations`
--
ALTER TABLE `conversations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_conv_user` (`user_id`),
  ADD KEY `idx_conv_psych` (`psychologist_user_id`);

--
-- Indexes for table `group_members`
--
ALTER TABLE `group_members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_group_user` (`therapy_group_id`,`user_id`),
  ADD UNIQUE KEY `uniq_group_pseudonym` (`therapy_group_id`,`pseudonym`),
  ADD KEY `idx_group_user` (`therapy_group_id`,`user_id`),
  ADD KEY `fk_group_members_user` (`user_id`);

--
-- Indexes for table `group_messages`
--
ALTER TABLE `group_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_group_messages` (`therapy_group_id`,`created_at`),
  ADD KEY `fk_group_messages_user` (`user_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_messages_booking` (`booking_id`),
  ADD KEY `idx_messages_sender` (`sender_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `psikiaters`
--
ALTER TABLE `psikiaters`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_psikiater_user_id` (`user_id`);

--
-- Indexes for table `psikologs`
--
ALTER TABLE `psikologs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `therapy_groups`
--
ALTER TABLE `therapy_groups`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `slug_2` (`slug`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `conversations`
--
ALTER TABLE `conversations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `group_members`
--
ALTER TABLE `group_members`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `group_messages`
--
ALTER TABLE `group_messages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `psikiaters`
--
ALTER TABLE `psikiaters`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `psikologs`
--
ALTER TABLE `psikologs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `therapy_groups`
--
ALTER TABLE `therapy_groups`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `group_members`
--
ALTER TABLE `group_members`
  ADD CONSTRAINT `fk_group_members_group` FOREIGN KEY (`therapy_group_id`) REFERENCES `therapy_groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_group_members_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `group_messages`
--
ALTER TABLE `group_messages`
  ADD CONSTRAINT `fk_group_messages_group` FOREIGN KEY (`therapy_group_id`) REFERENCES `therapy_groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_group_messages_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `fk_messages_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `psikiaters`
--
ALTER TABLE `psikiaters`
  ADD CONSTRAINT `fk_psikiater_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `psikologs`
--
ALTER TABLE `psikologs`
  ADD CONSTRAINT `fk_psikologs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
