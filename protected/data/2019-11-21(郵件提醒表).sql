/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : hrdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2019-11-21 14:00:39
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for hr_email
-- ----------------------------
DROP TABLE IF EXISTS `hr_email`;
CREATE TABLE `hr_email` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `subject` text NOT NULL COMMENT '邮件主題',
  `message` text NOT NULL COMMENT '郵件內容',
  `city_id` text COMMENT '收到郵件的城市',
  `city_str` text COMMENT '收到郵件的城市',
  `city` varchar(100) NOT NULL COMMENT '歸屬城市',
  `staff_id` text COMMENT '額外收件人',
  `staff_str` text,
  `status_type` int(2) DEFAULT '4',
  `lcu` varchar(100) DEFAULT NULL,
  `luu` varchar(100) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='郵件提醒列表';
