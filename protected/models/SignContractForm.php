<?php

class SignContractForm extends CFormModel
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
	public $lcd;
	public $status_type;
	public $fix_time;
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

    public $downList = array();

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
			array('id, courier_code,courier_str,remark,send_date,employee_id','safe'),
            array('id,courier_code,courier_str,send_date','required'),
            array('id','validateId'),
            array('courier_code','validateFile'),
            array('files, removeFileId, docMasterId, no_of_attm','safe'),
		);
	}


    public function validateDown(){
        $staffList = array();
        $this->downList = array();
        $city_allow = Yii::app()->user->city_allow();
        $nowList = Yii::app()->db->createCommand()->select("*")->from("hr_employee")
            ->where("id=:id and staff_status=0 and city in ($city_allow)",array(":id"=>$this->employee_id))->queryRow();
        if($nowList){
            if(empty($this->his_id)){
                $staffList = $nowList;
            }else{
                $row = Yii::app()->db->createCommand()->select("*")->from("hr_employee_operate")
                    ->where("id=:id and employee_id=:employee_id",array(":id"=>$this->his_id,":employee_id"=>$this->employee_id))->queryRow();
                if($row){
                    $staffList = $row;
                }else{
                    $message = "員工不存在2";
                    $this->addError("id",$message);
                    return false;
                }
            }
        }else{
            $message = "員工不存在1";
            $this->addError("id",$message);
            return false;
        }
        $this->downList["company"]=CompanyForm::getCompanyToId($staffList["company_id"]);
        $wordIdList = ContractForm::getWordListToConIdDesc($staffList["contract_id"]);
        $this->downList["word"]=array();
        $this->downList["staff"]=$staffList;
        foreach ($wordIdList as $wordId){
            $url = WordForm::getDocxUrlToId($wordId["name"]);
            if($url){
                array_push($this->downList["word"],$url["docx_url"]);
            }
        }
        return true;
    }

    public function validateFile($attribute, $params){
        $id = $this->employee_id;
        $date = date("Y/m/d H:i:s",strtotime($this->lcd));
        $suffix = Yii::app()->params['envSuffix'];
        $rows = Yii::app()->db->createCommand()->select("b.lcd")->from("docman$suffix.dm_master a")
            ->leftJoin("docman$suffix.dm_file b","b.mast_id = a.id")
            ->where("a.doc_type_code='SIGNC' and a.doc_id = '$id' and date_format(b.lcd,'%Y/%m/%d %H:%i:%s') > '$date'")->queryRow();
        if(!$rows){
            $message = "没有上传合同，不允许寄出";
            $this->addError($attribute,$message);
        }
    }


    public function validateId($attribute, $params){
        $city_allow = Yii::app()->user->city_allow();
        $rows = Yii::app()->db->createCommand()->select("a.lcd,a.history_id,a.status_type,a.sign_type,a.reject_remark,a.employee_id,b.company_id,b.phone,b.entry_time,b.user_card,b.end_time,b.start_time,b.department,b.fix_time,b.name,b.code,b.position,b.city")
            ->from("hr_sign_contract a")
            ->leftJoin("hr_employee b","a.employee_id = b.id")
            ->where("a.id=:id and b.city in ($city_allow) and a.status_type IN (-1,0,1,4) ",array(':id'=>$this->id))->queryRow();
        if(!$rows){
            $this->employee_id = '';
            $message = Yii::t('contract','send staff'). Yii::t('contract',' Did not find');
            $this->addError($attribute,$message);
        }else{
            $this->employee_id = $rows["employee_id"];
            $this->reject_remark = $rows["reject_remark"];
            $this->code = $rows["code"];
            $this->name = $rows["name"];
            $this->city = $rows["city"];
            $this->city_name = CGeneral::getCityName($rows["city"]);
            $this->phone = $rows["phone"];
            $this->entry_time = $rows["entry_time"];
            $this->user_card = $rows["user_card"];
            $this->his_id = $rows["history_id"];
            $this->end_time = $rows["end_time"];
            $this->start_time = $rows["start_time"];
            $this->lcd = $rows["lcd"];
            $this->fix_time = $rows["fix_time"];
            $this->sign_type = SignContractList::getSignTypeListOrId($rows['sign_type'],true);
            $this->department = DeptForm::getDeptToid($rows['department']);
            $this->position = DeptForm::getDeptToid($rows['position']);
            $this->company_id=CompanyForm::getCompanyToId($rows['company_id'])["name"];
        }
    }

    public function updateStatusType(){
        Yii::app()->db->createCommand()->update('hr_sign_contract', array(
            'status_type'=>-1,
        ), 'id=:id and status_type in (0,1)', array(':id'=>$this->id));
    }

	public function retrieveData($index) {
        $suffix = Yii::app()->params['envSuffix'];
        $city_allow = Yii::app()->user->city_allow();
        $row = Yii::app()->db->createCommand()->select("a.*,b.company_id,b.phone,b.entry_time,b.user_card,b.end_time,b.start_time,b.department,b.fix_time,b.name,b.code,b.position,b.city,docman$suffix.countdoc('SIGNC',b.id) as signcdoc")
            ->from("hr_sign_contract a")
            ->leftJoin("hr_employee b","a.employee_id = b.id")
            ->where("a.id=:id and b.city in ($city_allow) and a.status_type IN (-1,0,1,2,3,4) ",array(':id'=>$index))->queryRow();
		if ($row) {
            $this->id = $row['id'];
            $this->city = $row['city'];
            $this->lcd = $row['lcd'];
            $this->city_name = CGeneral::getCityName($row["city"]);
            $this->code = $row['code'];
            $this->reject_remark = $row['reject_remark'];
            $this->name = $row['name'];
            $this->employee_id = $row['employee_id'];
            $this->status_type = $row['status_type'];
            $this->sign_type = SignContractList::getSignTypeListOrId($row['sign_type'],true);
            $this->company_id=CompanyForm::getCompanyToId($row['company_id'])["name"];

            $this->fix_time = $row["fix_time"];
            $this->his_id = $row["history_id"];
            $this->phone = $row["phone"];
            $this->entry_time = $row["entry_time"];
            $this->user_card = $row["user_card"];
            $this->end_time = $row["end_time"];
            $this->start_time = $row["start_time"];
            $this->department = DeptForm::getDeptToid($row['department']);
            $this->position = DeptForm::getDeptToid($row['position']);

            $this->send_date = empty($row['send_date'])?date("Y/m/d"):$row['send_date'];
            $this->courier_str = $row['courier_str'];
            $this->courier_code = $row['courier_code'];
            $this->remark = $row['remark'];
            $this->no_of_attm['signc'] = $row['signcdoc'];
		}
		return true;
	}

    //刪除驗證
    public function deleteValidate(){
        $row = Yii::app()->db->createCommand()->select("a.id")->from("hr_sign_contract a")
            ->leftJoin("hr_employee b","a.employee_id = b.id")
            ->where("a.id=:id and (b.table_type != 1 or a.status_type = 3 or (a.status_type in (-1,0,1,4) and b.staff_status=-1))",array(":id"=>$this->id))->queryRow();
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
                $sql = "delete from hr_sign_contract where id = :id";
                break;
            case 'edit':
                $sql = "update hr_sign_contract set
							send_date = :send_date, 
							courier_str = :courier_str, 
							courier_code = :courier_code, 
							status_type = :status_type, 
							reject_remark = '',
							remark = :remark,
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
        if (strpos($sql,':send_date')!==false)
            $command->bindParam(':send_date',$this->send_date,PDO::PARAM_STR);
        if (strpos($sql,':courier_str')!==false)
            $command->bindParam(':courier_str',$this->courier_str,PDO::PARAM_STR);
        if (strpos($sql,':courier_code')!==false)
            $command->bindParam(':courier_code',$this->courier_code,PDO::PARAM_STR);
        if (strpos($sql,':status_type')!==false)
            $command->bindParam(':status_type',$this->status_type,PDO::PARAM_INT);
        if (strpos($sql,':remark')!==false)
            $command->bindParam(':remark',$this->remark,PDO::PARAM_STR);

        if (strpos($sql,':luu')!==false)
            $command->bindParam(':luu',$uid,PDO::PARAM_STR);
        if (strpos($sql,':lcu')!==false)
            $command->bindParam(':lcu',$uid,PDO::PARAM_STR);
        $command->execute();

        if ($this->scenario=='new'){
            $this->id = Yii::app()->db->getLastInsertID();
            $this->scenario = "edit";
        }

        if ($this->status_type==2){ //發送郵件
            $this->sendEmail();
        }
		return true;
	}

	protected function sendEmail(){
        $city_name = $this->city_name;
        $subject = "員工合同已寄出，請簽收 - ".$this->name."（".$city_name."）";
        $description=$subject;
        $message="<p>員工編號：".$this->code."</p>";
        $message.="<p>員工姓名：".$this->name."</p>";
        $message.="<p>員工所在城市：".$city_name."</p>";
        $message.="<p>寄出日期：".$this->send_date."</p>";
        $message.="<p>快遞公司：".$this->courier_str."</p>";
        $message.="<p>快遞單號：".$this->courier_code."</p>";
        if(!empty($this->remark)){
            $message.="<p>備註：".$this->remark."</p>";
        }
        $email = new Email($subject,$message,$description);
        $email->addEmailToPrefixNullCity("ZG08");
        $email->sent();
    }
}
