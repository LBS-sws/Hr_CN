<?php

class AppointWorkForm extends CFormModel
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
            array('id','validateID'),
        );
    }

    public function validateID($attribute, $params){
        $row = Yii::app()->db->createCommand()->select("a.*")
            ->from("hr_employee_work a")
            ->where("id=:id",array(":id"=>$this->id))->queryRow();
        if($row){
            $this->employee_id = $row["employee_id"];
            $this->z_index = $row["z_index"];
            $this->appointList = AppointSetForm::getAppointSet($this->employee_id);
            if(!$this->appointList){
                $message = "该员工没有指定审核人，数据异常";
                $this->addError($attribute,$message);
            }elseif (!key_exists($this->z_index,$this->appointList)){
                $message = "加班单异常，请与管理员联系。ID:{$this->id}，z_index:{$this->z_index}";
                $this->addError($attribute,$message);
            }else{
                $uid = Yii::app()->user->id;
                $auditUser = $this->appointList[$this->z_index];
                if($auditUser!=$uid){
                    $message = "审核人异常,审核人应该是：".$auditUser;
                    $this->addError($attribute,$message);
                }
            }
        }else{
            $message = "加班单不存在，数据异常";
            $this->addError($attribute,$message);
        }
    }

    public function retrieveData($index) {
        $city_allow = Yii::app()->user->city_allow();
        $city = Yii::app()->user->city();
        $suffix = Yii::app()->params['envSuffix'];

        $rows = Yii::app()->db->createCommand()->select("a.*,b.wage,b.staff_type,b.city AS s_city,b.name as employee_name,docman$suffix.countdoc('WORKEM',a.id) as workemdoc")
            ->from("hr_employee_work a")
            ->leftJoin("hr_employee b","a.employee_id = b.id")
            ->where("a.status in (1,3) and a.id=:id",array(":id"=>$index))->queryAll();
        if (count($rows) > 0) {
            foreach ($rows as $row) {
                $this->id = $row['id'];
                $this->work_code = $row['work_code'];
                //$this->employee_name = $row['employee_name'];
                $this->employee_id = $row['employee_name'];
                $this->employee_name = $row['employee_id'];
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
        $sql = '';
        $only = $this->z_index;
        $userLcdList = array(
            10=>"pers_lcd",
            11=>"user_lcd",
            12=>"area_lcd",
            13=>"head_lcd",
            14=>"you_lcd",
        );
        $auditSql="";
        $clause=$userLcdList[$only]." = :".$userLcdList[$only].",";
        switch ($this->scenario) {
            case 'audit':
                $sql = "update hr_employee_work set
							z_index = :z_index,
							audit_remark = :audit_remark,
							 ";
                $only++;
                if(is_array($this->appointList)&&!key_exists($only,$this->appointList)){
                    $auditSql = "status = 4,";
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
            ->where('id=:id', array(':id'=>$this->employee_id))->queryRow();
        $message="<p>加班编号：".$this->work_code."</p>";
        $message.="<p>员工编号：".$row["code"]."</p>";
        $message.="<p>员工姓名：".$row["name"]."</p>";
        $message.="<p>员工城市：".General::getCityName($row["city"])."</p>";
        $message.="<p>加班时间：".$this->start_time." ~ ".$this->end_time."  (".$this->log_time."$dayStr)</p>";
        if($this->scenario == "audit"){
            if (is_array($this->appointList)&&key_exists($this->z_index,$this->appointList)){
                $key = $this->z_index-10;
                $description="加班{$key}次审核 - ".$row["name"];
                $subject="加班{$key}次审核 - ".$row["name"];
                $email->addEmailToLcu($this->appointList[$this->z_index]);
            }else{
                $description="加班审核通过 - ".$row["name"];
                $subject="加班审核通过 - ".$row["name"];
                $email->addEmailToStaffId($row["id"]);
            }
        }else{
            $description="加班被拒絕 - ".$row["name"];
            $subject="加班被拒絕 - ".$row["name"];
            $message.="<p>拒絕原因：".$this->reject_cause."</p>";
            $email->addEmailToStaffId($row["id"]);
        }
        $email->setDescription($description);
        $email->setMessage($message);
        $email->setSubject($subject);
        $email->sent();
    }

    //判斷輸入框能否修改
    public function getInputBool(){
        return true;
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
