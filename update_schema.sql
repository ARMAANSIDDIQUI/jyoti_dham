-- This script updates the 'users' table to ensure it has all the necessary columns.
-- It's safe to run even if the columns already exist; you may see errors for columns that are already present, which can be ignored.

ALTER TABLE `users`
ADD COLUMN `gender` ENUM('male', 'female', 'other', 'prefer_not_to_say') NULL AFTER `password_hash`,
ADD COLUMN `dob` DATE NULL AFTER `gender`,
ADD COLUMN `phone` VARCHAR(20) NULL AFTER `dob`,
ADD COLUMN `address` TEXT NULL AFTER `phone`,
ADD COLUMN `family_size` INT NULL DEFAULT 0 AFTER `address`,
ADD COLUMN `vehicle_number` VARCHAR(50) NULL AFTER `family_size`,
ADD COLUMN `profile_image_url` VARCHAR(255) NULL AFTER `vehicle_number`,
ADD COLUMN `profile_image_public_id` VARCHAR(255) NULL AFTER `profile_image_url`,
ADD COLUMN `role` VARCHAR(50) NOT NULL DEFAULT 'user' AFTER `profile_image_public_id`;
