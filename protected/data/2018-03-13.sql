/*
Navicat MySQL Data Transfer

Source Server         : local
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : hrdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2018-03-13 09:42:40
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for hr_dept
-- ----------------------------
DROP TABLE IF EXISTS `hr_dept`;
CREATE TABLE `hr_dept` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `z_index` varchar(11) DEFAULT '0',
  `type` int(11) NOT NULL DEFAULT '0' COMMENT '0:部門  1:職位',
  `city` varchar(255) DEFAULT NULL,
  `dept_id` int(11) DEFAULT '1' COMMENT '部門id',
  `dept_class` varchar(50) DEFAULT NULL COMMENT '職位類別',
  `manager` int(2) NOT NULL DEFAULT '0' COMMENT '0:不是經理  1：是經理',
  `lcu` varchar(50) DEFAULT NULL,
  `luu` varchar(50) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COMMENT='工作部門';

-- ----------------------------
-- Table structure for hr_prize
-- ----------------------------
DROP TABLE IF EXISTS `hr_prize`;
CREATE TABLE `hr_prize` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `prize_date` date DEFAULT NULL COMMENT '嘉许日期',
  `prize_num` int(5) DEFAULT NULL COMMENT '参与人数',
  `employee_id` int(11) NOT NULL,
  `prize_pro` varchar(50) DEFAULT NULL COMMENT '嘉许项目',
  `customer_name` varchar(100) DEFAULT NULL COMMENT '客戶名稱',
  `contact` varchar(50) DEFAULT NULL COMMENT '聯繫人',
  `phone` varchar(50) DEFAULT NULL COMMENT '聯繫人電話',
  `posi` varchar(100) DEFAULT NULL COMMENT '聯繫人職務',
  `photo1` varchar(255) DEFAULT NULL COMMENT '表揚信（獨照）',
  `photo2` varchar(255) DEFAULT NULL COMMENT '與客戶合照',
  `prize_type` int(2) NOT NULL DEFAULT '0' COMMENT '0：表揚信  1：錦旗',
  `type_num` int(11) NOT NULL DEFAULT '0' COMMENT '錦旗數量',
  `status` int(5) DEFAULT '0' COMMENT '0:草稿  1：發送  2：拒絕  3：完成',
  `remark` text COMMENT '備註',
  `reject_remark` text COMMENT '拒絕原因',
  `city` varchar(100) DEFAULT NULL,
  `lcu` varchar(100) DEFAULT NULL,
  `luu` varchar(100) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='錦旗表';
