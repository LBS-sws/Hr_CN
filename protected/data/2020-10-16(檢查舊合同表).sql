/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : hrdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2020-10-16 10:11:21
*/

-- ----------------------------
-- Table structure for hr_sign_contract
-- ----------------------------
ALTER TABLE hr_sign_contract ADD COLUMN history_id int(11) NOT NULL DEFAULT 0 COMMENT '歷史id'  AFTER employee_id;

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for hr_check_staff
-- ----------------------------
DROP TABLE IF EXISTS `hr_check_staff`;
CREATE TABLE `hr_check_staff` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `old_type` int(11) NOT NULL DEFAULT '0' COMMENT '0:未驗證  1：未有合同  2：已有合同',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='驗證員工的舊合同是否存在';
