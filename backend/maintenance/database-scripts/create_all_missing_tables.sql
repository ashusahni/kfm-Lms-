-- Comprehensive SQL script to create all commonly missing tables
-- Run this in phpMyAdmin if migrations keep failing
-- This creates tables WITHOUT foreign keys to avoid dependency issues

SET FOREIGN_KEY_CHECKS=0;

-- Settings tables
CREATE TABLE IF NOT EXISTS `settings` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `page` enum('general','financial','personalization','notifications','seo','customization','other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'other',
    `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `value` text COLLATE utf8mb4_unicode_ci NOT NULL,
    `updated_at` int(11) DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `setting_translations` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `setting_id` int(10) unsigned NOT NULL,
    `locale` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
    `value` text COLLATE utf8mb4_unicode_ci NOT NULL,
    PRIMARY KEY (`id`),
    KEY `setting_id` (`setting_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Notifications tables
CREATE TABLE IF NOT EXISTS `notifications` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `user_id` int(10) unsigned DEFAULT NULL,
    `sender_id` int(10) unsigned DEFAULT NULL,
    `group_id` int(10) unsigned DEFAULT NULL,
    `webinar_id` int(10) unsigned DEFAULT NULL,
    `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
    `sender` enum('system','admin') COLLATE utf8mb4_unicode_ci DEFAULT 'system',
    `type` enum('single','all_users','students','instructors','organizations','group','course_students') COLLATE utf8mb4_unicode_ci NOT NULL,
    `created_at` int(10) unsigned NOT NULL,
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`),
    KEY `group_id` (`group_id`),
    KEY `webinar_id` (`webinar_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `notifications_status` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `user_id` int(10) unsigned NOT NULL,
    `notification_id` int(10) unsigned NOT NULL,
    `seen_at` int(10) unsigned NOT NULL,
    PRIMARY KEY (`id`),
    KEY `notification_id` (`notification_id`),
    KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Cart table
CREATE TABLE IF NOT EXISTS `cart` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `creator_id` int(10) unsigned NOT NULL,
    `webinar_id` int(10) unsigned DEFAULT NULL,
    `ticket_id` int(10) unsigned DEFAULT NULL,
    `reserve_meeting_id` int(10) unsigned DEFAULT NULL,
    `subscribe_id` int(10) unsigned DEFAULT NULL,
    `promotion_id` int(10) unsigned DEFAULT NULL,
    `bundle_id` int(10) unsigned DEFAULT NULL,
    `product_order_id` int(10) unsigned DEFAULT NULL,
    `special_offer_id` int(10) unsigned DEFAULT NULL,
    `installment_order_id` int(10) unsigned DEFAULT NULL,
    `gift_id` int(10) unsigned DEFAULT NULL,
    `batch_id` int(10) unsigned DEFAULT NULL,
    `created_at` int(10) unsigned NOT NULL,
    `updated_at` int(10) unsigned DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `creator_id` (`creator_id`),
    KEY `webinar_id` (`webinar_id`),
    KEY `bundle_id` (`bundle_id`),
    KEY `product_order_id` (`product_order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Products tables
CREATE TABLE IF NOT EXISTS `products` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `creator_id` int(10) unsigned NOT NULL,
    `type` enum('physical','virtual') COLLATE utf8mb4_unicode_ci NOT NULL,
    `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `category_id` int(10) unsigned DEFAULT NULL,
    `price` bigint(20) unsigned DEFAULT NULL,
    `point` bigint(20) unsigned DEFAULT NULL,
    `unlimited_inventory` tinyint(1) NOT NULL DEFAULT 0,
    `ordering` tinyint(1) NOT NULL DEFAULT 0,
    `inventory` int(10) unsigned DEFAULT NULL,
    `inventory_warning` int(10) unsigned DEFAULT NULL,
    `inventory_updated_at` bigint(20) unsigned DEFAULT NULL,
    `delivery_fee` bigint(20) unsigned DEFAULT NULL,
    `delivery_estimated_time` int(10) unsigned DEFAULT NULL,
    `message_for_reviewer` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `tax` int(10) unsigned DEFAULT NULL,
    `commission` int(10) unsigned DEFAULT NULL,
    `status` enum('draft','pending','active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL,
    `updated_at` bigint(20) unsigned NOT NULL,
    `created_at` bigint(20) unsigned NOT NULL,
    PRIMARY KEY (`id`),
    KEY `type` (`type`),
    KEY `slug` (`slug`),
    KEY `creator_id` (`creator_id`),
    KEY `category_id` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `product_translations` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `product_id` int(10) unsigned NOT NULL,
    `locale` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
    `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `seo_description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `summary` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `description` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `locale` (`locale`),
    KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `product_orders` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `user_id` int(10) unsigned NOT NULL,
    `product_id` int(10) unsigned NOT NULL,
    `sale_id` int(10) unsigned DEFAULT NULL,
    `quantity` int(11) NOT NULL DEFAULT 1,
    `amount` decimal(15,2) NOT NULL,
    `total_amount` decimal(15,2) NOT NULL,
    `discount` decimal(15,2) DEFAULT NULL,
    `tax` decimal(15,2) DEFAULT NULL,
    `status` enum('pending','paid','canceled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
    `created_at` int(10) unsigned NOT NULL,
    `updated_at` int(10) unsigned DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`),
    KEY `product_id` (`product_id`),
    KEY `sale_id` (`sale_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Group users table
CREATE TABLE IF NOT EXISTS `group_users` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `group_id` int(10) unsigned NOT NULL,
    `user_id` int(10) unsigned NOT NULL,
    `created_at` int(10) unsigned NOT NULL,
    PRIMARY KEY (`id`),
    KEY `group_id` (`group_id`),
    KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sales table
CREATE TABLE IF NOT EXISTS `sales` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `user_id` int(10) unsigned NOT NULL,
    `buyer_id` int(10) unsigned DEFAULT NULL,
    `seller_id` int(10) unsigned DEFAULT NULL,
    `order_id` int(10) unsigned NOT NULL,
    `webinar_id` int(10) unsigned DEFAULT NULL,
    `meeting_id` int(10) unsigned DEFAULT NULL,
    `meeting_time_id` int(10) unsigned DEFAULT NULL,
    `ticket_id` int(10) unsigned DEFAULT NULL,
    `subscribe_id` int(10) unsigned DEFAULT NULL,
    `promotion_id` int(10) unsigned DEFAULT NULL,
    `promotion_type` enum('fixed_amount','percentage') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `registration_package_id` int(10) unsigned DEFAULT NULL,
    `product_id` int(10) unsigned DEFAULT NULL,
    `bundle_id` int(10) unsigned DEFAULT NULL,
    `gift_id` int(10) unsigned DEFAULT NULL,
    `installment_order_id` int(10) unsigned DEFAULT NULL,
    `type` enum('webinar','meeting') COLLATE utf8mb4_unicode_ci NOT NULL,
    `payment_method` enum('credit','payment_channel','subscribe') COLLATE utf8mb4_unicode_ci NOT NULL,
    `amount` decimal(15,2) unsigned NOT NULL,
    `tax` decimal(15,2) unsigned DEFAULT NULL,
    `commission` decimal(15,2) unsigned DEFAULT NULL,
    `discount` decimal(15,2) unsigned DEFAULT NULL,
    `total_amount` decimal(15,2) unsigned NOT NULL,
    `product_delivery_fee` decimal(15,2) DEFAULT NULL,
    `manual_added` tinyint(1) NOT NULL DEFAULT 0,
    `batch_id` int(10) unsigned DEFAULT NULL,
    `created_at` int(10) unsigned NOT NULL,
    `refund_at` int(10) unsigned DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`),
    KEY `buyer_id` (`buyer_id`),
    KEY `seller_id` (`seller_id`),
    KEY `order_id` (`order_id`),
    KEY `webinar_id` (`webinar_id`),
    KEY `product_id` (`product_id`),
    KEY `bundle_id` (`bundle_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add order column to categories if missing (run only if column doesn't exist)
-- If you get "Duplicate column name" error, skip this - column already exists
ALTER TABLE `categories` 
ADD COLUMN `order` int(10) unsigned DEFAULT NULL;

-- Bundles tables
CREATE TABLE IF NOT EXISTS `bundles` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `creator_id` int(10) unsigned NOT NULL,
    `teacher_id` int(10) unsigned NOT NULL,
    `category_id` int(10) unsigned DEFAULT NULL,
    `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `thumbnail` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `image_cover` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `video_demo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `video_demo_source` enum('upload','youtube','vimeo','external_link','iframe') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `price` int(10) unsigned DEFAULT NULL,
    `points` int(10) unsigned DEFAULT NULL,
    `subscribe` tinyint(1) NOT NULL DEFAULT 0,
    `access_days` int(10) unsigned DEFAULT NULL,
    `message_for_reviewer` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `status` enum('active','pending','is_draft','inactive') COLLATE utf8mb4_unicode_ci NOT NULL,
    `created_at` bigint(20) unsigned NOT NULL,
    `updated_at` bigint(20) unsigned DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `slug` (`slug`),
    KEY `creator_id` (`creator_id`),
    KEY `teacher_id` (`teacher_id`),
    KEY `category_id` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `bundle_translations` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `bundle_id` int(10) unsigned NOT NULL,
    `locale` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
    `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `seo_description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `description` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `locale` (`locale`),
    KEY `bundle_id` (`bundle_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add order column to categories if missing (run only if column doesn't exist)
-- If you get "Duplicate column name" error, skip this - column already exists
ALTER TABLE `categories` 
ADD COLUMN `order` int(10) unsigned DEFAULT NULL;

-- Add type column to webinars table if missing (run only if column doesn't exist)
-- If you get "Duplicate column name" error, skip this - column already exists
ALTER TABLE `webinars` 
ADD COLUMN `type` enum('webinar','course','text_lesson') COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `category_id`;

-- Add duration column to webinars table if missing (run only if column doesn't exist)
-- If you get "Duplicate column name" error, skip this - column already exists
ALTER TABLE `webinars` 
ADD COLUMN `duration` int(10) unsigned NOT NULL AFTER `start_date`;

-- Add bundle_id column to comments table if missing (run only if column doesn't exist)
-- If you get "Duplicate column name" error, skip this - column already exists
ALTER TABLE `comments` 
ADD COLUMN `bundle_id` int(10) unsigned DEFAULT NULL AFTER `webinar_id`;

-- Add blog_id column to comments table if missing (run only if column doesn't exist)
-- If you get "Duplicate column name" error, skip this - column already exists
ALTER TABLE `comments` 
ADD COLUMN `blog_id` int(10) unsigned DEFAULT NULL AFTER `bundle_id`;

-- Add product_id column to comments table if missing
ALTER TABLE `comments` 
ADD COLUMN `product_id` int(10) unsigned DEFAULT NULL AFTER `blog_id`;

-- Add product_review_id column to comments table if missing
ALTER TABLE `comments` 
ADD COLUMN `product_review_id` int(10) unsigned DEFAULT NULL AFTER `product_id`;

-- Add bundle_id to other tables if missing
ALTER TABLE `sales` 
ADD COLUMN `bundle_id` int(10) unsigned DEFAULT NULL AFTER `webinar_id`;

ALTER TABLE `order_items` 
ADD COLUMN `bundle_id` int(10) unsigned DEFAULT NULL AFTER `webinar_id`;

ALTER TABLE `accounting` 
ADD COLUMN `bundle_id` int(10) unsigned DEFAULT NULL AFTER `webinar_id`;

ALTER TABLE `favorites` 
ADD COLUMN `bundle_id` int(10) unsigned DEFAULT NULL AFTER `webinar_id`;

ALTER TABLE `tags` 
ADD COLUMN `bundle_id` int(10) unsigned DEFAULT NULL AFTER `webinar_id`;

ALTER TABLE `tickets` 
ADD COLUMN `bundle_id` int(10) unsigned DEFAULT NULL AFTER `webinar_id`;

ALTER TABLE `faqs` 
ADD COLUMN `bundle_id` int(10) unsigned DEFAULT NULL AFTER `webinar_id`;

ALTER TABLE `special_offers` 
ADD COLUMN `bundle_id` int(10) unsigned DEFAULT NULL AFTER `webinar_id`;

ALTER TABLE `webinar_reviews` 
ADD COLUMN `bundle_id` int(10) unsigned DEFAULT NULL AFTER `webinar_id`;

ALTER TABLE `subscribe_uses` 
ADD COLUMN `bundle_id` int(10) unsigned DEFAULT NULL AFTER `webinar_id`;

-- Update sales type enum to include bundle
ALTER TABLE `sales` 
MODIFY COLUMN `type` enum('webinar','meeting','subscribe','promotion','registration_package','product','bundle') COLLATE utf8mb4_unicode_ci NOT NULL;

-- ============================================
-- PAYOUTS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS `payouts` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `user_id` int(10) unsigned NOT NULL,
    `user_selected_bank_id` int(10) unsigned DEFAULT NULL,
    `amount` decimal(13,2) NOT NULL,
    `status` enum('waiting','done','reject') COLLATE utf8mb4_unicode_ci NOT NULL,
    `created_at` int(10) unsigned NOT NULL,
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`),
    KEY `user_selected_bank_id` (`user_selected_bank_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- OFFLINE_PAYMENTS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS `offline_payments` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `user_id` int(10) unsigned NOT NULL,
    `offline_bank_id` int(10) unsigned DEFAULT NULL,
    `amount` int(10) unsigned NOT NULL,
    `bank` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `reference_number` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `attachment` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `pay_date` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `status` enum('waiting','approved','reject') COLLATE utf8mb4_unicode_ci NOT NULL,
    `created_at` int(10) unsigned NOT NULL,
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`),
    KEY `offline_bank_id` (`offline_bank_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- AI_CONTENT_TEMPLATES TABLE (Required by AdminAuthenticate middleware)
-- ============================================
CREATE TABLE IF NOT EXISTS `ai_content_templates` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `type` enum('text','image') COLLATE utf8mb4_unicode_ci NOT NULL,
    `enable_length` tinyint(1) NOT NULL DEFAULT 0,
    `length` int(10) unsigned DEFAULT NULL,
    `image_size` enum('256','512','1024') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `enable` tinyint(1) NOT NULL DEFAULT 0,
    `created_at` bigint(20) unsigned NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- AI_CONTENT_TEMPLATE_TRANSLATIONS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS `ai_content_template_translations` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `ai_content_template_id` int(10) unsigned NOT NULL,
    `locale` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
    `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `prompt` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `locale` (`locale`),
    KEY `ai_content_template_id` (`ai_content_template_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- AI_CONTENTS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS `ai_contents` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `user_id` int(10) unsigned NOT NULL,
    `service_type` enum('text','image') COLLATE utf8mb4_unicode_ci NOT NULL,
    `service_id` int(10) unsigned DEFAULT NULL,
    `keyword` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `language` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `prompt` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `result` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `created_at` bigint(20) unsigned NOT NULL,
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`),
    KEY `service_id` (`service_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- ACCOUNTING TABLE (Required by DashboardController)
-- ============================================
CREATE TABLE IF NOT EXISTS `accounting` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `user_id` int(10) unsigned DEFAULT NULL,
    `creator_id` int(10) unsigned DEFAULT NULL,
    `webinar_id` int(10) unsigned DEFAULT NULL,
    `meeting_id` int(10) unsigned DEFAULT NULL,
    `meeting_time_id` int(10) unsigned DEFAULT NULL,
    `bundle_id` int(10) unsigned DEFAULT NULL,
    `installment_order_id` int(10) unsigned DEFAULT NULL,
    `system` tinyint(1) NOT NULL DEFAULT 0,
    `tax` tinyint(1) NOT NULL DEFAULT 0,
    `amount` int(11) NOT NULL,
    `type` enum('addiction','deduction') COLLATE utf8mb4_unicode_ci NOT NULL,
    `type_account` enum('income','asset') COLLATE utf8mb4_unicode_ci NOT NULL,
    `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `affiliate` tinyint(1) NOT NULL DEFAULT 0,
    `created_at` int(10) unsigned NOT NULL,
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`),
    KEY `creator_id` (`creator_id`),
    KEY `webinar_id` (`webinar_id`),
    KEY `bundle_id` (`bundle_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- SALES_LOG TABLE (Required by DashboardController::getNewSalesCount)
-- ============================================
CREATE TABLE IF NOT EXISTS `sales_log` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `sale_id` int(10) unsigned NOT NULL,
    `viewed_at` int(10) unsigned NOT NULL,
    PRIMARY KEY (`id`),
    KEY `sale_id` (`sale_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- SUPPORTS TABLE (Required by DashboardController::getNewTicketsCount)
-- ============================================
CREATE TABLE IF NOT EXISTS `supports` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `user_id` int(10) unsigned NOT NULL,
    `webinar_id` int(10) unsigned DEFAULT NULL,
    `department_id` int(10) unsigned DEFAULT NULL,
    `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `status` enum('open','close','replied') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
    `created_at` int(10) unsigned DEFAULT NULL,
    `updated_at` int(10) unsigned DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`),
    KEY `webinar_id` (`webinar_id`),
    KEY `department_id` (`department_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- SUPPORT_DEPARTMENTS TABLE (Required by supports table)
-- ============================================
CREATE TABLE IF NOT EXISTS `support_departments` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `created_at` int(10) unsigned DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- CURRENCIES TABLE (Required by MultiCurrency mixin in AdminAuthenticate)
-- ============================================
CREATE TABLE IF NOT EXISTS `currencies` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `currency` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `currency_position` enum('left','right','left_with_space','right_with_space') COLLATE utf8mb4_unicode_ci NOT NULL,
    `currency_separator` enum('dot','comma') COLLATE utf8mb4_unicode_ci NOT NULL,
    `currency_decimal` int(10) unsigned DEFAULT NULL,
    `exchange_rate` float DEFAULT NULL,
    `order` int(10) unsigned DEFAULT NULL,
    `created_at` bigint(20) unsigned NOT NULL,
    PRIMARY KEY (`id`),
    KEY `order` (`order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- PAYMENT_CHANNELS TABLE (Required by PaymentChannelsTableSeeder)
-- ============================================
CREATE TABLE IF NOT EXISTS `payment_channels` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `class_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
    `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `settings` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `created_at` int(10) unsigned NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS=1;
