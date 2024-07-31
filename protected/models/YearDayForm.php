<?php

class YearDayForm extends CFormModel
{
	public $id;
	public $employee_id;
	public $year;
	public $add_num;

	public function attributeLabels()
	{
        return array(
            'employee_id'=>Yii::t('contract','Employee Name'),
            'year'=>Yii::t('fete','Year'),
            'add_num'=>Yii::t('fete','Cumulative Day'),
        );
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id, employee_id,year,add_num','safe'),
            array('employee_id','required'),
            array('year','required'),
            array('add_num','required'),
            array('employee_id','numerical',"integerOnly"=>true),
            array('year','numerical',"integerOnly"=>true),
            array('add_num','numerical',"integerOnly"=>false),
		);
	}

	public function retrieveData($index) {
        $city_allow = Yii::app()->user->city_allow();
		$rows = Yii::app()->db->createCommand()->select("*")
            ->from("hr_staff_year")->where("id=:id",array(":id"=>$index))->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
                $this->id = $row['id'];
                $this->employee_id = $row['employee_id'];
                $this->year = $row['year'];
                $this->add_num = $row['add_num'];
                break;
			}
		}
		return true;
	}

    //獲取可以選擇的員工
    public function getEmployeeList(){
        $city_allow = Yii::app()->user->city_allow();
	    $arr = array(""=>"");
        $rows = Yii::app()->db->createCommand()->select("b.code,b.name,b.id")
            ->from("hr_binding a")->leftJoin("hr_employee b","a.employee_id = b.id")
            ->where("b.city in($city_allow)")->queryAll();
        if($rows){
            foreach ($rows as $row){
                $arr[$row["id"]] = $row["code"]." - ".$row["name"];
            }
        }
        return $arr;
    }

    //累積天數
    public function getSumDayToYear($employee_id,$time=""){
        if(empty($employee_id)||!is_numeric($employee_id)){
            return 0;
        }
        if(empty($time)){
            $time = date("Y/m/d");
        }else{
            $time = date("Y/m/d",strtotime($time));
        }
        $year = date("Y",strtotime($time));
        $month = date("m/d",strtotime($time));
        $time = date("Y/m/d",strtotime("$time - 1 year"));
        // and replace(entry_time,'-','/')<='$time'
        $sql = "SELECT year_day,entry_time FROM hr_employee WHERE staff_status = 0 AND id=$employee_id";
        $row = Yii::app()->db->createCommand($sql)->queryRow();
        $yearDay = 0;
        if($row){
            if(date("Y/m/d",strtotime($row["entry_time"]))<=$time){
                $yearDay+=floatval($row["year_day"]);
            }
            $entry_time = date("m/d",strtotime($row["entry_time"]));
            if($entry_time>$month){
                $year--;
            }
            $sql = "select sum(add_num) AS sumDay from hr_staff_year WHERE year=$year AND employee_id=$employee_id";
            $Sum = Yii::app()->db->createCommand($sql)->queryRow();
            if($Sum){
                $yearDay+=floatval($Sum["sumDay"]);
            }
        }
        return $yearDay;
    }
    //刪除驗證
    public function deleteValidate(){
        return true;
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
                $sql = "delete from hr_staff_year where id = :id";
                break;
            case 'new':
                $sql = "insert into hr_staff_year(
							employee_id,year,add_num, lcu
						) values (
							:employee_id,:year,:add_num, :lcu
						)";
                break;
            case 'edit':
                $sql = "update hr_staff_year set
							employee_id = :employee_id, 
							year = :year, 
							add_num = :add_num, 
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
        if (strpos($sql,':employee_id')!==false)
            $command->bindParam(':employee_id',$this->employee_id,PDO::PARAM_INT);
        if (strpos($sql,':year')!==false)
            $command->bindParam(':year',$this->year,PDO::PARAM_INT);
        if (strpos($sql,':add_num')!==false)
            $command->bindParam(':add_num',$this->add_num,PDO::PARAM_INT);

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
