<?php

class PinApplyForm extends CFormModel
{
	public $id;
	public $apply_date;
	public $pin_code;
	public $name;
	public $employee_id;
	public $inventory_id;
	public $class_id;
	public $name_id;
	public $city;
	public $pin_num;
	public $position;
	public $entry_time;
	public $z_index=0;

	public function attributeLabels()
	{
        return array(
            'apply_date'=>Yii::t('contract','Pin Date'),
            'pin_code'=>Yii::t('contract','Pin Code'),
            'employee_id'=>Yii::t('contract','Employee Name'),
            'name_id'=>Yii::t('app','Pin Name'),
            'class_id'=>Yii::t('app','Pin Class'),
            'city'=>Yii::t('contract','City'),
            'pin_num'=>Yii::t('contract','Pin Num'),
            'position'=>Yii::t('contract','Position'),
            'entry_time'=>Yii::t('contract','Entry Time'),
        );
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id,apply_date,pin_num,position,entry_time,pin_code,name_id,class_id,city,employee_id','safe'),
            array('apply_date,employee_id,name_id,pin_num,class_id','required'),
            array('employee_id,name_id,pin_num', 'numerical', 'min'=>1, 'integerOnly'=>true),
            array('name_id','validateId'),
		);
	}

    public function validateId($attribute, $params){
	    if(!empty($this->employee_id)&&!empty($this->name_id)){
            $row = Yii::app()->db->createCommand()->select("id,name,city")->from("hr_employee")
                ->where("id=:id",array(":id"=>$this->employee_id))->queryRow();
            if($row){
                $pinRow = Yii::app()->db->createCommand()->select("city,pin_num")->from("hr_pin")
                    ->where("id=:id",array(":id"=>$this->id))->queryRow();
                $this->city = $pinRow?$pinRow["city"]:$row["city"];
                $row = Yii::app()->db->createCommand()->select("id,residue_num")->from("hr_pin_inventory")
                    ->where("pin_name_id=:id and city='{$this->city}'",array(":id"=>$this->name_id))->queryRow();
                if($row){
                    $this->inventory_id = $row["id"];
                }else{
                    $message = "襟章没有添加库存，请与管理员联系";
                    $this->addError($attribute,$message);
                }
            }else{
                $message = "员工不存在，请刷新重试";
                $this->addError($attribute,$message);
            }
        }
    }

	public function retrieveData($index) {
		$row = Yii::app()->db->createCommand()
            ->select("a.*,b.entry_time,p.name as dept_name,g.class_id,g.id as name_id")
            ->from("hr_pin a")
            ->leftJoin("hr_employee b","a.employee_id=b.id")
            ->leftJoin("hr_dept p","b.position=p.id")
            ->leftJoin("hr_pin_inventory f","a.inventory_id=f.id")
            ->leftJoin("hr_pin_name g","f.pin_name_id = g.id")
            ->where("a.id=:id",array(":id"=>$index))->queryRow();
		if ($row) {
            $this->id = $row['id'];
            $this->position = $row['dept_name'];
            $this->entry_time = $row['entry_time'];
            $this->pin_code = $row['pin_code'];
            $this->apply_date = CGeneral::toDate($row['apply_date']);
            $this->employee_id = $row['employee_id'];
            $this->inventory_id = $row['inventory_id'];
            $this->pin_num = $row['pin_num'];
            $this->class_id = $row['class_id'];
            $this->name_id = $row['name_id'];
            $this->city = $row['city'];
            return true;
		}
        return false;
	}

    //获取所有员工列表
    public static function getEmployeeList($id=0){
        $city_allow = Yii::app()->user->city_allow();
        $arr = array();
        $rows = Yii::app()->db->createCommand()->select("a.id,a.code,a.name,a.entry_time,b.name as dept_name")->from("hr_employee a")
            ->leftJoin("hr_dept b","a.position=b.id")
            ->where("a.id='{$id}' or (a.staff_status=0 and b.dept_class='Technician' and a.city in({$city_allow}))")
            ->order("a.name asc")->queryAll();
        if($rows){
            foreach ($rows as $row){
                $arr[]=array(
                    "id"=>$row["id"],
                    "entry"=>$row["entry_time"],
                    "dept"=>$row["dept_name"],
                    "name"=>$row["name"]."({$row['code']})",
                );
            }
        }
        return $arr;
    }

    //获取所有名称列表
    public static function getPinNameList(){
        $rows = Yii::app()->db->createCommand()->select("a.id,a.name,a.class_id")
            ->from("hr_pin_name a")
            ->leftJoin("hr_pin_class b",'a.class_id=b.id')
            ->order("b.z_index asc,a.z_index asc")->queryAll();
        return $rows;
    }

	//获取select控件
    public static function getSelectForData($model,$str,$rows,$htmlArr=array()){
        $className = get_class($model);
        $selectClass = key_exists("class",$htmlArr)?$htmlArr["class"]:"";
        $readonly = key_exists("readonly",$htmlArr)&&$htmlArr["readonly"]?"readonly":"";
        $html = "<select id='{$str}' name='{$className}[{$str}]' {$readonly} class='form-control {$selectClass}'>";
        $html.="<option value='' data-class=''></option>";
        if($rows){
            foreach ($rows as $row){
                $dataOption = "";
                foreach ($row as $option=>$value){
                    if(!in_array($option,array("id","name"))){
                        $dataOption.=" data-{$option}='{$value}'";
                    }
                }
                $select = $row['id']==$model->$str?"selected":"";
                $html.="<option value='{$row['id']}'{$dataOption} {$select}>{$row['name']}</option>";
            }
        }
        $html.="</select>";
        return $html;
    }
	//获取select控件
    public static function getSelectForDataEx($className,$str,$rows,$htmlArr=array()){
        $selectClass = key_exists("class",$htmlArr)?$htmlArr["class"]:"";
        $readonly = key_exists("readonly",$htmlArr)&&$htmlArr["readonly"]?"readonly":"";
        $html = "<select id='{$className}' name='{$className}' {$readonly} class='form-control {$selectClass}'>";
        $html.="<option value='' data-class=''></option>";
        if($rows){
            foreach ($rows as $row){
                $dataOption = "";
                foreach ($row as $option=>$value){
                    if(!in_array($option,array("id","name"))){
                        $dataOption.=" data-{$option}='{$value}'";
                    }
                }
                $select = $row['id']==$str?"selected":"";
                $html.="<option value='{$row['id']}'{$dataOption} {$select}>{$row['name']}</option>";
            }
        }
        $html.="</select>";
        return $html;
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
			$this->saveHistory($connection);
			$this->saveGoods($connection);
			$transaction->commit();
		}
		catch(Exception $e) {
			$transaction->rollback();
			throw new CHttpException(404,'Cannot update. ('.$e->getMessage().')');
		}
	}

	protected function saveHistory(&$connection) {
        $oldModel = new  PinApplyForm();
        $oldModel->retrieveData($this->id);
        $num = empty($oldModel->pin_num)?0:$oldModel->pin_num;
        switch ($this->scenario) {
            case 'delete':
                $numSql = "+{$num}";
                $sql="update hr_pin_inventory set residue_num=residue_num{$numSql} WHERE id={$this->inventory_id}";
                //var_dump($sql);die();
                $connection->createCommand($sql)->execute();
                $connection->createCommand()->insert("hr_pin_inventory_history",array(
                    "apply_date"=>date("Y-m-d H:i:s"),
                    "inventory_id"=>$this->inventory_id,
                    "pin_name_id"=>$this->name_id,
                    "pin_code"=>$this->pin_code,
                    "old_sum"=>$num,
                    "now_sum"=>$num,
                    "status_type"=>4,
                    "apply_name"=>Yii::app()->user->user_display_name(),
                ));
                break;
            case 'new':
            case 'edit':
                $num = $num - $this->pin_num;
                if(!empty($num)){//数量有变化
                    $numSql = $num>0?"+{$num}":"{$num}";
                    $sql="update hr_pin_inventory set residue_num=residue_num{$numSql} WHERE id={$this->inventory_id}";
                    $connection->createCommand($sql)->execute();
                    $connection->createCommand()->insert("hr_pin_inventory_history",array(
                        "apply_date"=>date("Y-m-d H:i:s"),
                        "inventory_id"=>$this->inventory_id,
                        "pin_name_id"=>$this->name_id,
                        "pin_code"=>$this->pin_code,
                        "old_sum"=>$this->pin_num+$num,
                        "now_sum"=>$this->pin_num,
                        "status_type"=>$this->scenario=="new"?2:3,
                        "apply_name"=>Yii::app()->user->user_display_name(),
                    ));
                }
                break;
        }
    }

	protected function saveGoods(&$connection) {
		$sql = '';
        switch ($this->scenario) {
            case 'delete':
                $sql = "delete from hr_pin where id = :id";
                break;
            case 'new':
                $sql = "insert into hr_pin(
							apply_date,employee_id,inventory_id,pin_num,city, lcu
						) values (
							:apply_date,:employee_id,:inventory_id,:pin_num,:city, :lcu
						)";
                break;
            case 'edit':
                $sql = "update hr_pin set
							apply_date = :apply_date, 
							employee_id = :employee_id, 
							inventory_id = :inventory_id, 
							pin_num = :pin_num, 
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

        if (strpos($sql,':apply_date')!==false)
            $command->bindParam(':apply_date',$this->apply_date,PDO::PARAM_STR);
        if (strpos($sql,':employee_id')!==false)
            $command->bindParam(':employee_id',$this->employee_id,PDO::PARAM_INT);
        if (strpos($sql,':inventory_id')!==false)
            $command->bindParam(':inventory_id',$this->inventory_id,PDO::PARAM_INT);
        if (strpos($sql,':pin_num')!==false)
            $command->bindParam(':pin_num',$this->pin_num,PDO::PARAM_INT);

        if (strpos($sql,':city')!==false)
            $command->bindParam(':city',$this->city,PDO::PARAM_STR);
        if (strpos($sql,':luu')!==false)
            $command->bindParam(':luu',$uid,PDO::PARAM_STR);
        if (strpos($sql,':lcu')!==false)
            $command->bindParam(':lcu',$uid,PDO::PARAM_STR);
        $command->execute();

        if ($this->scenario=='new'){
            $this->id = Yii::app()->db->getLastInsertID();
            $this->pin_code = $this->city.(100000+$this->id);
            Yii::app()->db->createCommand()->update("hr_pin",array(
                "pin_code"=>$this->pin_code
            ),"id={$this->id}");
            $this->scenario = "edit";
        }
		return true;
	}
}
