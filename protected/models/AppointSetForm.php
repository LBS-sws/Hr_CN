<?php

class AppointSetForm extends CFormModel
{
	/* User Fields */
	public $id;
	public $employee_id;
	public $employee_name;
	public $audit_user_str;
	public $appoint_code;
    public $detail = array(
        array('id'=>0,
            'appoint_id'=>0,
            'audit_user'=>'',
            'z_index'=>0,
            'uflag'=>'N',
        ),
    );

	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
            'employee_id'=>Yii::t('contract','employee name'),
            'appoint_code'=>Yii::t('contract','appoint code'),
            'city_name'=>Yii::t('contract','City'),
            'audit_user_str'=>Yii::t('contract','appoint audit'),
            'audit_user'=>Yii::t('contract','appoint audit'),
            'z_index'=>Yii::t('contract','z_index'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
            array('appoint_code,id,employee_id,employee_name,audit_user_str,detail','safe'),
            array('employee_id','required'),
            array('employee_id','validateName'),
            array('detail','validateDetail'),
		);
	}

    public function validateDetail($attribute, $params){
	    $list = array();
	    $auditUser = self::getAppointAuditUserList();
	    $userStr = array();
	    foreach ($this->detail as $row){
            if($row["uflag"]=="D"){
                $list[]=$row;
            }else{
                if(key_exists($row["audit_user"],$auditUser)){
                    $userStr[] = $auditUser[$row["audit_user"]];
                    $list[]=$row;
                }
            }
        }
        $this->audit_user_str = implode(",",$userStr);
        $this->detail=$list;
    }

    public function validateName($attribute, $params){
        $id = -1;
        if(!empty($this->id)){
            $id = $this->id;
        }
        $row = Yii::app()->db->createCommand()->select("appoint_code")->from("hr_appoint")
            ->where('employee_id=:employee_id and id!=:id',
                array(':employee_id'=>$this->employee_id,':id'=>$id))->queryRow();
        if($row){
            $message = "该员工已被指定，指定编号：".$row["appoint_code"];
            $this->addError($attribute,$message);
        }
    }

    public static function getLabelHtml($model,$str,$option=array()){
        if(in_array($model->z_index,array(1,2,3,4,5))){
            $html = TbHtml::label($model->getAttributeLabel($str),'',$option);
        }else{
            $list = array(
                "pers_lcu"=>Yii::t("contract","audit user")."1",
                "pers_lcd"=>Yii::t("contract","audit user date")."1",
                "user_lcu"=>Yii::t("contract","audit user")."2",
                "user_lcd"=>Yii::t("contract","audit user date")."2",
                "area_lcu"=>Yii::t("contract","audit user")."3",
                "area_lcd"=>Yii::t("contract","audit user date")."3",
                "head_lcu"=>Yii::t("contract","audit user")."4",
                "head_lcd"=>Yii::t("contract","audit user date")."4",
                "you_lcu"=>Yii::t("contract","audit user")."5",
                "you_lcd"=>Yii::t("contract","audit user date")."5",
            );
            $html = "<label class='col-lg-2 control-label'>";
            $html.= $list[$str];
            $html.= "</label>";
        }
        return $html;
    }

    public static function getAppointSet($employee_id){
        $list = false;
        $rows = Yii::app()->db->createCommand()->select("a.audit_user")
            ->from("hr_appoint_info a")
            ->leftJoin("hr_appoint b","b.id = a.appoint_id")
            ->where("b.employee_id=:id", array(':id'=>$employee_id))
            ->order("a.z_index asc,a.id asc")->queryAll();
        if($rows){//如果该员工被指定审核人，则指定审核人
            $list = array();
            foreach ($rows as $i=>$row){
                $key = 10+$i;
                $list[$key]=$row["audit_user"];
            }
            return $list;
        }
        return $list;
    }

    public static function getZIndexForUser(){
        $list = array(
            10=>"pers_lcu",
            11=>"user_lcu",
            12=>"area_lcu",
            13=>"head_lcu",
            14=>"you_lcu",
        );
        return $list;
    }

    public static function getAppointAuditUserList($username=""){
        $suffix = Yii::app()->params['envSuffix'];
        $systemId = Yii::app()->params['systemId'];
        $city_allow = Yii::app()->user->city_allow();
        $list=array();
        $rows = Yii::app()->db->createCommand()->select("b.username,b.disp_name")
            ->from("security{$suffix}.sec_user_access a")
            ->leftJoin("security{$suffix}.sec_user b","a.username=b.username")
            ->where("a.system_id='{$systemId}' and 
            (a.username=:username or a.a_read_write like '%ZG11%' or a.a_read_write like '%ZG12%')
            ",array(
                ":username"=>$username
            ))
            ->queryAll();
        if($rows){
            foreach ($rows as $row){
                $list[$row["username"]] = $row["disp_name"];
            }
        }
        return $list;
    }

    public static function getEmployeeName($employee_id){
        $row = Yii::app()->db->createCommand()->select("code,name")
            ->from("hr_employee")
            ->where("id=:id",array(":id"=>$employee_id))
            ->queryRow();
        return $row?$row["name"]." ({$row["code"]})":$employee_id;
    }

	public function retrieveData($index)
	{
		$city = Yii::app()->user->city();
		$sql = "select * from hr_appoint where id=".$index." ";
		$row = Yii::app()->db->createCommand($sql)->queryRow();
		if ($row!==false) {
			$this->id = $row['id'];
			$this->appoint_code = $row['appoint_code'];
			$this->employee_id = $row['employee_id'];
			$this->employee_name = self::getEmployeeName($row['employee_id']);
			$this->audit_user_str = $row['audit_user_str'];
            $sql = "select * from hr_appoint_info where appoint_id=".$index." ";
            $classRows = Yii::app()->db->createCommand($sql)->queryAll();
            if($classRows){
                $this->detail=array();
                foreach ($classRows as $classRow){
                    $temp = array();
                    $temp["id"] = $classRow["id"];
                    $temp["appoint_id"] = $classRow["appoint_id"];
                    $temp["audit_user"] = $classRow["audit_user"];
                    $temp["z_index"] = $classRow["z_index"];
                    $temp['uflag'] = 'N';
                    $this->detail[] = $temp;
                }
            }
		}
		return true;
	}
	
	public function saveData()
	{
		$connection = Yii::app()->db;
		$transaction=$connection->beginTransaction();
		try {
			$this->save($connection);
            $this->saveDetail($connection);
			$transaction->commit();
		}
		catch(Exception $e) {
			$transaction->rollback();
			var_dump($e);
			throw new CHttpException(404,'Cannot update.');
		}
	}

    protected function saveDetail(&$connection)
    {
        $uid = Yii::app()->user->id;

        foreach ($this->detail as $row) {
            $sql = '';
            switch ($this->scenario) {
                case 'delete':
                    $sql = "delete from hr_appoint_info where appoint_id = :appoint_id";
                    break;
                case 'new':
                    if ($row['uflag']=='Y') {
                        $sql = "insert into hr_appoint_info(
									appoint_id, audit_user, z_index,lcu
								) values (
									:appoint_id,:audit_user,:z_index,:lcu
								)";
                    }
                    break;
                case 'edit':
                    switch ($row['uflag']) {
                        case 'D':
                            $sql = "delete from hr_appoint_info where id = :id";
                            break;
                        case 'Y':
                            $sql = ($row['id']==0)
                                ?
                                "insert into hr_appoint_info(
									    appoint_id, audit_user, z_index,lcu
									) values (
									    :appoint_id,:audit_user,:z_index,:lcu
									)"
                                :
                                "update hr_appoint_info set
										audit_user = :audit_user, 
										z_index = :z_index,
										luu = :luu 
									where id = :id
									";
                            break;
                    }
                    break;
            }

            if ($sql != '') {
//                print_r('<pre>');
//                print_r($sql);exit();
                $command=$connection->createCommand($sql);
                if (strpos($sql,':id')!==false)
                    $command->bindParam(':id',$row['id'],PDO::PARAM_INT);
                if (strpos($sql,':appoint_id')!==false)
                    $command->bindParam(':appoint_id',$this->id,PDO::PARAM_INT);
                if (strpos($sql,':audit_user')!==false)
                    $command->bindParam(':audit_user',$row['audit_user'],PDO::PARAM_STR);
                if (strpos($sql,':z_index')!==false){
                    $row['z_index'] = empty($row['z_index'])?0:$row['z_index'];
                    $command->bindParam(':z_index',$row['z_index'],PDO::PARAM_INT);
                }
                if (strpos($sql,':luu')!==false)
                    $command->bindParam(':luu',$uid,PDO::PARAM_STR);
                if (strpos($sql,':lcu')!==false)
                    $command->bindParam(':lcu',$uid,PDO::PARAM_STR);
                $command->execute();
            }
        }
        return true;
    }

	protected function save(&$connection)
	{
		$sql = '';
		switch ($this->scenario) {
			case 'delete':
				$sql = "delete from hr_appoint where id = :id";
				break;
			case 'new':
				$sql = "insert into hr_appoint(
						employee_id, audit_user_str, lcu) values (
						:employee_id, :audit_user_str, :lcu)";
				break;
			case 'edit':
				$sql = "update hr_appoint set 
					audit_user_str = :audit_user_str,
					luu = :luu
					where id = :id";
				break;
		}

		$uid = Yii::app()->user->id;
        $city = Yii::app()->user->city();

		$command=$connection->createCommand($sql);
		if (strpos($sql,':id')!==false)
			$command->bindParam(':id',$this->id,PDO::PARAM_INT);
		if (strpos($sql,':employee_id')!==false)
			$command->bindParam(':employee_id',$this->employee_id,PDO::PARAM_STR);
		if (strpos($sql,':audit_user_str')!==false)
			$command->bindParam(':audit_user_str',$this->audit_user_str,PDO::PARAM_INT);

		if (strpos($sql,':luu')!==false)
			$command->bindParam(':luu',$uid,PDO::PARAM_STR);
		if (strpos($sql,':lcu')!==false)
			$command->bindParam(':lcu',$uid,PDO::PARAM_STR);
		$command->execute();

		if ($this->scenario=='new'){
            $this->id = Yii::app()->db->getLastInsertID();
            $this->appoint_code="AT".(100000+$this->id);
            Yii::app()->db->createCommand()->update('hr_appoint', array(
                'appoint_code'=>$this->appoint_code,
            ), 'id=:id', array(':id'=>$this->id));
        }
		return true;
	}

	public function isOccupied($index) {
        $employee_id = $this->employee_id;
		$sql = "select a.id from hr_employee_leave a where a.z_index in (10,11,12,13,14) and a.status in (1,2) and a.employee_id='{$employee_id}'";
		$leaveRow = Yii::app()->db->createCommand($sql)->queryRow();
		$sql = "select a.id from hr_employee_work a where a.z_index in (10,11,12,13,14) and a.status in (1,2) and a.employee_id='{$employee_id}'";
		$workRow = Yii::app()->db->createCommand($sql)->queryRow();
		if($leaveRow||$workRow){
		    return true;//不允许删除
        }else{
            return false;
        }
	}
}
