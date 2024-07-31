/* Insert dept */
insert into hr_dept(name, z_index,type,city,dept_id,dept_class,lcu,luu) 
select x.name, x.z_index, x.type, y.code, x.dept_id, x.dept_class, 'admin', 'admin'
from hr_dept x, securityuat.sec_city y 
where x.city='CD' and x.type=0 and y.code<>'CD' and y.code not in (select b.region from securityuat.sec_city b where b.region is not null)

/* Create Mapping table */
create temporary table deptconv as 
select x.id, y.id as cd_id, x.city
from hr_dept x inner join hr_dept y on x.name=y.name and y.city='CD'
where x.city<>'CD' and x.city not in (select b.region from securityuat.sec_city b where b.region is not null)

/* Insert Post */
insert into hr_dept(name, z_index,type,city,dept_id,dept_class,lcu,luu) 
select x.name, x.z_index, x.type, y.code, z.id, x.dept_class, 'admin', 'admin'
from hr_dept x inner join securityuat.sec_city y 
left outer join deptconv z on  x.dept_id=z.cd_id and z.city=y.code
where x.city='CD' and x.type=1 and y.code<>'CD' and y.code not in (select b.region from securityuat.sec_city b where b.region is not null)


