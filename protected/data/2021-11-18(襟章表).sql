/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : hrdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2021-11-18 14:19:42
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for hr_pin
-- ----------------------------
DROP TABLE IF EXISTS `hr_pin`;
CREATE TABLE `hr_pin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pin_code` varchar(255) DEFAULT NULL,
  `apply_date` date NOT NULL COMMENT '获章日期',
  `employee_id` int(11) NOT NULL COMMENT '员工id',
  `inventory_id` int(11) NOT NULL,
  `pin_num` int(11) NOT NULL COMMENT '获章数量',
  `city` varchar(20) NOT NULL,
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COMMENT='襟章種類';

-- ----------------------------
-- Table structure for hr_pin_class
-- ----------------------------
DROP TABLE IF EXISTS `hr_pin_class`;
CREATE TABLE `hr_pin_class` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `z_index` int(11) NOT NULL DEFAULT '0',
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='襟章種類';

-- ----------------------------
-- Table structure for hr_pin_inventory
-- ----------------------------
DROP TABLE IF EXISTS `hr_pin_inventory`;
CREATE TABLE `hr_pin_inventory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pin_name_id` int(11) NOT NULL,
  `inventory` int(11) NOT NULL DEFAULT '0' COMMENT '庫存數量',
  `safe_stock` int(11) NOT NULL DEFAULT '0' COMMENT '安全庫存',
  `city` varchar(20) NOT NULL,
  `display` int(11) NOT NULL DEFAULT '1' COMMENT '是否顯示 1：顯示',
  `z_index` int(11) NOT NULL DEFAULT '0',
  `residue_num` int(11) NOT NULL DEFAULT '0' COMMENT '剩余数量',
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 COMMENT='襟章庫存';

-- ----------------------------
-- Table structure for hr_pin_inventory_history
-- ----------------------------
DROP TABLE IF EXISTS `hr_pin_inventory_history`;
CREATE TABLE `hr_pin_inventory_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `apply_date` datetime NOT NULL COMMENT '记录日期',
  `inventory_id` int(11) NOT NULL COMMENT '库存id',
  `pin_name_id` int(11) NOT NULL,
  `old_sum` int(11) NOT NULL COMMENT '庫存數量(变更前)',
  `now_sum` int(11) NOT NULL DEFAULT '0' COMMENT '庫存數量(变更后)',
  `apply_name` varchar(255) NOT NULL COMMENT '操作人员的登录账户（昵称）',
  `status_type` int(11) NOT NULL DEFAULT '1' COMMENT '1:庫存修改 2：登記新增 3：登記修改 4:登記刪除',
  `pin_code` varchar(255) DEFAULT NULL,
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8 COMMENT='襟章庫存历史表';

-- ----------------------------
-- Table structure for hr_pin_name
-- ----------------------------
DROP TABLE IF EXISTS `hr_pin_name`;
CREATE TABLE `hr_pin_name` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `class_id` int(11) NOT NULL,
  `z_index` int(11) NOT NULL DEFAULT '0',
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COMMENT='襟章名稱';
