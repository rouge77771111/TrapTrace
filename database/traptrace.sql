-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 05, 2026 at 03:05 PM
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
-- Database: `traptrace`
--

-- --------------------------------------------------------

--
-- Table structure for table `attack_events`
--

CREATE TABLE `attack_events` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `attack_type` varchar(100) NOT NULL,
  `command` text DEFAULT NULL,
  `activity` text NOT NULL,
  `risk_level` enum('Low','Medium','High','Critical') NOT NULL DEFAULT 'Low',
  `status` enum('Detected','Investigating','Resolved') NOT NULL DEFAULT 'Detected',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attack_events`
--

INSERT INTO `attack_events` (`id`, `ip_address`, `username`, `attack_type`, `command`, `activity`, `risk_level`, `status`, `created_at`) VALUES
(1, '192.168.1.105', 'admin', 'Brute Force', 'login attempt', 'Multiple failed login attempts detected', 'High', 'Detected', '2026-10-05 11:24:07'),
(2, '10.0.0.25', 'root', 'Credential Attack', 'ssh root', 'Unauthorized SSH login attempt detected', 'Critical', 'Detected', '2026-10-05 11:24:07'),
(3, '172.16.0.15', 'guest', 'Suspicious Login', 'login', 'Repeated invalid credentials submitted', 'Medium', 'Investigating', '2026-10-05 11:24:07'),
(4, '::1', 'admin', 'Credential Attack', 'Username: admin', 'Unauthorized login attempt on KV Enterprise', 'High', 'Detected', '2026-10-05 12:05:55'),
(5, '::1', 'hacker', 'Credential Attack', 'Username: hacker', 'Unauthorized login attempt on KV Enterprise', 'High', 'Detected', '2026-10-05 12:09:51'),
(6, '::1', 'admin', 'Credential Attack', 'Username: admin', 'Unauthorized login attempt on KV Enterprise', 'High', 'Detected', '2026-10-05 12:16:19'),
(7, '::1', 'admin', 'Credential Attack', 'Username: admin', 'Unauthorized login attempt on KV Enterprise', 'High', 'Detected', '2026-10-05 12:18:32'),
(8, '::1', 'hi', 'Repeated Login Attempt', 'Username: hi', 'Repeated failed login attempts detected from the same IP address.', 'High', 'Detected', '2026-10-05 12:26:33'),
(9, '::1', 'admin', 'Repeated Login Attempt', 'Username: admin', 'Repeated failed login attempts detected from the same IP address.', 'High', 'Detected', '2026-10-05 12:27:53'),
(10, '::1', 'admin', 'Repeated Login Attempt', 'Username: admin', 'Repeated failed login attempts detected from the same IP address.', 'High', 'Detected', '2026-10-05 12:37:25'),
(11, '::1', 'fihsof', 'Repeated Login Attempt', 'Username: fihsof', 'Repeated failed login attempts detected from the same IP address.', 'High', 'Detected', '2026-10-05 12:40:04'),
(12, '::1', 'hhgg', 'Repeated Login Attempt', 'Username: hhgg', 'Repeated failed login attempts detected from the same IP address.', 'High', 'Detected', '2026-10-05 12:40:36'),
(13, '::1', 'hhgg', 'Repeated Login Attempt', 'Username: hhgg', 'Repeated failed login attempts detected from the same IP address.', 'High', 'Detected', '2026-10-05 12:42:42'),
(14, '::1', 'dsdsd', 'Credential Attack', 'Username: dsdsd', 'Failed login attempt detected on KV Enterprise.', 'Medium', 'Detected', '2026-10-05 12:57:59'),
(15, '::1', 'dsdsds', 'Repeated Login Attempt', 'Username: dsdsds', 'Repeated failed login attempts detected from the same IP address.', 'High', 'Detected', '2026-10-05 12:58:10'),
(16, '::1', 'fdsfd', 'Repeated Login Attempt', 'Username: fdsfd', 'Repeated failed login attempts detected from the same IP address.', 'High', 'Detected', '2026-10-05 12:58:19'),
(17, '::1', 'xsdd', 'Repeated Login Attempt', 'Username: xsdd', 'Repeated failed login attempts detected from the same IP address.', 'High', 'Detected', '2026-10-05 12:58:29'),
(18, '::1', 'sdadsa', 'Brute Force Attack', 'Username: sdadsa', 'Five or more failed login attempts detected from the same IP address within 10 minutes, indicating possible brute-force behavior.', 'Critical', 'Detected', '2026-10-05 12:58:38');

-- --------------------------------------------------------

--
-- Table structure for table `packet_capture_logs`
--

CREATE TABLE `packet_capture_logs` (
  `id` int(11) NOT NULL,
  `source_ip` varchar(45) NOT NULL,
  `destination_ip` varchar(45) NOT NULL,
  `protocol` varchar(20) NOT NULL,
  `source_port` int(11) DEFAULT NULL,
  `destination_port` int(11) DEFAULT NULL,
  `packet_size` int(11) DEFAULT NULL,
  `packet_info` text DEFAULT NULL,
  `captured_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `packet_capture_logs`
--

INSERT INTO `packet_capture_logs` (`id`, `source_ip`, `destination_ip`, `protocol`, `source_port`, `destination_port`, `packet_size`, `packet_info`, `captured_at`) VALUES
(1, '192.168.1.105', '192.168.1.10', 'TCP', 54321, 22, 128, 'SSH connection attempt', '2026-10-05 11:24:07'),
(2, '10.0.0.25', '192.168.1.10', 'TCP', 49152, 80, 256, 'HTTP request detected', '2026-10-05 11:24:07'),
(3, '172.16.0.15', '192.168.1.10', 'UDP', 5353, 53, 96, 'DNS request detected', '2026-10-05 11:24:07');

-- --------------------------------------------------------

--
-- Table structure for table `system_activity_logs`
--

CREATE TABLE `system_activity_logs` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `activity_type` varchar(100) NOT NULL,
  `activity` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `system_activity_logs`
--

INSERT INTO `system_activity_logs` (`id`, `ip_address`, `username`, `activity_type`, `activity`, `created_at`) VALUES
(1, '::1', 'admin', 'Successful Login', 'Authorized user successfully logged in to KV Enterprise.', '2026-10-05 12:34:39');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `role` varchar(100) NOT NULL DEFAULT 'Cybersecurity Analyst',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `role`, `created_at`) VALUES
(1, 'admin', '$2y$12$R1Q4enFPl5XXmfOKg1pubOtMwuENLkMNyI07CHWCTG6osa4RVAO3W', 'System Administrator', 'System Administrator', '2026-10-05 11:24:07');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attack_events`
--
ALTER TABLE `attack_events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `packet_capture_logs`
--
ALTER TABLE `packet_capture_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `system_activity_logs`
--
ALTER TABLE `system_activity_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attack_events`
--
ALTER TABLE `attack_events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `packet_capture_logs`
--
ALTER TABLE `packet_capture_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `system_activity_logs`
--
ALTER TABLE `system_activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
