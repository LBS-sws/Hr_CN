/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : hrdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2019-11-06 11:12:23
*/
-- ----------------------------
-- Table structure for hr_dept
-- ----------------------------
ALTER TABLE hr_dept ADD COLUMN review_type int(2) NOT NULL DEFAULT 1 COMMENT '评核类型 1:正常 2:技术员 3:銷售 4:地區主管' AFTER review_status;


-- ----------------------------
-- Table structure for hr_review
-- ----------------------------
ALTER TABLE hr_review ADD COLUMN review_type int(2) NOT NULL DEFAULT 1 COMMENT '评核类型 1:正常 2:技术员 3:銷售 4:地區主管' AFTER status_type;
ALTER TABLE hr_review ADD COLUMN change_num float(4,2) NOT NULL DEFAULT 0.00 COMMENT '請假天數（技術員）或者評核得分（銷售）' AFTER review_type;

-- ----------------------------
-- Table structure for hr_review_h
-- ----------------------------
ALTER TABLE hr_review_h ADD COLUMN four_with_sum float(4,2) NOT NULL DEFAULT 0.00 COMMENT '四用得分' AFTER review_sum;
ALTER TABLE hr_review_h ADD COLUMN four_with_count int(4) NOT NULL DEFAULT 0 COMMENT '四用的項目數量' AFTER four_with_sum;


-- ----------------------------
-- Table structure for hr_set
-- ----------------------------
ALTER TABLE hr_set ADD COLUMN four_with int(2) NOT NULL DEFAULT 0 COMMENT '是否是四用 0：不是  1：是' AFTER set_type;
