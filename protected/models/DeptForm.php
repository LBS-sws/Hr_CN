<?php

/**
 *
 * UserForm class.
 * UserForm is the data structure for keeping
 * user form data. It is used by the 'user' action of 'SiteController'.
 */
class DeptForm extends CFormModel
{
	/* User Fields */
	public $id;
	public $name;
	public $city;
	public $z_index;
	public $dept_id=0;
	public $type;
	public $dept_class;
	public $manager=0;
	public $technician=0;
	public $review_status=0;
	public $review_type=1;
	public $sales_type=0;
	public $review_leave=0;
	public $manager_type=0;
	public $manager_leave=0;
	public $level_type;

    public $jd_set = array();
    public static $jd_set_list=array(
        array("field_id"=>"jd_dept_code","field_type"=>"text","field_name"=>"jd dept code"),
    );
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
			'id'=>Yii::t('staff','Record ID'),
			'name'=>Yii::t('contract',' Name'),
			'city'=>Yii::t('misc','City'),
			'z_index'=>Yii::t('contract','Level'),
            'dept_id'=>Yii::t('contract','in department'),
            'dept_class'=>Yii::t('contract','Job category'),
            'manager'=>Yii::t('fete','Manager level audit'),
            'technician'=>Yii::t('fete','technician'),
            'review_status'=>Yii::t('contract','dept review'),
            'review_type'=>Yii::t('contract','review type'),
            'review_leave'=>Yii::t('contract','review leave'),
            'sales_type'=>Yii::t('contract','sales type'),
            'manager_type'=>Yii::t('contract','manager type'),
            'manager_leave'=>Yii::t('contract','manager leave'),
            'level_type'=>Yii::t('fete','level type'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			//array('id, position, leave_reason, remarks, email, staff_type, leader','safe'),
            array('jd_set,id, name, level_type, sales_type, z_index, type, dept_class, manager, technician, review_status, review_type, review_leave, manager_type, manager_leave','safe'),
			array('name','required'),
			array('review_leave','required'),
			array('review_type','required'),
			//array('city','validateCity'),
            //array('dept_id','validateDeptId'),
			array('name','validateName'),
		);
	}

	public function validateName($attribute, $params){
	    $this->setCityToDept();
        $rows = Yii::app()->db->createCommand()->select()->from("hr_dept")
            ->where('id!=:id and name=:name and city=:city and type=:type and dept_id=:dept_id ',
                array(':id'=>$this->id,':name'=>$this->name,':city'=>$this->city,':type'=>$this->type,':dept_id'=>$this->dept_id))->queryAll();
        if (count($rows) > 0){
            $message = Yii::t('contract',' Name'). Yii::t('contract',' can not repeat');
            $this->addError($attribute,$message);
        }
    }

	public function validateDeptId($attribute, $params){
	    if($this->type == 1){
	        if(empty($this->dept_id)||!is_numeric($this->dept_id)){
                $message = Yii::t('contract','in department'). Yii::t('contract',' can not be empty');
                $this->addError($attribute,$message);
            }else{
                $rows = Yii::app()->db->createCommand()->select()->from("hr_dept")
                    ->where('id=:id and type=0 ', array(':id'=>$this->dept_id))->queryRow();
                if (!$rows){
                    $message = Yii::t('contract','in department'). Yii::t('contract',' can not be empty');
                    $this->addError($attribute,$message);
                }
            }
        }
    }

	public function validateCity($attribute, $params){
	    if($this->type != 1){
	        if(empty($this->city)){
                $message = Yii::t('misc','City'). Yii::t('contract',' can not be empty');
                $this->addError($attribute,$message);
            }
        }
    }

    public function getTypeName(){
        if ($this->type == 1){
            return Yii::t("contract","Leader");
        }else{
            return Yii::t("contract","Dept");
        }
    }
    public function getTypeAcc(){
        if ($this->type == 1){
            return "ZC02";
        }else{
            return "ZC01";
        }
    }
    public function setCityToDept(){
        if ($this->type == 1&&!empty($this->dept_id)){
            $rows = Yii::app()->db->createCommand()->select()->from("hr_dept")
                ->where('id=:id', array(':id'=>$this->dept_id))->queryRow();
            if($rows){
                $this->city = $rows["city"];
            }else{
                throw new CHttpException(404,'Cannot update.');
            }
        }
    }
    public static function getDeptSqlLikeName($dept_name){
        $sql = "select id from hr_dept
                where type=1 AND name LIKE '%$dept_name%'
			";
        $rows = Yii::app()->db->createCommand($sql)->queryAll();
        $arr = array();
        foreach ($rows as $row){
            array_push($arr,"'".$row["id"]."'");
        }
        if(empty($arr)){
            return "('')";
        }else{
            $arr = implode(",",$arr);
            return "($arr)";
        }
    }
    //獲取職位列表
	public function getDeptAllListNoCity($type=0){
        $city = Yii::app()->user->city();
	    $arr=array(""=>"");
        $rows = Yii::app()->db->createCommand()->select()->from("hr_dept")
            ->where('type=:type', array(':type'=>$type))->order("city,z_index desc")->queryAll();
        if ($rows){
            foreach ($rows as $row){
                $arr[$row["id"]] = $row["name"]." - ".WordForm::getCityNameToCode($row["city"]);
            }
        }
        return $arr;
    }
    //獲取職位列表
	public function getDeptAllList($type=0){
        $city = Yii::app()->user->city();
        $city_allow = Yii::app()->user->city_allow();
	    $arr=array(""=>"");
        $rows = Yii::app()->db->createCommand()->select()->from("hr_dept")
            ->where("type=:type and city in ($city_allow)", array(':type'=>$type))->order("z_index desc")->queryAll();
        if ($rows){
            foreach ($rows as $row){
                $arr[$row["id"]] = $row["name"];
            }
        }
        return $arr;
    }

    public static function getReviewType($str=""){
	    $arr = array(
	        1=>Yii::t("fete","normal"),
	        2=>Yii::t("fete","technician"),
	        3=>Yii::t("staff","Sales"),
	        4=>Yii::t("fete","charge"),
        );
	    if(key_exists($str,$arr)){
            return $arr[$str];
        }
        return $arr;
    }

    public static function getGroupType($str="",$bool=false){
	    $arr = array(
	        0=>Yii::t("fete","none"),//無
	        1=>Yii::t("contract","group business"),//商業組
	        2=>Yii::t("contract","group repast"),//餐飲組
        );
	    if($bool){
            if(key_exists($str,$arr)){
                return $arr[$str];
            }else{
                return $str;
            }
        }
        return $arr;
    }

    public function getReviewLeave($str="",$bool=false){
	    $arr = array(
	        Yii::t("fete","none"),
	        Yii::t("app","Region"),
	        Yii::t("misc","All")
        );
	    if($bool){
	        if(key_exists($str,$arr)){
                return $arr[$str];
            }else{
                return $str;
            }
        }
        return $arr;
    }

    public static function getManagerTypeLeave($str="",$bool=false){
	    $arr = array(
	        Yii::t("fete","none"),
	        Yii::t("contract","Employee"),
	        Yii::t("contract","assistant manager"),
	        Yii::t("contract","sales manager")
        );
	    if($bool){
	        if(key_exists($str,$arr)){
                return $arr[$str];
            }else{
                return $str;
            }
        }
        return $arr;
    }

    public static function getSalesType($str="",$bool=false){
        $arr = array(
            Yii::t("misc","No"),
            Yii::t("misc","Yes")
        );
        if($bool){
            if(key_exists($str,$arr)){
                return $arr[$str];
            }else{
                return $str;
            }
        }
        return $arr;
    }

    //獲取職位列表
	public static function getDeptListToCity($dept_id,$city=''){
	    $sql = "";
	    if(!empty($dept_id)&&is_numeric($dept_id)){
	        $sql = " or id='$dept_id'";
        }
        if(empty($city)){
            $city = Yii::app()->user->city();
        }
	    $arr=array(""=>"");
        $rows = Yii::app()->db->createCommand()->select()->from("hr_dept")
            ->where("(type='0' and z_del=0) and id>0 $sql")->order("z_index desc")->queryAll();
        if ($rows){
            foreach ($rows as $row){
                $arr[$row["id"]] = $row["name"];
            }
        }
        return $arr;
    }

    //是否是銷售部門  0：不是  1：是銷售部
	public static function getSalesTypeToId($dept_id){
        $rows = Yii::app()->db->createCommand()->select()->from("hr_dept")
            ->where("id=:id",array(":id"=>$dept_id))->queryRow();
        if ($rows){
            return $rows["sales_type"];
        }
        return 0;
    }

    //獲取崗位列表
	public static function getPosiList($dept_id){
	    $arr=array(""=>"");
        $rows = Yii::app()->db->createCommand()->select()->from("hr_dept")
            ->where("(type='1' and z_del=0) or id =:id",array(":id"=>$dept_id))->order("z_index desc")->queryAll();
        if ($rows){
            foreach ($rows as $row){
                $arr[$row["id"]] = $row["name"];
            }
        }
        return $arr;
    }

    //獲取職位列表(僅職位)
    public static function getDeptOneAllList(){
        $city = Yii::app()->user->city();
        $city_allow = Yii::app()->user->city_allow();
        $arr=array(""=>array("name"=>"","type"=>"","dept_class"=>""));
        $rows = Yii::app()->db->createCommand()->select()->from("hr_dept")
            ->where("type=:type and city in ($city_allow)", array(':type'=>1))->order("z_index desc")->queryAll();
        if ($rows){
            foreach ($rows as $row){
                $arr[$row["id"]] = array("name"=>$row["name"],"type"=>$row["dept_id"],"dept_class"=>$row["dept_class"]);
            }
        }
        return $arr;
    }
    //獲取職位名字
	public static function getDeptToId($dept_id){
        $rows = Yii::app()->db->createCommand()->select()->from("hr_dept")
            ->where('id=:id', array(':id'=>$dept_id))->queryRow();
        if ($rows){
            return $rows["name"];
        }
        return $dept_id;
    }

    //職位刪除時必須沒有員工
	public function validateDelete(){
	    if($this->type == 1){
            $rows = Yii::app()->db->createCommand()->select()->from("hr_employee")
                ->where('position=:position', array(':position'=>$this->id))->queryAll();
        }else{
            $rows = Yii::app()->db->createCommand()->select()->from("hr_employee")
                ->where('department=:department', array(':department'=>$this->id))->queryAll();
        }
        if ($rows){
            return false;
        }
        return true;
    }

    //level_type
    public static function getConditionList(){
        return array(
            0=>"",
            1=>Yii::t("fete","Technician level"),
            2=>Yii::t("fete","Technical supervisor"),
            3=>Yii::t("fete","Other personnel"),
            4=>Yii::t("fete","KA Technician"),//KA技术服务
        );
    }

    //level_type
    public static function getConditionNameForId($id){
        $list = array(
            0=>"",
            1=>Yii::t("fete","Tec level"),
            2=>Yii::t("fete","Tec supervisor"),
            3=>Yii::t("fete","Other personnel"),
        );
        if(key_exists($id,$list)){
            return $list[$id];
        }
        return $id;
    }

	public function retrieveData($index)
	{
        $city = Yii::app()->user->city();
        $rows = Yii::app()->db->createCommand()->select()->from("hr_dept")
            ->where('id=:id ', array(':id'=>$index))->queryAll();
		if (count($rows) > 0)
		{
			foreach ($rows as $row)
			{
				$this->id = $row['id'];
				$this->name = $row['name'];
				$this->z_index = $row['z_index'];
				$this->city = $row['city'];
                $this->type = $row['type'];
                $this->sales_type = $row['sales_type'];
                $this->dept_id = $row['dept_id'];
                $this->dept_class = $row['dept_class'];
                $this->manager = $row['manager'];
                $this->technician = $row['technician'];
                $this->review_type = $row['review_type'];
                $this->review_status = $row['review_status'];
                $this->review_leave = $row['review_leave'];
                $this->manager_type = $row['manager_type'];
                $this->manager_leave = $row['manager_leave'];
                $this->level_type = $row['level_type'];

                $setRows = Yii::app()->db->createCommand()->select("field_id,field_value")
                    ->from("hr_send_set_jd")->where("table_id=:table_id and set_type='dept'",array(":table_id"=>$index))->queryAll();
                $setList = array();
                foreach ($setRows as $setRow){
                    $setList[$setRow["field_id"]] = $setRow["field_value"];
                }
                $this->jd_set=array();
                foreach (self::$jd_set_list as $item){
                    $fieldValue = key_exists($item["field_id"],$setList)?$setList[$item["field_id"]]:null;
                    $this->jd_set[$item["field_id"]] = $fieldValue;
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
		    //$this->setCityToDept();//自動完成職位的城市歸屬
			$this->saveStaff($connection);
            //保存金蝶要求的字段
            $this->saveJDSetInfo($connection);
			$transaction->commit();
		}
		catch(Exception $e) {
		    var_dump($e);
			$transaction->rollback();
			throw new CHttpException(404,'Cannot update.');
		}
	}

    //保存金蝶要求的字段
    protected function saveJDSetInfo(&$connection) {
        foreach (self::$jd_set_list as $list){
            $field_value = key_exists($list["field_id"],$this->jd_set)?$this->jd_set[$list["field_id"]]:null;
            $rs = Yii::app()->db->createCommand()->select("id,field_id")->from("hr_send_set_jd")
                ->where("set_type ='dept' and table_id=:table_id and field_id=:field_id",array(
                    ':field_id'=>$list["field_id"],':table_id'=>$this->id,
                ))->queryRow();
            if($rs){
                $connection->createCommand()->update('hr_send_set_jd',array(
                    "field_value"=>$field_value,
                ),"id=:id",array(':id'=>$rs["id"]));
            }else{
                $connection->createCommand()->insert('hr_send_set_jd',array(
                    "table_id"=>$this->id,
                    "set_type"=>'dept',
                    "field_id"=>$list["field_id"],
                    "field_value"=>$field_value,
                ));
            }
        }
    }

	protected function saveStaff(&$connection)
	{
		$sql = '';
        $city = Yii::app()->user->city();
        $uid = Yii::app()->user->id;
		switch ($this->scenario) {
			case 'delete':
                $sql = "delete from hr_dept where id = :id";
				break;
			case 'new':
				$sql = "insert into hr_dept(
							z_del,name, type, level_type, sales_type, z_index, manager_type, manager_leave, dept_class, manager, technician, review_status, review_type, review_leave, lcu
						) values (
							0,:name, :type, :level_type, :sales_type, :z_index, :manager_type, :manager_leave, :dept_class, :manager, :technician, :review_status, :review_type, :review_leave, :lcu
						)";
				break;
			case 'edit':
				$sql = "update hr_dept set
							name = :name, 
							type = :type, 
							z_index = :z_index,
							manager_type = :manager_type,
							manager_leave = :manager_leave,
							dept_class = :dept_class,
							manager = :manager,
							technician = :technician,
							review_status = :review_status,
							review_type = :review_type,
							review_leave = :review_leave,
							sales_type = :sales_type,
							level_type = :level_type,
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
		if (strpos($sql,':dept_id')!==false)
			$command->bindParam(':dept_id',$this->dept_id,PDO::PARAM_STR);
		if (strpos($sql,':z_index')!==false)
			$command->bindParam(':z_index',$this->z_index,PDO::PARAM_INT);
		if (strpos($sql,':type')!==false)
			$command->bindParam(':type',$this->type,PDO::PARAM_INT);
		if (strpos($sql,':sales_type')!==false)
			$command->bindParam(':sales_type',$this->sales_type,PDO::PARAM_INT);
		if (strpos($sql,':level_type')!==false)
			$command->bindParam(':level_type',$this->level_type,PDO::PARAM_INT);
		if (strpos($sql,':dept_class')!==false)
			$command->bindParam(':dept_class',$this->dept_class,PDO::PARAM_STR);
		if (strpos($sql,':manager')!==false)
			$command->bindParam(':manager',$this->manager,PDO::PARAM_STR);
		if (strpos($sql,':technician')!==false)
			$command->bindParam(':technician',$this->technician,PDO::PARAM_STR);
		if (strpos($sql,':review_status')!==false)
			$command->bindParam(':review_status',$this->review_status,PDO::PARAM_STR);
		if (strpos($sql,':review_type')!==false)
			$command->bindParam(':review_type',$this->review_type,PDO::PARAM_STR);
		if (strpos($sql,':review_leave')!==false)
			$command->bindParam(':review_leave',$this->review_leave,PDO::PARAM_STR);
		if (strpos($sql,':manager_type')!==false)
			$command->bindParam(':manager_type',$this->manager_type,PDO::PARAM_STR);
		if (strpos($sql,':manager_leave')!==false){
            $this->manager_leave = empty($this->manager_leave)?0:$this->manager_leave;
            $command->bindParam(':manager_leave',$this->manager_leave,PDO::PARAM_STR);
        }

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

	public function copyCity($toCity,$formCity){
        $uid = Yii::app()->user->id;
        $dateTime = date_format(date_create(),"Y/m/d H:i:s");
        $rows = Yii::app()->db->createCommand()->select()->from("hr_dept")
            ->where('type=0 and city=:city', array(':city'=>$formCity))->queryAll();
        echo "复制<b>{$formCity}</b>城市的部门及职位到<b>{$toCity}</b>城市<br/>";
        echo "start:<br/>";
        if($rows){
            $i=0;
            foreach ($rows as $row){
                $j=0;
                $i++;
                echo "{$i}、copy 部门:".$row["name"];
                $oneList = $row;
                unset($oneList["id"]);
                $oneList["city"]=$toCity;
                $oneList["lcu"]=$uid;
                $oneList["luu"]=$uid;
                $oneList["lcd"]=$dateTime;
                $deptRow = Yii::app()->db->createCommand()->select("id")->from("hr_dept")
                    ->where('type=0 and city=:city and name=:name', array(':city'=>$toCity,':name'=>$row["name"]))->queryRow();
                if($deptRow){
                    $deptId = $deptRow["id"];
                    echo " - 已存在，不重复添加。<br/>";
                }else{
                    Yii::app()->db->createCommand()->insert("hr_dept",$oneList);
                    $deptId = Yii::app()->db->getLastInsertID();
                    echo " - 添加成功。<br/>";
                }
                $poRows = Yii::app()->db->createCommand()->select()->from("hr_dept")
                    ->where('type=1 and dept_id=:dept_id', array(':dept_id'=>$row["id"]))->queryAll();
                if($poRows){
                    foreach ($poRows as $poRow){
                        $j++;
                        echo "&nbsp;&nbsp;&nbsp;&nbsp;{$i}.{$j}、copy 职位:".$poRow["name"];
                        $twoList = $poRow;
                        unset($twoList["id"]);
                        $twoList["city"]=$toCity;
                        $twoList["dept_id"]=$deptId;
                        $twoList["lcu"]=$uid;
                        $twoList["luu"]=$uid;
                        $twoList["lcd"]=$dateTime;
                        $deptRow = Yii::app()->db->createCommand()->select("id")->from("hr_dept")
                            ->where('type=1 and dept_id=:dept_id and name=:name',
                                array(':dept_id'=>$deptId,':name'=>$poRow["name"]))->queryRow();
                        if($deptRow){
                            echo " - 已存在，不重复添加。<br/>";
                        }else{
                            Yii::app()->db->createCommand()->insert("hr_dept",$twoList);
                            echo " - 添加成功。<br/>";
                        }
                    }
                }
            }
        }
        echo "end;<br/>";
        return "Success!";
    }
}
