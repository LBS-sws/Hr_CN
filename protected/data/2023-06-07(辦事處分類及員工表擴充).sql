/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : hrdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2023-06-07 10:12:21
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for hr_office
-- ----------------------------
DROP TABLE IF EXISTS `hr_office`;
CREATE TABLE `hr_office` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT '辦事處名字',
  `z_display` int(1) NOT NULL DEFAULT '1' COMMENT '是否顯示 1：是 0：否',
  `city` varchar(100) DEFAULT NULL,
  `lcu` varchar(100) DEFAULT NULL,
  `luu` varchar(100) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='辦事處';

-- ----------------------------
-- Table structure for hr_employee
-- ----------------------------
ALTER TABLE hr_employee ADD COLUMN office_id int(11) NOT NULL DEFAULT 0 COMMENT '辦事處id' AFTER code_old;

-- ----------------------------
-- Table structure for hr_employee_operate
-- ----------------------------
ALTER TABLE hr_employee_operate ADD COLUMN office_id int(11) NOT NULL DEFAULT 0 COMMENT '辦事處id' AFTER code_old;