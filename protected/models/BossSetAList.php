<?php

class BossSetAList extends CListPageModel
{
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
        return array(
            'list_text'=>Yii::t("contract","matters"),
            'tacitly'=>Yii::t("contract","tacitly"),
            'num_ratio'=>Yii::t("contract","one_11"),
            'city'=>Yii::t('contract','City')
        );
	}

	public function retrieveDataByPage($pageNum=1)
	{
        $suffix = Yii::app()->params['envSuffix'];
        $city_allow = Yii::app()->user->city_allow();
		$sql1 = "select a.id,a.city,a.list_text,a.tacitly,a.num_ratio,b.name from hr_boss_set_a a 
                LEFT JOIN security$suffix.sec_city b ON a.city=b.code 
                where a.id>0 
			";
		$sql2 = "select count(a.id) from hr_boss_set_a a 
                LEFT JOIN security$suffix.sec_city b ON a.city=b.code 
                where a.id>0 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'list_text':
					$clause .= General::getSqlConditionClause('a.list_text',$svalue);
					break;
				case 'tacitly':
                    $svalue = strpos(Yii::t("contract","tacitly"),$svalue)!==false?1:0;
					$clause .= General::getSqlConditionClause('a.tacitly',$svalue);
					break;
				case 'city':
					$clause .= General::getSqlConditionClause('b.name',$svalue);
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

		$this->attr = array();
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
				$this->attr[] = array(
					'id'=>$record['id'],
					'list_text'=>$record['list_text'],
					'city_name'=>$record['name'],
					'num_ratio'=>$record['num_ratio']."%",
					'tacitly'=>$record['tacitly']==1?Yii::t("contract","tacitly"):""
				);
			}
		}
		$session = Yii::app()->session;
		$session['bossSetA_01'] = $this->getCriteria();
		return true;
	}
}
