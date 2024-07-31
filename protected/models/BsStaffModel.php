<?php
//北森员工同步逻辑
class BsStaffModel {
    protected $_successList=array();
    protected $_errorList=array();

	public static $data=array(
        "employeeInfo"=>array("requite"=>true,"function"=>"validateStaffInfo"),//查询员工信息数据结果
        "recordInfo"=>array("requite"=>true,"function"=>"validateRecordInfo"),//任职记录信息列表
	);

    public static function validateRecordInfo(&$data,$keyStr){//
        $recordInfo = key_exists($keyStr,$data)?$data[$keyStr]:array();
        $staffList = $data["staffList"];
        //员工类型
        if(is_array($recordInfo["translateProperties"])&&key_exists("EmploymentFormText",$recordInfo["translateProperties"])){
            $table_type = self::getTableTypeForBsForm($recordInfo["translateProperties"]["EmploymentFormText"]);
            $data["staffList"]["table_type"] = $table_type;
            $data["staffList"]["staff_status"] = $table_type==1?0:1;
        }else{
            $data["staffList"]["table_type"] = 1;
            $data["staffList"]["staff_status"] = 0;
        }
        $boolStaff = $data["staffList"]["table_type"]==1;//兼职员工不需要验证
        if(key_exists("jobNumber",$recordInfo)){//员工编号
            $staffCode = $recordInfo["jobNumber"];
            if(empty($staffCode)){
                if($boolStaff){
                    return array("bool"=>false,"error"=>$staffList["name"]."：员工编号不能为空。");
                }
            }else{
                $data["staffList"]["code"] = $staffCode;
            }
        }else{
            if($boolStaff){
                return array("bool"=>false,"error"=>$staffList["name"]."：员工编号不存在。");
            }
        }
        if ($boolStaff){//正式员工
            $staffId = self::getEmployeeIdForStaffCode($data["staffList"]["code"]);
        }else{//外部人员
            if(!key_exists("user_card",$data["staffList"])||empty($data["staffList"]["user_card"])){
                return array("bool"=>true,"error"=>$staffList["name"]."：身份证号不能为空。");
            }else{
                $staffId = self::getEmployeeIdForUserCard($data["staffList"]["user_card"]);
            }
        }
        if(!empty($staffId)){
            $data["staffList"]["db_type"] = "update";
            $data["staffList"]["id"] = $staffId;
        }else{
            $data["staffList"]["db_type"] = "add";
        }
        //入职日期
        if(key_exists("entryDate",$recordInfo)){//入职日期
            if(empty($recordInfo["entryDate"])){
                return array("bool"=>false,"error"=>$staffList["name"]."：入职日期不能为空。");
            }else{
                $data["staffList"]["entry_time"] = date_format(date_create($recordInfo["entryDate"]),"Y/m/d");
            }
        }else{
            return array("bool"=>false,"error"=>$staffList["name"]."：入职日期不存在。");
        }
        //部门
        if(key_exists("oIdDepartment",$recordInfo)){//部门
            if(empty($recordInfo["oIdDepartment"])){
                return array("bool"=>false,"error"=>$staffList["name"]."：部门不能为空。");
            }else{
                $data["staffList"]["department"] = self::getDeptIDForBsDept($recordInfo["oIdDepartment"],$recordInfo["translateProperties"]["OIdDepartmentText"],0);
            }
        }else{
            return array("bool"=>false,"error"=>$staffList["name"]."：部门不存在。");
        }
        //职位
        if(key_exists("oIdJobPost",$recordInfo)){//职位
            if(empty($recordInfo["oIdJobPost"])){
                if($boolStaff){
                    return array("bool"=>false,"error"=>$staffList["name"]."：职位不能为空。");
                }
            }else{
                $oldJobPostText = key_exists("OIdJobPostText",$recordInfo["translateProperties"])?$recordInfo["translateProperties"]["OIdJobPostText"]:"";
                $data["staffList"]["position"] = self::getDeptIDForBsDept($recordInfo["oIdJobPost"],$oldJobPostText,1);
            }
        }else{
            if($boolStaff){
                return array("bool"=>false,"error"=>$staffList["name"]."：职位不存在。");
            }
        }
        //试用期
        if(key_exists("isHaveProbation",$recordInfo)){//试用期
            if(!empty($recordInfo["isHaveProbation"])){ //有试用期
                $start = new DateTime($recordInfo["probationStartDate"]);
                $end = new DateTime($recordInfo["probationStopDate"]);
                $startTime = $start->getTimestamp();
                $endTime = $end->getTimestamp();
                $interval = $endTime-$startTime;
                $interval = $interval/(60*60*24*30);
                $interval = round($interval);
                $data["staffList"]["test_type"] = 1;
                $data["staffList"]["test_length"] = $interval>6?6:$interval;
                $data["staffList"]["test_start_time"] = $start->format('Y-m-d');
                $data["staffList"]["test_end_time"] = $end->format('Y-m-d');
            }else{
                $data["staffList"]["test_type"] = 0;
                $data["staffList"]["test_length"] = null;
                $data["staffList"]["test_start_time"] = null;
                $data["staffList"]["test_end_time"] = null;
            }
        }
        //城市(旧)extsuozaidiqu_611774_1587630752
        if(is_array($recordInfo["customProperties"])&&key_exists("extsuozaidiqu_611774_1587630752",$recordInfo["customProperties"])){
            $bsCityName = $recordInfo["customProperties"]["extsuozaidiqu_611774_1587630752"];
            $city = self::getCityForBsCityName($bsCityName);
            if(!empty($city)){
                $data["staffList"]["city"] = $city;
            }else{
                return array("bool"=>false,"error"=>$staffList["name"]."：LBS不存在城市({$bsCityName})。");
            }
        }else{
            return array("bool"=>false,"error"=>$staffList["name"]."：不存在城市字段。");
        }
        /*
        //城市 （新）extlbschengshi_611774_1703426123
        if(is_array($recordInfo["customProperties"])&&key_exists("extlbschengshi_611774_1703426123",$recordInfo["customProperties"])){
            $bsCityCode = $recordInfo["customProperties"]["extlbschengshi_611774_1703426123"];
            $city = self::getCityForBsCityCode($bsCityCode);
            if(!empty($city)){
                $data["staffList"]["city"] = $city;
            }else{
                //return array("bool"=>false,"error"=>$staffList["name"]."：LBS不存在城市({$bsCityCode})。");
            }
        }else{
            //return array("bool"=>false,"error"=>$staffList["name"]."：不存在城市字段。");
        }
        if(!isset($data["staffList"]["city"])){
            return array("bool"=>false,"error"=>$staffList["name"]."：城市字段不能为空。");
        }
        */
        //队长/组长
        if(is_array($recordInfo["customProperties"])&&key_exists("extduizhangzuzhang_611774_1810504733",$recordInfo["customProperties"])){
            $staff_leader = self::getStaffLeaderForBsDui($recordInfo["customProperties"]["extduizhangzuzhang_611774_1810504733"]);
            $data["staffList"]["staff_leader"] = $staff_leader;
        }
        //离职日期
        if(key_exists("employeeStatus",$recordInfo)&&$recordInfo["employeeStatus"]==8) {//离职
            $data["staffList"]["staff_status"] = -1;//离职
            $data["staffList"]["leave_time"] = date_format(date_create($recordInfo["lastWorkDate"]),"Y/m/d");
            $data["staffList"]["leave_reason"] = $recordInfo["changeReason"];
        }
        //备注
        if(key_exists("remarks",$recordInfo)) {//备注
            $data["staffList"]["remark"] = $recordInfo["remarks"];
        }
        //员工类别
        if(is_array($recordInfo["translateProperties"])&&key_exists("OIdJobSequenceText",$recordInfo["translateProperties"])){
            $staff_type = self::getStaffTypeForBsSequence($recordInfo["translateProperties"]["OIdJobSequenceText"]);
            if(!empty($staff_type)){
                $data["staffList"]["staff_type"] = $staff_type;
            }
        }
        //员工归属
        if(is_array($recordInfo["translateProperties"])&&key_exists("extyuangongguishugongsi_611774_795442796Text",$recordInfo["translateProperties"])){
            $bsCompanyText=$recordInfo["translateProperties"]["extyuangongguishugongsi_611774_795442796Text"];
            $bsCompanyID=isset($recordInfo["customProperties"]["extyuangongguishugongsi_611774_795442796"])?$recordInfo["customProperties"]["extyuangongguishugongsi_611774_795442796"]:null;
            if(empty($bsCompanyText)){
                if($boolStaff){
                    return array("bool"=>false,"error"=>$staffList["name"]."：员工归属不能为空。");
                }
            }else{
                $staff_id = self::getCompanyIdForBsGuiShu($data,$bsCompanyText,$bsCompanyID);
                if(!empty($staff_id)){
                    $data["staffList"]["staff_id"] = $staff_id;
                }
            }
        }else{
            if($boolStaff){
                return array("bool"=>false,"error"=>$staffList["name"]."：员工归属不存在。");
            }
        }
        //员工合同归属
        if(is_array($recordInfo["translateProperties"])&&key_exists("extyuangonghetongguishugongsi_611774_503095597Text",$recordInfo["translateProperties"])){
            $bsCompanyText=$recordInfo["translateProperties"]["extyuangonghetongguishugongsi_611774_503095597Text"];
            $bsCompanyID=isset($recordInfo["customProperties"]["extyuangonghetongguishugongsi_611774_503095597"])?$recordInfo["customProperties"]["extyuangonghetongguishugongsi_611774_503095597"]:null;
            if(empty($bsCompanyText)){
                if($boolStaff){
                    return array("bool"=>false,"error"=>$staffList["name"]."：员工合同归属不能为空。");
                }
            }else{
                $company_id = self::getCompanyIdForBsGuiShu($data,$bsCompanyText,$bsCompanyID);
                if(!empty($company_id)){
                    $data["staffList"]["company_id"] = $company_id;
                }
            }
        }else{
            if($boolStaff){
                return array("bool"=>false,"error"=>$staffList["name"]."：员工合同归属不存在。");
            }
        }

        return array("bool"=>true);
    }

    public static function validateStaffInfo(&$data,$keyStr){//
        $staffInfo = key_exists($keyStr,$data)?$data[$keyStr]:array();
        if(key_exists("name",$staffInfo)){
            //初始化需要保存的员工信息
            $staffList=array("name"=>$staffInfo["name"],"bs_staff_id"=>$staffInfo["userID"]);

            self::setStaffListForStr($staffList,$staffInfo,"gender","sex");//性别
            self::setStaffListForStr($staffList,$staffInfo,"birthday","birth_time");//出生日期
            self::setStaffListForStr($staffList,$staffInfo,"residenceAddress","address");//户籍地址
            self::setStaffListForStr($staffList,$staffInfo,"postalCode","address_code");//邮政编码
            self::setStaffListForStr($staffList,$staffInfo,"homeAddress","contact_address");//通讯地址
            self::setStaffListForStr($staffList,$staffInfo,"mobilePhone","phone");//员工电话
            //self::setStaffListForStr($staffList,$staffInfo,"","phone2");//紧急电话
            self::setStaffListForStr($staffList,$staffInfo,"iDNumber","user_card");//身份证号码
            self::setStaffListForStr($staffList,$staffInfo,"certificateValidityTerm","user_card_date");//身份证有效期
            self::setStaffListForStr($staffList,$staffInfo,"weiXin","wechat");//微信账号
            //self::setStaffListForStr($staffList,$staffInfo,"","urgency_card");//紧急联系人身份证
            self::setStaffListForStr($staffList,$staffInfo,"emergencyContact","emergency_user");//紧急联络人姓名
            self::setStaffListForStr($staffList,$staffInfo,"emergencyContactPhone","emergency_phone");//紧急联络人手机号
            //self::setStaffListForStr($staffList,$staffInfo,"","empoyment_code");//就业登记证号
            self::setStaffListForStr($staffList,$staffInfo,"nation","nation");//民族
            //self::setStaffListForStr($staffList,$staffInfo,"","household");//户籍类型
            self::setStaffListForStr($staffList,$staffInfo,"email","email");//邮箱
            //self::setStaffListForStr($staffList,$staffInfo,"","health");//身体状况
            //self::setStaffListForStr($staffList,$staffInfo,"","work_area");//主要工作地点
            //self::setStaffListForStr($staffList,$staffInfo,"","group_type");//组别分类
            //self::setStaffListForStr($staffList,$staffInfo,"","bank_type");//银行简称
            //self::setStaffListForStr($staffList,$staffInfo,"","bank_number");//银行卡号
            self::setStaffListForStr($staffList,$staffInfo,"educationLevel","education");//学历或文化程度
            self::setStaffListForStr($staffList,$staffInfo,"major","english");//外语水平
            self::setStaffListForStr($staffList,$staffInfo,"speciality","technology");//技术水平
            self::setStaffListForStr($staffList,$staffInfo,"personalHomepage","other");//其它
            //self::setStaffListForStr($staffList,$staffInfo,"iDPhoto","image_user");//员工照片
            //self::setStaffListForStr($staffList,$staffInfo,"iDPortraitSide","image_code");//身份证照片
            //self::setStaffListForStr($staffList,$staffInfo,"","image_work");//工作证明
            //self::setStaffListForStr($staffList,$staffInfo,"","image_other");//其它照片
            //self::setStaffListForStr($staffList,$staffInfo,"educationLevel","remark");//备注
            //self::setStaffListForStr($staffList,$staffInfo,"","social_code");//社会保障卡号
            //self::setStaffListForStr($staffList,$staffInfo,"","jj_card");//公积金卡
            //self::setStaffListForStr($staffList,$staffInfo,"","office_id");//办事处
            //推荐人
            if(is_array($staffInfo["customProperties"])&&key_exists("exttuijianrenzidingyi_611774_726466977",$staffInfo["customProperties"])){
                $recommend_user = self::getEmployeeIdForBsStaffID($staffInfo["customProperties"]["exttuijianrenzidingyi_611774_726466977"]);
                if(!empty($recommend_user)){
                    $staffList["recommend_user"] = $recommend_user;
                }
            }
            $data["staffList"] = $staffList;
            return array("bool"=>true);
        }else{
            return array("bool"=>false,"error"=>"员工姓名不存在。");
        }
    }

    protected static function setStaffListForStr(&$staff,$data,$str,$newStr=""){
        if(key_exists($str,$data)&&!empty($data[$str])){
            $newStr = empty($newStr)?$str:$newStr;
            $staff[$newStr] = $data[$str];
        }
    }

    //验证数据
	public static function validateRow(&$data){
		foreach (self::$data as $keyStr=>$item){
			$requite = key_exists("requite",$item)?$item["requite"]:false;
			$maxLen = key_exists("maxLen",$item)?$item["maxLen"]:0;
			$fun = key_exists("function",$item)?$item["function"]:"";
            $keyStr = key_exists("keyStr",$item)?$item["keyStr"]:$keyStr;
            if($requite&&(!key_exists($keyStr,$data)||$data[$keyStr]===""||$data[$keyStr]===null)){
                return array("bool"=>false,"error"=>$keyStr."不能为空");
            }
			if($maxLen>0&&key_exists($keyStr,$data)&&mb_strlen($data[$keyStr],'UTF-8')>$maxLen){
                $data[$keyStr] = mb_substr($data[$keyStr],0,$maxLen,'UTF-8');
                //return array("bool"=>false,"error"=>$keyStr."的长度不能大于{$maxLen}");
			}
			if(!empty($fun)){//函数验证及自动完成
                $result = self::$fun($data,$keyStr);
                if($result["bool"]===false){ //验证失败不继续验证
                    $result["saveData"] = $data;
					return $result;
                }
			}
		}
		return array("bool"=>true,"saveData"=>$data);
	}

    //保存的数据
    protected static function saveTableForSaveData($db,&$saveData){
        $suffix = Yii::app()->params['envSuffix'];
	    $returnList=array('code'=>200,'msg'=>"成功");
	    $staffList = $saveData["staffList"];
        if(isset($staffList["sex"])){//性别
            $staffList["sex"] =$staffList["sex"]==1?"woman":"man";
        }
        if(isset($staffList["user_card_date"])){//身份证有效期
            $staffList["user_card_date"] =date_format(date_create($staffList["user_card_date"]),"Y/m/d");
        }
	    if($staffList["db_type"]=="update"){
            unset($staffList["db_type"]);
            $staff_id = $staffList["id"];
            unset($staffList["id"]);
            $returnList["msg"] = $staffList["name"]."：修改成功({$staff_id})";
            $db->createCommand()->update("hr{$suffix}.hr_employee",$staffList,"id=".$staff_id);
            $db->createCommand()->insert("hr{$suffix}.hr_employee_history",array(
                "employee_id"=>$staff_id,
                "status"=>"bs curl update",
            ));
            $saveData["staffList"]["id"] = $staff_id;
            $saveData["staffList"]["scenario"] = "edit";
        }else{
            unset($staffList["db_type"]);
            $staffList["lcu"]="bsAdmin";
            $db->createCommand()->insert("hr{$suffix}.hr_employee",$staffList);
            $staff_id = Yii::app()->db->getLastInsertID();
            $returnList["msg"] = $staffList["name"]."：新增成功({$staff_id})";
            $db->createCommand()->insert("hr{$suffix}.hr_employee_history",array(
                "employee_id"=>$staff_id,
                "status"=>"bs curl add",
            ));
            $saveData["staffList"]["id"] = $staff_id;
            $saveData["staffList"]["scenario"] = "add";
        }
        $staffCode = Yii::app()->db->createCommand()->select("code")->from("hr{$suffix}.hr_employee")
            ->where("id=:id",array(":id"=>$staff_id))->queryRow();
	    if($staffCode&&empty($staffCode["code"])){
            $code = "Ex".(10000+$staff_id);
            $db->createCommand()->update("hr{$suffix}.hr_employee",array("code"=>$code),"id=".$staff_id);
        }
	    return $returnList;
    }

	public function syncChangeOne($row) {
        $suffix = Yii::app()->params['envSuffix'];
        $connection = Yii::app()->db;
        $transaction=$connection->beginTransaction();
        try {
            $result = self::validateRow($row);
            if($result["bool"]){
				$saveData = $result["saveData"];
                $msgList = self::saveTableForSaveData($connection,$saveData);
                $this->_successList[]=array("list"=>$saveData,"msg"=>"");
                $transaction->commit();
                return $msgList;
            }else{
                $this->_errorList[]=array("list"=>$result["saveData"],"msg"=>$result["error"]);
                $transaction->rollback();//失败也需要结束事务
                return array('code'=>400,'msg'=>$result["error"]);
			}
        }catch(Exception $e) {
            $transaction->rollback();
            return array('code'=>400,'msg'=>$e->getMessage());
        }
    }

    public function syncChangeFull($rows) {
        $returnRes = array('code'=>400,'msg'=>"没有需要修改的数据");
        $successList = array();
        $errorList = array();
        if(!empty($rows)){
            $this->_successList=array();
            $this->_errorList=array();
            foreach ($rows as $key=>$row){
                $res = $this->syncChangeOne($row);
                if($res["code"]==200){
                    $successList[]=$res["msg"];
                }else{
                    $errorList[]=$res["msg"];
                }
            }
        }
        if (!empty($successList)){ //只要有成功的单条数据，返回成功
            $returnRes["code"]=200;
        }
        if (!empty($errorList)||!empty($successList)){
            $html = "";
            $html.= !empty($errorList)?"失败详情:\r\n\t".implode("\r\n\t",$errorList):"";
            if(!empty($html)&&!empty($successList)){
                $html.="\r\n";
            }
            $html.= !empty($successList)?"成功详情:\r\n\t".implode("\r\n\t",$successList):"";
            $returnRes["html"]=$html;
            $returnRes["msg"]="成功数量：".count($successList)."，失败数量：".count($errorList);
        }
        //$this->sendStaffToU();//把成功员工发送给派单系统
        //$this->sendStaffToEmail();//把失败员工发送给管理员
        return $returnRes;
    }

    //对成功的员工发送给派单系统
    protected function sendStaffToU(){
        if(!empty($this->_successList)){
            $curlData=array("data"=>array());
            $model = new StaffForm();
            $curlModel = new ApiCurl("employeeFull",$curlData);
            foreach ($this->_successList as $row){
                $id = $row["list"]["staffList"]["id"];
                $scenario = $row["list"]["staffList"]["scenario"];
                $model->retrieveData($id,false);
                $model->setScenario($scenario);
                $data = $model->curlData();
                $curlData["data"][]=$data;
            }
            $curlModel->curlData = $curlData;
            $curlModel->sendCurlAndAdd();
        }
    }

    //把失败员工发送给管理员
    protected function sendStaffToEmail(){

    }

    //根据北森staffCode获取LBS员工id
    public static function getEmployeeIdForStaffCode($staffCode){
        //bs_staff_id
        $suffix = Yii::app()->params['envSuffix'];
        $row = Yii::app()->db->createCommand()->select("id")->from("hr{$suffix}.hr_employee")
            ->where("code=:code",array(":code"=>$staffCode))->queryRow();
        return $row?$row["id"]:0;
    }

    //根据北森userCard获取LBS员工id
    public static function getEmployeeIdForUserCard($userCard){
        //bs_staff_id
        $suffix = Yii::app()->params['envSuffix'];
        $row = Yii::app()->db->createCommand()->select("id")->from("hr{$suffix}.hr_employee")
            ->where("user_card=:user_card",array(":user_card"=>$userCard))->queryRow();
        return $row?$row["id"]:0;
    }

    //根据北森userid获取LBS员工id
    public static function getEmployeeIdForBsStaffID($bsStaffId){
        //bs_staff_id
        $suffix = Yii::app()->params['envSuffix'];
        $row = Yii::app()->db->createCommand()->select("id")->from("hr{$suffix}.hr_employee")
            ->where("bs_staff_id=:id",array(":id"=>$bsStaffId))->queryRow();
        return $row?$row["id"]:0;
    }

    //获取LBS员工类型
    public static function getTableTypeForBsForm($bsFormText){
        $list = array(
            "非全员工"=>2,//兼职
            "劳务工"=>2,//兼职
            "兼职"=>2,//兼职
            "外聘"=>3,//外聘
            "外部顾问"=>3,//外聘
            "形式外包"=>3,//外聘
            "业务承揽"=>4,//业务承揽
            "外包商"=>5,//外包商
            "临时账号"=>6,//临时账号
        );
        if(key_exists($bsFormText,$list)){
            return $list[$bsFormText];
        }else{
            return 1;//专职
        }
    }

    //队长、组长
    public static function getStaffLeaderForBsDui($bsDuiCode){
        $list = array(
            "1"=>"Team Leader",//队长
            "2"=>"Group Leader",//组长
        );
        if(key_exists($bsDuiCode,$list)){
            return $list[$bsDuiCode];
        }else{
            return "Nil";//
        }
    }

    //获取LBS员工类别
    public static function getStaffTypeForBsSequence($bsSequenceText){
        $list = array(
            "办公室"=>"Office",//
            "销售"=>"Sales",//
            "服务"=>"Technician",//
            "其他"=>"Others",//
        );
        if(key_exists($bsSequenceText,$list)){
            return $list[$bsSequenceText];
        }else{
            return "";//
        }
    }

    //获取LBS城市
    public static function getCityForBsCityName($bsCityName){
        $suffix = Yii::app()->params['envSuffix'];
        $row = Yii::app()->db->createCommand()->select("code")->from("security{$suffix}.sec_city")
            ->where("name=:name",array(":name"=>$bsCityName))->queryRow();
        return $row?$row["code"]:"";
    }

    //获取LBS城市
    public static function getCityForBsCityCode($bsCityCode){
        $suffix = Yii::app()->params['envSuffix'];
        $row = Yii::app()->db->createCommand()->select("code")->from("security{$suffix}.sec_city")
            ->where("code=:code",array(":code"=>$bsCityCode))->queryRow();
        return $row?$row["code"]:"";
    }

    //获取员工归属
    public static function getCompanyIdForBsGuiShu($data,$bsCompanyName,$bsCompanyID){
        $suffix = Yii::app()->params['envSuffix'];
        $row = Yii::app()->db->createCommand()->select("id")->from("hr{$suffix}.hr_company")
            ->where("name=:name",array(":name"=>$bsCompanyName))->queryRow();
        if($row){
            $companyId = $row["id"];
        }else{
            Yii::app()->db->createCommand()->insert("hr{$suffix}.hr_company",array(
                "name"=>$bsCompanyName,
                "agent"=>"北森同步",
                "head"=>"北森同步",
                "address"=>"北森同步",
                "city"=>$data["staffList"]["city"],
            ));
            $companyId = Yii::app()->db->getLastInsertID();
        }
        $rs = Yii::app()->db->createCommand()->select("id,field_id")->from("hr{$suffix}.hr_send_set_jd")
            ->where("set_type ='company' and table_id=:table_id and field_id=:field_id",array(
                ':field_id'=>"bs_company_code",':table_id'=>$companyId,
            ))->queryRow();//查询公司配置表是否储存了北森编号
        if($rs){
            Yii::app()->db->createCommand()->update('hr_send_set_jd',array(
                "field_value"=>$bsCompanyID,
            ),"id=:id",array(':id'=>$rs["id"]));
        }else{
            Yii::app()->db->createCommand()->insert('hr_send_set_jd',array(
                "table_id"=>$companyId,
                "set_type"=>'company',
                "field_id"=>"bs_company_code",
                "field_value"=>$bsCompanyID,
            ));
        }
        return $companyId;
    }

    //获取员工部门/职位 type：0部门 1职位
    public static function getDeptIDForBsDept($bsDeptCode,$bsDeptName,$type=0){
        $suffix = Yii::app()->params['envSuffix'];
        $row = Yii::app()->db->createCommand()->select("*")->from("hr{$suffix}.hr_dept")
            ->where("name=:name and type=:type",array(":name"=>$bsDeptName,":type"=>$type))
            ->order("z_del asc,id desc")->queryRow();
        if($row){
            $deptId = $row["id"];
            if($row["z_del"]==1){//已删除
                $newRow = $row;
                unset($newRow["id"]);
                $newRow["city"]="_BS_";
                $newRow["z_del"]=0;
                Yii::app()->db->createCommand()->insert("hr{$suffix}.hr_dept",$newRow);
                $deptId = Yii::app()->db->getLastInsertID();
            }
        }else{
            Yii::app()->db->createCommand()->insert("hr{$suffix}.hr_dept",array(
                "name"=>$bsDeptName,
                "type"=>$type,
                "city"=>"_BS_",
                "z_del"=>0,
            ));
            $deptId = Yii::app()->db->getLastInsertID();
        }
        $rs = Yii::app()->db->createCommand()->select("id,field_id")->from("hr{$suffix}.hr_send_set_jd")
            ->where("set_type ='dept' and table_id=:table_id and field_id=:field_id",array(
                ':field_id'=>"bs_dept_code",':table_id'=>$deptId,
            ))->queryRow();//查询公司配置表是否储存了北森编号
        if($rs){
            Yii::app()->db->createCommand()->update('hr_send_set_jd',array(
                "field_value"=>$bsDeptCode,
            ),"id=:id",array(':id'=>$rs["id"]));
        }else{
            Yii::app()->db->createCommand()->insert('hr_send_set_jd',array(
                "table_id"=>$deptId,
                "set_type"=>'dept',
                "field_id"=>"bs_dept_code",
                "field_value"=>$bsDeptCode,
            ));
        }
        return $deptId;
    }
}
?>
