<?php
class RptStaffEnList extends CReport {//新员工录入表

	protected function fields() {
		return array(
			'code'=>array('label'=>Yii::t('report','Staff No.'),'width'=>15,'align'=>'L'),//员工编号
			'name'=>array('label'=>Yii::t('report','Name'),'width'=>25,'align'=>'L'),//姓名
            'user_card'=>array('label'=>Yii::t('report','ID No.'),'width'=>20,'align'=>'L'),//公民身份号码
            'table_type'=>array('label'=>Yii::t('report','Staff Type'),'width'=>20,'align'=>'L'),//员工类型
            'staff_status'=>array('label'=>Yii::t('report','Staff Status'),'width'=>20,'align'=>'L'),//员工状态
            'entry_time'=>array('label'=>Yii::t('report','Entry Time'),'width'=>20,'align'=>'L'),//入职日期
			'department'=>array('label'=>Yii::t('report','Department'),'width'=>25,'align'=>'L'),//部门
            'city_name'=>array('label'=>Yii::t('report','City Name'),'width'=>25,'align'=>'L'),//城市
            'work_area'=>array('label'=>Yii::t('contract','work area'),'width'=>25,'align'=>'L'),//主要工作地点
			'position'=>array('label'=>Yii::t('report','Position'),'width'=>25,'align'=>'L'),//职位
            'test_type'=>array('label'=>Yii::t('report','Probation Type'),'width'=>25,'align'=>'L'),//试用期类型
            'test_length'=>array('label'=>Yii::t('report','Probation Time Longer'),'width'=>25,'align'=>'L'),//试用期时期
            'test_end_time'=>array('label'=>Yii::t('report','Probation End Time'),'width'=>25,'align'=>'L'),//试用期结束时间
            'test_wage'=>array('label'=>Yii::t('report','Probation Wage'),'width'=>15,'align'=>'R'),//试用期工资
            'wage'=>array('label'=>Yii::t('report','Contract Pay'),'width'=>15,'align'=>'R'),//合同工资
            'staff_id'=>array('label'=>Yii::t('report','Employee Belong'),'width'=>25,'align'=>'L'),//员工归属
            'company_id'=>array('label'=>Yii::t('report','Employee Contract Belong'),'width'=>25,'align'=>'L'),//员工合同归属
            'contract_id'=>array('label'=>Yii::t('report','Employee Contract'),'width'=>25,'align'=>'L'),//员工合同模版
            'fix_time'=>array('label'=>Yii::t('report','contract deadline'),'width'=>25,'align'=>'L'),//合同期限
            'start_dt'=>array('label'=>Yii::t('report','Contract Start Date'),'width'=>25,'align'=>'L'),//劳动合同起始日期
            'end_dt'=>array('label'=>Yii::t('report','Contract End Date'),'width'=>25,'align'=>'L'),//劳动合同终止日期
            'remarks'=>array('label'=>Yii::t('report','Remarks'),'width'=>40,'align'=>'L'),//备注
            'phone'=>array('label'=>Yii::t('report','Contact Type'),'width'=>20,'align'=>'L'),//联系方式（如手机号）
            'household'=>array('label'=>Yii::t('report','Household type'),'width'=>20,'align'=>'L'),//户籍类型
            'bank_type'=>array('label'=>Yii::t('contract','Bank Abbr Name'),'width'=>20,'align'=>'L'),//银行简称
            'bank_number'=>array('label'=>Yii::t('contract','Bank card'),'width'=>20,'align'=>'L'),//银行卡号
		);
	}


    public function genReport() {
        $this->retrieveData();
        $this->title = $this->getReportName();
        $this->subtitle = Yii::t('report','Date').':'.$this->criteria['SEARCHS'].' - '.$this->criteria['SEARCHE']
        ;
        if (isset($this->criteria['CITY'])&&!empty($this->criteria['CITY'])) {
            $this->subtitle.= empty($this->subtitle)?"":" ；";
            $this->subtitle.= Yii::t('report','City').': ';
            $this->subtitle.= General::getCityNameForList($this->criteria['CITY']);
        }
        return $this->exportExcel();
    }

    public function retrieveData() {
        $start_dt = $this->criteria['SEARCHS']."/01";
        $end_dt = $this->criteria['SEARCHE']."/01";
        $start_dt = date("Y/m/01",strtotime($start_dt));
        $end_dt = date("Y/m/t",strtotime($end_dt));
        $city = $this->criteria['CITY'];
        $suffix = Yii::app()->params['envSuffix'];

        if(!General::isJSON($city)){
            $city_allow = strpos($city,"'")!==false?$city:"'{$city}'";
        }else{
            $city_allow = json_decode($city,true);
            $city_allow = "'".implode("','",$city_allow)."'";
        }

        $sql_date=" (
        (a.staff_status not in (0,-1) and a.table_type=1) or 
        (a.staff_status not in (1,-1) and a.table_type!=1)
        ) 
         and date_format(a.entry_time,'%Y/%m/%d') BETWEEN '{$start_dt}' and '{$end_dt}'
         ";

        $localOffice = Yii::t("contract","local office");
        $bankTypeList = StaffFun::getBankTypeList();
        $sql = "select a.*,
                p.name as position_name,
                d.name as department_name,
                b.name as city_name,
                if(a.office_id=0,'{$localOffice}',f.name) as office_name
                from hr_employee a 
                LEFT JOIN hr_dept p ON a.position = p.id
                LEFT JOIN hr_dept d ON a.department = d.id
                LEFT JOIN hr_office f ON f.id=a.office_id
                LEFT JOIN security{$suffix}.sec_city b ON a.city = b.code
                where a.city in({$city_allow}) and {$sql_date} 
				order by a.city asc,a.table_type asc,a.staff_status desc,a.entry_time asc
			";
        $rows = Yii::app()->db->createCommand($sql)->queryAll();
        if (count($rows) > 0) {
            foreach ($rows as $row) {
                $temp = array();
                $temp['code'] = $row['code'];
                $temp['name'] = $row['name'];
                $temp['user_card'] = " ".$row['user_card'];
                $temp['table_type'] = StaffFun::getTableTypeNameForID($row['table_type']);
                $temp['staff_status'] = $this->getStaffEnStatus($row['staff_status']);
                $temp['entry_time'] = $row['entry_time'];
                $temp['city_name'] = $row['city_name'];
                $temp['work_area'] = $row['work_area'];
                $temp['department'] = $row['department_name'];
                $temp['position'] = $row['position_name'];
                $temp['office_name'] = $row['office_name'];
                $temp['staff_type'] = StaffFun::getStaffTypeNameForID($row['staff_type']);
                $temp['staff_leader'] = StaffFun::getStaffLeaderNameForID($row['staff_leader']);
                $temp['staff_id'] = StaffFun::getCompanyNameToID($row['staff_id']);
                $temp['company_id'] = StaffFun::getCompanyNameToID($row['company_id']);
                $temp['contract_id'] = StaffFun::getContractNameToID($row['contract_id']);
                $temp['fix_time'] = StaffFun::getFixTimeNameForID($row['fix_time']);
                $temp['start_dt'] = $row['start_time'];
                $temp['end_dt'] = $row['fix_time']=="nofixed"?"":$row['end_time'];
                $temp['test_length']="";
                $temp['test_end_time']="";
                $temp['test_wage']="";
                if($row['test_type']=="1"){//有试用期
                    $temp['test_length'] = StaffFun::getTestMonthLengthNameForID($row['test_length']);
                    $temp['test_end_time'] = $row['test_end_time'];
                    $temp['test_wage'] = " ".$row['test_wage'];
                }
                $temp['test_type'] = StaffFun::getTestTypeNameForID($row['test_type']);
                $temp['wage'] = " ".$row['wage'];
                $temp['email'] = $row['email'];
                $temp['phone'] = " ".$row['phone'];
                $temp['user_card_date'] = $row['user_card_date'];
                $temp['dob'] = $row['birth_time'];
                $temp['gender'] = StaffFun::getAgeForBirthDate($row['birth_time']);
                $temp['sex'] = StaffFun::getSexNameForID($row['sex']);
                $temp['education'] = StaffFun::getEducationNameForID($row['education']);
                $temp['household'] = StaffFun::getNationNameForID($row['household']);
                $temp['address'] = $row['address'];
                $temp['contact_address'] = $row['contact_address'];
                $temp['social_code'] = $row['social_code'];
                $temp['emergency_user'] = $row['emergency_user'];
                $temp['emergency_phone'] = " ".$row['emergency_phone'];
                $temp['bank_type'] = "".$row['bank_type'];
                $temp['bank_type'] = key_exists($row['bank_type'],$bankTypeList)?$bankTypeList[$row['bank_type']]:"";
                $temp['bank_number'] = " ".$row['bank_number'];
                $temp['change_dt'] = "";
                $temp['reason'] = "";
                $temp['remarks'] = $row['remark'];
                $this->data[] = $temp;
            }
        }
        return true;
    }

    private function getStaffEnStatus($status){
	    $list = array(
	        1=>Yii::t("contract","Draft"),//草稿
	        2=>Yii::t("contract","Sent, pending approval"),//已發送，等待審核
	        3=>Yii::t("contract","Rejected"),//拒絕
	        4=>Yii::t("contract","Wait for social security"),//等待社保
            9=>Yii::t("contract","Draft"),//草稿
        );
	    if(key_exists($status,$list)){
	        return $list[$status];
        }
        return $status;
    }

    public function getReportName() {
        //$city_name = isset($this->criteria) ? ' - '.General::getCityName($this->criteria['CITY']) : '';
        return (isset($this->criteria) ? Yii::t('report',$this->criteria['RPT_NAME']) : Yii::t('report','Nil'));
    }
}
?>