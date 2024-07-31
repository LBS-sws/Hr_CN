/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : hrdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2020-02-25 17:04:49
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for hr_employee_leave_info
-- ----------------------------
DROP TABLE IF EXISTS `hr_employee_leave_info`;
CREATE TABLE `hr_employee_leave_info` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `leave_id` int(11) NOT NULL COMMENT '请假單id',
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='員工请假額外擴充的時間表';


-- ----------------------------
-- 修改员工表的必填为非必填
-- ----------------------------
alter table hr_employee modify name varchar(100) null;
alter table hr_employee modify company_id int(100) null;
alter table hr_employee modify contract_id int(100) null;
alter table hr_employee modify user_card varchar(50) null;
alter table hr_employee modify address varchar(255) null;
alter table hr_employee modify contact_address varchar(255) null;
alter table hr_employee modify phone varchar(50) null;
alter table hr_employee modify department varchar(20) null;
alter table hr_employee modify position varchar(20) null;
alter table hr_employee modify fix_time varchar(11) null;
alter table hr_employee modify start_time date null;
