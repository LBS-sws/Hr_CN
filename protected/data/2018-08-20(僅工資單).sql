/*
Navicat MySQL Data Transfer

Source Server         : local
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : hrdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2018-08-20 15:00:48
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for hr_employee_wages
-- ----------------------------
DROP TABLE IF EXISTS `hr_employee_wages`;
CREATE TABLE `hr_employee_wages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `city` varchar(40) DEFAULT NULL,
  `employee_id` int(10) unsigned NOT NULL,
  `wages_arr` text,
  `wages_date` date DEFAULT NULL,
  `wages_status` int(11) NOT NULL DEFAULT '0' COMMENT '0:草稿  1：發送 2：拒絕 3：完成',
  `just_remark` varchar(255) DEFAULT NULL,
  `apply_time` date DEFAULT NULL COMMENT '申請時間',
  `sum` varchar(50) DEFAULT NULL COMMENT '實際發放工資',
  `lcu` varchar(50) DEFAULT NULL,
  `luu` varchar(50) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='員工的工資表';
