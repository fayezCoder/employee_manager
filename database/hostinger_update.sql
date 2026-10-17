-- ============================================================================
-- نظام إدارة الحضور والتحضير اليومي - تحديث قاعدة البيانات لاستضافة هوستنجر (Hostinger)
-- متوافق مع: MySQL 5.7+ / MySQL 8.0+ / MariaDB 10.3+
-- آمن تماماً وغير مدمر: ينشئ الجداول الجديدة فقط ولا يحذف أي بيانات حالية
-- ============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------------------------------------------------------
-- 1. جدول الجهات التنظيمية (organizational_units)
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `organizational_units` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_id` bigint unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `organizational_units_code_unique` (`code`),
  KEY `organizational_units_type_index` (`type`),
  KEY `organizational_units_parent_id_index` (`parent_id`),
  KEY `organizational_units_is_active_index` (`is_active`),
  CONSTRAINT `fk_org_units_parent` FOREIGN KEY (`parent_id`) REFERENCES `organizational_units` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 2. جدول الموظفين (employees)
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `employees` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `national_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `job_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `org_unit_id` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `needs_review` tinyint(1) NOT NULL DEFAULT '0',
  `review_notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employees_national_id_unique` (`national_id`),
  KEY `employees_org_unit_id_index` (`org_unit_id`),
  KEY `employees_user_id_index` (`user_id`),
  KEY `employees_is_active_index` (`is_active`),
  KEY `employees_needs_review_index` (`needs_review`),
  CONSTRAINT `fk_employees_org_unit` FOREIGN KEY (`org_unit_id`) REFERENCES `organizational_units` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_employees_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 3. جدول تعيينات الصلاحيات للجهات (user_unit_assignments)
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `user_unit_assignments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `org_unit_id` bigint unsigned DEFAULT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `include_sub_branches` tinyint(1) NOT NULL DEFAULT '0',
  `can_edit_approved` tinyint(1) NOT NULL DEFAULT '0',
  `can_manage_employees` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_unit_role_unique` (`user_id`, `org_unit_id`, `role`),
  KEY `user_unit_assignments_user_id_role_index` (`user_id`, `role`),
  KEY `user_unit_assignments_org_unit_id_index` (`org_unit_id`),
  CONSTRAINT `fk_uua_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_uua_org_unit` FOREIGN KEY (`org_unit_id`) REFERENCES `organizational_units` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 4. جدول سجلات التحضير اليومي للجهات (daily_rosters)
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `daily_rosters` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `org_unit_id` bigint unsigned NOT NULL,
  `date` date NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `version` int NOT NULL DEFAULT '1',
  `unit_name_snapshot` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit_code_snapshot` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit_type_snapshot` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit_path_snapshot` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `submitted_by` bigint unsigned DEFAULT NULL,
  `submitted_at` timestamp NULL DEFAULT NULL,
  `branch_approved_by` bigint unsigned DEFAULT NULL,
  `branch_approved_at` timestamp NULL DEFAULT NULL,
  `fully_approved_by` bigint unsigned DEFAULT NULL,
  `fully_approved_at` timestamp NULL DEFAULT NULL,
  `was_corrected` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_roster_unit_date` (`org_unit_id`, `date`),
  KEY `daily_rosters_date_index` (`date`),
  KEY `daily_rosters_status_index` (`status`),
  CONSTRAINT `fk_dr_unit` FOREIGN KEY (`org_unit_id`) REFERENCES `organizational_units` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_dr_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_dr_submitted_by` FOREIGN KEY (`submitted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_dr_branch_approved_by` FOREIGN KEY (`branch_approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_dr_fully_approved_by` FOREIGN KEY (`fully_approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 5. جدول بنود تحضير الموظفين (daily_roster_items)
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `daily_roster_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `daily_roster_id` bigint unsigned NOT NULL,
  `employee_id` bigint unsigned NOT NULL,
  `date` date NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `employee_name_snapshot` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `employee_national_id_snapshot` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `employee_job_title_snapshot` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_item_roster_employee` (`daily_roster_id`, `employee_id`),
  UNIQUE KEY `uq_item_employee_date` (`employee_id`, `date`),
  KEY `daily_roster_items_status_index` (`status`),
  KEY `daily_roster_items_date_index` (`date`),
  CONSTRAINT `fk_dri_roster` FOREIGN KEY (`daily_roster_id`) REFERENCES `daily_rosters` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_dri_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 6. جدول سجل تدقيق تصحيحات التحضير (roster_audit_logs)
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `roster_audit_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `daily_roster_id` bigint unsigned NOT NULL,
  `daily_roster_item_id` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned NOT NULL,
  `action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `field_changed` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `old_value` text COLLATE utf8mb4_unicode_ci,
  `new_value` text COLLATE utf8mb4_unicode_ci,
  `reason` text COLLATE utf8mb4_unicode_ci,
  `roster_version` int NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `roster_audit_logs_daily_roster_id_index` (`daily_roster_id`),
  KEY `roster_audit_logs_action_index` (`action`),
  KEY `roster_audit_logs_user_id_index` (`user_id`),
  CONSTRAINT `fk_ral_roster` FOREIGN KEY (`daily_roster_id`) REFERENCES `daily_rosters` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ral_item` FOREIGN KEY (`daily_roster_item_id`) REFERENCES `daily_roster_items` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_ral_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 7. جدول سجل أحداث مسار الاعتمادات (roster_workflow_events)
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `roster_workflow_events` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `daily_roster_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `from_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `to_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stage_label` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `roster_version` int NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `roster_workflow_events_daily_roster_id_index` (`daily_roster_id`),
  KEY `roster_workflow_events_user_id_index` (`user_id`),
  KEY `roster_workflow_events_to_status_index` (`to_status`),
  CONSTRAINT `fk_rwe_roster` FOREIGN KEY (`daily_roster_id`) REFERENCES `daily_rosters` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_rwe_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 8. جدول سجل تدقيق الهيكل والصلاحيات (structure_audit_logs)
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `structure_audit_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `target_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_id` bigint unsigned DEFAULT NULL,
  `action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `old_values` text COLLATE utf8mb4_unicode_ci,
  `new_values` text COLLATE utf8mb4_unicode_ci,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `structure_audit_logs_target_type_index` (`target_type`),
  KEY `structure_audit_logs_target_type_target_id_index` (`target_type`, `target_id`),
  KEY `structure_audit_logs_user_id_index` (`user_id`),
  KEY `structure_audit_logs_action_index` (`action`),
  CONSTRAINT `fk_sal_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 9. تسجيل أسماء الميجريشنز في جدول migrations لضمان توافق لارافيل
-- ----------------------------------------------------------------------------
INSERT IGNORE INTO `migrations` (`migration`, `batch`) VALUES
('2026_10_17_012509_create_organizational_units_table', 2),
('2026_10_17_012520_create_employees_table', 2),
('2026_10_17_012521_create_user_unit_assignments_table', 2),
('2026_10_17_012522_create_daily_rosters_and_items_tables', 2),
('2026_10_17_012523_create_roster_audit_logs_table', 2),
('2026_10_17_012525_migrate_legacy_users_to_employees', 2);

-- ----------------------------------------------------------------------------
-- 10. إدخال الهيكل التنظيمي المعتمد في حال عدم وجوده مسبقاً
-- ----------------------------------------------------------------------------
INSERT IGNORE INTO `organizational_units` (`id`, `name`, `code`, `type`, `parent_id`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'القيادة الرئيسية', 'HQ-MAIN', 'قيادة رئيسية', NULL, 1, NOW(), NOW()),
(2, 'شعبة الإدارة', 'HQ-ADM', 'شعبة', 1, 1, NOW(), NOW()),
(3, 'قسم المتابعة', 'HQ-ADM-FOL', 'قسم', 2, 1, NOW(), NOW()),
(4, 'شعبة الموارد البشرية', 'HQ-HR', 'شعبة', 1, 1, NOW(), NOW()),
(5, 'القيادة الفرعية الأولى', 'SUB-01', 'قيادة فرعية', 1, 1, NOW(), NOW()),
(6, 'المركز الأول', 'CTR-01', 'مركز', 5, 1, NOW(), NOW()),
(7, 'المركز الثاني', 'CTR-02', 'مركز', 5, 1, NOW(), NOW()),
(8, 'القيادة الفرعية الثانية', 'SUB-02', 'قيادة فرعية', 1, 1, NOW(), NOW()),
(9, 'المركز الثالث', 'CTR-03', 'مركز', 8, 1, NOW(), NOW());

-- ----------------------------------------------------------------------------
-- 11. ترحيل حسابات الموظفين السابقة إلى جدول الموظفين الجديد (بشكل غير مدمر)
-- ----------------------------------------------------------------------------
INSERT IGNORE INTO `employees` (`name`, `national_id`, `job_title`, `org_unit_id`, `user_id`, `is_active`, `needs_review`, `review_notes`, `created_at`, `updated_at`)
SELECT
  u.`name`,
  CONCAT('LEGACY-', LPAD(u.`id`, 8, '0')) AS `national_id`,
  NULL AS `job_title`,
  NULL AS `org_unit_id`,
  u.`id` AS `user_id`,
  u.`is_active`,
  1 AS `needs_review`,
  'تم استيراد الموظف من حساب النظام السابق. يتطلب مراجعة واعتماد المسؤول الأول لتحديث رقم الهوية وتحديد الجهة التنظيمية.' AS `review_notes`,
  NOW() AS `created_at`,
  NOW() AS `updated_at`
FROM `users` u
WHERE u.`role` = 'employee'
  AND NOT EXISTS (SELECT 1 FROM `employees` e WHERE e.`user_id` = u.`id`);

SET FOREIGN_KEY_CHECKS = 1;
