/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : hrdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2020-07-15 11:12:23
*/
-- ----------------------------
-- Table structure for hr_employee_work
-- ----------------------------
ALTER TABLE hr_employee_work ADD COLUMN pers_lcu varchar(100) NULL AFTER status;
ALTER TABLE hr_employee_work ADD COLUMN pers_lcd varchar(100) NULL AFTER pers_lcu;

-- ----------------------------
-- Table structure for hr_employee_leave
-- ----------------------------
ALTER TABLE hr_employee_leave ADD COLUMN pers_lcu varchar(100) NULL AFTER status;
ALTER TABLE hr_employee_leave ADD COLUMN pers_lcd varchar(100) NULL AFTER pers_lcu;
