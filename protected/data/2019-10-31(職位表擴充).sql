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
ALTER TABLE hr_dept ADD COLUMN review_status int(2) NOT NULL DEFAULT 0 COMMENT '是否參與評分高低差異 0:不參與 1:參與' AFTER technician;
