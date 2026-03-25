-- ============================================================
-- COMPLETE DATABASE UPDATE FROM KFM.SQL
-- This script adds ALL missing columns from KFM.sql to your database
-- Run this in phpMyAdmin after importing your theboost_lmsnew.sql
-- ============================================================

SET FOREIGN_KEY_CHECKS=0;

-- ============================================================
-- USERS TABLE - Complete structure from KFM.sql
-- ============================================================
-- Add ALL missing columns from KFM.sql users table

ALTER TABLE `users` 
ADD COLUMN `organ_id` int DEFAULT NULL AFTER `role_id`;

ALTER TABLE `users` 
ADD COLUMN `bio` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `email`;

ALTER TABLE `users` 
ADD COLUMN `logged_count` int unsigned NOT NULL DEFAULT 0 AFTER `remember_token`;

ALTER TABLE `users` 
ADD COLUMN `verified` tinyint(1) NOT NULL DEFAULT 0 AFTER `logged_count`;

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
ADD COLUMN `avatar_settings` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `avatar`;

ALTER TABLE `users` 
ADD COLUMN `cover_img` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `avatar_settings`;

ALTER TABLE `users` 
ADD COLUMN `headline` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `cover_img`;

ALTER TABLE `users` 
ADD COLUMN `about` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci AFTER `headline`;

ALTER TABLE `users` 
ADD COLUMN `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `about`;

ALTER TABLE `users` 
ADD COLUMN `country_id` int unsigned DEFAULT NULL AFTER `address`;

ALTER TABLE `users` 
ADD COLUMN `province_id` int unsigned DEFAULT NULL AFTER `country_id`;

ALTER TABLE `users` 
ADD COLUMN `city_id` int unsigned DEFAULT NULL AFTER `province_id`;

ALTER TABLE `users` 
ADD COLUMN `district_id` int unsigned DEFAULT NULL AFTER `city_id`;

ALTER TABLE `users` 
ADD COLUMN `location` point DEFAULT NULL AFTER `district_id`;

ALTER TABLE `users` 
ADD COLUMN `level_of_training` bit(3) DEFAULT NULL AFTER `location`;

ALTER TABLE `users` 
ADD COLUMN `meeting_type` enum('all','in_person','online') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'all' AFTER `level_of_training`;

ALTER TABLE `users` 
ADD COLUMN `access_content` tinyint(1) NOT NULL DEFAULT 1 AFTER `status`;

ALTER TABLE `users` 
ADD COLUMN `enable_ai_content` tinyint(1) NOT NULL DEFAULT 0 AFTER `access_content`;

ALTER TABLE `users` 
ADD COLUMN `language` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `enable_ai_content`;

ALTER TABLE `users` 
ADD COLUMN `currency` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `language`;

ALTER TABLE `users` 
ADD COLUMN `timezone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `currency`;

ALTER TABLE `users` 
ADD COLUMN `newsletter` tinyint(1) NOT NULL DEFAULT 0 AFTER `timezone`;

ALTER TABLE `users` 
ADD COLUMN `public_message` tinyint(1) NOT NULL DEFAULT 0 AFTER `newsletter`;

ALTER TABLE `users` 
ADD COLUMN `identity_scan` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `public_message`;

ALTER TABLE `users` 
ADD COLUMN `certificate` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `identity_scan`;

ALTER TABLE `users` 
ADD COLUMN `commission` int unsigned DEFAULT NULL AFTER `certificate`;

ALTER TABLE `users` 
ADD COLUMN `affiliate` tinyint(1) NOT NULL DEFAULT 1 AFTER `commission`;

ALTER TABLE `users` 
ADD COLUMN `can_create_store` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Despite disabling the store feature in the settings, we can enable this feature for that user through the edit page of a user and turning on the store toggle.' AFTER `affiliate`;

ALTER TABLE `users` 
ADD COLUMN `ban` tinyint(1) NOT NULL DEFAULT 0 AFTER `can_create_store`;

ALTER TABLE `users` 
ADD COLUMN `ban_start_at` int unsigned DEFAULT NULL AFTER `ban`;

ALTER TABLE `users` 
ADD COLUMN `ban_end_at` int unsigned DEFAULT NULL AFTER `ban_start_at`;

ALTER TABLE `users` 
ADD COLUMN `offline` tinyint(1) NOT NULL DEFAULT 0 AFTER `ban_end_at`;

ALTER TABLE `users` 
ADD COLUMN `offline_message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci AFTER `offline`;

-- ============================================================
-- GROUPS TABLE - Complete structure from KFM.sql
-- ============================================================
ALTER TABLE `groups` 
ADD COLUMN `discount` int DEFAULT NULL AFTER `name`;

ALTER TABLE `groups` 
ADD COLUMN `commission` int DEFAULT NULL AFTER `discount`;

ALTER TABLE `groups` 
ADD COLUMN `status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'inactive' AFTER `commission`;

-- ============================================================
-- ACCOUNTING TABLE - Missing columns from KFM.sql
-- ============================================================
ALTER TABLE `accounting` 
ADD COLUMN `order_item_id` int unsigned DEFAULT NULL AFTER `creator_id`;

ALTER TABLE `accounting` 
ADD COLUMN `subscribe_id` int unsigned DEFAULT NULL AFTER `meeting_time_id`;

ALTER TABLE `accounting` 
ADD COLUMN `promotion_id` int unsigned DEFAULT NULL AFTER `subscribe_id`;

ALTER TABLE `accounting` 
ADD COLUMN `registration_package_id` int unsigned DEFAULT NULL AFTER `promotion_id`;

ALTER TABLE `accounting` 
ADD COLUMN `product_id` int unsigned DEFAULT NULL AFTER `registration_package_id`;

ALTER TABLE `accounting` 
ADD COLUMN `installment_payment_id` int unsigned DEFAULT NULL AFTER `product_id`;

ALTER TABLE `accounting` 
ADD COLUMN `installment_order_id` int unsigned DEFAULT NULL COMMENT 'This field is filled in the seller''s financial document to find the installment order' AFTER `installment_payment_id`;

ALTER TABLE `accounting` 
ADD COLUMN `gift_id` int unsigned DEFAULT NULL AFTER `installment_order_id`;

ALTER TABLE `accounting` 
ADD COLUMN `type_account` enum('income','asset','subscribe','promotion','registration_package','installment_payment') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `type`;

ALTER TABLE `accounting` 
ADD COLUMN `store_type` enum('automatic','manual') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'automatic' AFTER `type_account`;

ALTER TABLE `accounting` 
ADD COLUMN `referred_user_id` int unsigned DEFAULT NULL AFTER `store_type`;

ALTER TABLE `accounting` 
ADD COLUMN `is_affiliate_amount` tinyint(1) NOT NULL DEFAULT 0 AFTER `referred_user_id`;

ALTER TABLE `accounting` 
ADD COLUMN `is_affiliate_commission` tinyint(1) NOT NULL DEFAULT 0 AFTER `is_affiliate_amount`;

ALTER TABLE `accounting` 
ADD COLUMN `is_registration_bonus` tinyint(1) NOT NULL DEFAULT 0 AFTER `is_affiliate_commission`;

ALTER TABLE `accounting` 
ADD COLUMN `is_cashback` tinyint(1) NOT NULL DEFAULT 0 AFTER `is_registration_bonus`;

-- Update amount column type if needed
ALTER TABLE `accounting` 
MODIFY COLUMN `amount` decimal(13,2) DEFAULT NULL;

-- ============================================================
-- COMMENTS TABLE - Missing columns
-- ============================================================
ALTER TABLE `comments` 
ADD COLUMN `bundle_id` int(10) UNSIGNED DEFAULT NULL AFTER `webinar_id`;

ALTER TABLE `comments` 
ADD COLUMN `upcoming_course_id` int(10) UNSIGNED DEFAULT NULL AFTER `bundle_id`;

ALTER TABLE `comments` 
ADD COLUMN `blog_id` int(10) UNSIGNED DEFAULT NULL AFTER `upcoming_course_id`;

ALTER TABLE `comments` 
ADD COLUMN `product_id` int(10) UNSIGNED DEFAULT NULL AFTER `blog_id`;

ALTER TABLE `comments` 
ADD COLUMN `product_review_id` int(10) UNSIGNED DEFAULT NULL AFTER `product_id`;

-- ============================================================
-- WEBINARS TABLE - Missing columns
-- ============================================================
ALTER TABLE `webinars` 
ADD COLUMN `type` enum('webinar','course','text_lesson') DEFAULT NULL AFTER `category_id`;

ALTER TABLE `webinars` 
ADD COLUMN `duration` int(10) UNSIGNED NOT NULL AFTER `start_date`;

-- ============================================================
-- SALES TABLE - Missing columns
-- ============================================================
ALTER TABLE `sales` 
ADD COLUMN `bundle_id` int(10) UNSIGNED DEFAULT NULL AFTER `webinar_id`;

-- Update sales type enum to include bundle
ALTER TABLE `sales` 
MODIFY COLUMN `type` enum('webinar','meeting','subscribe','promotion','registration_package','product','bundle') COLLATE utf8mb4_unicode_ci NOT NULL;

-- ============================================================
-- CART TABLE - Missing columns
-- ============================================================
ALTER TABLE `cart` 
ADD COLUMN `bundle_id` int(10) UNSIGNED DEFAULT NULL AFTER `webinar_id`;

-- ============================================================
-- ORDER_ITEMS TABLE - Missing columns
-- ============================================================
ALTER TABLE `order_items` 
ADD COLUMN `bundle_id` int(10) UNSIGNED DEFAULT NULL AFTER `webinar_id`;

-- ============================================================
-- CATEGORIES TABLE - Missing columns
-- ============================================================
ALTER TABLE `categories` 
ADD COLUMN `order` int(10) UNSIGNED DEFAULT NULL;

-- ============================================================
-- COMMENTS_REPORTS TABLE - Missing columns
-- ============================================================
ALTER TABLE `comments_reports` 
ADD COLUMN `product_id` int(10) UNSIGNED DEFAULT NULL;

-- ============================================================
-- FAVORITES TABLE - Missing columns
-- ============================================================
ALTER TABLE `favorites` 
ADD COLUMN `bundle_id` int(10) UNSIGNED DEFAULT NULL AFTER `webinar_id`;

-- ============================================================
-- TAGS TABLE - Missing columns
-- ============================================================
ALTER TABLE `tags` 
ADD COLUMN `bundle_id` int(10) UNSIGNED DEFAULT NULL;

-- ============================================================
-- TICKETS TABLE - Missing columns
-- ============================================================
ALTER TABLE `tickets` 
ADD COLUMN `bundle_id` int(10) UNSIGNED DEFAULT NULL AFTER `webinar_id`;

-- ============================================================
-- FAQS TABLE - Missing columns
-- ============================================================
ALTER TABLE `faqs` 
ADD COLUMN `bundle_id` int(10) UNSIGNED DEFAULT NULL AFTER `webinar_id`;

-- ============================================================
-- SPECIAL_OFFERS TABLE - Missing columns
-- ============================================================
ALTER TABLE `special_offers` 
ADD COLUMN `bundle_id` int(10) UNSIGNED DEFAULT NULL AFTER `webinar_id`;

-- ============================================================
-- WEBINAR_REVIEWS TABLE - Missing columns
-- ============================================================
ALTER TABLE `webinar_reviews` 
ADD COLUMN `bundle_id` int(10) UNSIGNED DEFAULT NULL AFTER `webinar_id`;

-- ============================================================
-- SUBSCRIBE_USES TABLE - Missing columns
-- ============================================================
ALTER TABLE `subscribe_uses` 
ADD COLUMN `bundle_id` int(10) UNSIGNED DEFAULT NULL;

SET FOREIGN_KEY_CHECKS=1;

-- ============================================================
-- COMPLETE!
-- ============================================================
-- This script has added all missing columns from KFM.sql
-- If you get "Duplicate column name" errors, those columns already exist - that's fine!
-- ============================================================
