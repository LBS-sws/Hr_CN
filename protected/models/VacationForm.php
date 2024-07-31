<?php

class VacationForm extends CFormModel
{
	public $id=0;
	public $name;
	public $log_bool;
	public $max_log=0;
	public $sub_bool;
	public $sub_multiple=0;
	public $city;
    public $only;
    public $ass_id_name;
    public $ass_id;
    public $ass_bool;
    public $z_display=1; //1:显示  0：隐藏
    public $vaca_type;//休假類型
    public $remark;//請假類型說明

	public function attributeLabels()
	{
		return array(
            'name'=>Yii::t('fete','Vacation Name'),
            'city'=>Yii::t('contract','City'),
            //'log_bool'=>Yii::t('fete','or max number of days'),
            //'max_log'=>Yii::t('fete','most number of days'),
            'log_bool'=>Yii::t('contract','Holiday rules'),
            'sub_bool'=>Yii::t('fete','Whether to deduct salary'),
            'sub_multiple'=>Yii::t('fete','deduct multiple'),
            'only'=>Yii::t('fete','Scope of application'),
            'vaca_type'=>Yii::t('fete','Vacation Type'),
            'ass_id_name'=>Yii::t('contract','associated config'),
            'ass_bool'=>Yii::t('contract','associated bool'),
            'remark'=>Yii::t('contract','Vacation Type Remark'),
            'z_display'=>Yii::t('contract','display'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id, name, z_display, remark,log_bool,max_log,sub_bool,city,sub_multiple,only,vaca_type,ass_id_name,ass_id,ass_bool','safe'),
            array('name','required'),
            array('city','required'),
            array('ass_bool','required'),
            array('vaca_type','required'),
            array('only','required'),
			array('name','validateName'),
			array('name','validateSub'),
            array('max_log','validateLog'),
            array('ass_bool','validateAss'),
		);
	}

	public function validateName($attribute, $params){
        $city = $this->city;
        $id = -1;
        if(!empty($this->id)){
            $id = $this->id;
        }
        $rows = Yii::app()->db->createCommand()->select("id")->from("hr_vacation")
            ->where('name=:name and city=:city and id!=:id',
                array(':name'=>$this->name,':id'=>$id,':city'=>$city))->queryAll();
        if(count($rows)>0){
            $message = Yii::t('contract','Reward Name'). Yii::t('contract',' can not repeat');
            $this->addError($attribute,$message);
        }
	}

	private function resetArr($arr){
	    $unArr = $arr;
	    unset($unArr["other"]);
	    if (empty($unArr)){
	        return $arr;
        }else{
            ksort($unArr);
            $unArr["other"] = $arr["other"];
            return $unArr;
        }
    }

	public function validateLog($attribute, $params){
        if($this->log_bool == 1){
            if(is_array($this->max_log)){
                $arr = array();
                foreach ($this->max_log as $list){
                    if($list["monthLong"] != "other"){
                        if(empty($list["monthLong"])||!is_numeric($list["monthLong"])||floatval($list["monthLong"])!=intval($list["monthLong"])||$list["monthLong"]<0){
                            $message = Yii::t('contract','Holiday monthLong'). Yii::t('contract',' can not be empty');
                            $this->addError($attribute,$message);
                            return false;
                        }
                    }
                    if($list["dayNum"]===""||!is_numeric($list["dayNum"])||floatval($list["dayNum"])!=intval($list["dayNum"])||$list["dayNum"]<0){
                        $message = Yii::t('contract','Holiday dayNum'). Yii::t('contract',' can not be empty');
                        $this->addError($attribute,$message);
                        return false;
                    }
                    $arr[$list["monthLong"]] = array("monthLong"=>$list["monthLong"],"dayNum"=>$list["dayNum"]);
                }

                $arr = $this->resetArr($arr);
                $this->max_log = json_encode($arr);
            }else{
                $message = Yii::t('contract','Holiday rules'). Yii::t('contract',' can not be empty');
                $this->addError($attribute,$message);
                return false;
            }
        }else{
            $this->log_bool = 0;
            $this->max_log = '';
        }
	}

	public function validateAss($attribute, $params){
        if($this->ass_bool == 1){
            if (empty($this->ass_id)){
                $message = Yii::t('contract','associated config'). Yii::t('contract',' can not be empty');
                $this->addError($attribute,$message);
                return false;
            }
            $idList = explode(",",$this->ass_id);
            $idArr = array();
            $nameArr = array();
            foreach ($idList as $id){
                if(in_array($id,$idArr)){
                    continue;
                }
                $row = Yii::app()->db->createCommand()->select("id,name")->from("hr_vacation")
                    ->where('id=:id and log_bool=1',array(':id'=>$id))->queryRow();
                if($row){
                    $idArr[] = $row["id"];
                    $nameArr[] = $row["name"];
                }else{
                    $message = Yii::t('contract','associated config'). Yii::t('contract',' not exist');
                    $this->addError($attribute,$message);
                    return false;
                }
            }
            $this->ass_id = implode(",",$idArr);
            $this->ass_id_name = implode(",",$nameArr);
        }else{
            $this->ass_bool = 0;
            $this->ass_id = '';
            $this->ass_id_name = '';
        }
	}

	public function getAssociatedList(){
	    $arr = array();
        $rows = Yii::app()->db->createCommand()->select("id,name")->from("hr_vacation")
            ->where('id!=:id and log_bool=1',array(':id'=>$this->id))->queryAll();
        if($rows){
            foreach ($rows as $row){
                $arr[$row["id"]] = $row["name"];
            }
        }
        return $arr;
    }

	public function validateSub($attribute, $params){
        if($this->sub_bool == 1 && empty($this->sub_multiple)){
            $message = Yii::t('fete','deduct multiple'). Yii::t('contract',' can not be empty');
            $this->addError($attribute,$message);
        }
	}

	public function retrieveData($index) {
        $city_allow = Yii::app()->user->city_allow();
		$rows = Yii::app()->db->createCommand()->select("*")
            ->from("hr_vacation")->where("id=:id and (city in ($city_allow) OR only='default') ",array(":id"=>$index))->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
                $this->id = $row['id'];
                $this->name = $row['name'];
                $this->log_bool = $row['log_bool'];
                $this->max_log = $row['log_bool'] == 1?json_decode($row['max_log'],true):array();
                $this->sub_bool = $row['sub_bool'];
                $this->sub_multiple = $row['sub_multiple'];
                $this->city = $row['city'];
                $this->only = $row['only'];
                $this->vaca_type = $row['vaca_type'];
                $this->ass_bool = $row['ass_bool'];
                $this->ass_id = $row['ass_id'];
                $this->ass_id_name = $row['ass_id_name'];
                $this->remark = $row['remark'];
                $this->z_display = $row['z_display'];
                break;
			}
		}
		return true;
	}

    //根據id獲取請假類型
    public function getVacationNameToId($id){
        $rows = Yii::app()->db->createCommand()->select("name")
            ->from("hr_vacation")->where("id=:id",array(":id"=>$id))->queryRow();
        if($rows){
            return $rows["name"];
        }else{
            return $id;
        }
    }

    //根據id獲取請假類型
    public function getVacaTypeLIst(){
/*        $arr = array(
            "E"=>Yii::t("fete","annual leave"),
            "A"=>Yii::t("fete","Overtime, special accommodation"),
            "B"=>Yii::t("fete","Wedding leave, funeral leave, nursing leave, maternity leave, late childbirth, breast-feeding leave"),
            "C"=>Yii::t("fete","Prenatal leave, sick leave"),
            "D"=>Yii::t("fete","Private affair leave")
        );*/
        $arr = array();
        $rows = Yii::app()->db->createCommand()->select("vaca_code,vaca_name")
            ->from("hr_vacation_type")->order("vaca_code asc")->queryAll();
        if($rows){
            foreach ($rows as $row){
                $arr[$row["vaca_code"]] = $row["vaca_name"];
            }
        }
        return $arr;
    }

    public function getTrHtml($num,$bool=false,$list=array()){
        if(empty($list)){
            $list = array(
                "monthLong"=>"",
                "dayNum"=>""
            );
        }
        $className = get_class($this);
        $html="";
        if($num === "other"){
            $html.="<tr id='otherTr'><td class='text-center'>";
            $html.=TbHtml::hiddenField($className."[max_log][$num][monthLong]",$list["monthLong"]);
            $html.=Yii::t("contract","Other")."</td>";
        }else{
            $html.="<tr><td>".TbHtml::numberField($className."[max_log][$num][monthLong]",$list["monthLong"],array("class"=>"form-control","readonly"=>$bool))."</td>";
        }
        $html.="<td>".TbHtml::numberField($className."[max_log][$num][dayNum]",$list["dayNum"],array("class"=>"form-control","readonly"=>$bool))."</td>";
        if(!$bool){
            if($num === "other"){
                $html.="<td>&nbsp;</td>";
            }else{
                $html.="<td>".TbHtml::button(Yii::t("misc","Delete"),array("class"=>"btn btn-danger delRule"))."</td>";
            }
        }
        $html.="</tr>";
        return $html;
    }

    //假期規則
    public function parentTable(){
        $bool = $this->isReadonly();
        $num = is_array($this->max_log)?count($this->max_log):0;
        $html = "<table class='table table-bordered table-striped' id='ruleTable'><thead><tr>";
        $html.="<th width='50%'>".Yii::t("contract","Holiday monthLong")."</th>";
        $html.="<th width='50%'>".Yii::t("contract","Holiday dayNum")."</th>";
        if (!$bool){
            $html.="<th>&nbsp;</th>";
        }
        $html.="</tr></thead><tbody data-num='$num'>";
        $otherList = array("monthLong"=>"other","dayNum"=>"");
        if($this->log_bool == 1){
            if(is_array($this->max_log)){
                $num = 0;
                foreach ($this->max_log as $key => $list){
                    if($key==="other"){
                        $otherList = $list;
                    }else{
                        $html.=$this->getTrHtml($num,$bool,$list);
                    }
                    $num++;
                }
            }
        }
        $html.=$this->getTrHtml("other",$bool,$otherList);
        $html.= "</tbody>";
        if(!$bool){
            $html.="<tfoot><tr><th colspan='2'>&nbsp;</th><th class='text-right'>";
            $html.=TbHtml::button(Yii::t("misc","Add"),array("class"=>"btn btn-primary","id"=>"addRule"));
            $html.="</th></tr></tfoot>";
        }
        $html.="</table>";
        echo $html;
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
		if($this->rwFunction()){
            switch ($this->scenario) {
                case 'delete':
                    $sql = "delete from hr_vacation where id = :id";
                    break;
                case 'new':
                    $sql = "insert into hr_vacation(
							name,remark,z_display,log_bool,max_log, sub_bool, sub_multiple, vaca_type, city, ass_id_name, ass_id, ass_bool, only, lcu
						) values (
							:name,:remark,:z_display,:log_bool,:max_log, :sub_bool, :sub_multiple, :vaca_type, :city, :ass_id_name, :ass_id, :ass_bool, :only, :lcu
						)";
                    break;
                case 'edit':
                    $sql = "update hr_vacation set
							name = :name, 
							remark = :remark, 
							z_display = :z_display, 
							log_bool = :log_bool, 
							vaca_type = :vaca_type, 
							max_log = :max_log, 
							sub_bool = :sub_bool, 
							city = :city, 
							ass_id_name = :ass_id_name, 
							ass_id = :ass_id, 
							ass_bool = :ass_bool, 
							only = :only, 
							sub_multiple = :sub_multiple, 
							luu = :luu
						where id = :id
						";
                    break;
            }
        }else{
            $sql = "update hr_vacation set
							remark = :remark, 
							luu = :luu
						where id = :id
						";
        }
		if (empty($sql)) return false;

        $city = Yii::app()->user->city();
        $uid = Yii::app()->user->id;

        $command=$connection->createCommand($sql);
        if (strpos($sql,':id')!==false)
            $command->bindParam(':id',$this->id,PDO::PARAM_INT);
        //log_bool,max_log,sub_bool,sub_multiple
        if (strpos($sql,':name')!==false)
            $command->bindParam(':name',$this->name,PDO::PARAM_STR);
        if (strpos($sql,':vaca_type')!==false)
            $command->bindParam(':vaca_type',$this->vaca_type,PDO::PARAM_STR);
        if (strpos($sql,':log_bool')!==false)
            $command->bindParam(':log_bool',$this->log_bool,PDO::PARAM_STR);
        if (strpos($sql,':max_log')!==false)
            $command->bindParam(':max_log',$this->max_log,PDO::PARAM_STR);
        if (strpos($sql,':sub_bool')!==false)
            $command->bindParam(':sub_bool',$this->sub_bool,PDO::PARAM_STR);
        if (strpos($sql,':sub_multiple')!==false)
            $command->bindParam(':sub_multiple',$this->sub_multiple,PDO::PARAM_STR);
        if (strpos($sql,':ass_id_name')!==false)
            $command->bindParam(':ass_id_name',$this->ass_id_name,PDO::PARAM_STR);
        if (strpos($sql,':ass_id')!==false)
            $command->bindParam(':ass_id',$this->ass_id,PDO::PARAM_STR);
        if (strpos($sql,':ass_bool')!==false)
            $command->bindParam(':ass_bool',$this->ass_bool,PDO::PARAM_STR);
        if (strpos($sql,':z_display')!==false)
            $command->bindParam(':z_display',$this->z_display,PDO::PARAM_INT);
        if (strpos($sql,':remark')!==false)
            $command->bindParam(':remark',$this->remark,PDO::PARAM_STR);

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

	public function isReadonly(){
        return $this->scenario=='view'||!$this->rwFunction();
    }

    public function rwFunction(){
	    return Yii::app()->user->validRWFunction('ZC12');
    }
}
