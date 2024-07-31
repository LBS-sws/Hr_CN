/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : hrdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2020-03-20 16:08:23
*/
-- ----------------------------
-- Table structure for hr_company
-- ----------------------------
ALTER TABLE hr_contract ADD COLUMN retire int(2) NOT NULL DEFAULT 0 COMMENT '0:非退休合同  1：退休合同' AFTER city;
