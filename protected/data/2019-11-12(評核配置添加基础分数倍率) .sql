/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : hrdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2019-11-12 11:12:23
*/
-- ----------------------------
-- Table structure for hr_set
-- ----------------------------
ALTER TABLE hr_set ADD COLUMN num_ratio int(2) NOT NULL DEFAULT 1 COMMENT '基础分数倍率' AFTER four_with;

-- ----------------------------
-- 清空考核數據
-- ----------------------------
DELETE a,b FROM hr_review a LEFT JOIN hr_review_h b ON a.id = b.review_id WHERE a.id>0