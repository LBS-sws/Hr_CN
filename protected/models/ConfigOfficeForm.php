<?php

class ConfigOfficeForm extends CFormModel
{
	public $id;
	public $name;
    public $city;
    public $u_id;
	public $z_display=1;
	public $office_sum = 0;

	public function attributeLabels()
	{
		return array(
            'name'=>Yii::t('contract','office Name'),
            'city'=>Yii::t('contract','City'),
            'z_display'=>Yii::t('contract','display'),
            'office_sum'=>Yii::t('contract','office sum'),
            'u_id'=>Yii::t('contract','u_id'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id, u_id, name, city,z_display,office_sum','safe'),
            array('name','required'),
			array('name','validateName'),
		);
	}

	public function validateName($attribute, $params){
        $city = Yii::app()->user->city();
        $id = -1;
        if(!empty($this->id)){
            $id = $this->id;
        }
        $this->city = empty($this->city)?$city:$this->city;
        $row = Yii::app()->db->createCommand()->select("id")->from("hr_office")
            ->where('name=:name and id!=:id and city=:city',
                array(':name'=>$this->name,':id'=>$id,':city'=>$this->city))->queryRow();
        if($row){
            $message = Yii::t('contract','office Name'). Yii::t('contract',' can not repeat');
            $this->addError($attribute,$message);
        }
	}

	public function retrieveData($index) {
		$rows = Yii::app()->db->createCommand()->select("*")
            ->from("hr_office")->where("id=:id",array(":id"=>$index))->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
                $this->id = $row['id'];
                $this->name = $row['name'];
                $this->u_id = $row['u_id'];
                $this->z_display = $row['z_display'];
                $this->city = $row['city'];
                $this->office_sum = Yii::app()->db->createCommand()->select("COUNT(id)")
                    ->from("hr_employee")->where("staff_status!=1 AND office_id=:id",array(":id"=>$index))
                    ->queryScalar();
                break;
			}
		}
		return true;
	}

	//获取办事处名称
    public static function getOfficeName($office_id){
        $row = Yii::app()->db->createCommand()->select("name")
            ->from("hr_office")->where("id=:id",array(":id"=>$office_id))->queryRow();
        return $row?$row["name"]:$office_id;
    }

	//获取办事处员工
    public static function getOfficeStaff($office_id){
        $suffix = Yii::app()->params['envSuffix'];
        $rows = Yii::app()->db->createCommand()
            ->select("a.code,a.name,a.phone,a.entry_time,b.name as dept_name,f.name as city_name")
            ->from("hr_employee a")
            ->leftJoin("hr_dept b","a.position=b.id")
            ->leftJoin("security{$suffix}.sec_city f","a.city=f.code")
            ->where("a.staff_status!=1 AND a.office_id=:id",array(":id"=>$office_id))
            ->queryAll();
        return $rows;
    }

	//获取办事处员工
    public static function getOfficeStaffToHtml($office_id){
        $html="";
        $rows = self::getOfficeStaff($office_id);
        if($rows){
            foreach ($rows as $row){
                $html.="<tr>";
                $html.="<td>".$row["code"]."</td>";
                $html.="<td>".$row["name"]."</td>";
                $html.="<td>".$row["city_name"]."</td>";
                $html.="<td>".$row["phone"]."</td>";
                $html.="<td>".$row["dept_name"]."</td>";
                $html.="<td>".$row["entry_time"]."</td>";
                $html.="</tr>";
            }
        }
        return $html;
    }

    //獲取办事处列表
    public static function getOfficeList($city="",$id=0){
        $city = empty($city)?Yii::app()->user->city():$city;
	    $arr = array(0=>Yii::t("contract","local office"));
        $rs = Yii::app()->db->createCommand()->select("id,name")->from("hr_office")
            ->where("(z_display=1 and city=:city) or id=:id",array(":id"=>$id,":city"=>$city))->queryAll();
        if($rs){
            foreach ($rs as $row){
                $arr[$row["id"]] = $row["name"];
            }
        }
        return $arr;
    }

    //刪除驗證
    public function deleteValidate(){
        $rs0 = Yii::app()->db->createCommand()->select()->from("hr_employee")->where('office_id=:office_id',array(':office_id'=>$this->id))->queryRow();
        if($rs0){
            return false;
        }else{
            return true;
        }
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
                $sql = "delete from hr_office where id = :id and city=:city";
                break;
            case 'new':
                $sql = "insert into hr_office(
							name,u_id,z_display, city, lcu
						) values (
							:name,:u_id,:z_display, :city, :lcu
						)";
                break;
            case 'edit':
                $sql = "update hr_office set
							name = :name, 
							u_id = :u_id, 
							z_display = :z_display, 
							luu = :luu
						where id = :id and city=:city
						";
                break;
        }
		if (empty($sql)) return false;

        $city = Yii::app()->user->city();
        $uid = Yii::app()->user->id;

        $command=$connection->createCommand($sql);
        if (strpos($sql,':id')!==false)
            $command->bindParam(':id',$this->id,PDO::PARAM_INT);
        if (strpos($sql,':u_id')!==false)
            $command->bindParam(':u_id',$this->u_id,PDO::PARAM_INT);
        if (strpos($sql,':name')!==false)
            $command->bindParam(':name',$this->name,PDO::PARAM_STR);
        if (strpos($sql,':z_display')!==false)
            $command->bindParam(':z_display',$this->z_display,PDO::PARAM_INT);

        if (strpos($sql,':city')!==false){
            $this->city = empty($this->city)?$city:$this->city;
            $command->bindParam(':city',$this->city,PDO::PARAM_STR);
        }
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
