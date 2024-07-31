<?php

class SupportSearchForm extends CFormModel
{
	public $id;
	public $support_code;
	public $apply_date;
	public $apply_num;
	public $apply_type;
	public $service_type;
	public $apply_end_date;
	public $apply_length=1;
	public $apply_remark;
	public $length_type=1;
	public $apply_city;
	public $city_name;
	public $apply_lcu;
	public $update_type;
	public $update_remark;
	public $employee_id;
	public $employee_name;
	public $audit_remark;
	public $tem_list;
	public $tem_s_ist;
	public $tem_str;
	public $tem_sum;
	public $sumNum=0;
	public $review_sum;
	public $status_type=1;
	public $change_num;
	public $early_remark;
	public $early_date;
	public $reject_remark;
    public $privilege;
    public $privilege_user;
	public $city="HK";

	public $position_name;
	public $service_type_name;
	public $lud;

	public function attributeLabels()
	{
        return array(
            'support_code'=>Yii::t('contract','support code'),
            'apply_city'=>Yii::t('contract','apply city'),
            'apply_date'=>Yii::t('contract','Start Time'),
            'length_type'=>Yii::t('contract','support length'),
            'apply_end_date'=>Yii::t('contract','End Time'),
            'employee_id'=>Yii::t('contract','support employee'),
            'apply_remark'=>Yii::t('contract','apply remark'),
            'review_sum'=>Yii::t('contract','review sum'),
            'status_type'=>Yii::t('contract','Status'),

            'update_remark'=>Yii::t('contract','update remark'),
            'audit_remark'=>Yii::t('contract','audit remark'),

            'early_remark9'=>Yii::t('contract','early remark'),
            'early_date9'=>Yii::t('contract','early date'),
            'early_remark'=>Yii::t('contract','early remark'),
            'early_date'=>Yii::t('contract','early date'),

            'early_remark10'=>Yii::t('contract','renewal remark'),
            'early_date10'=>Yii::t('contract','renewal date'),
            'reject_remark'=>Yii::t('contract','Rejected Remark'),
            'apply_type'=>Yii::t('queue','Type'),
            'privilege'=>Yii::t('contract','privilege'),
            'privilege_user'=>Yii::t('contract','privilege user'),
            'service_type'=>Yii::t('contract','service type'),
        );
	}
    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return array(
            array('id, early_date, early_remark','safe'),
            array('id','required'),
            array('early_date','required'),
            array('early_remark','required'),
            array('id','validateID'),
            array('early_date','validateApplyDate'),
        );
    }

    public function validateID($attribute, $params){
        $row = Yii::app()->db->createCommand()->select("*")->from("hr_apply_support")
            ->where("id=:id and status_type in (5,6,8,11) and apply_type = 2",array(":id"=>$this->id))->queryRow();
        if(!$row){
            $message = "支援单不存在，无法提前結束";
            $this->addError($attribute,$message);
        }else{
            $this->apply_date = $row["apply_date"];
            $this->apply_end_date = $row["apply_end_date"];

            $this->employee_id = $row["employee_id"];
            $this->apply_city = $row["apply_city"];
            $this->support_code = $row["support_code"];
            $this->service_type = $row["service_type"];
            $this->update_remark = $row["update_remark"];
        }
    }
    public function validateApplyDate($attribute, $params){
        if(!empty($this->early_date)){
            if(strtotime($this->early_date)<strtotime($this->apply_date)){
                $message = "提前结束时间不能小于开始时间：".$this->apply_date;
                $this->addError($attribute,$message);
            }elseif(strtotime($this->early_date)>strtotime($this->apply_end_date)){
                $message = "提前结束时间不能大于结束时间：".$this->apply_end_date;
                $this->addError($attribute,$message);
            }else{
                $this->length_type =2;
                $this->apply_length = (strtotime($this->early_date) - strtotime($this->apply_date))/86400;
                $this->apply_length ++;
                $this->update_type = 1;
                $this->update_remark .= "时间修改：".date("Y/m/d",strtotime($this->apply_date))." ~ ".date("Y/m/d",strtotime($this->apply_end_date))." 修改成 ".date("Y/m/d",strtotime($this->apply_date))." ~ ".date("Y/m/d",strtotime($this->early_date))."
";
            }
        }
    }

	public function retrieveData($index) {
        $suffix = Yii::app()->params['envSuffix'];
        $city = Yii::app()->user->city;
        $city_allow = Yii::app()->user->city_allow();
        if(Yii::app()->user->validFunction('ZR11')||Yii::app()->user->validFunction('AY02')){
            $sqlEx = " ";
            //$sqlEx = " and a.apply_city in ($city_allow) ";
        }else{
            $bindEmployee = BindingForm::getEmployeeIdToUsername();
            $sqlEx = " and a.employee_id=$bindEmployee ";
        }
        $row = Yii::app()->db->createCommand()->select("f.name as position_name,a.*,b.name as employee_name,c.name as city_name")->from("hr_apply_support a")
            ->leftJoin("hr_employee b","a.employee_id = b.id")
            ->leftJoin("hr_dept f","b.position = f.id")
            ->leftJoin("security$suffix.sec_city c","c.code = a.apply_city")
            ->where(" a.status_type != 1 $sqlEx and a.id=:id",array(":id"=>$index))->queryRow();
		if ($row) {
            $this->id = $row['id'];
            $this->support_code = $row['support_code'];
            $this->apply_city = $row['apply_city'];
            $this->city_name = $row['city_name'];
            $this->apply_date = $row['apply_date'];
            $this->apply_end_date = $row['apply_end_date'];
            $this->apply_remark = $row['apply_remark'];
            $this->status_type = $row['status_type'];

            $this->apply_type = $row['apply_type'];
            $this->service_type = $row['service_type'];
            $this->service_type_name = SupportApplyList::getServiceList($row['service_type'],true);
            $this->position_name = $row['position_name'];

            $this->employee_id = $row['employee_id'];
            $this->employee_name = $row['employee_name'];
            $this->update_type = $row['update_type'];
            $this->update_remark = $row['update_remark'];
            $this->audit_remark = $row['audit_remark'];
            $this->length_type = $row['length_type'];
            $this->apply_length = $row['apply_length'];
            $this->tem_str = $row['tem_str'];
            $this->tem_sum = $row['tem_sum'];
            $this->review_sum = $row['review_sum'];
            $this->early_date = $row['early_date'];
            $this->early_remark = $row['early_remark'];
            $this->tem_s_ist = json_decode($row['tem_s_ist'],true);

            $this->privilege = $row['privilege'];
            $this->privilege_user = $row['privilege_user'];
            $this->lud = date("Y-m-d",strtotime($row['lud']));
		}
		return true;
	}

    public function getHistoryHtml($id){
        $suffix = Yii::app()->params['envSuffix'];
        $html = '<table id="tblFlow" class="table table-bordered table-striped table-hover">';
        $html.= '<thead><tr>';
        $html.='<th>'.Yii::t("contract","Start Time").'</th>';
        $html.='<th>'.Yii::t("contract","End Time").'</th>';
        $html.='<th>'.Yii::t("contract","Operator Time").'</th>';
        $html.='<th>'.Yii::t("contract","Operator User").'</th>';
        $html.='<th>'.Yii::t("contract","Status").'</th>';
        $html.='</tr></thead><tbody>';

        $rows = Yii::app()->db->createCommand()->select("a.start_date,a.end_date,a.status_type,b.disp_name,a.lcd")->from("hr_apply_support_history a")
            ->leftJoin("security$suffix.sec_user b","a.lcu = b.username")
            ->where("a.support_id=:id",array(":id"=>$id))->order("lcd asc")->queryAll();
        if($rows){
            $arr = SupportApplyList::getStatusList();
            foreach ($rows as $row){
                $status = key_exists($row['status_type'],$arr)?$arr[$row['status_type']]:array('status'=>$row['status_type']);
                $html.="<tr>";
                $html.='<td>'.$row['start_date'].'</td>';
                $html.='<td>'.$row['end_date'].'</td>';
                $html.='<td>'.$row['lcd'].'</td>';
                $html.='<td>'.$row['disp_name'].'</td>';
                $html.='<td>'.$status['status'].'</td>';
                $html.="</tr>";
            }
        }
        $html.='</tbody></table>';

        return $html;
    }

    //是否只讀
    public function getReadonly(){
        return $this->scenario=='view'||!in_array($this->status_type,array(1,2,3,4));
    }


    public function deleteValidate(){
        $row = Yii::app()->db->createCommand()->select("*")->from("hr_apply_support")
            ->where("id=:id",array(":id"=>$this->id))->queryRow();
        if($row){
            return true;
        }else{
            return false;
        }
    }

    public function backValidate(){
        $row = Yii::app()->db->createCommand()->select("*")->from("hr_apply_support")
            ->where("id=:id and status_type = 5",array(":id"=>$this->id))->queryRow();
        if($row){
            $this->apply_date = $row["apply_date"];
            $this->apply_end_date = $row["apply_end_date"];
            $this->apply_length = $row["apply_length"];
            $this->length_type = $row["length_type"];
            return true;
        }else{
            return false;
        }
    }

    public function deleteData(){
        Yii::app()->db->createCommand()->delete('hr_apply_support', 'id=:id', array(':id'=>$this->id));
        Yii::app()->db->createCommand()->delete('hr_apply_support_history', 'support_id=:id', array(':id'=>$this->id));
    }

    public function saveData($str){
        switch ($str){
            case "early":
                Yii::app()->db->createCommand()->update('hr_apply_support', array(
                    'length_type'=>2,
                    'apply_end_date'=>$this->early_date,
                    'early_remark'=>$this->early_remark,
                    'update_type'=>$this->update_type,
                    'update_remark'=>$this->update_remark,
                    'apply_length'=>$this->apply_length
                ), 'id=:id', array(':id'=>$this->id));

                $this->setSupportHistory();//記錄操作并发送邮件
                break;
            case "back":
                Yii::app()->db->createCommand()->update('hr_apply_support', array(
                    'status_type'=>2,
                ), 'id=:id', array(':id'=>$this->id));
                Yii::app()->db->createCommand()->insert('hr_apply_support_history', array(
                    'support_id'=>$this->id,
                    'start_date'=>$this->apply_date,
                    'end_date'=>$this->apply_end_date,
                    'apply_length'=>$this->apply_length,
                    'length_type'=>$this->length_type,
                    'status_type'=>13,
                    'lcu'=>Yii::app()->user->id,
                ));
                break;
            default:
        }
    }

    private function setSupportHistory(){
        if (in_array($this->status_type,array(4,5,7,8,11,12))){
            $this->employee_name = YearDayList::getEmployeeNameToId($this->employee_id);
            $this->city_name = CGeneral::getCityName($this->apply_city);
            $email = new Email();
            $message = "支援编号:".$this->support_code."<br>";
            $message.= "服务类型:".SupportApplyList::getServiceList($this->service_type,true)."<br>";
            $message.= "支援城市:".$this->city_name."<br>";
            $message.= "申请时间:".$this->apply_date."<br>";
            $message.= "结束时间:".$this->apply_end_date."<br>";
            $message.= "支援时长:".$this->apply_length.($this->length_type==1?"个月":"天")."<br>";
            if(!empty($this->employee_name)){
                $message.= "支援员工:".$this->employee_name."<br>";
            }
            $email->setSubject("支援单（".$this->support_code."） - 已修改結束时间");
            $status_remark = $this->early_remark;
            $message.= "审核备注:$status_remark<br>";
            $email->setMessage($message);
            $email->addSupportPreEmail();
            $email->addEmailToStaffId($this->employee_id);
            $email->addEmailToPrefixAndOnlyCity("AY01",$this->apply_city);//該城市有申請權限的人收到郵件
            $email->sent();
            Yii::app()->db->createCommand()->insert('hr_apply_support_history', array(
                'support_id'=>$this->id,
                'start_date'=>$this->apply_date,
                'end_date'=>$this->early_date,
                'apply_length'=>$this->apply_length,
                'length_type'=>$this->length_type,
                'status_type'=>9,
                'status_remark'=>$status_remark,
                'lcu'=>Yii::app()->user->id,
            ));
        }
    }
}
