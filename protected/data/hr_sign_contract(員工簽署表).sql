/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : hrdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2020-09-14 10:06:16
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for hr_sign_contract
-- ----------------------------
DROP TABLE IF EXISTS `hr_sign_contract`;
CREATE TABLE `hr_sign_contract` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `send_date` date DEFAULT NULL,
  `sign_type` int(11) NOT NULL DEFAULT '0' COMMENT '類型 0:新合同 1:續約 2:退休合同',
  `employee_id` int(11) NOT NULL,
  `courier_str` varchar(255) DEFAULT NULL,
  `courier_code` varchar(255) DEFAULT NULL,
  `status_type` int(11) NOT NULL DEFAULT '0' COMMENT '0:未填寫 1:草稿 2:已發送 3:審核通過 4:被拒絕 5:刪除',
  `remark` text,
  `reject_remark` text,
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='員工入職、續簽、調職後需要重新簽署合同';
