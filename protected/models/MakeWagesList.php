<?php

class MakeWagesList extends CListPageModel
{
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(	
			'id'=>Yii::t('contract','ID'),
			'name'=>Yii::t('contract','Employee Name'),
			'code'=>Yii::t('contract','Employee Code'),
			'phone'=>Yii::t('contract','Employee Phone'),
			'position'=>Yii::t('contract','Position'),
			'company_id'=>Yii::t('contract','Company Name'),
			'contract_id'=>Yii::t('contract','Contract Name'),
			'staff_status'=>Yii::t('contract','Wages Status'),
            'city_name'=>Yii::t('contract','City'),
			'city'=>Yii::t('contract','City'),
            'wages_date'=>Yii::t('contract','Wages Time'),
		);
	}

	public function retrieveDataByPage($pageNum=1)
	{
		$suffix = Yii::app()->params['envSuffix'];
		$city = Yii::app()->user->city();
        $lcuId = Yii::app()->user->id;
        $city_allow = Yii::app()->user->city_allow();
		$sql1 = "select a.*,b.name,b.code,b.position,b.city AS s_city from hr_employee_wages a 
                LEFT JOIN hr_employee b ON a.employee_id = b.id
                where b.city IN ($city_allow) AND ((a.wages_status = 0 AND a.lcu='$lcuId') or a.wages_status != 0)
			";
		$sql2 = "select count(a.id)
				from hr_employee_wages a 
                LEFT JOIN hr_employee b ON a.employee_id = b.id
                where b.city IN ($city_allow) AND ((a.wages_status = 0 AND a.lcu='$lcuId') or a.wages_status != 0)
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'name':
					$clause .= General::getSqlConditionClause('b.name',$svalue);
					break;
				case 'code':
					$clause .= General::getSqlConditionClause('b.code',$svalue);
					break;
				case 'city':
					$clause .= General::getSqlConditionClause('b.city',$svalue);
					break;
				case 'position':
					$clause .= General::getSqlConditionClause('b.position',$svalue);
					break;
                case 'city_name':
                    $clause .= ' and b.city in '.WordForm::getCityCodeSqlLikeName($svalue);
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
			    $arr = $this->returnStaffStatus($record);
				$this->attr[] = array(
					'id'=>$record['id'],
					'name'=>$record['name'],
					'wages_date'=>date("Y-m",strtotime($record['wages_date'])),
					'code'=>$record['code'],
                    'city'=>CGeneral::getCityName($record["s_city"]),
					'position'=>DeptForm::getDeptToid($record['position']),
					'staff_status'=>$arr["status"],
					'style'=>$arr["style"],
				);
			}
		}
		$session = Yii::app()->session;
		$session['makewages_01'] = $this->getCriteria();
		return true;
	}

	public function returnStaffStatus($record){
        switch ($record["wages_status"]){
            case 0:
                return array(
                    "status"=>Yii::t("contract","Produced, pending submission"),
                    "style"=>" "
                );//已製作，待提交
            case 1:
                return array(
                    "status"=>Yii::t("contract","Produced, pending audit"),
                    "style"=>" text-success"
                );//已提交，待審核
            case 2:
                return array(
                    "status"=>Yii::t("contract","reject"),
                    "style"=>" text-red"
                );//已拒絕
            case 3:
                return array(
                    "status"=>Yii::t("contract","Finish"),
                    "style"=>" text-primary"
                );//已完成
/*            case 4:
                return array(
                    "status"=>Yii::t("contract","audited, pending finish"),
                    "style"=>" text-yellow"
                );//已審核，待確認*/

        }
    }
}
