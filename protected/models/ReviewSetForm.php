<?php

class ReviewSetForm extends CFormModel
{
	public $id;
	public $city;
	public $set_name;
	public $four_with;
	public $num_ratio=1;
	public $z_index=1;

	public function attributeLabels()
	{
        return array(
            'id'=>Yii::t('contract','ID'),
            'set_name'=>Yii::t('contract','set name'),
            'four_with'=>Yii::t('contract','four with'),
            'num_ratio'=>Yii::t('contract','num ratio'),
            'z_index'=>Yii::t('fete','level'),
        );
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id, city,set_name,z_index,four_with,num_ratio','safe'),
            array('set_name','required'),
            array('num_ratio','required'),
            array('num_ratio','numerical','min'=>1),
            array('set_name','validateName'),
		);
	}

    public function validateName($attribute, $params){
        $id = -1;
        if(!empty($this->id)){
            $id = $this->id;
        }
        $rows = Yii::app()->db->createCommand()->select("id")->from("hr_set")
            ->where('set_name=:set_name and id!=:id',
                array(':set_name'=>$this->set_name,':id'=>$id))->queryAll();
        if(count($rows)>0){
            $message = Yii::t('contract','set name'). Yii::t('contract',' can not repeat');
            $this->addError($attribute,$message);
        }
    }

    public function getSetNameToId($id){
        $rows = Yii::app()->db->createCommand()->select("set_name")->from("hr_set")
            ->where('id=:id',array(':id'=>$id))->queryRow();
        if($rows){
            return $rows["set_name"];
        }else{
            return $id;
        }
    }

    public function getFourWith($str=''){
        $arr = array(
            Yii::t("misc","No"),
            Yii::t("misc","Yes"),
        );
        if(key_exists($str,$arr)){
            return $arr[$str];
        }
        return $arr;
    }

	public function retrieveData($index) {
        $suffix = Yii::app()->params['envSuffix'];
        $row = Yii::app()->db->createCommand()->select("*")->from("hr_set")
            ->where("id=:id",array(":id"=>$index))->queryRow();
		if ($row) {
            $this->id = $row['id'];
            $this->city = $row['city'];
            $this->set_name = $row['set_name'];
            $this->four_with = $row['four_with'];
            $this->z_index = $row['z_index'];
            $this->num_ratio = $row['num_ratio'];
		}
		return true;
	}

    //刪除驗證
    public function deleteValidate(){
        $row = Yii::app()->db->createCommand()->select("*")->from("hr_set_pro")
            ->where("set_id=:id",array(":id"=>$this->id))->queryRow();
        if ($row) {
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
                $sql = "delete from hr_set where id = :id";
                break;
            case 'new':
                $sql = "insert into hr_set(
							city,z_index,set_name,four_with,num_ratio, lcu
						) values (
							:city,:z_index,:set_name,:four_with,:num_ratio, :lcu
						)";
                break;
            case 'edit':
                $sql = "update hr_set set
							z_index = :z_index, 
							set_name = :set_name, 
							four_with = :four_with, 
							num_ratio = :num_ratio, 
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
        if (strpos($sql,':set_name')!==false)
            $command->bindParam(':set_name',$this->set_name,PDO::PARAM_STR);
        if (strpos($sql,':four_with')!==false)
            $command->bindParam(':four_with',$this->four_with,PDO::PARAM_INT);
        if (strpos($sql,':num_ratio')!==false)
            $command->bindParam(':num_ratio',$this->num_ratio,PDO::PARAM_INT);

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
