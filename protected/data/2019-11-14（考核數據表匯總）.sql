/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : hrdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2019-11-14 10:10:19
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for hr_review
-- ----------------------------
DROP TABLE IF EXISTS `hr_review`;
CREATE TABLE `hr_review` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `year` int(11) NOT NULL DEFAULT '2019' COMMENT '評核年份',
  `year_type` int(11) NOT NULL DEFAULT '1' COMMENT '1:上半年  2：下半年',
  `id_list` text COMMENT '評核人id列表(json)',
  `id_s_list` varchar(255) NOT NULL COMMENT '考核人id（以逗號分割）',
  `name_list` varchar(255) NOT NULL COMMENT '評核人列表',
  `employee_remark` text COMMENT '員工的自我功績',
  `review_remark` text COMMENT '其它功績（主管填寫）',
  `strengths` text COMMENT '員工長處',
  `target` text COMMENT '員工目標',
  `improve` text COMMENT '改進',
  `tem_s_ist` text NOT NULL COMMENT '評核項目列表(json)',
  `tem_str` text NOT NULL,
  `review_sum` float(10,2) DEFAULT NULL COMMENT '評核總分',
  `status_type` int(11) NOT NULL DEFAULT '1' COMMENT '1:評核中  2：部分評核完成 3：評核成功 4：草稿',
  `review_type` int(2) NOT NULL DEFAULT '1' COMMENT '评核类型 1:正常 2:技术员 3:銷售 4:地區主管',
  `change_num` float(4,2) NOT NULL DEFAULT '0.00' COMMENT '請假天數（技術員）或者評核得分（銷售）',
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=utf8 COMMENT='員工分配表（評估）';

-- ----------------------------
-- Table structure for hr_review_h
-- ----------------------------
DROP TABLE IF EXISTS `hr_review_h`;
CREATE TABLE `hr_review_h` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `review_id` int(11) NOT NULL,
  `handle_id` int(11) NOT NULL COMMENT '審核人id',
  `handle_name` varchar(255) NOT NULL COMMENT '審核人名字',
  `handle_per` int(11) NOT NULL COMMENT '考核佔比（%）',
  `tem_s_ist` text NOT NULL COMMENT '評核項目列表(json)',
  `tem_sum` int(11) DEFAULT NULL COMMENT '考核项目的总数量',
  `review_remark` text COMMENT '其它功績',
  `strengths` text COMMENT '員工長處',
  `target` text COMMENT '員工目標',
  `improve` text COMMENT '改進',
  `review_sum` float(10,2) DEFAULT NULL COMMENT '評核總分',
  `four_with_sum` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '四用得分',
  `four_with_count` int(4) NOT NULL DEFAULT '0',
  `status_type` int(11) NOT NULL DEFAULT '1' COMMENT '1:待考核 3：評核成功 4：草稿',
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=83 DEFAULT CHARSET=utf8 COMMENT='審核列表（主管審核詳情）';

-- ----------------------------
-- Table structure for hr_set
-- ----------------------------
DROP TABLE IF EXISTS `hr_set`;
CREATE TABLE `hr_set` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `set_name` varchar(255) NOT NULL,
  `z_index` int(11) NOT NULL DEFAULT '1',
  `set_type` int(11) NOT NULL DEFAULT '1' COMMENT '1:全部可見  2：進本城市可見',
  `four_with` int(2) NOT NULL DEFAULT '0' COMMENT '是否是四用 0：不是  1：是',
  `num_ratio` int(2) NOT NULL DEFAULT '1' COMMENT '基础分数倍率',
  `city` varchar(255) DEFAULT NULL,
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='審核項目配置表(子項）';

-- ----------------------------
-- Table structure for hr_set_pro
-- ----------------------------
DROP TABLE IF EXISTS `hr_set_pro`;
CREATE TABLE `hr_set_pro` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `set_id` int(11) NOT NULL,
  `pro_name` varchar(255) NOT NULL,
  `z_index` int(11) NOT NULL DEFAULT '1',
  `pro_type` int(11) NOT NULL DEFAULT '1' COMMENT '1:全部可見  2：進本城市可見',
  `city` varchar(255) DEFAULT NULL,
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8 COMMENT='審核項目配置表';

-- ----------------------------
-- Table structure for hr_template
-- ----------------------------
DROP TABLE IF EXISTS `hr_template`;
CREATE TABLE `hr_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tem_name` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `tem_str` text NOT NULL,
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='考核範本';

-- ----------------------------
-- Table structure for hr_template_employee
-- ----------------------------
DROP TABLE IF EXISTS `hr_template_employee`;
CREATE TABLE `hr_template_employee` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tem_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `id_list` text COMMENT '評核人id列表(json)',
  `id_s_list` varchar(255) NOT NULL COMMENT '考核人id（以逗號分割）',
  `name_list` varchar(255) NOT NULL COMMENT '評核人列表',
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='員工綁定考核模板';

-- ----------------------------
-- Table structure for hr_dept
-- ----------------------------
ALTER TABLE hr_dept ADD COLUMN review_status int(2) NOT NULL DEFAULT 0 COMMENT '是否參與評分高低差異 0:不參與 1:參與' AFTER technician;
ALTER TABLE hr_dept ADD COLUMN review_type int(2) NOT NULL DEFAULT 1 COMMENT '评核类型 1:正常 2:技术员 3:銷售 4:地區主管' AFTER review_status;
