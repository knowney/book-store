-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 21, 2026 at 12:35 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bookstore`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `product_id`, `quantity`) VALUES
(33, 7, 2, 1),
(34, 7, 1, 2),
(38, 7, 3, 1),
(42, 1, 4, 1),
(43, 1, 5, 1),
(44, 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `status` enum('pending','confirmed','shipped','delivered','cancelled','cancel_requested') DEFAULT 'pending',
  `slip_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `cancel_reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_price`, `status`, `slip_image`, `created_at`, `cancel_reason`) VALUES
(1, 1, 1199.00, 'confirmed', 'uploads/slips/1771601023_สกรีนช็อต 2026-02-18 000311.png', '2026-02-20 15:23:43', NULL),
(2, 1, 849.00, 'confirmed', 'uploads/slips/1771601489_สกรีนช็อต 2026-02-17 235146.png', '2026-02-20 15:31:29', NULL),
(4, 1, 1348.00, 'confirmed', 'uploads/slips/1771602656_IMG_8035.jpg', '2026-02-20 15:50:56', NULL),
(5, 7, 700.00, 'confirmed', 'uploads/slips/1771602823_สกรีนช็อต 2026-02-17 235146.png', '2026-02-20 15:53:43', NULL),
(7, 7, 350.00, 'confirmed', 'uploads/slips/1771603283_สกรีนช็อต 2026-02-18 000311.png', '2026-02-20 16:01:23', NULL),
(10, 7, 1339.00, 'confirmed', 'uploads/slips/1771650056_____________________________2026-02-16_151522.png', '2026-02-21 05:00:56', NULL),
(11, 7, 989.00, 'cancelled', 'uploads/slips/1771652059_____________________________2026-02-18_000311.png', '2026-02-21 05:34:19', 'ไม่สะดวกชำระเงิน'),
(12, 1, 1339.00, 'shipped', 'uploads/slips/1771652697_____________________________2026-02-18_000311.png', '2026-02-21 05:44:57', NULL),
(13, 7, 1339.00, 'confirmed', 'uploads/slips/1771657885_____________________________2026-02-18_000311.png', '2026-02-21 07:11:25', NULL),
(14, 1, 7688.00, 'pending', 'uploads/slips/1771672987_____________________________2026-02-18_000311.png', '2026-02-21 11:23:07', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 1, 1, 2, 350.00),
(2, 1, 2, 1, 499.00),
(3, 2, 1, 1, 350.00),
(4, 2, 2, 1, 499.00),
(7, 4, 1, 1, 350.00),
(8, 4, 2, 2, 499.00),
(9, 5, 1, 2, 350.00),
(12, 7, 1, 1, 350.00),
(16, 10, 1, 1, 350.00),
(17, 10, 2, 1, 499.00),
(18, 10, 3, 1, 490.00),
(19, 11, 2, 1, 499.00),
(20, 11, 3, 1, 490.00),
(21, 12, 1, 1, 350.00),
(22, 12, 2, 1, 499.00),
(23, 12, 3, 1, 490.00),
(24, 13, 1, 1, 350.00),
(25, 13, 2, 1, 499.00),
(26, 13, 3, 1, 490.00),
(27, 14, 4, 2, 1299.00),
(28, 14, 5, 2, 2370.00),
(29, 14, 1, 1, 350.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `author` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `title`, `author`, `description`, `price`, `stock`, `image`, `created_at`) VALUES
(1, 'Harry Potter', 'Garret', 'new book', 350.00, 12, 'uploads/products/1771600744_____________________________2025-10-26_135205.png', '2026-02-20 15:12:14'),
(2, 'Cat\'s Rose', 'Carrot', 'new book', 499.00, 8, 'uploads/products/1771600751_____________________________2026-02-20_221410.png', '2026-02-20 15:14:34'),
(3, 'Billie Eilish', 'Magii', 'It Time to listen her', 490.00, 6, 'uploads/products/1771649899_____________________________2026-02-21_115714.png', '2026-02-21 04:58:19'),
(4, 'YoungOhm', 'YoungOhm', 'YoungOhm Albums', 1299.00, 17, 'uploads/products/1771663346_____________________________2026-02-21_154209.png', '2026-02-21 08:42:26'),
(5, 'พยายามกี่ครั้งก็ตามแต่...', 'Hangman', 'Hangman - พยายามกี่ครั้งก็ตามแต่...', 2370.00, 13, 'uploads/products/1771663518_____________________________2026-02-21_154435.png', '2026-02-21 08:45:18');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `full_name`, `phone`, `address`, `role`, `created_at`) VALUES
(1, 'admin', 'admin@gmail.com', '$2y$10$Oo1M9VkUKFXggT/QNDrs3.zDv6eRu8.X0TzzwJutvnRuOkBZVMrDu', 'Adminstator', '090990192', 'Surat', 'admin', '2026-02-20 14:22:30'),
(6, 'nixnix', 'nix@gmail.com', '$2y$10$yRvnfrkXVJfZoL.jehTw5eePbaxyPPJLMzLQ3Ut9qfqbQ9FdKzFii', 'nix', '0987364523', 'Thailand', 'user', '2026-02-20 14:37:43'),
(7, 'nix', 'nix2@gmail.com', '$2y$10$RG6TZsa41TZFM4MKCnikqO6SetVYDmMlu.8hsF9XQHM94m18yLZdq', 'nixky', '080067384', 'Surat , SVC', 'user', '2026-02-20 14:37:48');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
