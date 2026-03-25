-- Comprehensive SQL script to add ALL commonly missing ID columns
-- Run this in phpMyAdmin to fix all missing ID column issues at once
-- If you get "Duplicate column name" errors, those columns already exist - that's fine!

SET FOREIGN_KEY_CHECKS=0;

-- ============================================
-- USERS TABLE - Add ALL missing columns from KFM.sql
-- ============================================
-- Note: These will error if columns already exist - that's fine, just means they're already there!

-- Core organization and profile columns
ALTER TABLE `users` 
ADD COLUMN `organ_id` int DEFAULT NULL AFTER `role_id`;

ALTER TABLE `users` 
ADD COLUMN `bio` varchar(128) DEFAULT NULL AFTER `email`;

ALTER TABLE `users` 
ADD COLUMN `logged_count` int(10) unsigned NOT NULL DEFAULT 0 AFTER `remember_token`;

ALTER TABLE `users` 
ADD COLUMN `verified` tinyint(1) NOT NULL DEFAULT 0 AFTER `remember_token`;

ALTER TABLE `users` 
ADD COLUMN `financial_approval` tinyint(1) NOT NULL DEFAULT 0 AFTER `verified`;

ALTER TABLE `users` 
ADD COLUMN `installment_approval` tinyint(1) DEFAULT 0 AFTER `financial_approval`;

ALTER TABLE `users` 
ADD COLUMN `enable_installments` tinyint(1) DEFAULT 1 AFTER `installment_approval`;

ALTER TABLE `users` 
ADD COLUMN `disable_cashback` tinyint(1) DEFAULT 0 AFTER `enable_installments`;

ALTER TABLE `users` 
ADD COLUMN `enable_registration_bonus` tinyint(1) NOT NULL DEFAULT 0 AFTER `disable_cashback`;

ALTER TABLE `users` 
ADD COLUMN `registration_bonus_amount` double(15,2) DEFAULT NULL AFTER `enable_registration_bonus`;

ALTER TABLE `users` 
ADD COLUMN `avatar_settings` varchar(255) DEFAULT NULL AFTER `avatar`;

ALTER TABLE `users` 
ADD COLUMN `cover_img` varchar(255) DEFAULT NULL AFTER `avatar_settings`;

ALTER TABLE `users` 
ADD COLUMN `headline` varchar(255) DEFAULT NULL AFTER `cover_img`;

ALTER TABLE `users` 
ADD COLUMN `about` text DEFAULT NULL AFTER `headline`;

ALTER TABLE `users` 
ADD COLUMN `address` varchar(255) DEFAULT NULL AFTER `about`;

ALTER TABLE `users` 
ADD COLUMN `country_id` int(10) unsigned DEFAULT NULL AFTER `address`;

ALTER TABLE `users` 
ADD COLUMN `province_id` int(10) unsigned DEFAULT NULL AFTER `country_id`;

ALTER TABLE `users` 
ADD COLUMN `city_id` int(10) unsigned DEFAULT NULL AFTER `province_id`;

ALTER TABLE `users` 
ADD COLUMN `district_id` int(10) unsigned DEFAULT NULL AFTER `city_id`;

ALTER TABLE `users` 
ADD COLUMN `level_of_training` bit(3) DEFAULT NULL;

ALTER TABLE `users` 
ADD COLUMN `meeting_type` enum('all','in_person','online') NOT NULL DEFAULT 'all' AFTER `level_of_training`;

ALTER TABLE `users` 
ADD COLUMN `access_content` tinyint(1) NOT NULL DEFAULT 1 AFTER `status`;

ALTER TABLE `users` 
ADD COLUMN `enable_ai_content` tinyint(1) NOT NULL DEFAULT 0 AFTER `access_content`;

ALTER TABLE `users` 
ADD COLUMN `language` varchar(255) DEFAULT NULL AFTER `enable_ai_content`;

ALTER TABLE `users` 
ADD COLUMN `currency` varchar(255) DEFAULT NULL AFTER `language`;

ALTER TABLE `users` 
ADD COLUMN `timezone` varchar(255) DEFAULT NULL AFTER `currency`;

ALTER TABLE `users` 
ADD COLUMN `newsletter` tinyint(1) NOT NULL DEFAULT 0 AFTER `timezone`;

ALTER TABLE `users` 
ADD COLUMN `public_message` tinyint(1) NOT NULL DEFAULT 0 AFTER `newsletter`;

ALTER TABLE `users` 
ADD COLUMN `identity_scan` varchar(128) DEFAULT NULL AFTER `public_message`;

ALTER TABLE `users` 
ADD COLUMN `certificate` varchar(128) DEFAULT NULL AFTER `identity_scan`;

ALTER TABLE `users` 
ADD COLUMN `commission` int(10) unsigned DEFAULT NULL AFTER `certificate`;

ALTER TABLE `users` 
ADD COLUMN `affiliate` tinyint(1) NOT NULL DEFAULT 1 AFTER `commission`;

ALTER TABLE `users` 
ADD COLUMN `can_create_store` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Despite disabling the store feature in the settings, we can enable this feature for that user through the edit page of a user and turning on the store toggle.' AFTER `affiliate`;

ALTER TABLE `users` 
ADD COLUMN `ban` tinyint(1) NOT NULL DEFAULT 0 AFTER `can_create_store`;

ALTER TABLE `users` 
ADD COLUMN `ban_start_at` int(10) unsigned DEFAULT NULL AFTER `ban`;

ALTER TABLE `users` 
ADD COLUMN `ban_end_at` int(10) unsigned DEFAULT NULL AFTER `ban_start_at`;

ALTER TABLE `users` 
ADD COLUMN `offline` tinyint(1) NOT NULL DEFAULT 0 AFTER `ban_end_at`;

ALTER TABLE `users` 
ADD COLUMN `offline_message` text DEFAULT NULL AFTER `offline`;

-- ============================================
-- GROUPS TABLE - Add missing columns (CRITICAL)
-- ============================================
-- Add discount column if missing
ALTER TABLE `groups` 
ADD COLUMN `discount` int DEFAULT NULL AFTER `name`;

-- Add commission column if missing
ALTER TABLE `groups` 
ADD COLUMN `commission` int DEFAULT NULL AFTER `discount`;

-- Add status column if missing (FIXES YOUR CURRENT ERROR!)
ALTER TABLE `groups` 
ADD COLUMN `status` enum('active','inactive') NOT NULL DEFAULT 'inactive' AFTER `commission`;

-- ============================================
-- COMMENTS TABLE - Add all missing ID columns
-- ============================================
-- Add bundle_id if missing
ALTER TABLE `comments` 
ADD COLUMN `bundle_id` int(10) unsigned DEFAULT NULL AFTER `webinar_id`;

-- Add blog_id if missing
ALTER TABLE `comments` 
ADD COLUMN `blog_id` int(10) unsigned DEFAULT NULL AFTER `bundle_id`;

-- Add product_id if missing
ALTER TABLE `comments` 
ADD COLUMN `product_id` int(10) unsigned DEFAULT NULL AFTER `blog_id`;

-- Add product_review_id if missing
ALTER TABLE `comments` 
ADD COLUMN `product_review_id` int(10) unsigned DEFAULT NULL AFTER `product_id`;

-- Add upcoming_course_id if missing (if upcoming_courses table exists)
ALTER TABLE `comments` 
ADD COLUMN `upcoming_course_id` int(10) unsigned DEFAULT NULL AFTER `product_review_id`;

-- ============================================
-- SALES TABLE - Add missing ID columns
-- ============================================
-- bundle_id should already be in our create script, but add it here if missing
ALTER TABLE `sales` 
ADD COLUMN `bundle_id` int(10) unsigned DEFAULT NULL AFTER `webinar_id`;

-- Update sales type enum to include bundle if needed
ALTER TABLE `sales` 
MODIFY COLUMN `type` enum('webinar','meeting','subscribe','promotion','registration_package','product','bundle') COLLATE utf8mb4_unicode_ci NOT NULL;

-- ============================================
-- CART TABLE - Add missing ID columns
-- ============================================
-- bundle_id should already be in our create script, but add it here if missing
ALTER TABLE `cart` 
ADD COLUMN `bundle_id` int(10) unsigned DEFAULT NULL AFTER `webinar_id`;

-- ============================================
-- ORDER_ITEMS TABLE - Add missing ID columns
-- ============================================
ALTER TABLE `order_items` 
ADD COLUMN `bundle_id` int(10) unsigned DEFAULT NULL AFTER `webinar_id`;

-- ============================================
-- ACCOUNTING TABLE - Add missing ID columns
-- ============================================
ALTER TABLE `accounting` 
ADD COLUMN `bundle_id` int(10) unsigned DEFAULT NULL AFTER `webinar_id`;

-- ============================================
-- FAVORITES TABLE - Add missing ID columns
-- ============================================
ALTER TABLE `favorites` 
ADD COLUMN `bundle_id` int(10) unsigned DEFAULT NULL AFTER `webinar_id`;

-- ============================================
-- TAGS TABLE - Add missing ID columns
-- ============================================
ALTER TABLE `tags` 
ADD COLUMN `bundle_id` int(10) unsigned DEFAULT NULL AFTER `webinar_id`;

-- ============================================
-- TICKETS TABLE - Add missing ID columns
-- ============================================
ALTER TABLE `tickets` 
ADD COLUMN `bundle_id` int(10) unsigned DEFAULT NULL AFTER `webinar_id`;

-- ============================================
-- FAQS TABLE - Add missing ID columns
-- ============================================
ALTER TABLE `faqs` 
ADD COLUMN `bundle_id` int(10) unsigned DEFAULT NULL AFTER `webinar_id`;

-- ============================================
-- SPECIAL_OFFERS TABLE - Add missing ID columns
-- ============================================
ALTER TABLE `special_offers` 
ADD COLUMN `bundle_id` int(10) unsigned DEFAULT NULL AFTER `webinar_id`;

-- ============================================
-- WEBINAR_REVIEWS TABLE - Add missing ID columns
-- ============================================
ALTER TABLE `webinar_reviews` 
ADD COLUMN `bundle_id` int(10) unsigned DEFAULT NULL AFTER `webinar_id`;

-- ============================================
-- SUBSCRIBE_USES TABLE - Add missing ID columns
-- ============================================
ALTER TABLE `subscribe_uses` 
ADD COLUMN `bundle_id` int(10) unsigned DEFAULT NULL AFTER `webinar_id`;

-- ============================================
-- COMMENTS_REPORTS TABLE - Add missing ID columns
-- ============================================
ALTER TABLE `comments_reports` 
ADD COLUMN `product_id` int(10) unsigned DEFAULT NULL AFTER `webinar_id`;

-- ============================================
-- WEBINARS TABLE - Add missing columns
-- ============================================
-- Add type column if missing
ALTER TABLE `webinars` 
ADD COLUMN `type` enum('webinar','course','text_lesson') COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `category_id`;

-- Add duration column if missing
ALTER TABLE `webinars` 
ADD COLUMN `duration` int(10) unsigned NOT NULL AFTER `start_date`;

-- ============================================
-- CATEGORIES TABLE - Add missing columns
-- ============================================
-- Add order column if missing
ALTER TABLE `categories` 
ADD COLUMN `order` int(10) unsigned DEFAULT NULL;

SET FOREIGN_KEY_CHECKS=1;

-- ============================================
-- PAYOUTS TABLE - Create if missing
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
-- OFFLINE_PAYMENTS TABLE - Create if missing
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
-- AI_CONTENT_TEMPLATES TABLE - Required by AdminAuthenticate middleware
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

-- ============================================
-- NOTES:
-- ============================================
-- 1. If you get "Duplicate column name" errors, those columns already exist - skip them
-- 2. If you get "Table already exists" errors, those tables already exist - skip them
-- 3. Foreign keys are NOT added here to avoid dependency issues
-- 4. After running this, you may want to add foreign keys manually if needed
-- 5. Run the migration route to ensure all tables are created first:
--    https://kfm.brandboostup.in/emergencyDatabaseUpdate?key=run_migrations_2024_temp_key_change_me&fix=1
  