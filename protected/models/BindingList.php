<?php

class BindingList extends CListPageModel
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
			'employee_name'=>Yii::t('contract','Employee Name'),
			'employee_city'=>Yii::t('contract','Employee City'),
			'user_city'=>Yii::t('contract','User City'),
			'user_name'=>Yii::t('contract','Account number'),
            'city_name'=>Yii::t('contract','City'),
		);
	}

	public function retrieveDataByPage($pageNum=1)
	{
		$suffix = Yii::app()->params['envSuffix'];
		$city = Yii::app()->user->city();
        $city_allow = Yii::app()->user->city_allow();
        $plusSql = PlusCityForm::getPlusEmployeeList()["plusSql"];
		$sql1 = "select a.id,b.name,b.city as employee_city,d.city as user_city,d.disp_name from hr_binding a 
                LEFT JOIN hr_employee b ON a.employee_id = b.id
                LEFT JOIN security$suffix.sec_user d ON d.username = a.user_id
                where (b.city IN ($city_allow) or b.id in ($plusSql)) 
			";
        $sql2 = "select count(a.id) from hr_binding a 
                LEFT JOIN hr_employee b ON a.employee_id = b.id
                LEFT JOIN security$suffix.sec_user d ON d.username = a.user_id
                where (b.city IN ($city_allow) or b.id in ($plusSql)) 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'employee_name':
					$clause .= General::getSqlConditionClause('b.name',$svalue);
					break;
                case 'employee_city':
                    $clause .= ' and b.city in '.WordForm::getCityCodeSqlLikeName($svalue);
                    break;
                case 'user_city':
                    $clause .= ' and d.city in '.WordForm::getCityCodeSqlLikeName($svalue);
                    break;
				case 'user_name':
					$clause .= General::getSqlConditionClause('d.disp_name',$svalue);
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
		$userList = CompanyForm::getUserList();
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
				$this->attr[] = array(
					'id'=>$record['id'],
					'employee_name'=>$record['name'],
                    'employee_city'=>CGeneral::getCityName($record["employee_city"]),
					'user_name'=>$record['disp_name'],
                    'user_city'=>CGeneral::getCityName($record["user_city"]),
				);
			}
		}
		$session = Yii::app()->session;
		$session['binding_01'] = $this->getCriteria();
		return true;
	}

}
