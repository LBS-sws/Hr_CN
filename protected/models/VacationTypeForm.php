<?php

class VacationTypeForm extends CFormModel
{
	public $id=0;
	public $vaca_code;
	public $vaca_name;

	public function attributeLabels()
	{
		return array(
            'vaca_code'=>Yii::t('fete','Vacation type code'),
            'vaca_name'=>Yii::t('fete','Vacation type name')
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id, vaca_code,vaca_name','safe'),
            array('vaca_code','required'),
            array('vaca_name','required'),
			array('vaca_code','validateCode'),
			array('vaca_name','validateName'),
		);
	}

	public function validateCode($attribute, $params){
        $id = -1;
        if(!empty($this->id)){
            $id = $this->id;
        }
        $rows = Yii::app()->db->createCommand()->select("id")->from("hr_vacation_type")
            ->where('vaca_code=:vaca_code and id!=:id',
                array(':vaca_code'=>$this->vaca_code,':id'=>$id))->queryAll();
        if(count($rows)>0){
            $message = Yii::t('fete','Vacation type code'). Yii::t('contract',' can not repeat');
            $this->addError($attribute,$message);
        }
	}

	public function validateName($attribute, $params){
        $id = -1;
        if(!empty($this->id)){
            $id = $this->id;
        }
        $rows = Yii::app()->db->createCommand()->select("id")->from("hr_vacation_type")
            ->where('vaca_name=:vaca_name and id!=:id',
                array(':vaca_name'=>$this->vaca_name,':id'=>$id))->queryAll();
        if(count($rows)>0){
            $message = Yii::t('fete','Vacation type name'). Yii::t('contract',' can not repeat');
            $this->addError($attribute,$message);
        }
	}

	public function retrieveData($index) {
        $city_allow = Yii::app()->user->city_allow();
		$rows = Yii::app()->db->createCommand()->select("*")
            ->from("hr_vacation_type")->where("id=:id",array(":id"=>$index))->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
                $this->id = $row['id'];
                $this->vaca_code = $row['vaca_code'];
                $this->vaca_name = $row['vaca_name'];
                break;
			}
		}
		return true;
	}

    //根據請假類型
    public function getVacaTypeLIst(){
        $rows = Yii::app()->db->createCommand()->select("vaca_code")
            ->from("hr_vacation_type")->where("id!=:id",array(":id"=>$this->id))->queryAll();
        if ($rows){
            $rows = array_column($rows,"vaca_code");
        }else{
            $rows = array();
        }
        $arr =array();
        for($i=65;$i<91;$i++){
            $str = chr($i);
            if(in_array($str,$rows)){
                continue;
            }
            $arr[$str]=$str;
        }
        return $arr;
    }

    //刪除驗證
    public function deleteValidate(){
        $rows = Yii::app()->db->createCommand()->select("id")
            ->from("hr_vacation")->where("vaca_type=:vaca_type",array(":vaca_type"=>$this->vaca_code))->queryRow();
        if($rows){
            return false;
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

	protected function saveGoods(&$connection) {
		$sql = '';
        switch ($this->scenario) {
            case 'delete':
                $sql = "delete from hr_vacation_type where id = :id";
                break;
            case 'new':
                $sql = "insert into hr_vacation_type(
							vaca_code,vaca_name
						) values (
							:vaca_code,:vaca_name
						)";
                break;
            case 'edit':
                $sql = "update hr_vacation_type set
							vaca_code = :vaca_code, 
							vaca_name = :vaca_name
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
        if (strpos($sql,':vaca_code')!==false)
            $command->bindParam(':vaca_code',$this->vaca_code,PDO::PARAM_STR);
        if (strpos($sql,':vaca_name')!==false)
            $command->bindParam(':vaca_name',$this->vaca_name,PDO::PARAM_STR);
        $command->execute();

        if ($this->scenario=='new'){
            $this->id = Yii::app()->db->getLastInsertID();
            $this->scenario = "edit";
        }
		return true;
	}
}
