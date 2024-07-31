/*
Navicat MySQL Data Transfer

Source Server         : local
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : hrdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2018-01-19 10:47:05
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for hr_vacation
-- ----------------------------
DROP TABLE IF EXISTS `hr_vacation`;
CREATE TABLE `hr_vacation` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `vaca_type` varchar(10) NOT NULL DEFAULT 'A' COMMENT '假期種類',
  `log_bool` int(11) DEFAULT '0' COMMENT '是否有最大天數限制 0:無 1：有',
  `max_log` int(11) DEFAULT NULL COMMENT '最大天數限制',
  `sub_bool` int(11) DEFAULT '0' COMMENT '是否扣減工資  0：否  1：是',
  `sub_multiple` int(11) DEFAULT '0' COMMENT '扣減倍數（0-100）%',
  `city` varchar(255) DEFAULT NULL,
  `only` varchar(100) DEFAULT 'local',
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT NULL,
  `lud` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='請假配置表';
