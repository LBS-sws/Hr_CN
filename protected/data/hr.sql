CREATE DATABASE hr CHARACTER SET utf8 COLLATE utf8_general_ci;

GRANT SELECT, INSERT, UPDATE, DELETE, EXECUTE ON hr.* TO 'swuser'@'localhost' IDENTIFIED BY 'swisher168';

use hr;

DROP TABLE IF EXISTS hr_staff;
CREATE TABLE hr_staff(
	id int unsigned NOT NULL auto_increment primary key,
	code varchar(15),
	name varchar(250) NOT NULL,
	position varchar(250),
	staff_type varchar(15),
	leader varchar(15),
	join_dt datetime,
	ctrt_start_dt datetime,
	ctrt_period tinyint default 0,
	ctrt_renew_dt datetime,
	email varchar(255),
	leave_dt datetime,
	leave_reason varchar(1000),
	remarks varchar(1000),
	city char(5) not null,
	lcu varchar(30),
	luu varchar(30),
	lcd timestamp default CURRENT_TIMESTAMP,
	lud timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS hr_queue;
CREATE TABLE hr_queue (
	id int unsigned NOT NULL auto_increment primary key,
	rpt_desc varchar(250) NOT NULL,
	req_dt datetime,
	fin_dt datetime,
	username varchar(30) NOT NULL,
	status char(1) NOT NULL,
	rpt_type varchar(10) NOT NULL,
	ts timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
	rpt_content longblob
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS hr_queue_param;
CREATE TABLE hr_queue_param (
	id int unsigned NOT NULL auto_increment primary key,
	queue_id int unsigned NOT NULL,
	param_field varchar(50) NOT NULL,
	param_value varchar(500),
	ts timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS hr_queue_user;
CREATE TABLE hr_queue_user (
	id int unsigned NOT NULL auto_increment primary key,
	queue_id int unsigned NOT NULL,
	username varchar(30) NOT NULL,
	ts timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

