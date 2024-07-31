/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : hrdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2021-11-15 10:08:23
*/
-- ----------------------------
-- Table structure for hr_boss_audit
-- ----------------------------
ALTER TABLE hr_boss_audit ADD COLUMN ratio_a int(2) NOT NULL DEFAULT 50 COMMENT 'a占比：50%' AFTER json_listX;
ALTER TABLE hr_boss_audit ADD COLUMN ratio_b int(2) NOT NULL DEFAULT 35 COMMENT 'b占比：35%' AFTER json_listX;
ALTER TABLE hr_boss_audit ADD COLUMN ratio_c int(2) NOT NULL DEFAULT 15 COMMENT 'c占比：15%' AFTER json_listX;

-- ----------------------------
-- Table structure for hr_boss_set_a
-- ----------------------------
ALTER TABLE hr_boss_set_a ADD COLUMN num_ratio int(2) NOT NULL DEFAULT 50 COMMENT '占比：50%' AFTER tacitly;

-- ----------------------------
-- Table structure for hr_boss_set_b
-- ----------------------------
ALTER TABLE hr_boss_set_b ADD COLUMN num_ratio int(2) NOT NULL DEFAULT 35 COMMENT '占比：35%' AFTER tacitly;

-- ----------------------------
-- Table structure for hr_kpi
-- ----------------------------
INSERT INTO hr_kpi VALUES ('26', 'two_service', '蔚诺租赁服务机器台数', '0', '1', '0', 'CN', '1', '', 'shenchao', '2021-11-12 17:28:02');
