<?php

return array(
    'Company Detail'=>array(//集团/公司资质信息
        'access'=>'CL',
        'icon'=>'fa-building',
        'items'=>array(
            //公司资料
            'Company Info'=>array(
                'access'=>'CL01',
                'url'=>'/company/index',
            ),
        ),
    ),
	'Staff Apply Info'=>array(//员工申请流程
		'access'=>'ZA',
		'icon'=>'fa-pencil-square-o',
		'items'=>array(
            //配置工资单
            'Wages Config'=>array(
                'access'=>'ZA03',
                'url'=>'/wages/index',
            ),
            //制作工资单
            'Wages Make'=>array(
                'access'=>'ZA04',
                'url'=>'/makeWages/index',
            ),
            //加班申请
            'apply for work overtime'=>array(
                'access'=>'ZA05',
                'url'=>'/work/index',
            ),
            //请假申请
            'Application for leave'=>array(
                'access'=>'ZA06',
                'url'=>'/leave/index',
            ),
            //出差申请
            'Application for trip'=>array(
                'access'=>'ZA10',
                'url'=>'/trip/index',
            ),
            //奖金申请
            'Reward Apply'=>array(
                'access'=>'ZA07',
                'url'=>'/reward/index',
            ),
            //加班审核
            'audit for work overtime'=>array(
                'access'=>'ZA08',
                //'url'=>'/employer/index',
                'url'=>'/auditWork/index?only=1',
            ),
            //请假审核
            'audit for leave'=>array(
                'access'=>'ZA09',
                'url'=>'/auditLeave/index?only=1',
            ),
		),
	),
    //意見反饋
	'Feedback'=>array(
		'access'=>'HL',
		'icon'=>'fa-paper-plane',
		'items'=>array(
		    //心意信
            'Apply Heart letter'=>array(
                'access'=>'HL01',
                'url'=>'/heartLetter/index',
            ),
		    //審核心意信
            'Audit Heart letter'=>array(
                'access'=>'HL02',
                'url'=>'/heartLetterAudit/index',
            ),
		    //查詢心意信得分
            'Search Heart letter'=>array(
                'access'=>'HL03',
                'url'=>'/heartLetterSearch/index',
            ),
		    //查詢心意信得分
            'Search Heart letter rank'=>array(
                'access'=>'HL04',
                'url'=>'/heartLetterRank/index',
            ),
		),
	),
    //襟章
	'Pin'=>array(
		'access'=>'PI',
		'icon'=>'fa-cube',
		'items'=>array(
		    //襟章登記
            'Apply For Pin'=>array(
                'access'=>'PI01',
                'url'=>'/pinApply/index',
            ),
		    //襟章庫存
            'Pin Inventory'=>array(
                'access'=>'PI02',
                'url'=>'/pinInventory/index',
            ),
		    //襟章種類
            'Pin Class'=>array(
                'access'=>'PI03',
                'url'=>'/pinClass/index',
            ),
		    //襟章名稱
            'Pin Name'=>array(
                'access'=>'PI04',
                'url'=>'/pinName/index',
            ),
		    //襟章发放统计表
            'Pin Table'=>array(
                'access'=>'PI05',
                'url'=>'/pinTable/index',
            ),
		),
	),
    //人事
    'Personnel Audit'=>array(
        'access'=>'ZP',
        'icon'=>'fa-beer',
        'items'=>array(
            //加班审核
            'audit for work overtime'=>array(
                'access'=>'ZP01',
                'url'=>'/auditWork/index?only=5',
            ),
            //请假审核
            'audit for leave'=>array(
                'access'=>'ZP02',
                'url'=>'/auditLeave/index?only=5',
            ),
            //招聘登記
            'recruit apply'=>array(
                'access'=>'ZP03',
                'url'=>'/recruitApply/index',
            ),
            //招聘進度匯總
            'recruit summary'=>array(
                'access'=>'ZP04',
                'url'=>'/recruitSummary/index',
            ),
            //部门人数统计
            'staff summary'=>array(
                'access'=>'ZP05',
                'url'=>'/staffSummary/index',
            ),
        )
    ),
    //合同模塊
	'Contract'=>array(
		'access'=>'ZD',
		'icon'=>'fa-file-pdf-o',
		'items'=>array(
            //补充协议
			'Supplemental Agreement'=>array(
				'access'=>'ZD03',
				'url'=>'/agreement/index',
			),
            //合同文档
			'Contract Word'=>array(
				'access'=>'ZD01',
				'url'=>'/word/index',
			),
            //合同模板列表
			'Contract List'=>array(
				'access'=>'ZD02',
				'url'=>'/contract/index',
			),
            //常用表格下载
            'Common forms download'=>array(
                'access'=>'ZD04',//YB07
                'url'=>'/downForm/index',
            ),
		),
	),
    //合约提醒
	'Treaty Hint'=>array(
        'access'=>'TH',
        'icon'=>'fa-archive',
        'items'=>array(
            //進行中的合約
            'Treaty Service'=>array(
                'access'=>'TH01',//
                'url'=>'/treatyService/index',
            ),
            //已停止的合約
            'Treaty Stop'=>array(
                'access'=>'TH02',//
                'url'=>'/treatyStop/index',
            ),
        )
    ),
	'Employee'=>array(
		'access'=>'ZE',
		'icon'=>'fa-smile-o',
		'items'=>array(
            //員工錄入
            'Employee Info'=>array(
                'access'=>'ZE01',
                'url'=>'/employ/index',
            ),
            //在職員工列表
			'Job Employee List'=>array(
				'access'=>'ZE03',
				'url'=>'/employee/index',
			),
            //離職員工列表
            'Departure Employee List'=>array(
                'access'=>'ZE02',
                'url'=>'/departure/index',
            ),
            //員工變更列表
			'Employee Update List'=>array(
				'access'=>'ZE04',
				'url'=>'/history/index',
			),
            //簽署合同
            'sign contract'=>array(
                'access'=>'ZE09',
                'url'=>'/signContract/index',
            ),
            //加班审核
            'audit for work overtime'=>array(
                'access'=>'ZE05',
                'url'=>'/auditWork/index?only=2',
            ),
            //请假审核
            'audit for leave'=>array(
                'access'=>'ZE06',
                'url'=>'/auditLeave/index?only=2',
            ),
            //技术员评估
            'Staff appraisal'=>array(
                'access'=>'ZE07',
                'url'=>'/assess/index',
            ),
            //锦旗列表
            'Pennants List'=>array(
                'access'=>'ZE08',
                'url'=>'/prize/index',
            ),
            //员工户籍查询
            'household Search'=>array(
                'access'=>'ZE10',
                'url'=>'/houseSearch/index',
            ),
		),
	),
	'External'=>array(//兼职及外聘员工
		'access'=>'EL',
		'icon'=>'fa-puzzle-piece',
		'items'=>array(
            //兼职及外聘员工
            'External Info'=>array(
                'access'=>'EL01',
                'url'=>'/external/index',
            ),
            //兼职及外聘修改
            'External Update'=>array(
                'access'=>'EL02',
                'url'=>'/extUpdate/index',
            ),
		),
	),
    //審核模塊
	'Audit'=>array(
		'access'=>'ZG',
		'icon'=>'fa-legal',
		'items'=>array(
            //入職審核
            'Employee Audit'=>array(
                'access'=>'ZG01',
                'url'=>'/audit/index',
            ),
            //變更審核
			'Employee Update Audit'=>array(
				'access'=>'ZG02',
				'url'=>'/auditHistory/index',
			),
            //簽署合同
            'Audit sign contract'=>array(
                'access'=>'ZG08',
                'url'=>'/auditSign/index',
            ),
            //檢查員工舊合同
            'Review of old contracts'=>array(
                'access'=>'ZG09',
                'url'=>'/oldContract/index',
            ),
            //工資單審核
			'Wages Audit'=>array(
				'access'=>'ZG03',
				'url'=>'/auditWages/index',
			),
            //加班审核
            'audit for work overtime'=>array(
                'access'=>'ZG04',
                //'url'=>'/employer/index',
                'url'=>'/auditWork/index?only=3',
            ),
            //请假审核
            'audit for leave'=>array(
                'access'=>'ZG05',
                'url'=>'/auditLeave/index?only=3',
            ),
            //请假指定审核
            'appoint for leave'=>array(
                'access'=>'ZG11',
                'url'=>'/appointLeave/index',
            ),
            //加班指定审核
            'appoint for work'=>array(
                'access'=>'ZG12',
                'url'=>'/appointWork/index',
            ),
            //出差指定审核
            'appoint for trip'=>array(
                'access'=>'ZG13',
                'url'=>'/appointTrip/index',
            ),
            //出差审核
            'audit for trip'=>array(
                'access'=>'ZG10',
                'url'=>'/auditTrip/index',
            ),
            //奖金审核
            'Reward Audit'=>array(
                'access'=>'ZG06',
                'url'=>'/auditReward/index',
            ),
            //锦旗审核
            'Pennants Audit'=>array(
                'access'=>'ZG07',
                'url'=>'/auditPrize/index',
            ),
		),
	),
    //考核
    'review'=>array(
        'access'=>'RE',
        'icon'=>'fa-life-ring',
        'items'=>array(
            'Review Allot'=>array( //分配員工
                'access'=>'RE01',
                'url'=>'/ReviewAllot/index',
            ),
            'Review Handle'=>array( //優化人才評估
                'access'=>'RE02',
                'url'=>'/ReviewHandle/index',
            ),
            'Review Search'=>array( //查詢
                'access'=>'RE03',
                'url'=>'/ReviewSearch/index',
            ),
            'Review Set'=>array( //評估選項設置
                'access'=>'RE04',
                'url'=>'/ReviewSet/index',
            ),
            'Review Template'=>array( //分配模板
                'access'=>'RE05',
                'url'=>'/Template/index',
            ),
            'Review Template Employee'=>array( //員工綁定考核模板
                'access'=>'RE06',
                'url'=>'/TemplateEmployee/index',
            ),
            'Email Hint'=>array( //郵件提醒
                'access'=>'RE07',
                'url'=>'/Email/index',
            ),
        ),
    ),
    //老總年度考核
    'Boss Year Audit'=>array(
        'access'=>'BA',
        'icon'=>'fa-coffee',
        'items'=>array(
            //考核申請
            'Boss Apply'=>array(
                'access'=>'BA01',
                'url'=>'/bossApply/index',
            ),
            //考核查詢
            'Boss Search'=>array(
                'access'=>'BA02',
                'url'=>'/bossSearch/index',
            ),
            //考核審批（繞生）
            'Boss Audit(Joe)'=>array(
                'access'=>'BA06',
                'url'=>'/bossAudit/index?type=3',
            ),
            //考核審批（總監）
            'Boss Audit(director)'=>array(
                'access'=>'BA03',
                'url'=>'/bossAudit/index?type=1',
            ),
            //考核審批（副總監）
            'Boss Audit(Deputy director)'=>array(
                'access'=>'BA05',
                'url'=>'/bossAudit/index?type=2',
            ),
            //KPI設置
            'Setting KPI'=>array(
                'access'=>'BA04',
                'url'=>'/bossKPI/index',
            ),
            //老總年度考核A項設置
            'Setting Boss A'=>array(
                'access'=>'BA07',
                'url'=>'/bossSetA/index',
            ),
            //老總年度考核B項設置
            'Setting Boss B'=>array(
                'access'=>'BA08',
                'url'=>'/bossSetB/index',
            ),
        ),
    ),
    //銷售評分
    'Sales Review'=>array(
        'access'=>'SR',
        'icon'=>'fa-anchor',
        'items'=>array(
            'Sales Group'=>array( //銷售分組
                'access'=>'SR01',
                'url'=>'/SalesGroup/index',
            ),
            'Sales Review Search'=>array( //審核中央技术支持
                'access'=>'SR02',
                'url'=>'/SalesReview/index',
            ),
        ),
    ),
    //中央技术支持
    'technical support'=>array(
        'access'=>'AY',
        'icon'=>'fa-grav',
        'items'=>array(
            'Apply technical support'=>array( //申请中央技术支持
                'access'=>'AY01',
                'url'=>'/SupportApply/index',
            ),
            'Audit technical support'=>array( //審核中央技术支持
                'access'=>'AY02',
                'url'=>'/SupportAudit/index',
            ),
            'Search technical support'=>array( //查詢中央技术支持
                'access'=>'AY03',
                'url'=>'/SupportSearch/index',
            ),
            'Search support employee'=>array( //員工支援概況
                'access'=>'AY04',
                'url'=>'/SupportEmployee/index',
            ),
            'Email support employee'=>array( //員工支點分配
                'access'=>'AY05',
                'url'=>'/SupportEmail/index',
            ),
        ),
    ),
	'System Setting'=>array(//系统设置
		'access'=>'ZC',
		'icon'=>'fa-gear',
		'items'=>array(
			'Group Name'=>array(//员工分组设置
				'access'=>'ZC24',
				'url'=>'/groupName/index',
			),
			'Department'=>array(
				'access'=>'ZC01',
				//'url'=>'/employer/index',
				'url'=>'/dept/index',
			),
			'Leader'=>array(
				'access'=>'ZC02',
				'url'=>'/dept/index?type=1',
			),
			'Fete Config'=>array(
				'access'=>'ZC03',
				'url'=>'/fete/index',
			),
			'Vacation Type Config'=>array(
				'access'=>'ZC12',
				'url'=>'/vacationType/index',
			),
			'Vacation Config'=>array(
				'access'=>'ZC04',
				'url'=>'/vacation/index',
			),
			'cumulative annual leave'=>array(
				'access'=>'ZC07',
				'url'=>'/YearDay/index',
			),
			'employee plus city'=>array(
				'access'=>'ZC13',
				'url'=>'/plusCity/index',
			),
			'employee binding account'=>array(
				'access'=>'ZC05',
				'url'=>'/binding/index',
			),
			'Reward Config'=>array(
				'access'=>'ZC06',
				'url'=>'/rewardCon/index',
			),
/*			'Audit Config'=>array(
				'access'=>'ZC08',
				'url'=>'/AuditConfig/index',
			),*/
			'City index'=>array(
				'access'=>'ZC09',
				'url'=>'/city/index',
			),
            'audit for work overtime'=>array(
                'access'=>'ZC10',
                'url'=>'/auditWork/index?only=4',
            ),
            'audit for leave'=>array(
                'access'=>'ZC11',
                'url'=>'/auditLeave/index?only=4',
            ),
            'trip money set'=>array(//出差费用配置
                'access'=>'ZC16',
                'url'=>'/tripMoneySet/index',
            ),
            'trip result set'=>array(//出差結果配置
                'access'=>'ZC17',
                'url'=>'/tripResultSet/index',
            ),
            'appoint set'=>array(//加班、请假指定审核人
                'access'=>'ZC18',
                'url'=>'/appointSet/index',
            ),
            'appoint trip set'=>array(//出差指定审核人
                'access'=>'ZC19',
                'url'=>'/appointTripSet/index',
            ),
            'look manage set'=>array(//查看管理配置
                'access'=>'ZC22',
                'url'=>'/lookSet/index',
            ),
            'Config office'=>array(//办事处配置
                'access'=>'ZC15',
                'url'=>'/configOffice/index',
            ),
            'Bank Abbr set'=>array(//银行简称设置
                'access'=>'ZC21',
                'url'=>'/bankAbbrSet/index',
            ),
            'Config System'=>array(//開發者配置
                'access'=>'ZC14',
                'url'=>'/configSystem/index',
            ),
            'Curl Notes'=>array(//Curl同步记录
                'access'=>'ZC20',
                'url'=>'/curlNotes/index',
            ),
            'Curl Bs Notes'=>array(//北森同步记录
                'access'=>'ZC23',
                'url'=>'/curlBsNotes/index',
            ),
		),
	),
	'Report'=>array(
		'access'=>'YB',
		'icon'=>'fa-file-text-o',
		'items'=>array(
			'Staff Rpt List'=>array( //员工花名册
				'access'=>'YB04',
				'url'=>'/report/staffRpt',
				//'hidden'=>true,
			),
			'Staff Entry Rpt List'=>array( //新员工录入表
				'access'=>'YB14',
				'url'=>'/report/staffEnRpt',
				//'hidden'=>true,
			),
			'Overtime records List'=>array(
				'access'=>'YB02',
                'url'=>'/report/overtimelist',
			),
			'Work Compensatory List'=>array(//加班请假报表（未调休）
				'access'=>'YB16',
                'url'=>'/report/workCompensatory',
			),
			'Leave record List'=>array(
				'access'=>'YB03',
                'url'=>'/report/leavelist',
			),
			'report for trip'=>array( //出差報表
				'access'=>'YB11',
                'url'=>'/report/triplist',
			),
			'report for duty'=>array( //在職人員報表
				'access'=>'YB12',
                'url'=>'/report/staffDuty',
			),
			'report for update'=>array( //变更人員報表
				'access'=>'YB15',
                'url'=>'/report/staffUpdate',
			),
			'report for departure'=>array( //離職人員報表
				'access'=>'YB13',
                'url'=>'/report/staffDeparture',
			),
			'Pennants ex List'=>array(
				'access'=>'YB05',
                'url'=>'/report/pennantexlist',
			),
			'Pennants cumulative List'=>array(
				'access'=>'YB06',
                'url'=>'/report/pennantculist',
			),
			'Optimize assessment report'=>array(
				'access'=>'YB07',
                'url'=>'/report/reviewlist',
			),
			'Estimated statement report'=>array(//老总年度考核预计数据报表
				'access'=>'YB08',
                'url'=>'/report/estimated',
			),
            'Boss Audit Plan Report'=>array(//老总年度考核进度报表
                'access'=>'YB10',
                'url'=>'/report/bossReport',
            ),
            'Pin Report'=>array(
                'access'=>'YB09',
                'url'=>'/report/pinReport',
            ),
			'Report Manager'=>array(
				'access'=>'YB01',
                'url'=>'/queue/index',
			),
		),
	),
);
