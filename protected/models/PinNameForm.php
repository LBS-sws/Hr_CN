<?php

class PinNameForm extends CFormModel
{
	public $id;
	public $name;
	public $class_id;
	public $image_url;
	public $pin_type;
	public $z_index=0;

	public function attributeLabels()
	{
        return array(
            'name'=>Yii::t('app','Pin Name'),
            'class_id'=>Yii::t('app','Pin Class'),
            'image_url'=>Yii::t('contract','Pin Image'),
            'z_index'=>Yii::t('contract','Level'),
            'pin_type'=>Yii::t('contract','pin type'),
        );
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id,name,class_id,z_index,image_url,pin_type','safe'),
            array('z_index,class_id,name','required'),
            //array('image_url','validateImage'),
            array('id','validateId',"on"=>array("delete")),
		);
	}

    public function validateImage($attribute, $params){
        $row = Yii::app()->db->createCommand()->select("id")->from("hr_pin_inventory")
            ->where("pin_name_id=:pin_name_id",array(":pin_name_id"=>$this->id))->queryRow();
        if($row){
            $message = "襟章已被地区录入库存，无法删除";
            $this->addError($attribute,$message);
        }
    }

    public function validateId($attribute, $params){
        $row = Yii::app()->db->createCommand()->select("id")->from("hr_pin_inventory")
            ->where("pin_name_id=:pin_name_id",array(":pin_name_id"=>$this->id))->queryRow();
        if($row){
            $message = "襟章已被地区录入库存，无法删除";
            $this->addError($attribute,$message);
        }
    }

	public function retrieveData($index) {
		$row = Yii::app()->db->createCommand()->select("id,name,pin_type,image_url,class_id,z_index")->from("hr_pin_name")
            ->where("id=:id",array(":id"=>$index))->queryRow();
		if ($row) {
            $this->id = $row['id'];
            $this->name = $row['name'];
            $this->class_id = $row['class_id'];
            $this->image_url = $row['image_url'];
            $this->pin_type = $row['pin_type'];
            $this->z_index = $row['z_index'];
            return true;
		}
        return false;
	}

    //获取所有名称列表
    public static function getPinNameList(){
        $rows = Yii::app()->db->createCommand()->select("a.id,a.name,a.pin_type,a.z_index")
            ->from("hr_pin_name a")
            ->leftJoin("hr_pin_class b",'a.class_id=b.id')
            ->order("b.z_index asc,a.z_index asc")->queryAll();
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
                $sql = "delete from hr_pin_name where id = :id";
                break;
            case 'new':
                $sql = "insert into hr_pin_name(
							name,z_index,class_id,image_url,pin_type, lcu
						) values (
							:name,:z_index,:class_id,:image_url,:pin_type, :lcu
						)";
                break;
            case 'edit':
                $sql = "update hr_pin_name set
							name = :name, 
							class_id = :class_id, 
							z_index = :z_index, 
							image_url = :image_url, 
							pin_type = :pin_type, 
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

        if (strpos($sql,':image_url')!==false)
            $command->bindParam(':image_url',$this->image_url,PDO::PARAM_STR);
        if (strpos($sql,':class_id')!==false)
            $command->bindParam(':class_id',$this->class_id,PDO::PARAM_INT);
        if (strpos($sql,':pin_type')!==false)
            $command->bindParam(':pin_type',$this->pin_type,PDO::PARAM_INT);
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
