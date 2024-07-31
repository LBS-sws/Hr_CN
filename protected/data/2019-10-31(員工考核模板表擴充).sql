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
-- Table structure for hr_template_employee
-- ----------------------------
ALTER TABLE hr_template_employee ADD COLUMN id_list text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '評核人id列表(json)' AFTER employee_id;
ALTER TABLE hr_template_employee ADD COLUMN id_s_list varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '考核人id（以逗號分割）' AFTER id_list;
ALTER TABLE hr_template_employee ADD COLUMN name_list varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '評核人列表'  AFTER id_s_list;
