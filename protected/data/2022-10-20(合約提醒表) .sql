/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : hrdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2022-10-20 14:18:27
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for hr_treaty
-- ----------------------------
DROP TABLE IF EXISTS `hr_treaty`;
CREATE TABLE `hr_treaty` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `treaty_code` varchar(255) DEFAULT NULL COMMENT '合約編號',
  `treaty_name` varchar(255) NOT NULL COMMENT '合約名稱',
  `month_num` int(11) DEFAULT NULL COMMENT '合約月份（未使用）',
  `treaty_num` int(11) NOT NULL DEFAULT '0' COMMENT '合約續期次數',
  `city` varchar(20) NOT NULL,
  `apply_date` date DEFAULT NULL COMMENT '合約起始時間',
  `start_date` date DEFAULT NULL COMMENT '合約開始時間',
  `end_date` date DEFAULT NULL COMMENT '合約結束時間',
  `state_type` int(11) NOT NULL DEFAULT '0' COMMENT '0：未使用 1：合約進行中  2：合約已過期 3：合約已終止',
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='合约到期提醒表（主表）';

-- ----------------------------
-- Table structure for hr_treaty_info
-- ----------------------------
DROP TABLE IF EXISTS `hr_treaty_info`;
CREATE TABLE `hr_treaty_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contract_code` varchar(255) DEFAULT NULL COMMENT '合約編號',
  `treaty_id` int(11) NOT NULL,
  `start_date` date NOT NULL COMMENT '合同开始日期',
  `month_num` int(11) DEFAULT NULL COMMENT '合約月份',
  `end_date` date NOT NULL COMMENT '合同结束日期',
  `remark` text COMMENT '备注',
  `email_hint` int(11) NOT NULL DEFAULT '0' COMMENT '0:不需要郵件提醒  1：需要郵件提醒',
  `email_date` date DEFAULT NULL COMMENT '郵件發送時間',
  `email_id` int(11) DEFAULT '0' COMMENT '郵件存儲id',
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='合约到期提醒表（副表）';
