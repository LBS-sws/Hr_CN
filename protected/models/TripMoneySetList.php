<?php

class TripMoneySetList extends CListPageModel
{
	public function attributeLabels()
	{
		return array(
			'pro_name'=>Yii::t('fete','project name'),
            'z_display'=>Yii::t('contract','display'),
            'z_index'=>Yii::t('fete','index'),
		);
	}
	
	public function retrieveDataByPage($pageNum=1)
	{
		$city = Yii::app()->user->city();
		$sql1 = "select *
				from hr_trip_money_set
				where id >= 0 
			";
		$sql2 = "select count(id)
				from hr_trip_money_set
				where id >= 0 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'pro_name':
					$clause .= General::getSqlConditionClause('pro_name', $svalue);
					break;
			}
		}
		
		$order = "";
		if (!empty($this->orderField)) {
			$order .= " order by ".$this->orderField." ";
			if ($this->orderType=='D') $order .= "desc ";
		} else
			$order = " order by id desc";

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
						'pro_name'=>$record['pro_name'],
						'z_index'=>$record['z_index'],
						'z_display'=>empty($record['z_display'])?Yii::t("contract","none"):Yii::t("contract","show"),
					);
			}
		}
		$session = Yii::app()->session;
		$session['tripMoneySet_ya01'] = $this->getCriteria();
		return true;
	}

}
