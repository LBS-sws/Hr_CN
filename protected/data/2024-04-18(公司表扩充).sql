
-- ----------------------------
-- Table structure for hr_company
-- ----------------------------
ALTER TABLE hr_company ADD COLUMN share_bool int(1) NULL DEFAULT 0 COMMENT '是否共享：0（否）1（是）' AFTER phone;
