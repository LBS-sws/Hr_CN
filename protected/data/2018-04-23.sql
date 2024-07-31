/*
Navicat MySQL Data Transfer

Source Server         : local
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : hrdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2018-04-23 16:17:22
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for hr_assess
-- ----------------------------
DROP TABLE IF EXISTS `hr_assess`;
CREATE TABLE `hr_assess` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `city` varchar(100) NOT NULL,
  `work_type` varchar(255) DEFAULT NULL COMMENT '工种',
  `service_effect` varchar(255) DEFAULT NULL COMMENT '服務效果',
  `service_process` varchar(255) DEFAULT NULL COMMENT '服务流程',
  `carefully` varchar(255) DEFAULT NULL COMMENT '細心度',
  `judge` varchar(255) DEFAULT NULL COMMENT '判斷力',
  `deal` varchar(255) DEFAULT NULL COMMENT '處理能力',
  `connects` varchar(255) DEFAULT NULL COMMENT '溝通能力',
  `obey` varchar(255) DEFAULT NULL COMMENT '服從度',
  `leadership` varchar(255) DEFAULT NULL COMMENT '領導力',
  `characters` text COMMENT '性格',
  `assess` text COMMENT '評估',
  `email_bool` int(2) DEFAULT '0' COMMENT '是否已經發送郵件0：無 1：有',
  `email_list` text,
  `staff_type` varchar(255) DEFAULT '3' COMMENT '工種',
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='員工評估表';
