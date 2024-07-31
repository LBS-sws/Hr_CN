/*
Navicat MySQL Data Transfer

Source Server         : local
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : hrdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2018-01-12 17:58:45
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for hr_employee_leave
-- ----------------------------
DROP TABLE IF EXISTS `hr_employee_leave`;
CREATE TABLE `hr_employee_leave` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `leave_code` varchar(255) DEFAULT NULL COMMENT '請假編號',
  `employee_id` varchar(200) NOT NULL COMMENT '員工id',
  `vacation_id` int(10) NOT NULL DEFAULT '0' COMMENT '請假類型的id',
  `leave_cause` text COMMENT '請假原因',
  `start_time` datetime DEFAULT NULL COMMENT '請假開始時間',
  `start_time_lg` varchar(10) DEFAULT 'AM',
  `end_time` datetime DEFAULT NULL COMMENT '請假結束時間',
  `end_time_lg` varchar(10) DEFAULT 'PM',
  `log_time` float(5,1) DEFAULT NULL COMMENT '請假總時長',
  `leave_cost` float(10,2) DEFAULT NULL COMMENT '請假費用',
  `z_index` int(10) DEFAULT '3' COMMENT '審核層級（0:地區審核、1：總部審核、2：完成審核）',
  `status` int(10) DEFAULT '0' COMMENT '審核的狀態(0:草稿、1：審核、2：審核通過、3：拒絕、4：完成）',
  `user_lcu` varchar(255) DEFAULT NULL,
  `user_lcd` varchar(255) DEFAULT NULL,
  `area_lcu` varchar(255) DEFAULT NULL,
  `area_lcd` varchar(255) DEFAULT NULL,
  `head_lcu` varchar(255) DEFAULT NULL,
  `head_lcd` varchar(255) DEFAULT NULL,
  `audit_remark` text,
  `reject_cause` text COMMENT '拒絕原因',
  `auto_cost` int(2) DEFAULT '1' COMMENT '費用是否自動計算（0：否、 1：是）',
  `city` varchar(255) DEFAULT NULL,
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8 COMMENT='員工請假表';

-- ----------------------------
-- Table structure for hr_employee_work
-- ----------------------------
DROP TABLE IF EXISTS `hr_employee_work`;
CREATE TABLE `hr_employee_work` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `work_code` varchar(255) DEFAULT NULL COMMENT '加班編號',
  `employee_id` varchar(200) NOT NULL COMMENT '員工id',
  `work_type` int(10) NOT NULL DEFAULT '0' COMMENT '加班類型',
  `work_cause` text COMMENT '加班原因',
  `work_address` varchar(255) DEFAULT NULL COMMENT '加班地點',
  `start_time` datetime DEFAULT NULL COMMENT '加班開始時間',
  `end_time` datetime DEFAULT NULL COMMENT '加班結束時間',
  `log_time` int(10) DEFAULT NULL COMMENT '加班總時長',
  `work_cost` float(10,2) DEFAULT NULL COMMENT '加班費用',
  `z_index` int(10) DEFAULT '3' COMMENT '審核層級（0:地區審核、1：總部審核、2：完成審核）',
  `status` int(10) DEFAULT '0' COMMENT '審核的狀態(0:草稿、1：審核、2：審核通過、3：拒絕、4：完成）',
  `user_lcu` varchar(255) DEFAULT NULL,
  `user_lcd` varchar(255) DEFAULT NULL,
  `area_lcu` varchar(255) DEFAULT NULL,
  `area_lcd` varchar(255) DEFAULT NULL,
  `head_lcu` varchar(255) DEFAULT NULL,
  `head_lcd` varchar(255) DEFAULT NULL,
  `reject_cause` text COMMENT '拒絕原因',
  `auto_cost` int(2) DEFAULT '1' COMMENT '費用是否自動計算（0：否、 1：是）',
  `audit_remark` text COMMENT '審核備註',
  `city` varchar(255) DEFAULT NULL,
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COMMENT='員工加班表（新）';
