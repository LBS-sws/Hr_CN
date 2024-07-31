<?php

class ConfigSystemForm extends CFormModel
{
	public $id;
	public $set_city;
	public $set_name;
	public $set_value;

	public function attributeLabels()
	{
		return array(
            'set_city'=>"配置城市",
            'set_name'=>"配置名称",
            'set_value'=>"配置的值"
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id, set_city, set_name,set_value','safe'),
            array('set_city','required'),
            array('set_name','required'),
            array('set_name','validateName'),
            array('set_value','required'),
		);
	}

	public function validateName($attribute, $params){
        $id = -1;
        if(!empty($this->id)){
            $id = $this->id;
        }
        $row = Yii::app()->db->createCommand()->select("id")->from("hr_setting")
            ->where('set_name=:set_name and id!=:id and set_city=:set_city',
                array(':set_name'=>$this->set_name,':set_city'=>$this->set_city,':id'=>$id))->queryRow();
        if($row){
            $message = "配置名称". Yii::t('contract',' can not repeat');
            $this->addError($attribute,$message);
        }
	}

	public function retrieveData($index) {
        $city_allow = Yii::app()->user->city_allow();
		$rows = Yii::app()->db->createCommand()->select("id,set_value,set_city,set_name")
            ->from("hr_setting")->where("id=:id",array(":id"=>$index))->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
                $this->id = $row['id'];
                $this->set_city = $row['set_city'];
                $this->set_name = $row['set_name'];
                $this->set_value = $row['set_value'];
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
                $sql = "delete from hr_setting where id = :id";
                break;
            case 'new':
                $sql = "insert into hr_setting(
							set_name,set_value,set_city
						) values (
							:set_name,:set_value,:set_city
						)";
                break;
            case 'edit':
                $sql = "update hr_setting set
							set_city = :set_city, 
							set_name = :set_name, 
							set_value = :set_value
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
        //id, name,start_time,end_time,log_time,city,cost_num
        if (strpos($sql,':set_value')!==false)
            $command->bindParam(':set_value',$this->set_value,PDO::PARAM_INT);
        if (strpos($sql,':set_name')!==false)
            $command->bindParam(':set_name',$this->set_name,PDO::PARAM_STR);
        if (strpos($sql,':set_city')!==false)
            $command->bindParam(':set_city',$this->set_city,PDO::PARAM_STR);
        $command->execute();

        if ($this->scenario=='new'){
            $this->id = Yii::app()->db->getLastInsertID();
            $this->scenario = "edit";
        }
		return true;
	}
}
