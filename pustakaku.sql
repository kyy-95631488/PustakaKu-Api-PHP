-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 05, 2025 at 12:23 PM
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
-- Database: `pustakaku`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `last_login` datetime DEFAULT NULL,
  `salt` varchar(64) DEFAULT NULL,
  `session_token` varchar(255) DEFAULT NULL,
  `token_expiry` datetime DEFAULT NULL,
  `verified` tinyint(1) DEFAULT 0,
  `verification_code` varchar(10) DEFAULT NULL,
  `code_expiry` datetime DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `created_at`, `last_login`, `salt`, `session_token`, `token_expiry`, `verified`, `verification_code`, `code_expiry`, `reset_token`, `reset_expiry`) VALUES
(2, 'Hendriansyah Rizky Setiawan', 'musikgratis1@gmail.com', '29980a1eaac480b1594aeef0f9fb1aceef74777f4c6a78d978daa05504a4f20e1dba859dc8cf3d1543189049d18aba309c36b33b8e7868abf69e763ee5584688', '2025-05-03 21:20:47', '2025-05-05 17:17:15', '00a7a33a196606f449451b0a329c270c7619484d51af2e3b573fb15fd6c9a080', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VySWQiOjIsImV4cCI6MTc0NjQ0MzgzNX0.e_-MW7cLRBwW4z675t7R5TNDwlSRhN8LsEf_G-HVSy8', '2025-05-05 18:17:15', 1, NULL, NULL, NULL, NULL),
(4, 'laravel', 'cerberus404x@gmail.com', '5eb7e308d538a2a48d4e489c6dbe8139c76d07edb73e0d834d8df9bb45cf33900646863825ab57808f906f5d3c4ba5aa9956718b0e19950e2c6a451984440c1f', '2025-05-04 20:12:30', '2025-05-04 20:13:30', '52f237636ca489f886ba9f8b66f979cf9fb52c0e3f917dccb15acaf8f55710b4', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VySWQiOjQsImV4cCI6MTc0NjM2ODAwOX0.PrupuRALaVuABuQAF6Fv4ITo85LO8NtRAF41fhsEiE0', '2025-05-04 21:13:29', 0, '978156', '2025-05-04 15:23:30', NULL, NULL);

--
-- Indexes for dumped tables
--

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
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
