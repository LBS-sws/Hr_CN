<?php

class SupportEmailForm extends CFormModel
{
	public $id;
	public $employee_id;
	public $employee_name;
	public $city="ZY";
	public $dept_name;
	public $code;
	public $phone;
	public $status_type;
	public $support_city;
	public $wage_city;
	public $start_date;
	public $end_date;

	public $supportInfo=array(
	    array("uflag"=>"Y","id"=>0,"support_city"=>"","wage_city"=>"","start_date"=>"","end_date"=>"")
    );

	public function attributeLabels()
	{
		return array(
            'employee_name'=>Yii::t('contract','Employee Name'),
            'code'=>Yii::t('contract','Employee Code'),
            'phone'=>Yii::t('contract','Employee Phone'),
            'dept_name'=>Yii::t('contract','Position'),
            'status_type'=>Yii::t('contract','Status'),
            'city'=>Yii::t('contract','City'),
            'wage_city'=>Yii::t('contract','wage city'),
            'support_city'=>Yii::t('contract','support city'),
            'start_date'=>Yii::t('contract','Start Time'),
            'end_date'=>Yii::t('contract','End Time'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id,employee_id,status_type,support_city,wage_city,start_date,end_date,supportInfo','safe'),
            array('employee_id','required'),
            array('support_city,start_date','required'),
            array('employee_id','validateName'),
            array('supportInfo','validateDetail'),
		);
	}

	public function validateDetail($attribute, $params){
        foreach ($this->supportInfo as $detail){
            if(!empty($detail["support_city"])&&in_array($detail["uflag"],array("Y","N"))){
                if(empty($detail["start_date"])){
                    $message = Yii::t('contract','Start Time'). Yii::t('contract',' not exist');
                    $this->addError($attribute,$message);
                    return false;
                }
                if(empty($detail["end_date"])){
                    $message = Yii::t('contract','End Time'). Yii::t('contract',' not exist');
                    $this->addError($attribute,$message);
                    return false;
                }
            }
        }
    }
	public function validateName($attribute, $params){
        $city_allow = Yii::app()->user->city_allow();
        $rows = Yii::app()->db->createCommand()->select("a.name,a.code,b.name as dept_name")->from("hr_employee a")
            ->leftJoin("hr_dept b","a.position = b.id")
            ->where("a.id=:id and a.city ='$this->city' AND a.staff_status = 0",array(":id"=>$this->employee_id))->queryRow();
        if($rows){
            $this->code = $rows["code"];
            $this->employee_name = $rows["name"];
            $this->dept_name = $rows["dept_name"];
            $rows = Yii::app()->db->createCommand()->select("id,support_city")->from("hr_apply_support_email")
                ->where("employee_id=:id",array(":id"=>$this->employee_id))->queryRow();
            if($rows){
                $this->id = $rows["id"];
                $this->setScenario("edit");
            }else{
                $this->setScenario("new");
            }
        }else{
            $message = Yii::t('contract','Employee Name'). Yii::t('contract',' not exist');
            $this->addError($attribute,$message);
        }
	}

	public function retrieveData($index) {
        $city_allow = Yii::app()->user->city_allow();
        $suffix = Yii::app()->params['envSuffix'];
		$row = Yii::app()->db->createCommand()
            ->select("a.id,a.name,a.code,d.name as dept_name")
            ->from("hr_employee a")
            ->leftJoin("hr_dept d","a.position = d.id")
            ->where("a.id=:id and a.city ='$this->city' AND a.staff_status = 0",array(":id"=>$index))->queryRow();
		if ($row) {
            $this->employee_id = $row['id'];
            $this->employee_name = $row['name'];
            $this->dept_name = $row['dept_name'];
            $this->code = $row['code'];
            $review = Yii::app()->db->createCommand()->select("id,support_city,wage_city,end_date,start_date")->from("hr_apply_support_email")
                ->where("employee_id=:id",array(":id"=>$this->employee_id))->queryRow();
            if($review){
                $this->id = $review["id"];
                $this->support_city = $review["support_city"];
                $this->wage_city = $review["wage_city"];
                $this->end_date = $review["end_date"];
                $this->start_date = $review["start_date"];
            }
            $detailRows = Yii::app()->db->createCommand()->select("id,wage_city,support_city,start_date,end_date")->from("hr_apply_support_info")
                ->where("ase_id=:id",array(":id"=>$this->id))->queryAll();
            if($detailRows){
                $this->supportInfo = array();
                foreach ($detailRows as $detail){
                    $temp = array();
                    $temp['uflag'] = 'N';
                    $temp["id"] = $detail["id"];
                    $temp["wage_city"] = $detail["wage_city"];
                    $temp["support_city"] = $detail["support_city"];
                    $temp["start_date"] = CGeneral::toMyDate($detail["start_date"]);
                    $temp["end_date"] = CGeneral::toMyDate($detail["end_date"]);
                    $this->supportInfo[] = $temp;
                }
            }
            return true;
		}else{
		    return false;
        }
	}

	public static function getCityList(){
	    $arr = array(""=>"");
	    return array_merge($arr,CGeneral::getCityList());
    }


	public function getReadonly(){
        if ($this->getScenario()=='view'){
            return true;//只读
        }else{
            return false;
        }
    }


    //刪除驗證
    public function deleteValidate(){
        $row = Yii::app()->db->createCommand()->select("id")->from("hr_apply_support_email")
            ->where("id=:id",array(":id"=>$this->id))->queryRow();
        if($row){
            return true;
        }
        return false;
    }

	public function saveData()
	{
		$connection = Yii::app()->db;
		//$transaction=$connection->beginTransaction();
		try {
			$this->saveGoods($connection);
            $this->saveSupportDtl($connection);
			//$transaction->commit();
		}
		catch(Exception $e) {
			//$transaction->rollback();
			throw new CHttpException(404,'Cannot update. ('.$e->getMessage().')');
		}
	}

	protected function saveGoods(&$connection) {
        $oldRow =Yii::app()->db->createCommand()->select("*")->from("hr_apply_support_email")
            ->where("id=:id",array(":id"=>$this->id))->queryRow();
        $uid = Yii::app()->user->id;
        $this->end_date=empty($this->end_date)?null:$this->end_date;
        switch ($this->scenario) {
            case 'new':
                $connection->createCommand()->insert("hr_apply_support_email", array(
                    'employee_id'=>$this->employee_id,
                    'support_city'=>$this->support_city,
                    'wage_city'=>$this->wage_city,
                    'start_date'=>$this->start_date,
                    'end_date'=>$this->end_date,
                    'lcu'=>$uid,
                ));
                $this->id = Yii::app()->db->getLastInsertID();
                break;
            case 'edit':
                $connection->createCommand()->update('hr_apply_support_email', array(
                    'support_city'=>$this->support_city,
                    'wage_city'=>$this->wage_city,
                    'start_date'=>$this->start_date,
                    'end_date'=>$this->end_date,
                    'luu'=>$uid,
                ), 'id=:id', array(':id'=>$this->id));
                break;
            case 'delete':
                $connection->createCommand()->delete('hr_apply_support_email', 'id=:id', array(':id'=>$this->id));
                break;
        }

        $this->sendEmail($oldRow);
		return true;
	}

	protected function sendEmail($oldRow){
        if(!$oldRow){ //新增不需要發郵件
            return;
        }
        if($oldRow["support_city"]!=$this->support_city){
            $subject = "通知：员工-{$this->employee_name}支点（驻点）城市变更";
            $message = "";
            $message.="<p>支点/驻点城市变更通知</p>";
            $message.="<p>员工姓名：{$this->employee_name}</p>";
            $message.="<p>";
            $message.="<dt style='float: left;width: 300px;'>原支点/驻点城市：".CGeneral::getCityName($oldRow["support_city"])."</dt>";
            $message.="<dt>变更后支点/驻点城市：".CGeneral::getCityName($this->support_city)."</dt>";
            $message.="</p>";
            $message.="<p>";
            $message.="<dt style='float: left;width: 300px;'>开始时间：".CGeneral::toMyDate($this->start_date)."</dt>";
            if(!empty($this->end_date)){
                $message.="<dt>结束时间：".CGeneral::toMyDate($this->end_date)."</dt>";
            }
            $message.="</p>";
            $message.="<p>";
            $message.="<dt style='float: left;width: 300px;'>原发工资城市：".CGeneral::getCityName($oldRow["wage_city"])."</dt>";
            $message.="<dt>变更后发工资城市：".CGeneral::getCityName($this->wage_city)."</dt>";
            $message.="</p>";
            $email = new Email($subject,$message,$subject);
            $email->addEmailToCity($oldRow["wage_city"]);
            $email->addEmailToCity($oldRow["support_city"]);
            $email->addEmailToCity($this->wage_city);
            $email->addEmailToCity($this->support_city);
            $email->addEmailToLcu(Yii::app()->user->id);
            $email->addEmailToStaffId($this->employee_id);
            $email->addSupportPreEmail();
            $email->sent();
        }
    }

    protected function saveSupportDtl(&$connection)
    {
        $uid = Yii::app()->user->id;

        foreach ($this->supportInfo as $row) {
            if(empty($row["support_city"])){
                continue;
            }
            switch ($this->scenario) {
                case 'delete':
                    $connection->createCommand()->delete('hr_apply_support_info', 'ase_id=:id', array(':id'=>$this->id));
                    break;
                case 'new':
                    if ($row['uflag']=='Y') {
                        $connection->createCommand()->insert("hr_apply_support_info", array(
                            'ase_id'=>$this->id,
                            'support_city'=>$row["support_city"],
                            'wage_city'=>$row["wage_city"],
                            'start_date'=>$row["start_date"],
                            'end_date'=>$row["end_date"],
                            'lcu'=>$uid,
                        ));
                    }
                    break;
                case 'edit':
                    switch ($row['uflag']) {
                        case 'D':
                            $connection->createCommand()->delete('hr_apply_support_info', 'id=:id', array(':id'=>$row["id"]));
                            break;
                        case 'Y':
                            if(empty($row["id"])){
                                $connection->createCommand()->insert("hr_apply_support_info", array(
                                    'ase_id'=>$this->id,
                                    'support_city'=>$row["support_city"],
                                    'wage_city'=>$row["wage_city"],
                                    'start_date'=>$row["start_date"],
                                    'end_date'=>$row["end_date"],
                                    'lcu'=>$uid,
                                ));
                            }else{
                                $connection->createCommand()->update('hr_apply_support_email', array(
                                    'support_city'=>$row["support_city"],
                                    'wage_city'=>$row["wage_city"],
                                    'start_date'=>$row["start_date"],
                                    'end_date'=>$row["end_date"],
                                    'luu'=>$uid,
                                ), 'id=:id', array(':id'=>$row["id"]));
                            }
                            break;
                    }
                    break;
            }
        }
        return true;
    }
}
