<?php

class PinClassForm extends CFormModel
{
	public $id;
	public $name;
	public $z_index=0;

	public function attributeLabels()
	{
        return array(
            'name'=>Yii::t('app','Pin Class'),
            'z_index'=>Yii::t('contract','Level'),
        );
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id,name,z_index','safe'),
            array('z_index,name','required'),
            array('id','validateId','on'=>array('delete')),
		);
	}

    public function validateId($attribute, $params){
        $row = Yii::app()->db->createCommand()->select("id,name,z_index")->from("hr_pin_name")
            ->where("class_id=:class_id",array(":class_id"=>$this->id))->queryRow();
        if($row){
            $message = "襟章类别里有襟章，无法删除";
            $this->addError($attribute,$message);
        }
    }

	public function retrieveData($index) {
		$row = Yii::app()->db->createCommand()->select("id,name,z_index")->from("hr_pin_class")
            ->where("id=:id",array(":id"=>$index))->queryRow();
		if ($row) {
            $this->id = $row['id'];
            $this->name = $row['name'];
            $this->z_index = $row['z_index'];
            return true;
		}
        return false;
	}

	//获取所有类别列表
    public static function getPinClassList(){
        $rows = Yii::app()->db->createCommand()->select("id,name,z_index")->from("hr_pin_class")
            ->order("z_index asc")->queryAll();
        $arr = array(""=>"");
        if($rows){
            foreach ($rows as $row){
                $arr[$row["id"]] = $row["name"];
            }
        }
        return $arr;
    }

    //刪除驗證
    public function deleteValidate(){
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
                $sql = "delete from hr_pin_class where id = :id";
                break;
            case 'new':
                $sql = "insert into hr_pin_class(
							name,z_index, lcu
						) values (
							:name,:z_index, :lcu
						)";
                break;
            case 'edit':
                $sql = "update hr_pin_class set
							name = :name, 
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
        if (strpos($sql,':z_index')!==false)
            $command->bindParam(':z_index',$this->z_index,PDO::PARAM_INT);

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
