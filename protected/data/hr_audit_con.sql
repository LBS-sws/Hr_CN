/*
Navicat MySQL Data Transfer

Source Server         : local
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : hrdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2018-03-12 10:25:46
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for hr_audit_con
-- ----------------------------
DROP TABLE IF EXISTS `hr_audit_con`;
CREATE TABLE `hr_audit_con` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `city` varchar(100) NOT NULL,
  `audit_index` int(3) NOT NULL DEFAULT '3' COMMENT '1:一層  2：二層  3：三層',
  `lcu` varchar(100) DEFAULT NULL,
  `luu` varchar(100) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='加班、請假審核配置表';
