/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : hrdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2020-06-20 12:37:40
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for hr_boss_audit
-- ----------------------------
DROP TABLE IF EXISTS `hr_boss_audit`;
CREATE TABLE `hr_boss_audit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `city` varchar(255) DEFAULT NULL,
  `results_a` float(11,2) DEFAULT NULL,
  `results_b` float(11,2) DEFAULT NULL,
  `results_c` float(11,2) DEFAULT NULL,
  `results_sum` float(11,2) DEFAULT NULL,
  `status_type` int(2) DEFAULT NULL COMMENT '狀態：0：草稿 1：待審核  2：完成  3：拒絕',
  `reject_remark` text,
  `audit_year` int(5) DEFAULT NULL COMMENT '考核年份',
  `json_text` text COMMENT '考核明細（json）',
  `apply_date` datetime DEFAULT NULL COMMENT '申请日期',
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT NULL COMMENT 'CURRENT_TIMESTAMP',
  `lud` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT 'CURRENT_TIMESTAMP',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='老總年度考核表';
