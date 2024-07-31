/*
Navicat MySQL Data Transfer

Source Server         : vm1
Source Server Version : 50626
Source Host           : 192.168.1.9:3306
Source Database       : hr

Target Server Type    : MYSQL
Target Server Version : 50626
File Encoding         : 65001

Date: 2017-07-06 15:52:33
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
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ldd` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='公司資料表';

-- ----------------------------
-- Records of hr_company
-- ----------------------------

-- ----------------------------
-- Table structure for hr_contract
-- ----------------------------
DROP TABLE IF EXISTS `hr_contract`;
CREATE TABLE `hr_contract` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `city` varchar(30) NOT NULL,
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COMMENT='合同表';

-- ----------------------------
-- Records of hr_contract
-- ----------------------------

-- ----------------------------
-- Table structure for hr_contract_docx
-- ----------------------------
DROP TABLE IF EXISTS `hr_contract_docx`;
CREATE TABLE `hr_contract_docx` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `contract_id` int(10) NOT NULL,
  `docx` int(10) NOT NULL,
  `index` int(10) DEFAULT NULL COMMENT '層級',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8 COMMENT='合同與文檔的關連表';

-- ----------------------------
-- Records of hr_contract_docx
-- ----------------------------

-- ----------------------------
-- Table structure for hr_docx
-- ----------------------------
DROP TABLE IF EXISTS `hr_docx`;
CREATE TABLE `hr_docx` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `docx_url` varchar(300) NOT NULL,
  `type` varchar(30) NOT NULL COMMENT '文檔可見類型（local：本地可見，default：全球可見）',
  `city` varchar(30) DEFAULT NULL,
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COMMENT='合同文檔';

-- ----------------------------
-- Records of hr_docx
-- ----------------------------
INSERT INTO `hr_docx` VALUES ('5', '合同基本条例', 'upload/contract/20170705114757.docx', 'default', 'SZ', 'shenchao', null, '2017-07-05 11:47:57', null);
INSERT INTO `hr_docx` VALUES ('6', '保密协议', 'upload/contract/20170705114843.docx', 'default', 'SZ', 'shenchao', null, '2017-07-05 11:48:43', null);
INSERT INTO `hr_docx` VALUES ('7', '外勤协议', 'upload/contract/20170705114921.docx', 'default', 'SZ', 'shenchao', 'shenchao', '2017-07-05 11:49:54', '2017-07-05 11:49:55');
INSERT INTO `hr_docx` VALUES ('8', '安全协议', 'upload/contract/20170705114947.docx', 'default', 'SZ', 'shenchao', null, '2017-07-05 11:49:47', null);

-- ----------------------------
-- Table structure for hr_employee
-- ----------------------------
DROP TABLE IF EXISTS `hr_employee`;
CREATE TABLE `hr_employee` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL COMMENT '員工名字',
  `code` varchar(20) NOT NULL COMMENT '員工編號',
  `sex` varchar(10) DEFAULT NULL,
  `city` varchar(20) NOT NULL,
  `company_id` int(10) unsigned NOT NULL COMMENT '公司id',
  `contract_id` int(10) unsigned NOT NULL COMMENT '合同id',
  `user_card` varchar(50) NOT NULL COMMENT '身份證號碼',
  `address` varchar(255) NOT NULL COMMENT '員工住址',
  `contact_address` varchar(255) NOT NULL COMMENT '通訊地址',
  `phone` varchar(20) NOT NULL COMMENT '聯繫電話',
  `department` varchar(20) NOT NULL COMMENT '部門',
  `position` varchar(20) NOT NULL COMMENT '職位',
  `wage` int(20) unsigned NOT NULL COMMENT '工資',
  `start_time` date NOT NULL COMMENT '合同開始時間',
  `end_time` date NOT NULL COMMENT '合同結束時間',
  `test_start_time` date DEFAULT NULL COMMENT '試用期開始時間',
  `test_end_time` date DEFAULT NULL COMMENT '試用期結束時間',
  `test_wage` int(20) unsigned DEFAULT NULL COMMENT '試用期工資',
  `test_type` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '試用期類型：0（無試用期）、 1（有試用期）',
  `word_status` int(10) NOT NULL DEFAULT '0' COMMENT '是否已經生成合同：0（沒有）、1（有）',
  `word_url` varchar(300) DEFAULT NULL COMMENT '員工合同的地址',
  `lcu` varchar(20) DEFAULT NULL,
  `luu` varchar(20) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8 COMMENT='員工表';

-- ----------------------------
-- Records of hr_employee
-- ----------------------------
