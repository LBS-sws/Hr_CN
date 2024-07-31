/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : hrdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2023-11-14 11:29:19
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for hr_set_trip
-- ----------------------------
DROP TABLE IF EXISTS `hr_set_trip`;
CREATE TABLE `hr_set_trip` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `appoint_code` varchar(255) DEFAULT NULL COMMENT '编号',
  `employee_id` int(11) NOT NULL COMMENT '账号',
  `audit_user_str` varchar(255) NOT NULL COMMENT '审核人',
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='加班、请假指定审核配置表';

-- ----------------------------
-- Table structure for hr_set_trip_info
-- ----------------------------
DROP TABLE IF EXISTS `hr_set_trip_info`;
CREATE TABLE `hr_set_trip_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `appoint_id` int(11) NOT NULL COMMENT '指定id',
  `audit_user` varchar(255) NOT NULL COMMENT '审核人',
  `z_index` int(11) DEFAULT '0' COMMENT '排序',
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COMMENT='加班、请假指定审核配置表';
