<?php

class ReviewHandleForm extends CFormModel
{
	public $id;
	public $employee_id;
	public $city;
	public $name;
	public $entry_time;
	public $company_name;
	public $dept_name;
	public $status_type;
	public $year_type;
	public $review_id;
	public $code;
	public $phone;
	public $year;
	public $department;
	public $review_type;
	public $change_num;
	public $four_with_sum;
	public $four_with_count;

    public $employee_remark;
    public $review_remark;
    public $strengths;
    public $target;
    public $improve;

    public $handle_id;
    public $handle_name;
    public $handle_per;
    public $review_sum;

	public $tem_s_ist;//审核权限的序列化
	public $tem_sum;


    public $no_of_attm = array(
        'review'=>0
    );
    public $docType = 'REVIEW';
    public $docMasterId = array(
        'review'=>0
    );
    public $files;
    public $removeFileId = array(
        'review'=>0
    );
	public function attributeLabels()
	{
		return array(
            'name'=>Yii::t('contract','Employee Name'),
            'code'=>Yii::t('contract','Employee Code'),
            'phone'=>Yii::t('contract','Employee Phone'),
            'dept_name'=>Yii::t('contract','Position'),
            'company_name'=>Yii::t('contract','Company Name'),
            'status_type'=>Yii::t('contract','Status'),
            'city'=>Yii::t('contract','City'),
            'entry_time'=>Yii::t('contract','Entry Time'),
            'year'=>Yii::t('contract','what year'),
            'year_type'=>Yii::t('contract','monthly'),
            'handle_name'=>Yii::t('contract','reviewAllot manager'),
            'handle_per'=>Yii::t('contract','manager percent'),

            'employee_remark'=>Yii::t('contract','employee remark'),
            'review_remark'=>Yii::t('contract','review remark'),
            'strengths'=>Yii::t('contract','employee strengths'),
            'target'=>Yii::t('contract','employee target'),
            'improve'=>Yii::t('contract','employee improve'),
            'review_type'=>Yii::t('contract','review type'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id,employee_id, name,code,phone,dept_name,company_name,handle_per,status_type,city,entry_time,year,year_type,handle_id,
			handle_name,tem_s_ist,employee_remark,review_remark,strengths,target,improve,review_type','safe'),
            array('id','required'),
            array('tem_s_ist','required'),
            array('id','validateID'),
            array('tem_s_ist','validateList'),
		);
	}

	public function validateID($attribute, $params){
	    if($this->validateEmployee()){
            $rows = Yii::app()->db->createCommand()->select("id,handle_per,tem_sum,review_id,four_with_count")->from("hr_review_h")
                ->where("id=:id and handle_id=:handle_id",array(":id"=>$this->id,":handle_id"=>$this->handle_id))->queryRow();
            if(!$rows){
                $message = Yii::t('contract','Employee Name'). Yii::t('contract',' not exist');
                $this->addError($attribute,$message);
            }else{
                $this->review_id = $rows["review_id"];
                $this->handle_per = $rows["handle_per"];
                $this->tem_sum = $rows["tem_sum"];
                $this->four_with_count = $rows["four_with_count"];
            }
        }else{
            $message = Yii::t("contract",'The account has no binding staff, please contact the administrator');
            $this->addError($attribute,$message);
        }
	}

    public function validateList($attribute, $params){
        if(!empty($this->tem_s_ist)){
            $rows = Yii::app()->db->createCommand()->select("c.city,b.employee_id,b.tem_s_ist,b.change_num,b.review_type,b.year,b.year_type")
                ->from("hr_review b")
                ->leftJoin("hr_employee c","c.id = b.employee_id")
                ->where("b.id=:id",array(":id"=>$this->review_id))->queryRow();
            if($rows){
                $this->review_sum = 0;
                $this->four_with_sum = 0;
                $this->city = $rows["city"];
                $this->employee_id = $rows["employee_id"];
                $this->review_type = $rows["review_type"];
                $this->change_num = $rows["change_num"];
                $this->year = $rows["year"];
                $this->year_type = $rows["year_type"];
                $rows = json_decode($rows["tem_s_ist"],true);
                foreach ($rows as $key => &$row){
                    foreach ($row["list"] as &$item){
                        if(isset($this->tem_s_ist[$key]['list'][$item['id']])){
                            $item = $this->tem_s_ist[$key]['list'][$item['id']];
                        }else{
                            $message = $item['name'].Yii::t("contract"," can not be empty");
                            $this->addError($attribute,$message);
                            return false;
                        }
                        if (!is_numeric($item["value"])){
                            $message = Yii::t('contract','review score'). Yii::t('contract',' Must be Numbers');
                            $this->addError($attribute,$message);
                            return false;
                        }
                        if($item["value"]>10 || $item["value"]<0){
                            $message = Yii::t('contract','review score').'必须在0至10之间';
                            $this->addError($attribute,$message);
                            return false;
                        }
                        if(!$this->scoringOk($item["value"])&&$this->getScenario()=="edit"){
                            if(!isset($item["remark"])||empty($item["remark"])){
                                $message = Yii::t('contract','Scoring remark')."（".$item['name']."）".Yii::t("contract"," can not be empty");
                                $this->addError($attribute,$message);
                                return false;
                            }
                        }else{
                            if(key_exists("remark",$item)&&$item["remark"] === ""){
                                unset($item["remark"]);
                            }
                        }
                        $this->review_sum+=intval($item["value"])*intval($row["num_ratio"]);
                        if($row["four_with"]==1){
                            $this->four_with_sum+=intval($item["value"])*intval($row["num_ratio"]);
                        }
                    }
                }
                $this->tem_s_ist = $rows;
            }else{
                $message = Yii::t('contract','reviewAllot project').Yii::t("contract"," can not be empty");
                $this->addError($attribute,$message);
                return false;
            }
        }
    }

    //評分安全數字範圍
    public function scoringOk($num){
        if(is_numeric($num)){
            $num = intval($num);
            if(in_array($num,array(6,7,8))){
                return true;
            }
        }
        return false;
    }

    //驗證賬號是否綁定員工
    public function validateEmployee(){
        $uid = Yii::app()->user->id;
        $rows = Yii::app()->db->createCommand()->select("employee_id,employee_name")->from("hr_binding")
            ->where('user_id=:user_id',
                array(':user_id'=>$uid))->queryRow();
        if ($rows){
            $this->handle_id = $rows["employee_id"];
            return $rows["employee_id"];
        }
        return false;
    }

	public function retrieveData($index) {
        $suffix = Yii::app()->params['envSuffix'];
        $city_allow = Yii::app()->user->city_allow();
        //,b.status_type,b.year,b.year_type,b.id as review_id
		$row = Yii::app()->db->createCommand()
            ->select("docman$suffix.countdoc('REVIEW',a.review_id) as reviewdoc,a.review_remark,a.strengths,a.target,a.improve,b.review_type,b.employee_remark,a.review_sum,a.handle_name,a.handle_per,a.tem_s_ist,a.review_id,b.employee_id,c.name,c.code,c.phone,c.city,c.entry_time,d.name as company_name,e.name as dept_name,a.status_type,b.year,b.year_type,a.id")
            ->from("hr_review_h a")
            ->leftJoin("hr_review b","a.review_id = b.id")
            ->leftJoin("hr_employee c","c.id = b.employee_id")
            ->leftJoin("hr_company d","c.company_id = d.id")
            ->leftJoin("hr_dept e","c.position = e.id")
            ->where("a.id=:id and a.handle_id = :handle_id and a.status_type in (1,4)",array(":id"=>$index,":handle_id"=>$this->handle_id))->queryRow();
		if ($row) {
            $this->id = $row['id'];
            $this->no_of_attm['review'] = $row['reviewdoc'];
            $this->status_type = $row['status_type'];
            //$this->status_type = ReviewAllotList::getReviewStatuts($review['status_type'])["status"];
            $this->handle_name = $row['handle_name'];
            $this->handle_per = $row['handle_per'];
            $this->review_id = $row['review_id'];
            $this->tem_s_ist = json_decode($row['tem_s_ist'],true);
            $this->year = $row['year'];
            $this->year_type = $row['year_type'];
            $this->employee_id = $row['employee_id'];
            $this->review_type = $row['review_type'];
            $this->name = $row['name'];
            $this->city = $row["city"];
            $this->entry_time = $row['entry_time'];
            $this->company_name = $row['company_name'];
            $this->dept_name = $row['dept_name'];
            $this->code = $row['code'];
            $this->phone = $row['phone'];
            $this->review_sum = $row['review_sum'];

            $this->employee_remark = $row['employee_remark'];
            $this->review_remark = $row['review_remark'];
            $this->strengths = $row['strengths'];
            $this->target = $row['target'];
            $this->improve = $row['improve'];
            return true;
		}else{
		    return false;
        }
	}

	public function getReadonly(){
        if ($this->getScenario()=='view'){
            return true;//只读
        }else{
            return false;
        }
    }

    public function getReviewNumList(){
        return array(
            0=>Yii::t("contract","zero"),
            1=>Yii::t("contract","Behave extremely badly"),
            2=>Yii::t("contract","Bad performance"),
            3=>Yii::t("contract","Behave badly"),
            4=>Yii::t("contract","Poor performance"),
            5=>Yii::t("contract","Performance can be"),
            6=>Yii::t("contract","Performance standards"),
            7=>Yii::t("contract","Stable performance"),
            8=>Yii::t("contract","perform well"),
            9=>Yii::t("contract","Good performance"),
            10=>Yii::t("contract","Excellent performance"),
        );
    }

    public function reviewHandleDiv($model='',$textBool=false){
        if(empty($model)){
            $model = $this;
        }
        $bool = $model->getReadonly();
        $className = get_class($model);
        $html = '';
        if(is_array($model->tem_s_ist)){
            foreach ($model->tem_s_ist as $set_id =>$items){
                $html.=TbHtml::hiddenField($className."[tem_s_ist][$set_id][code]",$items['code']);
                $html.=TbHtml::hiddenField($className."[tem_s_ist][$set_id][name]",$items['name']);
                $html.=TbHtml::hiddenField($className."[tem_s_ist][$set_id][num_ratio]",$items['num_ratio']);
                $html.=TbHtml::hiddenField($className."[tem_s_ist][$set_id][four_with]",$items['four_with']);
                $html.="<div class='form-group'><div class='col-sm-9 col-sm-offset-2'><table class='table table-bordered table-striped reviewTable' data-ratio='".$items['num_ratio']."' data-four='".$items['four_with']."'>";
                $html.="<thead><tr><th colspan='2'>".$items['code']."（".$items['name']."）</th><th width='36%'>".Yii::t("contract","Scoring remark")."</th></tr></thead><tbody>";

                foreach ($items['list'] as $key =>$item){
                    if(!is_array($item)){
                        return '';
                    }//
                    $item['value'] = isset($item['value'])?$item['value']:7;
                    $num = $key;
                    $name = $className."[tem_s_ist][$set_id][list][$num]";
                    $html.="<tr data-name='$name'><td width='33%'>".$item['name']."</td>";
                    $html.="<td>";
                    if(isset($item['id'])){
                        $html.=TbHtml::hiddenField($name."[id]",$item['id']);
                    }
                    $html.=TbHtml::hiddenField($name."[name]",$item['name']);
                    $html.=TbHtml::dropDownList($name."[value]",$item['value'],$this->getReviewNumList(),array('class'=>'form-control changeSelect','readonly'=>$textBool));
                    $html.="</td>";
                    $html.="<td class='remark'>";
                    if(!$this->scoringOk($item['value'])||key_exists("remark",$item)||$textBool){
                        $item['remark'] = isset($item['remark'])?$item['remark']:'';
                        $html.=TbHtml::textArea($name."[remark]",$item['remark'],array('rows'=>1,'readonly'=>$textBool));
                    }else{
                        $html.=TbHtml::button("<span class='glyphicon glyphicon-plus'></span>",array("class"=>"addRemark"));
                    }
                    $html.="</td>";
                    $html.="</tr>";
                }

                $html.="</tbody></table></div></div>";
            }
        }
        return $html;
    }

    //刪除驗證
    public function deleteValidate(){
        return false;
    }

    //拷貝
    public function getLastTemList(){
        $arr = array();
        $className = get_class($this);
        $row = Yii::app()->db->createCommand()
            ->select("a.tem_s_ist,a.review_remark,a.strengths,a.target,a.improve")->from("hr_review_h a")
            ->leftJoin("hr_review b","a.review_id = b.id")
            ->where("a.handle_id = :handle_id and a.status_type = 3 and b.employee_id=:id",
                array(":id"=>$this->employee_id,":handle_id"=>$this->handle_id)
            )->order("b.year desc,b.year_type desc")->queryRow();
        if($row){
            $name = $className."_review_remark";
            $arr[$name] = array('value'=>$row["review_remark"]);
            $name = $className."_strengths";
            $arr[$name] = array('value'=>$row["strengths"]);
            $name = $className."_target";
            $arr[$name] = array('value'=>$row["target"]);
            $name = $className."_improve";
            $arr[$name] = array('value'=>$row["improve"]);
            $tem_s_list = json_decode($row["tem_s_ist"],true);
            foreach ($tem_s_list as $set_id => $list){
                foreach ($list["list"] as $key=>$item){
                    $name = $className."_tem_s_ist_".$set_id."_list_".$key."_value";
                    $arr[$name] = $item;
                }
            }
        }
        return $arr;
    }

	public function saveData()
	{
		$connection = Yii::app()->db;
		//$transaction=$connection->beginTransaction();
		try {
			$this->saveGoods($connection);
			//$transaction->commit();
		}
		catch(Exception $e) {
			//$transaction->rollback();
			throw new CHttpException(404,'Cannot update. ('.$e->getMessage().')');
		}
	}

	protected function saveGoods(&$connection) {
        $uid = Yii::app()->user->id;
        switch ($this->scenario) {
            case 'edit':
                $connection->createCommand()->update('hr_review_h', array(
                    'tem_s_ist'=>json_encode($this->tem_s_ist),
                    'review_sum'=>$this->review_sum,
                    'four_with_sum'=>$this->four_with_sum,
                    'status_type'=>$this->status_type,
                    'review_remark'=>$this->review_remark,
                    'strengths'=>$this->strengths,
                    'target'=>$this->target,
                    'improve'=>$this->improve,
                    'luu'=>$uid,
                ), 'id=:id', array(':id'=>$this->id));
                break;
        }

        $this->sendReview($connection);
		return true;
	}

	protected function sendReview($connection){
        if($this->status_type == 3){ //考核完成
            $status_type = 3;//2:部分考核完成（兩個經理時，有一個已經完成）
            $review_sum = $this->sumReview(array(
                "review_sum"=>$this->review_sum,
                "handle_per"=>$this->handle_per,
                "tem_sum"=>$this->tem_sum,
                "four_with_sum"=>$this->four_with_sum,
                "four_with_count"=>$this->four_with_count,
            ));
            $rows = Yii::app()->db->createCommand()->select("*")->from("hr_review_h")
                ->where('review_id=:review_id',
                    array(':review_id'=>$this->review_id))->queryAll();
            if($rows){
                foreach ($rows as $row){
                    if ($row['id']!=$this->id){ //不計算自己
                        if($row['status_type']!=3){
                            $status_type = 2;
                        }
                        $row["review_sum"] = empty($row["review_sum"])?0:intval($row["review_sum"]);
                        //$review_sum+=$row["review_sum"];
                        $review_sum+=$this->sumReview($row);
                    }
                }
            }
            if($status_type == 3){
                switch ($this->review_type){
                    case 2://技術員
                        $review_sum*=0.85;
                        $change_num = 15-($this->change_num*0.5);
                        $change_num = $change_num<0?0:$change_num;
                        $review_sum+=$change_num;
                        break;
                    case 3://銷售
                        $review_sum+=$this->change_num*7;
                        break;
                }
            }
            $connection->createCommand()->update("hr_review", array(
                'status_type'=>$status_type,
                'review_sum'=>$status_type==3?$review_sum:null
            ), 'id=:id', array(':id'=>$this->review_id));
            if($status_type == 3 && $this->city!="BJ"){
                $this->sendEmail($review_sum,$rows);
            }
        }
    }

    protected function sendEmail($review_sum,$rows){
        $email = new Email();
        $description="人才優化評核完成 - ".$this->name."(".$this->year." ".ReviewAllotList::getYearTypeList($this->year_type,$this->year).")";
        $subject=$description;
        $colspan = count($rows)+1;
        $width = intval(50/$colspan);
        $message="<p>员工编号：".$this->code."</p>";
        $message.="<p>员工姓名：".$this->name."</p>";
        $message.="<p>入职时间：".$this->entry_time."</p>";
        $message.="<p>员工职位：".$this->dept_name."</p>";
        $message.="<p>评核总得分：$review_sum</p>";
        $message.="<table width='600px' border='1px'>";
        $message.="<thead><tr><th colspan='2' width='50%'>表现因素</th>";
        $footArr = array( //表格底部統計
            'sumNum'=>array(),
            'sumList'=>array(),
            'preList'=>array()
        );
        $withArr = $footArr;
        $handleNameHtml = "";
        if($this->review_type == 3){
            $rows[] = array(
                "four_with_count"=>0,
                "four_with_sum"=>0,
                "tem_sum"=>1,
                "handle_per"=>70,
                "review_sum"=>$this->change_num,
                "handle_name"=>Yii::t("contract","Substantial sales performance")
            );
        }
        foreach ($rows as $row){
            if (key_exists("handle_id",$row)){
                if(Yii::app()->params['retire']||!isset(Yii::app()->params['retire'])){ //台灣地區不發送郵件
                    $email->addEmailToStaffId($row['handle_id']);//添加考核人郵箱
                }
            }
            $message.="<th width='$width%'>".$row['handle_per']."(%)</th>";
            $handleNameHtml.="<th>".$row['handle_name']."</th>";
            $footArr['sumNum'][] = $row['tem_sum'];
            $footArr['sumList'][] = $row['review_sum'];
            $footArr['preList'][] = $row['handle_per'];
            $withArr['sumNum'][] = $row['four_with_count'];
            $withArr['sumList'][] = $row['four_with_sum'];
            $withArr['preList'][] = $row['handle_per'];
        }
        $model = new ReviewSearchForm();
        $model->with_foot = $withArr;
        $model->employee_id = $this->employee_id;
        $model->status_type = 3;
        $model->review_type = $this->review_type;
        $model->change_num = $this->change_num;
        $model->year = $this->year;
        $model->year_type = $this->year_type;
        $message.="<th width='$width%'>&nbsp;</th></tr>";
        $message.="<tr><th colspan='2'>被评核员工</th><th colspan='$colspan'>".$this->name."</th></tr>";
        $message.="<tr><th colspan='2'>做出评核之员工</th>$handleNameHtml<th>总分</th></tr>";
        $message.="</thead><tbody><tr><td colspan='".($colspan+2)."'>季度评核得分 (";
        $message.=$this->review_type==2?85:100;
        switch ($this->review_type){
            case 2:
                $message.=Yii::t("contract","Quarterly assessment score")." (85%)";
                break;
            case 4:
                if(!empty($withArr['sumNum'][0])){
                    $message.=Yii::t("contract","Evaluate project score")." (90%)";
                }else{
                    $message.=Yii::t("contract","Evaluate project score")." (100%)";
                }
                break;
            default:
                $message.=Yii::t("contract","Quarterly assessment score")." (100%)";
        }
        $message.="%)</td></tr></tbody>";
        $message.="<tfoot>";
        $message.=$model->returnTableFoot($footArr,'',true);
        $message.="</tfoot>";
        $message.="</table>";
        $email->setDescription($description);
        $email->setMessage($message);
        $email->setSubject($subject);
        if(Yii::app()->params['retire']||!isset(Yii::app()->params['retire'])){ //台灣地區
            $email->addEmailToStaffId($this->employee_id);//添加備考人郵箱
        }else{
            $email->addEmailOnlyRetire();//,统一发至中央资源部
        }
        $email->sent();
    }

    //private function sumReview($sum,$pro,$num,$four_with_sum){
    private function sumReview($arr){
        //row["review_sum"],$row["handle_per"],$row["tem_sum"],$row["four_with_sum"]
        $sum = intval($arr["review_sum"]);
        $pro = intval($arr["handle_per"]);
        $num = intval($arr["tem_sum"])*10;
        $fourSum = intval($arr["four_with_sum"]);
        $fourCount = intval($arr["four_with_count"])*10;
        if($this->review_type == 4&&!empty($fourCount)){ //地區主管
            $reviewSum = ($fourSum/$fourCount)*$pro*0.1;
            $reviewSum+= (($sum-$fourSum)/($num-$fourCount))*$pro*0.9;
            return sprintf("%.2f",$reviewSum);
        }else{
            return sprintf("%.2f",($sum/$num)*$pro);
        }
    }
}
