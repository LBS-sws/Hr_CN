<?php

class WagesList extends CListPageModel
{
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(	
			'wages_name'=>Yii::t('contract','Wages Name'),
			'city'=>Yii::t('contract','City'),
		);
	}

	public function retrieveDataByPage($pageNum=1)
	{
		$suffix = Yii::app()->params['envSuffix'];
		//$city = Yii::app()->user->city();
        $city_allow = Yii::app()->user->city_allow();
		$sql1 = "select * from hr_wages 
                where city in ($city_allow) 
			";
		$sql2 = "select count(id)
				from hr_company 
				where city in ($city_allow) 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'city':
					$clause .= General::getSqlConditionClause('city',$svalue);
					break;
				case 'wages_name':
					$clause .= General::getSqlConditionClause('wages_name',$svalue);
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
		
		$list = array();
		$this->attr = array();
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
				$this->attr[] = array(
					'id'=>$record['id'],
					'wages_name'=>$record['wages_name'],
                    'city'=>CGeneral::getCityName($record["city"]),
				);
			}
		}
		$session = Yii::app()->session;
		$session['wages_01'] = $this->getCriteria();
		return true;
	}

}
