/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : hrdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2023-04-26 15:42:30
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for hr_recruit
-- ----------------------------
DROP TABLE IF EXISTS `hr_recruit`;
CREATE TABLE `hr_recruit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `year` int(4) NOT NULL COMMENT '招聘的年份',
  `start_date` date DEFAULT NULL COMMENT '招聘開始日期（暫時不用）',
  `end_date` date DEFAULT NULL COMMENT '招聘結束日期（暫時不用）',
  `city` varchar(20) NOT NULL COMMENT '招聘城市',
  `leader_id` int(11) NOT NULL COMMENT '崗位id',
  `dept_id` int(11) NOT NULL COMMENT '職位id',
  `recruit_num` int(5) NOT NULL COMMENT '招聘人數',
  `now_num` int(5) DEFAULT '0' COMMENT '已到崗人數（暫時不用）',
  `leave_num` int(5) DEFAULT '0' COMMENT '到崗後再離職人數（暫時不用）',
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='招聘登記表';
