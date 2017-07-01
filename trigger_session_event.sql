-- CREATE TABLE `session_event_archive` (
--   `pk` int(11) NOT NULL AUTO_INCREMENT,
--   `id` int(11) NOT NULL,
--   `schedule_item_id` int(11) NOT NULL,
--   `session_event_date` datetime NOT NULL,
--   `session_fee_numbers_sold` int(11) DEFAULT NULL,
--   `session_fee_revenue_sold` int(11) DEFAULT NULL,
--   `date_updated` datetime DEFAULT NULL,
--   `date_created` datetime NOT NULL,
--   `date_archived` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
--   PRIMARY KEY (`pk`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TRIGGER IF EXISTS session_event_after_insert;

DROP TRIGGER IF EXISTS session_event_update;

DROP TRIGGER IF EXISTS session_event_delete;

DELIMITER //
CREATE TRIGGER session_event_after_insert
    AFTER INSERT ON `session_event`
    FOR EACH ROW

BEGIN
 INSERT INTO `subsmanager`.`session_event_archive`
	(`id`,
	`schedule_item_id`,
	`session_event_date`,
	`session_fee_numbers_sold`,
	`session_fee_revenue_sold`,
	`date_updated`,
	`date_created`)
	VALUES
	(
		NEW.id,
		NEW.schedule_item_id,
		NEW.session_event_date,
		NEW.session_fee_numbers_sold,
		NEW.session_fee_revenue_sold,
		NEW.date_updated,
		NEW.date_created
    );
END //

CREATE TRIGGER session_event_update
    BEFORE UPDATE ON `session_event`
    FOR EACH ROW
BEGIN
	INSERT INTO `subsmanager`.`session_event_archive`
	(`id`,
	 `schedule_item_id`,
	 `session_event_date`,
	 `session_fee_numbers_sold`,
	 `session_fee_revenue_sold`,
	 `date_updated`,
	 `date_created`)
	VALUES
		(
			NEW.id,
			NEW.schedule_item_id,
			NEW.session_event_date,
			NEW.session_fee_numbers_sold,
			NEW.session_fee_revenue_sold,
			NEW.date_updated,
			NEW.date_created
		);
END //

CREATE TRIGGER session_event_delete
    BEFORE DELETE ON `session_event`
    FOR EACH ROW
BEGIN
	INSERT INTO `subsmanager`.`session_event_archive`
	(`id`,
	 `schedule_item_id`,
	 `session_event_date`,
	 `session_fee_numbers_sold`,
	 `session_fee_revenue_sold`,
	 `date_updated`,
	 `date_created`)
	VALUES
		(
			OLD.id,
			OLD.schedule_item_id,
			OLD.session_event_date,
			OLD.session_fee_numbers_sold,
			OLD.session_fee_revenue_sold,
			OLD.date_updated,
			OLD.date_created
		);
END //