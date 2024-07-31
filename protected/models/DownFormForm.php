<?php

/**
 * UserForm class.
 * UserForm is the data structure for keeping
 * user form data. It is used by the 'user' action of 'SiteController'.
 */
class DownFormForm extends CFormModel
{
	/* User Fields */
	public $id;
	public $remark;
	public $name;
	public $docx_url;
	public $file;
	//public $word_html;
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
			'id'=>Yii::t('staff','Record ID'),
			'name'=>Yii::t('contract','Word Name'),
			'file'=>Yii::t('contract','Word File'),
			'remark'=>Yii::t('contract','Remark'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			//array('id, position, leave_reason, remarks, email, staff_type, leader','safe'),
            array('id, name, docx_url, file, remark','safe'),
			array('name','required'),
			array('name','validateName'),
            array('file', 'file', 'types'=>'xlsx,xls,doc,docx,pdf', 'allowEmpty'=>false, 'maxFiles'=>1,'on'=>"new"),
            array('file', 'file', 'types'=>'xlsx,xls,doc,docx,pdf', 'allowEmpty'=>true, 'maxFiles'=>1,'on'=>"edit"),
		);
	}

	public function validateName($attribute, $params){
        $rows = Yii::app()->db->createCommand()->select()->from("hr_down_form")
            ->where('id!=:id and name=:name ', array(':id'=>$this->id,':name'=>$this->name))->queryAll();
        if (count($rows) > 0){
            $message = Yii::t('contract','Word Name'). Yii::t('contract',' can not repeat');
            $this->addError($attribute,$message);
        }
    }

    //文檔刪除
    public function validateDelete(){
        return true;
    }

//根據id獲取文檔地址
	public function getDocxUrlToId($index)
	{
        $city = Yii::app()->user->city();
        $rows = Yii::app()->db->createCommand()->select()->from("hr_down_form")
            ->where('id=:id', array(':id'=>$index))->queryAll();
		if (count($rows) > 0){
		    return $rows[0];
		}
		return false;
	}

	public function retrieveData($index)
	{
        $city = Yii::app()->user->city();
        $rows = Yii::app()->db->createCommand()->select()->from("hr_down_form")
            ->where('id=:id', array(':id'=>$index))->queryAll();
		if (count($rows) > 0)
		{
			foreach ($rows as $row)
			{
				$this->id = $row['id'];
				$this->name = $row['name'];
				$this->remark = $row['remark'];
				$this->docx_url = $row['docx_url'];
				break;
			}
		}
		return true;
	}
	
	public function saveData()
	{
		$connection = Yii::app()->db;
		$transaction=$connection->beginTransaction();
		try {
			$this->saveStaff($connection);
			$transaction->commit();
		}
		catch(Exception $e) {
			$transaction->rollback();
			throw new CHttpException(404,'Cannot update.');
		}
	}

	protected function saveStaff(&$connection)
	{
		$sql = '';
        $city = Yii::app()->user->city();
        $uid = Yii::app()->user->id;
		switch ($this->scenario) {
			case 'delete':
                $sql = "delete from hr_down_form where id = :id";
				break;
			case 'new':
				$sql = "insert into hr_down_form(
							name, docx_url, remark, lcu
						) values (
							:name, :docx_url, :remark, :lcu
						)";
				break;
			case 'edit':
				$sql = "update hr_down_form set
							name = :name, 
							docx_url = :docx_url, 
							remark = :remark, 
							luu = :luu 
						where id = :id
						";
				break;
		}

		$command=$connection->createCommand($sql);
		if (strpos($sql,':id')!==false)
			$command->bindParam(':id',$this->id,PDO::PARAM_INT);
		if (strpos($sql,':name')!==false)
			$command->bindParam(':name',$this->name,PDO::PARAM_STR);
		if (strpos($sql,':docx_url')!==false)
            $command->bindParam(':docx_url',$this->docx_url,PDO::PARAM_STR);

        if (strpos($sql,':remark')!==false)
            $command->bindParam(':remark',$this->remark,PDO::PARAM_STR);
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
