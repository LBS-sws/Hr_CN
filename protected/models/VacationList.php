<?php

class VacationList extends CListPageModel
{
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(	
			'name'=>Yii::t('fete','Vacation Name'),
			'city'=>Yii::t('contract','City'),
            'only'=>Yii::t('fete','Scope of application'),
            'ass_id_name'=>Yii::t('contract','associated config'),
            'ass_bool'=>Yii::t('contract','associated bool'),
		);
	}

	public function retrieveDataByPage($pageNum=1)
	{
        $city_allow = Yii::app()->user->city_allow();
		$sql1 = "select * from hr_vacation 
                where (city in ($city_allow) OR only='default')
			";
		$sql2 = "select count(id)
				from hr_vacation 
                where (city in ($city_allow) OR only='default')
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'name':
					$clause .= General::getSqlConditionClause('name',$svalue);
					break;
				case 'city':
					$clause .= General::getSqlConditionClause('city',$svalue);
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
					'name'=>$record['name'],
					'ass_id_name'=>$record['ass_id_name'],
					'ass_bool'=>$record['ass_bool']==1?Yii::t("misc","Yes"):Yii::t("misc","No"),
					'only'=>Yii::t("fete",$record['only']),
                    'city'=>CGeneral::getCityName($record["city"]),
				);
			}
		}
		$session = Yii::app()->session;
		$session['vacation_01'] = $this->getCriteria();
		return true;
	}
}
