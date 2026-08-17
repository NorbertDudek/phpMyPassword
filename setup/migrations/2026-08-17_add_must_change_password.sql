-- Adds the "must change password on next login" flag to the users table.
-- Safe to run once against an existing phpMyPassword database.
--
-- Usage:
--   mysql -u <user> -p <database> < setup/migrations/2026-08-17_add_must_change_password.sql

ALTER TABLE `users`
  ADD COLUMN `must_change_password` TINYINT(1) NOT NULL DEFAULT 0 AFTER `admin`;
