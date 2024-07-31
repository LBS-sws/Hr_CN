<?php
class RptPinReport extends CReport {
	protected function fields() {
		return array(
			'pin_code'=>array('label'=>Yii::t('contract','Pin Code'),'width'=>20,'align'=>'L'),
			'apply_date'=>array('label'=>Yii::t('contract','Pin Date'),'width'=>15,'align'=>'L'),
			'employee_id'=>array('label'=>Yii::t('contract','Employee Name'),'width'=>25,'align'=>'C'),
			'city'=>array('label'=>Yii::t('contract','City'),'width'=>20,'align'=>'L'),
			'position'=>array('label'=>Yii::t('contract','Leader'),'width'=>20,'align'=>'L'),
			'entry_time'=>array('label'=>Yii::t('contract','Entry Time'),'width'=>25,'align'=>'L'),
			'class_id'=>array('label'=>Yii::t('app','Pin Class'),'width'=>25,'align'=>'L'),
			'name_id'=>array('label'=>Yii::t('app','Pin Name'),'width'=>25,'align'=>'L'),
			'pin_num'=>array('label'=>Yii::t('contract','Pin Num'),'width'=>20,'align'=>'L'),
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
        $sql = "select j.name as class_name,b.entry_time,a.pin_code,p.name as dept_name,a.id,a.apply_date,a.pin_num,b.code,b.name,b.position,g.name as pin_name,h.name as city_name 
                from hr_pin a 
                LEFT JOIN hr_employee b ON a.employee_id=b.id
                LEFT JOIN hr_dept p ON b.position=p.id
                LEFT JOIN hr_pin_inventory f ON a.inventory_id=f.id
                LEFT JOIN hr_pin_name g ON f.pin_name_id = g.id
                LEFT JOIN hr_pin_class j ON j.id = g.class_id
                LEFT JOIN security$suffix.sec_city h ON a.city=h.code
                where a.city in ($citylist) and date_format(a.apply_date,'%Y/%m/%d') >= '$start_dt'
                $cond_staff
				order by b.city desc, a.apply_date
			";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
				$temp = array();
				$temp['pin_code'] = $row['pin_code'];
				$temp['apply_date'] = $row['apply_date'];
				$temp['employee_id'] = $row['name'];
				$temp['city'] = $row['city_name'];
				$temp['position'] = $row['dept_name'];
				$temp['entry_time'] = $row['entry_time'];
				$temp['class_id'] = $row['class_name'];
				$temp['name_id'] = $row['pin_name'];
				$temp['pin_num'] = $row['pin_num'];
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