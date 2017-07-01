CREATE TABLE `attendance_history_archive` (
  `pk` int(11) NOT NULL AUTO_INCREMENT,
  `id` int(11) NOT NULL,
  `session_event_id` int(11) DEFAULT NULL,
  `attendee_id` int(11) DEFAULT NULL,
  `subscription_in_use_id` int(11) DEFAULT NULL,
  `date_updated` datetime DEFAULT NULL,
  `date_created` datetime NOT NULL,
  `date_archived` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`pk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `session_event_archive` (
  `pk` int(11) NOT NULL AUTO_INCREMENT,
  `id` int(11) NOT NULL,
  `schedule_item_id` int(11) NOT NULL,
  `session_event_date` datetime NOT NULL,
  `session_fee_numbers_sold` int(11) DEFAULT NULL,
  `session_fee_revenue_sold` int(11) DEFAULT NULL,
  `date_updated` datetime DEFAULT NULL,
  `date_created` datetime NOT NULL,
  `date_archived` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`pk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `subscription_archive` (
  `pk` int(11) NOT NULL AUTO_INCREMENT,
  `id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `date_start_date` datetime NOT NULL,
  `date_due_date` datetime NOT NULL,
  `extensions_count` int(11) NOT NULL,
  `attendance_count` int(11) DEFAULT NULL,
  `price` int(11) NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  `date_created` datetime NOT NULL,
  `date_archived` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`pk`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
