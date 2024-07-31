<?php

/**
 * UserForm class.
 * UserForm is the data structure for keeping
 * user form data. It is used by the 'user' action of 'SiteController'.
 */
class AuditWagesForm extends CFormModel
{
    /* User Fields */
    public $id;
    public $employee_id;
    public $staff_list;
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
            'employee_id'=>Yii::t('contract','Staff View'),
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
            array('id, employee_id, wages_status, staff_list, sum, wages_date, wages_arr, just_remark','safe'),
			array('just_remark','required',"on"=>"reject"),
/*            array('license_time, organization_time','date','allowEmpty'=>true,
                'format'=>array('yyyy/MM/dd','yyyy-MM-dd','yyyy/M/d'),
            ),*/
		);
	}

	public function retrieveData($index)
	{
        $row = Yii::app()->db->createCommand()->select("*")->from("hr_employee_wages")
            ->where("id =:id",array(":id"=>$index))->queryRow();
        if ($row){
            $this->id = $row['id'];
            $this->employee_id = $row['employee_id'];
            $this->staff_list = StaffFun::getEmployeeOneToId($row['employee_id']);
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
			throw new CHttpException(404,'Cannot update.');
		}
	}

	protected function saveStaff(&$connection)
	{
		$sql = '';
        $city = Yii::app()->user->city();
        $uid = Yii::app()->user->id;
		switch ($this->scenario) {
			case 'audit':
                $sql = "update hr_employee_wages set
							wages_status = 3, 
							luu = :luu 
						where id = :id
						";
                break;
			case 'reject':
				$sql = "update hr_employee_wages set
							wages_status = 2, 
							just_remark = :just_remark, 
							luu = :luu 
						where id = :id
						";
				break;
		}
		if(is_array($this->wages_body)){
            $this->wages_body = implode(",",$this->wages_body);
        }
		$command=$connection->createCommand($sql);
		if (strpos($sql,':id')!==false)
			$command->bindParam(':id',$this->id,PDO::PARAM_INT);
		if (strpos($sql,':just_remark')!==false)
			$command->bindParam(':just_remark',$this->just_remark,PDO::PARAM_INT);

		if (strpos($sql,':luu')!==false)
			$command->bindParam(':luu',$uid,PDO::PARAM_STR);

		$command->execute();
        return true;
	}
}
