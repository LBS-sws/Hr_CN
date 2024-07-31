<?php

class AuditRewardForm extends CFormModel
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
	public $city;
	public $reject_remark;
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
			array('id, employee_id,reward_id,remark,employee_name,employee_code,reward_name,reward_money,reward_goods,status','safe'),
            array('reject_remark','required','on'=>"reject"),
		);
	}


	public function retrieveData($index) {
		$rows = Yii::app()->db->createCommand()->select("*")
            ->from("hr_employee_reward")->where("id=:id AND status !=0 AND status !=4 ",array(":id"=>$index))->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
                $this->id = $row['id'];
                $this->employee_id = $row['employee_id'];
                $this->employee_code = $row['employee_code'];
                $this->employee_name = $row['employee_name'];
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
                $sql = "update hr_employee_reward set
							status = 2, 
							luu = :luu
						where id = :id
						";
                break;
            case 'reject':
                $sql = "update hr_employee_reward set
							status = 3, 
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

        if (strpos($sql,':status')!==false)
            $command->bindParam(':status',$this->status,PDO::PARAM_INT);
        if (strpos($sql,':luu')!==false)
            $command->bindParam(':luu',$uid,PDO::PARAM_STR);
        $command->execute();
		return true;
	}
}
