<?php

/**
 * UserForm class.
 * UserForm is the data structure for keeping
 * user form data. It is used by the 'user' action of 'SiteController'.
 */
class StaffForm extends CFormModel
{
	/* User Fields */
	public $employee_id=0;
	public $id;
	public $name;
	public $city;
	public $code;
    public $sex;
	public $company_id;
	public $staff_id;
	public $address;
	public $address_code;
	public $contact_address;
	public $contact_address_code;
	public $phone;
	public $phone2;//緊急電話
	public $contract_id;
	public $user_card;
	public $department;
	public $position;
	public $wage;
	public $time=1;
	public $start_time;
	public $end_time;
	public $test_start_time;
	public $test_end_time;
	public $test_wage;
	public $word_status=1;
	public $test_type=1;
	public $word_html="";
	public $staff_status = 1;
	public $entry_time;//入職時間
	public $birth_time;//出生日期
	public $age;//年齡
	public $health;//身體狀況
	public $education;//學歷
	public $experience;//工作經驗
	public $english;//外語水平
	public $technology;//技術水平
	public $other;//其它說明
	public $year_day;//年假
	public $email;//員工郵箱
	public $remark;//備註
	public $price1;//每月工資
	public $price2;//加班工資
	public $price3=array();//每月補貼
	public $image_user;//員工照片
	public $image_code;//身份證照片
	public $image_work;//工作證明照片
	public $image_other;//其它照片
	public $ject_remark;//拒絕原因
	public $ld_card;//勞動保障卡號
	public $sb_card;//社保卡號
	public $jj_card;//公積金卡號
	public $staff_type;//员工类别
	public $staff_leader;//队长/组长
	public $test_length;//
	public $attachment="";//附件
    public $nation;//民族
    public $household;//户籍类型
    public $empoyment_code;//就业登记证号
    public $social_code;//社会保障卡号
    public $fix_time=0;//合同類型
    public $user_card_date;//身份证有效期
    public $emergency_user;//紧急联络人姓名
    public $emergency_phone;//紧急联络人手机号
    public $code_old;//員工編號（舊）
    public $group_type;//組別類型
    public $office_id;//办事处id
    public $lcu;//
    public $luu;//
    public $lcd;//
    public $lud;//
    public $leave_time;//
    public $leave_reason;//
    public $bank_type;//
    public $bank_number;//

    public $work_area;//主要工作地点
    public $wechat;//微信賬號
    public $recommend_user;//推荐人
    public $urgency_card;//緊急聯繫人身份證

    public $table_type=1;//类型：1：专职  2：兼职 3：外聘
    public $audit=false;//是否审核

    public $no_of_attm = array(
        'employee'=>0,
        'employ'=>0,
        'signc'=>0,
    );
    public $docType = 'EMPLOY';
    public $docMasterId = array(
        'employee'=>0,
        'employ'=>0,
        'signc'=>0,
    );
    public $files;
    public $removeFileId = array(
        'employee'=>0,
        'employ'=>0,
        'signc'=>0,
    );
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
			'id'=>Yii::t('staff','Record ID'),
			'city'=>Yii::t('contract','City'),
			'code'=>Yii::t('contract','Employee Code'),
			'sex'=>Yii::t('contract','Sex'),
			'age'=>Yii::t('contract','Age'),
			'birth_time'=>Yii::t('contract','Birth Date'),
			'name'=>Yii::t('contract','Employee Name'),
			'staff_id'=>Yii::t('contract','Employee Belong'),
			'company_id'=>Yii::t('contract','Employee Contract Belong'),
			'contract_id'=>Yii::t('contract','Employee Contract'),
			'address'=>Yii::t('contract','Old Address'),
			'contact_address'=>Yii::t('contract','Contact Address'),
            'phone'=>Yii::t('contract','Employee Phone'),
            'phone2'=>Yii::t('contract','Emergency call'),
            'user_card'=>Yii::t('contract','ID Card'),
            'department'=>Yii::t('contract','Department'),
            'position'=>Yii::t('contract','Position'),
            'wage'=>Yii::t('contract','Contract Pay'),
            'time'=>Yii::t('contract','Contract Time'),
            'start_time'=>Yii::t('contract','Contract Start Time'),
            'end_time'=>Yii::t('contract','Contract End Time'),
            'test_type'=>Yii::t('contract','Probation Type'),
            'test_time'=>Yii::t('contract','Probation Time'),
            'test_start_time'=>Yii::t('contract','Probation Start Time'),
            'test_end_time'=>Yii::t('contract','Probation End Time'),
            'test_wage'=>Yii::t('contract','Probation Wage'),
            'entry_time'=>Yii::t('contract','Entry Time'),
            'health'=>Yii::t('contract','Physical condition'),
            'education'=>Yii::t('contract','Degree level'),
            'experience'=>Yii::t('contract','Work experience'),
            'english'=>Yii::t('contract','Foreign language level'),
            'technology'=>Yii::t('contract','Technical level'),
            'other'=>Yii::t('contract','Other'),
            'year_day'=>Yii::t('contract','Annual leave'),
            'email'=>Yii::t('contract','Email'),
            'remark'=>Yii::t('contract','Remark'),
            'price1'=>Yii::t('contract','Wages Name'),
            'price3'=>Yii::t('contract','Wages Type'),
            'image_user'=>Yii::t('contract','Staff photo'),
            'image_code'=>Yii::t('contract','Id photo'),
            'image_work'=>Yii::t('contract','Work photo'),
            'image_other'=>Yii::t('contract','Other photo'),
            'ject_remark'=>Yii::t('contract','Rejected Remark'),
            'ld_card'=>Yii::t('contract','Labor security card'),
            'sb_card'=>Yii::t('contract','Social security card'),
            'jj_card'=>Yii::t('contract','Accumulation fund card'),
            'update_remark'=>Yii::t('contract',"Operation")."".Yii::t('contract','Remark'),
            'staff_type'=>Yii::t('staff','Staff Type'),
            'staff_leader'=>Yii::t('staff','Team/Group Leader'),
            'test_length'=>Yii::t('contract','Probation Time Longer'),
            'nation'=>Yii::t('contract','nation'),
            'household'=>Yii::t('contract','Household type'),
            'empoyment_code'=>Yii::t('contract','Employment registration certificate'),
            'social_code'=>Yii::t('contract','Social security card number'),
            'fix_time'=>Yii::t('contract','contract deadline'),
            'user_card_date'=>Yii::t('contract','ID Card Date'),
            'emergency_user'=>Yii::t('contract','Emergency User'),
            'emergency_phone'=>Yii::t('contract','Emergency Phone'),
            'code_old'=>Yii::t('contract','Code Old'),
            'group_type'=>Yii::t('contract','group type'),
            'effect_time'=>Yii::t('contract','Effect Time'),
            'opr_type'=>Yii::t('contract','Operation Type'),
            'change_city'=>Yii::t('contract','Change City'),
            'change_city_old'=>Yii::t('contract','Staff City'),
            'leave_reason'=>Yii::t('contract','Leave Reason'),
            'leave_time'=>Yii::t('contract','Leave Time'),
            'wechat'=>Yii::t('contract','wechat'),
            'recommend_user'=>Yii::t('contract','recommend user'),
            'urgency_card'=>Yii::t('contract','urgency card'),
            'office_id'=>Yii::t('contract','staff office'),
            'table_type'=>Yii::t('contract','Employee Type'),
            'address_code'=>Yii::t('contract','postcode'),
            'contact_address_code'=>Yii::t('contract','postcode'),
            'work_area'=>Yii::t('contract','work area'),
            'bank_type'=>Yii::t('contract','Bank Abbr Name'),
            'bank_number'=>Yii::t('contract','Bank card'),
		);
	}
	/**
     *
	 * Declares the validation rules.
	 */
	public function rules()
	{
		$rules = array(
			//array('id, position, leave_reason, remarks, email, staff_type, leader','safe'),
            array('id, group_type,office_id,work_area,code, name, staff_id, company_id, contract_id, address, address_code, contact_address, contact_address_code, phone, phone2, user_card, department, position, wage,time,
             start_time, end_time, test_type, test_start_time, sex, test_end_time, test_wage, word_status, city, entry_time, age, birth_time, health, ject_remark, staff_status,
              education,wechat,recommend_user,urgency_card, experience, english, technology, other, year_day, email, remark, image_user, image_code, image_work, image_other, fix_time, code_old,
               test_length,staff_type,staff_leader,nation, household, empoyment_code, social_code, user_card_date, emergency_user,
                emergency_phone,bank_type,bank_number','safe'),
            array('id','validateID'),
		);
        //额外规则
		$rules = array_merge($rules,$this->rulesEx());
        //自动完成
        $rules[]=array('id','completeData');
        //附件
        $rules[]=array('files, removeFileId, docMasterId, no_of_attm','safe');
		return $rules;
	}

	protected function rulesEx()
	{
		return array();
	}

    public function getRequiredList(){//必填内容
        return array(
            "city","name","household","staff_id","sex","company_id","contract_id","address",
            "contact_address","phone","user_card","department","position","work_area","image_user",
            "time","fix_time","start_time","test_type","year_day","image_code"
        );
    }
    //验证是否必填

    public function isRequired($str){
        if(in_array($str,$this->getRequiredList())){
            return true;
        }else{
            return false;
        }
    }

	//验证年假
    public function validateYearDay($attribute, $params){
        if(!empty($this->year_day)){
            if(!is_numeric($this->year_day)){
                $message = "年假只能为数字";
                $this->addError($attribute,$message);
            }elseif(floatval($this->year_day)<0){
                $message = "年假不能小于0";
                $this->addError($attribute,$message);
            }else{
                $year_day = strval($this->year_day);
                $year_day = explode('.',$year_day);
                if(count($year_day)===2){
                    $year_day = end($year_day);
                    if($year_day%5 !== 0){
                        $message = "年假必须为0.5的倍数";
                        $this->addError($attribute,$message);
                    }
                }
            }
        }
    }

    //验证合同期限
    public function validateEndTime($attribute, $params){
        if($this->fix_time == "fixation"){
            if(empty($this->end_time)){
                $message = Yii::t('contract','Contract End Time'). Yii::t('contract',' can not be empty');
                $this->addError($attribute,$message);
            }
        }
    }

    //验证工资
    public function validateWage($attribute, $params){
        if(empty($this->wage)){
            if(Yii::app()->user->validFunction('ZR02')){
                $message = Yii::t('contract','Contract Pay'). Yii::t('contract',' can not be empty');
                $this->addError($attribute,$message);
            }else{
                $message = Yii::t('contract','You do not have salary change authority, please save the contact leader');
                $this->addError($attribute,$message);
            }
        }
    }

    //验证试用期
    public function validateTestType($attribute, $params){
        if(!empty($this->test_type)){
            if(empty($this->test_end_time)||empty($this->test_end_time)){
                $message = Yii::t('contract','Probation Time'). Yii::t('contract',' can not be empty');
                $this->addError($attribute,$message);
            }
            if(empty($this->test_wage)){
                $message = Yii::t('contract','Probation Wage'). Yii::t('contract',' can not be empty');
                $this->addError($attribute,$message);
            }elseif (!is_numeric($this->test_wage)){
                $message = Yii::t('contract','Probation Wage'). Yii::t('contract',' Must be Numbers');
                $this->addError($attribute,$message);
            }
        }
    }

    public function completeData($attribute, $params){
        if($this->fix_time == "nofixed"){// 无固定期限
            $this->end_time = null;
        }
        if(intval($this->test_type) != 1){// 无试用期
            $this->test_wage = null;
            $this->test_start_time = null;
            $this->test_end_time = null;
            $this->test_length = null;
        }
    }

    public function validateID($attribute, $params){
	    if($this->getScenario()!='new'){
            $allow_city = Yii::app()->user->city_allow();
            $row = Yii::app()->db->createCommand()->select("id,city")->from("hr_employee")
                ->where("id=:id and city in ({$allow_city})", array(':id'=>$this->id))
                ->queryRow();
            if($row){
                $this->city = $row["city"];
            }else{
                $this->addError($attribute,"员工不存在，请刷新重试");
            }
        }else{
	        $this->city = Yii::app()->user->city();
        }
    }

    public function validateCode($attribute, $params){
        $rows = Yii::app()->db->createCommand()->select()->from("hr_employee")
            ->where('id!=:id and code=:code ', array(':id'=>$this->id,':code'=>$this->code))->queryAll();
        if (count($rows) > 0){
            $message = Yii::t('contract','Employee Code'). Yii::t('contract',' can not repeat');
            $this->addError($attribute,$message);
        }
    }

    protected function getMyAttr(){
        return $arr = array(
            "id"=>1,"group_type"=>3,"office_id"=>3,"table_type"=>3,"code"=>1,"name"=>1,
            "staff_id"=>1,"company_id"=>1,"contract_id"=>1,"address"=>1,"address_code"=>1,
            "contact_address"=>1,"contact_address_code"=>1,"phone"=>1,"phone2"=>1,"user_card"=>1,
            "department"=>1,"position"=>1,"wage"=>1,"word_status"=>1,"attachment"=>1,
            "start_time"=>2,"end_time"=>2,"test_type"=>1,"test_start_time"=>2,
            "sex"=>1,"test_end_time"=>2,"test_wage"=>1,"city"=>1,"staff_status"=>1,
            "entry_time"=>2,"age"=>"birth_time","birth_time"=>2,"health"=>1,"ject_remark"=>1,
            "education"=>1,"wechat"=>1,"recommend_user"=>1,"urgency_card"=>1,
            "experience"=>1,"english"=>1,"technology"=>1,"other"=>1,"year_day"=>1,
            "email"=>1,"remark"=>1,"image_user"=>1,"image_code"=>1,"image_work"=>1,
            "image_other"=>1,"fix_time"=>1,"code_old"=>1,"test_length"=>1,"staff_type"=>1,
            "staff_leader"=>1,"nation"=>1,"household"=>1,"empoyment_code"=>1,"jj_card"=>1,
            "social_code"=>1,"user_card_date"=>1,"emergency_user"=>1,"emergency_phone"=>1,
            "lud"=>1,"lcd"=>1,"lcu"=>1,"luu"=>1,"work_area"=>1,"bank_type"=>3,"bank_number"=>1,
        );
    }

	public function retrieveData($index,$bool=true)
	{
        $suffix = Yii::app()->params['envSuffix'];
        $citySql = "";
        if($bool){
            $allow_city = Yii::app()->user->city_allow();
            $citySql = " and city in ({$allow_city})";
        }
        $row = Yii::app()->db->createCommand()->select("*,docman$suffix.countdoc('EMPLOY',id) as employdoc")->from("hr_employee")
            ->where("id=:id {$citySql}", array(':id'=>$index))->queryRow();
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

    //員工刪除時必須是草稿
    public function validateDelete(){
        $allow_city = Yii::app()->user->city_allow();
        $row = Yii::app()->db->createCommand()->select()->from("hr_employee")
            ->where("id=:id and staff_status in (1,3,4) and city in ({$allow_city})", array(':id'=>$this->id))->queryRow();
        if ($row){
            return true;
        }
        return false;
    }

	public function saveData()
	{
		$connection = Yii::app()->db;
		$transaction=$connection->beginTransaction();
		try {
			$this->saveStaff($connection);
            $this->updateDocman($connection,'EMPLOY');
			$transaction->commit();

			$this->dataEx();//数据保存后的额外操作
            if ($this->scenario=='new') {
                $this->scenario = "edit";
            }
		}catch(Exception $e) {
			$transaction->rollback();
			throw new CHttpException(404,'Cannot update.'.$e->getMessage());
		}
	}

    //数据保存后的额外操作
	protected function dataEx(){

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

    protected function getSaveList() {
        $list=array();
        $arr = array(
            "group_type"=>3,"office_id"=>3,"name"=>1,
            "staff_id"=>1,"company_id"=>3,"contract_id"=>3,"address"=>1,"address_code"=>1,
            "contact_address"=>1,"contact_address_code"=>1,"phone"=>1,"phone2"=>1,"user_card"=>1,
            "department"=>1,"position"=>1,"wage"=>1,"city"=>1,
            "start_time"=>2,"end_time"=>2,"test_type"=>3,"test_start_time"=>2,
            "sex"=>1,"test_end_time"=>2,"test_wage"=>1,"staff_status"=>3,
            "entry_time"=>2,"birth_time"=>2,"age"=>"birth_time","health"=>1,
            "education"=>1,"wechat"=>1,"recommend_user"=>1,"urgency_card"=>1,
            "experience"=>1,"english"=>1,"technology"=>1,"other"=>1,"year_day"=>1,
            "email"=>1,"remark"=>1,"image_user"=>1,"image_code"=>1,"image_work"=>1,
            "image_other"=>1,"fix_time"=>1,"code_old"=>1,"test_length"=>1,"staff_type"=>1,
            "staff_leader"=>1,"nation"=>1,"household"=>1,"empoyment_code"=>1,
            "user_card_date"=>1,"emergency_user"=>1,"emergency_phone"=>1,"work_area"=>1,
            "bank_type"=>3,"bank_number"=>1,
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
                case "birth_time"://年龄
                    $this->$key = isset($this->birth_time)?StaffFun::getAgeForBirthDate($this->birth_time):floatval($value);
                    break;
            }
            $this->$key=$value;
            $list[$key] = $value;
        }
        if(!$this->validateWageInput()){//工资栏位的修改
            unset($list["wage"]);//没有权限不允许修改工资
        }
        return $list;
    }

	protected function saveStaff(&$connection){
	    //保存数据
	}

	protected function sendEmail(){
	    //邮件
    }

    protected function lenStr(){
        $code = strval($this->id);
//Percy: Yii::app()->params['employeeCode']用來處理不同地區版本不同字首
        $this->code = Yii::app()->params['employeeCode'];
        for($i = 0;$i < 5-strlen($code);$i++){
            $this->code.="0";
        }
        $this->code .= $code;
    }

    //給勞務合同的文本賦予默認值
    public static function resetStaff($staffList){
        foreach ($staffList as &$staff){
            foreach ($staff as &$value){
                if ($value === ""){
                    $value = "/";
                }
            }
        }
        return $staffList;
    }

    //生成合同文件
    public static function updateEmployeeWord($employee_id,$arr=array()){
        if(is_array($employee_id)){
            $staff = $employee_id;
        }else{
            $staff = StaffFun::getEmployeeDocListToId($employee_id);
        }
        if (!$staff){
            return false;
        }else{
            try{
                if(!empty($arr)){
                    $staff["word"] = $arr;
                }
                $staff = self::resetStaff($staff);
                $bool = true;
                if ($staff["staff"]["test_type"] != 1){
                    $bool = false;//無試用期
                }
                $contractBool = $staff["staff"]["fix_time"] == "fixation";
                $word = new Template($staff["word"],$bool,$contractBool,$staff["staff"]["city"]);

                if(key_exists("city",$staff["company"])){//如果公司存在
                    $word->setHeader("city",$staff["company"]["city_name"]);
                    $word->setValue("city",$staff["company"]["city_name"]);
                    $word->setValue("companyname",$staff["company"]["name"]);
                    $word->setValue("companyaddresspost2",$staff["company"]["postal2"]);//公司地址2 邮编
                    $word->setValue("companyaddresspost",$staff["company"]["postal"]);//公司地址 邮编
                    $word->setValue("companyaddress2",$staff["company"]["address2"]);//公司地址 2
                    $word->setValue("companyaddress",$staff["company"]["address"]);
                    $word->setValue("companyhead",$staff["company"]["legal"]);//公司负责人  (后期修改：負責人不顯示，顯示法定代表人）
                    $word->setValue("companylegal",$staff["company"]["legal"]);//法定代表人
                    $word->setValue("companyagent",$staff["company"]["agent"]);//委託代理人
                    $word->setValue("companyphone",$staff["company"]["phone"]);
                    $word->setValue("companyprotectno",$staff["company"]["security_code"]);//劳动保障代码
                    $word->setValue("companyorgno",$staff["company"]["organization_code"]);//组织机构代码
                    $word->setValue("companyregno",$staff["company"]["license_code"]);//证照编号
                }

                $word->setValue("staffprovpostcode",$staff["staff"]["address_code"]);//原住地址 邮编
                $word->setValue("staffaddrpostcode",$staff["staff"]["contact_address_code"]);//通讯地址 邮编
                $word->setValue("staffdob",$staff["staff"]["birth_time"]);//出生日期
                $word->setValue("staffage",$staff["staff"]["age"]);//員工年齡
                $word->setValue("staffeducation",Yii::t("staff",$staff["staff"]["education"]));//学历
                $word->setValue("staffjoindate",date("Y-m-d",strtotime($staff["staff"]["entry_time"])));//入职时间
                $word->setValue("stafflanglevel",$staff["staff"]["english"]);//外语水平
                $word->setValue("stafftechlevel",$staff["staff"]["technology"]);//技术水平
                $word->setValue("staffotherinfo",$staff["staff"]["other"]);//补充资料-其它
                $word->setValue("staffprotectno",$staff["staff"]["social_code"]);//社会保障卡号
                $word->setValue("staffregno",$staff["staff"]["empoyment_code"]);//就业登记证号
                $word->setValue("staffprovtype",Yii::t("contract",$staff["staff"]["household"]));//戶籍類型
                $word->setValue("staffhealth",Yii::t("staff",$staff["staff"]["health"]));//身体状况
                $word->setValue("staffworkexp",$staff["staff"]["experience"]);//工作经验

                if(empty($staff["staff"]["work_area"])){//如果地点为空，则默认当前城市
                    $staff["staff"]["work_area"] = $staff["company"]["city_name"];
                }
                $word->setValue("workarea",$staff["staff"]["work_area"]);//主要工作地点
                //2020-04-14新增（開始）
                $word->setValue("staffwechat",$staff["staff"]["wechat"]);//微信賬號
                $word->setValue("staffemergencycard",$staff["staff"]["urgency_card"]);//緊急聯絡人身份證
                //2020-04-14新增（結束）

                $word->setValue("staffemail",$staff["staff"]["email"]);//員工郵箱
                $word->setValue("staffemergencytelno",$staff["staff"]["emergency_phone"]);//紧急联络人電話
                $word->setValue("staffemergency",$staff["staff"]["emergency_user"]);//紧急联络人姓名

                $word->setValue("staffname",$staff["staff"]["name"]);//員工名字
                $word->setValue("staffcode",$staff["staff"]["code"]);//員工編號
                $word->setValue("staffgender",Yii::t("contract",$staff["staff"]["sex"]));//性別
                $word->setValue("staffidno",$staff["staff"]["user_card"]);//身份證號碼
                $word->setValue("staffprov",$staff["staff"]["address"]);//戶籍地址
                $word->setValue("staffaddress",$staff["staff"]["contact_address"]);//現住地址
                $word->setValue("stafftelno",$staff["staff"]["phone"]);//手機號碼
                $word->setValue("staffdept",DeptForm::getDeptToId($staff["staff"]["department"]));
                $word->setValue("staffpost",DeptForm::getDeptToId($staff["staff"]["position"]));
                $word->setValue("staffsalary",$staff["staff"]["wage"]);//stafftestwage

                if($staff["staff"]["fix_time"] == "fixation"){
                    $word->setValue("staffyears1",date("Y",strtotime($staff["staff"]["start_time"])));
                    $word->setValue("staffmonth1",date("m",strtotime($staff["staff"]["start_time"])));
                    $word->setValue("staffday1",date("d",strtotime($staff["staff"]["start_time"])));
                    $word->setValue("staffyears2",date("Y",strtotime($staff["staff"]["end_time"])));
                    $word->setValue("staffmonth2",date("m",strtotime($staff["staff"]["end_time"])));
                    $word->setValue("staffday2",date("d",strtotime($staff["staff"]["end_time"])));
                    $word->setValue("staffyears3","/");
                    $word->setValue("staffmonth3","/");
                    $word->setValue("staffday3","/");

                    $date1 = strtotime($staff["staff"]["end_time"]);
                    $date2 = strtotime($staff["staff"]["start_time"]);
                    $time_difference = $date1 - $date2;
                    $seconds_per_year = 60*60*24*365;
                    $yrs = round($time_difference / $seconds_per_year);
                    $duration = strval($yrs);
                    $word->setValue("staffduration",$duration);
                }else{
                    $word->setValue("staffyears1","/");
                    $word->setValue("staffmonth1","/");
                    $word->setValue("staffday1","/");
                    $word->setValue("staffyears2","/");
                    $word->setValue("staffmonth2","/");
                    $word->setValue("staffday2","/");
                    $word->setValue("staffyears3",date("Y",strtotime($staff["staff"]["start_time"])));
                    $word->setValue("staffmonth3",date("m",strtotime($staff["staff"]["start_time"])));
                    $word->setValue("staffday3",date("d",strtotime($staff["staff"]["start_time"])));
                    $word->setValue("staffduration","/");
                }
                if($staff["staff"]["test_type"]==1){
                    $word->setValue("stafftestyears1",date("Y",strtotime($staff["staff"]["test_start_time"])));
                    $word->setValue("stafftestmonth1",date("m",strtotime($staff["staff"]["test_start_time"])));
                    $word->setValue("stafftestday1",date("d",strtotime($staff["staff"]["test_start_time"])));
                    $word->setValue("stafftestyears2",date("Y",strtotime($staff["staff"]["test_end_time"])));
                    $word->setValue("stafftestmonth2",date("m",strtotime($staff["staff"]["test_end_time"])));
                    $word->setValue("stafftestday2",date("d",strtotime($staff["staff"]["test_end_time"])));
                    $test_start_time = strtotime($staff["staff"]["test_start_time"]);
                    $test_end_time = strtotime($staff["staff"]["test_end_time"]);
                    $yearNum = intval(date("Y",$test_end_time))-intval(date("Y",$test_start_time));
                    $monthNum = intval(date("m",$test_end_time))-intval(date("m",$test_start_time));
                    $testNum = $yearNum*12 + $monthNum;
                    if(intval(date("d",$test_end_time))>intval(date("d",$test_start_time))){
                        $testNum++;
                    }
                }else{
                    $word->setValue("stafftestyears1","/");
                    $word->setValue("stafftestmonth1","/");
                    $word->setValue("stafftestday1","/");
                    $word->setValue("stafftestyears2","/");
                    $word->setValue("stafftestmonth2","/");
                    $word->setValue("stafftestday2","/");
                    $testNum = "/";//　
                    $staff["staff"]["test_wage"]="/";
                }
                $word->setValue("stafftestwage",$staff["staff"]["test_wage"]);
                $word->setValue("stafftest",$testNum);


                $word->save($staff["staff"]["code"]);
                //合同的地址格式：upload/staff/所在地區/員工編號.docx
                $wordUrl = "upload/staff/".$staff["staff"]["city"]."/".$staff["staff"]["code"].".docx";
                if(!is_array($employee_id)){
                    Yii::app()->db->createCommand()->update('hr_employee', array(
                        'word_status'=>1,
                        'word_url'=>$wordUrl
                    ), 'id=:id', array(':id'=>$employee_id));
                }

                return array(
                    "word_url"=>$wordUrl,
                    "name"=>$staff["staff"]["name"]
                );
            }catch (Exception $e){
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Error:Word Error , Not Font Word'));
                return false;
            }
        }
    }

    //工資權限
    public function validateWageInput(){
	    if(Yii::app()->user->validFunction('ZR02')||Yii::app()->user->validRWFunction('ZG01')||Yii::app()->user->validRWFunction('ZG02')){
	        return true;
        }else{
	        return false;
        }
    }

    public function readonly(){
        return $this->scenario=='view'||!in_array($this->staff_status,array(1,3));
    }

    //curl需要的字段
    public function curlData(){
        $list = array();
        $arr = array(
            "scenario"=>1,"id"=>3,"staff_status"=>3,"code"=>1,
            "name"=>1,"sex"=>1,"table_type"=>3,"office_id"=>3,"phone"=>5,
            "city"=>1,"luu"=>5,"lcu"=>5,"lcd"=>4,"contact_address"=>1,"department"=>3,
            "position"=>3,"wage"=>1,"entry_time"=>2,"birth_time"=>2,"leave_time"=>2,
            "age"=>1,"email"=>6,"remark"=>1,"lud"=>4
        );
        foreach ($arr as $key=>$type){
            $value=$this->$key;
            switch ($type){
                case 1://原值
                    $value = $value===""?null:$value;
                    break;
                case 2://日期
                    $value = empty($value)?null:General::toMyDate($value);
                    break;
                case 3://数字
                    $value = $value===""?0:floatval($value);
                    break;
                case 4://日期+时间
                    $value = empty($value)?null:General::toMyDateTime($value);
                    break;
                case 5://必填字段，但lbs为空的时候
                    $value = empty($value)?0:$value;
                    break;
                case 6://邮箱
                    $value = $value===""?null:(StaffFun::isEmail($value)?$value:null);
                    break;
            }
            $this->$key=$value;
            $list[$key] = $value;
        }
        return $list;
    }

    public static function sendCurl($employee_id,$scenario=""){
        if(in_array($scenario,array("new","edit","delete"))){
            $model = new StaffForm($scenario);
            $model->retrieveData($employee_id);
            if(empty($model->id)){
                return false;
            }else{
                $data = $model->curlData();
                $curlModel = new ApiCurl("employee",$data);
                $curlModel->sendCurlAndAdd();
                return true;
            }
        }
        return false;
    }
}
