<?php

/**
 * UserForm class.
 * UserForm is the data structure for keeping
 * user form data. It is used by the 'user' action of 'SiteController'.
 */
class AuditHistoryForm extends StaffForm
{
    public $update_remark;
    public $effect_time;
    public $operation;
    public $change_city;

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
        return array(
            array('employee_id,jj_card,social_code,update_remark,effect_time,operation,change_city','safe'),
            array('ject_remark','required',"on"=>"reject"),
        );
    }

    public function validateID($attribute, $params){
        $allow_city = Yii::app()->user->city_allow();
        $row = Yii::app()->db->createCommand()->select("id,employee_id,table_type,city")->from("hr_employee_operate")
            ->where("id=:id and finish != 1", array(':id'=>$this->id))
            ->queryRow();
        if($row){
            $this->employee_id = $row["employee_id"];
            $this->table_type = $row["table_type"];
            if($this->table_type!=1){
                $this->change_city = $row["city"];
            }
        }else{
            $this->addError($attribute,"更改记录不存在，请刷新重试");
        }
    }

    //自動變化表頭
    public function setFormTitle(){
        return Yii::t("app","Employee Update Audit");
    }

    public function retrieveData($index){
        $suffix = Yii::app()->params['envSuffix'];
        $allow_city = Yii::app()->user->city_allow();
        $row = Yii::app()->db->createCommand()->select("*,docman$suffix.countdoc('EMPLOYEE',id) as employeedoc,docman$suffix.countdoc('EMPLOY',employee_id) as employdoc")->from("hr_employee_operate")
            ->where('id=:id and finish != 1', array(':id'=>$index))->queryRow();
        if ($row){
            $this->no_of_attm['employee'] = $row['employeedoc'];
            $this->no_of_attm['employ'] = $row['employdoc'];
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
                    case "birth_time"://年龄
                        $this->$key = isset($row["birth_time"])?StaffFun::getAgeForBirthDate($row["birth_time"]):floatval($row[$key]);
                        break;
                    default:
                }
            }
            return true;
        }
        return false;
    }

	public function saveData()
	{
        $uid = Yii::app()->user->id;
        $lud = date("Y-m-d H:i:s");
        $city = Yii::app()->user->city();
        $update_html="";
        switch ($this->scenario){
            case "audit"://通過
                $update_html = "<span class='text-success'>审核通过</span>";
                Yii::app()->db->createCommand()->update('hr_employee_operate', array(
                    'staff_status'=>4,
                    'city'=>$this->change_city,
                    'luu'=>$uid,
                    'lud'=>$lud,
                ), 'id=:id', array(':id'=>$this->id));
                break;
            case "reject"://拒絕
                $update_html = "<span class='text-danger'>审核被拒绝</span><br/>";
                $update_html.= "<span>{$this->ject_remark}</span>";
                Yii::app()->db->createCommand()->update('hr_employee_operate', array(
                    'staff_status'=>3,
                    'ject_remark'=>$this->ject_remark,
                    'luu'=>$uid,
                    'lud'=>$lud,
                ), 'id=:id', array(':id'=>$this->id));
                break;
        }
        $thisScenario = $this->getScenario();
        if($this->scenario == "audit"){
            $this->finish();
            $this->resetEmployeeStatusAndIndex();
        }

        if($this->table_type==1){
            //記錄
            Yii::app()->db->createCommand()->insert('hr_employee_history', array(
                "employee_id"=>$this->employee_id,
                "status"=>$thisScenario,
                "remark"=>$this->ject_remark,
                "lcu"=>$uid,
                "lcd"=>$lud,
            ));
        }else{
            Yii::app()->db->createCommand()->insert("hr_table_history", array(
                "table_name"=>"hr_employee",
                "table_id"=>$this->employee_id,
                "lcu"=>$uid,
                "update_type"=>1,
                "update_html"=>$update_html,
            ));
        }
        //發送郵件
        $this->sendEmail($thisScenario);
	}

    private function signContract($staffNew,$city_allow=0){
        $signedContractType = Yii::app()->db->createCommand()->select("set_value")->from("hr_setting")
            ->where("set_name='signedContractType' and set_city in ($city_allow)")->order("set_value asc")->queryScalar();
        if(empty($signedContractType)&&$this->opr_type == "contract"){
            $sign_type = 1;//續約
            $row = Yii::app()->db->createCommand()->select("retire")->from("hr_contract")
                ->where("id=:id",array(":id"=>$staffNew["contract_id"]))->queryRow();
            if($row){
                $sign_type = $row["retire"] == 1?2:1;
            }
            Yii::app()->db->createCommand()->update('hr_sign_contract', array(
                'history_id'=>$this->id
            ), 'employee_id=:id and history_id=0', array(':id'=>$this->employee_id));
            Yii::app()->db->createCommand()->insert('hr_sign_contract',array(
                'employee_id'=>$this->employee_id,
                'status_type'=>0,
                'history_id'=>0,
                'sign_type'=>$sign_type,
                'lcu'=>Yii::app()->user->id,
            ));
        }
    }


    protected function sendEmail($thisScenario){
        $row = Yii::app()->db->createCommand()->select("*")->from("hr_employee_operate")
            ->where("id=:id",array(":id"=>$this->id))->queryRow();
        if($row){
            if ($thisScenario == "audit"){
                $description="员工变更审核 - ".$row["name"]."（通过）";
                $subject="员工变更审核 - ".$row["name"]."（通过）";
            }else{
                $description="员工变更审核 - ".$row["name"]."（拒绝）";
                $subject="员工变更审核 - ".$row["name"]."（拒绝）";
            }
            $message="<p>员工类型：".StaffFun::getTableTypeNameForID($row["table_type"])."</p>";
            $message.="<p>员工编号：".$row["code"]."</p>";
            $message.="<p>员工姓名：".$row["name"]."</p>";
            $message.="<p>员工所在城市：".CGeneral::getCityName($row["city"])."</p>";
            $message.="<p>员工职位：".DeptForm::getDeptToId($row["position"])."</p>";
            $message.="<p>员工合同归属：".StaffFun::getCompanyNameToID($row["staff_id"])."</p>";
            $message.="<p>员工归属：".StaffFun::getCompanyNameToID($row["company_id"])."</p>";
            $message.="<p>操作原因：".Yii::t("contract",$row["operation"])."</p>";
            $message.="<p>审核日期：".date('Y-m-d H:i:s')."</p>";
            if ($thisScenario == "reject"){
                $message.="<p>拒绝原因：".$this->ject_remark."</p>";
            }
            $email = new Email($subject,$message,$description);
            $email->addEmailToLcu($row["lcu"]);
            $email->sent();
        }
    }

    public function setAttachment(){
        $str = $this->attachment;
        if(empty($str)){
            $arr = array();
        }else{
            $arr = explode(",",$str);
            for($i = 0;$i<count($arr);$i++){
                $rows = Yii::app()->db->createCommand()->select()->from("hr_attachment")
                    ->where('id=:id', array(':id'=>$arr[$i]))->queryRow();
                if($rows){
                    $arr[$i] = $rows;
                }else{
                    unset($arr[$i]);
                }
            }
        }
        $this->attachment = $arr;
        return $arr;
    }

    //變更完成
    public function finish(){
        $uid = Yii::app()->user->id;
        $date = date("Y-m-d H:i:s");
        $city_allow = array();
        $staff = Yii::app()->db->createCommand()->select()->from("hr_employee")
            ->where('id=:id', array(':id'=>$this->employee_id))->queryRow();
        $staffNew = Yii::app()->db->createCommand()->select()->from("hr_employee_operate")
            ->where('id=:id', array(':id'=>$this->id))->queryRow();
        $staffNew["code"] = $staff["code"];//不允許變更員工編號
        $city_allow[] = '"'.$staff["city"].'"';
        $city_allow[] = '"'.$staffNew["city"].'"';
        $this->opr_type = $staffNew["opr_type"];
        unset($staff["id"]);
        unset($staff["lcd"]);
        unset($staff["lud"]);
        unset($staff["luu"]);
        unset($staff["lcu"]);
        $keyList =array_keys($staff);
        $operation = $staffNew['operation'];
        $dateKey = array("test_start_time","test_end_time","entry_time","birth_time");
        foreach ($staffNew as $key =>$value){
            if (!in_array($key,$keyList)){
                unset($staffNew[$key]);
                continue;
            }
            if(empty($value)&&in_array($key,$dateKey)){
                unset($staffNew[$key]);
            }
        }
        if($operation === "departure"){
            $staffNew["staff_status"] = -1;//離職
            $this->setScenario($operation);
        }else{
            $staffNew["staff_status"] = $this->table_type==1?0:1;//兼职的状态为1
        }
        $staff["finish"] = 1;
        Yii::app()->db->createCommand()->update('hr_employee', $staffNew, 'id=:id', array(':id'=>$this->employee_id));
        Yii::app()->db->createCommand()->update('hr_employee_operate', $staff, 'id=:id', array(':id'=>$this->id));

        if($this->table_type==1){
            //修改流程的記錄的時間
            Yii::app()->db->createCommand()->update('hr_employee_history', array('lud'=>$date), 'history_id=:id',array(":id"=>$this->id));
            //交換員工附件
            $this->replaceAttachment();
            //判斷是否需要生成簽署合同
            $this->signContract($staffNew,implode(",",$city_allow));
            //員工姓名變更後需要修改其它數據表
            $this->resetOtherTable($staff,$staffNew);
        }

        //員工離職後需要隨機修改登錄賬號的密碼
        $this->updateUserPassword($staffNew);

        //U系统同步
        $str = $operation === "departure"?"edit":"edit";//离职也是同步修改，不是delete
        StaffForm::sendCurl($this->employee_id,$str);
    }

    //員工離職後需要隨機修改登錄賬號的密碼
    private function updateUserPassword($staffNew){
        if($staffNew["staff_status"]==-1){//員工離職
            $password = date("YmdHis")."_".$this->employee_id;
            $suffix = Yii::app()->params['envSuffix'];
            $row = Yii::app()->db->createCommand()->select("user_id")->from("hr_binding")
                ->where('employee_id=:id', array(':id'=>$this->employee_id))->queryRow();
            if($row){//如果該員工綁定了登錄賬戶
                Yii::app()->db->createCommand()->update("security$suffix.sec_user",array(
                    "password"=>$password
                ),"username=:username",array(":username"=>$row["user_id"]));
            }
        }
    }

    //強制刷新員工姓名(歷史id)
    public static function resetOnlyHistory($history_id){
        $staff = Yii::app()->db->createCommand()->select("employee_id as id,code,name")->from("hr_employee_operate")
            ->where('id=:id', array(':id'=>$history_id))->queryRow();
        if($staff){
            $staffNew = Yii::app()->db->createCommand()->select("code,name")->from("hr_employee")
                ->where('id=:id', array(':id'=>$staff["id"]))->queryRow();
            if($staffNew){
                self::resetOtherTable($staff,$staffNew,true);
                echo "success";
            }else{
                echo "employee_id error";
            }
        }else{
            echo "history_id error";
        }
    }

    //員工姓名變更後需要修改其它數據表
    private function resetOtherTable($staff,$staffNew,$echoBool=false){
        $staffCode = $staff["code"];//員工code
        $oldName = $staff["name"];//員工舊名字
        $oldCodeName = $staff["name"]." (".$staff["code"].")";
        $newName = $staffNew["name"];
        $newCodeName = $staffNew["name"]." (".$staffNew["code"].")";
        $suffix = Yii::app()->params['envSuffix'];
        if($oldName!=$newName){
            $list = array(
                array("table"=>"swoper$suffix.swo_service","updateData"=>array("salesman"=>$newCodeName),"whereSql"=>"salesman='$oldCodeName'"),
                array("table"=>"swoper$suffix.swo_service","updateData"=>array("othersalesman"=>$newCodeName),"whereSql"=>"othersalesman='$oldCodeName'"),
                array("table"=>"swoper$suffix.swo_service","updateData"=>array("technician"=>$newCodeName),"whereSql"=>"technician='$oldCodeName'"),
                array("table"=>"swoper$suffix.swo_service","updateData"=>array("first_tech"=>$newCodeName),"whereSql"=>"first_tech='$oldCodeName'"),
                array("table"=>"swoper$suffix.swo_followup","updateData"=>array("resp_staff"=>$newCodeName),"whereSql"=>"resp_staff='$oldCodeName'"),
                array("table"=>"swoper$suffix.swo_followup","updateData"=>array("resp_tech"=>$newCodeName),"whereSql"=>"resp_tech='$oldCodeName'"),
                array("table"=>"swoper$suffix.swo_followup","updateData"=>array("follow_tech"=>$newCodeName),"whereSql"=>"follow_tech='$oldCodeName'"),
                array("table"=>"swoper$suffix.swo_followup","updateData"=>array("follow_staff"=>$newCodeName),"whereSql"=>"follow_staff='$oldCodeName'"),
                array("table"=>"swoper$suffix.swo_enquiry","updateData"=>array("follow_staff"=>$newCodeName),"whereSql"=>"follow_staff='$oldCodeName'"),
                array("table"=>"swoper$suffix.swo_enquiry","updateData"=>array("record_by"=>$newCodeName),"whereSql"=>"record_by='$oldCodeName'"),
                array("table"=>"swoper$suffix.swo_logistic","updateData"=>array("salesman"=>$newCodeName),"whereSql"=>"salesman='$oldCodeName'"),
                array("table"=>"swoper$suffix.swo_logistic","updateData"=>array("follow_staff"=>$newCodeName),"whereSql"=>"follow_staff='$oldCodeName'"),
                array("table"=>"swoper$suffix.swo_qc","updateData"=>array("job_staff"=>" ".$newCodeName),"whereSql"=>"job_staff=' $oldCodeName'"),
                array("table"=>"swoper$suffix.swo_qc","updateData"=>array("qc_staff"=>" ".$newCodeName),"whereSql"=>"qc_staff=' $oldCodeName'"),
                array("table"=>"account$suffix.acc_service_comm_copy","updateData"=>array("salesman"=>$newCodeName),"whereSql"=>"salesman='$oldCodeName'"),
                array("table"=>"account$suffix.acc_service_comm_copy","updateData"=>array("othersalesman"=>$newCodeName),"whereSql"=>"othersalesman='$oldCodeName'"),
                array("table"=>"account$suffix.acc_service_comm_copy","updateData"=>array("technician"=>$newCodeName),"whereSql"=>"technician='$oldCodeName'"),
                array("table"=>"account$suffix.acc_service_comm_copy","updateData"=>array("first_tech"=>$newCodeName),"whereSql"=>"first_tech='$oldCodeName'"),
                array("table"=>"account$suffix.acc_service_comm_hdr","updateData"=>array("employee_name"=>$newName),"whereSql"=>"employee_code='$staffCode'"),
                array("table"=>"account$suffix.acc_request","updateData"=>array("payee_name"=>$newCodeName),"whereSql"=>"payee_name='$oldCodeName'"),
                array("table"=>"account$suffix.acc_trans_info","updateData"=>array("field_value"=>$newCodeName),"whereSql"=>"field_id='payer_name' and field_value='$oldCodeName'"),
                array("table"=>"account$suffix.acc_trans_info","updateData"=>array("field_value"=>$newCodeName),"whereSql"=>"field_id='handle_staff_name' and field_value='$oldCodeName'"),
                array("table"=>"sales$suffix.sal_search","updateData"=>array("employee_name"=>$newName),"whereSql"=>"employee_code='$staffCode'"),
            );
            foreach ($list as $row){
                $number = Yii::app()->db->createCommand()->update($row["table"],$row["updateData"],$row["whereSql"]);
                if($echoBool){
                    $table = end(explode(".",$row["table"]));
                    echo $table." update Num:".$number."<br/>";
                }
            }
        }
    }

    //交換員工附件
    public function replaceAttachment(){
        $connection = Yii::app()->db;
        $suffix = Yii::app()->params['envSuffix'];
        $attachment_old = $connection->createCommand()->select("id,doc_type_code,doc_id,remove")->from("docman$suffix.dm_master")
            ->where('doc_id=:doc_id and doc_type_code=:doc_type_code', array(
                ':doc_id'=>$this->employee_id,
                ':doc_type_code'=>"EMPLOY"
            ))->queryRow();
        $attachment_now = $connection->createCommand()->select("id,doc_type_code,doc_id,remove")->from("docman$suffix.dm_master")
            ->where('doc_id=:doc_id and doc_type_code=:doc_type_code', array(
                ':doc_id'=>$this->id,
                ':doc_type_code'=>"EMPLOYEE"
            ))->queryRow();

        if($attachment_old&&$attachment_now){
            //都有附件
            $old_id =$attachment_old["id"];
            $now_id =$attachment_now["id"];
            unset($attachment_old["id"]);
            unset($attachment_now["id"]);
            $connection->createCommand()->update("docman$suffix.dm_master",$attachment_old,'id=:id', array(':id'=>$now_id));
            $connection->createCommand()->update("docman$suffix.dm_master",$attachment_now,'id=:id', array(':id'=>$old_id));
        }elseif($attachment_old){
            //有旧没有新
            $old_id =$attachment_old["id"];
            unset($attachment_old["id"]);
            $attachment_now["doc_type_code"]="EMPLOYEE";
            $attachment_now["doc_id"]=$this->id;
            $connection->createCommand()->update("docman$suffix.dm_master",$attachment_now,'id=:id', array(':id'=>$old_id));
        }elseif ($attachment_now){
            //有新没有旧
            $now_id =$attachment_now["id"];
            unset($attachment_now["id"]);
            $attachment_now["doc_type_code"]="EMPLOY";
            $attachment_now["doc_id"]=$this->employee_id;
            $connection->createCommand()->update("docman$suffix.dm_master",$attachment_now,'id=:id', array(':id'=>$now_id));
        }
    }

    //刷新员工的状态及排序
    private function resetEmployeeStatusAndIndex(){
        $firstday = date("Y/m/d");
        $lastday = date("Y/m/d",strtotime("$firstday + 1 month"));
        $command = Yii::app()->db->createCommand();
        $aaa = $command->update('hr_employee', array("z_index"=>2),"staff_status=0 and test_type=1 and replace(test_start_time,'-', '/') <= '$firstday' and replace(test_end_time,'-', '/') >='$firstday'");//試用期
        $command->reset();
        //echo "試用期:$aaa<br>";
        $aaa = $command->update('hr_employee', array("z_index"=>1),"staff_status=0 and test_type=1 and replace(test_start_time,'-', '/') >= '$firstday'");//未入職
        $command->reset();
        //echo "未入職:$aaa<br>";
        $aaa = $command->update('hr_employee', array("z_index"=>5),"staff_status=0 and (test_type=0 or replace(test_end_time,'-', '/') <='$firstday')");//正式員工
        $command->reset();
        //echo "正式員工:$aaa<br>";
        $aaa = $command->update('hr_employee', array("z_index"=>4),"staff_status=0 and fix_time='fixation' and replace(end_time,'-', '/') >='$firstday' and replace(end_time,'-', '/') <='$lastday'");//合同即將過期
        $command->reset();
        //echo "合同即將過期:$aaa<br>";
        $aaa = $command->update('hr_employee', array("z_index"=>3),"staff_status=0 and fix_time='fixation' and replace(end_time,'-', '/') <'$firstday'");//合同過期
        //echo "合同過期:$aaa<br>";

        if (!isset(Yii::app()->params['retire']) || Yii::app()->params['retire']==true) {
            //echo "員工退休年齡(男60 女50):$aaa<br>";
            $row = Yii::app()->db->createCommand()->select("set_value")->from("hr_setting")
                ->where('set_name="retirementAgeType"')->queryScalar();
            switch ($row){
                case 1://新加坡-62岁
                    $manDate = date("Y/m/d", strtotime("-62 year"));
                    $womanDate = date("Y/m/d", strtotime("-62 year"));
                    break;
                case 2://吉隆坡-60岁
                    $manDate = date("Y/m/d", strtotime("-60 year"));
                    $womanDate = date("Y/m/d", strtotime("-60 year"));
                    break;
                default://echo "員工退休年齡(男60 女50):$aaa<br>";
                    $manDate = date("Y/m/d", strtotime("-60 year"));
                    $womanDate = date("Y/m/d", strtotime("-50 year"));
            }
            $sql = "UPDATE hr_employee a LEFT JOIN hr_contract b ON a.contract_id = b.id SET a.z_index = 0 WHERE ";
            $sql .= "a.birth_time is not null and a.birth_time != '' and a.staff_status=0 and b.retire=0 and ((replace(a.birth_time,'-', '/') <='$womanDate' and a.sex='woman') or (replace(a.birth_time,'-', '/') <='$manDate' and a.sex='man'))";
            $aa = Yii::app()->db->createCommand($sql)->execute();//要退休的員工前排顯示
        }
    }
}
