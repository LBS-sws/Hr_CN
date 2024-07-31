<?php

class RewardConList extends CListPageModel
{
	public function attributeLabels()
	{
		return array(
			'name'=>Yii::t('contract','Reward Name'),
			'money'=>Yii::t('contract','Reward Money')."（RMB）",
            'goods'=>Yii::t('contract','Reward Goods'),
		);
	}
	
	public function retrieveDataByPage($pageNum=1)
	{
		$city = Yii::app()->user->city();
		$sql1 = "select *
				from hr_reward
				where id >= 0 
			";
		$sql2 = "select count(id)
				from hr_reward
				where id >= 0 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'name':
					$clause .= General::getSqlConditionClause('name', $svalue);
					break;
				case 'money':
					$clause .= General::getSqlConditionClause('money', $svalue);
					break;
				case 'goods':
					$clause .= General::getSqlConditionClause('goods', $svalue);
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
						'name'=>$record['name'],
						'goods'=>empty($record['goods'])?"无":$record['goods'],
						'money'=>empty($record['money'])?"无":sprintf("%.2f",$record['money'])
					);
			}
		}
		$session = Yii::app()->session;
		$session['rewardCon_ya01'] = $this->getCriteria();
		return true;
	}

}
