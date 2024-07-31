<?php

class HeartLetterSearchForm extends CFormModel
{
	public $id;
	public $employee_id;
	public $employee_code;
	public $employee_name;
	public $state=0;

	public $letter_id=0;
	public $letter_type;
	public $letter_title;
	public $letter_body;
	public $letter_num=0;
	public $letter_reply;

    public $lcu;
    public $luu;
	public $city;
	public $lcd;

	public $letterList=array();

	public function attributeLabels()
	{
		return array(
            'letter_type'=>Yii::t('contract','type for director'),
            'letter_title'=>Yii::t('queue','Subject'),
            'letter_body'=>Yii::t('contract','letter body'),
            'employee_name'=>Yii::t('contract','Employee Name'),
            'employee_code'=>Yii::t('contract','Employee Code'),

            'letter_num'=>Yii::t('contract','review score'),
            'letter_reply'=>Yii::t('contract','reply'),

            'state'=>Yii::t('contract','Status'),
            'city'=>Yii::t('contract','City'),
            'lcd'=>Yii::t('fete','apply for time'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id,employee_id,employee_code,employee_name,city,','safe'),
            //array('employee_id','validateUser'),
            array('id','validateUpdate'),
		);
	}

	public function validateUpdate($attribute, $params){
        $row = Yii::app()->db->createCommand()->select("id,employee_id")->from("hr_letter")
            ->where('id=:id and state = 4',array(':id'=>$this->id))->queryRow();
        if(!$row){
            $message = "心意信封不存在，请于管理员联系";
            $this->addError($attribute,$message);
        }
    }

	public function retrieveData($index) {
        $lcuId = Yii::app()->user->id;
        $city_allow = Yii::app()->user->city_allow();
        $suffix = Yii::app()->params['envSuffix'];
        $staff = Yii::app()->db->createCommand()->select("code,name,city")->from("hr_employee")
            ->where("id=:id and city in ($city_allow)",array(":id"=>$index))->queryRow();
        if($staff){
            $this->employee_id = $index;
            $this->employee_code = $staff["code"];
            $this->employee_name = $staff["name"];
            $this->city = CGeneral::getCityName($staff["city"]);
            $sql = "";
            $session = Yii::app()->session;
            if (isset($session['heartLetterSearch_01']) && !empty($session['heartLetterSearch_01'])) {
                $session = $session['heartLetterSearch_01'];
                if (!empty($session["searchTimeStart"])) {
                    $sql .= " and lcd >='".$session["searchTimeStart"]." 00:00:00' ";
                }
                if (!empty($session["searchTimeEnd"])) {
                    $sql .= " and lcd <='".$session["searchTimeEnd"]." 23:59:59' ";
                }
            }
            $rows = Yii::app()->db->createCommand()->select("*")->from("hr_letter")
                ->where("employee_id=:id and state=4 $sql",array(":id"=>$index))->queryAll();
            $this->letterList = $rows?$rows:array();
            return true;
        }else{
            return false;
        }
	}

	public function getTable(){
	    $html="<table class='table table-bordered table-hover table-striped'><thead><tr>";
	    $html.="<th width='15%'>".Yii::t('fete','apply for time')."</th>";
	    $html.="<th width='15%'>".Yii::t('contract','Audit Date')."</th>";
	    $html.="<th width='10%'>".Yii::t('queue','Type')."</th>";
	    $html.="<th>".Yii::t('queue','Subject')."</th>";
	    $html.="<th width='28%'>".Yii::t("contract","three_four")."</th>";
        if(Yii::app()->user->validRWFunction('HL03')){
            $html.="<th width='1%'>&nbsp;</th>";
        }
	    $html.="</tr></thead><tbody>";
        $sum = 0;
	    if(!empty($this->letterList)){
	        foreach ($this->letterList as $row){
                $sum += $row["letter_num"];
	            $html.="<tr>";
                $html.="<td>".$row["lcd"]."</td>";
                $html.="<td>".$row["lud"]."</td>";
                $html.="<td>".HeartLetterForm::getLetterTypeList($row["letter_type"],true)."</td>";
                $html.="<td>".$row["letter_title"]."</td>";
                $html.="<td>".HeartLetterAuditForm::getLetterNumToIcon($row["letter_num"])."</td>";
                if(Yii::app()->user->validRWFunction('HL03')){
                    $html.="<td>";
                    $html.=TbHtml::button("<span class='fa fa-reply'></span>".Yii::t("contract","send back"),array(
                        'submit'=>Yii::app()->createUrl('heartLetterSearch/back',array("index"=>$row["id"]))
                    ));
                    $html.="</td>";
                }
	            $html.="</tr>";
            }
        }
	    $html.="</tbody><tfoot><tr>";
	    $html.="<td colspan='4' class='text-right'>".Yii::t("contract","review number")."</td>";
        if(Yii::app()->user->validRWFunction('HL03')){
            $html.="<td colspan='2' class='text-center'><b class='fa-lg'>".$sum."分</b></td>";
        }else{
            $html.="<td class='text-center'><b class='fa-lg'>".$sum."分</b></td>";
        }
	    $html.="</tr></tfoot></table>";
	    return $html;
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
        $sql = "update hr_letter set
							state = :state, 
							luu = :luu
						where id = :id and state=4
						";
		if (empty($sql)) return false;

        $city = Yii::app()->user->city();
        $uid = Yii::app()->user->id;//ZR06

        $command=$connection->createCommand($sql);
        if (strpos($sql,':id')!==false)
            $command->bindParam(':id',$this->id,PDO::PARAM_INT);
        //employee_id,city,state,letter_id,letter_type,letter_title,letter_body,letter_num,letter_reply,lcd
        if (strpos($sql,':state')!==false)
            $command->bindParam(':state',$this->state,PDO::PARAM_STR);
        if (strpos($sql,':letter_type')!==false)
            $command->bindParam(':letter_type',$this->letter_type,PDO::PARAM_STR);
        if (strpos($sql,':letter_num')!==false)
            $command->bindParam(':letter_num',$this->letter_num,PDO::PARAM_STR);
        if (strpos($sql,':letter_reply')!==false)
            $command->bindParam(':letter_reply',$this->letter_reply,PDO::PARAM_STR);

        if (strpos($sql,':luu')!==false)
            $command->bindParam(':luu',$uid,PDO::PARAM_STR);
        $command->execute();
		return true;
	}

	//判斷輸入框能否修改
	public function getInputBool(){
        if($this->scenario == "view"||!empty($this->state)){
            return true;
        }
        return false;
    }
}
