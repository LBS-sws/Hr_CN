<?php

class PlusCityList extends CListPageModel
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
			'employee_code'=>Yii::t('contract','Employee Code'),
			'employee_name'=>Yii::t('contract','Employee Name'),
			'original_city'=>Yii::t('contract','original city'),
			'city'=>Yii::t('contract','plus city'),
			'plus_department'=>Yii::t('contract','plus dept'),
			'plus_position'=>Yii::t('contract','plus leader'),
		);
	}

	public function retrieveDataByPage($pageNum=1)
	{
		$suffix = Yii::app()->params['envSuffix'];
		$city = Yii::app()->user->city();
        $city_allow = Yii::app()->user->city_allow();
		$sql1 = "select a.*,b.code as employee_code,b.name as employee_name,b.city as original_city,d.name as plus_department,e.name as plus_position from hr_plus_city a 
                LEFT JOIN hr_employee b ON a.employee_id = b.id
                LEFT JOIN hr_dept d ON a.department = d.id
                LEFT JOIN hr_dept e ON a.position = e.id
                where b.city IN ($city_allow) 
			";
        $sql2 = "select count(a.id) from hr_plus_city a 
                LEFT JOIN hr_employee b ON a.employee_id = b.id
                LEFT JOIN hr_dept d ON a.department = d.id
                LEFT JOIN hr_dept e ON a.position = e.id
                where b.city IN ($city_allow) 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'employee_code':
					$clause .= General::getSqlConditionClause('b.code',$svalue);
					break;
				case 'employee_name':
					$clause .= General::getSqlConditionClause('b.name',$svalue);
					break;
                case 'original_city':
                    $clause .= ' and b.city in '.WordForm::getCityCodeSqlLikeName($svalue);
                    break;
                case 'city':
                    $clause .= ' and a.city in '.WordForm::getCityCodeSqlLikeName($svalue);
                    break;
				case 'plus_department':
					$clause .= General::getSqlConditionClause('d.name',$svalue);
					break;
				case 'plus_position':
					$clause .= General::getSqlConditionClause('e.name',$svalue);
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
				$this->attr[] = array(
					'id'=>$record['id'],
					'employee_code'=>$record['employee_code'],
					'employee_name'=>$record['employee_name'],
                    'original_city'=>CGeneral::getCityName($record["original_city"]),
                    'city'=>CGeneral::getCityName($record["city"]),
					'plus_position'=>$record['plus_position'],
					'plus_department'=>$record['plus_department'],
				);
			}
		}
		$session = Yii::app()->session;
		$session['plusCity_01'] = $this->getCriteria();
		return true;
	}

}
