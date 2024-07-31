<?php

return array(
	'drs'=>array(
		'webroot'=>'https://dms.lbsapps.cn/dr-uat',
		'name'=>'Daily Report',
		'icon'=>'fa fa-pencil-square-o',
	),
	'acct'=>array(
		'webroot'=>'https://dms.lbsapps.cn/ac-uat',
		'name'=>'Accounting',
		'icon'=>'fa fa-money',
	),
	'ops'=>array(
		'webroot'=>'https://dms.lbsapps.cn/op-uat',
		'name'=>'Operation',
		'icon'=>'fa fa-gears',
	),
	'hr'=>array(
		'webroot'=>'https://dms.lbsapps.cn/hr-uat',
		'name'=>'Personnel',
		'icon'=>'fa fa-users',
	),
	'sal'=>array(
		'webroot'=>'https://dms.lbsapps.cn/sa-uat',
		'name'=>'Sales',
		'icon'=>'fa fa-suitcase',
	),
	'quiz'=>array(
		'webroot'=>'https://dms.lbsapps.cn/qz-uat',
		'name'=>'Quiz',
		'icon'=>'fa fa-pencil',
	),
	'sp'=>array(
		'webroot'=>'https://dms.lbsapps.cn/sp-uat',
		'name'=>'Academic Credit',
		'icon'=>'fa fa-cube',
	),
	'ch'=>array(
		'webroot'=>'https://dms.lbsapps.cn/ch-uat',
		'name'=>'Charitable Credit',
		'icon'=>'fa fa-object-ungroup',
	),
	'svc'=>array(
		'webroot'=>'https://dms.lbsapps.cn/sv-uat',
		'name'=>'Service Report',
		'icon'=>'fa fa-wrench',
	),
	'inv'=>array(
		'webroot'=>'https://dms.lbsapps.cn/in-uat',
		'name'=>'Investment',
		'icon'=>'fa fa-bullseye',
	),
	'fed'=>array(
		'webroot'=>'https://dms.lbsapps.cn/fed-uat',
		'name'=>'Project progress',
		'icon'=>'fa fa-bug',
	),

	'onlib'=>array(
		'webroot'=>'https://onlib.lbsapps.com/seeddms',
		'script'=>'remoteLoginOnlib',
		'name'=>'Online Library',
		'icon'=>'fa fa-book',
		'external'=>array(
				'layout'=>'onlib',
				'update'=>'saveOnlib',		//function defined in UserFormEx.php
				'fields'=>'fieldsOnlib',
			),
	),
	    'nu'=>array(
        'webroot'=>'http://lbsapps.local.cn/nu',
        'name'=>'New United',
        'icon'=>'fa fa-suitcase',
        'param'=>'/admin',
        'script'=>'goNewUnited',
    ),
/*
	'apps'=>array(
		'webroot'=>'https://app.lbsgroup.com.tw/web',
		'script'=>'remoteLoginTwApp',
		'name'=>'Apps System',
		'icon'=>'fa fa-rocket',
		'external'=>true,
	),
*/
);

?>
