<?php

class ReviewSetProForm extends CFormModel
{
	public $id;
	public $city;
	public $pro_name;
	public $set_id;
	public $set_name;
	public $z_index=1;

	public function attributeLabels()
	{
        return array(
            'id'=>Yii::t('contract','ID'),
            'set_name'=>Yii::t('contract','set name'),
            'pro_name'=>Yii::t('contract','pro name'),
            'z_index'=>Yii::t('fete','level'),
        );
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id, city, set_id,set_name,pro_name,z_index','safe'),
            array('set_id','required'),
            array('set_name','required'),
            array('pro_name','required'),
            array('set_id','validateID'),
            array('pro_name','validateName'),
		);
	}

    public function validateID($attribute, $params){
        $rows = Yii::app()->db->createCommand()->select("id")->from("hr_set")
            ->where('id=:id',array(':id'=>$this->set_id))->queryRow();
        if(!$rows){
            $message = Yii::t('contract','set name'). Yii::t('contract',' not exist');
            $this->addError($attribute,$message);
        }
    }

    public function validateName($attribute, $params){
        $id = -1;
        if(!empty($this->id)){
            $id = $this->id;
        }
        $rows = Yii::app()->db->createCommand()->select("id")->from("hr_set_pro")
            ->where('pro_name=:pro_name and id!=:id and set_id=:set_id',
                array(':pro_name'=>$this->pro_name,':id'=>$id,':set_id'=>$this->set_id))->queryAll();
        if(count($rows)>0){
            $message = Yii::t('contract','pro name'). Yii::t('contract',' can not repeat');
            $this->addError($attribute,$message);
        }
    }

	public function retrieveData($index) {
        $suffix = Yii::app()->params['envSuffix'];
        $row = Yii::app()->db->createCommand()->select("*")->from("hr_set_pro")
            ->where("id=:id",array(":id"=>$index))->queryRow();
		if ($row) {
            $this->id = $row['id'];
            $this->city = $row['city'];
            $this->set_id = $row['set_id'];
            $this->pro_name = $row['pro_name'];
            $this->set_name = ReviewSetForm::getSetNameToId($row['set_id']);
            $this->z_index = $row['z_index'];
		}
		return true;
	}

    //刪除驗證
    public function deleteValidate(){
        $row = Yii::app()->db->createCommand()->select("*")->from("hr_set_pro")
            ->where("id=:id",array(":id"=>$this->id))->queryRow();
        if($row){
            $this->set_id = $row['set_id'];
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
                $sql = "delete from hr_set_pro where id = :id";
                break;
            case 'new':
                $sql = "insert into hr_set_pro(
							city,z_index,set_id,pro_name, lcu
						) values (
							:city,:z_index,:set_id,:pro_name, :lcu
						)";
                break;
            case 'edit':
                $sql = "update hr_set_pro set
							z_index = :z_index, 
							set_id = :set_id, 
							pro_name = :pro_name, 
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
        //log_bool,max_log,sub_bool,sub_multiple
        if (strpos($sql,':city')!==false)
            $command->bindParam(':city',$city,PDO::PARAM_STR);
        if (strpos($sql,':z_index')!==false)
            $command->bindParam(':z_index',$this->z_index,PDO::PARAM_INT);
        if (strpos($sql,':set_id')!==false)
            $command->bindParam(':set_id',$this->set_id,PDO::PARAM_INT);
        if (strpos($sql,':pro_name')!==false)
            $command->bindParam(':pro_name',$this->pro_name,PDO::PARAM_STR);

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
