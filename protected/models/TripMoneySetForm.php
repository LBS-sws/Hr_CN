<?php

class TripMoneySetForm extends CFormModel
{
	public $id;
	public $pro_name;
	public $z_index=0;
	public $z_display=1;

	public function attributeLabels()
	{
        return array(
            'pro_name'=>Yii::t('fete','project name'),
            'z_display'=>Yii::t('contract','display'),
            'z_index'=>Yii::t('fete','index'),
        );
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id, pro_name,z_display,z_index','safe'),
            array('pro_name','required'),
			array('pro_name','validateName'),
		);
	}

	public function validateName($attribute, $params){
        $city = Yii::app()->user->city();
        $id = -1;
        if(!empty($this->id)){
            $id = $this->id;
        }
        $row = Yii::app()->db->createCommand()->select("id")->from("hr_trip_money_set")
            ->where('pro_name=:pro_name and id!=:id',
                array(':pro_name'=>$this->pro_name,':id'=>$id))->queryRow();
        if($row){
            $message = Yii::t('contract','project name'). Yii::t('contract',' can not repeat');
            $this->addError($attribute,$message);
        }
	}

	public function retrieveData($index) {
        $row = Yii::app()->db->createCommand()->select("*")
            ->from("hr_trip_money_set")->where("id=:id",array(":id"=>$index))->queryRow();
		if ($row) {
            $this->id = $row['id'];
            $this->pro_name = $row['pro_name'];
            $this->z_display = $row['z_display'];
            $this->z_index = $row['z_index'];
            return true;
		}else{
            return false;
        }
	}

    //獲取出差项目费用列表
    public static function getTripMoneySetList($id=0){
	    $arr = array(""=>"");
        $rs = Yii::app()->db->createCommand()->select("id,pro_name")
            ->from("hr_trip_money_set")
            ->where("z_display=1 or id=:id",array(":id"=>$id))
            ->order("z_index desc")->queryAll();
        if($rs){
            foreach ($rs as $row){
                $arr[$row["id"]] = $row["pro_name"];
            }
        }
        return $arr;
    }

    //刪除驗證
    public function deleteValidate(){
        $row = Yii::app()->db->createCommand()->select("*")
            ->from("hr_employee_trip_money")->where("money_set_id=:id",array(":id"=>$this->id))->queryRow();
        if($row){
            return false;
        }else{
            return true;
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

	protected function saveGoods(&$connection) {
		$sql = '';
        switch ($this->scenario) {
            case 'delete':
                $sql = "delete from hr_trip_money_set where id = :id";
                break;
            case 'new':
                $sql = "insert into hr_trip_money_set(
							pro_name,z_display,z_index, city, lcu
						) values (
							:pro_name,:z_display,:z_index, :city, :lcu
						)";
                break;
            case 'edit':
                $sql = "update hr_trip_money_set set
							pro_name = :pro_name, 
							z_display = :z_display, 
							z_index = :z_index, 
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
        if (strpos($sql,':pro_name')!==false)
            $command->bindParam(':pro_name',$this->pro_name,PDO::PARAM_STR);
        if (strpos($sql,':z_display')!==false)
            $command->bindParam(':z_display',$this->z_display,PDO::PARAM_INT);
        if (strpos($sql,':z_index')!==false)
            $command->bindParam(':z_index',$this->z_index,PDO::PARAM_INT);

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
