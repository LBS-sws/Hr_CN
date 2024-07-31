<?php


class SalesGroupForm extends CFormModel
{
	public $id;
	public $city;
	public $group_name;
	public $local=0;

	public function attributeLabels()
	{
        return array(
            'id'=>Yii::t('contract','ID'),
            'group_name'=>Yii::t('contract','group name'),
            'local'=>Yii::t('contract','group restrict'),
        );
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id, city,group_name,local','safe'),
            array('group_name','required'),
            array('group_name','validateName'),
		);
	}

    public function validateName($attribute, $params){
        $id = -1;
        if(!empty($this->id)){
            $id = $this->id;
        }
        //var_dump($id);die();
        $this->city = Yii::app()->user->city();
        $rows = Yii::app()->db->createCommand()->select("id,group_name")->from("hr_sales_group")
            ->where('group_name=:group_name and id!=:id and city=:city',
                array(':group_name'=>$this->group_name,':id'=>$id,':city'=>$this->city))->queryRow();
        if($rows){
            $message = Yii::t('contract','group name'). Yii::t('contract',' can not repeat')."- id:".$rows["id"];
            $this->addError($attribute,$message);
        }
    }

	public function retrieveData($index) {
        $suffix = Yii::app()->params['envSuffix'];
        $city = Yii::app()->user->city();
        $row = Yii::app()->db->createCommand()->select("*")->from("hr_sales_group")
            ->where("id=:id and city='$city'",array(":id"=>$index))->queryRow();
		if ($row) {
            $this->id = $row['id'];
            $this->city = $row['city'];
            $this->group_name = $row['group_name'];
            $this->local = $row['local'];
		}
		return true;
	}
	public static function getGroupListToId($id){
        $row = Yii::app()->db->createCommand()->select("*")->from("hr_sales_group")
            ->where('id=:id',array(':id'=>$id))->queryRow();
        if($row){
            return $row;
        }else{
            return array('id'=>$id,'group_name'=>"");
        }
    }

    //刪除驗證
    public function deleteValidate(){
        $row = Yii::app()->db->createCommand()->select("*")->from("hr_sales_staff")
            ->where("group_id=:id",array(":id"=>$this->id))->queryRow();
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
                $sql = "delete from hr_sales_group where id = :id";
                break;
            case 'new':
                $sql = "insert into hr_sales_group(
							group_name,local,city, lcu
						) values (
							:group_name,1,:city, :lcu
						)";
                break;
            case 'edit':
                $sql = "update hr_sales_group set
							group_name = :group_name, 
							local = 1, 
							city = :city, 
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
        if (strpos($sql,':local')!==false)
            $command->bindParam(':local',$this->local,PDO::PARAM_INT);
        //log_bool,max_log,sub_bool,sub_multiple
        if (strpos($sql,':city')!==false)
            $command->bindParam(':city',$city,PDO::PARAM_STR);
        if (strpos($sql,':group_name')!==false)
            $command->bindParam(':group_name',$this->group_name,PDO::PARAM_INT);

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
