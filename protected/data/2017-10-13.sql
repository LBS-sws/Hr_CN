/*
Navicat MySQL Data Transfer

Source Server         : local
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : hrdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2017-10-13 10:39:26
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for hr_dept
-- ----------------------------
DROP TABLE IF EXISTS `hr_dept`;
CREATE TABLE `hr_dept` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `z_index` varchar(11) DEFAULT '0',
  `type` int(11) NOT NULL DEFAULT '0' COMMENT '0:部門  1:職位',
  `city` varchar(255) DEFAULT NULL,
  `dept_id` int(11) DEFAULT '1' COMMENT '部門id',
  `dept_class` varchar(50) DEFAULT NULL COMMENT '職位類別',
  `lcu` varchar(50) DEFAULT NULL,
  `luu` varchar(50) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 COMMENT='工作部門';
