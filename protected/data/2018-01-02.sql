/*
Navicat MySQL Data Transfer

Source Server         : local
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : hrdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2018-01-02 14:23:29
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
  `z_index` int(10) DEFAULT '0' COMMENT '審核層級（0:地區審核、1：總部審核、2：完成審核）',
  `status` int(10) DEFAULT '0' COMMENT '審核的狀態(0:草稿、1：審核、2：審核通過、3：拒絕、4：完成）',
  `area_lcu` varchar(255) DEFAULT NULL,
  `area_lcd` varchar(255) DEFAULT NULL,
  `head_lcu` varchar(255) DEFAULT NULL,
  `head_lcd` varchar(255) DEFAULT NULL,
  `reject_cause` text COMMENT '拒絕原因',
  `auto_cost` int(2) DEFAULT '1' COMMENT '費用是否自動計算（0：否、 1：是）',
  `city` varchar(255) DEFAULT NULL,
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8 COMMENT='員工請假表';
