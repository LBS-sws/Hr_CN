<?php

class BossSearchForm extends CFormModel
{
	public $id;
	public $employee_id;
	public $lcu;
	public $code;
	public $name;
	public $city;
	public $audit_year;
	public $apply_date;
	public $status_type=0;
	public $reject_remark;
	public $json_text=array();
	public $results_sum;
	public $results_a;
	public $results_b;
	public $results_c;
    public $ratio_a=50;//占比
    public $ratio_b=35;//占比
    public $ratio_c=15;//占比
    public $json_listX;

	public function attributeLabels()
	{
		return array(
            'name'=>Yii::t('contract','Employee Name'),
            'code'=>Yii::t('contract','Employee Code'),
            'audit_year'=>Yii::t('contract','audit year'),
            'results_sum'=>Yii::t('contract','Sum Results'),
            'status_type'=>Yii::t('contract','Status'),
            'reject_remark'=>Yii::t('contract','Rejected Remark'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id','safe'),
            array('id','required'),
            array('id','validateId','on'=>array('back'))
		);
	}


    public function validateId($attribute, $params){
        $rows = Yii::app()->db->createCommand()->select("id")->from("hr_boss_audit")
            ->where('id=:id and status_type=2',array(':id'=>$this->id))->queryRow();
        if(!$rows){
            $message = Yii::t('contract','audit year'). Yii::t('contract',' can not repeat');
            $this->addError($attribute,$message);
        }
    }

	public function getBossFlowList($boss_id) {
        $city_allow = Yii::app()->user->city_allow();
        $suffix = Yii::app()->params['envSuffix'];
        $rows = Yii::app()->db->createCommand()->select("c.disp_name,a.*")
            ->from("hr_boss_flow a")
            //->leftJoin("hr_boss_audit b","a.boss_id = b.id")
            ->leftJoin("security$suffix.sec_user c","a.lcu = c.username")
            ->where("a.boss_id=:id",array(":id"=>$boss_id))->queryAll();
		return $rows?$rows:array();
	}

	public function retrieveData($index) {
        $city_allow = Yii::app()->user->city_allow();
        $suffix = Yii::app()->params['envSuffix'];
        $row = Yii::app()->db->createCommand()->select("a.*,b.code as employee_code,b.name as employee_name")
            ->from("hr_boss_audit a")
            ->leftJoin("hr_employee b","a.employee_id = b.id")
            ->where("a.id=:id and a.status_type = 2 and a.city IN ($city_allow)",array(":id"=>$index))->queryRow();
		if ($row) {
            $this->id = $row['id'];
            $this->employee_id = $row['employee_id'];
            $this->lcu = $row['lcu'];
            $this->code = $row['employee_code'];
            $this->name = $row['employee_name'];
            $this->city = $row['city'];
            $this->apply_date = $row['apply_date'];
            $this->audit_year = $row['audit_year'];
            $this->json_text = json_decode($row['json_text'],true);
            $this->reject_remark = $row['reject_remark'];
            $this->status_type = $row['status_type'];
            $this->results_sum = $row['results_sum'];
            $this->results_a = $row['results_a'];
            $this->results_b = $row['results_b'];
            $this->results_c = $row['results_c'];
            $this->ratio_a = $row['ratio_a'];
            $this->ratio_b = $row['ratio_b'];
            $this->ratio_c = $row['ratio_c'];
            $this->json_listX = empty($row['json_listX'])?array():json_decode($row['json_listX'],true);
		}
		return true;
	}

	public function setDataToEmployeeIdAndYear($employee_id,$year,$cityBool=true) {
        if($cityBool){
            $city_allow = Yii::app()->user->city_allow();
            $citySql = " and b.city in ($city_allow) ";
        }else{
            $citySql = "";
        }
        $suffix = Yii::app()->params['envSuffix'];
        $row = Yii::app()->db->createCommand()->select("a.*,b.code as employee_code,b.name as employee_name")
            ->from("hr_boss_audit a")
            ->leftJoin("hr_employee b","a.employee_id = b.id")
            ->where("a.employee_id=:employee_id $citySql and a.audit_year = :year",array(":employee_id"=>$employee_id,":year"=>$year))->queryRow();
		if ($row) {
            $this->id = $row['id'];
            $this->employee_id = $row['employee_id'];
            $this->lcu = $row['lcu'];
            $this->code = $row['employee_code'];
            $this->name = $row['employee_name'];
            $this->city = $row['city'];
            $this->apply_date = $row['apply_date'];
            $this->audit_year = $row['audit_year'];
            $this->json_text = json_decode($row['json_text'],true);
            $this->reject_remark = $row['reject_remark'];
            $this->status_type = $row['status_type'];
            $this->results_sum = $row['results_sum'];
            $this->results_a = $row['results_a'];
            $this->results_b = $row['results_b'];
            $this->results_c = $row['results_c'];
            $this->json_listX = empty($row['json_listX'])?array():json_decode($row['json_listX'],true);
		}else{
		    $this->employee_id = $employee_id;
		    $this->audit_year = $year;
		    $this->json_text = array();
        }
        return true;
	}


	public function setDataToCityAndYear($city,$year) {
        $row = Yii::app()->db->createCommand()->select("a.*,b.code as employee_code,b.name as employee_name")
            ->from("hr_boss_audit a")
            ->leftJoin("hr_employee b","a.employee_id = b.id")
            ->where("a.city=:city and a.audit_year = :year",array(":city"=>$city,":year"=>$year))->queryRow();
		if ($row) {
            $this->id = $row['id'];
            $this->employee_id = $row['employee_id'];
            $this->lcu = $row['lcu'];
            $this->code = $row['employee_code'];
            $this->name = $row['employee_name'];
            $this->city = $row['city'];
            $this->apply_date = $row['apply_date'];
            $this->audit_year = $row['audit_year'];
            $this->json_text = json_decode($row['json_text'],true);
            $this->reject_remark = $row['reject_remark'];
            $this->status_type = $row['status_type'];
            $this->results_sum = $row['results_sum'];
            $this->results_a = $row['results_a'];
            $this->results_b = $row['results_b'];
            $this->results_c = $row['results_c'];
            $this->json_listX = empty($row['json_listX'])?array():json_decode($row['json_listX'],true);
            return true;
		}else{
		    $this->employee_id = 0;
		    $this->audit_year = $year;
		    $this->json_text = array();
            return false;
        }
	}

    //刪除驗證
    public function deleteValidate(){
        return false;
    }

	public function saveData($str='')
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
            case 'back':
                $sql = "update hr_boss_audit set
							status_type = 0, 
							luu = :luu
						where id = :id AND status_type = 2
						";
                break;
        }
		if (empty($sql)) return false;

        $city = Yii::app()->user->city();
        $uid = Yii::app()->user->id;

        $command=$connection->createCommand($sql);
        if (strpos($sql,':id')!==false)
            $command->bindParam(':id',$this->id,PDO::PARAM_INT);

        if (strpos($sql,':luu')!==false)
            $command->bindParam(':luu',$uid,PDO::PARAM_STR);
        $command->execute();

		return true;
	}

	//判斷輸入框能否修改
	public function getInputBool(){
        return true;
    }
}
