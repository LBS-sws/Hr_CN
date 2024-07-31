/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : hrdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2020-05-07 18:23
*/
-- ----------------------------
-- Table structure for hr_sales_staff
-- ----------------------------
ALTER TABLE hr_sales_staff ADD COLUMN time_off int(11) NOT NULL DEFAULT 0 COMMENT '時間段限制：0（不限制） 1（有限制）' AFTER employee_id;
ALTER TABLE hr_sales_staff ADD COLUMN start_time date NULL DEFAULT NULL AFTER time_off;
ALTER TABLE hr_sales_staff ADD COLUMN end_time date NULL DEFAULT NULL AFTER start_time;
