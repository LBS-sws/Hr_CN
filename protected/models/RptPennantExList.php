<?php
class RptPennantExList extends CReport {
	protected function fields() {
		return array(
			'city'=>array('label'=>Yii::t('contract','City'),'width'=>20,'align'=>'L'),
			'level'=>array('label'=>Yii::t('fete','level'),'width'=>15,'align'=>'L'),
			'prize_num'=>array('label'=>Yii::t('fete','prize num'),'width'=>25,'align'=>'C'),
			'employee_id'=>array('label'=>Yii::t('report','Name'),'width'=>20,'align'=>'L'),
			'position'=>array('label'=>Yii::t('contract','Leader'),'width'=>20,'align'=>'L'),
			'entry_time'=>array('label'=>Yii::t('contract','Entry Time'),'width'=>25,'align'=>'L'),
			'prize_date'=>array('label'=>Yii::t('fete','prize date'),'width'=>25,'align'=>'L'),
			'customer_name'=>array('label'=>Yii::t('fete','customer name'),'width'=>25,'align'=>'L'),
			'prize_pro'=>array('label'=>Yii::t('fete','prize pro'),'width'=>25,'align'=>'L'),
			'prize_type'=>array('label'=>Yii::t('fete','prize')."/".Yii::t('fete','testimonial'),'width'=>30,'align'=>'L'),
            'type_num'=>array('label'=>Yii::t('fete','type number'),'width'=>25,'align'=>'L'),
            'lcd'=>array('label'=>Yii::t('queue','Req. Date'),'width'=>15,'align'=>'L'),
            'lud'=>array('label'=>Yii::t('queue','Comp. Date'),'width'=>15,'align'=>'L'),
		);
	}
	
	public function genReport() {
		$this->retrieveData();
		$this->title = $this->getReportName();
		$this->subtitle = Yii::t('report','Year').':'.$this->criteria['START_DT'].' ~ '.$this->criteria['END_DT'].' / '
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
        $start_dt = date("Y/m/d",strtotime($this->criteria['START_DT']));
        $end_dt = $this->criteria['END_DT'];
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
		if(!empty($end_dt)){
            $end_dt = date("Y/m/d",strtotime($end_dt));
            $cond_staff.=" and date_format(a.lud,'%Y/%m/%d') <= '$end_dt' ";
        }
        $sql = "select a.*,b.name AS employee_name,b.entry_time,b.position,b.city AS s_city 
                from hr_prize a 
                LEFT JOIN hr_employee b ON a.employee_id = b.id
                where b.city in($citylist) and a.status=3 and date_format(a.lud,'%Y/%m/%d') >= '$start_dt' AND a.id is NOT NULL 
                $cond_staff
				order by b.city desc, a.lcd
			";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
		    $prizeList = PrizeList::getPrizeList();
		    $prizeTypeList = array(Yii::t('fete','testimonial'),Yii::t('fete','prize'));
			foreach ($rows as $row) {
				$temp = array();
				$temp['city'] = CGeneral::getCityName($row['s_city']);
				$temp['level'] = CityList::getLevelToCity($row['s_city']);
				$temp['prize_num'] = $row['prize_num'];
				$temp['employee_id'] = $row['employee_name'];
				$temp['position'] = DeptForm::getDeptToId($row['position']);
                $temp['prize_date'] = date("Y/m/d",strtotime($row['prize_date']));
                $temp['entry_time'] = date("Y/m/d",strtotime($row['entry_time']));
                $temp['prize_pro'] = $prizeList[$row['prize_pro']];
                $temp['prize_type'] = $prizeTypeList[$row['prize_type']];
                $temp['type_num'] = $row['type_num'];
                $temp['customer_name'] = $this->getCustomerNameToId($row['customer_name']);
                $temp['lcd'] = date("Y/m/d",strtotime($row['lcd']));
                $temp['lud'] = date("Y/m/d",strtotime($row['lud']));
				$this->data[] = $temp;
			}
		}
		return true;
	}

    //獲取客戶列表
    public function getCustomerNameToId($id){
        $suffix = Yii::app()->params['envSuffix'];
        $sql = "select name from swoper$suffix.swo_company WHERE id ='$id'";
        $rows = Yii::app()->db->createCommand($sql)->queryRow();
        if($rows){
            return $rows["name"];
        }
        return $id;
    }
	
	public function getReportName() {
		//$city_name = isset($this->criteria) ? ' - '.General::getCityName($this->criteria['CITY']) : '';
		return (isset($this->criteria) ? Yii::t('report',$this->criteria['RPT_NAME']) : Yii::t('report','Nil'));
	}
}
?>