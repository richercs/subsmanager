
-- Schedule item
ALTER TABLE `schedule_item`
    CHANGE COLUMN `scheduled_day` `scheduled_day` VARCHAR(255) NULL,
    CHANGE COLUMN `scheduled_start_time` `scheduled_start_time` VARCHAR(255) NULL,
    CHANGE COLUMN `scheduled_due_time` `scheduled_due_time` VARCHAR(255) NULL;

ALTER TABLE `schedule_item` ADD `is_weekly_online` TINYINT(1) NOT NULL AFTER `scheduled_due_time`;

-- Subscription
ALTER TABLE `subscription`
    ADD `credit` INT DEFAULT NULL AFTER `attendance_count`,
    ADD `subscription_type` ENUM('attendance', 'credit') AFTER `price`;

UPDATE `subscription` SET `subscription_type` = 'attendance' WHERE `attendance_count` IS NOT NULL;

-- Session Event
ALTER TABLE `session_event` ADD `session_credit_requirement` INT DEFAULT NULL AFTER `session_event_date`;

-- Announced Session
ALTER TABLE `announced_session`
    ADD `time_of_signup_start` DATETIME DEFAULT NULL AFTER `time_of_event`,
    ADD `announced_session_type` ENUM('single_limited', 'weekly_online_unlimited') AFTER `schedule_item_id`,
    CHANGE COLUMN `max_number_of_signups` `max_number_of_signups` INT DEFAULT NULL;

UPDATE `announced_session` SET `time_of_signup_start` = DATE_ADD(`time_of_event`, INTERVAL 24 HOUR) WHERE `time_of_signup_start` IS NULL;

ALTER TABLE `announced_session` CHANGE COLUMN `time_of_signup_start` `time_of_signup_start` DATETIME NOT NULL;

UPDATE `announced_session` SET `announced_session_type` = 'single_limited' WHERE `max_number_of_signups` IS NOT NULL;
