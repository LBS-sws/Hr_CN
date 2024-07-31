/*
Navicat MySQL Data Transfer

Source Server         : local
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : hrdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2017-09-11 14:32:37
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for hr_dept
-- ----------------------------
DROP TABLE IF EXISTS `hr_dept`;
CREATE TABLE `hr_dept` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `z_index` int(11) DEFAULT '0',
  `type` int(11) NOT NULL DEFAULT '0' COMMENT '0:部門  1:職位',
  `city` varchar(255) DEFAULT NULL,
  `lcu` varchar(50) DEFAULT NULL,
  `luu` varchar(50) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COMMENT='工作部門';

-- ----------------------------
-- Table structure for hr_employee
-- ----------------------------
DROP TABLE IF EXISTS `hr_employee`;
CREATE TABLE `hr_employee` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL COMMENT '員工名字',
  `code` varchar(20) DEFAULT NULL COMMENT '員工編號',
  `sex` varchar(10) DEFAULT NULL,
  `city` varchar(20) NOT NULL,
  `company_id` int(10) unsigned NOT NULL COMMENT '公司id',
  `contract_id` int(10) unsigned NOT NULL COMMENT '合同id',
  `user_card` varchar(50) NOT NULL COMMENT '身份證號碼',
  `address` varchar(255) NOT NULL COMMENT '員工住址',
  `address_code` varchar(10) DEFAULT NULL COMMENT '郵政編碼',
  `contact_address` varchar(255) NOT NULL COMMENT '通訊地址',
  `contact_address_code` varchar(10) DEFAULT NULL COMMENT '郵政編碼',
  `phone` varchar(20) NOT NULL COMMENT '聯繫電話',
  `phone2` varchar(20) DEFAULT NULL COMMENT '緊急電話',
  `department` varchar(20) NOT NULL COMMENT '部門',
  `position` varchar(20) NOT NULL COMMENT '職位',
  `wage` int(20) unsigned NOT NULL COMMENT '工資',
  `start_time` date NOT NULL COMMENT '合同開始時間',
  `end_time` date NOT NULL COMMENT '合同結束時間',
  `test_start_time` date DEFAULT NULL COMMENT '試用期開始時間',
  `test_end_time` date DEFAULT NULL COMMENT '試用期結束時間',
  `test_wage` varchar(20) DEFAULT NULL COMMENT '試用期工資',
  `test_type` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '試用期類型：0（無試用期）、 1（有試用期）',
  `test_length` varchar(10) DEFAULT NULL,
  `word_status` int(10) NOT NULL DEFAULT '0' COMMENT '是否已經生成合同：0（沒有）、1（有）',
  `word_url` varchar(300) DEFAULT NULL COMMENT '員工合同的地址',
  `staff_old_status` int(11) NOT NULL DEFAULT '1' COMMENT '員工的前一個狀態',
  `staff_status` int(11) NOT NULL DEFAULT '0' COMMENT '員工狀態：0（已經入職）',
  `entry_time` date DEFAULT '2017-08-01' COMMENT '入職時間',
  `age` varchar(11) DEFAULT NULL COMMENT '年齡',
  `birth_time` date DEFAULT NULL COMMENT '出生日期',
  `ld_card` varchar(40) DEFAULT NULL COMMENT '勞動保障卡號',
  `sb_card` varchar(40) DEFAULT NULL COMMENT '社保卡號',
  `jj_card` varchar(40) DEFAULT NULL COMMENT '公積金卡號',
  `house_type` varchar(20) DEFAULT NULL COMMENT '戶籍類型',
  `health` varchar(100) DEFAULT NULL COMMENT '身體狀態',
  `education` varchar(100) DEFAULT NULL COMMENT '學歷',
  `experience` varchar(100) DEFAULT NULL COMMENT '工作經驗',
  `english` text COMMENT '外語水平',
  `technology` text COMMENT '技術水平',
  `other` text COMMENT '其它',
  `year_day` varchar(11) DEFAULT NULL COMMENT '年假',
  `email` varchar(50) DEFAULT NULL COMMENT '員工郵箱',
  `ject_remark` text COMMENT '拒絕備註',
  `remark` text COMMENT '備註',
  `price1` varchar(20) DEFAULT NULL COMMENT '每月工資',
  `price2` varchar(20) DEFAULT NULL COMMENT '加班工資',
  `price3` varchar(255) DEFAULT NULL COMMENT '每月補貼',
  `image_user` varchar(255) DEFAULT NULL COMMENT '員工照片地址',
  `image_code` varchar(255) DEFAULT NULL COMMENT '員工身份證照片',
  `image_work` varchar(255) DEFAULT NULL COMMENT '工作證明照片',
  `image_other` varchar(255) DEFAULT NULL COMMENT '其它照片',
  `staff_type` varchar(50) DEFAULT NULL,
  `staff_leader` varchar(50) DEFAULT NULL,
  `lcu` varchar(20) DEFAULT NULL,
  `luu` varchar(20) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8 COMMENT='員工表';

-- ----------------------------
-- Table structure for hr_employee_history
-- ----------------------------
DROP TABLE IF EXISTS `hr_employee_history`;
CREATE TABLE `hr_employee_history` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` int(10) unsigned NOT NULL COMMENT '員工id',
  `history_id` varchar(10) DEFAULT NULL COMMENT '歷史id',
  `status` varchar(255) NOT NULL COMMENT '操作',
  `remark` varchar(255) DEFAULT NULL COMMENT '備註',
  `lcu` varchar(255) NOT NULL COMMENT '操作人',
  `lcd` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '操作時間',
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=utf8 COMMENT='員工被操作的歷史';

-- ----------------------------
-- Table structure for hr_employee_operate
-- ----------------------------
DROP TABLE IF EXISTS `hr_employee_operate`;
CREATE TABLE `hr_employee_operate` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` int(10) unsigned NOT NULL,
  `name` varchar(30) NOT NULL COMMENT '員工名字',
  `code` varchar(20) DEFAULT NULL COMMENT '員工編號',
  `sex` varchar(10) DEFAULT NULL,
  `city` varchar(20) NOT NULL,
  `company_id` int(10) unsigned NOT NULL COMMENT '公司id',
  `contract_id` int(10) unsigned NOT NULL COMMENT '合同id',
  `user_card` varchar(50) NOT NULL COMMENT '身份證號碼',
  `address` varchar(255) NOT NULL COMMENT '員工住址',
  `address_code` varchar(10) DEFAULT NULL COMMENT '郵政編碼',
  `contact_address` varchar(255) NOT NULL COMMENT '通訊地址',
  `contact_address_code` varchar(10) DEFAULT NULL COMMENT '郵政編碼',
  `phone` varchar(20) NOT NULL COMMENT '聯繫電話',
  `phone2` varchar(20) DEFAULT NULL COMMENT '緊急電話',
  `department` varchar(20) NOT NULL COMMENT '部門',
  `position` varchar(20) NOT NULL COMMENT '職位',
  `wage` int(20) unsigned NOT NULL COMMENT '工資',
  `start_time` date NOT NULL COMMENT '合同開始時間',
  `end_time` date NOT NULL COMMENT '合同結束時間',
  `test_start_time` date DEFAULT NULL COMMENT '試用期開始時間',
  `test_end_time` date DEFAULT NULL COMMENT '試用期結束時間',
  `test_wage` varchar(20) DEFAULT NULL COMMENT '試用期工資',
  `test_type` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '試用期類型：0（無試用期）、 1（有試用期）',
  `test_length` varchar(10) DEFAULT NULL,
  `word_status` int(10) NOT NULL DEFAULT '0' COMMENT '是否已經生成合同：0（沒有）、1（有）',
  `word_url` varchar(300) DEFAULT NULL COMMENT '員工合同的地址',
  `staff_old_status` int(11) NOT NULL DEFAULT '1' COMMENT '員工的前一個狀態',
  `operation` varchar(255) NOT NULL DEFAULT 'update' COMMENT '操作原因',
  `finish` int(10) NOT NULL DEFAULT '0' COMMENT '是否完成。1：是，0：否',
  `staff_status` int(11) NOT NULL DEFAULT '0' COMMENT '員工狀態：0（已經入職）',
  `entry_time` date DEFAULT '2017-08-01' COMMENT '入職時間',
  `age` varchar(11) DEFAULT NULL COMMENT '年齡',
  `birth_time` date DEFAULT NULL COMMENT '出生日期',
  `ld_card` varchar(40) DEFAULT NULL COMMENT '勞動保障卡號',
  `sb_card` varchar(40) DEFAULT NULL COMMENT '社保卡號',
  `jj_card` varchar(40) DEFAULT NULL COMMENT '公積金卡號',
  `house_type` varchar(20) DEFAULT NULL COMMENT '戶籍類型',
  `health` varchar(100) DEFAULT NULL COMMENT '身體狀態',
  `education` varchar(100) DEFAULT NULL COMMENT '學歷',
  `experience` varchar(100) DEFAULT NULL COMMENT '工作經驗',
  `english` text COMMENT '外語水平',
  `technology` text COMMENT '技術水平',
  `other` text COMMENT '其它',
  `year_day` varchar(11) DEFAULT NULL COMMENT '年假',
  `email` varchar(50) DEFAULT NULL COMMENT '員工郵箱',
  `ject_remark` text COMMENT '拒絕備註',
  `remark` text COMMENT '備註',
  `update_remark` text COMMENT '變更說明',
  `price1` varchar(20) DEFAULT NULL COMMENT '每月工資',
  `price2` varchar(20) DEFAULT NULL COMMENT '加班工資',
  `price3` varchar(255) DEFAULT NULL COMMENT '每月補貼',
  `image_user` varchar(255) DEFAULT NULL COMMENT '員工照片地址',
  `image_code` varchar(255) DEFAULT NULL COMMENT '員工身份證照片',
  `image_work` varchar(255) DEFAULT NULL COMMENT '工作證明照片',
  `image_other` varchar(255) DEFAULT NULL COMMENT '其它照片',
  `staff_type` varchar(50) DEFAULT NULL,
  `staff_leader` varchar(50) DEFAULT NULL,
  `lcu` varchar(20) DEFAULT NULL,
  `luu` varchar(20) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 COMMENT='員工表';

-- ----------------------------
-- Table structure for hr_employee_wages
-- ----------------------------
DROP TABLE IF EXISTS `hr_employee_wages`;
CREATE TABLE `hr_employee_wages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `city` varchar(40) DEFAULT NULL,
  `employee_id` int(10) unsigned NOT NULL,
  `wages_head` text,
  `wages_body` varchar(255) DEFAULT NULL,
  `wages_date` date DEFAULT NULL,
  `wages_status` int(11) NOT NULL DEFAULT '1',
  `just_remark` varchar(255) DEFAULT NULL,
  `hour` varchar(50) DEFAULT NULL,
  `sum` varchar(50) DEFAULT NULL,
  `lcu` varchar(50) DEFAULT NULL,
  `luu` varchar(50) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='員工的工資表';

-- ----------------------------
-- Table structure for hr_wages
-- ----------------------------
DROP TABLE IF EXISTS `hr_wages`;
CREATE TABLE `hr_wages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `wages_name` varchar(30) NOT NULL COMMENT '工資組成名稱',
  `city` varchar(30) NOT NULL COMMENT '所在城市',
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='工資配置表';

-- ----------------------------
-- Table structure for hr_wages_con
-- ----------------------------
DROP TABLE IF EXISTS `hr_wages_con`;
CREATE TABLE `hr_wages_con` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `wages_id` int(10) unsigned NOT NULL COMMENT '工資id',
  `type_name` varchar(30) NOT NULL COMMENT '屬性名稱',
  `compute` int(11) NOT NULL DEFAULT '0' COMMENT '計算方式：0（固定工資）、1（每小時工資）、2（提成百分比）',
  `z_index` int(11) NOT NULL DEFAULT '0' COMMENT '層級',
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COMMENT='工資組合表(配置)';
