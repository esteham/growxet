-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 27, 2025 at 03:18 PM
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
-- Database: `grownet`
--

-- --------------------------------------------------------

--
-- Table structure for table `balance_transactions`
--

CREATE TABLE `balance_transactions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` enum('deposit','withdraw','investment','admin_adjustment','profit','donation','loan_disbursement','loan_repayment','loan_cancellation','penalty_charge','loan_pool') NOT NULL,
  `transfer_type` enum('bank','card','bkash','grownet') DEFAULT 'bank',
  `amount` decimal(12,2) NOT NULL,
  `bank_account` varchar(255) DEFAULT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `currency` varchar(10) DEFAULT 'bdt',
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `uniqid` varchar(32) DEFAULT NULL,
  `sender_id` int(11) DEFAULT NULL,
  `receiver_id` int(11) DEFAULT NULL,
  `payment_method` enum('bank','card','bkash') DEFAULT 'bank',
  `card_number` varchar(32) DEFAULT NULL,
  `card_holder` varchar(100) DEFAULT NULL,
  `card_cvv` varchar(10) DEFAULT NULL,
  `bkash_phone` varchar(20) DEFAULT NULL,
  `bkash_pin` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `balance_transactions`
--

INSERT INTO `balance_transactions` (`id`, `user_id`, `type`, `transfer_type`, `amount`, `bank_account`, `bank_name`, `currency`, `description`, `created_at`, `uniqid`, `sender_id`, `receiver_id`, `payment_method`, `card_number`, `card_holder`, `card_cvv`, `bkash_phone`, `bkash_pin`) VALUES
(1, 15, 'deposit', 'bank', 150000.00, '168923458787', 'Bangladesh Bank', 'bdt', 'Deposit from bank account 168923458787 (Bangladesh Bank)', '2025-06-23 15:46:24', NULL, 15, NULL, 'bank', NULL, NULL, NULL, NULL, NULL),
(2, 15, 'withdraw', 'bank', 150000.00, '168923458787', 'Bangladesh Bank', 'bdt', 'Withdrawal to bank account 168923458787 (Bangladesh Bank)', '2025-06-23 15:47:05', NULL, 15, NULL, 'bank', NULL, NULL, NULL, NULL, NULL),
(3, 8, 'deposit', 'bank', 15678000.00, '1343263477385859', 'Pubali Bank Ltd.', 'bdt', 'Deposit from bank account 1343263477385859 (Pubali Bank Ltd.)', '2025-06-23 20:14:01', NULL, 8, NULL, 'bank', NULL, NULL, NULL, NULL, NULL),
(4, 15, 'deposit', 'bank', 1245000.00, '168923458787', 'Sonali Bank Ltd.', 'bdt', 'Deposit from bank account 168923458787 (Sonali Bank Ltd.)', '2025-06-23 20:14:32', NULL, 15, NULL, 'bank', NULL, NULL, NULL, NULL, NULL),
(5, 15, 'investment', 'bank', -162000.00, NULL, NULL, 'bdt', 'Investment in project ID 13 (12 shares)', '2025-06-23 20:14:45', NULL, NULL, NULL, 'bank', NULL, NULL, NULL, NULL, NULL),
(6, 15, 'investment', 'bank', -35000.00, NULL, NULL, 'bdt', 'Investment in project ID 12 (1 shares)', '2025-06-23 20:34:08', 'txn_6859ba4082434', NULL, NULL, 'bank', NULL, NULL, NULL, NULL, NULL),
(7, 8, 'investment', 'bank', 35000.00, NULL, NULL, 'bdt', 'Investment received for project ID 12 (1 shares) from user 15', '2025-06-23 20:34:08', 'txn_6859ba4082434', NULL, NULL, 'bank', NULL, NULL, NULL, NULL, NULL),
(8, 15, 'investment', 'bank', -70000.00, NULL, NULL, 'bdt', 'Investment in project ID 12 (2 shares)', '2025-06-23 20:51:52', 'txn_6859be6825bda', NULL, NULL, 'bank', NULL, NULL, NULL, NULL, NULL),
(9, 8, 'investment', 'bank', 70000.00, NULL, NULL, 'bdt', 'Investment received for project ID 12 (2 shares) from user 15', '2025-06-23 20:51:52', 'txn_6859be6825bda', NULL, NULL, 'bank', NULL, NULL, NULL, NULL, NULL),
(10, 15, 'investment', 'bank', -35000.00, NULL, NULL, 'bdt', 'Investment in project ID 12 (1 shares)', '2025-06-23 20:57:15', 'txn_6859bfab82b9b', NULL, NULL, 'bank', NULL, NULL, NULL, NULL, NULL),
(11, 8, 'investment', 'bank', 35000.00, NULL, NULL, 'bdt', 'Investment received for project ID 12 (1 shares) from user 15', '2025-06-23 20:57:15', 'txn_6859bfab82b9b', NULL, NULL, 'bank', NULL, NULL, NULL, NULL, NULL),
(12, 15, 'investment', 'bank', -35000.00, NULL, NULL, 'bdt', 'Investment in project ID 12 (1 shares)', '2025-06-24 03:21:53', 'txn_685a19d1a7ec8', NULL, NULL, 'bank', NULL, NULL, NULL, NULL, NULL),
(13, 8, 'investment', 'bank', 35000.00, NULL, NULL, 'bdt', 'Investment received for project ID 12 (1 shares) from user 15', '2025-06-24 03:21:53', 'txn_685a19d1a7ec8', NULL, NULL, 'bank', NULL, NULL, NULL, NULL, NULL),
(14, 15, 'investment', 'bank', -150000.00, NULL, NULL, 'bdt', 'Investment in project ID 16 (3 shares)', '2025-06-27 15:30:47', 'txn_685eb927b238c', NULL, NULL, 'bank', NULL, NULL, NULL, NULL, NULL),
(15, 8, 'investment', 'bank', 150000.00, NULL, NULL, 'bdt', 'Investment received for project ID 16 (3 shares) from user 15', '2025-06-27 15:30:47', 'txn_685eb927b238c', NULL, NULL, 'bank', NULL, NULL, NULL, NULL, NULL),
(16, 15, '', 'bank', -150000.00, NULL, NULL, 'bdt', 'Investment in project ID 16 (3 shares)', '2025-06-28 00:59:23', NULL, NULL, NULL, 'bank', NULL, NULL, NULL, NULL, NULL),
(17, 15, '', 'bank', -150000.00, NULL, NULL, 'bdt', 'Investment in project ID 16 (3 shares)', '2025-06-28 00:59:23', 'txn_685f3e6bab69d', 0, 0, 'bank', NULL, NULL, NULL, NULL, NULL),
(18, 8, '', 'bank', 150000.00, NULL, NULL, 'bdt', 'Investment received for project ID 16 (3 shares) from user UID_6858f55d3bfe2', '2025-06-28 00:59:23', 'txn_685f3e6bab69d', 0, 0, 'bank', NULL, NULL, NULL, NULL, NULL),
(19, 15, 'investment', 'bank', -50000.00, NULL, NULL, 'bdt', 'Investment in project ID 16 (1 shares)', '2025-06-28 01:10:49', 'txn_685f411932710', 0, 0, 'bank', NULL, NULL, NULL, NULL, NULL),
(20, 8, 'investment', 'bank', 50000.00, NULL, NULL, 'bdt', 'Investment received for project ID 16 (1 shares) from user UID_6858f55d3bfe2', '2025-06-28 01:10:49', 'txn_685f411932710', 0, 0, 'bank', NULL, NULL, NULL, NULL, NULL),
(21, 15, 'investment', 'bank', -50000.00, NULL, NULL, 'bdt', 'Investment in project ID 16 (1 shares)', '2025-06-28 01:10:49', 'txn_685f411960c3c', 15, 8, 'bank', NULL, NULL, NULL, NULL, NULL),
(22, 8, 'investment', 'bank', 50000.00, NULL, NULL, 'bdt', 'Investment received for project ID 16 (1 shares) from user UID_6858f55d3bfe2', '2025-06-28 01:10:49', 'txn_685f411960c3c', 15, 8, 'bank', NULL, NULL, NULL, NULL, NULL),
(23, 8, 'investment', 'bank', -80000.00, NULL, NULL, 'bdt', 'Investment in project ID 15 (4 shares)', '2025-06-28 01:28:18', 'txn_685f4532bad58', 0, 0, 'bank', NULL, NULL, NULL, NULL, NULL),
(24, 8, 'investment', 'bank', 80000.00, NULL, NULL, 'bdt', 'Investment received for project ID 15 (4 shares) from user UID_6852439a70808', '2025-06-28 01:28:18', 'txn_685f4532bad58', 0, 0, 'bank', NULL, NULL, NULL, NULL, NULL),
(25, 15, 'investment', 'bank', -40000.00, NULL, NULL, 'bdt', 'Investment in project ID 15 (2 shares)', '2025-06-28 01:28:50', 'txn_685f455224ef0', 0, 0, 'bank', NULL, NULL, NULL, NULL, NULL),
(26, 8, 'investment', 'bank', 40000.00, NULL, NULL, 'bdt', 'Investment received for project ID 15 (2 shares) from user UID_6858f55d3bfe2', '2025-06-28 01:28:50', 'txn_685f455224ef0', 0, 0, 'bank', NULL, NULL, NULL, NULL, NULL),
(27, 8, 'admin_adjustment', 'bank', -12500.00, NULL, 'GrowNet', 'bdt', 'Balance transfer to user ID 15, account 168923458787 (Sonali Bank Ltd.)', '2025-06-28 20:57:53', NULL, 0, 0, 'bank', NULL, NULL, NULL, NULL, NULL),
(28, 15, 'admin_adjustment', 'bank', 12500.00, NULL, 'GrowNet', 'bdt', 'Balance received from user ID 8', '2025-06-28 20:57:53', NULL, 0, 0, 'bank', NULL, NULL, NULL, NULL, NULL),
(29, 8, 'deposit', 'bank', 135000.00, '168923458787', 'Sonali Bank Ltd.', 'bdt', 'Deposit from bank account 168923458787 (Sonali Bank Ltd.)', '2025-07-02 05:13:14', NULL, 8, NULL, 'bank', '', '', '', '', ''),
(30, 15, 'investment', 'bank', -150000.00, NULL, NULL, 'bdt', 'Investment in project ID 16 (3 shares)', '2025-07-02 05:52:08', 'txn_6864c90869628', 0, 0, 'bank', NULL, NULL, NULL, NULL, NULL),
(31, 8, 'investment', 'bank', 150000.00, NULL, NULL, 'bdt', 'Investment received for project ID 16 (3 shares) from user UID_6858f55d3bfe2', '2025-07-02 05:52:08', 'txn_6864c90869628', 0, 0, 'bank', NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `banks`
--

CREATE TABLE `banks` (
  `id` int(11) NOT NULL,
  `bank_name` varchar(100) NOT NULL,
  `branch_name` varchar(100) DEFAULT NULL,
  `branch_code` varchar(20) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `banks`
--

INSERT INTO `banks` (`id`, `bank_name`, `branch_name`, `branch_code`, `city`, `is_active`) VALUES
(1, 'Sonali Bank Ltd.', 'Motjheel', '1', 'Dhaka', 1),
(2, 'Pubali Bank Ltd.', 'Paltan', '2', 'Dhaka', 1);

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `id` int(10) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `category_name`, `created_at`) VALUES
(1, 'Agriculture & Farming', '2025-05-31 02:01:13'),
(2, 'Garment & Textile', '2025-05-31 02:01:13'),
(3, 'Food & Beverage', '2025-05-31 02:01:38'),
(4, 'Health & Wellness', '2025-05-31 02:01:38'),
(5, 'Education & Training', '2025-05-31 02:02:06'),
(6, 'Technology & IT', '2025-05-31 02:02:06'),
(7, 'Handicrafts & Small Industries', '2025-05-31 02:02:36'),
(8, 'Transportation & Logistics', '2025-05-31 02:02:36'),
(9, 'Tourism & Hospitality', '2025-05-31 02:02:41');

-- --------------------------------------------------------

--
-- Table structure for table `loans`
--

CREATE TABLE `loans` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `loan_amount` decimal(12,2) NOT NULL,
  `purpose` text DEFAULT NULL,
  `status` enum('pending','approved','rejected','cancelled','paid','due') DEFAULT 'pending',
  `due_amount` decimal(12,2) DEFAULT 0.00,
  `interest_rate` decimal(5,2) DEFAULT 10.00,
  `repayment_period_months` int(11) DEFAULT 12,
  `disbursement_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `processing_fee_percent` decimal(5,2) DEFAULT 1.50,
  `total_amount_due` decimal(12,2) DEFAULT NULL,
  `monthly_payment` decimal(12,2) DEFAULT NULL,
  `late_fee_percent` decimal(5,2) DEFAULT 2.00,
  `approved_by` int(11) DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_seen` tinyint(1) NOT NULL DEFAULT 0,
  `tin_certificate` varchar(255) DEFAULT NULL,
  `business_id_certificate` varchar(255) DEFAULT NULL,
  `national_id` varchar(255) DEFAULT NULL,
  `project_uniqid` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loans`
--

INSERT INTO `loans` (`id`, `user_id`, `loan_amount`, `purpose`, `status`, `due_amount`, `interest_rate`, `repayment_period_months`, `disbursement_date`, `due_date`, `processing_fee_percent`, `total_amount_due`, `monthly_payment`, `late_fee_percent`, `approved_by`, `approved_at`, `created_at`, `updated_at`, `is_seen`, `tin_certificate`, `business_id_certificate`, `national_id`, `project_uniqid`) VALUES
(1, 8, 65000.00, 'Not Enough Shares', 'cancelled', 0.00, 10.00, 12, NULL, NULL, 1.50, NULL, NULL, 2.00, NULL, NULL, '2025-07-02 00:22:34', '2025-07-02 02:06:28', 0, 'tin_uid8_1751394154.pdf', 'business_uid8_1751394154.pdf', 'nid_uid8_1751394154.pdf', 'prj_68588a0dc4134'),
(2, 8, 65000.00, 'Not enough shares', 'rejected', 0.00, 10.00, 12, NULL, NULL, 1.50, NULL, NULL, 2.00, 12, NULL, '2025-07-02 02:07:08', '2025-07-02 02:11:27', 0, 'tin_uid8_1751400428.pdf', 'business_uid8_1751400428.pdf', 'nid_uid8_1751400428.pdf', 'prj_68588a0dc4134'),
(4, 8, 120000.00, 'Not Enough Investment', 'approved', 133800.00, 10.00, 12, '2025-07-05', '2026-07-02', 1.50, NULL, 11150.00, 2.00, 12, '2025-07-02 06:10:02', '2025-07-02 09:15:45', '2025-07-02 10:10:02', 0, 'tin_uid8_1751426145.pdf', 'business_uid8_1751426145.pdf', 'nid_uid8_1751426145.pdf', 'prj_685a3dd24bbdc'),
(5, 8, 150000.00, 'Not Enough Investment For The Project', 'pending', 0.00, 10.00, 12, NULL, NULL, 1.50, NULL, NULL, 2.00, NULL, NULL, '2025-07-02 11:03:02', '2025-07-02 11:03:02', 0, 'tin_uid8_1751432582.pdf', 'business_uid8_1751432582.pdf', 'nid_uid8_1751432582.pdf', 'prj_68588a0dc4134');

-- --------------------------------------------------------

--
-- Table structure for table `loan_repayments`
--

CREATE TABLE `loan_repayments` (
  `id` int(11) NOT NULL,
  `loan_id` int(11) NOT NULL,
  `installment_number` int(11) NOT NULL COMMENT 'Which installment this is (1, 2, 3...)',
  `due_date` date NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `paid_amount` decimal(12,2) DEFAULT 0.00,
  `payment_date` datetime DEFAULT NULL,
  `payment_method` enum('bank','bkash','nagad','card','wallet') DEFAULT NULL,
  `transaction_reference` varchar(100) DEFAULT NULL COMMENT 'Payment gateway reference or bank transaction ID',
  `late_fee` decimal(12,2) DEFAULT 0.00 COMMENT 'Calculated late fee if payment is late',
  `status` enum('pending','partial','paid','late') DEFAULT 'pending',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `location`
--

CREATE TABLE `location` (
  `id` int(11) NOT NULL,
  `city` varchar(100) NOT NULL,
  `country` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `location`
--

INSERT INTO `location` (`id`, `city`, `country`) VALUES
(1, 'Dhaka', 'Bangladesh'),
(2, 'Chittagong', 'Bangladesh'),
(3, 'New York', 'USA'),
(4, 'London', 'UK');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `type` enum('project','loan','message','system') DEFAULT 'system',
  `title` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int(10) NOT NULL,
  `uniqid` varchar(255) DEFAULT NULL,
  `project_name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `project_img` varchar(255) NOT NULL,
  `categoryId` int(10) NOT NULL,
  `project_type` enum('long_term','short_term') NOT NULL DEFAULT 'long_term',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `project_status` enum('pending','active','closed','approved','declined') NOT NULL,
  `owner_id` int(10) NOT NULL,
  `shares` int(10) UNSIGNED NOT NULL,
  `price_per_share` decimal(12,2) NOT NULL,
  `total_amount` decimal(12,2) DEFAULT NULL,
  `campaign_start` date DEFAULT NULL,
  `campaign_end` date DEFAULT NULL,
  `project_start` date DEFAULT NULL,
  `project_end` date DEFAULT NULL,
  `is_flexible_duration` tinyint(1) DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_seen` tinyint(1) NOT NULL DEFAULT 0,
  `rejection_reason` text DEFAULT NULL,
  `avg_rating` decimal(3,2) DEFAULT 0.00,
  `review_count` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `uniqid`, `project_name`, `description`, `project_img`, `categoryId`, `project_type`, `created_at`, `project_status`, `owner_id`, `shares`, `price_per_share`, `total_amount`, `campaign_start`, `campaign_end`, `project_start`, `project_end`, `is_flexible_duration`, `updated_at`, `is_seen`, `rejection_reason`, `avg_rating`, `review_count`) VALUES
(12, 'prj_6858662be4f9f', 'Dairy Products Manufacturing Project', 'Lorem Ipsum', 'project_1750623787.jpg', 3, 'short_term', '2025-06-22 20:23:07', 'active', 8, 5, 35000.00, 175000.00, '2025-06-24', '2025-07-24', NULL, NULL, 0, '2025-07-07 17:51:01', 0, NULL, 0.00, 0),
(13, 'prj_68588a0dc4134', 'Shrimp Farming', 'Lorem Ipsum', 'project_1750632973.png', 1, 'short_term', '2025-06-22 22:56:13', 'active', 8, 12, 13500.00, 162000.00, '2025-06-24', '2025-07-24', NULL, NULL, 0, '2025-07-07 17:51:34', 0, NULL, 0.00, 0),
(15, 'prj_6859b41736f79', 'Shrimp Farming', 'sdgfsg', 'project_1750709271.png', 1, 'long_term', '2025-06-23 20:07:51', 'active', 8, 10, 20000.00, 200000.00, '2025-06-30', '2025-12-30', NULL, NULL, 0, '2025-07-07 17:51:14', 0, NULL, 0.00, 0),
(16, 'prj_685a3dd24bbdc', 'Garment Factory', 'Lorem Ipsum', 'project_1750744530.jpg', 2, 'long_term', '2025-06-24 05:55:30', 'active', 8, 15, 50000.00, 750000.00, '2025-06-30', '2026-06-30', NULL, NULL, 0, '2025-07-07 17:51:19', 0, NULL, 0.00, 0),
(17, 'prj_685b72f9405c6', 'Education Project for Orphans', 'Lorem Ipsum Solor Domit', 'project_1750823673.jpg', 5, 'long_term', '2025-06-25 03:54:33', 'pending', 8, 20, 50000.00, 1000000.00, '2025-06-26', '2025-07-25', NULL, NULL, 0, '2025-07-07 17:51:27', 0, NULL, 0.00, 0),
(18, 'prj_6864be7c6be4f', 'Car Mechanic Shop', 'This project is for opening a car mechanical shop which will train and empower human resources.', 'project_1751432828.jpg', 5, 'long_term', '2025-07-02 05:07:08', 'pending', 21, 25, 35000.00, 875000.00, '2025-07-31', '2026-07-31', NULL, NULL, 0, '2025-07-02 05:07:08', 0, NULL, 0.00, 0);

-- --------------------------------------------------------

--
-- Table structure for table `project_reviews`
--

CREATE TABLE `project_reviews` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `reviewer_id` int(11) NOT NULL,
  `rating` tinyint(4) NOT NULL CHECK (`rating` between 1 and 5),
  `review_text` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_approved` tinyint(1) DEFAULT 0 COMMENT 'For admin moderation'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `share_orders`
--

CREATE TABLE `share_orders` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `shares_bought` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `share_orders`
--

INSERT INTO `share_orders` (`id`, `project_id`, `user_id`, `shares_bought`, `created_at`) VALUES
(4, 13, 15, 12, '2025-06-23 20:14:45'),
(5, 12, 15, 1, '2025-06-23 20:34:08'),
(6, 12, 15, 2, '2025-06-23 20:51:52'),
(7, 12, 15, 1, '2025-06-23 20:57:15'),
(8, 12, 15, 1, '2025-06-24 03:21:53'),
(9, 16, 15, 3, '2025-06-27 15:30:47'),
(10, 16, 15, 3, '2025-06-28 00:59:23'),
(11, 16, 15, 1, '2025-06-28 01:10:49'),
(12, 15, 8, 4, '2025-06-28 01:28:18'),
(13, 15, 15, 2, '2025-06-28 01:28:50'),
(14, 16, 15, 3, '2025-07-02 05:52:08');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) NOT NULL,
  `uniqid` varchar(50) DEFAULT NULL,
  `user_name` varchar(100) NOT NULL,
  `f_name` varchar(100) NOT NULL,
  `dob` date DEFAULT NULL,
  `contact` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_email` varchar(100) NOT NULL,
  `user_pass` varchar(255) NOT NULL,
  `account_status` enum('active','inactive','','') NOT NULL DEFAULT 'inactive',
  `is_online` tinyint(1) NOT NULL DEFAULT 0,
  `gender` enum('male','female','others') DEFAULT NULL,
  `user_img` varchar(255) DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `uniqid`, `user_name`, `f_name`, `dob`, `contact`, `created_at`, `user_email`, `user_pass`, `account_status`, `is_online`, `gender`, `user_img`, `location_id`, `description`) VALUES
(8, 'UID_6852439a70808', 'sheehan', 'Sheehab Raihan', '2025-10-09', '01560983721', '2025-06-18 04:42:02', 'sheehan9010@gmail.com', '$2y$10$SgMWoKIwV3LrQNyTnwj5kuK0bNdRV4HI.WisCcuOggegPDEJrxgs.', 'active', 0, 'male', 'assets/profile/profile_8_1750744453.png', 1, NULL),
(12, 'adm_6856df63dbe89', 'admin', 'Admin User', NULL, '0000000000', '2025-06-21 16:35:47', 'admin@grownet.com', '$2y$10$XI1KTLsbVHW8pHm3piaA5e8cSpi7GwOb2fddwJEEG4yoxpsCcYlIy', 'active', 1, NULL, NULL, NULL, NULL),
(15, 'UID_6858f55d3bfe2', 'rflbd', 'RFL Company Bangladesh', '0000-00-00', '+8801234589899', '2025-06-23 06:34:05', 'rflbd@gmail.com', '$2y$10$RkudwojYpVWSvYEHafwE7eYXNkw4gjfdWK/oSuqlJPZKk6BzPVRlO', 'active', 1, '', 'assets/profile/profile_15_1750691523.png', 1, NULL),
(16, 'UID_685964c24edc0', 'pranbd', 'Pran Foods', '0000-00-00', '+8809234136773', '2025-06-23 14:29:22', 'pranbd@gmail.com', '$2y$10$zram/iZjYlsWKihDjwVUq.b5S5XcHDCBApZIVcnmroRG2NLlnsPFm', 'active', 1, '', 'assets/profile/profile_16_1750744237.png', 1, NULL),
(17, 'UID_685965388299a', 'beximcobd', 'Beximco', '0000-00-00', '+8809555643217', '2025-06-23 14:31:20', 'beximco@gmail.com', '$2y$10$MSOYvpJEMXIkyGGQYmje3Oc/Zw3PohAoC8R9AGvkZu1kM8GOK/rWm', 'active', 1, '', 'assets/profile/profile_17_1750823389.png', 1, NULL),
(18, 'UID_68596618e7cfc', 'acibd', 'ACI PLC', NULL, '+8809667547376', '2025-06-23 14:35:04', 'acibd@gmail.com', '$2y$10$AWOU/n3tby5H2iNXp7ECMOZJLYP2arLv3qE.t4G2A.g.a5/cZEDkm', 'active', 0, NULL, NULL, NULL, NULL),
(19, 'UID_685967a22fcf4', 'dailystar', 'The Daily Star', NULL, '+8807654334674', '2025-06-23 14:41:38', 'dailystar@gmail.com', '$2y$10$xbVjAxwAyWj0Fe53HrvhD.C3ldaSRIr13PdLYBrluQkOTrdRD41tK', 'active', 0, NULL, NULL, NULL, NULL),
(20, 'UID_685967f2283be', 'pralo', 'Prothom Alo', NULL, '+8809567432590', '2025-06-23 14:42:58', 'pralo@gmail.com', '$2y$10$fPGgZ/qY85Tm2nf3SfqLbumfGWFEhqF9yrXa8X.jr1hNiAv9ZZz4O', 'active', 0, NULL, NULL, NULL, NULL),
(21, 'UID_6864bdae5618e', 'rubaiyat', 'Rubaiyat Afreen', NULL, '01945559018', '2025-07-02 05:03:42', 'sep1999rafreen@gmail.com', '$2y$10$sy0TGV8MAbm3vBtV2t6Vz.cgo9l/t04hONfI6xiTlHHxi9M/w9yB2', 'active', 0, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_balance`
--

CREATE TABLE `user_balance` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `balance` decimal(12,2) NOT NULL DEFAULT 0.00,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_types`
--

CREATE TABLE `user_types` (
  `id` int(11) NOT NULL,
  `type_name` varchar(50) NOT NULL,
  `can_review` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_types`
--

INSERT INTO `user_types` (`id`, `type_name`, `can_review`) VALUES
(1, 'admin', 1),
(2, 'manager', 1),
(3, 'investor', 1),
(4, 'debtor', 1),
(5, 'founder', 1),
(6, 'user', 1),
(7, 'donator', 1),
(8, 'beneficiary', 1),
(9, 'partner', 1),
(10, 'lender', 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_type_map`
--

CREATE TABLE `user_type_map` (
  `user_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_type_map`
--

INSERT INTO `user_type_map` (`user_id`, `type_id`) VALUES
(8, 5),
(8, 6),
(12, 1),
(15, 6),
(15, 9),
(16, 6),
(16, 9),
(17, 6),
(17, 9),
(18, 6),
(18, 9),
(19, 6),
(19, 9),
(20, 6),
(20, 9),
(21, 5),
(21, 6);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `balance_transactions`
--
ALTER TABLE `balance_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `banks`
--
ALTER TABLE `banks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `loans`
--
ALTER TABLE `loans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `approved_by` (`approved_by`);

--
-- Indexes for table `loan_repayments`
--
ALTER TABLE `loan_repayments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `loan_id` (`loan_id`);

--
-- Indexes for table `location`
--
ALTER TABLE `location`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_projects_owner` (`owner_id`),
  ADD KEY `fk_projects_category` (`categoryId`);

--
-- Indexes for table `project_reviews`
--
ALTER TABLE `project_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `reviewer_id` (`reviewer_id`);

--
-- Indexes for table `share_orders`
--
ALTER TABLE `share_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniqid` (`uniqid`),
  ADD KEY `fk_location` (`location_id`);

--
-- Indexes for table `user_balance`
--
ALTER TABLE `user_balance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `user_types`
--
ALTER TABLE `user_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `type_name` (`type_name`);

--
-- Indexes for table `user_type_map`
--
ALTER TABLE `user_type_map`
  ADD PRIMARY KEY (`user_id`,`type_id`),
  ADD KEY `type_id` (`type_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `balance_transactions`
--
ALTER TABLE `balance_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `banks`
--
ALTER TABLE `banks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `loans`
--
ALTER TABLE `loans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `loan_repayments`
--
ALTER TABLE `loan_repayments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `location`
--
ALTER TABLE `location`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `project_reviews`
--
ALTER TABLE `project_reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `share_orders`
--
ALTER TABLE `share_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `user_balance`
--
ALTER TABLE `user_balance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_types`
--
ALTER TABLE `user_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `balance_transactions`
--
ALTER TABLE `balance_transactions`
  ADD CONSTRAINT `balance_transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `loans`
--
ALTER TABLE `loans`
  ADD CONSTRAINT `loans_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `loans_ibfk_2` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `loan_repayments`
--
ALTER TABLE `loan_repayments`
  ADD CONSTRAINT `loan_repayments_ibfk_1` FOREIGN KEY (`loan_id`) REFERENCES `loans` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `fk_projects_category` FOREIGN KEY (`categoryId`) REFERENCES `category` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_projects_owner` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `project_reviews`
--
ALTER TABLE `project_reviews`
  ADD CONSTRAINT `project_reviews_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_reviews_ibfk_2` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `share_orders`
--
ALTER TABLE `share_orders`
  ADD CONSTRAINT `share_orders_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `share_orders_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_location` FOREIGN KEY (`location_id`) REFERENCES `location` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `user_balance`
--
ALTER TABLE `user_balance`
  ADD CONSTRAINT `user_balance_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_type_map`
--
ALTER TABLE `user_type_map`
  ADD CONSTRAINT `user_type_map_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_type_map_ibfk_2` FOREIGN KEY (`type_id`) REFERENCES `user_types` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
