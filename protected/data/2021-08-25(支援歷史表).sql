/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : hrdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2021-08-25 09:02:29
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for hr_apply_support_info
-- ----------------------------
DROP TABLE IF EXISTS `hr_apply_support_info`;
CREATE TABLE `hr_apply_support_info` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ase_id` int(11) NOT NULL COMMENT 'hr_apply_support_email表的id',
  `support_city` varchar(100) NOT NULL COMMENT '驻点城市',
  `wage_city` varchar(100) DEFAULT NULL COMMENT '发工资城市',
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COMMENT='驻点历史表（由于是后续添加，所以很奇怪）';


-- ----------------------------
-- Table structure for hr_employee_operate
-- ----------------------------
ALTER TABLE hr_apply_support_email ADD COLUMN end_date date NULL DEFAULT NULL AFTER support_city;
ALTER TABLE hr_apply_support_email ADD COLUMN start_date date NULL DEFAULT NULL AFTER support_city;
ALTER TABLE hr_apply_support_email ADD COLUMN wage_city varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '发工资城市' AFTER support_city;
