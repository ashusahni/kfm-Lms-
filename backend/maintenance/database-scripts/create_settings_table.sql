-- SQL script to create missing settings tables
-- Run this in phpMyAdmin if the settings table is missing after migrations

-- Create settings table (with page column)
CREATE TABLE IF NOT EXISTS `settings` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `page` enum('general','financial','personalization','notifications','seo','customization','other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'other',
    `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `value` text COLLATE utf8mb4_unicode_ci NOT NULL,
    `updated_at` int(11) DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- If settings table already exists without the page column, add it:
-- Run this ONLY if the page column is missing (check table structure first)
ALTER TABLE `settings` 
ADD COLUMN `page` enum('general','financial','personalization','notifications','seo','customization','other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'other' AFTER `id`;

-- ============================================
-- Create notifications tables
-- ============================================

-- Create notifications table
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
    KEY `webinar_id` (`webinar_id`),
    KEY `sender_id` (`sender_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create notifications_status table
CREATE TABLE IF NOT EXISTS `notifications_status` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `user_id` int(10) unsigned NOT NULL,
    `notification_id` int(10) unsigned NOT NULL,
    `seen_at` int(10) unsigned NOT NULL,
    PRIMARY KEY (`id`),
    KEY `notification_id` (`notification_id`),
    KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add foreign keys (only if referenced tables exist)
-- Uncomment these if users, groups, and webinars tables exist:
-- ALTER TABLE `notifications` 
-- ADD CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
-- ADD CONSTRAINT `notifications_group_id_foreign` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE,
-- ADD CONSTRAINT `notifications_webinar_id_foreign` FOREIGN KEY (`webinar_id`) REFERENCES `webinars` (`id`) ON DELETE CASCADE;

-- ALTER TABLE `notifications_status` 
-- ADD CONSTRAINT `notifications_status_notification_id_foreign` FOREIGN KEY (`notification_id`) REFERENCES `notifications` (`id`) ON DELETE CASCADE;

-- Create setting_translations table
CREATE TABLE IF NOT EXISTS `setting_translations` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `setting_id` int(10) unsigned NOT NULL,
    `locale` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
    `value` text COLLATE utf8mb4_unicode_ci NOT NULL,
    PRIMARY KEY (`id`),
    KEY `setting_id` (`setting_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
