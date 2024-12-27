<?php
class RptWorkCompensatory extends CReport {
	protected function fields() {
		return array(
			'work_code'=>array('label'=>Yii::t('fete','Work Code'),'width'=>15,'align'=>'L'),
			'employee_code'=>array('label'=>Yii::t('contract','Employee Code'),'width'=>22,'align'=>'L'),
			'employee_name'=>array('label'=>Yii::t('contract','Employee Name'),'width'=>30,'align'=>'L'),
			'work_type'=>array('label'=>Yii::t('fete','Work Type'),'width'=>25,'align'=>'C'),
            'leave_code'=>array('label'=>Yii::t('fete','Leave Code'),'width'=>15,'align'=>'L'),
            'start_time'=>array('label'=>Yii::t('contract','Start Time'),'width'=>20,'align'=>'L'),
			'end_time'=>array('label'=>Yii::t('contract','End Time'),'width'=>20,'align'=>'L'),
			'log_time'=>array('label'=>Yii::t('fete','Log Date')."(小时)",'width'=>15,'align'=>'L'),
            'com_log_time'=>array('label'=>"计算后时间(小时)",'width'=>30,'align'=>'L'),
			'lcd'=>array('label'=>Yii::t('fete','apply for time'),'width'=>15,'align'=>'L'),
			'work_cause'=>array('label'=>Yii::t('fete','Work Cause'),'width'=>30,'align'=>'L'),
			'work_address'=>array('label'=>Yii::t('fete','Work Address'),'width'=>30,'align'=>'L'),
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
		$city = $this->criteria['CITY'];

        if(!General::isJSON($city)){
            $citylist = strpos($city,"'")!==false?$city:"'{$city}'";
        }else{
            $citylist = json_decode($city,true);
            $citylist = "'".implode("','",$citylist)."'";
        }
		
		$suffix = Yii::app()->params['envSuffix'];
        $sql = "select a.*,b.name AS employee_name,b.code AS employee_code,b.city AS s_city 
                from hr_employee_work a 
                LEFT JOIN hr_employee b ON a.employee_id = b.id
                LEFT JOIN hr_work_leave g ON a.id = g.work_id
                where b.city in($citylist) and a.work_type=4 and a.status=4 AND g.leave_id is NULL 
				order by b.city desc,b.lcd desc, a.id
			";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
                $row["com_log_time"] = $row['log_time'];
			    if($row['log_time']>8){
                    self::resetWorkDate($row);
                }
                $temp = array();
                $temp['start_time'] = date("Y/m/d H:i:s",strtotime($row['start_time']));
                $temp['end_time'] = date("Y/m/d H:i:s",strtotime($row['end_time']));
				$temp['work_address'] = $row['work_address'];
				$temp['work_cause'] = $row['work_cause'];
				$temp['work_code'] = $row['work_code'];
				$temp['leave_code'] = LeaveList::getCodeForWorkLeave($row["id"],"work",false);
				$temp['employee_code'] = $row['employee_code'];
				$temp['employee_name'] = $row['employee_name'];
				$temp['work_type'] = "加班调休";
				$temp['log_time'] = $row['log_time'];
				$temp['com_log_time'] = $row['com_log_time'];
                $temp['lcd'] = $row['lcd'];
				$this->data[] = $temp;
			}
		}
		return true;
	}

    public static function resetWorkDate(&$record){
	    $com_log_time = 0;
        if(date("Y/m/d",strtotime($record['end_time']))==date("Y/m/d",strtotime($record['start_time']))){
            //同一天
            $logTime = strtotime($record['end_time'])-strtotime($record['start_time']);
            $logTime = $logTime/(60*60);
            $logTime = $logTime>8?8:round($logTime,1);//一天最多加班8小时
            $com_log_time+=$logTime;
        }
        $rows = Yii::app()->db->createCommand()->select("*")->from("hr_employee_word_info")
            ->where('work_id=:work_id',array(':work_id'=>$record["id"]))->queryAll();
        if($rows){
            foreach ($rows as $row){
                if(date("Y/m/d",strtotime($row['start_time']))==date("Y/m/d",strtotime($row['end_time']))){
                    //同一天
                    $logTime = strtotime($row['end_time'])-strtotime($row['start_time']);
                    $logTime = $logTime/(60*60);
                    $logTime = $logTime>8?8:round($logTime,1);//一天最多加班8小时
                    $com_log_time+=$logTime;
                }
            }
        }
        $record['com_log_time'] = $com_log_time;
    }
	
	public function getReportName() {
		//$city_name = isset($this->criteria) ? ' - '.General::getCityName($this->criteria['CITY']) : '';
		return (isset($this->criteria) ? Yii::t('report',$this->criteria['RPT_NAME']) : Yii::t('report','Nil'));
	}
}
?>