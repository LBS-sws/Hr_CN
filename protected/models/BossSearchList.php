<?php

class BossSearchList extends CListPageModel
{
    public $employee_id;//員工id
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
            'name'=>Yii::t('contract','Employee Name'),
            'code'=>Yii::t('contract','Employee Code'),
            'audit_year'=>Yii::t('contract','audit year'),
            'results_a'=>Yii::t('contract','Results (A)'),
            'results_b'=>Yii::t('contract','Results (B)'),
            'results_c'=>Yii::t('contract','Results (C)'),
            'results_sum'=>Yii::t('contract','Sum Results'),
            'status_type'=>Yii::t('contract','Status'),
            'city_name'=>Yii::t('contract','City'),
		);
	}

	public function retrieveDataByPage($pageNum=1)
	{
        $suffix = Yii::app()->params['envSuffix'];
        $city_allow = Yii::app()->user->city_allow();
		$sql1 = "select b.name,b.code,d.name as city_name,a.* from hr_boss_audit a 
                LEFT JOIN hr_employee b ON a.employee_id = b.id 
                LEFT JOIN security$suffix.sec_city d ON d.code = a.city 
                where a.status_type = 2 and a.city IN ($city_allow) 
			";
		$sql2 = "select count(a.id)
				from hr_boss_audit a 
                LEFT JOIN hr_employee b ON a.employee_id = b.id 
                LEFT JOIN security$suffix.sec_city d ON d.code = a.city 
                where a.status_type = 2 and a.city IN ($city_allow) 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'audit_year':
					$clause .= General::getSqlConditionClause('a.audit_year',$svalue);
					break;
				case 'code':
					$clause .= General::getSqlConditionClause('b.code',$svalue);
					break;
				case 'name':
					$clause .= General::getSqlConditionClause('b.name',$svalue);
					break;
				case 'city_name':
					$clause .= General::getSqlConditionClause('d.name',$svalue);
					break;
			}
		}
		
		$order = "";
		if (!empty($this->orderField)) {
			$order .= " order by ".$this->orderField." ";
			if ($this->orderType=='D') $order .= "desc ";
		}else{
            $order .= " order by a.audit_year desc ";
        }

		$sql = $sql2.$clause;
		$this->totalRow = Yii::app()->db->createCommand($sql)->queryScalar();
		
		$sql = $sql1.$clause.$order;
		$sql = $this->sqlWithPageCriteria($sql, $this->pageNum);
		$records = Yii::app()->db->createCommand($sql)->queryAll();

		$this->attr = array();
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
                $ratio_a = $record['ratio_a']*0.01;
                $ratio_b = $record['ratio_b']*0.01;
                //$arrList = $this->statusToColor($record['status_type']);
                $bossRewardType = BossApplyForm::getBossRewardType($record['city']);
                $record["results_a"]=empty($record['results_a'])?0:floatval($record['results_a'])*$ratio_a;
                if($bossRewardType == 1){
                    $record['results_c'] = "-";
                    $record["results_b"]=empty($record['results_b'])?0:floatval($record['results_b'])*$ratio_b;
                    $record['results_sum'] = $record["results_a"]+$record["results_b"];
                }else{
                    $record['results_c'] = $record['results_c']."%";
                    $record["results_b"]=empty($record['results_b'])?0:floatval($record['results_b'])*$ratio_b;
                    $record['results_sum'] = $record["results_a"]+$record["results_b"]+$record['results_c'];
                }
                $record['results_sum'] = sprintf("%.2f",$record['results_sum']);
				$this->attr[] = array(
					'id'=>$record['id'],
					'code'=>$record['code'],
					'name'=>$record['name'],
					'results_a'=>$record['results_a']."%",
					'results_b'=>$record['results_b']."%",
					'results_c'=>$record['results_c'],
					'results_sum'=>$record['results_sum'],
					'audit_year'=>$record['audit_year'],
                    'city_name'=>$record['city_name'],
					'status_type'=>'',
					'style'=>'',
				);
			}
		}
		$session = Yii::app()->session;
		$session['bossSearch_01'] = $this->getCriteria();
		return true;
	}
    //根據狀態獲取顏色 - 查詢不顯示狀態
    public function statusToColor($status){
        switch ($status){
            case 0:
                return array(
                    "status"=>Yii::t("contract","Draft"),
                    "style"=>""
                );
            case 1:
                return array(
                    "status"=>Yii::t("contract","pending approval"),//已發送，等待審核
                    "style"=>" text-primary"
                );
            case 2:
                return array(
                    "status"=>Yii::t("contract","Finish"),//審核通過
                    "style"=>" text-success"
                );
            case 3:
                return array(
                    "status"=>Yii::t("contract","Rejected"),//拒絕
                    "style"=>" text-danger"
                );
        }
        return array(
            "status"=>$status,
            "style"=>""
        );
    }
}
