<?php

class SupportSearchList extends CListPageModel
{
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
            'support_code'=>Yii::t('contract','support code'),
            'apply_city'=>Yii::t('contract','apply city'),
            'apply_date'=>Yii::t('contract','Start Time'),
            'apply_end_date'=>Yii::t('contract','End Time'),
            'employee_id'=>Yii::t('contract','support employee'),
            'review_sum'=>Yii::t('contract','review sum'),
            'status_type'=>Yii::t('contract','Status'),
            'service_type'=>Yii::t('contract','service type'),
            'apply_type'=>Yii::t('queue','Type'),
            'privilege'=>Yii::t('contract','privilege'),
            'dept_name'=>Yii::t('contract','Position'),
		);
	}

	public function retrieveDataByPage($pageNum=1)
	{
        $suffix = Yii::app()->params['envSuffix'];
        $city = Yii::app()->user->city;
        $city_allow = Yii::app()->user->city_allow();
        $uid = Yii::app()->user->id;
        if(Yii::app()->user->validFunction('AY02')){
            $sqlEx = " ";
        }elseif (Yii::app()->user->validFunction('ZR11')){
            $sqlEx = " and a.apply_city in ($city_allow) ";
        }else{
            $bindEmployee = BindingForm::getEmployeeIdToUsername();
            $sqlEx = " and a.employee_id=$bindEmployee ";
        }
		$sql1 = "select a.*,b.name,c.name as city_name,e.name as dept_name from hr_apply_support a 
                LEFT JOIN hr_employee b ON a.employee_id = b.id
                LEFT JOIN hr_dept e ON b.position = e.id
                LEFT JOIN security$suffix.sec_city c ON a.apply_city = c.code
                where a.status_type != 1 $sqlEx 
			";
		$sql2 = "select count(*) from hr_apply_support a 
                LEFT JOIN hr_employee b ON a.employee_id = b.id
                LEFT JOIN hr_dept e ON b.position = e.id
                LEFT JOIN security$suffix.sec_city c ON a.apply_city = c.code
                where a.status_type != 1 $sqlEx
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'support_code':
					$clause .= General::getSqlConditionClause('a.support_code',$svalue);
					break;
				case 'employee_id':
					$clause .= General::getSqlConditionClause('b.name',$svalue);
					break;
				case 'apply_city':
					$clause .= General::getSqlConditionClause('c.name',$svalue);
					break;
                case 'dept_name':
                    $clause .= General::getSqlConditionClause('e.name',$svalue);
                    break;
			}
		}
		
		$order = "";
		if (!empty($this->orderField)) {
			$order .= " order by ".$this->orderField." ";
			if ($this->orderType=='D') $order .= "desc ";
		}else{
            $order = " order by id desc";
        }

		$sql = $sql2.$clause;
		$this->totalRow = Yii::app()->db->createCommand($sql)->queryScalar();
		
		$sql = $sql1.$clause.$order;
		$sql = $this->sqlWithPageCriteria($sql, $this->pageNum);
		$records = Yii::app()->db->createCommand($sql)->queryAll();

		$this->attr = array();
		if (count($records) > 0) {
            $supportApplyList = new SupportApplyList();
			foreach ($records as $k=>$record) {
                $arr = $this->getStatus($record);
				$this->attr[] = array(
					'id'=>$record['id'],
					'support_code'=>$record['support_code'],
					'apply_city'=>$record['city_name'],
					'apply_date'=>$record['apply_date'],
					'apply_end_date'=>$record['apply_end_date'],
					'name'=>$record['name'],
					'dept_name'=>$record['dept_name'],
					'review_sum'=>$record['review_sum'],
                    'apply_type'=>$supportApplyList->getApplyTypeList($record['apply_type'],true),
                    'service_type'=>$supportApplyList->getServiceList($record['service_type'],true),
                    'privilege'=>$supportApplyList->getPrivilegeList($record['privilege'],true),
					'status'=>$arr['status'],
					'style'=>$arr['style'],
				);
			}
		}
		$session = Yii::app()->session;
		$session['supportSearch_01'] = $this->getCriteria();
		return true;
	}

    public function getStatus($arr){
	    $list =SupportApplyList::getStatusList();
	    if(key_exists($arr["status_type"],$list)){
            return $list[$arr["status_type"]];
        }else{
            return array(
                "status"=>Yii::t("contract","not sent"),
                "style"=>"text-danger"
            );//未發送
        }
    }
}
