/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : hrdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2019-12-27 14:59:56
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for hr_apply_support
-- ----------------------------
DROP TABLE IF EXISTS `hr_apply_support`;
CREATE TABLE `hr_apply_support` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `support_code` varchar(255) DEFAULT NULL,
  `service_type`  int(11) NOT NULL DEFAULT 1 COMMENT '服務類型 1：服务支援 2：技術支援' ,
  `apply_date` date NOT NULL COMMENT '申請時間',
  `apply_num` int(11) NOT NULL DEFAULT '1' COMMENT '申請人數（暫定字段，不使用）',
  `apply_type` int(11) NOT NULL DEFAULT '1' COMMENT '申请类型： 1（新申请） 2（续期）',
  `apply_end_date` date NOT NULL COMMENT '結束時間',
  `apply_length` int(11) NOT NULL DEFAULT '1' COMMENT '申請總時間',
  `apply_remark` text COMMENT '申請備註',
  `length_type` int(11) NOT NULL DEFAULT '1' COMMENT '時間類型  1：月  2：天',
  `apply_city` varchar(255) NOT NULL COMMENT '申請支援的城市',
  `apply_lcu` varchar(255) NOT NULL COMMENT '申請人',
  `update_type` int(11) NOT NULL DEFAULT '0' COMMENT '0:无修改  1：修改申请时间',
  `update_remark` text COMMENT '修改備註',
  `employee_id` int(11) DEFAULT NULL COMMENT '支援过去的员工id',
  `audit_remark` text COMMENT '審核備註',
  `tem_s_ist` text COMMENT '評核項目列表(json)',
  `tem_str` text,
  `tem_sum` int(11) DEFAULT NULL COMMENT '項目總分（需要乘10）',
  `review_sum` float(10,2) DEFAULT NULL COMMENT '評核總分',
  `status_type` int(11) NOT NULL DEFAULT '1' COMMENT '1:草稿  2:申請中  3:已查看 4:排隊等候 5:待評分 6:已評分 7:已完成 8:拒绝提前結束 9:申請提前結束 10:申請续期 11:拒绝续期',
  `change_num` float(3,2) NOT NULL DEFAULT '0.00' COMMENT '請假天數',
  `early_date` date DEFAULT NULL COMMENT '提前結束/續期時間',
  `early_remark` text COMMENT '提前結束/續期備註',
  `reject_remark` text COMMENT '拒绝備註',
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 COMMENT='中央技术支援申请';

-- ----------------------------
-- Table structure for hr_apply_support_history
-- ----------------------------
DROP TABLE IF EXISTS `hr_apply_support_history`;
CREATE TABLE `hr_apply_support_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `support_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `apply_length` int(11) NOT NULL DEFAULT '1' COMMENT '申請總時間',
  `length_type` int(11) NOT NULL DEFAULT '1' COMMENT '時間類型  1：月  2：天',
  `status_type` int(255) NOT NULL DEFAULT '2' COMMENT '同步申請支援的狀態',
  `status_remark` text,
  `lcu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8 COMMENT='中央支援记录表';
