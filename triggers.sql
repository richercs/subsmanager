-- CREATE TABLE `subscription_archive` (
--   `pk` int(11) NOT NULL AUTO_INCREMENT,
--   `id` int(11) NOT NULL,
--   `owner_id` int(11) NOT NULL,
--   `date_start_date` datetime NOT NULL,
--   `date_due_date` datetime NOT NULL,
--   `extensions_count` int(11) NOT NULL,
--   `attendance_count` int(11) DEFAULT NULL,
--   `price` int(11) NOT NULL,
--   `date_updated` datetime DEFAULT NULL,
--   `date_created` datetime NOT NULL,
--   `date_archived` datetime DEFAULT CURRENT_TIMESTAMP,
--   PRIMARY KEY (`pk`)
-- ) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TRIGGER IF EXISTS subscription_after_insert;

DROP TRIGGER IF EXISTS subscription_update;

DROP TRIGGER IF EXISTS subscription_delete;

DELIMITER //
CREATE TRIGGER subscription_after_insert
    AFTER INSERT ON `subscription`
    FOR EACH ROW

BEGIN
 INSERT INTO `subsmanager`.`subscription_archive`
	(`id`,
	`owner_id`,
	`date_start_date`,
	`date_due_date`,
	`extensions_count`,
	`attendance_count`,
	`price`,
	`date_updated`,
	`date_created`)
	VALUES
	(
		NEW.id,
		NEW.owner_id,
		NEW.date_start_date,
		NEW.date_due_date,
		NEW.extensions_count,
		NEW.attendance_count,
		NEW.price,
		NEW.date_updated,
		NEW.date_created
    );

END //

CREATE TRIGGER subscription_update
    BEFORE UPDATE ON `subscription`
    FOR EACH ROW
BEGIN
 INSERT INTO `subsmanager`.`subscription_archive`
	(`id`,
	`owner_id`,
	`date_start_date`,
	`date_due_date`,
	`extensions_count`,
	`attendance_count`,
	`price`,
	`date_updated`,
	`date_created`)
	VALUES
	(
		NEW.id,
		NEW.owner_id,
		NEW.date_start_date,
		NEW.date_due_date,
		NEW.extensions_count,
		NEW.attendance_count,
		NEW.price,
		NEW.date_updated,
		NEW.date_created
    );
END //

CREATE TRIGGER subscription_delete
    BEFORE DELETE ON `subscription`
    FOR EACH ROW
BEGIN
  INSERT INTO `subsmanager`.`subscription_archive`
	(`id`,
	`owner_id`,
	`date_start_date`,
	`date_due_date`,
	`extensions_count`,
	`attendance_count`,
	`price`,
	`date_updated`,
	`date_created`)
	VALUES
	(
		OLD.id,
		OLD.owner_id,
		OLD.date_start_date,
		OLD.date_due_date,
		OLD.extensions_count,
		OLD.attendance_count,
		OLD.price,
		OLD.date_updated,
		OLD.date_created
    );
END //