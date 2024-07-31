<?php

class RewardForm extends CFormModel
{
	public $id;
	public $employee_id;
	public $employee_code;
	public $employee_name;
	public $reward_id;
	public $reward_name;
	public $reward_money;
	public $reward_goods;
	public $remark;
	public $reject_remark;
	public $city;
	public $status = 0;

	public function attributeLabels()
	{
		return array(
            'employee_id'=>Yii::t('contract','Select employee'),
            'reward_id'=>Yii::t('contract','Select reward'),
            'employee_name'=>Yii::t('contract','Employee Name'),
            'employee_code'=>Yii::t('contract','Employee Code'),
            'reward_name'=>Yii::t('contract','Reward Name'),
            'reward_money'=>Yii::t('contract','Reward Money')."（RMB）",
            'reward_goods'=>Yii::t('contract','Reward Goods'),
            'remark'=>Yii::t('contract','Remark'),
            'reject_remark'=>Yii::t('contract','Rejected Remark'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id, employee_id,reward_id,remark,employee_name,employee_code,reward_name,reward_money,reward_goods,reject_remark','safe'),
            array('employee_id','required'),
            array('reward_id','required'),
            array('employee_id','validateEmployee'),
            array('reward_id','validateReward'),
		);
	}

	public function validateEmployee($attribute, $params){
	    if(!empty($this->employee_id)){
            $rows = Yii::app()->db->createCommand()->select("*")
                ->from("hr_employee")->where("staff_status=0 and id=:id",array(":id"=>$this->employee_id))->queryRow();
            if($rows){
                $this->employee_code = $rows["code"];
                $this->employee_name = $rows["name"];
                $this->city = $rows["city"];
            }else{
                $message = Yii::t('contract','Employee').Yii::t('contract',' not exist');
                $this->addError($attribute,$message);
            }
        }
	}

	public function validateReward($attribute, $params){
	    if(!empty($this->reward_id)){
            $rows = Yii::app()->db->createCommand()->select("*")
                ->from("hr_reward")->where("id=:id",array(":id"=>$this->reward_id))->queryRow();
            if($rows){
                $this->reward_name = $rows["name"];
                $this->reward_money = $rows["money"];
                $this->reward_goods = $rows["goods"];
            }else{
                $message = Yii::t('contract','Reward').Yii::t('contract',' not exist');
                $this->addError($attribute,$message);
            }
        }
	}

	public function retrieveData($index) {
        $city_allow = Yii::app()->user->city_allow();
		$rows = Yii::app()->db->createCommand()->select("*")
            ->from("hr_employee_reward")->where("id=:id and city in($city_allow)",array(":id"=>$index))->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
                $this->id = $row['id'];
                $this->employee_id = $row['employee_id'];
                $this->employee_code = $row['employee_code'];
                $this->employee_name = $row['employee_name'];
                $this->city = $row['city'];
                $this->reward_id = $row['reward_id'];
                $this->reward_name = $row['reward_name'];
                $this->reward_money = empty($row['reward_money'])?"":sprintf("%.2f",$row['reward_money']);
                $this->reward_goods = $row['reward_goods'];
                $this->remark = $row['remark'];
                $this->status = $row['status'];
                $this->reject_remark = $row['reject_remark'];
                break;
			}
		}
		return true;
	}

    //刪除驗證
    public function deleteValidate(){
/*        $rs0 = Yii::app()->db->createCommand()->select()->from("opr_goods_do")->where('classify_id=:classify_id',array(':classify_id'=>$this->id))->queryAll();
        $rs1 = Yii::app()->db->createCommand()->select()->from("opr_goods_fa")->where('classify_id=:classify_id',array(':classify_id'=>$this->id))->queryAll();
        $rs2 = Yii::app()->db->createCommand()->select()->from("opr_goods_im")->where('classify_id=:classify_id',array(':classify_id'=>$this->id))->queryAll();
        $rs3 = Yii::app()->db->createCommand()->select()->from("opr_warehouse")->where('classify_id=:classify_id',array(':classify_id'=>$this->id))->queryAll();
        if($rs0 || $rs1 || $rs2 || $rs3){
            return false;
        }else{
            return true;
        }*/
        return true;
    }

    //獲取正式員工
    public function getEmployeeList(){
        $arr = array(""=>"");
        $city_allow = Yii::app()->user->city_allow();
        $rows = Yii::app()->db->createCommand()->select("*")
            ->from("hr_employee")->where("staff_status=0 and city in($city_allow)")->queryAll();
        foreach ($rows as $row){
            $arr[$row["id"]] = $row["code"]." - ".$row["name"];
        }
        return $arr;
    }

    //獲取獎勵配置列表
    public function getRewardConListJSON(){
        $arr = array();
        $rs = Yii::app()->db->createCommand()->select()->from("hr_reward")->queryAll();
        if($rs){
            foreach ($rs as $row){
                $arr[$row["id"]] = array(
                    "money"=>$row["money"],
                    "goods"=>$row["goods"]
                );
            }
        }
        if (empty($arr)){
            return "''";
        }else{
            return json_encode($arr);
        }
    }

    //判斷是否允許輸入
    public function yesOrNo(){
        if($this->scenario == "view"){
            return true;
        }
        if($this->scenario == "new"||$this->status == 0||$this->status == 3){
            return false;
        }else{
            return true;
        }
    }

    //完成獎金的驗證
    public function finshValidate(){
        $city_allow = Yii::app()->user->city_allow();
        $rows = Yii::app()->db->createCommand()->select("*")
            ->from("hr_employee_reward")->where("id=:id and city in($city_allow) and status = 2",array(":id"=>$this->id))->queryRow();
        if($rows){
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
        $city_allow = Yii::app()->user->city_allow();
        switch ($this->scenario) {
            case 'delete':
                $sql = "delete from hr_employee_reward where id = :id";
                break;
            case 'new':
                $sql = "insert into hr_employee_reward(
							employee_id,employee_code,employee_name, reward_id, reward_name, reward_money, reward_goods, lcu, remark, city, status
						) values (
							:employee_id,:employee_code,:employee_name, :reward_id, :reward_name, :reward_money, :reward_goods, :lcu, :remark, :city, :status
						)";
                break;
            case 'edit':
                $sql = "update hr_employee_reward set
							employee_id = :employee_id, 
							employee_code = :employee_code, 
							employee_name = :employee_name, 
							reward_id = :reward_id, 
							reward_name = :reward_name, 
							reward_money = :reward_money, 
							reward_goods = :reward_goods, 
							remark = :remark, 
							city = :city, 
							status = :status, 
							luu = :luu
						where id = :id
						";
                break;
            case 'finish':
                $sql = "update hr_employee_reward set
							status = :status, 
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
        if (strpos($sql,':employee_id')!==false)
            $command->bindParam(':employee_id',$this->employee_id,PDO::PARAM_STR);
        if (strpos($sql,':employee_code')!==false)
            $command->bindParam(':employee_code',$this->employee_code,PDO::PARAM_STR);
        if (strpos($sql,':employee_name')!==false)
            $command->bindParam(':employee_name',$this->employee_name,PDO::PARAM_STR);
        if (strpos($sql,':reward_id')!==false)
            $command->bindParam(':reward_id',$this->reward_id,PDO::PARAM_INT);
        if (strpos($sql,':reward_name')!==false)
            $command->bindParam(':reward_name',$this->reward_name,PDO::PARAM_STR);
        if (strpos($sql,':reward_money')!==false)
            $command->bindParam(':reward_money',$this->reward_money,PDO::PARAM_STR);
        if (strpos($sql,':reward_goods')!==false)
            $command->bindParam(':reward_goods',$this->reward_goods,PDO::PARAM_STR);
        if (strpos($sql,':remark')!==false)
            $command->bindParam(':remark',$this->remark,PDO::PARAM_STR);

        if (strpos($sql,':status')!==false)
            $command->bindParam(':status',$this->status,PDO::PARAM_INT);

        if (strpos($sql,':city')!==false)
            $command->bindParam(':city',$this->city,PDO::PARAM_STR);
        if (strpos($sql,':luu')!==false)
            $command->bindParam(':luu',$uid,PDO::PARAM_STR);
        if (strpos($sql,':lcu')!==false)
            $command->bindParam(':lcu',$uid,PDO::PARAM_STR);
        $command->execute();

        if ($this->scenario=='new'){
            $this->id = Yii::app()->db->getLastInsertID();
            $this->scenario = "edit";
        }
		return true;
	}
}
