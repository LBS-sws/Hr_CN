<?php

class ReviewHandleList extends CListPageModel
{

    public $employee_id;


	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(	
			'id'=>Yii::t('contract','ID'),
			'name'=>Yii::t('contract','Employee Name'),
			'code'=>Yii::t('contract','Employee Code'),
			'phone'=>Yii::t('contract','Employee Phone'),
			'position'=>Yii::t('contract','Position'),
			'company_id'=>Yii::t('contract','Company Name'),
			'contract_id'=>Yii::t('contract','Contract Name'),
			'status'=>Yii::t('contract','Status'),
			'city'=>Yii::t('contract','City'),
            'city_name'=>Yii::t('contract','City'),
            'entry_time'=>Yii::t('contract','Entry Time'),
            'year'=>Yii::t('contract','what year'),
            'year_type'=>Yii::t('contract','monthly'),
            'department'=>Yii::t("contract","Department"),
		);
	}

    //驗證賬號是否綁定員工
    public function validateEmployee(){
        $uid = Yii::app()->user->id;
        $rows = Yii::app()->db->createCommand()->select("employee_id,employee_name")->from("hr_binding")
            ->where('user_id=:user_id',
                array(':user_id'=>$uid))->queryRow();
        if ($rows){
            $this->employee_id = $rows["employee_id"];
            return $rows["employee_id"];
        }
        return false;
    }

	public function retrieveDataByPage($pageNum=1)
	{
		$suffix = Yii::app()->params['envSuffix'];
		$city = Yii::app()->user->city();
        $city_allow = Yii::app()->user->city_allow();
        //FIND_IN_SET
        $expr_sql = " and a.handle_id = $this->employee_id and a.status_type in (1,4)";
		$sql1 = "select docman$suffix.countdoc('REVIEW',b.id) as reviewdoc,c.name,f.name as ment_name,c.code,c.phone,c.city,c.entry_time,d.name as company_name,e.name as dept_name,a.status_type,b.year,b.year_type,a.id 
                from hr_review_h a 
                LEFT JOIN hr_review b ON a.review_id = b.id
                LEFT JOIN hr_employee c ON c.id = b.employee_id
                LEFT JOIN hr_company d ON c.company_id = d.id
                LEFT JOIN hr_dept e ON c.position = e.id
                LEFT JOIN hr_dept f ON c.department = f.id
                where c.staff_status = 0 $expr_sql
			";
		$sql2 = "select count(*) from hr_review_h a 
                LEFT JOIN hr_review b ON a.review_id = b.id
                LEFT JOIN hr_employee c ON c.id = b.employee_id
                LEFT JOIN hr_company d ON c.company_id = d.id
                LEFT JOIN hr_dept e ON c.position = e.id
                LEFT JOIN hr_dept f ON c.department = f.id
                where c.staff_status = 0 $expr_sql
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'name':
					$clause .= General::getSqlConditionClause('c.name',$svalue);
					break;
				case 'code':
					$clause .= General::getSqlConditionClause('c.code',$svalue);
					break;
				case 'phone':
					$clause .= General::getSqlConditionClause('c.phone',$svalue);
					break;
                case 'department':
                    $clause .= General::getSqlConditionClause('f.name',$svalue);
                    break;
                case 'position':
                    $clause .= General::getSqlConditionClause('e.name',$svalue);
                    break;
                case 'city_name':
                    $clause .= ' and c.city in '.WordForm::getCityCodeSqlLikeName($svalue);
                    break;
			}
		}
		
		$order = "";
		if (!empty($this->orderField)) {
            $order .= " order by ".$this->orderField." ";
            if ($this->orderType=='D') $order .= "desc ";
		}else{
            $order .= " order by a.id asc ";
        }

		$sql = $sql2.$clause;
		$this->totalRow = Yii::app()->db->createCommand($sql)->queryScalar();
		
		$sql = $sql1.$clause.$order;
		$sql = $this->sqlWithPageCriteria($sql, $this->pageNum);
		$records = Yii::app()->db->createCommand($sql)->queryAll();

		$this->attr = array();
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
                $arr = $this->getReviewStatuts($record["status_type"]);
				$this->attr[] = array(
					'id'=>$record['id'],
					'name'=>$record['name'],
					'year'=>$record['year'],
					'year_type'=>ReviewAllotList::getYearTypeList($record['year_type'],$record['year']),
					'code'=>$record['code'],
					'position'=>$record['dept_name'],
					'company_id'=>$record['company_name'],
					'department'=>$record['ment_name'],
					'reviewdoc'=>$record['reviewdoc'],
					'phone'=>$record['phone'],
					'status'=>$arr["status"],
					'style'=>$arr["style"],
                    'city'=>CGeneral::getCityName($record["city"]),
                    'entry_time'=>$record["entry_time"],
				);
			}
		}
		$session = Yii::app()->session;
		$session['reviewHandle_01'] = $this->getCriteria();
		return true;
	}

    public function getReviewStatuts($str){
        switch ($str){
            case 1:
                return array(
                    "status"=>Yii::t("contract","none review"),
                    "style"=>"text-danger"
                );//未評核
            case 4:
                return array(
                    "status"=>Yii::t("contract","Draft"),
                    "style"=>""
                );//評核完成
                break;
        }
    }
}
