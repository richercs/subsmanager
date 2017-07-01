# CREATE TABLE `attendance_history_archive` (
# 	`pk` int(11) NOT NULL AUTO_INCREMENT,
# 	`id` int(11) NOT NULL,
# 	`session_event_id` int(11) DEFAULT NULL,
# 	`attendee_id` int(11) DEFAULT NULL,
# 	`subscription_in_use_id` int(11) DEFAULT NULL,
# 	`date_updated` datetime DEFAULT NULL,
# 	`date_created` datetime NOT NULL,
# 	`date_archived` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
# 	PRIMARY KEY (`pk`)
# ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TRIGGER IF EXISTS attendance_history_after_insert;

DROP TRIGGER IF EXISTS attendance_history_update;

DROP TRIGGER IF EXISTS attendance_history_delete;

DELIMITER //
CREATE TRIGGER attendance_history_after_insert
    AFTER INSERT ON `attendance_history`
    FOR EACH ROW

BEGIN
 INSERT INTO `subsmanager`.`attendance_history_archive`
	(`id`,
	`session_event_id`,
	`attendee_id`,
	`subscription_in_use_id`,
	`date_updated`,
	`date_created`)
	VALUES
	(
		NEW.id,
		NEW.session_event_id,
		NEW.attendee_id,
		NEW.subscription_in_use_id,
		NEW.date_updated,
		NEW.date_created
    );
END //

CREATE TRIGGER attendance_history_update
    BEFORE UPDATE ON `attendance_history`
    FOR EACH ROW
BEGIN
	INSERT INTO `subsmanager`.`attendance_history_archive`
	(`id`,
	 `session_event_id`,
	 `attendee_id`,
	 `subscription_in_use_id`,
	 `date_updated`,
	 `date_created`)
	VALUES
		(
			NEW.id,
			NEW.session_event_id,
			NEW.attendee_id,
			NEW.subscription_in_use_id,
			NEW.date_updated,
			NEW.date_created
		);
END //

CREATE TRIGGER attendance_history_delete
    BEFORE DELETE ON `attendance_history`
    FOR EACH ROW
BEGIN
  INSERT INTO `subsmanager`.`attendance_history_archive`
  (`id`,
   `session_event_id`,
   `attendee_id`,
   `subscription_in_use_id`,
   `date_updated`,
   `date_created`)
  VALUES
    (
      OLD.id,
      OLD.session_event_id,
      OLD.attendee_id,
      OLD.subscription_in_use_id,
      OLD.date_updated,
      OLD.date_created
    );
END //