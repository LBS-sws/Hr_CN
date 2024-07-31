<?php

/**
 * UserForm class.
 * UserForm is the data structure for keeping
 * user form data. It is used by the 'user' action of 'SiteController'.
 */
class HistoryForm extends StaffForm
{
    public $update_remark;
    public $effect_time;
    public $operation;
    public $change_city;
    public $docType = 'EMPLOYEE';

    public $opr_type;
    public $leave_time;
    public $leave_reason;

    public function getRequiredList(){
        $list = parent::getRequiredList();
        $list = array_merge($list,array("entry_time","emergency_user","emergency_phone"));
        return $list;
    }

    public function getMyAttrEx(){
        $list = parent::getMyAttr();
        $list["employee_id"]=3;
        $list["update_remark"]=1;
        $list["change_city"]=1;
        $list["effect_time"]=2;
        $list["operation"]=1;
        $list["opr_type"]=1;
        $list["leave_time"]=2;
        $list["leave_reason"]=1;
        return $list;
    }
	/**
	 * Declares the validation rules.
	 */
	public function rulesEx()
	{
        $requiredList = $this->getRequiredList();
        $requiredStr = implode(",",$requiredList);
		return array(
            array('employee_id,jj_card,social_code,update_remark,effect_time,operation,change_city','safe'),
            array($requiredStr,'required'),

            array('effect_time','required',"on"=>"change"),
			array('opr_type','required',"on"=>"change"),
			array('leave_time','required',"on"=>"departure"),
			array('leave_reason','required',"on"=>"departure"),

			array('code','validateCode'),
			array('name','validateName'),
            array('wage','validateWage','on'=>"audit"),//由於工資有些用戶沒有權限

			array('end_time','validateEndTime'),
			array('test_type','validateTestType'),
            array('year_day', 'validateYearDay'),
            array('office_id','companyOffice',"on"=>"departure"),
            //array('employee_id', 'validateSign'),//2023/01/16不需要此驗證
		);
	}

    public function companyOffice($attribute, $params){
	    $this->office_id=0;
    }

    public function validateSign($attribute, $params){
	    if(!in_array($this->scenario,array("departure","update"))){
            $rows = Yii::app()->db->createCommand()->select("a.status_type")->from("hr_sign_contract a")
                ->where("a.employee_id=:id and a.status_type IN (0,1,2,4) ",array(':id'=>$this->employee_id))->queryRow();
            if($rows){
                $message = "該員工有未簽署的合同，無法變更";
                $this->addError($attribute,$message);
            }
        }
    }

    public function validateID($attribute, $params){
        $allow_city = Yii::app()->user->city_allow();
        $row = Yii::app()->db->createCommand()->select("id,city,code")->from("hr_employee")
            ->where("id=:id and city in ({$allow_city})", array(':id'=>$this->employee_id))
            ->queryRow();
        if($row){
            $this->city = $row["city"];
            $this->code = $row["code"];
        }else{
            $this->addError($attribute,"员工不存在，请刷新重试");
        }
    }

    public function validateCode($attribute, $params){
        $rows = Yii::app()->db->createCommand()->select()->from("hr_employee")
            ->where('id!=:id and code=:code ', array(':id'=>$this->employee_id,':code'=>$this->code))->queryAll();
        if (count($rows) > 0){
            $message = Yii::t('contract','Employee Code'). Yii::t('contract',' can not repeat');
            $this->addError($attribute,$message);
        }
    }

	public function validateName($attribute, $params){
/*        $rows = Yii::app()->db->createCommand()->select()->from("hr_employee")
            ->where('id!=:id and name=:name ', array(':id'=>$this->employee_id,':name'=>$this->name))->queryAll();
        if (count($rows) > 0){
            $message = Yii::t('contract','Employee Name'). Yii::t('contract',' can not repeat');
            $this->addError($attribute,$message);
        }*/
    }

    //自動變化表頭
    public function setFormTitle(){
        switch ($this->scenario){
            case "update":
                return Yii::t("contract","Staff Update");
            case "change":
                return Yii::t("contract","Staff Change");
            case "departure":
                return Yii::t("contract","Staff Departure");
            case "view":
                return Yii::t("contract","Staff View");
            default:
                return Yii::t("contract","Staff View");
        }
    }

    //驗證是否有變更記錄
    public function validateStaff($index,$type){
        $arr = array("update","change","departure");
        $city = Yii::app()->user->city();
        $city_allow = Yii::app()->user->city_allow();
        if(in_array($type,$arr)){
            $count = Yii::app()->db->createCommand()->select("count(id)")->from("hr_employee_operate")
                ->where("employee_id=:id and city in ($city_allow)  and finish=0", array(':id'=>$index))->queryScalar();
            if($count>0){
                return false;
            }
        }
        return true;
    }

    public function newData($index){
        $suffix = Yii::app()->params['envSuffix'];
        $allow_city = Yii::app()->user->city_allow();
        $row = Yii::app()->db->createCommand()->select("*,docman$suffix.countdoc('EMPLOY',id) as employdoc")->from("hr_employee")
            ->where("id=:id and city in ({$allow_city})", array(':id'=>$index))->queryRow();
        if ($row){
            $this->employee_id = $row['id'];
            $this->copyAttachment();
            $arr = $this->getMyAttr();
            foreach ($arr as $key => $type){
                switch ($type){
                    case 1://原值
                        $value = $row[$key];
                        $value = $value===""?null:$value;
                        $this->$key = $value;
                        break;
                    case 2://日期
                        $this->$key = empty($row[$key])?null:General::toDate($row[$key]);
                        break;
                    case 3://数字
                        $this->$key = $row[$key]===null?null:floatval($row[$key]);
                        break;
                    default:
                }
            }

            $this->change_city = empty($this->change_city)?$this->city:$this->change_city;
            $this->staff_status = 1;
            $this->id = "";
            return true;
        }
        return false;
    }

    public function retrieveData($index){
        $suffix = Yii::app()->params['envSuffix'];
        $allow_city = Yii::app()->user->city_allow();
        $row = Yii::app()->db->createCommand()->select("*,docman$suffix.countdoc('EMPLOYEE',id) as employeedoc")->from("hr_employee_operate")
            ->where("id=:id", array(':id'=>$index))->queryRow();
        if ($row){
            $this->no_of_attm['employee'] = $row['employeedoc'];
            $arr = $this->getMyAttrEx();
            foreach ($arr as $key => $type){
                switch ($type){
                    case 1://原值
                        $value = $row[$key];
                        $value = $value===""?null:$value;
                        $this->$key = $value;
                        break;
                    case 2://日期
                        $this->$key = empty($row[$key])?null:General::toDate($row[$key]);
                        break;
                    case 3://数字
                        $this->$key = $row[$key]===null?null:floatval($row[$key]);
                        break;
                    default:
                }
            }
            $this->setScenario($row['operation']);
            return true;
        }
        return false;
    }

	//刪除驗證
    public function validateDelete(){
        $rows = Yii::app()->db->createCommand()->select("id")->from("hr_employee_operate")
            ->where('id=:id and staff_status in (1,3)', array(':id'=>$this->id))->queryRow();
        if($rows){
            return true;
        }else{
            return false;
        }
    }

	//刪除草稿
    public function deleteHistory(){
        Yii::app()->db->createCommand()->delete('hr_employee_operate', 'id=:id', array(':id'=>$this->id));
    }

    protected function getSaveList() {
        $list=array();
        $arr = array(
            "group_type"=>3,"office_id"=>3,"code"=>1,"name"=>1,
            "staff_id"=>1,"company_id"=>3,"contract_id"=>3,"address"=>1,"address_code"=>1,
            "contact_address"=>1,"contact_address_code"=>1,"phone"=>1,"phone2"=>1,"user_card"=>1,
            "department"=>1,"position"=>1,"wage"=>1,"city"=>1,
            "start_time"=>2,"end_time"=>2,"test_type"=>3,"test_start_time"=>2,
            "sex"=>1,"test_end_time"=>2,"test_wage"=>1,"staff_status"=>3,
            "entry_time"=>2,"age"=>1,"birth_time"=>2,"health"=>1,
            "education"=>1,"wechat"=>1,"recommend_user"=>1,"urgency_card"=>1,
            "experience"=>1,"english"=>1,"technology"=>1,"other"=>1,"year_day"=>1,
            "email"=>1,"remark"=>1,"image_user"=>1,"image_code"=>1,"image_work"=>1,
            "image_other"=>1,"fix_time"=>1,"code_old"=>1,"test_length"=>1,"staff_type"=>1,
            "staff_leader"=>1,"nation"=>1,"household"=>1,"empoyment_code"=>1,
            "social_code"=>1,"jj_card"=>1,"user_card_date"=>1,"emergency_user"=>1,"emergency_phone"=>1,
            "leave_time"=>2,"leave_reason"=>1,"work_area"=>1,"bank_type"=>3,"bank_number"=>1,
        );
        foreach ($arr as $key=>$type){
            $value=$this->$key;
            switch ($type){
                case 1://原值
                    $value = $value===""?null:$value;
                    break;
                case 2://日期
                    $value = empty($value)?null:General::toDate($value);
                    break;
                case 3://数字
                    $value = $value===""?null:floatval($value);
                    break;
            }
            $this->$key=$value;
            $list[$key] = $value;
        }
        if(!$this->validateWageInput()){//工资栏位的修改
            unset($list["wage"]);//没有权限不允许修改工资
        }
        return $list;
    }
	
	public function saveData()
	{
        $uid = Yii::app()->user->id;
        $row = self::getSaveList();
        $row['lcu'] = $uid;
        $row['lcd'] = date("Y-m-d H:i:s");
        if($this->audit){
            $row['staff_status'] = 2;
        }else{
            $row['staff_status'] = 1;
        }
        $row['ject_remark'] = "";
        $row['operation'] = $this->scenario;
        $row['effect_time'] = $this->effect_time;
        $row['opr_type'] = $this->opr_type;
        $row['employee_id'] = $this->employee_id;
        $row['update_remark'] = $this->update_remark;
        $row['change_city'] = $this->change_city;
        $row['attachment'] = 0;//後期修改（員工合同過期后是否已發送郵件 0：未發送  1：已發送）
        if($this->scenario == "view" && $this->staff_status == 3){
            unset($row['operation']);
            Yii::app()->db->createCommand()->update('hr_employee_operate', $row, 'id=:id', array(':id'=>$this->id));
            $row['operation'] = "Again Audit";//再次審核
            $id = "";
        }else if (empty($this->id)){
            $connection = Yii::app()->db;
            $connection->createCommand()->insert('hr_employee_operate', $row);
            $id = $connection->getLastInsertID();
            $this->id = $id;
            $this->updateDocman($connection,'EMPLOYEE');
            //複製員工的附件
            $this->copyAttachment();
        }else{
            Yii::app()->db->createCommand()->update('hr_employee_operate', $row, 'id=:id', array(':id'=>$this->id));
            $id = $this->id;
        }
        if(!$this->audit){ //草稿不生成記錄
            return true;
        }
        $his_arr =  array(
            "employee_id"=>$this->employee_id,
            "history_id"=>$id,
            "remark"=>"",
            "lcu"=>$row['lcu'],
            "lcd"=>$row['lcd'],
        );
        if($row['operation'] =="change"){
            $his_arr["status"] = $row['opr_type'];
            $num = Yii::app()->db->createCommand()->select("count('id')")->from("hr_employee_history")
                ->where('employee_id=:employee_id and status="contract"',array(":employee_id"=>$this->employee_id))->queryScalar();
            if($num > 0 && $row['opr_type'] == "contract"){
                $num++;
                $his_arr["num"] = " - ".$num;
            }
        }else{
            $his_arr["status"] = $row['operation'];
        }
        //記錄
        Yii::app()->db->createCommand()->insert('hr_employee_history',$his_arr);

        //發送郵件
        $this->sendEmail($row,$his_arr);
	}


	//發送郵件
    protected function sendEmail($row,$his_arr){
        if($row){
            $description=Yii::t("contract",$his_arr["status"])." - ".$row["name"];
            $subject=Yii::t("contract",$his_arr["status"])." - ".$row["name"];
            $message="<p>员工编号：".$row["code"]."</p>";
            $message.="<p>员工姓名：".$row["name"]."</p>";
            $message.="<p>员工所在城市：".CGeneral::getCityName($row["city"])."</p>";
            $message.="<p>员工职位：".DeptForm::getDeptToId($row["position"])."</p>";
            $message.="<p>员工合同归属：".StaffFun::getCompanyNameToID($row["staff_id"])."</p>";
            $message.="<p>员工归属：".StaffFun::getCompanyNameToID($row["company_id"])."</p>";
            $message.="<p>要求审核日期：".date('Y-m-d H:i:s')."</p>";
            $message.="<p>操作备注：".$row["update_remark"]."</p>";
            $email = new Email($subject,$message,$description);
            //$email->addEmailToPrefix("ZG02");
            $email->addEmailToPrefixAndCity("ZG02",$row["city"]);
            $email->sent();
        }
    }

    protected function updateDocman(&$connection, $doctype) {
        $docidx = strtolower($doctype);
        if ($this->docMasterId[$docidx] > 0) {
            $docman = new DocMan($doctype,$this->id,get_class($this));
            $docman->masterId = $this->docMasterId[$docidx];
            $docman->updateDocId($connection, $this->docMasterId[$docidx]);
        }
    }

//複製員工的附件
    public function copyAttachment(){
        $connection = Yii::app()->db;
        $uid = Yii::app()->user->id;
        $suffix = Yii::app()->params['envSuffix'];
        $sql="SELECT b.lcd,a.id,b.display_name,b.phy_file_name,b.phy_path_name,b.file_type,b.remove,b.archive FROM docman$suffix.dm_master a,docman$suffix.dm_file b WHERE a.id = b.mast_id AND a.doc_type_code='EMPLOY' AND a.doc_id=".$this->employee_id;
        $attachment_old = $connection->createCommand($sql)->queryAll();
        if($attachment_old){//如果有附件
            $connection->createCommand()->insert("docman$suffix.dm_master", array(
                'doc_type_code'=>'EMPLOYEE',
                'doc_id'=>0,
                'lcu'=>$uid,
            ));
            $innerId = $connection->getLastInsertID();
            $this->docMasterId['employee']=$innerId;
            $this->no_of_attm['employee']=count($attachment_old);
            foreach ($attachment_old as $attachment){
                $arr = $attachment;
                unset($arr["id"]);
                $arr["mast_id"]=$innerId;
                $connection->createCommand()->insert("docman$suffix.dm_file", $arr);
            }
        }
    }

    //根據歷史記錄的id獲取員工歷史信息
    public static function getStaffToHistoryId($index){
        $rows = Yii::app()->db->createCommand()->select()->from("hr_employee_operate")
            ->where('id=:id', array(':id'=>$index))->queryRow();
        if($rows){
            return $rows;
        }else{
            return array();
        }
    }
    public function readonly(){
        return $this->scenario=='view'||!in_array($this->staff_status,array(1,3));
    }
}
