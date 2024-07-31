<?php

class AuditConfigForm extends CFormModel
{
	public $id;
	public $city;
	public $audit_index;

	public function attributeLabels()
	{
        return array(
            'city'=>Yii::t('contract','City'),
            'audit_index'=>Yii::t('fete','Audit index'),
        );
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id, city,audit_index','safe'),
            array('city','required'),
            array('audit_index','required'),
            array('city','validateCity'),
		);
	}


    public function validateCity($attribute, $params){
        $id = -1;
        if(!empty($this->id)){
            $id = $this->id;
        }
        $rows = Yii::app()->db->createCommand()->select("id")->from("hr_audit_con")
            ->where('city=:city and id!=:id',
                array(':id'=>$id,':city'=>$this->city))->queryAll();
        if(count($rows)>0){
            $message = Yii::t('contract','City'). Yii::t('contract',' can not repeat');
            $this->addError($attribute,$message);
        }
    }

    public static function getManager($staff_id){
        $staffList = Yii::app()->db->createCommand()->select("a.department,a.city,c.manager as c_manager,a.group_type")->from("hr_employee a")
            ->leftJoin("hr_dept c","c.id = a.position")
            ->where("a.id=:id", array(':id'=>$staff_id))->queryRow();
        if($staffList){
            if($staffList["city"]!=Yii::app()->user->city()){
                $plusList = Yii::app()->db->createCommand()->select("a.department,c.manager as c_manager")->from("hr_plus_city a")
                    ->leftJoin("hr_dept c","c.id = a.position")
                    ->where("a.employee_id=:id and a.city=:city", array(':id'=>$staff_id,':city'=>Yii::app()->user->city()))->queryRow();
                if($plusList){
                    $staffList["c_manager"] = $plusList["c_manager"];
                    $staffList["department"] = $plusList["department"];
                }
            }
            return array(
                "manager"=>$staffList["c_manager"],
                "department"=>$staffList["department"],
                "group_type"=>$staffList["group_type"],
            );
        }else{
            return array(
                "manager"=>0,
                "department"=>0,
                "group_type"=>0,
            );
        }
    }

	public static function getCityAuditToCodeTest($employee_id,$auditType="") { //調試專用
        $staffList = Yii::app()->db->createCommand()->select("a.id,a.city,a.department,c.manager as c_manager")->from("hr_employee a")
            ->leftJoin("hr_dept c","c.id = a.position")
            ->where("a.id=:id", array(':id'=>$employee_id))->queryRow();
        var_dump($staffList);
        echo "<br/>";
        if($staffList){
            $personnelBool = Yii::app()->db->createCommand()->select("id")->from("hr_setting")
                ->where("set_name='personnelType' and set_city=:set_city and set_value=2",
                    array(':set_city'=>$staffList['city']))->queryRow();
            if($staffList["city"]!=Yii::app()->user->city()){
                $plusList = Yii::app()->db->createCommand()->select("a.department,c.manager as c_manager")->from("hr_plus_city a")
                    ->leftJoin("hr_dept c","c.id = a.position")
                    ->where("a.employee_id=:id and a.city=:city", array(':id'=>$employee_id,':city'=>Yii::app()->user->city()))->queryRow();
                if($plusList){
                    $staffList["c_manager"] = $plusList["c_manager"];
                    $staffList["department"] = $plusList["department"];
                }
            }
            var_dump($staffList);
            echo "<br/>";
            $manager = empty($staffList["c_manager"])?0:intval($staffList["c_manager"]);
            var_dump("manager1:{$manager}");
            echo "<br/>";
            //var_dump($manager);die();
            if(in_array($manager,array(1,2,3,4))){
                $manager++;
                $manager = $manager>=4?4:$manager;
            }elseif($personnelBool){ //後續因為新加波添加人事審核（部門審核之前）
                $type = empty($auditType)?"ZP01":"ZP02";
                $systemId = Yii::app()->params['systemId'];
                $suffix = Yii::app()->params['envSuffix'];
                $personnelList = Yii::app()->db->createCommand()->select("a.employee_id")->from("hr_binding a")
                    ->leftJoin("hr_employee d","d.id = a.employee_id")
                    ->leftJoin("security$suffix.sec_user b","b.username = a.user_id")
                    ->leftJoin("security$suffix.sec_user_access c","c.username = a.user_id")
                    ->where("b.status='A' and b.city=d.city and c.system_id='$systemId' and c.a_read_write like '%$type%' and d.city=:city",
                        array(":city"=>$staffList['city'])
                    )->queryAll();
                if($personnelList){ //存在人事審核的權限
                    $manager = 5;
                    foreach ($personnelList as $perList){
                        if($perList["employee_id"] == $employee_id){//當申請人有人事系統的審核權限時
                            $manager = 2;
                            break;
                        }
                    }
                }else{
                    $manager = 1;
                }
            }else{
                $manager = 1;
            }
            var_dump("manager2:{$manager}");
            echo "<br/>";

            //後續添加
            $assList = AuditConfigForm::getAccessAndCity($staffList["city"],$staffList["department"],$auditType);
            for($i = $manager;$i<=count($assList);$i++){
                if($assList[$i]){
                    $manager = $i;
                    break;
                }
            }

            var_dump("assList:");
            echo "<br/>";
            var_dump($assList);
            echo "<br/>";

            var_dump("manager3:{$manager}");
            echo "<br/>";
            return $manager;
        }

        return 1;
	}

	public static function getCityAuditToCode($employee_id,$auditType="") {
        $staffList = Yii::app()->db->createCommand()->select("a.*,c.manager as c_manager")->from("hr_employee a")
            ->leftJoin("hr_dept c","c.id = a.position")
            ->where("a.id=:id", array(':id'=>$employee_id))->queryRow();
        if($staffList){
            $personnelBool = Yii::app()->db->createCommand()->select("id")->from("hr_setting")
                ->where("set_name='personnelType' and set_city=:set_city and set_value=2",
                    array(':set_city'=>$staffList['city']))->queryRow();
            if($staffList["city"]!=Yii::app()->user->city()){
                $plusList = Yii::app()->db->createCommand()->select("a.department,c.manager as c_manager")->from("hr_plus_city a")
                    ->leftJoin("hr_dept c","c.id = a.position")
                    ->where("a.employee_id=:id and a.city=:city", array(':id'=>$employee_id,':city'=>Yii::app()->user->city()))->queryRow();
                if($plusList){
                    $staffList["c_manager"] = $plusList["c_manager"];
                    $staffList["department"] = $plusList["department"];
                }
            }
            $manager = empty($staffList["c_manager"])?0:intval($staffList["c_manager"]);
            //var_dump($manager);die();
            if(in_array($manager,array(1,2,3,4))){
                $manager++;
                $manager = $manager>=4?4:$manager;
            }elseif($personnelBool){ //後續因為新加波添加人事審核（部門審核之前）
                $type = empty($auditType)?"ZP01":"ZP02";
                $systemId = Yii::app()->params['systemId'];
                $suffix = Yii::app()->params['envSuffix'];
                $personnelList = Yii::app()->db->createCommand()->select("a.employee_id")->from("hr_binding a")
                    ->leftJoin("hr_employee d","d.id = a.employee_id")
                    ->leftJoin("security$suffix.sec_user b","b.username = a.user_id")
                    ->leftJoin("security$suffix.sec_user_access c","c.username = a.user_id")
                    ->where("b.status='A' and b.city=d.city and c.system_id='$systemId' and c.a_read_write like '%$type%' and d.city=:city",
                        array(":city"=>$staffList['city'])
                    )->queryAll();
                if($personnelList){ //存在人事審核的權限
                    $manager = 5;
                    foreach ($personnelList as $perList){
                        if($perList["employee_id"] == $employee_id){//當申請人有人事系統的審核權限時
                            $manager = 2;
                            break;
                        }
                    }
                }else{
                    $manager = 1;
                }
            }else{
                $manager = 1;
            }

            //後續添加
            $assList = AuditConfigForm::getAccessAndCity($staffList["city"],$staffList["department"],$auditType);
            for($i = $manager;$i<=count($assList);$i++){
                if($assList[$i]){
                    $manager = $i;
                    break;
                }
            }

            return $manager;
        }

        return 1;
	}

    //查找管轄某城市的所有城市（根據小城市查找大城市）
    public static function getAllCityToMinCity($minCity){
        if(empty($minCity)){
            return array();
        }
        $cityList = array($minCity);
        $suffix = Yii::app()->params['envSuffix'];
        $command = Yii::app()->db->createCommand();
        $rows = $command->select("region")->from("security$suffix.sec_city")
            ->where("code=:code",array(":code"=>$minCity))->queryAll();
        if($rows){
            foreach ($rows as $row){
                $foreachList = self::getAllCityToMinCity($row["region"]);
                $cityList = array_merge($foreachList,$cityList);
            }
        }

        return $cityList;
    }

    //判斷審核人是否空缺
    public static function getAccessAndCity($city,$department,$auditType=""){
        $systemId = Yii::app()->params['systemId'];
        $suffix = Yii::app()->params['envSuffix'];
        $command = Yii::app()->db->createCommand();
        if(empty($auditType)){ //加班
            $workOne = $command->select("a.user_id")->from("hr_binding a")
                ->leftJoin("hr_employee d","d.id = a.employee_id")
                ->leftJoin("security$suffix.sec_user b","b.username = a.user_id")
                ->leftJoin("security$suffix.sec_user_access c","c.username = a.user_id")
                ->where("b.status='A' and d.city='{$city}' and c.system_id='$systemId' and c.a_read_write like '%ZA08%' and d.department='$department'")
                ->queryAll();
            if($workOne){
                $workOne = array_column($workOne, 'user_id');
            }
            $command->reset();
            $workTwo = $command->select("a.username")->from("security$suffix.sec_user a")
                ->leftJoin("security$suffix.sec_user_access b","b.username = a.username")
                ->where("a.status='A' and b.system_id='$systemId' and b.a_read_write like '%ZE05%' and a.city=:city",
                    array(':city'=>$city))->queryAll();
            if($workTwo){
                $workTwo = array_column($workTwo, 'username');
            }
            $command->reset();
            $workThree = $command->select("a.username")->from("security$suffix.sec_user a")
                ->leftJoin("security$suffix.sec_user_access b","b.username = a.username")
                ->where("a.status='A' and b.system_id='$systemId' and b.a_read_write like '%ZG04%' and FIND_IN_SET('{$city}',a.look_city)")
                ->queryAll();
            if($workThree){
                $workThree = array_column($workThree, 'username');
            }
            $boolOne=true;
            $boolTwo=true;
            $boolThree=true;
            if((!$workOne)||($workOne==$workTwo)){
                $boolOne = false;
            }
            if(!$workTwo||($workTwo==$workThree)){
                $boolTwo = false;
            }
            if(!$workThree){
                $boolThree = false;
            }
        }else{ //請假
            $leaveOne = $command->select("a.user_id")->from("hr_binding a")
                ->leftJoin("hr_employee d","d.id = a.employee_id")
                ->leftJoin("security$suffix.sec_user b","b.username = a.user_id")
                ->leftJoin("security$suffix.sec_user_access c","c.username = a.user_id")
                ->where("b.status='A' and d.city='{$city}' and c.system_id='$systemId' and c.a_read_write like '%ZA09%' and d.department='$department'")->queryAll();
            if($leaveOne){
                $leaveOne = array_column($leaveOne, 'user_id');
            }
            $command->reset();
            $leaveTwo = $command->select("a.username")->from("security$suffix.sec_user a")
                ->leftJoin("security$suffix.sec_user_access b","b.username = a.username")
                ->where("a.status='A' and b.system_id='$systemId' and b.a_read_write like '%ZE06%' and a.city=:city",
                    array(':city'=>$city))->queryAll();
            if($leaveTwo){
                $leaveTwo = array_column($leaveTwo, 'username');
            }
            $command->reset();
            $leaveThree = $command->select("a.username")->from("security$suffix.sec_user a")
                ->leftJoin("security$suffix.sec_user_access b","b.username = a.username")
                ->where("a.status='A' and b.system_id='$systemId' and b.a_read_write like '%ZG05%' and FIND_IN_SET('{$city}',a.look_city)")
                ->queryAll();
            if($leaveThree){
                $leaveThree = array_column($leaveThree, 'username');
            }
            $boolOne=true;
            $boolTwo=true;
            $boolThree=true;
            if((!$leaveOne)||($leaveOne==$leaveTwo)){
                $boolOne = false;
            }
            if(!$leaveTwo||($leaveTwo==$leaveThree)){
                $boolTwo = false;
            }
            if(!$leaveThree){
                $boolThree = false;
            }
        }

        return array(
            1=>$boolOne,
            2=>$boolTwo,
            3=>$boolThree,
            4=>true,
        );
    }

	public function retrieveData($index) {
        $city_allow = Yii::app()->user->city_allow();
		$rows = Yii::app()->db->createCommand()->select("*")
            ->from("hr_audit_con")->where("id=:id",array(":id"=>$index))->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
                $this->id = $row['id'];
                $this->city = $row['city'];
                $this->audit_index = $row['audit_index'];
                break;
			}
		}
		return true;
	}

    //刪除驗證
    public function deleteValidate(){
        return true;
    }

    //獲取城市列表
    public function getCityList(){
        $suffix = Yii::app()->params['envSuffix'];
        $idSql ="";
        if(!empty($this->id)){
            $idSql = " and a.id !=".$this->id;
        }
        //select * from  B where (select count(1) as num from A where A.ID = B.ID) = 0
        $sql = "select * from security$suffix.sec_city b where (select count(1) as num from hr$suffix.hr_audit_con a where a.city = b.code $idSql) = 0";
        $records = Yii::app()->db->createCommand($sql)->queryAll();
        $arr = array(""=>"");
        if($records){
            foreach ($records as $record){
                $arr[$record["code"]] = $record["name"];
            }
        }
        return $arr;
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
                $sql = "delete from hr_audit_con where id = :id";
                break;
            case 'new':
                $sql = "insert into hr_audit_con(
							city,audit_index, lcu
						) values (
							:city,:audit_index, :lcu
						)";
                break;
            case 'edit':
                $sql = "update hr_audit_con set
							city = :city, 
							audit_index = :audit_index, 
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
            $command->bindParam(':city',$this->city,PDO::PARAM_STR);
        if (strpos($sql,':audit_index')!==false)
            $command->bindParam(':audit_index',$this->audit_index,PDO::PARAM_INT);

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
