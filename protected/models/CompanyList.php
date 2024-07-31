<?php

class CompanyList extends CListPageModel
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
			'name'=>Yii::t('contract','Company Name'),
			'head'=>Yii::t('contract','Head'),
			'agent'=>Yii::t('contract','Agent'),
			'tacitly'=>Yii::t('contract','Status'),
            'city_name'=>Yii::t('contract','City'),
            'share_bool'=>Yii::t('contract','share bool'),
		);
	}

	public function retrieveDataByPage($pageNum=1)
	{
		$suffix = Yii::app()->params['envSuffix'];
		$city = Yii::app()->user->city();
        $city_allow = Yii::app()->user->city_allow();
        $whereSql = " (city in ($city_allow) or share_bool=1) ";
		$sql1 = "select * from hr_company 
                where {$whereSql} 
			";
		$sql2 = "select count(id)
				from hr_company 
                where {$whereSql} 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'name':
					$clause .= General::getSqlConditionClause('name',$svalue);
					break;
				case 'head':
					$clause .= General::getSqlConditionClause('head',$svalue);
					break;
				case 'agent':
					$clause .= General::getSqlConditionClause('agent',$svalue);
					break;
				case 'city_name':
                    $clause .= ' and city in '.WordForm::getCityCodeSqlLikeName($svalue);
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
		//$userList = CompanyForm::getUserList();
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
				$this->attr[] = array(
					'id'=>$record['id'],
					'name'=>$record['name'],
					'head'=>$record['head'],
					'agent'=>$record['agent'],
					'share_bool'=>$record['share_bool']==1?Yii::t("contract","share"):Yii::t("contract","not share"),
                    'city'=>CGeneral::getCityName($record["city"]),
					'tacitly'=>$record['tacitly']
				);
			}
		}
		$session = Yii::app()->session;
		$session['company_01'] = $this->getCriteria();
		return true;
	}

	//獲取管轄城市列表
    public static function getSingleCityToList($city="") {
        $city_allow = Yii::app()->user->city_allow();
        $suffix = Yii::app()->params['envSuffix'];
        $rows = Yii::app()->db->createCommand()->select()->from("security{$suffix}.sec_city")
            ->where("code in ({$city_allow}) or code=:code",array(":code"=>$city))->queryAll();

        $arr = array();
        if($rows){
            foreach ($rows as $row){
                $arr[$row["code"]] = $row["name"];
            }
        }
        return $arr;
    }

}
