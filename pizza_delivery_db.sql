-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 25, 2026 at 09:17 AM
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
-- Database: `pizza_delivery_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE `menu` (
  `menu_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(8,2) NOT NULL,
  `size` enum('Small','Medium','Large') NOT NULL,
  `category` varchar(50) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`menu_id`, `name`, `description`, `price`, `size`, `category`, `image`, `is_available`, `created_at`) VALUES
(1, 'Margherita', 'Classic tomato and mozzarella', 149.00, 'Medium', 'Veg', 'margherita.jpeg', 1, '2026-03-26 18:59:44'),
(2, 'Pepperoni', 'Loaded with pepperoni slices', 249.00, 'Medium', 'Non-Veg', 'pepperoni.jpeg', 1, '2026-03-26 18:59:44'),
(5, 'Paneer Tikka', 'Spiced paneer with tandoori flavour', 229.00, 'Medium', 'Veg', 'paneer_tikka.jpeg', 1, '2026-03-26 18:59:44'),
(6, 'Chicken valcano double cheese', 'Grilled chicken with BBQ sauce', 299.00, 'Large', 'Non-Veg', 'cheese chicken.jpeg', 1, '2026-03-27 19:04:38'),
(7, 'Farm House', 'Bell peppers, onions, mushrooms', 199.00, 'Large', 'Veg', 'Farm house.jpeg', 1, '2026-03-27 19:04:38'),
(16, 'Chicken Sausage Ultimate Cheese', 'Chicken sausage, onion, extra molten cheese and a melty gooey Cheese Crown on the crust, you’ll want to Flip to the Cheese first', 249.00, 'Small', 'Non-veg', 'pizza_1777615415.jpeg', 1, '2026-05-01 11:33:35'),
(17, 'Chicken Tikka Ultimate Cheese', 'Tandoori-spiced chicken tikka, onion, tomato, tandoori sauce, extra molten cheese and a melty gooey cheese crown on the crust, you’ll want to Flip to the Cheese first.', 289.00, 'Small', 'Non-veg', 'pizza_1777615470.jpeg', 1, '2026-05-01 11:34:30'),
(18, 'Chicken Tikka Supreme', 'A divine combination of delicious Chicken Tikka & Malai Chicken Tikka, Onion, spicy Red Paprika with flavourful pan sauce and mozzarella cheese.', 299.00, 'Medium', 'Non-veg', 'pizza_1777615519.jpeg', 0, '2026-05-01 11:35:19'),
(19, 'Tandoori Paneer', 'It\'s our signature. Spiced Paneer, Crunchy Onions & Green Capsicum, spicy Red Paprika with delicious Tandoori Sauce and Mozzarella cheese!', 349.00, 'Large', 'Veg', 'pizza_1777615577.jpeg', 1, '2026-05-01 11:36:17'),
(20, 'Country Feast', 'Crust filled with creamy cheese, base loaded with gooey liquid cheese, topped with mozzarella cheese, herbed onion & green capsicum, sweet corn & tomatoes', 219.00, 'Small', 'Veg', 'pizza_1777615646.jpeg', 1, '2026-05-01 11:37:26'),
(21, 'Butter Chicken Pizza', 'Dil bole Butter Chicken! Desi vibes to your Italian delicacy with savory butter chicken gravy, chicken tikka chunks, capsicum, tomato, and crispy onions', 349.00, 'Medium', 'Non-veg', 'pizza_1777615698.jpeg', 1, '2026-05-01 11:38:18'),
(23, 'corn pizza', '', 353.00, 'Large', 'Veg', 'pizza_1782027175.jpeg', 1, '2026-06-21 13:02:55');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(8,2) NOT NULL,
  `delivery_addr` text NOT NULL,
  `status` enum('Confirmed','Preparing','Out for Delivery','Delivered') DEFAULT 'Confirmed',
  `order_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `total_amount`, `delivery_addr`, `status`, `order_date`) VALUES
(57, 15, 339.00, 'Prasana nilaya, gujjadi,576247', 'Delivered', '2026-05-23 11:13:44'),
(58, 15, 259.00, 'Prasana nilaya, gujjadi,576247', 'Delivered', '2026-05-23 11:31:30'),
(59, 15, 637.00, 'Prasana nilaya, gujjadi,576247', 'Delivered', '2026-05-23 16:13:39'),
(60, 15, 937.00, 'Prasana nilaya, gujjadi,576247', 'Out for Delivery', '2026-06-13 12:44:12'),
(62, 15, 588.00, 'Prasana nilaya, gujjadi,576247', 'Delivered', '2026-06-21 12:02:03'),
(63, 16, 637.00, 'Near light house, gangolli,576247', 'Delivered', '2026-06-21 12:16:11'),
(64, 16, 289.00, 'Near light house, gangolli,576247', 'Delivered', '2026-06-21 12:17:31'),
(86, 21, 338.00, 'dfgyhutr', 'Delivered', '2026-06-22 14:57:18'),
(87, 21, 837.00, 'dfgyhutr', 'Confirmed', '2026-06-22 14:57:53');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(8,2) NOT NULL,
  `subtotal` decimal(8,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`item_id`, `order_id`, `menu_id`, `quantity`, `unit_price`, `subtotal`) VALUES
(83, 57, 6, 1, 299.00, 299.00),
(84, 58, 20, 1, 219.00, 219.00),
(85, 59, 1, 2, 149.00, 298.00),
(86, 59, 6, 1, 299.00, 299.00),
(87, 60, 6, 3, 299.00, 897.00),
(90, 62, 2, 1, 249.00, 249.00),
(91, 62, 6, 1, 299.00, 299.00),
(92, 63, 7, 3, 199.00, 597.00),
(93, 64, 16, 1, 249.00, 249.00),
(118, 86, 1, 2, 149.00, 298.00),
(119, 87, 2, 1, 249.00, 249.00),
(120, 87, 6, 1, 299.00, 299.00),
(121, 87, 16, 1, 249.00, 249.00);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` decimal(8,2) NOT NULL,
  `payment_method` enum('COD','UPI') NOT NULL,
  `payment_status` enum('Pending','Completed','Failed') DEFAULT 'Pending',
  `payment_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `order_id`, `user_id`, `amount`, `payment_method`, `payment_status`, `payment_date`) VALUES
(47, 57, 15, 339.00, 'COD', 'Completed', '2026-05-23 11:13:47'),
(48, 58, 15, 259.00, 'COD', 'Completed', '2026-05-23 11:31:33'),
(49, 59, 15, 637.00, 'COD', 'Completed', '2026-05-23 16:13:51'),
(50, 60, 15, 937.00, 'UPI', 'Completed', '2026-06-13 12:44:20'),
(52, 62, 15, 588.00, 'COD', 'Completed', '2026-06-21 12:02:05'),
(53, 63, 16, 637.00, 'COD', 'Completed', '2026-06-21 12:16:29'),
(54, 64, 16, 289.00, 'UPI', 'Completed', '2026-06-21 12:18:00'),
(63, 86, 21, 338.00, 'COD', 'Completed', '2026-06-22 14:57:24'),
(64, 87, 21, 837.00, 'UPI', 'Completed', '2026-06-22 14:58:08');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `fname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `password` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `role` enum('user','admin') NOT NULL DEFAULT 'user',
  `created_at` datetime DEFAULT current_timestamp(),
  `lname` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `fname`, `email`, `phone`, `password`, `address`, `role`, `created_at`, `lname`) VALUES
(5, 'Admin', 'admin@pizza.com', '9999999999', '$2y$10$nlP6WV.M1o4uA.2DIhQVReErSPJGCAB.AnNSLNklhFS5to2tBi2um', 'Restaurant Address', 'admin', '2026-03-27 19:48:13', ''),
(15, 'Deeksha', 'deeksha29@gmail.com', '7259444232', '$2y$10$5K2FftKtJq6TQGIkJwUU9Oy8RCveNTuO665GYhFQTGbcdJRSAx1Ey', 'Prasana nilaya, gujjadi,576247', 'user', '2026-05-23 10:49:26', 'Nayak'),
(16, 'Thrisha', 'thrisha08@gmail.com', '7411007102', '$2y$10$nnB2Zeh.lrB62rZfIVvKKOymeNKziN4HUunPv6BtHrU5i1uEB4b5.', 'Near light house, gangolli,576247', 'user', '2026-05-23 10:51:28', 'Mogaveera'),
(17, 'Deeya', 'diyakharvi@gmail.com', '6361286790', '$2y$10$GLjwGtPlyG05DJ0TtXE/u.5WkdkiqDExVONJQfWfIFxbFEvhf303u', 'Diya house, Marvanthe,576201', 'user', '2026-05-23 10:53:22', 'Kharvi'),
(18, 'Vismitha', 'vismi19@gmail.com', '8088354677', '$2y$10$fpY/8LYF.bxIWW8h6ZvZNufuF8Z/ttVJYxYHwu0PDUb.cuy4KjDOq', 'Annapoorneshwari,main road panchayath koni,kundapura', 'user', '2026-05-23 11:01:00', 'Puthran'),
(21, 'amratha', 'ara@gmail.com', '9087655444', '$2y$10$fkCabXEfaXsxiIxaSWsa0eNpOR/tR5gfz01sPmp3y5NHNghQ8BT8W', 'dfgyhutr', 'user', '2026-06-22 14:54:36', 'dfdd');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`menu_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `menu_id` (`menu_id`),
  ADD KEY `fk_orderitems_order` (`order_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `fk_payments_order` (`order_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `menu`
--
ALTER TABLE `menu`
  MODIFY `menu_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=122;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_orderitems_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`menu_id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `fk_payments_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
