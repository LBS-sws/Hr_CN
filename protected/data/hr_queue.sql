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
