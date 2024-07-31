<?php

class EmployDown extends CFormModel{
    public $id;
    public $file;
    protected $data;//excel的所有内容

    public $_errorList=array();//导入失败的所有资料
    public $_successList=array();//导入成功的所有资料
    public $_errorSum=0;//失败数量

    private $titleKey=array();//页头对应的列
    private $currentRow=0;

    private $_cityList=array();//城市列表
    private $_deptList=array();//职位列表
    private $_officeList=array();//办事处列表
    private $_companyList=array();//公司列表
    private $_contractList=array();//合同列表
    private $_staffTypeList=array();//员工类别
    private $_nationList=array();//户籍类型
    private $_groupTypeList=array();//组别分类
    private $_staffLeaderList=array();//队长/组长
    private $_healthList=array();//身体状况
    private $_educationList=array();//学历或文化程度
    private $_tableTypeList=array();//表格类型

    private $downDateTime=null;

    public function insertStaticList(){
        $city_allow = Yii::app()->user->city_allow();
        $this->_cityList = StaffFun::getCityForCityAllow($city_allow);
        $this->_deptList = StaffFun::getDeptForCityAllow($city_allow);
        $this->_officeList = StaffFun::getOfficeForCityAllow($city_allow);
        $this->_companyList = StaffFun::getCompanyForCityAllow($city_allow);
        $this->_contractList = StaffFun::getContractForCityAllow($city_allow);
        $this->_staffTypeList = StaffFun::getStaffTypeList();
        $this->_nationList = StaffFun::getNationList();
        $this->_groupTypeList = StaffFun::getGroupTypeList();
        $this->_staffLeaderList = StaffFun::getStaffLeaderList();
        $this->_healthList = StaffFun::getHealthList();
        $this->_educationList = StaffFun::getEducationList();
        $this->_tableTypeList = StaffFun::getTableTypeList(false);
    }
    /**
     *
     * Declares the validation rules.
     */
    public function rules()
    {
        return array(
            array('id,file','safe'),
            array('file', 'file', 'types'=>'xlsx,xls', 'allowEmpty'=>false, 'maxFiles'=>1),
        );
    }

    public static function getTopArr(){
        return array(
            array("width"=>20,"name"=>"姓名","sql"=>"name","max_len"=>100,"background"=>"#AFECFF","color"=>"#000000"),//
            array("width"=>25,"name"=>"公民身份号码","sql"=>"user_card","max_len"=>50,"background"=>"#AFECFF","color"=>"#000000"),//
            array("width"=>12,"name"=>"员工类型","sql"=>"table_type","max_len"=>50,"background"=>"#AFECFF","color"=>"#000000"),//
            array("width"=>12,"name"=>"城市","sql"=>"city","max_len"=>50,"background"=>"#AFECFF","color"=>"#000000"),//
            array("width"=>12,"name"=>"入职日期","dateType"=>true,"sql"=>"entry_time","max_len"=>50,"background"=>"#AFECFF","color"=>"#000000"),//
            array("width"=>15,"name"=>"主要工作地点","sql"=>"work_area","max_len"=>50,"background"=>"#AFECFF","color"=>"#000000"),//
            array("width"=>12,"name"=>"部门","sql"=>"department","max_len"=>100,"background"=>"#AFECFF","color"=>"#000000"),//
            array("width"=>12,"name"=>"职位","sql"=>"position","max_len"=>100,"background"=>"#AFECFF","color"=>"#000000"),//
            array("width"=>15,"name"=>"办事处","sql"=>"office_id","max_len"=>100,"background"=>"#AFECFF","color"=>"#000000"),//
            array("width"=>12,"name"=>"员工类别","sql"=>"staff_type","max_len"=>100,"background"=>"#AFECFF","color"=>"#000000"),//
            array("width"=>12,"name"=>"组别分类","sql"=>"group_type","max_len"=>100,"background"=>"#AFECFF","color"=>"#000000"),//
            array("width"=>15,"name"=>"队长/组长","sql"=>"staff_leader","max_len"=>100,"background"=>"#AFECFF","color"=>"#000000"),//
            array("width"=>20,"name"=>"员工归属","sql"=>"staff_id","max_len"=>200,"background"=>"#AFECFF","color"=>"#000000"),//
            array("width"=>20,"name"=>"员工合同归属","sql"=>"company_id","max_len"=>200,"background"=>"#AFECFF","color"=>"#000000"),//
            array("width"=>25,"name"=>"员工合同模版","sql"=>"contract_id","max_len"=>200,"background"=>"#AFECFF","color"=>"#000000"),//
            array("width"=>12,"name"=>"合同期限","sql"=>"fix_time","max_len"=>100,"background"=>"#AFECFF","color"=>"#000000"),//
            array("width"=>20,"name"=>"劳动合同起始日期","dateType"=>true,"sql"=>"start_time","max_len"=>50,"background"=>"#AFECFF","color"=>"#000000"),//
            array("width"=>20,"name"=>"劳动合同终止日期","dateType"=>true,"sql"=>"end_time","max_len"=>50,"background"=>"#AFECFF","color"=>"#000000"),//
            array("width"=>12,"name"=>"合同工资","sql"=>"wage","max_len"=>18,"background"=>"#AFECFF","color"=>"#000000"),//
            array("width"=>15,"name"=>"试用期类型","sql"=>"test_type","max_len"=>50,"background"=>"#AFECFF","color"=>"#000000"),//
            array("width"=>15,"name"=>"试用期时期","sql"=>"test_length","max_len"=>50,"background"=>"#AFECFF","color"=>"#000000"),//
            array("width"=>20,"name"=>"试用期起始时间","dateType"=>true,"sql"=>"test_start_time","max_len"=>50,"background"=>"#AFECFF","color"=>"#000000"),//
            array("width"=>15,"name"=>"试用期工资","sql"=>"test_wage","max_len"=>18,"background"=>"#AFECFF","color"=>"#000000"),//
            array("width"=>12,"name"=>"年假","sql"=>"year_day","max_len"=>8,"background"=>"#AFECFF","color"=>"#000000"),//
            array("width"=>20,"name"=>"邮箱","sql"=>"email","max_len"=>50,"background"=>"#AFECFF","color"=>"#000000"),//
            array("width"=>15,"name"=>"微信账号","sql"=>"wechat","max_len"=>50,"background"=>"#AFECFF","color"=>"#000000"),//
            array("width"=>12,"name"=>"员工电话","sql"=>"phone","max_len"=>50,"background"=>"#AFECFF","color"=>"#000000"),//
            array("width"=>20,"name"=>"公民身份证有效期","sql"=>"user_card_date","max_len"=>50,"background"=>"#AFECFF","color"=>"#000000"),//
            array("width"=>12,"name"=>"出生日期","dateType"=>true,"sql"=>"birth_time","max_len"=>50,"background"=>"#AFECFF","color"=>"#000000"),//
            array("width"=>12,"name"=>"性别","sql"=>"sex","max_len"=>10,"background"=>"#AFECFF","color"=>"#000000"),//
            array("width"=>12,"name"=>"身体状况","sql"=>"health","max_len"=>50,"background"=>"#AFECFF","color"=>"#000000"),//
            array("width"=>12,"name"=>"民族","sql"=>"nation","max_len"=>50,"background"=>"#AFECFF","color"=>"#000000"),//
            array("width"=>20,"name"=>"学历或文化程度","sql"=>"education","max_len"=>50,"background"=>"#AFECFF","color"=>"#000000"),//
            array("width"=>20,"name"=>"工作经验","sql"=>"experience","background"=>"#AFECFF","color"=>"#000000"),//
            array("width"=>20,"name"=>"外语水平","sql"=>"english","background"=>"#AFECFF","color"=>"#000000"),//
            array("width"=>20,"name"=>"技术水平","sql"=>"technology","background"=>"#AFECFF","color"=>"#000000"),//
            array("width"=>20,"name"=>"其它","sql"=>"other","background"=>"#AFECFF","color"=>"#000000"),//
            array("width"=>12,"name"=>"户籍类型","sql"=>"household","max_len"=>50,"background"=>"#AFECFF","color"=>"#000000"),//
            array("width"=>25,"name"=>"户籍地址","sql"=>"address","max_len"=>200,"background"=>"#AFECFF","color"=>"#000000"),//
            array("width"=>25,"name"=>"通讯地址","sql"=>"contact_address","max_len"=>200,"background"=>"#AFECFF","color"=>"#000000"),//
            array("width"=>25,"name"=>"社保卡号","sql"=>"social_code","max_len"=>50,"background"=>"#AFECFF","color"=>"#000000"),//
            array("width"=>25,"name"=>"公积金卡","sql"=>"jj_card","max_len"=>50,"background"=>"#AFECFF","color"=>"#000000"),//
            array("width"=>25,"name"=>"就业登记证号","sql"=>"empoyment_code","max_len"=>50,"background"=>"#AFECFF","color"=>"#000000"),//
            array("width"=>20,"name"=>"紧急联络人姓名","sql"=>"emergency_user","max_len"=>50,"background"=>"#AFECFF","color"=>"#000000"),//
            array("width"=>20,"name"=>"紧急联系人身份证","sql"=>"urgency_card","max_len"=>50,"background"=>"#AFECFF","color"=>"#000000"),//
            array("width"=>20,"name"=>"紧急联络人手机号","sql"=>"emergency_phone","max_len"=>50,"background"=>"#AFECFF","color"=>"#000000"),//
            array("width"=>20,"name"=>"备注","sql"=>"remark","background"=>"#AFECFF","color"=>"#000000"),//
        );
    }

    public function loadData($excelArr){
        $this->downDateTime = date("Y-m-d H:i:s");
        $this->data = key_exists("listBody",$excelArr)?$excelArr["listBody"]:array();

        if($this->validateHeader()){
            $this->addDataBody();
            return true;
        }else{
            return false;
        }
    }

    private function addDataBody(){
        $data = $this->data;
        if(isset($data[0])){
            unset($data[0]);
        }
        $headList = self::getTopArr();
        $connection = Yii::app()->db;
        $transaction=$connection->beginTransaction();
        $uid = Yii::app()->user->id;
        try {
            foreach ($data as $row){
                $addList = array();//需要保存的字段
                $bool=true;
                foreach ($headList as $title){
                    $column = $this->titleKey[$title["sql"]]["column"];
                    $value = $row[$column];
                    $bool = $this->resetTempForTitle($addList,$value,$title,$row);
                    if($bool===false){ //数据有异常
                        break;
                    }
                }
                if($bool){
                    $this->saveData($connection,$addList,$row);
                }
                $this->currentRow++;
            }
            $transaction->commit();
        }catch(Exception $e) {
            $transaction->rollback();
            throw new CHttpException(404,$e->getMessage());
        }
    }

    private function saveData(&$connection,$temp,$dataBody){
        $this->_successList[] = $dataBody;
        $temp["lcu"]=Yii::app()->user->id;
        $temp["remark"]=empty($temp["remark"])?"":$temp["remark"];
        $temp["remark"].="\n导入账号：{$temp["lcu"]}\n导入时间：".date("Y-m-d H:i:s");
        $connection->createCommand()->insert("hr_employee", $temp);
        $this->id = Yii::app()->db->getLastInsertID();
        //生成员工编号
        $code = $temp["table_type"]==1?$this->lenStr():$this->lenStrForOther();
        $connection->createCommand()->update("hr_employee", array(
            "code"=>$code
        ),"id=".$this->id);//保存员工编号

        if($temp["table_type"]==1){//专职
            $connection->createCommand()->insert("hr_employee_history", array(
                "employee_id"=>$this->id,
                "status"=>"inset",
                "remark"=>"导入",
                "lcu"=>$temp["lcu"],
            ));
        }else{
            $connection->createCommand()->insert("hr_table_history", array(
                "table_id"=>$this->id,
                "table_name"=>"hr_employee",
                "update_type"=>2,
                "update_html"=>"<span>导入</span>",
                "lcu"=>$temp["lcu"],
            ));
        }
    }

    private function lenStr(){ //专职的员工编号
        $id = strval($this->id);
        $code = Yii::app()->params['employeeCode'];
        for($i = 0;$i < 5-strlen($id);$i++){
            $code.="0";
        }
        $code .= $id;
        return $code;
    }

    protected function lenStrForOther(){ //外聘、兼职的员工编号
        $codeStr = "E";
        $codeLength = strlen($codeStr)+1;
        $numberSql = "SUBSTRING(code,{$codeLength})";
        $maxCode = Yii::app()->db->createCommand()
            ->select("max(CONVERT({$numberSql}, UNSIGNED))")
            ->from("hr_employee")->where("table_type in (2,3)")->queryScalar();
        $maxCode = empty($maxCode)||!is_numeric($maxCode)?0:$maxCode;
        $maxCode++;
        $code = strval($maxCode);
        $returnCode = $codeStr;
        for($i = 0;$i < 5-strlen($code);$i++){
            $returnCode.="0";
        }
        $returnCode .= $code;
        return $returnCode;
    }

    private function resetTempForTitle(&$temp,$value,$title,$dataRow){
        if(isset($title["dateType"])&&$title["dateType"]&&!empty($value)){
            //转换excel内的日期格式
        }
        if(isset($title["max_len"])&&mb_strlen($value, 'UTF-8')>=$title["max_len"]){
            $dataRow["error"]=$title["name"]."长度不能大于".$title["max_len"];
            $this->_errorList[]=$dataRow;
            $this->_errorSum++;
            return false;
        }
        $bool = true;
        switch ($title["sql"]){
            case "entry_time"://入职日期
                $dateObj = DateTime::createFromFormat('Y-m-d',$value);
                if (empty($value)||$dateObj===false){
                    $dataRow["error"]=$title["name"]."时间格式异常";
                    $this->_errorList[]=$dataRow;
                    $this->_errorSum++;
                    return false;
                }else{
                    $username = $temp["name"];
                    $city = $temp["city"];
                    $value = $dateObj->format('Y-m-d');
                    $row = Yii::app()->db->createCommand()->select("id,code")->from("hr_employee")
                        ->where("city='{$city}' and staff_status!='-1' and name='{$username}' and date_format(entry_time,'%Y-%m-%d') = '{$value}'")
                        ->queryRow();
                    if($row){
                        $dataRow["error"]=$title["name"]."员工名称重复，重复员工：".$row["code"];
                        $this->_errorList[]=$dataRow;
                        $this->_errorSum++;
                        return false;
                    }else{
                        $temp[$title["sql"]]=$value;
                    }
                }
                break;
            case "table_type"://员工类型(1：专职，2：兼职，3：外聘)
                $tableType = array_search($value,$this->_tableTypeList);
                if($tableType==false){
                    $dataRow["error"]=$title["name"]."不存在";
                    $this->_errorList[]=$dataRow;
                    $this->_errorSum++;
                    return false;
                }else{
                    $temp[$title["sql"]]=$tableType;
                    $temp["staff_status"]=$tableType==1?0:1;
                }
                break;
            case "city"://城市
                $code = array_search($value,$this->_cityList);
                if($code===false){
                    $dataRow["error"]=$title["name"]."城市不存在或没有权限管辖该城市";
                    $this->_errorList[]=$dataRow;
                    $this->_errorSum++;
                    return false;
                }else{
                    $temp[$title["sql"]]=$code;
                }
                break;
            case "department"://部门
            case "position"://部门
                $city = $temp["city"];
                if(key_exists($city,$this->_deptList)){
                    $dept_id = array_search($value,$this->_deptList[$city]);
                    if($dept_id===false){
                        $dataRow["error"]=$title["name"]."不存在";
                        $this->_errorList[]=$dataRow;
                        $this->_errorSum++;
                        return false;
                    }else{
                        $temp[$title["sql"]]=$dept_id;
                    }
                }else{
                    $dataRow["error"]=$title["name"]."该城市没有职位信息，请与管理员联系";
                    $this->_errorList[]=$dataRow;
                    $this->_errorSum++;
                    return false;
                }
                break;
            case "office_id"://办事处
                $city = $temp["city"];
                if($value!="本部"&&!empty($value)&&key_exists($city,$this->_officeList)){
                    $office_id = array_search($value,$this->_officeList[$city]);
                    if($office_id===false){
                        $dataRow["error"]=$title["name"]."不存在";
                        $this->_errorList[]=$dataRow;
                        $this->_errorSum++;
                        return false;
                    }else{
                        $temp[$title["sql"]]=$office_id;
                    }
                }
                break;
            case "staff_type"://员工类别
                $key = array_search($value,$this->_staffTypeList);
                if($key!==false){
                    $temp[$title["sql"]]=$key;
                }
                break;
            case "group_type"://组别分类
                $key = array_search($value,$this->_groupTypeList);
                if($key!==false){
                    $temp[$title["sql"]]=$key;
                }
                break;
            case "staff_leader"://队长/组长
                $key = array_search($value,$this->_staffLeaderList);
                if($key!==false){
                    $temp[$title["sql"]]=$key;
                }
                break;
            case "staff_id"://员工归属
            case "company_id"://员工合同归属
                $city = $temp["city"];
                if(key_exists($city,$this->_companyList)){
                    $company_id = array_search($value,$this->_companyList[$city]);
                    if($company_id===false){
                        $dataRow["error"]=$title["name"]."不存在";
                        $this->_errorList[]=$dataRow;
                        $this->_errorSum++;
                        return false;
                    }else{
                        $temp[$title["sql"]]=$company_id;
                    }
                }else{
                    $dataRow["error"]=$title["name"]."该城市没有公司信息，请与管理员联系";
                    $this->_errorList[]=$dataRow;
                    $this->_errorSum++;
                    return false;
                }
                break;
            case "contract_id"://员工合同模版
                $city = $temp["city"];
                $contract_id = false;
                if($contract_id===false&&key_exists($city,$this->_contractList)){
                    //查找本城市合同模版
                    $contract_id = array_search($value,$this->_contractList[$city]);
                }
                if($contract_id===false&&key_exists("all",$this->_contractList)){
                    //查找通用合同模版
                    $contract_id = array_search($value,$this->_contractList["all"]);
                }
                if($contract_id===false){
                    $temp[$title["sql"]]=null;
                    /*2024-04-02不需要验证
                    $dataRow["error"]=$title["name"]."不存在";
                    $this->_errorList[]=$dataRow;
                    $this->_errorSum++;
                    return false;
                    */
                }else{
                    $temp[$title["sql"]]=$contract_id;
                }
                break;
            case "fix_time"://员工归属
                $value = $value=="有固定期限"?"fixation":"nofixed";
                $temp[$title["sql"]]=$value;
                break;
            case "start_time"://合同起始时间
                $dateObj = DateTime::createFromFormat('Y-m-d',$value);
                if (empty($value)||$dateObj===false){
                    $dataRow["error"]=$title["name"]."时间格式异常";
                    $this->_errorList[]=$dataRow;
                    $this->_errorSum++;
                    return false;
                }else{
                    $temp[$title["sql"]]=$dateObj->format('Y-m-d');
                }
                break;
            case "end_time"://劳动合同终止日期
                if($temp["fix_time"]=="fixation"){
                    $dateObj = DateTime::createFromFormat('Y-m-d',$value);
                    if (empty($value)||$dateObj===false){
                        $temp[$title["sql"]]=null;
                    /*2024-04-02不需要验证
                        $dataRow["error"]=$title["name"]."时间格式异常";
                        $this->_errorList[]=$dataRow;
                        $this->_errorSum++;
                        return false;
                    */
                    }else{
                        $temp[$title["sql"]]=$dateObj->format('Y-m-d');
                    }
                }
                break;
            case "wage"://合同工资
            case "test_wage"://试用期工资
                if(!empty($value)&&!is_numeric($value)){
                    $dataRow["error"]=$title["name"]."只能为数字";
                    $this->_errorList[]=$dataRow;
                    $this->_errorSum++;
                    return false;
                }else{
                    $temp[$title["sql"]]=$value;
                }
                break;
            case "test_type"://试用期类型
                $value = $value=="有试用期"?1:0;
                $temp[$title["sql"]]=$value;
                break;
            case "test_length"://试用期时期
                if($temp["test_type"]==1){
                    $value = intval($value);
                    $value = is_numeric($value)?$value:1;
                    $temp[$title["sql"]]=$value;
                }
                break;
            case "test_start_time"://试用期起始时间
                if($temp["test_type"]==1){
                    $dateObj = DateTime::createFromFormat('Y-m-d',$value);
                    if(empty($value)||$dateObj===false){
                        $dataRow["error"]=$title["name"]."时间格式异常";
                        $this->_errorList[]=$dataRow;
                        $this->_errorSum++;
                        return false;
                    }else{
                        $len = $temp["test_length"];
                        $startDate = $dateObj->format('Y-m-d');
                        $dateObj->modify("{$len} month");
                        $endDate = $dateObj->format('Y-m-d');
                        $temp[$title["sql"]]=$startDate;
                        $temp["test_end_time"]=$endDate;
                    }
                }
                break;
            case "user_card_date"://公民身份证有效期
            case "birth_time"://出生日期
                $dateObj = DateTime::createFromFormat('Y-m-d',$value);
                if(!empty($value)&&$dateObj===false){
                    $dataRow["error"]=$title["name"]."时间格式异常";
                    $this->_errorList[]=$dataRow;
                    $this->_errorSum++;
                    return false;
                }else{
                    $temp[$title["sql"]]=empty($value)?null:$dateObj->format('Y-m-d');
                }
                break;
            case "sex"://性别
                $value = $value=="男"?"man":"woman";
                $temp[$title["sql"]]=$value;
                break;
            case "health"://身体状况
                $key = array_search($value,$this->_healthList);
                if($key!==false){
                    $temp[$title["sql"]]=$key;
                }
                break;
            case "education"://学历或文化程度
                $key = array_search($value,$this->_educationList);
                if($key!==false){
                    $temp[$title["sql"]]=$key;
                }
                break;
            case "household"://户籍类型
                $key = array_search($value,$this->_nationList);
                if($key!==false){
                    $temp[$title["sql"]]=$key;
                }
                break;
            default:
                $temp[$title["sql"]]=$value;
        }
        return $bool;
    }

    private function validateHeader(){
        $bool = true;
        $headList = reset($this->data);
        $rows = self::getTopArr();
        foreach ($rows as $row){
            $key = array_search($row["name"],$headList);
            if($key===false){
                $this->addError("file", "Excel的第四行没有找到 ".$row["name"]);
                $bool = false;
            }else{
                $row['column'] = $key;
                $this->titleKey[$row["sql"]]=$row;
            }
        }
        return $bool;
    }

    //下载导入模板
    public function downTemp(){
        $excelData=array(
            array(
                "name"=>"例子1",
                "user_card"=>"360428199211111111",
                "table_type"=>"专职",
                "city"=>"ZHU HAI",
                "entry_time"=>"2024-03-22",
                "work_area"=>"香洲",
                "department"=>"it",
                "position"=>"初級",
                "office_id"=>"本部",
                "staff_type"=>"办公室",
                "group_type"=>"商业组",
                "staff_leader"=>"组长",
                "staff_id"=>"珠海史伟莎",
                "company_id"=>"珠海史伟莎",
                "contract_id"=>"IT合同",
                "fix_time"=>"有固定期限",
                "start_time"=>"2024-03-20",
                "end_time"=>"2027-03-20",
                "wage"=>"4000",
                "test_type"=>"有试用期",
                "test_length"=>"2个月",
                "test_start_time"=>"2024-03-20",
                "test_wage"=>"3000",
                "year_day"=>"5",
                "email"=>"1111111@qq.com",
                "wechat"=>"111112",
                "phone"=>"1801111111",
                "user_card_date"=>"2047-05-10",
                "birth_time"=>"1992-11-11",
                "sex"=>"男",
                "health"=>"良好",
                "nation"=>"汉族",
                "education"=>"大专",
                "experience"=>"",
                "english"=>"",
                "technology"=>"",
                "other"=>"",
                "household"=>"非农户口",
                "address"=>"九江市香洲区某某街道某某号",
                "contact_address"=>"珠海市某某街道某某号",
                "social_code"=>"22222222222",
                "jj_card"=>"3333333333",
                "empoyment_code"=>"4444444444",
                "emergency_user"=>"朋友1",
                "urgency_card"=>"36042819922222222222",
                "emergency_phone"=>"15022222222",
                "remark"=>"测试导入",
            ),
            array(
                "name"=>"例子2",
                "user_card"=>"360428199011111111",
                "table_type"=>"兼职",
                "city"=>"ZHU HAI",
                "entry_time"=>"2024-04-22",
                "work_area"=>"香洲",
                "department"=>"it",
                "position"=>"初級",
                "office_id"=>"本部",
                "staff_type"=>"办公室",
                "group_type"=>"商业组",
                "staff_leader"=>"组长",
                "staff_id"=>"珠海史伟莎",
                "company_id"=>"珠海史伟莎",
                "contract_id"=>"IT合同",
                "fix_time"=>"无固定期限",
                "start_time"=>"2024-03-20",
                "end_time"=>"",
                "wage"=>"4000",
                "test_type"=>"无试用期",
                "test_length"=>"",
                "test_start_time"=>"",
                "test_wage"=>"",
                "year_day"=>"7",
                "email"=>"222222@qq.com",
                "wechat"=>"333332",
                "phone"=>"180333333",
                "user_card_date"=>"2067-05-10",
                "birth_time"=>"1990-11-11",
                "sex"=>"女",
                "health"=>"良好",
                "nation"=>"汉族",
                "education"=>"研究生",
                "experience"=>"1",
                "english"=>"2",
                "technology"=>"3",
                "other"=>"4",
                "household"=>"农业户口",
                "address"=>"深圳市香洲区某某街道某某号",
                "contact_address"=>"广州市某某街道某某号",
                "social_code"=>"a22222222222",
                "jj_card"=>"b3333333333",
                "empoyment_code"=>"c4444444444",
                "emergency_user"=>"朋友2",
                "urgency_card"=>"36042819902222222222",
                "emergency_phone"=>"14022222222",
                "remark"=>"测试导入2",
            ),
        );
        $headList = self::getTopArr();
        $excel = new DownArrExcel();
        $excel->colTwo=1;
        $excel->SetHeaderTitle("导入模板");
        $str="注：从第5行开始（例子可以删除）\n";
        $str.="列宽、行高可以拉伸（不影响导入）";
        $excel->SetHeaderString($str);
        $excel->init();
        $excel->setHeaderForOneList($headList);
        $excel->setHeight(4,30);
        $excel->setArrData($excelData);
        $excel->outExcel("导入模板");
    }

    //下载失败列表
    public function downErrorList(){
        $error = array(
            array("name"=>"失败原因","sql"=>"error","width"=>"35","background"=>"#f0ff9d","color"=>"#a94442"),//
        );
        $headList = reset($this->data);
        $headList = array_merge($error,$headList);
        $group["group"][]='attr';
        $excel = new DownArrExcel();
        $excel->colTwo=1;
        $excel->SetHeaderTitle("导入失败");
        $str="注：导入失败的文档修改后可以重新导入\n";
        $str.="只需要修改单元格内容";
        $excel->SetHeaderString($str);
        $excel->init();
        $excel->setHeaderForErrorList($headList);
        $excel->setHeight(4,30);
        $excel->setErrorData($this->_errorList,$error);
        $excel->outExcel("导入失败");
    }
}
