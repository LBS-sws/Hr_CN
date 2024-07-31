<?php

class HeartLetterAuditForm extends CFormModel
{
	public $id;
	public $employee_id;
	public $employee_code;
	public $employee_name;
	public $state=0;

	public $letter_id=0;
	public $letter_type;
	public $letter_title;
	public $letter_body;
	public $letter_num=0;
	public $letter_reply;

	public $wait_date;

    public $lcu;
    public $luu;
	public $city;
	public $lcd;

    public $no_of_attm = array(
        'letter'=>0
    );
    public $docType = 'LETTER';
    public $docMasterId = array(
        'letter'=>0
    );
    public $files;
    public $removeFileId = array(
        'letter'=>0
    );

	public function attributeLabels()
	{
		return array(
            'letter_type'=>Yii::t('contract','type for director'),
            'letter_title'=>Yii::t('queue','Subject'),
            'letter_body'=>Yii::t('contract','letter body'),
            'employee_name'=>Yii::t('contract','Employee Name'),
            'employee_code'=>Yii::t('contract','Employee Code'),

            'letter_num'=>Yii::t('contract','review score'),
            'letter_reply'=>Yii::t('contract','reply'),
            'wait_date'=>Yii::t('contract','wait date'),

            'state'=>Yii::t('contract','Status'),
            'city'=>Yii::t('contract','City'),
            'lcd'=>Yii::t('contract','send date'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id,letter_id,employee_id,employee_code,employee_name,city,state,letter_id,letter_type,letter_title,letter_body,letter_num,letter_reply,lcd,no_of_attm,wait_date','safe'),
            //array('employee_id','validateUser'),
            array('letter_title','required'),
            array('letter_body','required'),
            array('id','validateUpdate'),
            array('letter_type','validateType','on'=>array("audit")),
            array('wait_date','validateWait','on'=>array("save")),
            //array('log_time','numerical','allowEmpty'=>true,'integerOnly'=>false,'on'=>array("new","edit","audit")),
            array('files, removeFileId, docMasterId','safe'),
		);
	}

	public function validateType($attribute, $params){
	    if(empty($this->letter_reply)){
            $message = "内容不能为空";
            $this->addError($attribute,$message);
        }
    }

	public function validateWait($attribute, $params){
	    if(empty($this->wait_date)){
            $message = Yii::t('contract','wait date')."不能为空";
            $this->addError($attribute,$message);
        }else{
	        $nowDate = date("Y-m-d");
	        $this->wait_date = date("Y-m-d",strtotime($this->wait_date));
	        if($this->wait_date<$nowDate){
                $message = Yii::t('contract','wait date')."不能小于".$nowDate;
                $this->addError($attribute,$message);
            }
        }
    }

	public function validateUpdate($attribute, $params){
        $row = Yii::app()->db->createCommand()->select("id,employee_id")->from("hr_letter")
            ->where('id=:id and state in (1,3)',array(':id'=>$this->id))->queryRow();
        if(!$row){
            $message = "心意信封不存在，请于管理员联系";
            $this->addError($attribute,$message);
        }else{
            $this->employee_id = $row["employee_id"];
        }
    }

	public function retrieveData($index) {
        $lcuId = Yii::app()->user->id;
        $city_allow = Yii::app()->user->city_allow();
        $suffix = Yii::app()->params['envSuffix'];
        $rows = Yii::app()->db->createCommand()->select("a.*,b.code as employee_code,b.name as employee_name,docman$suffix.countdoc('LETTER',a.id) as letterdoc")
            ->from("hr_letter a")
            ->leftJoin("hr_employee b","a.employee_id = b.id")
            ->where("a.id=:id and a.state in (1,3,4)",array(":id"=>$index))->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
                $this->id = $row['id'];
                //employee_id,city,state,letter_id,letter_type,letter_title,letter_body,letter_num,letter_reply,lcd
                $this->employee_id = $row['employee_id'];
                $this->employee_code = $row['employee_code'];
                $this->employee_name = $row['employee_name'];
                $this->state = $row['state'];
                $this->letter_id = $row['letter_id'];
                $this->letter_type = $row['letter_type'];
                $this->letter_title = $row['letter_title'];
                $this->letter_body = $row['letter_body'];
                $this->letter_num = $row['letter_num'];
                $this->letter_reply = $row['letter_reply'];
                $this->lcd = $row['lcd'];
                $this->city = $row['city'];
                $this->no_of_attm['letter'] = $row['letterdoc'];
                $this->wait_date = $this->state==3?$this->getWaitDate():"";
                break;
			}
		}
		return true;
	}

	static public function getLetterNumToIcon($num){
	    $num = in_array($num,array(0,1,2,3,4,5))?intval($num):0;
        $html = "";
        for($i=0;$i<5;$i++){
            if($i<$num){
                $html.="<span class='changeIcon fa fa-2x fa-star'></span>";
            }else{
                $html.="<span class='changeIcon fa fa-2x fa-star-o'></span>";
            }
        }
        $html.="<span id='num_icon' style='margin-left: 10px;vertical-align: super;'>".$num."分</span>";
        return $html;
    }

    //刪除驗證
    public function deleteValidate(){
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
        $sql = "update hr_letter set
							state = :state, 
							letter_type = :letter_type, 
							letter_num = :letter_num, 
							letter_reply = :letter_reply, 
							luu = :luu
						where id = :id
						";
		if (empty($sql)) return false;

        $city = Yii::app()->user->city();
        $uid = Yii::app()->user->id;//ZR06

        $command=$connection->createCommand($sql);
        if (strpos($sql,':id')!==false)
            $command->bindParam(':id',$this->id,PDO::PARAM_INT);
        //employee_id,city,state,letter_id,letter_type,letter_title,letter_body,letter_num,letter_reply,lcd
        if (strpos($sql,':state')!==false)
            $command->bindParam(':state',$this->state,PDO::PARAM_STR);
        if (strpos($sql,':letter_type')!==false)
            $command->bindParam(':letter_type',$this->letter_type,PDO::PARAM_STR);
        if (strpos($sql,':letter_num')!==false)
            $command->bindParam(':letter_num',$this->letter_num,PDO::PARAM_STR);
        if (strpos($sql,':letter_reply')!==false)
            $command->bindParam(':letter_reply',$this->letter_reply,PDO::PARAM_STR);

        if (strpos($sql,':luu')!==false)
            $command->bindParam(':luu',$uid,PDO::PARAM_STR);
        $command->execute();

        if ($this->scenario=='new'){
            $this->id = Yii::app()->db->getLastInsertID();
        }


        //發送郵件
        $this->sendEmail();
		return true;
	}

    protected function getWaitDate(){
        $suffix = Yii::app()->params['envSuffix'];
        $row = Yii::app()->db->createCommand()->select("request_dt")->from("swoper$suffix.swo_email_queue")
            ->where("lcu=:lcu",array(":lcu"=>"心意信封_待处理_".$this->id))
            ->queryRow();
        if($row){
            return date("Y-m-d",strtotime($row["request_dt"]));
        }else{
            return "";
        }

    }

	//預計處理
    protected function waitDate($message){
        $from_addr = Yii::app()->params['adminEmail'];
        $suffix = Yii::app()->params['envSuffix'];
        if($this->state == 3){
            $uid = Yii::app()->user->id;
            $email = Yii::app()->db->createCommand()->select("email")->from("security$suffix.sec_user")
                ->where("username=:username",array(":username"=>$uid))
                ->queryRow();
            $url = Yii::app()->createAbsoluteUrl('heartLetterAudit/edit',array("index"=>$this->id));
            
            $message.="<p><a href='$url' target='_blank'>点击处理心意信封</a></p>";
            if($email&&!empty($email["email"])){
                Yii::app()->db->createCommand()->insert("swoper$suffix.swo_email_queue", array(
                    'request_dt'=>$this->wait_date,
                    'from_addr'=>$from_addr,
                    'to_addr'=>$email["email"],
                    'subject'=>"心意信封 - 待处理",//郵件主題
                    'description'=>"心意信封 - 待处理",//郵件副題
                    'message'=>$message,//郵件內容（html）
                    'status'=>"P",
                    'lcu'=>"心意信封_待处理_".$this->id,
                    'lcd'=>date('Y-m-d H:i:s'),
                ));
            }
        }else{
            Yii::app()->db->createCommand()->delete("swoper$suffix.swo_email_queue","lcu=:lcu",array(
                ":lcu"=>"心意信封_待处理_".$this->id
            ));
        }
    }

	protected function sendEmail(){
        if(in_array($this->state,array(3,4))){
            $email = new Email();
            $row = Yii::app()->db->createCommand()->select("code,name,city")->from("hr_employee")
                ->where('id=:id', array(':id'=>$this->employee_id))->queryRow();
            $description="心意信封 - ";
            $message="<p>员工编号：".$row["code"]."</p>";
            $message.="<p>员工姓名：".$row["name"]."</p>";
            $message.="<p>员工城市：".General::getCityName($row["city"])."</p>";
            $message.="<p>心意信封类型：".HeartLetterForm::getLetterTypeList($this->letter_type,true)."</p>";
            $message.="<p>心意信封标题：".$this->letter_title."</p>";
            $message.="<p>心意信封内容：".$this->letter_body."</p>";
            $messageWait = $message;
            if($this->getScenario()=="audit"){
                $message.="<p>心意信封回复：".$this->letter_reply."</p>";
                $message.="<p>心意信封分数：".$this->letter_num."分(满分5分)</p>";
            }
            if($this->state == 3){
                $description.="待处理";
                $message.="<p>您的建议/反馈总监已收到，请等待总监下一步回复</p>";
            }else{
                $description="你的意见/反馈总监已收到，谢谢";
            }
            $this->waitDate($messageWait);//待处理的邮件通知
            $subject=$description;
            $email->setDescription($description);
            $email->setMessage($message);
            $email->setSubject($subject);
            $email->addEmailToStaffId($this->employee_id);
            $email->sent();
        }
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
        if($this->scenario == "view"||!empty($this->state)){
            return true;
        }
        return false;
    }
}
