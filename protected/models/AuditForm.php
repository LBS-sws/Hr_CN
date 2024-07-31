<?php

/**
 * UserForm class.
 * UserForm is the data structure for keeping
 * user form data. It is used by the 'user' action of 'SiteController'.
 */
class AuditForm extends StaffForm
{

	/**
     *
	 * Declares the validation rules.
	 */
	public function rulesEx()
	{
        return array(
            array('ject_remark','required',"on"=>"reject"),
        );
	}

    public function validateID($attribute, $params){
        $city_allow = Yii::app()->user->city_allow();
        $row = Yii::app()->db->createCommand()->select("city,table_type")->from("hr_employee")
            ->where("id=:id and city in ($city_allow) and staff_status=2",array(':id'=>$this->id))->queryRow();
        if($row){
            $this->city = $row["city"];
            $this->table_type = $row["table_type"];
        }else{
            $message = "审核单不存在或已审核，请刷新重试";
            $this->addError($attribute,$message);
        }
    }

    public function retrieveData($index)
    {
        $suffix = Yii::app()->params['envSuffix'];
        $allow_city = Yii::app()->user->city_allow();
        $row = Yii::app()->db->createCommand()->select("*,docman$suffix.countdoc('EMPLOY',id) as employdoc")->from("hr_employee")
            ->where("id=:id and city in ({$allow_city}) and staff_status in (2,4)", array(':id'=>$index))->queryRow();
        if ($row){
            $this->no_of_attm['employ'] = $row['employdoc'];
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

    protected function saveStaff(&$connection)
    {
        $city = Yii::app()->user->city();
        $allow_city = Yii::app()->user->city_allow();
        $uid = Yii::app()->user->id;
        $update_html="";
        switch ($this->scenario) {
            case 'reject':
                $update_html = "<span class='text-danger'>审核被拒绝</span><br/>";
                $update_html.= "<span>{$this->ject_remark}</span>";
                $connection->createCommand()->update("hr_employee", array(
                    "staff_status"=>3,
                    "ject_remark"=>$this->ject_remark,
                    "luu"=>$uid,
                ), "id=:id and city in ({$allow_city})", array(":id" => $this->id));
                break;
            case 'audit':
                $update_html = "<span class='text-success'>审核通过</span>";
                $connection->createCommand()->update("hr_employee", array(
                    "staff_status"=>$this->table_type==1?4:1,
                    "lcd"=>date('Y-m-d H:i:s'),
                    "luu"=>$uid,
                ), "id=:id and city in ({$allow_city})", array(":id" => $this->id));
                break;
        }

        //記錄
        if($this->table_type==1){
            Yii::app()->db->createCommand()->insert('hr_employee_history', array(
                "employee_id"=>$this->id,
                "status"=>$this->scenario,
                "lcu"=>$uid,
                "lcd"=>date('Y-m-d H:i:s'),
            ));
            //判斷是否需要生成簽署合同
            $this->signContract();
        }else{
            Yii::app()->db->createCommand()->insert("hr_table_history", array(
                "table_name"=>"hr_employee",
                "table_id"=>$this->id,
                "lcu"=>$uid,
                "update_type"=>1,
                "update_html"=>$update_html,
            ));
        }
        $this->sendEmail();


        if($this->getScenario() == "audit"){
            //U系统同步
            StaffForm::sendCurl($this->id,"new");
        }
    }

    private function signContract(){
        $signedContractType = Yii::app()->db->createCommand()->select("set_value")->from("hr_setting")
            ->where('set_name="signedContractType" and set_city=:city',array(":city"=>$this->city))->queryScalar();
        if(empty($signedContractType)&&$this->getScenario() == "audit"){
            Yii::app()->db->createCommand()->insert('hr_sign_contract',array(
                'employee_id'=>$this->id,
                'status_type'=>0,
                'lcu'=>Yii::app()->user->id,
            ));
        }
    }

    protected function sendEmail(){
        $row = Yii::app()->db->createCommand()->select("*")->from("hr_employee")
            ->where("id=:id",array(":id"=>$this->id))->queryRow();
        if($row){
            if ($this->getScenario() == "audit"){
                $description="员工审核 - ".$row["name"]."（通过）";
                $subject="员工审核 - ".$row["name"]."（通过）";
            }else{
                $description="员工审核 - ".$row["name"]."（拒绝）";
                $subject="员工审核 - ".$row["name"]."（拒绝）";
            }
            $message="<p>员工类型：".StaffFun::getTableTypeNameForID($row["table_type"])."</p>";
            $message.="<p>员工编号：".$row["code"]."</p>";
            $message.="<p>员工姓名：".$row["name"]."</p>";
            $message.="<p>员工所在城市：".CGeneral::getCityName($row["city"])."</p>";
            $message.="<p>员工入职日期：".$row["entry_time"]."</p>";
            $message.="<p>员工职位：".DeptForm::getDeptToId($row["position"])."</p>";
            $message.="<p>员工合同归属：".StaffFun::getCompanyNameToID($row["staff_id"])."</p>";
            $message.="<p>员工归属：".StaffFun::getCompanyNameToID($row["company_id"])."</p>";
            $message.="<p>审核日期：".date('Y-m-d H:i:s')."</p>";
            if ($this->getScenario() == "reject"){
                $message.="<p>拒绝原因：".$this->ject_remark."</p>";
            }
            $email = new Email($subject,$message,$description);
            $email->addEmailToLcu($row["lcu"]);
            $email->sent();
        }
    }
}
