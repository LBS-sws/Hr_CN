<?php

class LeaveList extends CListPageModel
{
    public $employee_id;//員工id
    public $searchTimeStart;//開始日期
    public $searchTimeEnd;//結束日期
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(	
			'leave_code'=>Yii::t('fete','Leave Code'),
			'vacation_id'=>Yii::t('fete','Leave Type'),
			'employee_name'=>Yii::t('contract','Employee Name'),
            'employee_code'=>Yii::t('contract','Employee Code'),
			'start_time'=>Yii::t('contract','Start Time'),
			'end_time'=>Yii::t('contract','End Time'),
			'log_time'=>Yii::t('fete','Log Date'),
			'status'=>Yii::t('contract','Status'),
            'city'=>Yii::t('contract','City'),
            'city_name'=>Yii::t('contract','City'),
            'lcd'=>Yii::t('fete','apply for time'),
            'leavedoc'=>Yii::t('contract','Attachment'),
		);
	}

    public function rules()
    {
        return array(
            array('attr, pageNum, noOfItem, totalRow, searchField, searchValue, orderField, orderType, searchTimeStart, searchTimeEnd','safe',),
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
            return true;
        }
        return false;
    }

	public function retrieveDataByPage($pageNum=1)
	{
        $suffix = Yii::app()->params['envSuffix'];
        $uid = Yii::app()->user->id;
        $city_allow = Yii::app()->user->city_allow();
        $employee_id = $this->employee_id;
        $auditSql = "";
        foreach (AppointSetForm::getZIndexForUser() as $key=>$item){
            $auditSql.= empty($auditSql)?"":" or ";
            $auditSql.= "a.{$item}='$uid'";
        }
        $auditSql.=LookSetForm::getLookSqlForStr("a.employee_id");
        $manager = AuditConfigForm::getManager($employee_id);
        //,docman$suffix.countdoc('LEAVE',a.id) as leavedoc
        $masSql = Yii::app()->db->createCommand()->select("ew.id,max(df.lud) as file_lud")
            ->from("docman$suffix.dm_master dm")
            ->leftJoin("docman$suffix.dm_file df","df.mast_id = dm.id")
            ->leftJoin("hr_employee_leave ew","dm.doc_id = ew.id")
            ->leftJoin("hr_employee he","ew.employee_id = he.id")
            ->where("dm.doc_type_code='LEAVE' and ew.status=4 and he.city in($city_allow)")->group("ew.id")->getText();
		$sql1 = "select  a.*,(case when a.lud<=ifnull(ms.file_lud,0) THEN 1 ELSE 0 end) as leavedoc,f.name as vacation_name,b.name AS employee_name,b.code AS employee_code,b.city AS s_city 
              from hr_employee_leave a 
              LEFT JOIN hr_vacation f ON a.vacation_id = f.id 
              LEFT JOIN hr_employee b ON a.employee_id = b.id 
              LEFT JOIN hr_dept d ON b.position = d.id 
              LEFT JOIN ($masSql) ms ON ms.id = a.id 
              where a.id!=0 ";
		$sql2 = "select count(a.id)
				from hr_employee_leave a 
                LEFT JOIN hr_vacation f ON a.vacation_id = f.id 
				LEFT JOIN hr_employee b ON a.employee_id = b.id 
                LEFT JOIN hr_dept d ON b.position = d.id 
				where a.id!=0 ";
		if(!Yii::app()->user->validFunction('ZR04')){
            $sql1.=" and (d.manager <= {$manager['manager']} or a.z_index>=10)";
            $sql2.=" and (d.manager <= {$manager['manager']} or a.z_index>=10)";
        }
        if(Yii::app()->user->validFunction('ZR04')){
            $sql1.=" and ((b.city in($city_allow) and a.status !=0) or a.employee_id='$employee_id' or a.lcu='$uid' or {$auditSql}) ";
            $sql2.=" and ((b.city in($city_allow) and a.status !=0) or a.employee_id='$employee_id' or a.lcu='$uid' or {$auditSql}) ";
        }elseif($manager["manager"] == 1){
            $sql1.=" and ((b.city in($city_allow) and b.department='".$manager["department"]."' ";
            $sql2.=" and ((b.city in($city_allow) and b.department='".$manager["department"]."' ";
            if(!empty($manager["group_type"])){
                $sql1.=" and b.group_type='".$manager["group_type"]."' ";
                $sql2.=" and b.group_type='".$manager["group_type"]."' ";
            }
            $sql1.=" and a.status !=0) or a.employee_id='$employee_id' or {$auditSql}) ";
            $sql2.=" and a.status !=0) or a.employee_id='$employee_id' or {$auditSql}) ";
        }else{
            $sql1.=" and (a.employee_id='$employee_id' or a.lcu='$uid' or {$auditSql}) ";
            $sql2.=" and (a.employee_id='$employee_id' or a.lcu='$uid' or {$auditSql}) ";
        }
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'leave_code':
					$clause .= General::getSqlConditionClause('a.leave_code',$svalue);
					break;
				case 'vacation_id':
					$clause .= General::getSqlConditionClause('f.name',$svalue);
					break;
                case 'employee_name':
                    $clause .= General::getSqlConditionClause('b.name',$svalue);
                    break;
                case 'employee_code':
                    $clause .= General::getSqlConditionClause('b.code',$svalue);
                    break;
                case 'city_name':
                    $clause .= ' and b.city in '.WordForm::getCityCodeSqlLikeName($svalue);
                    break;
                case 'status':
                    $clause .= $this->searchToStatus($svalue);
                    break;
			}
		}
        if (!empty($this->searchTimeStart) && !empty($this->searchTimeStart)) {
            $svalue = str_replace("'","\'",$this->searchTimeStart);
            $clause .= " and a.start_time >='$svalue 00:00:00' ";
        }
        if (!empty($this->searchTimeEnd) && !empty($this->searchTimeEnd)) {
            $svalue = str_replace("'","\'",$this->searchTimeEnd);
            $clause .= " and a.start_time <='$svalue 23:59:59' ";
        }
		
		$order = "";
		if (!empty($this->orderField)) {
			$order .= " order by ".$this->orderField." ";
			if ($this->orderType=='D') $order .= "desc ";
            if($this->orderField=="leavedoc"){
                $order .= ",(case when a.status=4 then 10 else a.status end) desc ";
            }
		}else{
            $order .= " order by a.id desc ";
        }

		$sql = $sql2.$clause;
		$this->totalRow = Yii::app()->db->createCommand($sql)->queryScalar();
		
		$sql = $sql1.$clause.$order;
		$sql = $this->sqlWithPageCriteria($sql, $this->pageNum);
		$records = Yii::app()->db->createCommand($sql)->queryAll();

		$this->attr = array();
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
			    $colorList = self::statusToColor($record['status']);
				$this->attr[] = array(
					'id'=>$record['id'],
					//'leavedoc'=>LeaveList::docmanSearch("LEAVE",$record["id"],$record["lud"],$record['status'])?1:0,
					'leavedoc'=>$record['leavedoc'],
					'leave_code'=>$record['leave_code'],
					'employee_name'=>$record['employee_name'],
					'employee_code'=>$record['employee_code'],
					'vacation_id'=>$record['vacation_name'].LeaveList::getCodeForWorkLeave($record['id'],"leave"),
					'lcd'=>CGeneral::toDateTime($record['lcd']),
					'start_time'=>date("Y/m/d",strtotime($record['start_time'])),
					'end_time'=>date("Y/m/d",strtotime($record['end_time'])),
					'log_time'=>$record['log_time'].Yii::t("contract","day."),
					//'vacation_id'=>VacationForm::getVacationNameToId($record['vacation_id']),
					'status'=>$colorList["status"],
                    'city'=>CGeneral::getCityName($record["s_city"]),
					'style'=>$colorList["style"],
				);
			}
		}
		$session = Yii::app()->session;
		$session['leave_01'] = $this->getCriteria();
		return true;
	}

    public function getCriteria() {
        return array(
            'searchField'=>$this->searchField,
            'searchValue'=>$this->searchValue,
            'orderField'=>$this->orderField,
            'orderType'=>$this->orderType,
            'noOfItem'=>$this->noOfItem,
            'pageNum'=>$this->pageNum,
            'filter'=>$this->filter,
            'searchTimeStart'=>$this->searchTimeStart,
            'searchTimeEnd'=>$this->searchTimeEnd,
        );
    }

	//获取请假、加班的对应关联编号
    public static function getCodeForWorkLeave($id,$thisStr="leave",$bool=true){
        $returnList = array();
        $returnStr = "";
        if($thisStr=="leave"){
            $rows = Yii::app()->db->createCommand()->select("b.work_code")->from("hr_work_leave a")
                ->leftJoin("hr_employee_work b","a.work_id=b.id")
                ->where("a.leave_id=:id",array(":id"=>$id))->queryAll();
            if($rows){
                foreach ($rows as $row){
                    $returnList[] = $row["work_code"];
                }
            }
        }else{
            $rows = Yii::app()->db->createCommand()->select("b.leave_code")->from("hr_work_leave a")
                ->leftJoin("hr_employee_leave b","a.leave_id=b.id")
                ->where("a.work_id=:id",array(":id"=>$id))->queryAll();
            if($rows){
                foreach ($rows as $row){
                    $returnList[] = $row["leave_code"];
                }
            }
        }
        if(!empty($returnList)&&$bool){
            return "(".implode("、",$returnList).")";
        }
        return implode("、",$returnList);
    }

    //根據狀態獲取顏色
    public static function statusToColor($status){
        switch ($status){
            // text-danger
            case 0:
                return array(
                    "status"=>Yii::t("contract","Draft"),
                    "style"=>""
                );
            case 1:
                return array(
                    "status"=>Yii::t("contract","Sent, pending approval"),//已發送，等待審核
                    "style"=>" text-primary"
                );
            case 2:
                return array(
                    "status"=>Yii::t("contract","audit"),//審核通過
                    "style"=>" text-yellow"
                );
            case 3:
                return array(
                    "status"=>Yii::t("contract","Rejected"),//拒絕
                    "style"=>" text-danger"
                );
            case 4:
                return array(
                    "status"=>Yii::t("fete","approve"),//批准
                    "style"=>" text-green"
                );
            case 5:
                return array(
                    "status"=>Yii::t("contract","cancel"),//取消
                    "style"=>" text-aqua"
                );
        }
        return array(
            "status"=>"",
            "style"=>""
        );
    }

    private function searchToStatus($search){
        $arr = array(10);
        $list = array(
            Yii::t("contract","Draft"),
            Yii::t("contract","Sent, pending approval"),
            Yii::t("contract","audit"),
            Yii::t("contract","Rejected"),
            Yii::t("fete","approve"),
            Yii::t("contract","cancel")
        );
        foreach ($list as $key=>$status){
            if (strpos($status,$search)!==false){
                $arr[] = $key;
            }
        }
        if($search!=""){
            return " and a.status in (".implode(",",$arr).")";
        }else{
            return "";
        }
    }



    //請假、加班附件變更查詢
    public static function docmanSearch($docType,$id,$date,$status=1){
        if($status==4){
            $date = date("Y/m/d H:i:s",strtotime($date));
            $suffix = Yii::app()->params['envSuffix'];
            $rows = Yii::app()->db->createCommand()->select("b.lcd")->from("docman$suffix.dm_master a")
                ->leftJoin("docman$suffix.dm_file b","b.mast_id = a.id")
                ->where("a.doc_type_code='$docType' and a.doc_id = '$id' and date_format(b.lcd,'%Y/%m/%d %H:%i:%s') > '$date'")->queryRow();
            if($rows){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
}
