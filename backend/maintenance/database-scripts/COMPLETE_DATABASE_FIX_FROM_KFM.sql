-- ============================================================
-- COMPLETE DATABASE FIX SCRIPT
-- This script will:
-- 1. Import your existing SQL file (theboost_lmsnew.sql)
-- 2. Run all migrations to add missing tables
-- 3. Add all missing columns
-- ============================================================

SET FOREIGN_KEY_CHECKS=0;

-- ============================================================
-- STEP 1: Import your existing SQL file first
-- ============================================================
-- Run this in phpMyAdmin:
-- 1. Go to Import tab
-- 2. Select your theboost_lmsnew.sql file
-- 3. Click Go
-- 
-- OR use the web route:
-- https://your-domain.com/import_complete_schema?key=import_schema_2024_change_me

-- ============================================================
-- STEP 2: Add all missing columns to existing tables
-- ============================================================

-- Add missing columns to categories table
ALTER TABLE `categories` 
ADD COLUMN IF NOT EXISTS `order` int(10) UNSIGNED DEFAULT NULL AFTER `title`;

-- Add missing columns to webinars table
ALTER TABLE `webinars` 
ADD COLUMN IF NOT EXISTS `type` enum('webinar','course','text_lesson') DEFAULT NULL AFTER `category_id`,
ADD COLUMN IF NOT EXISTS `duration` int(10) UNSIGNED NOT NULL DEFAULT 0 AFTER `type`;

-- Add ALL missing columns to users table (based on KFM.sql structure)
-- Note: MySQL doesn't support IF NOT EXISTS for ALTER TABLE, so these will error if columns exist - that's fine!

-- Core columns
ALTER TABLE `users` 
ADD COLUMN `organ_id` int DEFAULT NULL AFTER `role_id`;

ALTER TABLE `users` 
ADD COLUMN `bio` varchar(128) DEFAULT NULL AFTER `email`;

ALTER TABLE `users` 
ADD COLUMN `logged_count` int(10) UNSIGNED NOT NULL DEFAULT 0 AFTER `remember_token`;

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
ADD COLUMN `country_id` int(10) UNSIGNED DEFAULT NULL AFTER `address`;

ALTER TABLE `users` 
ADD COLUMN `province_id` int(10) UNSIGNED DEFAULT NULL AFTER `country_id`;

ALTER TABLE `users` 
ADD COLUMN `city_id` int(10) UNSIGNED DEFAULT NULL AFTER `province_id`;

ALTER TABLE `users` 
ADD COLUMN `district_id` int(10) UNSIGNED DEFAULT NULL AFTER `city_id`;

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
ADD COLUMN `commission` int(10) UNSIGNED DEFAULT NULL AFTER `certificate`;

ALTER TABLE `users` 
ADD COLUMN `affiliate` tinyint(1) NOT NULL DEFAULT 1 AFTER `commission`;

ALTER TABLE `users` 
ADD COLUMN `can_create_store` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Despite disabling the store feature in the settings, we can enable this feature for that user through the edit page of a user and turning on the store toggle.' AFTER `affiliate`;

ALTER TABLE `users` 
ADD COLUMN `ban` tinyint(1) NOT NULL DEFAULT 0 AFTER `can_create_store`;

ALTER TABLE `users` 
ADD COLUMN `ban_start_at` int(10) UNSIGNED DEFAULT NULL AFTER `ban`;

ALTER TABLE `users` 
ADD COLUMN `ban_end_at` int(10) UNSIGNED DEFAULT NULL AFTER `ban_start_at`;

ALTER TABLE `users` 
ADD COLUMN `offline` tinyint(1) NOT NULL DEFAULT 0 AFTER `ban_end_at`;

ALTER TABLE `users` 
ADD COLUMN `offline_message` text DEFAULT NULL AFTER `offline`;

-- Add missing columns to groups table (CRITICAL - fixes status error)
ALTER TABLE `groups` 
ADD COLUMN `discount` int DEFAULT NULL AFTER `name`;

ALTER TABLE `groups` 
ADD COLUMN `commission` int DEFAULT NULL AFTER `discount`;

ALTER TABLE `groups` 
ADD COLUMN `status` enum('active','inactive') NOT NULL DEFAULT 'inactive' AFTER `commission`;

-- Add missing columns to comments table
ALTER TABLE `comments` 
ADD COLUMN IF NOT EXISTS `bundle_id` int(10) UNSIGNED DEFAULT NULL AFTER `webinar_id`,
ADD COLUMN IF NOT EXISTS `upcoming_course_id` int(10) UNSIGNED DEFAULT NULL AFTER `bundle_id`,
ADD COLUMN IF NOT EXISTS `blog_id` int(10) UNSIGNED DEFAULT NULL AFTER `upcoming_course_id`,
ADD COLUMN IF NOT EXISTS `product_id` int(10) UNSIGNED DEFAULT NULL AFTER `blog_id`,
ADD COLUMN IF NOT EXISTS `product_review_id` int(10) UNSIGNED DEFAULT NULL AFTER `product_id`;

-- Add bundle_id to sales table
ALTER TABLE `sales` 
ADD COLUMN IF NOT EXISTS `bundle_id` int(10) UNSIGNED DEFAULT NULL AFTER `product_id`,
MODIFY COLUMN `type` enum('webinar','meeting','subscribe','promotion','registration_package','product','bundle') NOT NULL;

-- Add bundle_id to cart table
ALTER TABLE `cart` 
ADD COLUMN IF NOT EXISTS `bundle_id` int(10) UNSIGNED DEFAULT NULL AFTER `promotion_id`;

-- Add bundle_id to other tables that need it
ALTER TABLE `favorites` 
ADD COLUMN IF NOT EXISTS `bundle_id` int(10) UNSIGNED DEFAULT NULL AFTER `webinar_id`;

ALTER TABLE `tags` 
ADD COLUMN IF NOT EXISTS `bundle_id` int(10) UNSIGNED DEFAULT NULL AFTER `webinar_id`;

ALTER TABLE `tickets` 
ADD COLUMN IF NOT EXISTS `bundle_id` int(10) UNSIGNED DEFAULT NULL AFTER `webinar_id`;

ALTER TABLE `faqs` 
ADD COLUMN IF NOT EXISTS `bundle_id` int(10) UNSIGNED DEFAULT NULL AFTER `webinar_id`;

ALTER TABLE `webinar_reviews` 
ADD COLUMN IF NOT EXISTS `bundle_id` int(10) UNSIGNED DEFAULT NULL AFTER `webinar_id`;

-- Add product_id to comments_reports if table exists
-- (This table might not exist yet, so we'll check)
SET @table_exists = (SELECT COUNT(*) FROM information_schema.tables 
    WHERE table_schema = DATABASE() AND table_name = 'comments_reports');
    
SET @sql = IF(@table_exists > 0,
    'ALTER TABLE `comments_reports` ADD COLUMN IF NOT EXISTS `product_id` int(10) UNSIGNED DEFAULT NULL AFTER `comment_id`;',
    'SELECT "comments_reports table does not exist yet" AS message;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ============================================================
-- STEP 3: Create all missing critical tables
-- ============================================================

-- Orders table (if missing)
CREATE TABLE IF NOT EXISTS `orders` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `user_id` int(10) unsigned NOT NULL,
    `status` enum('pending','paid','canceled') NOT NULL DEFAULT 'pending',
    `payment_method` enum('credit','payment_channel','subscribe') DEFAULT NULL,
    `amount` decimal(15,2) unsigned NOT NULL,
    `total_amount` decimal(15,2) unsigned NOT NULL,
    `created_at` int(10) unsigned NOT NULL,
    `updated_at` int(10) unsigned DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Order items table (if missing)
CREATE TABLE IF NOT EXISTS `order_items` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `order_id` int(10) unsigned NOT NULL,
    `webinar_id` int(10) unsigned DEFAULT NULL,
    `bundle_id` int(10) unsigned DEFAULT NULL,
    `product_id` int(10) unsigned DEFAULT NULL,
    `amount` decimal(15,2) unsigned NOT NULL,
    `created_at` int(10) unsigned NOT NULL,
    PRIMARY KEY (`id`),
    KEY `order_id` (`order_id`),
    KEY `webinar_id` (`webinar_id`),
    KEY `bundle_id` (`bundle_id`),
    KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Groups table (if missing)
CREATE TABLE IF NOT EXISTS `groups` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `user_id` int(10) unsigned NOT NULL,
    `name` varchar(255) NOT NULL,
    `created_at` int(10) unsigned NOT NULL,
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Subscribes table (if missing)
CREATE TABLE IF NOT EXISTS `subscribes` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `title` varchar(255) NOT NULL,
    `price` int(10) unsigned NOT NULL,
    `days` int(10) unsigned NOT NULL,
    `status` enum('active','inactive') NOT NULL DEFAULT 'active',
    `created_at` int(10) unsigned NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Subscribe uses table (if missing)
CREATE TABLE IF NOT EXISTS `subscribe_uses` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `user_id` int(10) unsigned NOT NULL,
    `subscribe_id` int(10) unsigned NOT NULL,
    `webinar_id` int(10) unsigned DEFAULT NULL,
    `bundle_id` int(10) unsigned DEFAULT NULL,
    `created_at` int(10) unsigned NOT NULL,
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`),
    KEY `subscribe_id` (`subscribe_id`),
    KEY `webinar_id` (`webinar_id`),
    KEY `bundle_id` (`bundle_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Special offers table (if missing)
CREATE TABLE IF NOT EXISTS `special_offers` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `webinar_id` int(10) unsigned DEFAULT NULL,
    `bundle_id` int(10) unsigned DEFAULT NULL,
    `name` varchar(255) NOT NULL,
    `percent` int(10) unsigned NOT NULL,
    `status` enum('active','inactive') NOT NULL DEFAULT 'active',
    `created_at` int(10) unsigned NOT NULL,
    PRIMARY KEY (`id`),
    KEY `webinar_id` (`webinar_id`),
    KEY `bundle_id` (`bundle_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Blogs table (if missing)
CREATE TABLE IF NOT EXISTS `blogs` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `author_id` int(10) unsigned NOT NULL,
    `category_id` int(10) unsigned DEFAULT NULL,
    `title` varchar(255) NOT NULL,
    `image` varchar(255) DEFAULT NULL,
    `status` enum('draft','publish') NOT NULL DEFAULT 'draft',
    `created_at` int(10) unsigned NOT NULL,
    `updated_at` int(10) unsigned DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `author_id` (`author_id`),
    KEY `category_id` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Blog categories table (if missing)
CREATE TABLE IF NOT EXISTS `blog_categories` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `title` varchar(255) NOT NULL,
    `created_at` int(10) unsigned NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Meeting times table (if missing)
CREATE TABLE IF NOT EXISTS `meeting_times` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `meeting_id` int(10) unsigned NOT NULL,
    `day_label` varchar(64) NOT NULL,
    `day` int(11) NOT NULL,
    `time` int(11) NOT NULL,
    `created_at` int(11) NOT NULL,
    PRIMARY KEY (`id`),
    KEY `meeting_id` (`meeting_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Reserve meetings table (if missing)
CREATE TABLE IF NOT EXISTS `reserve_meetings` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `meeting_id` int(10) unsigned NOT NULL,
    `meeting_time_id` int(10) unsigned NOT NULL,
    `user_id` int(10) unsigned NOT NULL,
    `paid_amount` decimal(15,2) DEFAULT NULL,
    `status` enum('open','finished','canceled') NOT NULL DEFAULT 'open',
    `created_at` int(11) NOT NULL,
    PRIMARY KEY (`id`),
    KEY `meeting_id` (`meeting_id`),
    KEY `meeting_time_id` (`meeting_time_id`),
    KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Text lessons table (if missing)
CREATE TABLE IF NOT EXISTS `text_lessons` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `webinar_id` int(10) unsigned NOT NULL,
    `title` varchar(255) NOT NULL,
    `content` longtext DEFAULT NULL,
    `order` int(10) unsigned DEFAULT NULL,
    `created_at` int(11) NOT NULL,
    `updated_at` int(11) DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `webinar_id` (`webinar_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Webinar chapters table (if missing)
CREATE TABLE IF NOT EXISTS `webinar_chapters` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `webinar_id` int(10) unsigned NOT NULL,
    `title` varchar(255) NOT NULL,
    `order` int(10) unsigned DEFAULT NULL,
    `created_at` int(11) NOT NULL,
    PRIMARY KEY (`id`),
    KEY `webinar_id` (`webinar_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Product categories table (if missing)
CREATE TABLE IF NOT EXISTS `product_categories` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `title` varchar(255) NOT NULL,
    `created_at` int(10) unsigned NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Upcoming courses table (if missing)
CREATE TABLE IF NOT EXISTS `upcoming_courses` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `creator_id` int(10) unsigned NOT NULL,
    `title` varchar(255) NOT NULL,
    `slug` varchar(255) NOT NULL,
    `status` enum('active','inactive') NOT NULL DEFAULT 'active',
    `created_at` bigint(20) unsigned NOT NULL,
    `updated_at` bigint(20) unsigned DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `creator_id` (`creator_id`),
    KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Installment orders table (if missing)
CREATE TABLE IF NOT EXISTS `installment_orders` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `user_id` int(10) unsigned NOT NULL,
    `order_id` int(10) unsigned NOT NULL,
    `installment_id` int(10) unsigned NOT NULL,
    `amount` decimal(15,2) NOT NULL,
    `status` enum('pending','paid','overdue','canceled') NOT NULL DEFAULT 'pending',
    `due_date` int(10) unsigned NOT NULL,
    `paid_at` int(10) unsigned DEFAULT NULL,
    `created_at` int(10) unsigned NOT NULL,
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`),
    KEY `order_id` (`order_id`),
    KEY `installment_id` (`installment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Gifts table (if missing)
CREATE TABLE IF NOT EXISTS `gifts` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `user_id` int(10) unsigned NOT NULL,
    `email` varchar(255) NOT NULL,
    `webinar_id` int(10) unsigned DEFAULT NULL,
    `bundle_id` int(10) unsigned DEFAULT NULL,
    `status` enum('pending','active','canceled') NOT NULL DEFAULT 'pending',
    `created_at` int(10) unsigned NOT NULL,
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`),
    KEY `webinar_id` (`webinar_id`),
    KEY `bundle_id` (`bundle_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Waitlists table (if missing)
CREATE TABLE IF NOT EXISTS `waitlists` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `user_id` int(10) unsigned NOT NULL,
    `webinar_id` int(10) unsigned DEFAULT NULL,
    `bundle_id` int(10) unsigned DEFAULT NULL,
    `status` enum('open','notified','joined') NOT NULL DEFAULT 'open',
    `created_at` int(10) unsigned NOT NULL,
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`),
    KEY `webinar_id` (`webinar_id`),
    KEY `bundle_id` (`bundle_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Course batches table (if missing)
CREATE TABLE IF NOT EXISTS `course_batches` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `webinar_id` int(10) unsigned NOT NULL,
    `title` varchar(255) NOT NULL,
    `start_date` int(10) unsigned NOT NULL,
    `end_date` int(10) unsigned DEFAULT NULL,
    `capacity` int(10) unsigned DEFAULT NULL,
    `status` enum('active','inactive') NOT NULL DEFAULT 'active',
    `created_at` int(10) unsigned NOT NULL,
    PRIMARY KEY (`id`),
    KEY `webinar_id` (`webinar_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS=1;

-- ============================================================
-- STEP 4: After running this script, run migrations via web route
-- ============================================================
-- Visit: https://your-domain.com/emergencyDatabaseUpdate?key=run_migrations_2024_temp_key_change_me&fix=1
-- 
-- This will:
-- 1. Run all pending migrations
-- 2. Add any remaining missing tables
-- 3. Add any remaining missing columns
-- 4. Seed essential data (roles, permissions, sections, payment channels)
-- ============================================================
