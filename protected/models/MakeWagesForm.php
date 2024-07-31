<?php

/**
 * UserForm class.
 * UserForm is the data structure for keeping
 * user form data. It is used by the 'user' action of 'SiteController'.
 */
class MakeWagesForm extends CFormModel
{
	/* User Fields */
	public $id;
	public $employee_id;
    public $wages_date;
    public $apply_time;
    public $wages_arr;
    public $audit = 0;
    public $wages_status; //0:草稿  1：發送 2：拒絕 3：完成
    public $just_remark;
    public $sum;
    public $wages_body;
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
			'id'=>Yii::t('staff','Record ID'),
            'employee_id'=>Yii::t('contract','Select the employee'),
            'wages_date'=>Yii::t('contract','Wages Time'),
            'apply_time'=>Yii::t('contract','Apply Time'),
            'sum'=>Yii::t('contract','Wages Sum'),
            'city'=>Yii::t('contract','City'),
            'wages_arr'=>Yii::t('contract','Wages Detail'),
            'wages_body'=>Yii::t('contract','wage list(Convenient)'),
            'just_remark'=>Yii::t('contract','Rejected Remark'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			//array('id, position, leave_reason, remarks, email, staff_type, leader','safe'),
            array('id, employee_id, wages_status, sum, wages_date, wages_arr, just_remark','safe'),
			array('wages_arr','required'),
			array('sum','required'),
			array('employee_id','required'),
            array('employee_id','validateEmployee'),
			array('wages_arr','required'),
			array('wages_arr','validateList'),
/*            array('license_time, organization_time','date','allowEmpty'=>true,
                'format'=>array('yyyy/MM/dd','yyyy-MM-dd','yyyy/M/d'),
            ),*/
		);
	}
    public function validateEmployee($attribute, $params){
        $city_allow = Yii::app()->user->city_allow();
        $rows = Yii::app()->db->createCommand()->select()->from("hr_employee")
            ->where("id=:id and city in ($city_allow) ", array(':id'=>$this->employee_id))->queryAll();
        if (!$rows){
            $message = Yii::t('contract','Employee Name'). Yii::t('contract',' not exist');
            $this->addError($attribute,$message);
        }
    }

    public function validateList($attribute, $params){
        if(!empty($this->wages_arr)){
            if(is_array($this->wages_arr)){
                foreach ($this->wages_arr as $value){
                    if($value[0] === ""||$value[1] === ""){
                        $message = Yii::t('contract','Wages Detail'). Yii::t('contract',' can not be empty');
                        $this->addError($attribute,$message);
                        return false;
                    }
                    if(!is_numeric($value[1])){
                        $message = Yii::t('contract','Wage Number'). Yii::t('contract',' Must be Numbers');
                        $this->addError($attribute,$message);
                        return false;
                    }
                }
            }else{
                $message = Yii::t('contract','Wages Detail'). Yii::t('contract',' Error');
                $this->addError($attribute,$message);
            }
        }
    }

    public function getEmployeeList(){
        $date = date("Y-m");
        $city_allow = Yii::app()->user->city_allow();
        $arr = array(""=>"");
        $sql = "";
        if(empty($this->id)){
            $this->id = 0;
        }
        $rows = Yii::app()->db->createCommand()->select("employee_id")->from("hr_employee_wages")
            ->where("wages_date>='$date-01' and wages_date<='$date-31' and id!=:id",array(":id"=>$this->id))->queryAll();
        if($rows){
            $rows = array_column($rows,"employee_id");
            $sql="and id not in(".implode(",",$rows).")";
        }
        $rows = Yii::app()->db->createCommand()->select()->from("hr_employee")
            ->where("city in ($city_allow) and staff_status = 0 $sql")->queryAll();
        if ($rows){
            foreach ($rows as $row){
                $arr[$row["id"]] = $row["code"]." - ".$row["name"];
            }
        }
        return $arr;
    }

    //完成操作
    public function finishWages($id){
        $city_allow = Yii::app()->user->city_allow();
        $rs = Yii::app()->db->createCommand()->select()->from("hr_employee_wages")
            ->where("id=:id and city in($city_allow)", array(':id'=>$id))->queryRow();
        if($rs){
            Yii::app()->db->createCommand()->update('hr_employee_wages', array(
                'wages_status'=>0,
            ), 'id=:id', array(':id'=>$id));
            return $rs["employee_id"];
        }else{
            return "";
        }
    }

    public function getEmplyeeHtmlToEmployeeId($staff_id){
        $city_allow = Yii::app()->user->city_allow();
        $row = Yii::app()->db->createCommand()->select()->from("hr_employee")
            ->where("id=:id and city in($city_allow)", array(':id'=>$staff_id))->queryRow();
        if($row){
            $html = '<div class="col-sm-10 col-sm-offset-1"><table class="table table-bordered table-striped">';
            $html .= '<thead><tr>';
            $html .= '<td>'.Yii::t("contract","Employee Code").'</td>';
            $html .= '<td>'.Yii::t("contract","Employee Name").'</td>';
            $html .= '<td>'.Yii::t("contract","Department").'</td>';
            $html .= '<td>'.Yii::t("contract","City").'</td>';
            $html .= '</tr></thead><tbody><tr>';
            $html .= '<td>'.$row["code"].'</td>';
            $html .= '<td>'.$row["name"].'</td>';
            $html .= '<td>'.DeptForm::getDeptToId($row["department"]).'</td>';
            $html .= '<td>'.CGeneral::getCityName($row["city"]).'</td>';
            $html .= '</tr></tbody></table></div>';
            return CJSON::encode(array('status'=>1,'html'=>$html));
        }else{
            return CJSON::encode(array('status'=>0));
        }
    }

    public function getWagesHtmlToConId($con_id){
        $addHtml = $this->getAddHtmlTr();
        $rows = Yii::app()->db->createCommand()->select()->from("hr_wages_con")
            ->where("wages_id=:wages_id", array(':wages_id'=>$con_id))->order("z_index desc")->queryAll();
        if($rows){
            $html = "";
            $key = 0;
            foreach ($rows as $row){
                $key++;
                $html .= strtr($addHtml, array(":key" =>$key,":wage_name" =>$row["type_name"],":wage_num" =>''));
            }
            return CJSON::encode(array('status'=>1,'html'=>$html));
        }else{
            return CJSON::encode(array('status'=>0));
        }
    }

    public function getAddHtmlTr(){
        $addHtml = '<tr data-key=":key">';
        $addHtml .= '<td>'.TbHtml::textField("MakeWagesForm[wages_arr][:key][0]",":wage_name",array("readonly"=>$this->getOnly())).'</td>';
        $addHtml .= '<td>'.TbHtml::numberField("MakeWagesForm[wages_arr][:key][1]",":wage_num",array("min"=>0,"readonly"=>$this->getOnly())).'</td>';
        if(!$this->getOnly()){
            $addHtml .= '<td>';
            $addHtml.=TbHtml::button(Yii::t('dialog','Remove'), array(
                    "class"=>"delWage",
                    'color'=>TbHtml::BUTTON_COLOR_WARNING)
            );
            $addHtml .= '</td>';
        }
        $addHtml .='</tr>';
        return $addHtml;
    }

	public function retrieveData($index)
	{
        $city = Yii::app()->user->city();
        $city_allow = Yii::app()->user->city_allow();
        $row = Yii::app()->db->createCommand()->select("*")->from("hr_employee_wages")
            ->where("id =:id",array(":id"=>$index))->queryRow();
		if ($row){
            $this->id = $row['id'];
            $this->employee_id = $row['employee_id'];
            $this->wages_arr = unserialize($row['wages_arr']);
            $this->wages_date = date("Y-m",strtotime($row['wages_date']));
            $this->wages_status = $row['wages_status'];
            $this->just_remark = $row['just_remark'];
            $this->sum = $row['sum'];
            $this->apply_time = $row['apply_time'];
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
			throw new CHttpException(404,'Cannot update.'.$e->getMessage());
		}
	}

	protected function saveStaff(&$connection)
	{
		$sql = '';
        $uid = Yii::app()->user->id;
        $city = Yii::app()->user->city();
        $city_allow = Yii::app()->user->city_allow();
		switch ($this->scenario) {
			case 'delete':
                $sql = "delete from hr_company where id = :id and city in ($city_allow)";
				break;
			case 'new':
				$sql = "insert into hr_employee_wages(
							city, employee_id, wages_date, wages_arr, apply_time, wages_status, sum, lcu
						) values (
							:city, :employee_id, :wages_date, :wages_arr, :apply_time, :wages_status, :sum, :lcu
						)";
				break;
			case 'edit':
				$sql = "update hr_employee_wages set
							employee_id = :employee_id, 
							wages_arr = :wages_arr, 
							apply_time = :apply_time, 
							wages_status = :wages_status, 
							sum = :sum, 
							just_remark = '',
							luu = :luu 
						where id = :id
						";
				break;
		}
		if(!empty($this->wages_arr)){
            $this->wages_arr = serialize($this->wages_arr);
        }

		$command=$connection->createCommand($sql);
		if (strpos($sql,':id')!==false)
			$command->bindParam(':id',$this->id,PDO::PARAM_INT);
		if (strpos($sql,':employee_id')!==false)
			$command->bindParam(':employee_id',$this->employee_id,PDO::PARAM_INT);
		if (strpos($sql,':wages_status')!==false)
            $command->bindParam(':wages_status',$this->wages_status,PDO::PARAM_INT);
		if (strpos($sql,':wages_arr')!==false)
			$command->bindParam(':wages_arr',$this->wages_arr,PDO::PARAM_STR);
		if (strpos($sql,':sum')!==false)
			$command->bindParam(':sum',$this->sum,PDO::PARAM_STR);
		if (strpos($sql,':wages_date')!==false)
			$command->bindParam(':wages_date',date("Y-m-d"),PDO::PARAM_STR);
		if (strpos($sql,':apply_time')!==false)
			$command->bindParam(':apply_time',date("Y-m-d"),PDO::PARAM_STR);

        if (strpos($sql,':city')!==false)
            $command->bindParam(':city',$city,PDO::PARAM_STR);
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

	public function getOnly(){
	    return $this->scenario=='view'||$this->wages_status==1||$this->wages_status==3;
    }
}
