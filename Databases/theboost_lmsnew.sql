-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 06, 2026 at 10:34 AM
-- Server version: 10.5.27-MariaDB-cll-lve
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `theboost_lmsnew`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounting`
--

CREATE TABLE `accounting` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `creator_id` int(10) UNSIGNED DEFAULT NULL,
  `webinar_id` int(10) UNSIGNED DEFAULT NULL,
  `meeting_id` int(10) UNSIGNED DEFAULT NULL,
  `meeting_time_id` int(10) UNSIGNED DEFAULT NULL,
  `bundle_id` int(10) UNSIGNED DEFAULT NULL,
  `installment_order_id` int(10) UNSIGNED DEFAULT NULL,
  `system` tinyint(1) NOT NULL DEFAULT 0,
  `tax` tinyint(1) NOT NULL DEFAULT 0,
  `amount` int(11) NOT NULL,
  `type` enum('addiction','deduction') NOT NULL,
  `type_account` enum('income','asset') NOT NULL,
  `description` text DEFAULT NULL,
  `affiliate` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ai_contents`
--

CREATE TABLE `ai_contents` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `service_type` enum('text','image') NOT NULL,
  `service_id` int(10) UNSIGNED DEFAULT NULL,
  `keyword` varchar(255) DEFAULT NULL,
  `language` varchar(255) DEFAULT NULL,
  `prompt` text DEFAULT NULL,
  `result` text DEFAULT NULL,
  `created_at` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ai_content_templates`
--

CREATE TABLE `ai_content_templates` (
  `id` int(10) UNSIGNED NOT NULL,
  `type` enum('text','image') NOT NULL,
  `enable_length` tinyint(1) NOT NULL DEFAULT 0,
  `length` int(10) UNSIGNED DEFAULT NULL,
  `image_size` enum('256','512','1024') DEFAULT NULL,
  `enable` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ai_content_template_translations`
--

CREATE TABLE `ai_content_template_translations` (
  `id` int(10) UNSIGNED NOT NULL,
  `ai_content_template_id` int(10) UNSIGNED NOT NULL,
  `locale` varchar(191) NOT NULL,
  `title` varchar(255) NOT NULL,
  `prompt` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bundles`
--

CREATE TABLE `bundles` (
  `id` int(10) UNSIGNED NOT NULL,
  `creator_id` int(10) UNSIGNED NOT NULL,
  `teacher_id` int(10) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED DEFAULT NULL,
  `slug` varchar(255) NOT NULL,
  `thumbnail` varchar(255) NOT NULL,
  `image_cover` varchar(255) NOT NULL,
  `video_demo` varchar(255) DEFAULT NULL,
  `video_demo_source` enum('upload','youtube','vimeo','external_link','iframe') DEFAULT NULL,
  `price` int(10) UNSIGNED DEFAULT NULL,
  `points` int(10) UNSIGNED DEFAULT NULL,
  `subscribe` tinyint(1) NOT NULL DEFAULT 0,
  `access_days` int(10) UNSIGNED DEFAULT NULL,
  `message_for_reviewer` text DEFAULT NULL,
  `status` enum('active','pending','is_draft','inactive') NOT NULL,
  `created_at` bigint(20) UNSIGNED NOT NULL,
  `updated_at` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bundle_translations`
--

CREATE TABLE `bundle_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `bundle_id` int(10) UNSIGNED NOT NULL,
  `locale` varchar(191) NOT NULL,
  `title` varchar(255) NOT NULL,
  `seo_description` text DEFAULT NULL,
  `description` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(10) UNSIGNED NOT NULL,
  `creator_id` int(10) UNSIGNED NOT NULL,
  `webinar_id` int(10) UNSIGNED DEFAULT NULL,
  `ticket_id` int(10) UNSIGNED DEFAULT NULL,
  `reserve_meeting_id` int(10) UNSIGNED DEFAULT NULL,
  `subscribe_id` int(10) UNSIGNED DEFAULT NULL,
  `promotion_id` int(10) UNSIGNED DEFAULT NULL,
  `bundle_id` int(10) UNSIGNED DEFAULT NULL,
  `product_order_id` int(10) UNSIGNED DEFAULT NULL,
  `special_offer_id` int(10) UNSIGNED DEFAULT NULL,
  `installment_order_id` int(10) UNSIGNED DEFAULT NULL,
  `gift_id` int(10) UNSIGNED DEFAULT NULL,
  `batch_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` int(10) UNSIGNED NOT NULL,
  `updated_at` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `title` varchar(64) NOT NULL,
  `order` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `certificates`
--

CREATE TABLE `certificates` (
  `id` int(10) UNSIGNED NOT NULL,
  `quiz_id` int(10) UNSIGNED NOT NULL,
  `quiz_result_id` int(10) UNSIGNED NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `user_grade` int(10) UNSIGNED DEFAULT NULL,
  `file` varchar(255) DEFAULT NULL,
  `created_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `certificates_templates`
--

CREATE TABLE `certificates_templates` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `body` text DEFAULT NULL,
  `image` varchar(255) NOT NULL,
  `position_x` varchar(64) DEFAULT NULL,
  `position_y` varchar(64) DEFAULT NULL,
  `font_size` varchar(64) DEFAULT NULL,
  `text_color` varchar(64) DEFAULT NULL,
  `status` enum('draft','publish') NOT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `review_id` int(10) UNSIGNED DEFAULT NULL,
  `webinar_id` int(10) UNSIGNED DEFAULT NULL,
  `bundle_id` int(10) UNSIGNED DEFAULT NULL,
  `upcoming_course_id` int(10) UNSIGNED DEFAULT NULL,
  `blog_id` int(10) UNSIGNED DEFAULT NULL,
  `product_id` int(10) UNSIGNED DEFAULT NULL,
  `product_review_id` int(10) UNSIGNED DEFAULT NULL,
  `reply_id` int(10) UNSIGNED DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `status` enum('pending','active') NOT NULL,
  `created_at` int(11) NOT NULL,
  `report` tinyint(1) NOT NULL DEFAULT 0,
  `disabled` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `currencies`
--

CREATE TABLE `currencies` (
  `id` int(10) UNSIGNED NOT NULL,
  `currency` varchar(255) NOT NULL,
  `currency_position` enum('left','right','left_with_space','right_with_space') NOT NULL,
  `currency_separator` enum('dot','comma') NOT NULL,
  `currency_decimal` int(10) UNSIGNED DEFAULT NULL,
  `exchange_rate` float DEFAULT NULL,
  `order` int(10) UNSIGNED DEFAULT NULL,
  `created_at` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `faqs`
--

CREATE TABLE `faqs` (
  `id` int(10) UNSIGNED NOT NULL,
  `webinar_id` int(10) UNSIGNED NOT NULL,
  `bundle_id` int(10) UNSIGNED DEFAULT NULL,
  `title` varchar(64) NOT NULL,
  `answer` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

CREATE TABLE `favorites` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `webinar_id` int(10) UNSIGNED NOT NULL,
  `bundle_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

CREATE TABLE `files` (
  `id` int(10) UNSIGNED NOT NULL,
  `webinar_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(64) NOT NULL,
  `accessibility` enum('free','paid') NOT NULL,
  `file` varchar(255) NOT NULL,
  `volume` varchar(64) NOT NULL,
  `file_type` varchar(64) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `filters`
--

CREATE TABLE `filters` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(64) NOT NULL,
  `category_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `filter_options`
--

CREATE TABLE `filter_options` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(64) NOT NULL,
  `filter_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `group_users`
--

CREATE TABLE `group_users` (
  `id` int(10) UNSIGNED NOT NULL,
  `group_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `meetings`
--

CREATE TABLE `meetings` (
  `id` int(10) UNSIGNED NOT NULL,
  `creator_id` int(10) UNSIGNED NOT NULL,
  `amount` int(10) UNSIGNED DEFAULT NULL,
  `discount` int(11) DEFAULT NULL,
  `disabled` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2020_08_09_145553_create_roles_table', 1),
(6, '2020_08_09_145834_create_sections_table', 1),
(7, '2020_08_09_145926_create_permissions_table', 1),
(8, '2020_08_24_163003_create_webinars_table', 1),
(9, '2020_08_24_164823_create_webinar_partner_teacher_table', 1),
(10, '2020_08_24_165658_create_tags_table', 1),
(11, '2020_08_24_165835_create_webinar_tag_table', 1),
(12, '2020_08_24_171611_create_categories_table', 1),
(13, '2020_08_29_052437_create_filters_table', 1),
(14, '2020_08_29_052900_create_filter_options_table', 1),
(15, '2020_08_29_054455_add_category_id_in_webinar_table', 1),
(16, '2020_09_01_174741_add_seo_description_and_start_end_time_in_webinar_table', 1),
(17, '2020_09_02_180508_create_webinar_filter_option_table', 1),
(18, '2020_09_02_193923_create_tickets_table', 1),
(19, '2020_09_02_210447_create_sessions_table', 1),
(20, '2020_09_02_212642_create_files_table', 1),
(21, '2020_09_03_175543_create_faqs_table', 1),
(22, '2020_09_08_175539_delete_webinar_tag_and_update_tag_table', 1),
(23, '2020_09_09_154522_create_quizzes_table', 1),
(24, '2020_09_09_174646_create_quizzes_questions_table', 1),
(25, '2020_09_09_182726_create_quizzes_questions_answers_table', 1),
(26, '2020_09_14_160028_create_prerequisites_table', 1),
(27, '2020_09_14_183235_nullable_item_id_in_quizzes_table', 1),
(28, '2020_09_16_163835_create_quizzes_results_table', 1),
(29, '2020_09_24_102115_add_total_mark_in_quize_table', 1),
(30, '2020_09_24_132242_create_comment_table', 1),
(31, '2020_09_24_132639_create_favorites_table', 1),
(32, '2020_09_26_181200_create_certificate_table', 1),
(33, '2020_09_26_181444_create_certificates_templates_table', 1),
(34, '2020_09_30_170451_add_slug_in_webinars_table', 1),
(35, '2020_09_30_191202_create_purchases_table', 1),
(36, '2020_10_02_094723_edit_table_and_add_foreign_key', 1),
(37, '2020_10_08_055408_add_reviwes_table', 1),
(38, '2020_10_08_084100_edit_status_comments_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `sender_id` int(10) UNSIGNED DEFAULT NULL,
  `group_id` int(10) UNSIGNED DEFAULT NULL,
  `webinar_id` int(10) UNSIGNED DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `sender` enum('system','admin') DEFAULT 'system',
  `type` enum('single','all_users','students','instructors','organizations','group','course_students') NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications_status`
--

CREATE TABLE `notifications_status` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `notification_id` int(10) UNSIGNED NOT NULL,
  `seen_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `offline_payments`
--

CREATE TABLE `offline_payments` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `offline_bank_id` int(10) UNSIGNED DEFAULT NULL,
  `amount` int(10) UNSIGNED NOT NULL,
  `bank` varchar(64) DEFAULT NULL,
  `reference_number` varchar(64) DEFAULT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `pay_date` varchar(64) DEFAULT NULL,
  `status` enum('waiting','approved','reject') NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_channels`
--

CREATE TABLE `payment_channels` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `class_name` varchar(255) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `image` varchar(255) DEFAULT NULL,
  `settings` text DEFAULT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payment_channels`
--

INSERT INTO `payment_channels` (`id`, `title`, `class_name`, `status`, `image`, `settings`, `created_at`) VALUES
(1, 'Razorpay', 'Razorpay', 'active', NULL, '', 1772755126);

-- --------------------------------------------------------

--
-- Table structure for table `payouts`
--

CREATE TABLE `payouts` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `user_selected_bank_id` int(10) UNSIGNED DEFAULT NULL,
  `amount` decimal(13,2) NOT NULL,
  `status` enum('waiting','done','reject') NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` int(10) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED DEFAULT NULL,
  `section_id` int(10) UNSIGNED DEFAULT NULL,
  `allow` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `role_id`, `section_id`, `allow`) VALUES
(1, 2, 1, 1),
(2, 2, 2, 1),
(3, 2, 3, 1),
(4, 2, 4, 1),
(5, 2, 5, 1),
(6, 2, 6, 1),
(7, 2, 7, 1),
(8, 2, 8, 1),
(9, 2, 9, 1),
(10, 2, 10, 1),
(11, 2, 11, 1),
(12, 2, 12, 1),
(13, 2, 13, 1),
(14, 2, 14, 1),
(15, 2, 15, 1),
(16, 2, 16, 1),
(17, 2, 17, 1),
(50, 2, 50, 1),
(51, 2, 51, 1),
(52, 2, 52, 1),
(53, 2, 53, 1),
(54, 2, 54, 1),
(100, 2, 100, 1),
(101, 2, 101, 1),
(102, 2, 102, 1),
(103, 2, 103, 1),
(104, 2, 104, 1),
(105, 2, 105, 1),
(106, 2, 106, 1),
(107, 2, 107, 1),
(108, 2, 108, 1),
(109, 2, 109, 1),
(110, 2, 110, 1),
(111, 2, 111, 1),
(112, 2, 112, 1),
(113, 2, 113, 1),
(114, 2, 114, 1),
(115, 2, 115, 1),
(116, 2, 116, 1),
(150, 2, 150, 1),
(151, 2, 151, 1),
(152, 2, 152, 1),
(153, 2, 153, 1),
(154, 2, 154, 1),
(155, 2, 155, 1),
(156, 2, 156, 1),
(157, 2, 157, 1),
(158, 2, 158, 1),
(200, 2, 200, 1),
(201, 2, 201, 1),
(202, 2, 202, 1),
(203, 2, 203, 1),
(204, 2, 204, 1),
(205, 2, 205, 1),
(206, 2, 206, 1),
(207, 2, 207, 1),
(208, 2, 208, 1),
(250, 2, 250, 1),
(251, 2, 251, 1),
(252, 2, 252, 1),
(253, 2, 253, 1),
(254, 2, 254, 1),
(300, 2, 300, 1),
(301, 2, 301, 1),
(302, 2, 302, 1),
(303, 2, 303, 1),
(304, 2, 304, 1),
(350, 2, 350, 1),
(351, 2, 351, 1),
(352, 2, 352, 1),
(353, 2, 353, 1),
(354, 2, 354, 1),
(355, 2, 355, 1),
(356, 2, 356, 1),
(357, 2, 357, 1),
(400, 2, 400, 1),
(401, 2, 401, 1),
(402, 2, 402, 1),
(403, 2, 403, 1),
(404, 2, 404, 1),
(405, 2, 405, 1),
(450, 2, 450, 1),
(451, 2, 451, 1),
(452, 2, 452, 1),
(453, 2, 453, 1),
(454, 2, 454, 1),
(455, 2, 455, 1),
(456, 2, 456, 1),
(457, 2, 457, 1),
(458, 2, 458, 1),
(459, 2, 459, 1),
(500, 2, 500, 1),
(501, 2, 501, 1),
(502, 2, 502, 1),
(503, 2, 503, 1),
(504, 2, 504, 1),
(505, 2, 505, 1),
(550, 2, 550, 1),
(551, 2, 551, 1),
(552, 2, 552, 1),
(553, 2, 553, 1),
(554, 2, 554, 1),
(600, 2, 600, 1),
(601, 2, 601, 1),
(602, 2, 602, 1),
(603, 2, 603, 1),
(650, 2, 650, 1),
(651, 2, 651, 1),
(652, 2, 652, 1),
(653, 2, 653, 1),
(654, 2, 654, 1),
(655, 2, 655, 1),
(656, 2, 656, 1),
(700, 2, 700, 1),
(701, 2, 701, 1),
(702, 2, 702, 1),
(703, 2, 703, 1),
(704, 2, 704, 1),
(705, 2, 705, 1),
(706, 2, 706, 1),
(707, 2, 707, 1),
(708, 2, 708, 1),
(750, 2, 750, 1),
(751, 2, 751, 1),
(752, 2, 752, 1),
(753, 2, 753, 1),
(754, 2, 754, 1),
(800, 2, 800, 1),
(801, 2, 801, 1),
(802, 2, 802, 1),
(803, 2, 803, 1),
(850, 2, 850, 1),
(851, 2, 851, 1),
(852, 2, 852, 1),
(853, 2, 853, 1),
(854, 2, 854, 1),
(900, 2, 900, 1),
(901, 2, 901, 1),
(902, 2, 902, 1),
(903, 2, 903, 1),
(904, 2, 904, 1),
(950, 2, 950, 1),
(951, 2, 951, 1),
(952, 2, 952, 1),
(953, 2, 953, 1),
(954, 2, 954, 1),
(955, 2, 955, 1),
(956, 2, 956, 1),
(957, 2, 957, 1),
(958, 2, 958, 1),
(959, 2, 959, 1),
(1000, 2, 1000, 1),
(1001, 2, 1001, 1),
(1002, 2, 1002, 1),
(1003, 2, 1003, 1),
(1004, 2, 1004, 1),
(1050, 2, 1050, 1),
(1051, 2, 1051, 1),
(1052, 2, 1052, 1),
(1053, 2, 1053, 1),
(1054, 2, 1054, 1),
(1055, 2, 1055, 1),
(1056, 2, 1056, 1),
(1057, 2, 1057, 1),
(1058, 2, 1058, 1),
(1059, 2, 1059, 1),
(1060, 2, 1060, 1),
(1075, 2, 1075, 1),
(1076, 2, 1076, 1),
(1077, 2, 1077, 1),
(1078, 2, 1078, 1),
(1079, 2, 1079, 1),
(1100, 2, 1100, 1),
(1101, 2, 1101, 1),
(1102, 2, 1102, 1),
(1103, 2, 1103, 1),
(1104, 2, 1104, 1),
(1150, 2, 1150, 1),
(1151, 2, 1151, 1),
(1152, 2, 1152, 1),
(1153, 2, 1153, 1),
(1154, 2, 1154, 1),
(1200, 2, 1200, 1),
(1201, 2, 1201, 1),
(1202, 2, 1202, 1),
(1203, 2, 1203, 1),
(1204, 2, 1204, 1),
(1230, 2, 1230, 1),
(1231, 2, 1231, 1),
(1232, 2, 1232, 1),
(1233, 2, 1233, 1),
(1234, 2, 1234, 1),
(1235, 2, 1235, 1),
(1250, 2, 1250, 1),
(1251, 2, 1251, 1),
(1252, 2, 1252, 1),
(1253, 2, 1253, 1),
(1300, 2, 1300, 1),
(1301, 2, 1301, 1),
(1302, 2, 1302, 1),
(1303, 2, 1303, 1),
(1304, 2, 1304, 1),
(1305, 2, 1305, 1),
(1350, 2, 1350, 1),
(1351, 2, 1351, 1),
(1352, 2, 1352, 1),
(1353, 2, 1353, 1),
(1354, 2, 1354, 1),
(1355, 2, 1355, 1),
(1400, 2, 1400, 1),
(1401, 2, 1401, 1),
(1402, 2, 1402, 1),
(1403, 2, 1403, 1),
(1404, 2, 1404, 1),
(1405, 2, 1405, 1),
(1406, 2, 1406, 1),
(1407, 2, 1407, 1),
(1408, 2, 1408, 1),
(1409, 2, 1409, 1);

-- --------------------------------------------------------

--
-- Table structure for table `prerequisites`
--

CREATE TABLE `prerequisites` (
  `id` int(10) UNSIGNED NOT NULL,
  `webinar_id` int(10) UNSIGNED NOT NULL,
  `prerequisite_id` int(10) UNSIGNED NOT NULL,
  `required` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(10) UNSIGNED NOT NULL,
  `creator_id` int(10) UNSIGNED NOT NULL,
  `type` enum('physical','virtual') NOT NULL,
  `slug` varchar(255) NOT NULL,
  `category_id` int(10) UNSIGNED DEFAULT NULL,
  `price` bigint(20) UNSIGNED DEFAULT NULL,
  `point` bigint(20) UNSIGNED DEFAULT NULL,
  `unlimited_inventory` tinyint(1) NOT NULL DEFAULT 0,
  `ordering` tinyint(1) NOT NULL DEFAULT 0,
  `inventory` int(10) UNSIGNED DEFAULT NULL,
  `inventory_warning` int(10) UNSIGNED DEFAULT NULL,
  `inventory_updated_at` bigint(20) UNSIGNED DEFAULT NULL,
  `delivery_fee` bigint(20) UNSIGNED DEFAULT NULL,
  `delivery_estimated_time` int(10) UNSIGNED DEFAULT NULL,
  `message_for_reviewer` text DEFAULT NULL,
  `tax` int(10) UNSIGNED DEFAULT NULL,
  `commission` int(10) UNSIGNED DEFAULT NULL,
  `status` enum('draft','pending','active','inactive') NOT NULL,
  `updated_at` bigint(20) UNSIGNED NOT NULL,
  `created_at` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_orders`
--

CREATE TABLE `product_orders` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `sale_id` int(10) UNSIGNED DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `amount` decimal(15,2) NOT NULL,
  `total_amount` decimal(15,2) NOT NULL,
  `discount` decimal(15,2) DEFAULT NULL,
  `tax` decimal(15,2) DEFAULT NULL,
  `status` enum('pending','paid','canceled') NOT NULL DEFAULT 'pending',
  `created_at` int(10) UNSIGNED NOT NULL,
  `updated_at` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_translations`
--

CREATE TABLE `product_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `locale` varchar(191) NOT NULL,
  `title` varchar(255) NOT NULL,
  `seo_description` text DEFAULT NULL,
  `summary` text DEFAULT NULL,
  `description` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchases`
--

CREATE TABLE `purchases` (
  `id` int(10) UNSIGNED NOT NULL,
  `webinar_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `created_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quizzes`
--

CREATE TABLE `quizzes` (
  `id` int(10) UNSIGNED NOT NULL,
  `webinar_id` int(10) UNSIGNED DEFAULT NULL,
  `creator_user_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(64) NOT NULL,
  `webinar_title` varchar(64) DEFAULT NULL,
  `time` int(11) NOT NULL DEFAULT 0,
  `attempt` int(11) NOT NULL,
  `pass_mark` int(11) NOT NULL,
  `certificate` tinyint(1) NOT NULL,
  `status` enum('active','inactive') NOT NULL,
  `total_mark` int(10) UNSIGNED DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quizzes_questions`
--

CREATE TABLE `quizzes_questions` (
  `id` int(10) UNSIGNED NOT NULL,
  `quiz_id` int(10) UNSIGNED NOT NULL,
  `creator_user_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `grade` varchar(255) NOT NULL,
  `type` enum('multiple','descriptive') NOT NULL,
  `correct` varchar(255) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quizzes_questions_answers`
--

CREATE TABLE `quizzes_questions_answers` (
  `id` int(10) UNSIGNED NOT NULL,
  `creator_user_id` int(10) UNSIGNED NOT NULL,
  `question_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `correct` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quizzes_results`
--

CREATE TABLE `quizzes_results` (
  `id` int(10) UNSIGNED NOT NULL,
  `quiz_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `results` text DEFAULT NULL,
  `user_grade` int(11) DEFAULT NULL,
  `status` enum('passed','failed','waiting') NOT NULL,
  `created_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(64) NOT NULL,
  `caption` varchar(64) NOT NULL,
  `users_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `caption`, `users_count`, `is_admin`, `created_at`) VALUES
(1, 'user', 'User role', 0, 0, 1772755122),
(2, 'admin', 'Admin role', 0, 1, 1772755122),
(3, 'organization', 'Organization role', 0, 0, 1772755122),
(4, 'teacher', 'Teacher role', 0, 0, 1772755122);

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `buyer_id` int(10) UNSIGNED DEFAULT NULL,
  `seller_id` int(10) UNSIGNED DEFAULT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `webinar_id` int(10) UNSIGNED DEFAULT NULL,
  `meeting_id` int(10) UNSIGNED DEFAULT NULL,
  `meeting_time_id` int(10) UNSIGNED DEFAULT NULL,
  `ticket_id` int(10) UNSIGNED DEFAULT NULL,
  `subscribe_id` int(10) UNSIGNED DEFAULT NULL,
  `promotion_id` int(10) UNSIGNED DEFAULT NULL,
  `promotion_type` enum('fixed_amount','percentage') DEFAULT NULL,
  `registration_package_id` int(10) UNSIGNED DEFAULT NULL,
  `product_id` int(10) UNSIGNED DEFAULT NULL,
  `bundle_id` int(10) UNSIGNED DEFAULT NULL,
  `gift_id` int(10) UNSIGNED DEFAULT NULL,
  `installment_order_id` int(10) UNSIGNED DEFAULT NULL,
  `type` enum('webinar','meeting','subscribe','promotion','registration_package','product','bundle') NOT NULL,
  `payment_method` enum('credit','payment_channel','subscribe') NOT NULL,
  `amount` decimal(15,2) UNSIGNED NOT NULL,
  `tax` decimal(15,2) UNSIGNED DEFAULT NULL,
  `commission` decimal(15,2) UNSIGNED DEFAULT NULL,
  `discount` decimal(15,2) UNSIGNED DEFAULT NULL,
  `total_amount` decimal(15,2) UNSIGNED NOT NULL,
  `product_delivery_fee` decimal(15,2) DEFAULT NULL,
  `manual_added` tinyint(1) NOT NULL DEFAULT 0,
  `batch_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` int(10) UNSIGNED NOT NULL,
  `refund_at` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sales_log`
--

CREATE TABLE `sales_log` (
  `id` int(10) UNSIGNED NOT NULL,
  `sale_id` int(10) UNSIGNED NOT NULL,
  `viewed_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

CREATE TABLE `sections` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(64) NOT NULL,
  `section_group_id` int(10) UNSIGNED DEFAULT NULL,
  `caption` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sections`
--

INSERT INTO `sections` (`id`, `name`, `section_group_id`, `caption`) VALUES
(1, 'admin_general_dashboard', NULL, 'General Dashboard'),
(2, 'admin_general_dashboard_show', 1, 'General Dashboard page'),
(3, 'admin_general_dashboard_quick_access_links', 1, 'Quick access links in General Dashboard'),
(4, 'admin_general_dashboard_daily_sales_statistics', 1, 'Daily Sales Type Statistics Section'),
(5, 'admin_general_dashboard_income_statistics', 1, 'Income Statistics Section'),
(6, 'admin_general_dashboard_total_sales_statistics', 1, 'Total Sales Statistics Section'),
(7, 'admin_general_dashboard_new_sales', 1, 'New Sales Section'),
(8, 'admin_general_dashboard_new_comments', 1, 'New Comments Section'),
(9, 'admin_general_dashboard_new_tickets', 1, 'New Tickets Section'),
(10, 'admin_general_dashboard_new_reviews', 1, 'New Reviews Section'),
(11, 'admin_general_dashboard_sales_statistics_chart', 1, 'Sales Statistics Chart'),
(12, 'admin_general_dashboard_recent_comments', 1, 'Recent comments Section'),
(13, 'admin_general_dashboard_recent_tickets', 1, 'Recent tickets Section'),
(14, 'admin_general_dashboard_recent_webinars', 1, 'Recent webinars Section'),
(15, 'admin_general_dashboard_recent_courses', 1, 'Recent courses Section'),
(16, 'admin_general_dashboard_users_statistics_chart', 1, 'Users Statistics Chart'),
(17, 'admin_clear_cache', 1, 'Clear cache'),
(25, 'admin_marketing_dashboard', NULL, 'Marketing Dashboard'),
(26, 'admin_marketing_dashboard_show', 25, 'Marketing Dashboard page'),
(50, 'admin_roles', NULL, 'Roles'),
(51, 'admin_roles_list', 50, 'Roles List'),
(52, 'admin_roles_create', 50, 'Roles Create'),
(53, 'admin_roles_edit', 50, 'Roles Edit'),
(54, 'admin_roles_delete', 50, 'Roles Delete'),
(100, 'admin_users', NULL, 'Users'),
(101, 'admin_staffs_list', 100, 'Staffs list'),
(102, 'admin_users_list', 100, 'Students list'),
(103, 'admin_instructors_list', 100, 'Instructors list'),
(104, 'admin_organizations_list', 100, 'Organizations list'),
(105, 'admin_users_create', 100, 'Users Create'),
(106, 'admin_users_edit', 100, 'Users Edit'),
(107, 'admin_users_delete', 100, 'Users Delete'),
(108, 'admin_users_export_excel', 100, 'List Export excel'),
(109, 'admin_users_badges', 100, 'Users Badges'),
(110, 'admin_users_badges_edit', 100, 'Badges edit'),
(111, 'admin_users_badges_delete', 100, 'Badges delete'),
(112, 'admin_users_impersonate', 100, 'users impersonate (login by users)'),
(113, 'admin_become_instructors_list', 100, 'Lists of requests for become instructors'),
(114, 'admin_become_instructors_reject', 100, 'Reject requests for become instructors'),
(115, 'admin_become_instructors_delete', 100, 'Delete requests for become instructors'),
(116, 'admin_update_user_registration_package', 100, 'Edit user registration package'),
(117, 'admin_update_user_meeting_settings', 100, 'Edit user meeting settings'),
(150, 'admin_webinars', NULL, 'Webinars'),
(151, 'admin_webinars_list', 150, 'Webinars List'),
(152, 'admin_webinars_create', 150, 'Webinars Create'),
(153, 'admin_webinars_edit', 150, 'Webinars Edit'),
(154, 'admin_webinars_delete', 150, 'Webinars Delete'),
(155, 'admin_webinars_export_excel', 150, 'Export excel webinars list'),
(156, 'admin_feature_webinars', 150, 'Feature webinars list'),
(157, 'admin_feature_webinars_create', 150, 'create feature webinar'),
(158, 'admin_feature_webinars_export_excel', 150, 'Feature webinar export excel'),
(159, 'admin_webinar_students_lists', 150, 'Webinar students Lists'),
(160, 'admin_webinar_students_delete', 150, 'Webinar students delete'),
(161, 'admin_webinar_notification_to_students', 150, 'Send notification to course students'),
(162, 'admin_webinar_statistics', 150, 'Course statistics'),
(163, 'admin_agora_history_list', 150, 'Agora history lists'),
(164, 'admin_agora_history_export', 150, 'Agora history export'),
(165, 'admin_course_question_forum_list', 150, 'Forum Question Lists'),
(166, 'admin_course_question_forum_answers', 150, 'Forum Answers Lists'),
(200, 'admin_categories', NULL, 'Categories'),
(201, 'admin_categories_list', 200, 'Categories List'),
(202, 'admin_categories_create', 200, 'Categories Create'),
(203, 'admin_categories_edit', 200, 'Categories Edit'),
(204, 'admin_categories_delete', 200, 'Categories Delete'),
(205, 'admin_trending_categories', 200, 'Trends Categories List'),
(206, 'admin_create_trending_categories', 200, 'Create Trend Categories'),
(207, 'admin_edit_trending_categories', 200, 'Edit Trend Categories'),
(208, 'admin_delete_trending_categories', 200, 'Delete Trend Categories'),
(250, 'admin_tags', NULL, 'Tags'),
(251, 'admin_tags_list', 250, 'Tags List'),
(252, 'admin_tags_create', 250, 'Tags Create'),
(253, 'admin_tags_edit', 250, 'Tags Edit'),
(254, 'admin_tags_delete', 250, 'Tags Delete'),
(300, 'admin_filters', NULL, 'Filters'),
(301, 'admin_filters_list', 300, 'Filters List'),
(302, 'admin_filters_create', 300, 'Filters Create'),
(303, 'admin_filters_edit', 300, 'Filters Edit'),
(304, 'admin_filters_delete', 300, 'Filters Delete'),
(350, 'admin_quizzes', NULL, 'Quizzes'),
(351, 'admin_quizzes_list', 350, 'Quizzes List'),
(352, 'admin_quizzes_create', 350, 'Create Quiz'),
(353, 'admin_quizzes_edit', 350, 'Edit Quiz'),
(354, 'admin_quizzes_delete', 350, 'Delete Quiz'),
(355, 'admin_quizzes_results', 350, 'Quizzes results'),
(356, 'admin_quizzes_results_delete', 350, 'Quizzes results delete'),
(357, 'admin_quizzes_lists_excel', 350, 'Quizzes export excel'),
(400, 'admin_quiz_result', NULL, 'Quiz Result'),
(401, 'admin_quiz_result_list', 400, 'Quiz Result List'),
(402, 'admin_quiz_result_create', 400, 'Quiz Result Create'),
(403, 'admin_quiz_result_edit', 400, 'Quiz Result Edit'),
(404, 'admin_quiz_result_delete', 400, 'Quiz Result Delete'),
(405, 'admin_quiz_result_export_excel', 400, 'quiz result export excel'),
(450, 'admin_certificate', NULL, 'Certificate'),
(451, 'admin_certificate_list', 450, 'Certificate List'),
(452, 'admin_certificate_create', 450, 'Certificate Create'),
(453, 'admin_certificate_edit', 450, 'Certificate Edit'),
(454, 'admin_certificate_delete', 450, 'Certificate Delete'),
(455, 'admin_certificate_template_list', 450, 'Certificate template lists'),
(456, 'admin_certificate_template_create', 450, 'Certificate template create'),
(457, 'admin_certificate_template_edit', 450, 'Certificate template edit'),
(458, 'admin_certificate_template_delete', 450, 'Certificate template delete'),
(459, 'admin_certificate_export_excel', 450, 'Certificates export excel'),
(460, 'admin_course_certificate_list', 450, 'Course Competition Certificates'),
(500, 'admin_discount_codes', NULL, 'Discount codes'),
(501, 'admin_discount_codes_list', 500, 'Discount codes list'),
(502, 'admin_discount_codes_create', 500, 'Discount codes create'),
(503, 'admin_discount_codes_edit', 500, 'Discount codes edit'),
(504, 'admin_discount_codes_delete', 500, 'Discount codes delete'),
(505, 'admin_discount_codes_export', 500, 'Discount codes export excel'),
(550, 'admin_group', NULL, 'Groups'),
(551, 'admin_group_list', 550, 'Groups List'),
(552, 'admin_group_create', 550, 'Groups Create'),
(553, 'admin_group_edit', 550, 'Groups Edit'),
(554, 'admin_group_delete', 550, 'Groups Delete'),
(555, 'admin_update_group_registration_package', 550, 'Update group registration package'),
(600, 'admin_payment_channel', NULL, 'Payment Channels'),
(601, 'admin_payment_channel_list', 600, 'Payment Channels List'),
(602, 'admin_payment_channel_toggle_status', 600, 'active or inactive channel'),
(603, 'admin_payment_channel_edit', 600, 'Payment Channels Edit'),
(650, 'admin_settings', NULL, 'settings'),
(651, 'admin_settings_general', 650, 'General settings'),
(652, 'admin_settings_financial', 650, 'Financial settings'),
(653, 'admin_settings_personalization', 650, 'Personalization settings'),
(654, 'admin_settings_notifications', 650, 'Notifications settings'),
(655, 'admin_settings_seo', 650, 'Seo settings'),
(656, 'admin_settings_customization', 650, 'Customization settings'),
(657, 'admin_settings_update_app', 650, 'Update App settings'),
(700, 'admin_blog', NULL, 'Blog'),
(701, 'admin_blog_lists', 700, 'Blog lists'),
(702, 'admin_blog_create', 700, 'Blog create'),
(703, 'admin_blog_edit', 700, 'Blog edit'),
(704, 'admin_blog_delete', 700, 'Blog delete'),
(705, 'admin_blog_categories', 700, 'Blog categories list'),
(706, 'admin_blog_categories_create', 700, 'Blog categories create'),
(707, 'admin_blog_categories_edit', 700, 'Blog categories edit'),
(708, 'admin_blog_categories_delete', 700, 'Blog categories delete'),
(750, 'admin_sales', NULL, 'Sales'),
(751, 'admin_sales_list', 750, 'Sales List'),
(752, 'admin_sales_refund', 750, 'Sales Refund'),
(753, 'admin_sales_invoice', 750, 'Sales invoice'),
(754, 'admin_sales_export', 750, 'Sales Export Excel'),
(800, 'admin_documents', NULL, 'Balances'),
(801, 'admin_documents_list', 800, 'Balances List'),
(802, 'admin_documents_create', 800, 'Balances Create'),
(803, 'admin_documents_print', 800, 'Balances print'),
(850, 'admin_payouts', NULL, 'Payout'),
(851, 'admin_payouts_list', 850, 'Payout List'),
(852, 'admin_payouts_reject', 850, 'Payout Reject'),
(853, 'admin_payouts_payout', 850, 'Payout accept'),
(854, 'admin_payouts_export_excel', 850, 'Payout export excel'),
(900, 'admin_offline_payments', NULL, 'Offline Payments'),
(901, 'admin_offline_payments_list', 900, 'Offline Payments List'),
(902, 'admin_offline_payments_reject', 900, 'Offline Payments Reject'),
(903, 'admin_offline_payments_approved', 900, 'Offline Payments Approved'),
(904, 'admin_offline_payments_export_excel', 900, 'Offline Payments export excel'),
(950, 'admin_supports', NULL, 'Supports'),
(951, 'admin_supports_list', 950, 'Supports List'),
(952, 'admin_support_send', 950, 'Send Support'),
(953, 'admin_supports_reply', 950, 'Supports reply'),
(954, 'admin_supports_delete', 950, 'Supports delete'),
(955, 'admin_support_departments', 950, 'Support departments lists'),
(956, 'admin_support_department_create', 950, 'Create support department'),
(957, 'admin_support_departments_edit', 950, 'Edit support departments'),
(958, 'admin_support_departments_delete', 950, 'Delete support department'),
(959, 'admin_support_course_conversations', 950, 'Course conversations'),
(1000, 'admin_subscribe', NULL, 'Subscribes'),
(1001, 'admin_subscribe_list', 1000, 'Subscribes list'),
(1002, 'admin_subscribe_create', 1000, 'Subscribes create'),
(1003, 'admin_subscribe_edit', 1000, 'Subscribes edit'),
(1004, 'admin_subscribe_delete', 1000, 'Subscribes delete'),
(1050, 'admin_notifications', NULL, 'Notifications'),
(1051, 'admin_notifications_list', 1050, 'Notifications list'),
(1052, 'admin_notifications_send', 1050, 'Send Notifications'),
(1053, 'admin_notifications_edit', 1050, 'Edit and details Notifications'),
(1054, 'admin_notifications_delete', 1050, 'Delete Notifications'),
(1055, 'admin_notifications_markAllRead', 1050, 'Mark All Read Notifications'),
(1056, 'admin_notifications_templates', 1050, 'Notifications templates'),
(1057, 'admin_notifications_template_create', 1050, 'Create notification template'),
(1058, 'admin_notifications_template_edit', 1050, 'Edit notification template'),
(1059, 'admin_notifications_template_delete', 1050, 'Delete notification template'),
(1060, 'admin_notifications_posted_list', 1050, 'Notifications Posted list'),
(1075, 'admin_noticeboards', NULL, 'Noticeboards'),
(1076, 'admin_noticeboards_list', 1075, 'Noticeboards list'),
(1077, 'admin_noticeboards_send', 1075, 'Send Noticeboards'),
(1078, 'admin_noticeboards_edit', 1075, 'Edit Noticeboards'),
(1079, 'admin_noticeboards_delete', 1075, 'Delete Noticeboards'),
(1080, 'admin_course_noticeboards_list', 1075, 'Course Noticeboards list'),
(1081, 'admin_course_noticeboards_send', 1075, 'Send Course Noticeboards'),
(1082, 'admin_course_noticeboards_edit', 1075, 'Edit Course Noticeboards'),
(1083, 'admin_course_noticeboards_delete', 1075, 'Delete Course Noticeboards'),
(1100, 'admin_promotion', NULL, 'Promotions'),
(1101, 'admin_promotion_list', 1100, 'Promotions list'),
(1102, 'admin_promotion_create', 1100, 'Promotion create'),
(1103, 'admin_promotion_edit', 1100, 'Promotion edit'),
(1104, 'admin_promotion_delete', 1100, 'Promotion delete'),
(1150, 'admin_testimonials', NULL, 'testimonials'),
(1151, 'admin_testimonials_list', 1150, 'testimonials list'),
(1152, 'admin_testimonials_create', 1150, 'testimonials create'),
(1153, 'admin_testimonials_edit', 1150, 'testimonials edit'),
(1154, 'admin_testimonials_delete', 1150, 'testimonials delete'),
(1200, 'admin_advertising', NULL, 'advertising'),
(1201, 'admin_advertising_banners', 1200, 'advertising banners list'),
(1202, 'admin_advertising_banners_create', 1200, 'create advertising banner'),
(1203, 'admin_advertising_banners_edit', 1200, 'edit advertising banner'),
(1204, 'admin_advertising_banners_delete', 1200, 'delete advertising banner'),
(1230, 'admin_newsletters', NULL, 'Newsletters'),
(1231, 'admin_newsletters_lists', 1230, 'Newsletters lists'),
(1232, 'admin_newsletters_send', 1230, 'Send Newsletters'),
(1233, 'admin_newsletters_history', 1230, 'Newsletters histories'),
(1234, 'admin_newsletters_delete', 1230, 'Delete newsletters item'),
(1235, 'admin_newsletters_export_excel', 1230, 'Export excel newsletters item'),
(1250, 'admin_contacts', NULL, 'Contacts'),
(1251, 'admin_contacts_lists', 1250, 'Contacts lists'),
(1252, 'admin_contacts_reply', 1250, 'Contacts reply'),
(1253, 'admin_contacts_delete', 1250, 'Contacts delete'),
(1300, 'admin_product_discount', NULL, 'product discount'),
(1301, 'admin_product_discount_list', 1300, 'product discount list'),
(1302, 'admin_product_discount_create', 1300, 'create product discount'),
(1303, 'admin_product_discount_edit', 1300, 'edit product discount'),
(1304, 'admin_product_discount_delete', 1300, 'delete product discount'),
(1305, 'admin_product_discount_export', 1300, 'delete product export excel'),
(1350, 'admin_pages', NULL, 'pages'),
(1351, 'admin_pages_list', 1350, 'pages list'),
(1352, 'admin_pages_create', 1350, 'pages create'),
(1353, 'admin_pages_edit', 1350, 'pages edit'),
(1354, 'admin_pages_toggle', 1350, 'pages toggle publish/draft'),
(1355, 'admin_pages_delete', 1350, 'pages delete'),
(1400, 'admin_comments', NULL, 'Comments'),
(1401, 'admin_comments_edit', 1400, 'Comments edit'),
(1402, 'admin_comments_reply', 1400, 'Comments reply'),
(1403, 'admin_comments_delete', 1400, 'Comments delete'),
(1404, 'admin_comments_status', 1400, 'Comments status (active or pending)'),
(1405, 'admin_comments_reports', 1400, 'Reports'),
(1406, 'admin_webinar_comments', 1400, 'Classes comments'),
(1407, 'admin_blog_comments', 1400, 'Blog comments'),
(1408, 'admin_product_comments', 1400, 'Product comments'),
(1409, 'admin_bundle_comments', 1400, 'Bundle comments'),
(1450, 'admin_reports', NULL, 'Reports'),
(1451, 'admin_webinar_reports', 1450, 'Classes reports'),
(1452, 'admin_webinar_comments_reports', 1450, 'Classes Comments reports'),
(1453, 'admin_webinar_reports_delete', 1450, 'Classes reports delete'),
(1454, 'admin_blog_comments_reports', 1450, 'Blog Comments reports'),
(1455, 'admin_report_reasons', 1450, 'Reports reasons'),
(1456, 'admin_product_comments_reports', 1450, 'Products Comments reports'),
(1457, 'admin_forum_topic_post_reports', 1450, 'Forum Topic Posts Reports'),
(1500, 'admin_additional_pages', NULL, 'Additional Pages'),
(1501, 'admin_additional_pages_404', 1500, '404 error page settings'),
(1502, 'admin_additional_pages_contact_us', 1500, 'Contact page settings'),
(1503, 'admin_additional_pages_footer', 1500, 'Footer settings'),
(1504, 'admin_additional_pages_navbar_links', 1500, 'Top Navbar links settings'),
(1550, 'admin_appointments', NULL, 'Appointments'),
(1551, 'admin_appointments_lists', 1550, 'Appointments lists'),
(1552, 'admin_appointments_join', 1550, 'Appointments join'),
(1553, 'admin_appointments_send_reminder', 1550, 'Appointments send reminder'),
(1554, 'admin_appointments_cancel', 1550, 'Appointments cancel'),
(1600, 'admin_reviews', NULL, 'Reviews'),
(1601, 'admin_reviews_lists', 1600, 'Reviews lists'),
(1602, 'admin_reviews_status_toggle', 1600, 'Reviews status toggle (publish or hidden)'),
(1603, 'admin_reviews_detail_show', 1600, 'Review details page'),
(1604, 'admin_reviews_reply', 1600, 'Review reply'),
(1605, 'admin_reviews_delete', 1600, 'Review delete'),
(1650, 'admin_consultants', NULL, 'Consultants'),
(1651, 'admin_consultants_lists', 1650, 'Consultants lists'),
(1652, 'admin_consultants_export_excel', 1650, 'Consultants export excel'),
(1675, 'admin_referrals', NULL, 'Referrals'),
(1676, 'admin_referrals_history', 1675, 'Referrals History'),
(1677, 'admin_referrals_users', 1675, 'Referrals users'),
(1678, 'admin_referrals_export', 1675, 'Export Referrals'),
(1725, 'admin_regions', NULL, 'Regions'),
(1726, 'admin_regions_countries', 1725, 'countries lists'),
(1727, 'admin_regions_provinces', 1725, 'provinces lists'),
(1728, 'admin_regions_cities', 1725, 'cities lists'),
(1729, 'admin_regions_districts', 1725, 'districts lists'),
(1730, 'admin_regions_create', 1725, 'create item'),
(1731, 'admin_regions_edit', 1725, 'edit item'),
(1732, 'admin_regions_delete', 1725, 'delete item'),
(1750, 'admin_rewards', NULL, 'Rewards'),
(1751, 'admin_rewards_history', 1750, 'Rewards history'),
(1752, 'admin_rewards_settings', 1750, 'Rewards settings'),
(1753, 'admin_rewards_items', 1750, 'Rewards items'),
(1754, 'admin_rewards_item_delete', 1750, 'Reward item delete'),
(1775, 'admin_registration_packages', NULL, 'Registration packages'),
(1776, 'admin_registration_packages_lists', 1775, 'packages lists'),
(1777, 'admin_registration_packages_new', 1775, 'New package'),
(1778, 'admin_registration_packages_edit', 1775, 'Edit package'),
(1779, 'admin_registration_packages_delete', 1775, 'Delete package'),
(1780, 'admin_registration_packages_reports', 1775, 'Reports'),
(1781, 'admin_registration_packages_settings', 1775, 'Settings'),
(1800, 'admin_store', NULL, 'Store'),
(1801, 'admin_store_products', 1800, 'Products lists'),
(1802, 'admin_store_new_product', 1800, 'Create New Product'),
(1803, 'admin_store_edit_product', 1800, 'Edit Product'),
(1804, 'admin_store_delete_product', 1800, 'Delete Product'),
(1805, 'admin_store_export_products', 1800, 'Export excel Products'),
(1806, 'admin_store_categories_list', 1800, 'Store Categories Lists'),
(1807, 'admin_store_categories_create', 1800, 'Create Store Category'),
(1808, 'admin_store_categories_edit', 1800, 'Edit Store Category'),
(1809, 'admin_store_categories_delete', 1800, 'Delete Store Category'),
(1810, 'admin_store_filters_list', 1800, 'Store Filters Lists'),
(1811, 'admin_store_filters_create', 1800, 'Create Store Filter'),
(1812, 'admin_store_filters_edit', 1800, 'Edit Store Filter'),
(1813, 'admin_store_filters_delete', 1800, 'Delete Store Filter'),
(1814, 'admin_store_specifications', 1800, 'Store Specifications'),
(1815, 'admin_store_specifications_create', 1800, 'Create New Store Specification'),
(1816, 'admin_store_specifications_edit', 1800, 'Edit Store Specification'),
(1817, 'admin_store_specifications_delete', 1800, 'Delete Store Specification'),
(1818, 'admin_store_discounts', 1800, 'Store Discounts Lists'),
(1819, 'admin_store_discounts_create', 1800, 'Create New Store discount'),
(1820, 'admin_store_discounts_edit', 1800, 'Edit Store discount'),
(1821, 'admin_store_discounts_delete', 1800, 'Delete Store discount'),
(1822, 'admin_store_products_orders', 1800, 'Products Orders'),
(1823, 'admin_store_products_orders_refund', 1800, 'Products Orders Refund'),
(1824, 'admin_store_products_orders_invoice', 1800, 'Products Orders View Invoice'),
(1825, 'admin_store_products_orders_export', 1800, 'Products Orders Export Excel'),
(1826, 'admin_store_products_orders_tracking_code', 1800, 'Products Orders Tracking code'),
(1827, 'admin_store_products_reviews', 1800, 'Reviews lists'),
(1828, 'admin_store_products_reviews_status_toggle', 1800, 'Reviews status toggle (publish or hidden)'),
(1829, 'admin_store_products_reviews_detail_show', 1800, 'Review details page'),
(1830, 'admin_store_products_reviews_delete', 1800, 'Review delete'),
(1831, 'admin_store_settings', 1800, 'Store settings'),
(1832, 'admin_store_in_house_products', 1800, 'In-house products'),
(1833, 'admin_store_in_house_orders', 1800, 'In-house Products Orders'),
(1834, 'admin_store_products_sellers', 1800, 'Products Sellers'),
(1850, 'admin_webinar_assignments', NULL, 'Webinar assignments'),
(1851, 'admin_webinar_assignments_lists', 1850, 'Assignments lists'),
(1852, 'admin_webinar_assignments_students', 1850, 'Assignment students'),
(1853, 'admin_webinar_assignments_conversations', 1850, 'Assignment students conversations'),
(1875, 'admin_users_not_access_content', NULL, 'Users do not have access to the content'),
(1876, 'admin_users_not_access_content_lists', 1875, 'Users lists'),
(1877, 'admin_users_not_access_content_toggle', 1875, 'Toggle active/inactive users to view content'),
(1900, 'admin_bundles', NULL, 'Bundles'),
(1901, 'admin_bundles_list', 1900, 'Bundles Lists'),
(1902, 'admin_bundles_create', 1900, 'Create new Bundle'),
(1903, 'admin_bundles_edit', 1900, 'Edit bundle'),
(1904, 'admin_bundles_delete', 1900, 'Delete bundle'),
(1905, 'admin_bundles_export_excel', 1900, 'Export excel'),
(1925, 'admin_forum', NULL, 'Forums'),
(1926, 'admin_forum_list', 1925, 'Forums Lists'),
(1927, 'admin_forum_create', 1925, 'Forums create'),
(1928, 'admin_forum_edit', 1925, 'Forums edit'),
(1929, 'admin_forum_delete', 1925, 'Forums delete'),
(1930, 'admin_forum_topics_lists', 1925, 'Forums topics lists'),
(1931, 'admin_forum_topics_create', 1925, 'Forums topics create'),
(1932, 'admin_forum_topics_delete', 1925, 'Forums topics delete'),
(1933, 'admin_forum_topics_posts', 1925, 'Forums topic posts'),
(1934, 'admin_forum_topics_create_posts', 1925, 'Forums topic store posts'),
(1950, 'admin_featured_topics', NULL, 'Featured topics'),
(1951, 'admin_featured_topics_list', 1950, 'Featured topics Lists'),
(1952, 'admin_featured_topics_create', 1950, 'Featured topics create'),
(1953, 'admin_featured_topics_edit', 1950, 'Featured topics edit'),
(1954, 'admin_featured_topics_delete', 1950, 'Featured topics delete'),
(1975, 'admin_recommended_topics', NULL, 'Recommended topics'),
(1976, 'admin_recommended_topics_list', 1975, 'Recommended topics Lists'),
(1977, 'admin_recommended_topics_create', 1975, 'Recommended topics create'),
(1978, 'admin_recommended_topics_edit', 1975, 'Recommended topics edit'),
(1979, 'admin_recommended_topics_delete', 1975, 'Recommended topics delete'),
(2000, 'admin_advertising_modal', NULL, 'Advertising modal'),
(2001, 'admin_advertising_modal_config', 2000, 'Set Advertising modal'),
(2015, 'admin_enrollment', NULL, 'Enrollment'),
(2016, 'admin_enrollment_history', 2015, 'Enrollment History'),
(2017, 'admin_enrollment_add_student_to_items', 2015, 'Enrollment Add Student To Items'),
(2018, 'admin_enrollment_block_access', 2015, 'Enrollment Block Access'),
(2019, 'admin_enrollment_enable_access', 2015, 'Enrollment Enable Access'),
(2020, 'admin_enrollment_export', 2015, 'Enrollment Export History'),
(2030, 'admin_delete_account_requests', NULL, 'Delete Account Requests'),
(2031, 'admin_delete_account_requests_lists', 2030, 'Delete Account Requests Lists'),
(2032, 'admin_delete_account_requests_confirm', 2030, 'Delete Account Requests Confirm'),
(2050, 'admin_upcoming_courses', NULL, 'Upcoming Course'),
(2051, 'admin_upcoming_courses_list', 2050, 'Lists'),
(2052, 'admin_upcoming_courses_create', 2050, 'Create'),
(2053, 'admin_upcoming_courses_edit', 2050, 'Edit and Update'),
(2054, 'admin_upcoming_courses_delete', 2050, 'Delete'),
(2055, 'admin_upcoming_courses_followers', 2050, 'Followers'),
(2070, 'admin_installments', NULL, 'Installments'),
(2071, 'admin_installments_list', 2070, 'Lists'),
(2072, 'admin_installments_create', 2070, 'Create'),
(2073, 'admin_installments_edit', 2070, 'Edit and Update'),
(2074, 'admin_installments_delete', 2070, 'Delete'),
(2075, 'admin_installments_settings', 2070, 'Settings'),
(2076, 'admin_installments_purchases', 2070, 'Purchases'),
(2077, 'admin_installments_overdue_lists', 2070, 'Overdue Installments'),
(2078, 'admin_installments_overdue_history', 2070, 'Overdue History'),
(2079, 'admin_installments_verification_requests', 2070, 'Verification Requests'),
(2080, 'admin_installments_verified_users', 2070, 'Verified Users'),
(2081, 'admin_installments_orders', 2070, 'Approve/Reject/Refund Requests'),
(2090, 'admin_registration_bonus', NULL, 'Registration Bonus'),
(2091, 'admin_registration_bonus_history', 2090, 'History'),
(2092, 'admin_registration_bonus_settings', 2090, 'Settings'),
(2093, 'admin_registration_bonus_export_excel', 2090, 'Export Excel'),
(3000, 'admin_floating_bar', NULL, 'Top/Bottom Floating Bar'),
(3001, 'admin_floating_bar_create', 3000, 'Create/Edit'),
(3010, 'admin_cashback', NULL, 'Cashback'),
(3011, 'admin_cashback_rules', 3010, 'Rules'),
(3012, 'admin_cashback_transactions', 3010, 'Transactions'),
(3013, 'admin_cashback_history', 3010, 'History'),
(3020, 'admin_waitlists', NULL, 'Waitlists'),
(3021, 'admin_waitlists_lists', 3020, 'Lists'),
(3022, 'admin_waitlists_users', 3020, 'Joined Users'),
(3023, 'admin_waitlists_exports', 3020, 'Export excel lists'),
(3024, 'admin_waitlists_clear_list', 3020, 'Clear lists'),
(3025, 'admin_waitlists_disable', 3020, 'Disable'),
(3030, 'admin_gift', NULL, 'Gifts'),
(3031, 'admin_gift_history', 3030, 'History'),
(3032, 'admin_gift_send_reminder', 3030, 'Send Reminder'),
(3033, 'admin_gift_cancel', 3030, 'Cancel'),
(3034, 'admin_gift_settings', 3030, 'Settings'),
(3035, 'admin_gift_export', 3030, 'Export Excel'),
(3040, 'admin_forms', NULL, 'Forms'),
(3041, 'admin_forms_lists', 3040, 'Lists'),
(3042, 'admin_forms_create', 3040, 'Create'),
(3043, 'admin_forms_edit', 3040, 'Edit'),
(3044, 'admin_forms_delete', 3040, 'Delete'),
(3045, 'admin_forms_export', 3040, 'Export'),
(3046, 'admin_forms_submissions', 3040, 'Submissions'),
(3050, 'admin_ai_contents', NULL, 'AI Contents'),
(3051, 'admin_ai_contents_lists', 3050, 'Generated Contents Lists'),
(3052, 'admin_ai_contents_templates_lists', 3050, 'Template Lists'),
(3053, 'admin_ai_contents_templates_create', 3050, 'Template Create'),
(3054, 'admin_ai_contents_templates_edit', 3050, 'Template Edit'),
(3055, 'admin_ai_contents_templates_delete', 3050, 'Template Delete'),
(3056, 'admin_ai_contents_settings', 3050, 'Settings');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` int(10) UNSIGNED NOT NULL,
  `webinar_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(64) NOT NULL,
  `date` int(11) NOT NULL,
  `duration` int(11) NOT NULL,
  `link` varchar(128) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(10) UNSIGNED NOT NULL,
  `page` enum('general','financial','personalization','notifications','seo','customization','other') NOT NULL DEFAULT 'other',
  `name` varchar(255) NOT NULL,
  `value` text NOT NULL,
  `updated_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `setting_translations`
--

CREATE TABLE `setting_translations` (
  `id` int(10) UNSIGNED NOT NULL,
  `setting_id` int(10) UNSIGNED NOT NULL,
  `locale` varchar(10) NOT NULL,
  `value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `supports`
--

CREATE TABLE `supports` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `webinar_id` int(10) UNSIGNED DEFAULT NULL,
  `department_id` int(10) UNSIGNED DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `status` enum('open','close','replied') NOT NULL DEFAULT 'open',
  `created_at` int(10) UNSIGNED DEFAULT NULL,
  `updated_at` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `support_departments`
--

CREATE TABLE `support_departments` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `created_at` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE `tags` (
  `id` int(10) UNSIGNED NOT NULL,
  `webinar_id` int(10) UNSIGNED NOT NULL,
  `bundle_id` int(10) UNSIGNED DEFAULT NULL,
  `title` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

CREATE TABLE `tickets` (
  `id` int(10) UNSIGNED NOT NULL,
  `webinar_id` int(10) UNSIGNED NOT NULL,
  `bundle_id` int(10) UNSIGNED DEFAULT NULL,
  `title` varchar(64) NOT NULL,
  `start_date` int(11) DEFAULT NULL,
  `end_date` int(11) DEFAULT NULL,
  `discount` int(11) NOT NULL,
  `capacity` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `full_name` varchar(128) DEFAULT NULL,
  `role_name` varchar(64) NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL,
  `avatar` varchar(64) DEFAULT NULL,
  `mobile` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `google_id` varchar(255) DEFAULT NULL,
  `facebook_id` varchar(255) DEFAULT NULL,
  `remember_token` varchar(255) DEFAULT NULL,
  `status` enum('active','pending','inactive') NOT NULL DEFAULT 'active',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `role_name`, `role_id`, `avatar`, `mobile`, `email`, `password`, `google_id`, `facebook_id`, `remember_token`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'admin', 'admin', 2, NULL, '09379332830', 'admin@gmail.com', '$2y$10$WMki4DNyWgyP426OjH5s0Oa8sOSzgGdG187KpafZvCi4LrhqduVWi', NULL, NULL, NULL, 'active', 1597826952, 1597826952, NULL),
(2, 'user', 'user', 1, NULL, '09379332831', 'user@gmail.com', '$2y$10$TRYlOMdo3e.gwAE/HRL2C.dPTuM4Zc5sw1SZjmUcswNyuUCPWG7Dy', NULL, NULL, NULL, 'active', 1597826952, 1597826952, NULL),
(3, 'teacher', 'teacher', 4, NULL, '09379332832', 'teacher@gmail.com', '$2y$10$fkj1vQtVCmhtIcVLlapevu0iyWmB4vgrOYZhSh8SMyJzYVxinQ6yu', NULL, NULL, NULL, 'active', 1597826952, 1597826952, NULL),
(4, 'organ', 'organization', 3, NULL, '09379332833', 'organ@gmail.com', '$2y$10$566QMmQYn3dqlYSveTXIjOuqCJ2Y.siCvvvLiYTH.KJUiJ7oUsQaS', NULL, NULL, NULL, 'active', 1597826952, 1597826952, NULL),
(5, 'Demo Instructor', 'teacher', 4, NULL, '09379332834', 'instructor@demo.com', '$2y$10$yNGp9qvzgs8e3s/mKJzQZOeyxcoYyQykT96v62FPuDvpJw4IMzzQq', NULL, NULL, NULL, 'active', 1772755126, 1772755126, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `webinars`
--

CREATE TABLE `webinars` (
  `id` int(10) UNSIGNED NOT NULL,
  `teacher_id` int(10) UNSIGNED NOT NULL,
  `creator_user_id` int(10) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED NOT NULL,
  `type` enum('webinar','course','text_lesson') DEFAULT NULL,
  `title` varchar(64) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `start_date` int(11) NOT NULL,
  `duration` int(10) UNSIGNED NOT NULL,
  `start_time` int(11) NOT NULL,
  `end_time` int(11) NOT NULL,
  `seo_description` varchar(128) DEFAULT NULL,
  `image_cover` varchar(255) NOT NULL,
  `video_demo` varchar(255) DEFAULT NULL,
  `capacity` int(10) UNSIGNED NOT NULL,
  `price` double(15,3) UNSIGNED NOT NULL,
  `description` text DEFAULT NULL,
  `support` tinyint(1) NOT NULL DEFAULT 0,
  `partner_instructor` tinyint(1) NOT NULL DEFAULT 0,
  `subscribe` tinyint(1) NOT NULL DEFAULT 0,
  `message_for_reviewer` text DEFAULT NULL,
  `status` enum('active','pending','is_draft','inactive') NOT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `webinar_filter_option`
--

CREATE TABLE `webinar_filter_option` (
  `id` int(10) UNSIGNED NOT NULL,
  `webinar_id` int(10) UNSIGNED NOT NULL,
  `filter_option_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `webinar_partner_teacher`
--

CREATE TABLE `webinar_partner_teacher` (
  `id` int(10) UNSIGNED NOT NULL,
  `webinar_id` int(10) UNSIGNED NOT NULL,
  `teacher_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `webinar_reviews`
--

CREATE TABLE `webinar_reviews` (
  `id` int(10) UNSIGNED NOT NULL,
  `webinar_id` int(10) UNSIGNED NOT NULL,
  `bundle_id` int(10) UNSIGNED DEFAULT NULL,
  `creator_user_id` int(10) UNSIGNED NOT NULL,
  `content_quality` int(10) UNSIGNED NOT NULL,
  `instructor_skills` int(10) UNSIGNED NOT NULL,
  `purchase_worth` int(10) UNSIGNED NOT NULL,
  `support_quality` int(10) UNSIGNED NOT NULL,
  `rates` int(10) UNSIGNED NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` int(10) UNSIGNED NOT NULL,
  `status` enum('pending','active') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounting`
--
ALTER TABLE `accounting`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `creator_id` (`creator_id`),
  ADD KEY `webinar_id` (`webinar_id`),
  ADD KEY `bundle_id` (`bundle_id`);

--
-- Indexes for table `ai_contents`
--
ALTER TABLE `ai_contents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `service_id` (`service_id`);

--
-- Indexes for table `ai_content_templates`
--
ALTER TABLE `ai_content_templates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ai_content_template_translations`
--
ALTER TABLE `ai_content_template_translations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `locale` (`locale`),
  ADD KEY `ai_content_template_id` (`ai_content_template_id`);

--
-- Indexes for table `bundles`
--
ALTER TABLE `bundles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `slug` (`slug`),
  ADD KEY `creator_id` (`creator_id`),
  ADD KEY `teacher_id` (`teacher_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `bundle_translations`
--
ALTER TABLE `bundle_translations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `locale` (`locale`),
  ADD KEY `bundle_id` (`bundle_id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `creator_id` (`creator_id`),
  ADD KEY `webinar_id` (`webinar_id`),
  ADD KEY `bundle_id` (`bundle_id`),
  ADD KEY `product_order_id` (`product_order_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `certificates`
--
ALTER TABLE `certificates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `certificates_quiz_id_foreign` (`quiz_id`),
  ADD KEY `certificates_quiz_result_id_foreign` (`quiz_result_id`),
  ADD KEY `certificates_student_id_foreign` (`student_id`);

--
-- Indexes for table `certificates_templates`
--
ALTER TABLE `certificates_templates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `comments_webinar_id_foreign` (`webinar_id`),
  ADD KEY `comments_user_id_foreign` (`user_id`),
  ADD KEY `comments_review_id_foreign` (`review_id`);

--
-- Indexes for table `currencies`
--
ALTER TABLE `currencies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order` (`order`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `faqs`
--
ALTER TABLE `faqs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `faqs_webinar_id_foreign` (`webinar_id`);

--
-- Indexes for table `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`),
  ADD KEY `favorites_webinar_id_foreign` (`webinar_id`),
  ADD KEY `favorites_user_id_foreign` (`user_id`);

--
-- Indexes for table `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `files_webinar_id_foreign` (`webinar_id`);

--
-- Indexes for table `filters`
--
ALTER TABLE `filters`
  ADD PRIMARY KEY (`id`),
  ADD KEY `filters_category_id_foreign` (`category_id`);

--
-- Indexes for table `filter_options`
--
ALTER TABLE `filter_options`
  ADD PRIMARY KEY (`id`),
  ADD KEY `filter_options_filter_id_foreign` (`filter_id`);

--
-- Indexes for table `group_users`
--
ALTER TABLE `group_users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `group_id` (`group_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `meetings`
--
ALTER TABLE `meetings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `meetings_creator_id_foreign` (`creator_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `group_id` (`group_id`),
  ADD KEY `webinar_id` (`webinar_id`);

--
-- Indexes for table `notifications_status`
--
ALTER TABLE `notifications_status`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notification_id` (`notification_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `offline_payments`
--
ALTER TABLE `offline_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `offline_bank_id` (`offline_bank_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `payment_channels`
--
ALTER TABLE `payment_channels`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payouts`
--
ALTER TABLE `payouts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `user_selected_bank_id` (`user_selected_bank_id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `permissions_role_id_index` (`role_id`),
  ADD KEY `permissions_section_id_index` (`section_id`);

--
-- Indexes for table `prerequisites`
--
ALTER TABLE `prerequisites`
  ADD PRIMARY KEY (`id`),
  ADD KEY `prerequisites_webinar_id_foreign` (`webinar_id`),
  ADD KEY `prerequisites_prerequisite_id_foreign` (`prerequisite_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `type` (`type`),
  ADD KEY `slug` (`slug`),
  ADD KEY `creator_id` (`creator_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `product_orders`
--
ALTER TABLE `product_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `sale_id` (`sale_id`);

--
-- Indexes for table `product_translations`
--
ALTER TABLE `product_translations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `locale` (`locale`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `purchases`
--
ALTER TABLE `purchases`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchases_webinar_id_foreign` (`webinar_id`),
  ADD KEY `purchases_user_id_foreign` (`user_id`);

--
-- Indexes for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quizzes_webinar_id_foreign` (`webinar_id`);

--
-- Indexes for table `quizzes_questions`
--
ALTER TABLE `quizzes_questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quizzes_questions_quiz_id_foreign` (`quiz_id`),
  ADD KEY `quizzes_questions_creator_user_id_foreign` (`creator_user_id`);

--
-- Indexes for table `quizzes_questions_answers`
--
ALTER TABLE `quizzes_questions_answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quizzes_questions_answers_question_id_foreign` (`question_id`),
  ADD KEY `quizzes_questions_answers_creator_user_id_foreign` (`creator_user_id`);

--
-- Indexes for table `quizzes_results`
--
ALTER TABLE `quizzes_results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quizzes_results_quiz_id_foreign` (`quiz_id`),
  ADD KEY `quizzes_results_user_id_foreign` (`user_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `buyer_id` (`buyer_id`),
  ADD KEY `seller_id` (`seller_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `webinar_id` (`webinar_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `bundle_id` (`bundle_id`);

--
-- Indexes for table `sales_log`
--
ALTER TABLE `sales_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_id` (`sale_id`);

--
-- Indexes for table `sections`
--
ALTER TABLE `sections`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_webinar_id_foreign` (`webinar_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `name` (`name`);

--
-- Indexes for table `setting_translations`
--
ALTER TABLE `setting_translations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `setting_id` (`setting_id`);

--
-- Indexes for table `supports`
--
ALTER TABLE `supports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `webinar_id` (`webinar_id`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `support_departments`
--
ALTER TABLE `support_departments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tags_webinar_id_foreign` (`webinar_id`);

--
-- Indexes for table `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tickets_webinar_id_foreign` (`webinar_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_mobile_unique` (`mobile`);

--
-- Indexes for table `webinars`
--
ALTER TABLE `webinars`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `webinars_slug_unique` (`slug`),
  ADD KEY `webinars_teacher_id_foreign` (`teacher_id`),
  ADD KEY `webinars_creator_user_id_foreign` (`creator_user_id`),
  ADD KEY `webinars_category_id_foreign` (`category_id`),
  ADD KEY `webinars_slug_index` (`slug`);

--
-- Indexes for table `webinar_filter_option`
--
ALTER TABLE `webinar_filter_option`
  ADD PRIMARY KEY (`id`),
  ADD KEY `webinar_filter_option_webinar_id_foreign` (`webinar_id`),
  ADD KEY `webinar_filter_option_filter_option_id_foreign` (`filter_option_id`);

--
-- Indexes for table `webinar_partner_teacher`
--
ALTER TABLE `webinar_partner_teacher`
  ADD PRIMARY KEY (`id`),
  ADD KEY `webinar_partner_teacher_webinar_id_foreign` (`webinar_id`),
  ADD KEY `webinar_partner_teacher_teacher_id_foreign` (`teacher_id`);

--
-- Indexes for table `webinar_reviews`
--
ALTER TABLE `webinar_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `webinar_reviews_creator_user_id_foreign` (`creator_user_id`),
  ADD KEY `webinar_reviews_webinar_id_foreign` (`webinar_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounting`
--
ALTER TABLE `accounting`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ai_contents`
--
ALTER TABLE `ai_contents`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ai_content_templates`
--
ALTER TABLE `ai_content_templates`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ai_content_template_translations`
--
ALTER TABLE `ai_content_template_translations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bundles`
--
ALTER TABLE `bundles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bundle_translations`
--
ALTER TABLE `bundle_translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `certificates`
--
ALTER TABLE `certificates`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `certificates_templates`
--
ALTER TABLE `certificates_templates`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `currencies`
--
ALTER TABLE `currencies`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `faqs`
--
ALTER TABLE `faqs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `files`
--
ALTER TABLE `files`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `filters`
--
ALTER TABLE `filters`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `filter_options`
--
ALTER TABLE `filter_options`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `group_users`
--
ALTER TABLE `group_users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `meetings`
--
ALTER TABLE `meetings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications_status`
--
ALTER TABLE `notifications_status`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `offline_payments`
--
ALTER TABLE `offline_payments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_channels`
--
ALTER TABLE `payment_channels`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `payouts`
--
ALTER TABLE `payouts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1410;

--
-- AUTO_INCREMENT for table `prerequisites`
--
ALTER TABLE `prerequisites`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_orders`
--
ALTER TABLE `product_orders`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_translations`
--
ALTER TABLE `product_translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchases`
--
ALTER TABLE `purchases`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quizzes_questions`
--
ALTER TABLE `quizzes_questions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quizzes_questions_answers`
--
ALTER TABLE `quizzes_questions_answers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quizzes_results`
--
ALTER TABLE `quizzes_results`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sales_log`
--
ALTER TABLE `sales_log`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3057;

--
-- AUTO_INCREMENT for table `sessions`
--
ALTER TABLE `sessions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setting_translations`
--
ALTER TABLE `setting_translations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `supports`
--
ALTER TABLE `supports`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `support_departments`
--
ALTER TABLE `support_departments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tags`
--
ALTER TABLE `tags`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `webinars`
--
ALTER TABLE `webinars`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `webinar_filter_option`
--
ALTER TABLE `webinar_filter_option`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `webinar_partner_teacher`
--
ALTER TABLE `webinar_partner_teacher`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `webinar_reviews`
--
ALTER TABLE `webinar_reviews`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `certificates`
--
ALTER TABLE `certificates`
  ADD CONSTRAINT `certificates_quiz_id_foreign` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `certificates_quiz_result_id_foreign` FOREIGN KEY (`quiz_result_id`) REFERENCES `quizzes_results` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `certificates_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_review_id_foreign` FOREIGN KEY (`review_id`) REFERENCES `webinar_reviews` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_webinar_id_foreign` FOREIGN KEY (`webinar_id`) REFERENCES `webinars` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `faqs`
--
ALTER TABLE `faqs`
  ADD CONSTRAINT `faqs_webinar_id_foreign` FOREIGN KEY (`webinar_id`) REFERENCES `webinars` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `favorites_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `favorites_webinar_id_foreign` FOREIGN KEY (`webinar_id`) REFERENCES `webinars` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `files`
--
ALTER TABLE `files`
  ADD CONSTRAINT `files_webinar_id_foreign` FOREIGN KEY (`webinar_id`) REFERENCES `webinars` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `filters`
--
ALTER TABLE `filters`
  ADD CONSTRAINT `filters_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `filter_options`
--
ALTER TABLE `filter_options`
  ADD CONSTRAINT `filter_options_filter_id_foreign` FOREIGN KEY (`filter_id`) REFERENCES `filters` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `meetings`
--
ALTER TABLE `meetings`
  ADD CONSTRAINT `meetings_creator_id_foreign` FOREIGN KEY (`creator_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `permissions`
--
ALTER TABLE `permissions`
  ADD CONSTRAINT `permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `permissions_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`);

--
-- Constraints for table `prerequisites`
--
ALTER TABLE `prerequisites`
  ADD CONSTRAINT `prerequisites_prerequisite_id_foreign` FOREIGN KEY (`prerequisite_id`) REFERENCES `webinars` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `prerequisites_webinar_id_foreign` FOREIGN KEY (`webinar_id`) REFERENCES `webinars` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `purchases`
--
ALTER TABLE `purchases`
  ADD CONSTRAINT `purchases_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchases_webinar_id_foreign` FOREIGN KEY (`webinar_id`) REFERENCES `webinars` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD CONSTRAINT `quizzes_webinar_id_foreign` FOREIGN KEY (`webinar_id`) REFERENCES `webinars` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quizzes_questions`
--
ALTER TABLE `quizzes_questions`
  ADD CONSTRAINT `quizzes_questions_creator_user_id_foreign` FOREIGN KEY (`creator_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quizzes_questions_quiz_id_foreign` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quizzes_questions_answers`
--
ALTER TABLE `quizzes_questions_answers`
  ADD CONSTRAINT `quizzes_questions_answers_creator_user_id_foreign` FOREIGN KEY (`creator_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quizzes_questions_answers_question_id_foreign` FOREIGN KEY (`question_id`) REFERENCES `quizzes_questions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quizzes_results`
--
ALTER TABLE `quizzes_results`
  ADD CONSTRAINT `quizzes_results_quiz_id_foreign` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quizzes_results_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sessions`
--
ALTER TABLE `sessions`
  ADD CONSTRAINT `sessions_webinar_id_foreign` FOREIGN KEY (`webinar_id`) REFERENCES `webinars` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tags`
--
ALTER TABLE `tags`
  ADD CONSTRAINT `tags_webinar_id_foreign` FOREIGN KEY (`webinar_id`) REFERENCES `webinars` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `tickets_webinar_id_foreign` FOREIGN KEY (`webinar_id`) REFERENCES `webinars` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `webinars`
--
ALTER TABLE `webinars`
  ADD CONSTRAINT `webinars_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `webinars_creator_user_id_foreign` FOREIGN KEY (`creator_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `webinars_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `webinar_filter_option`
--
ALTER TABLE `webinar_filter_option`
  ADD CONSTRAINT `webinar_filter_option_filter_option_id_foreign` FOREIGN KEY (`filter_option_id`) REFERENCES `filter_options` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `webinar_filter_option_webinar_id_foreign` FOREIGN KEY (`webinar_id`) REFERENCES `webinars` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `webinar_partner_teacher`
--
ALTER TABLE `webinar_partner_teacher`
  ADD CONSTRAINT `webinar_partner_teacher_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `webinar_partner_teacher_webinar_id_foreign` FOREIGN KEY (`webinar_id`) REFERENCES `webinars` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `webinar_reviews`
--
ALTER TABLE `webinar_reviews`
  ADD CONSTRAINT `webinar_reviews_creator_user_id_foreign` FOREIGN KEY (`creator_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `webinar_reviews_webinar_id_foreign` FOREIGN KEY (`webinar_id`) REFERENCES `webinars` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
