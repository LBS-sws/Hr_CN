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
-- Table structure for hr_dept
-- ----------------------------
ALTER TABLE hr_dept ADD COLUMN sales_type int(2) NOT NULL DEFAULT 0 COMMENT '是否是銷售部門 0:不是 1:是' AFTER dept_class;
