/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : hrdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2020-04-14 16:08:23
*/
-- ----------------------------
-- Table structure for hr_employee
-- ----------------------------
ALTER TABLE hr_employee ADD COLUMN wechat varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '微信号码' AFTER phone2;
ALTER TABLE hr_employee ADD COLUMN urgency_card varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '緊急聯繫人身份證' AFTER wechat;

-- ----------------------------
-- Table structure for hr_employee_operate
-- ----------------------------
ALTER TABLE hr_employee_operate ADD COLUMN wechat varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '微信号码' AFTER phone2;
ALTER TABLE hr_employee_operate ADD COLUMN urgency_card varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '緊急聯繫人身份證' AFTER wechat;

-- ----------------------------
-- Table structure for hr_company
-- ----------------------------
ALTER TABLE hr_company ADD COLUMN agent_address varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '委托代理人地址' AFTER agent;
