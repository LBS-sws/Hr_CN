/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : hrdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2023-07-07 16:27:30
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for hr_employee_trip
-- ----------------------------
DROP TABLE IF EXISTS `hr_employee_trip`;
CREATE TABLE `hr_employee_trip` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `trip_code` varchar(255) DEFAULT NULL COMMENT '出差編號',
  `employee_id` varchar(200) NOT NULL COMMENT '員工id',
  `trip_type` int(10) NOT NULL DEFAULT '0' COMMENT '出差類型',
  `trip_address` varchar(255) DEFAULT NULL COMMENT '目的地',
  `trip_cost` float(10,2) DEFAULT NULL COMMENT '預估費用',
  `trip_cause` text COMMENT '出差目的',
  `start_time` datetime DEFAULT NULL COMMENT '出差開始時間',
  `start_time_lg` varchar(10) DEFAULT 'AM',
  `end_time` datetime DEFAULT NULL COMMENT '出差結束時間',
  `end_time_lg` varchar(10) DEFAULT 'PM',
  `log_time` float(5,1) DEFAULT NULL COMMENT '出差總時長',
  `z_index` int(10) DEFAULT '1' COMMENT '審核層級（1:部門審核、2：主管、3：總監、4：你）',
  `status` int(10) DEFAULT '0' COMMENT '審核的狀態(0:草稿、1：審核、2：審核通過、3：拒絕、4：完成、5：取消）',
  `pers_lcu` varchar(100) DEFAULT NULL,
  `pers_lcd` varchar(100) DEFAULT NULL,
  `user_lcu` varchar(255) DEFAULT NULL,
  `user_lcd` varchar(255) DEFAULT NULL,
  `area_lcu` varchar(255) DEFAULT NULL,
  `area_lcd` varchar(255) DEFAULT NULL,
  `head_lcu` varchar(255) DEFAULT NULL,
  `head_lcd` varchar(255) DEFAULT NULL,
  `you_lcu` varchar(255) DEFAULT NULL,
  `you_lcd` varchar(255) DEFAULT NULL,
  `audit_remark` text,
  `reject_cause` text COMMENT '拒絕原因',
  `auto_cost` int(2) DEFAULT '1' COMMENT '費用是否自動計算（0：否、 1：是）',
  `city` varchar(255) DEFAULT NULL,
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='員工出差表';

-- ----------------------------
-- Table structure for hr_employee_trip_info
-- ----------------------------
DROP TABLE IF EXISTS `hr_employee_trip_info`;
CREATE TABLE `hr_employee_trip_info` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `trip_id` int(11) NOT NULL COMMENT '出差單id',
  `start_time` date NOT NULL,
  `start_time_lg` varchar(10) NOT NULL DEFAULT 'AM',
  `end_time` date NOT NULL,
  `end_time_lg` varchar(10) NOT NULL DEFAULT 'AM',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='員工出差額外擴充的時間表';
