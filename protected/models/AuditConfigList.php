<?php

class AuditConfigList extends CListPageModel
{
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
            'city'=>Yii::t('contract','City'),
            'city_name'=>Yii::t('contract','City'),
            'audit_index'=>Yii::t('fete','Audit index'),
		);
	}

	public function retrieveDataByPage($pageNum=1)
	{
        $suffix = Yii::app()->params['envSuffix'];
        $city_allow = Yii::app()->user->city_allow();
		$sql1 = "select a.*,b.name AS city_name from hr_audit_con a 
                LEFT JOIN security$suffix.sec_city b ON a.city = b.code 
                where a.id>0 
			";
		$sql2 = "select count(a.id)
				from hr_audit_con a 
                LEFT JOIN security$suffix.sec_city b ON a.city = b.code 
                where a.id>0 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'audit_index':
					$clause .= General::getSqlConditionClause('a.audit_index',$svalue);
					break;
                case 'city_name':
                    $clause .= ' and b.code in '.WordForm::getCityCodeSqlLikeName($svalue);
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
		    $auditIndexList = $this->getAuditIndexList();
			foreach ($records as $k=>$record) {
				$this->attr[] = array(
					'id'=>$record['id'],
					'city_name'=>$record['city_name'],
					'audit_index'=>$auditIndexList[$record['audit_index']],
				);
			}
		}
		$session = Yii::app()->session;
		$session['auditCon_01'] = $this->getCriteria();
		return true;
	}

	function getAuditIndexList(){
	    return array(
	        ""=>"",
	        1=>Yii::t("fete","one index"),
	        2=>Yii::t("fete","two index"),
	        3=>Yii::t("fete","three index"),
        );
    }
}
