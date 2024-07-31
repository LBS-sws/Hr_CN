<?php

class AuditPrizeForm extends CFormModel
{
    public $id;
    public $employee_id;
    public $employee_name;
    public $employee_code;
    public $work_type;
    public $prize_date;
    public $city;
    public $prize_num;
    public $prize_pro;
    public $customer_dis;
    public $customer_name;
    public $contact;
    public $phone;
    public $posi;
    public $photo1;
    public $photo2;
    public $status;
    public $remark;
    public $prize_type;
    public $type_num;
    public $reject_remark;
    public $lcd;
    public $lcu;
    public $lud;

    public function attributeLabels()
    {
        return array(
            'work_type'=>Yii::t('contract','Leader'),
            'prize_date'=>Yii::t('fete','prize date'),
            'prize_num'=>Yii::t('fete','prize num'),
            'prize_pro'=>Yii::t('fete','prize pro'),
            'customer_name'=>Yii::t('fete','customer name'),
            'contact'=>Yii::t('fete','contact'),
            'phone'=>Yii::t('fete','contact phone'),
            'posi'=>Yii::t('fete','contact position'),
            'photo1'=>Yii::t('fete','testimonials'),
            'photo2'=>Yii::t('fete','Picture with customers'),
            'employee_id'=>Yii::t('contract','Employee Name'),
            'employee_name'=>Yii::t('contract','Employee Name'),
            'employee_code'=>Yii::t('contract','Employee Code'),
            'city'=>Yii::t('contract','City'),
            'status'=>Yii::t('contract','Status'),
            'remark'=>Yii::t('contract','Remark'),
            'reject_remark'=>Yii::t('contract','Rejected Remark'),
            'type_num'=>Yii::t('fete','type number'),
            'type_num_ex'=>Yii::t('fete','A commendatory letter is equal to two flags'),
            'prize_type'=>Yii::t('fete','prize type'),
            'lcd'=>Yii::t('contract','Apply Date'),
            'lud'=>Yii::t('contract','Audit Date'),
        );
    }

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return array(
            array('id,employee_id,prize_date,city,prize_num,prize_pro,customer_name,customer_dis,contact,phone,posi,photo1,photo2,remark,status,
            work_type,type_num,prize_type,reject_remark','safe'),
            array('reject_remark','required',"on"=>"reject"),
        );
    }


    public function retrieveData($index) {
        $city_allow = Yii::app()->user->city_allow();
        $suffix = Yii::app()->params['envSuffix'];
        $rows = Yii::app()->db->createCommand()->select("a.*,b.name as employee_name,b.position,b.code AS employee_code,b.city AS s_city")
            ->from("hr_prize a")
            ->leftJoin("hr_employee b","a.employee_id = b.id")
            ->where("a.id=:id and b.city in ($city_allow)",array(":id"=>$index))->queryAll();
        if (count($rows) > 0) {
            foreach ($rows as $row) {
                $this->id = $row['id'];
                $this->employee_id = $row['employee_id'];
                $this->employee_name = $row['employee_name'];
                $this->employee_code = $row['employee_code'];
                $this->prize_date = $row['prize_date'];
                $this->city = $row['s_city'];
                $this->prize_num = $row['prize_num'];
                $this->prize_pro = $row['prize_pro'];
                $this->customer_dis = PrizeForm::getCustomerNameToId($row['customer_name']);
                $this->customer_name = $row['customer_name'];
                $this->work_type = DeptForm::getDeptToId($row['position']);
                $this->contact = $row['contact'];
                $this->phone = $row['phone'];
                $this->posi = $row['posi'];
                $this->photo1 = $row['photo1'];
                $this->photo2 = $row['photo2'];
                $this->type_num = $row['type_num'];
                $this->prize_type = $row['prize_type'];
                $this->status = $row['status'];
                $this->remark = $row['remark'];
                $this->lcu = $row['lcu'];
                $this->lcd = date("Y-m-d",strtotime($row['lcd']));
                $this->lud = date("Y-m-d",strtotime($row['lud']));
                $this->reject_remark = $row['reject_remark'];
                break;
            }
        }
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

    /*  id;employee_id;employee_code;employee_name;reward_id;reward_name;reward_money;reward_goods;remark;city;*/
	protected function saveGoods(&$connection) {
		$sql = '';
        switch ($this->scenario) {
            case 'audit':
                $sql = "update hr_prize set
							status = 3, 
							luu = :luu
						where id = :id
						";
                break;
            case 'reject':
                $sql = "update hr_prize set
							status = 2, 
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
        $prizeProList = PrizeList::getPrizeList();
        $prizeTypeList = array(Yii::t("fete","testimonial"),Yii::t("fete","prize"));
        $email = new Email();
        $this->retrieveData($this->id);
        $message="<p>员工编号：".$this->employee_code."</p>";
        $message.="<p>员工姓名：".$this->employee_name."</p>";
        $message.="<p>员工城市：".General::getCityName($this->city)."</p>";
        $message.="<p>嘉许日期：".date("Y-m-d",strtotime($this->prize_date))."</p>";
        $message.="<p>嘉许项目：".$prizeProList[$this->prize_pro]."</p>";
        $message.="<p>客户奖励：".$prizeTypeList[$this->prize_type]."</p>";
        $message.="<p>锦旗总数：".$this->type_num."</p>";
        $message.="<p>申请时间：".$this->lcd."</p>";
        if($this->scenario == "audit"){
            $description="锦旗申请已通过 - ".$this->employee_name;
            $subject="锦旗申请已通过 - ".$this->employee_name;
        }else{
            $description="锦旗申请被拒绝 - ".$this->employee_name;
            $subject="锦旗申请被拒绝 - ".$this->employee_name;
            $message.="<p style='color:red;'>拒絕原因：".$this->reject_remark."</p>";
        }
        $email->setDescription($description);
        $email->setMessage($message);
        $email->setSubject($subject);
        $email->addEmailToLcu($this->lcu);
        $email->sent();
    }

    //判斷輸入框能否修改
    public function getInputBool(){
        return true;
    }
}
