/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : hrdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2022-05-25 10:51:28
*/
-- ----------------------------
-- Table structure for hr_pin_name
-- ----------------------------
ALTER TABLE hr_pin_name ADD COLUMN pin_type int(2) NOT NULL DEFAULT 0 COMMENT '0:普通章 1：兩項章' AFTER class_id;
