/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : hrdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2023-01-11 11:40:24
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for hr_work_leave
-- ----------------------------
DROP TABLE IF EXISTS `hr_work_leave`;
CREATE TABLE `hr_work_leave` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `work_id` int(11) NOT NULL,
  `leave_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='請假調休關聯加班';
