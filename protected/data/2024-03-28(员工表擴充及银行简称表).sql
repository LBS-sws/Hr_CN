/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : hrdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2024-03-28 12:42:25
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for hr_bank_set
-- ----------------------------
DROP TABLE IF EXISTS `hr_bank_set`;
CREATE TABLE `hr_bank_set` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT '辦事處名字',
  `display` int(1) NOT NULL DEFAULT '1' COMMENT '是否顯示 1：是 0：否',
  `z_index` int(11) NOT NULL DEFAULT '0',
  `city` varchar(100) DEFAULT NULL,
  `lcu` varchar(100) DEFAULT NULL,
  `luu` varchar(100) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='银行简称表';



-- ----------------------------
-- Table structure for hr_employee
-- ----------------------------
ALTER TABLE hr_employee ADD COLUMN bank_type int(11) NULL DEFAULT NULL COMMENT '银行简称id' AFTER phone2;
ALTER TABLE hr_employee ADD COLUMN bank_number varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '银行卡号' AFTER phone2;

-- ----------------------------
-- Table structure for hr_employee_operate
-- ----------------------------

ALTER TABLE hr_employee_operate ADD COLUMN bank_type int(11) NULL DEFAULT NULL COMMENT '银行简称id' AFTER phone2;
ALTER TABLE hr_employee_operate ADD COLUMN bank_number varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '银行卡号' AFTER phone2;


-- ----------------------------
-- Table structure for hr_contract
-- ----------------------------
ALTER TABLE hr_contract ADD COLUMN local_type int(2) NOT NULL DEFAULT '0' COMMENT '是否通用。0：不通用 1：通用' AFTER city;
