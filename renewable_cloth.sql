-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 12, 2025 at 01:40 PM
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
-- Database: `renewable_cloth`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_logs`
--

CREATE TABLE `admin_logs` (
  `log_id` char(36) NOT NULL,
  `admin_id` char(36) NOT NULL,
  `action_type` enum('Approved','Rejected','Deleted','Modified','Suspended') NOT NULL,
  `item_id` char(36) DEFAULT NULL,
  `user_id` char(36) DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `available_items`
-- (See below for the actual view)
--
CREATE TABLE `available_items` (
`item_id` char(36)
,`owner_id` char(36)
,`title` varchar(255)
,`description` text
,`category` enum('Shirt','Pants','Jacket','Dress','Shoes','Accessories','Other')
,`size` enum('XS','S','M','L','XL','XXL','One Size')
,`condition` enum('New','Like New','Good','Fair','Used')
,`tags` longtext
,`images` longtext
,`status` enum('Pending','Available','Swapped','Redeemed')
,`point_value` int(11)
,`created_at` timestamp
,`updated_at` timestamp
,`owner_name` varchar(100)
,`owner_points` int(11)
);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `name`, `description`, `icon`, `is_active`, `created_at`) VALUES
(1, 'Shirt', 'T-shirts, polo shirts, button-down shirts', 'fas fa-tshirt', 1, '2025-07-12 06:06:53'),
(2, 'Pants', 'Jeans, trousers, shorts, leggings', 'fas fa-socks', 1, '2025-07-12 06:06:53'),
(3, 'Jacket', 'Coats, jackets, blazers, hoodies', 'fas fa-mitten', 1, '2025-07-12 06:06:53'),
(4, 'Dress', 'Dresses, skirts, formal wear', 'fas fa-female', 1, '2025-07-12 06:06:53'),
(5, 'Shoes', 'Sneakers, boots, sandals, formal shoes', 'fas fa-shoe-prints', 1, '2025-07-12 06:06:53'),
(6, 'Accessories', 'Hats, scarves, belts, jewelry', 'fas fa-hat-cowboy', 1, '2025-07-12 06:06:53'),
(7, 'Other', 'Miscellaneous clothing items', 'fas fa-ellipsis-h', 1, '2025-07-12 06:06:53');

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `item_id` char(36) NOT NULL,
  `owner_id` char(36) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `category` enum('Shirt','Pants','Jacket','Dress','Shoes','Accessories','Other') NOT NULL,
  `size` enum('XS','S','M','L','XL','XXL','One Size') NOT NULL,
  `condition` enum('New','Like New','Good','Fair','Used') NOT NULL,
  `tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`tags`)),
  `images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`images`)),
  `status` enum('Pending','Available','Swapped','Redeemed') DEFAULT 'Pending',
  `point_value` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`item_id`, `owner_id`, `title`, `description`, `category`, `size`, `condition`, `tags`, `images`, `status`, `point_value`, `created_at`, `updated_at`) VALUES
('bc6d4963-5efa-11f0-9a48-88a4c2aaf9aa', 'user-uuid-001', 'Vintage Denim Jacket', 'Classic blue denim jacket in excellent condition. Perfect for layering in any season. Features a comfortable fit and timeless style.', 'Jacket', 'M', 'Good', '[\"vintage\", \"denim\", \"casual\", \"classic\"]', '[\"https://images.unsplash.com/photo-1576871337622-98d48d1cf531?w=400\", \"https://images.unsplash.com/photo-1591047139829-d91aecb6caea?w=400\"]', '', 150, '2025-07-12 08:32:24', '2025-07-12 10:48:19'),
('bc6f1d58-5efa-11f0-9a48-88a4c2aaf9aa', 'user-uuid-002', 'Organic Cotton T-Shirt', 'Soft, breathable organic cotton t-shirt. Perfect for everyday wear. Available in a neutral color that goes with everything.', 'Shirt', 'L', 'Like New', '[\"organic\", \"cotton\", \"casual\", \"sustainable\"]', '[\"https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=400\", \"https://images.unsplash.com/photo-1503341504253-dff4815485f1?w=400\"]', 'Available', 80, '2025-07-12 08:32:24', '2025-07-12 08:32:24'),
('bc6f1fca-5efa-11f0-9a48-88a4c2aaf9aa', 'user-uuid-001', 'Leather Crossbody Bag', 'Genuine leather crossbody bag with adjustable strap. Multiple compartments for organization. Perfect for daily use.', 'Accessories', 'One Size', 'Good', '[\"leather\", \"crossbody\", \"practical\", \"stylish\"]', '[\"https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=400\", \"https://images.unsplash.com/photo-1590874103328-eac38a683ce7?w=400\"]', 'Available', 200, '2025-07-12 08:32:24', '2025-07-12 08:32:24'),
('bc6f20c6-5efa-11f0-9a48-88a4c2aaf9aa', 'user-uuid-002', 'High-Waisted Jeans', 'Comfortable high-waisted jeans with a modern fit. Stretchy denim material that looks great and feels even better.', 'Pants', 'S', 'New', '[\"high-waisted\", \"stretchy\", \"modern\", \"comfortable\"]', '[\"https://images.unsplash.com/photo-1542272604-787c3835535d?w=400\", \"https://images.unsplash.com/photo-1541099649105-f69ad21f3246?w=400\"]', 'Available', 120, '2025-07-12 08:32:24', '2025-07-12 08:32:24'),
('bc6f21cd-5efa-11f0-9a48-88a4c2aaf9aa', 'user-uuid-001', 'Wool Sweater', 'Cozy wool sweater perfect for cold weather. Soft texture and comfortable fit. Available in a versatile neutral color.', 'Jacket', 'XL', 'Like New', '[\"wool\", \"warm\", \"cozy\", \"winter\"]', '[\"https://images.unsplash.com/photo-1434389677669-e08b4cac3105?w=400\", \"https://images.unsplash.com/photo-1551028719-00167b16eac5?w=400\"]', 'Available', 180, '2025-07-12 08:32:24', '2025-07-12 08:32:24'),
('bc6f229a-5efa-11f0-9a48-88a4c2aaf9aa', 'user-uuid-002', 'Summer Dress', 'Light and flowy summer dress perfect for warm weather. Features a flattering silhouette and comfortable fabric.', 'Dress', 'M', 'Good', '[\"summer\", \"flowy\", \"comfortable\", \"casual\"]', '[\"https://images.unsplash.com/photo-1515372039744-b8f02a3ae446?w=400\", \"https://images.unsplash.com/photo-1496747611176-843222e1e57c?w=400\"]', 'Available', 100, '2025-07-12 08:32:24', '2025-07-12 08:32:24'),
('bc6fbff2-5efa-11f0-9a48-88a4c2aaf9aa', 'user-uuid-001', 'Canvas Sneakers', 'Comfortable canvas sneakers perfect for everyday wear. Durable construction and classic design.', 'Shoes', '', 'Good', '[\"canvas\", \"sneakers\", \"casual\", \"comfortable\"]', '[\"https://images.unsplash.com/photo-1549298916-b41d501d3772?w=400\", \"https://images.unsplash.com/photo-1606107557195-0e29a4b5b4aa?w=400\"]', 'Available', 90, '2025-07-12 08:32:24', '2025-07-12 08:32:24');

-- --------------------------------------------------------

--
-- Table structure for table `item_reviews`
--

CREATE TABLE `item_reviews` (
  `review_id` char(36) NOT NULL,
  `item_id` char(36) NOT NULL,
  `reviewer_id` char(36) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `type` enum('swap_request','swap_accepted','swap_rejected','points_earned','item_approved','item_rejected','redemption_status') NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `related_id` char(36) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `point_transactions`
--

CREATE TABLE `point_transactions` (
  `transaction_id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `amount` int(11) NOT NULL,
  `type` enum('earned','spent','refunded','bonus','penalty') NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `related_item_id` char(36) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `redemptions`
--

CREATE TABLE `redemptions` (
  `redemption_id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `item_id` char(36) NOT NULL,
  `points_used` int(11) NOT NULL,
  `status` enum('Pending Shipment','Shipped','Delivered','Completed','Cancelled') DEFAULT 'Pending Shipment',
  `shipping_address` text DEFAULT NULL,
  `tracking_number` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `swap_requests`
--

CREATE TABLE `swap_requests` (
  `request_id` char(36) NOT NULL,
  `from_user_id` char(36) NOT NULL,
  `to_user_id` char(36) NOT NULL,
  `offered_item_id` char(36) NOT NULL,
  `requested_item_id` char(36) NOT NULL,
  `status` enum('Pending','Accepted','Rejected','Completed','Cancelled') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` char(36) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `points` int(11) DEFAULT 0,
  `role` enum('user','admin') DEFAULT 'user',
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `password_hash`, `points`, `role`, `status`, `created_at`, `updated_at`) VALUES
('admin-uuid-001', 'Admin User', 'admin@renewablecloth.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1000, 'admin', 'Pending', '2025-07-12 06:56:43', '2025-07-12 06:56:43'),
('user-uuid-001', 'John Doe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 500, 'user', 'Pending', '2025-07-12 06:56:43', '2025-07-12 06:56:43'),
('user-uuid-002', 'Jane Smith', 'jane@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 750, 'user', 'Pending', '2025-07-12 06:56:45', '2025-07-12 06:56:45'),
('user_6871fe313e0589.62350485', 'D24CE169', 'd24ce169@charusat.edu.in', '$2y$10$H1.JIaXO.eWzjaPjFCaAhOEG1OA24eHa5yBJmrd4X3e/GIBpT3Qn.', 0, 'user', 'Pending', '2025-07-12 06:18:25', '2025-07-12 06:18:25');

-- --------------------------------------------------------

--
-- Table structure for table `user_profiles`
--

CREATE TABLE `user_profiles` (
  `profile_id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `bio` text DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `avatar_url` varchar(500) DEFAULT NULL,
  `preferences` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`preferences`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `user_redemptions`
-- (See below for the actual view)
--
CREATE TABLE `user_redemptions` (
`redemption_id` char(36)
,`user_id` char(36)
,`item_id` char(36)
,`points_used` int(11)
,`status` enum('Pending Shipment','Shipped','Delivered','Completed','Cancelled')
,`shipping_address` text
,`tracking_number` varchar(100)
,`created_at` timestamp
,`updated_at` timestamp
,`item_title` varchar(255)
,`item_category` enum('Shirt','Pants','Jacket','Dress','Shoes','Accessories','Other')
,`user_name` varchar(100)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `user_swap_history`
-- (See below for the actual view)
--
CREATE TABLE `user_swap_history` (
`request_id` char(36)
,`from_user_id` char(36)
,`to_user_id` char(36)
,`offered_item_id` char(36)
,`requested_item_id` char(36)
,`status` enum('Pending','Accepted','Rejected','Completed','Cancelled')
,`created_at` timestamp
,`updated_at` timestamp
,`offered_item_title` varchar(255)
,`requested_item_title` varchar(255)
,`from_user_name` varchar(100)
,`to_user_name` varchar(100)
);

-- --------------------------------------------------------

--
-- Structure for view `available_items`
--
DROP TABLE IF EXISTS `available_items`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `available_items`  AS SELECT `i`.`item_id` AS `item_id`, `i`.`owner_id` AS `owner_id`, `i`.`title` AS `title`, `i`.`description` AS `description`, `i`.`category` AS `category`, `i`.`size` AS `size`, `i`.`condition` AS `condition`, `i`.`tags` AS `tags`, `i`.`images` AS `images`, `i`.`status` AS `status`, `i`.`point_value` AS `point_value`, `i`.`created_at` AS `created_at`, `i`.`updated_at` AS `updated_at`, `u`.`name` AS `owner_name`, `u`.`points` AS `owner_points` FROM (`items` `i` join `users` `u` on(`i`.`owner_id` = `u`.`user_id`)) WHERE `i`.`status` = 'Available' ORDER BY `i`.`created_at` DESC ;

-- --------------------------------------------------------

--
-- Structure for view `user_redemptions`
--
DROP TABLE IF EXISTS `user_redemptions`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `user_redemptions`  AS SELECT `r`.`redemption_id` AS `redemption_id`, `r`.`user_id` AS `user_id`, `r`.`item_id` AS `item_id`, `r`.`points_used` AS `points_used`, `r`.`status` AS `status`, `r`.`shipping_address` AS `shipping_address`, `r`.`tracking_number` AS `tracking_number`, `r`.`created_at` AS `created_at`, `r`.`updated_at` AS `updated_at`, `i`.`title` AS `item_title`, `i`.`category` AS `item_category`, `u`.`name` AS `user_name` FROM ((`redemptions` `r` join `items` `i` on(`r`.`item_id` = `i`.`item_id`)) join `users` `u` on(`r`.`user_id` = `u`.`user_id`)) ORDER BY `r`.`created_at` DESC ;

-- --------------------------------------------------------

--
-- Structure for view `user_swap_history`
--
DROP TABLE IF EXISTS `user_swap_history`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `user_swap_history`  AS SELECT `sr`.`request_id` AS `request_id`, `sr`.`from_user_id` AS `from_user_id`, `sr`.`to_user_id` AS `to_user_id`, `sr`.`offered_item_id` AS `offered_item_id`, `sr`.`requested_item_id` AS `requested_item_id`, `sr`.`status` AS `status`, `sr`.`created_at` AS `created_at`, `sr`.`updated_at` AS `updated_at`, `i1`.`title` AS `offered_item_title`, `i2`.`title` AS `requested_item_title`, `u1`.`name` AS `from_user_name`, `u2`.`name` AS `to_user_name` FROM ((((`swap_requests` `sr` join `items` `i1` on(`sr`.`offered_item_id` = `i1`.`item_id`)) join `items` `i2` on(`sr`.`requested_item_id` = `i2`.`item_id`)) join `users` `u1` on(`sr`.`from_user_id` = `u1`.`user_id`)) join `users` `u2` on(`sr`.`to_user_id` = `u2`.`user_id`)) ORDER BY `sr`.`created_at` DESC ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_admin_id` (`admin_id`),
  ADD KEY `idx_action_type` (`action_type`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `idx_owner_id` (`owner_id`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_point_value` (`point_value`),
  ADD KEY `idx_items_category_status` (`category`,`status`);

--
-- Indexes for table `item_reviews`
--
ALTER TABLE `item_reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD UNIQUE KEY `unique_item_reviewer` (`item_id`,`reviewer_id`),
  ADD KEY `idx_item_id` (`item_id`),
  ADD KEY `idx_reviewer_id` (`reviewer_id`),
  ADD KEY `idx_rating` (`rating`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_is_read` (`is_read`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_notifications_user_read` (`user_id`,`is_read`);

--
-- Indexes for table `point_transactions`
--
ALTER TABLE `point_transactions`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `related_item_id` (`related_item_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_type` (`type`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `redemptions`
--
ALTER TABLE `redemptions`
  ADD PRIMARY KEY (`redemption_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_item_id` (`item_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_redemptions_status_created` (`status`,`created_at`);

--
-- Indexes for table `swap_requests`
--
ALTER TABLE `swap_requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `offered_item_id` (`offered_item_id`),
  ADD KEY `requested_item_id` (`requested_item_id`),
  ADD KEY `idx_from_user_id` (`from_user_id`),
  ADD KEY `idx_to_user_id` (`to_user_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_swap_requests_status_created` (`status`,`created_at`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_role` (`role`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD PRIMARY KEY (`profile_id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD CONSTRAINT `admin_logs_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `admin_logs_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `items` (`item_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `admin_logs_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `items`
--
ALTER TABLE `items`
  ADD CONSTRAINT `items_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `item_reviews`
--
ALTER TABLE `item_reviews`
  ADD CONSTRAINT `item_reviews_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`item_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `item_reviews_ibfk_2` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `point_transactions`
--
ALTER TABLE `point_transactions`
  ADD CONSTRAINT `point_transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `point_transactions_ibfk_2` FOREIGN KEY (`related_item_id`) REFERENCES `items` (`item_id`) ON DELETE SET NULL;

--
-- Constraints for table `redemptions`
--
ALTER TABLE `redemptions`
  ADD CONSTRAINT `redemptions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `redemptions_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `items` (`item_id`) ON DELETE CASCADE;

--
-- Constraints for table `swap_requests`
--
ALTER TABLE `swap_requests`
  ADD CONSTRAINT `swap_requests_ibfk_1` FOREIGN KEY (`from_user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `swap_requests_ibfk_2` FOREIGN KEY (`to_user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `swap_requests_ibfk_3` FOREIGN KEY (`offered_item_id`) REFERENCES `items` (`item_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `swap_requests_ibfk_4` FOREIGN KEY (`requested_item_id`) REFERENCES `items` (`item_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD CONSTRAINT `user_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
