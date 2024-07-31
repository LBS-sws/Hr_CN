<?php

class SupportEmailList extends CListPageModel
{

    public $city="ZY";


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
            'department'=>Yii::t("contract","Department"),
            'support_city'=>Yii::t('contract','support city'),
			'status'=>Yii::t('contract','Status'),
		);
	}

	public function retrieveDataByPage($pageNum=1)
	{
		$suffix = Yii::app()->params['envSuffix'];
        //$dateTime = date("Y/m/d",strtotime("$dateTime - 3 month"));
        //$expr_sql = " and (b.year=$this->year or b.year is null) and (b.year_type=$this->year_type or b.year_type is null)";
		$sql1 = "select a.id,a.name,a.code,a.phone,d.name as dept_name,d.review_type ,e.name as ment_name 
                from hr_employee a 
                LEFT JOIN hr_company c ON a.company_id = c.id
                LEFT JOIN hr_dept d ON a.position = d.id
                LEFT JOIN hr_dept e ON a.department = e.id
                where a.city ='$this->city' AND a.staff_status = 0 
			";
		$sql2 = "select count(*) from hr_employee a 
                LEFT JOIN hr_company c ON a.company_id = c.id
                LEFT JOIN hr_dept d ON a.position = d.id
                LEFT JOIN hr_dept e ON a.department = e.id
                where a.city ='$this->city' AND a.staff_status = 0 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'name':
					$clause .= General::getSqlConditionClause('a.name',$svalue);
					break;
				case 'code':
					$clause .= General::getSqlConditionClause('a.code',$svalue);
					break;
				case 'phone':
					$clause .= General::getSqlConditionClause('a.phone',$svalue);
					break;
                case 'position':
                    $clause .= General::getSqlConditionClause('d.name',$svalue);
                    break;
                case 'department':
                    $clause .= General::getSqlConditionClause('e.name',$svalue);
                    break;
                case 'status':
                    $clause .= $this->searchStatus($svalue);//失效，暫時不適用
                    break;
			}
		}
		
		$order = "";
		if (!empty($this->orderField)) {
		    if($this->orderField == "status"||$this->orderField == "support_city"){
                $order .=$this->orderStatusType();
            }else{
                $order .= " order by ".$this->orderField." ";
                if ($this->orderType=='D') $order .= "desc ";
            }
		}else{
            $order .= " order by a.id asc ";
        }

		$sql = $sql2.$clause;
		$this->totalRow = Yii::app()->db->createCommand($sql)->queryScalar();
		
		$sql = $sql1.$clause.$order;
		$sql = $this->sqlWithPageCriteria($sql, $this->pageNum);
		$records = Yii::app()->db->createCommand($sql)->queryAll();
		
		$list = array();
		$this->attr = array();
		if (count($records) > 0) {
            $time = date("Y-m-d");
            $reviewTypeList = DeptForm::getReviewType();
			foreach ($records as $k=>$record) {
                $this->resetStatus($record);
				$this->attr[] = array(
					'id'=>$record['id'],
					'name'=>$record['name'],
					'code'=>$record['code'],
					'position'=>$record['dept_name'],
                    'department'=>$record['ment_name'],
					'phone'=>$record['phone'],
					'status'=>$record["status"],
					'style'=>$record["style"],
                    'support_city'=>$record["support_city"],
				);
			}
		}
		$session = Yii::app()->session;
		$session['supportEmail_01'] = $this->getCriteria();
		return true;
	}

    protected function orderStatusType(){
        if ($this->orderType=='D'){
            $order_type = "desc";
        }else{
            $order_type = "asc";
        }
        $rows = Yii::app()->db->createCommand()->select("employee_id")
            ->from("hr_apply_support_email")->order("support_city $order_type")->queryAll();
        if($rows){
            $rows = implode(",",array_column($rows,"employee_id"));
            return " order by find_in_set(a.id,'$rows') $order_type,a.name $order_type";
        }else{
            return " order by a.name $order_type ";
        }
    }

	protected function resetStatus(&$record){
        $rows = Yii::app()->db->createCommand()->select("support_city")
            ->from("hr_apply_support_email")
            ->where("employee_id=:id",array(":id"=>$record["id"]))->queryRow();
        if($rows){
            $record["status"] = Yii::t("contract","allocated");
            $record["support_city"] = CGeneral::getCityName($rows["support_city"]);
            $record["style"] = "text-primary";
        }else{
            $record["status"] = Yii::t("contract","undistributed");
            $record["support_city"] = "";
            $record["style"] = "";
        }
    }

    private function searchStatus($str){
        if($str === ""){
            return "";
        }
        $arr = array(
            1=>Yii::t("contract","allocated"),
            2=>Yii::t("contract","undistributed")
        );
        $idList = array();
        if($str === Yii::t("contract","none review")||$str === "未"){
            $rows = Yii::app()->db->createCommand()->select("employee_id")->from("hr_review")
                ->where("year = :year and year_type = :year_type",
                    array(":year"=>$this->year,":year_type"=>$this->year_type)
                )->queryAll();
            if($rows){
                foreach ($rows as $row){
                    if(!in_array($row["employee_id"],$idList)){
                        $idList[] = $row["employee_id"];
                    }
                }
            }
            if(!empty($idList)){
                return " and a.id not in (".implode(",",$idList).")";
            }
        }else{
            foreach ($arr as $key =>$item){
                if (strpos($item,$str)!==false){
                    $rows = Yii::app()->db->createCommand()->select("employee_id")->from("hr_review")
                        ->where("status_type=:status_type and year = :year and year_type = :year_type",
                            array(":status_type"=>$key,":year"=>$this->year,":year_type"=>$this->year_type)
                        )->queryAll();
                    if($rows){
                        foreach ($rows as $row){
                            if(!in_array($row["employee_id"],$idList)){
                                $idList[] = $row["employee_id"];
                            }
                        }
                    }
                }
            }
            if(!empty($idList)){
                return " and a.id in (".implode(",",$idList).")";
            }
        }
        return " and a.id=0";
    }
}
