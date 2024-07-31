<?php

/**
 * UserForm class.
 * UserForm is the data structure for keeping
 * user form data. It is used by the 'user' action of 'SiteController'.
 */
class WagesForm extends CFormModel
{
	/* User Fields */
	public $id;
	public $wages_name;
	public $city;
	public $wages_list=array(array("type_name"=>"基本工資","compute"=>"0","z_index"=>"999"));
	public $bool = true;//是否允許操作
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
			'id'=>Yii::t('staff','Record ID'),
			'wages_name'=>Yii::t('contract','Wages Name'),
			'wages_list'=>Yii::t('contract','Wages Type'),
			'city'=>Yii::t('contract','City'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			//array('id, position, leave_reason, remarks, email, staff_type, leader','safe'),
            array('id,bool,city, wages_name, wages_list','safe'),
			array('wages_name','required'),
			array('city','required'),
			array('wages_name','validateName'),
			array('wages_list','required'),
			array('wages_list','validateList'),
			array('wages_list','validateBool','on'=>array('edit','delete')),
		);
	}

    public function validateName($attribute, $params){
        $city = $this->city;
        $rows = Yii::app()->db->createCommand()->select()->from("hr_wages")
            ->where('id!=:id and wages_name=:wages_name and city=:city ', array(':id'=>$this->id,':wages_name'=>$this->wages_name,':city'=>$city))->queryAll();
        if (count($rows) > 0){
            $message = Yii::t('contract','Wages Name'). Yii::t('contract',' can not repeat');
            $this->addError($attribute,$message);
        }
    }

    public function validateBool($attribute, $params){
        $city = Yii::app()->user->city();
        $rows = Yii::app()->db->createCommand()->select()->from("hr_employee")
            ->where('price1=:id and staff_status != 1', array(':id'=>$this->id))->queryAll();
        if (count($rows) > 0){
            $message = Yii::t('dialog','This record is already in use');
            $this->addError($attribute,$message);
        }
    }

	public function validateList($attribute, $params){
        if (!empty($this->wages_list)){
            if (!is_array($this->wages_list)){
                $message = Yii::t('contract','Wages Type'). Yii::t('contract',' can not be empty');
                $this->addError($attribute,$message);
            }else{
                foreach ($this->wages_list as $list){
                    if(empty($list["type_name"])){
                        $message = Yii::t('contract','Wages Type Name'). Yii::t('contract',' can not be empty');
                        $this->addError($attribute,$message);
                        return false;
                    }
                }
            }
        }
    }

    //獲取所有工资组合(相同城市)
	public function getWagesList(){
        $city = Yii::app()->user->city();
        $city_allow = Yii::app()->user->city_allow();
        $rows = Yii::app()->db->createCommand()->select("id,wages_name")->from("hr_wages")->where("city in ($city_allow)")->queryAll();
        $arr = array(""=>"");
        foreach ($rows as $row){
            $arr[$row["id"]] = $row["wages_name"];
        }
        return $arr;
    }

    //工资组合刪除時必須沒有員工
	public function validateDelete(){
        $rows = Yii::app()->db->createCommand()->select()->from("hr_employee")
            ->where('price1=:id and staff_status != 1', array(':id'=>$this->id))->queryAll();
        if ($rows){
            return false;
        }
        return true;
    }

    //獲取工資組合類型列表
	public function getWagesTypeList($wages_id){
        $rows = Yii::app()->db->createCommand()->select()->from("hr_wages_con")
            ->where('wages_id=:wages_id', array(':wages_id'=>$wages_id))->order("z_index desc")->queryAll();
        if ($rows){
            return $rows;
        }
        return array();
    }
    //刪除工資單下的某個屬性
    public function delWagesConfigToWagesId($id){
	    $wagesId = Yii::app()->db->createCommand()->select("wages_id")->from('hr_wages_con')->where("id=:id",array(":id"=>$id))->queryRow();
	    if($wagesId){
            $rows = Yii::app()->db->createCommand()->select()->from("hr_employee")
                ->where('price1=:id and staff_status != 1', array(':id'=>$wagesId["wages_id"]))->queryAll();
            if($rows){
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','This record is already in use'));
                return false;
            }else{
                $rows = Yii::app()->db->createCommand()->delete('hr_wages_con', 'id=:id', array(':id'=>$id));
                return true;
            }
        }else{
	        return false;
        }
    }
    //獲取計算公式
    public function getComputeList(){
        return array(Yii::t("contract","Fixed wages"),Yii::t("contract","Hour wages"),Yii::t("contract","Commission wages"));
    }

	public function retrieveData($index)
	{
        $city_allow = Yii::app()->user->city_allow();
        $rows = Yii::app()->db->createCommand()->select()->from("hr_wages")
            ->where("id=:id and city in ($city_allow)", array(':id'=>$index))->queryAll();
		if (count($rows) > 0)
		{
			foreach ($rows as $row)
			{
				$this->id = $row['id'];
				$this->wages_name = $row['wages_name'];
				$this->city = $row['city'];
				$this->wages_list = $this->getWagesTypeList($this->id);

                $bool = Yii::app()->db->createCommand()->select()->from("hr_employee")
                    ->where('price1=:id and staff_status != 1', array(':id'=>$this->id))->queryAll();
                if($bool){
                    $this->bool = false;
                }else{
                    $this->bool = true;
                }
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
        $city_allow = Yii::app()->user->city_allow();
        $uid = Yii::app()->user->id;
		switch ($this->scenario) {
			case 'delete':
                $sql = "delete from hr_wages where id = :id and city in ($city_allow)";
				break;
			case 'new':
				$sql = "insert into hr_wages(
							wages_name, city, lcu, lcd
						) values (
							:wages_name, :city, :lcu, :lcd
						)";
				break;
			case 'edit':
				$sql = "update hr_wages set
							wages_name = :wages_name, 
							city = :city, 
							lud = :lud,
							luu = :luu 
						where id = :id
						";
				break;
		}

		$command=$connection->createCommand($sql);
		if (strpos($sql,':id')!==false)
			$command->bindParam(':id',$this->id,PDO::PARAM_INT);
		if (strpos($sql,':wages_name')!==false)
			$command->bindParam(':wages_name',$this->wages_name,PDO::PARAM_STR);

        if (strpos($sql,':city')!==false)
            $command->bindParam(':city',$this->city,PDO::PARAM_STR);
		if (strpos($sql,':luu')!==false)
			$command->bindParam(':luu',$uid,PDO::PARAM_STR);
		if (strpos($sql,':lcu')!==false)
			$command->bindParam(':lcu',$uid,PDO::PARAM_STR);
		if (strpos($sql,':lcd')!==false)
			$command->bindParam(':lcd',date('Y-m-d H:i:s'),PDO::PARAM_STR);
		if (strpos($sql,':lud')!==false)
			$command->bindParam(':lud',date('Y-m-d H:i:s'),PDO::PARAM_STR);

		$command->execute();

        if ($this->scenario=='new'){
            $this->id = Yii::app()->db->getLastInsertID();
            $this->scenario = "edit";
        }

        //更新工資配置表
        $this->renewalWagesList();
        return true;
	}

	private function renewalWagesList(){
        if (!empty($this->wages_list)){
            foreach ($this->wages_list as $value){
                if(!empty($value["id"])){
                    //修改
                    Yii::app()->db->createCommand()->update('hr_wages_con', array(
                        'wages_id'=>$this->id,
                        'type_name'=>$value["type_name"],
                        'z_index'=>$value["z_index"]
                    ), 'id=:id', array(':id'=>$value["id"]));
                }else{
                    //添加
                    Yii::app()->db->createCommand()->insert('hr_wages_con', array(
                        'wages_id'=>$this->id,
                        'type_name'=>$value["type_name"],
                        'z_index'=>$value["z_index"]
                    ));
                }
            }
        }
    }
}
