/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : hrdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2020-02-27 09:08:23
*/
-- ----------------------------
-- Table structure for hr_dept
-- ----------------------------
ALTER TABLE hr_employee_leave_info ADD COLUMN start_time_lg varchar(10) NOT NULL DEFAULT 'AM' AFTER start_time;
ALTER TABLE hr_employee_leave_info ADD COLUMN end_time_lg varchar(10) NOT NULL DEFAULT 'AM' AFTER end_time;
