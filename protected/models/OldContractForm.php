<?php

/**
 * UserForm class.
 * UserForm is the data structure for keeping
 * user form data. It is used by the 'user' action of 'SiteController'.
 */
class OldContractForm extends CFormModel
{
	/* User Fields */
	public $id;
	public $old_type;
	public $bool = false;//是否特別標註本條數據
	public $his_id=0;//本條數據的id
	public $update=false;//修改類型 false無法修改  1：檢查修改  2：下載

	public function attributeLabels()
	{
		return array(
            'id'=>Yii::t('contract','ID'),
            'name'=>Yii::t('contract','Employee Name'),
            'code'=>Yii::t('contract','Employee Code'),
            'phone'=>Yii::t('contract','Employee Phone'),
            'position'=>Yii::t('contract','Position'),
            'company_id'=>Yii::t('contract','Company Name'),
            'contract_id'=>Yii::t('contract','Contract Name'),
            'status'=>Yii::t('contract','Status'),
            'city'=>Yii::t('contract','City'),
            'city_name'=>Yii::t('contract','City'),
            'entry_time'=>Yii::t('contract','Entry Time'),

            'old_type'=>Yii::t('contract','Status'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			//array('id, position, leave_reason, remarks, email, staff_type, leader','safe'),
            array('id','safe'),
			array('id','required'),
			array('id','validateId'),
        );
	}

	public function validateId($attribute, $params){
        $nowList = Yii::app()->db->createCommand()->select("id")->from("hr_employee")
            ->where("id=:id and staff_status=0",array(":id"=>$this->id))->queryRow();
        if($nowList){
            $row = Yii::app()->db->createCommand()->select("id")->from("hr_sign_contract")
                ->where("employee_id=:id and status_type != 3",array(":id"=>$this->id))->queryRow();
            if($row){
                $this->old_type = 1;//未有合同
            }else{
                $this->old_type = 2;//已有合同
            }
        }else{
            $message = "員工不存在";
            $this->addError($attribute,$message);
        }
    }

	public function validateUnsigned($his_id){
	    $this->his_id = $his_id;
        $nowList = Yii::app()->db->createCommand()->select("id")->from("hr_employee")
            ->where("id=:id and staff_status=0",array(":id"=>$this->id))->queryRow();
        if($nowList){
            $row = Yii::app()->db->createCommand()->select("id")->from("hr_sign_contract")
                ->where("employee_id=:id and history_id = :his_id",array(":id"=>$this->id,":his_id"=>$his_id))->queryRow();
            if($row){
                $message = "數據異常，请与管理员联系";
                $this->addError("id",$message);
                return false;
            }else{
                $rows = Yii::app()->db->createCommand()->select("id,contract_id")->from("hr_employee_operate")
                    ->where("employee_id=:id and opr_type='contract'",array(":id"=>$this->id))->order("lcd asc")->queryAll();
                if(count($rows)>0){
                    $row = $rows[0];
                    if ($row["id"]==$his_id){
                        $this->old_type = 0;
                    }else{
                        $row = Yii::app()->db->createCommand()->select("retire")->from("hr_contract")
                            ->where("id=:id",array(":id"=>$row["contract_id"]))->queryRow();
                        if($row){
                            $this->old_type = $row["retire"] == 1?2:1;
                        }else{
                            $this->old_type = 1;
                        }
                    }
                }else{
                    $this->old_type = 0;
                }
            }
        }else{
            $message = "員工不存在";
            $this->addError("id",$message);
            return false;
        }
        return true;
    }

	public function validateRecall($his_id){
	    $this->his_id = $his_id;
        $nowList = Yii::app()->db->createCommand()->select("id")->from("hr_employee")
            ->where("id=:id and staff_status=0",array(":id"=>$this->id))->queryRow();
        if($nowList){
            $row = Yii::app()->db->createCommand()->select("id")->from("hr_sign_contract")
                ->where("employee_id=:id and history_id = :his_id and status_type not in (2,3)",array(":id"=>$this->id,":his_id"=>$his_id))->queryRow();
            if($row){
                return true;
            }else{
                $message = "數據異常，请与管理员联系";
                $this->addError("id",$message);
                return false;
            }
        }else{
            $message = "員工不存在";
            $this->addError("id",$message);
            return false;
        }
    }


    public function printTableBody($staff_id,$bool=false,$his_id=0,$update=false){
	    $this->id = $staff_id;
        $this->bool = $bool;
        $this->his_id = $his_id;
        $this->update = $update;
        $html = "";
        $nowList = Yii::app()->db->createCommand()->select("*")->from("hr_employee")
            ->where("id=:id",array(":id"=>$staff_id))->queryRow();
        $rows = Yii::app()->db->createCommand()->select("*")->from("hr_employee_operate")
            ->where("employee_id=:id and opr_type='contract'",array(":id"=>$staff_id))->order("lcd desc")->queryAll();
        $nowList["employee_id"] =$staff_id;
        $nowList["show_status"] =count($rows)>0?Yii::t("contract","contract renewal"):Yii::t("contract","new contract");
        $html.=$this->tableTr($nowList,true);
        if(count($rows)>0){
            $i = 0;
            foreach ($rows as $row){
                $i++;
                $row["show_status"] =count($rows)==$i?Yii::t("contract","new contract"):Yii::t("contract","contract renewal");
                $html.=$this->tableTr($row,false);
            }
        }
        return $html;
    }

    protected function tableTr($arr,$now=false){
        if($now){
            $arr["his_id"] = 0;
        }else{
            $arr["his_id"] = $arr["id"];
        }
        $arr["end_time"] = $arr["fix_time"] == "fixation"?$arr["end_time"]:"";
        if($this->bool){
            $html = "<tr";
            $html.=$this->his_id == $arr["his_id"]?" class='danger' ":"";
            $html.=">";
        }else{
            $html = "<tr>";
        }
        $html.="<td>".$arr["show_status"]."</td>";
        $html.="<td>".$arr["code"]."</td>";
        $html.="<td>".$arr["name"]."</td>";
        $html.="<td>".$arr["user_card"]."</td>";
        $html.="<td>".DeptForm::getDeptToid($arr['department'])."</td>";
        $html.="<td>".DeptForm::getDeptToid($arr['position'])."</td>";
        $html.="<td>".CompanyForm::getCompanyToId($arr['company_id'])["name"]."</td>";
        $html.="<td>".Yii::t("contract",$arr["fix_time"])."</td>";
        $html.="<td>".$arr["start_time"]."</td>";
        $html.="<td>".$arr["end_time"]."</td>";
        switch ($this->update){
            case 1://檢查合同狀態（專屬）
                $html.=$this->updateOne($arr);
                break;
            case 2://提供下載功能
                $html.=$this->updateTwo($arr);
                break;
        }

        return $html."</tr>";
    }

    protected function updateOne($arr){
        $html = "";
        $row = Yii::app()->db->createCommand()->select("status_type")->from("hr_sign_contract")
            ->where("employee_id=:id and history_id=:his_id",array(":id"=>$arr["employee_id"],":his_id"=>$arr["his_id"]))->queryRow();
        if($row){
            switch ($row["status_type"]){
                case 2://合同已寄出
                    $html.="<td>".Yii::t("contract","contract has been sent")."</td>";
                    $html.="<td>&nbsp;</td>";
                    break;
                case 3://合同已签收
                    $html.="<td>".Yii::t("contract","contract has been signed")."</td>";
                    $html.="<td>&nbsp;</td>";
                    break;
                case 5://合同已签收(不顯示)
                    $html.="<td>".Yii::t("contract","contract has been signed")."</td>";
                    $html.="<td>";
                    $html.=TbHtml::button(Yii::t('contract','recall'), array('submit'=>Yii::app()->createUrl('oldContract/recall',array("index"=>$arr["his_id"]))));
                    $html.="</td>";
                    break;
                default://
                    $html.="<td>".Yii::t("contract","To be sent under contract")."</td>";
                    $html.="<td>";
                    $html.=TbHtml::button(Yii::t('contract','recall'), array('submit'=>Yii::app()->createUrl('oldContract/recall',array("index"=>$arr["his_id"]))));
                    $html.="</td>";
                    break;
            }
        }else{
            $html.="<td>";
            $html.=Yii::t("contract","Contract not received");
            $html.=TbHtml::button(Yii::t('contract','received'), array('submit'=>Yii::app()->createUrl('oldContract/have',array("index"=>$arr["his_id"]))));

            $html.="</td>";
            $html.="<td>";
            $html.=TbHtml::button(Yii::t('contract','Make up'), array('submit'=>Yii::app()->createUrl('oldContract/unsigned',array("index"=>$arr["his_id"]))));
            $html.="</td>";
        }
        return $html;
    }

    protected function updateTwo($arr){
        $html="<td>";
        if($this->his_id == $arr["his_id"]){
            $html.=TbHtml::button(Yii::t('contract','Down'), array('submit'=>Yii::app()->createUrl('signContract/down',array("index"=>$arr["his_id"]))));
        }
        $html.="</td>";

        return $html;
    }

	public function retrieveData($index)
	{
        $row = Yii::app()->db->createCommand()->select("a.id,b.old_type")->from("hr_employee a")
            ->leftJoin("hr_check_staff b","a.id = b.employee_id")
            ->where("a.id=:id and a.staff_status=0",array(":id"=>$index))->queryRow();
		if ($row){
            $this->id = $index;
            switch ($row["old_type"]){
                case 1:
                    $this->old_type = Yii::t("contract","No contract");
                    break;
                case 2:
                    $this->old_type = Yii::t("contract","Existing contract");
                    break;
                default:
                    $this->old_type = Yii::t("contract","unchecked");
            }
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

    public function saveStaff()
	{
        $row = Yii::app()->db->createCommand()->select("id")->from("hr_check_staff")
            ->where("employee_id=:id",array(":id"=>$this->id))->queryRow();
        if($row){
            Yii::app()->db->createCommand()->update('hr_check_staff', array(
                'old_type'=>$this->old_type
            ), 'id=:id', array(':id'=>$row["id"]));
        }else{
            Yii::app()->db->createCommand()->insert("hr_check_staff", array(
                'old_type'=>$this->old_type,
                'employee_id'=>$this->id,
            ));
        }
	}

    public function saveUnsigned(){
        Yii::app()->db->createCommand()->insert('hr_sign_contract',array(
            'sign_type'=>$this->old_type,
            'employee_id'=>$this->id,
            'history_id'=>$this->his_id,
            'lcu'=>Yii::app()->user->id,
        ));
	}

    public function saveRecall(){
        Yii::app()->db->createCommand()->delete('hr_sign_contract', "employee_id=:id and history_id = :his_id",array(":id"=>$this->id,":his_id"=>$this->his_id));
	}

    public function saveHasContract(){
        Yii::app()->db->createCommand()->insert('hr_sign_contract',array(
            'sign_type'=>$this->old_type,
            'employee_id'=>$this->id,
            'history_id'=>$this->his_id,
            'lcu'=>Yii::app()->user->id,
            'status_type'=>5,
            'remark'=>"检查员工旧合同专属",
        ));
	}


}
