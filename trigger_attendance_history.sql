
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
	`date_created`,
	`operation`)
	VALUES
	(
		NEW.id,
		NEW.session_event_id,
		NEW.attendee_id,
		NEW.subscription_in_use_id,
		NEW.date_updated,
		NEW.date_created,
		'INSERT'
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
	 `date_created`,
	 `operation`)
	VALUES
		(
			NEW.id,
			NEW.session_event_id,
			NEW.attendee_id,
			NEW.subscription_in_use_id,
			NEW.date_updated,
			NEW.date_created,
			'UPDATE'
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
   `date_created`,
   `operation`)
  VALUES
    (
      OLD.id,
      OLD.session_event_id,
      OLD.attendee_id,
      OLD.subscription_in_use_id,
      OLD.date_updated,
      OLD.date_created,
	    'DELETE'
    );
END //