<?php

class BossKPIList extends CListPageModel
{
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
            'kpi_name'=>Yii::t('contract','kpi name'),
            'sum_bool'=>Yii::t('contract','kpi sum bool'),
            'tacitly'=>Yii::t("contract","tacitly"),
            'city'=>Yii::t('contract','City')
		);
	}

	public function retrieveDataByPage($pageNum=1)
	{
        $suffix = Yii::app()->params['envSuffix'];
        $city_allow = Yii::app()->user->city_allow();
		$sql1 = "select a.id,a.kpi_name,a.kpi_str,a.sum_bool,a.tacitly,b.name from hr_kpi a 
                LEFT JOIN security$suffix.sec_city b ON a.city=b.code 
                where a.id>0 
			";
		$sql2 = "select count(a.id)
				from hr_kpi a 
                LEFT JOIN security$suffix.sec_city b ON a.city=b.code 
                where a.id>0 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'kpi_name':
					$clause .= General::getSqlConditionClause('a.kpi_str',$svalue);
					break;
                case 'city':
                    $clause .= General::getSqlConditionClause('b.name',$svalue);
                    break;
                case 'tacitly':
                    $svalue = strpos(Yii::t("contract","tacitly"),$svalue)!==false?1:0;
                    $clause .= General::getSqlConditionClause('a.tacitly',$svalue);
                    break;
			}
		}
		
		$order = "";
		if (!empty($this->orderField)) {
			$order .= " order by ".$this->orderField." ";
			if ($this->orderType=='D') $order .= "desc ";
		}else{
            $order = " order by a.kpi_str asc";
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
					'kpi_name'=>Yii::t("contract",$record['kpi_name']),
					'sum_bool'=>empty($record['sum_bool'])?Yii::t("contract","Off"):Yii::t("contract","On"),
                    'city_name'=>$record['name'],
                    'tacitly'=>$record['tacitly']==1?Yii::t("contract","tacitly"):""
				);
			}
		}
		$session = Yii::app()->session;
		$session['bossKPI_01'] = $this->getCriteria();
		return true;
	}
}
