/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : hrdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2024-07-19 09:46:40
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for hr_send_set_jd
-- ----------------------------
DROP TABLE IF EXISTS `hr_send_set_jd`;
CREATE TABLE `hr_send_set_jd` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `table_id` varchar(255) NOT NULL,
  `set_type` varchar(255) NOT NULL DEFAULT 'warehouse',
  `field_id` varchar(255) NOT NULL,
  `field_value` varchar(255) DEFAULT NULL,
  `field_type` varchar(255) DEFAULT 'text',
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` datetime DEFAULT NULL,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8 COMMENT='金蝶关联的配置表';

-- ----------------------------
-- Table structure for sync_jd_api_curl
-- ----------------------------
DROP TABLE IF EXISTS `hr_bs_api_curl`;
CREATE TABLE `hr_bs_api_curl` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `source_time` varchar(100) NULL DEFAULT NULL COMMENT '查询时间',
  `status_type` char(255) NOT NULL DEFAULT 'p' COMMENT '状态，P:未处理 I:处理中 C：已完成 E：响应异常',
  `info_type` varchar(255) NOT NULL COMMENT '接口类型',
  `data_content` longtext NOT NULL COMMENT '请求内容（json字符串）',
  `out_content` longtext COMMENT '响应的内容',
  `cmd_content` text COMMENT '执行结果',
  `message` varchar(255) DEFAULT NULL,
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='获取北森员工变更（北森系统专属）';

-- ----------------------------
-- Table structure for hr_dept
-- ----------------------------
ALTER TABLE hr_dept ADD COLUMN z_del int(2) NOT NULL DEFAULT 1 COMMENT '是否已删除 1:已删除 0：未删除' AFTER manager_leave;

-- ----------------------------
-- Table structure for hr_employee
-- ----------------------------
ALTER TABLE hr_employee ADD COLUMN bs_staff_id varchar(100) NULL DEFAULT NULL COMMENT '北森员工id' AFTER id;
ALTER TABLE hr_employee_operate ADD COLUMN bs_staff_id varchar(100) NULL DEFAULT NULL COMMENT '北森员工id' AFTER id;

ALTER TABLE hr_employee ADD COLUMN old_department int(10) NULL DEFAULT NULL COMMENT '旧部门id' AFTER department;
ALTER TABLE hr_employee_operate ADD COLUMN old_department int(10) NULL DEFAULT NULL COMMENT '旧部门id' AFTER department;
ALTER TABLE hr_employee ADD COLUMN old_position int(10) NULL DEFAULT NULL COMMENT '旧职位id' AFTER position;
ALTER TABLE hr_employee_operate ADD COLUMN old_position int(10) NULL DEFAULT NULL COMMENT '旧职位id' AFTER position;

update hr_employee set old_department=if(department='' or !(department REGEXP '^[0-9]+$'),null,department) where id>0;
update hr_employee_operate set old_department=if(department='' or !(department REGEXP '^[0-9]+$'),null,department) where id>0;
update hr_employee set old_position=if(position='' or !(position REGEXP '^[0-9]+$'),null,position) where id>0;
update hr_employee_operate set old_position=if(position='' or !(position REGEXP '^[0-9]+$'),null,position) where id>0;