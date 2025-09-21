-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 18, 2025 at 03:10 PM
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
-- Database: `levelup`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `customer_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `color` varchar(50) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `status` enum('Pending','Completed','Cancelled') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `order_id`, `customer_id`, `product_id`, `color`, `quantity`, `status`, `created_at`, `updated_at`) VALUES
(74, 73, 12, 25, 'Black', 1, 'Completed', '2025-09-18 12:52:35', '2025-09-18 12:53:00');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Joysticks', 'Gaming joysticks and controllers', 1, '2025-08-05 21:58:07', NULL),
(13, 'Joystick', 'The Fantech EOS GP15 is a multi-platform wireless gamepad with tri-mode connectivity (Bluetooth, 2.4GHz, wired), Hall-effect anti-drift sticks and triggers, swappable thumbsticks/D-pad, rear mappable buttons, motion sensor, and long battery life — built for smooth, responsive, and customizable gaming across PC, mobile, and consoles.', 1, '2025-09-14 09:05:21', NULL),
(14, 'Joystick', 'The EOS PRO joystick is a versatile, high-precision game controller featuring Hall-effect analog sticks and triggers, customizable thumbsticks and D-pad, mappable rear paddles, motion sensing, adjustable vibration, and multi-mode connectivity (Bluetooth, wireless, and wired) for PC, consoles, and mobile devices.', 1, '2025-09-14 09:09:50', NULL),
(15, 'Joystick', 'The EOS PRO II Series is Fantech’s next-gen wireless gamepad with Hall-Effect (drift-free) sticks and triggers, tri-mode connectivity (Bluetooth, 2.4GHz, wired), swappable thumbsticks/D-pads, rear paddles with macros, motion sensing, and long battery life. The Pro II S adds premium TMR sticks and mechanical buttons for extra precision.', 1, '2025-09-14 09:11:45', NULL),
(16, 'Joystick', 'Fantech Nova II WGP16 is a wireless multi-platform gamepad with Hall-effect sticks, Bluetooth 5.3 & USB-C, 6-axis gyro, touchpad, macro/turbo buttons, and a 600 mAh battery (10 hrs) — built for smooth, drift-free gaming on PC, console, and mobile.', 1, '2025-09-14 09:13:20', NULL),
(17, 'Joystick', 'The Fantech Shooter III X is a wireless gaming controller with Hall-effect sticks, dual connectivity (Bluetooth & 2.4GHz), macro/turbo support, motion sensing, and a long-lasting battery, designed for smooth and precise gameplay across PC, console, and mobile.', 1, '2025-09-14 09:16:25', NULL),
(18, 'Joystick', 'The Fantech Nova Pro WGP14 V2 is a premium wireless gamepad featuring Hall-effect drift-free sticks, tri-mode connectivity (Bluetooth, 2.4GHz, wired), 6-axis gyro, mappable rear buttons, and a long battery life, built for pro-level multi-platform gaming.', 1, '2025-09-14 09:18:33', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `category_details`
--

CREATE TABLE `category_details` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `detail_name` varchar(50) NOT NULL,
  `detail_value` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category_details`
--

INSERT INTO `category_details` (`id`, `category_id`, `detail_name`, `detail_value`, `created_at`, `updated_at`) VALUES
(24, 13, 'Black', '1757840721_Product_Image_EOS_GP15_Black_3f7ce9de-8491-4410-862f-14ea17f21a16.webp', '2025-09-14 09:05:21', NULL),
(25, 13, 'Compatibility', 'PC, PlayStation, Xbox', '2025-09-14 09:05:21', NULL),
(26, 14, 'Black', '1757840990_Product_Image_EOS_Pro_Epsilon.webp', '2025-09-14 09:09:50', NULL),
(27, 14, 'White', '1757840990_Product_Image_EOS_Pro_Electra.webp', '2025-09-14 09:09:50', NULL),
(28, 14, 'Blue', '1757840990_Product_Image_EOS_Pro_Polaris.webp', '2025-09-14 09:09:50', NULL),
(29, 14, 'Orange', '1757840990_Product_Image_EOS_Pro_Solaris.webp', '2025-09-14 09:09:50', NULL),
(30, 14, 'Compatibility', 'PC, PlayStation, Xbox', '2025-09-14 09:09:50', NULL),
(31, 15, 'Black', '1757841105_Product_Image_EOS_Pro_II_Black_c8ec3215-ff54-4558-93b3-850b170c2a21.webp', '2025-09-14 09:11:45', NULL),
(32, 15, 'White', '1757841105_Product_Image_EOS_Pro_II_Grey_f613738f-8fc8-475b-ac82-0497a76be589.webp', '2025-09-14 09:11:45', NULL),
(33, 15, 'Compatibility', 'PC, PlayStation, Xbox', '2025-09-14 09:11:45', NULL),
(34, 16, 'Black', '1757841200_Product_Image_Nova_II_WGP16_Black.webp', '2025-09-14 09:13:20', NULL),
(35, 16, 'White', '1757841200_Product_Image_Nova_II_WGP16_White.webp', '2025-09-14 09:13:20', NULL),
(36, 16, 'Compatibility', 'PC, PlayStation, Xbox', '2025-09-14 09:13:20', NULL),
(37, 17, 'Black', '1757841385_Product_Image_Shooter_III_WGP13X_BLACK.webp', '2025-09-14 09:16:25', NULL),
(38, 17, 'White', '1757841385_Product_Image_Shooter_III_WGP13X_WHITE.webp', '2025-09-14 09:16:25', NULL),
(39, 17, 'Compatibility', 'PC, PlayStation, Xbox', '2025-09-14 09:16:25', NULL),
(40, 18, 'Black', '1757841513_Product_Image_Nova_Pro_Atomic_Black (1).webp', '2025-09-14 09:18:33', NULL),
(41, 18, 'Compatibility', 'PC, PlayStation, Xbox', '2025-09-14 09:18:33', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `fullname` varchar(50) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(250) NOT NULL,
  `address` varchar(250) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `fullname`, `phone`, `email`, `password`, `address`, `status`, `created_at`, `updated_at`, `updated_by`) VALUES
(11, 'anuraj', '1213123', 'anurajshrestha@gmail.com', '$2y$10$S6IYoQdQeaLi79yYvS01he7pwdjgZIRcfnrCxMqH76vxJr8HpeeUC', 'dhumbarai', 0, '2025-09-14 06:31:21', NULL, NULL),
(12, 'Anuraj Shrestha', '9848007539', 'anurajshrestha75@gmail.com', '$2y$10$tFqUcGRrFF9iq38FqvKVSeRBF5iyUb2Y37Gg.M2lsLZr7OF12CGwe', NULL, 0, '2025-09-14 06:43:10', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `total_amount` decimal(10,2) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `customer_id`, `order_date`, `total_amount`, `status`, `created_at`, `updated_at`, `updated_by`) VALUES
(73, 12, '2025-09-18 12:53:00', 150.00, 'Completed', '2025-09-18 12:53:00', '2025-09-18 12:53:05', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE `order_details` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `color` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `zip` varchar(20) DEFAULT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_details`
--

INSERT INTO `order_details` (`id`, `order_id`, `color`, `address`, `city`, `state`, `zip`, `product_id`, `quantity`, `unit_price`, `subtotal`, `created_at`, `updated_at`) VALUES
(71, 73, 'Black', 'Mandikahar', 'Kathmandu', 'Bagmati', '33600', 25, 1, 150.00, 150.00, '2025-09-18 12:53:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `payment_status` varchar(20) NOT NULL DEFAULT 'pending',
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `order_id`, `amount`, `payment_method`, `payment_status`, `payment_date`, `created_at`, `updated_at`) VALUES
(12, 73, 150.00, 'cod', 'completed', '2025-09-18 12:53:05', '2025-09-18 12:53:05', '2025-09-18 12:53:05');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock_quantity` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `description`, `price`, `stock_quantity`, `status`, `created_at`, `updated_at`) VALUES
(24, 13, 'EOS GP15', 'The Fantech EOS GP15 is a multi-platform wireless gamepad with tri-mode connectivity (Bluetooth, 2.4GHz, wired), Hall-effect anti-drift sticks and triggers, swappable thumbsticks/D-pad, rear mappable buttons, motion sensor, and long battery life — built for smooth, responsive, and customizable gaming across PC, mobile, and consoles.', 100.00, 10, 1, '2025-09-14 09:05:21', NULL),
(25, 14, 'EOS PRO WGP15', 'The EOS PRO joystick is a versatile, high-precision game controller featuring Hall-effect analog sticks and triggers, customizable thumbsticks and D-pad, mappable rear paddles, motion sensing, adjustable vibration, and multi-mode connectivity (Bluetooth, wireless, and wired) for PC, consoles, and mobile devices.', 150.00, 10, 1, '2025-09-14 09:09:50', NULL),
(26, 15, 'EOS PRO II SERIES', 'The EOS PRO II Series is Fantech’s next-gen wireless gamepad with Hall-Effect (drift-free) sticks and triggers, tri-mode connectivity (Bluetooth, 2.4GHz, wired), swappable thumbsticks/D-pads, rear paddles with macros, motion sensing, and long battery life. The Pro II S adds premium TMR sticks and mechanical buttons for extra precision.', 200.00, 10, 1, '2025-09-14 09:11:45', NULL),
(27, 16, 'NOVA II WGP16', 'Fantech Nova II WGP16 is a wireless multi-platform gamepad with Hall-effect sticks, Bluetooth 5.3 & USB-C, 6-axis gyro, touchpad, macro/turbo buttons, and a 600 mAh battery (10 hrs) — built for smooth, drift-free gaming on PC, console, and mobile.', 250.00, 10, 1, '2025-09-14 09:13:20', NULL),
(28, 17, 'SHOOTER III X', 'The Fantech Shooter III X is a wireless gaming controller with Hall-effect sticks, dual connectivity (Bluetooth & 2.4GHz), macro/turbo support, motion sensing, and a long-lasting battery, designed for smooth and precise gameplay across PC, console, and mobile.', 150.00, 15, 1, '2025-09-14 09:16:25', NULL),
(29, 18, 'NOVA PRO WGP14V2', 'The Fantech Nova Pro WGP14 V2 is a premium wireless gamepad featuring Hall-effect drift-free sticks, tri-mode connectivity (Bluetooth, 2.4GHz, wired), 6-axis gyro, mappable rear buttons, and a long battery life, built for pro-level multi-platform gaming.', 100.00, 10, 1, '2025-09-14 09:18:33', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(50) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(250) NOT NULL,
  `address` varchar(250) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `phone`, `email`, `password`, `address`, `status`, `created_at`, `updated_at`) VALUES
(2, 'anuraj', '98119202', 'anuraj@gmail.com', '$2y$10$4zLnO/L.wO9o2pSlp/3lxem2oCXmVr9qiO.YHYE6U5QoVvsDZ.4X6', 'Dumbarai', 0, '2025-09-10 08:40:52', NULL),
(3, 'raj', '9848007539', 'raj@gmail.com', '$2y$10$3YQfczbDlOnEGN2DiiFR5uaCe7uGo5OJJaI2iZ3GwgtPKHYUdP7sS', 'Mandikhatar', 0, '2025-09-14 06:16:54', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `fk_cart_order` (`order_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `category_details`
--
ALTER TABLE `category_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `updated_by` (`updated_by`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `updated_by` (`updated_by`);

--
-- Indexes for table `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

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
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `category_details`
--
ALTER TABLE `category_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT for table `order_details`
--
ALTER TABLE `order_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `fk_cart_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `category_details`
--
ALTER TABLE `category_details`
  ADD CONSTRAINT `category_details_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Constraints for table `customers`
--
ALTER TABLE `customers`
  ADD CONSTRAINT `customers_ibfk_1` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_details_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
