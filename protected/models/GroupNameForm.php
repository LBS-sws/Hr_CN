<?php

class GroupNameForm extends CFormModel
{
	/* User Fields */
	public $id;
	public $group_code;
	public $group_name;
	public $group_remark;
	public $group_sum=0;

	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
            'group_code'=>Yii::t('group','GroupCode'),
            'group_name'=>Yii::t('group','GroupName'),
            'group_remark'=>Yii::t('group','GroupRemark'),
            'group_sum'=>Yii::t('group','GroupSum'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
            array('id,group_code,group_name,group_remark,group_sum','safe'),
			array('group_code,group_name','required'),
			array('id','validateDel',"on"=>array("delete")),
		);
	}
    public function validateDel($attribute, $params){
        $row = Yii::app()->db->createCommand()->select("id")
            ->from("hr_group_staff")->where("group_id=:id",array(":id"=>$this->id))->queryRow();
        if($row){
            $message = "该分组内含有员工，无法删除";
            $this->addError($attribute,$message);
        }
    }

	public function retrieveData($index)
	{
		$suffix = Yii::app()->params['envSuffix'];
		$sql = "select * from hr_group where id='".$index."'";
		$row = Yii::app()->db->createCommand($sql)->queryRow();
		if ($row!==false) {
			$this->id = $row['id'];
			$this->group_code = $row['group_code'];
			$this->group_name = $row['group_name'];
			$this->group_remark = $row['group_remark'];
            return true;
		}else{
		    return false;
        }
	}

	public static function getGroupNameToID($group_id){
        $row = Yii::app()->db->createCommand()->select("group_name")
            ->from("hr_group")->where("id=:id",array(":id"=>$group_id))->queryRow();
        if($row){
            return $row["group_name"];
        }else{
            return $group_id;
        }
    }
	public static function resetGroupSum($group_id){
        $group_id = empty($group_id)||!is_numeric($group_id)?0:intval($group_id);
        $sql = "update hr_group set group_sum=(SELECT count(b.id) FROM hr_group_staff b WHERE b.group_id={$group_id}) WHERE id={$group_id}";
        Yii::app()->db->createCommand($sql)->execute();
    }
	
	public function saveData()
	{
		$connection = Yii::app()->db;
		$transaction=$connection->beginTransaction();
		try {
			$this->saveDataForSql($connection);
			$transaction->commit();
		}
		catch(Exception $e) {
		    var_dump($e);
			$transaction->rollback();
			throw new CHttpException(404,'Cannot update.');
		}
	}

	protected function saveDataForSql(&$connection)
	{
		$suffix = Yii::app()->params['envSuffix'];
		$sql = '';
		switch ($this->scenario) {
			case 'delete':
				$sql = "delete from hr_group where id = :id";
				break;
			case 'new':
				$sql = "insert into hr_group(
						group_code, group_name, group_remark, lcu, lcd) values (
						:group_code, :group_name, :group_remark, :lcu, :lcd)";
				break;
			case 'edit':
				$sql = "update hr_group set 
					group_code = :group_code, 
					group_name = :group_name,
					group_remark = :group_remark,
					luu = :luu
					where id = :id";
				break;
		}

		$uid = Yii::app()->user->id;

		$command=$connection->createCommand($sql);
		if (strpos($sql,':id')!==false)
			$command->bindParam(':id',$this->id,PDO::PARAM_INT);
		if (strpos($sql,':group_code')!==false)
			$command->bindParam(':group_code',$this->group_code,PDO::PARAM_STR);
		if (strpos($sql,':group_name')!==false)
			$command->bindParam(':group_name',$this->group_name,PDO::PARAM_STR);
		if (strpos($sql,':group_remark')!==false)
			$command->bindParam(':group_remark',$this->group_remark,PDO::PARAM_STR);

		if (strpos($sql,':lcu')!==false)
			$command->bindParam(':lcu',$uid,PDO::PARAM_STR);
		if (strpos($sql,':luu')!==false)
			$command->bindParam(':luu',$uid,PDO::PARAM_STR);
		if (strpos($sql,':lcd')!==false){
            $date = date("Y-m-d H:i:s");
            $command->bindParam(':lcd',$date,PDO::PARAM_STR);
        }
		$command->execute();

        if ($this->scenario=='new')
            $this->id = Yii::app()->db->getLastInsertID();

		return true;
	}
}