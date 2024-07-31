<?php

class TemplateEmployeeList extends CListPageModel
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
            'tem_name'=>Yii::t('contract','template name'),
            'review_type'=>Yii::t('contract','review type'),
            'department'=>Yii::t("contract","Department"),
            'audit_name'=>Yii::t("contract","reviewAllot manager"),
		);
	}

	public function retrieveDataByPage($pageNum=1)
	{
		$suffix = Yii::app()->params['envSuffix'];
		$city = Yii::app()->user->city();
        $city_allow = Yii::app()->user->city_allow();
        //$expr_sql = " and (b.year=$this->year or b.year is null) and (b.year_type=$this->year_type or b.year_type is null)";
		$sql1 = "select a.id,f.name as ment_name,a.name,a.code,a.phone,a.city,a.entry_time,c.name as company_name,d.name as dept_name ,d.review_type 
                from hr_employee a 
                LEFT JOIN hr_company c ON a.company_id = c.id
                LEFT JOIN hr_dept d ON a.position = d.id
                LEFT JOIN hr_dept f ON a.department = f.id
                where a.city IN ($city_allow) AND d.review_type IN (1,2,3,4) AND a.staff_status = 0 
			";
		$sql2 = "select count(*) from hr_employee a 
                LEFT JOIN hr_company c ON a.company_id = c.id
                LEFT JOIN hr_dept d ON a.position = d.id
                LEFT JOIN hr_dept f ON a.department = f.id
                where a.city IN ($city_allow) AND d.review_type IN (1,2,3,4) AND a.staff_status = 0 
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
                case 'department':
                    $clause .= General::getSqlConditionClause('f.name',$svalue);
                    break;
                case 'position':
                    $clause .= General::getSqlConditionClause('d.name',$svalue);
                    break;
                case 'city_name':
                    $clause .= ' and a.city in '.WordForm::getCityCodeSqlLikeName($svalue);
                    break;
                case 'status':
                    $clause .= $this->searchStatus($svalue);
                    break;
			}
		}
		
		$order = "";
		if (!empty($this->orderField)) {
		    if($this->orderField == "status_type"||$this->orderField == "tem_name"){
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
		    $reviewTypeList = DeptForm::getReviewType();
			foreach ($records as $k=>$record) {
                $arr = $this->resetStatus($record);
				$this->attr[] = array(
					'id'=>$record['id'],
					'name'=>$record['name'],
					'code'=>$record['code'],
					'position'=>$record['dept_name'],
					'company_id'=>$record['company_name'],
					'phone'=>$record['phone'],
					'tem_name'=>$record['tem_name'],
                    'department'=>$record['ment_name'],
					'review_type'=>key_exists($record["review_type"],$reviewTypeList)?$reviewTypeList[$record["review_type"]]:$record["review_type"],
					'status'=>$arr["status"],
					'style'=>$arr["style"],
                    'city'=>CGeneral::getCityName($record["city"]),
                    'entry_time'=>$record["entry_time"],
                    'audit_name'=>$record["audit_name"],
				);
			}
		}
		$session = Yii::app()->session;
		$session['templateEmployee_01'] = $this->getCriteria();
		return true;
	}

    private function searchStatus($str){
        if($str === ""){
            return "";
        }
        $city_allow = Yii::app()->user->city_allow();
        $idList = array();
        if($str === Yii::t("contract","undistributed")||$str === "未"){
            $rows = Yii::app()->db->createCommand()->select("a.employee_id")->from("hr_template_employee a")
                ->leftJoin("hr_employee b","a.employee_id = b.id")
                ->where(" b.city IN ($city_allow)")->queryAll();
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
        }elseif($str === Yii::t("contract","allocated")||$str === "已"){
            $rows = Yii::app()->db->createCommand()->select("a.employee_id")->from("hr_template_employee a")
                ->leftJoin("hr_employee b","a.employee_id = b.id")
                ->where(" b.city IN ($city_allow)")->queryAll();
            if($rows){
                foreach ($rows as $row){
                    if(!in_array($row["employee_id"],$idList)){
                        $idList[] = $row["employee_id"];
                    }
                }
            }
            if(!empty($idList)){
                return " and a.id in (".implode(",",$idList).")";
            }
        }
        return " and a.id=0";
    }

    protected function orderStatusType(){
        if ($this->orderType=='D'){
            $order_type = "desc";
        }else{
            $order_type = "asc";
        }
        if($this->orderField == "tem_name"){
            $orderField = "c.tem_name";
        }else{
            $orderField = "b.city";
        }
        $city_allow = Yii::app()->user->city_allow();
        $rows = Yii::app()->db->createCommand()
            ->select("a.employee_id")
            ->from("hr_template_employee a")
            ->leftJoin("hr_employee b","a.employee_id = b.id")
            ->leftJoin("hr_template c","a.tem_id = c.id")
            ->where(" b.city IN ($city_allow)")->order("$orderField $order_type")->queryAll();
        if($rows){
            $rows = implode(",",array_column($rows,"employee_id"));
            return " order by find_in_set(a.id,'$rows') $order_type,a.name $order_type";
        }else{
            return " order by a.name $order_type ";
        }
    }

	protected function resetStatus(&$record){
	    //,b.status_type,b.year,b.year_type,b.id as review_id
        $row = Yii::app()->db->createCommand()->select("b.tem_name,a.id_list")
            ->from("hr_template_employee a")
            ->leftJoin("hr_template b","a.tem_id = b.id")
            ->where("a.employee_id=:id",array(":id"=>$record["id"]))->queryRow();
        if($row){
            //[{"employee_id":"13","num":"100","employee_name":"\u6d4b\u8bd513"}]
            $json = json_decode($row["id_list"],true);
            $record["tem_name"] = $row["tem_name"];
            $record["audit_name"] = array();
            if(!empty($json)){
                foreach ($json as $list){
                    $staff = Yii::app()->db->createCommand()
                        ->select("code,name,staff_status")
                        ->from("hr_employee")
                        ->where("id=:id",array(":id"=>$list["employee_id"]))->queryRow();
                    if($staff){
                        if($staff["staff_status"]==-1){
                            $record["audit_name"][]="<span class='text-danger'>".$staff["name"]." - 已离职"." ({$list['num']}%)</span>";
                        }else{
                            $record["audit_name"][]="<span>".$staff["name"]." ({$list['num']}%)</span>";
                        }
                    }
                }
            }
        }else{
            $record["tem_name"] = "";
            $record["audit_name"] = array();
        }
        $record["audit_name"] = implode("<br/>",$record["audit_name"]);

        return $this->getTemplateNameStatuts($record["tem_name"]);
    }

    protected function getTemplateNameStatuts($str){
	    if($str===""){
            return array(
                "status"=>Yii::t("contract","undistributed"),
                "style"=>"text-danger"
            );//未分配
        }else{
            return array(
                "status"=>Yii::t("contract","allocated"),
                "style"=>""
            );//已分配
        }
    }
}
