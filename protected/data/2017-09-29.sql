/*
Navicat MySQL Data Transfer

Source Server         : local
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : hrdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2017-09-29 17:59:56
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for hr_binding
-- ----------------------------
DROP TABLE IF EXISTS `hr_binding`;
CREATE TABLE `hr_binding` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` int(10) unsigned NOT NULL,
  `employee_name` varchar(255) NOT NULL,
  `user_id` varchar(100) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='員工綁定賬號';

-- ----------------------------
-- Table structure for hr_contract
-- ----------------------------
DROP TABLE IF EXISTS `hr_contract`;
CREATE TABLE `hr_contract` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `city` varchar(30) NOT NULL,
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 COMMENT='合同表';

-- ----------------------------
-- Table structure for hr_employee_work
-- ----------------------------
DROP TABLE IF EXISTS `hr_employee_work`;
CREATE TABLE `hr_employee_work` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` int(10) unsigned NOT NULL,
  `employee_name` varchar(255) DEFAULT NULL,
  `type` int(11) DEFAULT '0' COMMENT '0：假期 1：加班',
  `holiday_id` int(10) unsigned NOT NULL,
  `holiday_name` varchar(100) DEFAULT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `hour` varchar(10) DEFAULT '1' COMMENT '時間（單位為：hour）',
  `remark` text COMMENT '備註',
  `reject_remark` text,
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '狀態 0：草稿  1：發送  2：審核通過 3：拒絕 4:完成',
  `city` varchar(255) DEFAULT NULL,
  `lcu` varchar(100) DEFAULT NULL,
  `luu` varchar(100) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='員工加班、請假表';

-- ----------------------------
-- Table structure for hr_holiday
-- ----------------------------
DROP TABLE IF EXISTS `hr_holiday`;
CREATE TABLE `hr_holiday` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `city` varchar(50) DEFAULT NULL,
  `z_index` varchar(50) DEFAULT NULL,
  `type` int(10) NOT NULL DEFAULT '0' COMMENT '0：假期 1：加班',
  `lcu` varchar(50) DEFAULT NULL,
  `luu` varchar(50) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='假期配置表';
