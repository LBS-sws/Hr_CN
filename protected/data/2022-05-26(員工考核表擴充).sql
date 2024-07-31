/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : hrdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2022-05-26 10:51:28
*/
-- ----------------------------
-- Table structure for hr_review
-- ----------------------------
ALTER TABLE hr_review ADD COLUMN ranking_review varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '參與差異性的考核id' AFTER change_num;
ALTER TABLE hr_review ADD COLUMN ranking_bool int(11) NOT NULL DEFAULT 0 COMMENT '是否參與差異性排名 0：不參與' AFTER change_num;
ALTER TABLE hr_review ADD COLUMN ranking_sum int(11) NOT NULL DEFAULT 0 COMMENT '差異性參與總人數' AFTER change_num;
