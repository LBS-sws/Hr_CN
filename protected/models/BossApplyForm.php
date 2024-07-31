<?php

class BossApplyForm extends CFormModel
{
	public $id;
	public $employee_id;
	public $lcu;
	public $code;
	public $name;
	public $city;
	public $audit_year;
	public $apply_date;
	public $status_type=0;
	public $reject_remark;
	public $json_text=array();
	public $results_sum;
	public $results_a=0;
	public $results_b=0;
	public $results_c=0;
    public $ratio_a=50;//占比
    public $ratio_b=35;//占比
    public $ratio_c=15;//占比
    public $json_listX;

	public function attributeLabels()
	{
		return array(
            'name'=>Yii::t('contract','Employee Name'),
            'code'=>Yii::t('contract','Employee Code'),
            'audit_year'=>Yii::t('contract','audit year'),
            'results_sum'=>Yii::t('contract','Sum Results'),
            'status_type'=>Yii::t('contract','Status'),
            'reject_remark'=>Yii::t('contract','Rejected Remark'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id,employee_id,json_text,audit_year,reject_remark','safe'),
            array('audit_year','required','on'=>array("add","new","edit")),
            array('json_text','required','on'=>array("new","edit")),
            array('employee_id','validateStaff','on'=>array("new","edit")),
            array('id','validateID','on'=>array("new","edit")),
            array('json_text','validateJson','on'=>array("new","edit")),
            array('reject_remark','required','on'=>array("reject")),
		);
	}

    public function validateID($attribute, $params){
	    if(!is_numeric($this->audit_year)){
            $message = Yii::t('contract','audit year')."只能為數字";
            $this->addError($attribute,$message);
            return false;
        }
        $city = Yii::app()->user->city();
        $row = Yii::app()->db->createCommand()->select("id,status_type,ratio_a,ratio_b,ratio_c,json_text,apply_date,json_listX")
            ->from("hr_boss_audit")
            ->where('employee_id=:id and audit_year=:year and city=:city',
                array(':id'=>$this->employee_id,':year'=>$this->audit_year,':city'=>$city)
            )->queryRow();
	    if($this->getScenario()=='new'&&$row){
            $message = "該考核已存在，不允許重複添加";
            $this->addError($attribute,$message);
            return false;
        }
        if ($this->getScenario()=='edit'&&!$row){
            $message = "該考核不存在，無法修改";
            $this->addError($attribute,$message);
            return false;
        }
        if($row&&in_array($row["status_type"],array(4,0,3))){
            $this->json_listX = json_decode($row['json_listX'],true);
            $this->apply_date = $row["apply_date"];
            $this->ratio_a = $row["ratio_a"];
            $this->ratio_b = $row["ratio_b"];
            $this->ratio_c = $row["ratio_c"];
            if($this->status_type==1){
                $this->status_type=$row["status_type"]==4?5:1;
            }else{
                $this->status_type=$row["status_type"]==4?4:0;
            }
            $jsonTest = json_decode($row['json_text'],true);
            if(isset($jsonTest["three"]["list"])&&in_array($this->status_type,array(4,5))){
                foreach ($jsonTest["three"]["list"] as $key =>&$list){
                    if(key_exists("three_four",$list)&&isset($this->json_text["three"]["list"][$key]["three_four"])){
                        $list["three_four"] = $this->json_text["three"]["list"][$key]["three_four"];
                        $list["three_two"] = $this->json_text["three"]["list"][$key]["three_two"];
                    }
                }
                $this->json_text = $jsonTest;
            }
        }
    }

    public function validateStaff($attribute, $params){
        $uid = Yii::app()->user->id;
        $rows = Yii::app()->db->createCommand()->select("a.employee_id,b.code,b.name,b.city")->from("hr_binding a")
            ->leftJoin("hr_employee b","a.employee_id = b.id")
            ->where('a.user_id=:user_id',
                array(':user_id'=>$uid))->queryRow();
        if ($rows){
            $this->employee_id = $rows["employee_id"];
            $this->code = $rows["code"];
            $this->name = $rows["name"];
            $this->city = Yii::app()->user->city();
            $this->lcu = $uid;
        }else{
            $message = "員工不存在，請於管理員聯繫";
            $this->addError($attribute,$message);
        }
    }

    public function validateJson($attribute, $params){
	    if(!empty($this->json_text)){
	        $bool = $this->status_type==1;
	        //A類驗證
            $bossReviewA = new BossReviewA($this);
            if(!empty($this->json_listX)){
                $bossReviewA->resetListX($this->json_listX);
            }elseif($this->getScenario()=="new"){
                $bossReviewA->cityListX();
            }
            $bossReviewA->validateJson($this,$bool);
            $this->json_text = $bossReviewA->json_text;
            $this->results_a = $bossReviewA->scoreSum;
            //B類驗證
            $bossReviewB = new BossReviewB($this);
            if(!empty($this->json_listX)){
                $bossReviewB->resetListX($this->json_listX);
            }elseif($this->getScenario()=="new"){
                $bossReviewB->cityListX();
            }
            $bossReviewB->validateJson($this,$bool);
            $this->json_text = $bossReviewB->json_text;
            $this->results_b = $bossReviewB->scoreSum;
            //C類驗證
            $bossRewardType = BossApplyForm::getBossRewardType($this->city);
            $ratio_a = $this->ratio_a*0.01;
            $ratio_b = $this->ratio_b*0.01;
            $this->ratio_c = 100-($this->ratio_a+$this->ratio_b);
            if($bossRewardType == 1){
                $this->results_c = 0;
                $this->results_sum = $this->results_a*$ratio_a+$this->results_b*$ratio_b;
            }else{
                $bossReviewC = new BossReviewC($this);
                $bossReviewC->validateJson($this,$bool);
                $this->json_text = $bossReviewC->json_text;
                $this->results_c = $bossReviewC->scoreSum;
                $this->results_sum = $this->results_a*$ratio_a+$this->results_b*$ratio_b+$this->results_c;
            }
            if(empty($this->json_listX)){
                $this->json_listX= array(
                    "bossA"=>$bossReviewA->getListX(),
                    "bossB"=>$bossReviewB->getListX()
                );
            }
        }
    }

    public function getBossApplyYearHtml(){
        $city = Yii::app()->user->city();
        $rows = Yii::app()->db->createCommand()->select("a.audit_year")->from("hr_boss_audit a")
            ->leftJoin("hr_employee b","a.employee_id = b.id")
            ->where("b.id=:id and a.city=:city",array(":id"=>$this->employee_id,":city"=>$city))->queryAll();
        if($rows){
            $rows = array_column($rows,"audit_year");
        }else{
            $rows = array();
        }
        $year = date("Y");
        $html = "<select class='form-control submit_select' name='year'>";
        for($i=$year-3;$i<$year+4;$i++){
            if($i<2019){
                continue;
            }
            if(in_array($i,$rows)){
                $html.="<option value='$i' disabled>".$i.Yii::t("contract"," year")." - ".Yii::t("contract","applied")."</option>";
            }else{
                if($i == $year){
                    $html.="<option value='$i' selected>".$i.Yii::t("contract"," year")."</option>";
                }else{
                    $html.="<option value='$i'>".$i.Yii::t("contract"," year")."</option>";
                }
            }
        }
        $html.="</select>";
        return $html;
    }

	public function retrieveData($index,$bool=true) {
        $sql = "1!=1";
        if(!$bool){
            $sql = "1=1";
        }
        $city_allow = Yii::app()->user->city_allow();
        $suffix = Yii::app()->params['envSuffix'];
        $row = Yii::app()->db->createCommand()->select("a.*,b.code as employee_code,b.name as employee_name")
            ->from("hr_boss_audit a")
            ->leftJoin("hr_employee b","a.employee_id = b.id")
            ->where("a.id=:id and ($sql or b.id=:employee_id) ",array(":id"=>$index,":employee_id"=>$this->employee_id))->queryRow();
		if ($row) {
            $this->id = $row['id'];
            $this->employee_id = $row['employee_id'];
            $this->lcu = $row['lcu'];
            $this->code = $row['employee_code'];
            $this->name = $row['employee_name'];
            $this->city = $row['city'];
            $this->apply_date = $row['apply_date'];
            $this->audit_year = $row['audit_year'];
            $this->json_text = json_decode($row['json_text'],true);
            $this->reject_remark = $row['reject_remark'];
            $this->status_type = $row['status_type'];
            $this->results_sum = $row['results_sum'];
            $this->results_a = $row['results_a'];
            $this->results_b = $row['results_b'];
            $this->results_c = $row['results_c'];
            $this->ratio_a = $row['ratio_a'];
            $this->ratio_b = $row['ratio_b'];
            $this->ratio_c = $row['ratio_c'];
            $this->json_listX = empty($row['json_listX'])?array():json_decode($row['json_listX'],true);
            return true;
		}else{
		    return false;
        }
	}

	public function getAjaxPlanYear($data){
        $type = key_exists("type",$data)?$data['type']:"planYearA";
        $city = key_exists("city",$data)?$data['city']:"";
        $name = key_exists("name",$data)?$data['name']:"";
        $value = key_exists("value",$data)?$data['value']:0;
        $value = floatval($value);
        $one_0 = key_exists("one_0",$data)?$data['one_0']:0;
        $one_0 = floatval($one_0);
        $one_1 = key_exists("one_1",$data)?$data['one_1']:0;
        $one_1 = floatval($one_1);
        $cofNow = key_exists("cofNow",$data)?$data['cofNow']:0;
        $cofNow = floatval($cofNow);
        $one_11 = key_exists("one_11",$data)?$data['one_11']:0;
        $one_11 = floatval($one_11);
        if($type === "planYearB"){
            $arr = $this->planYearB($name,$value,$one_1,$cofNow,$one_11,$city);
        }else{
            $arr = $this->planYearA($name,$value,$one_1,$cofNow,$one_11,$one_0,$city);
        }
        return $arr;
    }

    //$one_11:佔比
    protected function planYearB($name,$value,$one_1,$cofNow,$one_11,$city=""){
        $arr = array();
        $bossReviewCof = new BossReviewCof($name);
        $bossReviewCof->city = $city;
        $arr["two_3"] = $bossReviewCof->getClassCof($value,$one_1,$name);//系數
        $one_5 = $bossReviewCof->getClassLadder($arr["two_3"],$cofNow,$name,$one_1);//階梯落差
        $arr["two_5"] = abs($one_5);//階梯落差
        $one_5 = $one_5>0?$one_5*0.03:$one_5*0.08;
        $arr["two_6"] = $one_5;//落差系数
        $arr["two_7"] = $arr["two_3"]+$arr["two_6"];//实际系数
        $arr["two_7"] = $arr["two_7"]<0?0:$arr["two_7"];
        $arr["two_9"] = ($arr["two_7"]*$one_11)."%";//得分
        return $arr;
    }

    protected function planYearA($name,$value,$one_1,$cofNow,$one_11,$one_0,$city=""){
        $arr = array();
        $bossReviewCof = new BossReviewCof($name);
        $bossReviewCof->city = $city;
        if(in_array($name,array("one_six","one_seven","one_eight"))){
            $one_4 = $value;
            $arr["one_4"]= "\\";
        }else{
            if($name=="one_two"){
                $one_4 = empty($one_0)?0:($value-$one_0)/abs($one_0);
            }else{
                $one_4 = empty($one_0)?0:($value-$one_0)/$one_0;
            }
            $one_4 = round($one_4*100);
            $arr["one_4"]= $one_4."%";
        }
        if($name == "one_nine"){
            $one_4 = $value;
        }
        $arr["one_5"] = $bossReviewCof->getClassCof($one_4,$one_1,$name);//系數
        $one_8 = $bossReviewCof->getClassLadder($arr["one_5"],$cofNow,$name,$one_1);//階梯落差
        $arr["one_8"] = abs($one_8);//階梯落差
        $one_8 = $one_8>0?$one_8*0.03:$one_8*0.08;
        $arr["one_9"] = $one_8;//落差系数
        $arr["one_10"] = $arr["one_5"]+$arr["one_9"];//实际系数
        $arr["one_10"] = $arr["one_10"]<0?0:$arr["one_10"];
        $arr["one_12"] = ($arr["one_10"]*$one_11)."%";//得分
        return $arr;
    }

    public static function getBossRewardType($city){
        $row = Yii::app()->db->createCommand()->select("set_value")->from("hr_setting")
            ->where('set_name="bossRewardType" and set_city=:city',array(":city"=>$city))->queryScalar();
        return $row;
    }

	public function getContractTabList($model){
        $html = "<legend>";
        $html.=Yii::t("contract","review number")."：";
        if($model->status_type == 2){
            $html.='<span id="sum_label" data-num="no">';
        }else{
            $html.='<span id="sum_label" >';
        }
        $list = array(
            array(
                "name"=>Yii::t("contract","(A)Goal setting part"),
                "class"=>"BossReviewA"
            ),
            array(
                "name"=>Yii::t("contract","(B)Other details"),
                "class"=>"BossReviewB"
            )
        );
        $row = self::getBossRewardType($model->city);
        $ratio_a = $model->ratio_a*0.01;
        $ratio_b = $model->ratio_b*0.01;
        $model->ratio_c = 100-($model->ratio_a+$model->ratio_b);
        if($row!=1){//系統配置該城市不需要C部分
            $results = $model->results_a*$ratio_a+$model->results_b*$ratio_b+$model->results_c;
            $results = sprintf("%.2f",$results);
            $html.= $model->results_a."*{$model->ratio_a}% + ".$model->results_b."*{$model->ratio_b}% + ".$model->results_c."% = ".$results;
            $html.= "</span><span id='bossRewardType' data-num='0'></span>";
            $list[]=array(
                "name"=>Yii::t("contract","(C)Optional project section"),
                "class"=>"BossReviewC"
            );
        }else{
            $results = $model->results_a*$ratio_a+$model->results_b*$ratio_b;
            $results = sprintf("%.2f",$results);
            $html.= $model->results_a."*{$model->ratio_a}% + ".$model->results_b."*{$model->ratio_b}% = ".$results."%";
            $html.= "</span><span id='bossRewardType' data-num='1'></span>";
        }
        $html.="</legend>";
        echo $html;//輸出總分
        return $list;
    }

    public function getTabList(&$model,$searchBool=false){
        $list = $this->getContractTabList($model);
        $tabs = array();
        $updateBool = false;

        foreach ($list as $key=>$item){
            $className = $item["class"];
            $bossReviewModel = new $className($model,$searchBool);
            //後續修改，A項、B項的內容可以自由設定（開始）
            if(in_array($className,array("BossReviewA","BossReviewB"))){
                if(!empty($model->json_listX)){
                    $bossReviewModel->resetListX($model->json_listX);
                }elseif($this->getScenario()=="new"){
                    $bossReviewModel->cityListX();
                }
            }
            //後續修改，A項、B項的內容可以自由設定（結束）
            $html = $bossReviewModel->getTableHtml();
            //由于数据误差，所以需要实时修改
            if(!empty($model->id)&&$className=="BossReviewA"&&$bossReviewModel->scoreSum!=$model->results_a){
                $model->results_a = $bossReviewModel->scoreSum;
                $updateBool = true;
            }
            if(!empty($model->id)&&$className=="BossReviewB"&&$bossReviewModel->scoreSum!=$model->results_b){
                $model->results_b = $bossReviewModel->scoreSum;
                $updateBool = true;
            }
            $tabs[] = array(
                'id'=>"table_id_".$className,
                'label'=>$item["name"],
                'content'=>$html,
                'active'=>$key == 0?true:false,
            );
        }
        if($updateBool&&!$searchBool){
            $bossRewardType = BossApplyForm::getBossRewardType($model->city);
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
            ), 'id=:id', array(':id'=>$model->id));
        }
        return $tabs;
    }

    //刪除驗證
    public function deleteValidate(){
        $row = Yii::app()->db->createCommand()->select("employee_id")->from("hr_boss_audit")
            ->where('id=:id and status_type in (0,3,4)',array(':id'=>$this->id))->queryRow();
        if($row){
            return true;
        }else{
            return false;
        }
    }

	public function saveData($str='')
	{
		$connection = Yii::app()->db;
		$transaction=$connection->beginTransaction();
		try {
			$this->saveGoods($connection);
			$transaction->commit();
		}
		catch(Exception $e) {
			$transaction->rollback();
			throw new CHttpException(404,'Cannot update. ('.$e->getMessage().')');
		}
	}

	protected function saveGoods(&$connection) {
		$sql = '';
        switch ($this->scenario) {
            case 'delete':
                $sql = "delete from hr_boss_audit where id = :id and status_type in(0,3,4)";
                break;
            case 'new':
                $sql = "insert into hr_boss_audit(
							employee_id,results_a,results_b, results_c,ratio_a,ratio_b, ratio_c, results_sum, status_type, audit_year, json_text, city, apply_date, lcu
						) values (
							:employee_id,:results_a,:results_b, :results_c,:ratio_a,:ratio_b, :ratio_c, :results_sum, :status_type, :audit_year, :json_text, :city, :apply_date, :lcu
						)";
                break;
            case 'edit':
                $sql = "update hr_boss_audit set
							results_a = :results_a, 
							results_b = :results_b, 
							results_c = :results_c, 
							results_sum = :results_sum, 
							status_type = :status_type, 
							json_text = :json_text, 
							city = :city, 
							reject_remark = '', 
							apply_date = :apply_date,
							luu = :luu
						where id = :id and status_type in(0,3,4)
						";
                break;
        }
		if (empty($sql)) return false;

        $city = Yii::app()->user->city();
        $uid = Yii::app()->user->id;

        $command=$connection->createCommand($sql);
        if (strpos($sql,':id')!==false)
            $command->bindParam(':id',$this->id,PDO::PARAM_INT);
        //employee_id,results_a,results_b, results_c, results_sum, status_type, audit_year, json_text, city, apply_date, lcu
        if (strpos($sql,':employee_id')!==false)
            $command->bindParam(':employee_id',$this->employee_id,PDO::PARAM_INT);
        if (strpos($sql,':results_a')!==false)
            $command->bindParam(':results_a',$this->results_a,PDO::PARAM_INT);
        if (strpos($sql,':results_b')!==false)
            $command->bindParam(':results_b',$this->results_b,PDO::PARAM_INT);
        if (strpos($sql,':results_c')!==false)
            $command->bindParam(':results_c',$this->results_c,PDO::PARAM_INT);
        if (strpos($sql,':ratio_a')!==false)
            $command->bindParam(':ratio_a',$this->ratio_a,PDO::PARAM_INT);
        if (strpos($sql,':ratio_b')!==false)
            $command->bindParam(':ratio_b',$this->ratio_b,PDO::PARAM_INT);
        if (strpos($sql,':ratio_c')!==false)
            $command->bindParam(':ratio_c',$this->ratio_c,PDO::PARAM_INT);
        if (strpos($sql,':results_sum')!==false)
            $command->bindParam(':results_sum',$this->results_sum,PDO::PARAM_INT);
        if (strpos($sql,':status_type')!==false)
            $command->bindParam(':status_type',$this->status_type,PDO::PARAM_INT);
        if (strpos($sql,':audit_year')!==false)
            $command->bindParam(':audit_year',$this->audit_year,PDO::PARAM_INT);
        if (strpos($sql,':json_text')!==false){
            $json_text = json_encode($this->json_text);
            $command->bindParam(':json_text',$json_text,PDO::PARAM_LOB);
        }
        if (strpos($sql,':apply_date')!==false){
            $this->apply_date = in_array($this->status_type,array(4,5))?$this->apply_date:date('Y-m-d H:i:s');
            $command->bindParam(':apply_date',$this->apply_date,PDO::PARAM_STR);
        }

        if (strpos($sql,':city')!==false)
            $command->bindParam(':city',$city,PDO::PARAM_STR);
        if (strpos($sql,':luu')!==false)
            $command->bindParam(':luu',$uid,PDO::PARAM_STR);
        if (strpos($sql,':lcu')!==false)
            $command->bindParam(':lcu',$uid,PDO::PARAM_STR);
        $command->execute();

        if ($this->scenario=='new'){
            $this->id = Yii::app()->db->getLastInsertID();
            $this->setJsonListX();
        }

        $this->saveBossFlow();
        $this->sendEmail();//發送郵件
		return true;
	}

	protected function saveBossFlow(){
        if(in_array($this->status_type,array(1,5))){
            Yii::app()->db->createCommand()->insert('hr_boss_flow',array(
                'boss_id'=>$this->id,
                'state_type'=>"For Audit",
                'state_remark'=>"",
                'none_info'=>0,
                'lcu'=>Yii::app()->user->id,
            ));
        }
    }

	protected function setJsonListX(){
        Yii::app()->db->createCommand()->update('hr_boss_audit', array(
            'json_listX'=>json_encode($this->json_listX),
        ), 'id=:id', array(':id'=>$this->id));
    }

    protected function sendEmail(){
        if(in_array($this->status_type,array(1,5))){
            $email = new Email();
            $cityName = CGeneral::getCityName($this->city);
            if($this->status_type == 1){
                $description="老总年度考核申请 - ".$this->name."（".$cityName."）";
            }else{
                $description="老总年度考核二次审核 - ".$this->name."（".$cityName."）";
            }
            $subject=$description;
            $message="<p>员工编号：".$this->code."</p>";
            $message.="<p>员工姓名：".$this->name."</p>";
            $message.="<p>员工城市：".$cityName."</p>";
            $message.="<p>考核年份：".$this->audit_year."年</p>";
            $ratio_a = $this->ratio_a*0.01;
            $ratio_b = $this->ratio_b*0.01;
            if($this->status_type == 5){
                $message.="<p>得分（A）项：".($this->results_a*$ratio_a)."</p>";
                $message.="<p>得分（B）项：".($this->results_b*$ratio_b)."</p>";
                $message.="<p>得分（C）项：".$this->results_c."</p>";
                $message.="<p>总得分：".$this->results_sum."</p>";
            }
            $email->setDescription($description);
            $email->setMessage($message);
            $email->setSubject($subject);
            $email->addEmailToPrefixAndCity('BA05',$this->city);
            if(empty($email->getToAddr())){//沒有副總監
                $email->addEmailToPrefixAndCity('BA03',$this->city);
                Yii::app()->db->createCommand()->update('hr_boss_audit', array(
                    'boss_type'=>1,
                ), 'id=:id', array(':id'=>$this->id));
            }else{
                Yii::app()->db->createCommand()->update('hr_boss_audit', array(
                    'boss_type'=>2,
                ), 'id=:id', array(':id'=>$this->id));
            }
            $email->sent();
        }
    }

    private function lenStr($id){
        $code = strval($id);
        $str = "B";
        for($i = 0;$i < 5-strlen($code);$i++){
            $str.="0";
        }
        $str .= $code;
        return $str;
    }

    //驗證賬號是否綁定員工
    public function validateEmployee(){
        $uid = Yii::app()->user->id;
        $rows = Yii::app()->db->createCommand()->select("a.employee_id,b.code,b.name,b.city")->from("hr_binding a")
            ->leftJoin("hr_employee b","a.employee_id = b.id")
            ->where('a.user_id=:user_id',
                array(':user_id'=>$uid))->queryRow();
        if ($rows){
            $this->employee_id = $rows["employee_id"];
            $this->code = $rows["code"];
            $this->name = $rows["name"];
            $this->city = Yii::app()->user->city();
            $this->lcu = $uid;
            return true;
        }
        return false;
    }

	//判斷輸入框能否修改
	public function getInputBool(){
        if($this->getScenario() == "view"){
            return true;
        }
        if(!in_array($this->status_type,array(0,3))){
            return true;
        }else{
            return false;
        }
    }

    public function downExcel($downData){
        $cityName = General::getCityName($this->city);
        $excel = new DownBossExcel();
        $excel->SetYear($this->audit_year);
        $excel->SetUserName($this->name."({$this->code})");
        $excel->SetCityName($cityName);
        $excel->init();
        $excel->setBossExcelBody($downData);
        $excel->outExcel("bossAudit");
    }
}
