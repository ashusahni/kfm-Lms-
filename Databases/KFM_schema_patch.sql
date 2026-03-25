-- =============================================================================
-- KFM Schema Patch for Rocket LMS
-- Run this ONCE after importing KFM.sql so the database matches backend/frontend.
-- Adds: course_batches table + batch_id on cart, order_items, sales.
-- If you use Laravel migrations instead, run: php artisan migrate
-- =============================================================================

-- 1) Create course_batches table (required by CourseBatch model & batch enrollment)
CREATE TABLE IF NOT EXISTS `course_batches` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `webinar_id` int unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(50) DEFAULT NULL,
  `start_date` int unsigned DEFAULT NULL,
  `end_date` int unsigned DEFAULT NULL,
  `capacity` int unsigned DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'draft',
  `sort_order` int NOT NULL DEFAULT 0,
  `created_at` int unsigned DEFAULT NULL,
  `updated_at` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `course_batches_webinar_id_status_index` (`webinar_id`,`status`),
  KEY `course_batches_start_date_index` (`start_date`),
  CONSTRAINT `course_batches_webinar_id_foreign` FOREIGN KEY (`webinar_id`) REFERENCES `webinars` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2) Add batch_id to cart (after ticket_id)
ALTER TABLE `cart`
  ADD COLUMN `batch_id` int unsigned DEFAULT NULL AFTER `ticket_id`,
  ADD KEY `cart_batch_id_index` (`batch_id`),
  ADD CONSTRAINT `cart_batch_id_foreign` FOREIGN KEY (`batch_id`) REFERENCES `course_batches` (`id`) ON DELETE SET NULL;

-- 3) Add batch_id to order_items (after webinar_id)
ALTER TABLE `order_items`
  ADD COLUMN `batch_id` int unsigned DEFAULT NULL AFTER `webinar_id`,
  ADD KEY `order_items_batch_id_index` (`batch_id`),
  ADD CONSTRAINT `order_items_batch_id_foreign` FOREIGN KEY (`batch_id`) REFERENCES `course_batches` (`id`) ON DELETE SET NULL;

-- 4) Add batch_id to sales (after webinar_id)
ALTER TABLE `sales`
  ADD COLUMN `batch_id` int unsigned DEFAULT NULL AFTER `webinar_id`,
  ADD KEY `sales_batch_id_index` (`batch_id`),
  ADD CONSTRAINT `sales_batch_id_foreign` FOREIGN KEY (`batch_id`) REFERENCES `course_batches` (`id`) ON DELETE SET NULL;

-- 5) Add initial_conversation_at to sales (video content unlock after dietician conversation)
ALTER TABLE `sales`
  ADD COLUMN `initial_conversation_at` bigint unsigned DEFAULT NULL AFTER `created_at`;
