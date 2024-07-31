/*
Navicat MySQL Data Transfer

Source Server         : local
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : hrdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2018-09-06 17:19:31
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for hr_employee_word_info
-- ----------------------------
DROP TABLE IF EXISTS `hr_employee_word_info`;
CREATE TABLE `hr_employee_word_info` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `work_id` int(11) NOT NULL COMMENT '加班單id',
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='員工加班額外擴充的時間表';
