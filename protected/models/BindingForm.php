<?php

/**
 * UserForm class.
 * UserForm is the data structure for keeping
 * user form data. It is used by the 'user' action of 'SiteController'.
 */
class BindingForm extends CFormModel
{
	/* User Fields */
	public $id = 0;
	public $employee_id;
	public $employee_name;
	public $user_id;
	public $city;
	public $employee_city;
	public $user_city;
	public $user_name;
	public $lcu;
	public $luu;
	public $lcd;
	public $lud;
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
			'id'=>Yii::t('staff','Record ID'),
            'employee_id'=>Yii::t('contract','Employee Name'),
            'user_id'=>Yii::t('contract','Account number'),
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
            array('id, user_id, employee_id','safe'),
			array('user_id','required'),
			array('employee_id','required'),
			array('id','validateID'),
			array('user_id','validateUser'),
			array('employee_id','validateEmployee'),
		);
	}

    //
    public function validateID($attribute, $params){
	    if($this->getScenario()=="edit"){
            $row = Yii::app()->db->createCommand()->select("lcu,luu,lcd,lud")
                ->from("hr_binding a")
                ->where("a.id=:id", array(':id'=>$this->id))->queryRow();
            if($row){
                $this->lcu = $row['lcu'];
                $this->lcd = $row['lcd'];
                $this->luu = Yii::app()->user->id;
                $this->lud = date("Y-m-d H:i:s");
            }else{
                $message = "数据异常，请刷新重试";
                $this->addError($attribute,$message);
            }
        }elseif ($this->getScenario()=="new"){
            $this->lcu = Yii::app()->user->id;
            $this->lcd = date("Y-m-d H:i:s");
        }
    }

	public function validateUser($attribute, $params){
        $city_allow = Yii::app()->user->city_allow();
        $city_allow = explode(",",$city_allow);
        $citySql = "";
        foreach ($city_allow as $city){
            $citySql.=empty($citySql)?"":" or ";
            $citySql.=" FIND_IN_SET({$city},look_city) ";
        }
        $suffix = Yii::app()->params['envSuffix'];
        $from = "security".$suffix.".sec_user";
        $rows = Yii::app()->db->createCommand()->select("disp_name")->from($from)
            ->where("username=:username and ({$citySql})", array(':username'=>$this->user_id))->queryRow();
        if ($rows){
            $this->user_name = $rows["disp_name"];
        }else{
            $message = Yii::t('contract','Account number'). Yii::t('contract',' Did not find');
            $this->addError($attribute,$message);
        }
    }

	public function validateEmployee($attribute, $params){
        $city = Yii::app()->user->city();
        $city_allow = Yii::app()->user->city_allow();
        $plusSql = PlusCityForm::getPlusEmployeeList()["plusSql"];
        $rows = Yii::app()->db->createCommand()->select("name,city")->from("hr_employee")
            ->where("id=:id and (city in ($city_allow) or id in ($plusSql)) and (staff_status=0 or (table_type!=1 and staff_status=1)) ", array(':id'=>$this->employee_id))->queryRow();
        if ($rows){
            $this->employee_name = $rows["name"];
            $this->city = $rows["city"];
        }else{
            $message = Yii::t('contract','Employee Name'). Yii::t('contract',' Did not find');
            $this->addError($attribute,$message);
        }
    }
    //獲取用戶表的所有用戶(相同城市)
	public function getUserList($username){
        $city_allow = Yii::app()->user->city_allow();
        $city_allow = explode(",",$city_allow);
        $citySql = "";
        foreach ($city_allow as $city){
            $citySql.=empty($citySql)?"":" or ";
            $citySql.=" FIND_IN_SET({$city},look_city) ";
        }
        $suffix = Yii::app()->params['envSuffix'];
        $from = "security".$suffix.".sec_user";
        $rows = Yii::app()->db->createCommand()->select("username,disp_name")->from($from)->where("(username='$username' or {$citySql}) and status='A'")->queryAll();
        $bindList = Yii::app()->db->createCommand()->select("user_id")->from("hr_binding")->where("id !=:id",array(":id"=>$this->id))->queryAll();
        $bindList = array_column($bindList,"user_id");
        $arr = array(""=>"");
        foreach ($rows as $row){
            if(!in_array($row["username"],$bindList)){
                $arr[$row["username"]] = $row["disp_name"];
            }
        }
        return $arr;
    }
    //獲取用戶表的所有員工(相同城市)
	public function getEmployeeList($employee_id=''){
        $city_allow = Yii::app()->user->city_allow();
        $from = "hr_employee";
        $plusList = PlusCityForm::getPlusEmployeeList();
        $whereSql="";
        if(!empty($employee_id)){
            $whereSql = " or id='{$employee_id}' ";
        }
        $rows = Yii::app()->db->createCommand()->select("id,name,table_type")->from($from)
            ->where("(city in ($city_allow) {$whereSql} or id in (:plusSql)) and (staff_status=0 or (table_type!=1 and staff_status=1))",array(
                ":plusSql"=>$plusList["plusSql"]
            ))->order("table_type asc,id desc")->queryAll();
        $bindList = Yii::app()->db->createCommand()->select("employee_id")
            ->from("hr_binding")->where("id !=:id",array(":id"=>$this->id))->queryAll();
        $arr = array(""=>"");
        $bindList = array_column($bindList,"employee_id");
        foreach ($rows as $row){
            if(!in_array($row["id"],$bindList)||in_array($row["id"],$plusList["plusRows"])){
                $arr[$row["id"]] = $row["name"];
            }
        }
        return $arr;
    }

    public static function getEmployeeIdToUsername($username=""){
	    if(empty($username)){
            $username = Yii::app()->user->id;
        }
        $rows = Yii::app()->db->createCommand()->select("employee_id")->from("hr_binding")
            ->where("user_id=:user_id", array(':user_id'=>$username))->queryRow();
        if($rows){
            return $rows["employee_id"];
        }
	    return 0;
    }

    public static function getEmployeeListToUsername($username=""){
	    if(empty($username)){
            $username = Yii::app()->user->id;
        }
        $rows = Yii::app()->db->createCommand()->select("b.*,c.manager as c_manager")->from("hr_binding a")
            ->leftJoin("hr_employee b","b.id = a.employee_id")
            ->leftJoin("hr_dept c","c.id = b.position")
            ->where("a.user_id=:user_id", array(':user_id'=>$username))->queryRow();
        if($rows){
            if($rows["city"]!=Yii::app()->user->city()){
                $plusList = Yii::app()->db->createCommand()->select("a.department,c.manager as c_manager")->from("hr_plus_city a")
                    ->leftJoin("hr_dept c","c.id = a.position")
                    ->where("a.employee_id=:id and a.city=:city", array(':id'=>$rows["id"],':city'=>Yii::app()->user->city()))->queryRow();
                if($plusList){
                    $rows["c_manager"] = $plusList["c_manager"];
                    $rows["department"] = $plusList["department"];
                }
            }
            return $rows;
        }
	    return array();
    }

    public static function getEmployeeListToEmployeeId($employee_id){
        $rows = Yii::app()->db->createCommand()->select("a.*,c.manager as c_manager")->from("hr_employee a")
            ->leftJoin("hr_dept c","c.id = a.position")
            ->where("a.id=:id", array(':id'=>$employee_id))->queryRow();
        if($rows){
            return $rows;
        }
	    return array();
    }

    //公司刪除時必須沒有員工
	public function validateDelete(){
        $city_allow = Yii::app()->user->city_allow();
        $plusList = PlusCityForm::getPlusEmployeeList();
        $row = Yii::app()->db->createCommand()
            ->select("a.id,a.employee_id,a.user_id,a.lcu,a.luu,a.lcd,a.lud")
            ->from("hr_binding a")
            ->leftJoin("hr_employee b","a.employee_id = b.id")
            ->where("a.id=:id and (b.city in ($city_allow) or b.id in (:plusSql)) ", array(':id'=>$this->id,':plusSql'=>$plusList["plusSql"]))->queryRow();
        if($row){
            $this->employee_id = $row["employee_id"];
            $this->user_id = $row["user_id"];
            $this->lcu = $row['lcu'];
            $this->lcd = $row['lcd'];
            $this->luu = Yii::app()->user->id;
            $this->lud = date("Y-m-d H:i:s");
            return true;
        }else{
            return false;
        }
    }

	public function retrieveData($index)
	{
        $city = Yii::app()->user->city();
        $suffix = Yii::app()->params['envSuffix'];
        $city_allow = Yii::app()->user->city_allow();
        $plusList = PlusCityForm::getPlusEmployeeList();
        $rows = Yii::app()->db->createCommand()
            ->select("a.*,b.name as employ_name,b.city as employee_city,d.disp_name,d.city as user_city")
            ->from("hr_binding a")
            ->leftJoin("hr_employee b","a.employee_id = b.id")
            ->leftJoin("security{$suffix}.sec_user d","d.username = a.user_id")
            ->where("a.id=:id and (b.city in ($city_allow) or b.id in (:plusSql)) ", array(':id'=>$index,':plusSql'=>$plusList["plusSql"]))->queryAll();
        if (count($rows) > 0)
		{
			foreach ($rows as $row)
			{
				$this->id = $row['id'];
				$this->user_name = $row['disp_name'];
				$this->user_id = $row['user_id'];
                $this->employee_id = $row['employee_id'];
                $this->employee_name = $row['employ_name'];
                $this->city = $row['employee_city'];
                $this->employee_city = $row['employee_city'];
                $this->user_city = $row['user_city'];
                $this->lcu = $row['lcu'];
                $this->luu = $row['luu'];
                $this->lcd = $row['lcd'];
                $this->lud = $row['lud'];
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
                $sql = "delete from hr_binding where id = :id and city IN ($city_allow)";
				break;
			case 'new':
				$sql = "insert into hr_binding(
							employee_id, employee_name, user_id, user_name, city, lcu
						) values (
							:employee_id, :employee_name, :user_id, :user_name, :city, :lcu
						)";
				break;
			case 'edit':
				$sql = "update hr_binding set
							employee_id = :employee_id, 
							employee_name = :employee_name, 
							city = :city, 
							user_id = :user_id,
							user_name = :user_name,
							luu = :luu,
							city = :city
						where id = :id
						";
				break;
		}

		$command=$connection->createCommand($sql);
		if (strpos($sql,':id')!==false)
			$command->bindParam(':id',$this->id,PDO::PARAM_INT);
		if (strpos($sql,':employee_id')!==false)
			$command->bindParam(':employee_id',$this->employee_id,PDO::PARAM_STR);
		if (strpos($sql,':employee_name')!==false)
			$command->bindParam(':employee_name',$this->employee_name,PDO::PARAM_STR);
		if (strpos($sql,':user_id')!==false)
			$command->bindParam(':user_id',$this->user_id,PDO::PARAM_STR);
		if (strpos($sql,':user_name')!==false)
			$command->bindParam(':user_name',$this->user_name,PDO::PARAM_STR);

        if (strpos($sql,':city')!==false)
            $command->bindParam(':city',$this->city,PDO::PARAM_STR);
		if (strpos($sql,':luu')!==false)
			$command->bindParam(':luu',$uid,PDO::PARAM_STR);
		if (strpos($sql,':lcu')!==false)
			$command->bindParam(':lcu',$uid,PDO::PARAM_STR);

		$command->execute();

        if ($this->scenario=='new'){
            $this->id = Yii::app()->db->getLastInsertID();
        }

        //同步记录
        $this->sendCurl($this->getScenario());

        if ($this->scenario=='new'){
            $this->setScenario("edit");
        }
        return true;
	}

    //curl需要的字段
    protected function curlData(){
        $suffix = Yii::app()->params['envSuffix'];
        $employeeRow = Yii::app()->db->createCommand()->select("code,name,city")
            ->from("hr_employee")->where("id=:id", array(':id'=>$this->employee_id))->queryRow();
        if($employeeRow){
            $this->employee_name = $employeeRow["name"];
            $this->employee_city = $employeeRow["city"];
            $this->city = $employeeRow["city"];
        }
        $userRow = Yii::app()->db->createCommand()->select("disp_name,city")
            ->from("security{$suffix}.sec_user")->where("username=:username", array(':username'=>$this->user_id))->queryRow();
        if($userRow){
            $this->user_name = $userRow["disp_name"];
            $this->user_city = $userRow["city"];
        }
        $list = array();
        $arr = array(
            "scenario"=>1,"id"=>3,"employee_id"=>3,
            "employee_name"=>1,"user_id"=>1,"user_name"=>1,"city"=>1,
            //"employee_city"=>1,"user_city"=>1,
            "luu"=>5,"lcu"=>5,"lcd"=>4,"lud"=>4
        );
        foreach ($arr as $key=>$type){
            $value=$this->$key;
            switch ($type){
                case 1://原值
                    $value = $value===""?null:$value;
                    break;
                case 2://日期
                    $value = empty($value)?null:General::toMyDate($value);
                    break;
                case 3://数字
                    $value = $value===""?0:floatval($value);
                    break;
                case 4://日期+时间
                    $value = empty($value)?null:General::toMyDateTime($value);
                    break;
                case 5://必填字段，但lbs为空的时候
                    $value = empty($value)?0:$value;
                    break;
            }
            $this->$key=$value;
            $list[$key] = $value;
        }
        return $list;
    }

    public function sendCurl($scenario=""){
        if(in_array($scenario,array("new","edit","delete"))){
            $data = $this->curlData();
            $curlModel = new ApiCurl("binding",$data);
            $curlModel->sendCurlAndAdd();
            return false;
        }
        return true;
    }
}
