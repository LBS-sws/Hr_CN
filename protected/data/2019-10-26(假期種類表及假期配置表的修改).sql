/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : hrdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2019-10-26 11:47:23
*/
-- ----------------------------
-- Table structure for hr_vacation
-- ----------------------------
alter table hr_vacation modify column max_log text CHARACTER SET utf8 COLLATE utf8_general_ci NULL;
ALTER TABLE hr_vacation ADD COLUMN ass_id varchar(11) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '關聯id（0：不關聯）' AFTER max_log ;
ALTER TABLE hr_vacation ADD COLUMN ass_bool int(11) NOT NULL DEFAULT 0  AFTER ass_id;
ALTER TABLE hr_vacation ADD COLUMN ass_id_name varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL  AFTER ass_bool;

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for hr_vacation_type
-- ----------------------------
DROP TABLE IF EXISTS `hr_vacation_type`;
CREATE TABLE `hr_vacation_type` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `vaca_code` varchar(255) NOT NULL COMMENT '假期種類編號（E：年假）',
  `vaca_name` varchar(255) NOT NULL COMMENT '假期種類名字',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of hr_vacation_type
-- ----------------------------
INSERT INTO `hr_vacation_type` VALUES ('1', 'E', '年假');
INSERT INTO `hr_vacation_type` VALUES ('2', 'A', '加班调休');
INSERT INTO `hr_vacation_type` VALUES ('4', 'B', '婚假');
INSERT INTO `hr_vacation_type` VALUES ('5', 'C', '产前假');
INSERT INTO `hr_vacation_type` VALUES ('6', 'D', '事假');
INSERT INTO `hr_vacation_type` VALUES ('7', 'F', '特别调休');
INSERT INTO `hr_vacation_type` VALUES ('8', 'G', '丧假');
INSERT INTO `hr_vacation_type` VALUES ('9', 'H', '护理假');
INSERT INTO `hr_vacation_type` VALUES ('10', 'I', '产假');
INSERT INTO `hr_vacation_type` VALUES ('11', 'J', '晚育假');
INSERT INTO `hr_vacation_type` VALUES ('12', 'K', '哺乳假');
INSERT INTO `hr_vacation_type` VALUES ('13', 'L', '病假');
