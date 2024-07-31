<?php

class ConfigOfficeList extends CListPageModel
{
	public function attributeLabels()
	{
		return array(
			'name'=>Yii::t('contract','office Name'),
            'city'=>Yii::t('contract','City'),
			'z_display'=>Yii::t('contract','display'),
			'office_sum'=>Yii::t('contract','office sum'),
			'staff'=>Yii::t('app','Employee'),
			'u_id'=>Yii::t('contract','u_id'),
		);
	}
	
	public function retrieveDataByPage($pageNum=1)
	{
		$city = Yii::app()->user->city();
        $city_allow = Yii::app()->user->city_allow();
        $suffix = Yii::app()->params['envSuffix'];
		$sql1 = "select a.*,b.office_sum,f.name as city_name
				from hr_office a
				LEFT JOIN security{$suffix}.sec_city f ON a.city=f.code
				LEFT JOIN (SELECT office_id,COUNT(id) as office_sum FROM hr_employee WHERE city in ({$city_allow}) and staff_status!=1 AND office_id!=0 GROUP BY office_id) b ON a.id=b.office_id
				where a.city in ({$city_allow}) 
			";
		$sql2 = "select count(a.id)
				from hr_office a
				LEFT JOIN security{$suffix}.sec_city f ON a.city=f.code
				where a.city in ({$city_allow}) 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'u_id':
					$clause .= General::getSqlConditionClause('a.u_id', $svalue);
					break;
				case 'name':
					$clause .= General::getSqlConditionClause('a.name', $svalue);
					break;
				case 'city':
					$clause .= General::getSqlConditionClause('f.name', $svalue);
					break;
				case 'staff':
					$clause .= " and a.id in (select office_id from hr_employee where city in ({$city_allow}) and (code like '%$svalue%' or name like '%$svalue%'))";
					break;
			}
		}
		
		$order = "";
		if (!empty($this->orderField)) {
			$order .= " order by ".$this->orderField." ";
			if ($this->orderType=='D') $order .= "desc ";
		} else
			$order = " order by a.id desc";

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
						'u_id'=>$record['u_id'],
						'city'=>$record['city_name'],
						'office_sum'=>empty($record['office_sum'])?0:$record['office_sum'],
						'z_display'=>empty($record['z_display'])?Yii::t("contract","none"):Yii::t("contract","show"),
					);
			}
		}
		$session = Yii::app()->session;
		$session['configOffice_ya01'] = $this->getCriteria();
		return true;
	}

}
