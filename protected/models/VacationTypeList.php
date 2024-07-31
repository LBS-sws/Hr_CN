<?php

class VacationTypeList extends CListPageModel
{
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(	
			'vaca_code'=>Yii::t('fete','Vacation type code'),
			'vaca_name'=>Yii::t('fete','Vacation type name')
		);
	}

	public function retrieveDataByPage($pageNum=1)
	{
        $city_allow = Yii::app()->user->city_allow();
		$sql1 = "select * from hr_vacation_type 
                where vaca_code IS NOT NULL 
			";
		$sql2 = "select count(*)
				from hr_vacation_type 
                where vaca_code IS NOT NULL 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'vaca_code':
					$clause .= General::getSqlConditionClause('vaca_code',$svalue);
					break;
				case 'vaca_name':
					$clause .= General::getSqlConditionClause('vaca_name',$svalue);
					break;
			}
		}
		
		$order = "";
		if (!empty($this->orderField)) {
			$order .= " order by ".$this->orderField." ";
			if ($this->orderType=='D') $order .= "desc ";
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
					'vaca_name'=>$record['vaca_name'],
					'vaca_code'=>$record['vaca_code'],
				);
			}
		}
		$session = Yii::app()->session;
		$session['vacationType_01'] = $this->getCriteria();
		return true;
	}
}
