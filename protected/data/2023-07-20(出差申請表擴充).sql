
ALTER TABLE hr_employee_trip ADD COLUMN company_name varchar(255) DEFAULT NULL COMMENT '公司名称' AFTER trip_address;

UPDATE hr_employee_trip SET company_name = area_lcu,area_lcu = null WHERE id>0;