<?php

class RewardList extends CListPageModel
{
	public function attributeLabels()
	{
		return array(
			'employee_name'=>Yii::t('contract','Employee Name'),
			'employee_code'=>Yii::t('contract','Employee Code'),
			'reward_name'=>Yii::t('contract','Reward Name'),
			'reward_money'=>Yii::t('contract','Reward Money')."（RMB）",
            'reward_goods'=>Yii::t('contract','Reward Goods'),
            'lcd'=>Yii::t('queue','Req. Date'),
            'status'=>Yii::t('contract','Status'),
            'city'=>Yii::t('contract','City'),
		);
	}
	
	public function retrieveDataByPage($pageNum=1)
	{
		$city = Yii::app()->user->city();
        $city_allow = Yii::app()->user->city_allow();
		$sql1 = "select *
				from hr_employee_reward
				where id >= 0 AND city IN ($city_allow) 
			";
		$sql2 = "select count(id)
				from hr_employee_reward
				where id >= 0 AND city IN ($city_allow) 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'employee_code':
					$clause .= General::getSqlConditionClause('employee_code', $svalue);
					break;
				case 'employee_name':
					$clause .= General::getSqlConditionClause('employee_name', $svalue);
					break;
				case 'reward_name':
					$clause .= General::getSqlConditionClause('reward_name', $svalue);
					break;
				case 'reward_goods':
					$clause .= General::getSqlConditionClause('reward_goods', $svalue);
					break;
				case 'reward_money':
					$clause .= General::getSqlConditionClause('reward_money', $svalue);
					break;
				case 'city':
					$clause .= General::getSqlConditionClause('city', $svalue);
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
						'employee_id'=>$record['employee_id'],
						'employee_code'=>$record['employee_code'],
						'employee_name'=>$record['employee_name'],
						'reward_id'=>$record['reward_id'],
						'reward_name'=>$record['reward_name'],
                        'city'=>CGeneral::getCityName($record["city"]),
						'status'=>$this->getListStatus($record['status']),
						'lcd'=>date("Y-m-d",strtotime($record['lcd'])),
						'reward_goods'=>empty($record['reward_goods'])?"无":$record['reward_goods'],
						'reward_money'=>empty($record['reward_money'])?"无":sprintf("%.2f",$record['reward_money'])
					);
			}
		}
		$session = Yii::app()->session;
		$session['reward_ya01'] = $this->getCriteria();
		return true;
	}


    public function getListStatus($status){
        switch ($status){
            case 0:
                return array(
                    "status"=>Yii::t("contract","Draft"),
                    "style"=>" "
                );//已製作，待提交（草稿)
            case 1:
                return array(
                    "status"=>Yii::t("contract","Sent, pending approval"),
                    "style"=>" text-success"
                );//已提交，待審核
            case 2:
                return array(
                    "status"=>Yii::t("contract","audited, pending finish"),
                    "style"=>" text-yellow"
                );//已審核，待確認
            case 3:
                return array(
                    "status"=>Yii::t("contract","reject"),
                    "style"=>" text-red"
                );//已拒絕
            case 4:
                return array(
                    "status"=>Yii::t("contract","Finish"),
                    "style"=>" text-primary"
                );//已完成
        }
        return array(
            "status"=>"",
            "style"=>""
        );
    }
}
