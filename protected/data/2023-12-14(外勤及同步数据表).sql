
-- ----------------------------
-- Table structure for hr_api_curl
-- ----------------------------
DROP TABLE IF EXISTS `hr_api_curl`;
CREATE TABLE `hr_api_curl` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `status_type` char(255) NOT NULL DEFAULT 'p' COMMENT '状态，p:未发送 C：已完成 E：响应异常',
  `info_type` varchar(255) NOT NULL,
  `info_url` varchar(255) NOT NULL COMMENT '接口地址',
  `data_content` longtext NOT NULL COMMENT '发送的curl（json字符串）',
  `out_content` text COMMENT '响应的内容',
  `message` varchar(255) DEFAULT NULL,
  `lcu` varchar(255) DEFAULT NULL,
  `luu` varchar(255) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8 COMMENT='已发送的CURL';

-- ----------------------------
-- Table structure for hr_table_history
-- ----------------------------
DROP TABLE IF EXISTS `hr_table_history`;
CREATE TABLE `hr_table_history` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `table_id` int(11) NOT NULL COMMENT '数据表id',
  `table_name` varchar(255) NOT NULL COMMENT '数据表名称',
  `update_type` int(11) NOT NULL DEFAULT '1' COMMENT '修改類型 1：修改 2:新增 3:删除',
  `update_html` text NOT NULL COMMENT '修改內容',
  `update_json` text COMMENT '修改後的json數據',
  `lcu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='兼职外聘修改記錄表';

-- ----------------------------
-- Table structure for hr_employee
-- ----------------------------
ALTER TABLE hr_employee ADD COLUMN table_type int(2) NULL DEFAULT 1 COMMENT '类型：1：专职  2：兼职 3：外聘' AFTER code;

-- ----------------------------
-- Table structure for hr_employee_operate
-- ----------------------------
ALTER TABLE hr_employee_operate ADD COLUMN table_type int(2) NULL DEFAULT 1 COMMENT '类型：1：专职  2：兼职 3：外聘' AFTER code;
