<?php

/**
 * UserForm class.
 * UserForm is the data structure for keeping
 * user form data. It is used by the 'user' action of 'SiteController'.
 */
class PlusCityForm extends CFormModel
{
	/* User Fields */
	public $id = 0;
	public $employee_id;
	public $employee_name;
	public $original_city;
	public $city;
	public $department;
	public $position;
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
            'id'=>Yii::t('contract','ID'),
            'employee_id'=>Yii::t('contract','Employee Name'),
            'original_city'=>Yii::t('contract','original city'),
            'city'=>Yii::t('contract','plus city'),
            'department'=>Yii::t('contract','plus dept'),
            'position'=>Yii::t('contract','plus leader'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			//array('id, position, leave_reason, remarks, email, staff_type, leader','safe'),
            array('id, employee_id, city, department, position','safe'),
			array('employee_id, city, department, position','required'),
			array('employee_id','validateEmployee'),
		);
	}

	public function validateEmployee($attribute, $params){
	    if(!empty($this->employee_id)){
            $city_allow = Yii::app()->user->city_allow();
            $rows = Yii::app()->db->createCommand()->select("name,city")->from("hr_employee")
                ->where("id=:id and city in ($city_allow) and staff_status=0 ", array(':id'=>$this->employee_id))->queryRow();
            if ($rows){
                $this->employee_name = $rows["name"];
                $this->original_city = $rows["city"];
            }else{
                $message = Yii::t('contract','Employee Name'). Yii::t('contract',' Did not find');
                $this->addError($attribute,$message);
            }
        }
    }
    //獲取用戶表的所有員工(相同城市)
	public function getEmployeeList(){
        $city = Yii::app()->user->city();
        $city_allow = Yii::app()->user->city_allow();
        $city_allow = empty($city_allow)?"'$city'":$city_allow;
        $rows = Yii::app()->db->createCommand()->select("id,code,name")->from("hr_employee")
            ->where("city in ($city_allow) and staff_status=0")->order("name desc")->queryAll();
        $arr = array(""=>"");
        foreach ($rows as $row){
            $arr[$row["id"]] = $row["code"]." -- ".$row["name"];
        }
        return $arr;
    }

    //
	public static function getPlusEmployeeList(){;
        $city_allow = Yii::app()->user->city_allow();
        $plusRows = Yii::app()->db->createCommand()->select("a.employee_id")->from("hr_plus_city a")
            ->leftJoin("hr_employee b","a.employee_id = b.id")
            ->where("a.city in ($city_allow) or b.city in ($city_allow)")->queryAll();
        if(count($plusRows)>0){
            $plusRows = array_column($plusRows,"employee_id");
            $plusSql = implode(",",$plusRows);
        }else{
            $plusSql = "''";
        }
        return array('plusSql'=>$plusSql,'plusRows'=>$plusRows);
    }

    //
	public function getAjaxPlusCity($city,$department,$position){
        $arr = array('department'=>'<option value=""></option>','position'=>'<option value=""></option>');
        if(!empty($city)){
            $departmentList = Yii::app()->db->createCommand()->select("id,name")->from("hr_dept")
                ->where("city=:city and type=0",array(":city"=>$city))->order("z_index desc")->queryAll();
            foreach ($departmentList as $item){
                if($department==$item["id"]){
                    $arr['department'] .= "<option value=".$item["id"]." selected>".$item["name"]."</option>";
                }else{
                    $arr['department'] .= "<option value=".$item["id"].">".$item["name"]."</option>";
                }
            }
        }
        if(!empty($department)){
            $positionList = Yii::app()->db->createCommand()->select("id,name")->from("hr_dept")
                ->where("dept_id=:dept_id",array(":dept_id"=>$department))->order("z_index desc")->queryAll();
            foreach ($positionList as $item){
                if($position==$item["id"]){
                    $arr['position'] .= "<option value=".$item["id"]." selected>".$item["name"]."</option>";
                }else{
                    $arr['position'] .= "<option value=".$item["id"].">".$item["name"]."</option>";
                }
            }
        }
        return $arr;
    }

    //公司刪除時必須沒有員工
	public function validateDelete(){
/*        $rows = Yii::app()->db->createCommand()->select()->from("hr_employee")
            ->where('company_id=:company_id', array(':company_id'=>$this->id))->queryAll();
        if ($rows){
            return false;
        }*/
        return true;
    }

	public function retrieveData($index)
	{
        $city = Yii::app()->user->city();
        $city_allow = Yii::app()->user->city_allow();
        $rows = Yii::app()->db->createCommand()->select("a.*,b.city as original_city")->from("hr_plus_city a")
            ->leftJoin("hr_employee b","a.employee_id = b.id")
            ->where("a.id=:id and b.city in ($city_allow) ", array(':id'=>$index))->queryAll();
		if (count($rows) > 0)
		{
			foreach ($rows as $row)
			{
				$this->id = $row['id'];
				$this->employee_id = $row['employee_id'];
				$this->city = $row['city'];
                $this->department = $row['department'];
                $this->position = $row['position'];
                $this->original_city = $row['original_city'];
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
                $sql = "delete from hr_plus_city where id = :id";
				break;
			case 'new':
				$sql = "insert into hr_plus_city(
							employee_id, city, department, position, lcu
						) values (
							:employee_id, :city, :department, :position, :lcu
						)";
				break;
			case 'edit':
				$sql = "update hr_plus_city set
							employee_id = :employee_id, 
							city = :city, 
							department = :department, 
							position = :position,
							luu = :luu
						where id = :id
						";
				break;
		}

		$command=$connection->createCommand($sql);
		if (strpos($sql,':id')!==false)
			$command->bindParam(':id',$this->id,PDO::PARAM_INT);
		if (strpos($sql,':employee_id')!==false)
			$command->bindParam(':employee_id',$this->employee_id,PDO::PARAM_STR);
		if (strpos($sql,':department')!==false)
			$command->bindParam(':department',$this->department,PDO::PARAM_STR);
		if (strpos($sql,':position')!==false)
			$command->bindParam(':position',$this->position,PDO::PARAM_STR);

        if (strpos($sql,':city')!==false)
            $command->bindParam(':city',$this->city,PDO::PARAM_STR);
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

if (!function_exists('array_column')) {
    function array_column($arr2, $column_key)
    {
        $data = [];
        foreach ($arr2 as $key => $value) {
            $data[] = $value[$column_key];
        }
        return $data;
    }
}
