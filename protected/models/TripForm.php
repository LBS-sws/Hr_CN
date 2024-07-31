<?php

class TripForm extends CFormModel
{
	public $id;
    public $trip_code;
    public $employee_id;
    public $employee_code="";
    public $employee_name="";
    public $city="";

    public $trip_cause;
    public $trip_cost;
    public $trip_address;
    public $company_name;//公司名称
    public $start_time;
    public $start_time_lg;
    public $end_time;
    public $end_time_lg='PM';
    public $log_time=0;
    public $z_index=4;//1:部門審核、2：主管、3：總監、4：你
    public $status=0;
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
    public $result_id;
    public $result_text;
    public $lcd;
    public $audit = false;//是否需要審核

    public $addTime=array(
        array('id'=>0,
            'trip_id'=>0,
            'start_time'=>'',
            'start_time_lg'=>'AM',
            'end_time'=>'',
            'end_time_lg'=>'PM',
            'uflag'=>'N',
        ),
    );

    public $addMoney=array(
        array('id'=>0,
            'trip_id'=>0,
            'money_set_id'=>'',
            'trip_money'=>'',
            'uflag'=>'N',
        ),
    );

    public $no_of_attm = array(
        'trip'=>0
    );
    public $docType = 'TRIP';
    public $docMasterId = array(
        'trip'=>0
    );
    public $files;
    public $removeFileId = array(
        'trip'=>0
    );

    protected $appointList=false;

	public function attributeLabels()
	{
        if(in_array($this->status,array(4,5))){
            $reject_cause = Yii::t('contract','cancel cause');
        }else{
            $reject_cause = Yii::t('contract','Rejected Remark');
        }
        return array(
            'trip_code'=>Yii::t('fete','trip code'),
            'addTime'=>Yii::t('fete','trip date'),
            'employee_id'=>Yii::t('contract','Employee Name'),
            'start_time'=>Yii::t('contract','Start Time'),
            'end_time'=>Yii::t('contract','End Time'),
            'trip_cause'=>Yii::t('fete','trip cause'),
            'trip_cost'=>Yii::t('fete','trip cost'),
            'trip_address'=>Yii::t('fete','trip address'),
            'company_name'=>Yii::t('fete','company name'),
            'log_time'=>Yii::t('fete','Log Date'),
            'status'=>Yii::t('contract','Status'),
            'pers_lcu'=>Yii::t('fete','personnel lcu'),
            'pers_lcd'=>Yii::t('fete','personnel lcd'),
            'user_lcu'=>Yii::t('fete','user lcu'),
            'user_lcd'=>Yii::t('fete','user lcd'),
            'area_lcu'=>Yii::t('fete','area lcu'),
            'area_lcd'=>Yii::t('fete','area lcd'),
            'head_lcu'=>Yii::t('fete','trip lcu'),
            'head_lcd'=>Yii::t('fete','trip lcd'),
            'you_lcu'=>Yii::t('fete','you lcu'),
            'you_lcd'=>Yii::t('fete','you lcd'),
            'audit_remark'=>Yii::t('fete','Audit Remark'),
            'reject_cause'=>$reject_cause,
            'wage'=>Yii::t('contract','Contract Pay'),
            'lcd'=>Yii::t('fete','apply for time'),
            'state'=>Yii::t('contract','Status'),
            'result_id'=>Yii::t('fete','trip result'),
            'result_text'=>Yii::t('fete','trip result text'),
            'money_set_id'=>Yii::t('fete','project name'),
            'trip_money'=>Yii::t('fete','trip amount'),
            'addMoney'=>Yii::t('fete','trip money'),
        );
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
            array('id,z_index,result_id,company_name,result_text,addMoney,addTime,trip_code,trip_cost,trip_address,employee_id,employee_code,employee_name,city,status,trip_cause,start_time,end_time,start_time_lg,end_time_lg,log_time,lcd,reject_cause','safe'),
            array('id','validateRejectCause','on'=>array("cancel")),
            //array('employee_id','validateUser','on'=>array("new","edit","audit")),
            array('employee_id,trip_cost,trip_address','required','on'=>array("new","edit","audit")),
            array('employee_id','validateStaff'),
            array('addTime','validateEndTime','on'=>array("new","edit","audit")),
            array('addMoney','validateMoney','on'=>array("new","edit","audit")),
            array('id','validateCount','on'=>array("new","edit","audit")),
            array('files, removeFileId, docMasterId','safe'),
		);
	}

    public function validateCount($attribute, $params){
        $row = Yii::app()->db->createCommand()->select("trip_code")
            ->from("hr_employee_trip")
            ->where("employee_id=:id and status=2",array(":id"=>$this->employee_id))->queryRow();
        if($row){
            $message = "您有出差单未填写出差结果，无法继续申请出差。({$row['trip_code']})";
            $this->addError($attribute,$message);
            return false;
        }
        return true;
    }

    public function validateStaff($attribute, $params){
        if($this->getScenario()=="new"){
            TripList::validateEmployee($this);
        }else{
            $uid = Yii::app()->user->id;
            $row = Yii::app()->db->createCommand()
                ->select("a.user_id,b.id,b.code,b.name,b.city")
                ->from("hr_binding a")
                ->leftJoin("hr_employee b","a.employee_id=b.id")
                ->where('a.employee_id=:employee_id',array(':employee_id'=>$this->employee_id))->queryRow();
            $this->employee_code = $row["code"];
            $this->employee_name = $row["name"];
            $this->city = $row["city"];
            if(!Yii::app()->user->validFunction('ZR24')&&$uid!=$row["user_id"]){
                $message = "权限不足，无法修改";
                $this->addError($attribute,$message);
            }
        }
    }

    public function validateRejectCause($attribute, $params){
        if(empty($this->reject_cause)){
            $message = Yii::t('contract','cancel cause').Yii::t('contract',' can not be empty');
            $this->addError($attribute,$message);
        }else{
            $row = Yii::app()->db->createCommand()->select("id")->from("hr_employee_trip")
                ->where("id=:id and status=4",array(":id"=>$this->id))->queryRow();
            if(!$row){
                $message = "請假單不存在，請於管理員聯繫";
                $this->addError($attribute,$message);
            }
        }
    }

    //請假時間段的驗證
    public function validateEndTime($attribute, $params){
        $this->start_time = '';
        $this->start_time_lg = '';
        $this->end_time = '';
        $this->end_time_lg = '';
        if(empty($this->addTime)||!is_array($this->addTime)){
            $message = "申请时间不能为空";
            $this->addError($attribute,$message);
            return false;
        }else{
            foreach ($this->addTime as $keyOne=> $list){
                if($list["uflag"]=="D"){//删除不需要验证
                    continue;
                }
                if(!empty($list["start_time"])&&!empty($list["end_time"])){
                    if($list["start_time"]>$list["end_time"]){
                        $message = "开始时间不能大于结束时间";
                        $this->addError($attribute,$message);
                        return false;
                    }
                    foreach ($this->addTime as $keyTwo => $forList){
                        if($keyOne!=$keyTwo){
                            if($this->timeThatReturnBool($list,$forList)){
                                $message = "时间段不能重复";
                                $this->addError($attribute,$message);
                                return false;
                            }
                        }
                    }
                    if(empty($this->start_time)||date("Y-m-d",strtotime($this->start_time))>=date("Y-m-d",strtotime($list["start_time"]))){
                        $this->start_time = $list["start_time"];
                        $this->start_time_lg = $list["start_time_lg"];
                    }
                    if(empty($this->end_time)||date("Y-m-d",strtotime($this->end_time))<=date("Y-m-d",strtotime($list["end_time"]))){
                        $this->end_time = $list["end_time"];
                        $this->end_time_lg = $list["end_time_lg"];
                    }
                    if($list["start_time_lg"] == "AM"){
                        $startTime = date("Y-m-d",strtotime($list["start_time"]))." 10:00:00";
                    }else{
                        $startTime = date("Y-m-d",strtotime($list["start_time"]))." 14:00:00";
                    }
                    if($list["end_time_lg"] == "AM"){
                        $endTime = date("Y-m-d",strtotime($list["end_time"]))." 10:00:00";
                    }else{
                        $endTime = date("Y-m-d",strtotime($list["end_time"]))." 14:00:00";
                    }
                    $sql = "select b.trip_code from hr_employee_trip_info a LEFT JOIN hr_employee_trip b ON a.trip_id = b.id WHERE b.status != 5 AND ((a.start_time>'$startTime' AND a.end_time <'$endTime') OR (a.start_time<='$startTime' AND a.end_time >='$startTime') OR (a.start_time<='$endTime' AND a.end_time >='$endTime')) ";
                    //var_dump($sql);die();
                    $sql.=" and b.employee_id='".$this->employee_id."'";
                    if(!empty($this->id)&&is_numeric($this->id)){
                        $sql.=" and b.id!=".$this->id;
                    }
                    $connection = Yii::app()->db;
                    $rows = $connection->createCommand($sql)->queryRow();
                    if($rows){
                        $message = "該時間段已有出差單：".$rows["trip_code"];
                        $this->addError($attribute,$message);
                        return false;
                    }
                }else{
                    $message = "时间不能为空";
                    $this->addError($attribute,$message);
                    return false;
                }
            }
        }
    }

    //請假時間段的驗證
    public function validateMoney($attribute, $params){
        $this->trip_cost = 0;
        foreach ($this->addMoney as $row) {
            if(!empty($row['money_set_id'])){
                $money = empty($row['trip_money'])?0:floatval($row['trip_money']);
                $this->trip_cost +=$money;
            }
        }
    }

    //判斷兩個時間段是否有交集
    private function timeThatReturnBool($list,$forList){
        $list["start_time"] = date("Y-m-d",strtotime($list["start_time"]));
        $list["end_time"] = date("Y-m-d",strtotime($list["end_time"]));
        $forList["start_time"] = date("Y-m-d",strtotime($forList["start_time"]));
        $forList["end_time"] = date("Y-m-d",strtotime($forList["end_time"]));
        if($list["start_time"]>$forList["start_time"]&&$list["start_time"]<$forList["end_time"]){
            return true;
        }
        if($list["end_time"]>$forList["start_time"]&&$list["end_time"]<$forList["end_time"]){
            return true;
        }
        if($list["start_time"]<$forList["start_time"]&&$list["end_time"]>$forList["end_time"]){
            return true;
        }
        return false;
    }

	public function retrieveData($index) {
        $city_allow = Yii::app()->user->city_allow();
        $employee_id = empty($this->employee_id)?0:$this->employee_id;
        $suffix = Yii::app()->params['envSuffix'];
        $uid = Yii::app()->user->id;
        $auditSql = "";
        foreach (AppointTripSetForm::getZIndexForUser() as $key=>$item){
            $auditSql.= empty($auditSql)?"":" or ";
            $auditSql.= "a.{$item}='$uid'";
        }
        if(Yii::app()->user->validFunction('ZR24')){//所有出差記錄
            $whereSql=" and ((b.id={$employee_id}) or {$auditSql} or(b.city in ({$city_allow}) and a.status!=0))";
        }else{
            $whereSql=" and (b.id={$employee_id} or {$auditSql})";
        }
        $rows = Yii::app()->db->createCommand()
            ->select("a.*,b.wage,b.city as s_city,b.staff_type,b.code as employee_code,b.name as employee_name,docman$suffix.countdoc('TRIP',a.id) as tripdoc")
            ->from("hr_employee_trip a")
            ->leftJoin("hr_employee b","a.employee_id = b.id")
            ->where("a.id=:id {$whereSql}",array(":id"=>$index))->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
                $this->id = $row['id'];
                $this->trip_code = $row['trip_code'];
                $this->employee_id = $row['employee_id'];
                $this->employee_code = $row['employee_code'];
                $this->employee_name = $row['employee_name'];
                $this->trip_cause = $row['trip_cause'];
                $this->trip_address = $row['trip_address'];
                $this->company_name = $row['company_name'];
                $this->trip_cost = floatval($row['trip_cost']);
                $this->start_time = date("Y/m/d",strtotime($row['start_time']));
                $this->end_time = date("Y/m/d",strtotime($row['end_time']));
                $this->log_time = $row['log_time'];
                $this->z_index = $row['z_index'];
                $this->start_time_lg = $row['start_time_lg'];
                $this->end_time_lg = $row['end_time_lg'];
                $this->status = $row['status'];
                $this->pers_lcu = isset($row['pers_lcu'])?$row['pers_lcu']:"";
                $this->pers_lcd = isset($row['pers_lcd'])?$row['pers_lcd']:"";
                $this->user_lcu = $row['user_lcu'];
                $this->user_lcd = $row['user_lcd'];
                $this->area_lcu = $row['area_lcu'];
                $this->area_lcd = $row['area_lcd'];
                $this->lcd = $row['lcd'];
                $this->head_lcu = $row['head_lcu'];
                $this->head_lcd = $row['head_lcd'];
                $this->you_lcu = $row['you_lcu'];
                $this->you_lcd = $row['you_lcd'];
                $this->city = $row['s_city'];
                $this->audit_remark = $row['audit_remark'];
                $this->reject_cause = $row['reject_cause'];
                $this->no_of_attm['trip'] = $row['tripdoc'];
                $this->result_id=$row['result_id'];
                $this->result_text=$row['result_text'];
                break;
			}
		}
        $sql = "select * from hr_employee_trip_info where trip_id=$index";
        $rows = Yii::app()->db->createCommand($sql)->queryAll();
        if (count($rows) > 0) {
            $this->addTime = array();
            foreach ($rows as $row) {

                $temp = array();
                $temp['id'] = $row['id'];
                $temp['trip_id'] = $row['trip_id'];
                $temp['start_time'] = General::toDate($row['start_time']);
                $temp['end_time'] = General::toDate($row['end_time']);
                $temp['start_time_lg'] = $row['start_time_lg'];
                $temp['end_time_lg'] = $row['end_time_lg'];
                $temp['uflag'] = 'N';
                $this->addTime[] = $temp;
            }
        }

        $sql = "select * from hr_employee_trip_money where trip_id=$index";
        $rows = Yii::app()->db->createCommand($sql)->queryAll();
        if (count($rows) > 0) {
            $this->addMoney = array();
            foreach ($rows as $row) {
                $temp = array();
                $temp['id'] = $row['id'];
                $temp['trip_id'] = $row['trip_id'];
                $temp['money_set_id'] = $row['money_set_id'];
                $temp['trip_money'] = floatval($row['trip_money']);
                $temp['uflag'] = 'N';
                $this->addMoney[] = $temp;
            }
        }
		return true;
	}

    //刪除驗證
    public function deleteValidate(){
        return true;
    }

    //出差结果驗證
    public function validateResult(){
        $row = Yii::app()->db->createCommand()
            ->select("a.*,b.wage,b.city as s_city,b.staff_type,b.code as employee_code,b.name as employee_name")
            ->from("hr_employee_trip a")
            ->leftJoin("hr_employee b","a.employee_id = b.id")
            ->where("a.id=:id and status=2",array(":id"=>$this->id))->queryRow();
        if($row){
            $this->id = $row['id'];
            $this->trip_code = $row['trip_code'];
            $this->employee_id = $row['employee_id'];
            $this->employee_code = $row['employee_code'];
            $this->employee_name = $row['employee_name'];
            $this->city = $row['s_city'];
            $this->trip_address = $row['trip_address'];
            $this->start_time = date("Y/m/d",strtotime($row['start_time']));
            $this->end_time = date("Y/m/d",strtotime($row['end_time']));
            $this->start_time_lg = $row['start_time_lg'];
            $this->end_time_lg = $row['end_time_lg'];
        }else{
            $message = "数据异常，请刷新重试";
            $this->addError("result_id",$message);
            return false;
        }
        if(empty($this->result_text)){
            $message = "出差结果说明不能为空";
            $this->addError("result_id",$message);
            return false;
        }
        return true;
    }

    //出差结果保存
    public function saveResult(){
        $uid = Yii::app()->user->id;
        Yii::app()->db->createCommand()->update('hr_employee_trip', array(
            'status'=>4,
            'luu'=>$uid,
            'result_id'=>0,
            'result_text'=>$this->result_text
        ), 'id=:id', array(':id'=>$this->id));

        $this->sendEmail("result");//發送郵件
    }

    //發送郵件
    private function sendEmail($status){
        switch ($status){
            case "result":
                $subject = "出差申请已填写结果 - ".$this->employee_name;
                $link = Yii::app()->createAbsoluteUrl("trip/view",array("index"=>$this->id));
                $message = "<p><a target='_blank' href='{$link}'>出差编号:".$this->trip_code."</a></p>";
                $message.= "<p>员工编号:".$this->employee_code."</p>";
                $message.= "<p>员工姓名:".$this->employee_name."</p>";
                $message.= "<p>员工城市:".CGeneral::getCityName($this->city)."</p>";
                $message.= "<p>目的地:".$this->trip_address."</p>";
                $message.= "<p>计划出差时间开始时间:".$this->start_time."</p>";
                $message.= "<p>计划出差时间结束时间:".$this->end_time."</p>";
                $message.= "<p>出差结果:".$this->result_text."</p>";
                $emailModel = new Email($subject,$message,$subject);
                if(is_array($this->appointList)){//指定审核人
                    foreach ($this->appointList as $auditUser){
                        $emailModel->addEmailToLcu($auditUser);
                    }
                }else{
                    $emailModel->addEmailToPrefixAndCity("ZG10",$this->city);
                }
                $emailModel->sent();
                return;
            case "audit":
                $subject = "出差申请审核 - ".$this->employee_name;
                $link = Yii::app()->createAbsoluteUrl("auditTrip/edit",array("index"=>$this->id));
                $message = "<p><a target='_blank' href='{$link}'>出差编号:".$this->trip_code."</a></p>";
                $message.= "<p>员工编号:".$this->employee_code."</p>";
                $message.= "<p>员工姓名:".$this->employee_name."</p>";
                $message.= "<p>员工城市:".CGeneral::getCityName($this->city)."</p>";
                $message.= "<p>目的地:".$this->trip_address."</p>";
                $message.= "<p>计划出差时间开始时间:".$this->start_time."</p>";
                $message.= "<p>计划出差时间结束时间:".$this->end_time."</p>";
                $emailModel = new Email($subject,$message,$subject);
                if($this->appointList){//指定审核人
                    $emailModel->addEmailToLcu($this->appointList[$this->z_index]);
                }else{
                    $emailModel->addEmailToPrefixAndCity("ZG10",$this->city);
                }
                $emailModel->sent();
                return;
        }
    }

	public function saveData()
	{
		$connection = Yii::app()->db;
		$transaction=$connection->beginTransaction();
		try {
			$this->saveGoods($connection);
            $this->saveDtl($connection);
            $this->saveDtlMoney($connection);
            $this->updateDocman($connection,'TRIP');
			$transaction->commit();
		}
		catch(Exception $e) {
			$transaction->rollback();
			throw new CHttpException(404,'Cannot update. ('.$e->getMessage().')');
		}
	}

    protected function updateDocman(&$connection, $doctype) {
        if ($this->scenario=='new') {
            $docidx = strtolower($doctype);
            if ($this->docMasterId[$docidx] > 0) {
                $docman = new DocMan($doctype,$this->id,get_class($this));
                $docman->masterId = $this->docMasterId[$docidx];
                $docman->updateDocId($connection, $this->docMasterId[$docidx]);
            }
            $this->scenario = "edit";
        }
    }

    protected function saveDtl(&$connection)
    {
        $city = Yii::app()->user->city();
        $uid = Yii::app()->user->id;

        foreach ($this->addTime as $row) {
            $sql = '';
            switch ($this->scenario) {
                case 'delete':
                    $sql = "delete from hr_employee_trip_info where trip_id = :trip_id";
                    break;
                case 'new':
                    if ($row['uflag']=='Y') {
                        $sql = "insert into hr_employee_trip_info(
									trip_id, start_time, start_time_lg, end_time, end_time_lg
								) values (
									:trip_id, :start_time, :start_time_lg, :end_time,:end_time_lg
								)";
                    }
                    break;
                case 'edit':
                    switch ($row['uflag']) {
                        case 'D':
                            $sql = "delete from hr_employee_trip_info where id = :id";
                            break;
                        case 'Y':
                            $sql = ($row['id']==0)
                                ?
                                "insert into hr_employee_trip_info(
									    trip_id, start_time, start_time_lg, end_time, end_time_lg
									) values (
									    :trip_id, :start_time, :start_time_lg, :end_time,:end_time_lg
									)
									"
                                :
                                "update hr_employee_trip_info set
										start_time = :start_time,
										start_time_lg = :start_time_lg, 
										end_time=:end_time,
										end_time_lg = :end_time_lg
									where id = :id 
									";
                            break;
                    }
                    break;
            }

            if ($sql != '') {
                $command=$connection->createCommand($sql);
                if (strpos($sql,':id')!==false)
                    $command->bindParam(':id',$row['id'],PDO::PARAM_INT);
                if (strpos($sql,':trip_id')!==false)
                    $command->bindParam(':trip_id',$this->id,PDO::PARAM_INT);
                if (strpos($sql,':start_time_lg')!==false) {
                    $command->bindParam(':start_time_lg',$row['start_time_lg'],PDO::PARAM_STR);
                }
                if (strpos($sql,':end_time_lg')!==false) {
                    $command->bindParam(':end_time_lg',$row['end_time_lg'],PDO::PARAM_STR);
                }
                if (strpos($sql,':end_time')!==false) {
                    $end_time = General::toMyDate($row['end_time']);
                    $command->bindParam(':end_time',$end_time,PDO::PARAM_STR);
                }
                if (strpos($sql,':start_time')!==false) {
                    $dead = General::toMyDate($row['start_time']);
                    $command->bindParam(':start_time',$dead,PDO::PARAM_STR);
                }
                $command->execute();
            }
        }
        return true;
    }

    protected function saveDtlMoney(&$connection)
    {
        $city = Yii::app()->user->city();
        $uid = Yii::app()->user->id;

        foreach ($this->addMoney as $row) {
            if(empty($row['money_set_id'])){
                continue;
            }
            $sql = '';
            switch ($this->scenario) {
                case 'delete':
                    $sql = "delete from hr_employee_trip_money where trip_id = :trip_id";
                    break;
                case 'new':
                    if ($row['uflag']=='Y') {
                        $sql = "insert into hr_employee_trip_money(
									trip_id, money_set_id, trip_money
								) values (
									:trip_id, :money_set_id, :trip_money
								)";
                    }
                    break;
                case 'edit':
                    switch ($row['uflag']) {
                        case 'D':
                            $sql = "delete from hr_employee_trip_money where id = :id";
                            break;
                        case 'Y':
                            $sql = ($row['id']==0)
                                ?
                                "insert into hr_employee_trip_money(
									    trip_id, money_set_id, trip_money
									) values (
									    :trip_id, :money_set_id, :trip_money
									)
									"
                                :
                                "update hr_employee_trip_money set
										money_set_id=:money_set_id,
										trip_money = :trip_money
									where id = :id 
									";
                            break;
                    }
                    break;
            }

            if ($sql != '') {
                $command=$connection->createCommand($sql);
                if (strpos($sql,':id')!==false)
                    $command->bindParam(':id',$row['id'],PDO::PARAM_INT);
                if (strpos($sql,':trip_id')!==false)
                    $command->bindParam(':trip_id',$this->id,PDO::PARAM_INT);
                if (strpos($sql,':money_set_id')!==false) {
                    $command->bindParam(':money_set_id',$row['money_set_id'],PDO::PARAM_INT);
                }
                if (strpos($sql,':trip_money')!==false) {
                    $row['trip_money'] = empty($row['trip_money'])?0:$row['trip_money'];
                    $command->bindParam(':trip_money',$row['trip_money'],PDO::PARAM_INT);
                }
                $command->execute();
            }
        }
        return true;
    }

	protected function saveGoods(&$connection) {
		$sql = '';
        switch ($this->scenario) {
            case 'delete':
                $sql = "delete from hr_employee_trip where id = :id";
                break;
            case 'new':
                $sql = "insert into hr_employee_trip(
							employee_id,company_name,trip_cause,trip_address,start_time, start_time_lg, end_time, end_time_lg,log_time,trip_cost,z_index, city, lcu,
							status,result_id
						) values (
							:employee_id,:company_name,:trip_cause,:trip_address,:start_time, :start_time_lg, :end_time, :end_time_lg,:log_time,:trip_cost,:z_index, :city, :lcu,
							:status,0
						)";
                break;
            case 'edit':
                $sql = "update hr_employee_trip set
							company_name = :company_name, 
							trip_cause = :trip_cause, 
							trip_cost = :trip_cost, 
							trip_address = :trip_address, 
							start_time_lg = :start_time_lg, 
							end_time_lg = :end_time_lg, 
							start_time = :start_time, 
							end_time = :end_time, 
							log_time = :log_time, 
							z_index = :z_index, 
							status = :status, 
							reject_cause = '', 
							pers_lcd = NULL, 
							user_lcd = NULL, 
							area_lcd = NULL, 
							head_lcd = NULL, 
							you_lcd = NULL, 
							luu = :luu
						where id = :id
						";
                break;
            case 'cancel':
                $sql = "update hr_employee_trip set
							reject_cause = :reject_cause, 
							status = 5
						where id = :id
						";
                //$sql = "delete from hr_employee_leave where id = :id";
                break;
            case 'reply':
                $sql = "update hr_employee_trip set
							status = 0, 
							luu = :luu
						where id = :id
						";
                //$sql = "delete from hr_employee_leave where id = :id";
                break;
        }
		if (empty($sql)) return false;

        $city = Yii::app()->user->city();
        $uid = Yii::app()->user->id;

        $command=$connection->createCommand($sql);
        if (strpos($sql,':id')!==false)
            $command->bindParam(':id',$this->id,PDO::PARAM_INT);
        if (strpos($sql,':employee_id')!==false)
            $command->bindParam(':employee_id',$this->employee_id,PDO::PARAM_STR);
        if (strpos($sql,':trip_cause')!==false)
            $command->bindParam(':trip_cause',$this->trip_cause,PDO::PARAM_STR);
        if (strpos($sql,':company_name')!==false)
            $command->bindParam(':company_name',$this->company_name,PDO::PARAM_STR);
        if (strpos($sql,':trip_cost')!==false)
            $command->bindParam(':trip_cost',$this->trip_cost,PDO::PARAM_STR);
        if (strpos($sql,':trip_address')!==false)
            $command->bindParam(':trip_address',$this->trip_address,PDO::PARAM_STR);
        if (strpos($sql,':start_time_lg')!==false)
            $command->bindParam(':start_time_lg',$this->start_time_lg,PDO::PARAM_STR);
        if (strpos($sql,':end_time_lg')!==false)
            $command->bindParam(':end_time_lg',$this->end_time_lg,PDO::PARAM_STR);
        if (strpos($sql,':start_time')!==false)
            $command->bindParam(':start_time',$this->start_time,PDO::PARAM_STR);
        if (strpos($sql,':end_time')!==false)
            $command->bindParam(':end_time',$this->end_time,PDO::PARAM_STR);
        if (strpos($sql,':log_time')!==false)
            $command->bindParam(':log_time',$this->log_time,PDO::PARAM_STR);
        if (strpos($sql,':status')!==false)
            $command->bindParam(':status',$this->status,PDO::PARAM_STR);
        if (strpos($sql,':z_index')!==false){
            $this->appointList = AppointTripSetForm::getAppointTripSet($this->employee_id);
            if($this->appointList){ //指定审核人
                $this->z_index = 10;
            }else{
                $this->z_index = 4;
            }
            $command->bindParam(':z_index',$this->z_index,PDO::PARAM_STR);
        }
        if (strpos($sql,':reject_cause')!==false)
            $command->bindParam(':reject_cause',$this->reject_cause,PDO::PARAM_STR);

        if (strpos($sql,':city')!==false)
            $command->bindParam(':city',$this->city,PDO::PARAM_STR);
        if (strpos($sql,':luu')!==false)
            $command->bindParam(':luu',$uid,PDO::PARAM_STR);
        if (strpos($sql,':lcu')!==false)
            $command->bindParam(':lcu',$uid,PDO::PARAM_STR);
        $command->execute();

        if ($this->scenario=='new'){
            $this->id = Yii::app()->db->getLastInsertID();
            $this->trip_code = $this->lenStr();
            Yii::app()->db->createCommand()->update('hr_employee_trip', array(
                'trip_code'=>$this->trip_code
            ), 'id=:id', array(':id'=>$this->id));
        }

        if($this->status==1){//要求審核
            //保存指定审核人
            $this->saveAppointUser();
            $this->sendEmail("audit");
        }
		return true;
	}

    //保存指定审核人资料
    private function saveAppointUser(){
        if($this->appointList&&is_array($this->appointList)){
            $userStr = array(
                10=>"pers_lcu",
                11=>"user_lcu",
                12=>"area_lcu",
                13=>"head_lcu",
                14=>"you_lcu",
            );
            $arr=array();
            foreach ($userStr as $key=>$item){
                $arr[$item] = key_exists($key,$this->appointList)?$this->appointList[$key]:null;
            }
            Yii::app()->db->createCommand()->update('hr_employee_trip', $arr, 'id=:id', array(':id'=>$this->id));
        }
    }

    private function lenStr(){
        $year = date("Y");
        $row = Yii::app()->db->createCommand()->select("trip_code")
            ->from("hr_employee_trip")
            ->where("trip_code like '{$year}%'")->order("trip_code desc")->queryRow();
        if($row){
            $str = intval($row["trip_code"])+1;
        }else{
            $str = $year*100000+1;
        }
        return $str;
    }

    public function ready(){
        return $this->getScenario()=='view'||!in_array($this->status,array(0,3));
    }
}
