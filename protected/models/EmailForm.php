<?php

class EmailForm extends CFormModel
{
	public $id;
	public $city;
	public $subject;
	public $message;
	public $request_dt;
	public $city_id;
	public $city_str='全部';
	public $staff_id;
	public $staff_str;
	public $status_type;

	public function attributeLabels()
	{
        return array(
            'city'=>Yii::t('contract','City'),
            'subject'=>Yii::t('queue','Subject'),
            'message'=>Yii::t('queue','Message'),
            'city_name'=>Yii::t('contract','City'),
            'city_str'=>Yii::t('contract','send city'),
            'staff_str'=>Yii::t('contract','send staff'),
            'status_type'=>Yii::t('contract','Status'),
            'request_dt'=>Yii::t('contract','request date'),
        );
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id, city,subject,message,city_id,city_str,staff_id,staff_str,status_type,request_dt','safe'),
            array('subject','required'),
            array('message','required'),
            array('city_id','validateCity'),
            array('staff_id','validateStaff'),
		);
	}


    public function validateStaff($attribute, $params){
        if(!empty($this->staff_id)){
            $suffix = Yii::app()->params['envSuffix'];
            $staffList = explode("~",$this->staff_id);
            foreach ($staffList as $staff){
                $rows = Yii::app()->db->createCommand()->select("username")->from("security$suffix.sec_user")
                    ->where('username=:username',array(':username'=>$staff))->queryRow();
                if(!$rows){
                    $message = Yii::t('contract','send staff'). Yii::t('contract',' Did not find');
                    $this->addError($attribute,$message);
                }
            }
        }
    }


    public function validateCity($attribute, $params){
        if(!empty($this->city_id)){
            $suffix = Yii::app()->params['envSuffix'];
            $cityList = explode("~",$this->city_id);
            foreach ($cityList as $city){
                $rows = Yii::app()->db->createCommand()->select("code")->from("security$suffix.sec_city")
                    ->where('code=:code',array(':code'=>$city))->queryRow();
                if(!$rows){
                    $message = Yii::t('contract','send city'). Yii::t('contract',' Did not find');
                    $this->addError($attribute,$message);
                }
            }
        }
    }

	public function retrieveData($index) {
        $suffix = Yii::app()->params['envSuffix'];
        $row = Yii::app()->db->createCommand()->select("*")->from("hr_email")
            ->where("id=:id",array(":id"=>$index))->queryRow();
		if ($row) {
            $this->id = $row['id'];
            $this->request_dt = empty($row['request_dt'])?"":date("Y/m/d",strtotime($row['request_dt']));
            $this->city = $row['city'];
            $this->subject = $row['subject'];
            $this->message = $row['message'];
            $this->city_id = $row['city_id'];
            $this->city_str = $row['city_str'];
            $this->staff_id = $row['staff_id'];
            $this->staff_str = $row['staff_str'];
            $this->status_type = $row['status_type'];
		}
		return true;
	}

    //刪除驗證
    public function deleteValidate(){
        $row = Yii::app()->db->createCommand()->select("*")->from("hr_email")
            ->where("id=:id",array(":id"=>$this->id))->queryRow();
        if($row){
            return true;
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
		$sql = '';
        switch ($this->scenario) {
            case 'delete':
                $sql = "delete from hr_email where id = :id";
                break;
            case 'new':
                $sql = "insert into hr_email(
							city,subject,message,city_id,city_str,staff_id,staff_str,status_type,request_dt, lcu
						) values (
							:city,:subject,:message,:s_city_id,:s_city_str,:staff_id,:staff_str,:status_type,:request_dt, :lcu
						)";
                break;
            case 'edit':
                $sql = "update hr_email set
							subject = :subject, 
							message = :message, 
							city_id = :s_city_id, 
							city_str = :s_city_str, 
							staff_id = :staff_id, 
							staff_str = :staff_str, 
							status_type = :status_type, 
							request_dt = :request_dt, 
							luu = :luu
						where id = :id
						";
                break;
        }
		if (empty($sql)) return false;

        $city = Yii::app()->user->city();
        $uid = Yii::app()->user->id;

        $command=$connection->createCommand($sql);
        if (strpos($sql,':id')!==false)
            $command->bindParam(':id',$this->id,PDO::PARAM_INT);
        //id, city,subject,message,city_id,city_str,staff_id,staff_str,status_type
        if (strpos($sql,':subject')!==false)
            $command->bindParam(':subject',$this->subject,PDO::PARAM_STR);
        if (strpos($sql,':message')!==false)
            $command->bindParam(':message',$this->message,PDO::PARAM_STR);
        if (strpos($sql,':s_city_id')!==false)
            $command->bindParam(':s_city_id',$this->city_id,PDO::PARAM_STR);
        if (strpos($sql,':s_city_str')!==false)
            $command->bindParam(':s_city_str',$this->city_str,PDO::PARAM_STR);
        if (strpos($sql,':staff_id')!==false)
            $command->bindParam(':staff_id',$this->staff_id,PDO::PARAM_STR);
        if (strpos($sql,':staff_str')!==false)
            $command->bindParam(':staff_str',$this->staff_str,PDO::PARAM_STR);
        if (strpos($sql,':request_dt')!==false){
            if(empty($this->request_dt)){
                $this->request_dt = null;
            }
            $command->bindParam(':request_dt',$this->request_dt,PDO::PARAM_STR);
        }
        if (strpos($sql,':status_type')!==false)
            $command->bindParam(':status_type',$this->status_type,PDO::PARAM_INT);

        if (strpos($sql,':city')!==false)
            $command->bindParam(':city',$city,PDO::PARAM_STR);
        if (strpos($sql,':luu')!==false)
            $command->bindParam(':luu',$uid,PDO::PARAM_STR);
        if (strpos($sql,':lcu')!==false)
            $command->bindParam(':lcu',$uid,PDO::PARAM_STR);
        $command->execute();

        if ($this->scenario=='new'){
            $this->id = Yii::app()->db->getLastInsertID();
            $this->scenario = "edit";
        }

        if ($this->status_type==3){ //發送郵件
            $this->sendEmail();
        }
		return true;
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
