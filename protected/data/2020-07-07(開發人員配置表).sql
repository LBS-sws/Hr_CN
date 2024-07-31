/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : hrdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2020-07-09 14:24:16
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for hr_setting
-- ----------------------------
DROP TABLE IF EXISTS `hr_setting`;
CREATE TABLE `hr_setting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `set_name` varchar(255) NOT NULL,
  `set_city` varchar(255) DEFAULT NULL COMMENT '設置的城市',
  `set_value` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='人事系統的系統配置表';

-- ----------------------------
-- Records of hr_setting
-- ----------------------------
INSERT INTO `hr_setting` VALUES ('1', 'yearLeaveType', 'HK', '1');
