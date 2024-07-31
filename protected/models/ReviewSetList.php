<?php

class ReviewSetList extends CListPageModel
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
            'set_name'=>Yii::t('contract','set name'),
            'pro_num'=>Yii::t('contract','pro num'),
            'four_with'=>Yii::t('contract','four with'),
            'z_index'=>Yii::t('fete','level'),
            'num_ratio'=>Yii::t('contract','num ratio'),
		);
	}

	public function retrieveDataByPage($pageNum=1)
	{
        $suffix = Yii::app()->params['envSuffix'];
        $city_allow = Yii::app()->user->city_allow();
		$sql1 = "select a.*,count(b.set_id) as pro_num from hr_set a
                LEFT JOIN hr_set_pro b ON a.id = b.set_id
                where a.id != '' 
			";
		$sql2 = "select count(*) from hr_set a
                where a.id != '' 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'id':
					$clause .= General::getSqlConditionClause('a.id',$svalue);
					break;
				case 'set_name':
					$clause .= General::getSqlConditionClause('a.set_name',$svalue);
					break;
                case 'city_name':
                    $clause .= ' and a.code in '.WordForm::getCityCodeSqlLikeName($svalue);
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
		
		$sql = $sql1.$clause." group by a.id ".$order;
		$sql = $this->sqlWithPageCriteria($sql, $this->pageNum);
		$records = Yii::app()->db->createCommand($sql)->queryAll();

		$this->attr = array();
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
				$this->attr[] = array(
					'id'=>$record['id'],
					'set_name'=>$record['set_name'],
					'pro_num'=>$record['pro_num'],
					'num_ratio'=>$record['num_ratio'],
					'four_with'=>ReviewSetForm::getFourWith($record['four_with']),
					'z_index'=>$record['z_index']
				);
			}
		}
		$session = Yii::app()->session;
		$session['reviewSet_01'] = $this->getCriteria();
		return true;
	}
}
