<?php

class DeptList extends CListPageModel
{
    public $type = 0;
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(	
			'id'=>Yii::t('contract','ID'),
			'z_index'=>Yii::t('contract','Level'),
			'city'=>Yii::t('misc','City'),
			'name'=>Yii::t('contract',' Name'),
            'name_0'=>Yii::t('contract','Dept Name'),
			'name_1'=>Yii::t('contract','Leader Name'),
			'dept_id'=>Yii::t('contract','in department'),
			'dept_class'=>Yii::t('contract','Job category'),
            'review_status'=>Yii::t('contract','dept review'),
            'review_type'=>Yii::t('contract','review type'),
            'manager_type'=>Yii::t('contract','manager type'),
            'manager_leave'=>Yii::t('contract','manager leave'),
            'level_type'=>Yii::t('fete','level type'),
		);
	}
	public function getTypeName(){
	    if ($this->type == 1){
            return Yii::t("contract","Leader");
        }else{
            return Yii::t("contract","Dept");
        }
    }
	public function getTypeAcc(){
	    if ($this->type == 1){
            return "ZC02";
        }else{
            return "ZC01";
        }
    }

	public function retrieveDataByPage($pageNum=1)
	{
		$suffix = Yii::app()->params['envSuffix'];
		$city = Yii::app()->user->city();
		$type = $this->type;
		$sql1 = "select * from hr_dept 
                where type=$type AND z_del=0 
			";
		$sql2 = "select count(id)
				from hr_dept 
				where type=$type AND z_del=0 
			";
		$clause = "";
        $rw = Yii::app()->user->validRWFunction($this->getTypeAcc());
        if(!$rw){
            //$sql1.=" and city='$city' ";
            //$sql2.=" and city='$city' ";
        }

		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'name':
					$clause .= General::getSqlConditionClause('name',$svalue);
					break;
				case 'city':
					$clause .= ' and city in '.WordForm::getCityCodeSqlLikeName($svalue);
					break;
				case 'review_type':
					$clause .= ' and review_type in '.$this->getReviewSqlLikeName($svalue);
					break;
				case 'dept_id':
					$clause .= ' and dept_id in '.$this->getDeptSqlLikeName($svalue);
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
		$userList = CompanyForm::getUserList();
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
				$this->attr[] = array(
					'id'=>$record['id'],
					'name'=>$record['name'],
					'city'=>WordForm::getCityNameToCode($record['city']),
					'z_index'=>$record['z_index'],
					'dept_id'=>$record['dept_id'],
					'level_type'=>DeptForm::getConditionNameForId($record['level_type']),
					'review_status'=>empty($record['review_status'])?Yii::t("contract","not Participate"):Yii::t("contract","Participate"),
					'manager_leave'=>empty($record['manager_leave'])?Yii::t("contract","not Participate"):Yii::t("contract","Participate"),
					'review_type'=>DeptForm::getReviewType($record['review_type']),
					'manager_type'=>DeptForm::getManagerTypeLeave($record['manager_type'],true),
					'dept_class'=>Yii::t("staff",$record['dept_class']),
                    'acc'=>$this->getTypeAcc()
				);
			}
		}
		$session = Yii::app()->session;
		$session['dept_01'] = $this->getCriteria();
		return true;
	}

    public function getReviewSqlLikeName($code)
    {
        $arr = array();
        $reviewArr = DeptForm::getReviewType();
        foreach ($reviewArr as $key=> $review){
            if (strpos($review,$code)!==false){
                array_push($arr,$key);
            }
        }
        if(empty($arr)){
            return "('')";
        }else{
            $arr = implode(",",$arr);
            return "($arr)";
        }
    }

    public function getDeptSqlLikeName($code)
    {
        $from =  'security'.Yii::app()->params['envSuffix'].'.sec_city';
        $rows = Yii::app()->db->createCommand("select id from hr_dept where type=0 AND name like '%$code%'")->queryAll();
        $arr = array();
        foreach ($rows as $row){
            array_push($arr,$row["id"]);
        }
        if(empty($arr)){
            return "('')";
        }else{
            $arr = implode(",",$arr);
            return "($arr)";
        }
    }
}
