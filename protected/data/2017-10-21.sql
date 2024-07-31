/*
Navicat MySQL Data Transfer

Source Server         : local
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : hrdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2017-10-21 10:57:33
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for hr_employee_reward
-- ----------------------------
DROP TABLE IF EXISTS `hr_employee_reward`;
CREATE TABLE `hr_employee_reward` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `employee_code` varchar(100) NOT NULL,
  `employee_name` varchar(255) NOT NULL,
  `reward_id` int(11) NOT NULL,
  `reward_name` varchar(255) NOT NULL,
  `reward_money` varchar(255) DEFAULT NULL,
  `reward_goods` varchar(255) DEFAULT NULL,
  `remark` text,
  `reject_remark` text,
  `status` int(10) NOT NULL DEFAULT '0',
  `city` varchar(255) DEFAULT NULL,
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='員工獲獎列表';

-- ----------------------------
-- Table structure for hr_reward
-- ----------------------------
DROP TABLE IF EXISTS `hr_reward`;
CREATE TABLE `hr_reward` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT '獎勵名字',
  `type` int(11) NOT NULL DEFAULT '0' COMMENT '獎勵類型：0（僅獎金）、1（僅物品）、2（獎金加物品）',
  `money` varchar(100) DEFAULT NULL COMMENT '獎金',
  `goods` varchar(255) DEFAULT NULL COMMENT '獎勵物品',
  `city` varchar(100) DEFAULT NULL,
  `lcu` varchar(100) DEFAULT NULL,
  `luu` varchar(100) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='獎勵表';
