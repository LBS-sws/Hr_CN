<?php

class AuditTripList extends CListPageModel
{
	public function attributeLabels()
	{
        return array(
            'trip_code'=>Yii::t('fete','trip code'),
            'lcd'=>Yii::t('fete','apply for time'),
            'employee_name'=>Yii::t('contract','Employee Name'),
            'employee_code'=>Yii::t('contract','Employee Code'),
            'city'=>Yii::t('contract','City'),
            'city_name'=>Yii::t('contract','City'),
            'start_time'=>Yii::t('contract','Start Time'),
            'end_time'=>Yii::t('contract','End Time'),
            'status'=>Yii::t('contract','Status'),
            'tripdoc'=>Yii::t('contract','Attachment'),
            'trip_cost'=>Yii::t('fete','trip cost'),
            'trip_address'=>Yii::t('fete','trip address'),
        );
	}
	
	public function retrieveDataByPage($pageNum=1)
	{
		$city = Yii::app()->user->city();
        $suffix = Yii::app()->params['envSuffix'];
        $city_allow = Yii::app()->user->city_allow();
		$sql1 = "select a.*,f.name as city_name,b.code,b.name
				from hr_employee_trip a
                LEFT JOIN hr_employee b ON a.employee_id=b.id
                LEFT JOIN security{$suffix}.sec_city f ON b.city=f.code
				where b.city IN ($city_allow) and a.z_index=4 AND a.status in (1,3) 
			";
		$sql2 = "select count(a.id)
				from hr_employee_trip a
                LEFT JOIN hr_employee b ON a.employee_id=b.id
                LEFT JOIN security{$suffix}.sec_city f ON b.city=f.code
				where b.city IN ($city_allow) and a.z_index=4 AND a.status in (1,3) 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
                case 'trip_code':
                    $clause .= General::getSqlConditionClause('a.trip_code',$svalue);
                    break;
                case 'trip_address':
                    $clause .= General::getSqlConditionClause('a.trip_address',$svalue);
                    break;
                case 'employee_name':
                    $clause .= General::getSqlConditionClause('b.name',$svalue);
                    break;
                case 'city_name':
                    $clause .= General::getSqlConditionClause('f.name',$svalue);
                    break;
			}
		}
		
		$order = "";
		if (!empty($this->orderField)) {
			$order .= " order by ".$this->orderField." ";
			if ($this->orderType=='D') $order .= "desc ";
		} else
			$order = " order by a.id desc";

		$sql = $sql2.$clause;
		$this->totalRow = Yii::app()->db->createCommand($sql)->queryScalar();
		
		$sql = $sql1.$clause.$order;
		$sql = $this->sqlWithPageCriteria($sql, $this->pageNum);
		$records = Yii::app()->db->createCommand($sql)->queryAll();
		
		$this->attr = array();
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
                $colorList = AuditLeaveList::statusToColor($record['status']);
                $this->attr[] = array(
                    'id'=>$record['id'],
                    'trip_code'=>$record['trip_code'],
                    'lcd'=>$record['lcd'],
                    'employee_name'=>$record['name'],
                    'log_time'=>$record['log_time'],
                    'end_time'=>CGeneral::toDate($record['end_time']),
                    'start_time'=>CGeneral::toDate($record['start_time']),
                    'city_name'=>$record['city_name'],
                    'trip_address'=>$record['trip_address'],
                    'trip_cost'=>floatval($record['trip_cost']),
                    'status'=>$colorList["status"],
                    'style'=>$colorList["style"],
                );
			}
		}
		$session = Yii::app()->session;
		$session['auditTrip_ya01'] = $this->getCriteria();
		return true;
	}

}
