/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : hrdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2019-10-15 11:59:41
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
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8 COMMENT='員工分配表（評估）';

-- ----------------------------
-- Records of hr_review
-- ----------------------------

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
  `status_type` int(11) NOT NULL DEFAULT '1' COMMENT '1:待考核 3：評核成功 4：草稿',
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8 COMMENT='審核列表（主管審核詳情）';

-- ----------------------------
-- Records of hr_review_h
-- ----------------------------

-- ----------------------------
-- Table structure for hr_set
-- ----------------------------
DROP TABLE IF EXISTS `hr_set`;
CREATE TABLE `hr_set` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `set_code` varchar(255) NOT NULL,
  `set_name` varchar(255) NOT NULL,
  `z_index` int(11) NOT NULL DEFAULT '1',
  `set_type` int(11) NOT NULL DEFAULT '1' COMMENT '1:全部可見  2：進本城市可見',
  `city` varchar(255) DEFAULT NULL,
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='審核項目配置表(子項）';

-- ----------------------------
-- Records of hr_set
-- ----------------------------
INSERT INTO `hr_set` VALUES ('1', '甲', '个人基本能力', '999', '1', 'HK', 'shenchao', 'shenchao', '2019-10-07 10:50:53', '2019-10-07 11:05:43');
INSERT INTO `hr_set` VALUES ('2', '乙', '沟通', '900', '1', 'HK', 'shenchao', 'shenchao', '2019-10-07 10:52:21', '2019-10-07 11:06:06');
INSERT INTO `hr_set` VALUES ('3', '丙', '分析及判断', '899', '1', 'HK', 'shenchao', 'shenchao', '2019-10-07 10:52:59', '2019-10-07 11:06:28');
INSERT INTO `hr_set` VALUES ('4', '丁', '公司理念', '888', '1', 'HK', 'shenchao', 'shenchao', '2019-10-07 10:53:50', '2019-10-07 11:05:35');

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
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8 COMMENT='審核項目配置表';

-- ----------------------------
-- Records of hr_set_pro
-- ----------------------------
INSERT INTO `hr_set_pro` VALUES ('1', '4', '发自内心对工作热情，从心底希望下属、 同事、公司能够胜出', '5', '1', 'HK', 'shenchao', 'shenchao', '2019-10-07 15:31:15', '2019-10-07 15:34:47');
INSERT INTO `hr_set_pro` VALUES ('2', '4', ' 理解认同公司核心价值及目标', '1', '1', 'HK', 'shenchao', null, '2019-10-07 15:35:02', '2019-10-07 15:35:02');
INSERT INTO `hr_set_pro` VALUES ('3', '3', '跟进问题至最后及总结', '99', '1', 'HK', 'shenchao', 'shenchao', '2019-10-07 15:38:28', '2019-10-07 15:38:45');
INSERT INTO `hr_set_pro` VALUES ('4', '3', '将工作分辨优先次序予以执行', '55', '1', 'HK', 'shenchao', null, '2019-10-07 15:39:03', '2019-10-07 15:39:03');
INSERT INTO `hr_set_pro` VALUES ('5', '3', '找出事情重点能力', '1', '1', 'HK', 'shenchao', null, '2019-10-07 15:39:16', '2019-10-07 15:39:16');
INSERT INTO `hr_set_pro` VALUES ('8', '1', '责任感', '99', '1', 'HK', 'shenchao', null, '2019-10-07 16:05:13', '2019-10-07 16:05:13');
INSERT INTO `hr_set_pro` VALUES ('9', '2', '能用说话清楚及精准地表达', '1', '1', 'HK', 'shenchao', 'shenchao', '2019-10-10 17:41:16', '2019-10-11 10:23:02');
INSERT INTO `hr_set_pro` VALUES ('10', '2', '将信息、资料、知识文字化、规范化、标准化能力', '1', '1', 'HK', 'shenchao', 'shenchao', '2019-10-10 17:41:16', '2019-10-11 10:23:02');
INSERT INTO `hr_set_pro` VALUES ('11', '2', '聆听能力', '1', '1', 'HK', 'shenchao', null, '2019-10-11 10:24:09', '2019-10-11 10:24:09');
INSERT INTO `hr_set_pro` VALUES ('12', '2', '客观听取别人意见', '1', '1', 'HK', 'shenchao', null, '2019-10-11 10:24:26', '2019-10-11 10:24:26');
INSERT INTO `hr_set_pro` VALUES ('13', '2', '善用及支持有效内部沟通，包括恰当地回应下属、旁线', '1', '1', 'HK', 'shenchao', null, '2019-10-11 10:24:37', '2019-10-11 10:24:37');
INSERT INTO `hr_set_pro` VALUES ('14', '2', '的提问或建议，主动及定时向上司报告及交待工作进度', '1', '1', 'HK', 'shenchao', null, '2019-10-11 10:24:45', '2019-10-11 10:24:45');
INSERT INTO `hr_set_pro` VALUES ('15', '2', '善于与人交流意见及知识及推动交流机会', '1', '1', 'HK', 'shenchao', null, '2019-10-11 10:24:54', '2019-10-11 10:24:54');
INSERT INTO `hr_set_pro` VALUES ('16', '2', '会议之表现及态度', '1', '1', 'HK', 'shenchao', null, '2019-10-11 10:25:03', '2019-10-11 10:25:03');
INSERT INTO `hr_set_pro` VALUES ('17', '2', '对上司 / 下属 / 旁线坦诚度', '1', '1', 'HK', 'shenchao', null, '2019-10-11 10:25:11', '2019-10-11 10:25:11');
INSERT INTO `hr_set_pro` VALUES ('18', '2', '团队及和谐气氛建立', '1', '1', 'HK', 'shenchao', null, '2019-10-11 10:25:20', '2019-10-11 10:25:20');
INSERT INTO `hr_set_pro` VALUES ('19', '1', '以身作则', '1', '1', 'HK', 'shenchao', null, '2019-10-11 10:25:55', '2019-10-11 10:25:55');
INSERT INTO `hr_set_pro` VALUES ('20', '1', '学习精神及能力', '1', '1', 'HK', 'shenchao', null, '2019-10-11 10:26:03', '2019-10-11 10:26:03');
INSERT INTO `hr_set_pro` VALUES ('21', '1', '执行力', '1', '1', 'HK', 'shenchao', null, '2019-10-11 10:26:12', '2019-10-11 10:26:12');
INSERT INTO `hr_set_pro` VALUES ('22', '1', '解决问题能力', '1', '1', 'HK', 'shenchao', null, '2019-10-11 10:26:20', '2019-10-11 10:26:20');
INSERT INTO `hr_set_pro` VALUES ('23', '1', '于压力中的稳定表现力', '1', '1', 'HK', 'shenchao', null, '2019-10-11 10:26:29', '2019-10-11 10:26:29');
INSERT INTO `hr_set_pro` VALUES ('24', '1', '承担力及有勇气去承认错误', '1', '1', 'HK', 'shenchao', null, '2019-10-11 10:26:38', '2019-10-11 10:26:38');
INSERT INTO `hr_set_pro` VALUES ('25', '1', '正面能量及乐观度 (高期望)', '1', '1', 'HK', 'shenchao', null, '2019-10-11 10:26:46', '2019-10-11 10:26:46');
INSERT INTO `hr_set_pro` VALUES ('26', '1', '理解及自我推动以达至公司对该职位之要求及期望，行多一步 (高要求/高期望)', '1', '1', 'HK', 'shenchao', null, '2019-10-11 10:26:55', '2019-10-11 11:17:09');
INSERT INTO `hr_set_pro` VALUES ('28', '1', '配合及协助其他部门的意识及能力', '1', '1', 'HK', 'shenchao', null, '2019-10-11 10:27:13', '2019-10-11 10:27:13');
INSERT INTO `hr_set_pro` VALUES ('29', '1', '健康状态', '1', '1', 'HK', 'shenchao', null, '2019-10-11 10:27:22', '2019-10-11 10:27:22');
INSERT INTO `hr_set_pro` VALUES ('30', '1', '成本控制', '1', '1', 'HK', 'shenchao', null, '2019-10-11 10:27:31', '2019-10-11 10:27:31');
INSERT INTO `hr_set_pro` VALUES ('31', '1', '机密资料处理', '1', '1', 'HK', 'shenchao', null, '2019-10-11 10:27:39', '2019-10-11 10:27:39');
INSERT INTO `hr_set_pro` VALUES ('32', '1', '个人纪律', '1', '1', 'HK', 'shenchao', null, '2019-10-11 10:27:48', '2019-10-11 10:27:48');
INSERT INTO `hr_set_pro` VALUES ('33', '1', '能于既定时间或之前完成工作', '1', '1', 'HK', 'shenchao', null, '2019-10-11 10:27:56', '2019-10-11 10:27:56');
INSERT INTO `hr_set_pro` VALUES ('34', '1', '上班出席率及准时度', '1', '1', 'HK', 'shenchao', null, '2019-10-11 10:28:05', '2019-10-11 10:28:05');
INSERT INTO `hr_set_pro` VALUES ('35', '1', '工作之细心度从细节中经营)', '1', '1', 'HK', 'shenchao', null, '2019-10-11 10:28:13', '2019-10-11 10:28:13');

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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='考核範本';

-- ----------------------------
-- Records of hr_template
-- ----------------------------
