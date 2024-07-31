<?php

class LeaveForm extends CFormModel
{
	public $id;
	public $leave_code;
	public $employee_id;
	public $vacation_id;
	public $leave_cause;//加班原因 
    public $leave_cost;//加班費用
	public $start_time;
	public $start_time_lg;
	public $end_time;
	public $end_time_lg='PM';
	public $log_time;
	public $z_index;//1:部門審核、2：主管、3：總監、4：你
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
	public $vacation_list;//倍率
	public $city;
	public $lcd;
	public $audit = false;//是否需要審核
    public $wage;//合約工資
    public $staff_type;//員工的辦公類型

    public $state;//員工的辦公類型

    public $addTime=array();

    public $no_of_attm = array(
        'leave'=>0
    );
    public $docType = 'LEAVE';
    public $docMasterId = array(
        'leave'=>0
    );
    public $files;
    public $removeFileId = array(
        'leave'=>0
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
            'leave_code'=>Yii::t('fete','Leave Code'),
            'vacation_id'=>Yii::t('fete','Leave Type'),
            'leave_cause'=>Yii::t('fete','Leave Cause'),
            'leave_cost'=>Yii::t('fete','Leave Cost'),
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
            'head_lcd'=>Yii::t('fete','head lcd'),
            'you_lcu'=>Yii::t('fete','you lcu'),
            'you_lcd'=>Yii::t('fete','you lcd'),
            'audit_remark'=>Yii::t('fete','Audit Remark'),
            'reject_cause'=>$reject_cause,
            'wage'=>Yii::t('contract','Contract Pay'),
            'lcd'=>Yii::t('fete','apply for time'),
            'state'=>Yii::t('contract','Status'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id,addTime,leave_code,employee_id,vacation_id,city,status,leave_cause,start_time,end_time,start_time_lg,end_time_lg,log_time,lcd,reject_cause','safe'),
            array('id','validateRejectCause','on'=>array("cancel")),
            array('employee_id','validateUser','on'=>array("new","edit","audit")),
            array('vacation_id','required','on'=>array("new","edit","audit")),
            array('leave_cause','required','on'=>array("new","edit","audit")),
            array('log_time','required','on'=>array("new","edit","audit")),
            array('addTime','validateEndTime','on'=>array("new","edit","audit")),
            array('log_time','validateLogTime','on'=>array("new","edit","audit")),
            array('log_time','numerical','allowEmpty'=>true,'integerOnly'=>false,'on'=>array("new","edit","audit")),
            array('vacation_id','validateLeaveType','on'=>array("new","edit","audit")),
            array('files, removeFileId, docMasterId','safe'),
		);
	}

	public function validateRejectCause($attribute, $params){
        if(empty($this->reject_cause)){
            $message = Yii::t('contract','cancel cause').Yii::t('contract',' can not be empty');
            $this->addError($attribute,$message);
        }else{
            $row = Yii::app()->db->createCommand()->select("id")->from("hr_employee_leave")
                ->where("id=:id and status=4",array(":id"=>$this->id))->queryRow();
            if(!$row){
                $message = "請假單不存在，請於管理員聯繫";
                $this->addError($attribute,$message);
            }
        }
    }

	public function validateUser($attribute, $params){
        if(Yii::app()->user->validFunction('ZR06')){
            if(empty($this->employee_id)){
                $message = Yii::t('contract','Employee Name').Yii::t('contract',' not exist');
                $this->addError($attribute,$message);
            }else{
                $employeeList = StaffFun::getEmployeeOneToId($this->employee_id);
                if($employeeList){
                    $this->city = $employeeList["city"];
                }else{
                    $message = "用戶不存在";
                    $this->addError($attribute,$message);
                }
            }
        }
    }
    //獲取年假的最大日期
    public function getMaxYearLeaveDate($employee_id,$time){
        $entry_time = date("Y/m/d",strtotime(date("Y/m/d")."+2 year"));
        $sql = "SELECT entry_time FROM hr_employee WHERE staff_status = 0 AND id=$employee_id";
        $row = Yii::app()->db->createCommand($sql)->queryRow();
        if($row){
            $year = empty($time)?date("Y"):date("Y",strtotime($time));
            $thisMonth = empty($time)?date("/m/d"):date("/m/d",strtotime($time));
            $month = date("/m/d",strtotime($row["entry_time"]." - 1 day"));
            if($thisMonth>$month){
                $year++;
            }
            $entry_time = $year.$month;
        }
        return $entry_time;
    }

	//驗證請假類型
    public function validateLeaveType($attribute, $params){
        $model = new VacationDayForm($this->employee_id,$this->vacation_id,$this->start_time);
        $leaveNum = $model->getVacationSum();
        $row = Yii::app()->db->createCommand()->select("*")->from("hr_vacation")
            ->where("id=:id",array(":id"=>$this->vacation_id))->queryRow();
        if ($model->getErrorBool()||!$row){
            $message = Yii::t('fete','Leave Type').Yii::t('contract',' not exist');
            $this->addError($attribute,$message);
        }else{
            $this->vacation_list = $row;
            $maxDate = $model->getEndTime();
            if($model->remain_bool){
                if(floatval($this->log_time) > $leaveNum){
                    $message = Yii::t('fete','Log Date')."不能大于".$leaveNum."天";
                    $this->addError($attribute,$message);
                }
                if(date("Y-m-d",strtotime($this->end_time))>date("Y-m-d",strtotime($maxDate))){
                    $message = Yii::t('contract','End Time')."不能大于".$maxDate;
                    $this->addError($attribute,$message);
                }
            }
            /*if($this->vacation_list["vaca_type"]=="A"&&$this->audit) { //请假类型为调休
                $work_id = key_exists("work_id",$_POST)?$_POST["work_id"]:0;
                if(empty($work_id)){
                    $message = "加班单不能为空";
                    $this->addError($attribute,$message);
                }
            }*/
        }
    }

	//驗證時間週期
    public function validateLogTime($attribute, $params){
        if(!empty($this->log_time)){
            if(!is_numeric($this->log_time)){
                $message = Yii::t('fete','Log Date')."必須为数字";
                $this->addError($attribute,$message);
            }else{
                if (strpos($this->log_time,'.')!==false){
                    //含有小數
                    $float = end(explode(".",$this->log_time));
                    $float = intval($float);
                    if($float !== 5 && $float !== 0){
                        $message = Yii::t('fete','Log Date')."的小数必须为0.5";
                        $this->addError($attribute,$message);
                    }
                }
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
                    $sql = "select b.leave_code from hr_employee_leave_info a LEFT JOIN hr_employee_leave b ON a.leave_id = b.id WHERE b.status != 5 AND ((a.start_time>'$startTime' AND a.end_time <'$endTime') OR (a.start_time<='$startTime' AND a.end_time >='$startTime') OR (a.start_time<='$endTime' AND a.end_time >='$endTime')) ";
                    //var_dump($sql);die();
                    if(Yii::app()->user->validFunction('ZR06')){
                        $sql.=" and b.employee_id='".$this->employee_id."'";
                    }else{
                        $sql.=" and b.employee_id='".$this->getEmployeeIdToUser()."'";;
                    }
                    if(!empty($this->id)&&is_numeric($this->id)){
                        $sql.=" and b.id!=".$this->id;
                    }
                    $connection = Yii::app()->db;
                    $rows = $connection->createCommand($sql)->queryRow();
                    if($rows){
                        $message = Yii::t('fete','A leave order has been issued during this period')."：".$rows["leave_code"];
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

    //根據加班id獲取加班信息
    public function getLeaveListToLeaveId($leave_id){
        $connection = Yii::app()->db;
        $sql = "select a.*,b.name AS employee_name,b.code AS employee_code ,b.entry_time,b.department,b.position,d.name AS vacation_name,d.vaca_type,e.name as company_name
                from hr_employee_leave a 
                LEFT JOIN hr_employee b ON a.employee_id = b.id
                LEFT JOIN hr_company e ON b.company_id=e.id
                LEFT JOIN hr_vacation d ON a.vacation_id=d.id
                where a.id =$leave_id
			";
        $records = $connection->createCommand($sql)->queryRow();
        if($records){
            $this->resetLeaveType($records);
            $records["dept_name"]=DeptForm::getDeptToId($records["department"]);
            $records["posi_name"]=DeptForm::getDeptToId($records["position"]);
            $model = new VacationDayForm($records["employee_id"],$records["vacation_id"],$records["start_time"]);
            $model->getVacationSum($records['lcd']);
            $records["sumDay"]=$model->getSumDay()+$model->getExtraDay();
            $records["leaveNum"]=$model->getUseDay();
            return $records;
        }else{
            return false;
        }
    }

    private function resetLeaveType(&$records){
        $arr = array(
            "A"=>array("A","F"),//A类：加班调休、年休假、特别调休
            "B"=>array("B","G","H","I","J","K"),//B 类：婚假、丧假、护理假、产假、晚育假、哺乳假
            "C"=>array("C","L"),//C类：产前假、病假
            "D"=>array("D"),//D类：事假
            "E"=>array("E")//年假
        );
        foreach ($arr as $key => $list){
            if(in_array($records["vaca_type"],$list)){
                $records["vaca_type"] = $key;
                break;
            }
        }

        //後續請假表能填寫多個時間段（擴充）
        $rows = Yii::app()->db->createCommand()->select("*")
            ->from("hr_employee_leave_info")->where("leave_id=:leave_id",array(":leave_id"=>$records["id"]))->queryAll();
        $records["time_list"]=array();
        if($rows){
            foreach ($rows as $row){
                $records["time_list"][]=array(
                    "start_time"=>$row["start_time"],
                    "start_time_lg"=>self::getAMPMList($row["start_time_lg"],true),
                    "end_time"=>$row["end_time"],
                    "end_time_lg"=>self::getAMPMList($row["end_time_lg"],true)
                );
            }
        }else{
            $records["time_list"][]=array(
                "start_time"=>$records["start_time"],
                "start_time_lg"=>self::getAMPMList($records["start_time_lg"],true),
                "end_time"=>$records["end_time"],
                "end_time_lg"=>self::getAMPMList($records["end_time_lg"],true)
            );
        }
    }

    //獲取員工的簽名信息
    public function getSignatureToStaffId($staff_id,$bool = true){
        if($bool){
            $row = Yii::app()->db->createCommand()->select("*")
                ->from("hr_binding")->where("employee_id=:employee_id",array(":employee_id"=>$staff_id))->queryRow();
        }else{
            $row = array("user_id"=>$staff_id);
        }
        if($row){
            $suffix = Yii::app()->params['envSuffix'];
            $user_id = $row["user_id"];
            $field_blob = Yii::app()->db->createCommand()->select("field_blob")
                ->from("security$suffix.sec_user_info")->where("username=:username and field_id='signature'",array(":username"=>$user_id))->queryRow();
            if($field_blob){
                $field_blob = $field_blob["field_blob"];
                $field_value = Yii::app()->db->createCommand()->select("field_value")
                    ->from("security$suffix.sec_user_info")->where("username=:username and field_id='signature_file_type'",array(":username"=>$user_id))->queryRow();
                if($field_value){
                    $field_value = $field_value["field_value"];
                    if(!empty($field_value)&&!empty($field_blob)){
                        return array(
                            "field_blob"=>$field_blob,
                            //"field_blob"=>base64_decode($field_blob),
                            "field_value"=>$field_value,
                        );
                    }
                }
            }
        }
        return false;
    }

    //某年累積的請假天數（僅年假)
    public function getLeaveNumToYear($employee_id,$time="",$endBool=false,$lcd=''){
        if(empty($employee_id)){
            return 0;
        }
        $rows = Yii::app()->db->createCommand()->select("*")
            ->from("hr_employee")->where("id=:id",array(":id"=>$employee_id))->queryRow();
        if(!$rows){
            return 0;
        }
        if(empty($time)){
            $time = date("Y-m-d H:i:s");
        }
        $year = date("Y",strtotime($time));
        $month = date("m",strtotime($rows["entry_time"]));
        $day = date("d",strtotime($rows["entry_time"]));
        if(date("m-d",strtotime($time))>=date("m-d",strtotime($rows["entry_time"]))){
            $start_time = "$year-$month-$day 00:00:00";
            $end_time = (intval($year)+1)."-$month-$day 00:00:00";
        }else{
            $start_time = (intval($year)-1)."-$month-$day 00:00:00";
            $end_time = "$year-$month-$day 00:00:00";
        }
        $statusSql = "a.status IN (1,2,4)";
        if($endBool){
            //$end_time = date("Y-m-d 23:59:59",strtotime($time));
            $statusSql = "a.status =  4 and a.lcd<='$lcd'";
        }
        $sql = "select sum(a.log_time) AS sumDay from hr_employee_leave a 
            LEFT JOIN hr_vacation b ON a.vacation_id = b.id
            WHERE a.start_time>'$start_time'AND a.start_time<='$end_time' AND $statusSql AND b.vaca_type='E' AND a.employee_id=$employee_id";
        //var_dump($sql);die();
        $Sum = Yii::app()->db->createCommand($sql)->queryRow();
        if($Sum){
            return $Sum["sumDay"];
        }else{
            return 0;
        }
    }

	public function retrieveData($index) {
        $lcuId = Yii::app()->user->id;
        $city_allow = Yii::app()->user->city_allow();
        $suffix = Yii::app()->params['envSuffix'];
        $rows = Yii::app()->db->createCommand()->select("a.*,b.wage,b.city as s_city,b.staff_type,b.name as employee_name,docman$suffix.countdoc('LEAVE',a.id) as leavedoc")
            ->from("hr_employee_leave a")
            ->leftJoin("hr_employee b","a.employee_id = b.id")
            ->where("a.id=:id",array(":id"=>$index))->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
                $this->id = $row['id'];
                $this->leave_code = $row['leave_code'];
                $this->employee_id = $row['employee_id'];
                $this->wage = $row['wage'];
                $this->staff_type = $row['staff_type'];
                $this->vacation_id = $row['vacation_id'];
                $this->leave_cause = $row['leave_cause'];
                $this->start_time = date("Y/m/d",strtotime($row['start_time']));
                $this->end_time = date("Y/m/d",strtotime($row['end_time']));
                $this->log_time = $row['log_time'];
                $this->z_index = $row['z_index'];
                $this->start_time_lg = $row['start_time_lg'];
                $this->end_time_lg = $row['end_time_lg'];
                $this->state = LeaveForm::translationState($row['z_index']);
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
                $this->leave_cost = $row['leave_cost'];
                $this->no_of_attm['leave'] = $row['leavedoc'];
                break;
			}
		}
		return true;
	}

//1:部門審核、2：主管、3：總監、4：你
	public static function translationState($str){
        switch ($str){
            case 1:
                return "部門審核（數據輸入 → 審核）";
            case 2:
                return "主管審核（員工 → 審核）";
            case 3:
                return "總監審核（審核 → 審核）";
            case 4:
                return "最高審核（系統設置 → 審核）";
            case 5:
                return "人事審核（人事 → 審核）";
            case 10:
                return "指定審核（審核）";
            default:
                return "指定審核（審核）".$str;
        }
    }

    public function parintLeaveTimeTable($bool){
	    $list = $this->addTime;
	    if(empty($list)){
            $list = Yii::app()->db->createCommand()->select("*")
                ->from("hr_employee_leave_info")->where("leave_id=:leave_id",array(":leave_id"=>$this->id))->queryAll();
        }
        if(empty($list)){
	        $list[] = array('start_time'=>$this->start_time,'start_time_lg'=>$this->start_time_lg,'end_time'=>$this->end_time,'end_time_lg'=>$this->end_time_lg);
        }
        $html = "<table class='table table-bordered table-striped' id='addTimeTable'><thead><tr>";
        $html.="<th>".Yii::t("contract","Start Time")."</th>";
        $html.="<th>".Yii::t("contract","End Time")."</th>";
        if(!$bool){
            $html.="<th>&nbsp;</th>";
        }
        $html.="</tr></thead><tbody  data-num='".count($list)."'>";
        foreach ($list as $key =>$row){
            $html.=$this->getTableTrHtml("LeaveForm[addTime][$key]",$row,$bool);
        }
        if(!$bool){
            $html.=$this->getTableTrHtml("#key#",array('start_time'=>'','start_time_lg'=>'','end_time'=>'','end_time_lg'=>'PM'),$bool);
            $html.="<tfoot><tr><td colspan='2'>&nbsp;</td><td>".TbHtml::button(Yii::t("app","New"),array("class"=>"btn btn-primary","id"=>"addLeaveTime"))."</td></tr></tfoot>";
        }

	    $html.="</tbody></table>";

	    echo $html;
    }

    private function getTableTrHtml($keyName,$row,$bool){
        if(!empty($row["start_time"])){
            $row["start_time"] = date("Y/m/d",strtotime($row["start_time"]));
        }
        if(!empty($row["end_time"])){
            $row["end_time"] = date("Y/m/d",strtotime($row["end_time"]));
        }
        $html='';
        if($keyName=="#key#"){
            $html.="<tr style='display: none;' id='leaveTrModel'>";
        }else{
            $html.="<tr>";
        }
        $html.="<td>";

        $html.='<div class="input-group"><div class="input-group-addon"><i class="fa fa-calendar"></i></div>';
        $html.=TbHtml::textField($keyName."[start_time]",$row["start_time"],array("readonly"=>$bool,'class'=>"dateTime s_time"));
        $html.='<div class="input-group-btn" style="width: 100px;">';
        $html.=TbHtml::dropDownList($keyName."[start_time_lg]",$row["start_time_lg"],LeaveForm::getAMPMList(),array("readonly"=>$bool,'class'=>"s_long"));
        $html.='</div></div>';

        $html.="</td>";
        $html.="<td>";

        $html.='<div class="input-group"><div class="input-group-addon"><i class="fa fa-calendar"></i></div>';
        $html.=TbHtml::textField($keyName."[end_time]",$row["end_time"],array("readonly"=>$bool,'class'=>"dateTime e_time"));
        $html.='<div class="input-group-btn" style="width: 100px;">';
        $html.=TbHtml::dropDownList($keyName."[end_time_lg]",$row["end_time_lg"],LeaveForm::getAMPMList(),array("readonly"=>$bool,'class'=>"e_long"));
        $html.='</div></div>';

        $html.="</td>";
        if(!$bool){
            $html.="<td>".TbHtml::button(Yii::t("dialog","Remove"),array("class"=>"btn btn-danger delWages"))."</td>";
        }
        $html.="</tr>";
        return $html;
    }

    //刪除驗證
    public function deleteValidate(){
        return true;
    }
    //獲取員工工作日
    public function getUserWorkDay(){
        $dayNum = $this->staff_type == "Office"?22:26;
        return $dayNum;
    }

    //獲取加班調休列表
    public static function getWorkIDForLeaveID($leave_id){
        $work_id=array();
        $rows = Yii::app()->db->createCommand()->select("work_id")->from("hr_work_leave")
            ->where("leave_id=:leave_id",array(":leave_id"=>$leave_id))
            ->queryAll();
        if($rows){
            foreach ($rows as $row){
                $work_id[]=$row["work_id"];
            }
        }
        return implode(",",$work_id);
    }

    //獲取加班調休列表
    public static function getWorkListForEmployeeId($employee_id){
        $list = array();
        $rows = Yii::app()->db->createCommand()
            ->select("a.id as work_id,a.work_code,a.start_time,a.log_time,a.status,b.leave_id")
            ->from("hr_employee_work a")
            ->leftJoin("(select work_id,max(leave_id) as leave_id from hr_work_leave group by work_id) b","a.id=b.work_id")
            ->where("a.employee_id=:employee_id and a.work_type=4 and a.status in (1,2,4)",array(":employee_id"=>$employee_id))
            ->order("a.start_time desc")->queryAll();
        if($rows){
            $list = array_merge($list,$rows);
        }
        return $list;
    }

    //獲取加班調休的选择html
    public static function workSelectHtml($employee_id,$value=array(),$ready=false){
        $value = empty($value)?array():$value;
        $value = is_array($value)?$value:explode(",",$value);
        if($ready){
            $html = "<select class=\"form-control select2 disabled\" multiple name=\"work_id[]\" disabled id='work_id'>";
        }else{
            $html = "<select class=\"form-control select2\" multiple name=\"work_id[]\" id='work_id'>";
        }
        $list = self::getWorkListForEmployeeId($employee_id);
        foreach ($list as $key=>$row){
            if(!is_array($row)){
                $html.="<option value='{$key}'>{$row}</option>";
                continue;
            }
            if($row["status"]==4&&(empty($row["leave_id"])||in_array($row["work_id"],$value))){
                $html.="<option value='{$row['work_id']}'";
            }else{
                $html.="<option value='{$row['work_id']}' disabled class='disabled' style='background:rgb(210, 214, 222);'";
            }
            if(in_array($row["work_id"],$value)){
                $html.=" selected ";
            }
            $html.=">";
            $html.="加班编号：".$row["work_code"]."，开始时间：".CGeneral::toDate($row["start_time"]);
            $html.="，加班时长：".floatval($row["log_time"])."小时，状态：";
            $html.=$row["status"]!=4?"审批中":(empty($row["leave_id"])||in_array($row["work_id"],$value)?"可调休":"已调休");
            $html.="</option>";
        }
        $html.= "</select>";
        return $html;
    }

    //调休
    public static function getWorkSelectDiv($vacationList,$employee_id,$work_id,$ready=false){
        $html = "";
        //请假类型为：调休
        if(key_exists("vaca_type",$vacationList)&&$vacationList["vaca_type"]=="A"){
            $html.= "<div class=\"form-group\">";
            //.'<span class="required">*</span>'
            $html.= TbHtml::label("选择加班单","",array("class"=>"col-lg-2 control-label"));
            $html.= "<div class=\"col-lg-7\">";
            $html.= self::workSelectHtml($employee_id,$work_id,$ready);
            $html.= "</div>";
            $html.= "</div>";
        }
        return $html;
    }

    //獲取假期的倍率
    public function getMuplite(){
        $id = $this->vacation_id;
        $rows = Yii::app()->db->createCommand()->select("*")
            ->from("hr_vacation")->where("id='$id'")->queryRow();
        if($rows){
            $sub_multiple = floatval($rows["sub_multiple"])/100;
        }else{
            $sub_multiple = 1.5;
        }
        return $sub_multiple;
    }
    //獲取當前城市的所有請假類型
    public static function getLeaveTypeList($city,$id=0){
        $id = empty($id)?0:$id;
        if(empty($city)){
            $city = Yii::app()->user->city();
        }
        $arr = array();
        $rows = Yii::app()->db->createCommand()->select("a.id,a.name")
            ->from("hr_vacation a")->where("((a.city='$city' OR a.only='default') and a.z_display=1) OR a.id=:id",array(":id"=>$id))->order("a.vaca_type in ('E') desc")->queryAll();
        if($rows){
            foreach ($rows as $row){
                $arr[$row["id"]] = $row["name"];
            }
        }
        return $arr;
    }
    //獲取請假類型说明
    public static function getLeaveTypeRemark($id=0){
        $row = Yii::app()->db->createCommand()->select("remark")
            ->from("hr_vacation")->where("id=:id",array(":id"=>$id))->queryRow();
        return $row?$row["remark"]:"";
    }

	public function saveData()
	{
		$connection = Yii::app()->db;
		$transaction=$connection->beginTransaction();
		try {
			$this->saveGoods($connection);
            $this->saveLeaveWork();//保存调休的加班单
            $this->updateDocman($connection,'LEAVE');
			$transaction->commit();
		}
		catch(Exception $e) {
			$transaction->rollback();
			throw new CHttpException(404,'Cannot update. ('.$e->getMessage().')');
		}
	}

    //保存调休的加班单
    protected function saveLeaveWork() {
        if($this->vacation_list["vaca_type"]=="A"){ //请假类型为调休
            $work_list = key_exists("work_id",$_POST)?$_POST["work_id"]:array();
            if(!empty($work_list)){
                foreach ($work_list as $work_id){
                    $row = Yii::app()->db->createCommand()->select("id")->from("hr_work_leave")
                        ->where("leave_id=:id and work_id=:work_id",array(":id"=>$this->id,":work_id"=>$work_id))->queryRow();
                    if($row){//修改
                        Yii::app()->db->createCommand()->update('hr_work_leave', array(
                            'work_id'=>$work_id
                        ), 'id=:id', array(':id'=>$row["id"]));
                    }else{//新增
                        Yii::app()->db->createCommand()->insert('hr_work_leave', array(
                            'work_id'=>$work_id,
                            'leave_id'=>$this->id
                        ));
                    }
                }
                return true;
            }
        }
        Yii::app()->db->createCommand()->delete('hr_work_leave',"leave_id={$this->id}");
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

	protected function saveGoods(&$connection) {
		$sql = '';
        switch ($this->scenario) {
            case 'delete':
                $sql = "delete from hr_employee_leave where id = :id";
                break;
            case 'cancel':
                $sql = "update hr_employee_leave set
							reject_cause = :reject_cause, 
							status = 5
						where id = :id
						";
                //$sql = "delete from hr_employee_leave where id = :id";
                break;
            case 'new':
                $sql = "insert into hr_employee_leave(
							employee_id,vacation_id,leave_cause, start_time_lg, end_time_lg, start_time, end_time, log_time, leave_cost, city, z_index, status, lcu
						) values (
							:employee_id,:vacation_id,:leave_cause, :start_time_lg, :end_time_lg, :start_time, :end_time, :log_time, :leave_cost, :city, :z_index, :status, :lcu
						)";
                break;
            case 'edit':
                $sql = "update hr_employee_leave set
							vacation_id = :vacation_id, 
							employee_id = :employee_id, 
							leave_cause = :leave_cause, 
							leave_cost = :leave_cost, 
							start_time_lg = :start_time_lg, 
							end_time_lg = :end_time_lg, 
							start_time = :start_time, 
							end_time = :end_time, 
							log_time = :log_time, 
							city = :city, 
							z_index = :z_index, 
							lcd = :lcd, 
							status = :status, 
							reject_cause = '', 
							luu = :luu
						where id = :id
						";
                break;
        }
		if (empty($sql)) return false;

        $city = Yii::app()->user->city();
        $uid = Yii::app()->user->id;//ZR06
        if(!Yii::app()->user->validFunction('ZR06')){
            $employeeList = $this->getEmployeeOneToUser();
            $this->employee_id = $employeeList["id"];
            $this->city = $employeeList["city"];
        }

        $this->resetLeaveCost();//計算員工的工資

        $command=$connection->createCommand($sql);
        if (strpos($sql,':id')!==false)
            $command->bindParam(':id',$this->id,PDO::PARAM_INT);
        if (strpos($sql,':employee_id')!==false)
            $command->bindParam(':employee_id',$this->employee_id,PDO::PARAM_STR);
        if (strpos($sql,':vacation_id')!==false)
            $command->bindParam(':vacation_id',$this->vacation_id,PDO::PARAM_STR);
        if (strpos($sql,':leave_cause')!==false)
            $command->bindParam(':leave_cause',$this->leave_cause,PDO::PARAM_STR);
        if (strpos($sql,':leave_cost')!==false)
            $command->bindParam(':leave_cost',$this->leave_cost,PDO::PARAM_STR);
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
        if (strpos($sql,':reject_cause')!==false)
            $command->bindParam(':reject_cause',$this->reject_cause,PDO::PARAM_STR);
        if (strpos($sql,':z_index')!==false){
            $this->appointList = AppointSetForm::getAppointSet($this->employee_id);
            if($this->appointList){ //指定审核人
                $this->z_index = 10;
            }else{
                $z_index = AuditConfigForm::getCityAuditToCode($this->employee_id,1);
                $this->z_index = $z_index;
            }
            $command->bindParam(':z_index',$this->z_index,PDO::PARAM_STR);
        }

        if (strpos($sql,':lcd')!==false)
            $command->bindParam(':lcd',date('Y-m-d H:i:s'),PDO::PARAM_STR);
        if (strpos($sql,':city')!==false)
            $command->bindParam(':city',$this->city,PDO::PARAM_STR);
        if (strpos($sql,':luu')!==false)
            $command->bindParam(':luu',$uid,PDO::PARAM_STR);
        if (strpos($sql,':lcu')!==false)
            $command->bindParam(':lcu',$uid,PDO::PARAM_STR);
        $command->execute();

        if ($this->scenario=='new'){
            $this->id = Yii::app()->db->getLastInsertID();
            Yii::app()->db->createCommand()->update('hr_employee_leave', array(
                'leave_code'=>"Q".$this->lenStr($this->id)
            ), 'id=:id', array(':id'=>$this->id));
        }

        //保存指定审核人
        $this->saveAppointUser();
        //發送郵件
        $this->sendEmail();

        foreach ($this->addTime as $list){
            Yii::app()->db->createCommand()->insert("hr_employee_leave_info", array(
                "leave_id"=>$this->id,
                "start_time"=>$list["start_time"],
                "start_time_lg"=>$list["start_time_lg"],
                "end_time_lg"=>$list["end_time_lg"],
                "end_time"=>$list["end_time"]
            ));
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
            Yii::app()->db->createCommand()->update('hr_employee_leave', $arr, 'id=:id', array(':id'=>$this->id));
        }
    }

	protected function sendEmail(){
        if($this->audit){
            $assList=array(
                1=>"ZA09",
                2=>"ZE06",
                3=>"ZG05",
                4=>"ZC11",
                5=>"ZP02",
                10=>"ZG11",//指定审核人
            );
            $email = new Email();
            $row = Yii::app()->db->createCommand()->select("*")->from("hr_employee")
                ->where('id=:id', array(':id'=>$this->employee_id))->queryRow();
            $description="新的请假单 - ".$row["name"];
            $subject="新的请假单 - ".$row["name"];
            $message="<p>请假编号：".$this->leave_code."</p>";
            $message.="<p>员工编号：".$row["code"]."</p>";
            $message.="<p>员工姓名：".$row["name"]."</p>";
            $message.="<p>员工城市：".General::getCityName($row["city"])."</p>";
            $message.="<p>请假时间：".$this->start_time." ~ ".$this->end_time."  (".$this->log_time."天)</p>";
            $message.="<p>请假原因：".$this->leave_cause."</p>";
            $email->setDescription($description);
            $email->setMessage($message);
            $email->setSubject($subject);
            $assType = $assList[$this->z_index];
            switch ($this->z_index){
                case 1:
                    $email->addEmailToPrefixAndPoi($assType,$row["department"],$row["group_type"]);
                    break;
                case 2:
                    $email->addEmailToPrefixAndOnlyCity($assType,$row["city"]);
                    break;
                case 5:
                    $email->addEmailToPrefixAndOnlyCity($assType,$row["city"]);
                    break;
                case 10://指定审核人
                    $email->addEmailToLcu($this->appointList[$this->z_index]);
                    break;
                default:
                    $email->addEmailToPrefixAndCity($assType,$row["city"]);
            }
            $email->sent();
        }
    }


	//獲取綁定員工的列表(解決地區變化問題$staff_id)
    public static function getBindEmployeeList($staff_id=0,$city=''){
        $city = empty($city)?Yii::app()->user->city():$city;
        $city_allow = Yii::app()->user->city_allow();
        $arr = array(""=>"");
        $rows = Yii::app()->db->createCommand()->select("a.employee_id as id,b.name as name")->from("hr_binding a")
            ->leftJoin("hr_employee b","a.employee_id=b.id")
            ->where("b.city='{$city}' or b.id=:id",array(":id"=>$staff_id))->queryAll();
        if($rows){
            foreach ($rows as $row){
                $arr[$row["id"]] = $row["name"];
            }
        }
        return $arr;
    }


    //獲取上午下午列表
    public static function getAMPMList($str='',$bool=false){
        $arr = array(
            "AM"=>Yii::t("fete","AM"),
            "PM"=>Yii::t("fete","PM")
        );
        if($bool){
            if(key_exists($str,$arr)){
                return $arr[$str];
            }else{
                return $str;
            }
        }else{
            return $arr;
        }
    }

    private function foreachAddTime(){
        Yii::app()->db->createCommand()->delete("hr_employee_leave_info", "leave_id=:leave_id",array("leave_id"=>$this->id));
        $day = 0;
        foreach ($this->addTime as &$list){
            $startTime = strtotime($list["start_time"]);
            $weekStart = getdate($startTime);
            $startPm = $list["start_time_lg"];
            $endTime = strtotime($list["end_time"]);
            $weekEnd = getdate($endTime);
            $endPm = $list["end_time_lg"];
            $day += ($endTime-$startTime)/(60*60*24);
            if($startPm != $endPm){
                if($startPm =="AM"){
                    $day++;
                }
            }else{
                $day+=0.5;
            }
            if($startPm == "AM"){
                $list["start_time"].=" 9:00:00";
            }else{
                $list["start_time"].=" 13:00:00";
            }
            if($endPm == "AM"){
                $list["end_time"].=" 12:00:00";
            }else{
                $list["end_time"].=" 18:00:00";
            }
            if(in_array($weekStart["wday"],array(0,6))||in_array($weekEnd["wday"],array(0,6))||$day>=6||$weekStart["wday"]>$weekEnd["wday"]){
                //允許修改時間
                return false;
            }
        }
        $this->log_time = $day;
    }

	//計算員工的請假費用
    public function resetLeaveCost(){
        if($this->audit){
            $this->status = 1;
        }else{
            $this->status = 0;
        }
        $this->foreachAddTime();
        $employeeList = StaffFun::getEmployeeOneToId($this->employee_id);
        $wage = floatval($employeeList["wage"]);
        $vacationList = $this->vacation_list;
        if($vacationList["sub_bool"] == 1){ //
            $dayNum = $employeeList["staff_type"] == "Office"?22:26;
            $sub_multiple = floatval($vacationList["sub_multiple"])/100;
            $this->leave_cost = ($wage/$dayNum)*floatval($this->log_time)*$sub_multiple;
        }else{
            $this->leave_cost = 0;
        }
    }

    private function lenStr($id){
        $code = strval($id);
//Percy: Yii::app()->params['employeeCode']用來處理不同地區版本不同字首
        $str = Yii::app()->params['employeeCode'];
        for($i = 0;$i < 5-strlen($code);$i++){
            $str.="0";
        }
        $str .= $code;
        $this->leave_code = $str;
        return $str;
    }

	//獲取當前用戶的員工id
	public function getEmployeeIdToUser(){
        $uid = Yii::app()->user->id;
        $rows = Yii::app()->db->createCommand()->select("employee_id")->from("hr_binding")
            ->where('user_id=:user_id',
                array(':user_id'=>$uid))->queryRow();
        if ($rows){
            return $rows["employee_id"];
        }
        return "";
    }
	//獲取當前用戶的員工id
	public function getEmployeeOneToUser(){
        $uid = Yii::app()->user->id;
        $rows = Yii::app()->db->createCommand()->select("b.id,b.city")->from("hr_binding a")
            ->leftJoin("hr_employee b","a.employee_id=b.id")
            ->where('user_id=:user_id',array(':user_id'=>$uid))->queryRow();
        if ($rows){
            return $rows;
        }
        return "";
    }

	//判斷輸入框能否修改
	public function getInputBool(){
        if($this->scenario == "view"){
            return true;
        }
        if(!in_array($this->status,array(0,3))){
            return true;
        }
        return false;
    }

    //驗證賬號是否綁定員工
    public function validateEmployee(){
        $uid = Yii::app()->user->id;
        $rows = Yii::app()->db->createCommand()->select("a.employee_id,b.name,b.city")
            ->from("hr_binding a")
            ->leftJoin("hr_employee b","a.employee_id=b.id")
            ->where('a.user_id=:user_id',
                array(':user_id'=>$uid))->queryRow();
        if ($rows){
            $this->employee_id = $rows["employee_id"];
            $this->city = $rows["city"];
            return true;
        }
        return false;
    }
}
