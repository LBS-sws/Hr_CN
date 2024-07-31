<?php

class ContractList extends CListPageModel
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
			'name'=>Yii::t('contract','Contract Name'),
			'retire'=>Yii::t('contract','judge retire'),
			'local_type'=>Yii::t('contract','Restrict'),
		);
	}

	public function retrieveDataByPage($pageNum=1)
	{
		$suffix = Yii::app()->params['envSuffix'];
		$city = Yii::app()->user->city();
		$sql1 = "select * from hr_contract 
                where id!=0  
			";
		$sql2 = "select count(id)
				from hr_contract 
				where id!=0  
			";
        $rw = Yii::app()->user->validRWFunction("ZD02");
        if(!$rw){
            $sql1.=" and city='$city' ";
            $sql2.=" and city='$city' ";
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
					'retire'=>$record['retire']==1?Yii::t("misc","Yes"):Yii::t("misc","No"),
					'local_type'=>self::getWordStrToType($record['local_type']),
					'city'=>WordForm::getCityNameToCode($record['city']),
				);
			}
		}
		$session = Yii::app()->session;
        $session['contract_01'] = $this->getCriteria();
		return true;
	}

    public static function getWordType(){
        return array(
            "0"=>Yii::t("contract","local"),
            "1"=>Yii::t("contract","default"),
            "2"=>Yii::t("contract","none"),
        );
    }

    public static function getWordStrToType($type){
        $list = self::getWordType();
        $type = "".$type;
        if(key_exists($type,$list)){
            return $list[$type];
        }else{
            return $type;
        }
    }
}
