/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : hrdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2023-07-20 17:32:23
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for hr_employee_trip_money
-- ----------------------------
DROP TABLE IF EXISTS `hr_employee_trip_money`;
CREATE TABLE `hr_employee_trip_money` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `trip_id` int(11) NOT NULL COMMENT '出差單id',
  `money_set_id` int(11) NOT NULL,
  `trip_money` float(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='員工出差額外擴充的時間表';

-- ----------------------------
-- Table structure for hr_trip_money_set
-- ----------------------------
DROP TABLE IF EXISTS `hr_trip_money_set`;
CREATE TABLE `hr_trip_money_set` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pro_name` varchar(30) NOT NULL COMMENT '項目名稱',
  `z_display` int(2) NOT NULL DEFAULT '1' COMMENT '0:隱藏 1：顯示',
  `z_index` int(11) NOT NULL DEFAULT '0' COMMENT '層級',
  `city` varchar(10) DEFAULT NULL,
  `lcu` varchar(40) DEFAULT NULL,
  `luu` varchar(40) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='出差费用配置';

-- ----------------------------
-- Table structure for hr_trip_result_set
-- ----------------------------
DROP TABLE IF EXISTS `hr_trip_result_set`;
CREATE TABLE `hr_trip_result_set` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pro_name` varchar(30) NOT NULL COMMENT '項目名稱',
  `pro_num` int(3) NOT NULL COMMENT '結果數值（0-100）',
  `valid_bool` int(2) NOT NULL DEFAULT '1' COMMENT '1：有效的  0：無效的',
  `z_display` int(2) NOT NULL DEFAULT '1' COMMENT '0:隱藏 1：顯示',
  `z_index` int(11) NOT NULL DEFAULT '0' COMMENT '層級',
  `city` varchar(10) DEFAULT NULL,
  `lcu` varchar(40) DEFAULT NULL,
  `luu` varchar(40) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='出差結果配置';

-- ----------------------------
-- Table structure for hr_employee_trip
-- ----------------------------
ALTER TABLE hr_employee_trip ADD COLUMN result_id int(11) NOT NULL COMMENT '結果id' AFTER reject_cause;
ALTER TABLE hr_employee_trip ADD COLUMN result_text text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '結果說明' AFTER reject_cause;