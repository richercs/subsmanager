
-- Schedule item
ALTER TABLE `schedule_item`
    CHANGE COLUMN `scheduled_day` `scheduled_day` VARCHAR(255) NULL ,
    CHANGE COLUMN `scheduled_start_time` `scheduled_start_time` VARCHAR(255) NULL ,
    CHANGE COLUMN `scheduled_due_time` `scheduled_due_time` VARCHAR(255) NULL ;

ALTER TABLE `schedule_item` ADD `is_weekly_online` TINYINT(1) NOT NULL AFTER `scheduled_due_time`;

-- -- Subscription
-- ALTER TABLE `subscription` ADD `credit` INT DEFAULT NULL AFTER `attendance_count`, ADD `subscription_type` ENUM('attendance', 'credit') AFTER `price`;
-- UPDATE `subscription` SET `subscription_type` = 'attendance' WHERE `attendance_count` IS NOT NULL;
