/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : hrdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2019-10-31 11:12:23
*/
-- ----------------------------
-- Table structure for hr_dept
-- ----------------------------
ALTER TABLE hr_employee ADD COLUMN group_type int(2) NOT NULL DEFAULT 0 COMMENT '組別分類 0:無 1:商業組 2：餐飲組' AFTER staff_id;
ALTER TABLE hr_employee_operate ADD COLUMN group_type int(2) NOT NULL DEFAULT 0 COMMENT '組別分類 0:無 1:商業組 2：餐飲組' AFTER staff_id;
ALTER TABLE hr_dept DROP COLUMN group_type;
