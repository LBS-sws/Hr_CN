<?php

class FeteList extends CListPageModel
{
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(	
			'name'=>Yii::t('fete','Fete Name'),
			'city'=>Yii::t('contract','City'),
			'start_time'=>Yii::t('contract','Start Time'),
			'end_time'=>Yii::t('contract','End Time'),
			'log_time'=>Yii::t('fete','Log Time'),
			'cost_num'=>Yii::t('fete','Cost Num'),
			'only'=>Yii::t('fete','Scope of application'),
		);
	}

	public function retrieveDataByPage($pageNum=1)
	{
        $city_allow = Yii::app()->user->city_allow();
		$sql1 = "select * from hr_fete 
                where (city in ($city_allow) OR only='default')
			";
		$sql2 = "select count(id)
				from hr_fete 
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
				case 'start_time':
					$clause .= General::getSqlConditionClause('start_time',$svalue);
					break;
				case 'end_time':
					$clause .= General::getSqlConditionClause('end_time',$svalue);
					break;
				case 'log_time':
					$clause .= General::getSqlConditionClause('log_time',$svalue);
					break;
				case 'cost_num':
					$clause .= General::getSqlConditionClause('cost_num',$svalue);
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
		
		$costNumList = $this->getCostNumList();
		$this->attr = array();
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
				$this->attr[] = array(
					'id'=>$record['id'],
					'name'=>$record['name'],
					'start_time'=>$record['start_time'],
					'end_time'=>$record['end_time'],
					'log_time'=>$record['log_time'],
					'only'=>Yii::t("fete",$record['only']),
					'cost_num'=>$costNumList[$record['cost_num']],
                    'city'=>CGeneral::getCityName($record["city"]),
				);
			}
		}
		$session = Yii::app()->session;
		$session['fete_01'] = $this->getCriteria();
		return true;
	}

	//獲取工資倍率
    public function getCostNumList(){
	    return array(Yii::t("fete","Two times the salary"),Yii::t("fete","Three times the salary"));
    }
}
