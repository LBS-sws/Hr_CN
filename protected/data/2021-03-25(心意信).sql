/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : hrdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2021-03-25 17:11:28
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for hr_letter
-- ----------------------------
DROP TABLE IF EXISTS `hr_letter`;
CREATE TABLE `hr_letter` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `letter_id` int(11) NOT NULL DEFAULT '0' COMMENT '前一個id(無效欄位）',
  `employee_id` int(11) NOT NULL,
  `letter_type` int(11) DEFAULT '0' COMMENT '0:建議類  1：傾訴類 2：其他類',
  `letter_title` varchar(255) NOT NULL,
  `letter_body` text NOT NULL,
  `state` int(11) DEFAULT NULL COMMENT '0:草稿 1:已發送 3:待處理 4:已完成',
  `letter_num` int(11) NOT NULL DEFAULT '0' COMMENT '分數',
  `letter_reply` text COMMENT '心意信回復',
  `city` varchar(255) DEFAULT NULL,
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COMMENT='心意信';
