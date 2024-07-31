<?php
class RptTripList extends CReport {
    protected function fields() {
        return array(
            'status'=>array('label'=>Yii::t('contract','Status'),'width'=>25,'align'=>'L'),
            'lcd'=>array('label'=>Yii::t('fete','apply for time'),'width'=>25,'align'=>'L'),
            'trip_code'=>array('label'=>Yii::t('fete','trip code'),'width'=>15,'align'=>'L'),
            'employee_code'=>array('label'=>Yii::t('contract','Employee Code'),'width'=>22,'align'=>'L'),
            'employee_name'=>array('label'=>Yii::t('contract','Employee Name'),'width'=>30,'align'=>'L'),
            'city_name'=>array('label'=>Yii::t('contract','City'),'width'=>25,'align'=>'C'),
            'start_time'=>array('label'=>Yii::t('contract','Start Time'),'width'=>20,'align'=>'L'),
            'end_time'=>array('label'=>Yii::t('contract','End Time'),'width'=>20,'align'=>'L'),
            'trip_address'=>array('label'=>Yii::t('fete','trip address'),'width'=>15,'align'=>'L'),
            'trip_cost'=>array('label'=>Yii::t('fete','trip cost'),'width'=>15,'align'=>'R'),
            'trip_cause'=>array('label'=>Yii::t('fete','trip cause'),'width'=>30,'align'=>'L'),
            'result_id'=>array('label'=>Yii::t('fete','trip result'),'width'=>30,'align'=>'L'),
            'result_text'=>array('label'=>Yii::t('fete','trip result text'),'width'=>30,'align'=>'L'),
            'start_time_info'=>array('label'=>Yii::t('contract','Start Time'),'width'=>20,'align'=>'L'),
            'end_time_info'=>array('label'=>Yii::t('contract','End Time'),'width'=>20,'align'=>'L'),
        );
    }
    public function report_structure() {
        return array(
            'status',
            'lcd',
            'trip_code',
            'employee_code',
            'employee_name',
            'city_name',
            'start_time',
            'end_time',
            'trip_address',
            'trip_cost',
            'trip_cause',
            'result_id',
            'result_text',
            array(
                'start_time_info',
                'end_time_info',
            )
        );
    }

    public function genReport() {
        $this->retrieveData();
        $this->title = $this->getReportName();
        $this->subtitle = Yii::t('report','Date').':'.$this->criteria['START_DT'].' - '.$this->criteria['END_DT'].' / '
            .Yii::t('report','Staffs').':'.$this->criteria['STAFFSDESC']
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
        $start_dt = date("Y-m-d 00:00:00",strtotime($start_dt));
        $end_dt = date("Y-m-d 23:59:59",strtotime($end_dt));
        $city = $this->criteria['CITY'];
        $staff_id = $this->criteria['STAFFS'];

        if(!General::isJSON($city)){
            $citylist = strpos($city,"'")!==false?$city:"'{$city}'";
        }else{
            $citylist = json_decode($city,true);
            $citylist = "'".implode("','",$citylist)."'";
        }

        $suffix = Yii::app()->params['envSuffix'];

        $cond_staff = '';
        if (!empty($staff_id)) {
            $ids = explode('~',$staff_id);
            if(count($ids)>1){
                $cond_staff = implode(",",$ids);
            }else{
                $cond_staff = $staff_id;
            }
            if ($cond_staff!=''){
                $cond_staff = " and a.employee_id in ($cond_staff)";
            }
        }
        $sql = "select a.*,f.name as city_name,b.name AS employee_name,b.code AS employee_code,b.city AS s_city,
                g.pro_name,g.pro_num
                from hr_employee_trip a 
                LEFT JOIN hr_employee b ON a.employee_id = b.id
                LEFT JOIN hr_trip_result_set g ON a.result_id = g.id
                LEFT JOIN security{$suffix}.sec_city f ON b.city=f.code
                where b.city in($citylist) and a.status in (2,4,5) and a.start_time >= '$start_dt' and a.start_time <= '$end_dt' 
                $cond_staff
				order by a.employee_id,a.lcd desc
			";
        $rows = Yii::app()->db->createCommand($sql)->queryAll();
        if (count($rows) > 0) {
            foreach ($rows as $row) {
                $status = self::getStatusDesc($row);
                $temp = array();
                $temp['status'] = $status;
                $temp['trip_address'] = $row['trip_address'];
                $temp['trip_cost'] = $row['trip_cost'];
                $temp['trip_cause'] = $row['trip_cause'];
                $temp['trip_code'] = $row['trip_code'];
                $temp['employee_code'] = $row['employee_code'];
                $temp['employee_name'] = $row['employee_name'];
                $temp['lcd'] = $row['lcd'];
                $temp['city_name'] = $row['city_name'];
                $temp['result_id'] = empty($row['result_id'])?"":"（{$row['pro_num']}%）{$row['pro_name']}";
                $temp['result_text'] = $row['result_text'];
                $temp['start_time'] = CGeneral::toDate($row['start_time']);
                $temp['end_time'] = CGeneral::toDate($row['end_time']);
                $detail=array();
                $sql = "select * from hr_employee_trip_info where trip_id=".$row["id"];
                $detailRows = Yii::app()->db->createCommand($sql)->queryAll();
                if($detailRows){
                    foreach ($detailRows as $detailRow){
                        $detailRow['start_time'] = CGeneral::toDate($detailRow['start_time']);
                        $detailRow['start_time'].=" (".LeaveForm::getAMPMList($detailRow['start_time_lg'],true).")";
                        $detailRow['end_time'] = CGeneral::toDate($detailRow['end_time']);
                        $detailRow['end_time'].=" (".LeaveForm::getAMPMList($detailRow['end_time_lg'],true).")";
                        $detail[]=array(
                            "start_time_info"=>$detailRow["start_time"],
                            "end_time_info"=>$detailRow["end_time"]
                        );
                    }
                }
                $temp["detail"] = $detail;
                $this->data[] = $temp;
            }
        }
        return true;
    }

    private function getStatusDesc($row){
        $status=$row["status"];
        switch ($row["status"]){
            case 2:
                $status = "已审核，等待出差结果";
                break;
            case 4:
                $status = "已完成";
                break;
            case 5:
                $status = "已取消";
                break;
        }
        return $status;
    }

    public function getReportName() {
        //$city_name = isset($this->criteria) ? ' - '.General::getCityName($this->criteria['CITY']) : '';
        return (isset($this->criteria) ? Yii::t('report',$this->criteria['RPT_NAME']) : Yii::t('report','Nil'));
    }
}
?>