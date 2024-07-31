<?php

class SupportAuditForm extends CFormModel
{
    //强制刷新本文件
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
	public $city="ZY";
    public $privilege;
    public $privilege_user;

    public $bathFinish=array();

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
			array('id, support_code, privilege, privilege_user, service_type, reject_remark, apply_date,apply_end_date,apply_remark,apply_city,apply_length,length_type,audit_remark,tem_list,employee_id','safe'),
            array('apply_date','required'),
            array('apply_end_date','required'),
            array('apply_city','required'),
            array('employee_id','required','on'=>array('save','audit','support','wait')),
            array('apply_date','validateApplyDate'),
            array('privilege','validatePrivilege','on'=>array('save','audit','support')),
            array('employee_id','validateStaff','on'=>array('save','audit','support')),
            array('audit_remark','required','on'=>array('reject','wait')),
            array('tem_list','validateList','on'=>array('save','audit','support','wait')),
            array('reject_remark','required','on'=>array('reject')),
            array('audit_remark','required','on'=>array('endReply')),
            array('id','validateEndReply','on'=>array('endReply')),
            array('id','validateIDGeneral','on'=>array('undo','finish')),
            array('id','validateIDGeneral','on'=>array('reject','renewal','early'),'status_type'=>"10,9"),
            array('id','validateIDEx','on'=>array('renewal','early')),
		);
	}
    public function validatePrivilege($attribute, $params){
        $city = $this->apply_city;
        switch ($this->privilege){
            case 1://人員置換
                if(empty($this->privilege_user)){
                    $message = Yii::t('contract','privilege user').Yii::t('contract',' can not be empty');
                    $this->addError($attribute,$message);
                }else{
                    $row = Yii::app()->db->createCommand()->select("id")->from("hr_employee")
                        ->where("id=:id and city='$city'",array(":id"=>$this->privilege_user))->queryRow();
                    if(!$row){
                        $message = "置換的員工不存在!";
                        $this->addError($attribute,$message);
                    }
                }
                break;
            case 2://優先權
                $month = floatval(date("m",strtotime($this->apply_date)));
                if($month<7){
                    $startDate = date("Y/01/00", strtotime($this->apply_date));
                }else{
                    $startDate = date("Y/07/00", strtotime($this->apply_date));
                }
                //$startDate = date("Y/m/31", strtotime($this->apply_date." - 6 month"));
                $row = Yii::app()->db->createCommand()->select("support_code,apply_date,apply_end_date")->from("hr_apply_support")
                    ->where("date_format(apply_end_date,'%Y/%m/%d')>:apply_date and apply_city='$city' and status_type!=1 and privilege=2 and id!=:id",
                        array(":apply_date"=>$startDate,":id"=>$this->id))->queryRow();
                if($row){
                    $message = "使用优先权必须相隔六个月。重复支援编号：".$row["support_code"]."（".$row["apply_date"]." ~ ".$row["apply_end_date"]."）";
                    $this->addError($attribute,$message);
                }
                break;
            default:
                $this->privilege = 0;
        }
    }

    //驗證續期及提前結束
    public function validateEndReply($attribute, $params){
        $sql = '';
        if(!empty($this->id)&&is_numeric($this->id)){
            $sql = " and a.id !=".$this->id;
        }
        $city = empty($this->city)?"ZY":$this->city;
        $rows = Yii::app()->db->createCommand()->select("a.id,a.code,a.name")->from("hr_employee a")
            ->leftJoin("hr_dept b","a.position = b.id")
            ->where("a.city = '$city' and b.dept_class = 'Technician' and staff_status = 0")->queryAll();
        if($rows){
            foreach ($rows as $row){
                $boolList = $this->SupportEmployeeToSates($row['id'],$this->id);
                if($boolList["bool"] === false){
                    $message = "该时间段含有待支援的员工，无法完成。員工(".$row["name"].")";
                    $this->addError($attribute,$message);
                    return false;
                }
            }
        }
        $startDate = empty($this->apply_date)?date("Y/m/d"):date("Y/m/d",strtotime($this->apply_date));
        $endDate = empty($this->apply_end_date)?date("Y/m/d", strtotime("+1 month")):date("Y/m/d",strtotime($this->apply_end_date));
        //添加置換員工
        $rows = Yii::app()->db->createCommand()->select("b.id,b.code,b.name")->from("hr_apply_support a")
            ->leftJoin("hr_employee b","a.privilege_user = b.id")
            ->where("a.status_type != 1 and a.privilege = 1 $sql and ((date_format(a.apply_date,'%Y/%m/%d')>='$startDate' and date_format(a.apply_date,'%Y/%m/%d')<='$endDate') or (date_format(a.apply_end_date,'%Y/%m/%d')<='$endDate' and date_format(a.apply_end_date,'%Y/%m/%d')>='$startDate'))")->queryAll();
        if($rows){
            foreach ($rows as $row){
                $boolList = $this->SupportEmployeeToSates($row['id'],$this->id);
                if($boolList["bool"] === false){
                    $message = "该时间段含有待支援的员工，无法完成。員工(".$row["name"].")";
                    $this->addError($attribute,$message);
                    return false;
                }
            }
        }
    }

    //驗證續期及提前結束
    public function validateIDEx($attribute, $params){
        $row = Yii::app()->db->createCommand()->select("status_type,apply_date,apply_end_date,early_date")->from("hr_apply_support")
            ->where("id=:id and status_type in (10,9)",array(":id"=>$this->id))->queryRow();
        if($row){
            $this->status_type =$row["status_type"];
            $this->length_type =2;
            $this->apply_end_date =$row["early_date"];
            $this->apply_length = (strtotime($row["early_date"]) - strtotime($row["apply_date"]))/86400;
            return true;
        }else{
            $message = "支援單非法，請於管理員聯繫";
            $this->addError($attribute,$message);
            return false;
        }
    }

    //驗證批量完成
    public function validateBatchFinish($idList){
        if(!empty($idList)){
            $this->bathFinish = array();
            foreach ($idList as $id){
                if(is_numeric($id)){
                    $this->bathFinish[] = $id;
                }
            }
            $idList = implode(",",$this->bathFinish);
            $row = Yii::app()->db->createCommand()->select("id")->from("hr_apply_support")
                ->where("id in ($idList) and status_type = 6")->queryRow();
            if($row){
                return true;
            }
        }
        $message = Yii::t("contract","Please select the support list you want to complete");
        $this->addError("support_code",$message);
        return false;
    }

    //批量完成
    public function batchFinish(){
        $idList = implode(",",$this->bathFinish);
        Yii::app()->db->createCommand()->update('hr_apply_support', array(
            'status_type'=>7,
            'luu'=>Yii::app()->user->id
        ), "id in ($idList) and status_type = 6");
    }

    //id通用驗證
    public function validateIDGeneral($attribute, $params){
        $status_type = key_exists("status_type",$params)?$params["status_type"]:6;
        $row = Yii::app()->db->createCommand()->select("status_type")->from("hr_apply_support")
            ->where("id=:id and status_type in ($status_type)",array(":id"=>$this->id))->queryRow();
        if($row){
            $this->status_type =$row["status_type"];
            if($this->getScenario() == "reject"){
                if($this->status_type == 9){ //申請提前結束
                    $this->status_type = 8;
                }else{ //申請续期
                    $this->status_type = 11;
                }
            }
            return true;
        }else{
            $message = "支援單非法，請於管理員聯繫";
            $this->addError($attribute,$message);
            return false;
        }
    }

    public function validateStaff($attribute, $params){
        if(!empty($this->employee_id)){
            $row = Yii::app()->db->createCommand()->select("id,code,name")->from("hr_employee")
                ->where("id=:id",array(":id"=>$this->employee_id))->queryRow();
            if($row){
                $boolList = SupportAuditForm::SupportEmployeeToSates($row['id'],$this->id);
                if($boolList["bool"]){
                    $message = "員工（".$row["name"]."）已支援（".$boolList['code']."），请重新选择员工支援";
                    $this->addError($attribute,$message);
                }
            }else{
                $message = "員工異常，請與管理員聯繫";
                $this->addError($attribute,$message);
            }
        }
    }


    public function validateApplyDate($attribute, $params){
        if(!empty($this->apply_date)){
            $uid = Yii::app()->user->id;
            $applyDate = date("Y/m/d", strtotime($this->apply_date));
            $apply_length = is_numeric($this->apply_length)?$this->apply_length:1;
            $this->apply_length = $apply_length;
            $length_type = $this->length_type == 1?"month":"day";
            $this->apply_end_date = date("Y/m/d", strtotime("$applyDate +$apply_length $length_type"));
            $this->apply_end_date = date("Y/m/d", strtotime("$this->apply_end_date -1 day"));
            if($this->privilege == 2){//后续添加，優先權的最後日期只能是本月的最後一天
                $date = date("Y/m/01",strtotime($applyDate));
                $this->apply_end_date = date("Y/m/d",strtotime("$date + 1 month - 1 day"));
            }

            $row = Yii::app()->db->createCommand()->select("*")->from("hr_apply_support")
                ->where("id=:id and (status_type not in (1,7,8) or (status_type = 1 and lcu = '$uid')) ",array(":id"=>$this->id))->queryRow();
            if($row){
                if($this->status_type == 1){ //草稿不需要修改備註
                    return;
                }
                $oldApplyDate = date("Y/m/d",strtotime($row['apply_date']));
                $oldApply_end_date = date("Y/m/d",strtotime($row['apply_end_date']));
                $this->update_type = $row["update_type"];
                $this->update_remark = $row["update_remark"];
                if($oldApplyDate != $applyDate || $oldApply_end_date != $this->apply_end_date){
                    $this->update_type = 1;
                    $this->update_remark .= "时间修改：".date("Y/m/d",strtotime($oldApplyDate))." ~ ".date("Y/m/d",strtotime($oldApply_end_date))." 修改成 ".date("Y/m/d",strtotime($applyDate))." ~ ".date("Y/m/d",strtotime($this->apply_end_date))."
";
                }
                if($row["service_type"]!=$this->service_type){
                    $this->update_type = 1;
                    $this->update_remark .= "服务修改：".SupportApplyList::getServiceList($row["service_type"],true)." 修改成 ".SupportApplyList::getServiceList($this->service_type,true)."
";
                }
                if($row["privilege"]!=$this->privilege){
                    $this->update_type = 1;
                    $this->update_remark .= "特权修改：".SupportApplyList::getPrivilegeList($row["privilege"],true)." 修改成 ".SupportApplyList::getPrivilegeList($this->privilege,true)."
";
                }
            }else if($this->getScenario() != "support"){
                $message = "權限不足，請於管理員聯繫";
                $this->addError($attribute,$message);
            }
        }
    }

    public function validateList($attribute, $params){
        if(!empty($this->tem_list)||$this->getScenario()=="save"){
            $arr = array();
            $tem_s_list = array();
            $this->tem_sum = 0;
            $i = -1;
            foreach ($this->tem_list as $key => $list){
                if($list=='on'){
                    $rows = Yii::app()->db->createCommand()->select("a.id,a.pro_name,a.set_id,b.num_ratio,b.set_name,b.four_with")->from("hr_set_pro a")
                        ->leftJoin("hr_set b","b.id = a.set_id")
                        ->where('a.id=:id',array(':id'=>$key))->queryRow();
                    if(!$rows){
                        $message = Yii::t('contract','template name'). Yii::t('contract',' not exist');
                        $this->addError($attribute,$message);
                        break;
                    }else{
                        if(!key_exists($rows['set_id'],$tem_s_list)){
                            $i++;
                        }
                        $this->tem_sum+=intval($rows['num_ratio']);
                        $arr[] = $key;
                        $tem_s_list[$rows['set_id']]['code']=ReviewAllotForm::getSetCodeToKey($i);
                        $tem_s_list[$rows['set_id']]['name']=$rows['set_name'];
                        $tem_s_list[$rows['set_id']]['num_ratio']=$rows['num_ratio'];
                        $tem_s_list[$rows['set_id']]['four_with']=$rows['four_with'];
                        $tem_s_list[$rows['set_id']]['list'][$this->tem_sum]['id']=$this->tem_sum;
                        $tem_s_list[$rows['set_id']]['list'][$this->tem_sum]['name']=$rows['pro_name'];
                    }
                }
            }
            if (empty($arr)&&$this->getScenario()!="save"){
                $message = Yii::t('contract','reviewAllot project'). Yii::t('contract',' can not be empty');
                $this->addError($attribute,$message);
                return false;
            }
            $this->tem_str = implode(",",$arr);
            $this->tem_s_ist = $tem_s_list;
        }else{
            $message = Yii::t('contract','reviewAllot project'). Yii::t('contract',' can not be empty');
            $this->addError($attribute,$message);
            return false;
        }
    }

	public function retrieveData($index) {
        $suffix = Yii::app()->params['envSuffix'];
        $city = Yii::app()->user->city;
        $uid = Yii::app()->user->id;
        $row = Yii::app()->db->createCommand()->select("*")->from("hr_apply_support")
            ->where("id=:id and status_type in (2,3,4,6,9,10) ",array(":id"=>$index))->queryRow();
		if ($row) {
            $this->id = $row['id'];
            $this->support_code = $row['support_code'];
            $this->apply_city = $row['apply_city'];
            $this->apply_date = $row['apply_date'];
            $this->apply_end_date = $row['apply_end_date'];
            $this->apply_remark = $row['apply_remark'];
            $this->status_type = $row['status_type'];

            $this->employee_id = $row['employee_id'];
            $this->update_type = $row['update_type'];
            $this->update_remark = $row['update_remark'];
            $this->audit_remark = $row['audit_remark'];
            $this->length_type = $row['length_type'];
            $this->apply_length = $row['apply_length'];
            $this->tem_str = $row['tem_str'];
            $this->early_date = $row['early_date'];
            $this->early_remark = $row['early_remark'];
            $this->tem_s_ist = json_decode($row['tem_s_ist'],true);

            $this->apply_type = $row['apply_type'];
            $this->service_type = $row['service_type'];

            $this->privilege = $row['privilege'];
            $this->privilege_user = $row['privilege_user'];
		}
		return true;
	}

    //所有城市
    public function getAllCity(){
        $suffix = Yii::app()->params['envSuffix'];
        $arr = array(""=>"");
        $rows = Yii::app()->db->createCommand()->select("code,name")->from("security$suffix.sec_city")->queryAll();
        if($rows){
            foreach ($rows as $row){
                $arr[$row['code']] = $row['name'];
            }
        }
        return $arr;
    }

    //獲取支援的員工
    public function getSupportEmployee(){
        $sql = '';
        if(!empty($this->id)&&is_numeric($this->id)){
            $sql = " and a.id !=".$this->id;
        }
        $city = empty($this->city)?"ZY":$this->city;
        $arr = array(""=>"");
        $rows = Yii::app()->db->createCommand()->select("a.id,a.code,a.name")->from("hr_employee a")
            ->leftJoin("hr_dept b","a.position = b.id")
            ->where("a.city = '$city' and b.dept_class = 'Technician' and staff_status = 0")->queryAll();
        if($rows){
            foreach ($rows as $row){
                $boolList = $this->SupportEmployeeToSates($row['id'],$this->id);
                $str = $boolList["bool"]?"（已支援）":"（空闲）";
                $arr[$row['id']] = $row['code']." - ".$row['name'].$str;
            }
        }
        $startDate = empty($this->apply_date)?date("Y/m/d"):date("Y/m/d",strtotime($this->apply_date));
        $endDate = empty($this->apply_end_date)?date("Y/m/d", strtotime("+1 month")):date("Y/m/d",strtotime($this->apply_end_date));
        //添加置換員工
        $rows = Yii::app()->db->createCommand()->select("b.id,b.code,b.name")->from("hr_apply_support a")
            ->leftJoin("hr_employee b","a.privilege_user = b.id")
            ->where("a.status_type != 1 and a.privilege = 1 $sql and ((date_format(a.apply_date,'%Y/%m/%d')>='$startDate' and date_format(a.apply_date,'%Y/%m/%d')<='$endDate') or (date_format(a.apply_end_date,'%Y/%m/%d')<='$endDate' and date_format(a.apply_end_date,'%Y/%m/%d')>='$startDate'))")->queryAll();
        if($rows){
            foreach ($rows as $row){
                $boolList = $this->SupportEmployeeToSates($row['id'],$this->id);
                $str = $boolList["bool"]?"（置换员工 - 已支援）":"（置换员工 - 空闲）";
                $arr[$row['id']] = $row['code']." - ".$row['name'].$str;
            }
        }
        return $arr;
    }


    protected function getTableHeaderHtml(){
        $handleHtml="<div class='form-group'><div class='col-sm-5 col-sm-offset-2'><table class='table table-bordered'>";
        $handleHtml.="<thead>";
        $handleHtml.="<tr><th colspan='2' class='text-center' width='70%'>".Yii::t("contract","support code")."</th><th>".$this->support_code."</th></tr>";
        $handleHtml.="<tr><th colspan='2' class='text-center'>".Yii::t("contract","support employee")."</th><th>".$this->employee_name."</th></tr>";
        $handleHtml.="<tr><th colspan='2' class='text-center'>".Yii::t("contract","apply city")."</th><th>".$this->city_name."</th></tr>";
        //$handleHtml.="</thead>";

        return $handleHtml;
    }

    protected function reviewSearchDiv($setList,$handleHtml){
        $sum = (count($setList['list'])*10)*intval($setList['num_ratio']);
        $footArr = array( //表格底部統計
            'sumNum'=>0,
            'sumSum'=>$sum
        );
        //表格頭部顯示
        $html=$handleHtml;
        $html.="<tr><th width='1%'>".$setList['code']."</th><th>".$setList['name']."</th>";
        $html.="<th>$sum</th></tr>";
        $html.="</thead><tbody>";
        $num =0;
        //表格內容
        foreach ($setList["list"] as $proList) {
            $num++;
            if($num%2 == 0){
                $html.="<tr><td>$num</td>";
            }else{
                $html.="<tr class='active'><td>$num</td>";
            }
            $html.="<td class='showNum'>".$proList["name"]."</td>";
            if(key_exists("value",$proList)){
                $proValue = intval($proList["value"])*intval($setList['num_ratio']);
                $this->sumNum+=$proValue;
                $footArr["sumNum"]+=$proValue;
                $html.="<td class='showNum'>$proValue</td>";
            }else{
                $html.="<td class='showNum'>-</td>";
            }
            if(key_exists("remark",$proList)){
                $html.="<td class='remark'>";
                $html.="<span class='text-danger'>".$proList["remark"]."</span>";
                $html.="</td>";
            }
            $html.='</tr>';
        }
        $html.="</tbody><tfoot>";
        //表格底部統計
        $html.=$this->returnTableFoot($footArr);

        $html.="</tfoot></table></div></div>";
        return $html;
    }
    protected function returnTableFoot($footArr){
        if(empty($footArr["sumSum"])){
            $sum = 0;
        }else{
            $sum = ($footArr["sumNum"]/$footArr["sumSum"])*100;
        }
        $footHtml ="<tr><th width='1%'>A</th><th>".Yii::t("contract","Project total score")."</th><th>".$footArr["sumSum"]."</th></tr>";
        $footHtml.="<tr><th>B</th><th>".Yii::t("contract","assessed total score")."</th><th>".$footArr["sumNum"]."</th></tr>";
        $footHtml.="<tr><th>C</th><th>".Yii::t("contract","Percentage score")."（B/A*100）</th><th>".sprintf("%.2f",$sum)."</th></tr>";

        return $footHtml;
    }
    protected function getCountTable($handleHtml){
        $html = $handleHtml."</thead>";
        $html.="<tbody><tr><td colspan='3'>".Yii::t("contract","Evaluate project score")." (100%)</td></tr></tbody>";
        $html.="<tfoot>";
        $html.=$this->returnTableFoot(array('sumNum'=>$this->sumNum,'sumSum'=>($this->tem_sum*10)));
        $html.="</tfoot></table></div></div>";
        return $html;
    }

    public function getTabList($model=''){
        if(empty($model)){
            $model = $this;
        }
        $tabs = array();
        $row = Yii::app()->db->createCommand()->select("support_code,tem_sum,apply_city,tem_s_ist,employee_id")->from("hr_apply_support")
            ->where("id=:id",array(":id"=>$model->id))->queryRow();
        if($row){
            $this->tem_s_ist = json_decode($row["tem_s_ist"],true);
            $this->support_code = $row["support_code"];
            $this->tem_sum = intval($row["tem_sum"]);
            $this->employee_name = YearDayList::getEmployeeNameToId($row["employee_id"]);
            $this->city_name = CGeneral::getCityName($row["apply_city"]);
            $handleHtml=$this->getTableHeaderHtml();
            foreach ($this->tem_s_ist as $set_id => $setList) {
                //$this->pro_str = empty($this->pro_str)?"（".$setList['code']."-":$this->pro_str;
                $content = $this->reviewSearchDiv($setList,$handleHtml);
                $tabs[] = array(
                    'label'=>$setList['code']."（".$setList['name']."）",
                    'content'=>"<p>&nbsp;</p>".$content,
                    'active'=>false,
                );
            }
            $content = "<p>&nbsp;</p>".$this->getCountTable($handleHtml);
            $tabs[] = array(
                'label'=>Yii::t("contract","review sum"),
                'content'=>$content,
                'active'=>true,
            );
        }

        return $tabs;
    }

    //判断员工是否可以支援
    public function SupportEmployeeToSates($employee_id,$id = 0){
        $sql = !is_numeric($id)?"":" and id !=$id";
        $startDate = empty($this->apply_date)?date("Y/m/d"):date("Y/m/d",strtotime($this->apply_date));
        $endDate = empty($this->apply_end_date)?date("Y/m/d", strtotime("+1 month")):date("Y/m/d",strtotime($this->apply_end_date));
        $row = Yii::app()->db->createCommand()->select("id,support_code")->from("hr_apply_support")
            ->where("status_type != 1 and employee_id=:employee_id and ((date_format(apply_date,'%Y/%m/%d')>='$startDate' and date_format(apply_date,'%Y/%m/%d')<='$endDate') or (date_format(apply_end_date,'%Y/%m/%d')<='$endDate' and date_format(apply_end_date,'%Y/%m/%d')>='$startDate')) $sql",array(":employee_id"=>$employee_id))->queryRow();
        if($row){
            return array("bool"=>true,"code"=>$row["support_code"]);
        }
        return array("bool"=>false);
    }

    //是否只讀
    public function getReadonly(){
        return $this->scenario=='view'||!in_array($this->status_type,array(1,2,3,4));
    }

    //刪除驗證
    public function deleteValidate(){
        $row = Yii::app()->db->createCommand()->select("*")->from("hr_apply_support")
            ->where("id=:id and status_type =2",array(":id"=>$this->id))->queryRow();
        if($row){
            return true;
        }
        return false;
    }

	public function saveData($serviceStr='edit')
	{
		$connection = Yii::app()->db;
		$transaction=$connection->beginTransaction();
		try {
			$this->saveGoods($connection,$serviceStr);
			$transaction->commit();
		}
		catch(Exception $e) {
			$transaction->rollback();
			throw new CHttpException(404,'Cannot update. ('.$e->getMessage().')');
		}
	}

	protected function saveGoods(&$connection,$serviceStr) {
		$sql = '';
        switch ($serviceStr) {
            case 'delete':
                $sql = "delete from hr_apply_support where id = :id and status_type =2";
                break;
            case 'reject'://不同意提前結束或續期 8,11
                $sql = "update hr_apply_support set
							status_type = :status_type, 
							reject_remark = :reject_remark, 
							luu = :luu
						where id = :id";
                break;
            case 'renewal'://同意續期 5
                $sql = "update hr_apply_support set
							status_type = :status_type, 
							apply_end_date = :apply_end_date, 
							apply_length = :apply_length, 
							length_type = :length_type, 
							apply_type = 2, 
							luu = :luu
						where id = :id";
                break;
            case 'early'://同意提前結束 7
                $sql = "update hr_apply_support set
							status_type = :status_type, 
							apply_end_date = :apply_end_date, 
							apply_length = :apply_length, 
							length_type = :length_type, 
							luu = :luu
						where id = :id";
                break;
            case 'finish': //完成 7
                $sql = "update hr_apply_support set
							status_type = :status_type , 
							luu = :luu
						where id = :id";
                break;
            case 'undo'://撤回 5
                $sql = "update hr_apply_support set
							status_type = 5 , 
							luu = :luu
						where id = :id";
                break;
            case 'endReply'://回復/完成 12
                $sql = "update hr_apply_support set
							status_type = 12 , 
							audit_remark = :audit_remark, 
							luu = :luu
						where id = :id";
                break;
            case 'new'://支援 5
                $sql = "insert into hr_apply_support(
							service_type,apply_date,privilege,privilege_user,apply_remark,apply_end_date,apply_city,apply_lcu,status_type, lcu, luu
							,tem_s_ist,tem_str,tem_sum,employee_id,audit_remark,apply_length,length_type,apply_type
						) values (
							:service_type,:apply_date,:privilege,:user_privilege,:apply_remark,:apply_end_date,:apply_city,:apply_lcu,:status_type, :lcu, :luu
							,:tem_s_ist,:tem_str,:tem_sum,:employee_id,:audit_remark,:apply_length,:length_type,2
						)";
                break;
            case 'edit'://查看、待定、分配 3,4,5
                $sql = "update hr_apply_support set
							service_type = :service_type, 
							apply_date = :apply_date, 
							apply_end_date = :apply_end_date, 
							privilege = :privilege, 
							privilege_user = :user_privilege, 
							length_type = :length_type, 
							apply_length = :apply_length, 
							update_type = :update_type, 
							update_remark = :update_remark, 
							audit_remark = :audit_remark, 
							status_type = :status_type, 
							employee_id = :employee_id, 
							tem_str = :tem_str, 
							tem_s_ist = :tem_s_ist, 
							tem_sum = :tem_sum, 
							luu = :luu
						where id = :id";
                break;
        }
		if (empty($sql)) return false;

        $city = Yii::app()->user->city();
        $uid = Yii::app()->user->id;

        $command=$connection->createCommand($sql);
        if (strpos($sql,':id')!==false)
            $command->bindParam(':id',$this->id,PDO::PARAM_INT);
        //id, city,subject,message,city_id,city_str,staff_id,staff_str,status_type
        if (strpos($sql,':apply_date')!==false)
            $command->bindParam(':apply_date',$this->apply_date,PDO::PARAM_STR);
        if (strpos($sql,':apply_end_date')!==false)
            $command->bindParam(':apply_end_date',$this->apply_end_date,PDO::PARAM_STR);
        if (strpos($sql,':apply_remark')!==false)
            $command->bindParam(':apply_remark',$this->apply_remark,PDO::PARAM_STR);
        if (strpos($sql,':apply_lcu')!==false)
            $command->bindParam(':apply_lcu',$uid,PDO::PARAM_STR);
        if (strpos($sql,':apply_city')!==false)
            $command->bindParam(':apply_city',$this->apply_city,PDO::PARAM_STR);
        if (strpos($sql,':status_type')!==false)
            $command->bindParam(':status_type',$this->status_type,PDO::PARAM_INT);
        if (strpos($sql,':service_type')!==false)
            $command->bindParam(':service_type',$this->service_type,PDO::PARAM_INT);

        if (strpos($sql,':apply_length')!==false)
            $command->bindParam(':apply_length',$this->apply_length,PDO::PARAM_STR);
        if (strpos($sql,':length_type')!==false)
            $command->bindParam(':length_type',$this->length_type,PDO::PARAM_STR);
        if (strpos($sql,':update_type')!==false)
            $command->bindParam(':update_type',$this->update_type,PDO::PARAM_STR);
        if (strpos($sql,':update_remark')!==false)
            $command->bindParam(':update_remark',$this->update_remark,PDO::PARAM_STR);
        if (strpos($sql,':audit_remark')!==false)
            $command->bindParam(':audit_remark',$this->audit_remark,PDO::PARAM_STR);
        if (strpos($sql,':reject_remark')!==false)
            $command->bindParam(':reject_remark',$this->reject_remark,PDO::PARAM_STR);
        if (strpos($sql,':tem_str')!==false)
            $command->bindParam(':tem_str',$this->tem_str,PDO::PARAM_STR);
        if (strpos($sql,':employee_id')!==false)
            $command->bindParam(':employee_id',$this->employee_id,PDO::PARAM_STR);
        if (strpos($sql,':tem_sum')!==false)
            $command->bindParam(':tem_sum',$this->tem_sum,PDO::PARAM_STR);
        if (strpos($sql,':tem_s_ist')!==false){
            $this->tem_s_ist = json_encode($this->tem_s_ist);
            $command->bindParam(':tem_s_ist',$this->tem_s_ist,PDO::PARAM_STR);
        }
        if (strpos($sql,':privilege')!==false)
            $command->bindParam(':privilege',$this->privilege,PDO::PARAM_INT);
        if (strpos($sql,':user_privilege')!==false){
            if(empty($this->privilege_user)){
                $command->bindValue(':user_privilege',null,PDO::PARAM_INT);
            }else{
                $command->bindParam(':user_privilege',$this->privilege_user,PDO::PARAM_INT);
            }
        }

        if (strpos($sql,':luu')!==false)
            $command->bindParam(':luu',$uid,PDO::PARAM_STR);
        if (strpos($sql,':lcu')!==false)
            $command->bindParam(':lcu',$uid,PDO::PARAM_STR);
        $command->execute();

        if ($serviceStr=='new'){
            $this->id = Yii::app()->db->getLastInsertID();
            $this->lenStr();
            Yii::app()->db->createCommand()->update('hr_apply_support', array(
                'support_code'=>$this->support_code
            ), 'id=:id', array(':id'=>$this->id));
        }

        $this->setSupportHistory();//記錄操作并发送邮件
		return true;
	}

	private function setSupportHistory(){
        if (in_array($this->status_type,array(4,5,7,8,11,12))){
            $this->employee_name = YearDayList::getEmployeeNameToId($this->employee_id);
            $this->city_name = CGeneral::getCityName($this->apply_city);
            $email = new Email();
            $message = "支援编号:".$this->support_code."<br>";
            $message.= "服务类型:".SupportApplyList::getServiceList($this->service_type,true)."<br>";
            $message.= "申请城市:".$this->city_name."<br>";
            $message.= "申请时间:".$this->apply_date."<br>";
            $message.= "结束时间:".$this->apply_end_date."<br>";
            $message.= "支援时长:".$this->apply_length.($this->length_type==1?"个月":"天")."<br>";
            if(!empty($this->employee_name)){
                $message.= "支援员工:".$this->employee_name."<br>";
            }
            $messagePre = $message;
            switch ($this->status_type){
                case 4://排隊等候
                    $email->setSubject("支援单（".$this->support_code."） - 排队等候");
                    $status_remark = $this->audit_remark;
                    $message.= "审核备注:$status_remark<br>";
                    $messagePre = $message;
                    $message.="<a href='".Yii::app()->createAbsoluteUrl("SupportSearch/view",array("index"=>$this->id))."'>快速访问</a><br>";

                    break;
                case 5://待評分
                    //後續要求 - 給支點發送郵件（開始）
                    $email->setSubject("通知：员工（".$this->employee_name."）支援到".$this->city_name);
                    $email->setMessage($messagePre);
                    $email->resetToAddr();
                    $email->addSupportPreEmailToEmployeeId($this->employee_id);
                    $email->sent();
                    $email->resetToAddr();
                    //後續要求 - 給支點發送郵件（結束）
                    $email->setSubject("支援单（".$this->support_code."） - 待評分");
                    $status_remark = $this->audit_remark;
                    $message.= "审核备注:$status_remark<br>";
                    $messagePre = $message;
                    $email->setMessage($message."<a href='".Yii::app()->createAbsoluteUrl("SupportSearch/view",array("index"=>$this->id))."'>快速访问</a><br>");
                    $email->addEmailToStaffId($this->employee_id);
                    $email->sent();//單獨發給支援的員工
                    $email->resetToAddr();
                    $message.="<a href='".Yii::app()->createAbsoluteUrl("SupportApply/edit",array("index"=>$this->id))."'>快速访问</a><br>";

                    break;
                case 7://已完成
                    $email->setSubject("支援单（".$this->support_code."） - 已完成");
                    $email->addEmailToStaffId($this->employee_id);
                    $status_remark = $this->audit_remark;
                    $message.= "审核备注:$status_remark<br>";
                    $messagePre = $message;
                    $email->setMessage($message."<a href='".Yii::app()->createAbsoluteUrl("SupportSearch/view",array("index"=>$this->id))."'>快速访问</a><br>");
                    $email->addEmailToStaffId($this->employee_id);
                    $email->sent();//單獨發給支援的員工
                    $email->resetToAddr();
                    $message.="<a href='".Yii::app()->createAbsoluteUrl("SupportApply/edit",array("index"=>$this->id))."'>快速访问</a><br>";

                    break;
                case 8://拒絕提前結束
                    $email->setSubject("支援单（".$this->support_code."） - 拒絕提前結束");
                    $status_remark = $this->reject_remark;
                    $message.= "审核备注:$status_remark<br>";
                    $messagePre = $message;
                    $email->setMessage($message."<a href='".Yii::app()->createAbsoluteUrl("SupportSearch/view",array("index"=>$this->id))."'>快速访问</a><br>");
                    $email->addEmailToStaffId($this->employee_id);
                    $email->sent();//單獨發給支援的員工
                    $email->resetToAddr();
                    $message.="<a href='".Yii::app()->createAbsoluteUrl("SupportApply/edit",array("index"=>$this->id))."'>快速访问</a><br>";

                    break;
                case 11://拒絕續期
                    $email->setSubject("支援单（".$this->support_code."） - 拒絕續期");
                    $status_remark = $this->reject_remark;
                    $message.= "审核备注:$status_remark<br>";
                    $messagePre = $message;
                    $email->setMessage($message."<a href='".Yii::app()->createAbsoluteUrl("SupportSearch/view",array("index"=>$this->id))."'>快速访问</a><br>");
                    $email->addEmailToStaffId($this->employee_id);
                    $email->sent();//單獨發給支援的員工
                    $email->resetToAddr();
                    $message.="<a href='".Yii::app()->createAbsoluteUrl("SupportApply/edit",array("index"=>$this->id))."'>快速访问</a><br>";

                    break;
                case 12://回復、完成
                    $email->setSubject("支援单（".$this->support_code."） - 沒有支援，请和支援组联系");
                    $status_remark = $this->audit_remark;
                    $message.= "审核备注:$status_remark<br>";
                    $messagePre = $message;
                    $message.="<a href='".Yii::app()->createAbsoluteUrl("SupportApply/edit",array("index"=>$this->id))."'>快速访问</a><br>";

                    break;
                default:
                    return false;
            }
            $email->setMessage($message);
            $email->addEmailToPrefixAndOnlyCity("AY01",$this->apply_city);//該城市有申請權限的人收到郵件
            $email->sent();
            //後續要求 - 需要額外發送郵件（開始）
            $messagePre.="<a href='".Yii::app()->createAbsoluteUrl("SupportSearch/view",array("index"=>$this->id))."'>快速访问</a><br>";
            $email->setMessage($messagePre);
            $email->resetToAddr();
            $email->addSupportPreEmail();
            $email->sent();
            //後續要求 - 需要額外發送郵件（結束）
            Yii::app()->db->createCommand()->insert('hr_apply_support_history', array(
                'support_id'=>$this->id,
                'start_date'=>$this->apply_date,
                'end_date'=>$this->apply_end_date,
                'apply_length'=>$this->apply_length,
                'length_type'=>$this->length_type,
                'status_type'=>$this->status_type,
                'status_remark'=>$status_remark,
                'lcu'=>Yii::app()->user->id,
            ));
        }
    }

    private function lenStr(){
        $code = strval($this->id);
        $this->support_code = "U";
        for($i = 0;$i < 5-strlen($code);$i++){
            $this->support_code.="0";
        }
        $this->support_code .= $code;
    }

	protected function sendEmail(){
        //request_dt
        $email = new Email($this->subject,$this->message);
        if(empty($this->city_id)){
            $email->addEmailToAllCity();
        }else{
            $cityList = explode("~",$this->city_id);
            foreach ($cityList as $city){
                $email->addEmailToCity($city);
            }
        }

        if(!empty($this->staff_id)){
            $staffList = explode("~",$this->staff_id);
            foreach ($staffList as $staff){
                $email->addEmailToLcu($staff);
            }
        }
        $email->sent('','',$this->request_dt);
    }
}
