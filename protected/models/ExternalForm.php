<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2023/12/12 0012
 * Time: 14:20
 */
class ExternalForm extends StaffForm
{
    public $table_type=2;
    public $staff_status=9;//外聘人员，状态：9：草稿 1:入职

    /**
     * Declares customized attribute labels.
     * If not declared here, an attribute would have a label that is
     * the same as its name with the first letter in upper case.
     */
    public function attributeLabels()
    {
        $list = parent::attributeLabels();
        $list['table_type']=Yii::t('contract','Employee Type');
        $list['address_code']=Yii::t('contract','postcode');
        $list['contact_address_code']=Yii::t('contract','postcode');
        return $list;
    }

    public function getRequiredList(){//必填内容
        return array(
            "name","sex","table_type","office_id",
            "phone","department","position","birth_time","user_card","user_card_date"
        );
    }
    /**
     * Declares the validation rules.
     */
    public function rulesEx()
    {
        $requiredList = $this->getRequiredList();
        $requiredStr = implode(",",$requiredList);
        return array(
            array('table_type,jj_card','safe'),
            array($requiredStr,'required'),
            array('table_type','validateTableType'),
        );
    }

    public function validateTableType($attribute, $params){
        $list = StaffFun::getTableTypeList();
        if(!key_exists($this->table_type,$list)){
            $this->addError($attribute,"员工类型不存在，请刷新重试");
        }else{
            if(in_array($this->table_type,array(2,3,4))){
                if(empty($this->staff_id)){
                    $this->addError($attribute,"员工归属不能为空");
                }
                if(empty($this->company_id)){
                    $this->addError($attribute,"员工合同归属不能为空");
                }
            }
        }
    }

    public function validateID($attribute, $params){
        $city = $this->city;
        $allow_city = Yii::app()->user->city_allow();
        if (strpos($allow_city,"'{$city}'")!==false){
            $this->city = $city;
        }else{
            $this->city = Yii::app()->user->city();
        }
        if($this->getScenario()!='new'){
            $row = Yii::app()->db->createCommand()->select("id,city")->from("hr_employee")
                ->where("id=:id and city in ({$allow_city}) and table_type!=1 AND staff_status in (9,3)", array(
                    ':id'=>$this->id
                ))->queryRow();
            if($row){
                $this->id = $row["id"];
            }else{
                $this->addError($attribute,"员工不存在，请刷新重试");
            }
        }
    }

    //員工刪除時必須是草稿
    public function validateDelete(){
        $allow_city = Yii::app()->user->city_allow();
        $row = Yii::app()->db->createCommand()->select()->from("hr_employee")
            ->where("id=:id and table_type!=1 AND staff_status in (9,3) and city in ({$allow_city})", array(':id'=>$this->id))->queryRow();
        if ($row){
            return true;
        }
        return false;
    }

    public function retrieveData($index){
        $suffix = Yii::app()->params['envSuffix'];
        $city = Yii::app()->user->city();
        $allow_city = Yii::app()->user->city_allow();
        $whereSql = " and a.city in ({$allow_city}) and a.table_type!=1";
        //$whereSql = " and a.status_type not in (8,10)";
        $sql = "select a.*,docman$suffix.countdoc('EMPLOY',a.id) as employdoc 
          from hr_employee a where a.id='{$index}' {$whereSql}";
        $row = Yii::app()->db->createCommand($sql)->queryRow();
        if ($row!==false) {
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
            return true;
        }else{
            return false;
        }
    }

    public function getUpdateJson(){
        $list = array();
        foreach (self::historyUpdateList() as $key){
            $list[$key] = $this->$key;
        }
        return json_encode($list);
    }

    public function saveData()
    {
        $connection = Yii::app()->db;
        $transaction=$connection->beginTransaction();
        try {
            $model = new ExternalForm();
            $model->retrieveData($this->id);
            $this->save($connection);
            $this->historySave($connection,$model);
            $this->updateDocman($connection,'EMPLOY');
            $transaction->commit();
            if($this->getScenario()=="new"){
                $this->setScenario("edit");
            }
        }catch(Exception $e) {
            $transaction->rollback();
            throw new CHttpException(404,$e->getMessage());
        }
    }

    protected function updateDocman(&$connection, $doctype) {
        if ($this->scenario=='new') {
            $docidx = strtolower($doctype);
            if ($this->docMasterId[$docidx] > 0) {
                $docman = new DocMan($doctype,$this->id,get_class($this));
                $docman->masterId = $this->docMasterId[$docidx];
                $docman->updateDocId($connection, $this->docMasterId[$docidx]);
            }
        }
    }

    //哪些字段修改后需要记录
    protected static function historyUpdateList(){
        return array("group_type","office_id","table_type","name","staff_id","company_id",
            "contract_id","address","address_code","contact_address","contact_address_code",
            "phone","phone2","user_card","department","position",
            "wage","start_time","end_time","test_type",
            "test_start_time","sex","test_end_time","test_wage",
            "entry_time","age","birth_time","health",
            "education","wechat","recommend_user","urgency_card","experience",
            "english","technology","other","year_day","email",
            "remark","fix_time","code_old","test_length","staff_type",
            "staff_leader","attachment","nation","household","empoyment_code",
            "social_code","user_card_date","emergency_user","emergency_phone",
            "work_area","bank_type","bank_number","jj_card",
        );
    }

    protected static function getNameForValue($type,$value){
        switch ($type){
            case "position":
            case "department":
                $value = DeptForm::getDeptToId($value);
                break;
            case "staff_id":
            case "company_id":
                $value = StaffFun::getCompanyNameToID($value);
                break;
            case "table_type":
                $value = StaffFun::getTableTypeNameForID($value);
                break;
            case "sex":
                $value = StaffFun::getSexNameForID($value);
                break;
            case "recommend_user":
                $value = StaffFun::getEmployeeNameAndCode($value);
                break;
            case "household":
                $value = StaffFun::getNationNameForID($value);
                break;
            case "health":
                $value = StaffFun::getHealthNameForID($value);
                break;
            case "staff_type":
                $value = StaffFun::getStaffTypeNameForID($value);
                break;
            case "staff_leader":
                $value = StaffFun::getStaffLeaderNameForID($value);
                break;
            case "group_type":
                $value = StaffFun::getGroupTypeNameForID($value);
                break;
            case "fix_time":
                $value = StaffFun::getFixTimeNameForID($value);
                break;
            case "contract_id":
                $value = StaffFun::getContractNameToID($value);
                break;
            case "test_length":
                $value = StaffFun::getTestMonthLengthNameForID($value);
                break;
            case "education":
                $value = StaffFun::getEducationNameForID($value);
                break;
            case "bank_type":
                $value = StaffFun::getBankTypeNameForId($value);
                break;
        }
        return $value;
    }

    //保存历史记录
    protected function historySave(&$connection,$model){
        $uid = Yii::app()->user->id;
        switch ($this->getScenario()){
            case "delete":
                $connection->createCommand()->delete("hr_table_history", "table_id=:id and table_name='hr_employee'",array(":id"=>$this->id));
                break;
            case "edit":
                $keyArr = self::historyUpdateList();
                $list=array("table_id"=>$this->id,"table_name"=>"hr_employee","lcu"=>$uid,"update_type"=>1,"update_html"=>array());
                foreach ($keyArr as $key){
                    if($model->$key!=$this->$key){
                        $list["update_html"][]="<span>".$this->getAttributeLabel($key)."：".self::getNameForValue($key,$model->$key)." 修改为 ".self::getNameForValue($key,$this->$key)."</span>";
                    }
                }
                if(!empty($list["update_html"])){
                    $list["update_html"] = implode("<br/>",$list["update_html"]);
                    $list["update_json"] = $this->getUpdateJson();
                    $connection->createCommand()->insert("hr_table_history", $list);
                }
                break;
            case "new"://新增
                $list=array(
                    "table_name"=>"hr_employee",
                    "table_id"=>$this->id,
                    "lcu"=>$uid,
                    "update_type"=>2,
                    "update_html"=>"<span>新增</span>",
                    "update_json"=>$this->getUpdateJson(),
                );
                $connection->createCommand()->insert("hr_table_history", $list);
                break;
        }
        if($this->staff_status==2){
            $connection->createCommand()->insert("hr_table_history", array(
                "table_name"=>"hr_employee",
                "table_id"=>$this->id,
                "lcu"=>$uid,
                "update_type"=>1,
                "update_html"=>"<span class='text-success'>要求审核</span>",
            ));
        }
    }

    protected function save(&$connection)
    {
        $uid = Yii::app()->user->id;
        $city = Yii::app()->user->city();
        $list=array();
        $arr = array(
            "group_type"=>3,"office_id"=>3,"table_type"=>3,"staff_status"=>3,"name"=>1,"city"=>1,
            "staff_id"=>1,"company_id"=>1,"contract_id"=>1,"address"=>1,"address_code"=>1,
            "contact_address"=>1,"contact_address_code"=>1,"phone"=>1,"phone2"=>1,"user_card"=>1,
            "department"=>1,"position"=>1,"wage"=>1,
            "start_time"=>2,"end_time"=>2,"test_type"=>3,"test_start_time"=>2,
            "sex"=>1,"test_end_time"=>2,"test_wage"=>1,
            "entry_time"=>2,"age"=>1,"birth_time"=>2,"health"=>1,
            "education"=>1,"wechat"=>1,"recommend_user"=>1,"urgency_card"=>1,
            "experience"=>1,"english"=>1,"technology"=>1,"other"=>1,"year_day"=>1,
            "email"=>1,"remark"=>1,"image_user"=>1,"image_code"=>1,"image_work"=>1,
            "image_other"=>1,"fix_time"=>1,"code_old"=>1,"test_length"=>1,"staff_type"=>1,
            "staff_leader"=>1,"attachment"=>1,"nation"=>1,"household"=>1,"empoyment_code"=>1,
            "social_code"=>1,"user_card_date"=>1,"emergency_user"=>1,"emergency_phone"=>1,
            "work_area"=>1,"bank_type"=>3,"bank_number"=>1,"jj_card"=>1,
        );
        foreach ($arr as $key=>$type){
            $value=$this->$key;
            switch ($type){
                case 1://原值
                    $value = $value===""?null:$value;
                    break;
                case 2://日期
                    $value = empty($value)?null:General::toDate($value);
                    break;
                case 3://数字
                    $value = $value===""?null:floatval($value);
                    break;
            }
            $this->$key=$value;
            $list[$key] = $value;
        }
        switch ($this->scenario) {
            case 'delete':
                $connection->createCommand()->delete("hr_employee", "id=:id and table_type!=1 AND staff_status in (3,9)", array(":id" => $this->id));
                break;
            case 'new':
                $list["lcu"] = $uid;
                $connection->createCommand()->insert("hr_employee", $list);
                break;
            case 'edit':
                $list["luu"] = $uid;
                $connection->createCommand()->update("hr_employee", $list, "id=:id", array(":id" => $this->id));
                break;
        }

        if ($this->scenario=='new'){
            $this->id = Yii::app()->db->getLastInsertID();
            $this->lenStr();
            Yii::app()->db->createCommand()->update('hr_employee', array(
                'code'=>$this->code
            ), 'id=:id', array(':id'=>$this->id));
        }

        if($this->staff_status==2){
            $this->sendEmail();//发送邮件
        }

        //U系统同步(由于需要审核，所以同步放到审核)
        //StaffForm::sendCurl($this->id,$this->getScenario());
        return true;
    }

    protected function sendEmail(){
        $description="兼职、外聘员工录入 - ".$this->name;
        $subject="兼职、外聘员工录入 - ".$this->name;
        $message="<p>员工类型：".StaffFun::getTableTypeNameForID($this->table_type)."</p>";
        $message.="<p>员工编号：".$this->code."</p>";
        $message.="<p>员工姓名：".$this->name."</p>";
        $message.="<p>员工所在城市：".CGeneral::getCityName($this->city)."</p>";
        $message.="<p>员工职位：".DeptForm::getDeptToId($this->position)."</p>";
        $message.="<p>员工入职日期：".$this->entry_time."</p>";
        $message.="<p>员工合同归属：".StaffFun::getCompanyNameToID($this->staff_id)."</p>";
        $message.="<p>员工归属：".StaffFun::getCompanyNameToID($this->company_id)."</p>";
        $email = new Email($subject,$message,$description);
        //$email->addEmailToPrefix("ZG01");
        $email->addEmailToPrefixAndCity("ZG01",$this->city);
        $email->sent();
    }

    protected function lenStr(){
        $codeStr = "E";
        $code = strval($this->id);
        $this->code = $codeStr;
        for($i = 0;$i < 5-strlen($code);$i++){
            $this->code.="0";
        }
        $this->code .= $code;
    }


    public function readonly(){
        return $this->scenario=='view'||!in_array($this->staff_status,array(9,3));
    }
}