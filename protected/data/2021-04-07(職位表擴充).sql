/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : hrdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2021-04-07 10:51:28
*/
-- ----------------------------
-- Table structure for hr_dept
-- ----------------------------
ALTER TABLE hr_dept ADD COLUMN manager_leave int(2) NOT NULL DEFAULT 0 COMMENT '是否參與銷售部門的段位 0:不參與 1:參與' AFTER manager_type;

update hr_dept set manager_leave = 1 where manager_type in (1,2,3);