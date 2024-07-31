<?php
/** 
 *  Created by PhpStorm.
 * User:  Administrator
 * Date: 2023/12/13 0013
 * Time: 9:14
 */

class StaffFun
{
    //获取员工类型列表
    public static function getTableTypeListIndex(){
        $list = array(
            ""=>Yii::t("misc","All"),//全部
            2=>Yii::t("contract","part-time"),//兼职
            3=>Yii::t("contract","external-time"),//外聘
            4=>Yii::t("contract","contracting"),//业务承揽
            5=>Yii::t("contract","outsourcer"),//外包商
            6=>Yii::t("contract","temporary employee"),//临时账号
        );
        return $list;
    }

    //获取员工类型列表
    public static function getTableTypeList($bool=true){
        $list = array(
            1=>Yii::t("contract","full-time"),//专职
            2=>Yii::t("contract","part-time"),//兼职
            3=>Yii::t("contract","external-time"),//外聘
            4=>Yii::t("contract","contracting"),//业务承揽
            5=>Yii::t("contract","outsourcer"),//外包商
            6=>Yii::t("contract","temporary employee"),//临时账号
        );
        if($bool){
            unset($list[1]);
        }
        return $list;
    }

    //获取员工类型翻译
    public static function getTableTypeNameForID($id){
        $id = "".$id;
        $list = self::getTableTypeList(false);
        if(key_exists($id,$list)){
            return $list[$id];
        }else{
            return $id;
        }
    }

    //获取员工类型翻译
    public static function getCurlStatusNameToID($id){
        $id = "".$id;
        $list = array(
            "P"=>"未进行",
            "C"=>"已完成",
            "E"=>"错误",
        );
        if(key_exists($id,$list)){
            return $list[$id];
        }else{
            return $id;
        }
    }

    //獲取性別列表
    public static function getSexList(){
        return array(""=>"","man"=>Yii::t("contract","man"),"woman"=>Yii::t("contract","woman"));
    }

    //獲取性別翻译
    public static function getSexNameForID($id){
        $id = "".$id;
        $list = self::getSexList();
        if(key_exists($id,$list)){
            return $list[$id];
        }else{
            return $id;
        }
    }
    //獲取年齡列表
    public static function getAgeList(){
        $list = array(""=>"");
        for ($num = 18;$num<70;$num++){
            $list[$num] = $num;
        }
        return $list;
    }
    //獲取年齡翻译
    public static function getAgeNameForID($id){
        $id = "".$id;
        $list = self::getAgeList();
        if(key_exists($id,$list)){
            return $list[$id];
        }else{
            return $id;
        }
    }
    //獲取健康列表
    public static function getHealthList(){
        return array(""=>"","poor"=>Yii::t("staff","poor"),"general"=>Yii::t("staff","general"),"good"=>Yii::t("staff","good"));
    }
    //獲取健康翻译
    public static function getHealthNameForID($id){
        $id = "".$id;
        $list = self::getHealthList();
        if(key_exists($id,$list)){
            return $list[$id];
        }else {
            return $id;
        }
    }
    //獲取戶籍列表
    public static function getNationList(){
        return array(
            ""=>"",
            "Non-agricultural"=>Yii::t("contract","Non-agricultural"),
            "Agricultural"=>Yii::t("contract","Agricultural")
        );
    }
    //獲取戶籍翻译
    public static function getNationNameForID($id){
        $id = "".$id;
        $list = self::getNationList();
        if(key_exists($id,$list)){
            return $list[$id];
        }else{
            return $id;
        }
    }
    //獲取合同期限列表
    public static function getFixTimeList(){
        return array(
            "fixation"=>Yii::t("contract","fixation"),
            "nofixed"=>Yii::t("contract","nofixed")
        );
    }
    //獲取合同期限翻译
    public static function getFixTimeNameForID($id){
        $id = "".$id;
        $list = self::getFixTimeList();
        if(key_exists($id,$list)){
            return $list[$id];
        }else{
            return $id;
        }
    }
    //獲取试用期类型列表
    public static function getTestTypeList(){
        return array(
            "1"=>Yii::t("contract","Have probation period"),
            "0"=>Yii::t("contract","No probation period")
        );
    }
    //獲取试用期类型翻译
    public static function getTestTypeNameForID($id){
        $id = "".$id;
        $list = self::getTestTypeList();
        if(key_exists($id,$list)){
            return $list[$id];
        }else{
            return $id;
        }
    }
    //獲取合同期限列表
    public static function getOperationTypeList($staff_id = 0,$type=""){
        if(empty($staff_id)){
            $num = "";
        }else{
            $num = self::getContractNumber($staff_id);
            if($type == "change"){
                $num++;
            }
            $num = " - ".$num;
        }
        return array(
            ""=>"",
            "salary"=>Yii::t("contract","salary"),
            "promotion"=>Yii::t("contract","promotion"),
            "transfer"=>Yii::t("contract","transfer"),
            "contract"=>Yii::t("contract","contract").$num
        );
    }
    //獲取健康列表
    public static function getMonthList(){
        $list = array(""=>"");
        for ($num = 1;$num<=12;$num++){
            $list[$num]=$num.Yii::t("staff"," months");
        }
        return $list;
    }
    //獲取健康翻译
    public static function getMonthNameForID($id){
        $id = "".$id;
        $list = self::getMonthList();
        if(key_exists($id,$list)){
            return $list[$id];
        }else{
            return $id;
        }
    }
    //試用期時長列表
    public static function getTestMonthLengthList(){
        $list = array(""=>"");
        for ($num = 1;$num<=6;$num++){
            $list[$num]=$num.Yii::t("staff"," months");
        }
        return $list;
    }
    //試用期時長翻译
    public static function getTestMonthLengthNameForID($id){
        $id = "".$id;
        $list = self::getTestMonthLengthList();
        if(key_exists($id,$list)){
            return $list[$id];
        }else{
            return $id;
        }
    }
    //獲取學歷列表
    public static function getEducationList(){
        return array(
            ""=>"",
            "Primary school"=>Yii::t("staff","Primary school"),
            "Junior school"=>Yii::t("staff","Junior school"),
            "High school"=>Yii::t("staff","High school"),
            "Technical school"=>Yii::t("staff","Technical school"),
            "College school"=>Yii::t("staff","College school"),
            "Undergraduate"=>Yii::t("staff","Undergraduate"),
            "Graduate"=>Yii::t("staff","Graduate"),
            "Doctorate"=>Yii::t("staff","Doctorate")
        );
    }
    //獲取學歷翻译
    public static function getEducationNameForID($id){
        $id = "".$id;
        $list = self::getEducationList();
        if(key_exists($id,$list)){
            return $list[$id];
        }else{
            return $id;
        }
    }
    //獲取員工職能列表
    public static function getStaffLeaderList(){
        return array("Nil"=>Yii::t("staff","Nil"),"Group Leader"=>Yii::t("staff","Group Leader"),"Team Leader"=>Yii::t("staff","Team Leader"));
    }
    //獲取員工職能翻译
    public static function getStaffLeaderNameForID($id){
        $id = "".$id;
        $list = self::getStaffLeaderList();
        if(key_exists($id,$list)){
            return $list[$id];
        }else{
            return $id;
        }
    }
    //獲取員工類別列表
    public static function getStaffTypeList(){
        return array(""=>"","Office"=>Yii::t("staff","Office"),"Sales"=>Yii::t("staff","Sales"),"Technician"=>Yii::t("staff","Technician"),"Others"=>Yii::t("staff","Others"));
    }
    //獲取員工類別翻译
    public static function getStaffTypeNameForID($id){
        $id = "".$id;
        $list = self::getStaffTypeList();
        if(key_exists($id,$list)){
            return $list[$id];
        }else{
            return $id;
        }
    }
    //技術員列表
    public static function getTechnicianList(){
        return array(Yii::t("misc","No"),Yii::t("misc","Yes"));
    }
    //技術員翻译
    public static function getTechnicianNameForID($id){
        $id = "".$id;
        $list = self::getTechnicianList();
        if(key_exists($id,$list)){
            return $list[$id];
        }else{
            return $id;
        }
    }
    //經理級別列表
    public static function getManagerList(){
        return array(
            Yii::t("fete","none"),
            Yii::t("fete","handle"),
            Yii::t("fete","charge"),
            Yii::t("fete","director"),
            Yii::t("fete","you")
        );
    }
    //經理級別翻译
    public static function getManagerNameForID($id){
        $id = "".$id;
        $list = self::getManagerList();
        if(key_exists($id,$list)){
            return $list[$id];
        }else{
            return $id;
        }
    }
    //组别列表
    public static function getGroupTypeList(){
        return array(
            0=>Yii::t("fete","none"),//無
            1=>Yii::t("contract","group business"),//商業組
            2=>Yii::t("contract","group repast"),//餐飲組
        );
    }
    //组别翻译
    public static function getGroupTypeNameForID($id){
        $id = "".$id;
        $list = self::getGroupTypeList();
        if(key_exists($id,$list)){
            return $list[$id];
        }else{
            return $id;
        }
    }

    //獲取員工續約的次數
    public static function getContractNumber($staff_id){
        $num = Yii::app()->db->createCommand()->select("count('id')")->from("hr_employee_history")
            ->where('employee_id=:employee_id and status="contract"',array(":employee_id"=>$staff_id))->queryScalar();
        if($num){
            return $num;
        }else{
            return 0;
        }
    }

    //根據id獲取員信息
    public static function getEmployeeOneToId($id){
        $row = Yii::app()->db->createCommand()->select()->from("hr_employee")
            ->where('id=:id', array(':id'=>$id))->queryRow();
        if($row){
            return $row;
        }
        return false;
    }

    //根據id獲取員列表
    public static function getEmployeeListToCity($id=0,$city){
        $arr = array(""=>"");
        $rows = Yii::app()->db->createCommand()->select("id,code,name")->from("hr_employee")
            ->where('id=:id or city=:city ', array(':id'=>$id,':city'=>$city))->order("name asc")->queryAll();
        if($rows){
            foreach ($rows as $row){
                $arr[$row['id']] = $row["name"]." ({$row['code']})";
            }
        }
        return $arr;
    }

    //根據id獲取員列表
    public static function getEmployeeNameAndCode($id=0){
        $row = Yii::app()->db->createCommand()->select("id,code,name")->from("hr_employee")
            ->where('id=:id', array(':id'=>$id))->queryRow();
        if($row){
            return $row["name"]." ({$row['code']})";
        }
        return $id;
    }

    //獲取可用公司
    public static function getCompanyListToCity($city,$company_id){
        $arr = array(""=>"");
        $rows = Yii::app()->db->createCommand()->select()->from("hr_company")
            ->where("city=:city or id=:id", array(':city'=>$city,':id'=>$company_id))->queryAll();
        if(count($rows)>0){
            foreach ($rows as $row){
                $arr[$row["id"]] = $row["name"];
            }
        }
        return $arr;
    }

    //獲取可用公司(翻译)
    public static function getCompanyNameToID($company_id){
        $row = Yii::app()->db->createCommand()->select("name")->from("hr_company")
            ->where("id=:id", array(':id'=>$company_id))->queryRow();
        if($row){
            return $row["name"];
        }
        return $company_id;
    }

    //獲取可用合同
    public static function getContractListToCity($city,$contract_id){
        $arr = array(""=>"");
        $rows = Yii::app()->db->createCommand()->select()->from("hr_contract")
            ->where("(city=:city and local_type!=2) or local_type=1 or id=:id", array(':city'=>$city,':id'=>$contract_id))->queryAll();
        if(count($rows)>0){
            foreach ($rows as $row){
                $arr[$row["id"]] = $row["name"];
            }
        }
        return $arr;
    }

    //獲取可用合同(翻译)
    public static function getContractNameToID($contract_id){
        $row = Yii::app()->db->createCommand()->select("name")->from("hr_contract")
            ->where("id=:id", array(':id'=>$contract_id))->queryRow();
        if($row){
            return $row["name"];
        }
        return $contract_id;
    }

    //根據id獲取公司員工合同信息
    public static function getEmployeeDocListToId($id){
        $row = Yii::app()->db->createCommand()->select()->from("hr_employee")
            ->where('id=:id', array(':id'=>$id))->queryRow();
        if($row){
            $arr["company"]=CompanyForm::getCompanyToId($row["company_id"]);
            $arr["word"]=array();
            $arr["staff"]=$row;
            $docRows = Yii::app()->db->createCommand()->select("a.*,b.docx_url")->from("hr_contract_docx a")
                ->leftJoin("hr_docx b","a.docx=b.id")
                ->where('contract_id=:contract_id', array(
                    ':contract_id'=>$row["contract_id"]
                ))->order('a.index desc')->queryAll();
            if($docRows){
                foreach ($docRows as $doc){
                    $arr["word"][]=$doc["docx_url"];
                }
            }
            return $arr;
        }
        return false;
    }

    //表格数据的历史信息
    public static function getTableHistoryRows($id,$table_name="hr_employee"){
        $rows = Yii::app()->db->createCommand()->select("id,update_html,lcu,lcd")
            ->from("hr_table_history")
            ->where("table_id=:table_id and table_name=:table_name",array(
                ":table_id"=>$id,
                ":table_name"=>$table_name
            ))->order("lcd desc,id desc")->queryAll();
        return $rows;
    }
    //獲取員工歷史
    public static function getStaffHistoryList($staff_id){
        $rows = Yii::app()->db->createCommand()->select("a.*,b.code,b.name")
            ->from("hr_employee_history a")
            ->leftJoin("hr_employee b","a.employee_id=b.id")
            ->where('a.employee_id=:id', array(':id'=>$staff_id))
            ->order('id desc')->queryAll();
        if ($rows){
            return $rows;
        }else{
            return "";
        }
    }

//获取地区列表
    public static function getCityListAll()
    {
        $from =  'security'.Yii::app()->params['envSuffix'].'.sec_city';
        $arr = array(""=>"");
        $rows = Yii::app()->db->createCommand()->select("code,name")->from($from)->queryAll();
        if($rows){
            foreach ($rows as $row){
                $arr[$row["code"]] = $row["name"];
            }
        }
        return $arr;
    }

//获取地区名字
    public static function getCityNameToCode($code)
    {
        $from =  'security'.Yii::app()->params['envSuffix'].'.sec_city';
        $rows = Yii::app()->db->createCommand()->select("name")->from($from)->where("code=:code",array(":code"=>$code))->queryRow();
        if($rows){
            return $rows["name"];
        }
        return $code;
    }

//获取银行简称列表
    public static function getBankTypeList()
    {
        $list = array();
        $from =  'hr'.Yii::app()->params['envSuffix'].'.hr_bank_set';
        $rows = Yii::app()->db->createCommand()->select("id,name")
            ->from($from)->order("z_index desc")->queryAll();
        if($rows){
            foreach ($rows as $row){
                $list[$row["id"]] = $row["name"];
            }
        }
        return $list;
    }

//获取银行简称名字
    public static function getBankTypeNameForId($bank_type)
    {
        $from =  'hr'.Yii::app()->params['envSuffix'].'.hr_bank_set';
        $row = Yii::app()->db->createCommand()->select("id,name")
            ->from($from)->where("id=:id",array(":id"=>$bank_type))->queryRow();
        if($row){
            return $row["name"];
        }
        return $bank_type;
    }

    //获取户籍相似的员工
    public static function changeUserCard($id,$userCard){
        $userCard="".$userCard;
        $userCard= strlen($userCard)>5?$userCard:"666666";
        $userCard = substr($userCard,0,6);
        $data = array('status'=>0,'html'=>'');
        $city = Yii::app()->user->city();
        $rows = Yii::app()->db->createCommand()->select("id,code,name,user_card,address")->from("hr_employee")
            ->where("id!=:id and user_card like '{$userCard}%' and city=:city", array(':id'=>$id,':city'=>$city))->order("name asc")->queryAll();
        if($rows){
            $data['status'] = 1;
            $data['html'] = '<div class="popover fade bottom in" id="userCardHint">';
            $data['html'].= '<div class="arrow"></div>';
            $data['html'].= '<div class="popover-title">户籍相似的员工</div><div class="popover-content">';
            $html="";
            foreach ($rows as $row){
                $html.=!empty($html)?"<p style='margin-bottom: 0px;'>---------------------------------------</p>":"";
                $html.= "<p style='margin-bottom: 0px;'>员工姓名:{$row['name']} - {$row['code']}</p>";
                $html.= "<p style='margin-bottom: 0px;'>户籍地址:{$row['address']}</p>";
                $html.= "<p style='margin-bottom: 0px;'>身份证号:{$row['user_card']}</p>";
            }
            $data['html'].= $html.'</div></div>';
        }
        return $data;
    }

    //邮箱验证
    public static function isEmail($email){
        return filter_var($email, FILTER_VALIDATE_EMAIL)!==false;
    }

    public static function getAgeForBirthDate($birth_date){
        list($age,$month,$day) = explode("-",date("Y-m-d",strtotime($birth_date)));
        $age = date("Y")-$age;
        $month = intval(date("m"))-intval($month);
        $month = date("d")-$day<0?$month-1:$month;
        $age = $month<0?$age-1:$age;

        return $age;
    }

//获取地区列表(导入专用)
    public static function getCityForCityAllow($city_allow)
    {
        $from =  'security'.Yii::app()->params['envSuffix'].'.sec_city';
        $arr = array();
        $rows = Yii::app()->db->createCommand()->select("code,name")
            ->from($from)->where("code in ({$city_allow})")->queryAll();
        if($rows){
            foreach ($rows as $row){
                $arr[$row["code"]] = $row["name"];
            }
        }
        return $arr;
    }

//获取职位列表(导入专用)
    public static function getDeptForCityAllow($city_allow)
    {
        $from =  'hr'.Yii::app()->params['envSuffix'].'.hr_dept';
        $arr = array();
        $rows = Yii::app()->db->createCommand()->select("id,name,city")
            ->from($from)->where("city in ({$city_allow})")->queryAll();
        if($rows){
            foreach ($rows as $row){
                $arr[$row["city"]][$row["id"]] = $row["name"];
            }
        }
        return $arr;
    }

//获取办事处列表(导入专用)
    public static function getOfficeForCityAllow($city_allow)
    {
        $from =  'hr'.Yii::app()->params['envSuffix'].'.hr_office';
        $arr = array();
        $rows = Yii::app()->db->createCommand()->select("id,name,city")
            ->from($from)->where("city in ({$city_allow})")->queryAll();
        if($rows){
            foreach ($rows as $row){
                $arr[$row["city"]][$row["id"]] = $row["name"];
            }
        }
        return $arr;
    }

//获取公司列表(导入专用)
    public static function getCompanyForCityAllow($city_allow)
    {
        $from =  'hr'.Yii::app()->params['envSuffix'].'.hr_company';
        $arr = array();
        $rows = Yii::app()->db->createCommand()->select("id,name,city")
            ->from($from)->where("city in ({$city_allow})")->queryAll();
        if($rows){
            foreach ($rows as $row){
                $arr[$row["city"]][$row["id"]] = $row["name"];
            }
        }
        return $arr;
    }

//获取合同列表(导入专用)
    public static function getContractForCityAllow($city_allow)
    {
        $from =  'hr'.Yii::app()->params['envSuffix'].'.hr_contract';
        $arr = array();
        $rows = Yii::app()->db->createCommand()->select("id,name,city,local_type")
            ->from($from)->where("city in ({$city_allow}) or local_type=1")->queryAll();
        if($rows){
            foreach ($rows as $row){
                if($row["local_type"]==1){
                    $arr["all"][$row["id"]] = $row["name"];
                }else{
                    $arr[$row["city"]][$row["id"]] = $row["name"];
                }
            }
        }
        return $arr;
    }
}