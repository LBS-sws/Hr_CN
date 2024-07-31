-- ----------------------------
-- Table structure for hr_dept
-- ----------------------------
ALTER TABLE hr_dept ADD COLUMN level_type int(2) NULL DEFAULT NULL COMMENT '技术员归类' AFTER manager_type;

update hr_dept set level_type = 1 where review_type=2 and review_status=1;
update hr_dept set level_type = 2 where dept_class='Technician' and review_status=0;