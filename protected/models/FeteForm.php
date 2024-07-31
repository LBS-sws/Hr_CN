<?php

class FeteForm extends CFormModel
{
	public $id;
	public $name;
	public $start_time;
	public $end_time;
	public $log_time=0;
	public $cost_num=0;
	public $only;
	public $city;

	public function attributeLabels()
	{
		return array(
            'name'=>Yii::t('fete','Fete Name'),
            'city'=>Yii::t('contract','City'),
            'start_time'=>Yii::t('contract','Start Time'),
            'end_time'=>Yii::t('contract','End Time'),
            'log_time'=>Yii::t('fete','Log Time'),
            'cost_num'=>Yii::t('fete','Cost Num'),
            'only'=>Yii::t('fete','Scope of application'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id, name,start_time,end_time,log_time,city,cost_num,only','safe'),
            array('name','required'),
            array('name','validateName'),
            array('start_time','required'),
            array('end_time','required'),
            array('city','required'),
            array('cost_num','required'),
            array('only','required'),
            array('end_time','validateTime'),
            array('log_time','numerical','allowEmpty'=>true,'integerOnly'=>true),
            array('end_time, start_time','date','allowEmpty'=>true,
                'format'=>array('yyyy/MM/dd','yyyy-MM-dd','yyyy/M/d'),
            ),
		);
	}

	public function validateName($attribute, $params){
        $city = $this->city;
        $id = -1;
        if(!empty($this->id)){
            $id = $this->id;
        }
        $rows = Yii::app()->db->createCommand()->select("id")->from("hr_fete")
            ->where('name=:name and city=:city and id!=:id',
                array(':name'=>$this->name,':id'=>$id,':city'=>$city))->queryAll();
        if(count($rows)>0){
            $message = Yii::t('fete','Fete Name'). Yii::t('contract',' can not repeat');
            $this->addError($attribute,$message);
        }
	}
	public function validateTime($attribute, $params){
	    if(!empty($this->end_time)&&!empty($this->start_time)){
            $date2 = strtotime($this->start_time);
            $date1 = strtotime($this->end_time);
            if($date2>$date1){
                $message = Yii::t('fete','Start time cannot be greater than end time');
                $this->addError($attribute,$message);
            }else{
                $time_difference = $date1 - $date2;
                $seconds_per_year = 60*60*24;
                $yrs = round($time_difference / $seconds_per_year);
                $this->log_time = strval($yrs);
            }
        }
	}

	public function retrieveData($index) {
        $city_allow = Yii::app()->user->city_allow();
		$rows = Yii::app()->db->createCommand()->select("*")
            ->from("hr_fete")->where("id=:id and (city in ($city_allow) OR only='default') ",array(":id"=>$index))->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
                $this->id = $row['id'];
                $this->name = $row['name'];
                $this->start_time = $row['start_time'];
                $this->end_time = $row['end_time'];
                $this->log_time = $row['log_time'];
                $this->city = $row['city'];
                $this->cost_num = $row['cost_num'];
                $this->only = $row['only'];
                break;
			}
		}
		return true;
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
                $sql = "delete from hr_fete where id = :id";
                break;
            case 'new':
                $sql = "insert into hr_fete(
							name,start_time,end_time, log_time, cost_num, only, city, lcu
						) values (
							:name,:start_time,:end_time, :log_time, :cost_num, :only, :city, :lcu
						)";
                break;
            case 'edit':
                $sql = "update hr_fete set
							name = :name, 
							start_time = :start_time, 
							end_time = :end_time, 
							log_time = :log_time, 
							city = :city, 
							cost_num = :cost_num, 
							only = :only, 
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
        //id, name,start_time,end_time,log_time,city,cost_num
        if (strpos($sql,':name')!==false)
            $command->bindParam(':name',$this->name,PDO::PARAM_STR);
        if (strpos($sql,':start_time')!==false)
            $command->bindParam(':start_time',$this->start_time,PDO::PARAM_STR);
        if (strpos($sql,':end_time')!==false)
            $command->bindParam(':end_time',$this->end_time,PDO::PARAM_STR);
        if (strpos($sql,':log_time')!==false)
            $command->bindParam(':log_time',$this->log_time,PDO::PARAM_STR);
        if (strpos($sql,':cost_num')!==false)
            $command->bindParam(':cost_num',$this->cost_num,PDO::PARAM_STR);
        if (strpos($sql,':only')!==false)
            $command->bindParam(':only',$this->only,PDO::PARAM_STR);

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
}
