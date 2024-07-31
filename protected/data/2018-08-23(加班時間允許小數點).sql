/*
Navicat MySQL Data Transfer

Source Server         : local
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : hrdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2018-08-23 15:11:44
*/

SET FOREIGN_KEY_CHECKS=0;

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
  `log_time` float(10,1) DEFAULT NULL COMMENT '加班總時長',
  `work_cost` float(10,2) DEFAULT NULL COMMENT '加班費用',
  `z_index` int(10) DEFAULT '1' COMMENT '審核層級（1:部門審核、2：主管、3：總監、4：你）',
  `status` int(10) DEFAULT '0' COMMENT '審核的狀態(0:草稿、1：審核、2：審核通過、3：拒絕、4：完成）',
  `user_lcu` varchar(255) DEFAULT NULL,
  `user_lcd` varchar(255) DEFAULT NULL,
  `area_lcu` varchar(255) DEFAULT NULL,
  `area_lcd` varchar(255) DEFAULT NULL,
  `head_lcu` varchar(255) DEFAULT NULL,
  `head_lcd` varchar(255) DEFAULT NULL,
  `you_lcu` varchar(255) DEFAULT NULL,
  `you_lcd` varchar(255) DEFAULT NULL,
  `reject_cause` text COMMENT '拒絕原因',
  `auto_cost` int(2) DEFAULT '1' COMMENT '費用是否自動計算（0：否、 1：是）',
  `audit_remark` text COMMENT '審核備註',
  `city` varchar(255) DEFAULT NULL,
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='員工加班表（新）';
