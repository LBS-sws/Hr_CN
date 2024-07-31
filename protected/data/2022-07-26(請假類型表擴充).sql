ALTER TABLE hr_vacation ADD COLUMN remark text CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '請假類型說明' AFTER city;
ALTER TABLE hr_vacation ADD COLUMN z_display int(2) NOT NULL DEFAULT 1 COMMENT '是否顯示 0：不顯示 1：顯示' AFTER city;
