/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : hrdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2020-01-02 11:12:23
*/
-- ----------------------------
-- Table structure for hr_apply_support
-- ----------------------------
ALTER TABLE hr_apply_support ADD COLUMN privilege int(2) NOT NULL DEFAULT 0 COMMENT '0:不使用特權  1：人員置換  2：優先權' AFTER apply_type;
ALTER TABLE hr_apply_support ADD COLUMN privilege_user int(2) NULL DEFAULT NULL COMMENT '人員置換的員工id' AFTER privilege;
