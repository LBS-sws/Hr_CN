/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : hrdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2020-08-20 16:12:23
*/
-- ----------------------------
-- Table structure for hr_boss_audit
-- ----------------------------
ALTER TABLE hr_boss_audit ADD COLUMN boss_type int(2) NOT NULL DEFAULT 1 COMMENT '1:總監審核  2：副總監審核'  AFTER apply_date;
