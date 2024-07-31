<?php

class BankAbbrSetForm extends CFormModel
{
	public $id;
	public $name;
	public $display = 1;
	public $z_index = 0;

	public function attributeLabels()
	{
		return array(
            'name'=>Yii::t('contract','Bank Abbr Name'),
            'display'=>Yii::t('contract','display'),
            'z_index'=>Yii::t('contract','z_index'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id, name,display,z_index','safe'),
            array('name','required'),
			array('name','validateName'),
		);
	}

	public function validateName($attribute, $params){
        $id = -1;
        if(!empty($this->id)){
            $id = $this->id;
        }
        $rows = Yii::app()->db->createCommand()->select("id")->from("hr_bank_set")
            ->where('name=:name and id!=:id',
                array(':name'=>$this->name,':id'=>$id))->queryAll();
        if(count($rows)>0){
            $message = Yii::t('contract','Bank Abbr Name'). Yii::t('contract',' can not repeat');
            $this->addError($attribute,$message);
        }
	}

	public function retrieveData($index) {
		$rows = Yii::app()->db->createCommand()->select("*")
            ->from("hr_bank_set")->where("id=:id",array(":id"=>$index))->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
                $this->id = $row['id'];
                $this->name = $row['name'];
                $this->display = $row['display'];
                $this->z_index = $row['z_index'];
                break;
			}
		}
		return true;
	}

    //刪除驗證
    public function deleteValidate(){
        $rs0 = Yii::app()->db->createCommand()->select()->from("hr_employee")
            ->where('bank_type=:bank_type',array(':bank_type'=>$this->id))->queryRow();
        if($rs0){
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
                $sql = "delete from hr_bank_set where id = :id";
                break;
            case 'new':
                $sql = "insert into hr_bank_set(
							name,display,z_index, city, lcu
						) values (
							:name,:display,:z_index, :city, :lcu
						)";
                break;
            case 'edit':
                $sql = "update hr_bank_set set
							name = :name, 
							display = :display, 
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
        if (strpos($sql,':name')!==false)
            $command->bindParam(':name',$this->name,PDO::PARAM_STR);
        if (strpos($sql,':display')!==false)
            $command->bindParam(':display',$this->display,PDO::PARAM_INT);
        if (strpos($sql,':z_index')!==false)
            $command->bindParam(':z_index',$this->z_index,PDO::PARAM_STR);

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
