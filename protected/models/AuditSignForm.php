<?php

class AuditSignForm extends CFormModel
{
	public $id;
	public $employee_id;
	public $name;
	public $code;
	public $position;
	public $entry_time;
	public $city;
	public $city_name;
	public $user_card;
	public $phone;
	public $department;
	public $start_time;
	public $end_time;
	public $contract_id;
	public $company_id;
	public $sign_type;
	public $his_id;

	public $send_date;
	public $courier_code;
	public $courier_str;
	public $remark;
	public $status_type;
	public $fix_time;
	public $lcd;
	public $reject_remark;

    public $no_of_attm = array(
        'signc'=>0
    );
    public $docType = 'SIGNC';
    public $docMasterId = array(
        'signc'=>0
    );
    public $files;
    public $removeFileId = array(
        'signc'=>0
    );

	public function attributeLabels()
	{
        return array(
            'name'=>Yii::t('contract','Employee Name'),
            'code'=>Yii::t('contract','Employee Code'),
            'city_name'=>Yii::t('contract','City'),
            'position'=>Yii::t('contract','Position'),
            'contract_id'=>Yii::t('contract','Contract Name'),
            'company_id'=>Yii::t('contract','Company Name'),
            'entry_time'=>Yii::t('contract','Entry Time'),
            'status_type'=>Yii::t('contract','Status'),
            'courier_code'=>Yii::t('contract','courier code'),
            'courier_str'=>Yii::t('contract','courier name'),
            'send_date'=>Yii::t('contract','pack off date'),

            'start_time'=>Yii::t('contract','Contract Start Time'),
            'end_time'=>Yii::t('contract','Contract End Time'),
            'phone'=>Yii::t('contract','Employee Phone'),
            'user_card'=>Yii::t('contract','ID Card'),
            'department'=>Yii::t('contract','Department'),
            'remark'=>Yii::t('contract','Remark'),
            'fix_time'=>Yii::t('contract','contract deadline'),
            'reject_remark'=>Yii::t('contract','Rejected Remark'),
            'sign_type'=>Yii::t('contract','contract type'),
            'lcd'=>Yii::t('contract','Apply Date'),
        );
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id, courier_code,courier_str,remark,send_date,reject_remark','safe'),
            array('id','required'),
            array('reject_remark','required','on'=>array('reject')),
            array('id','validateId'),
            array('files, removeFileId, docMasterId, no_of_attm','safe'),
		);
	}


    public function validateId($attribute, $params){
        $city_allow = Yii::app()->user->city_allow();
        $rows = Yii::app()->db->createCommand()->select("a.lcd,a.history_id,a.reject_remark,a.sign_type,a.status_type,a.employee_id,b.company_id,b.phone,b.entry_time,b.user_card,b.end_time,b.start_time,b.department,b.fix_time,b.name,b.code,b.position,b.city")
            ->from("hr_sign_contract a")
            ->leftJoin("hr_employee b","a.employee_id = b.id")
            ->where("a.id=:id and a.status_type =2 ",array(':id'=>$this->id))->queryRow();
        if(!$rows){
            $this->employee_id = '';
            $message = Yii::t('contract','send staff'). Yii::t('contract',' Did not find');
            $this->addError($attribute,$message);
        }else{
            $this->employee_id = $rows["employee_id"];
            $this->code = $rows["code"];
            $this->name = $rows["name"];
            $this->city = $rows["city"];
            $this->city_name = CGeneral::getCityName($rows["city"]);
            $this->phone = $rows["phone"];
            $this->his_id = $rows["history_id"];
            $this->entry_time = $rows["entry_time"];
            $this->user_card = $rows["user_card"];
            $this->end_time = $rows["end_time"];
            $this->start_time = $rows["start_time"];
            $this->sign_type = SignContractList::getSignTypeListOrId($rows['sign_type'],true);
            $this->lcd = $rows["lcd"];
            $this->fix_time = $rows["fix_time"];
            $this->department = DeptForm::getDeptToid($rows['department']);
            $this->position = DeptForm::getDeptToid($rows['position']);
            $this->company_id=CompanyForm::getCompanyToId($rows['company_id'])["name"];
        }
    }

	public function retrieveData($index) {
        $suffix = Yii::app()->params['envSuffix'];
        $city_allow = Yii::app()->user->city_allow();
        $row = Yii::app()->db->createCommand()->select("a.*,b.company_id,b.phone,b.entry_time,b.user_card,b.end_time,b.start_time,b.department,b.fix_time,b.name,b.code,b.position,b.city,docman$suffix.countdoc('SIGNC',b.id) as signcdoc")
            ->from("hr_sign_contract a")
            ->leftJoin("hr_employee b","a.employee_id = b.id")
            ->where("a.id=:id and a.status_type =2",array(':id'=>$index))->queryRow();
		if ($row) {
            $this->id = $row['id'];
            $this->city = $row['city'];
            $this->city_name = CGeneral::getCityName($row["city"]);
            $this->lcd = $row['lcd'];
            $this->code = $row['code'];
            $this->name = $row['name'];
            $this->sign_type = SignContractList::getSignTypeListOrId($row['sign_type'],true);
            $this->employee_id = $row['employee_id'];
            $this->status_type = $row['status_type'];
            $this->company_id=CompanyForm::getCompanyToId($row['company_id'])["name"];

            $this->fix_time = $row["fix_time"];
            $this->phone = $row["phone"];
            $this->entry_time = $row["entry_time"];
            $this->user_card = $row["user_card"];
            $this->end_time = $row["end_time"];
            $this->start_time = $row["start_time"];
            $this->department = DeptForm::getDeptToid($row['department']);
            $this->position = DeptForm::getDeptToid($row['position']);

            $this->send_date = $row['send_date'];
            $this->courier_str = $row['courier_str'];
            $this->courier_code = $row['courier_code'];
            $this->remark = $row['remark'];
            $this->reject_remark = $row['reject_remark'];
            $this->no_of_attm['signc'] = $row['signcdoc'];
		}
		return true;
	}

    //刪除驗證
    public function deleteValidate(){
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
            case 'audit':
                $sql = "update hr_sign_contract set
							status_type = 3, 
							luu = :luu
						where id = :id
						";
                break;
            case 'reject':
                $sql = "update hr_sign_contract set
							status_type = 4, 
							reject_remark = :reject_remark, 
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

        if (strpos($sql,':reject_remark')!==false)
            $command->bindParam(':reject_remark',$this->reject_remark,PDO::PARAM_STR);

        if (strpos($sql,':luu')!==false)
            $command->bindParam(':luu',$uid,PDO::PARAM_STR);
        $command->execute();

        $this->sendEmail();
		return true;
	}

	protected function sendEmail(){
        $city_name = $this->scenario=="audit"?"員工合同已簽收":"員工合同被拒絕";
        $subject = "員工合同已簽收 - ".$this->name."（".$city_name."）";
        $description=$subject;
        $message="<p>員工編號：".$this->code."</p>";
        $message.="<p>員工姓名：".$this->name."</p>";
        $message.="<p>員工所在城市：".$this->city_name."</p>";
        $message.="<p>寄出日期：".$this->send_date."</p>";
        $message.="<p>快遞公司：".$this->courier_str."</p>";
        $message.="<p>快遞單號：".$this->courier_code."</p>";
        if($this->scenario=="reject"){
            $message.="<p>拒绝原因：".$this->reject_remark."</p>";
        }
        $email = new Email($subject,$message,$description);
        $email->addEmailToPrefixNullCity("ZG08");
        $email->sent();
    }
}
