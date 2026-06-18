-- phpMyAdmin SQL Dump
-- version 4.9.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 04, 2026 at 05:48 PM
-- Server version: 10.4.10-MariaDB
-- PHP Version: 7.3.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `clothingstore`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbladdress`
--

DROP TABLE IF EXISTS `tbladdress`;
CREATE TABLE IF NOT EXISTS `tbladdress` (
  `address_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `address_label` enum('Home','Work','Other') COLLATE utf8mb4_unicode_ci NOT NULL,
  `street_address` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `suburb` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `province` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `postal_code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`address_id`),
  KEY `fk_address_user` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbladdress`
--

INSERT INTO `tbladdress` (`address_id`, `user_id`, `address_label`, `street_address`, `suburb`, `city`, `province`, `postal_code`, `is_default`, `created_at`) VALUES
(1, 1, 'Home', '12 Palm Street', 'Rosebank', 'Johannesburg', 'Gauteng', '2196', 1, '2026-05-04 15:55:09'),
(2, 2, 'Work', '45 Market Avenue', 'Cape Town CBD', 'Cape Town', 'Western Cape', '8001', 1, '2026-05-04 15:55:09'),
(3, 3, 'Home', '18 Ridge Road', 'Umhlanga', 'Durban', 'KwaZulu-Natal', '4319', 1, '2026-05-04 15:55:09'),
(4, 4, 'Other', '7 School Lane', 'Walmer', 'Gqeberha', 'Eastern Cape', '6070', 1, '2026-05-04 15:55:09'),
(5, 5, 'Home', '22 Protea Drive', 'Polokwane Central', 'Polokwane', 'Limpopo', '0700', 1, '2026-05-04 15:55:09'),
(6, 6, 'Home', '85 Sunny Rd', 'Parkview', 'Johannesburg', 'Gauteng', '2023', 1, '2026-05-04 16:58:45');

-- --------------------------------------------------------

--
-- Table structure for table `tbladmin`
--

DROP TABLE IF EXISTS `tbladmin`;
CREATE TABLE IF NOT EXISTS `tbladmin` (
  `admin_id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`admin_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbladmin`
--

INSERT INTO `tbladmin` (`admin_id`, `full_name`, `username`, `email`, `password_hash`, `created_at`) VALUES
(1, 'Admin One', 'admin@pastimes.co.za', 'admin@pastimes.co.za', '25d55ad283aa400af464c76d713c07ad', '2026-05-04 15:55:09'),
(2, 'Lerato Admin', 'lerato.admin@pastimes.co.za', 'lerato.admin@pastimes.co.za', '25d55ad283aa400af464c76d713c07ad', '2026-05-04 15:55:09'),
(3, 'Sizwe Control', 'sizwe.control@pastimes.co.za', 'sizwe.control@pastimes.co.za', '25d55ad283aa400af464c76d713c07ad', '2026-05-04 15:55:09'),
(4, 'Mia Support', 'mia.support@pastimes.co.za', 'mia.support@pastimes.co.za', '25d55ad283aa400af464c76d713c07ad', '2026-05-04 15:55:09'),
(5, 'Kopano Lead', 'kopano.lead@pastimes.co.za', 'kopano.lead@pastimes.co.za', '25d55ad283aa400af464c76d713c07ad', '2026-05-04 15:55:09');

-- --------------------------------------------------------

--
-- Table structure for table `tblcartitem`
--

DROP TABLE IF EXISTS `tblcartitem`;
CREATE TABLE IF NOT EXISTS `tblcartitem` (
  `cart_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `clothes_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`cart_id`),
  KEY `fk_cart_user` (`user_id`),
  KEY `fk_cart_clothes` (`clothes_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tblcartitem`
--

INSERT INTO `tblcartitem` (`cart_id`, `user_id`, `clothes_id`, `quantity`, `created_at`) VALUES
(1, 1, 1, 1, '2026-05-04 15:55:09'),
(2, 1, 2, 1, '2026-05-04 15:55:09'),
(3, 2, 3, 2, '2026-05-04 15:55:09'),
(4, 4, 4, 1, '2026-05-04 15:55:09'),
(5, 4, 5, 1, '2026-05-04 15:55:09');

-- --------------------------------------------------------

--
-- Table structure for table `tblclothes`
--

DROP TABLE IF EXISTS `tblclothes`;
CREATE TABLE IF NOT EXISTS `tblclothes` (
  `clothes_id` int(11) NOT NULL AUTO_INCREMENT,
  `seller_id` int(11) DEFAULT NULL,
  `title` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `brand` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gender` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `size_label` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `condition_rating` tinyint(4) NOT NULL,
  `sell_price` decimal(10,2) NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `inventory_quantity` int(11) NOT NULL DEFAULT 1,
  `status` enum('pending','approved','sold','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'approved',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`clothes_id`),
  KEY `fk_clothes_seller` (`seller_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tblclothes`
--

INSERT INTO `tblclothes` (`clothes_id`, `seller_id`, `title`, `brand`, `category`, `gender`, `size_label`, `condition_rating`, `sell_price`, `description`, `image_path`, `inventory_quantity`, `status`, `created_at`, `updated_at`) VALUES
(1, 3, 'Floral Summer Dress', 'Zara', 'Dresses', 'Women', 'M', 4, '450.00', 'Bright floral dress in excellent condition.', 'assets/images/products/floral-dress.jpg', 1, 'approved', '2026-05-04 15:55:09', '2026-05-04 15:55:09'),
(2, 3, 'Vintage Denim Jacket', 'Levi\'s', 'Outerwear', 'Women', 'L', 5, '750.00', 'Classic oversized denim jacket with minimal wear.', 'assets/images/products/denim-jacket.jpg', 1, 'approved', '2026-05-04 15:55:09', '2026-05-04 15:55:09'),
(3, 3, 'Silk Blouse', 'H&M', 'Tops', 'Women', 'S', 3, '280.00', 'Lightweight silk-blend blouse for work or weekends.', 'assets/images/products/silk-blouse.jpg', 0, 'sold', '2026-05-04 15:55:09', '2026-05-04 16:58:57'),
(4, 3, 'Men\'s Oxford Shirt', 'Country Road', 'Tops', 'Men', 'M', 4, '320.00', 'Smart casual oxford shirt with button-down collar.', 'assets/images/products/fallback-product.jpg', 1, 'approved', '2026-05-04 15:55:09', '2026-05-04 15:55:09'),
(5, 3, 'Chelsea Boots', 'Aldo', 'Shoes', 'Unisex', '8', 4, '690.00', 'Well-kept faux leather boots with sturdy soles.', 'assets/images/products/running-shoes.jpg', 1, 'approved', '2026-05-04 15:55:09', '2026-05-04 15:55:09'),
(6, 3, 'Tailored Wool Coat', 'Mango', 'Outerwear', 'Women', 'M', 5, '1200.00', 'Warm tailored coat with a polished pre-loved finish.', 'assets/images/products/wool-coat.jpg', 1, 'approved', '2026-05-04 15:55:09', '2026-05-04 15:55:09'),
(7, 3, 'Structured Leather Bag', 'Topshop', 'Accessories', 'Women', 'One Size', 4, '540.00', 'Compact leather-look bag with clean hardware and everyday space.', 'assets/images/products/leather-bag.jpg', 1, 'approved', '2026-05-04 15:55:09', '2026-05-04 15:55:09');

-- --------------------------------------------------------

--
-- Table structure for table `tblproductimage`
--

DROP TABLE IF EXISTS `tblproductimage`;
CREATE TABLE IF NOT EXISTS `tblproductimage` (
  `image_id` int(11) NOT NULL AUTO_INCREMENT,
  `clothes_id` int(11) NOT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 1,
  `alt_text` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`image_id`),
  UNIQUE KEY `ux_product_image_sort` (`clothes_id`,`sort_order`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tblproductimage`
--

INSERT INTO `tblproductimage` (`image_id`, `clothes_id`, `image_path`, `sort_order`, `alt_text`, `created_at`) VALUES
(1, 1, 'assets/images/products/floral-dress.jpg', 1, 'Floral Summer Dress', '2026-05-04 15:55:09'),
(2, 2, 'assets/images/products/denim-jacket.jpg', 1, 'Vintage Denim Jacket', '2026-05-04 15:55:09'),
(3, 3, 'assets/images/products/silk-blouse.jpg', 1, 'Silk Blouse', '2026-05-04 15:55:09'),
(4, 4, 'assets/images/products/fallback-product.jpg', 1, 'Men\'s Oxford Shirt', '2026-05-04 15:55:09'),
(5, 5, 'assets/images/products/running-shoes.jpg', 1, 'Chelsea Boots', '2026-05-04 15:55:09'),
(6, 6, 'assets/images/products/wool-coat.jpg', 1, 'Tailored Wool Coat', '2026-05-04 15:55:09'),
(7, 7, 'assets/images/products/leather-bag.jpg', 1, 'Structured Leather Bag', '2026-05-04 15:55:09');

-- --------------------------------------------------------

--
-- Table structure for table `tblmessage`
--

DROP TABLE IF EXISTS `tblmessage`;
CREATE TABLE IF NOT EXISTS `tblmessage` (
  `message_id` int(11) NOT NULL AUTO_INCREMENT,
  `sender_user_id` int(11) DEFAULT NULL,
  `sender_admin_id` int(11) DEFAULT NULL,
  `receiver_user_id` int(11) DEFAULT NULL,
  `receiver_admin_id` int(11) DEFAULT NULL,
  `related_order_id` int(11) DEFAULT NULL,
  `title` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message_body` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_broadcast` tinyint(1) NOT NULL DEFAULT 0,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`message_id`),
  KEY `fk_message_sender_user` (`sender_user_id`),
  KEY `fk_message_sender_admin` (`sender_admin_id`),
  KEY `fk_message_receiver_user` (`receiver_user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tblmessage`
--

INSERT INTO `tblmessage` (`message_id`, `sender_user_id`, `sender_admin_id`, `receiver_user_id`, `receiver_admin_id`, `related_order_id`, `title`, `message_body`, `is_broadcast`, `is_read`, `created_at`) VALUES
(1, 1, 1, 3, NULL, NULL, 'Dress enquiry', 'Hi, is the floral dress still available for immediate shipping?', 0, 1, '2026-05-04 15:55:09'),
(2, 3, 1, 1, NULL, NULL, 'Re: Dress enquiry', 'Yes, the dress is available and ready to ship this week.', 0, 0, '2026-05-04 15:55:09'),
(3, 1, 1, 1, NULL, NULL, 'Verification update', 'Your customer profile has been verified by the admin team.', 1, 0, '2026-05-04 15:55:09'),
(4, 1, 1, 2, NULL, NULL, 'Registration pending', 'Your account is pending customer verification by an administrator.', 1, 0, '2026-05-04 15:55:09'),
(5, 4, 1, 3, NULL, NULL, 'Sizing question', 'Could you confirm whether the oxford shirt fits true to size?', 0, 0, '2026-05-04 15:55:09'),
(6, NULL, 1, 6, NULL, NULL, 'Customer verification', 'Your customer profile has been verified. You may now log in.', 0, 1, '2026-05-04 16:54:37');

-- --------------------------------------------------------

--
-- Table structure for table `tblorder`
--

DROP TABLE IF EXISTS `tblorder`;
CREATE TABLE IF NOT EXISTS `tblorder` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `address_id` int(11) NOT NULL,
  `order_total` decimal(10,2) NOT NULL,
  `order_reference` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `session_reference` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('pending','dispatched','delivered','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`order_id`),
  KEY `fk_order_user` (`user_id`),
  KEY `fk_order_address` (`address_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tblorder`
--

INSERT INTO `tblorder` (`order_id`, `user_id`, `address_id`, `order_total`, `order_reference`, `session_reference`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '450.00', NULL, NULL, 'pending', '2026-05-04 15:55:09', '2026-05-04 15:55:09'),
(2, 2, 2, '1060.00', NULL, NULL, 'dispatched', '2026-05-04 15:55:09', '2026-05-04 15:55:09'),
(3, 4, 4, '320.00', NULL, NULL, 'delivered', '2026-05-04 15:55:09', '2026-05-04 15:55:09'),
(4, 1, 1, '690.00', NULL, NULL, 'cancelled', '2026-05-04 15:55:09', '2026-05-04 15:55:09'),
(5, 3, 3, '750.00', NULL, NULL, 'pending', '2026-05-04 15:55:09', '2026-05-04 15:55:09'),
(6, 6, 6, '280.00', NULL, NULL, 'pending', '2026-05-04 16:58:57', '2026-05-04 16:58:57');

-- --------------------------------------------------------

--
-- Table structure for table `tblorderitem`
--

DROP TABLE IF EXISTS `tblorderitem`;
CREATE TABLE IF NOT EXISTS `tblorderitem` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `clothes_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price_each` decimal(10,2) NOT NULL,
  PRIMARY KEY (`item_id`),
  KEY `fk_order_item_order` (`order_id`),
  KEY `fk_order_item_clothes` (`clothes_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tblorderitem`
--

INSERT INTO `tblorderitem` (`item_id`, `order_id`, `clothes_id`, `quantity`, `price_each`) VALUES
(1, 1, 1, 1, '450.00'),
(2, 2, 3, 1, '280.00'),
(3, 2, 5, 1, '780.00'),
(4, 3, 4, 1, '320.00'),
(5, 4, 5, 1, '690.00'),
(6, 6, 3, 1, '280.00');

-- --------------------------------------------------------

--
-- Table structure for table `tblsellerapplication`
--

DROP TABLE IF EXISTS `tblsellerapplication`;
CREATE TABLE IF NOT EXISTS `tblsellerapplication` (
  `application_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `id_number` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `motivation` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `admin_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reviewed_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`application_id`),
  KEY `fk_application_user` (`user_id`),
  KEY `fk_application_admin` (`admin_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tblsellerapplication`
--

INSERT INTO `tblsellerapplication` (`application_id`, `user_id`, `id_number`, `motivation`, `status`, `admin_id`, `created_at`, `reviewed_at`) VALUES
(1, 2, '9801015800081', 'I want to sell premium pre-loved items from my wardrobe.', 'pending', NULL, '2026-05-04 15:55:09', NULL),
(2, 4, '9605054800082', 'I already run social media thrift drops and want a safer platform.', 'pending', NULL, '2026-05-04 15:55:09', NULL),
(3, 1, '9501015800083', 'I have quality branded clothes ready for listing.', 'approved', NULL, '2026-05-04 15:55:09', NULL),
(4, 5, '9303035800084', 'I want to declutter and sell authenticated items.', 'rejected', NULL, '2026-05-04 15:55:09', NULL),
(5, 3, '9204045800085', 'Existing verified seller renewal sample.', 'approved', NULL, '2026-05-04 15:55:09', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tbluser`
--

DROP TABLE IF EXISTS `tbluser`;
CREATE TABLE IF NOT EXISTS `tbluser` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone_number` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profile_image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` enum('customer','seller','admin') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'customer',
  `customer_status` enum('pending','verified','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `seller_status` enum('none','pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'none',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbluser`
--

INSERT INTO `tbluser` (`user_id`, `full_name`, `first_name`, `last_name`, `username`, `email`, `password_hash`, `phone_number`, `profile_image_path`, `role`, `customer_status`, `seller_status`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'John Doe', 'John', 'Doe', 'jdoe', 'j.doe@abc.co.za', '25d55ad283aa400af464c76d713c07ad', '0823456789', NULL, 'customer', 'verified', 'none', 1, '2026-05-04 15:55:09', '2026-05-04 15:55:09'),
(2, 'Ayanda Mokoena', 'Ayanda', 'Mokoena', 'ayanda', 'ayanda@pastimes.co.za', '25d55ad283aa400af464c76d713c07ad', '0811234567', NULL, 'customer', 'pending', 'none', 1, '2026-05-04 15:55:09', '2026-05-04 15:55:09'),
(3, 'Lebo Khumalo', 'Lebo', 'Khumalo', 'lebo.k', 'lebo@pastimes.co.za', '25d55ad283aa400af464c76d713c07ad', '0831112222', NULL, 'seller', 'verified', 'approved', 1, '2026-05-04 15:55:09', '2026-05-04 15:55:09'),
(4, 'Naledi Smith', 'Naledi', 'Smith', 'naledi', 'naledi@pastimes.co.za', '25d55ad283aa400af464c76d713c07ad', '0849876543', NULL, 'customer', 'verified', 'pending', 1, '2026-05-04 15:55:09', '2026-05-04 15:55:09'),
(5, 'Chris Naidoo', 'Chris', 'Naidoo', 'cnaidoo', 'chris@pastimes.co.za', '25d55ad283aa400af464c76d713c07ad', '0725678901', NULL, 'customer', 'rejected', 'none', 1, '2026-05-04 15:55:09', '2026-05-04 15:55:09'),
(6, 'Sibongiseni Collel Ngwamba', 'Sibongiseni', 'Collel Ngwamba', 'sNgwa', 'sibongiseni.doe@mail.co.za', '$2y$10$/avNZDhyHe2LHXBa.oEyUOXhMizoABjtqNeOujXFC8YbqTJrlj8VO', '0855202580', NULL, 'customer', 'verified', 'none', 1, '2026-05-04 16:53:37', '2026-05-04 16:54:37');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
