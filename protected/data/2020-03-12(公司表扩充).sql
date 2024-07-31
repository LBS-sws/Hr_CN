/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : hrdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2020-03-12 14:08:23
*/
-- ----------------------------
-- Table structure for hr_company
-- ----------------------------
ALTER TABLE hr_company ADD COLUMN phone_two varchar(100) NULL AFTER phone;
