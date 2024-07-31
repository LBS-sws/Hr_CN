<?php

class RewardConForm extends CFormModel
{
	public $id;
	public $name;
	public $money;
	public $goods;
	public $type = 0;

	public function attributeLabels()
	{
		return array(
            'name'=>Yii::t('contract','Reward Name'),
            'money'=>Yii::t('contract','Reward Money')."（RMB）",
            'goods'=>Yii::t('contract','Reward Goods'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id, name,money,goods','safe'),
            array('name','required'),
			array('name','validateName'),
			array('money','validateMoney'),
		);
	}

	public function validateName($attribute, $params){
        $city = Yii::app()->user->city();
        $id = -1;
        if(!empty($this->id)){
            $id = $this->id;
        }
        $rows = Yii::app()->db->createCommand()->select("id")->from("hr_reward")
            ->where('name=:name and id!=:id',
                array(':name'=>$this->name,':id'=>$id))->queryAll();
        if(count($rows)>0){
            $message = Yii::t('contract','Reward Name'). Yii::t('contract',' can not repeat');
            $this->addError($attribute,$message);
        }
	}

	public function validateMoney($attribute, $params){
	    if(empty($this->money)&&empty($this->goods)){
            $message = Yii::t('contract','The reward amount and the item cannot be empty at the same time');
            $this->addError($attribute,$message);
        }
	}

	public function retrieveData($index) {
		$rows = Yii::app()->db->createCommand()->select("*")
            ->from("hr_reward")->where("id=:id",array(":id"=>$index))->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
                $this->id = $row['id'];
                $this->name = $row['name'];
                $this->money = empty($row['money'])?"":sprintf("%.2f",$row['money']);
                $this->goods = $row['goods'];
                $this->type = $row['type'];
                break;
			}
		}
		return true;
	}

    //獲取獎勵配置列表
    public function getRewardConList(){
	    $arr = array(""=>"");
        $rs = Yii::app()->db->createCommand()->select()->from("hr_reward")->queryAll();
        if($rs){
            foreach ($rs as $row){
                $arr[$row["id"]] = $row["name"];
            }
        }
        return $arr;
    }

    //獎勵配置信息
    public function getRewardConToId($rewardCon_id){
        $rs = Yii::app()->db->createCommand()->select("*")
            ->from("hr_reward")->where('id=:id',array(':id'=>$rewardCon_id))->queryRow();
        if($rs){
            return $rs;
        }
        return array("name"=>"","money"=>"","goods"=>"","type"=>"");
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

    protected function setRewardConType(){
        if(!empty($this->money)&&!empty($this->goods)){
            $this->type = 2;
        }elseif (!empty($this->goods)){
            $this->type = 1;
        }else{
            $this->type = 0;
        }
    }

	public function saveData()
	{
		$connection = Yii::app()->db;
		$transaction=$connection->beginTransaction();
		try {
		    $this->setRewardConType();//自動完成獎金類型
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
                $sql = "delete from hr_reward where id = :id";
                break;
            case 'new':
                $sql = "insert into hr_reward(
							name,type,money, city, lcu, goods
						) values (
							:name,:type,:money, :city, :lcu, :goods
						)";
                break;
            case 'edit':
                $sql = "update hr_reward set
							name = :name, 
							goods = :goods, 
							type = :type, 
							money = :money, 
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
        if (strpos($sql,':name')!==false)
            $command->bindParam(':name',$this->name,PDO::PARAM_STR);
        if (strpos($sql,':type')!==false)
            $command->bindParam(':type',$this->type,PDO::PARAM_INT);
        if (strpos($sql,':money')!==false)
            $command->bindParam(':money',$this->money,PDO::PARAM_STR);
        if (strpos($sql,':goods')!==false)
            $command->bindParam(':goods',$this->goods,PDO::PARAM_STR);

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
		return true;
	}
}
