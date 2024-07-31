<?php
class RptStaffDeparture extends CReport {
    protected function fields() {
        return array(
            'employee_code'=>array('label'=>Yii::t('contract','Employee Code'),'width'=>12,'align'=>'L'),
            'employee_name'=>array('label'=>Yii::t('contract','Employee Name'),'width'=>12,'align'=>'L'),
            'city_name'=>array('label'=>Yii::t('contract','City'),'width'=>15,'align'=>'C'),
            'user_card'=>array('label'=>Yii::t('contract','ID Card'),'width'=>25,'align'=>'R'),
            'company_name'=>array('label'=>Yii::t('contract','Affiliated company'),'width'=>20,'align'=>'L'),
            'department_name'=>array('label'=>Yii::t('contract','Department'),'width'=>15,'align'=>'L'),
            'position_name'=>array('label'=>Yii::t('contract','Position'),'width'=>15,'align'=>'L'),
            'entry_time'=>array('label'=>Yii::t('contract','Entry Time'),'width'=>20,'align'=>'L'),
            'leave_time'=>array('label'=>Yii::t('contract','Leave Date'),'width'=>20,'align'=>'L'),
            'leave_reason'=>array('label'=>Yii::t('contract','Leave Reason'),'width'=>30,'align'=>'L'),
        );
    }

    public function genReport() {
        $this->retrieveData();
        $this->title = $this->getReportName();
        $this->subtitle = Yii::t('report','Date').':'.$this->criteria['START_DT'].' - '.$this->criteria['END_DT']
        ;
        if (isset($this->criteria['CITY'])&&!empty($this->criteria['CITY'])) {
            $this->subtitle.= empty($this->subtitle)?"":" ；";
            $this->subtitle.= Yii::t('report','City').': ';
            $this->subtitle.= General::getCityNameForList($this->criteria['CITY']);
        }
        return $this->exportExcel();
    }

    public function retrieveData() {
        $start_dt = $this->criteria['START_DT'];
        $end_dt = $this->criteria['END_DT'];
        $start_dt = date("Y/m/d",strtotime($start_dt));
        $end_dt = date("Y/m/d",strtotime($end_dt));
        $city = $this->criteria['CITY'];

        if(!General::isJSON($city)){
            $citylist = strpos($city,"'")!==false?$city:"'{$city}'";
        }else{
            $citylist = json_decode($city,true);
            $citylist = "'".implode("','",$citylist)."'";
        }

        $suffix = Yii::app()->params['envSuffix'];
        $sql = "select a.code,a.name,a.entry_time,a.leave_time,a.leave_reason,a.user_card,f.name as city_name,
                b.name as company_name,d.name as department_name,p.name as position_name
                from hr_employee a 
                LEFT JOIN hr_company b ON a.staff_id=b.id
                LEFT JOIN security{$suffix}.sec_city f ON a.city=f.code
                LEFT JOIN hr_dept d ON a.department=d.id
                LEFT JOIN hr_dept p ON a.position=p.id
                where a.city in($citylist) and a.staff_status=-1 and replace(a.leave_time,'-', '/') BETWEEN '{$start_dt}' and '{$end_dt}'
				order by a.leave_time desc
			";
        $rows = Yii::app()->db->createCommand($sql)->queryAll();
        if (count($rows) > 0) {
            foreach ($rows as $row) {
                $temp = array();
                $temp['employee_code'] = $row['code'];
                $temp['employee_name'] = $row['name'];
                $temp['user_card'] = " ".$row['user_card'];
                $temp['city_name'] = $row['city_name'];
                $temp['company_name'] = $row['company_name'];
                $temp['position_name'] = $row['position_name'];
                $temp['department_name'] = $row['department_name'];
                $temp['leave_reason'] = $row['leave_reason'];

                $temp['entry_time'] = CGeneral::toDate($row['entry_time']);
                $temp['leave_time'] = CGeneral::toDate($row['leave_time']);
                $this->data[] = $temp;
            }
        }
        return true;
    }

    public function getReportName() {
        //$city_name = isset($this->criteria) ? ' - '.General::getCityName($this->criteria['CITY']) : '';
        return (isset($this->criteria) ? Yii::t('report',$this->criteria['RPT_NAME']) : Yii::t('report','Nil'));
    }
}
?>