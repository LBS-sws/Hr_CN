<?php

class TemplateList extends CListPageModel
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
            'tem_name'=>Yii::t('contract','template name'),
            'city'=>Yii::t('contract','City'),
            'tem_str'=>Yii::t('contract','pro num')
		);
	}

	public function retrieveDataByPage($pageNum=1)
	{
        $suffix = Yii::app()->params['envSuffix'];
        $city_allow = Yii::app()->user->city_allow();
        $city = Yii::app()->user->city();
		$sql1 = "select a.*,b.name as city_name from hr_template a
                LEFT JOIN security$suffix.sec_city b ON a.city = b.code
                where a.city = '$city' 
			";
		$sql2 = "select count(*) from hr_template a
                LEFT JOIN security$suffix.sec_city b ON a.city = b.code
                where a.city = '$city' 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'tem_name':
					$clause .= General::getSqlConditionClause('a.tem_name',$svalue);
					break;
			}
		}
		
		$order = "";
		if (!empty($this->orderField)) {
			$order .= " order by ".$this->orderField." ";
			if ($this->orderType=='D') $order .= "desc ";
		}else{
            $order .= " order by a.id desc ";
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
					'tem_name'=>$record['tem_name'],
					'city_name'=>$record['city_name'],
				);
			}
		}
		$session = Yii::app()->session;
		$session['template_01'] = $this->getCriteria();
		return true;
	}
}
