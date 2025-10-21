-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Jul 01, 2025 at 12:32 PM
-- Server version: 11.5.2-MariaDB
-- PHP Version: 7.3.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `apotek_systems_dbms`
--

-- --------------------------------------------------------

--
-- Table structure for table `acc_annual_expenses`
--

DROP TABLE IF EXISTS `acc_annual_expenses`;
CREATE TABLE IF NOT EXISTS `acc_annual_expenses` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Description` varchar(45) DEFAULT NULL,
  `Amount` decimal(20,2) DEFAULT NULL,
  `StartingDate` date DEFAULT NULL,
  `EndingDate` date DEFAULT NULL,
  `ExpenseCatID` int(11) DEFAULT NULL,
  `PaymentMethodID` int(11) DEFAULT NULL,
  `PaymentDate` date DEFAULT NULL,
  `Duration` int(11) DEFAULT NULL,
  `Updated` datetime DEFAULT NULL,
  `UpdatedBy` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `acc_cashflow`
--

DROP TABLE IF EXISTS `acc_cashflow`;
CREATE TABLE IF NOT EXISTS `acc_cashflow` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `TransactionDate` date NOT NULL,
  `StartCashIn` decimal(20,2) NOT NULL,
  `DailyCashSales` decimal(20,2) NOT NULL,
  `CreditPayments` decimal(20,2) NOT NULL,
  `CreditPayDesc` varchar(1000) DEFAULT NULL,
  `CashInOthers` decimal(20,2) NOT NULL,
  `CashInOthersDesc` varchar(1000) DEFAULT NULL,
  `Purchases` decimal(20,2) NOT NULL,
  `PurchasesDesc` varchar(1000) DEFAULT NULL,
  `Expenses` decimal(20,2) NOT NULL,
  `ExpensesDesc` varchar(1000) DEFAULT NULL,
  `TotalCashIn` decimal(20,2) NOT NULL,
  `TotalCashOut` decimal(20,2) NOT NULL,
  `CashBalance` decimal(20,2) NOT NULL,
  `UpdatedBy` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `acc_expenses`
--

DROP TABLE IF EXISTS `acc_expenses`;
CREATE TABLE IF NOT EXISTS `acc_expenses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `amount` decimal(20,2) NOT NULL,
  `expense_category_id` int(11) NOT NULL,
  `expense_sub_category_id` int(11) DEFAULT NULL,
  `expense_description` varchar(45) DEFAULT NULL,
  `payment_method_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `updated_by` int(11) NOT NULL,
  `store_id` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `acc_expense_categories`
--

DROP TABLE IF EXISTS `acc_expense_categories`;
CREATE TABLE IF NOT EXISTS `acc_expense_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `acc_expense_sub_categories`
--

DROP TABLE IF EXISTS `acc_expense_sub_categories`;
CREATE TABLE IF NOT EXISTS `acc_expense_sub_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `expense_category_id` int(11) DEFAULT NULL,
  `name` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `acc_payment_methods`
--

DROP TABLE IF EXISTS `acc_payment_methods`;
CREATE TABLE IF NOT EXISTS `acc_payment_methods` (
  `id` int(11) NOT NULL,
  `description` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

DROP TABLE IF EXISTS `activity_log`;
CREATE TABLE IF NOT EXISTS `activity_log` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `log_name` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `subject_id` bigint(20) UNSIGNED DEFAULT NULL,
  `subject_type` varchar(255) DEFAULT NULL,
  `causer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `causer_type` varchar(255) DEFAULT NULL,
  `properties` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_log`
--

INSERT INTO `activity_log` (`id`, `log_name`, `description`, `subject_id`, `subject_type`, `causer_id`, `causer_type`, `properties`, `created_at`, `updated_at`) VALUES
(1, 'default', 'created', 1, 'App\\GoodsReceiving', 17, 'App\\User', '{\"attributes\": {\"id\": 1, \"quantity\": \"20.00\", \"unit_cost\": \"0.00\", \"created_at\": \"2025-02-04 00:00:00\", \"created_by\": \"17\", \"product_id\": \"100007\", \"sell_price\": \"0.00\", \"total_cost\": \"0.00\", \"total_sell\": \"0.00\", \"expire_date\": null, \"item_profit\": \"0.00\", \"supplier_id\": \"1\"}}', '2025-02-04 19:52:05', '2025-02-04 19:52:05'),
(2, 'default', 'created', 2, 'App\\GoodsReceiving', 17, 'App\\User', '{\"attributes\": {\"id\": 2, \"quantity\": \"23.00\", \"unit_cost\": \"0.00\", \"created_at\": \"2025-02-04 00:00:00\", \"created_by\": \"17\", \"product_id\": \"100114\", \"sell_price\": \"0.00\", \"total_cost\": \"0.00\", \"total_sell\": \"0.00\", \"expire_date\": null, \"item_profit\": \"0.00\", \"supplier_id\": \"1\"}}', '2025-02-04 19:52:05', '2025-02-04 19:52:05'),
(3, 'default', 'created', 3, 'App\\GoodsReceiving', 17, 'App\\User', '{\"attributes\": {\"id\": 3, \"quantity\": \"3.00\", \"unit_cost\": \"0.00\", \"created_at\": \"2025-02-04 00:00:00\", \"created_by\": \"17\", \"product_id\": \"100002\", \"sell_price\": \"0.00\", \"total_cost\": \"0.00\", \"total_sell\": \"0.00\", \"expire_date\": null, \"item_profit\": \"0.00\", \"supplier_id\": \"1\"}}', '2025-02-04 19:53:22', '2025-02-04 19:53:22'),
(4, 'default', 'created', 4, 'App\\GoodsReceiving', 17, 'App\\User', '{\"attributes\": {\"id\": 4, \"quantity\": \"4.00\", \"unit_cost\": \"0.00\", \"created_at\": \"2025-02-04 00:00:00\", \"created_by\": \"17\", \"product_id\": \"100256\", \"sell_price\": \"0.00\", \"total_cost\": \"0.00\", \"total_sell\": \"0.00\", \"expire_date\": null, \"item_profit\": \"0.00\", \"supplier_id\": \"1\"}}', '2025-02-04 19:53:22', '2025-02-04 19:53:22'),
(5, 'default', 'created', 5, 'App\\GoodsReceiving', 17, 'App\\User', '{\"attributes\": {\"id\": 5, \"quantity\": \"1.00\", \"unit_cost\": \"0.00\", \"created_at\": \"2025-02-04 00:00:00\", \"created_by\": \"17\", \"product_id\": \"100027\", \"sell_price\": \"0.00\", \"total_cost\": \"0.00\", \"total_sell\": \"0.00\", \"expire_date\": null, \"item_profit\": \"0.00\", \"supplier_id\": \"1\"}}', '2025-02-04 19:54:25', '2025-02-04 19:54:25'),
(6, 'default', 'created', 6, 'App\\GoodsReceiving', 17, 'App\\User', '{\"attributes\": {\"id\": 6, \"quantity\": \"10.00\", \"unit_cost\": \"1850.00\", \"created_at\": \"2025-02-09 00:00:00\", \"created_by\": \"17\", \"product_id\": \"100074\", \"sell_price\": \"3500.00\", \"total_cost\": \"18500.00\", \"total_sell\": \"35000.00\", \"expire_date\": null, \"item_profit\": \"16500.00\", \"supplier_id\": \"1\"}}', '2025-02-10 20:51:13', '2025-02-10 20:51:13'),
(7, 'default', 'created', 7, 'App\\GoodsReceiving', 17, 'App\\User', '{\"attributes\": {\"id\": 7, \"quantity\": \"120.00\", \"unit_cost\": \"1200.00\", \"created_at\": \"2025-02-14 00:00:00\", \"created_by\": \"17\", \"product_id\": \"100114\", \"sell_price\": \"2500.00\", \"total_cost\": \"144000.00\", \"total_sell\": \"300000.00\", \"expire_date\": null, \"item_profit\": \"156000.00\", \"supplier_id\": \"1\"}}', '2025-02-14 16:00:14', '2025-02-14 16:00:14'),
(8, 'default', 'created', 8, 'App\\GoodsReceiving', 17, 'App\\User', '{\"attributes\": {\"id\": 8, \"quantity\": \"30.00\", \"unit_cost\": \"0.00\", \"created_at\": \"2025-02-14 00:00:00\", \"created_by\": \"17\", \"product_id\": \"100116\", \"sell_price\": \"0.00\", \"total_cost\": \"0.00\", \"total_sell\": \"0.00\", \"expire_date\": null, \"item_profit\": \"0.00\", \"supplier_id\": \"1\"}}', '2025-02-14 16:04:13', '2025-02-14 16:04:13'),
(9, 'default', 'created', 9, 'App\\GoodsReceiving', 17, 'App\\User', '{\"attributes\": {\"id\": 9, \"quantity\": \"25.00\", \"unit_cost\": \"0.00\", \"created_at\": \"2025-02-14 00:00:00\", \"created_by\": \"17\", \"product_id\": \"100115\", \"sell_price\": \"0.00\", \"total_cost\": \"0.00\", \"total_sell\": \"0.00\", \"expire_date\": null, \"item_profit\": \"0.00\", \"supplier_id\": \"1\"}}', '2025-02-14 16:04:13', '2025-02-14 16:04:13'),
(10, 'default', 'created', 10, 'App\\GoodsReceiving', 17, 'App\\User', '{\"attributes\":{\"id\":10,\"product_id\":100115,\"quantity\":\"100.00\",\"unit_cost\":\"2000.00\",\"total_cost\":\"200000.00\",\"supplier_id\":2,\"expire_date\":null,\"total_sell\":\"400000.00\",\"item_profit\":\"200000.00\",\"sell_price\":\"4000.00\",\"created_by\":17,\"created_at\":\"2025-06-19 00:00:00\"}}', '2025-06-19 13:29:19', '2025-06-19 13:29:19'),
(11, 'default', 'created', 11, 'App\\GoodsReceiving', 17, 'App\\User', '{\"attributes\":{\"id\":11,\"product_id\":100006,\"quantity\":\"18.00\",\"unit_cost\":\"3000.00\",\"total_cost\":\"54000.00\",\"supplier_id\":2,\"expire_date\":null,\"total_sell\":\"90000.00\",\"item_profit\":\"36000.00\",\"sell_price\":\"5000.00\",\"created_by\":17,\"created_at\":\"2025-06-19 00:00:00\"}}', '2025-06-19 13:29:19', '2025-06-19 13:29:19'),
(12, 'default', 'created', 12, 'App\\GoodsReceiving', 17, 'App\\User', '{\"attributes\":{\"id\":12,\"product_id\":102006,\"quantity\":\"10.00\",\"unit_cost\":\"2000.00\",\"total_cost\":\"20000.00\",\"supplier_id\":2,\"expire_date\":null,\"total_sell\":\"30000.00\",\"item_profit\":\"10000.00\",\"sell_price\":\"3000.00\",\"created_by\":17,\"created_at\":\"2025-06-26 00:00:00\"}}', '2025-06-26 15:36:45', '2025-06-26 15:36:45');

-- --------------------------------------------------------

--
-- Table structure for table `adjustment_reasons`
--

DROP TABLE IF EXISTS `adjustment_reasons`;
CREATE TABLE IF NOT EXISTS `adjustment_reasons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reason` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

DROP TABLE IF EXISTS `countries`;
CREATE TABLE IF NOT EXISTS `countries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `short_code` varchar(4) DEFAULT NULL,
  `country_code` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
CREATE TABLE IF NOT EXISTS `customers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `tin` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `address` varchar(200) DEFAULT NULL,
  `credit_limit` decimal(20,2) DEFAULT NULL,
  `total_credit` decimal(20,2) DEFAULT 0.00,
  `payment_term` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `tin`, `phone`, `email`, `address`, `credit_limit`, `total_credit`, `payment_term`) VALUES
(1, 'CASH', NULL, '+255787899888', NULL, NULL, NULL, 0.00, 1),
(2, 'Juma', NULL, '+255753536718', NULL, NULL, 100000.00, 7000.00, 2);

-- --------------------------------------------------------

--
-- Table structure for table `inv_categories`
--

DROP TABLE IF EXISTS `inv_categories`;
CREATE TABLE IF NOT EXISTS `inv_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `inv_categories`
--

INSERT INTO `inv_categories` (`id`, `name`) VALUES
(1, 'MEDICINES'),
(2, 'NON PHARMACEUTICALS'),
(3, 'COSMETICS'),
(4, 'SURGICAL');

-- --------------------------------------------------------

--
-- Table structure for table `inv_current_stock`
--

DROP TABLE IF EXISTS `inv_current_stock`;
CREATE TABLE IF NOT EXISTS `inv_current_stock` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `quantity` decimal(11,2) DEFAULT NULL,
  `unit_cost` double DEFAULT NULL,
  `batch_number` varchar(45) DEFAULT NULL,
  `shelf_number` varchar(45) DEFAULT NULL,
  `store_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `mode` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `inv_current_stock`
--

INSERT INTO `inv_current_stock` (`id`, `product_id`, `expiry_date`, `quantity`, `unit_cost`, `batch_number`, `shelf_number`, `store_id`, `created_at`, `created_by`, `updated_at`, `mode`) VALUES
(1, 100007, NULL, 20.00, 0, NULL, NULL, 3, '2025-02-04 14:52:05', NULL, '2025-02-04 14:52:05', NULL),
(2, 100114, NULL, 23.00, 0, NULL, NULL, 3, '2025-02-04 14:52:05', NULL, '2025-02-04 14:52:05', NULL),
(3, 100002, NULL, 0.00, 0, NULL, NULL, 1, '2025-02-04 14:53:22', 17, '2025-02-14 10:52:46', NULL),
(4, 100256, NULL, 2.00, 0, NULL, NULL, 1, '2025-02-04 14:53:22', 17, '2025-06-19 16:41:50', NULL),
(5, 100027, NULL, 1.00, 0, NULL, NULL, 2, '2025-02-04 14:54:25', NULL, '2025-02-04 14:54:25', NULL),
(6, 100074, NULL, 0.00, 1850, NULL, NULL, 1, '2025-02-10 15:51:13', 17, '2025-02-10 16:11:53', NULL),
(7, 100114, NULL, 73.00, 1200, NULL, NULL, 1, '2025-02-14 11:00:14', 17, '2025-02-14 15:20:50', NULL),
(8, 100116, NULL, 18.00, 0, NULL, NULL, 1, '2025-02-14 11:04:13', 17, '2025-06-23 14:34:16', NULL),
(9, 100115, NULL, 0.00, 0, NULL, NULL, 1, '2025-02-14 11:04:13', 17, '2025-06-23 14:34:16', NULL),
(10, 100115, NULL, 17.00, 2000, NULL, NULL, 1, '2025-06-19 16:29:18', 17, '2025-06-23 14:34:16', NULL),
(11, 100006, NULL, 0.00, 3000, NULL, NULL, 1, '2025-06-19 16:29:19', 17, '2025-06-19 16:37:18', NULL),
(12, 102006, NULL, 10.00, 2000, NULL, NULL, 1, '2025-06-26 18:36:45', NULL, '2025-06-26 18:36:45', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `inv_incoming_stock`
--

DROP TABLE IF EXISTS `inv_incoming_stock`;
CREATE TABLE IF NOT EXISTS `inv_incoming_stock` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) DEFAULT NULL,
  `quantity` decimal(10,2) DEFAULT NULL,
  `unit_cost` decimal(20,2) DEFAULT NULL,
  `sell_price` decimal(12,2) DEFAULT NULL,
  `total_cost` decimal(20,2) DEFAULT NULL,
  `total_sell` decimal(12,2) DEFAULT NULL,
  `item_profit` decimal(12,2) DEFAULT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `order_details_id` int(11) DEFAULT NULL,
  `batch_number` varchar(45) DEFAULT NULL,
  `expire_date` date DEFAULT NULL,
  `grn` varchar(45) DEFAULT NULL,
  `invoice_no` varchar(45) DEFAULT NULL,
  `status` varchar(45) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `store_id` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `inv_incoming_stock`
--

INSERT INTO `inv_incoming_stock` (`id`, `product_id`, `quantity`, `unit_cost`, `sell_price`, `total_cost`, `total_sell`, `item_profit`, `supplier_id`, `order_details_id`, `batch_number`, `expire_date`, `grn`, `invoice_no`, `status`, `created_by`, `created_at`, `updated_at`, `store_id`) VALUES
(1, 100007, 20.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1, NULL, NULL, NULL, NULL, NULL, NULL, 17, '2025-02-04 00:00:00', '2025-02-04 14:52:05', 1),
(2, 100114, 23.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1, NULL, NULL, NULL, NULL, NULL, NULL, 17, '2025-02-04 00:00:00', '2025-02-04 14:52:05', 1),
(3, 100002, 3.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1, NULL, NULL, NULL, NULL, NULL, NULL, 17, '2025-02-04 00:00:00', '2025-02-04 14:53:22', 1),
(4, 100256, 4.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1, NULL, NULL, NULL, NULL, NULL, NULL, 17, '2025-02-04 00:00:00', '2025-02-04 14:53:22', 1),
(5, 100027, 1.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1, NULL, NULL, NULL, NULL, NULL, NULL, 17, '2025-02-04 00:00:00', '2025-02-04 14:54:25', 1),
(6, 100074, 10.00, 1850.00, 3500.00, 18500.00, 35000.00, 16500.00, 1, NULL, NULL, NULL, NULL, NULL, NULL, 17, '2025-02-09 00:00:00', '2025-02-10 15:51:13', 1),
(7, 100114, 120.00, 1200.00, 2500.00, 144000.00, 300000.00, 156000.00, 1, NULL, NULL, NULL, NULL, NULL, NULL, 17, '2025-02-14 00:00:00', '2025-02-14 11:00:14', 1),
(8, 100116, 30.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1, NULL, NULL, NULL, NULL, NULL, NULL, 17, '2025-02-14 00:00:00', '2025-02-14 11:04:13', 1),
(9, 100115, 25.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1, NULL, NULL, NULL, NULL, NULL, NULL, 17, '2025-02-14 00:00:00', '2025-02-14 11:04:13', 1),
(10, 100115, 100.00, 2000.00, 4000.00, 200000.00, 400000.00, 200000.00, 2, NULL, NULL, NULL, NULL, NULL, NULL, 17, '2025-06-19 00:00:00', '2025-06-19 16:29:19', 1),
(11, 100006, 18.00, 3000.00, 5000.00, 54000.00, 90000.00, 36000.00, 2, NULL, NULL, NULL, NULL, NULL, NULL, 17, '2025-06-19 00:00:00', '2025-06-19 16:29:19', 1),
(12, 102006, 10.00, 2000.00, 3000.00, 20000.00, 30000.00, 10000.00, 2, NULL, NULL, NULL, NULL, NULL, NULL, 17, '2025-06-26 00:00:00', '2025-06-26 18:36:45', 1);

-- --------------------------------------------------------

--
-- Table structure for table `inv_invoices`
--

DROP TABLE IF EXISTS `inv_invoices`;
CREATE TABLE IF NOT EXISTS `inv_invoices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_no` varchar(45) NOT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `invoice_date` date DEFAULT NULL,
  `invoice_amount` decimal(20,2) DEFAULT NULL,
  `status` varchar(45) DEFAULT NULL,
  `received_status` varchar(45) DEFAULT NULL,
  `payment_due_date` datetime DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `paid_amount` decimal(20,2) DEFAULT NULL,
  `remain_balance` decimal(20,2) DEFAULT NULL,
  `grace_period` int(11) DEFAULT NULL,
  `remarks` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inv_issue_locations`
--

DROP TABLE IF EXISTS `inv_issue_locations`;
CREATE TABLE IF NOT EXISTS `inv_issue_locations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inv_issue_returns`
--

DROP TABLE IF EXISTS `inv_issue_returns`;
CREATE TABLE IF NOT EXISTS `inv_issue_returns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `issue_id` int(11) NOT NULL,
  `issue_qty` decimal(10,2) NOT NULL,
  `return_qty` decimal(10,2) NOT NULL,
  `return_value` decimal(20,2) NOT NULL,
  `issed_at` datetime NOT NULL,
  `returned_by` int(11) NOT NULL,
  `returned_at` datetime NOT NULL,
  `Reason` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inv_products`
--

DROP TABLE IF EXISTS `inv_products`;
CREATE TABLE IF NOT EXISTS `inv_products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `barcode` varchar(50) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `generic_name` varchar(100) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `sub_category_id` int(11) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL COMMENT 'Stockable and consumable',
  `standard_uom` varchar(45) DEFAULT NULL,
  `sales_uom` varchar(45) DEFAULT NULL,
  `purchase_uom` varchar(45) DEFAULT NULL,
  `indication` varchar(100) DEFAULT NULL,
  `dosage` varchar(45) DEFAULT NULL,
  `min_quantinty` int(11) DEFAULT NULL,
  `max_quantinty` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `npk_ratio` varchar(15) DEFAULT NULL,
  `brand` varchar(25) DEFAULT NULL,
  `pack_size` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=102008 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `inv_products`
--

INSERT INTO `inv_products` (`id`, `barcode`, `name`, `generic_name`, `category_id`, `sub_category_id`, `type`, `standard_uom`, `sales_uom`, `purchase_uom`, `indication`, `dosage`, `min_quantinty`, `max_quantinty`, `status`, `created_at`, `updated_at`, `npk_ratio`, `brand`, `pack_size`) VALUES
(100000, NULL, 'Abidec Drops 10mls', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, '2025-01-21 16:48:19', '2025-06-19 13:19:32', NULL, NULL, NULL),
(100001, NULL, 'Abidec Syrup', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:19', '2025-01-21 16:48:19', NULL, NULL, NULL),
(100002, NULL, 'Abitol Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:19', '2025-01-21 16:48:19', NULL, NULL, NULL),
(100003, NULL, 'AbNal Nasal Drop (Normal Saline)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:19', '2025-01-21 16:48:19', NULL, NULL, NULL),
(100004, NULL, 'AccuCheck Machine', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:19', '2025-01-21 16:48:19', NULL, NULL, NULL),
(100005, NULL, 'AccuCheck Strips', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:19', '2025-01-21 16:48:19', NULL, NULL, NULL),
(100006, NULL, 'ACE Drops 30ml (Pediatric)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:19', '2025-01-21 16:48:19', NULL, NULL, NULL),
(100007, NULL, 'ACE Drops 60ml (Children)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:19', '2025-01-21 16:48:19', NULL, NULL, NULL),
(100008, NULL, 'Aceclofenac Tabs (Acefen)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:19', '2025-01-21 16:48:19', NULL, NULL, NULL),
(100009, NULL, 'Aceclofenac Tabs (Zerodol)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:19', '2025-01-21 16:48:19', NULL, NULL, NULL),
(100010, NULL, 'Acetazolamide Tabs 250mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:19', '2025-01-21 16:48:19', NULL, NULL, NULL),
(100011, NULL, 'Acetylsacylic Acid Tabs 75mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:19', '2025-01-21 16:48:19', NULL, NULL, NULL),
(100012, NULL, 'Aciclovir Cream 10g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:19', '2025-01-21 16:48:19', NULL, NULL, NULL),
(100013, NULL, 'Aciclovir Cream 5g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:19', '2025-01-21 16:48:19', NULL, NULL, NULL),
(100014, NULL, 'Aciclovir Eye Oint 4.5g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:19', '2025-01-21 16:48:19', NULL, NULL, NULL),
(100015, NULL, 'Aciclovir Tabs 200mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:19', '2025-01-21 16:48:19', NULL, NULL, NULL),
(100016, NULL, 'Aciclovir Tabs 400mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:19', '2025-01-21 16:48:19', NULL, NULL, NULL),
(100017, NULL, 'Acne Free Cream 30g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:19', '2025-01-21 16:48:19', NULL, NULL, NULL),
(100018, NULL, 'Acne Scar Care Gel 12g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:19', '2025-01-21 16:48:19', NULL, NULL, NULL),
(100019, NULL, 'Acne Soap 90g', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:19', '2025-01-21 16:48:19', NULL, NULL, NULL),
(100020, NULL, 'Acnes Cream 12g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:19', '2025-01-21 16:48:19', NULL, NULL, NULL),
(100021, NULL, 'Acnes Cream Wash 100g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:19', '2025-01-21 16:48:19', NULL, NULL, NULL),
(100022, NULL, 'Acnes Cream Wash 50g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:19', '2025-01-21 16:48:19', NULL, NULL, NULL),
(100023, NULL, 'Acnes Sealing Gel 9g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:19', '2025-01-21 16:48:19', NULL, NULL, NULL),
(100024, NULL, 'Acnotin Tabs 10mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:19', '2025-01-21 16:48:19', NULL, NULL, NULL),
(100025, NULL, 'Acnotin Tabs 20mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:19', '2025-01-21 16:48:19', NULL, NULL, NULL),
(100026, NULL, 'Acrasone Cream 10g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:19', '2025-01-21 16:48:19', NULL, NULL, NULL),
(100027, NULL, 'Actal Tums Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:19', '2025-01-21 16:48:19', NULL, NULL, NULL),
(100028, NULL, 'Actified Cold Syrup 100ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:19', '2025-01-21 16:48:19', NULL, NULL, NULL),
(100029, NULL, 'Actified Dry Cough & Cold Syrup 100ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:19', '2025-01-21 16:48:19', NULL, NULL, NULL),
(100030, NULL, 'Actified Wet Cough & Cold Syrup 100ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:19', '2025-01-21 16:48:19', NULL, NULL, NULL),
(100031, NULL, 'Actinac Plus Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:19', '2025-01-21 16:48:19', NULL, NULL, NULL),
(100032, NULL, 'Actinac Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:19', '2025-01-21 16:48:19', NULL, NULL, NULL),
(100033, NULL, 'Action Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:19', '2025-01-21 16:48:19', NULL, NULL, NULL),
(100034, NULL, 'Active Mama Baby Coconut Butter 240g', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:19', '2025-01-21 16:48:19', NULL, NULL, NULL),
(100035, NULL, 'Active Mama Baby Fabric Softner', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:19', '2025-01-21 16:48:19', NULL, NULL, NULL),
(100036, NULL, 'Active Mama Baby Oil Advance 250ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:19', '2025-01-21 16:48:19', NULL, NULL, NULL),
(100037, NULL, 'Active Mama Baby Shower Gel 500ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:19', '2025-01-21 16:48:19', NULL, NULL, NULL),
(100038, NULL, 'Active Mama Baby Soap', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:19', '2025-01-21 16:48:19', NULL, NULL, NULL),
(100039, NULL, 'Active Mama Coconut Oil 125ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:19', '2025-01-21 16:48:19', NULL, NULL, NULL),
(100040, NULL, 'Active Mama Coconut Oil 250ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:19', '2025-01-21 16:48:19', NULL, NULL, NULL),
(100041, NULL, 'Active Mama Face Cream 50ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:19', '2025-01-21 16:48:19', NULL, NULL, NULL),
(100042, NULL, 'Active Mama Glowing Face Serum 300ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:19', '2025-01-21 16:48:19', NULL, NULL, NULL),
(100043, NULL, 'Active Mama Hand & Nail Cream 100ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:19', '2025-01-21 16:48:19', NULL, NULL, NULL),
(100044, NULL, 'Active Mama Hydrating Face Moisturizer 50ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:19', '2025-01-21 16:48:19', NULL, NULL, NULL),
(100045, NULL, 'Active Mama Oil Cotrol Face & Body Scrub', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:19', '2025-01-21 16:48:19', NULL, NULL, NULL),
(100046, NULL, 'Active Mama Organic Belly & Body Oil 250ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:19', '2025-01-21 16:48:19', NULL, NULL, NULL),
(100047, NULL, 'Active Mama Pumpkin Seeds', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:19', '2025-01-21 16:48:19', NULL, NULL, NULL),
(100048, NULL, 'Active Mama Shea Butter Baby Soap', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:19', '2025-01-21 16:48:19', NULL, NULL, NULL),
(100049, NULL, 'Active Mama Super Uji Flour 1Kg', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:19', '2025-01-21 16:48:19', NULL, NULL, NULL),
(100050, NULL, 'Adacin Gel 15g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:19', '2025-01-21 16:48:19', NULL, NULL, NULL),
(100051, NULL, 'Adalat LA Tabs 30mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:19', '2025-01-21 16:48:19', NULL, NULL, NULL),
(100052, NULL, 'Adapalene Gel 15g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:19', '2025-01-21 16:48:19', NULL, NULL, NULL),
(100053, NULL, 'Adenafil Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:19', '2025-01-21 16:48:19', NULL, NULL, NULL),
(100054, NULL, 'Adidas Deodorant', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:19', '2025-01-21 16:48:19', NULL, NULL, NULL),
(100055, NULL, 'Adidas Shower Gel', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:19', '2025-01-21 16:48:19', NULL, NULL, NULL),
(100056, NULL, 'Adidas Spray', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:19', '2025-01-21 16:48:19', NULL, NULL, NULL),
(100057, NULL, 'Adrenaline Inj', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:19', '2025-01-21 16:48:19', NULL, NULL, NULL),
(100058, NULL, 'Adult Diapers (Cuidado) L', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100059, NULL, 'Adult Diapers (Cuidado) M', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100060, NULL, 'Adult Diapers (Cuidado) XL', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100061, NULL, 'Adult Diapers (Pinotex) L', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100062, NULL, 'Adult Diapers (Pinotex) M', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100063, NULL, 'Adult Diapers (Pinotex) XL', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100064, NULL, 'Aerius Syrup 150ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100065, NULL, 'Aerius Syrup 60ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100066, NULL, 'Aerius Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100067, NULL, 'Aerocort Inhaler', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100068, NULL, 'Africana Coconut Oil 120ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100069, NULL, 'Africana Coconut Oil 1Ltr', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100070, NULL, 'Africana Coconut Oil 250ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100071, NULL, 'Africana Coconut Soap', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100072, NULL, 'After Shave Lotion 100ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100073, NULL, 'Air Freshner', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100074, NULL, 'Albendazole Syrup (ABZee)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100075, NULL, 'Albendazole Syrup (Albasol)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100076, NULL, 'Albendazole Syrup (Alom)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100077, NULL, 'Albendazole Syrup (Anthel)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100078, NULL, 'Albendazole Syrup (Azentel)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100079, NULL, 'Albendazole Syrup (Benpham)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100080, NULL, 'Albendazole Syrup (Elyzole)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100081, NULL, 'Albendazole Syrup (Filazole)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100082, NULL, 'Albendazole Syrup (Finazole)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100083, NULL, 'Albendazole Syrup (Zentel)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100084, NULL, 'Albendazole Tabs (ABZee)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100085, NULL, 'Albendazole Tabs (Albasol)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100086, NULL, 'Albendazole Tabs (Alben)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100087, NULL, 'Albendazole Tabs (Alzental)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100088, NULL, 'Albendazole Tabs (Anthel)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100089, NULL, 'Albendazole Tabs (Azentel)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100090, NULL, 'Albendazole Tabs (Elyzole)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100091, NULL, 'Albendazole Tabs (Filazole)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100092, NULL, 'Albendazole Tabs (Finazole)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100093, NULL, 'Albendazole Tabs (Womiban)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100094, NULL, 'Albendazole Tabs (Zentel)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100095, NULL, 'Alcohol Swabs', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100096, NULL, 'Alerid D Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100097, NULL, 'Algic P Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100098, NULL, 'Alinda Virgin Coconut Oil 125ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100099, NULL, 'Alinda Virgin Coconut Oil 250ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100100, NULL, 'Alkacore Soln100ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100101, NULL, 'Allercom Eye Drops', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100102, NULL, 'Allergotin Eye Drops 4% 10ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100103, NULL, 'Allopurinol Tabs 100mg (Alopron)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100104, NULL, 'Allopurinol Tabs 300mg (Alopron)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100105, NULL, 'Aloe Vera Juice', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100106, NULL, 'Alpha Milk No.1', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100107, NULL, 'Alpha Milk No.2', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100108, NULL, 'Alphaclav Inj 1.2g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100109, NULL, 'Alphaclav Syrup 228mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100110, NULL, 'Alphaclav Tabs 375mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100111, NULL, 'Alphaclav Tabs 625mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100112, NULL, 'Altapham Susp 100ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100113, NULL, 'Altoprine Drops', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100115, NULL, 'ALU Pack 18', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100116, NULL, 'ALU Pack 24', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100117, NULL, 'ALU Pack 6', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100118, NULL, 'ALU Syrup 60ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100119, NULL, 'Alugel Syrup 100ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100120, NULL, 'Always Pads', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100121, NULL, 'Amalif Shower Gel', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100122, NULL, 'Ambrodil Drops', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100123, NULL, 'Ambrodil Syrup', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100124, NULL, 'Ambrosan Syrup 100ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100125, NULL, 'Ambrox Drops 15ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100126, NULL, 'Ambrox Syrup (Adult)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100127, NULL, 'Ambrox Syrup (Pediatric)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100128, NULL, 'Amikacin Inj (Lanomycin)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100129, NULL, 'Aminophylline Inj', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100130, NULL, 'Aminophylline Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100131, NULL, 'Amiodarone Tabs 200mg (Cardilor)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100132, NULL, 'Amitriptyline Tabs (Amirol)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100133, NULL, 'Amlodipine Tabs 10mg  (Amloberg)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100134, NULL, 'Amlodipine Tabs 10mg  (Amlodac)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100135, NULL, 'Amlodipine Tabs 10mg  (Amlosun)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100136, NULL, 'Amlodipine Tabs 10mg  (Calchek)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100137, NULL, 'Amlodipine Tabs 10mg  (Coram)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100138, NULL, 'Amlodipine Tabs 10mg (Amtas)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100139, NULL, 'Amlodipine Tabs 10mg (Primodil)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100140, NULL, 'Amlodipine Tabs 2.5mg (Asomex)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100141, NULL, 'Amlodipine Tabs 5mg  (Amloberg)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:20', '2025-01-21 16:48:20', NULL, NULL, NULL),
(100142, NULL, 'Amlodipine Tabs 5mg  (Amlodac)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100143, NULL, 'Amlodipine Tabs 5mg  (Amlosun)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100144, NULL, 'Amlodipine Tabs 5mg  (Calchek)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100145, NULL, 'Amlodipine Tabs 5mg  (Coram)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100146, NULL, 'Amlodipine Tabs 5mg (Amtas)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100147, NULL, 'Amlodipine Tabs 5mg (Asomex)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100148, NULL, 'Amlodipine Tabs 5mg (Besylate)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100149, NULL, 'Amlodipine Tabs 5mg (Denk)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100150, NULL, 'Amol 6 Plus Syrup 100ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100151, NULL, 'Amol G Cream', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100152, NULL, 'Amol Syrup 100ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100153, NULL, 'Amorrete Pads', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100154, NULL, 'Amoxicillin Caps 250mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100155, NULL, 'Amoxicillin Caps 500mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100156, NULL, 'Amoxicillin DT 125mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100157, NULL, 'Amoxicillin DT 250mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100158, NULL, 'Amoxicillin Syrup', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100159, NULL, 'Amoxicillin Syrup (GSK)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100160, NULL, 'Ampicillin Caps', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100161, NULL, 'Ampicillin Inj', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100162, NULL, 'Ampicillin Syrup', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100163, NULL, 'Ampiclox Caps', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100164, NULL, 'Ampiclox Caps (GSK)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100165, NULL, 'Ampiclox Inj', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100166, NULL, 'Ampiclox Neonatal Drops 8ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100167, NULL, 'Ampiclox Neonatal Drops 8ml (GSK)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100168, NULL, 'Ampiclox Powder', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100169, NULL, 'Ampiclox Syrup', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100170, NULL, 'Ampiclox Syrup (GSK)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100171, NULL, 'Anafranil Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100172, NULL, 'Ananda Adult Diapers (L)', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100173, NULL, 'Ananda Adult Diapers (M)', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100174, NULL, 'Ananda Adult Diapers (XL)', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100175, NULL, 'Ananda Adult Diapers (XXL)', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100176, NULL, 'Angels Pads', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100177, NULL, 'Ankle Binder Comfort (L)', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100178, NULL, 'Ankle Binder Comfort (M)', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100179, NULL, 'Ankle Binder Comfort (XL)', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100180, NULL, 'Ankle Comfort (L)', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100181, NULL, 'Ankle Comfort (M)', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100182, NULL, 'Ankle Comfort (XL)', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100183, NULL, 'Antacid Mixture 100ml (Allucid)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100184, NULL, 'Antacid Mixture 100ml (Bells)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100185, NULL, 'Antacid Mixture 200ml (Allucid)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100186, NULL, 'Antacid Mixture 200ml (Bells)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100187, NULL, 'Antanazole Cream 10g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100188, NULL, 'Anti Marks Cream 30g', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100189, NULL, 'Anti Rabies Inj', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100190, NULL, 'Anti Rho - D Inj', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100191, NULL, 'Antihistamine Cream', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100192, NULL, 'Anusol Oint 25g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100193, NULL, 'Anusol Supp', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100194, NULL, 'Apdyl-H Syrup', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100195, NULL, 'Appet Plus Syrup 200ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100196, NULL, 'Appeton Syrup 120ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100197, NULL, 'Appeton Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100198, NULL, 'Aptamil Milk No.1', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100199, NULL, 'Aptamil Milk No.2', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100200, NULL, 'Aqua Fresh Mouth Wash', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100201, NULL, 'Aqua Fresh Toothpaste', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100202, NULL, 'Argisept Cream 20g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100203, NULL, 'Arm Pouch', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100204, NULL, 'Arm Sling L', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100205, NULL, 'Arm Sling M', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100206, NULL, 'Arm Sling XL', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100207, NULL, 'Artefan Tabs 20/120 (Child)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100208, NULL, 'Artefan Tabs 80/480 (Adult)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100209, NULL, 'Artemether Inj 80mg/ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100210, NULL, 'Artequick Tabs (Pack 6)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100211, NULL, 'Artequin Tabs 300/375 (Children)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100212, NULL, 'Artequin Tabs 600/750 (Adult)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100213, NULL, 'Artesunate Inj 120ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100214, NULL, 'Artesunate Inj 30ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100215, NULL, 'Artesunate Inj 60ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100216, NULL, 'Aryu Wipes', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100217, NULL, 'Asante Soap 125g (Papaya and Honey)', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100218, NULL, 'Asante Soap 125g (Tamarind and Goat Milk)', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100219, NULL, 'Asante Soap 125mg (Tamarind and Honey)', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100220, NULL, 'Ascoril-D Cough Syrup', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100221, NULL, 'Aspa Sweet 150 Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100222, NULL, 'Aspa Sweet 450 Tabs', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100223, NULL, 'Aspirin Junior Tabs 75mg (Ascard)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100224, NULL, 'Asthalin Inhaler', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100225, NULL, 'Asthalin Respules', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100226, NULL, 'Asthalin Syrup', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100227, NULL, 'Atenolol Tabs 100mg (Velorin)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100228, NULL, 'Atenolol Tabs 50mg (Anol)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:21', '2025-01-21 16:48:21', NULL, NULL, NULL),
(100229, NULL, 'Atenolol Tabs 50mg (Denk)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:22', '2025-01-21 16:48:22', NULL, NULL, NULL),
(100230, NULL, 'Atenolol Tabs 50mg (India)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:22', '2025-01-21 16:48:22', NULL, NULL, NULL),
(100231, NULL, 'Atenolol Tabs 50mg (Tenbeta)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:22', '2025-01-21 16:48:22', NULL, NULL, NULL),
(100232, NULL, 'Atenolol Tabs 50mg (Velorin)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:22', '2025-01-21 16:48:22', NULL, NULL, NULL),
(100233, NULL, 'Atorvastatin Tabs 10mg (Atorem)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:22', '2025-01-21 16:48:22', NULL, NULL, NULL),
(100234, NULL, 'Atorvastatin Tabs 10mg (Atstat)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:22', '2025-01-21 16:48:22', NULL, NULL, NULL),
(100235, NULL, 'Atorvastatin Tabs 10mg (Aztol)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:22', '2025-01-21 16:48:22', NULL, NULL, NULL),
(100236, NULL, 'Atorvastatin Tabs 10mg (Aztor)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:22', '2025-01-21 16:48:22', NULL, NULL, NULL),
(100237, NULL, 'Atorvastatin Tabs 10mg (Caditor)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:22', '2025-01-21 16:48:22', NULL, NULL, NULL),
(100238, NULL, 'Atorvastatin Tabs 20mg (Atorem)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:22', '2025-01-21 16:48:22', NULL, NULL, NULL),
(100239, NULL, 'Atorvastatin Tabs 20mg (Atstat)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:22', '2025-01-21 16:48:22', NULL, NULL, NULL),
(100240, NULL, 'Atorvastatin Tabs 20mg (Aztol)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:22', '2025-01-21 16:48:22', NULL, NULL, NULL),
(100241, NULL, 'Atorvastatin Tabs 20mg (Aztor)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:22', '2025-01-21 16:48:22', NULL, NULL, NULL),
(100242, NULL, 'Atorvastatin Tabs 20mg (Caditor)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:22', '2025-01-21 16:48:22', NULL, NULL, NULL),
(100243, NULL, 'Atorvastatin Tabs 40mg (Aztor)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:22', '2025-01-21 16:48:22', NULL, NULL, NULL),
(100244, NULL, 'Atorvastatin Tabs 40mg (Caditor)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:22', '2025-01-21 16:48:22', NULL, NULL, NULL),
(100245, NULL, 'Atropine Drops', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:22', '2025-01-21 16:48:22', NULL, NULL, NULL),
(100246, NULL, 'Atropine Inj', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:22', '2025-01-21 16:48:22', NULL, NULL, NULL),
(100247, NULL, 'Augumentin BDT Tabs 1g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:22', '2025-01-21 16:48:22', NULL, NULL, NULL),
(100248, NULL, 'Augumentin Inj 1.2g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:22', '2025-01-21 16:48:22', NULL, NULL, NULL),
(100249, NULL, 'Augumentin Syrup 228mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:22', '2025-01-21 16:48:22', NULL, NULL, NULL),
(100250, NULL, 'Augumentin Syrup 457mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:22', '2025-01-21 16:48:22', NULL, NULL, NULL),
(100251, NULL, 'Augumentin Tabs 375mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:22', '2025-01-21 16:48:22', NULL, NULL, NULL),
(100252, NULL, 'Augumentin Tabs 625mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:22', '2025-01-21 16:48:22', NULL, NULL, NULL),
(100253, NULL, 'Avams Nasal Spray', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:22', '2025-01-21 16:48:22', NULL, NULL, NULL),
(100254, NULL, 'Avoca Caustic Pencil', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:22', '2025-01-21 16:48:22', NULL, NULL, NULL),
(100255, NULL, 'AXE Body Spray', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:22', '2025-01-21 16:48:22', NULL, NULL, NULL),
(100256, NULL, 'AXE Chapa Shoka', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:22', '2025-01-21 16:48:22', NULL, NULL, NULL),
(100257, NULL, 'AXE Deodorant', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:22', '2025-01-21 16:48:22', NULL, NULL, NULL),
(100258, NULL, 'Axe Shower Gel', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:22', '2025-01-21 16:48:22', NULL, NULL, NULL),
(100259, NULL, 'Ayu-Kof Syrup', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:22', '2025-01-21 16:48:22', NULL, NULL, NULL),
(100260, NULL, 'Aza Mafuta ya Asilli', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:22', '2025-01-21 16:48:22', NULL, NULL, NULL),
(100261, NULL, 'Aza Sabuni ya Asili', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:22', '2025-01-21 16:48:22', NULL, NULL, NULL),
(100262, NULL, 'Azithromycin Syrup 15ml (Azilin)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:22', '2025-01-21 16:48:22', NULL, NULL, NULL),
(100263, NULL, 'Azithromycin Syrup 15ml (Mazit)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:22', '2025-01-21 16:48:22', NULL, NULL, NULL),
(100264, NULL, 'Azithromycin Syrup 15ml (Zimax)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:22', '2025-01-21 16:48:22', NULL, NULL, NULL),
(100265, NULL, 'Azithromycin Syup 15ml (ATM)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:22', '2025-01-21 16:48:22', NULL, NULL, NULL),
(100266, NULL, 'Azithromycin Tabs 250mg (Azicure)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:22', '2025-01-21 16:48:22', NULL, NULL, NULL),
(100267, NULL, 'Azithromycin Tabs 250mg (Azithral)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:22', '2025-01-21 16:48:22', NULL, NULL, NULL),
(100268, NULL, 'Azithromycin Tabs 250mg (Azuma)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:22', '2025-01-21 16:48:22', NULL, NULL, NULL),
(100269, NULL, 'Azithromycin Tabs 250mg (Emina)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:22', '2025-01-21 16:48:22', NULL, NULL, NULL),
(100270, NULL, 'Azithromycin Tabs 250mg (Mazit)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:22', '2025-01-21 16:48:22', NULL, NULL, NULL),
(100271, NULL, 'Azithromycin Tabs 250mg (Zaha)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:22', '2025-01-21 16:48:22', NULL, NULL, NULL),
(100272, NULL, 'Azithromycin Tabs 250mg (Zocin)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:22', '2025-01-21 16:48:22', NULL, NULL, NULL),
(100273, NULL, 'Azithromycin Tabs 250mg (Zoltrim)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:22', '2025-01-21 16:48:22', NULL, NULL, NULL),
(100274, NULL, 'Azithromycin Tabs 500mg (Azicure)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:22', '2025-01-21 16:48:22', NULL, NULL, NULL),
(100275, NULL, 'Azithromycin Tabs 500mg (Azithral)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:22', '2025-01-21 16:48:22', NULL, NULL, NULL),
(100276, NULL, 'Azithromycin Tabs 500mg (Azuma)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:22', '2025-01-21 16:48:22', NULL, NULL, NULL),
(100277, NULL, 'Azithromycin Tabs 500mg (Emina)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:22', '2025-01-21 16:48:22', NULL, NULL, NULL),
(100278, NULL, 'Azithromycin Tabs 500mg (Zaha)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:22', '2025-01-21 16:48:22', NULL, NULL, NULL),
(100279, NULL, 'Azithromycin Tabs 500mg (Zoltrim)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:22', '2025-01-21 16:48:22', NULL, NULL, NULL),
(100280, NULL, 'Baby Cheeky (L)', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:22', '2025-01-21 16:48:22', NULL, NULL, NULL),
(100281, NULL, 'Baby Cheeky (M)', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:22', '2025-01-21 16:48:22', NULL, NULL, NULL),
(100282, NULL, 'Baby Cheeky (S)', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:22', '2025-01-21 16:48:22', NULL, NULL, NULL),
(100283, NULL, 'Baby Cheeky Newborn', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:22', '2025-01-21 16:48:22', NULL, NULL, NULL),
(100284, NULL, 'Baby Cough Mixture 100ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:22', '2025-01-21 16:48:22', NULL, NULL, NULL),
(100285, NULL, 'Baby Gripe Water 100ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:22', '2025-01-21 16:48:22', NULL, NULL, NULL),
(100286, NULL, 'Baby Gripe Water 60ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:22', '2025-01-21 16:48:22', NULL, NULL, NULL),
(100287, NULL, 'Baclofen Tabs 10mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:22', '2025-01-21 16:48:22', NULL, NULL, NULL),
(100288, NULL, 'Bacotaz Inj (Piper + Tazobactam)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:22', '2025-01-21 16:48:22', NULL, NULL, NULL),
(100289, NULL, 'Bactisept Soln 100ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:22', '2025-01-21 16:48:22', NULL, NULL, NULL),
(100290, NULL, 'Bactisept Soln 250ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:22', '2025-01-21 16:48:22', NULL, NULL, NULL),
(100291, NULL, 'Bactroban Cream 15g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:22', '2025-01-21 16:48:22', NULL, NULL, NULL);
INSERT INTO `inv_products` (`id`, `barcode`, `name`, `generic_name`, `category_id`, `sub_category_id`, `type`, `standard_uom`, `sales_uom`, `purchase_uom`, `indication`, `dosage`, `min_quantinty`, `max_quantinty`, `status`, `created_at`, `updated_at`, `npk_ratio`, `brand`, `pack_size`) VALUES
(100292, NULL, 'Bactroban Oint 15g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:22', '2025-01-21 16:48:22', NULL, NULL, NULL),
(100293, NULL, 'Bandage 10cm', NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:22', '2025-01-21 16:48:22', NULL, NULL, NULL),
(100294, NULL, 'Bandage 12.5cm', NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:22', '2025-01-21 16:48:22', NULL, NULL, NULL),
(100295, NULL, 'Bandage 15cm', NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:22', '2025-01-21 16:48:22', NULL, NULL, NULL),
(100296, NULL, 'Bandage 5cm', NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:22', '2025-01-21 16:48:22', NULL, NULL, NULL),
(100297, NULL, 'Bandage 7.5cm', NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:22', '2025-01-21 16:48:22', NULL, NULL, NULL),
(100298, NULL, 'Bannisters Glycerine 100ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:22', '2025-01-21 16:48:22', NULL, NULL, NULL),
(100299, NULL, 'Bannisters Glycerine 50ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:22', '2025-01-21 16:48:22', NULL, NULL, NULL),
(100300, NULL, 'BBE Lotion', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:22', '2025-01-21 16:48:22', NULL, NULL, NULL),
(100301, NULL, 'Becoshel Syrup 100ml (Vitamin B)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:22', '2025-01-21 16:48:22', NULL, NULL, NULL),
(100302, NULL, 'Belladona Mixture', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:22', '2025-01-21 16:48:22', NULL, NULL, NULL),
(100303, NULL, 'Belle Maternity Pads', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:23', '2025-01-21 16:48:23', NULL, NULL, NULL),
(100304, NULL, 'Belle Pads', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:23', '2025-01-21 16:48:23', NULL, NULL, NULL),
(100305, NULL, 'Bendroflumethiazide Tabs 5mg (Aprinox)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:23', '2025-01-21 16:48:23', NULL, NULL, NULL),
(100306, NULL, 'Bendroflumethiazide Tabs 5mg (Benduric)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:23', '2025-01-21 16:48:23', NULL, NULL, NULL),
(100307, NULL, 'Benylin 4 Flue Syrup 100ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:23', '2025-01-21 16:48:23', NULL, NULL, NULL),
(100308, NULL, 'Benylin 4 Flue Syrup 200ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:23', '2025-01-21 16:48:23', NULL, NULL, NULL),
(100309, NULL, 'Benylin Chest Syrup 100ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:23', '2025-01-21 16:48:23', NULL, NULL, NULL),
(100310, NULL, 'Benylin Dry Syrup 100ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:23', '2025-01-21 16:48:23', NULL, NULL, NULL),
(100311, NULL, 'Benylin Syrup Pediatric 100ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:23', '2025-01-21 16:48:23', NULL, NULL, NULL),
(100312, NULL, 'Benzathine Benzylpenicillin Inj (Penadur)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:23', '2025-01-21 16:48:23', NULL, NULL, NULL),
(100313, NULL, 'Benzhexol Tabs 5mg (Artane)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:23', '2025-01-21 16:48:23', NULL, NULL, NULL),
(100314, NULL, 'Benzox Forte Cream', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:23', '2025-01-21 16:48:23', NULL, NULL, NULL),
(100315, NULL, 'Benzylpencillin Sodium Inj (X-Pen)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:23', '2025-01-21 16:48:23', NULL, NULL, NULL),
(100316, NULL, 'Beprosalic Oint 15g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:23', '2025-01-21 16:48:23', NULL, NULL, NULL),
(100317, NULL, 'Betacort-N Cream 15g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:23', '2025-01-21 16:48:23', NULL, NULL, NULL),
(100318, NULL, 'Betaderm Cream', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:23', '2025-01-21 16:48:23', NULL, NULL, NULL),
(100319, NULL, 'Betaderm NM Cream 10g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:23', '2025-01-21 16:48:23', NULL, NULL, NULL),
(100320, NULL, 'Betaderm Oint', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:23', '2025-01-21 16:48:23', NULL, NULL, NULL),
(100321, NULL, 'Betafen Syrup 100ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:23', '2025-01-21 16:48:23', NULL, NULL, NULL),
(100322, NULL, 'Betahistine Tabs 8mg (Betaserc)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:23', '2025-01-21 16:48:23', NULL, NULL, NULL),
(100323, NULL, 'Betamethasone Cream (Tasone)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:23', '2025-01-21 16:48:23', NULL, NULL, NULL),
(100324, NULL, 'Betamethasone Drops (Eye/Ear/Nose)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:23', '2025-01-21 16:48:23', NULL, NULL, NULL),
(100325, NULL, 'Betapyn Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:23', '2025-01-21 16:48:23', NULL, NULL, NULL),
(100326, NULL, 'Betnovate Cream 15g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:23', '2025-01-21 16:48:23', NULL, NULL, NULL),
(100327, NULL, 'Betnovate Oint 30g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:23', '2025-01-21 16:48:23', NULL, NULL, NULL),
(100328, NULL, 'Bezic Oint', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:23', '2025-01-21 16:48:23', NULL, NULL, NULL),
(100329, NULL, 'Bio Oil 125ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:23', '2025-01-21 16:48:23', NULL, NULL, NULL),
(100330, NULL, 'Bio Oil 200ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:23', '2025-01-21 16:48:23', NULL, NULL, NULL),
(100331, NULL, 'Bio Oil 25ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:23', '2025-01-21 16:48:23', NULL, NULL, NULL),
(100332, NULL, 'Bio Oil 60ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:23', '2025-01-21 16:48:23', NULL, NULL, NULL),
(100333, NULL, 'Biox Handwash 500ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:23', '2025-01-21 16:48:23', NULL, NULL, NULL),
(100334, NULL, 'Bisacodyl Tabs 5mg (Cyprus)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:23', '2025-01-21 16:48:23', NULL, NULL, NULL),
(100335, NULL, 'Bisacodyl Tabs 5mg (India)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:23', '2025-01-21 16:48:23', NULL, NULL, NULL),
(100336, NULL, 'Bisoprolol Tabs 10mg (Bisodac)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:23', '2025-01-21 16:48:23', NULL, NULL, NULL),
(100337, NULL, 'Bisoprolol Tabs 10mg (Concor)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:23', '2025-01-21 16:48:23', NULL, NULL, NULL),
(100338, NULL, 'Bisoprolol Tabs 10mg (Corbis)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:23', '2025-01-21 16:48:23', NULL, NULL, NULL),
(100339, NULL, 'Bisoprolol Tabs 5mg (Bisodac)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:23', '2025-01-21 16:48:23', NULL, NULL, NULL),
(100340, NULL, 'Bisoprolol Tabs 5mg (Concor)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:23', '2025-01-21 16:48:23', NULL, NULL, NULL),
(100341, NULL, 'Bisoprolol Tabs 5mg (Corbis)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:23', '2025-01-21 16:48:23', NULL, NULL, NULL),
(100342, NULL, 'Blood Bag', NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:23', '2025-01-21 16:48:23', NULL, NULL, NULL),
(100343, NULL, 'Blood Giving Set', NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:23', '2025-01-21 16:48:23', NULL, NULL, NULL),
(100344, NULL, 'Blood Pressure (Bp) Machine - Digital', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:23', '2025-01-21 16:48:23', NULL, NULL, NULL),
(100345, NULL, 'Blood Pressure (Bp) Machine - Manual', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:23', '2025-01-21 16:48:23', NULL, NULL, NULL),
(100346, NULL, 'B-Mycin Cream', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:23', '2025-01-21 16:48:23', NULL, NULL, NULL),
(100347, NULL, 'Body Spray Mix', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:23', '2025-01-21 16:48:23', NULL, NULL, NULL),
(100348, NULL, 'Bonnisan Oil 120ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:23', '2025-01-21 16:48:23', NULL, NULL, NULL),
(100349, NULL, 'Boric Acid Ear Drop', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:23', '2025-01-21 16:48:23', NULL, NULL, NULL),
(100350, NULL, 'Bracin Drops (Tobramycin)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:23', '2025-01-21 16:48:23', NULL, NULL, NULL),
(100351, NULL, 'Bright Eye Drop', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:23', '2025-01-21 16:48:23', NULL, NULL, NULL),
(100352, NULL, 'Bromocriptin Tabs (Bramestone)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:23', '2025-01-21 16:48:23', NULL, NULL, NULL),
(100353, NULL, 'Bromocriptin Tabs (Medocriptine)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:23', '2025-01-21 16:48:23', NULL, NULL, NULL),
(100354, NULL, 'Brozen Cough Syrup (Zenufa)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:23', '2025-01-21 16:48:23', NULL, NULL, NULL),
(100355, NULL, 'Brufen Syrup 100ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:23', '2025-01-21 16:48:23', NULL, NULL, NULL),
(100356, NULL, 'Brufen Tabs 200mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:23', '2025-01-21 16:48:23', NULL, NULL, NULL),
(100357, NULL, 'Brufen Tabs 400mg (Denk)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:23', '2025-01-21 16:48:23', NULL, NULL, NULL),
(100358, NULL, 'Brustan Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:23', '2025-01-21 16:48:23', NULL, NULL, NULL),
(100359, NULL, 'Budecort Inhaler 100mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:23', '2025-01-21 16:48:23', NULL, NULL, NULL),
(100360, NULL, 'Budecort Inhaler 200mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:23', '2025-01-21 16:48:23', NULL, NULL, NULL),
(100361, NULL, 'Budecort Respules 0.5mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:23', '2025-01-21 16:48:23', NULL, NULL, NULL),
(100362, NULL, 'Budesonide Inhaler', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:23', '2025-01-21 16:48:23', NULL, NULL, NULL),
(100363, NULL, 'Budesonide Nasal Spray', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:24', '2025-01-21 16:48:24', NULL, NULL, NULL),
(100364, NULL, 'Bump Patrol', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:24', '2025-01-21 16:48:24', NULL, NULL, NULL),
(100365, NULL, 'Burn Care Plus 25g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:24', '2025-01-21 16:48:24', NULL, NULL, NULL),
(100366, NULL, 'Burn Cream 25g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:24', '2025-01-21 16:48:24', NULL, NULL, NULL),
(100367, NULL, 'Burnox Cream', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:24', '2025-01-21 16:48:24', NULL, NULL, NULL),
(100368, NULL, 'Calamine Lotion100ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:24', '2025-01-21 16:48:24', NULL, NULL, NULL),
(100369, NULL, 'Calcimag Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:24', '2025-01-21 16:48:24', NULL, NULL, NULL),
(100370, NULL, 'Calcium 600 + D3 Caps (21st Century)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:24', '2025-01-21 16:48:24', NULL, NULL, NULL),
(100371, NULL, 'Calcium Lactate Tabs 300mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:24', '2025-01-21 16:48:24', NULL, NULL, NULL),
(100372, NULL, 'Calcivita Caps', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:24', '2025-01-21 16:48:24', NULL, NULL, NULL),
(100373, NULL, 'Calcivita Forte Caps', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:24', '2025-01-21 16:48:24', NULL, NULL, NULL),
(100374, NULL, 'Caloshel Tabs 500mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:24', '2025-01-21 16:48:24', NULL, NULL, NULL),
(100375, NULL, 'Calpo Syrup 60ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:24', '2025-01-21 16:48:24', NULL, NULL, NULL),
(100376, NULL, 'Calpol Syrup 100ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:24', '2025-01-21 16:48:24', NULL, NULL, NULL),
(100377, NULL, 'Candacort Cream', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:24', '2025-01-21 16:48:24', NULL, NULL, NULL),
(100378, NULL, 'Canderel Powder 400g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:24', '2025-01-21 16:48:24', NULL, NULL, NULL),
(100379, NULL, 'Canderel Powder 75g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:24', '2025-01-21 16:48:24', NULL, NULL, NULL),
(100380, NULL, 'Canderel Tabs 300', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:24', '2025-01-21 16:48:24', NULL, NULL, NULL),
(100381, NULL, 'Candesartan Tabs 16mg (Aderan)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:24', '2025-01-21 16:48:24', NULL, NULL, NULL),
(100382, NULL, 'Candesartan Tabs 16mg (Atacand)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:24', '2025-01-21 16:48:24', NULL, NULL, NULL),
(100383, NULL, 'Candesartan Tabs 16mg (Candez)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:24', '2025-01-21 16:48:24', NULL, NULL, NULL),
(100384, NULL, 'Candesartan Tabs 32mg (Aderan)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:24', '2025-01-21 16:48:24', NULL, NULL, NULL),
(100385, NULL, 'Candesartan Tabs 8mg (Aderan)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:24', '2025-01-21 16:48:24', NULL, NULL, NULL),
(100386, NULL, 'Candesartan Tabs 8mg (Atacand)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:24', '2025-01-21 16:48:24', NULL, NULL, NULL),
(100387, NULL, 'Candesartan Tabs 8mg (Candez)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:24', '2025-01-21 16:48:24', NULL, NULL, NULL),
(100388, NULL, 'Candibiotic Ear Drop', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:24', '2025-01-21 16:48:24', NULL, NULL, NULL),
(100389, NULL, 'Canditral Caps 100mg (Itraconazole)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:24', '2025-01-21 16:48:24', NULL, NULL, NULL),
(100390, NULL, 'Cani Maks V2 Vaginal Cream', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:24', '2025-01-21 16:48:24', NULL, NULL, NULL),
(100391, NULL, 'Cannula', NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:24', '2025-01-21 16:48:24', NULL, NULL, NULL),
(100392, NULL, 'Captopril Tabs 25mg (Cyprus)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:24', '2025-01-21 16:48:24', NULL, NULL, NULL),
(100393, NULL, 'Captopril Tabs 25mg (Denk)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:24', '2025-01-21 16:48:24', NULL, NULL, NULL),
(100394, NULL, 'Captopril Tabs 25mg (India)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:24', '2025-01-21 16:48:24', NULL, NULL, NULL),
(100395, NULL, 'Carambola Soap', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:24', '2025-01-21 16:48:24', NULL, NULL, NULL),
(100396, NULL, 'Carbagotine Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:24', '2025-01-21 16:48:24', NULL, NULL, NULL),
(100397, NULL, 'Carbamazapine Tabs (India)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:24', '2025-01-21 16:48:24', NULL, NULL, NULL),
(100398, NULL, 'Carbamazapine Tabs (Storilat)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:24', '2025-01-21 16:48:24', NULL, NULL, NULL),
(100399, NULL, 'Carbamazole Tabs 5mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:24', '2025-01-21 16:48:24', NULL, NULL, NULL),
(100400, NULL, 'Carbimazole Tabs 5mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:24', '2025-01-21 16:48:24', NULL, NULL, NULL),
(100401, NULL, 'Cardioace Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:24', '2025-01-21 16:48:24', NULL, NULL, NULL),
(100402, NULL, 'Cardiron Syrup', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:24', '2025-01-21 16:48:24', NULL, NULL, NULL),
(100403, NULL, 'CareStart Malaria (MRDT)', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:24', '2025-01-21 16:48:24', NULL, NULL, NULL),
(100404, NULL, 'Carofit Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:24', '2025-01-21 16:48:24', NULL, NULL, NULL),
(100405, NULL, 'Carvedilol Tabs 12.5mg (Cardivas)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:24', '2025-01-21 16:48:24', NULL, NULL, NULL),
(100406, NULL, 'Carvedilol Tabs 12.5mg (Cardoz)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:24', '2025-01-21 16:48:24', NULL, NULL, NULL),
(100407, NULL, 'Carvedilol Tabs 12.5mg (Denk)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:24', '2025-01-21 16:48:24', NULL, NULL, NULL),
(100408, NULL, 'Carvedilol Tabs 12.5mg (Vacodil)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:24', '2025-01-21 16:48:24', NULL, NULL, NULL),
(100409, NULL, 'Carvedilol Tabs 25mg (Denk)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:24', '2025-01-21 16:48:24', NULL, NULL, NULL),
(100410, NULL, 'Carvedilol Tabs 6.25mg (Cardivas)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:24', '2025-01-21 16:48:24', NULL, NULL, NULL),
(100411, NULL, 'Carvedilol Tabs 6.25mg (Cardoz)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:24', '2025-01-21 16:48:24', NULL, NULL, NULL),
(100412, NULL, 'Carvedilol Tabs 6.25mg (Denk)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:25', '2025-01-21 16:48:25', NULL, NULL, NULL),
(100413, NULL, 'Carvedilol Tabs 6.25mg (Vacodil)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:25', '2025-01-21 16:48:25', NULL, NULL, NULL),
(100414, NULL, 'Castor Oil 100ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:25', '2025-01-21 16:48:25', NULL, NULL, NULL),
(100415, NULL, 'Cataflam Tabs 50mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:25', '2025-01-21 16:48:25', NULL, NULL, NULL),
(100416, NULL, 'Catheter', NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:25', '2025-01-21 16:48:25', NULL, NULL, NULL),
(100417, NULL, 'Cathy Pad', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:25', '2025-01-21 16:48:25', NULL, NULL, NULL),
(100418, NULL, 'Cefadroxil Caps 500mg (Drox)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:25', '2025-01-21 16:48:25', NULL, NULL, NULL),
(100419, NULL, 'Cefadroxil Caps 500mg (Sandrox)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:25', '2025-01-21 16:48:25', NULL, NULL, NULL),
(100420, NULL, 'Cefadroxil Susp 125ml (Sandrox)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:25', '2025-01-21 16:48:25', NULL, NULL, NULL),
(100421, NULL, 'Cefadroxil Susp 250ml (Drox)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:25', '2025-01-21 16:48:25', NULL, NULL, NULL),
(100422, NULL, 'Cefepime Inj 2g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:25', '2025-01-21 16:48:25', NULL, NULL, NULL),
(100423, NULL, 'Cefixime Susp 100mg (Cef OD)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:25', '2025-01-21 16:48:25', NULL, NULL, NULL),
(100424, NULL, 'Cefixime Susp 100mg (C-Tax)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:25', '2025-01-21 16:48:25', NULL, NULL, NULL),
(100425, NULL, 'Cefixime Susp 100mg (Fixinect)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:25', '2025-01-21 16:48:25', NULL, NULL, NULL),
(100426, NULL, 'Cefixime Susp 100mg (Sanix)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:25', '2025-01-21 16:48:25', NULL, NULL, NULL),
(100427, NULL, 'Cefixime Susp 100mg (Taxim O)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:25', '2025-01-21 16:48:25', NULL, NULL, NULL),
(100428, NULL, 'Cefixime Susp 100mg (Theofix)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:25', '2025-01-21 16:48:25', NULL, NULL, NULL),
(100429, NULL, 'Cefixime Tabs 200mg (C-Tax)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:25', '2025-01-21 16:48:25', NULL, NULL, NULL),
(100430, NULL, 'Cefixime Tabs 200mg (Fixinect)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:25', '2025-01-21 16:48:25', NULL, NULL, NULL),
(100431, NULL, 'Cefixime Tabs 200mg (Sanix)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:25', '2025-01-21 16:48:25', NULL, NULL, NULL),
(100432, NULL, 'Cefixime Tabs 200mg (Taxim O)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:25', '2025-01-21 16:48:25', NULL, NULL, NULL),
(100433, NULL, 'Cefixime Tabs 200mg (Theofix)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:25', '2025-01-21 16:48:25', NULL, NULL, NULL),
(100434, NULL, 'Cefixime Tabs 400mg (C-Tax)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:25', '2025-01-21 16:48:25', NULL, NULL, NULL),
(100435, NULL, 'Cefixime Tabs 400mg (Fixinect)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:25', '2025-01-21 16:48:25', NULL, NULL, NULL),
(100436, NULL, 'Cefixime Tabs 400mg (Sanix)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:25', '2025-01-21 16:48:25', NULL, NULL, NULL),
(100437, NULL, 'Cefixime Tabs 400mg (Taxim O)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:25', '2025-01-21 16:48:25', NULL, NULL, NULL),
(100438, NULL, 'Cefixime Tabs 400mg (Theofix)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:25', '2025-01-21 16:48:25', NULL, NULL, NULL),
(100439, NULL, 'Cefotaxime Inj 1g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:25', '2025-01-21 16:48:25', NULL, NULL, NULL),
(100440, NULL, 'Cefotaxime Inj 250mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:25', '2025-01-21 16:48:25', NULL, NULL, NULL),
(100441, NULL, 'Cefpodoxime Syrup (Cefodox)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:25', '2025-01-21 16:48:25', NULL, NULL, NULL),
(100442, NULL, 'Cefpodoxime Syrup (Sandrox)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:25', '2025-01-21 16:48:25', NULL, NULL, NULL),
(100443, NULL, 'Cefpodoxime Syrup (Vercef)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:25', '2025-01-21 16:48:25', NULL, NULL, NULL),
(100444, NULL, 'Cefpodoxime Tabs (Cefodox)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:25', '2025-01-21 16:48:25', NULL, NULL, NULL),
(100445, NULL, 'Cefpodoxime Tabs (Sandrox)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:25', '2025-01-21 16:48:25', NULL, NULL, NULL),
(100446, NULL, 'Ceftriaxone + Sulbactum Inj', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:25', '2025-01-21 16:48:25', NULL, NULL, NULL),
(100447, NULL, 'Ceftriaxone Inj 1g (Epicephin)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:25', '2025-01-21 16:48:25', NULL, NULL, NULL),
(100448, NULL, 'Ceftriaxone Inj 1g (Powercef)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:25', '2025-01-21 16:48:25', NULL, NULL, NULL),
(100449, NULL, 'Ceftriaxone Inj 250mg (Powercef)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:25', '2025-01-21 16:48:25', NULL, NULL, NULL),
(100450, NULL, 'Ceftriaxone Inj 500mg (Powercef)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:25', '2025-01-21 16:48:25', NULL, NULL, NULL),
(100451, NULL, 'Ceftriaxone Inj 750mg (Powercef)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:25', '2025-01-21 16:48:25', NULL, NULL, NULL),
(100452, NULL, 'Cefuroxime Susp 50ml (Auxtocef)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:25', '2025-01-21 16:48:25', NULL, NULL, NULL),
(100453, NULL, 'Cefuroxime Susp 50ml (Zinnat)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:25', '2025-01-21 16:48:25', NULL, NULL, NULL),
(100454, NULL, 'Cefuroxime Tabs 250mg (Auxtocef)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:25', '2025-01-21 16:48:25', NULL, NULL, NULL),
(100455, NULL, 'Cefuroxime Tabs 250mg (Zinnat)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:25', '2025-01-21 16:48:25', NULL, NULL, NULL),
(100456, NULL, 'Cefuroxime Tabs 500mg (Auxtocef)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:25', '2025-01-21 16:48:25', NULL, NULL, NULL),
(100457, NULL, 'Cefuroxime Tabs 500mg (Zinnat)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:25', '2025-01-21 16:48:25', NULL, NULL, NULL),
(100458, NULL, 'Celestamine Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:25', '2025-01-21 16:48:25', NULL, NULL, NULL),
(100459, NULL, 'Celestone Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:25', '2025-01-21 16:48:25', NULL, NULL, NULL),
(100460, NULL, 'Centrum Tabs 30+', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:25', '2025-01-21 16:48:25', NULL, NULL, NULL),
(100461, NULL, 'Centrum Tabs 50+', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:25', '2025-01-21 16:48:25', NULL, NULL, NULL),
(100462, NULL, 'Centrum Tabs 60+', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:25', '2025-01-21 16:48:25', NULL, NULL, NULL),
(100463, NULL, 'Cephalexin Caps 250mg (Novaphex)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:25', '2025-01-21 16:48:25', NULL, NULL, NULL),
(100464, NULL, 'Cephalexin Caps 250mg (Sanceph)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:25', '2025-01-21 16:48:25', NULL, NULL, NULL),
(100465, NULL, 'Cephalexin Caps 500mg (Novaphex)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:25', '2025-01-21 16:48:25', NULL, NULL, NULL),
(100466, NULL, 'Cephalexin Caps 500mg (Sanceph)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:26', '2025-01-21 16:48:26', NULL, NULL, NULL),
(100467, NULL, 'Cephalexin Susp 125mg (Felexin)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:26', '2025-01-21 16:48:26', NULL, NULL, NULL),
(100468, NULL, 'Cephalexin Susp 125mg (Inlex)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:26', '2025-01-21 16:48:26', NULL, NULL, NULL),
(100469, NULL, 'Cephalexin Susp 125mg (Novaphex)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:26', '2025-01-21 16:48:26', NULL, NULL, NULL),
(100470, NULL, 'Cephalexin Susp 125mg (Sanceph)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:26', '2025-01-21 16:48:26', NULL, NULL, NULL),
(100471, NULL, 'Cephalexin Susp 250mg (Sanceph)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:26', '2025-01-21 16:48:26', NULL, NULL, NULL),
(100472, NULL, 'Cerumol Ear Drops 11ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:26', '2025-01-21 16:48:26', NULL, NULL, NULL),
(100473, NULL, 'Cervical Collar (L)', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:26', '2025-01-21 16:48:26', NULL, NULL, NULL),
(100474, NULL, 'Cervical Collar (M)', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:26', '2025-01-21 16:48:26', NULL, NULL, NULL),
(100475, NULL, 'Cervical Collar (S)', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:26', '2025-01-21 16:48:26', NULL, NULL, NULL),
(100476, NULL, 'Cetirizine Syrup', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:26', '2025-01-21 16:48:26', NULL, NULL, NULL),
(100477, NULL, 'Cetirizine Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:26', '2025-01-21 16:48:26', NULL, NULL, NULL),
(100478, NULL, 'Cherish Pads', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:26', '2025-01-21 16:48:26', NULL, NULL, NULL),
(100479, NULL, 'ChestCof Syrup', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:26', '2025-01-21 16:48:26', NULL, NULL, NULL),
(100480, NULL, 'ChestCof Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:26', '2025-01-21 16:48:26', NULL, NULL, NULL),
(100481, NULL, 'Children Cough Sysrup (Bells)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:26', '2025-01-21 16:48:26', NULL, NULL, NULL),
(100482, NULL, 'Chlopropamide Tabs (Dibonis)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:26', '2025-01-21 16:48:26', NULL, NULL, NULL),
(100483, NULL, 'Chloramphenical Caps', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:26', '2025-01-21 16:48:26', NULL, NULL, NULL),
(100484, NULL, 'Chloramphenical Ear Drops', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:26', '2025-01-21 16:48:26', NULL, NULL, NULL),
(100485, NULL, 'Chloramphenical Eye Drops', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:26', '2025-01-21 16:48:26', NULL, NULL, NULL),
(100486, NULL, 'Chloramphenical Eye Oint', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:26', '2025-01-21 16:48:26', NULL, NULL, NULL),
(100487, NULL, 'Chloramphenical Inj', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:26', '2025-01-21 16:48:26', NULL, NULL, NULL),
(100488, NULL, 'Chloramphenical Susp', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:26', '2025-01-21 16:48:26', NULL, NULL, NULL),
(100489, NULL, 'Chlorpheniramine Syrup (Piriton)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:26', '2025-01-21 16:48:26', NULL, NULL, NULL),
(100490, NULL, 'Chlorpheniramine Syrup (Rinalin)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:26', '2025-01-21 16:48:26', NULL, NULL, NULL),
(100491, NULL, 'Chlorpheniramine Tabs (Piriton)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:26', '2025-01-21 16:48:26', NULL, NULL, NULL),
(100492, NULL, 'Chlorpromazine Inj', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:26', '2025-01-21 16:48:26', NULL, NULL, NULL),
(100493, NULL, 'Chlorpromazine Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:26', '2025-01-21 16:48:26', NULL, NULL, NULL),
(100494, NULL, 'Chromic Cut Gut', NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:26', '2025-01-21 16:48:26', NULL, NULL, NULL),
(100495, NULL, 'Cifran CT Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:26', '2025-01-21 16:48:26', NULL, NULL, NULL),
(100496, NULL, 'Cifran OD Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:26', '2025-01-21 16:48:26', NULL, NULL, NULL),
(100497, NULL, 'Cimetidine Tabs 200mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:26', '2025-01-21 16:48:26', NULL, NULL, NULL),
(100498, NULL, 'Cimetidine Tabs 400mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:26', '2025-01-21 16:48:26', NULL, NULL, NULL),
(100499, NULL, 'Cipro CT Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:26', '2025-01-21 16:48:26', NULL, NULL, NULL),
(100500, NULL, 'Ciprofloxacin Ear/Eye Drop', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:26', '2025-01-21 16:48:26', NULL, NULL, NULL),
(100501, NULL, 'Ciprofloxacin Inj', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:26', '2025-01-21 16:48:26', NULL, NULL, NULL),
(100502, NULL, 'Ciprofloxacin Tabs 250mg (Zindolin)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:26', '2025-01-21 16:48:26', NULL, NULL, NULL),
(100503, NULL, 'Ciprofloxacin Tabs 500mg (Denk)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:26', '2025-01-21 16:48:26', NULL, NULL, NULL),
(100504, NULL, 'Ciprofloxacin Tabs 500mg (Egypt)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:26', '2025-01-21 16:48:26', NULL, NULL, NULL),
(100505, NULL, 'Ciprofloxacin Tabs 500mg (India)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:26', '2025-01-21 16:48:26', NULL, NULL, NULL),
(100506, NULL, 'Ciprofloxacin Tabs 500mg (Zindolin)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:26', '2025-01-21 16:48:26', NULL, NULL, NULL),
(100507, NULL, 'Cital Solution', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:26', '2025-01-21 16:48:26', NULL, NULL, NULL),
(100508, NULL, 'Clarithromycin Syrup 125mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:26', '2025-01-21 16:48:26', NULL, NULL, NULL),
(100509, NULL, 'Clarithromycin Tabs 250mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:26', '2025-01-21 16:48:26', NULL, NULL, NULL),
(100510, NULL, 'Clarithromycin Tabs 500mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:26', '2025-01-21 16:48:26', NULL, NULL, NULL),
(100511, NULL, 'Clavam Syrup 156mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:26', '2025-01-21 16:48:26', NULL, NULL, NULL),
(100512, NULL, 'Clavam Syrup 228ml BID', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:26', '2025-01-21 16:48:26', NULL, NULL, NULL),
(100513, NULL, 'Clavam Tabs 1g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:26', '2025-01-21 16:48:26', NULL, NULL, NULL),
(100514, NULL, 'Clavam Tabs 375mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:26', '2025-01-21 16:48:26', NULL, NULL, NULL),
(100515, NULL, 'Clavam Tabs 625mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:26', '2025-01-21 16:48:26', NULL, NULL, NULL),
(100516, NULL, 'Clavicle Brace (L)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:26', '2025-01-21 16:48:26', NULL, NULL, NULL),
(100517, NULL, 'Clavicle Brace (XL)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:26', '2025-01-21 16:48:26', NULL, NULL, NULL),
(100518, NULL, 'Clavullin Syrup 156mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:26', '2025-01-21 16:48:26', NULL, NULL, NULL),
(100519, NULL, 'Clavullin Syrup 228mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:26', '2025-01-21 16:48:26', NULL, NULL, NULL),
(100520, NULL, 'Clavullin Tabs 375mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:26', '2025-01-21 16:48:26', NULL, NULL, NULL),
(100521, NULL, 'Clavullin Tabs 625mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:26', '2025-01-21 16:48:26', NULL, NULL, NULL),
(100522, NULL, 'Cledomox Susp 228mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:26', '2025-01-21 16:48:26', NULL, NULL, NULL),
(100523, NULL, 'Cledomox Tabs 375mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:26', '2025-01-21 16:48:26', NULL, NULL, NULL),
(100524, NULL, 'Cledomox Tabs 625mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:26', '2025-01-21 16:48:26', NULL, NULL, NULL),
(100525, NULL, 'Clere Body Cream 125ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:27', '2025-01-21 16:48:27', NULL, NULL, NULL),
(100526, NULL, 'Clere Body Cream 300ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:27', '2025-01-21 16:48:27', NULL, NULL, NULL),
(100527, NULL, 'Clere Body Cream 500ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:27', '2025-01-21 16:48:27', NULL, NULL, NULL),
(100528, NULL, 'Clere Deodorant', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:27', '2025-01-21 16:48:27', NULL, NULL, NULL),
(100529, NULL, 'Clere Glycerine 100ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:27', '2025-01-21 16:48:27', NULL, NULL, NULL),
(100530, NULL, 'Clere Glycerine 200ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:27', '2025-01-21 16:48:27', NULL, NULL, NULL),
(100531, NULL, 'Clere Glycerine 50ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:27', '2025-01-21 16:48:27', NULL, NULL, NULL),
(100532, NULL, 'Clere Lotion 400ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:27', '2025-01-21 16:48:27', NULL, NULL, NULL),
(100533, NULL, 'Clindamycin Gel (Clincin)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:27', '2025-01-21 16:48:27', NULL, NULL, NULL),
(100534, NULL, 'Clindamycin Gel 25g (C-Mycin)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:27', '2025-01-21 16:48:27', NULL, NULL, NULL),
(100535, NULL, 'Clindamycin Inj 600mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:27', '2025-01-21 16:48:27', NULL, NULL, NULL),
(100536, NULL, 'Clindamycin Lotion (C-Mycin)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:27', '2025-01-21 16:48:27', NULL, NULL, NULL),
(100537, NULL, 'Clindamycin Tabs 150 (Dalacin)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:27', '2025-01-21 16:48:27', NULL, NULL, NULL),
(100538, NULL, 'Clindamycin Tabs 300mg (Dalacin)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:27', '2025-01-21 16:48:27', NULL, NULL, NULL),
(100539, NULL, 'Clindamycin Tabs 300mg (Tidact)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:27', '2025-01-21 16:48:27', NULL, NULL, NULL),
(100540, NULL, 'Clindamycin V Cream 40g (Vagibact)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:27', '2025-01-21 16:48:27', NULL, NULL, NULL),
(100541, NULL, 'Clinical Thermometer', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:27', '2025-01-21 16:48:27', NULL, NULL, NULL),
(100542, NULL, 'Cloben-G Cream 15g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:27', '2025-01-21 16:48:27', NULL, NULL, NULL),
(100543, NULL, 'Clobetasol Cream (Clobederm)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:27', '2025-01-21 16:48:27', NULL, NULL, NULL),
(100544, NULL, 'Cloderm Oint 15g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:27', '2025-01-21 16:48:27', NULL, NULL, NULL),
(100545, NULL, 'Clomiphene Tabs (Comitab)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:27', '2025-01-21 16:48:27', NULL, NULL, NULL),
(100546, NULL, 'Clomiphene Tabs (OvaMit)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:27', '2025-01-21 16:48:27', NULL, NULL, NULL),
(100547, NULL, 'Clonazepam Tabs 2mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:27', '2025-01-21 16:48:27', NULL, NULL, NULL),
(100548, NULL, 'Clopidogrel Tabs + Aspirin (Clavix As)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:27', '2025-01-21 16:48:27', NULL, NULL, NULL),
(100549, NULL, 'Clopidogrel Tabs 75mg  (Clopiplav)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:27', '2025-01-21 16:48:27', NULL, NULL, NULL),
(100550, NULL, 'Clopidogrel Tabs 75mg (Clopact)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:27', '2025-01-21 16:48:27', NULL, NULL, NULL),
(100551, NULL, 'Clopidogrel Tabs 75mg (Denk)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:27', '2025-01-21 16:48:27', NULL, NULL, NULL),
(100552, NULL, 'Clopidogrel Tabs 75mg (Deplat)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:27', '2025-01-21 16:48:27', NULL, NULL, NULL),
(100553, NULL, 'Clopidogrel Tabs 75mg (Instaclop)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:27', '2025-01-21 16:48:27', NULL, NULL, NULL),
(100554, NULL, 'Clotrimazole Cream (Candid B)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:27', '2025-01-21 16:48:27', NULL, NULL, NULL),
(100555, NULL, 'Clotrimazole Cream (Candistat)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:27', '2025-01-21 16:48:27', NULL, NULL, NULL),
(100556, NULL, 'Clotrimazole Cream (Fungivita-C)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:27', '2025-01-21 16:48:27', NULL, NULL, NULL),
(100557, NULL, 'Clotrimazole Cream (Labesten)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:27', '2025-01-21 16:48:27', NULL, NULL, NULL),
(100558, NULL, 'Clotrimazole Cream 15g (Candiderm)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:27', '2025-01-21 16:48:27', NULL, NULL, NULL),
(100559, NULL, 'Clotrimazole Cream 15g (Canestal)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:27', '2025-01-21 16:48:27', NULL, NULL, NULL),
(100560, NULL, 'Clotrimazole Cream 15g (CLOB)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:27', '2025-01-21 16:48:27', NULL, NULL, NULL),
(100561, NULL, 'Clotrimazole Cream 15g (Clotrilin)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:27', '2025-01-21 16:48:27', NULL, NULL, NULL),
(100562, NULL, 'Clotrimazole Cream 15g (SKANDID)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:27', '2025-01-21 16:48:27', NULL, NULL, NULL),
(100563, NULL, 'Clotrimazole Cream 20g (Candid)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:27', '2025-01-21 16:48:27', NULL, NULL, NULL),
(100564, NULL, 'Clotrimazole Cream 20g (Candigen)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:27', '2025-01-21 16:48:27', NULL, NULL, NULL),
(100565, NULL, 'Clotrimazole Cream 20g (Clotrine)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:27', '2025-01-21 16:48:27', NULL, NULL, NULL),
(100566, NULL, 'Clotrimazole Cream 20g (Dermosporin)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:27', '2025-01-21 16:48:27', NULL, NULL, NULL),
(100567, NULL, 'Clotrimazole Cream 20g (Pricostat)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:27', '2025-01-21 16:48:27', NULL, NULL, NULL),
(100568, NULL, 'Clotrimazole Ear Drops (Candid)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:27', '2025-01-21 16:48:27', NULL, NULL, NULL),
(100569, NULL, 'Clotrimazole Lotion (Candid B)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:27', '2025-01-21 16:48:27', NULL, NULL, NULL),
(100570, NULL, 'Clotrimazole Lotion (Candid)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:27', '2025-01-21 16:48:27', NULL, NULL, NULL),
(100571, NULL, 'Clotrimazole Mouth Paint (Candid)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:27', '2025-01-21 16:48:27', NULL, NULL, NULL),
(100572, NULL, 'Clotrimazole Powder (Candid)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:27', '2025-01-21 16:48:27', NULL, NULL, NULL),
(100573, NULL, 'Clotrimazole Powder 30g (Emina)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:27', '2025-01-21 16:48:27', NULL, NULL, NULL),
(100574, NULL, 'Clotrimazole Susp 60ml (Candid TV)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:27', '2025-01-21 16:48:27', NULL, NULL, NULL),
(100575, NULL, 'Clotrimazole Tabs V6 (Candistat)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:27', '2025-01-21 16:48:27', NULL, NULL, NULL),
(100576, NULL, 'Clotrimazole V Cream  15g (Pricostat)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:27', '2025-01-21 16:48:27', NULL, NULL, NULL),
(100577, NULL, 'Clotrimazole V Cream 15g (Clotrilin)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:27', '2025-01-21 16:48:27', NULL, NULL, NULL),
(100578, NULL, 'Clotrimazole V Cream 7g (Candigo)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:27', '2025-01-21 16:48:27', NULL, NULL, NULL),
(100579, NULL, 'Clotrimazole V Gel (Candid)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:27', '2025-01-21 16:48:27', NULL, NULL, NULL);
INSERT INTO `inv_products` (`id`, `barcode`, `name`, `generic_name`, `category_id`, `sub_category_id`, `type`, `standard_uom`, `sales_uom`, `purchase_uom`, `indication`, `dosage`, `min_quantinty`, `max_quantinty`, `status`, `created_at`, `updated_at`, `npk_ratio`, `brand`, `pack_size`) VALUES
(100580, NULL, 'Clotrimazole V Tabs (Labesten)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:27', '2025-01-21 16:48:27', NULL, NULL, NULL),
(100581, NULL, 'Clotrimazole V-1 VP (Candid)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:27', '2025-01-21 16:48:27', NULL, NULL, NULL),
(100582, NULL, 'Clotrimazole V-3 VP (Candid)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:27', '2025-01-21 16:48:27', NULL, NULL, NULL),
(100583, NULL, 'Clotrimazole V-6 VP (Candid)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:27', '2025-01-21 16:48:27', NULL, NULL, NULL),
(100584, NULL, 'Clotrimazole VP (Candistat)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:27', '2025-01-21 16:48:27', NULL, NULL, NULL),
(100585, NULL, 'Clotrimazole VP (Clorifort)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:27', '2025-01-21 16:48:27', NULL, NULL, NULL),
(100586, NULL, 'Clotrimazole VP (Clotrine)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:27', '2025-01-21 16:48:27', NULL, NULL, NULL),
(100587, NULL, 'Clotrisone Cream 10g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:27', '2025-01-21 16:48:27', NULL, NULL, NULL),
(100588, NULL, 'Co Losar Denk Tabs 50/12.5', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:27', '2025-01-21 16:48:27', NULL, NULL, NULL),
(100589, NULL, 'Co Q10  Caps (21st Century)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:27', '2025-01-21 16:48:27', NULL, NULL, NULL),
(100590, NULL, 'Co-Artesian Susp 120ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:27', '2025-01-21 16:48:27', NULL, NULL, NULL),
(100591, NULL, 'Co-Artesian Susp 60ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:27', '2025-01-21 16:48:27', NULL, NULL, NULL),
(100592, NULL, 'Codactiv Fort Syrup 100ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:27', '2025-01-21 16:48:27', NULL, NULL, NULL),
(100593, NULL, 'Coff-Ex Syrup (Adult)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:27', '2025-01-21 16:48:27', NULL, NULL, NULL),
(100594, NULL, 'Coff-Ex Syrup (Pediatric)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:27', '2025-01-21 16:48:27', NULL, NULL, NULL),
(100595, NULL, 'Coffnil Herbal Syrup 100ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:27', '2025-01-21 16:48:27', NULL, NULL, NULL),
(100596, NULL, 'Cofta Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:27', '2025-01-21 16:48:27', NULL, NULL, NULL),
(100597, NULL, 'Colchicine Tabs 0.5mg (G-Out)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:28', '2025-01-21 16:48:28', NULL, NULL, NULL),
(100598, NULL, 'ColdCap Caps', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:28', '2025-01-21 16:48:28', NULL, NULL, NULL),
(100599, NULL, 'ColdCap Syrup', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:28', '2025-01-21 16:48:28', NULL, NULL, NULL),
(100600, NULL, 'ColdOff Caps', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:28', '2025-01-21 16:48:28', NULL, NULL, NULL),
(100601, NULL, 'Coldril Caps', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:28', '2025-01-21 16:48:28', NULL, NULL, NULL),
(100602, NULL, 'Coldril Sryup', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:28', '2025-01-21 16:48:28', NULL, NULL, NULL),
(100603, NULL, 'ColdVan Caps', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:28', '2025-01-21 16:48:28', NULL, NULL, NULL),
(100604, NULL, 'Colecalciferol 1000iu (Vitamin D3)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:28', '2025-01-21 16:48:28', NULL, NULL, NULL),
(100605, NULL, 'Colgate Herbal Toothpaste 140g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:28', '2025-01-21 16:48:28', NULL, NULL, NULL),
(100606, NULL, 'Colgate Herbal Toothpaste 70g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:28', '2025-01-21 16:48:28', NULL, NULL, NULL),
(100607, NULL, 'Colgate Junior Toothpaste 50g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:28', '2025-01-21 16:48:28', NULL, NULL, NULL),
(100608, NULL, 'Colgate Max Cavity Toothpaste 140g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:28', '2025-01-21 16:48:28', NULL, NULL, NULL),
(100609, NULL, 'Colgate Max Cavity Toothpaste 175g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:28', '2025-01-21 16:48:28', NULL, NULL, NULL),
(100610, NULL, 'Colgate Max Cavity Toothpaste 35g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:28', '2025-01-21 16:48:28', NULL, NULL, NULL),
(100611, NULL, 'Colgate Max Cavity Toothpaste 70g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:28', '2025-01-21 16:48:28', NULL, NULL, NULL),
(100612, NULL, 'Colgate Max Fresh Toothpaste 100g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:28', '2025-01-21 16:48:28', NULL, NULL, NULL),
(100613, NULL, 'Colgate Max Fresh Toothpaste 130g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:28', '2025-01-21 16:48:28', NULL, NULL, NULL),
(100614, NULL, 'Colgate Max Fresh Toothpaste 70g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:28', '2025-01-21 16:48:28', NULL, NULL, NULL),
(100615, NULL, 'Colgate Maxcavity Toothpaste 100g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:28', '2025-01-21 16:48:28', NULL, NULL, NULL),
(100616, NULL, 'Colgate Mouth Wash', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:28', '2025-01-21 16:48:28', NULL, NULL, NULL),
(100617, NULL, 'Colgate Sensitive Toothpaste 100g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:28', '2025-01-21 16:48:28', NULL, NULL, NULL),
(100618, NULL, 'Colgate Toothbrush (0-2)yrs', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:28', '2025-01-21 16:48:28', NULL, NULL, NULL),
(100619, NULL, 'Colgate Toothbrush (2-5)yrs', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:28', '2025-01-21 16:48:28', NULL, NULL, NULL),
(100620, NULL, 'Colgate Toothbrush (Double Action)', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:28', '2025-01-21 16:48:28', NULL, NULL, NULL),
(100621, NULL, 'Colgate Triple Action Toothpaste 140g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:28', '2025-01-21 16:48:28', NULL, NULL, NULL),
(100622, NULL, 'Colgate Triple Action Toothpaste 70g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:28', '2025-01-21 16:48:28', NULL, NULL, NULL),
(100623, NULL, 'Colostomy Bag', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:28', '2025-01-21 16:48:28', NULL, NULL, NULL),
(100624, NULL, 'Co-Malather Compact Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:28', '2025-01-21 16:48:28', NULL, NULL, NULL),
(100625, NULL, 'Co-Malather Syrup 60ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:28', '2025-01-21 16:48:28', NULL, NULL, NULL),
(100626, NULL, 'Combantrin Tabs 125mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:28', '2025-01-21 16:48:28', NULL, NULL, NULL),
(100627, NULL, 'Combiflo Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:28', '2025-01-21 16:48:28', NULL, NULL, NULL),
(100628, NULL, 'Condom (Bareback)', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:28', '2025-01-21 16:48:28', NULL, NULL, NULL),
(100629, NULL, 'Condom (Bull)', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:28', '2025-01-21 16:48:28', NULL, NULL, NULL),
(100630, NULL, 'Condom (Catheter)', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:28', '2025-01-21 16:48:28', NULL, NULL, NULL),
(100631, NULL, 'Condom (Desire)', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:28', '2025-01-21 16:48:28', NULL, NULL, NULL),
(100632, NULL, 'Condom (Durex)', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:28', '2025-01-21 16:48:28', NULL, NULL, NULL),
(100633, NULL, 'Condom (Endurance)', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:28', '2025-01-21 16:48:28', NULL, NULL, NULL),
(100634, NULL, 'Condom (Erotica)', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:28', '2025-01-21 16:48:28', NULL, NULL, NULL),
(100635, NULL, 'Condom (Extreme)', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:28', '2025-01-21 16:48:28', NULL, NULL, NULL),
(100636, NULL, 'Condom (Familia)', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:28', '2025-01-21 16:48:28', NULL, NULL, NULL),
(100637, NULL, 'Condom (Fiesta)', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:28', '2025-01-21 16:48:28', NULL, NULL, NULL),
(100638, NULL, 'Condom (Flavour)', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:28', '2025-01-21 16:48:28', NULL, NULL, NULL),
(100639, NULL, 'Condom (Hot)', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:28', '2025-01-21 16:48:28', NULL, NULL, NULL),
(100640, NULL, 'Condom (Kamasutra)', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:28', '2025-01-21 16:48:28', NULL, NULL, NULL),
(100641, NULL, 'Condom (Kingsize)', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:28', '2025-01-21 16:48:28', NULL, NULL, NULL),
(100642, NULL, 'Condom (Kiss)', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:28', '2025-01-21 16:48:28', NULL, NULL, NULL),
(100643, NULL, 'Condom (Lifeguard)', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:28', '2025-01-21 16:48:28', NULL, NULL, NULL),
(100644, NULL, 'Condom (Mfalme)', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:28', '2025-01-21 16:48:28', NULL, NULL, NULL),
(100645, NULL, 'Condom (Original)', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:28', '2025-01-21 16:48:28', NULL, NULL, NULL),
(100646, NULL, 'Condom (Power Play)', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:28', '2025-01-21 16:48:28', NULL, NULL, NULL),
(100647, NULL, 'Condom (Rough Rider)', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:28', '2025-01-21 16:48:28', NULL, NULL, NULL),
(100648, NULL, 'Condom (Salama)', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:28', '2025-01-21 16:48:28', NULL, NULL, NULL),
(100649, NULL, 'Condom (Strawberry)', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:28', '2025-01-21 16:48:28', NULL, NULL, NULL),
(100650, NULL, 'Condom (Trust)', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:28', '2025-01-21 16:48:28', NULL, NULL, NULL),
(100651, NULL, 'Condom (Ultra Thin)', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:29', '2025-01-21 16:48:29', NULL, NULL, NULL),
(100652, NULL, 'Condom (WetnWild)', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:29', '2025-01-21 16:48:29', NULL, NULL, NULL),
(100653, NULL, 'Confidence Pads', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:29', '2025-01-21 16:48:29', NULL, NULL, NULL),
(100654, NULL, 'Contoured Lumber Corset with Strap (L)', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:29', '2025-01-21 16:48:29', NULL, NULL, NULL),
(100655, NULL, 'Contoured Lumber Corset with Strap (M)', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:29', '2025-01-21 16:48:29', NULL, NULL, NULL),
(100656, NULL, 'Contoured Lumber Corset with Strap (XL)', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:29', '2025-01-21 16:48:29', NULL, NULL, NULL),
(100657, NULL, 'Contractubex Cream', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:29', '2025-01-21 16:48:29', NULL, NULL, NULL),
(100658, NULL, 'Contratubex Gel', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:29', '2025-01-21 16:48:29', NULL, NULL, NULL),
(100659, NULL, 'Cophydex Syrup 100ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:29', '2025-01-21 16:48:29', NULL, NULL, NULL),
(100660, NULL, 'Corecid Susp 200ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:29', '2025-01-21 16:48:29', NULL, NULL, NULL),
(100661, NULL, 'Co-Trimoxazole Inj (Septrin)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:29', '2025-01-21 16:48:29', NULL, NULL, NULL),
(100662, NULL, 'Co-Trimoxazole Susp (Septrin)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:29', '2025-01-21 16:48:29', NULL, NULL, NULL),
(100663, NULL, 'Co-Trimoxazole Syrup (Septrin) - GSK', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:29', '2025-01-21 16:48:29', NULL, NULL, NULL),
(100664, NULL, 'Co-Trimoxazole Tabs (Septrin)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:29', '2025-01-21 16:48:29', NULL, NULL, NULL),
(100665, NULL, 'Co-Trimoxazole Tabs (Septrin) - GSK', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:29', '2025-01-21 16:48:29', NULL, NULL, NULL),
(100666, NULL, 'Cotton Buds 100', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:29', '2025-01-21 16:48:29', NULL, NULL, NULL),
(100667, NULL, 'Cotton Buds 200', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:29', '2025-01-21 16:48:29', NULL, NULL, NULL),
(100668, NULL, 'Cotton Wool 100g', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:29', '2025-01-21 16:48:29', NULL, NULL, NULL),
(100669, NULL, 'Cotton Wool 200g', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:29', '2025-01-21 16:48:29', NULL, NULL, NULL),
(100670, NULL, 'Cotton Wool 25g', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:29', '2025-01-21 16:48:29', NULL, NULL, NULL),
(100671, NULL, 'Cotton Wool 400g', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:29', '2025-01-21 16:48:29', NULL, NULL, NULL),
(100672, NULL, 'Cotton Wool 500g', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:29', '2025-01-21 16:48:29', NULL, NULL, NULL),
(100673, NULL, 'Cotton Wool 50g', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:29', '2025-01-21 16:48:29', NULL, NULL, NULL),
(100674, NULL, 'Covidol Soln 500ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:29', '2025-01-21 16:48:29', NULL, NULL, NULL),
(100675, NULL, 'Covinil Soln 500ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:29', '2025-01-21 16:48:29', NULL, NULL, NULL),
(100676, NULL, 'Crepe Bandage 10cm', NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:29', '2025-01-21 16:48:29', NULL, NULL, NULL),
(100677, NULL, 'Crepe Bandage 15cm', NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:29', '2025-01-21 16:48:29', NULL, NULL, NULL),
(100678, NULL, 'Crepe Bandage 5cm', NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:29', '2025-01-21 16:48:29', NULL, NULL, NULL),
(100679, NULL, 'Crepe Bandage 7.5cm', NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:29', '2025-01-21 16:48:29', NULL, NULL, NULL),
(100680, NULL, 'Crez Cocoa Lotion 275ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:29', '2025-01-21 16:48:29', NULL, NULL, NULL),
(100681, NULL, 'Crez Cocoa Lotion 500ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:29', '2025-01-21 16:48:29', NULL, NULL, NULL),
(100682, NULL, 'Crez Cocoa Lotion125ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:29', '2025-01-21 16:48:29', NULL, NULL, NULL),
(100683, NULL, 'Cudo Forte Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:29', '2025-01-21 16:48:29', NULL, NULL, NULL),
(100684, NULL, 'Cupid Tabs 50mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:29', '2025-01-21 16:48:29', NULL, NULL, NULL),
(100685, NULL, 'Curam Syrup 156mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:29', '2025-01-21 16:48:29', NULL, NULL, NULL),
(100686, NULL, 'Curam Syrup 228mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:29', '2025-01-21 16:48:29', NULL, NULL, NULL),
(100687, NULL, 'Curam Tabs 375mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:29', '2025-01-21 16:48:29', NULL, NULL, NULL),
(100688, NULL, 'Curam Tabs 625mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:29', '2025-01-21 16:48:29', NULL, NULL, NULL),
(100689, NULL, 'Cyclopam Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:29', '2025-01-21 16:48:29', NULL, NULL, NULL),
(100690, NULL, 'Cyproheptadine Syrup 100ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:29', '2025-01-21 16:48:29', NULL, NULL, NULL),
(100691, NULL, 'Dabur Herbal Toothpaste', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:29', '2025-01-21 16:48:29', NULL, NULL, NULL),
(100692, NULL, 'Dabur Medicated Soap', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:29', '2025-01-21 16:48:29', NULL, NULL, NULL),
(100693, NULL, 'Daflon Tabs 500mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:29', '2025-01-21 16:48:29', NULL, NULL, NULL),
(100694, NULL, 'Dafraclav Syrup 228mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:29', '2025-01-21 16:48:29', NULL, NULL, NULL),
(100695, NULL, 'Dafraclav Tabs 375mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:29', '2025-01-21 16:48:29', NULL, NULL, NULL),
(100696, NULL, 'Dafraclav Tabs 625mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:29', '2025-01-21 16:48:29', NULL, NULL, NULL),
(100697, NULL, 'Daily Amino Acid Caps (21st Century)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:29', '2025-01-21 16:48:29', NULL, NULL, NULL),
(100698, NULL, 'Daktacort Cream 15g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:29', '2025-01-21 16:48:29', NULL, NULL, NULL),
(100699, NULL, 'Daktarin Cream 20g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:29', '2025-01-21 16:48:29', NULL, NULL, NULL),
(100700, NULL, 'Daktarin Oral Gel 40g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:29', '2025-01-21 16:48:29', NULL, NULL, NULL),
(100701, NULL, 'Dapagliflozin Tabs 10mg (Dapzin)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:29', '2025-01-21 16:48:29', NULL, NULL, NULL),
(100702, NULL, 'Dapoxetine Tabs 60mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:29', '2025-01-21 16:48:29', NULL, NULL, NULL),
(100703, NULL, 'Dark Spots Soap', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:29', '2025-01-21 16:48:29', NULL, NULL, NULL),
(100704, NULL, 'D-Artepp Tabs 20/160mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:29', '2025-01-21 16:48:29', NULL, NULL, NULL),
(100705, NULL, 'D-Artepp Tabs 30/240mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:29', '2025-01-21 16:48:29', NULL, NULL, NULL),
(100706, NULL, 'D-Artepp Tabs 40/320mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:29', '2025-01-21 16:48:29', NULL, NULL, NULL),
(100707, NULL, 'D-Artepp Tabs 80/640mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:29', '2025-01-21 16:48:29', NULL, NULL, NULL),
(100708, NULL, 'Dawa Tatu Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:29', '2025-01-21 16:48:29', NULL, NULL, NULL),
(100709, NULL, 'Dawa ya Mba', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:29', '2025-01-21 16:48:29', NULL, NULL, NULL),
(100710, NULL, 'Dawacof Syrup', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:29', '2025-01-21 16:48:29', NULL, NULL, NULL),
(100711, NULL, 'Dazit Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:29', '2025-01-21 16:48:29', NULL, NULL, NULL),
(100712, NULL, 'Debridace Oint 15g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:29', '2025-01-21 16:48:29', NULL, NULL, NULL),
(100713, NULL, 'Deep Freeze Cold Spray 150ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:29', '2025-01-21 16:48:29', NULL, NULL, NULL),
(100714, NULL, 'Deep Freeze Gel-35g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:29', '2025-01-21 16:48:29', NULL, NULL, NULL),
(100715, NULL, 'Deep Heat Rub 15g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:29', '2025-01-21 16:48:29', NULL, NULL, NULL),
(100716, NULL, 'Deep Heat Rub 35g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:29', '2025-01-21 16:48:29', NULL, NULL, NULL),
(100717, NULL, 'Deep Heat Spray 15g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:29', '2025-01-21 16:48:29', NULL, NULL, NULL),
(100718, NULL, 'Deep Relief Gel 50g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:29', '2025-01-21 16:48:29', NULL, NULL, NULL),
(100719, NULL, 'Deep Wash (OXY)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:29', '2025-01-21 16:48:29', NULL, NULL, NULL),
(100720, NULL, 'Delased Chest Cough Syrup 100ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:30', '2025-01-21 16:48:30', NULL, NULL, NULL),
(100721, NULL, 'Delased Dry Cough Syrup 100ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:30', '2025-01-21 16:48:30', NULL, NULL, NULL),
(100722, NULL, 'Delased Syrup (Pediatric)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:30', '2025-01-21 16:48:30', NULL, NULL, NULL),
(100723, NULL, 'Dentamol Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:30', '2025-01-21 16:48:30', NULL, NULL, NULL),
(100724, NULL, 'Depo Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:30', '2025-01-21 16:48:30', NULL, NULL, NULL),
(100725, NULL, 'Derma Plus Cream 15g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:30', '2025-01-21 16:48:30', NULL, NULL, NULL),
(100726, NULL, 'Dermacin Cream 10g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:30', '2025-01-21 16:48:30', NULL, NULL, NULL),
(100727, NULL, 'Dermaquat Cream', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:30', '2025-01-21 16:48:30', NULL, NULL, NULL),
(100728, NULL, 'Dermidex Cream', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:30', '2025-01-21 16:48:30', NULL, NULL, NULL),
(100729, NULL, 'Dermofix Cream 20g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:30', '2025-01-21 16:48:30', NULL, NULL, NULL),
(100730, NULL, 'Dermogard Cream 20g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:30', '2025-01-21 16:48:30', NULL, NULL, NULL),
(100731, NULL, 'Dermovate Cream 25g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:30', '2025-01-21 16:48:30', NULL, NULL, NULL),
(100732, NULL, 'Dermovate Oint 25g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:30', '2025-01-21 16:48:30', NULL, NULL, NULL),
(100733, NULL, 'Desloratadine Syrup (Actilor)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:30', '2025-01-21 16:48:30', NULL, NULL, NULL),
(100734, NULL, 'Desloratadine Syrup (Alrinast)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:30', '2025-01-21 16:48:30', NULL, NULL, NULL),
(100735, NULL, 'Desloratadine Syrup (Lorias)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:30', '2025-01-21 16:48:30', NULL, NULL, NULL),
(100736, NULL, 'Desloratadine Tabs (Neolor)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:30', '2025-01-21 16:48:30', NULL, NULL, NULL),
(100737, NULL, 'Desloratadine Tabs 5mg (Alrinast)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:30', '2025-01-21 16:48:30', NULL, NULL, NULL),
(100738, NULL, 'Desloratadine Tabs 5mg (Denk)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:30', '2025-01-21 16:48:30', NULL, NULL, NULL),
(100739, NULL, 'Desloratadine Tabs 5mg (Deslora)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:30', '2025-01-21 16:48:30', NULL, NULL, NULL),
(100740, NULL, 'Desloratadine Tabs 5mg (Glendes)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:30', '2025-01-21 16:48:30', NULL, NULL, NULL),
(100741, NULL, 'Desloratadine Tabs 5mg (Lorias)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:30', '2025-01-21 16:48:30', NULL, NULL, NULL),
(100742, NULL, 'Dettol  Soap 100g', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:30', '2025-01-21 16:48:30', NULL, NULL, NULL),
(100743, NULL, 'Dettol Junior Soap 100g', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:30', '2025-01-21 16:48:30', NULL, NULL, NULL),
(100744, NULL, 'Dettol Shower Cream', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:30', '2025-01-21 16:48:30', NULL, NULL, NULL),
(100745, NULL, 'Dettol Soap 150g', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:30', '2025-01-21 16:48:30', NULL, NULL, NULL),
(100746, NULL, 'Dettol Soln 125ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:30', '2025-01-21 16:48:30', NULL, NULL, NULL),
(100747, NULL, 'Dettol Soln 1Ltr', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:30', '2025-01-21 16:48:30', NULL, NULL, NULL),
(100748, NULL, 'Dettol Soln 250ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:30', '2025-01-21 16:48:30', NULL, NULL, NULL),
(100749, NULL, 'Dettol Soln 500ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:30', '2025-01-21 16:48:30', NULL, NULL, NULL),
(100750, NULL, 'Dettol Soln 5Ltrs', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:30', '2025-01-21 16:48:30', NULL, NULL, NULL),
(100751, NULL, 'Dettol Soln 60ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:30', '2025-01-21 16:48:30', NULL, NULL, NULL),
(100752, NULL, 'Dexa-Chloro Eye Drops', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:30', '2025-01-21 16:48:30', NULL, NULL, NULL),
(100753, NULL, 'Dexa-Genta Eye Drops', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:30', '2025-01-21 16:48:30', NULL, NULL, NULL),
(100754, NULL, 'Dexamethasone Eye/Ear Drops', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:30', '2025-01-21 16:48:30', NULL, NULL, NULL),
(100755, NULL, 'Dexamethasone Inj', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:30', '2025-01-21 16:48:30', NULL, NULL, NULL),
(100756, NULL, 'Dexamethasone Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:30', '2025-01-21 16:48:30', NULL, NULL, NULL),
(100757, NULL, 'Dexa-Neo Eye/Ear Drops', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:30', '2025-01-21 16:48:30', NULL, NULL, NULL),
(100758, NULL, 'Dexaquine Cream 15g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:30', '2025-01-21 16:48:30', NULL, NULL, NULL),
(100759, NULL, 'Dextros 10% Drip (D10)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:30', '2025-01-21 16:48:30', NULL, NULL, NULL),
(100760, NULL, 'Dextros 5% Drip (D5)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:30', '2025-01-21 16:48:30', NULL, NULL, NULL),
(100761, NULL, 'Dextros 50% Drip (D50)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:30', '2025-01-21 16:48:30', NULL, NULL, NULL),
(100762, NULL, 'Dextros Normal Saline Drip (DNS)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:30', '2025-01-21 16:48:30', NULL, NULL, NULL),
(100763, NULL, 'Diabetic Formula Caps (21st Century)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:30', '2025-01-21 16:48:30', NULL, NULL, NULL),
(100764, NULL, 'Diabetone Caps', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:30', '2025-01-21 16:48:30', NULL, NULL, NULL),
(100765, NULL, 'Diazepam Inj (Valium)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:30', '2025-01-21 16:48:30', NULL, NULL, NULL),
(100766, NULL, 'Diazepam Tabs (Valium)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:30', '2025-01-21 16:48:30', NULL, NULL, NULL),
(100767, NULL, 'Diclofenac Gel 20g (Dicloran)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:30', '2025-01-21 16:48:30', NULL, NULL, NULL),
(100768, NULL, 'Diclofenac Gel 20g (Rheumac)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:30', '2025-01-21 16:48:30', NULL, NULL, NULL),
(100769, NULL, 'Diclofenac Gel 30g (Diclopham)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:30', '2025-01-21 16:48:30', NULL, NULL, NULL),
(100770, NULL, 'Diclofenac Gel 30g (Dicloran)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:30', '2025-01-21 16:48:30', NULL, NULL, NULL),
(100771, NULL, 'Diclofenac Gel 30g (Dinac)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:30', '2025-01-21 16:48:30', NULL, NULL, NULL),
(100772, NULL, 'Diclofenac Gel 30g (Vivian)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:30', '2025-01-21 16:48:30', NULL, NULL, NULL),
(100773, NULL, 'Diclofenac Inj 25mg/3ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:30', '2025-01-21 16:48:30', NULL, NULL, NULL),
(100774, NULL, 'Diclofenac Plus Tab (Vivian)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:30', '2025-01-21 16:48:30', NULL, NULL, NULL),
(100775, NULL, 'Diclofenac Tabs (Remethan)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:30', '2025-01-21 16:48:30', NULL, NULL, NULL),
(100776, NULL, 'Diclofenac Tabs (Vivian)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:30', '2025-01-21 16:48:30', NULL, NULL, NULL),
(100777, NULL, 'Diclokant Gel 20g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:30', '2025-01-21 16:48:30', NULL, NULL, NULL),
(100778, NULL, 'Diclokant HC Gel 30g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:30', '2025-01-21 16:48:30', NULL, NULL, NULL),
(100779, NULL, 'Diclomove Gel 30g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:30', '2025-01-21 16:48:30', NULL, NULL, NULL),
(100780, NULL, 'Diclopar Activa Gel', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:30', '2025-01-21 16:48:30', NULL, NULL, NULL),
(100781, NULL, 'Diclopar MR Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:30', '2025-01-21 16:48:30', NULL, NULL, NULL),
(100782, NULL, 'Diclopar Tabs (Shelys)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:31', '2025-01-21 16:48:31', NULL, NULL, NULL),
(100783, NULL, 'Digital Thermometer', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:31', '2025-01-21 16:48:31', NULL, NULL, NULL),
(100784, NULL, 'Digoxin Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:31', '2025-01-21 16:48:31', NULL, NULL, NULL),
(100785, NULL, 'Diltiazem Caps 60mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:31', '2025-01-21 16:48:31', NULL, NULL, NULL),
(100786, NULL, 'Dinoprostone V Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:31', '2025-01-21 16:48:31', NULL, NULL, NULL),
(100787, NULL, 'Diprofos Inj (Schering) 2ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:31', '2025-01-21 16:48:31', NULL, NULL, NULL),
(100788, NULL, 'Diprosalic Oint 10g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:31', '2025-01-21 16:48:31', NULL, NULL, NULL),
(100789, NULL, 'Diprosalic Ointt 30g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:31', '2025-01-21 16:48:31', NULL, NULL, NULL),
(100790, NULL, 'Disposable Breast Pads', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:31', '2025-01-21 16:48:31', NULL, NULL, NULL),
(100791, NULL, 'Distilled Water 1Ltrs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:31', '2025-01-21 16:48:31', NULL, NULL, NULL),
(100792, NULL, 'Distilled Water 5Ltrs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:31', '2025-01-21 16:48:31', NULL, NULL, NULL),
(100793, NULL, 'Docetaxel Inj 120mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:31', '2025-01-21 16:48:31', NULL, NULL, NULL),
(100794, NULL, 'Domperidone Syrup 30ml (Motinorm)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:31', '2025-01-21 16:48:31', NULL, NULL, NULL),
(100795, NULL, 'Domperidone Syrup100ml (Motilium)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:31', '2025-01-21 16:48:31', NULL, NULL, NULL),
(100796, NULL, 'Domperidone Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:31', '2025-01-21 16:48:31', NULL, NULL, NULL),
(100797, NULL, 'Dove Body Cream', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:31', '2025-01-21 16:48:31', NULL, NULL, NULL),
(100798, NULL, 'Dove Lotion', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:31', '2025-01-21 16:48:31', NULL, NULL, NULL),
(100799, NULL, 'Dove Shampoo', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:31', '2025-01-21 16:48:31', NULL, NULL, NULL),
(100800, NULL, 'Dove Shower Cream', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:31', '2025-01-21 16:48:31', NULL, NULL, NULL),
(100801, NULL, 'Dove Shower Jelly', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:31', '2025-01-21 16:48:31', NULL, NULL, NULL),
(100802, NULL, 'Dove Soap', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:31', '2025-01-21 16:48:31', NULL, NULL, NULL),
(100803, NULL, 'Doxycycline Caps 100mg (Cyprus)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:31', '2025-01-21 16:48:31', NULL, NULL, NULL),
(100804, NULL, 'Doxycycline Caps 100mg (India)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:31', '2025-01-21 16:48:31', NULL, NULL, NULL),
(100805, NULL, 'Dr. Cold Syrup', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:31', '2025-01-21 16:48:31', NULL, NULL, NULL),
(100806, NULL, 'Dr. Cold Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:31', '2025-01-21 16:48:31', NULL, NULL, NULL),
(100807, NULL, 'Duocetz Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:31', '2025-01-21 16:48:31', NULL, NULL, NULL),
(100808, NULL, 'Duo-Cotecxin Tabs (Adult)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:31', '2025-01-21 16:48:31', NULL, NULL, NULL),
(100809, NULL, 'Duo-Cotecxin Tabs (Pediatric)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:31', '2025-01-21 16:48:31', NULL, NULL, NULL),
(100810, NULL, 'Duoquin Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:31', '2025-01-21 16:48:31', NULL, NULL, NULL),
(100811, NULL, 'Duphalac Liquid 200ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:31', '2025-01-21 16:48:31', NULL, NULL, NULL),
(100812, NULL, 'Duphaston Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:31', '2025-01-21 16:48:31', NULL, NULL, NULL),
(100813, NULL, 'Duspatalin Tabs 135mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:31', '2025-01-21 16:48:31', NULL, NULL, NULL),
(100814, NULL, 'Dutasteride Caps  0.5mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:31', '2025-01-21 16:48:31', NULL, NULL, NULL),
(100815, NULL, 'East Touch Machine (Glucose)', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:31', '2025-01-21 16:48:31', NULL, NULL, NULL),
(100816, NULL, 'Ecodex G Cream', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:31', '2025-01-21 16:48:31', NULL, NULL, NULL),
(100817, NULL, 'Econazole  Cream 10g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:31', '2025-01-21 16:48:31', NULL, NULL, NULL),
(100818, NULL, 'Econazole  Pessaries', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:31', '2025-01-21 16:48:31', NULL, NULL, NULL),
(100819, NULL, 'Ekelfin Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:31', '2025-01-21 16:48:31', NULL, NULL, NULL),
(100820, NULL, 'Electric Breast Pump', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:31', '2025-01-21 16:48:31', NULL, NULL, NULL),
(100821, NULL, 'Elocom Cream 15g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:31', '2025-01-21 16:48:31', NULL, NULL, NULL),
(100822, NULL, 'Elocom Lotion', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:31', '2025-01-21 16:48:31', NULL, NULL, NULL),
(100823, NULL, 'Elocom Oint 15g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:31', '2025-01-21 16:48:31', NULL, NULL, NULL),
(100824, NULL, 'Elyclob-G Cream 15g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:31', '2025-01-21 16:48:31', NULL, NULL, NULL),
(100825, NULL, 'Elycort Cream 15g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:31', '2025-01-21 16:48:31', NULL, NULL, NULL),
(100826, NULL, 'Elycort Oint', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:31', '2025-01-21 16:48:31', NULL, NULL, NULL),
(100827, NULL, 'Elydac Syrup', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:31', '2025-01-21 16:48:31', NULL, NULL, NULL),
(100828, NULL, 'Elyvate Cream 15g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:31', '2025-01-21 16:48:31', NULL, NULL, NULL),
(100829, NULL, 'Elyvate Oint 15g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:31', '2025-01-21 16:48:31', NULL, NULL, NULL),
(100830, NULL, 'Emami Menthoplus Balm 30ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:31', '2025-01-21 16:48:31', NULL, NULL, NULL),
(100831, NULL, 'Emami Menthoplus Balm 9ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:31', '2025-01-21 16:48:31', NULL, NULL, NULL),
(100832, NULL, 'Emdelyn Syrup (Adult)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:31', '2025-01-21 16:48:31', NULL, NULL, NULL),
(100833, NULL, 'Emdelyn Syrup (Pediatric)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:31', '2025-01-21 16:48:31', NULL, NULL, NULL),
(100834, NULL, 'Emdewax Ear Drops 15ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:31', '2025-01-21 16:48:31', NULL, NULL, NULL),
(100835, NULL, 'Enalapril Tabs 10mg (Envas)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:31', '2025-01-21 16:48:31', NULL, NULL, NULL),
(100836, NULL, 'Enalapril Tabs 5mg (Envas)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:31', '2025-01-21 16:48:31', NULL, NULL, NULL),
(100837, NULL, 'Enalapril Tabs 5mg (Korandil)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:31', '2025-01-21 16:48:31', NULL, NULL, NULL),
(100838, NULL, 'Enat Caps 400 (Vit E)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:31', '2025-01-21 16:48:31', NULL, NULL, NULL),
(100839, NULL, 'Enat Cream 50g (Vit E)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:31', '2025-01-21 16:48:31', NULL, NULL, NULL),
(100840, NULL, 'Enemax Syrup (Enema)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:31', '2025-01-21 16:48:31', NULL, NULL, NULL),
(100841, NULL, 'ENO Sachets', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:31', '2025-01-21 16:48:31', NULL, NULL, NULL),
(100842, NULL, 'ENO Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:31', '2025-01-21 16:48:31', NULL, NULL, NULL),
(100843, NULL, 'Enoxaparine Inj 0.4ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:31', '2025-01-21 16:48:31', NULL, NULL, NULL),
(100844, NULL, 'Enoxaparine Inj 0.8ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:31', '2025-01-21 16:48:31', NULL, NULL, NULL),
(100845, NULL, 'Entezema Oint 30g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:31', '2025-01-21 16:48:31', NULL, NULL, NULL),
(100846, NULL, 'Ephedrin Inj 30mg/1ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:31', '2025-01-21 16:48:31', NULL, NULL, NULL),
(100847, NULL, 'Ephedrin Nasal Drops (Adult)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:31', '2025-01-21 16:48:31', NULL, NULL, NULL),
(100848, NULL, 'Ephedrin Nasal Drops (Pediatric)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:31', '2025-01-21 16:48:31', NULL, NULL, NULL),
(100849, NULL, 'Ephedrin Tabs (Gifol)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:31', '2025-01-21 16:48:31', NULL, NULL, NULL),
(100850, NULL, 'Epi Max Cream 400g', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:32', '2025-01-21 16:48:32', NULL, NULL, NULL),
(100851, NULL, 'Epi Max Cream100g', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:32', '2025-01-21 16:48:32', NULL, NULL, NULL),
(100852, NULL, 'Epi Max Cream125g', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:32', '2025-01-21 16:48:32', NULL, NULL, NULL),
(100853, NULL, 'Epi Max Juniour & Baby 400g', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:32', '2025-01-21 16:48:32', NULL, NULL, NULL),
(100854, NULL, 'Epi Max Plus Cream 400g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:32', '2025-01-21 16:48:32', NULL, NULL, NULL),
(100855, NULL, 'Epilim Tabs 200mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:32', '2025-01-21 16:48:32', NULL, NULL, NULL),
(100856, NULL, 'Epilim Tabs 500mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:32', '2025-01-21 16:48:32', NULL, NULL, NULL),
(100857, NULL, 'Ergometrin Inj', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:32', '2025-01-21 16:48:32', NULL, NULL, NULL),
(100858, NULL, 'Erythromycin Syrup (Cyprus)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:32', '2025-01-21 16:48:32', NULL, NULL, NULL),
(100859, NULL, 'Erythromycin Syrup (India)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:32', '2025-01-21 16:48:32', NULL, NULL, NULL),
(100860, NULL, 'Erythromycin Tabs (Cyprus)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:32', '2025-01-21 16:48:32', NULL, NULL, NULL),
(100861, NULL, 'Erythromycin Tabs (India)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:32', '2025-01-21 16:48:32', NULL, NULL, NULL),
(100862, NULL, 'Esaderma Gell 30g (Lemon)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:32', '2025-01-21 16:48:32', NULL, NULL, NULL),
(100863, NULL, 'Esaderma Gell 30g (Orange)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:32', '2025-01-21 16:48:32', NULL, NULL, NULL),
(100864, NULL, 'Esomeprazole Tabs 20mg (Esomac)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:32', '2025-01-21 16:48:32', NULL, NULL, NULL),
(100865, NULL, 'Esomeprazole Tabs 20mg (Esoz)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:32', '2025-01-21 16:48:32', NULL, NULL, NULL),
(100866, NULL, 'Esomeprazole Tabs 40mg (Esoz)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:32', '2025-01-21 16:48:32', NULL, NULL, NULL),
(100867, NULL, 'Etamsylate Tabs 500g (Sylate)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:32', '2025-01-21 16:48:32', NULL, NULL, NULL),
(100868, NULL, 'Eusol Solution 100ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:32', '2025-01-21 16:48:32', NULL, NULL, NULL),
(100869, NULL, 'Eusol Solution 1Litre', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:32', '2025-01-21 16:48:32', NULL, NULL, NULL),
(100870, NULL, 'Eve Tabs 500mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:32', '2025-01-21 16:48:32', NULL, NULL, NULL),
(100871, NULL, 'Evening Primerose Oil Caps (21st Century)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:32', '2025-01-21 16:48:32', NULL, NULL, NULL),
(100872, NULL, 'Examination Gloves', NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:32', '2025-01-21 16:48:32', NULL, NULL, NULL);
INSERT INTO `inv_products` (`id`, `barcode`, `name`, `generic_name`, `category_id`, `sub_category_id`, `type`, `standard_uom`, `sales_uom`, `purchase_uom`, `indication`, `dosage`, `min_quantinty`, `max_quantinty`, `status`, `created_at`, `updated_at`, `npk_ratio`, `brand`, `pack_size`) VALUES
(100873, NULL, 'Eye Support Caps (21st Century)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:32', '2025-01-21 16:48:32', NULL, NULL, NULL),
(100874, NULL, 'Fa Body Spray', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:32', '2025-01-21 16:48:32', NULL, NULL, NULL),
(100875, NULL, 'Fa Deodorant', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:32', '2025-01-21 16:48:32', NULL, NULL, NULL),
(100876, NULL, 'Fa Shower Gel', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:32', '2025-01-21 16:48:32', NULL, NULL, NULL),
(100877, NULL, 'Face Mask', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:32', '2025-01-21 16:48:32', NULL, NULL, NULL),
(100878, NULL, 'Familia Inj', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:32', '2025-01-21 16:48:32', NULL, NULL, NULL),
(100879, NULL, 'Familia Pills', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:32', '2025-01-21 16:48:32', NULL, NULL, NULL),
(100880, NULL, 'Family Care Mosquito Repellant Lotion', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:32', '2025-01-21 16:48:32', NULL, NULL, NULL),
(100881, NULL, 'Fansidar Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:32', '2025-01-21 16:48:32', NULL, NULL, NULL),
(100882, NULL, 'Fat Burner Caps (21st Century)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:32', '2025-01-21 16:48:32', NULL, NULL, NULL),
(100883, NULL, 'Febrex TM Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:32', '2025-01-21 16:48:32', NULL, NULL, NULL),
(100884, NULL, 'Febuxostat Tabs 40mg (Febuday)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:32', '2025-01-21 16:48:32', NULL, NULL, NULL),
(100885, NULL, 'Fefol Caps', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:32', '2025-01-21 16:48:32', NULL, NULL, NULL),
(100886, NULL, 'Feloglobin Caps', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:32', '2025-01-21 16:48:32', NULL, NULL, NULL),
(100887, NULL, 'Femiclean Kit', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:32', '2025-01-21 16:48:32', NULL, NULL, NULL),
(100888, NULL, 'Feritone B Syrup', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:32', '2025-01-21 16:48:32', NULL, NULL, NULL),
(100889, NULL, 'Feroglobin Caps', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:32', '2025-01-21 16:48:32', NULL, NULL, NULL),
(100890, NULL, 'Feroglobin Syrup 200ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:32', '2025-01-21 16:48:32', NULL, NULL, NULL),
(100891, NULL, 'Ferro-B Complex 100ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:32', '2025-01-21 16:48:32', NULL, NULL, NULL),
(100892, NULL, 'Ferro-B Complex 60ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:32', '2025-01-21 16:48:32', NULL, NULL, NULL),
(100893, NULL, 'Ferrobin Caps', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:32', '2025-01-21 16:48:32', NULL, NULL, NULL),
(100894, NULL, 'Ferrotone Caps', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:32', '2025-01-21 16:48:32', NULL, NULL, NULL),
(100895, NULL, 'Ferrotone Liquid 120ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:32', '2025-01-21 16:48:32', NULL, NULL, NULL),
(100896, NULL, 'Ferrotone Liquid 180ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:32', '2025-01-21 16:48:32', NULL, NULL, NULL),
(100897, NULL, 'Ferrous Sulphate Tabs 200mg (Agofer)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:32', '2025-01-21 16:48:32', NULL, NULL, NULL),
(100898, NULL, 'Feverex Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:32', '2025-01-21 16:48:32', NULL, NULL, NULL),
(100899, NULL, 'Fexofenadine Tabs 120mg (Allegix)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:32', '2025-01-21 16:48:32', NULL, NULL, NULL),
(100900, NULL, 'Fexofenadine Tabs 120mg (Fexidine)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:32', '2025-01-21 16:48:32', NULL, NULL, NULL),
(100901, NULL, 'Fexofenadine Tabs 180mg (Allegix)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:32', '2025-01-21 16:48:32', NULL, NULL, NULL),
(100902, NULL, 'Finasteride Tabs 5mg (Finagen)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:32', '2025-01-21 16:48:32', NULL, NULL, NULL),
(100903, NULL, 'Finasteride Tabs 5mg (Fincar)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:32', '2025-01-21 16:48:32', NULL, NULL, NULL),
(100904, NULL, 'First Aid Kit (Large)', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:32', '2025-01-21 16:48:32', NULL, NULL, NULL),
(100905, NULL, 'First Aid Kit (Small)', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:32', '2025-01-21 16:48:32', NULL, NULL, NULL),
(100906, NULL, 'First Aid Plasta', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:32', '2025-01-21 16:48:32', NULL, NULL, NULL),
(100907, NULL, 'Fish Oil Caps (21st Century)', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:32', '2025-01-21 16:48:32', NULL, NULL, NULL),
(100908, NULL, 'Flamar MX Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:32', '2025-01-21 16:48:32', NULL, NULL, NULL),
(100909, NULL, 'Fleming Susp 228.5mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:32', '2025-01-21 16:48:32', NULL, NULL, NULL),
(100910, NULL, 'Fleming Tabs 375mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:32', '2025-01-21 16:48:32', NULL, NULL, NULL),
(100911, NULL, 'Fleming Tabs 625mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:32', '2025-01-21 16:48:32', NULL, NULL, NULL),
(100912, NULL, 'Flex P Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:33', '2025-01-21 16:48:33', NULL, NULL, NULL),
(100913, NULL, 'Flexsa 1500mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:33', '2025-01-21 16:48:33', NULL, NULL, NULL),
(100914, NULL, 'Flomist Nasal Spray', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:33', '2025-01-21 16:48:33', NULL, NULL, NULL),
(100915, NULL, 'Flowless Pads', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:33', '2025-01-21 16:48:33', NULL, NULL, NULL),
(100916, NULL, 'Flucamox Caps', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:33', '2025-01-21 16:48:33', NULL, NULL, NULL),
(100917, NULL, 'Flucamox Syrup', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:33', '2025-01-21 16:48:33', NULL, NULL, NULL),
(100918, NULL, 'Flucloxacilline Caps 250mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:33', '2025-01-21 16:48:33', NULL, NULL, NULL),
(100919, NULL, 'Flucloxacilline Caps 500mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:33', '2025-01-21 16:48:33', NULL, NULL, NULL),
(100920, NULL, 'Flucloxacilline Syrup', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:33', '2025-01-21 16:48:33', NULL, NULL, NULL),
(100921, NULL, 'Flucomol Caps', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:33', '2025-01-21 16:48:33', NULL, NULL, NULL),
(100922, NULL, 'Fluconazole inj', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:33', '2025-01-21 16:48:33', NULL, NULL, NULL),
(100923, NULL, 'Fluconazole Tabs 150mg (Dilapan)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:33', '2025-01-21 16:48:33', NULL, NULL, NULL),
(100924, NULL, 'Fluconazole Tabs 150mg (Flucan)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:33', '2025-01-21 16:48:33', NULL, NULL, NULL),
(100925, NULL, 'Fluconazole Tabs 150mg (Fluderm)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:33', '2025-01-21 16:48:33', NULL, NULL, NULL),
(100926, NULL, 'Fluconazole Tabs 150mg (Zocon)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:33', '2025-01-21 16:48:33', NULL, NULL, NULL),
(100927, NULL, 'Fluconazole Tabs 200mg (Flucozal)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:33', '2025-01-21 16:48:33', NULL, NULL, NULL),
(100928, NULL, 'Fluconazole Tabs 200mg (Flutrox)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:33', '2025-01-21 16:48:33', NULL, NULL, NULL),
(100929, NULL, 'Fluconazole Tabs 200mg (Zocon)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:33', '2025-01-21 16:48:33', NULL, NULL, NULL),
(100930, NULL, 'Flucor Day (Liquid Gell)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:33', '2025-01-21 16:48:33', NULL, NULL, NULL),
(100931, NULL, 'Fluphenazine Inj', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:33', '2025-01-21 16:48:33', NULL, NULL, NULL),
(100932, NULL, 'Fluticasone Nasal Spray 12ml (Emina)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:33', '2025-01-21 16:48:33', NULL, NULL, NULL),
(100933, NULL, 'Folic Acid Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:33', '2025-01-21 16:48:33', NULL, NULL, NULL),
(100934, NULL, 'Foot Mask', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:33', '2025-01-21 16:48:33', NULL, NULL, NULL),
(100935, NULL, 'Foracort Inhaler 200mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:33', '2025-01-21 16:48:33', NULL, NULL, NULL),
(100936, NULL, 'Forever Bright Tooth Gel', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:33', '2025-01-21 16:48:33', NULL, NULL, NULL),
(100937, NULL, 'Forever Deodorant', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:33', '2025-01-21 16:48:33', NULL, NULL, NULL),
(100938, NULL, 'Forever Lip Bum', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:33', '2025-01-21 16:48:33', NULL, NULL, NULL),
(100939, NULL, 'Fortified Procaine Penicillin Inj (PPF)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:33', '2025-01-21 16:48:33', NULL, NULL, NULL),
(100940, NULL, 'Free Joint Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:33', '2025-01-21 16:48:33', NULL, NULL, NULL),
(100941, NULL, 'Free Style Pads', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:33', '2025-01-21 16:48:33', NULL, NULL, NULL),
(100942, NULL, 'Fricks Menthol Lozenges', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:33', '2025-01-21 16:48:33', NULL, NULL, NULL),
(100943, NULL, 'Fucide Oint 15g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:33', '2025-01-21 16:48:33', NULL, NULL, NULL),
(100944, NULL, 'Funbact A Cream 30g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:33', '2025-01-21 16:48:33', NULL, NULL, NULL),
(100945, NULL, 'Fungifen Cream', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:33', '2025-01-21 16:48:33', NULL, NULL, NULL),
(100946, NULL, 'Fungifen V Cream', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:33', '2025-01-21 16:48:33', NULL, NULL, NULL),
(100947, NULL, 'Fungistin VP', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:33', '2025-01-21 16:48:33', NULL, NULL, NULL),
(100948, NULL, 'Furazolidone Susp (Furazol) 100ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:33', '2025-01-21 16:48:33', NULL, NULL, NULL),
(100949, NULL, 'Furazolidone Tabs (Furazol)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:33', '2025-01-21 16:48:33', NULL, NULL, NULL),
(100950, NULL, 'Furosemide Inj', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:33', '2025-01-21 16:48:33', NULL, NULL, NULL),
(100951, NULL, 'Furosemide Tabs 40mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:33', '2025-01-21 16:48:33', NULL, NULL, NULL),
(100952, NULL, 'Fusiderm Oint', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:33', '2025-01-21 16:48:33', NULL, NULL, NULL),
(100953, NULL, 'Fusidic Acid Cream 15g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:33', '2025-01-21 16:48:33', NULL, NULL, NULL),
(100954, NULL, 'Fusigen Oint 10g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:33', '2025-01-21 16:48:33', NULL, NULL, NULL),
(100955, NULL, 'Gabapentin Caps 300mg (Gaba)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:33', '2025-01-21 16:48:33', NULL, NULL, NULL),
(100956, NULL, 'Gabapentin Caps 300mg (Gabalin)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:33', '2025-01-21 16:48:33', NULL, NULL, NULL),
(100957, NULL, 'Garlico Caps 400mg (21st Century)', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:33', '2025-01-21 16:48:33', NULL, NULL, NULL),
(100958, NULL, 'Gauze Roll 90cm/100yds', NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:33', '2025-01-21 16:48:33', NULL, NULL, NULL),
(100959, NULL, 'Gauze Roll 90cm/25yds', NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:33', '2025-01-21 16:48:33', NULL, NULL, NULL),
(100960, NULL, 'Gauze Roll 90cm/50yds', NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:33', '2025-01-21 16:48:33', NULL, NULL, NULL),
(100961, NULL, 'Gauze Roll 90cm/5yds', NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:33', '2025-01-21 16:48:33', NULL, NULL, NULL),
(100962, NULL, 'Gaviscon Liquid 150ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:33', '2025-01-21 16:48:33', NULL, NULL, NULL),
(100963, NULL, 'Gaviscon Liquid 200ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:33', '2025-01-21 16:48:33', NULL, NULL, NULL),
(100964, NULL, 'Gel Mask', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:33', '2025-01-21 16:48:33', NULL, NULL, NULL),
(100965, NULL, 'Gentalene C Cream', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:33', '2025-01-21 16:48:33', NULL, NULL, NULL),
(100966, NULL, 'Gentamycin Cream', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:33', '2025-01-21 16:48:33', NULL, NULL, NULL),
(100967, NULL, 'Gentamycin Eye/Ear Drops', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:33', '2025-01-21 16:48:33', NULL, NULL, NULL),
(100968, NULL, 'Gentamycin Inj 80mg/2ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:33', '2025-01-21 16:48:33', NULL, NULL, NULL),
(100969, NULL, 'Gentamycin Sulphate Cream 10g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:33', '2025-01-21 16:48:33', NULL, NULL, NULL),
(100970, NULL, 'Gentian Violent (GV)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:33', '2025-01-21 16:48:33', NULL, NULL, NULL),
(100971, NULL, 'Gentriderm Cream 10g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:33', '2025-01-21 16:48:33', NULL, NULL, NULL),
(100972, NULL, 'Gentrisone Cream 10g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:33', '2025-01-21 16:48:33', NULL, NULL, NULL),
(100973, NULL, 'Geratherm Thermometers', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:33', '2025-01-21 16:48:33', NULL, NULL, NULL),
(100974, NULL, 'Gestid Tabs (Antacid)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:33', '2025-01-21 16:48:33', NULL, NULL, NULL),
(100975, NULL, 'Gillette After Shave', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:33', '2025-01-21 16:48:33', NULL, NULL, NULL),
(100976, NULL, 'Gillette Blue 2', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:33', '2025-01-21 16:48:33', NULL, NULL, NULL),
(100977, NULL, 'Gillette Blue 3 Presto', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:33', '2025-01-21 16:48:33', NULL, NULL, NULL),
(100978, NULL, 'Gillette Blue II Plus Razor 10s', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:33', '2025-01-21 16:48:33', NULL, NULL, NULL),
(100979, NULL, 'Gillette Blue II Razor 5s', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:34', '2025-01-21 16:48:34', NULL, NULL, NULL),
(100980, NULL, 'Gillette Foam Regular 196g', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:34', '2025-01-21 16:48:34', NULL, NULL, NULL),
(100981, NULL, 'Gillette For Women 2', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:34', '2025-01-21 16:48:34', NULL, NULL, NULL),
(100982, NULL, 'Gillette Fusion Catrage', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:34', '2025-01-21 16:48:34', NULL, NULL, NULL),
(100983, NULL, 'Gillette Fusion Machine', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:34', '2025-01-21 16:48:34', NULL, NULL, NULL),
(100984, NULL, 'Gillette Gel 200ml', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:34', '2025-01-21 16:48:34', NULL, NULL, NULL),
(100985, NULL, 'Gillette Mach 3 Blades', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:34', '2025-01-21 16:48:34', NULL, NULL, NULL),
(100986, NULL, 'Gillette Mach 3 Razor', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:34', '2025-01-21 16:48:34', NULL, NULL, NULL),
(100987, NULL, 'Gillette Mach 3 Shaver', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:34', '2025-01-21 16:48:34', NULL, NULL, NULL),
(100988, NULL, 'Gillette Razor 1s', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:34', '2025-01-21 16:48:34', NULL, NULL, NULL),
(100989, NULL, 'Gillette Razor 5s', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:34', '2025-01-21 16:48:34', NULL, NULL, NULL),
(100990, NULL, 'Gillette Shave Gel 195g', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:34', '2025-01-21 16:48:34', NULL, NULL, NULL),
(100991, NULL, 'Gillette Shave Gel 60g', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:34', '2025-01-21 16:48:34', NULL, NULL, NULL),
(100992, NULL, 'Gillette Shaving Cream 93g', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:34', '2025-01-21 16:48:34', NULL, NULL, NULL),
(100993, NULL, 'Gillette Shaving Foam 300ml', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:34', '2025-01-21 16:48:34', NULL, NULL, NULL),
(100994, NULL, 'Gimepiride 1 Tabs (Gemer)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:34', '2025-01-21 16:48:34', NULL, NULL, NULL),
(100995, NULL, 'Gimepiride 2 Tabs (Gemer)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:34', '2025-01-21 16:48:34', NULL, NULL, NULL),
(100996, NULL, 'Ginkgo Biloba Caps (21st Century)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:34', '2025-01-21 16:48:34', NULL, NULL, NULL),
(100997, NULL, 'Ginseng Extract Caps (21st Century)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:34', '2025-01-21 16:48:34', NULL, NULL, NULL),
(100998, NULL, 'Ginsomin Caps', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:34', '2025-01-21 16:48:34', NULL, NULL, NULL),
(100999, NULL, 'Glibenclamide Tabs (Betanase)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:34', '2025-01-21 16:48:34', NULL, NULL, NULL),
(101000, NULL, 'Glibenclamide Tabs (Diaben)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:34', '2025-01-21 16:48:34', NULL, NULL, NULL),
(101001, NULL, 'Glibenclamide Tabs (Diamide)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:34', '2025-01-21 16:48:34', NULL, NULL, NULL),
(101002, NULL, 'Glibenclamide Tabs 5mg (Diolin)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:34', '2025-01-21 16:48:34', NULL, NULL, NULL),
(101003, NULL, 'Glibenclamide Tabs 5mg (Glitisol)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:34', '2025-01-21 16:48:34', NULL, NULL, NULL),
(101004, NULL, 'Gliclazide Tabs 60mg  (Dianorm)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:34', '2025-01-21 16:48:34', NULL, NULL, NULL),
(101005, NULL, 'Gliclazide Tabs 80mg  (Dianorm)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:34', '2025-01-21 16:48:34', NULL, NULL, NULL),
(101006, NULL, 'Gliclazide Tabs M (Dianorm)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:34', '2025-01-21 16:48:34', NULL, NULL, NULL),
(101007, NULL, 'Glifil M Forte Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:34', '2025-01-21 16:48:34', NULL, NULL, NULL),
(101008, NULL, 'Glimepiride + Metformin Tabs (Glimmet 1)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:34', '2025-01-21 16:48:34', NULL, NULL, NULL),
(101009, NULL, 'Glimepiride + Metformin Tabs (Glimmet 2)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:34', '2025-01-21 16:48:34', NULL, NULL, NULL),
(101010, NULL, 'Glimepiride + Metformin Tabs (ILet B1)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:34', '2025-01-21 16:48:34', NULL, NULL, NULL),
(101011, NULL, 'Glimepiride + Metformin Tabs (ILet B2)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:34', '2025-01-21 16:48:34', NULL, NULL, NULL),
(101012, NULL, 'Glimepiride Tabs 1mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:34', '2025-01-21 16:48:34', NULL, NULL, NULL),
(101013, NULL, 'Glimepiride Tabs 2mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:34', '2025-01-21 16:48:34', NULL, NULL, NULL),
(101014, NULL, 'Globin-Z Syrup 200ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:34', '2025-01-21 16:48:34', NULL, NULL, NULL),
(101015, NULL, 'Glory Pads', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:34', '2025-01-21 16:48:34', NULL, NULL, NULL),
(101016, NULL, 'Glory Sanitary Napkin', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:34', '2025-01-21 16:48:34', NULL, NULL, NULL),
(101017, NULL, 'Gluconavii Machine', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:34', '2025-01-21 16:48:34', NULL, NULL, NULL),
(101018, NULL, 'Gluconavii Strips', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:34', '2025-01-21 16:48:34', NULL, NULL, NULL),
(101019, NULL, 'GlucoPlus Machine', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:34', '2025-01-21 16:48:34', NULL, NULL, NULL),
(101020, NULL, 'GlucoPlus Strips', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:34', '2025-01-21 16:48:34', NULL, NULL, NULL),
(101021, NULL, 'Glucored Forte', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:34', '2025-01-21 16:48:34', NULL, NULL, NULL),
(101022, NULL, 'Glucose Powder (Tin)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:34', '2025-01-21 16:48:34', NULL, NULL, NULL),
(101023, NULL, 'Glucose Powder 80g (Packet)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:34', '2025-01-21 16:48:34', NULL, NULL, NULL),
(101024, NULL, 'Go Man Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:34', '2025-01-21 16:48:34', NULL, NULL, NULL),
(101025, NULL, 'Go Woman Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:34', '2025-01-21 16:48:34', NULL, NULL, NULL),
(101026, NULL, 'Gofen Tabs 200mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:34', '2025-01-21 16:48:34', NULL, NULL, NULL),
(101027, NULL, 'Gofen Tabs 400mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:34', '2025-01-21 16:48:34', NULL, NULL, NULL),
(101028, NULL, 'Goodmorning Syrup', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:34', '2025-01-21 16:48:34', NULL, NULL, NULL),
(101029, NULL, 'Goodmorning Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:34', '2025-01-21 16:48:34', NULL, NULL, NULL),
(101030, NULL, 'Grace Zoa Zoa Manjano Soap', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:34', '2025-01-21 16:48:34', NULL, NULL, NULL),
(101031, NULL, 'Griseofulvin Tabs 500mg (Diofulvin)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:34', '2025-01-21 16:48:34', NULL, NULL, NULL),
(101032, NULL, 'Gromin Syrup 100ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:34', '2025-01-21 16:48:34', NULL, NULL, NULL),
(101033, NULL, 'Gromin Syrup 200ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:34', '2025-01-21 16:48:34', NULL, NULL, NULL),
(101034, NULL, 'Gynosporin Vaginal Cream 35g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:34', '2025-01-21 16:48:34', NULL, NULL, NULL),
(101035, NULL, 'Gynozol V3 400mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:34', '2025-01-21 16:48:34', NULL, NULL, NULL),
(101036, NULL, 'Haema Caps', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:34', '2025-01-21 16:48:34', NULL, NULL, NULL),
(101037, NULL, 'Haloperidol 1.5mg Tabs (Haloxen)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:34', '2025-01-21 16:48:34', NULL, NULL, NULL),
(101038, NULL, 'Hand Sanitizer 100ml', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:34', '2025-01-21 16:48:34', NULL, NULL, NULL),
(101039, NULL, 'Hand Sanitizer 250ml', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:34', '2025-01-21 16:48:34', NULL, NULL, NULL),
(101040, NULL, 'Hand Sanitizer 500ml', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:34', '2025-01-21 16:48:34', NULL, NULL, NULL),
(101041, NULL, 'Hand Sanitizer 50ml', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:34', '2025-01-21 16:48:34', NULL, NULL, NULL),
(101042, NULL, 'HC Maternity Pads', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:34', '2025-01-21 16:48:34', NULL, NULL, NULL),
(101043, NULL, 'HC Pads', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:34', '2025-01-21 16:48:34', NULL, NULL, NULL),
(101044, NULL, 'HC Pantliner', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:34', '2025-01-21 16:48:34', NULL, NULL, NULL),
(101045, NULL, 'Hedapan Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:35', '2025-01-21 16:48:35', NULL, NULL, NULL),
(101046, NULL, 'Hedex Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:35', '2025-01-21 16:48:35', NULL, NULL, NULL),
(101047, NULL, 'Heel Balm', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:35', '2025-01-21 16:48:35', NULL, NULL, NULL),
(101048, NULL, 'Heligo Kit Tabs (Intas)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:35', '2025-01-21 16:48:35', NULL, NULL, NULL),
(101049, NULL, 'Heligo Kit Tabs (LCT)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:35', '2025-01-21 16:48:35', NULL, NULL, NULL),
(101050, NULL, 'Hematone Syrup', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:35', '2025-01-21 16:48:35', NULL, NULL, NULL),
(101051, NULL, 'Hemovit Caps', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:35', '2025-01-21 16:48:35', NULL, NULL, NULL),
(101052, NULL, 'Hemovit Syrup 200ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:35', '2025-01-21 16:48:35', NULL, NULL, NULL),
(101053, NULL, 'Herbal Slim Tea Caps (21st Century)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:35', '2025-01-21 16:48:35', NULL, NULL, NULL),
(101054, NULL, 'Himalaya Baby Cream 50ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:35', '2025-01-21 16:48:35', NULL, NULL, NULL),
(101055, NULL, 'Himalaya Baby Gift Set', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:35', '2025-01-21 16:48:35', NULL, NULL, NULL),
(101056, NULL, 'Himalaya Baby Hair Oil 200ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:35', '2025-01-21 16:48:35', NULL, NULL, NULL),
(101057, NULL, 'Himalaya Baby Lotion 100ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:35', '2025-01-21 16:48:35', NULL, NULL, NULL),
(101058, NULL, 'Himalaya Baby Lotion 200ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:35', '2025-01-21 16:48:35', NULL, NULL, NULL),
(101059, NULL, 'Himalaya Baby Lotion 400ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:35', '2025-01-21 16:48:35', NULL, NULL, NULL),
(101060, NULL, 'Himalaya Baby Massage Oil 100ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:35', '2025-01-21 16:48:35', NULL, NULL, NULL),
(101061, NULL, 'Himalaya Baby Powder 100g', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:35', '2025-01-21 16:48:35', NULL, NULL, NULL),
(101062, NULL, 'Himalaya Baby Shampoo 100ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:35', '2025-01-21 16:48:35', NULL, NULL, NULL),
(101063, NULL, 'Himalaya Baby Shampoo 200ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:35', '2025-01-21 16:48:35', NULL, NULL, NULL),
(101064, NULL, 'Himalaya Baby Soap 125g', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:35', '2025-01-21 16:48:35', NULL, NULL, NULL),
(101065, NULL, 'Himalaya Face Wash', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:35', '2025-01-21 16:48:35', NULL, NULL, NULL),
(101066, NULL, 'Himalaya Hand Cream', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:35', '2025-01-21 16:48:35', NULL, NULL, NULL),
(101067, NULL, 'Hit Spray 400ml', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:35', '2025-01-21 16:48:35', NULL, NULL, NULL),
(101068, NULL, 'Hit Spray 750ml', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:35', '2025-01-21 16:48:35', NULL, NULL, NULL),
(101069, NULL, 'HIV Test Kit', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:35', '2025-01-21 16:48:35', NULL, NULL, NULL),
(101070, NULL, 'Hope Baby Wipes 80pcs', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:35', '2025-01-21 16:48:35', NULL, NULL, NULL),
(101071, NULL, 'HOPECA Flour', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:35', '2025-01-21 16:48:35', NULL, NULL, NULL),
(101072, NULL, 'Horny Goat Weed (Support)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:35', '2025-01-21 16:48:35', NULL, NULL, NULL),
(101073, NULL, 'Hot Water Bottle', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:35', '2025-01-21 16:48:35', NULL, NULL, NULL),
(101074, NULL, 'Huggies Dry Comfort 44s', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:35', '2025-01-21 16:48:35', NULL, NULL, NULL),
(101075, NULL, 'Huggies Dry Comfort 50s', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:35', '2025-01-21 16:48:35', NULL, NULL, NULL),
(101076, NULL, 'Huggies New Born No.1', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:35', '2025-01-21 16:48:35', NULL, NULL, NULL),
(101077, NULL, 'Huggies New Born No.2', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:35', '2025-01-21 16:48:35', NULL, NULL, NULL),
(101078, NULL, 'Huggies Pants', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:35', '2025-01-21 16:48:35', NULL, NULL, NULL),
(101079, NULL, 'Huggies Wipes 56s', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:35', '2025-01-21 16:48:35', NULL, NULL, NULL),
(101080, NULL, 'Hydralazine Tabs 25mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:35', '2025-01-21 16:48:35', NULL, NULL, NULL),
(101081, NULL, 'Hydrocortisone Cream (Lucin)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:35', '2025-01-21 16:48:35', NULL, NULL, NULL),
(101082, NULL, 'Hydrocortisone Eye Drop', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:35', '2025-01-21 16:48:35', NULL, NULL, NULL),
(101083, NULL, 'Hydrocortisone Inj', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:35', '2025-01-21 16:48:35', NULL, NULL, NULL),
(101084, NULL, 'Hydrocortisone Oint (Lucin)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:35', '2025-01-21 16:48:35', NULL, NULL, NULL),
(101085, NULL, 'Hydrogen Peroxide (Ear Drops)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:35', '2025-01-21 16:48:35', NULL, NULL, NULL),
(101086, NULL, 'Hydrogen Peroxide (Mouth Wash)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:35', '2025-01-21 16:48:35', NULL, NULL, NULL),
(101087, NULL, 'Hydrogen Peroxide (Wound)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:35', '2025-01-21 16:48:35', NULL, NULL, NULL),
(101088, NULL, 'Hydroxyurea Caps 500mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:35', '2025-01-21 16:48:35', NULL, NULL, NULL),
(101089, NULL, 'Hyoscine Inj (Buscopan)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:35', '2025-01-21 16:48:35', NULL, NULL, NULL),
(101090, NULL, 'Hyoscine Syrup (Bispanol)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:35', '2025-01-21 16:48:35', NULL, NULL, NULL),
(101091, NULL, 'Hyoscine Syrup (Buscopan)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:35', '2025-01-21 16:48:35', NULL, NULL, NULL),
(101092, NULL, 'Hyoscine Tabs (Buscopan)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:35', '2025-01-21 16:48:35', NULL, NULL, NULL),
(101093, NULL, 'Ibugesic Plus Syrup 60ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:35', '2025-01-21 16:48:35', NULL, NULL, NULL),
(101094, NULL, 'Ibugesic Syrup 60ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:35', '2025-01-21 16:48:35', NULL, NULL, NULL),
(101095, NULL, 'Ibumex Ibuprofen Susp 100ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:35', '2025-01-21 16:48:35', NULL, NULL, NULL),
(101096, NULL, 'Ibumex Ibuprofen Susp 60ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:35', '2025-01-21 16:48:35', NULL, NULL, NULL),
(101097, NULL, 'Ibuprofen Tabs 200mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:35', '2025-01-21 16:48:35', NULL, NULL, NULL),
(101098, NULL, 'Ibuprofen Tabs 400mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:35', '2025-01-21 16:48:35', NULL, NULL, NULL),
(101099, NULL, 'Immunace Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:35', '2025-01-21 16:48:35', NULL, NULL, NULL),
(101100, NULL, 'Imperial Shower Gel 250ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:35', '2025-01-21 16:48:35', NULL, NULL, NULL),
(101101, NULL, 'Imperial Soap 115g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:35', '2025-01-21 16:48:35', NULL, NULL, NULL),
(101102, NULL, 'Imperial Soap 175g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:35', '2025-01-21 16:48:35', NULL, NULL, NULL),
(101103, NULL, 'Indclav Syrup 228mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:35', '2025-01-21 16:48:35', NULL, NULL, NULL),
(101104, NULL, 'Indclav Tabs 375mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:35', '2025-01-21 16:48:35', NULL, NULL, NULL),
(101105, NULL, 'Indclav Tabs 625mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:35', '2025-01-21 16:48:35', NULL, NULL, NULL),
(101106, NULL, 'Indomethacin Caps (Indocid)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:35', '2025-01-21 16:48:35', NULL, NULL, NULL),
(101107, NULL, 'Infacare 1', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:35', '2025-01-21 16:48:35', NULL, NULL, NULL),
(101108, NULL, 'Infacare 2', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:35', '2025-01-21 16:48:35', NULL, NULL, NULL),
(101109, NULL, 'Infacare 3', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:35', '2025-01-21 16:48:35', NULL, NULL, NULL),
(101110, NULL, 'Infacol Oral Susp', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL),
(101111, NULL, 'Infant Cereal 350g (Cerelac Wheat)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL),
(101112, NULL, 'Infant-D (Vitamin D)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL),
(101113, NULL, 'Inflamazole Oint', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL),
(101114, NULL, 'Insulin Inj (Actrapid)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL),
(101115, NULL, 'Insulin Inj (Mixtard)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL),
(101116, NULL, 'Intacycline Skin Oint', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL),
(101117, NULL, 'Intamine Cream', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL),
(101118, NULL, 'Intamine Syrup', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL),
(101119, NULL, 'Iodine Tincture', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL),
(101120, NULL, 'Irbesartan Tabs 150mg (Irovel)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL),
(101121, NULL, 'Irbesartan Tabs 300mg (Irovel)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL),
(101122, NULL, 'Irifone Gel 50mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL),
(101123, NULL, 'Irovel H Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL),
(101124, NULL, 'Isoryn Nasal Drop (Adult)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL),
(101125, NULL, 'Isoryn Nasal Drop (Pediatric)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL),
(101126, NULL, 'Isosorbide 10mg (Isorem)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL),
(101127, NULL, 'IVY Syrup', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL),
(101128, NULL, 'Johnson Baby Jelly 100ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL),
(101129, NULL, 'Johnson Baby Jelly 150ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL),
(101130, NULL, 'Johnson Baby Jelly 250ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL),
(101131, NULL, 'Johnson Baby Lotion 125ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL),
(101132, NULL, 'Johnson Baby Oil 125ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL),
(101133, NULL, 'Johnson Baby Oil 200ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL),
(101134, NULL, 'Johnson Baby Oil 300ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL),
(101135, NULL, 'Johnson Baby Oil 500ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL),
(101136, NULL, 'Johnson Baby Oil 50ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL),
(101137, NULL, 'Johnson Baby Powder 100g', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL),
(101138, NULL, 'Johnson Baby Powder 200g', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL),
(101139, NULL, 'Johnson Baby Powder 400g', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL),
(101140, NULL, 'Johnson Baby Powder 500g', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL),
(101141, NULL, 'Johnson Baby Powder 50g', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL),
(101142, NULL, 'Johnson Baby Soap 100g', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL),
(101143, NULL, 'Joint Lube Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL),
(101144, NULL, 'Joint Support Caps (21st Century)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL),
(101145, NULL, 'Jointace Tabs (Omega-3)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL),
(101146, NULL, 'Jointace Tabs (Original)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL),
(101147, NULL, 'Junior Care Syrup', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL),
(101148, NULL, 'Kafid Cough Syrup', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL),
(101149, NULL, 'Kaisiki Soap', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL),
(101150, NULL, 'Ketoconazole Cream (Dezor)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL),
(101151, NULL, 'Ketoconazole Cream (Ketineal)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL),
(101152, NULL, 'Ketoconazole Cream (Kinheal)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL),
(101153, NULL, 'Ketoconazole Cream 30g (Ketokant)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL),
(101154, NULL, 'Ketoconazole Shampoo (Ketoplus)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL),
(101155, NULL, 'Ketoconazole Shampoo (Kinazol)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL),
(101156, NULL, 'Ketoconazole Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL),
(101157, NULL, 'Ketogesic Caps', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL),
(101158, NULL, 'Ketogesic Gel', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL),
(101159, NULL, 'Ketoprofen Caps (Fastum)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL),
(101160, NULL, 'Ketoprofen Gel 30g (Fastum)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL),
(101161, NULL, 'Ketoprofen Gel 50g (Fastum)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL),
(101162, NULL, 'Ketotifen Tabs 1mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL),
(101163, NULL, 'Kidcare Syrup 100ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL),
(101164, NULL, 'Kidcare Syrup 200ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL),
(101165, NULL, 'Kids Toothbrush (Midoli)', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL);
INSERT INTO `inv_products` (`id`, `barcode`, `name`, `generic_name`, `category_id`, `sub_category_id`, `type`, `standard_uom`, `sales_uom`, `purchase_uom`, `indication`, `dosage`, `min_quantinty`, `max_quantinty`, `status`, `created_at`, `updated_at`, `npk_ratio`, `brand`, `pack_size`) VALUES
(101166, NULL, 'Kids Wipes (Kiss)', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL),
(101167, NULL, 'Kilimanjaro (Aloe Vera)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL),
(101168, NULL, 'Kilkof Syrup', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL),
(101169, NULL, 'Knee Support', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL),
(101170, NULL, 'KOACT Syrup 156mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL),
(101171, NULL, 'KOACT Tabs 375mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL),
(101172, NULL, 'KOACT Tabs 625mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL),
(101173, NULL, 'Koflyn Syrup (Adult)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL),
(101174, NULL, 'Koflyn Syrup (Pediatric)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL),
(101175, NULL, 'Kofol SF Syrup', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL),
(101176, NULL, 'Kotex Pads', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL),
(101177, NULL, 'KY Jelly 42g', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL),
(101178, NULL, 'KY Jelly 82g', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL),
(101179, NULL, 'Lactogen 1', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL),
(101180, NULL, 'Lactogen 2', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL),
(101181, NULL, 'Lactulose Soln 100ml (Kleenlac)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL),
(101182, NULL, 'Lactulose Soln 100ml (Lactease)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL),
(101183, NULL, 'Lactulose Soln 100ml (Laxalink)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL),
(101184, NULL, 'Lactulose Soln 100ml (Laxiwal)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:36', '2025-01-21 16:48:36', NULL, NULL, NULL),
(101185, NULL, 'Lactulose Soln 100ml (Livoluk)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101186, NULL, 'Lactulose Soln 200ml (Livoluk)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101187, NULL, 'LadininTabs 500mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101188, NULL, 'Laefin Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101189, NULL, 'Lancettes', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101190, NULL, 'Lansoprazole Caps (Lan-30)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101191, NULL, 'Lartem Syrup 60ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101192, NULL, 'Lasix Inj', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101193, NULL, 'Lasix Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101194, NULL, 'Latanoprost 0.005% Eye Drop', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101195, NULL, 'Lavy Pads', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101196, NULL, 'Lemonvate Cream 30g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101197, NULL, 'Letrozole Tabs 2.5mg (Denk)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101198, NULL, 'Letrozole Tabs 2.5mg (Letroday)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101199, NULL, 'Levamisole Tabs 40g (Letrax)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101200, NULL, 'Levodopa + Carbidopa Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101201, NULL, 'Levofloxacin Tabs 500mg (Levoz)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101202, NULL, 'Levonorgestrel Tabs (Emerginor)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101203, NULL, 'Levonorgestrel Tabs (P2)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101204, NULL, 'Levothyroxine 25mg (Euthyrox)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101205, NULL, 'Levothyroxine 50mg (Euthyrox)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101206, NULL, 'Lifebuoy Shower Gel 1000ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101207, NULL, 'Lifebuoy Shower Gel 300ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101208, NULL, 'Lifebuoy Shower Gel 500ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101209, NULL, 'Lifebuoy Soap 175g', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101210, NULL, 'Lifebuoy Soap100g', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101211, NULL, 'Lignocaine inj', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101212, NULL, 'Lincoderm Cream', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101213, NULL, 'Lincosone Cream 10g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101214, NULL, 'Liniment Alba 100ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101215, NULL, 'Lioton Gel', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101216, NULL, 'Lisinopril Tabs 10mg (Listril)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101217, NULL, 'Lisinopril Tabs 10mg (Zestril)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101218, NULL, 'Lisinopril Tabs 5mg (Listril)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101219, NULL, 'Lisinopril Tabs 5mg (Zestril)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101220, NULL, 'Listerine Mouth Wash 250 ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101221, NULL, 'Listerine Mouth Wash 500 ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101222, NULL, 'Livolin Forte Caps', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101223, NULL, 'Locid Susp 200ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101224, NULL, 'Lomefloxacin Tabs (Lomflox)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101225, NULL, 'Loperamide Tabs (Loperium)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101226, NULL, 'Loratadine Syrup (Lorata)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101227, NULL, 'Loratadine Syrup (Tidilol)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101228, NULL, 'Loratadine Syrup 100ml  (Clarityne)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101229, NULL, 'Loratadine Tabs 10mg  (Clarityne)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101230, NULL, 'Loratadine Tabs 10mg  (Loratyn)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101231, NULL, 'Loratadine Tabs 10mg (Clarinase)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101232, NULL, 'Loratadine Tabs 10mg (Lara)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101233, NULL, 'Lorazepam Tabs 1mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101234, NULL, 'Lorazepam Tabs 2mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101235, NULL, 'Losartan Potassium Tabs 25mg (Costan H)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101236, NULL, 'Losartan Potassium Tabs 25mg (Costan)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101237, NULL, 'Losartan Potassium Tabs 25mg (Intas)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101238, NULL, 'Losartan Potassium Tabs 25mg (Losa H)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101239, NULL, 'Losartan Potassium Tabs 25mg (Losa)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101240, NULL, 'Losartan Potassium Tabs 25mg (Losakind)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101241, NULL, 'Losartan Potassium Tabs 25mg (Presartan H)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101242, NULL, 'Losartan Potassium Tabs 25mg (Presartan)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101243, NULL, 'Losartan Potassium Tabs 50mg (Costan H)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101244, NULL, 'Losartan Potassium Tabs 50mg (Costan)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101245, NULL, 'Losartan Potassium Tabs 50mg (Denk )', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101246, NULL, 'Losartan Potassium Tabs 50mg (Intas)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101247, NULL, 'Losartan Potassium Tabs 50mg (Losa)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101248, NULL, 'Losartan Potassium Tabs 50mg (Losakind)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101249, NULL, 'Losartan Potassium Tabs 50mg (Presartan H)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101250, NULL, 'Losartan Potassium Tabs 50mg (Presartan)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101251, NULL, 'Loxoprofen Sodium Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101252, NULL, 'Lucozade Energy 300ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101253, NULL, 'Lucozade Energy 600ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101254, NULL, 'Lumerax Tabs 80/480', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101255, NULL, 'Maalox Susp 150ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101256, NULL, 'Maalox Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101257, NULL, 'Mackintosh 1mtr', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101258, NULL, 'Magic Shaving Powder', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101259, NULL, 'Magnesium Syrup', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101260, NULL, 'Magnesium Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101261, NULL, 'Makini Ukwaju Soap', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101262, NULL, 'Malafin Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101263, NULL, 'Mama Delivery Kit', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101264, NULL, 'Manicure Set Kit', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101265, NULL, 'Manual Breast Pump', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101266, NULL, 'MaxCal Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:37', '2025-01-21 16:48:37', NULL, NULL, NULL),
(101267, NULL, 'Maxitrol E/d', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101268, NULL, 'Mebendazole Susp (Natoa)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101269, NULL, 'Mebendazole Syrup (Wormol)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101270, NULL, 'Mebendazole Syrup (Wornil)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101271, NULL, 'Mebendazole Tabs (Natoa)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101272, NULL, 'Mebendazole Tabs (Wormnil)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101273, NULL, 'Mebendazole Tabs (Wormol)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101274, NULL, 'Mebo Oint', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101275, NULL, 'Medi Plast', NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101276, NULL, 'Medioral Mouth Wash 130ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101277, NULL, 'Medioral Mouth Wash 300ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101278, NULL, 'Medioral Toothpaste 50ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101279, NULL, 'Medisoft Mosquito Repellant Lotion', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101280, NULL, 'Meditrol Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101281, NULL, 'Mediven Cream', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101282, NULL, 'Mediven Oint 15g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101283, NULL, 'Med-Keel A Lozenges', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101284, NULL, 'Mefenamic Acid Caps (Cyprus)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101285, NULL, 'Mefenamic Acid Caps (India)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101286, NULL, 'Meftal - P 100mg Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101287, NULL, 'Meftal 250mg Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101288, NULL, 'Meftal 500mg Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101289, NULL, 'Meftal Forte Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101290, NULL, 'Meftal Spas Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101291, NULL, 'Mega 3 Salmon Oil', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101292, NULL, 'Mega iiCARE Caps', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101293, NULL, 'Mega iiCARE Plus Caps', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101294, NULL, 'Mega Kiddz Vita Chewz', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101295, NULL, 'Mega Prenatal Caps', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101296, NULL, 'Meloxicam Tabs 15mg (M-cam)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101297, NULL, 'Meloxicam Tabs 15mg (Muvera)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101298, NULL, 'Meloxicam Tabs 7.5mg (M-cam)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101299, NULL, 'Meloxicam Tabs 7.5mg (Muvera)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101300, NULL, 'Menopace Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101301, NULL, 'Menthobalm Relax', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101302, NULL, 'Menthodex Lozenges', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101303, NULL, 'Menthodex Syrup 100ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101304, NULL, 'Menthodex Syrup 200ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101305, NULL, 'Mentos Chewing Gum', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101306, NULL, 'Metformin Tabs 1000mg (Denk)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101307, NULL, 'Metformin Tabs 500mg (Cyprus)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101308, NULL, 'Metformin Tabs 500mg (Denk)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101309, NULL, 'Metformin Tabs 500mg (India)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101310, NULL, 'Metformin Tabs 850mg (Denk)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101311, NULL, 'Methyldopa Tabs 250mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101312, NULL, 'Metoclopramide Inj', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101313, NULL, 'Metoclopramide Oral Soln', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101314, NULL, 'Metoclopramide Tab', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101315, NULL, 'Metoprolol 50mg (Betaloc)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101316, NULL, 'Metronidazole Gel 20g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101317, NULL, 'Metronidazole Gel 30g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101318, NULL, 'Metronidazole Inj', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101319, NULL, 'Metronidazole Syrup', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101320, NULL, 'Metronidazole Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101321, NULL, 'Miconazole Cream 10g (Miconax)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101322, NULL, 'Miconazole Cream 15g (Fungistat)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101323, NULL, 'Miconazole Oral Gel 20g (Micona)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101324, NULL, 'Miconazole Oral Gel 40g (Micona)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101325, NULL, 'Miconazole Vaginal Cream (Gynozol)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101326, NULL, 'Miconazole VP (Gynozole)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101327, NULL, 'Microgynon Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101328, NULL, 'Mifupen Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101329, NULL, 'Milical Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101330, NULL, 'Minara Coconut Oil', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101331, NULL, 'Mini Bears Chewable', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101332, NULL, 'Misoprostol Tabs (Mispro)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101333, NULL, 'Misoprostol Tabs (Ney)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101334, NULL, 'Moisol Eye Drops', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101335, NULL, 'Momeasy Baby Travel Pack', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101336, NULL, 'Momeasy Baby Wipes', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101337, NULL, 'Momeasy Childrens Toothbrush', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101338, NULL, 'Momeasy Cotton Buds 100', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101339, NULL, 'Momeasy Cotton Buds 200', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101340, NULL, 'Momeasy Disposable Breast Pads', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101341, NULL, 'Momeasy Silicone Pacifier', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101342, NULL, 'Mometasone Cream 15g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101343, NULL, 'Montelukast Tabs 10mg (Breathezy)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101344, NULL, 'Montelukast Tabs 10mg (Kipel)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101345, NULL, 'Montelukast Tabs 10mg (Montemac)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101346, NULL, 'Montelukast Tabs 10mg (Zokast)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101347, NULL, 'Montelukast Tabs 5mg (Breathezy)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101348, NULL, 'Montelukast Tabs 5mg (Kipel)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101349, NULL, 'Montelukast Tabs 5mg (M-Kast)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:38', '2025-01-21 16:48:38', NULL, NULL, NULL),
(101350, NULL, 'Montelukast Tabs 5mg (Monast)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101351, NULL, 'Mosquito Repellant Lotion (Emina)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101352, NULL, 'Movit Hair Relaxer', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101353, NULL, 'Movit Herbal Jelly', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101354, NULL, 'Movit Soap', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101355, NULL, 'Moxikind Syrup 100ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101356, NULL, 'Muco-Asthalin Syrup 100ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101357, NULL, 'Mucogel Susp', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101358, NULL, 'Mucolyn Syrup (Adult)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101359, NULL, 'Mucolyn Syrup (Pediatric)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101360, NULL, 'Multivitamin Syrup 100ml (Zincovit)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101361, NULL, 'Multivitamin Syrup 60ml (Rinavit)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101362, NULL, 'Multivitamin Syrup100ml (Dayvit)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101363, NULL, 'Multivitamin Syrup100ml (Emdevit)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101364, NULL, 'Multivitamin Syrup100ml (Megavit)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101365, NULL, 'Multivitamin Syrup100ml (Phamactin)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101366, NULL, 'Multivitamin Syrup100ml (Rinavit)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101367, NULL, 'Multivitamin Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101368, NULL, 'Mumfer Syrup 150ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101369, NULL, 'Mumfer Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101370, NULL, 'Mupirocin Oint 15g (Bactopic)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101371, NULL, 'Mupirocin Oint 15g (Bactrox)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101372, NULL, 'Mupirocin Oint 15g (Dermocin)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101373, NULL, 'Mupirocin Oint 15g (Supirocin)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101374, NULL, 'Mupirocin Oint 5g (Bactopic)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101375, NULL, 'Mupirocin Oint 5g (Mupricon)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101376, NULL, 'Mupirocin Oint10g  (Mupisoft)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101377, NULL, 'Muscle Plus Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101378, NULL, 'Myclav Susp 156mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101379, NULL, 'Myclav Susp 228mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101380, NULL, 'Mycoderm Lotion', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101381, NULL, 'Mycoderm-C Powder', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101382, NULL, 'Mycota Cream 25g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101383, NULL, 'Mycota Powder 70', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101384, NULL, 'Nail Cut', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101385, NULL, 'Nalidixic Acid Tabs (Cyprus)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101386, NULL, 'Nalidixic Acid Tabs (India)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101387, NULL, 'Napkin Tissue', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101388, NULL, 'NAT B Caps', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101389, NULL, 'NAT C Caps', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101390, NULL, 'NAT D Caps', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101391, NULL, 'Nauma Gel 20g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101392, NULL, 'Nebivolol Tabs 10mg (Nebiem)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101393, NULL, 'Nebivolol Tabs 5mg ( Nodon)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101394, NULL, 'Nebivolol Tabs 5mg (Nebiem)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101395, NULL, 'Nebivolol Tabs 5mg (Nebilet)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101396, NULL, 'Nebivolol Tabs 5mg (Nebtas)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101397, NULL, 'Nebo Soap (Muarobaini)', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101398, NULL, 'Nebtas H Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101399, NULL, 'Neocare Diapers (ML)', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101400, NULL, 'Neocare Diapers (XL)', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101401, NULL, 'Neoclav Susp 228mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101402, NULL, 'Neoclav Tabs 375mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101403, NULL, 'Neoclav Tabs 625mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101404, NULL, 'Neosoft Baby Wipes 80pcs', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101405, NULL, 'Neosoft Pampers 12g+ (XL)', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101406, NULL, 'Neosoft Pampers 2-6kg (S)', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101407, NULL, 'Neosoft Pampers 5-9kg (M)', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101408, NULL, 'Neosoft Pampers 8-14kg (L)', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101409, NULL, 'Netasole Cream', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101410, NULL, 'Netasole Oint', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101411, NULL, 'Netazox Syrup 30ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101412, NULL, 'Netazox Tabs 500mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101413, NULL, 'Neuro Care Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101414, NULL, 'Neuro Forte Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101415, NULL, 'Neuro Support Caps (21st Century)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101416, NULL, 'Neurobian Forte Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101417, NULL, 'Neurorubine Forte Tabs (India)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101418, NULL, 'Neurorubine Forte Tabs (Swiss)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101419, NULL, 'Neurorubine Inj', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101420, NULL, 'Neuroton Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101421, NULL, 'Neurovit Forte Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101422, NULL, 'NGT', NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101423, NULL, 'Nifedipine & Atenolol Tabs (Nilol)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101424, NULL, 'Nifedipine Tabs 10mg (Cyprus)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101425, NULL, 'Nifedipine Tabs 10mg (Denk)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101426, NULL, 'Nifedipine Tabs 10mg (India)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101427, NULL, 'Nifedipine Tabs 20mg (Cyprus)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101428, NULL, 'Nifedipine Tabs 20mg (Denk)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101429, NULL, 'Nifedipine Tabs 20mg (India)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101430, NULL, 'Nilacid Susp 100ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101431, NULL, 'Nilacid Susp 200ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:39', '2025-01-21 16:48:39', NULL, NULL, NULL),
(101432, NULL, 'Nimodipine Inj', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101433, NULL, 'Nimodipine Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101434, NULL, 'Nitazoxanide Susp (Nitazox)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101435, NULL, 'Nitrofurantoin 100mg Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101436, NULL, 'Nivea Cream 150ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101437, NULL, 'Nivea Cream 60ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101438, NULL, 'Nivea Deodorant Roll', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101439, NULL, 'Nivea Deodorant Stick', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101440, NULL, 'Nivea Hand Cream', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101441, NULL, 'Nivea Jelly', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101442, NULL, 'Nivea Lotion 100ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101443, NULL, 'Nivea Lotion 200ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101444, NULL, 'Nivea Lotion 250ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101445, NULL, 'Nivea Lotion 400ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101446, NULL, 'Nivea Scrub 150ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101447, NULL, 'Nivea Shaving Foam Sensitive', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101448, NULL, 'Nivea Spray', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101449, NULL, 'Nivea Sun Lotion 30', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101450, NULL, 'Nivea Sun Lotion 50+', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101451, NULL, 'No-Bite', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101452, NULL, 'Nor T Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101453, NULL, 'Norfloxacin Tabs (Normax)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101454, NULL, 'Normal Saline Drip (NS)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101455, NULL, 'NOSIC Tabs 20mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101456, NULL, 'No-Spa Tabs 40mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL);
INSERT INTO `inv_products` (`id`, `barcode`, `name`, `generic_name`, `category_id`, `sub_category_id`, `type`, `standard_uom`, `sales_uom`, `purchase_uom`, `indication`, `dosage`, `min_quantinty`, `max_quantinty`, `status`, `created_at`, `updated_at`, `npk_ratio`, `brand`, `pack_size`) VALUES
(101457, NULL, 'Novagra Tabs 100mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101458, NULL, 'Novagra Tabs 50mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101459, NULL, 'Nuforce G Cream 20g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101460, NULL, 'Nylon Surgical Suture', NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101461, NULL, 'Nystatin Oral Susp', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101462, NULL, 'Nystatin Oral Tabs (Cyprus)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101463, NULL, 'Nystatin Oral Tabs (Nyska)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101464, NULL, 'Nystatin VP Tabs (Labstatin)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101465, NULL, 'Nystatin VP Tabs (Nyska-V)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101466, NULL, 'OB Tampoons Normal 16s', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101467, NULL, 'OB Tampoons Normal 8s', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101468, NULL, 'OB Tampoons Super 16s', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101469, NULL, 'OB Tampoons Super 8s', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101470, NULL, 'Ofloxacin Tabs 200mg (Eracin)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101471, NULL, 'Ofloxacin Tabs 200mg (Evaflox)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101472, NULL, 'Ofloxacin Tabs 200mg (Ofoxin)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101473, NULL, 'Ofloxacin Tabs 200mg (Toflox)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101474, NULL, 'Olanzapine Tabs 10mg (Jubolanz)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101475, NULL, 'Olanzapine Tabs 10mg (Olangem)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101476, NULL, 'Olanzapine Tabs 10mg (Oleanz)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101477, NULL, 'Olanzapine Tabs 10mg (Olmac)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101478, NULL, 'Olanzapine Tabs 5mg (Olangem)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101479, NULL, 'Olanzapine Tabs 5mg (Oleanz)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101480, NULL, 'Olanzapine Tabs 5mg (Olmac)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101481, NULL, 'Olfen Gel 20g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101482, NULL, 'Olfen Gel 50g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101483, NULL, 'Olfen Inj 2ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101484, NULL, 'Olfen Tabs 100mg SR', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101485, NULL, 'Olfen Tabs 50mg SR', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101486, NULL, 'Olfen Tabs 75mg SR', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101487, NULL, 'Olive Hair Oil', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101488, NULL, 'Olive Hand Cream 60g', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101489, NULL, 'Olive Oil B.P 70ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101490, NULL, 'Olive Oil Hair Spray L', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101491, NULL, 'Olive Oil Hair Spray S', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101492, NULL, 'Olive Oil Relaxer', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101493, NULL, 'Olmesar H Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101494, NULL, 'Olmesar Tabs 20mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101495, NULL, 'Omecare Caps 1000mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101496, NULL, 'Omeflex Caps', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101497, NULL, 'Omega 3 Caps (21st Century)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101498, NULL, 'Omega 3 Caps (Neopham)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101499, NULL, 'Omega 3 Caps (Vital)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101500, NULL, 'Omega 3 Soft Gel (Hovid)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101501, NULL, 'Omega 3 Soft Gel (Mega)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101502, NULL, 'Omega 3,6,9 Tabs (Ultra)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101503, NULL, 'Omeprazole Caps 20mg (Omesk)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101504, NULL, 'Omeprazole Caps 20mg (Omlink)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101505, NULL, 'Omeprazole Tabs 20mg (Zosec)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101506, NULL, 'Once A Day Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101507, NULL, 'Ondansetron Inj (Vomikind)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101508, NULL, 'One Daily Men', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101509, NULL, 'One Daily Women', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101510, NULL, 'Oneo Chewing Gum', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101511, NULL, 'Opele Lotion', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101512, NULL, 'Oracure Gel 15g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101513, NULL, 'Ornidazole Tabs (Dazolic)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101514, NULL, 'Orodar Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101515, NULL, 'Orofer Caps', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101516, NULL, 'Orofer Syrup', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101517, NULL, 'ORS', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101518, NULL, 'Osmolax Solution 100ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101519, NULL, 'Osmolax Solution 200ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101520, NULL, 'Osteo Support  Caps (21st Century)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101521, NULL, 'Osteocare Liquid', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101522, NULL, 'Osteocare Plus Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:40', '2025-01-21 16:48:40', NULL, NULL, NULL),
(101523, NULL, 'Osteocare Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101524, NULL, 'Osteomin Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101525, NULL, 'Otrivine Nasal Drop (Infant)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101526, NULL, 'Otrivine Nasal Drops (Adult)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101527, NULL, 'Oveready Pregnancy Test', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101528, NULL, 'Ovulation Test', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101529, NULL, 'Oxoferin Soln', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101530, NULL, 'Oxytocin Inj 10U', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101531, NULL, 'Oxytocin Inj 5U', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101532, NULL, 'P Natal Caps (21st Century)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101533, NULL, 'Palmer Cocoa Butter', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101534, NULL, 'Paludar-Z', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101535, NULL, 'Panadol Advance Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101536, NULL, 'Panadol Baby & Infant Syrup', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101537, NULL, 'Panadol Extra Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101538, NULL, 'Panadol Syrup (Neladol)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101539, NULL, 'Pantoprazole Inj 40mg (Panacid)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101540, NULL, 'Pantoprazole Inj 40mg (Pantacid)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101541, NULL, 'Pantoprazole Inj 40mg (Pantalink)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101542, NULL, 'Pantoprazole Inj 40mg (Pantocid)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101543, NULL, 'Pantoprazole Tabs 40mg (Panacid)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101544, NULL, 'Pantoprazole Tabs 40mg (Pantacid)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101545, NULL, 'Pantoprazole Tabs 40mg (Pantalink)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101546, NULL, 'Pantoprazole Tabs 40mg (Pantocid)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101547, NULL, 'Paracetamol Inj', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101548, NULL, 'Paracetamol Supp 125mg (Adol)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101549, NULL, 'Paracetamol Supp 125mg (Denk)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101550, NULL, 'Paracetamol Supp 250mg (Adol)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101551, NULL, 'Paracetamol Supp 250mg (Denk)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101552, NULL, 'Paracetamol Syrup (Bells)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101553, NULL, 'Paracetamol Syrup (Cetamol)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101554, NULL, 'Paracetamol Syrup (Dolomol)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101555, NULL, 'Paracetamol Syrup (Elymol)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101556, NULL, 'Paracetamol Syrup (Emdemol)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101557, NULL, 'Paracetamol Syrup (Sheladol)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101558, NULL, 'Paracetamol Syrup (Totolmol)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101559, NULL, 'Paracetamol Syrup (Totolyn)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101560, NULL, 'Paracetamol Syrup (Zenadol)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101561, NULL, 'Paracetamol Tabs (Denk)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101562, NULL, 'Paracetamol Tabs (India)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101563, NULL, 'Parachute Coconut Oil 100ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101564, NULL, 'Parachute Coconut Oil 1Ltr', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101565, NULL, 'Parachute Coconut Oil 200ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101566, NULL, 'Parachute Coconut Oil 500ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101567, NULL, 'Parachute Coconut Oil 50ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101568, NULL, 'ParaCo-Denk Tabs 500/30', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101569, NULL, 'Paraffin Cotton Gauze', NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101570, NULL, 'Pascalium Tabs 1.5mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101571, NULL, 'Pears Soap', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101572, NULL, 'Pen V Syrup', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101573, NULL, 'Pen V Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101574, NULL, 'Penpiclox Susp', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101575, NULL, 'Perfectile Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101576, NULL, 'Perindopril Tabs 5mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101577, NULL, 'Persol 2.5 Gel', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101578, NULL, 'Persol 5 Gel', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101579, NULL, 'Persol Forte Cream 20g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101580, NULL, 'Pharmacoff Syrup', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101581, NULL, 'Phenobarbitone Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101582, NULL, 'Phenytoin Inj', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101583, NULL, 'Phenytoin Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101584, NULL, 'Pioglitazone Tabs 15mg (Piosafe)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101585, NULL, 'Pioglitazone Tabs 30mg (P-Glitz)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101586, NULL, 'Pioglitazone Tabs 30mg (Piosafe)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101587, NULL, 'Piroxicam Tabs (Agomove)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101588, NULL, 'PK Chewing Gums', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101589, NULL, 'Plendil Tabs 10mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101590, NULL, 'Plendil Tabs 5mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101591, NULL, 'Polybamycin Oint 10g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101592, NULL, 'Ponpon Diapers 3-6kg (S)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101593, NULL, 'Ponpon Diapers 5-10kg (M)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101594, NULL, 'Ponpon Diapers 9-13kg (L)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101595, NULL, 'POP (Plaster Of Paris) 10cm', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101596, NULL, 'POP (Plaster Of Paris) 15cm', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101597, NULL, 'POP (Plaster Of Paris) 20cm', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101598, NULL, 'POP (Plaster Of Paris) 5cm', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101599, NULL, 'POP (Plaster Of Paris) 7.5cm', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101600, NULL, 'Potassium Permanganate Soln (PP)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101601, NULL, 'Povidone Iodine Soln 100ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101602, NULL, 'Povidone Iodine Soln 250ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101603, NULL, 'Povidone Iodine Soln 60ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101604, NULL, 'Power Vapour Rub', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101605, NULL, 'Praziquantel Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:41', '2025-01-21 16:48:41', NULL, NULL, NULL),
(101606, NULL, 'Predinisolone Eye Drops', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101607, NULL, 'Predinisolone Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101608, NULL, 'Pregabalin Caps 150mg (Ligaba)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101609, NULL, 'Pregabalin Caps 150mg (Neurogab)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101610, NULL, 'Pregabalin Caps 150mg (Pregasafe)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101611, NULL, 'Pregabalin Caps 75mg (Akugabalin)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101612, NULL, 'Pregabalin Caps 75mg (Ligaba)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101613, NULL, 'Pregabalin Caps 75mg (Pregaba)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101614, NULL, 'Pregabalin Caps 75mg (Pregalin)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101615, NULL, 'Pregabalin Caps 75mg (Pregasafe)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101616, NULL, 'Pregnacare Caps', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101617, NULL, 'Pregnacare Conception Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101618, NULL, 'Pregnacare Plus Tabs (Omega-3)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101619, NULL, 'Preparation H Oint 25g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101620, NULL, 'Preparation H Supp', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101621, NULL, 'Primolut Depot Inj', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101622, NULL, 'Primolut N Tabs 5mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101623, NULL, 'Prinaquin Syrup', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101624, NULL, 'Probeta N Drops', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101625, NULL, 'Profenazole Caps 250mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101626, NULL, 'Progesterone Caps 200mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101627, NULL, 'Progesterone Inj', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101628, NULL, 'Promethazine Inj (Phenegan)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101629, NULL, 'Promethazine Syrup (Phenegan)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101630, NULL, 'Promethazine Tabs (Phenegan)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101631, NULL, 'Propofol Inj', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101632, NULL, 'Propranolol Tabs 40mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101633, NULL, 'Prostacare Caps', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101634, NULL, 'Protex Soap 100g', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101635, NULL, 'Protex Soap 175g', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101636, NULL, 'Proviron Tabs 25mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101637, NULL, 'Proximexa Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101638, NULL, 'Prozin Cream', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101639, NULL, 'Prozin Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101640, NULL, 'Pyary Soap', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101641, NULL, 'Quadrajel 15g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101642, NULL, 'Queen Elizabeth  Cocoa Butter 250ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101643, NULL, 'Queen Elizabeth Cocoa Butter  125ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101644, NULL, 'Queen Elizabeth Cocoa Butter 500ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101645, NULL, 'Quinine Bisulphate Syrup (Quinizen)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101646, NULL, 'Quinine Inj', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101647, NULL, 'Quinine Sulphate Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101648, NULL, 'Quinine Syrup', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101649, NULL, 'Rabeprazole Inj (Rabeloc)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101650, NULL, 'Rabeprazole Tabs (Rabekind)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101651, NULL, 'Rabeprazole Tabs (Rabeloc)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101652, NULL, 'Rabeprazole Tabs (Rabemac)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101653, NULL, 'Ramipril Caps 2.5mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101654, NULL, 'Ramipril Caps 5mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101655, NULL, 'Ranferon Caps', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101656, NULL, 'Ranferon Inj', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101657, NULL, 'Ranitidine Inj', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101658, NULL, 'Ranitidine Tabs (Raniplex)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101659, NULL, 'Ranitidine Tabs (R-Loc)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101660, NULL, 'Rapiclav Syrup 228mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101661, NULL, 'Rapiclav Tabs 375mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101662, NULL, 'Rapiclav Tabs 625mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101663, NULL, 'Razac Lotion', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101664, NULL, 'RB Tone 200ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101665, NULL, 'Redin PN Syrup', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101666, NULL, 'Relcer Gel 100ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101667, NULL, 'Relcer Gel 180ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101668, NULL, 'Repace 50', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101669, NULL, 'Repace H', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101670, NULL, 'Retin A Cream 0.05%', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101671, NULL, 'Retin A Gel 0.025%', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101672, NULL, 'Revlon Aloe Vera', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101673, NULL, 'Revlon Original', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101674, NULL, 'Rexidin Mouth Wash', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101675, NULL, 'Rhinathiol Syrup 2% (Pediatric)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101676, NULL, 'Rhinathiol Syrup 5% (Adult)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101677, NULL, 'Ribena 300ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101678, NULL, 'Ribena 600ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101679, NULL, 'Ridmal 40/320 Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101680, NULL, 'Ringer Lactate Drip (RL)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101681, NULL, 'Risperidone Tabs 2mg (Risdone)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101682, NULL, 'Risperidone Tabs 2mg (Rispitas)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101683, NULL, 'Rohisol Syrup (Ketrax)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101684, NULL, 'Rosuvastatin Tabs 10mg (LDNIL)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101685, NULL, 'Rosuvastatin Tabs 10mg (Rosuchol)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101686, NULL, 'Rosuvastatin Tabs 10mg (Rosucor)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101687, NULL, 'Rosuvastatin Tabs 10mg (Rosumac)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101688, NULL, 'Rosuvastatin Tabs 20mg (LDNIL)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101689, NULL, 'Rosuvastatin Tabs 20mg (Rosuchol)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101690, NULL, 'Rosuvastatin Tabs 20mg (Rosucor)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101691, NULL, 'Rosuvastatin Tabs 20mg (Rosuvan)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101692, NULL, 'Rosuvastatin Tabs 20mg (Rosuvas)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101693, NULL, 'Rubee Lotion', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101694, NULL, 'Rungu Spray 400ml', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101695, NULL, 'Rungu Spray 700ml', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:42', '2025-01-21 16:48:42', NULL, NULL, NULL),
(101696, NULL, 'Safi Cream', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101697, NULL, 'Salbutamol Inhaler', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101698, NULL, 'Salbutamol Nebuliser Soln', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101699, NULL, 'Salbutamol Syrup', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101700, NULL, 'Salbutamol Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101701, NULL, 'Salimia Liniment 50ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101702, NULL, 'Saline Nasal Spray 20ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101703, NULL, 'Samona Jelly 150g', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101704, NULL, 'Samona Jelly 250g', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101705, NULL, 'Samona Soap', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101706, NULL, 'Savlon Antispetic 100ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101707, NULL, 'Savlon Antispetic 500ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101708, NULL, 'Savlon Antispetic 50ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101709, NULL, 'Scaboma Cream 25g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101710, NULL, 'Scaboma Lotion 100ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101711, NULL, 'Scalp Vein', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101712, NULL, 'Scheriproct Oint 30g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101713, NULL, 'Scheriproct Supp', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101714, NULL, 'Scotts Syrup 100ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101715, NULL, 'Secnidazole Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101716, NULL, 'Sedikof Syrup', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101717, NULL, 'Seditons Cough Lictus Syrup', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101718, NULL, 'Seditons Syrup', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101719, NULL, 'Sensodyne Toothpaste 100 ml (Rapid)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101720, NULL, 'Sensodyne Toothpaste 100ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101721, NULL, 'Sensodyne Toothpaste 40ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101722, NULL, 'Sensodyne Toothpaste 40ml (Rapid)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101723, NULL, 'Sensodyne Toothpaste 75 ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101724, NULL, 'Sensodyne Toothpaste 75ml (Rapid)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101725, NULL, 'Septol Soln 1Ltr', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101726, NULL, 'Septol Soln 500ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101727, NULL, 'Septol Soln 50ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101728, NULL, 'Serocort Inhaler 125mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101729, NULL, 'Serocort Inhaler 250mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101730, NULL, 'Seven Sees  Syrup', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101731, NULL, 'Sildenafil Tabs 100mg (Erecto)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101732, NULL, 'Sildenafil Tabs 100mg (Kamagra)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101733, NULL, 'Sildenafil Tabs 100mg (Penegra)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101734, NULL, 'Sildenafil Tabs 50mg (Erecto)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101735, NULL, 'Sildenafil Tabs 50mg (Kamagra)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101736, NULL, 'Sildenafil Tabs 50mg (Njoi)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101737, NULL, 'Sildenafil Tabs 50mg (Penegra)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101738, NULL, 'Silka Papaya Lotion 200ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101739, NULL, 'Silka Papaya Lotion 300ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101740, NULL, 'Silka Soap', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101741, NULL, 'Silverex Cream 10g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101742, NULL, 'Silverex Cream 25g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101743, NULL, 'Silverkank Cream 15g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101744, NULL, 'Silvo Cream 20g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101745, NULL, 'Simethicone Susp (Coliza)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101746, NULL, 'Simethicone Tabs (Coliza)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101747, NULL, 'Sinarest Forte Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101748, NULL, 'Sinarest Linctus Syrup 100ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL);
INSERT INTO `inv_products` (`id`, `barcode`, `name`, `generic_name`, `category_id`, `sub_category_id`, `type`, `standard_uom`, `sales_uom`, `purchase_uom`, `indication`, `dosage`, `min_quantinty`, `max_quantinty`, `status`, `created_at`, `updated_at`, `npk_ratio`, `brand`, `pack_size`) VALUES
(101749, NULL, 'Sitcom Cream', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101750, NULL, 'Sitcom Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101751, NULL, 'Skderm Cream 15g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101752, NULL, 'Skderm Cream 30g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101753, NULL, 'Skipodine Oint', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101754, NULL, 'Sktone Syrup 100ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101755, NULL, 'Sktone Syrup 200ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101756, NULL, 'Sleepy No 1(2-5) 92s', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101757, NULL, 'Sleepy No 2(3-6) 35s', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101758, NULL, 'Sleepy No 2(3-6) 80s', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101759, NULL, 'Sleepy No 3(5-9) 11s', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101760, NULL, 'Sleepy No 3(5-9) 30s', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101761, NULL, 'Sleepy No 3(5-9) 68s', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101762, NULL, 'Sleepy No 4(8-18) 25s', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101763, NULL, 'Sleepy No 5(12-25) 44s', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101764, NULL, 'Sleepy No1 (2-5) 48s', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101765, NULL, 'Sleepy No2 (3-6) 12s', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101766, NULL, 'Sleepy No2 (3-6) 54s', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101767, NULL, 'Sleepy No3 (5-9) 46s', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101768, NULL, 'Sleepy No3 (5-9) 90s', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101769, NULL, 'Sleepy No4 (7-14) 40s', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101770, NULL, 'Sleepy No4 (8-18) 60s', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101771, NULL, 'Sleepy No4 (8-18) 80s', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101772, NULL, 'Sleepy Wipes', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101773, NULL, 'Sodium Cromoglycate Eye Drop', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101774, NULL, 'Sodium Valporate Syrup', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101775, NULL, 'Sodium Valporate Tabs  500mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101776, NULL, 'Sodium Valporate Tabs 200mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101777, NULL, 'Softcare Baby Diaper 10', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101778, NULL, 'Softcare Baby Diaper 12', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101779, NULL, 'Softcare Baby Diaper 14', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101780, NULL, 'Softcare Baby Diaper 40', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101781, NULL, 'Softcare Baby Diaper 42', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:43', '2025-01-21 16:48:43', NULL, NULL, NULL),
(101782, NULL, 'Softcare Baby Diaper 48', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101783, NULL, 'Softcare Baby Diaper 64', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101784, NULL, 'Softcare Baby Diaper 72', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101785, NULL, 'Softcare Baby Diaper 80', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101786, NULL, 'Softcare Baby Pant 10', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101787, NULL, 'Softcare Baby Pants 12', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101788, NULL, 'Softcare Baby Pants 14', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101789, NULL, 'Softcare Baby Pants 40', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101790, NULL, 'Softcare Baby Pants 42', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101791, NULL, 'Softcare Baby Pants 48', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101792, NULL, 'Softcare Pad', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101793, NULL, 'Softcare Wipes', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101794, NULL, 'Sonaderm GM Cream 10g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101795, NULL, 'Sonatec Mouth Wash 100ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101796, NULL, 'Sonatec Mouth Wash 250ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101797, NULL, 'Soul Mate Herbal Hair Grow L', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101798, NULL, 'Soul Mate Herbal Hair Grow S', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101799, NULL, 'Spasmo Drop 15ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101800, NULL, 'Spectinomycin Inj', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101801, NULL, 'Spirit 100ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101802, NULL, 'Spirit 1Litre', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101803, NULL, 'Spirit 5Ltr', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101804, NULL, 'Spironolactone Tabs 25g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101805, NULL, 'Spotclav Susp 228mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101806, NULL, 'Spotclav Tabs 375mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101807, NULL, 'Spotclav Tabs 625mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101808, NULL, 'StaSoft 2Ltr', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101809, NULL, 'StaSoft 750ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101810, NULL, 'Stomacid Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101811, NULL, 'Stool Container', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101812, NULL, 'Strepsils Lozenges', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101813, NULL, 'Sucrafil Susp 100ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101814, NULL, 'Sucrafil Susp 200ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101815, NULL, 'Suito Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101816, NULL, 'Sulbacin Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101817, NULL, 'Sulbactomax Inj 1.5g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101818, NULL, 'Sulphadar Syrup', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101819, NULL, 'Sulphadar Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101820, NULL, 'Sulphur Oint 25g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101821, NULL, 'Surgical Blades', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101822, NULL, 'Surgical Gloves', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101823, NULL, 'Sweetex Tabs 300', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101824, NULL, 'Sweetex Tabs 700', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101825, NULL, 'Syphillis Kit', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101826, NULL, 'Syringe 10CC', NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101827, NULL, 'Syringe 1CC', NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101828, NULL, 'Syringe 20CC', NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101829, NULL, 'Syringe 2CC', NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101830, NULL, 'Syringe 50CC', NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101831, NULL, 'Syringe 5CC', NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101832, NULL, 'T3 Mycin Gel', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101833, NULL, 'T3 Mycin Lotion', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101834, NULL, 'T3 Pimple Gel', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101835, NULL, 'Tadalafil Tabs 20mg (Apcalis)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101836, NULL, 'Tadalafil Tabs 20mg (Eros)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101837, NULL, 'Tadalafil Tabs 20mg (Jovan)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101838, NULL, 'Tadalafil Tabs 20mg (Megalis)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101839, NULL, 'Tadalafil Tabs 20mg (Saheal)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101840, NULL, 'Tambac Susp', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101841, NULL, 'Tamsulosin Caps 0.4mg (Urimax)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101842, NULL, 'TCB Hair Relaxer', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101843, NULL, 'Tegretol Tabs 200mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101844, NULL, 'Teli - H Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101845, NULL, 'Telmisartan H Tabs 40mg (Cilzec Plus)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101846, NULL, 'Telmisartan H Tabs 40mg (Safetelmi)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101847, NULL, 'Telmisartan H Tabs 40mg (Telma)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101848, NULL, 'Telmisartan H Tabs 40mg (Telswift)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101849, NULL, 'Telmisartan H Tabs 80mg (Safetelmi)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101850, NULL, 'Telmisartan H Tabs 80mg (Telma)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101851, NULL, 'Telmisartan H Tabs 80mg (Telswift)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101852, NULL, 'Telmisartan Tabs 20mg (Telma)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101853, NULL, 'Telmisartan Tabs 40mg (Cilzec)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101854, NULL, 'Telmisartan Tabs 40mg (Safetelmi)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101855, NULL, 'Telmisartan Tabs 40mg (Telma)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101856, NULL, 'Telmisartan Tabs 80mg (Cilzec)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101857, NULL, 'Tenoxicam Cap 20mg (Soral)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101858, NULL, 'Terbinafine Cream (Binafin)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:44', '2025-01-21 16:48:44', NULL, NULL, NULL),
(101859, NULL, 'Terbinafine Cream (Terbifin)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101860, NULL, 'Terbinafine Tabs (Terbiforce)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101861, NULL, 'Tetmosol Soap 100g', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101862, NULL, 'Tetmosol Soap 75g', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101863, NULL, 'Tetracycline (TCL) Caps', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101864, NULL, 'Tetracycline (TCL) Eye Oint', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101865, NULL, 'Timol 0.25% Eye Drop', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101866, NULL, 'Timol 0.5% Eye Drop', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101867, NULL, 'Tinidazole Tabs (Norzole)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101868, NULL, 'Tinidazole Tabs (Phamazole )', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101869, NULL, 'Tinidazole Tabs (Wormazole)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101870, NULL, 'Toff Plus Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101871, NULL, 'Tongue Cleaner', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101872, NULL, 'Tooth Pick (L)', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101873, NULL, 'Tooth Pick (S)', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101874, NULL, 'Toothache Tincture 10ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101875, NULL, 'Toras Tabs 5mg (Denk)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101876, NULL, 'Tramadol Caps', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101877, NULL, 'Tramadol Caps (Domadol)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101878, NULL, 'Tramadol Inj 100mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101879, NULL, 'Tramadol Inj 50mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101880, NULL, 'Tranexamic Acid Inj 500mg (Trexamin)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101881, NULL, 'Translipo C Cream 15g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101882, NULL, 'Transvil Syrup', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101883, NULL, 'Travis Herbal Cough Syrup', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101884, NULL, 'Travis Herbal Lozenges', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101885, NULL, 'Tres-Orix Forte Syrup', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101886, NULL, 'Triam Inj', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101887, NULL, 'Trident Sugar Free Gums', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101888, NULL, 'Trigan D Inj', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101889, NULL, 'Trigan-D Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101890, NULL, 'Trimetazidine Tabs (Trivedon)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101891, NULL, 'Trioderm Cream 15g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101892, NULL, 'Trust Daisy Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101893, NULL, 'Trust Lily Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101894, NULL, 'Tumbosin Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101895, NULL, 'U.S.A Soap', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101896, NULL, 'Udihep Forte Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101897, NULL, 'Umbrical Cord Clamp', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101898, NULL, 'Under Pads', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101899, NULL, 'Unifed Syrup', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101900, NULL, 'Unisten Cream', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101901, NULL, 'Urine Bag', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101902, NULL, 'Urine Pregnancy Test (UPT)', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101903, NULL, 'V2 Plus Caps', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101904, NULL, 'Valsartan Tabs 80mg (Diovan)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101905, NULL, 'Varta Battery 9V', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101906, NULL, 'Varta Battery AA', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101907, NULL, 'Varta Battery AAA', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101908, NULL, 'Varta Battery CR2032', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101909, NULL, 'Vaseline Advanced Repair 400ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101910, NULL, 'Vaseline Anti-bacterial Hand Cream 50g', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101911, NULL, 'Vaseline Cocoa Glow 200ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101912, NULL, 'Vaseline Cocoa Glow Lotion 400ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101913, NULL, 'Vaseline Gentle Baby 250ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101914, NULL, 'Vaseline Hair Tonic 100ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101915, NULL, 'Vaseline Hair Tonic 200ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101916, NULL, 'Vaseline Hair Tonic 300ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101917, NULL, 'Vaseline Jelly 100ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101918, NULL, 'Vaseline Jelly 250ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101919, NULL, 'Vaseline Jelly 50ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101920, NULL, 'Vaseline Jelly Men Cooling 100ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101921, NULL, 'Vaseline Lip Therapy', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101922, NULL, 'Vaseline Men Cooling Lotion 400ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101923, NULL, 'Vaseline Men Extra Hydration Lotion 200ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101924, NULL, 'Vaseline Men Extra Hydration Lotion 400ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101925, NULL, 'Vasograin Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101926, NULL, 'Vatika Coconut Hair Oil 125ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101927, NULL, 'Vatika Conditioner 400ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101928, NULL, 'Vatika Hair Oil 250ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101929, NULL, 'Vatika Hair Oil Almond 300ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101930, NULL, 'Vatika Hair Oil Black Seed 300ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101931, NULL, 'Vatika Hair Oil Cactus 300ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101932, NULL, 'Vatika Hair Oil Coconut 200ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101933, NULL, 'Vatika Hair Oil Garlic 300ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101934, NULL, 'Vatika Shampoo 400ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101935, NULL, 'Veet Hair Removal 100ml', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101936, NULL, 'Veet Hair Removal 30g', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101937, NULL, 'Vental Inhaler', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101938, NULL, 'Ventolin Inhaler', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101939, NULL, 'Verapamil Tabs 40mg (Caveril)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:45', '2025-01-21 16:48:45', NULL, NULL, NULL),
(101940, NULL, 'Vicks Inhaler', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:46', '2025-01-21 16:48:46', NULL, NULL, NULL),
(101941, NULL, 'Vicks Kingo Lozenges', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:46', '2025-01-21 16:48:46', NULL, NULL, NULL),
(101942, NULL, 'Vicks Vapour Rub', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:46', '2025-01-21 16:48:46', NULL, NULL, NULL),
(101943, NULL, 'Vigor Doctor', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:46', '2025-01-21 16:48:46', NULL, NULL, NULL),
(101944, NULL, 'Viscid Susp 200ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:46', '2025-01-21 16:48:46', NULL, NULL, NULL),
(101945, NULL, 'Viscid Syrup 100ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:46', '2025-01-21 16:48:46', NULL, NULL, NULL),
(101946, NULL, 'Visionace Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:46', '2025-01-21 16:48:46', NULL, NULL, NULL),
(101947, NULL, 'Visking Lozengers', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:46', '2025-01-21 16:48:46', NULL, NULL, NULL),
(101948, NULL, 'Vitacaps Mega Caps', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:46', '2025-01-21 16:48:46', NULL, NULL, NULL),
(101949, NULL, 'Vitacee Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:46', '2025-01-21 16:48:46', NULL, NULL, NULL),
(101950, NULL, 'Vital Health Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:46', '2025-01-21 16:48:46', NULL, NULL, NULL),
(101951, NULL, 'Vitamin A Caps', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:46', '2025-01-21 16:48:46', NULL, NULL, NULL),
(101952, NULL, 'Vitamin B Caps', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:46', '2025-01-21 16:48:46', NULL, NULL, NULL),
(101953, NULL, 'Vitamin B Inj', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:46', '2025-01-21 16:48:46', NULL, NULL, NULL),
(101954, NULL, 'Vitamin B Syrup', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:46', '2025-01-21 16:48:46', NULL, NULL, NULL),
(101955, NULL, 'Vitamin B12 Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:46', '2025-01-21 16:48:46', NULL, NULL, NULL),
(101956, NULL, 'Vitamin B6 Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:46', '2025-01-21 16:48:46', NULL, NULL, NULL),
(101957, NULL, 'Vitamin C Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:46', '2025-01-21 16:48:46', NULL, NULL, NULL),
(101958, NULL, 'Vitamin K Inj', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:46', '2025-01-21 16:48:46', NULL, NULL, NULL),
(101959, NULL, 'Vitro Caps', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:46', '2025-01-21 16:48:46', NULL, NULL, NULL),
(101960, NULL, 'Volin Gel 30g', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:46', '2025-01-21 16:48:46', NULL, NULL, NULL),
(101961, NULL, 'Voltaren Retard Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:46', '2025-01-21 16:48:46', NULL, NULL, NULL),
(101962, NULL, 'Vomidoxine Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:46', '2025-01-21 16:48:46', NULL, NULL, NULL),
(101963, NULL, 'Vumtrex Syrup', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:46', '2025-01-21 16:48:46', NULL, NULL, NULL),
(101964, NULL, 'Vumtrex Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:46', '2025-01-21 16:48:46', NULL, NULL, NULL),
(101965, NULL, 'Water for Inj', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:46', '2025-01-21 16:48:46', NULL, NULL, NULL),
(101966, NULL, 'Water Guard Liquid', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:46', '2025-01-21 16:48:46', NULL, NULL, NULL),
(101967, NULL, 'Water Guard Tabs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:46', '2025-01-21 16:48:46', NULL, NULL, NULL),
(101968, NULL, 'Weak Iodine Solution', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:46', '2025-01-21 16:48:46', NULL, NULL, NULL),
(101969, NULL, 'Whitfields Oint 20g (Tube)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:46', '2025-01-21 16:48:46', NULL, NULL, NULL),
(101970, NULL, 'Whitfields Oint 40g (Tin)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:46', '2025-01-21 16:48:46', NULL, NULL, NULL),
(101971, NULL, 'Wix Body Oil', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:46', '2025-01-21 16:48:46', NULL, NULL, NULL),
(101972, NULL, 'Wix Serum', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:46', '2025-01-21 16:48:46', NULL, NULL, NULL),
(101973, NULL, 'Wix Soap', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:46', '2025-01-21 16:48:46', NULL, NULL, NULL),
(101974, NULL, 'Wrinkle Soap', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:46', '2025-01-21 16:48:46', NULL, NULL, NULL),
(101975, NULL, 'X-pel Spray', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:46', '2025-01-21 16:48:46', NULL, NULL, NULL),
(101976, NULL, 'Xylo Acino Nasal Spray 0.05%', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:46', '2025-01-21 16:48:46', NULL, NULL, NULL),
(101977, NULL, 'Xylo Acino Nasal Spray 0.1%', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:46', '2025-01-21 16:48:46', NULL, NULL, NULL),
(101978, NULL, 'Z O P (Zinc Oxide Plaster) 1.25cm', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:46', '2025-01-21 16:48:46', NULL, NULL, NULL),
(101979, NULL, 'Z O P (Zinc Oxide Plaster) 10cm', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:46', '2025-01-21 16:48:46', NULL, NULL, NULL),
(101980, NULL, 'Z O P (Zinc Oxide Plaster) 15cm', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:46', '2025-01-21 16:48:46', NULL, NULL, NULL),
(101981, NULL, 'Z O P (Zinc Oxide Plaster) 2.5cm', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:46', '2025-01-21 16:48:46', NULL, NULL, NULL),
(101982, NULL, 'Z O P (Zinc Oxide Plaster) 5cm', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:46', '2025-01-21 16:48:46', NULL, NULL, NULL),
(101983, NULL, 'Z O P (Zinc Oxide Plaster) 7.5cm', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:46', '2025-01-21 16:48:46', NULL, NULL, NULL),
(101984, NULL, 'Zecuf Cold Rub', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:46', '2025-01-21 16:48:46', NULL, NULL, NULL),
(101985, NULL, 'Zecuf Herbal Cough Lozenges', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:46', '2025-01-21 16:48:46', NULL, NULL, NULL),
(101986, NULL, 'Zecuf Herbal Cough Syrup', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:46', '2025-01-21 16:48:46', NULL, NULL, NULL),
(101987, NULL, 'Zeet Syrup', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:46', '2025-01-21 16:48:46', NULL, NULL, NULL),
(101988, NULL, 'Zenergy Syrup 100ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:46', '2025-01-21 16:48:46', NULL, NULL, NULL),
(101989, NULL, 'Zenilyn Syrup', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:46', '2025-01-21 16:48:46', NULL, NULL, NULL),
(101990, NULL, 'Zenkof Syrup', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:46', '2025-01-21 16:48:46', NULL, NULL, NULL),
(101991, NULL, 'Zentus Cough Syrup', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:46', '2025-01-21 16:48:46', NULL, NULL, NULL),
(101992, NULL, 'Zinc Chelated Caps (21st Century)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:46', '2025-01-21 16:48:46', NULL, NULL, NULL),
(101993, NULL, 'Zinc Suplphate Susp (Pedzinc)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:46', '2025-01-21 16:48:46', NULL, NULL, NULL),
(101994, NULL, 'Zinc Suplphate Tabs (Pedzinc)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:46', '2025-01-21 16:48:46', NULL, NULL, NULL),
(101995, NULL, 'Zincast Baby Cream', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:46', '2025-01-21 16:48:46', NULL, NULL, NULL),
(101996, NULL, 'Zn-Vital Syrup 100ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:46', '2025-01-21 16:48:46', NULL, NULL, NULL),
(101997, NULL, 'Zocin Susp 200mg/15ml', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:46', '2025-01-21 16:48:46', NULL, NULL, NULL),
(101998, NULL, 'Zolichek T Drop', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:46', '2025-01-21 16:48:46', NULL, NULL, NULL),
(101999, NULL, 'Zoo Friends Caps (21st Century)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:46', '2025-01-21 16:48:46', NULL, NULL, NULL),
(102000, NULL, 'Zwagra Tabs 50mg', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-01-21 16:48:46', '2025-01-21 16:48:46', NULL, NULL, NULL),
(102001, NULL, 'Albendazole Tabs 500mg (Barikimi)', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, '2025-02-10 20:49:13', '2025-02-10 20:49:13', NULL, NULL, NULL),
(102003, NULL, 'Uhai water 6ltrs', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, '2025-06-19 13:14:06', '2025-06-19 13:14:06', NULL, NULL, NULL),
(102006, '126152765971', 'Adictone', NULL, 2, NULL, NULL, NULL, '250gm', NULL, NULL, NULL, 100, 10000, 1, '2025-06-24 20:16:53', '2025-06-24 20:16:53', '23-32-fn-2', 'FOMI', '50kg');

-- --------------------------------------------------------

--
-- Table structure for table `inv_sales_invoices`
--

DROP TABLE IF EXISTS `inv_sales_invoices`;
CREATE TABLE IF NOT EXISTS `inv_sales_invoices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` varchar(45) NOT NULL,
  `quantity` decimal(10,2) DEFAULT NULL,
  `rate` decimal(20,2) DEFAULT NULL,
  `amount` decimal(20,2) DEFAULT NULL,
  `invoice_date` date DEFAULT NULL,
  `invoice_no` varchar(45) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `remarks` varchar(200) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inv_stock_adjustments`
--

DROP TABLE IF EXISTS `inv_stock_adjustments`;
CREATE TABLE IF NOT EXISTS `inv_stock_adjustments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `stock_id` int(11) NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `type` varchar(20) DEFAULT NULL,
  `reason` varchar(45) DEFAULT NULL,
  `description` varchar(100) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inv_stock_issues`
--

DROP TABLE IF EXISTS `inv_stock_issues`;
CREATE TABLE IF NOT EXISTS `inv_stock_issues` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `stock_id` int(11) DEFAULT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `status` int(11) DEFAULT NULL,
  `unit_cost` decimal(20,2) DEFAULT NULL,
  `sales_price` decimal(20,2) NOT NULL,
  `sub_total` decimal(20,2) DEFAULT NULL,
  `issue_no` varchar(50) NOT NULL,
  `Remarks` varchar(50) DEFAULT NULL,
  `issued_to` int(11) DEFAULT NULL,
  `created_at` date NOT NULL,
  `updated_at` datetime NOT NULL,
  `updated_by` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inv_stock_tracking`
--

DROP TABLE IF EXISTS `inv_stock_tracking`;
CREATE TABLE IF NOT EXISTS `inv_stock_tracking` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `stock_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `out_mode` varchar(45) DEFAULT NULL,
  `quantity` double DEFAULT NULL,
  `store_id` int(11) DEFAULT NULL,
  `updated_by` varchar(45) DEFAULT NULL,
  `updated_at` date DEFAULT NULL,
  `barcode` varchar(50) DEFAULT NULL,
  `movement` varchar(5) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `inv_stock_tracking`
--

INSERT INTO `inv_stock_tracking` (`id`, `stock_id`, `product_id`, `out_mode`, `quantity`, `store_id`, `updated_by`, `updated_at`, `barcode`, `movement`) VALUES
(1, 1, 100007, 'New Product Purchase', 20, 3, '17', '2025-02-04', NULL, 'IN'),
(2, 2, 100114, 'New Product Purchase', 23, 3, '17', '2025-02-04', NULL, 'IN'),
(3, 3, 100002, 'New Product Purchase', 3, 1, '17', '2025-02-04', NULL, 'IN'),
(4, 4, 100256, 'New Product Purchase', 4, 1, '17', '2025-02-04', NULL, 'IN'),
(5, 5, 100027, 'New Product Purchase', 1, 2, '17', '2025-02-04', NULL, 'IN'),
(6, 6, 100074, 'New Product Purchase', 10, 1, '17', '2025-02-10', NULL, 'IN'),
(7, 6, 100074, 'Cash Sales', 10, 1, '17', '2025-02-10', NULL, 'OUT'),
(8, 3, 100002, 'Cash Sales', 3, 1, '17', '2025-02-14', NULL, 'OUT'),
(9, 7, 100114, 'New Product Purchase', 120, 1, '17', '2025-02-14', NULL, 'IN'),
(10, 8, 100116, 'New Product Purchase', 30, 1, '17', '2025-02-14', NULL, 'IN'),
(11, 9, 100115, 'New Product Purchase', 25, 1, '17', '2025-02-14', NULL, 'IN'),
(12, 7, 100114, 'Cash Sales', 24, 1, '17', '2025-02-14', NULL, 'OUT'),
(13, 7, 100114, 'Cash Sales', 23, 1, '17', '2025-02-14', NULL, 'OUT'),
(14, 10, 100115, 'New Product Purchase', 100, 1, '17', '2025-06-19', NULL, 'IN'),
(15, 11, 100006, 'New Product Purchase', 18, 1, '17', '2025-06-19', NULL, 'IN'),
(16, 9, 100115, 'Cash Sales', 2, 1, '17', '2025-06-19', NULL, 'OUT'),
(17, 11, 100006, 'Cash Sales', 10, 1, '17', '2025-06-19', NULL, 'OUT'),
(18, 11, 100006, 'Cash Sales', 8, 1, '17', '2025-06-19', NULL, 'OUT'),
(19, 9, 100115, 'Cash Sales', 1, 1, '17', '2025-06-19', NULL, 'OUT'),
(20, 8, 100116, 'Cash Sales', 1, 1, '17', '2025-06-19', NULL, 'OUT'),
(21, 4, 100256, 'Cash Sales', 1, 1, '17', '2025-06-19', NULL, 'OUT'),
(22, 9, 100115, 'Cash Sales', 5, 1, '17', '2025-06-19', NULL, 'OUT'),
(23, 4, 100256, 'Cash Sales', 1, 1, '17', '2025-06-19', NULL, 'OUT'),
(24, 8, 100116, 'Cash Sales', 1, 1, '17', '2025-06-19', NULL, 'OUT'),
(25, 9, 100115, 'Cash Sales', 17, 1, '17', '2025-06-23', NULL, 'OUT'),
(26, 10, 100115, 'Cash Sales', 83, 1, '17', '2025-06-23', NULL, 'OUT'),
(27, 8, 100116, 'Cash Sales', 10, 1, '17', '2025-06-23', NULL, 'OUT'),
(28, 12, 102006, 'New Product Purchase', 10, 1, '17', '2025-06-26', NULL, 'IN');

-- --------------------------------------------------------

--
-- Table structure for table `inv_stock_transfers`
--

DROP TABLE IF EXISTS `inv_stock_transfers`;
CREATE TABLE IF NOT EXISTS `inv_stock_transfers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `stock_id` int(11) DEFAULT NULL,
  `transfer_no` varchar(50) DEFAULT NULL,
  `transfer_qty` decimal(10,2) DEFAULT NULL,
  `accepted_qty` decimal(10,2) DEFAULT NULL,
  `from_store` int(11) DEFAULT NULL,
  `to_store` int(11) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `remarks` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `evidence` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inv_stock_transfer_reserve`
--

DROP TABLE IF EXISTS `inv_stock_transfer_reserve`;
CREATE TABLE IF NOT EXISTS `inv_stock_transfer_reserve` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `StockID` varchar(50) NOT NULL,
  `ProductCode` varchar(50) NOT NULL,
  `TransferQty` decimal(10,2) NOT NULL,
  `TransferNo` varchar(50) NOT NULL,
  `BatchNumber` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inv_stores`
--

DROP TABLE IF EXISTS `inv_stores`;
CREATE TABLE IF NOT EXISTS `inv_stores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `inv_stores`
--

INSERT INTO `inv_stores` (`id`, `name`) VALUES
(1, 'DSM'),
(2, 'GODOWN'),
(3, 'ALL');

-- --------------------------------------------------------

--
-- Table structure for table `inv_sub_categories`
--

DROP TABLE IF EXISTS `inv_sub_categories`;
CREATE TABLE IF NOT EXISTS `inv_sub_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inv_suppliers`
--

DROP TABLE IF EXISTS `inv_suppliers`;
CREATE TABLE IF NOT EXISTS `inv_suppliers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `address` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `mobile` varchar(45) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `contact_person` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `inv_suppliers`
--

INSERT INTO `inv_suppliers` (`id`, `name`, `address`, `country`, `mobile`, `email`, `contact_person`) VALUES
(1, 'NONE', NULL, NULL, '0754 000 000', NULL, NULL),
(2, 'VUNJA BEI', 'DSM', NULL, '+255765432111', NULL, NULL),
(3, 'Salama', NULL, NULL, '+255714791924', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

DROP TABLE IF EXISTS `logs`;
CREATE TABLE IF NOT EXISTS `logs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `log_date` datetime NOT NULL,
  `table_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `log_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
CREATE TABLE IF NOT EXISTS `model_has_permissions` (
  `permission_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL,
  PRIMARY KEY (`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
CREATE TABLE IF NOT EXISTS `model_has_roles` (
  `role_id` int(10) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(6, 'App\\User', 12),
(6, 'App\\User', 13),
(6, 'App\\User', 14),
(7, 'App\\User', 15),
(8, 'App\\User', 16),
(8, 'App\\User', 0),
(8, 'App\\User', 18),
(8, 'App\\User', 19),
(7, 'App\\User', 22),
(8, 'App\\User', 24),
(8, 'App\\User', 26),
(8, 'App\\User', 23),
(8, 'App\\User', 20),
(9, 'App\\User', 21),
(8, 'App\\User', 27),
(7, 'App\\User', 28),
(8, 'App\\User', 29),
(8, 'App\\User', 25),
(8, 'App\\User', 17),
(7, 'App\\User', 30),
(9, 'App\\User', 32),
(8, 'App\\User', 33);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` bigint(20) UNSIGNED NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES
('afc2d0f8-a655-43d9-a167-41734f6b2e44', 'App\\Notifications\\StockNotification', 'App\\User', 17, '{\"data\":[4,0]}', NULL, '2025-06-29 19:42:11', '2025-06-29 19:42:11');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_number` varchar(45) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `ordered_by` int(11) NOT NULL,
  `ordered_at` datetime NOT NULL,
  `received_by` int(11) DEFAULT NULL,
  `received_at` datetime DEFAULT NULL,
  `Comment` varchar(255) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `total_vat` decimal(12,2) DEFAULT NULL,
  `total_amount` decimal(12,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_number`, `supplier_id`, `ordered_by`, `ordered_at`, `received_by`, `received_at`, `Comment`, `status`, `total_vat`, `total_amount`) VALUES
(1, '03F36E2B', 2, 17, '2025-06-19 00:00:00', NULL, NULL, 'dated', '1', 0.00, 2253000.00);

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

DROP TABLE IF EXISTS `order_details`;
CREATE TABLE IF NOT EXISTS `order_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `ordered_qty` int(11) NOT NULL,
  `received_qty` int(11) DEFAULT 0,
  `unit_price` decimal(12,2) NOT NULL,
  `discount` decimal(12,2) NOT NULL,
  `received_by` int(11) DEFAULT NULL,
  `received_at` datetime DEFAULT NULL,
  `item_status` varchar(15) DEFAULT NULL,
  `Remarks` varchar(100) DEFAULT NULL,
  `vat` decimal(12,2) DEFAULT NULL,
  `amount` decimal(12,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `order_details`
--

INSERT INTO `order_details` (`id`, `order_id`, `product_id`, `ordered_qty`, `received_qty`, `unit_price`, `discount`, `received_by`, `received_at`, `item_status`, `Remarks`, `vat`, `amount`) VALUES
(1, 1, 100115, 1000, 0, 2000.00, 0.00, NULL, NULL, NULL, NULL, 0.00, 2000000.00),
(2, 1, 100007, 100, 0, 2500.00, 0.00, NULL, NULL, NULL, NULL, 0.00, 250000.00),
(3, 1, 100006, 1, 0, 3000.00, 0.00, NULL, NULL, NULL, NULL, 0.00, 3000.00);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE IF NOT EXISTS `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_types`
--

DROP TABLE IF EXISTS `payment_types`;
CREATE TABLE IF NOT EXISTS `payment_types` (
  `id` int(11) NOT NULL,
  `name` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1033 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`, `category`) VALUES
(100, 'View Dashboard', 'web', '2019-10-17 20:25:55', '2019-10-17 20:25:55', 'MODULES'),
(101, 'View Cash Sales', 'web', '2019-10-17 20:25:55', '2019-10-17 20:25:55', 'SALES'),
(102, 'View Credit Sales', 'web', '2019-10-17 20:25:55', '2019-10-17 20:25:55', 'SALES'),
(103, 'View Sales History', 'web', '2019-10-17 20:25:55', '2019-10-17 20:25:55', 'SALES'),
(104, 'View Sales Orders', 'web', '2019-10-17 20:25:55', '2019-10-17 20:25:55', 'SALES'),
(105, 'View Sales Return', 'web', '2019-10-17 20:25:55', '2019-10-17 20:25:55', 'SALES'),
(106, 'View Sales Return Approval', 'web', '2019-10-17 20:25:55', '2019-10-17 20:25:55', 'SALES'),
(107, 'View Credit Payment', 'web', '2019-10-17 20:25:55', '2019-10-17 20:25:55', 'SALES'),
(108, 'View Credit Tracking', 'web', '2019-10-17 20:25:55', '2019-10-17 20:25:55', 'SALES'),
(110, 'View Customers', 'web', '2019-10-17 20:25:55', '2019-10-17 20:25:55', 'SALES'),
(111, 'Manage Customers', 'web', '2019-10-17 20:25:55', '2019-10-17 20:25:55', 'SALES'),
(200, 'View Sales', 'web', '2019-10-17 20:25:55', '2019-10-17 20:25:55', 'MODULES'),
(201, 'View Current Stock', 'web', '2019-10-17 20:25:55', '2019-10-17 20:25:55', 'INVENTORY'),
(202, 'View Price List', 'web', '2019-10-17 20:25:55', '2019-10-17 20:25:55', 'INVENTORY'),
(203, 'View Stock Adjustment', 'web', '2019-10-17 20:25:55', '2019-10-17 20:25:55', 'INVENTORY'),
(204, 'Stock Adjustment', 'web', '2019-10-17 20:25:55', '2019-10-17 20:25:55', 'INVENTORY'),
(205, 'View Outgoing Stock', 'web', '2019-10-17 20:25:55', '2019-10-17 20:25:55', 'INVENTORY'),
(206, 'View Product Ledger', 'web', '2019-10-17 20:25:55', '2019-10-17 20:25:55', 'INVENTORY'),
(207, 'View Daily Stock Count', 'web', '2019-10-17 20:25:55', '2019-10-17 20:25:55', 'INVENTORY'),
(208, 'View Inventory Count Sheet', 'web', '2019-10-17 20:25:55', '2019-10-17 20:25:55', 'INVENTORY'),
(209, 'View Stock Transfer', 'web', '2019-10-17 20:25:55', '2019-10-17 20:25:55', 'INVENTORY'),
(210, 'Transfer Acknowledgement', 'web', '2019-10-17 20:25:55', '2019-10-17 20:25:55', 'INVENTORY'),
(211, 'View Stock Transfer Re-Print', 'web', '2019-10-17 20:25:55', '2019-10-17 20:25:55', 'INVENTORY'),
(212, 'View Stock Transfer History', 'web', '2019-10-17 20:25:55', '2019-10-17 20:25:55', 'INVENTORY'),
(213, 'View Stock Issue', 'web', '2019-10-17 20:25:55', '2019-10-17 20:25:55', 'INVENTORY'),
(214, 'Issue Return', 'web', '2019-10-17 20:25:55', '2019-10-17 20:25:55', 'INVENTORY'),
(215, 'Issue Re-Print', 'web', '2019-10-17 20:25:55', '2019-10-17 20:25:55', 'INVENTORY'),
(216, 'Manage Stock Transfer', 'web', NULL, NULL, 'INVENTORY'),
(217, 'Manage Stock Issue', 'web', NULL, NULL, 'INVENTORY'),
(300, 'View Purchasing', 'web', '2019-10-17 20:25:55', '2019-10-17 20:25:55', 'MODULES'),
(301, 'View Goods Receiving', 'web', '2019-10-17 20:25:55', '2019-10-17 20:25:55', 'PURCHASING'),
(302, 'View Purchase Order', 'web', '2019-10-17 20:25:55', '2019-10-17 20:25:55', 'PURCHASING'),
(303, 'View Requisition', 'web', '2019-10-17 20:25:55', '2019-10-17 20:25:55', 'PURCHASING'),
(500, 'View Reports', 'web', '2019-10-17 20:25:55', '2019-10-17 20:25:55', 'REPORTS'),
(501, 'View Sales Reports', 'web', '2019-10-17 20:25:55', '2019-10-17 20:25:55', 'REPORTS'),
(502, 'View Inventory Reports', 'web', '2019-10-17 20:25:55', '2019-10-17 20:25:55', 'REPORTS'),
(503, 'View Expense Reports', 'web', '2019-10-17 20:25:55', '2019-10-17 20:25:55', 'REPORTS'),
(504, 'View Purchase Reports', 'web', '2019-10-17 20:25:55', '2019-10-17 20:25:55', 'REPORTS'),
(505, 'View Accounting Reports', 'web', NULL, NULL, 'REPORTS'),
(601, 'View Users', 'web', '2019-10-17 20:25:55', '2019-10-17 20:25:55', 'SETTINGS'),
(602, 'Manage Users', 'web', '2019-10-17 20:25:55', '2019-10-17 20:25:55', 'SETTINGS'),
(603, 'View Roles', 'web', '2019-10-17 20:25:55', '2019-10-17 20:25:55', 'SETTINGS'),
(604, 'Manage Roles', 'web', '2019-10-17 20:25:55', '2019-10-17 20:25:55', 'SETTINGS'),
(701, 'View Products', 'web', '2019-10-17 20:25:55', '2019-10-17 20:25:55', 'INVENTORY'),
(702, 'Manage Products', 'web', '2019-10-17 20:25:55', '2019-10-17 20:25:55', 'INVENTORY'),
(703, 'View Products Categories', 'web', '2019-10-17 20:25:55', '2019-10-17 20:25:55', 'INVENTORY'),
(704, 'Manage Products Categories', 'web', '2019-10-17 20:25:55', '2019-10-17 20:25:55', 'INVENTORY'),
(705, 'View Product Subcategories', 'web', '2019-10-17 20:25:55', '2019-10-17 20:25:55', 'INVENTORY'),
(706, 'Manage Product Subcategories', 'web', '2019-10-17 20:25:55', '2019-10-17 20:25:55', 'INVENTORY'),
(707, 'View Price Categories', 'web', '2019-10-17 20:25:55', '2019-10-17 20:25:55', 'INVENTORY'),
(708, 'Manage Price Categories', 'web', '2019-10-17 20:25:55', '2019-10-17 20:25:55', 'INVENTORY'),
(713, 'View Adjustment Reasons', 'web', '2019-10-17 20:25:55', '2019-10-17 20:25:55', 'INVENTORY'),
(714, 'Manage Adjustment Reasons', 'web', '2019-10-17 20:25:55', '2019-10-17 20:25:55', 'INVENTORY'),
(715, 'View Requisition Issue', 'web', '2019-10-17 20:25:55', '2019-10-17 20:25:55', 'PURCHASING'),
(716, 'View Suppliers', 'web', '2019-10-17 20:25:55', '2019-10-17 20:25:55', 'PURCHASING'),
(717, 'View Stores', 'web', '2019-10-17 20:25:55', '2019-10-17 20:25:55', 'SETTINGS'),
(718, 'Manage Stores', 'web', '2019-10-17 20:25:55', '2019-10-17 20:25:55', 'SETTINGS'),
(721, 'View Inventory', 'web', '2019-10-17 20:25:55', '2019-10-17 20:25:55', 'MODULES'),
(722, 'Manage Settings', 'web', '2019-10-17 20:25:55', '2019-10-17 20:25:55', 'SETTINGS'),
(800, 'View Data Import', 'web', NULL, NULL, 'INVENTORY'),
(900, 'View Accounting', 'web', NULL, NULL, 'MODULES'),
(901, 'Manage Current Stock', 'web', NULL, NULL, 'INVENTORY'),
(904, 'Manage Suppliers', 'web', NULL, NULL, 'PURCHASING'),
(906, 'View Requisitions Details', 'web', NULL, NULL, 'PURCHASING'),
(907, 'Create Requisitions', 'web', NULL, NULL, 'PURCHASING'),
(908, 'Delete Requisitions', 'web', NULL, NULL, 'PURCHASING'),
(909, 'Approve Requisitions', 'web', NULL, NULL, 'PURCHASING'),
(910, 'Print Requisitions', 'web', NULL, NULL, 'PURCHASING'),
(911, 'View Requisitions Issue', 'web', NULL, NULL, 'PURCHASING'),
(912, 'Manage Product Categories', 'web', NULL, NULL, 'INVENTORY'),
(913, 'View Alerts', 'web', '2024-11-11 19:35:44', '2024-11-11 19:35:44', 'SETTINGS'),
(914, 'View Security', 'web', '2024-11-11 20:00:28', '2024-11-11 20:00:28', 'SETTINGS'),
(916, 'View Tools', 'web', '2024-11-11 20:00:48', '2024-11-11 20:00:48', 'SETTINGS'),
(1004, 'View General', 'web', '2024-11-11 20:05:31', '2024-11-11 20:05:31', 'SETTINGS'),
(1005, 'View Reports', 'web', '2024-11-11 20:17:51', '2024-11-11 20:17:51', 'MODULES'),
(1006, 'View Sales Summary', 'web', '2024-11-15 16:49:39', '2024-11-15 16:49:39', 'DASHBOARD'),
(1007, 'View Purchasing Summary', 'web', '2024-11-15 16:49:56', '2024-11-15 16:49:56', 'DASHBOARD'),
(1008, 'View Inventory Summary', 'web', '2024-11-15 16:50:12', '2024-11-15 16:50:12', 'DASHBOARD'),
(1009, 'View Accounting Summary', 'web', '2024-11-15 16:50:28', '2024-11-15 16:50:28', 'DASHBOARD'),
(1010, 'View Order Receiving', 'web', '2024-11-15 19:11:38', '2024-11-15 19:11:38', 'PURCHASING'),
(1011, 'View Material Received', 'web', '2024-11-15 19:11:53', '2024-11-15 19:11:53', 'PURCHASING'),
(1012, 'View Order List', 'web', '2024-11-15 19:12:08', '2024-11-15 19:12:08', 'PURCHASING'),
(1013, 'View Requisition List', 'web', '2024-11-15 19:12:24', '2024-11-15 19:12:24', 'PURCHASING'),
(1014, 'View Invoice Receiving', 'web', '2024-11-15 19:14:30', '2024-11-15 19:14:30', 'PURCHASING'),
(1015, 'View Expenses', 'web', '2024-11-23 08:24:55', '2024-11-23 08:24:55', 'ACCOUNTING'),
(1016, 'Manage Expenses', 'web', NULL, NULL, 'ACCOUNTING'),
(1017, 'View Invoices', 'web', '2024-11-23 08:25:20', '2024-11-23 08:25:20', 'ACCOUNTING'),
(1018, 'Manage Invoices', 'web', '2024-11-23 08:25:36', '2024-11-23 08:25:36', 'ACCOUNTING'),
(1019, 'View Assets', 'web', '2024-11-23 08:27:22', '2024-11-23 08:27:22', 'ACCOUNTING'),
(1020, 'Manage Assets', 'web', '2024-11-23 08:27:38', '2024-11-23 08:27:38', 'ACCOUNTING'),
(1021, 'View Cash Flow', 'web', '2024-11-23 08:27:54', '2024-11-23 08:27:54', 'ACCOUNTING'),
(1022, 'Manage Cash Flow', 'web', '2024-11-23 08:29:21', '2024-11-23 08:29:21', 'ACCOUNTING'),
(1023, 'View Expense Categories', 'web', '2024-11-23 08:29:29', '2024-11-23 08:29:29', 'SETTINGS'),
(1024, 'Manage Expense Categories', 'web', '2024-11-23 08:29:43', '2024-11-23 08:29:43', 'ACCOUNTING'),
(1031, 'View Settings', 'web', NULL, NULL, 'MODULES'),
(1032, 'ADMIN', 'web', '2025-06-23 08:49:35', '2025-06-23 08:49:35', 'ACCOUNTING');

-- --------------------------------------------------------

--
-- Table structure for table `price_categories`
--

DROP TABLE IF EXISTS `price_categories`;
CREATE TABLE IF NOT EXISTS `price_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `type` varchar(5) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `price_categories`
--

INSERT INTO `price_categories` (`id`, `name`, `type`) VALUES
(1, 'RETAIL', 'CASH');

-- --------------------------------------------------------

--
-- Stand-in structure for view `product_ledger`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `product_ledger`;
CREATE TABLE IF NOT EXISTS `product_ledger` (
`id` int(11)
,`product_id` int(11)
,`product_name` varchar(100)
,`received` double
,`outgoing` double
,`user` varchar(45)
,`date` date
,`method` varchar(45)
,`movement` varchar(5)
,`store_id` int(11)
);

-- --------------------------------------------------------

--
-- Table structure for table `quote_items`
--

DROP TABLE IF EXISTS `quote_items`;
CREATE TABLE IF NOT EXISTS `quote_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `stock_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `quote_id` int(11) DEFAULT NULL,
  `amount` decimal(20,2) DEFAULT NULL,
  `vat` decimal(20,2) DEFAULT NULL,
  `price` decimal(20,2) DEFAULT NULL,
  `item_name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `requisitions`
--

DROP TABLE IF EXISTS `requisitions`;
CREATE TABLE IF NOT EXISTS `requisitions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `req_no` varchar(100) NOT NULL,
  `req_to` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0=pending, 1= Approved, 2 = Denied',
  `from_store` int(11) DEFAULT NULL,
  `to_store` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `requisition_details`
--

DROP TABLE IF EXISTS `requisition_details`;
CREATE TABLE IF NOT EXISTS `requisition_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `req_id` int(11) DEFAULT NULL,
  `product` int(11) DEFAULT NULL,
  `quantity` varchar(50) DEFAULT NULL,
  `quantity_given` varchar(50) DEFAULT NULL,
  `unit` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` varchar(100) DEFAULT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `description`, `guard_name`, `created_at`, `updated_at`) VALUES
(7, 'SALES', 'SALES', 'web', '2020-01-14 16:36:48', '2021-11-15 19:13:09'),
(8, 'ADMIN', 'ADMIN', 'web', '2020-12-02 15:45:00', '2024-10-16 17:18:57'),
(9, 'MANAGER', 'MANAGER', 'web', '2024-10-22 17:53:24', '2024-10-22 17:53:46'),
(10, 'CASHIER', 'CASHIER', 'web', '2024-10-22 17:55:41', '2024-10-22 17:55:41'),
(11, 'TECHNICIAN', 'TECHNICIAN', 'web', '2024-12-08 07:45:35', '2024-12-08 07:45:35');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
CREATE TABLE IF NOT EXISTS `role_has_permissions` (
  `permission_id` int(10) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(100, 10),
(104, 10),
(105, 10),
(106, 10),
(107, 10),
(111, 10),
(203, 10),
(209, 10),
(900, 9),
(905, 9),
(1006, 9),
(1007, 9),
(1008, 9),
(1009, 9),
(200, 9),
(201, 9),
(202, 9),
(203, 9),
(204, 9),
(205, 9),
(206, 9),
(207, 9),
(208, 9),
(209, 9),
(210, 9),
(211, 9),
(212, 9),
(213, 9),
(214, 9),
(215, 9),
(216, 9),
(217, 9),
(701, 9),
(702, 9),
(703, 9),
(704, 9),
(705, 9),
(706, 9),
(707, 9),
(708, 9),
(713, 9),
(714, 9),
(901, 9),
(912, 9),
(300, 9),
(301, 9),
(302, 9),
(303, 9),
(715, 9),
(716, 9),
(904, 9),
(906, 9),
(907, 9),
(908, 9),
(909, 9),
(910, 9),
(911, 9),
(500, 9),
(501, 9),
(502, 9),
(503, 9),
(504, 9),
(505, 9),
(100, 9),
(101, 9),
(102, 9),
(103, 9),
(104, 9),
(105, 9),
(106, 9),
(107, 9),
(108, 9),
(110, 9),
(111, 9),
(601, 9),
(602, 9),
(603, 9),
(604, 9),
(700, 9),
(717, 9),
(718, 9),
(719, 9),
(720, 9),
(721, 9),
(722, 9),
(800, 9),
(916, 9),
(100, 11),
(106, 11),
(110, 11),
(201, 11),
(202, 11),
(205, 11),
(208, 11),
(215, 11),
(300, 11),
(303, 11),
(1015, 7),
(1016, 7),
(1006, 7),
(1008, 7),
(1009, 7),
(201, 7),
(202, 7),
(207, 7),
(208, 7),
(701, 7),
(100, 7),
(200, 7),
(300, 7),
(721, 7),
(900, 7),
(303, 7),
(715, 7),
(907, 7),
(908, 7),
(1013, 7),
(101, 7),
(102, 7),
(103, 7),
(104, 7),
(105, 7),
(107, 7),
(108, 7),
(110, 7),
(111, 7),
(1015, 8),
(1016, 8),
(1017, 8),
(1018, 8),
(1024, 8),
(905, 8),
(1006, 8),
(1007, 8),
(1008, 8),
(1009, 8),
(201, 8),
(202, 8),
(203, 8),
(204, 8),
(205, 8),
(206, 8),
(207, 8),
(208, 8),
(209, 8),
(210, 8),
(211, 8),
(212, 8),
(213, 8),
(214, 8),
(215, 8),
(216, 8),
(217, 8),
(701, 8),
(702, 8),
(703, 8),
(704, 8),
(705, 8),
(706, 8),
(707, 8),
(708, 8),
(713, 8),
(714, 8),
(800, 8),
(901, 8),
(912, 8),
(100, 8),
(200, 8),
(300, 8),
(721, 8),
(900, 8),
(1005, 8),
(1031, 8),
(301, 8),
(302, 8),
(716, 8),
(904, 8),
(1010, 8),
(1011, 8),
(1012, 8),
(1014, 8),
(500, 8),
(501, 8),
(502, 8),
(503, 8),
(504, 8),
(505, 8),
(101, 8),
(102, 8),
(103, 8),
(104, 8),
(105, 8),
(106, 8),
(107, 8),
(108, 8),
(110, 8),
(111, 8),
(601, 8),
(602, 8),
(603, 8),
(604, 8),
(700, 8),
(717, 8),
(718, 8),
(722, 8),
(913, 8),
(914, 8),
(916, 8),
(1004, 8),
(1023, 8),
(1030, 8);

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

DROP TABLE IF EXISTS `sales`;
CREATE TABLE IF NOT EXISTS `sales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `receipt_number` varchar(45) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `price_category_id` int(11) DEFAULT NULL,
  `payment_type_id` int(11) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `receipt_number`, `customer_id`, `price_category_id`, `payment_type_id`, `date`, `created_by`) VALUES
(1, 'BC608631', 1, 1, NULL, '2025-02-10 00:00:00', 17),
(2, '63C69FC1', 1, 1, NULL, '2025-02-14 00:00:00', 17),
(3, 'A47BD073', 1, 1, NULL, '2025-02-14 00:00:00', 17),
(4, '1D2632EC', 1, 1, NULL, '2025-02-14 00:00:00', 17),
(5, '19478738', 1, 1, NULL, '2025-06-19 16:36:16', 17),
(6, 'FCB864', 1, 1, NULL, '2025-06-19 16:37:18', 17),
(7, '85E0F859', 1, 1, NULL, '2025-06-19 16:39:53', 17),
(8, 'CA53DAA8', 2, 1, NULL, '2025-06-19 16:41:50', 17),
(9, 'BF4BDFF8', 2, 1, NULL, '2025-06-23 14:34:16', 17);

-- --------------------------------------------------------

--
-- Table structure for table `sales_credits`
--

DROP TABLE IF EXISTS `sales_credits`;
CREATE TABLE IF NOT EXISTS `sales_credits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sale_id` int(11) DEFAULT NULL,
  `paid_amount` decimal(20,2) DEFAULT NULL,
  `balance` decimal(20,2) DEFAULT NULL,
  `grace_period` int(11) DEFAULT NULL,
  `remark` varchar(255) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `sales_credits`
--

INSERT INTO `sales_credits` (`id`, `sale_id`, `paid_amount`, `balance`, `grace_period`, `remark`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 8, 10000.00, 7000.00, 21, 'jjjjj', 17, 17, '2025-06-19 13:41:50', '2025-06-19 13:41:50');

-- --------------------------------------------------------

--
-- Table structure for table `sales_details`
--

DROP TABLE IF EXISTS `sales_details`;
CREATE TABLE IF NOT EXISTS `sales_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sale_id` int(11) DEFAULT NULL,
  `stock_id` int(11) DEFAULT NULL,
  `quantity` decimal(11,2) DEFAULT NULL,
  `price` decimal(20,2) DEFAULT NULL,
  `vat` decimal(20,2) DEFAULT NULL,
  `amount` decimal(20,2) DEFAULT NULL,
  `discount` decimal(20,2) DEFAULT 0.00,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `sales_details`
--

INSERT INTO `sales_details` (`id`, `sale_id`, `stock_id`, `quantity`, `price`, `vat`, `amount`, `discount`, `updated_at`, `updated_by`, `status`) VALUES
(1, 1, 6, 10.00, 35000.00, 0.00, 35000.00, 0.00, NULL, NULL, 1),
(2, 2, 3, 3.00, 0.00, 0.00, 0.00, 0.00, NULL, NULL, 1),
(3, 3, 7, 24.00, 60000.00, 0.00, 60000.00, 0.00, NULL, NULL, 1),
(4, 4, 7, 23.00, 57500.00, 0.00, 57500.00, 0.00, NULL, NULL, 1),
(5, 5, 9, 2.00, 8000.00, 0.00, 8000.00, 413.79, NULL, NULL, 1),
(6, 5, 11, 10.00, 50000.00, 0.00, 50000.00, 2586.21, NULL, NULL, 1),
(7, 6, 11, 8.00, 40000.00, 0.00, 40000.00, 2000.00, NULL, NULL, 1),
(8, 7, 9, 1.00, 4000.00, 0.00, 4000.00, 0.00, NULL, NULL, 1),
(9, 7, 8, 1.00, 0.00, 0.00, 0.00, 0.00, NULL, NULL, 1),
(10, 7, 4, 1.00, 0.00, 0.00, 0.00, 0.00, NULL, NULL, 1),
(11, 8, 9, 5.00, 20000.00, 0.00, 20000.00, 3000.00, NULL, NULL, 1),
(12, 8, 4, 1.00, 0.00, 0.00, 0.00, 0.00, NULL, NULL, 1),
(13, 8, 8, 1.00, 0.00, 0.00, 0.00, 0.00, NULL, NULL, 1),
(14, 9, 9, 17.00, 68000.00, 0.00, 68000.00, 3400.00, NULL, NULL, 1),
(15, 9, 10, 83.00, 332000.00, 0.00, 332000.00, 16600.00, NULL, NULL, 1),
(16, 9, 8, 10.00, 0.00, 0.00, 0.00, 0.00, NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `sales_orders`
--

DROP TABLE IF EXISTS `sales_orders`;
CREATE TABLE IF NOT EXISTS `sales_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `crd` varchar(100) NOT NULL,
  `order_number` varchar(100) DEFAULT NULL,
  `order_date` datetime NOT NULL,
  `ordered_by` int(11) NOT NULL,
  `Remarks` varchar(200) DEFAULT NULL,
  `product_id` varchar(100) NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `unit_cost` decimal(20,2) NOT NULL,
  `sub_total` decimal(20,2) NOT NULL,
  `customer_name` varchar(100) DEFAULT NULL,
  `stock_id` int(11) NOT NULL,
  `Status` varchar(50) DEFAULT NULL,
  `Type` varchar(45) NOT NULL,
  `sales_price` decimal(20,2) NOT NULL,
  `sale_type` varchar(45) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sales_orders_payments`
--

DROP TABLE IF EXISTS `sales_orders_payments`;
CREATE TABLE IF NOT EXISTS `sales_orders_payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_number` varchar(50) NOT NULL,
  `amount_paid` decimal(20,2) NOT NULL,
  `received_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sales_orders_summaries`
--

DROP TABLE IF EXISTS `sales_orders_summaries`;
CREATE TABLE IF NOT EXISTS `sales_orders_summaries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_number` varchar(50) NOT NULL,
  `order_date` datetime NOT NULL,
  `amount_total` decimal(20,2) NOT NULL,
  `amount_paid` decimal(20,2) NOT NULL,
  `balance` decimal(20,2) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `crn` varchar(50) NOT NULL,
  `status` varchar(50) NOT NULL,
  `ordered_by` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sales_prices`
--

DROP TABLE IF EXISTS `sales_prices`;
CREATE TABLE IF NOT EXISTS `sales_prices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `stock_id` int(11) DEFAULT NULL,
  `price` double DEFAULT NULL,
  `price_category_id` int(11) NOT NULL,
  `status` bit(1) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `sales_prices`
--

INSERT INTO `sales_prices` (`id`, `stock_id`, `price`, `price_category_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 0, 1, b'1', '2025-02-04 14:02:05', NULL),
(2, 2, 0, 1, b'1', '2025-02-04 14:02:05', NULL),
(3, 3, 0, 1, b'1', '2025-02-04 14:02:22', NULL),
(4, 4, 0, 1, b'1', '2025-02-04 14:02:22', NULL),
(5, 5, 0, 1, b'1', '2025-02-04 14:02:25', NULL),
(6, 6, 3500, 1, b'1', '2025-02-10 15:02:13', NULL),
(7, 7, 2500, 1, b'1', '2025-02-14 11:02:14', NULL),
(8, 8, 0, 1, b'1', '2025-02-14 11:02:13', NULL),
(9, 9, 0, 1, b'1', '2025-02-14 11:02:13', NULL),
(10, 10, 4000, 1, b'1', '2025-06-19 16:06:19', NULL),
(11, 11, 5000, 1, b'1', '2025-06-19 16:06:19', NULL),
(12, 12, 3000, 1, b'1', '2025-06-26 18:06:45', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sales_quotes`
--

DROP TABLE IF EXISTS `sales_quotes`;
CREATE TABLE IF NOT EXISTS `sales_quotes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `quote_number` varchar(45) DEFAULT NULL,
  `payment_type_id` int(11) DEFAULT NULL,
  `price_category_id` int(11) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `remark` varchar(255) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `created_by` varchar(45) DEFAULT NULL,
  `store_id` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `sales_quotes`
--

INSERT INTO `sales_quotes` (`id`, `quote_number`, `payment_type_id`, `price_category_id`, `customer_id`, `remark`, `date`, `created_by`, `store_id`) VALUES
(1, '57E60087', NULL, 1, 2, 'jjkuidgv', '2025-06-19 16:42:39', '17', 1),
(2, 'F6F1FC75', NULL, 1, 2, NULL, '2025-06-19 16:43:53', '17', 1),
(3, 'F6C171B3', NULL, 1, 2, 'khidh jbjk', '2025-06-19 16:44:58', '17', 1),
(4, 'F8287A6D', NULL, 1, 1, NULL, '2025-06-19 16:45:42', '17', 1),
(5, '8B9AB957', NULL, 1, 1, NULL, '2025-06-19 16:46:09', '17', 1),
(6, '250F1AE4', NULL, 1, 1, 'Addghu', '2025-06-19 16:46:31', '17', 1);

-- --------------------------------------------------------

--
-- Table structure for table `sales_quote_details`
--

DROP TABLE IF EXISTS `sales_quote_details`;
CREATE TABLE IF NOT EXISTS `sales_quote_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `quote_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(20,2) DEFAULT NULL,
  `vat` decimal(20,2) DEFAULT NULL,
  `amount` decimal(20,2) DEFAULT NULL,
  `discount` decimal(20,2) DEFAULT 0.00,
  `updated_at` datetime DEFAULT NULL,
  `status` int(11) DEFAULT 1,
  `updated_by` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `sales_quote_details`
--

INSERT INTO `sales_quote_details` (`id`, `quote_id`, `product_id`, `quantity`, `price`, `vat`, `amount`, `discount`, `updated_at`, `status`, `updated_by`) VALUES
(1, 1, 100115, 10000, 40000000.00, 0.00, 40000000.00, 20000.00, NULL, 1, NULL),
(2, 1, 100256, 60, 0.00, 0.00, 0.00, 0.00, NULL, 1, NULL),
(3, 1, 100116, 1, 0.00, 0.00, 0.00, 0.00, NULL, 1, NULL),
(4, 2, 100115, 10000, 40000000.00, 0.00, 40000000.00, 25000.00, NULL, 1, NULL),
(5, 3, 100115, 1000, 4000000.00, 0.00, 4000000.00, 10000.00, NULL, 1, NULL),
(6, 4, 100115, 1, 4000.00, 0.00, 4000.00, 1000.00, NULL, 1, NULL),
(7, 5, 100115, 1, 4000.00, 0.00, 4000.00, 500.00, NULL, 1, NULL),
(8, 6, 100115, 1, 4000.00, 0.00, 4000.00, 1000.00, NULL, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sales_returns`
--

DROP TABLE IF EXISTS `sales_returns`;
CREATE TABLE IF NOT EXISTS `sales_returns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sale_detail_id` int(11) DEFAULT NULL,
  `quantity` decimal(11,2) DEFAULT NULL,
  `reason` varchar(100) DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `updated_by` varchar(100) DEFAULT NULL,
  `status` int(11) DEFAULT 1,
  `date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `sale_details`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `sale_details`;
CREATE TABLE IF NOT EXISTS `sale_details` (
`sale_id` int(11)
,`sale_details_id` int(11)
,`receipt_number` varchar(45)
,`customer_id` int(11)
,`customer_name` varchar(50)
,`price_category_id` int(11)
,`price_category` varchar(45)
,`stock_id` int(11)
,`product_id` int(11)
,`product_name` varchar(100)
,`category` varchar(45)
,`quantity` decimal(11,2)
,`price` decimal(20,2)
,`vat` decimal(20,2)
,`amount` decimal(20,2)
,`discount` decimal(20,2)
,`sold_at` datetime
,`sold_by_id` int(11)
,`sold_by` varchar(100)
,`updated_at` datetime
,`updated_by_id` int(11)
,`updated_by` varchar(100)
,`status` int(11)
);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
CREATE TABLE IF NOT EXISTS `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `display_name` varchar(45) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=126 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `name`, `display_name`, `value`, `updated_by`, `updated_at`) VALUES
(100, 'business_name', 'Business Name', 'Bens Agrostar Co. Ltd', 17, '2024-10-15 18:18:24'),
(101, 'registration_number', 'Registration Number', '10288804', 17, '2024-10-15 18:48:55'),
(102, 'tin_number', 'TIN Number', '111-222-333', 17, '2024-10-15 18:49:07'),
(103, 'vrn_number', 'VRN Number', '07-804-897', 17, '2024-10-15 18:49:25'),
(104, 'slogan', 'Slogan', 'Our goals is customer satisfaction', 17, '2024-10-18 15:06:34'),
(105, 'logo', 'Logo', 'php6J3S8Q.png', 18, '2024-10-17 03:26:06'),
(106, 'address', 'Address', 'Tanzania, Dar es salaam Near Mlimani City Mall', 17, '2024-10-16 16:25:02'),
(107, 'phone', 'Phone', '+255 753 900 353 / +255 718 768 987', 17, '2024-10-16 16:25:38'),
(108, 'email', 'Email', 'info@bensagrostar.com', 17, '2024-10-15 18:18:53'),
(109, 'website', 'Website', 'www.bensagrostar.com', 17, '2024-10-15 18:18:36'),
(110, 'make_batch_number_mandatory', 'Make Batch Number Mandatory', 'NO', 17, '2024-12-11 20:42:15'),
(111, 'enable_discount', 'Enable Sales Discount', 'YES', 17, '2024-12-12 18:53:00'),
(112, 'enable_change', 'Enable Paid / Change', 'NO', 29, '2024-12-12 18:34:30'),
(114, 'enable_back_date_sale', 'Enable Back Date Sales', 'NO', 17, '2025-06-19 13:29:43'),
(115, 'make_invoice_number_mandatory', 'Make Invoice Number Mandatory', 'NO', 12, '2024-03-20 17:16:45'),
(117, 'receipt_printing', 'Receipt Printing', 'YES', 17, '2024-10-22 17:33:27'),
(119, 'receipt_size', 'Receipt Size', '80mm Thermal Paper', 17, '2025-06-19 13:38:08'),
(120, 'vat', 'VAT Percent', '0', 29, '2024-12-12 18:35:43'),
(121, 'support_multstore', 'Support Multi-Branches', 'NO', 17, '2024-12-11 20:56:01'),
(122, 'default_store_id', 'Default Branch', 'GODOWN', 17, '2024-12-12 17:57:35'),
(123, 'enable_expire_date', 'Enable Expire Date', 'NO', 23, '2024-12-09 22:44:11'),
(124, 'fixed_price', 'Fixed Price', 'NO', 17, '2024-12-12 18:53:21'),
(125, 'defaulr_sale_type', 'Default Price Category', 'RETAIL', 17, '2024-12-12 17:56:49');

-- --------------------------------------------------------

--
-- Stand-in structure for view `stock_details`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `stock_details`;
CREATE TABLE IF NOT EXISTS `stock_details` (
`id` int(11)
,`product_id` int(11)
,`product_name` varchar(100)
,`category` varchar(45)
,`category_id` int(11)
,`expiry_date` date
,`quantity` decimal(11,2)
,`unit_cost` double
,`batch_number` varchar(45)
,`shelf_number` varchar(45)
,`store_id` int(11)
,`store` varchar(100)
,`sales_id` int(11)
,`selling_price` double
,`price_category_id` int(11)
,`price_category_name` varchar(45)
,`created_at` datetime
,`updated_at` datetime
);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_assets`
--

DROP TABLE IF EXISTS `tbl_assets`;
CREATE TABLE IF NOT EXISTS `tbl_assets` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `serial_number` varchar(255) DEFAULT NULL,
  `category_id` int(11) NOT NULL,
  `location_id` int(11) NOT NULL,
  `assigned_user_id` int(11) DEFAULT NULL,
  `value` decimal(10,2) DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'available',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_assets_categories`
--

DROP TABLE IF EXISTS `tbl_assets_categories`;
CREATE TABLE IF NOT EXISTS `tbl_assets_categories` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_assets_locations`
--

DROP TABLE IF EXISTS `tbl_assets_locations`;
CREATE TABLE IF NOT EXISTS `tbl_assets_locations` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_budgets`
--

DROP TABLE IF EXISTS `tbl_budgets`;
CREATE TABLE IF NOT EXISTS `tbl_budgets` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `category` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `period` enum('monthly','yearly') NOT NULL DEFAULT 'monthly',
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_budget_categories`
--

DROP TABLE IF EXISTS `tbl_budget_categories`;
CREATE TABLE IF NOT EXISTS `tbl_budget_categories` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_requisitions`
--

DROP TABLE IF EXISTS `tbl_requisitions`;
CREATE TABLE IF NOT EXISTS `tbl_requisitions` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `requisition_number` varchar(255) NOT NULL,
  `requisition_date` date NOT NULL,
  `requester_id` bigint(20) UNSIGNED NOT NULL,
  `approver_id` bigint(20) UNSIGNED DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `currency` varchar(3) NOT NULL DEFAULT 'TZS',
  `remark` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `approval_status` varchar(255) DEFAULT 'pending',
  `priority` varchar(255) DEFAULT NULL,
  `required_delivery_date` date DEFAULT NULL,
  `delivery_location` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transporters`
--

DROP TABLE IF EXISTS `transporters`;
CREATE TABLE IF NOT EXISTS `transporters` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `registration_number` varchar(255) NOT NULL,
  `business_type` enum('individual','company','subcontractor') NOT NULL,
  `tin_number` varchar(255) DEFAULT NULL,
  `national_id` varchar(255) DEFAULT NULL,
  `contact_person` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `physical_address` text NOT NULL,
  `region` varchar(255) NOT NULL,
  `district` varchar(255) NOT NULL,
  `postal_address` varchar(255) DEFAULT NULL,
  `transport_type` enum('road','air','sea','rail','other') NOT NULL,
  `other_transport_type` varchar(255) DEFAULT NULL,
  `number_of_vehicles` int(11) DEFAULT 0,
  `vehicle_types` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`vehicle_types`)),
  `average_capacity` decimal(10,2) DEFAULT NULL,
  `vehicle_registration_numbers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`vehicle_registration_numbers`)),
  `total_drivers` int(11) DEFAULT 0,
  `driver_licensing_status` varchar(255) DEFAULT NULL,
  `insurance_coverage` varchar(255) DEFAULT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `account_number` varchar(255) DEFAULT NULL,
  `payment_terms` varchar(255) DEFAULT NULL,
  `preferred_payment_method` varchar(255) DEFAULT NULL,
  `contract_start_date` date DEFAULT NULL,
  `contract_expiry_date` date DEFAULT NULL,
  `rate_per_km` decimal(10,2) DEFAULT NULL,
  `rate_per_trip` decimal(10,2) DEFAULT NULL,
  `rate_per_tonne` decimal(10,2) DEFAULT NULL,
  `agreed_routes` text DEFAULT NULL,
  `status` enum('active','pending_approval','suspended','blacklisted') NOT NULL DEFAULT 'pending_approval',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `transporters_registration_number_unique` (`registration_number`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transporters`
--

INSERT INTO `transporters` (`id`, `name`, `registration_number`, `business_type`, `tin_number`, `national_id`, `contact_person`, `phone`, `email`, `physical_address`, `region`, `district`, `postal_address`, `transport_type`, `other_transport_type`, `number_of_vehicles`, `vehicle_types`, `average_capacity`, `vehicle_registration_numbers`, `total_drivers`, `driver_licensing_status`, `insurance_coverage`, `bank_name`, `account_number`, `payment_terms`, `preferred_payment_method`, `contract_start_date`, `contract_expiry_date`, `rate_per_km`, `rate_per_trip`, `rate_per_tonne`, `agreed_routes`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'Jambo Logistics Ltd', 'TZ123456', 'company', '123-456-789', '1234567890123456789', 'Frank Lawrent', '+255712345678', 'info@jambologistics.co.tz', 'P.O. Box 1234, Dar es Salaam', 'Dar es Salaam', 'Ilala', 'P.O. Box 1234', 'road', NULL, 5, '[\"Truck\", \"Van\", \"Pickup\"]', 10.50, '[\"T123ABC\", \"T124ABC\", \"T125ABC\"]', 8, 'All licensed', 'Comprehensive', 'CRDB Bank', '0152034567890', 'Net 30', 'bank', '2023-01-01', '2024-12-31', 2.50, 50000.00, 15000.00, 'Dar to Mwanza, Dar to Arusha', 'active', 'Reliable transporter with good track record', '2025-06-25 12:25:34', '2025-06-25 12:25:34'),
(2, 'Jensen Logistics Ltd', 'REG123456', 'company', 'TIN987654321', 'NIDA12345678', 'Mr. John Doe', '+255712345678', 'info@jensenlogistics.co.tz', 'Plot 45, Mandela Road', 'Dar es Salaam', 'Kinondoni', 'P.O. Box 5678', 'road', NULL, 5, '[\"Truck\", \"Van\", \"Container\"]', 10.50, '[\"T123ABC\", \"T456DEF\", \"T789GHI\"]', 12, 'Valid', 'Comprehensive', 'CRDB Bank', '0123456789012', 'Net 30', 'bank', '2025-01-01', '2025-12-31', 1500.00, 75000.00, 20000.00, 'Dar - Morogoro - Dodoma', 'active', 'Long term transporter, handles fertilizer consignments.', '2025-06-25 12:26:19', '2025-06-25 12:26:19'),
(3, 'JensTech', '123455567', 'company', NULL, NULL, 'Frank Lawrent', '0774566760', 'frenklddddddent12@gmail.com', 'Dar es Salaam', 'Arusha', 'NJIRO', NULL, 'sea', NULL, 1, NULL, NULL, NULL, 0, NULL, 'Comprehensive', 'NMB', '123456789', NULL, NULL, '2025-06-25', NULL, NULL, NULL, NULL, NULL, 'active', 'Hello', '2025-06-25 14:05:26', '2025-06-25 14:05:26'),
(4, 'Trevor Macias', '1235567432', 'company', NULL, NULL, 'Trevor Macias', '0774566766', 'patient@hms.com', 'Kinondoni\r\nEst libero dignissim', 'Arusha', 'NJIRO', NULL, 'rail', NULL, 3, NULL, NULL, NULL, 0, NULL, 'Third Party', 'DTB', '98765438876', NULL, NULL, '2025-06-25', NULL, NULL, NULL, NULL, NULL, 'active', 'Class A', '2025-06-25 14:57:06', '2025-06-25 14:57:06'),
(5, 'Trevor MaciOOO', '12345678', 'company', '1234567890', NULL, 'BHGGF', '0774566760', 'frenklddddddent72@gmail.com', 'ASDFGHJ', 'DSM', 'QWWEE', NULL, 'road', NULL, 1, NULL, NULL, NULL, 0, NULL, 'Comprehensive', 'DTB', '9876543', NULL, NULL, '2025-06-26', NULL, NULL, NULL, NULL, NULL, 'active', 'ASDFGHJKL', '2025-06-25 14:58:39', '2025-06-25 14:58:39'),
(7, 'Trevor MaciOOO', '123456700', 'company', '1234567890', NULL, 'BHGGF', '0774566763', 'frenklddddddent0002@gmail.com', 'ASDFGGHHHRE', 'Kilimanjaro', 'QWWEE', NULL, 'road', NULL, 1, NULL, NULL, NULL, 0, NULL, 'Comprehensive', 'DTB', '98765400', NULL, NULL, '2025-06-26', NULL, NULL, NULL, NULL, NULL, 'active', 'ASDFGHJKL', '2025-06-25 15:01:58', '2025-06-25 15:01:58'),
(8, 'Frank Lawrent Jensen', '5564321', 'company', '213345677876', NULL, 'Lawrent Gervas', '0774566709', 'frenklawrent@gmail.com', 'Dar es Salaam', 'Shinyanga', 'Kahama', NULL, 'sea', NULL, 6, NULL, NULL, NULL, 0, NULL, 'Comprehensive', 'crdb', '887765443211', NULL, NULL, '2025-06-23', NULL, NULL, NULL, NULL, NULL, 'active', 'Good Status', '2025-06-25 22:04:38', '2025-06-25 22:04:38'),
(9, 'Victoria Lawrent', '556432187764432', 'individual', '12345678933200', NULL, 'Frank Lawrent', '07745667332', 'vickient12@gmail.com', 'Dar es Salaam', 'Shinyanga', 'Kahama', NULL, 'road', NULL, 4, NULL, NULL, NULL, 0, NULL, 'Comprehensive', 'DTB', '23345432', NULL, NULL, '2025-07-03', NULL, NULL, NULL, NULL, NULL, 'active', 'Employed', '2025-06-25 22:20:17', '2025-06-25 22:20:17'),
(10, 'Juma Juku', '1234567800222', 'company', '2133456778762276', NULL, 'Frank Lawrent', '0774566711', 'frenklawrent111@gmail.com', 'Dar es Salaam', 'Arusha', 'Njiro', NULL, 'air', NULL, 3, NULL, NULL, NULL, 0, NULL, 'Comprehensive', 'Exim Bank', '987654388700', NULL, NULL, '2025-06-26', NULL, NULL, NULL, NULL, NULL, 'active', 'Good Driver', '2025-06-26 11:50:57', '2025-06-26 11:50:57');

-- --------------------------------------------------------

--
-- Table structure for table `transport_orders`
--

DROP TABLE IF EXISTS `transport_orders`;
CREATE TABLE IF NOT EXISTS `transport_orders` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_number` varchar(255) NOT NULL,
  `transporter_id` bigint(20) UNSIGNED NOT NULL,
  `pickup_location` varchar(255) NOT NULL,
  `delivery_location` varchar(255) NOT NULL,
  `pickup_date` datetime NOT NULL,
  `delivery_date` date NOT NULL,
  `product` varchar(255) NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `unit` varchar(255) NOT NULL,
  `priority` varchar(255) NOT NULL,
  `assigned_vehicle_id` bigint(20) UNSIGNED DEFAULT NULL,
  `transport_rate` decimal(10,2) NOT NULL,
  `advance_payment` decimal(10,2) DEFAULT 0.00,
  `payment_method` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `transport_orders_order_number_unique` (`order_number`),
  KEY `transport_orders_transporter_id_foreign` (`transporter_id`),
  KEY `transport_orders_assigned_vehicle_id_foreign` (`assigned_vehicle_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transport_orders`
--

INSERT INTO `transport_orders` (`id`, `order_number`, `transporter_id`, `pickup_location`, `delivery_location`, `pickup_date`, `delivery_date`, `product`, `quantity`, `unit`, `priority`, `assigned_vehicle_id`, `transport_rate`, `advance_payment`, `payment_method`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'TR-000001', 2, 'qwertyu', 'HHHH', '2025-06-27 00:00:00', '2025-08-02', 'hello', 2000.00, 'tons', 'urgent', 2, 99999999.99, 1000000.00, 'bank_transfer', 'dispatched', 'Testing', '2025-06-26 13:58:33', '2025-06-26 13:58:33'),
(2, 'TR-000002', 9, 'Arusha', 'Kahama', '2025-06-26 00:00:00', '2025-07-12', 'e4erwerqwerqwerqwerfqwerqwe', 20.00, 'tons', 'normal', 1, 5000000.00, 200000.00, 'cheque', 'draft', 'ertwertwertw', '2025-06-26 15:08:51', '2025-06-26 15:08:51');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `position` varchar(100) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `mobile` varchar(45) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `last_login_ip` varchar(45) DEFAULT NULL,
  `store_id` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `position`, `email`, `mobile`, `password`, `status`, `remember_token`, `created_at`, `updated_at`, `last_login_at`, `last_login_ip`, `store_id`) VALUES
(17, 'APOTEk', 'ADMIN', 'apoteksystems@gmail.com', '0785 744 955', '$2y$10$NTkYOx2Lp0DSaY1VRBe4CuhjH698Q94vJ6Elm60XTHU5q7vejDWMa', 1, '1pHfLHqKKJZY2srrHh1rojVrSUyQTXVdNhsMi6HjnoqEzuUXxJ6vWkyHykX9', '2024-03-23 16:07:55', '2024-12-12 18:27:07', NULL, NULL, 1),
(25, 'Peter Mtavangu', NULL, 'petermtavangu@bensagrostar.com', NULL, '$2y$10$cLSbrrdgT3GuBaIuQm3qh.B5cdjnp2XGwDhsIOKTWqIfWOF.O3NPS', 1, 'S7vK096B1eQb4usZlfe3koKh8Yg0oeMYFeUWojhC8NYk1hT6xQ5uKjiNPfkn', '2024-12-04 21:29:54', '2024-12-12 18:27:00', NULL, NULL, 1),
(28, 'DSM Sales', 'Sales', 'dsmsales@bensagrostar.com', '0754000000', '$2y$10$lpJ3p42S.Awkf4O.b5Xg5eYqH33Qc8Jhco9qijz0XbhMB0ZV0j5ZC', 1, NULL, '2024-12-12 16:21:26', '2024-12-12 16:21:26', NULL, NULL, 2),
(29, 'Joseph Mtafya', 'Accountant', 'joseph@bensagrostar.com', NULL, '$2y$10$w50FxHEMNGxeCRrYcOoZr.e2/HR03NeO6B10iLGkJ5UnsWlycezMq', 1, 'GXPOLcPcLpWPq56gu4oiADqSYw9lemVbcakAqoLmsTW7GCyxWTo5Nr7ILObs', '2024-12-12 18:24:53', '2024-12-12 18:26:49', NULL, NULL, 1),
(30, 'Vanessa', 'Sales', 'vanessa@bensagrostar.com', NULL, '$2y$10$hO.Pzfx3rqZsaXyc/ryfHeOau39paCN.Mb15VcZVHvNzB4lBs/Ft6', 1, NULL, '2024-12-12 18:39:26', '2024-12-12 18:39:26', NULL, NULL, 1),
(32, 'Manager', NULL, 'manager@gmail.com', NULL, '$2y$10$LdnfNgyqLTAZSitB22lI1uKEaeNIngaBjEqUs0ZBpHZDLoQadLBDu', 1, NULL, '2025-06-23 08:11:29', '2025-06-23 08:11:29', NULL, NULL, 3),
(33, 'Hamisi Kibonde', NULL, 'hamisi.kibonde@gmail.com', NULL, '$2y$10$EUnHtD0rAl.WQT1/.f4LwuMe0oVhpfcdRkl4jSqcmKJynxKqVmUPK', 1, NULL, '2025-06-23 14:42:30', '2025-06-23 14:42:30', NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `vehicles`
--

DROP TABLE IF EXISTS `vehicles`;
CREATE TABLE IF NOT EXISTS `vehicles` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `plate_number` varchar(255) NOT NULL,
  `transporter_id` bigint(20) UNSIGNED NOT NULL,
  `vehicle_type` varchar(255) NOT NULL,
  `capacity` decimal(8,2) NOT NULL COMMENT 'in tons',
  `make` varchar(255) DEFAULT NULL,
  `model` varchar(255) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `color` varchar(255) DEFAULT NULL,
  `chassis_number` varchar(255) DEFAULT NULL,
  `engine_number` varchar(255) DEFAULT NULL,
  `fitness_expiry` date DEFAULT NULL,
  `insurance_expiry` date DEFAULT NULL,
  `permit_expiry` date DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vehicles_plate_number_unique` (`plate_number`),
  KEY `vehicles_transporter_id_foreign` (`transporter_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vehicles`
--

INSERT INTO `vehicles` (`id`, `plate_number`, `transporter_id`, `vehicle_type`, `capacity`, `make`, `model`, `year`, `color`, `chassis_number`, `engine_number`, `fitness_expiry`, `insurance_expiry`, `permit_expiry`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(1, '34567', 2, 'pickup', 1000.00, 'hello', 'dfghk', 2023, 'black', '4432e44', '66787654', '2025-07-12', '2025-07-12', '2025-07-12', 'active', 'help', '2025-06-25 23:00:04', '2025-06-25 23:00:04'),
(2, '34569', 8, 'bus', 20000.00, 'Classic', 'New', 2020, 'Blue', '4432e4229', '66787654ssee', '2025-07-04', '2025-07-11', '2025-07-11', 'maintenance', 'Under', '2025-06-25 23:03:13', '2025-06-25 23:03:13'),
(3, '345693221', 9, 'container', 344000.00, 'Modern', 'Classic', 2021, 'Darkblue', '4432e444432', 'ffg554321', '2025-06-27', '2025-06-20', '2025-06-21', 'active', 'Testing', '2025-06-25 23:34:26', '2025-06-25 23:34:26'),
(4, '3456944', 2, 'container', 30000.00, '12kj', 'New', 2022, 'Darkblue', '4432e4229888', '66787654sseebgf', '2025-06-28', '2025-06-28', '2025-07-05', 'out_of_service', 'Testing', '2025-06-26 11:53:04', '2025-06-26 11:53:04');

-- --------------------------------------------------------

--
-- Structure for view `product_ledger`
--
DROP TABLE IF EXISTS `product_ledger`;

DROP VIEW IF EXISTS `product_ledger`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `product_ledger`  AS SELECT `inv_stock_tracking`.`id` AS `id`, `inv_stock_tracking`.`product_id` AS `product_id`, `inv_products`.`name` AS `product_name`, ifnull(case `inv_stock_tracking`.`movement` when 'IN' then `inv_stock_tracking`.`quantity` end,0) AS `received`, ifnull(case `inv_stock_tracking`.`movement` when 'Out' then 0 - `inv_stock_tracking`.`quantity` end,0) AS `outgoing`, `inv_stock_tracking`.`updated_by` AS `user`, `inv_stock_tracking`.`updated_at` AS `date`, `inv_stock_tracking`.`out_mode` AS `method`, `inv_stock_tracking`.`movement` AS `movement`, `inv_stock_tracking`.`store_id` AS `store_id` FROM (`inv_stock_tracking` join `inv_products` on(`inv_stock_tracking`.`product_id` = `inv_products`.`id`)) ;

-- --------------------------------------------------------

--
-- Structure for view `sale_details`
--
DROP TABLE IF EXISTS `sale_details`;

DROP VIEW IF EXISTS `sale_details`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `sale_details`  AS SELECT `s`.`id` AS `sale_id`, `sd`.`id` AS `sale_details_id`, `s`.`receipt_number` AS `receipt_number`, `s`.`customer_id` AS `customer_id`, `c`.`name` AS `customer_name`, `s`.`price_category_id` AS `price_category_id`, `p`.`name` AS `price_category`, `sd`.`stock_id` AS `stock_id`, `product`.`id` AS `product_id`, `product`.`name` AS `product_name`, `category`.`name` AS `category`, `sd`.`quantity` AS `quantity`, `sd`.`price` AS `price`, `sd`.`vat` AS `vat`, `sd`.`amount` AS `amount`, `sd`.`discount` AS `discount`, `s`.`date` AS `sold_at`, `s`.`created_by` AS `sold_by_id`, `u`.`name` AS `sold_by`, `sd`.`updated_at` AS `updated_at`, `sd`.`updated_by` AS `updated_by_id`, `u2`.`name` AS `updated_by`, `sd`.`status` AS `status` FROM ((((((((`sales` `s` join `sales_details` `sd` on(`s`.`id` = `sd`.`sale_id`)) join `price_categories` `p` on(`p`.`id` = `s`.`price_category_id`)) join `users` `u` on(`u`.`id` = `s`.`created_by`)) join `inv_current_stock` `stock` on(`stock`.`id` = `sd`.`stock_id`)) join `inv_products` `product` on(`product`.`id` = `stock`.`product_id`)) join `inv_categories` `category` on(`category`.`id` = `product`.`category_id`)) left join `users` `u2` on(`u2`.`id` = `sd`.`updated_by`)) left join `customers` `c` on(`c`.`id` = `s`.`customer_id`)) ;

-- --------------------------------------------------------

--
-- Structure for view `stock_details`
--
DROP TABLE IF EXISTS `stock_details`;

DROP VIEW IF EXISTS `stock_details`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `stock_details`  AS SELECT `i`.`id` AS `id`, `i`.`product_id` AS `product_id`, `p`.`name` AS `product_name`, `c`.`name` AS `category`, `c`.`id` AS `category_id`, `i`.`expiry_date` AS `expiry_date`, `i`.`quantity` AS `quantity`, `i`.`unit_cost` AS `unit_cost`, `i`.`batch_number` AS `batch_number`, `i`.`shelf_number` AS `shelf_number`, `i`.`store_id` AS `store_id`, `s`.`name` AS `store`, `sa`.`id` AS `sales_id`, `sa`.`price` AS `selling_price`, `sa`.`price_category_id` AS `price_category_id`, `pc`.`name` AS `price_category_name`, `i`.`created_at` AS `created_at`, `i`.`updated_at` AS `updated_at` FROM (((((`inv_current_stock` `i` join `inv_products` `p` on(`p`.`id` = `i`.`product_id`)) left join `inv_categories` `c` on(`c`.`id` = `p`.`category_id`)) join `inv_stores` `s` on(`s`.`id` = `i`.`store_id`)) join `sales_prices` `sa` on(`sa`.`stock_id` = `i`.`id`)) join `price_categories` `pc` on(`pc`.`id` = `sa`.`price_category_id`)) ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD CONSTRAINT `vehicles_transporter_id_foreign` FOREIGN KEY (`transporter_id`) REFERENCES `transporters` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
