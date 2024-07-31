<?php

class PrizeForm extends CFormModel
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
	public $lcd;
	public $reject_remark;
	public $audit = false;
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
			array('id,employee_id,lcd,prize_date,city,prize_num,prize_pro,customer_name,customer_dis,contact,phone,posi,photo1,photo2,remark,status,
			work_type,type_num,prize_type,lud','safe'),
            array('prize_date','required'),
            array('prize_num','required'),
            array('employee_id','required'),
            array('prize_type','required'),
            array('type_num','required'),
            array('prize_type', 'in', 'range' => array(0, 1)),
            array('type_num', 'numerical', 'min'=>1, 'integerOnly'=>true),
            array('prize_num', 'numerical', 'min'=>1, 'integerOnly'=>true),
            array('phone','required'),
            array('photo1','required'),
            array('photo2','required'),
            array('contact','required'),
            array('customer_name','required'),
            array('prize_pro','required'),
		);
	}

	public function validateID(){
        $row = Yii::app()->db->createCommand()->select("id")
            ->from("hr_prize")->where("id=:id and status = 3",array(":id"=>$this->id))->queryRow();
        if(!$row){
            $message = "錦旗異常，請於管理員聯繫";
            $this->addError("id",$message);
            return false;
        }else{
            return true;
        }
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
                $this->work_type = DeptForm::getDeptToId($row['position']);
                $this->city = $row['s_city'];
                $this->prize_num = $row['prize_num'];
                $this->prize_pro = $row['prize_pro'];
                $this->customer_dis = $this->getCustomerNameToId($row['customer_name']);
                $this->customer_name = $row['customer_name'];
                $this->contact = $row['contact'];
                $this->phone = $row['phone'];
                $this->posi = $row['posi'];
                $this->photo1 = $row['photo1'];
                $this->photo2 = $row['photo2'];
                $this->type_num = $row['type_num'];
                $this->prize_type = $row['prize_type'];
                $this->status = $row['status'];
                $this->remark = $row['remark'];
                $this->reject_remark = $row['reject_remark'];
                $this->lcd = date("Y-m-d",strtotime($row['lcd']));
                $this->lud = date("Y-m-d",strtotime($row['lud']));
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
                $sql = "delete from hr_prize where id = :id";
                break;
            case 'new':
                $sql = "insert into hr_prize(
							employee_id,prize_date,city,prize_num,prize_pro,customer_name,contact,phone,posi,photo1,photo2,remark, status, prize_type, type_num, lcu
						) values (
							:employee_id,:prize_date,:city,:prize_num,:prize_pro,:customer_name,:contact,:phone,:posi,:photo1,:photo2,:remark, :status, :prize_type, :type_num, :lcu
						)";
                break;
            case 'edit':
                $sql = "update hr_prize set
							employee_id = :employee_id, 
							prize_date = :prize_date, 
							prize_num = :prize_num, 
							prize_pro = :prize_pro, 
							customer_name = :customer_name, 
							contact = :contact, 
							phone = :phone, 
							posi = :posi, 
							photo1 = :photo1, 
							photo2 = :photo2, 
							type_num = :type_num, 
							prize_type = :prize_type, 
							remark = :remark, 
							status = :status, 
							lcd = :lcd, 
							luu = :luu
						where id = :id
						";
                break;
            case 'back':
                $sql = "update hr_prize set
							remark = '', 
							status = 0
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
        //employee_id,prize_date,city,prize_num,prize_pro,customer_name,contact,phone,posi,photo1,photo2,status;
        if (strpos($sql,':employee_id')!==false)
            $command->bindParam(':employee_id',$this->employee_id,PDO::PARAM_STR);
        if (strpos($sql,':prize_date')!==false)
            $command->bindParam(':prize_date',$this->prize_date,PDO::PARAM_STR);
        if (strpos($sql,':prize_num')!==false)
            $command->bindParam(':prize_num',$this->prize_num,PDO::PARAM_STR);
        if (strpos($sql,':prize_pro')!==false)
            $command->bindParam(':prize_pro',$this->prize_pro,PDO::PARAM_STR);
        if (strpos($sql,':customer_name')!==false)
            $command->bindParam(':customer_name',$this->customer_name,PDO::PARAM_STR);
        if (strpos($sql,':contact')!==false)
            $command->bindParam(':contact',$this->contact,PDO::PARAM_STR);
        if (strpos($sql,':phone')!==false)
            $command->bindParam(':phone',$this->phone,PDO::PARAM_STR);
        if (strpos($sql,':posi')!==false)
            $command->bindParam(':posi',$this->posi,PDO::PARAM_STR);
        if (strpos($sql,':photo1')!==false)
            $command->bindParam(':photo1',$this->photo1,PDO::PARAM_STR);
        if (strpos($sql,':photo2')!==false)
            $command->bindParam(':photo2',$this->photo2,PDO::PARAM_STR);
        if (strpos($sql,':prize_type')!==false)
            $command->bindParam(':prize_type',$this->prize_type,PDO::PARAM_INT);
        if (strpos($sql,':type_num')!==false)
            $command->bindParam(':type_num',$this->type_num,PDO::PARAM_INT);
        if (strpos($sql,':remark')!==false)
            $command->bindParam(':remark',$this->remark,PDO::PARAM_STR);
        if (strpos($sql,':status')!==false){
            if($this->audit){
                $this->status = 1;
            }else{
                $this->status = 0;
            }
            $command->bindParam(':status',$this->status,PDO::PARAM_INT);
        }

        if (strpos($sql,':lcd')!==false)
            $command->bindParam(':lcd',date("Y-m-d H:i:s"),PDO::PARAM_STR);
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

        $this->sendEmail();
		return true;
	}


    protected function sendEmail(){
        if($this->audit){
            $prizeProList = PrizeList::getPrizeList();
            $prizeTypeList = array(Yii::t("fete","testimonial"),Yii::t("fete","prize"));
            $email = new Email();
            $this->retrieveData($this->id);
            $description="锦旗申请 - ".$this->employee_name;
            $subject="锦旗申请 - ".$this->employee_name;
            $message="<p>员工编号：".$this->employee_code."</p>";
            $message.="<p>员工姓名：".$this->employee_name."</p>";
            $message.="<p>员工城市：".General::getCityName($this->city)."</p>";
            $message.="<p>嘉许日期：".date("Y-m-d",strtotime($this->prize_date))."</p>";
            $message.="<p>嘉许项目：".$prizeProList[$this->prize_pro]."</p>";
            $message.="<p>客户奖励：".$prizeTypeList[$this->prize_type]."</p>";
            $message.="<p>锦旗总数：".$this->type_num."</p>";
            $message.="<p>申请时间：".$this->lcd."</p>";
            $email->setDescription($description);
            $email->setMessage($message);
            $email->setSubject($subject);
            $email->addEmailToPrefixAndCity("ZG07",$this->city);
            $email->sent();
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
        return $str;
    }

    //獲取管轄城市列表
    public function getSingleCityToList() {
        $str = Yii::app()->session['city_allow'];
        $city = Yii::app()->session['city'];
        $cityName = Yii::app()->session['city_name'];
        $items = explode(",",str_replace("'","",$str));
        $arr = array(""=>"");
        if (($items===false) || empty($items)){
            $arr[$city] = $cityName;
        }else{
            if(count($items)<=1){
                $arr[$city] = $cityName;
            }else{
                foreach ($items as $item){
                    $arr[$item] = CGeneral::getCityName($item);
                }
            }
        }
        return $arr;
    }
    //獲取員工列表
    public function getEmployeeList($city=""){
        $city_allow = Yii::app()->user->city_allow();
        $sql = "select * from hr_employee WHERE staff_status=0";
        if(!empty($city)){
            $sql.=" AND city='$city'";
        }else{
            $sql.=" AND city in ($city_allow)";
        }
        $rows = Yii::app()->db->createCommand($sql)->queryAll();
        $arr = array(
            ""=>"",
        );
        if($rows){
            foreach ($rows as $row){
                $arr[$row["id"]]=$row["code"]." - ".$row["name"];
            }
        }
        return $arr;
    }
	//判斷輸入框能否修改
	public function getInputBool(){
        if($this->scenario == "view"||$this->status == 1||$this->status == 3){
            return true;
        }
        return false;
    }

    //獲取客戶列表
    public function getCustomerNameToId($id){
        $suffix = Yii::app()->params['envSuffix'];
        $sql = "select name from swoper$suffix.swo_company WHERE id ='$id'";
        $rows = Yii::app()->db->createCommand($sql)->queryRow();
        if($rows){
            return $rows["name"];
        }
        return $id;
    }

    //根據編號獲取單個客戶信息
    public function getCustomerToCode($code){
        $suffix = Yii::app()->params['envSuffix'];
        $sql = "select * from swoper$suffix.swo_company WHERE code ='$code'";
        $rows = Yii::app()->db->createCommand($sql)->queryRow();
        if($rows){
            return $rows;
        }
        return "";
    }
}
