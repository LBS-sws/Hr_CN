<?php

class RecruitApplyForm extends CFormModel
{
	/* User Fields */
	public $id;
	public $year;
	public $city;
	public $leader_id;
	public $dept_id;
	public $recruit_num=1;
    public $now_num;
    public $leave_num;
    public $lack_num;
    public $completion_rate;

	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
        return array(
            'year'=>Yii::t('recruit','year'),
            'city'=>Yii::t('recruit','city'),
            'dept_id'=>Yii::t('recruit','dept name'),
            'leader_id'=>Yii::t('recruit','leader name'),
            'recruit_num'=>Yii::t('recruit','recruit num'),
            'now_num'=>Yii::t('recruit','now num'),
            'leave_num'=>Yii::t('recruit','leave num'),
            'lack_num'=>Yii::t('recruit','lack num'),
            'completion_rate'=>Yii::t('recruit','completion rate'),
        );
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
            array('id,year,dept_id,leader_id,city','safe'),
            array('year,dept_id,leader_id,recruit_num','required'),
            array('year,dept_id,leader_id','numerical','allowEmpty'=>false,'integerOnly'=>true),
            array('recruit_num','numerical','min'=>1,'allowEmpty'=>false,'integerOnly'=>true),
            array('id','validateDept'),
        );
	}

    public function validateDept($attribute, $params){
        $row = Yii::app()->db->createCommand()->select("id")->from("hr_recruit")
            ->where("id!=:id and year=:year and dept_id=:dept_id",
                array(":id"=>$this->id,":year"=>$this->year,":dept_id"=>$this->dept_id)
            )->queryRow();
        if($row){
            $message = "该招聘已存在，无法重复添加({$row['id']})";
            $this->addError($attribute,$message);
            return false;
        }
    }

	public function retrieveData($index)
	{
		$suffix = Yii::app()->params['envSuffix'];
		$sql = "select * from hr_recruit where id='".$index."'";
		$row = Yii::app()->db->createCommand($sql)->queryRow();
		if ($row!==false) {
			$this->id = $row['id'];
			$this->year = $row['year'];
			$this->city = $row['city'];
			$this->dept_id = $row['dept_id'];
			$this->leader_id = $row['leader_id'];
			$this->recruit_num = $row['recruit_num'];
            return true;
		}else{
		    return false;
        }
	}

	public static function getDeptList($city){
        $rows = Yii::app()->db->createCommand()->select("a.id,a.dept_id,a.name")->from("hr_dept a")
            ->where("a.type=1 and a.city=:city",array(":city"=>$city))
            ->queryAll();
        $deptList = array(""=>"");
        $optionsList = array();
        if($rows){
            foreach ($rows as $row){
                $deptList[$row["id"]] = $row["name"];
                $optionsList[$row["id"]] = array("data-leader"=>$row["dept_id"]);
            }
        }
        return array("downList"=>$deptList,"optionsList"=>$optionsList);
    }
	
	public function saveData()
	{
		$connection = Yii::app()->db;
		$transaction=$connection->beginTransaction();
		try {
			$this->saveDataForSql($connection);
			$transaction->commit();
		}
		catch(Exception $e) {
		    var_dump($e);
			$transaction->rollback();
			throw new CHttpException(404,'Cannot update.');
		}
	}

	protected function saveDataForSql(&$connection)
	{
		$suffix = Yii::app()->params['envSuffix'];
		$sql = '';
		switch ($this->scenario) {
			case 'delete':
				$sql = "delete from hr_recruit where id = :id";
				break;
			case 'new':
				$sql = "insert into hr_recruit(
						year, city, leader_id, dept_id, recruit_num, lcu, lcd) values (
						:year, :city, :leader_id, :dept_id, :recruit_num, :lcu, :lcd)";
				break;
			case 'edit':
				$sql = "update hr_recruit set 
					year = :year, 
					leader_id = :leader_id,
					dept_id = :dept_id,
					recruit_num = :recruit_num,
					luu = :luu
					where id = :id";
				break;
		}

		$uid = Yii::app()->user->id;
        $city = Yii::app()->user->city();

		$command=$connection->createCommand($sql);
		if (strpos($sql,':id')!==false)
			$command->bindParam(':id',$this->id,PDO::PARAM_INT);
		if (strpos($sql,':year')!==false)
			$command->bindParam(':year',$this->year,PDO::PARAM_INT);
		if (strpos($sql,':leader_id')!==false)
			$command->bindParam(':leader_id',$this->leader_id,PDO::PARAM_INT);
		if (strpos($sql,':dept_id')!==false)
			$command->bindParam(':dept_id',$this->dept_id,PDO::PARAM_INT);
		if (strpos($sql,':recruit_num')!==false)
			$command->bindParam(':recruit_num',$this->recruit_num,PDO::PARAM_INT);
		if (strpos($sql,':city')!==false)
			$command->bindParam(':city',$city,PDO::PARAM_STR);

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