/*
Navicat MySQL Data Transfer

Source Server         : local
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : hrdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2017-09-20 15:57:23
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for hr_company
-- ----------------------------
DROP TABLE IF EXISTS `hr_company`;
CREATE TABLE `hr_company` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL COMMENT '公司名字',
  `agent` varchar(30) NOT NULL COMMENT '代理人',
  `head` varchar(30) NOT NULL COMMENT '負責人',
  `address` varchar(255) NOT NULL COMMENT '公司地址',
  `city` varchar(30) NOT NULL COMMENT '公司歸屬地區',
  `phone` varchar(255) DEFAULT NULL COMMENT '公司電話',
  `tacitly` varchar(11) DEFAULT '0' COMMENT '默認公司：0（否）1（是）',
  `organization_code` varchar(30) DEFAULT NULL COMMENT '組織機構代碼',
  `organization_time` varchar(60) DEFAULT NULL COMMENT '組織機構代碼發出時間',
  `security_code` varchar(30) DEFAULT NULL COMMENT '勞動保障代碼',
  `license_code` varchar(30) DEFAULT NULL COMMENT '證照編號',
  `license_time` varchar(60) DEFAULT NULL COMMENT '證照發出日期',
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='公司資料表';
