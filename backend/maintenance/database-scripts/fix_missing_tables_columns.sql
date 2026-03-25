-- SQL script to fix missing tables and columns
-- Run this in phpMyAdmin to fix common migration issues

-- ============================================
-- Fix categories table - add order column
-- ============================================
-- Run this ONLY if the order column doesn't exist (check table structure first)
-- If you get "Duplicate column name" error, the column already exists - skip this
ALTER TABLE `categories` 
ADD COLUMN `order` int(10) unsigned DEFAULT NULL;

-- ============================================
-- Create product_orders table if missing
-- ============================================
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

-- ============================================
-- Fix meetings table - remove invalid foreign key
-- ============================================
SET FOREIGN_KEY_CHECKS=0;

-- Remove invalid teacher_id foreign key (run this even if you get an error - it means it doesn't exist)
-- If you get "Can't DROP FOREIGN KEY" error, it means the constraint doesn't exist - that's fine, continue
ALTER TABLE `meetings` 
DROP FOREIGN KEY `meetings_teacher_id_foreign`;

SET FOREIGN_KEY_CHECKS=1;
