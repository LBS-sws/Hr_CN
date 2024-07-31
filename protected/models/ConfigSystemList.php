<?php

class ConfigSystemList extends CListPageModel
{
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
        return array(
            'set_city'=>"配置城市",
            'set_name'=>"配置名称",
            'set_value'=>"配置的值"
        );
	}

	public function retrieveDataByPage($pageNum=1)
	{
        $city_allow = Yii::app()->user->city_allow();
		$sql1 = "select id,set_value,set_city,set_name from hr_setting 
                where id>0 
			";
		$sql2 = "select count(id)
				from hr_setting 
                where id>0 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'set_value':
					$clause .= General::getSqlConditionClause('set_value',$svalue);
					break;
				case 'set_name':
					$clause .= General::getSqlConditionClause('set_name',$svalue);
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
					'set_city'=>CGeneral::getCityName($record['set_city']),
					'set_name'=>$record['set_name'],
					'set_value'=>$record['set_value']
				);
			}
		}
		$session = Yii::app()->session;
		$session['configSystem_01'] = $this->getCriteria();
		return true;
	}
}
