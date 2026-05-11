-- ================================================
-- Migration: Add soft-delete archive support
-- Run this on your existing electricity_db2 database
-- ================================================

ALTER TABLE `user`
  ADD COLUMN `is_archived`  TINYINT(1)  NOT NULL DEFAULT 0 AFTER `status`,
  ADD COLUMN `archived_at`  TIMESTAMP   NULL DEFAULT NULL AFTER `is_archived`;