<?php

class WordList extends CListPageModel
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
			'city'=>Yii::t('contract','City'),
			'name'=>Yii::t('contract','Word Name'),
			'type'=>Yii::t('contract','Restrict'),
		);
	}

	public function retrieveDataByPage($pageNum=1)
	{
		$suffix = Yii::app()->params['envSuffix'];
		$city = Yii::app()->user->city();
		$sql1 = "select id,city,name,type from hr_docx 
                where id > 0 
			";
		$sql2 = "select count(id)
				from hr_docx 
				where id > 0 
			";
        $rw = Yii::app()->user->validRWFunction("ZD01");
        if(!$rw){
            $sql1.=" and (city='$city' or type='default') ";
            $sql2.=" and (city='$city' or type='default') ";
        }
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'name':
					$clause .= General::getSqlConditionClause('name',$svalue);
					break;
                case 'city':
                    $clause .= " and city in ".WordForm::getCityCodeSqlLikeName($svalue);
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
					'name'=>$record['name'],
                    'city'=>WordForm::getCityNameToCode($record['city']),
					'type'=>Yii::t("contract",$record['type'])
				);
			}
		}
		$session = Yii::app()->session;
		$session['word_01'] = $this->getCriteria();
		return true;
	}

}
