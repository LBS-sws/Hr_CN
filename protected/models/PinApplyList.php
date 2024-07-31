<?php

class PinApplyList extends CListPageModel
{
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
            'apply_date'=>Yii::t('contract','Pin Date'),
            'pin_code'=>Yii::t('contract','Pin Code'),
            'employee_id'=>Yii::t('contract','Employee Name'),
            'name_id'=>Yii::t('app','Pin Name'),
            'class_id'=>Yii::t('app','Pin Class'),
            'city'=>Yii::t('contract','City'),
            'pin_num'=>Yii::t('contract','Pin Num'),
            'position'=>Yii::t('contract','Position'),
		);
	}

	public function retrieveDataByPage($pageNum=1)
	{
        $suffix = Yii::app()->params['envSuffix'];
        $city_allow = Yii::app()->user->city_allow();
		$sql1 = "select a.pin_code,p.name as dept_name,a.id,a.apply_date,a.pin_num,b.code,b.name,b.position,g.name as pin_name,h.name as city_name from hr_pin a 
                LEFT JOIN hr_employee b ON a.employee_id=b.id
                LEFT JOIN hr_dept p ON b.position=p.id
                LEFT JOIN hr_pin_inventory f ON a.inventory_id=f.id
                LEFT JOIN hr_pin_name g ON f.pin_name_id = g.id
                LEFT JOIN security$suffix.sec_city h ON a.city=h.code
                where a.city in ($city_allow) 
			";
		$sql2 = "select count(a.id) from hr_pin a 
                LEFT JOIN hr_employee b ON a.employee_id=b.id
                LEFT JOIN hr_dept p ON b.position=p.id
                LEFT JOIN hr_pin_inventory f ON a.inventory_id=f.id
                LEFT JOIN hr_pin_name g ON f.pin_name_id = g.id
                LEFT JOIN security$suffix.sec_city h ON a.city=h.code
                where a.city in ($city_allow) 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'position':
					$clause .= General::getSqlConditionClause('p.name',$svalue);
					break;
				case 'pin_code':
					$clause .= General::getSqlConditionClause('a.code',$svalue);
					break;
				case 'employee_id':
					$clause .= General::getSqlConditionClause('b.name',$svalue);
					break;
				case 'name_id':
					$clause .= General::getSqlConditionClause('g.name',$svalue);
					break;
				case 'apply_date':
					$clause .= General::getSqlConditionClause('a.apply_date',$svalue);
					break;
			}
		}
		
		$order = "";
		if (!empty($this->orderField)) {
			$order .= " order by ".$this->orderField." ";
			if ($this->orderType=='D') $order .= "desc ";
		}else{
            $order .= " order by id desc ";
        }

		$sql = $sql2.$clause;
		$this->totalRow = Yii::app()->db->createCommand($sql)->queryScalar();
		
		$sql = $sql1.$clause.$order;
		$sql = $this->sqlWithPageCriteria($sql, $this->pageNum);
		$records = Yii::app()->db->createCommand($sql)->queryAll();

		$this->attr = array();
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
				$this->attr[] = array(
					'id'=>$record['id'],
					'apply_date'=>$record['apply_date'],
					'pin_code'=>$record['pin_code'],
					'employee_id'=>$record['name'],
					'name_id'=>$record['pin_name'],
					'city'=>$record['city_name'],
					'position'=>$record['dept_name'],
					'pin_num'=>$record['pin_num'],
				);
			}
		}
		$session = Yii::app()->session;
		$session['pinApply_01'] = $this->getCriteria();
		return true;
	}
}
