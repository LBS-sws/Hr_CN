<?php

class AuditWorkForm extends CFormModel
{
    public $id;
    public $work_code;
    public $employee_id;
    public $employee_name;
    public $work_type;
    public $city;
    public $work_cause;//加班原因
    public $work_cost;//加班費用
    public $work_address;
    public $hours="08:00";//開始時間的小時
    public $hours_end="08:00";//開始時間的小時
    public $start_time;
    public $end_time;
    public $log_time;
    public $z_index;
    public $status;
    public $audit_remark;
    public $pers_lcu;
    public $pers_lcd;
    public $user_lcu;
    public $user_lcd;
    public $area_lcu;
    public $area_lcd;
    public $head_lcu;
    public $head_lcd;
    public $you_lcu;
    public $you_lcd;
    public $reject_cause;
    public $lcd;
    public $cost_num;//節假日的工資倍率
    public $wage;//合約工資
    public $only = 1;//1:部門審核、2：主管、3：總監、4：你
    public $bool_cost = 1;//是否支付加班費用  1：支付  0：不支付


    public $no_of_attm = array(
        'workem'=>0
    );
    public $docType = 'WORKEM';
    public $docMasterId = array(
        'workem'=>0
    );
    public $files;
    public $removeFileId = array(
        'workem'=>0
    );

    protected $appointList=false;

    public function attributeLabels()
    {
        return array(
            'work_code'=>Yii::t('fete','Work Code'),
            'work_type'=>Yii::t('fete','Work Type'),
            'work_address'=>Yii::t('fete','Work Address'),
            'work_cause'=>Yii::t('fete','Work Cause'),
            'work_cost'=>Yii::t('fete','Work Cost'),
            'employee_id'=>Yii::t('contract','Employee Name'),
            'start_time'=>Yii::t('contract','Start Time'),
            'end_time'=>Yii::t('contract','End Time'),
            'log_time'=>Yii::t('fete','Log Date'),
            'status'=>Yii::t('contract','Status'),
            'pers_lcu'=>Yii::t('fete','personnel lcu'),
            'pers_lcd'=>Yii::t('fete','personnel lcd'),
            'user_lcu'=>Yii::t('fete','user lcu'),
            'user_lcd'=>Yii::t('fete','user lcd'),
            'area_lcu'=>Yii::t('fete','area lcu'),
            'area_lcd'=>Yii::t('fete','area lcd'),
            'head_lcu'=>Yii::t('fete','head lcu'),
            'you_lcu'=>Yii::t('fete','you lcu'),
            'you_lcd'=>Yii::t('fete','you lcd'),
            'bool_cost'=>Yii::t('fete','Bool Work Cost'),
            'head_lcd'=>Yii::t('fete','Bool Work Cost'),
            'audit_remark'=>Yii::t('fete','Audit Remark'),
            'reject_cause'=>Yii::t('contract','Rejected Remark'),
            'wage'=>Yii::t('contract','Contract Pay'),
            'lcd'=>Yii::t('fete','apply for time'),
        );
    }

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return array(
            array('id,employee_id,work_code,work_type,work_address,status,work_cause,start_time,end_time,log_time,only,audit_remark,employee_name,bool_cost,city,lcd','safe'),

            array('reject_cause','required',"on"=>"reject"),
            array('files, removeFileId, docMasterId','safe'),
        );
    }

    public function validateTime($attribute, $params){
        if(!empty($this->end_time)&&!empty($this->start_time)){
            $date2 = strtotime($this->start_time);
            $date1 = strtotime($this->end_time);
            if($date2>$date1){
                $message = Yii::t('fete','Start time cannot be greater than end time');
                $this->addError($attribute,$message);
            }else{
                if($this->log_time <= 0){
                    $message = Yii::t('fete','Start time cannot be greater than end time');
                    $this->addError($attribute,$message);
                }
            }
        }
    }


    public function validateWorkType($attribute, $params){
        $city = Yii::app()->user->city();
        if($this->work_type == 2){
            $rows = Yii::app()->db->createCommand()->select("cost_num")->from("hr_fete")
                ->where("start_time<=:start_time and end_time >=:end_time and (city='$city' or only='default')", array(':start_time'=>$this->start_time,':end_time'=>$this->end_time))->queryRow();
            if($rows){
                $this->cost_num = $rows["cost_num"];
            }else{
                $message = Yii::t('fete','This time period is not a legal holiday, please contact the administrator');
                $this->addError($attribute,$message);
            }
        }else if($this->work_type == 1){
            $week = date("w",strtotime($this->start_time));
            if($week == 6 || $week == 0){
                //是週末
            }else{
                $message = Yii::t('fete','This time period is not a weekend');
                $this->addError($attribute,$message);
            }
        }
    }

    public function retrieveData($index) {
        $city_allow = Yii::app()->user->city_allow();
        $city = Yii::app()->user->city();
        $suffix = Yii::app()->params['envSuffix'];
        $only = $this->only;
        $staffList = BindingForm::getEmployeeListToUsername();
        if(!empty($staffList)){
            $staff_id = $staffList["id"];
            $department = $staffList["department"];//部門
        }else{
            $staff_id = 0;
            $department = 0;
        }
        $sql = " a.status in (1,3) AND a.z_index =$only and a.id=:id";
        switch ($only){
            case 1: //部門審核
                $sql.=" AND b.city = '$city'  AND b.department = '$department' ";
                break;
            case 2: //主管
                $sql.=" AND b.city = '$city' ";
                break;
            case 5: //人事
                $sql.=" AND b.city = '$city' ";
                break;
            case 3: //總監
                $sql.=" AND b.city in ($city_allow) ";
                break;
        }
        $rows = Yii::app()->db->createCommand()->select("a.*,b.wage,b.staff_type,b.city AS s_city,b.name as employee_name,docman$suffix.countdoc('WORKEM',a.id) as workemdoc")
            ->from("hr_employee_work a")
            ->leftJoin("hr_employee b","a.employee_id = b.id")
            ->where($sql,array(":id"=>$index))->queryAll();
        if (count($rows) > 0) {
            foreach ($rows as $row) {
                $this->id = $row['id'];
                $this->work_code = $row['work_code'];
                $this->employee_name = $row['employee_id'];
                $this->employee_id = $row['employee_name'];
                $this->wage = $row['wage'];
                $this->work_type = $row['work_type'];
                $this->work_cause = $row['work_cause'];
                $this->work_address = $row['work_address'];
                $this->work_cost = $row['work_cost'];
                $this->city = $row['s_city'];
                $this->start_time = date("Y/m/d",strtotime($row['start_time']));
                $this->hours = date("H:i",strtotime($row['start_time']));
                $this->end_time = date("Y/m/d",strtotime($row['end_time']));
                $this->hours_end = date("H:i",strtotime($row['end_time']));
                $this->log_time = $row['log_time'];
                $this->z_index = $row['z_index'];
                $this->status = $row['status'];
                $this->pers_lcu = isset($row['pers_lcu'])?$row['pers_lcu']:"";
                $this->pers_lcd = isset($row['pers_lcd'])?$row['pers_lcd']:"";
                $this->user_lcu = $row['user_lcu'];
                $this->user_lcd = $row['user_lcd'];
                $this->area_lcu = $row['area_lcu'];
                $this->area_lcd = $row['area_lcd'];
                $this->head_lcu = $row['head_lcu'];
                $this->head_lcd = $row['head_lcd'];
                $this->you_lcu = $row['you_lcu'];
                $this->you_lcd = $row['you_lcd'];
                $this->lcd = $row['lcd'];
                $this->audit_remark = $row['audit_remark'];
                $this->reject_cause = $row['reject_cause'];
                $this->no_of_attm['workem'] = $row['workemdoc'];
                break;
            }
        }
        return true;
    }

    //驗證員工是不是經理級別的審核流程
    public function validateManagerToEmployeeId($employeeId = ""){
        $rows = Yii::app()->db->createCommand()->select("b.manager")->from("hr_employee a")
            ->leftJoin("hr_dept b","b.id = a.position")->where("a.id=$employeeId")->queryRow();
        if($rows){
            if(!empty($rows["manager"])){
                return true;
            }
        }
        return false;
    }

    public function saveData()
    {
        $connection = Yii::app()->db;
        $transaction=$connection->beginTransaction();
        try {
            $this->saveGoods($connection);
            $transaction->commit();
        }
        catch(Exception $e) {
            $transaction->rollback();
            throw new CHttpException(404,'Cannot update. ('.$e->getMessage().')');
        }
    }

    protected function saveGoods(&$connection) {
        $systemId = Yii::app()->params['systemId'];
        $suffix = Yii::app()->params['envSuffix'];
        $sql = '';
        $only = intval($this->only);
        $staffList = BindingForm::getEmployeeListToEmployeeId($this->employee_name);
        if(!empty($staffList)){
            $manager= $staffList["c_manager"];
        }
        $auditSql = "";//申請人如果是經理、主管會直接完成申請
        switch ($only){
            case 1: //部門
                $clause="user_lcu = :user_lcu, user_lcd = :user_lcd, ";
/*                if(!empty($staffList)){
                    $workTwo = Yii::app()->db->createCommand()->select("a.username")->from("security$suffix.sec_user a")
                        ->leftJoin("security$suffix.sec_user_access b","b.username = a.username")
                        ->where("a.status='A' and b.system_id='$systemId' and b.a_read_write like '%ZE05%' and a.city=:city",
                            array(':city'=>$staffList["city"]))->queryRow();
                    if(!$workTwo){ //判斷主管是否存在
                        $only++;
                    }
                }*/
                break;
            case 2: //主管
                $clause="area_lcu = :area_lcu, area_lcd = :area_lcd, ";
                $auditSql = "status = 4,";
                break;
            case 3: //總監
                $clause="head_lcu = :head_lcu, head_lcd = :head_lcd, ";
                $auditSql = "status = 4,";
                break;
            case 4: //你
                $clause="you_lcu = :you_lcu, you_lcd = :you_lcd, ";
                $auditSql = "status = 4,";
                break;
            case 5: //你
                $clause="pers_lcu = :pers_lcu, pers_lcd = :pers_lcd, ";
                $assList = AuditConfigForm::getAccessAndCity($staffList["city"],$staffList["department"]);
                $only = 0;
                foreach ($assList as $key=>$keyBool){
                    if($keyBool){
                        $only = $key-1;
                        break;
                    }
                }
                break;
            default:
                throw new CHttpException(404,'數據異常');
                return false;
        }
        switch ($this->scenario) {
            case 'audit':
                $sql = "update hr_employee_work set
							z_index = :z_index,
							audit_remark = :audit_remark,
							 ";
                if($only!=4){
                    $only++;
                }
                $sql.=$clause.$auditSql."luu = :luu
						where id = :id";
                break;
            case 'reject':
                $sql = "update hr_employee_work set
							status = 3, 
							reject_cause = :reject_cause, 
							audit_remark = :audit_remark, 
							";
                $sql.=$clause."luu = :luu
						where id = :id";
                break;
        }
        if (empty($sql)) return false;

        $city = Yii::app()->user->city();
        $uid = Yii::app()->user->id;

        $command=$connection->createCommand($sql);
        if (strpos($sql,':id')!==false)
            $command->bindParam(':id',$this->id,PDO::PARAM_INT);
        if (strpos($sql,':status')!==false)
            $command->bindParam(':status',$this->status,PDO::PARAM_STR);
        if (strpos($sql,':reject_cause')!==false)
            $command->bindParam(':reject_cause',$this->reject_cause,PDO::PARAM_STR);
        if (strpos($sql,':audit_remark')!==false)
            $command->bindParam(':audit_remark',$this->audit_remark,PDO::PARAM_STR);
        if (strpos($sql,':z_index')!==false){
            $command->bindParam(':z_index',$only,PDO::PARAM_STR);
            $this->z_index = $only;
        }

        if (strpos($sql,':work_type')!==false)
            $command->bindParam(':work_type',$this->work_type,PDO::PARAM_STR);
        if (strpos($sql,':work_cause')!==false)
            $command->bindParam(':work_cause',$this->work_cause,PDO::PARAM_STR);
        if (strpos($sql,':work_address')!==false)
            $command->bindParam(':work_address',$this->work_address,PDO::PARAM_STR);
        if (strpos($sql,':work_cost')!==false)
            $command->bindParam(':work_cost',$this->work_cost,PDO::PARAM_STR);
        if (strpos($sql,':start_time')!==false)
            $command->bindParam(':start_time',$this->start_time,PDO::PARAM_STR);
        if (strpos($sql,':end_time')!==false)
            $command->bindParam(':end_time',$this->end_time,PDO::PARAM_STR);
        if (strpos($sql,':log_time')!==false)
            $command->bindParam(':log_time',$this->log_time,PDO::PARAM_STR);

        if (strpos($sql,':pers_lcu')!==false)
            $command->bindParam(':pers_lcu',$uid,PDO::PARAM_STR);
        if (strpos($sql,':pers_lcd')!==false)
            $command->bindParam(':pers_lcd',date("Y-m-d"),PDO::PARAM_STR);
        if (strpos($sql,':user_lcu')!==false)
            $command->bindParam(':user_lcu',$uid,PDO::PARAM_STR);
        if (strpos($sql,':user_lcd')!==false)
            $command->bindParam(':user_lcd',date("Y-m-d"),PDO::PARAM_STR);
        if (strpos($sql,':area_lcu')!==false)
            $command->bindParam(':area_lcu',$uid,PDO::PARAM_STR);
        if (strpos($sql,':area_lcd')!==false)
            $command->bindParam(':area_lcd',date("Y-m-d"),PDO::PARAM_STR);
        if (strpos($sql,':head_lcu')!==false)
            $command->bindParam(':head_lcu',$uid,PDO::PARAM_STR);
        if (strpos($sql,':head_lcd')!==false)
            $command->bindParam(':head_lcd',date("Y-m-d"),PDO::PARAM_STR);
        if (strpos($sql,':you_lcu')!==false)
            $command->bindParam(':you_lcu',$uid,PDO::PARAM_STR);
        if (strpos($sql,':you_lcd')!==false)
            $command->bindParam(':you_lcd',date("Y-m-d"),PDO::PARAM_STR);
        if (strpos($sql,':luu')!==false)
            $command->bindParam(':luu',$uid,PDO::PARAM_STR);
        $command->execute();


        //發送郵件
        $this->sendEmail();
        return true;
    }

    protected function sendEmail(){
        $dayStr ="小时";
        $email = new Email();
        $row = Yii::app()->db->createCommand()->select("*")->from("hr_employee")
            ->where('id=:id', array(':id'=>$this->employee_name))->queryRow();
        $message="<p>加班编号：".$this->work_code."</p>";
        $message.="<p>员工编号：".$row["code"]."</p>";
        $message.="<p>员工姓名：".$row["name"]."</p>";
        $message.="<p>员工城市：".General::getCityName($row["city"])."</p>";
        $message.="<p>加班时间：".$this->start_time." ~ ".$this->end_time."  (".$this->log_time."$dayStr)</p>";
        if($this->scenario == "audit"){
            if ($this->z_index == 2){
                $description="加班二次审核 - ".$row["name"];
                $subject="加班二次审核 - ".$row["name"];
                $email->addEmailToPrefixAndOnlyCity("ZE05",$row["city"]);
            }else{
                $description="加班审核通过 - ".$row["name"];
                $subject="加班审核通过 - ".$row["name"];
                $email->addEmailToStaffId($row["id"]);
            }
/*          總監說郵件太多，不想看郵件取消掉了
            if ($this->z_index == 3){ //普通員工的請假需要給總監發送郵件
                $subjectOne = "员工加班通过通知 - ".$row["name"];
                $emailOne = new Email($subjectOne,$message,$subjectOne);
                $emailOne->addEmailToPrefixAndCity("ZG04",$row["city"]);
                $emailOne->sent();
            }*/
        }else{
            $description="加班被拒絕 - ".$row["name"];
            $subject="加班被拒絕 - ".$row["name"];
            $message.="<p>拒絕原因：".$this->reject_cause."</p>";
            $email->addEmailToStaffId($row["id"]);
            //$email->addEmailToPrefixAndCity("ZG04",$row["city"]); 總監說郵件太多，不想看郵件取消掉了
        }
        $email->setDescription($description);
        $email->setMessage($message);
        $email->setSubject($subject);
        $email->sent();
    }

    //計算員工的加班費用
    public function resetWorkCost(){
        if($this->bool_cost == 0){ //不支付加班工資
            $wage = 0;
        }else{
            $employeeList = StaffFun::getEmployeeOneToId($this->employee_name);
            $wage = floatval($employeeList["wage"]);
        }
        switch ($this->work_type){
            case 2:
                if($this->cost_num == 1){
                    $this->cost_num = 3;
                }else{
                    $this->cost_num = 2;
                }
                $this->work_cost = ($wage/(21.75*8))*floatval($this->log_time)*intval($this->cost_num);
                break;
            case 1:
                $this->work_cost = ($wage/(21.75*8))*floatval($this->log_time)*2;
                break;
            default:
                $this->work_cost = ($wage/(21.75*8))*floatval($this->log_time)*1.5;
        }
        $this->start_time .=" ".$this->hours;
        $this->end_time .=" ".$this->hours_end;
    }

    //判斷輸入框能否修改
    public function getInputBool(){
        return true;
/*        if($this->only == 2&&$this->getScenario()!='view'){
            return false;
        }else{
            return true;
        }*/
    }

    //支付不支付列表
    public function getPayList(){
        return array(Yii::t("fete","Do not pay"),Yii::t("fete","pay"));
    }

    //獲取假期的倍率
    public function getMuplite(){
        switch ($this->work_type){
            case 2:
                $city = Yii::app()->user->city();
                $rows = Yii::app()->db->createCommand()->select("cost_num")->from("hr_fete")
                    ->where("start_time<=:start_time and end_time >=:end_time and (city='$city' or only='default')",
                        array(':start_time'=>$this->start_time,':end_time'=>$this->end_time))->queryRow();
                if($rows){
                    if($rows["cost_num"] == 1){
                        $this->cost_num = 3;
                    }else{
                        $this->cost_num = 2;
                    }
                    return $this->cost_num;
                }else{
                    return "1.5";
                }
                break;
            case 1:
                return 2;
                break;
            default:
                return 1.5;
        }
    }

    //獲取本月加班記錄
    public function getHistoryList(){
        $thisWork = Yii::app()->db->createCommand()->select("*")
            ->from("hr_employee_work")->where("id=:id",array(":id"=>$this->id))->queryRow();
        if($thisWork){
            $start_month = date("Y-m-01");
            $end_month = date("Y-m-d",strtotime("$start_month + 1 month - 1 day"));
            $historyList = array();
            $employeeList = StaffFun::getEmployeeOneToId($thisWork["employee_id"]);
            $workList = Yii::app()->db->createCommand()->select("*")
                ->from("hr_employee_work")->where("employee_id=:id and status = 4 and start_time>='$start_month' and start_time<='$end_month'",
                    array(":id"=>$thisWork["employee_id"]))->queryAll();
            foreach ($workList as $list){
                if($list['work_type'] == 2){
                    $list['start_time'] = date("Y/m/d",strtotime($list['start_time']));
                    $list['end_time'] = date("Y/m/d",strtotime($list['end_time']));
                    $dayStr ="天";
                }else{
                    $list['start_time'] = date("Y/m/d H:i:s",strtotime($list['start_time']));
                    $list['end_time'] = date("Y/m/d H:i:s",strtotime($list['end_time']));
                    $dayStr ="小時";
                }
                array_push($historyList,array(
                    "employee_code"=>$employeeList["code"],
                    "employee_name"=>$employeeList["name"],
                    "start_time"=>$list["start_time"],
                    "end_time"=>$list["end_time"],
                    "log_time"=>$list["log_time"].$dayStr,
                ));
            }
            return $historyList;
        }
        return array();
    }
}
