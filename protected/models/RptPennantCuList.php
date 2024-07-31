<?php
class RptPennantCuList extends CReport {
	protected function fields() {
		return array(
			'city'=>array('label'=>Yii::t('contract','City'),'width'=>20,'align'=>'L'),
            'employee_id'=>array('label'=>Yii::t('report','Name'),'width'=>20,'align'=>'L'),
            'cleaner'=>array('label'=>Yii::t('fete','cleaner'),'width'=>20,'align'=>'L'),
            'exterminators'=>array('label'=>Yii::t('fete','exterminators'),'width'=>20,'align'=>'L'),
            'cae'=>array('label'=>Yii::t('fete','cleaner and exterminators'),'width'=>30,'align'=>'L'),
            'subtotal'=>array('label'=>Yii::t('fete','Personal subtotal'),'width'=>30,'align'=>'L'),
            'total'=>array('label'=>Yii::t('fete','total'),'width'=>20,'align'=>'L'),
            'ranking'=>array('label'=>Yii::t('fete','ranking'),'width'=>30,'align'=>'L'),
		);
	}
    public function report_structure() {
        return array(
            'city',
            array(
                'employee_id',
                'cleaner',
                'exterminators',
                'cae',
                'subtotal',
            ),
            'total',
            'ranking',
        );
    }
	
	public function genReport() {
		$this->retrieveData();
		$this->title = $this->getReportName();
		$this->subtitle = Yii::t('report','Year').':'.$this->criteria['YEAR'].' - '.$this->criteria['MONTH'].' / '
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
        $year = $this->criteria['YEAR'];
        $month = intval($this->criteria['MONTH']);
		$city = $this->criteria['CITY'];
		$staff_id = $this->criteria['STAFFS'];

        if(!General::isJSON($city)){
            $citylist = strpos($city,"'")!==false?$city:"'{$city}'";
        }else{
            $citylist = json_decode($city,true);
            $citylist = "'".implode("','",$citylist)."'";
        }
		
		$suffix = Yii::app()->params['envSuffix'];
        $mPrice = $month<10?"0":"";
        $start_dt = $year."-01-01 00:00:00";
        $end_dt = $year."-$mPrice".$month."-31 59:59:59";
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
        $sql = "select a.*,b.name AS employee_name,b.entry_time,b.position,b.city AS s_city 
                from hr_prize a 
                LEFT JOIN hr_employee b ON a.employee_id = b.id
                where b.city in($citylist) and a.status=3 and a.lcd >= '$start_dt' and a.lcd <= '$end_dt' AND a.id is NOT NULL 
                $cond_staff
				order by b.city desc, a.lcd
			";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
		    $arrList = array();
		    $prizeList = array(
                1=>"cleaner", //清潔
                2=>"exterminators", //滅蟲
                3=>"cae", //清潔滅蟲
            );
		    $prizeTypeList = array(Yii::t('fete','testimonial'),Yii::t('fete','prize'));
			foreach ($rows as $row) {
			    $key = $prizeList[$row["prize_pro"]];
                $key = empty($key)?"cleaner":$key;
			    if(!array_key_exists($row["s_city"],$arrList)){
                    $arrList[$row["s_city"]]["city"] = CGeneral::getCityName($row['s_city']);
                    $arrList[$row["s_city"]]["total"] = intval($row['type_num']);
                    $arrList[$row["s_city"]]["detail"] = array();
                }else{
                    $arrList[$row["s_city"]]["total"] += intval($row['type_num']);
                }
			    if(!array_key_exists($row["employee_id"],$arrList[$row["s_city"]]["detail"])){
                    $arrList[$row["s_city"]]["detail"][$row["employee_id"]]["employee_id"] = $row["employee_name"];
                    $arrList[$row["s_city"]]["detail"][$row["employee_id"]]["cleaner"] = 0;
                    $arrList[$row["s_city"]]["detail"][$row["employee_id"]]["exterminators"] = 0;
                    $arrList[$row["s_city"]]["detail"][$row["employee_id"]]["cae"] = 0;
                    $arrList[$row["s_city"]]["detail"][$row["employee_id"]][$key] = intval($row['type_num']);
                    $arrList[$row["s_city"]]["detail"][$row["employee_id"]]["subtotal"] = intval($row['type_num']);
                }else{
                    $arrList[$row["s_city"]]["detail"][$row["employee_id"]][$key] += intval($row['type_num']);
                    $arrList[$row["s_city"]]["detail"][$row["employee_id"]]["subtotal"] += intval($row['type_num']);
                }
			}

			foreach ($arrList as &$preList){
                foreach ($arrList as &$nextList){
                    if(intval($preList["total"])>intval($nextList["total"])){
                        $temp = $preList;
                        $preList = $nextList;
                        $nextList = $temp;
                    }
                }
            }
            $key = 0;
			foreach ($arrList as &$list){
                $key++;
                $list["ranking"] ="第".$key."名";
            }
            $this->data = $arrList;
		}
		return true;
	}
	
	public function getReportName() {
		//$city_name = isset($this->criteria) ? ' - '.General::getCityName($this->criteria['CITY']) : '';
		return (isset($this->criteria) ? Yii::t('report',$this->criteria['RPT_NAME']) : Yii::t('report','Nil'));
	}
}
?>