<?php
class TimerCommand extends CConsoleCommand {
    protected $send_list = array();//信息列表
    protected $city_list = array();//所有有信息的城市（優化查詢使用）
    protected $city = "";//

    protected $in_list = array();//入職提示列表
    protected $out_list = array();//離職提示列表

    protected $review_list = array();//老總年度考核列表

    public function run() {

        $this->bossReviewEmailToMonth();//老总年度考核邮件（一个月提示一次)

        $command = Yii::app()->db->createCommand();
        $firstday = date("Y/m/d");
        echo "----------------------------------------------\r\n";
        echo "----------------------------------------------\r\n";
        echo "start:$firstday\r\n";
        $lastday = date("Y/m/d",strtotime("$firstday + 1 month"));
        $this->longTimeContract();//合同過期提示（郵件)
        $aaa = $command->update('hr_employee', array("z_index"=>2),"staff_status=0 and test_type=1 and replace(test_start_time,'-', '/') <= '$firstday' and replace(test_end_time,'-', '/') >='$firstday'");//試用期
        $command->reset();
        //echo "試用期:$aaa<br>";
        $aaa = $command->update('hr_employee', array("z_index"=>1),"staff_status=0 and test_type=1 and replace(test_start_time,'-', '/') >= '$firstday'");//未入職
        $command->reset();
        //echo "未入職:$aaa<br>";
        $aaa = $command->update('hr_employee', array("z_index"=>5),"staff_status=0 and (test_type=0 or replace(test_end_time,'-', '/') <='$firstday')");//正式員工
        $command->reset();
        //echo "正式員工:$aaa<br>";
        $aaa = $command->update('hr_employee', array("z_index"=>4),"staff_status=0 and fix_time='fixation' and replace(end_time,'-', '/') >='$firstday' and replace(end_time,'-', '/') <='$lastday'");//合同即將過期
        $command->reset();
        //echo "合同即將過期:$aaa<br>";
        $aaa = $command->update('hr_employee', array("z_index"=>3),"staff_status=0 and fix_time='fixation' and replace(end_time,'-', '/') <'$firstday'");//合同過期
        //echo "合同過期:$aaa<br>";

        $this->retireOutContract();//員工退休年齡

        $signedContractType = Yii::app()->db->createCommand()->select("set_value")->from("hr_setting")
            ->where('set_name="signedContractType"')->queryScalar();
        if(empty($signedContractType)){//需要合同寄出功能
            $this->signedContract();//是否簽署合同
            $this->signedContractEnd();//是否簽署合同
            $this->signedContractFinish();//是否簽署合同
            $this->contractCitySendEmail();//員工合同7天將過期(合同未過期)
            $this->contractAgoSendEmail();//合同過期10天后（合同已過期）給饒總發送郵件
        }

        //$this->contractAgainSendEmail();//合同续期10天后未更新附加提示，城市、老总(與合同寄出功能重複，不執行該提示)

        //加班、請假批准后的郵件提示（開始)
        $this->leaveThreeSendEmail();
        $this->leaveSevenSendEmail();
        $this->leaveMoreSendEmail();
        $this->workThreeSendEmail();
        $this->workSevenSendEmail();
        $this->workMoreSendEmail();
        //加班、請假批准后的郵件提示（結束)

        $this->signedSupportStart();//支援记录提醒地区做回馈
        $this->signedSupportEnd();//支援记录15天後還未回饋

        $this->sendEmail();//統一發送郵件

        $this->probationEndHint();//试用期即将结束的邮件提醒14天、7天、当天
        $this->dailyInAndOutHint();//入职、离职总览电邮
        $this->resetBossListScore();//老总年度考核的總分重新計算

        $this->resetTreaty();//合约提醒功能（需要每天刷新合约的状态）
        echo "end\r\n";
    }

    //合约提醒功能（需要每天刷新合约的状态）
    private function resetTreaty(){
        $rows = Yii::app()->db->createCommand()->select("id")->from("hr_treaty")
            ->where('state_type!=3')->queryAll();
        if($rows){
            $treatyModel = new TreatyServiceForm();
            foreach ($rows as $row){
                $treatyModel->retrieveData($row["id"],false);
            }
        }
    }

    //員工退休年齡
    private function retireOutContract(){
        // 因台灣版不適用而加的判斷
        if (!isset(Yii::app()->params['retire']) || Yii::app()->params['retire']==true) {
            $row = Yii::app()->db->createCommand()->select("set_value")->from("hr_setting")
                ->where('set_name="retirementAgeType"')->queryScalar();
            switch ($row){
                case 1://新加坡-62岁
                    $manDate = date("Y/m/d", strtotime("-62 year"));
                    $womanDate = date("Y/m/d", strtotime("-62 year"));
                    break;
                case 2://吉隆坡-60岁
                    $manDate = date("Y/m/d", strtotime("-60 year"));
                    $womanDate = date("Y/m/d", strtotime("-60 year"));
                    break;
                default://echo "員工退休年齡(男60 女50):$aaa<br>";
                    $manDate = date("Y/m/d", strtotime("-60 year"));
                    $womanDate = date("Y/m/d", strtotime("-50 year"));
            }
            $manDateMonth = date("Y/m/d", strtotime("$manDate + 1 month"));
            $womanDateMonth = date("Y/m/d", strtotime("$womanDate + 1 month"));
            $sql = "UPDATE hr_employee a LEFT JOIN hr_contract b ON a.contract_id = b.id SET a.z_index = -1 WHERE ";
            $sql.= "a.birth_time is not null and a.birth_time != '' and a.staff_status=0 and b.retire=0 and ((replace(a.birth_time,'-', '/') <='$womanDateMonth' and a.sex='woman') or (replace(a.birth_time,'-', '/') <='$manDateMonth' and a.sex='man'))";
            Yii::app()->db->createCommand($sql)->execute();//要退休的員工前排顯示(一個月後過期)
            $sql = "UPDATE hr_employee a LEFT JOIN hr_contract b ON a.contract_id = b.id SET a.z_index = 0 WHERE ";
            $sql.= "a.birth_time is not null and a.birth_time != '' and a.staff_status=0 and b.retire=0 and ((replace(a.birth_time,'-', '/') <='$womanDate' and a.sex='woman') or (replace(a.birth_time,'-', '/') <='$manDate' and a.sex='man'))";
            Yii::app()->db->createCommand($sql)->execute();//要退休的員工前排顯示
            $this->retireToMonth($manDate,$womanDate);//員工退休後是否簽署退休合同（提前一個月）
            $this->retireToWeek($manDate,$womanDate);//員工退休後是否簽署退休合同（提前一個星期）
            $this->retireToAgo($manDate,$womanDate);//員工退休後是否簽署退休合同（超過退休年齡未修改合同）
        }
    }

    //入职、离职总览电邮
    private function dailyInAndOutHint(){
        $suffix = Yii::app()->params['envSuffix'];
        $systemId = Yii::app()->params['systemId'];
        $this->in_list=array();
        $this->out_list=array();
        $email = new Email("入职、离职总览电邮","","入职、离职总览电邮");
        $this->setDailyHintHtml();

        if(!empty($this->in_list)||!empty($this->out_list)){//如果有提示信息則發送郵件
            $rs = Yii::app()->db->createCommand()->selectDistinct("b.email,b.city,b.look_city")->from("security$suffix.sec_user_access a")
                ->leftJoin("security$suffix.sec_user b","a.username=b.username")
                ->where("a.system_id='$systemId' and a.a_control like '%ZR10%' and b.email is not null and b.status='A'")
                ->queryAll();
            if($rs){
                echo "entry and dimission,users number:".count($rs)."\r\n";
                foreach ($rs as $row){
                    if(!empty($row["email"])){
                        $email->resetToAddr();
                        $email->addToAddrEmail($row["email"]);
                        $lookCity= $row["look_city"];
                        $cityList = empty($lookCity)?array():explode(",",$lookCity);
                        $message = $this->getDailyHintHtmlToCity($cityList);
                        if(!empty($message)){
                            $email->setMessage($message);
                            $email->sent("系統自動發送",$systemId);
                        }
                    }
                }
            }
        }
    }

    //獲取入职、离职的提示信息
    private function getDailyHintHtmlToCity($cityList){
        $message = "";
        if(!empty($this->out_list)){
            $body = "";
            foreach ($cityList as $city){
                if(key_exists($city,$this->out_list)){
                    $body.=$this->out_list[$city];
                }
            }
            if(!empty($body)){
                $message.="<table border='1' width='600px'><thead><tr><th colspan='4'>离职列表</th></tr>";
                $message.="<tr><th width='25%'>地区</th><th width='25%'>员工姓名</th><th width='25%'>部门</th><th width='25%'>职位</th></tr>";
                $message.="</thead><tbody>$body</tbody></table>";
            }
        }
        if(!empty($this->in_list)){
            $body = "";
            foreach ($cityList as $city){
                if(key_exists($city,$this->in_list)){
                    $body.=$this->in_list[$city];
                }
            }
            if(!empty($body)){
                $message.="<br><table border='1' width='600px' style='margin-top:20px;'><thead><tr><th colspan='4'>入职列表</th></tr>";
                $message.="<tr><th width='25%'>地区</th><th width='25%'>员工姓名</th><th width='25%'>部门</th><th width='25%'>职位</th></tr>";
                $message.="</thead><tbody>$body</tbody></table>";
            }
        }
        return $message;
    }

    //設置入职、离职的提示信息
    private function setDailyHintHtml(){
        $suffix = Yii::app()->params['envSuffix'];
        $date = date("Y/m/d", strtotime("-1 days"));
        $rows = Yii::app()->db->createCommand()->select("a.staff_status,a.name,a.city,b.name as city_name,d.name as dept_name,e.name as ment_name")->from("hr_employee a")
            ->leftJoin("security$suffix.sec_city b","a.city = b.code")//職位
            ->leftJoin("hr_dept d","a.position = d.id")//職位
            ->leftJoin("hr_dept e","a.department = e.id")//部門
            ->where("(date_format(a.lcd,'%Y/%m/%d') = '$date' and a.staff_status in (0,4)) or (date_format(a.lud,'%Y/%m/%d') = '$date' and a.staff_status=-1)")->order("a.city desc")->queryAll();
        if($rows){
            foreach ($rows as $row){
                $trHtml="<tr>";
                $trHtml.="<td>".$row["city_name"]."</td>";
                $trHtml.="<td>".$row["name"]."</td>";
                $trHtml.="<td>".$row["ment_name"]."</td>";
                $trHtml.="<td>".$row["dept_name"]."</td>";
                $trHtml.="</tr>";
                if($row["staff_status"] == -1){ //離職
                    if(!key_exists($row["city"],$this->out_list)){
                        $this->out_list[$row["city"]]="";
                    }
                    $this->out_list[$row["city"]].=$trHtml;
                }else{ //入職
                    if(!key_exists($row["city"],$this->in_list)){
                        $this->in_list[$row["city"]]="";
                    }
                    $this->in_list[$row["city"]].=$trHtml;
                }
            }
        }
    }

    private function sendEmail(){
        $systemId = Yii::app()->params['systemId'];
        $email = new Email("人事系統待處理事項","","人事系統待處理事項");
        //Autumn不需要收到邮件通知
        $userlist = $email->getEmailUserList($this->city_list,"kittyzhou","Autumn");
        $joeEmailList = $email->getJoeEmailList();
        $kittyEmail = $email->getKittyEmail();
        if($userlist){
            foreach ($userlist as $user){
                $this->city = $user["city"];
                $this->city_list = explode(",",$user["look_city"]);
                $message="";
                foreach ($this->send_list as $send){
                    $maxBool = false;//最大權限
                    $html = "";
                    $city_list = $send["city_allow"]?$this->city_list:array($user["city"]); //判斷是否需要查詢下級城市
                    $bool = array_intersect($this->city_list,$send["city_list"]);
                    if(key_exists("joeEmail",$send)){//驗證是否額外給繞生發郵件
                        if($send["joeEmail"]){
                            if(in_array($user["email"],$joeEmailList)){//用戶是繞生
                                $bool=1;//繞生不需要城市驗證
                                $maxBool = true;
                                $city_list = $send["city_list"];//繞生收到所有城市的郵件
                            }
                        }
                    }
                    if(key_exists("kittyEmail",$send)){//驗證是否額外給kitty發郵件
                        if($send["kittyEmail"]){
                            if($user["email"]==$kittyEmail){//用戶是kitty
                                $bool=1;//kitty不需要城市驗證
                                $maxBool = true;
                                $city_list = $send["city_list"];//kitty收到所有城市的郵件
                            }
                        }
                    }
                    if(key_exists("send_all_city",$send)&&$send["send_all_city"]){
                        $bool = 1;
                        $city_list = $send["city_list"];//所有城市的郵件
                    }
                    if(!$maxBool){
                        if(empty($bool)){
                            continue;//該城市沒有提示信息
                        }
                        $inchargeBool = !empty($send["incharge"])&&!empty($user["incharge"]);//boss身份
                        $authBool = !empty($send["auth_list"])&&$this->arrSearchStr($send["auth_list"],$user["a_read_write"]);
                        if($inchargeBool==false&&$authBool==false){
                            continue;
                        }
                    }
                    $html.=$send["title"];
                    $html.="<table border='1'>".$send["table_head"]."<tbody>";
                    $tBody="";
                    foreach ($city_list as $city){//城市循環
                        if(in_array($city,$send["city_list"])){
                            $tBody .= implode("",$send[$city]["table_body"]);
                        }
                    }
                    $html=$html.$tBody."</tbody></table><p>&nbsp;</p><br/>";
                    if(!empty($tBody)){
                        $message.=$html;
                    }
                }

                if(!empty($message)){ //如果有內容則發送郵件
                    echo "to do transaction:".$user['username']."\r\n";
                    $email->setMessage($message);
                    $email->addToAddrEmail($user["email"]);
                    $email->sent("系统生成",$systemId);
                    $email->resetToAddr();
                }
            }
        }
    }

    private function arrSearchStr($arr,$str){
        foreach ($arr as $item){
            if (strpos($str,$item)!==false)
                return true;
        }
        return false;
    }

    //員工退休後是否簽署退休合同（提前一個月）
    private function retireToMonth($manDatePro,$womanDatePro){
        $command = Yii::app()->db->createCommand();
        $manDate = date("Y/m/d", strtotime("$manDatePro + 1 month"));
        $womanDate = date("Y/m/d", strtotime("$womanDatePro + 1 month"));
        $firstManDay = date("Y/m/d",strtotime("$manDatePro + 1 week"));
        $firstWomanDay = date("Y/m/d",strtotime("$womanDatePro + 1 week"));
        $sql = "a.birth_time is not null and a.birth_time != '' and a.staff_status=0 and b.retire=0 and ((replace(a.birth_time,'-', '/') >'$firstWomanDay' and replace(a.birth_time,'-', '/') <='$womanDate' and a.sex='woman') or (replace(a.birth_time,'-', '/') >'$firstManDay' and replace(a.birth_time,'-', '/') <='$manDate' and a.sex='man'))";
        $rows = $command->select("a.*,b.name as contract_name")->from("hr_employee a")->leftJoin("hr_contract b","a.contract_id=b.id")->where($sql)->queryAll();
        if($rows){
            $description = "<p>下列员工即将到达退休年龄，请及时变更合同：</p>";
            $arr = $this->getListToRetireList($description,$rows);
            $arr["auth_list"] = array("ZE04","ZG02");
            $arr["city_allow"] = false;
            $arr["incharge"] = 0;
            if(count($arr)>6){
                $this->send_list[] = $arr;
            }
        }
    }

    //員工退休後是否簽署退休合同（一個星期）
    private function retireToWeek($manDatePro,$womanDatePro){
        $command = Yii::app()->db->createCommand();
        $manDate = date("Y/m/d", strtotime("$manDatePro + 1 week"));
        $womanDate = date("Y/m/d", strtotime("$womanDatePro + 1 week"));
        $firstManDay = $manDatePro;
        $firstWomanDay = $womanDatePro;
        $sql = "a.birth_time is not null and a.birth_time != '' and a.staff_status=0 and b.retire=0 and ((replace(a.birth_time,'-', '/') >'$firstWomanDay' and replace(a.birth_time,'-', '/') <='$womanDate' and a.sex='woman') or (replace(a.birth_time,'-', '/') >'$firstManDay' and replace(a.birth_time,'-', '/') <='$manDate' and a.sex='man'))";
        $rows = $command->select("a.*,b.name as contract_name")->from("hr_employee a")->leftJoin("hr_contract b","a.contract_id=b.id")->where($sql)->queryAll();
        if($rows){
            $description = "<p>下列员工即将到达退休年龄，请及时变更合同：</p>";
            $arr = $this->getListToRetireList($description,$rows);
            $arr["auth_list"] = array("ZE04","ZG02");
            $arr["city_allow"] = true;
            $arr["incharge"] = 0;
            if(count($arr)>6){
                $this->send_list[] = $arr;
            }
        }
    }

    //員工退休後是否簽署退休合同（超過退休年齡）
    private function retireToAgo($manDate,$womanDate){
        $command = Yii::app()->db->createCommand();
        $sql = "a.birth_time is not null and a.birth_time != '' and a.staff_status=0 and b.retire=0 and ((replace(a.birth_time,'-', '/') <='$womanDate' and a.sex='woman') or (replace(a.birth_time,'-', '/') <='$manDate' and a.sex='man'))";
        $rows = $command->select("a.*,b.name as contract_name")->from("hr_employee a")->leftJoin("hr_contract b","a.contract_id=b.id")->where($sql)->queryAll();
        if($rows){
            $description = "<p>下列员工已超過退休年龄，请变更合同：</p>";
            $arr = $this->getListToRetireList($description,$rows);
            $arr["auth_list"] = array();
            $arr["city_allow"] = true;
            $arr["incharge"] = 1;
            if(count($arr)>6){
                $this->send_list[] = $arr;
            }
        }
    }

    //合同即将到期
    private function longTimeContract(){
        $command = Yii::app()->db->createCommand();
        $firstday = date("Y/m/d");
        $lastday = date("Y/m/d",strtotime("$firstday + 1 month"));
        $sql = "staff_status=0 and fix_time='fixation' and replace(end_time,'-', '/') >='$firstday' and replace(end_time,'-', '/') <='$lastday'";
        $rows = $command->select("*")->from("hr_employee")->where($sql)->queryAll();
        if($rows){
            $description = "<p>下列员工的合同即将到期：</p>";
            $arr = $this->getListToStaffList($description,$rows);
            $arr["auth_list"] = array("ZG02","ZE04");
            $arr["city_allow"] = true;
            $arr["incharge"] = 0;
            if(count($arr)>6){
                $this->send_list[] = $arr;
            }
        }
    }

    //員工錄入后30天提示是否簽署合同
    private function signedContract(){
        $command = Yii::app()->db->createCommand();
        $command->reset();
        $day = date("Y/m/d");
        $firstDay = date("Y/m/d",strtotime("$day - 30 day"));
        $endDay = date("Y/m/d",strtotime("$day - 35 day"));
        $sql = "a.status_type in (0,1,4) and date_format(a.lcd,'%Y/%m/%d') <='$firstDay' and date_format(a.lcd,'%Y/%m/%d') >'$endDay'";
        $rows = $command->select("b.*")->from("hr_sign_contract a")
            ->leftJoin("hr_employee b","a.employee_id = b.id")
            ->where($sql)->queryAll();
        if($rows){
            $description = "<p>下列员工已超過30天未寄出合同：</p>";
            $arr = $this->getListToStaffList($description,$rows,false);
            $arr["auth_list"] = array("ZE09");
            $arr["city_allow"] = true;
            $arr["incharge"] = 1;
            if(count($arr)>6){
                $this->send_list[] = $arr;
            }
        }
    }

    //員工錄入后35天提示是否簽署合同
    private function signedContractEnd(){
        $command = Yii::app()->db->createCommand();
        $command->reset();
        $firstDay = date("Y/m/d");
        $firstDay = date("Y/m/d",strtotime("$firstDay - 35 day"));
        $sql = "a.status_type in (0,1,4) and date_format(a.lcd,'%Y/%m/%d') <='$firstDay'";
        $rows = $command->select("b.*")->from("hr_sign_contract a")
            ->leftJoin("hr_employee b","a.employee_id = b.id")
            ->where($sql)->queryAll();
        if($rows){
            $description = "<p>下列员工已超過35天未寄出合同：</p>";
            $arr = $this->getListToStaffList($description,$rows,false);
            $arr["auth_list"] = array("ZE09");
            $arr["city_allow"] = true;
            $arr["incharge"] = 1;
            $arr["joeEmail"] = true;//繞生收到郵件
            if(count($arr)>6){
                $this->send_list[] = $arr;
            }
        }
    }

    //員工錄入后10天提示是否簽收合同
    private function signedContractFinish(){
        $command = Yii::app()->db->createCommand();
        $command->reset();
        $firstDay = date("Y/m/d");
        $firstDay = date("Y/m/d",strtotime("$firstDay - 10 day"));
        $sql = "a.status_type = 2 and date_format(a.lud,'%Y/%m/%d') <='$firstDay'";
        $rows = $command->select("b.*")->from("hr_sign_contract a")
            ->leftJoin("hr_employee b","a.employee_id = b.id")
            ->where($sql)->queryAll();
        if($rows){
            $description = "<p>下列员工已超過10天未簽收合同：</p>";
            $arr = $this->getListToStaffList($description,$rows,false);
            $arr["auth_list"] = array('ZG08');
            $arr["city_allow"] = false;
            $arr["send_all_city"] = true;
            $arr["incharge"] = 0;
            if(count($arr)>6){
                $this->send_list[] = $arr;
            }
        }
    }

    //支援记录提醒地区做回馈
    private function signedSupportStart(){
        $command = Yii::app()->db->createCommand();
        $command->reset();
        $endDay = date("Y/m/d");
        $firstDay = date("Y/m/d",strtotime("$endDay - 15 day"));
        $sql = "a.status_type=5 and date_format(a.apply_end_date,'%Y/%m/%d') >'$firstDay' and date_format(a.apply_end_date,'%Y/%m/%d') <'$endDay'";
        $rows = $command->select("a.*,b.name as employee_name")->from("hr_apply_support a")
            ->leftJoin("hr_employee b","a.employee_id = b.id")
            ->where($sql)->queryAll();
        if($rows){
            $description = "<p>请对以下中央技术支援同事进行评分：</p>";
            $arr = $this->getListToSupportList($description,$rows);
            $arr["auth_list"] = array("AY01");
            $arr["city_allow"] = false;
            $arr["incharge"] = 0;
            if(count($arr)>6){
                $this->send_list[] = $arr;
            }
        }
    }

    //合同续期10天后未更新附加提示，城市、老总
    private function contractAgainSendEmail(){
        $command = Yii::app()->db->createCommand();
        $command->reset();
        $firstDay = date("Y/m/d");
        $firstDay = date("Y/m/d",strtotime("$firstDay - 10 day"));
        $sql = "b.staff_status=0 and a.lcd < a.lud and a.status='contract' and date_format(a.lud,'%Y/%m/%d') <='$firstDay'";
        //id,lcd,city,fix_time,start_time,end_time,code,name,entry_time
        $rows = $command->select("b.id,b.city,b.fix_time,b.start_time,b.end_time,b.code,b.name,b.entry_time,a.lud as lcd")
            ->from("hr_employee_history a")
            ->leftJoin("hr_employee b","a.employee_id=b.id")->where($sql)->queryAll();
        if($rows){
            $description = "<p>請檢查下列员工的是否簽署续签合同：</p>";
            $arr = $this->getListToStaffList($description,$rows,true);
            $arr["auth_list"] = array("ZE03","ZG02");
            $arr["city_allow"] = true;
            $arr["incharge"] = 0;
            $arr["joeEmail"] = true;//繞生收到郵件
            if(count($arr)>6){
                $this->send_list[] = $arr;
            }
        }
    }

    //支援记录15天後還未回饋
    private function signedSupportEnd(){
        $command = Yii::app()->db->createCommand();
        $command->reset();
        $endDay = date("Y/m/d");
        $firstDay = date("Y/m/d",strtotime("$endDay - 15 day"));
        $sql = "a.status_type=5 and date_format(a.apply_end_date,'%Y/%m/%d') <='$firstDay'";
        $rows = $command->select("a.*,b.name as employee_name")->from("hr_apply_support a")
            ->leftJoin("hr_employee b","a.employee_id = b.id")
            ->where($sql)->queryAll();
        if($rows){
            $description = "<p>中央技術支援回饋已超過15天提醒：</p>";
            $arr = $this->getListToSupportList($description,$rows);
            $arr["auth_list"] = "";
            $arr["city_allow"] = true;
            $arr["incharge"] = 1;
            $arr["joeEmail"] = true;//僅限繞生收到郵件
            $arr["kittyEmail"] = true;//僅限kitty收到郵件
            if(count($arr)>6){
                $this->send_list[] = $arr;
            }
        }
    }

    private function getListToSupportList($description,$rows){

        $arr = array();
        $arr["city_list"] = array();
        $arr["title"] = $description;
        $arr["table_head"] = "<thead><th>支援编号</th><th>服务类型</th><th>申请城市</th><th>开始时间</th><th>结束时间</th><th>支援员工</th></thead>";
        foreach ($rows as $row){
            if(!in_array($row["apply_city"],$this->city_list)){
                $this->city_list[] = $row["apply_city"];
            }
            if(!key_exists($row["apply_city"],$arr)){
                $arr["city_list"][]=$row["apply_city"];
                $arr[$row["apply_city"]]=array();
                $arr[$row["apply_city"]]["city_name"]=CGeneral::getCityName($row["apply_city"]);
            }
            $row["service_type"] = $row["service_type"]==1?Yii::t("contract","service support"):Yii::t("contract","service guide");
            $arr[$row["apply_city"]]["table_body"][]="<tr><td>".$row["support_code"]."</td>"."<td>".$row["service_type"]."</td>"."<td>".$arr[$row["apply_city"]]["city_name"]."</td>"."<td>".$row["apply_date"]."</td>"."<td>".$row["apply_end_date"]."</td>"."<td>".$row["employee_name"]."</td></tr>";
        }
        return $arr;
    }

    //員工合同7天將過期時給地區總監發送郵件
    private function contractCitySendEmail(){
        $command = Yii::app()->db->createCommand();
        $command->reset();
        $firstday = date("Y/m/d");
        $firstday = date("Y/m/d",strtotime("$firstday + 7 day"));
        $sql = "staff_status=0 and fix_time='fixation' and replace(end_time,'-', '/') ='$firstday'";
        $rows = $command->select("*")->from("hr_employee")->where($sql)->queryAll();
        if($rows){
            $description="<p>【紧急】下列員工的合同将于".date("Y年m月d日",strtotime($firstday))."到期,请记得安排续约</p>";
            $arr = $this->getListToStaffList($description,$rows);
            $arr["auth_list"] = '';
            $arr["city_allow"] = false;
            $arr["incharge"] = 1;//只給boss發郵件
            if(count($arr)>6){
                $this->send_list[] = $arr;
            }
        }
    }
    //員工合同過期10天給饒總發送郵件
    private function contractAgoSendEmail(){
        $command = Yii::app()->db->createCommand();
        $command->reset();
        $firstday = date("Y/m/d");
        $firstday = date("Y/m/d",strtotime("$firstday - 10 day"));
        $sql = "staff_status=0 and fix_time='fixation' and replace(end_time,'-', '/') ='$firstday'";
        $rows = $command->select("*")->from("hr_employee")->where($sql)->queryAll();
        if($rows){
            $description="<p>【紧急】下列員工的合同于".date("Y年m月d日",strtotime($firstday))."已到期</p>";
            $arr = $this->getListToStaffList($description,$rows);
            $arr["auth_list"] = '';
            $arr["city_allow"] = true;
            $arr["incharge"] = 1;
            $arr["joeEmail"] = true;//僅限繞生收到郵件
            if(count($arr)>7){
                $this->send_list[] = $arr;
            }
        }
    }
    private function getListToStaffList($description,$rows,$bool=false){

        $arr = array();
        $arr["city_list"] = array();
        //$description="【紧急】下列員工的合同于".date("Y年m月d日",$firstday)."已到期";
        //$arr["auth_list"] = array("ZE01");
        $arr["title"] = $description;
        $arr["table_head"] = "<thead><th>员工编号</th><th>员工姓名</th><th>员工所在城市</th><th>员工入职日期</th><th>合同日期</th></thead>";
        foreach ($rows as $row){
            if($bool){
                if(!$this->docmanSearch("EMPLOY",$row["id"],$row["lcd"])){
                    continue;
                }
            }
            if(!in_array($row["city"],$this->city_list)){
                $this->city_list[]=$row["city"];
            }
            if(!key_exists($row["city"],$arr)){
                $arr["city_list"][]=$row["city"];
                $arr[$row["city"]]=array();
                $arr[$row["city"]]["city_name"]=CGeneral::getCityName($row["city"]);
            }
            if("nofixed"==$row["fix_time"]){
                $con_date = date("Y-m-d",strtotime($row["start_time"]))."(无期限合同)";
            }else{
                $con_date = date("Y-m-d",strtotime($row["start_time"]))." - ".$row["end_time"];
            }
            $arr[$row["city"]]["table_body"][]="<tr><td>".$row["code"]."</td>"."<td>".$row["name"]."</td>"."<td>".$arr[$row["city"]]["city_name"]."</td>"."<td>".$row["entry_time"]."</td>"."<td>".$con_date."</td></tr>";
        }
        return $arr;
    }

    private function getListToRetireList($description,$rows){

        $arr = array();
        $arr["city_list"] = array();
        $arr["title"] = $description;
        $arr["table_head"] = "<thead><th>员工编号</th><th>员工姓名</th><th>出生日期</th><th>员工年龄</th><th>员工所在城市</th><th>员工入职日期</th><th>员工合同模板</th></thead>";
        foreach ($rows as $row){
            if(!in_array($row["city"],$this->city_list)){
                $this->city_list[]=$row["city"];
            }
            if(!key_exists($row["city"],$arr)){
                $arr["city_list"][]=$row["city"];
                $arr[$row["city"]]=array();
                $arr[$row["city"]]["city_name"]=CGeneral::getCityName($row["city"]);
            }
            list($age,$month,$day) = explode("-",date("Y-m-d",strtotime($row["birth_time"])));
            $age = date("Y")-$age;
            $month = intval(date("m"))-intval($month);
            $month = date("d")-$day<0?$month-1:$month;
            $age = $month<0?$age-1:$age;
            $html='<tr>';
            $html.="<td>".$row["code"]."</td>";
            $html.="<td>".$row["name"]."</td>";
            $html.="<td>".$row["birth_time"]."</td>";
            $html.="<td>".$age."</td>";
            $html.="<td>".$arr[$row["city"]]["city_name"]."</td>";
            $html.="<td>".$row["entry_time"]."</td>";
            $html.="<td>".$row["contract_name"]."</td>";
            $html.='</tr>';
            $arr[$row["city"]]["table_body"][]=$html;
        }
        return $arr;
    }

    private function getJobListToStaffList($description,$str,$rows){

        $arr = array();
        $arr["city_list"] = array();
        $arr["title"] = $description;
        $arr["table_head"] = "<thead><th>员工编号</th><th>员工姓名</th><th>员工所在城市</th><th>".$str."编号</th></thead>";
        $str = $str=="加班"?"WORKEM":"LEAVE";
        foreach ($rows as $row){
            if ($this->docmanSearch($str, $row["id"], $row["lud"])) {
                if(!in_array($row["city"],$this->city_list)){
                    $this->city_list[]=$row["city"];
                }
                if(!key_exists($row["city"],$arr)){
                    $arr["city_list"][]=$row["city"];
                    $arr[$row["city"]]=array();
                    $arr[$row["city"]]["city_name"]=CGeneral::getCityName($row["city"]);
                }
                $arr[$row["city"]]["table_body"][]="<tr><td>".$row["code"]."</td>"."<td>".$row["name"]."</td>"."<td>".$arr[$row["city"]]["city_name"]."</td>"."<td>".$row["job_code"]."</td></tr>";
            }
        }
        return $arr;
    }

    //加班附件提示(3天)
    private function workThreeSendEmail(){
        $command = Yii::app()->db->createCommand();
        $command->reset();
        $date = date("Y/m/d");
        $firstday = date("Y/m/d",strtotime("$date - 3 day"));
        $end = date("Y/m/d",strtotime("$date - 7 day"));
        $sql = "a.status=4 and b.staff_status=0 and date_format(a.lud,'%Y/%m/%d') <= '$firstday' and date_format(a.lud,'%Y/%m/%d') > '$end'";
        $rows = $command->select("a.work_code as job_code,b.code,b.name,b.city,a.id,a.lud")->from("hr_employee_work a")
            ->leftJoin("hr_employee b","a.employee_id=b.id")
            ->where($sql)->queryAll();
        if($rows){
            $description="<p>下列員工的加班單“批准”3天后,还未上传附件</p>";
            $arr = $this->getJobListToStaffList($description,"加班",$rows);
            $arr["auth_list"] = array('ZE01');
            $arr["city_allow"] = false;
            $arr["incharge"] = 1;
            if(count($arr)>6){
                $this->send_list[] = $arr;
            }
        }
    }

    //加班附件提示(7天)
    private function workSevenSendEmail(){
        $command = Yii::app()->db->createCommand();
        $command->reset();
        $date = date("Y/m/d");
        $firstday = date("Y/m/d",strtotime("$date - 7 day"));
        $endday = date("Y/m/d",strtotime("$date - 15 day"));
        $sql = "a.status=4 and b.staff_status=0 and date_format(a.lud,'%Y/%m/%d') <= '$firstday' and date_format(a.lud,'%Y/%m/%d') > '$endday'";
        $rows = $command->select("a.work_code as job_code,b.code,b.name,b.city,a.id,a.lud")->from("hr_employee_work a")
            ->leftJoin("hr_employee b","a.employee_id=b.id")
            ->where($sql)->queryAll();
        if($rows){
            $description="<p>下列員工的加班單“批准”7天后,还未上传附件</p>";
            $arr = $this->getJobListToStaffList($description,"加班",$rows);
            $arr["auth_list"] = array('ZE01');
            $arr["city_allow"] = true;
            $arr["incharge"] = 1;
            if(count($arr)>6){
                $this->send_list[] = $arr;
            }
        }
    }

    //加班附件提示(15天)
    private function workMoreSendEmail(){
        $command = Yii::app()->db->createCommand();
        $command->reset();
        $date = date("Y/m/d");
        $firstday = date("Y/m/d",strtotime("$date - 15 day"));
        $sql = "a.status=4 and b.staff_status=0 and date_format(a.lud,'%Y/%m/%d') <= '$firstday'";
        $rows = $command->select("a.work_code as job_code,b.code,b.name,b.city,a.id,a.lud")->from("hr_employee_work a")
            ->leftJoin("hr_employee b","a.employee_id=b.id")
            ->where($sql)->queryAll();
        if($rows){
            $description="<p>下列員工的加班單“批准”15天后,还未上传附件</p>";
            $arr = $this->getJobListToStaffList($description,"加班",$rows);
            $arr["auth_list"] = array('ZE01');
            $arr["city_allow"] = true;
            $arr["incharge"] = 1;
            $arr["joeEmail"] = true;//僅限繞生收到郵件
            if(count($arr)>7){
                $this->send_list[] = $arr;
            }
        }
    }

    //請假附件提示(3天)
    private function leaveThreeSendEmail(){
        $command = Yii::app()->db->createCommand();
        $command->reset();
        $date = date("Y/m/d");
        $firstday = date("Y/m/d",strtotime("$date - 3 day"));
        $endday = date("Y/m/d",strtotime("$date - 7 day"));
        $sql = "a.status=4 and b.staff_status=0 and date_format(a.lud,'%Y/%m/%d') <= '$firstday' and date_format(a.lud,'%Y/%m/%d') > '$endday'";
        $rows = $command->select("a.leave_code as job_code,b.code,b.name,b.city,a.id,a.lud")->from("hr_employee_leave a")
            ->leftJoin("hr_employee b","a.employee_id=b.id")
            ->where($sql)->queryAll();
        if($rows){
            $description="<p>下列員工的请假單“批准”3天后,还未上传附件</p>";
            $arr = $this->getJobListToStaffList($description,"请假",$rows);
            $arr["auth_list"] = array('ZE01');
            $arr["city_allow"] = false;
            $arr["incharge"] = 1;
            if(count($arr)>6){
                $this->send_list[] = $arr;
            }
        }
    }

    //請假附件提示(7天)
    private function leaveSevenSendEmail(){
        $command = Yii::app()->db->createCommand();
        $command->reset();
        $date = date("Y/m/d");
        $firstday = date("Y/m/d",strtotime("$date - 7 day"));
        $endday = date("Y/m/d",strtotime("$date - 15 day"));
        $sql = "a.status=4 and b.staff_status=0 and date_format(a.lud,'%Y/%m/%d') <= '$firstday' and date_format(a.lud,'%Y/%m/%d') > '$endday'";
        $rows = $command->select("a.leave_code as job_code,b.code,b.name,b.city,a.id,a.lud")->from("hr_employee_leave a")
            ->leftJoin("hr_employee b","a.employee_id=b.id")
            ->where($sql)->queryAll();
        if($rows){
            $description="<p>下列員工的请假單“批准”7天后,还未上传附件</p>";
            $arr = $this->getJobListToStaffList($description,"请假",$rows);
            $arr["auth_list"] = array('ZE01');
            $arr["city_allow"] = true;
            $arr["incharge"] = 1;
            if(count($arr)>6){
                $this->send_list[] = $arr;
            }
        }
    }

    //請假附件提示(15天)
    private function leaveMoreSendEmail(){
        $command = Yii::app()->db->createCommand();
        $command->reset();
        $firstday = date("Y/m/d");
        $firstday = date("Y/m/d",strtotime("$firstday - 15 day"));
        $sql = "a.status=4 and b.staff_status=0 and date_format(a.lud,'%Y/%m/%d') <= '$firstday'";
        $rows = $command->select("a.leave_code as job_code,b.code,b.name,b.city,a.id,a.lud")->from("hr_employee_leave a")
            ->leftJoin("hr_employee b","a.employee_id=b.id")
            ->where($sql)->queryAll();
        if($rows){
            $description="<p>下列員工的请假單“批准”15天后,还未上传附件</p>";
            $arr = $this->getJobListToStaffList($description,"请假",$rows);
            $arr["auth_list"] = array('ZE01');
            $arr["city_allow"] = true;
            $arr["incharge"] = 1;
            $arr["joeEmail"] = true;//僅限繞生收到郵件
            if(count($arr)>7){
                $this->send_list[] = $arr;
            }
        }
    }

    //請假、加班附件變更查詢
    private function docmanSearch($docType,$id,$date){
        $date = date("Y/m/d H:i:s",strtotime($date));
        $suffix = Yii::app()->params['envSuffix'];
        $rows = Yii::app()->db->createCommand()->select("b.lcd")->from("docman$suffix.dm_master a")
            ->leftJoin("docman$suffix.dm_file b","b.mast_id = a.id")
            ->where("a.doc_type_code='$docType' and a.doc_id = '$id' and date_format(b.lcd,'%Y/%m/%d %H:%i:%s') > '$date'")->queryRow();
        if($rows){
            return false;//不需要發送郵件
        }else{
            return true;//需要發送郵件
        }

    }

    //老总年度考核邮件（一个月提示一次)
    private function bossReviewEmailToMonth(){
        if(date("m")=="01"||date("m")=="02"||date("d")!="01") {//每月1號(1月、2月份不需要)
            return;
        }
        $systemId = Yii::app()->params['systemId'];
        $setSubject = "老总年度考核进度".date("(Y年m月)",strtotime("-2 month"));
        $email = new Email($setSubject,"",$setSubject);
        $userList = $email->getUserListToPrefix("BA01");
        if($userList){
            $bossList = $email->getOnlyLRTMUser();
            foreach ($userList as $user){
                $email->resetToAddr();
                $email->resetAttr();
                $html = $this->bossReviewEmailHtml($user);
                if(!empty($html)){
                    $email->setSubject($setSubject." - ".$user['name']);
                    $email->setMessage($html);
                    $email->addToAddrEmail($user['email']);
                    $email->addEmailToOnlyCityBoss($user['city'],$bossList);
                    $email->sent("系统生成",$systemId);
                }
            }
        }

        $this->sendReviewAllEmail();
        echo "boss review end\r\n";
    }

    //給華南、華西、華北、華東、繞生、林生發匯總郵件
    private function sendReviewAllEmail(){
        if(!empty($this->review_list)){
            $systemId = Yii::app()->params['systemId'];
            $setSubject = "老总年度考核進度汇总".date("(Y年m月)",strtotime("-2 month"));
            $email = new Email($setSubject,"",$setSubject);
            $userList = $email->getOnlyLRTMUserList();
            if($userList){
                foreach ($userList as $user){
                    $email->resetToAddr();
                    $email->resetAttr();
                    $html ="";

                    foreach ($this->review_list as $reviewList){
                        if(empty($user["cityList"])||in_array($reviewList["city"],$user["cityList"])){
                            $html.=empty($html)?"":"<p style='border-bottom: 2px dashed #000'>&nbsp;</p>";
                            $html.=$reviewList["html"];
                            $email->insertAttr($reviewList["attr"]["title"],$reviewList["attr"]["attr"]);
                        }
                    }
                    if(!empty($html)){
                        $email->setMessage($html);
                        $email->addToAddrEmail($user['email']);
                        $email->addToAddrUser($user['username']);
                        $email->sent("系统生成",$systemId);
                    }
                }
            }
        }
    }

    private function bossReviewEmailHtml($user){
        $year = date("Y");
        $month = date("n",strtotime("-2 months"));
        $month = $month>10?0:$month;
        $html = "";
        $rptBossPlanModel = new RptBossPlanList();
        $rptBossPlanModel->year = $year;
        $rptBossPlanModel->month = $month;
        $rptBossPlanModel->cityName = $user["city_name"];
        $rptBossPlanModel->userName = $user["name"]." - ".$user["code"];
        $bossModel = new BossSearchForm();
        $bossModel->setDataToEmployeeIdAndYear($user["id"],$year,false);
        $bossModel->city = $user["city"];
        $bossModel->lcu = $user["username"];
        if($bossModel->status_type != 2){
            $html = "<h2>城市：".$user["city_name"]."</h2>";
            $title = $year."年老总年度考核 - ".$user["name"];
            $html .= "<h2>{$title}</h2>";
            $list = array(
                array("name"=>"（A） 目标订立部分","class"=>"BossReviewA","width"=>"auto","colspan"=>8),
                array("name"=>"（B） 其他细节部分","class"=>"BossReviewB","width"=>"auto","colspan"=>6),
                array("name"=>"（C） 自选项目部分","class"=>"BossReviewC","width"=>"800px","colspan"=>4)
            );
            $bodyList=array();
            foreach ($list as $key=>$item){
                $html.="<p>&nbsp;</p>";
                $html.="<table width='".$item["width"]."' border='1px' style='border-color:#000;'>";
                $html.="<thead><tr><td colspan='".$item["colspan"]."'><b>".$item["name"]."</b></td></tr></thead>";
                $className = $item["class"];
                $bossReviewModel = new $className($bossModel,true);
                $bossReviewModel->resetListX($bossModel->json_listX);
                $bossReviewModel->search_month = $month;
                $html .= $bossReviewModel->getTableHtmlToEmail();
                $bodyList[$key]["title"]=$item["name"];
                $bodyList[$key]["list"]=$bossReviewModel->getDetailList();
                $html.="</table>";
            }
            $rptBossPlanModel->setBodyList($bodyList);
            $attr = array(
                "title"=>$title,
                "attr"=>$rptBossPlanModel->genReport(false)
            );
            $this->review_list[] = array('city'=>$user["city"],'html'=>$html,'attr'=>$attr);
        }
        return $html;
    }

    //初始化所有老總考核的總分
    public function resetBossListScore(){
        $rows = Yii::app()->db->createCommand()->select("a.json_listX,a.ratio_a,a.ratio_b,a.ratio_c,a.id,a.results_a,a.results_b,a.results_c,a.status_type,a.city,a.audit_year,a.employee_id,a.lcu,a.json_text,b.code as employee_code,b.name as employee_name")
            ->from("hr_boss_audit a")
            ->leftJoin("hr_employee b","a.employee_id = b.id")
            ->where("a.status_type !=2")->queryAll();
        if($rows){
            $model = new BossSearchForm();
            foreach ($rows as $row){
                $model->json_text = json_decode($row['json_text'],true);
                $model->lcu = $row['lcu'];
                $model->employee_id = $row['employee_id'];
                $model->audit_year = $row['audit_year'];
                $model->city = $row['city'];
                $model->ratio_a = $row['ratio_a'];
                $model->ratio_b = $row['ratio_b'];
                $model->ratio_c = $row['ratio_c'];
                $model->status_type = $row['status_type'];
                $model->results_c = floatval($row["results_c"]);
                //A類驗證
                $bossReviewA = new BossReviewA($model);
                if(!empty($row["json_listX"])){
                    $bossReviewA->resetListX(json_decode($row["json_listX"],true));
                }
                $bossReviewA->validateJson($model);
                $model->json_text = $bossReviewA->json_text;
                $model->results_a = $bossReviewA->scoreSum;
                //B類驗證
                $bossReviewB = new BossReviewB($model);
                if(!empty($row["json_listX"])){
                    $bossReviewB->resetListX(json_decode($row["json_listX"],true));
                }
                $bossReviewB->validateJson($model);
                $model->json_text = $bossReviewB->json_text;
                $model->results_b = $bossReviewB->scoreSum;
                if($model->results_a == floatval($row["results_a"])&&$model->results_b == floatval($row["results_b"])){
                    continue;//數據沒有變動，不需要更新
                }

                $bossRewardType = BossApplyForm::getBossRewardType($row['city']);
                $ratio_a = $model->ratio_a*0.01;
                $ratio_b = $model->ratio_b*0.01;
                if($bossRewardType == 1){
                    $model->results_sum = $model->results_a*$ratio_a+$model->results_b*$ratio_b;
                }else{
                    $model->results_sum = $model->results_a*$ratio_a+$model->results_b*$ratio_b+$model->results_c;
                }

                Yii::app()->db->createCommand()->update('hr_boss_audit', array(
                    'results_a'=>$model->results_a,
                    'results_b'=>$model->results_b,
                    'results_sum'=>$model->results_sum,
                ), 'id=:id', array(':id'=>$row['id']));
            }
        }
    }

    //试用期即将结束的邮件提醒14天、7天、当天
    private function probationEndHint(){
        $systemId = Yii::app()->params['systemId'];
        $suffix = Yii::app()->params['envSuffix'];
        $dayOne = date("Y-m-d");
        $dayTwo = date("Y-m-d",strtotime("+7 days"));
        $dayThree = date("Y-m-d",strtotime("+14 days"));
        $rows = Yii::app()->db->createCommand()
            ->select("a.code,a.name,a.entry_time,a.city,a.test_start_time,a.test_end_time,f.name as city_name,b.name as dept_name")
            ->from("hr_employee a")
            ->leftJoin("hr_dept b","a.position = b.id")
            ->leftJoin("security{$suffix}.sec_city f","a.city = f.code")
            ->where("a.test_type=1 and a.staff_status not in (-1,1) and replace(test_end_time,'/', '-') in ('{$dayOne}','{$dayTwo}','{$dayThree}')")->queryAll();
        //var_dump($rows);
        if($rows){
            $setSubject="试用期到期提醒";
            $email = new Email($setSubject,"",$setSubject);
            foreach ($rows as $row){
                $email->resetToAddr();
                $message ="<p>员工编号：{$row['code']}</p>";
                $message.="<p>员工姓名：{$row['name']}</p>";
                $message.="<p>员工城市：{$row['city_name']}</p>";
                $message.="<p>员工职位：{$row['dept_name']}</p>";
                $message.="<p>入职日期：{$row['entry_time']}</p>";
                $message.="<p>试用期时间：".CGeneral::toDate($row['test_start_time'])." ~ ".CGeneral::toDate($row['test_end_time'])."</p>";
                $message.="<p style='color: red;'>温馨提示：该员工试用期准备到期，请提前做好试用期转正准备。如已处理，请忽略本条信息。</p>";
                $email->setMessage($message);
                $email->setSubject($row["name"].$setSubject);
                $email->setDescription($row["name"].$setSubject);
                $email->addEmailToPrefixAndOnlyCity("ZE01",$row["city"]);
                $email->addEmailToCity($row["city"]);
                $email->sent("系统发送",$systemId);
            }
        }
    }
}
?>