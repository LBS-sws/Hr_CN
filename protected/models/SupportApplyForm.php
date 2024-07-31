<?php

class SupportApplyForm extends CFormModel
{
    //强制刷新本文件
	public $id;
	public $support_code;
	public $apply_date;
	public $apply_num;
	public $apply_type;
	public $service_type;
	public $apply_end_date;
	public $apply_length=1;
	public $apply_remark;
	public $length_type=1;
	public $apply_city;
	public $apply_lcu;
	public $update_type;
	public $update_remark;
	public $employee_id;
	public $audit_remark;
	public $tem_s_ist;
	public $tem_str;
	public $tem_sum;
	public $review_sum;
	public $status_type=1;
	public $change_num;
	public $early_remark;
	public $early_date;
	public $city_name;
	public $employee_name;
	public $reject_remark;
	public $privilege;
	public $privilege_user;

	public function attributeLabels()
	{
        return array(
            'support_code'=>Yii::t('contract','support code'),
            'apply_city'=>Yii::t('contract','apply city'),
            'apply_date'=>Yii::t('contract','Start Time'),
            'length_type'=>Yii::t('contract','support length'),
            'apply_end_date'=>Yii::t('contract','End Time'),
            'employee_id'=>Yii::t('contract','support employee'),
            'apply_remark'=>Yii::t('contract','apply remark'),
            'review_sum'=>Yii::t('contract','review sum'),
            'status_type'=>Yii::t('contract','Status'),
            'update_remark'=>Yii::t('contract','update remark'),
            'audit_remark'=>Yii::t('contract','audit remark'),
            'reject_remark'=>Yii::t('contract','Rejected Remark'),
            'apply_type'=>Yii::t('queue','Type'),
            'privilege'=>Yii::t('contract','privilege'),
            'privilege_user'=>Yii::t('contract','privilege user'),
            'service_type'=>Yii::t('contract','service type'),
            'early_remark'=>Yii::t('contract','early remark'),
            'early_date'=>Yii::t('contract','early date'),
        );
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id, service_type, privilege, privilege_user, apply_type, early_date, early_remark, reject_remark, status_type, support_code, apply_date,apply_end_date,apply_remark,apply_city,apply_length,length_type,audit_remark,tem_list,employee_id,change_num','safe'),
            array('service_type','required','on'=>array('edit','new')),
            array('apply_date','required','on'=>array('edit','new')),
            array('apply_remark','required','on'=>array('edit','new')),
            array('apply_end_date','required','on'=>array('edit','new')),
            array('apply_date','validateApplyDate','on'=>array('edit','new')),
            array('privilege','validatePrivilege','on'=>array('edit','new')),
            array('id','validateID','on'=>array('edit','new')),
            array('id','validateIDEarly','on'=>array('renewal','early','review')),
            array('tem_s_ist','validateList','on'=>array('review','early')),
            array('early_date','validateEarly','on'=>array('renewal','early')),
		);
	}

    public function validateID($attribute, $params){
        $city = Yii::app()->user->city;
        if($this->status_type == 2){
            $city = Yii::app()->user->city;
            $date = date("Y/m/d");
            $row = Yii::app()->db->createCommand()->select("*")->from("hr_apply_support")
                ->where("apply_city='$city' and status_type=5 and date_format(apply_end_date,'%Y/%m/%d') <'$date'")->queryRow();
            if($row){
                $message = "存在支援单未评分（支援编号:".$row['support_code']."）";
                $this->addError($attribute,$message);
                return false;
            }
        }
        if($this->status_type == 2&&!empty($this->id)){
            $row = Yii::app()->db->createCommand()->select("*")->from("hr_apply_support")
                ->where("id=:id and apply_city='$city' and status_type=1",array(":id"=>$this->id))->queryRow();
            if(!$row){
                $message = "支援单不存在，无法保存";
                $this->addError($attribute,$message);
            }
        }
    }

    public function validateIDEarly($attribute, $params){
        $city = Yii::app()->user->city;
        $row = Yii::app()->db->createCommand()->select("apply_end_date,apply_date")->from("hr_apply_support")
            ->where("id=:id and apply_city='$city' and status_type in (5,8,11,14)",array(":id"=>$this->id))->queryRow();
        if(!$row){
            $message = "支援单不存在，无法保存";
            $this->addError($attribute,$message);
        }else{
            $this->apply_end_date = $row["apply_end_date"];
            $this->apply_date = $row["apply_date"];
        }
    }

    public function validateEarly($attribute, $params){
	    if(empty($this->early_remark)||empty($this->early_date)){
            $message = "时间和备注不能为空";
            $this->addError($attribute,$message);
        }else{
            $start_date = date("Y/m/d",strtotime($this->apply_date));
            $end_date = date("Y/m/d",strtotime($this->apply_end_date));
            $new_date = date("Y/m/d",strtotime($this->early_date));
	        if($this->getScenario() == "early"){ //提前結束
	            if($new_date<=$start_date||$new_date>=$end_date){
                    $message = "提前結束的時間必須在 $start_date - $end_date 之間";
                    $this->addError($attribute,$message);
                }
            }else{ //續期
                if($new_date<=$end_date){
                    $message = "续期后的時間必須大于$end_date";
                    $this->addError($attribute,$message);
                }
            }
        }
    }

    public function validateList($attribute, $params){
        if(!empty($this->tem_s_ist)){
            $city = Yii::app()->user->city;
            $rows = Yii::app()->db->createCommand()->select("tem_s_ist,change_num,tem_sum")->from("hr_apply_support")
                ->where("id=:id and apply_city='$city' and status_type in (5,8,11,14)",array(":id"=>$this->id))->queryRow();
            if($rows){
                $this->tem_sum = intval($rows["tem_sum"]);
                $this->review_sum = 0;
                $rows = json_decode($rows["tem_s_ist"],true);
                foreach ($rows as $key => &$row){
                    foreach ($row["list"] as &$item){
                        if(isset($this->tem_s_ist[$key]['list'][$item['id']])){
                            $item = $this->tem_s_ist[$key]['list'][$item['id']];
                        }else{
                            $message = $item['name'].Yii::t("contract"," can not be empty");
                            $this->addError($attribute,$message);
                            return false;
                        }
                        if (!is_numeric($item["value"])){
                            $message = Yii::t('contract','review score'). Yii::t('contract',' Must be Numbers');
                            $this->addError($attribute,$message);
                            return false;
                        }
                        if($item["value"]>10 || $item["value"]<0){
                            $message = Yii::t('contract','review score').'必须在0至10之间';
                            $this->addError($attribute,$message);
                            return false;
                        }
                        if(!SupportApplyForm::scoringOk($item["value"])){
                            if(!isset($item["remark"])||empty($item["remark"])){
                                $message = Yii::t('contract','Scoring remark')."（".$item['name']."）".Yii::t("contract"," can not be empty");
                                $this->addError($attribute,$message);
                                return false;
                            }
                        }else{
                            if(key_exists("remark",$item)&&$item["remark"] === ""){
                                unset($item["remark"]);
                            }
                        }
                        $this->review_sum+=intval($item["value"])*intval($row["num_ratio"]);
                    }
                }
                $this->review_sum=$this->review_sum/($this->tem_sum*10)*100;
                $this->tem_s_ist = $rows;
            }else{
                $message = Yii::t('contract','reviewAllot project').Yii::t("contract"," can not be empty");
                $this->addError($attribute,$message);
                return false;
            }
        }
    }

    //評分安全數字範圍
    public function scoringOk($num){
        if(is_numeric($num)){
            $num = intval($num);
            if(in_array($num,array(7,8))){
                return true;
            }
        }
        return false;
    }

    public function validateApplyDate($attribute, $params){
        if(!empty($this->apply_date)){
            $date = date("Y/m/01");
            $applyDate = date("Y/m/d", strtotime($this->apply_date));
            $startDate = date("Y/m/d", strtotime("$date +1 month"));
            $endDate = date("Y/m/d", strtotime("$date +2 month - 1 day"));
            if($applyDate<$startDate || $applyDate>$endDate){
                $message = Yii::t('contract','Apply Time')."必须在$startDate 至 $endDate 之间";
                $this->addError($attribute,$message);
            }else{
                if($this->privilege == 2){
                    $this->apply_end_date = $endDate;
                }else{
                    $this->apply_end_date = date("Y/m/d", strtotime("$applyDate +1 month - 1 day"));
                }

            }
        }
    }
    public function validatePrivilege($attribute, $params){
        $city = Yii::app()->user->city;
        switch ($this->privilege){
            case 1://人員置換
                if(empty($this->privilege_user)){
                    $message = Yii::t('contract','privilege user').Yii::t('contract',' can not be empty');
                    $this->addError($attribute,$message);
                }else{
                    $row = Yii::app()->db->createCommand()->select("id")->from("hr_employee")
                        ->where("id=:id and city='$city'",array(":id"=>$this->privilege_user))->queryRow();
                    if(!$row){
                        $message = "置換的員工不存在!";
                        $this->addError($attribute,$message);
                    }
                }
                break;
            case 2://優先權
                $month = floatval(date("m",strtotime($this->apply_date)));
                if($month<7){
                    $startDate = date("Y/01/00", strtotime($this->apply_date));
                }else{
                    $startDate = date("Y/07/00", strtotime($this->apply_date));
                }
                //$startDate = date("Y/m/31", strtotime($this->apply_date." - 6 month"));
                $row = Yii::app()->db->createCommand()->select("support_code,apply_date,apply_end_date")->from("hr_apply_support")
                    ->where("date_format(apply_end_date,'%Y/%m/%d')>:apply_date and apply_city='$city' and status_type!=1 and privilege=2 and id!=:id",
                        array(":apply_date"=>$startDate,":id"=>$this->id))->queryRow();
                if($row){
                    $message = "使用优先权必须相隔六个月。重复支援编号：".$row["support_code"]."（".$row["apply_date"]." ~ ".$row["apply_end_date"]."）";
                    $this->addError($attribute,$message);
                }
                break;
            default:
                $this->privilege = 0;
        }
    }

    public function getReadonly(){
        return ($this->scenario=='view'||$this->status_type!=1);
    }

	public function retrieveData($index) {
        $suffix = Yii::app()->params['envSuffix'];
        $city = Yii::app()->user->city;
        $row = Yii::app()->db->createCommand()->select("*")->from("hr_apply_support")
            ->where("id=:id and apply_city='$city'",array(":id"=>$index))->queryRow();
		if ($row) {
            $this->id = $row['id'];
            $this->support_code = $row['support_code'];
            $this->apply_date = $row['apply_date'];
            $this->apply_end_date = $row['apply_end_date'];
            $this->apply_remark = $row['apply_remark'];
            $this->status_type = $row['status_type'];

            $this->apply_city = $row['apply_city'];
            $this->employee_id = $row['employee_id'];
            $this->update_type = $row['update_type'];
            $this->update_remark = $row['update_remark'];
            $this->audit_remark = $row['audit_remark'];
            $this->length_type = $row['length_type'];
            $this->apply_length = $row['apply_length'];
            $this->tem_str = $row['tem_str'];
            $this->apply_type = $row['apply_type'];
            $this->reject_remark = $row['reject_remark'];
            $this->tem_s_ist = json_decode($row['tem_s_ist'],true);

            $this->review_sum = $row['review_sum'];
            $this->early_remark = $row['early_remark'];

            $this->service_type = $row['service_type'];

            $this->privilege = $row['privilege'];
            $this->privilege_user = $row['privilege_user'];
		}
		return true;
	}

    //刪除驗證
    public function deleteValidate(){
        $row = Yii::app()->db->createCommand()->select("*")->from("hr_apply_support")
            ->where("id=:id and status_type in (1,8)",array(":id"=>$this->id))->queryRow();
        if($row){
            return true;
        }
        return false;
    }

    //刪除驗證
    public function getPrivilegeUserList($city=''){
        $arr = array(''=>'');
        if(empty($city)){
            $city = Yii::app()->user->city;
        }
        $rows = Yii::app()->db->createCommand()->select("id,code,name")->from("hr_employee")
            ->where("city=:city and staff_status = 0",array(":city"=>$city))->queryAll();
        if($rows){
            foreach ($rows as $row){
                $arr[$row["id"]] = $row["code"]." - ".$row["name"];
            }
        }
        return $arr;
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
        switch ($this->scenario) {
            case 'delete':
                $sql = "delete from hr_apply_support where id = :id";
                break;
            case 'new':
                $sql = "insert into hr_apply_support(
							apply_date,apply_remark,privilege_user,privilege,apply_end_date,apply_city,apply_lcu,status_type,service_type, lcu
						) values (
							:apply_date,:apply_remark,:user_privilege,:privilege,:apply_end_date,:apply_city,:apply_lcu,:status_type,:service_type, :lcu
						)";
                break;
            case 'edit':
                $sql = "update hr_apply_support set
							apply_date = :apply_date, 
							apply_remark = :apply_remark, 
							privilege = :privilege, 
							privilege_user = :user_privilege, 
							apply_end_date = :apply_end_date, 
							service_type = :service_type, 
							apply_city = :apply_city, 
							apply_lcu = :apply_lcu, 
							status_type = :status_type, 
							luu = :luu
						where id = :id
						";
                break;
            case 'review':
                $sql = "update hr_apply_support set
							review_sum = :review_sum, 
							tem_s_ist = :tem_s_ist, 
							tem_str = :tem_str,
							status_type = :status_type, 
							luu = :luu
						where id = :id
						";
                break;
            case 'early':
                $sql = "update hr_apply_support set
							review_sum = :review_sum, 
							tem_s_ist = :tem_s_ist, 
							tem_str = :tem_str,
							status_type = :status_type, 
							early_date = :early_date, 
							early_remark = :early_remark, 
							luu = :luu
						where id = :id
						";
                break;
            case 'renewal':
                $sql = "update hr_apply_support set
							status_type = :status_type, 
							early_date = :early_date, 
							early_remark = :early_remark, 
							luu = :luu
						where id = :id
						";
                break;
        }
		if (empty($sql)) return false;

        $city = Yii::app()->user->city();
        $this->apply_city = $city;
        $uid = Yii::app()->user->id;

        $command=$connection->createCommand($sql);
        if (strpos($sql,':id')!==false)
            $command->bindParam(':id',$this->id,PDO::PARAM_INT);
        //id, city,subject,message,city_id,city_str,staff_id,staff_str,status_type
        if (strpos($sql,':apply_date')!==false)
            $command->bindParam(':apply_date',$this->apply_date,PDO::PARAM_STR);
        if (strpos($sql,':apply_end_date')!==false)
            $command->bindParam(':apply_end_date',$this->apply_end_date,PDO::PARAM_STR);
        if (strpos($sql,':apply_remark')!==false)
            $command->bindParam(':apply_remark',$this->apply_remark,PDO::PARAM_STR);
        if (strpos($sql,':apply_lcu')!==false)
            $command->bindParam(':apply_lcu',$uid,PDO::PARAM_STR);
        if (strpos($sql,':apply_city')!==false)
            $command->bindParam(':apply_city',$city,PDO::PARAM_STR);
        if (strpos($sql,':status_type')!==false)
            $command->bindParam(':status_type',$this->status_type,PDO::PARAM_INT);
        if (strpos($sql,':service_type')!==false)
            $command->bindParam(':service_type',$this->service_type,PDO::PARAM_INT);
        if (strpos($sql,':privilege')!==false)
            $command->bindParam(':privilege',$this->privilege,PDO::PARAM_INT);
        if (strpos($sql,':user_privilege')!==false){
            if(empty($this->privilege_user)){
                $command->bindValue(':user_privilege',null,PDO::PARAM_INT);
            }else{
                $command->bindParam(':user_privilege',$this->privilege_user,PDO::PARAM_INT);
            }
        }

        if (strpos($sql,':review_sum')!==false)
            $command->bindParam(':review_sum',$this->review_sum,PDO::PARAM_STR);
        if (strpos($sql,':early_date')!==false)
            $command->bindParam(':early_date',$this->early_date,PDO::PARAM_STR);
        if (strpos($sql,':early_remark')!==false)
            $command->bindParam(':early_remark',$this->early_remark,PDO::PARAM_STR);
        if (strpos($sql,':tem_s_ist')!==false){
            $tem_s_ist = json_encode($this->tem_s_ist);
            $command->bindParam(':tem_s_ist',$tem_s_ist,PDO::PARAM_STR);
        }
        if (strpos($sql,':tem_str')!==false)
            $command->bindParam(':tem_str',$this->tem_str,PDO::PARAM_STR);
        if (strpos($sql,':luu')!==false)
            $command->bindParam(':luu',$uid,PDO::PARAM_STR);
        if (strpos($sql,':lcu')!==false)
            $command->bindParam(':lcu',$uid,PDO::PARAM_STR);
        $command->execute();

        if ($this->scenario=='new'){
            $this->id = Yii::app()->db->getLastInsertID();
            $this->scenario = "edit";
            $this->lenStr();
            Yii::app()->db->createCommand()->update('hr_apply_support', array(
                'support_code'=>$this->support_code
            ), 'id=:id', array(':id'=>$this->id));
        }

        $this->setSupportHistory();//記錄操作并发送邮件
		return true;
	}

    private function setSupportHistory(){
        if (in_array($this->status_type,array(2,6,9,10))){
            $this->employee_name = empty($this->employee_id)?"":YearDayList::getEmployeeNameToId($this->employee_id);
            $this->city_name = CGeneral::getCityName($this->apply_city);
            $email = new Email();
            $message = "支援编号:".$this->support_code."<br>";
            $message.= "服务类型:".SupportApplyList::getServiceList($this->service_type,true)."<br>";
            $message.= "申请城市:".$this->city_name."<br>";
            $message.= "申请时间:".$this->apply_date."<br>";
            $message.= "结束时间:".$this->apply_end_date."<br>";
            $message.= "支援时长:".$this->apply_length.($this->length_type==1?"个月":"天")."<br>";
            if(!empty($this->employee_id)){
                $message.= "支援员工:".$this->employee_name."<br>";
            }
            $messagePre = '';//額外郵件的郵件內容
            switch ($this->status_type){
                case 2://申請中
                    $email->setSubject("支援单（".$this->support_code."） - 申請支援");
                    $status_remark = '申请备注:'.$this->apply_remark;
                    $message.= "$status_remark<br>";
                    $messagePre = $message;
                    $message.="<a href='".Yii::app()->createAbsoluteUrl("supportAudit/edit",array("index"=>$this->id))."'>快速访问</a><br>";
                    break;
                case 6://已評分
                    $email->setSubject("支援单（".$this->support_code."） - 已評分");
                    $status_remark = '评分分数：'.sprintf("%.2f",$this->review_sum);
                    $message.= "$status_remark<br>";
                    $messagePre = $message;
                    $email->setMessage($message."<a href='".Yii::app()->createAbsoluteUrl("SupportSearch/view",array("index"=>$this->id))."'>快速访问</a><br>");
                    $email->addEmailToStaffId($this->employee_id);
                    $email->sent();//單獨發給支援的員工
                    $email->resetToAddr();
                    $message.="<a href='".Yii::app()->createAbsoluteUrl("supportAudit/edit",array("index"=>$this->id))."'>快速访问</a><br>";
                    break;
                case 9://申請提前結束
                    $email->setSubject("支援单（".$this->support_code."） - 申請提前結束");
                    $status_remark = '申请备注:'.$this->early_remark;
                    $message.= "$status_remark<br>";
                    $messagePre = $message;
                    $email->setMessage($message."<a href='".Yii::app()->createAbsoluteUrl("SupportSearch/view",array("index"=>$this->id))."'>快速访问</a><br>");
                    $email->addEmailToStaffId($this->employee_id);
                    $email->sent();//單獨發給支援的員工
                    $email->resetToAddr();
                    $message.="<a href='".Yii::app()->createAbsoluteUrl("supportAudit/edit",array("index"=>$this->id))."'>快速访问</a><br>";
                    break;
                case 10://申請續期
                    $email->setSubject("支援单（".$this->support_code."） - 申請續期");
                    $status_remark = '申请备注:'.$this->early_remark;
                    $message.= "$status_remark<br>";
                    $messagePre = $message;
                    $message.="<a href='".Yii::app()->createAbsoluteUrl("SupportSearch/view",array("index"=>$this->id))."'>快速访问</a><br>";
                    break;
                default:
                    return false;
            }
            $email->setMessage($message);
            $email->addEmailToPrefixNullCity("AY02");//有審核權限的人收到郵件
            $email->sent();
            //後續要求 - 需要額外發送郵件（開始）
            $messagePre.="<a href='".Yii::app()->createAbsoluteUrl("SupportSearch/view",array("index"=>$this->id))."'>快速访问</a><br>";
            $email->setMessage($messagePre);
            $email->resetToAddr();
            $email->addSupportPreEmail();
            $email->sent();
            //後續要求 - 需要額外發送郵件（結束）
            Yii::app()->db->createCommand()->insert('hr_apply_support_history', array(
                'support_id'=>$this->id,
                'start_date'=>$this->apply_date,
                'end_date'=>$this->apply_end_date,
                'apply_length'=>$this->apply_length,
                'length_type'=>$this->length_type,
                'status_type'=>$this->status_type,
                'status_remark'=>$status_remark,
                'lcu'=>Yii::app()->user->id,
            ));
        }
    }

    private function lenStr(){
        $code = strval($this->id);
        $this->support_code = "S";
        for($i = 0;$i < 5-strlen($code);$i++){
            $this->support_code.="0";
        }
        $this->support_code .= $code;
    }

	protected function sendEmail(){
        //request_dt
        $email = new Email($this->subject,$this->message);
        if(empty($this->city_id)){
            $email->addEmailToAllCity();
        }else{
            $cityList = explode("~",$this->city_id);
            foreach ($cityList as $city){
                $email->addEmailToCity($city);
            }
        }

        if(!empty($this->staff_id)){
            $staffList = explode("~",$this->staff_id);
            foreach ($staffList as $staff){
                $email->addEmailToLcu($staff);
            }
        }
        $email->sent('','',$this->request_dt);
    }
}
