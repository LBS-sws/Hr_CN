<?php

class GroupStaffForm extends CFormModel
{
	/* User Fields */
	public $id;
	public $group_id;
	public $employee_id;
	public $employee_name;
	public $branch_text;
	public $branchDetail=array(
        array('id'=>0,
            'employeeID'=>'',
            'employeeName'=>'',
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
            'group_id'=>Yii::t('group','GroupName'),
            'employeeID'=>Yii::t('group','branch text'),
            'employee_id'=>Yii::t('group','employee name'),
            'employee_name'=>Yii::t('group','employee name'),
            'branch_text'=>Yii::t('group','branch text'),
            'branchDetail'=>Yii::t('group','branch text'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
            array('id,group_id,employee_id,branchDetail','safe'),
			array('group_id,employee_id','required'),
			array('group_id','validateGroup'),
			array('employee_id','validateEmployee'),
			array('branchDetail','validateDetail'),
		);
	}
    public function validateGroup($attribute, $params){
        $row = Yii::app()->db->createCommand()->select("id")
            ->from("hr_group")->where("id=:id",array(":id"=>$this->group_id))->queryRow();
        if($row===false){
            $message = "分组名称不存在";
            $this->addError($attribute,$message);
        }
    }
    public function validateEmployee($attribute, $params){
        $row = Yii::app()->db->createCommand()->select("id")
            ->from("hr_employee")->where("id=:id",array(":id"=>$this->employee_id))->queryRow();
        if($row===false){
            $message = "员工不存在";
            $this->addError($attribute,$message);
        }
    }
    public function validateDetail($attribute, $params){
        $this->branch_text=null;
        $branchText = array();
        $updateArr = array();
        $delArr = array();
        if(!empty($this->branchDetail)){
            foreach ($this->branchDetail as $row){
                if($row["uflag"]=="D"){
                    $delArr[] = $row;
                }else{
                    $branchText[]=$row["employeeName"];
                    $updateArr[]=$row;
                }
            }
        }
        if(!empty($branchText)){
            $this->branch_text = implode(";",$branchText);
        }
        if(empty($this->getErrors())){
            $this->branchDetail = array_merge($updateArr,$delArr);
        }
    }

	public function retrieveData($index)
	{
		$suffix = Yii::app()->params['envSuffix'];
		$sql = "select a.*,b.code as employee_code,b.name as employee_name from hr_group_staff a 
        LEFT JOIN hr_employee b ON a.employee_id=b.id
        where a.id='".$index."'";
		$row = Yii::app()->db->createCommand($sql)->queryRow();
		if ($row!==false) {
			$this->id = $row['id'];
			$this->group_id = $row['group_id'];
			$this->employee_id = $row['employee_id'];
			$this->employee_name = $row['employee_name']." ({$row['employee_code']})";
			$details = Yii::app()->db->createCommand()->select("a.*,b.code as employee_code,b.name as employee_name")
                ->from("hr_group_branch a")
                ->leftJoin("hr_employee b","a.employee_id=b.id")
                ->where("a.group_staff_id=:id",array(":id"=>$this->id))
                ->queryAll();
			if($details){
			    $this->branchDetail=array();
			    foreach ($details as $detail){
                    $this->branchDetail[]=array(
                        'id'=>$detail["id"],
                        'employeeID'=>$detail["employee_id"],
                        'employeeName'=>$detail['employee_name']." ({$detail['employee_code']})",
                        'uflag'=>'N',
                    );
                }
            }
            return true;
		}else{
		    return false;
        }
	}

	public function retrieveCopy($index)
	{
		$suffix = Yii::app()->params['envSuffix'];
		$sql = "select a.id,a.group_id from hr_group_staff a 
        where a.id='".$index."'";
		$row = Yii::app()->db->createCommand($sql)->queryRow();
		if ($row!==false) {
			$this->group_id = $row['group_id'];
			$details = Yii::app()->db->createCommand()->select("a.*,b.code as employee_code,b.name as employee_name")
                ->from("hr_group_branch a")
                ->leftJoin("hr_employee b","a.employee_id=b.id")
                ->where("a.group_staff_id=:id",array(":id"=>$row["id"]))
                ->queryAll();
			if($details){
			    $this->branchDetail=array();
			    foreach ($details as $detail){
                    $this->branchDetail[]=array(
                        'id'=>0,
                        'employeeID'=>$detail["employee_id"],
                        'employeeName'=>$detail['employee_name']." ({$detail['employee_code']})",
                        'uflag'=>'Y',
                    );
                }
            }
            return true;
		}else{
		    return false;
        }
	}
	
	public function saveData()
	{
		$connection = Yii::app()->db;
		$transaction=$connection->beginTransaction();
		try {
			$this->saveDataForSql($connection);
			$this->saveDataForInfo($connection);
			GroupNameForm::resetGroupSum($this->group_id);
			$transaction->commit();
		}
		catch(Exception $e) {
		    var_dump($e);
			$transaction->rollback();
			throw new CHttpException(404,'Cannot update.');
		}
	}

	protected function saveDataForInfo(&$connection)
	{
        $uid = Yii::app()->user->id;
        foreach ($this->branchDetail as $row) {
            if(empty($row["employeeID"])){
                continue;
            }
            $sql = '';
            switch ($this->scenario) {
                case 'delete':
                    $sql = "delete from hr_group_branch where group_id = :group_id and group_staff_id=:group_staff_id";
                    break;
                case 'new':
                    if ($row['uflag']=='Y') {
                        $sql = "insert into hr_group_branch(
									group_id, group_staff_id, employee_id,lcu
								) values (
									:group_id,:group_staff_id,:employee_id,:lcu
								)";
                    }
                    break;
                case 'edit':
                    switch ($row['uflag']) {
                        case 'D':
                            $sql = "delete from hr_group_branch where id = :id and group_staff_id=:group_staff_id";
                            break;
                        case 'Y':
                            $sql = ($row['id']==0)
                                ?
                                "insert into hr_group_branch(
									    group_id, group_staff_id, employee_id,lcu
									) values (
									    :group_id,:group_staff_id,:employee_id,:lcu
									)"
                                :
                                "update hr_group_branch set
										group_id = :group_id, 
										group_staff_id = :group_staff_id, 
										employee_id = :employee_id,
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
                if (strpos($sql,':group_id')!==false)
                    $command->bindParam(':group_id',$this->group_id,PDO::PARAM_INT);
                if (strpos($sql,':group_staff_id')!==false)
                    $command->bindParam(':group_staff_id',$this->id,PDO::PARAM_INT);
                if (strpos($sql,':employee_id')!==false)
                    $command->bindParam(':employee_id',$row['employeeID'],PDO::PARAM_INT);
                if (strpos($sql,':luu')!==false)
                    $command->bindParam(':luu',$uid,PDO::PARAM_STR);
                if (strpos($sql,':lcu')!==false)
                    $command->bindParam(':lcu',$uid,PDO::PARAM_STR);
                $command->execute();
            }
        }
        return true;
    }

	protected function saveDataForSql(&$connection)
	{
		$suffix = Yii::app()->params['envSuffix'];
		$sql = '';
		switch ($this->scenario) {
			case 'delete':
				$sql = "delete from hr_group_staff where id = :id";
				break;
			case 'new':
				$sql = "insert into hr_group_staff(
						group_id, employee_id, branch_text, lcu, lcd) values (
						:group_id, :employee_id, :branch_text, :lcu, :lcd)";
				break;
			case 'edit':
				$sql = "update hr_group_staff set 
					group_id = :group_id, 
					employee_id = :employee_id,
					branch_text = :branch_text,
					luu = :luu
					where id = :id";
				break;
		}

		$uid = Yii::app()->user->id;

		$command=$connection->createCommand($sql);
		if (strpos($sql,':id')!==false)
			$command->bindParam(':id',$this->id,PDO::PARAM_INT);
		if (strpos($sql,':group_id')!==false)
			$command->bindParam(':group_id',$this->group_id,PDO::PARAM_INT);
		if (strpos($sql,':employee_id')!==false)
			$command->bindParam(':employee_id',$this->employee_id,PDO::PARAM_INT);
		if (strpos($sql,':branch_text')!==false)
			$command->bindParam(':branch_text',$this->branch_text,PDO::PARAM_STR);

		if (strpos($sql,':lcu')!==false)
			$command->bindParam(':lcu',$uid,PDO::PARAM_STR);
		if (strpos($sql,':luu')!==false)
			$command->bindParam(':luu',$uid,PDO::PARAM_STR);
		if (strpos($sql,':lcd')!==false){
            $date = date("Y-m-d H:i:s");
            $command->bindParam(':lcd',$date,PDO::PARAM_STR);
        }
		$command->execute();

        if ($this->scenario=='new')
            $this->id = Yii::app()->db->getLastInsertID();

		return true;
	}
}