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

ADD COLUMN `role` VARCHAR(50) NOT NULL DEFAULT 'user' AFTER `profile_image_public_id`,
ADD COLUMN `google_access_token` TEXT NULL AFTER `role`,
ADD COLUMN `google_refresh_token` TEXT NULL AFTER `google_access_token`,
ADD COLUMN `google_token_expires_at` DATETIME NULL AFTER `google_refresh_token`;

-- Create satsang table if not exists
CREATE TABLE IF NOT EXISTS `satsang` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `start_time` DATETIME NOT NULL,
    `end_time` DATETIME NOT NULL,
    `time_zone` VARCHAR(10) NOT NULL DEFAULT 'EST',
    `video_url` VARCHAR(500) NOT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Alter existing satsang table to match new structure (if it exists with old structure)
ALTER TABLE `satsang`
ADD COLUMN `title` VARCHAR(255) NOT NULL DEFAULT '',
ADD COLUMN `description` TEXT,
MODIFY COLUMN `start_time` DATETIME,
MODIFY COLUMN `end_time` DATETIME;

-- Add google_event_id to events table if not exists
ALTER TABLE `events`
ADD COLUMN `google_event_id` VARCHAR(255) NULL AFTER `is_featured`,
ADD COLUMN `created_by` INT UNSIGNED NULL AFTER `google_event_id`,
ADD FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL;

-- Create user_events junction table if not exists
CREATE TABLE IF NOT EXISTS `user_events` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `event_id` INT NOT NULL,
    `registration_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`event_id`) REFERENCES `events`(`id`) ON DELETE CASCADE,
    UNIQUE KEY `unique_user_event` (`user_id`, `event_id`)
);

-- Add image_url to events table
ALTER TABLE `events`
ADD COLUMN `image_url` VARCHAR(255) NOT NULL DEFAULT 'https://res.cloudinary.com/dfxl3oy4y/image/upload/v1764163447/Copy_of_Logo_dxmrrx.svg' AFTER `google_event_id`;