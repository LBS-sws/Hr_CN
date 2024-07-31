/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : hrdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2021-02-04 11:35:40
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for hr_boss_set_a
-- ----------------------------
DROP TABLE IF EXISTS `hr_boss_set_a`;
CREATE TABLE `hr_boss_set_a` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `city` varchar(255) DEFAULT NULL,
  `list_text` text,
  `json_text` text COMMENT '考核明細（json）',
  `tacitly` int(2) NOT NULL DEFAULT '0' COMMENT '1：默認  0：本地',
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT NULL COMMENT 'CURRENT_TIMESTAMP',
  `lud` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT 'CURRENT_TIMESTAMP',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 COMMENT='老總年度考核A項配置表';

-- ----------------------------
-- Table structure for hr_boss_set_b
-- ----------------------------
DROP TABLE IF EXISTS `hr_boss_set_b`;
CREATE TABLE `hr_boss_set_b` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `city` varchar(255) DEFAULT NULL,
  `list_text` text,
  `json_text` text COMMENT '考核明細（json）',
  `tacitly` int(2) NOT NULL DEFAULT '0' COMMENT '1：默認  0：本地',
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT NULL COMMENT 'CURRENT_TIMESTAMP',
  `lud` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT 'CURRENT_TIMESTAMP',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COMMENT='老總年度考核B項配置表';

-- ----------------------------
-- Table structure for hr_boss_audit
-- ----------------------------
ALTER TABLE hr_boss_audit ADD COLUMN json_listX text CHARACTER SET utf8 COLLATE utf8_general_ci NULL AFTER boss_type;


-- ----------------------------
-- Table structure for hr_kpi
-- ----------------------------
ALTER TABLE hr_kpi ADD COLUMN tacitly int(2) NOT NULL DEFAULT 1 AFTER sum_bool;
ALTER TABLE hr_kpi ADD COLUMN city varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER sum_bool;
ALTER TABLE hr_kpi ADD COLUMN lcu varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER tacitly;
