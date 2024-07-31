<?php

class TreatyStopForm extends CFormModel
{
	/* User Fields */
	public $id;
	public $treaty_code;
	public $treaty_name;
	public $month_num;
	public $treaty_num;
	public $city;
	public $city_name;
	public $apply_date;
	public $start_date;
	public $end_date;
	public $state_type;
	public $lcu;

	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
        return array(
            'treaty_code'=>Yii::t('treaty','treaty code'),
            'treaty_name'=>Yii::t('treaty','treaty name'),
            'month_num'=>Yii::t('treaty','month num'),
            'treaty_num'=>Yii::t('treaty','treaty num'),
            'city'=>Yii::t('treaty','city'),
            'city_name'=>Yii::t('treaty','city'),
            'apply_date'=>Yii::t('treaty','apply date'),
            'start_date'=>Yii::t('treaty','start date'),
            'end_date'=>Yii::t('treaty','end date'),
            'state_type'=>Yii::t('treaty','treaty state'),
            'lcu'=>Yii::t('treaty','treaty lcu'),
        );
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
            array('id,treaty_code,treaty_name,month_num,treaty_num,city,city_name,apply_date,start_date,end_date,state_type','safe'),
			array('treaty_name','required'),
		);
	}

	public function retrieveData($index)
	{
        $suffix = Yii::app()->params['envSuffix'];
        $city_allow = Yii::app()->user->city_allow();
        $uid = Yii::app()->user->id;
        if(Yii::app()->user->validFunction('ZR21')){ //允許查看管轄內的所有項目
            $whereSql = " and a.city in ({$city_allow}) ";
        }else{
            $whereSql = " and a.lcu='{$uid}' ";
        }
        $sql = "select a.* 
				from hr_treaty a
				where a.state_type=3 {$whereSql} and a.id='$index'
			";
		$row = Yii::app()->db->createCommand($sql)->queryRow();
		if ($row!==false) {
		    //id,treaty_code,treaty_name,month_num,treaty_num,city,apply_date,start_date,end_date,state_type
			$this->id = $row['id'];
			$this->treaty_code = $row['treaty_code'];
			$this->treaty_name = $row['treaty_name'];
			$this->month_num = $row['month_num'];
			$this->treaty_num = empty($row["treaty_num"])?"":$row['treaty_num'];
			$this->city = $row['city'];
			$this->apply_date = empty($row["apply_date"])?"":CGeneral::toDate($row["apply_date"]);
			$this->start_date = empty($row["start_date"])?"":CGeneral::toDate($row["start_date"]);
			$this->end_date = empty($row["end_date"])?"":CGeneral::toDate($row["end_date"]);
			$this->state_type = $row['state_type'];
			$this->lcu = $row['lcu'];
            return true;
		}else{
		    return false;
        }
	}
	
	public function blackData(){
        $uid = Yii::app()->user->id;
        Yii::app()->db->createCommand()->update("hr_treaty", array(
            'state_type'=>0,
            'luu'=>$uid
        ), "id=:id and state_type=3", array(':id'=>$this->id));
	}

}