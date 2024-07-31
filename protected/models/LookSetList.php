<?php

class LookSetList extends CListPageModel
{
	public function attributeLabels()
	{
		return array(
			'username'=>Yii::t('contract','username'),
			'staff_name_str'=>Yii::t('contract','look employee'),
		);
	}
	
	public function retrieveDataByPage($pageNum=1)
	{
		$city = Yii::app()->user->city();
		$sql1 = "select *
				from hr_set_look
				where id >= 0 
			";
		$sql2 = "select count(id)
				from hr_set_look
				where id >= 0 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'username':
					$clause .= General::getSqlConditionClause('username', $svalue);
					break;
				case 'staff_name_str':
					$clause .= General::getSqlConditionClause('staff_name_str', $svalue);
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
						'username'=>$record['username'],
						'staff_name_str'=>$record['staff_name_str'],
					);
			}
		}
		$session = Yii::app()->session;
		$session['lookSet_ya01'] = $this->getCriteria();
		return true;
	}

}
