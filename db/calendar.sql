CREATE DATABASE IF NOT EXISTS calendar_php DEFAULT CHARACTER SET utf8 
	COLLATE utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS calendar_php.events (
	event_id INT(11) NOT NULL AUTO_INCREMENT,
	event_title VARCHAR(100) NOT NULL,
	event_loc VARCHAR(160),
	event_type INT(1) NOT NULL DEFAULT 0, 
	event_desc TEXT,
	event_start TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
	event_end TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
	event_rem TIMESTAMP,
	PRIMARY KEY (event_id),
	INDEX (event_start)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci;

INSERT INTO events (event_id, event_title, event_loc, event_type, event_desc, event_start, event_end) VALUES (NULL, 'New Years Day', NULL, '2', 'Happy New Years!', '2018-01-01 00:00:00', '2018-01-01 23:59:59');