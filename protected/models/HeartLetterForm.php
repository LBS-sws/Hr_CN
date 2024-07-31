<?php

class HeartLetterForm extends CFormModel
{
	public $id;
	public $employee_id;
	public $employee_name;
	public $state=0;

	public $letter_id=0;
	public $letter_type;
	public $letter_title;
	public $letter_body;
	public $letter_num=0;
	public $letter_reply;

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
			array('id,letter_id,employee_id,employee_name,city,state,letter_id,letter_type,letter_title,letter_body,letter_num,letter_reply,lcd,no_of_attm','safe'),
            //array('employee_id','validateUser'),
            array('letter_title','required'),
            array('letter_body','required'),
            array('id','validateDelete','on'=>array("delete")),
            //array('log_time','numerical','allowEmpty'=>true,'integerOnly'=>false,'on'=>array("new","edit","audit")),
            array('files, removeFileId, docMasterId','safe'),
		);
	}

	public function validateDelete($attribute, $params){
        $row = Yii::app()->db->createCommand()->select("id")->from("hr_letter")
            ->where('id=:id and state = 0 and lcu=:lcu',
                array(':id'=>$this->id,':lcu'=>Yii::app()->user->id)
            )->queryRow();
        if(!$row){
            $message = "心意信封不存在，请于管理员联系";
            $this->addError($attribute,$message);
        }
    }

	public function goOnLetter() {
	    if(key_exists("letter_id",$_GET)){
            $index = $_GET["letter_id"];
            $row = Yii::app()->db->createCommand()->select("a.*")->from("hr_letter a")
                ->where("a.id=:id and a.employee_id=:employee_id",
                    array(":id"=>$index,":employee_id"=>$this->employee_id)
                )->queryRow();
            if($row){
                $this->letter_id = $row["id"];
                $this->letter_type = $row["letter_type"];
                $this->letter_title = $row["letter_title"]." - 续";
                $this->letter_body = "原：".$row["letter_body"]."\n----------------------------------------------------------------\n";
            }
        }
	}

	public function retrieveData($index) {
        $lcuId = Yii::app()->user->id;
        $city_allow = Yii::app()->user->city_allow();
        $suffix = Yii::app()->params['envSuffix'];
        $rows = Yii::app()->db->createCommand()->select("a.*,b.name as employee_name,docman$suffix.countdoc('LETTER',a.id) as letterdoc")
            ->from("hr_letter a")
            ->leftJoin("hr_employee b","a.employee_id = b.id")
            ->where("a.id=:id",array(":id"=>$index))->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
                $this->id = $row['id'];
                //employee_id,city,state,letter_id,letter_type,letter_title,letter_body,letter_num,letter_reply,lcd
                $this->employee_id = $row['employee_id'];
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
                break;
			}
		}
		return true;
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
            $this->updateDocman($connection,'LETTER');
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

	protected function saveGoods(&$connection) {
		$sql = '';
        switch ($this->scenario) {
            case 'delete':
                $sql = "delete from hr_letter where id = :id and state = 0";
                break;
            case 'new':
                //employee_id,city,state,letter_id,letter_type,letter_title,letter_body,letter_num,letter_reply,lcd
                $sql = "insert into hr_letter(
							employee_id,city,state, letter_id, letter_type, letter_title, letter_body, lcu
						) values (
							:employee_id,:city,:state, :letter_id, :letter_type, :letter_title, :letter_body, :lcu
						)";
                break;
            case 'edit':
                $sql = "update hr_letter set
							state = :state, 
							letter_type = :letter_type, 
							letter_title = :letter_title, 
							letter_body = :letter_body, 
							lcd = :lcd, 
							luu = :luu
						where id = :id
						";
                break;
        }
		if (empty($sql)) return false;

        $city = Yii::app()->user->city();
        $uid = Yii::app()->user->id;//ZR06
        $employeeList = $this->getEmployeeOneToUser();
        $this->employee_id = $employeeList["id"];
        $this->city = $employeeList["city"];

        $command=$connection->createCommand($sql);
        if (strpos($sql,':id')!==false)
            $command->bindParam(':id',$this->id,PDO::PARAM_INT);
        if (strpos($sql,':employee_id')!==false)
            $command->bindParam(':employee_id',$this->employee_id,PDO::PARAM_STR);
        //employee_id,city,state,letter_id,letter_type,letter_title,letter_body,letter_num,letter_reply,lcd
        if (strpos($sql,':state')!==false)
            $command->bindParam(':state',$this->state,PDO::PARAM_STR);
        if (strpos($sql,':letter_id')!==false){
            $row = Yii::app()->db->createCommand()->select("a.id")->from("hr_letter a")
                ->where("a.id=:id and a.employee_id=:employee_id",
                    array(":id"=>$this->letter_id,":employee_id"=>$this->employee_id)
                )->queryRow();
            $this->letter_id = $row?$row["id"]:0;
            $command->bindParam(':letter_id',$this->letter_id,PDO::PARAM_STR);
        }
        if (strpos($sql,':letter_type')!==false){
            $this->letter_type = 3;
            $command->bindParam(':letter_type',$this->letter_type,PDO::PARAM_STR);
        }
        if (strpos($sql,':letter_title')!==false)
            $command->bindParam(':letter_title',$this->letter_title,PDO::PARAM_STR);
        if (strpos($sql,':letter_body')!==false)
            $command->bindParam(':letter_body',$this->letter_body,PDO::PARAM_STR);

        if (strpos($sql,':city')!==false)
            $command->bindParam(':city',$this->city,PDO::PARAM_STR);
        if (strpos($sql,':luu')!==false)
            $command->bindParam(':luu',$uid,PDO::PARAM_STR);
        if (strpos($sql,':lcu')!==false)
            $command->bindParam(':lcu',$uid,PDO::PARAM_STR);
        if (strpos($sql,':lcd')!==false){
            $this->lcd = date("Y-m-d H:i:s");
            $command->bindParam(':lcd',$this->lcd,PDO::PARAM_STR);
        }
        $command->execute();

        if ($this->scenario=='new'){
            $this->id = Yii::app()->db->getLastInsertID();
        }

        //發送郵件
        $this->sendEmail();
		return true;
	}

	static public function getLetterTypeList($type="",$bool=false){
        $arr = array(
            Yii::t("contract","Suggest that class"),//建议类
            Yii::t("contract","Talk to the class"),//傾訴類
            Yii::t("contract","Other class"),//其他類
            Yii::t("fete","none")//無
        );
        if($bool){
            if(key_exists($type,$arr)){
                return $arr[$type];
            }else{
                return $type;
            }
        }else{
            unset($arr[3]);
            return $arr;
        }
    }

	protected function sendEmail(){
        if($this->state == 1){
            $email = new Email();
            $row = Yii::app()->db->createCommand()->select("code,name,city")->from("hr_employee")
                ->where('id=:id', array(':id'=>$this->employee_id))->queryRow();
            $description="新的心意信封 - ".$row["name"];
            $subject="新的心意信封 - ".$row["name"];
            $message="<p>员工编号：".$row["code"]."</p>";
            $message.="<p>员工姓名：".$row["name"]."</p>";
            $message.="<p>员工城市：".General::getCityName($row["city"])."</p>";
            $message.="<p>心意信封类型：".HeartLetterForm::getLetterTypeList($this->letter_type,true)."</p>";
            $message.="<p>心意信封标题：".$this->letter_title."</p>";
            $message.="<p>心意信封内容：".$this->letter_body."</p>";
            $email->setDescription($description);
            $email->setMessage($message);
            $email->setSubject($subject);
            $email->addEmailToPrefixAndCity("HL02",$row["city"]);
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
