<?php

class TreatyInfoForm extends CFormModel
{
	/* User Fields */
	public $id;
	public $treaty_id;
	public $treaty_code;
	public $treaty_name;
	public $city;
	public $city_name;
    public $contract_code;
	public $start_date;
    public $month_num;
	public $end_date;
	public $remark;
	public $email_hint=1;//默认发送邮件
	public $email_date;
	public $email_id;
	public $lcu;

	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
        return array(
            'treaty_code'=>Yii::t('treaty','treaty code'),
            'treaty_name'=>Yii::t('treaty','treaty name'),
            'month_num'=>Yii::t('treaty','month num'),
            'treaty_num'=>Yii::t('treaty','treaty num'),
            'city'=>Yii::t('treaty','city'),
            'city_name'=>Yii::t('treaty','city'),
            'apply_date'=>Yii::t('treaty','apply date'),
            'start_date'=>Yii::t('treaty','start date'),
            'end_date'=>Yii::t('treaty','end date'),
            'email_hint'=>Yii::t('treaty','email hint'),
            'remark'=>Yii::t('treaty','remark'),
            'contract_code'=>Yii::t('treaty','history code'),
            'lcu'=>Yii::t('treaty','treaty lcu'),
        );
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
            array('id,treaty_id,contract_code,treaty_name,month_num,city,city_name,start_date,end_date,email_hint,remark,email_date','safe'),
			array('email_hint,start_date,month_num,end_date','required'),
            array('id','validateID'),
            array('email_hint','validateEmail','on'=>array("new","edit")),
		);
	}

    public function validateID($attribute, $params) {
	    if($this->getScenario()!="new"){
            $id = $this->$attribute;
            $city_allow = Yii::app()->user->city_allow();
            $uid = Yii::app()->user->id;
            if(Yii::app()->user->validFunction('ZR21')){ //允許查看管轄內的所有項目
                $sqlCity = " and b.city in ({$city_allow}) ";
            }else{
                $sqlCity = " and b.lcu='{$uid}' ";
            }
            $row = Yii::app()->db->createCommand()->select("a.*,b.city,b.treaty_name,b.lcu as treaty_lcu")
                ->from("hr_treaty_info a")
                ->leftJoin("hr_treaty b","a.treaty_id=b.id")
                ->where("a.id=:id {$sqlCity}",array(":id"=>$id))->queryRow();
            if($row){
                $this->contract_code=$row["contract_code"];
                $this->treaty_id=$row["treaty_id"];
                $this->treaty_name = $row['treaty_name'];
                $this->city=$row["city"];
                $this->email_id=$row["email_id"];
                $this->lcu = $row['treaty_lcu'];
            }else{
                $this->addError($attribute, "数据异常，请刷新重试");
                return false;
            }
        }
    }

    public function validateEmail($attribute, $params) {
	    if(!empty($this->email_hint)){
	        if(empty($this->email_date)){
                $this->addError($attribute, "邮件通知时间不能为空");
                return false;
            }
        }else{
	        $this->email_date=null;
        }
    }

	public function retrieveNewData($treaty_id)
	{
        $suffix = Yii::app()->params['envSuffix'];
        $city_allow = Yii::app()->user->city_allow();
        $uid = Yii::app()->user->id;
        if(Yii::app()->user->validFunction('ZR21')){ //允許查看管轄內的所有項目
            $sqlCity = " and city in ({$city_allow}) ";
        }else{
            $sqlCity = " and lcu='{$uid}' ";
        }
        $row = Yii::app()->db->createCommand()->select("*")
            ->from("hr_treaty")
            ->where("id=:id {$sqlCity}",array(":id"=>$treaty_id))->queryRow();
		if ($row!==false) {
		    //id,treaty_code,treaty_name,month_num,treaty_num,city,apply_date,start_date,end_date,state_type
			$this->lcu = $row['lcu'];
			$this->treaty_id = $row['id'];
			$this->treaty_code = $row['treaty_code'];
			$this->treaty_name = $row['treaty_name'];
			$this->city = $row['city'];
			$this->city_name = CGeneral::getCityName($row['city']);
			$infoRow = Yii::app()->db->createCommand()->select("end_date")
                ->from("hr_treaty_info")
                ->where("treaty_id=:treaty_id",array(":treaty_id"=>$this->treaty_id))
                ->order("end_date desc")->queryRow();
			if($infoRow){
			    $this->start_date = date("Y/m/d",strtotime($infoRow["end_date"]." + 1 day"));
            }

            return true;
		}else{
		    return false;
        }
	}

	public function retrieveData($index,$bool=false)
	{
        $suffix = Yii::app()->params['envSuffix'];
        $city_allow = Yii::app()->user->city_allow();
        $uid = Yii::app()->user->id;
        if($bool){
            $sqlCity="";
        }elseif(Yii::app()->user->validFunction('ZR21')){ //允許查看管轄內的所有項目
            $sqlCity = " and b.city in ({$city_allow}) ";
        }else{
            $sqlCity = " and b.lcu='{$uid}' ";
        }
        $row = Yii::app()->db->createCommand()->select("a.*,b.treaty_code,b.treaty_name,b.lcu as treaty_lcu,b.city")
            ->from("hr_treaty_info a")
            ->leftJoin("hr_treaty b","a.treaty_id=b.id")
            ->where("a.id=:id {$sqlCity}",array(":id"=>$index))->queryRow();
		if ($row!==false) {
		    //id,treaty_code,treaty_name,month_num,treaty_num,city,apply_date,start_date,end_date,state_type
			$this->id = $row['id'];
			$this->lcu = $row['treaty_lcu'];
			$this->treaty_id = $row['treaty_id'];
			$this->treaty_code = $row['treaty_code'];
			$this->treaty_name = $row['treaty_name'];
			$this->contract_code = $row['contract_code'];
			$this->city = $row['city'];
			$this->city_name = CGeneral::getCityName($row['city']);
            $this->month_num = $row['month_num'];
			$this->start_date = empty($row["start_date"])?"":CGeneral::toDate($row["start_date"]);
			$this->end_date = empty($row["end_date"])?"":CGeneral::toDate($row["end_date"]);
            $this->email_hint = $row['email_hint'];
            $this->email_date = empty($row["email_date"])?"":CGeneral::toDate($row["email_date"]);
            $this->remark = $row['remark'];
            $this->email_id = $row['email_id'];
            return true;
		}else{
		    return false;
        }
	}

	
	public function saveData()
	{
		$connection = Yii::app()->db;
		$transaction=$connection->beginTransaction();
		try {
			$this->saveDataForSql($connection);
			$this->emailSave();
			$transaction->commit();
		}
		catch(Exception $e) {
		    var_dump($e);
			$transaction->rollback();
			throw new CHttpException(404,'Cannot update.');
		}
	}

	public function resetEmailForOld(){
        echo "update start:<br/>";
        $suffix = Yii::app()->params['envSuffix'];
        $rows = Yii::app()->db->createCommand()->select("id,email_id")
            ->from("hr_treaty_info")->where("email_hint=1 and email_id>0")->queryAll();
        if($rows){
            foreach ($rows as $row){
                $this->retrieveData($row["id"],true);
                $message = $this->getEmailHtml();
                $bool = Yii::app()->db->createCommand()->update("swoper$suffix.swo_email_queue", array(
                    'message'=>$message,
                ), "id=:id and status='P'", array(':id'=>$this->email_id));
                echo "info_id:{$row["id"]}，treaty_code:{$this->treaty_code}，contract_code:{$this->contract_code}，email_id:{$this->email_id}，bool:{$bool}<br/>";
            }
        }
        echo "update end!<br/>";
    }

	protected function emailSave(){
        $from_addr = Yii::app()->params['adminEmail'];
        $suffix = Yii::app()->params['envSuffix'];
	    if(empty($this->email_hint)){//不需要邮件提醒
            if(!empty($this->email_id)){//旧数据有邮件提醒
                Yii::app()->db->createCommand()->update("swoper$suffix.swo_email_queue", array(
                    'status'=>'C',
                ), "id=:id and status='P'", array(':id'=>$this->email_id));
            }
        }else{//需要邮件提醒
            $uid = Yii::app()->user->id;
            $nowDate = date("Y/m/d H:i:s");
            $message = $this->getEmailHtml();
            $emailTitle = Yii::t("treaty","Treaty Email Title");
            $emailModel = new Email();
            //$emailModel->addEmailToPrefixAndCity("TH01",$this->city);
            $emailModel->addEmailToPrefixAndCity("ZR21",$this->city,array(),3);//所有权限的人收到邮件
            $emailModel->addEmailToLcu($this->lcu);//项目负责人收到邮件
            $emailModel->addEmailToCity($this->city);
            $to_addr = $emailModel->getToAddr();
            $to_addr = empty($to_addr)?json_encode(array("it@lbsgroup.com.hk")):json_encode($to_addr);
            if(!empty($this->email_id)){//旧数据有邮件提醒
                Yii::app()->db->createCommand()->update("swoper$suffix.swo_email_queue", array(
                    'request_dt'=>$this->email_date,
                    'message'=>$message,
                    'status'=>'P',
                ), "id=:id", array(':id'=>$this->email_id));//邮件没有发送需要修改邮件内容
            }else{//旧数据没有邮件提醒
                Yii::app()->db->createCommand()->insert("swoper$suffix.swo_email_queue", array(
                    'request_dt'=>$this->email_date,
                    'from_addr'=>$from_addr,
                    'to_addr'=>$to_addr,
                    'subject'=>$emailTitle,//郵件主題
                    'description'=>$emailTitle,//郵件副題
                    'message'=>$message,//郵件內容（html）
                    'status'=>"P",
                    'lcu'=>$uid,
                    'lcd'=>date('Y-m-d H:i:s'),
                ));
                $this->email_id = Yii::app()->db->getLastInsertID();
                Yii::app()->db->createCommand()->update('hr_treaty_info', array(
                    'email_id'=>$this->email_id,
                ), 'id=:id', array(':id'=>$this->id));
            }
        }
    }

    protected function getEmailHtml(){
	    $html = "<p>".Yii::t("treaty","treaty name").":".$this->treaty_name."</p>";
	    $html.= "<p>".Yii::t("treaty","history code").":".$this->contract_code."</p>";
	    $html.= "<p>".Yii::t("treaty","start date").":".$this->start_date."</p>";
	    $html.= "<p>".Yii::t("treaty","month num").":".$this->month_num."</p>";
	    $html.= "<p>".Yii::t("treaty","end date").":".$this->end_date."</p>";
	    $html.= "<p>".Yii::t("treaty","remark").":".$this->remark."</p>";
	    $html.= "<p>".Yii::t("treaty","city").":".CGeneral::getCityName($this->city)."</p>";
	    return $html;
    }

	protected function saveDataForSql(&$connection)
	{
		$suffix = Yii::app()->params['envSuffix'];
		$sql = '';
		switch ($this->scenario) {
			case 'delete':
				$sql = "delete from hr_treaty_info where id = :id";
				break;
			case 'new':
				$sql = "insert into hr_treaty_info(
						treaty_id, start_date, month_num, end_date, remark, email_hint, email_date, email_id, lcu) values (
						:treaty_id, :start_date, :month_num, :end_date, :remark, :email_hint, :email_date, :email_id, :lcu)";
				break;
			case 'edit':
				$sql = "update hr_treaty_info set 
					start_date = :start_date, 
					month_num = :month_num, 
					end_date = :end_date, 
					remark = :remark, 
					email_hint = :email_hint, 
					email_date = :email_date, 
					email_id = :email_id, 
					luu = :luu
					where id = :id";
				break;
		}

		$uid = Yii::app()->user->id;
        $city = Yii::app()->user->city();

		$command=$connection->createCommand($sql);
		if (strpos($sql,':id')!==false)
			$command->bindParam(':id',$this->id,PDO::PARAM_INT);
		if (strpos($sql,':treaty_id')!==false)
			$command->bindParam(':treaty_id',$this->treaty_id,PDO::PARAM_INT);
		if (strpos($sql,':start_date')!==false)
			$command->bindParam(':start_date',$this->start_date,PDO::PARAM_STR);
		if (strpos($sql,':month_num')!==false)
			$command->bindParam(':month_num',$this->month_num,PDO::PARAM_STR);
		if (strpos($sql,':end_date')!==false)
			$command->bindParam(':end_date',$this->end_date,PDO::PARAM_STR);
        if (strpos($sql,':email_hint')!==false)
            $command->bindParam(':email_hint',$this->email_hint,PDO::PARAM_INT);
        if (strpos($sql,':email_date')!==false)
            $command->bindParam(':email_date',$this->email_date,PDO::PARAM_STR);
        if (strpos($sql,':email_id')!==false)
            $command->bindParam(':email_id',$this->email_id,PDO::PARAM_INT);
        if (strpos($sql,':remark')!==false)
            $command->bindParam(':remark',$this->remark,PDO::PARAM_STR);

		if (strpos($sql,':lcu')!==false)
			$command->bindParam(':lcu',$uid,PDO::PARAM_STR);
		if (strpos($sql,':luu')!==false)
			$command->bindParam(':luu',$uid,PDO::PARAM_STR);
		$command->execute();

        if ($this->scenario=='new'){
            $this->city = $city;
            $this->id = Yii::app()->db->getLastInsertID();
            $this->contract_code="C".(100000+$this->treaty_id+$this->id);
            Yii::app()->db->createCommand()->update('hr_treaty_info', array(
                'contract_code'=>$this->contract_code,
            ), 'id=:id', array(':id'=>$this->id));
        }

		return true;
	}
}
