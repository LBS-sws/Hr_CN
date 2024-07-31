<?php

/**
 * UserForm class.
 * UserForm is the data structure for keeping
 * user form data. It is used by the 'user' action of 'SiteController'.
 */
class EmployeeForm extends StaffForm
{
	/**
	 * Declares the validation rules.
	 */
	public function rulesEx()
	{
		return array();
	}

	public function validateName($attribute, $params){
    }

    public function retrieveData($index)
    {
        $suffix = Yii::app()->params['envSuffix'];
        $allow_city = Yii::app()->user->city_allow();
        $row = Yii::app()->db->createCommand()->select("*,
        docman$suffix.countdoc('SIGNC',id) as signcdoc,
        docman$suffix.countdoc('EMPLOY',id) as employdoc")->from("hr_employee")
            ->where("id=:id and city in ({$allow_city}) and staff_status=0", array(':id'=>$index))->queryRow();
        if ($row){
            $this->no_of_attm['employ'] = $row['employdoc'];
            $this->no_of_attm['signc'] = $row['signcdoc'];
            $arr = $this->getMyAttr();
            foreach ($arr as $key => $type){
                switch ($type){
                    case 1://原值
                        $value = $row[$key];
                        $value = $value===""?null:$value;
                        $this->$key = $value;
                        break;
                    case 2://日期
                        $this->$key = empty($row[$key])?null:General::toDate($row[$key]);
                        break;
                    case 3://数字
                        $this->$key = $row[$key]===null?null:floatval($row[$key]);
                        break;
                    case "birth_time"://年龄
                        $this->$key = isset($row["birth_time"])?StaffFun::getAgeForBirthDate($row["birth_time"]):floatval($row[$key]);
                        break;
                    default:
                }
            }
        }
        return true;
    }

    //檢查是否有補充協議
    public function staffHasAgreement(){
        $rows = Yii::app()->db->createCommand()->select()->from("hr_employee_history")
            ->where('employee_id=:employee_id and status in ("transfer","promotion","salary")', array(':employee_id'=>$this->id))->order('lcd desc')->queryAll();
        $arr = array();
        if($rows){
            foreach ($rows as $row){
                $staff_old =Yii::app()->db->createCommand()->select("city,change_city,finish")->from("hr_employee_operate")
                    ->where('id=:id', array(':id'=>$row['history_id']))->queryRow();
                if($staff_old["finish"] != 1){
                    continue;
                }
                if($row["status"] == "transfer"){
                    if($staff_old["city"] == $staff_old["change_city"]){
                        array_push($arr,$row);
                    }
                }else{
                    array_push($arr,$row);
                }
            }
        }
        return $arr;
    }

    //下载補充協議
    public function downAgreement($history_id){
        $word_url = AgreementForm::getAgreementUrl();
        if(empty($word_url)){
            throw new CHttpException(404,'協議文檔沒有配置，請與管理員聯繫');
        }else{
            $arr = $this->staffHasAgreement();
            if (empty($arr)){
                throw new CHttpException(404,'Not Find Agreement');
            }else{
                foreach ($arr as $key => $list){
                    if ($list["id"] == $history_id){
                        $staff["old"] = HistoryForm::getStaffToHistoryId($list["history_id"]);
                        if($key === 0){
                            $staff["now"] = $this->attributes;
                        }else{
                            $staff["now"] = HistoryForm::getStaffToHistoryId($arr[$key-1]["history_id"]);
                        }
                        $companyName = CompanyForm::getCompanyToId($staff["now"]["company_id"]);
                        $word = new Agreement($word_url,$this->city);

                        $word->setValue("oldDepartment",DeptForm::getDeptToId($staff["old"]["department"]));//崗位
                        $word->setValue("oldPosition",DeptForm::getDeptToId($staff["old"]["position"]));//職位
                        $word->setValue("oldWage",$staff["old"]["wage"]);//工資

                        $word->setValue("nowDepartment",DeptForm::getDeptToId($staff["now"]["department"]));//崗位
                        $word->setValue("nowPosition",DeptForm::getDeptToId($staff["now"]["position"]));//職位
                        $word->setValue("nowWage",$staff["now"]["wage"]);//工資

                        $word->setValue("companyname",$companyName["name"]);//公司名字
                        $word->setValue("staffname",$staff["now"]["name"]);//員工名字
                        $word->setValue("agreementyears",date("Y",strtotime($staff["old"]["effect_time"])));
                        $word->setValue("agreementmonth",date("m",strtotime($staff["old"]["effect_time"])));
                        $word->setValue("agreementday",date("d",strtotime($staff["old"]["effect_time"])));
                        $fileName = date("YmdHis",strtotime($list["lcd"]));
                        $word->save($fileName);
                        //協議的地址格式：upload/agreement/所在地區/協議時間.docx
                        $wordUrl = "upload/agreement/".$this->city."/".$fileName.".docx";
                        return $wordUrl;
                    }
                }
                throw new CHttpException(404,'Not Find Agreement');
            }
        }
    }

    public function validateDisplace($id){
        $suffix = Yii::app()->params['envSuffix'];
        $allow_city = Yii::app()->user->city_allow();
        $row = Yii::app()->db->createCommand()->select("id")->from("hr_employee")
            ->where("id=:id and city in ({$allow_city}) and staff_status=0", array(':id'=>$id))->queryRow();
        if($row){
            $count = Yii::app()->db->createCommand()->select("count(id)")->from("hr_employee_operate")
                ->where("employee_id=:id and city in ($allow_city)  and finish=0", array(':id'=>$id))->queryScalar();
            if($count>0){
                $this->addError("id","该员工已有变更信息，请先完成变更");
                return false;
            }
            $table_type = key_exists("table_type",$_POST)?$_POST["table_type"]:"";
            if(!in_array($table_type,array(2,3))){
                $this->addError("id","员工类型选择异常，请重试");
            }
            $this->id = $id;
            $this->table_type = $table_type;
            return true;
        }
        $this->addError("id","员工不存在，请刷新重试");
        return false;
    }

    public function saveDisplace(){
        $uid = Yii::app()->user->id;
        Yii::app()->db->createCommand()->update('hr_employee', array(
            "table_type"=>$this->table_type,
            "staff_status"=>1
        ), 'id=:id', array(':id'=>$this->id));
        //記錄
        $list=array(
            "table_name"=>"hr_employee",
            "table_id"=>$this->id,
            "lcu"=>$uid,
            "update_type"=>1,
            "update_html"=>"<span>员工转移</span>",
        );
        Yii::app()->db->createCommand()->insert("hr_table_history", $list);

        //U系统同步
        StaffForm::sendCurl($this->id,"edit");
    }
}
