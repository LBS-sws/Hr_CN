<?php

class LookSetForm extends CFormModel
{
	public $id;
	public $username;
	public $staff_id_str;
	public $staff_name_str;

	public function attributeLabels()
	{
		return array(
            'username'=>Yii::t('contract','username'),
            'staff_id_str'=>Yii::t('contract','look employee'),
            'staff_name_str'=>Yii::t('contract','look employee'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id, username,staff_id_str,staff_name_str','safe'),
            array('username,staff_name_str','required'),
			array('username','validateName'),
		);
	}

	public function validateName($attribute, $params){
        $id = -1;
        if(!empty($this->id)){
            $id = $this->id;
        }
        $row = Yii::app()->db->createCommand()->select("id")->from("hr_set_look")
            ->where('username=:username and id!=:id',
                array(':username'=>$this->username,':id'=>$id))->queryRow();
        if($row){
            $message = Yii::t('contract','username'). Yii::t('contract',' can not repeat');
            $this->addError($attribute,$message);
        }
	}

	public function retrieveData($index) {
		$rows = Yii::app()->db->createCommand()->select("*")
            ->from("hr_set_look")->where("id=:id",array(":id"=>$index))->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
                $this->id = $row['id'];
                $this->username = $row['username'];
                $this->staff_id_str = $row['staff_id_str'];
                $this->staff_name_str = $row['staff_name_str'];
                break;
			}
		}
		return true;
	}

	public function searchEmployee($city,$name,$position) {
	    $list=array();
        $whereSql="a.table_type=1 ";
        if(!empty($city)){
            $city = str_replace("'","\'",$city);
            $whereSql.=" and a.city='{$city}'";
        }
        if(!empty($name)){
            $name = str_replace("'","\'",$name);
            $whereSql.=" and (a.code like '%{$name}%' or a.name like '%{$name}%')";
        }
        if(!empty($position)){
            $position = str_replace("'","\'",$position);
            $whereSql.=" and b.name like '%{$position}%'";
        }
		$rows = Yii::app()->db->createCommand()->select("a.id,a.code,a.name,a.city,b.name as position_name")
            ->from("hr_employee a")
            ->leftJoin("hr_dept b","a.position=b.id")
            ->where($whereSql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
                $temp=array();
                $temp["id"] = $row['id'];
                $temp["code"] = $row['code'];
                $temp["name"] = $row['name'];
                $temp["city_name"] = CGeneral::getCityName($row['city']);
                $temp["position_name"] = $row['position_name'];
                $list[]=$temp;
			}
		}
		return $list;
	}

    //獲取用戶表的所有用戶(相同城市)
    public function getUserList($username){
	    $id = empty($this->id)?0:$this->id;
        $suffix = Yii::app()->params['envSuffix'];
        $from = "security".$suffix.".sec_user";
        $rows = Yii::app()->db->createCommand()->select("username,disp_name")->from($from)->where("username='$username' or status='A'")->queryAll();
        $bindList = Yii::app()->db->createCommand()->select("username")->from("hr_set_look")->where("id !=:id",array(":id"=>$id))->queryAll();
        $bindList = array_column($bindList,"username");
        $arr = array(""=>"");
        foreach ($rows as $row){
            if(!in_array($row["username"],$bindList)){
                $arr[$row["username"]] = $row["disp_name"];
            }
        }
        return $arr;
    }

    //獲取Sql
    public static function getLookSqlForStr($str){
        $sql = "";
        $uid = Yii::app()->user->id;
	    $row = Yii::app()->db->createCommand()->select("staff_id_str")->from("hr_set_look")
            ->where("username=:username",array(":username"=>$uid))->queryRow();
	    if($row){
	        $staff_id_str = empty($row['staff_id_str'])?0:$row['staff_id_str'];
            $sql=" or ({$str} in ({$staff_id_str}) and a.status!=0)";
        }
        return $sql;
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
                $sql = "delete from hr_set_look where id = :id";
                break;
            case 'new':
                $sql = "insert into hr_set_look(
							username,staff_id_str,staff_name_str, lcu
						) values (
							:username,:staff_id_str,:staff_name_str, :lcu
						)";
                break;
            case 'edit':
                $sql = "update hr_set_look set
							username = :username, 
							staff_id_str = :staff_id_str, 
							staff_name_str = :staff_name_str, 
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
        if (strpos($sql,':username')!==false)
            $command->bindParam(':username',$this->username,PDO::PARAM_STR);
        if (strpos($sql,':staff_id_str')!==false)
            $command->bindParam(':staff_id_str',$this->staff_id_str,PDO::PARAM_INT);
        if (strpos($sql,':staff_name_str')!==false)
            $command->bindParam(':staff_name_str',$this->staff_name_str,PDO::PARAM_STR);

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
