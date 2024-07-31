<?php

class ReviewSearchForm extends CFormModel
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
	public $name_list;
	public $login_id;
	public $review_type;
	public $change_num;
    public $department;//部門
    public $leave_bool = '待定';//評分差異排名後的等級顯示
    public $ranking_bool = false;//评分差异排名
    public $ranking_sum = false;//评分差异排名参与人数
    public $ranking_review;//參與差異性的考核id(新)

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
	public $table_foot;
	public $with_foot;//四用表格
	public $pro_str='';//統計表格的字符串.例如：（甲-乙）


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
            'name_list'=>Yii::t('contract','reviewAllot manager'),
            'review_sum'=>Yii::t('contract','review sum'),

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
			handle_name,name_list,tem_s_ist','safe'),
            array('id','required'),
            array('employee_remark','required'),
            array('id','validateID'),
		);
	}

	public function validateID($attribute, $params){
	    if($this->validateEmployee()){
            $rows = Yii::app()->db->createCommand()->select("id")->from("hr_review")
                ->where("id=:id and employee_id=:employee_id",array(":id"=>$this->id,":employee_id"=>$this->login_id))->queryRow();
            if(!$rows){
                $message = Yii::t('contract','Employee Name'). Yii::t('contract',' not exist');
                $this->addError($attribute,$message);
            }
        }else{
            $message = Yii::t("contract",'The account has no binding staff, please contact the administrator');
            $this->addError($attribute,$message);
        }
	}

    //驗證賬號是否綁定員工
    public function validateEmployee(){
        $uid = Yii::app()->user->id;
        $rows = Yii::app()->db->createCommand()->select("employee_id,employee_name")->from("hr_binding")
            ->where('user_id=:user_id',
                array(':user_id'=>$uid))->queryRow();
        if ($rows||Yii::app()->user->validFunction('ZR09')){
            $this->login_id = isset($rows["employee_id"])?$rows["employee_id"]:"";
            return true;
        }
        return false;
    }

	public function retrieveData($index) {
        $suffix = Yii::app()->params['envSuffix'];
        $city_allow = Yii::app()->user->city_allow();
        //,b.status_type,b.year,b.year_type,b.id as review_id
        $expr_sql = '';
        if(!Yii::app()->user->validFunction('ZR09')&&!Yii::app()->user->validFunction('ZR15')){//沒有所有權限
            $expr_sql.=" and (FIND_IN_SET('$this->login_id',b.id_s_list) or b.employee_id = '$this->login_id' or b.lcu = '$this->login_id')";
        }
		$row = Yii::app()->db->createCommand()
            ->select("c.department,b.review_type,b.ranking_sum,b.ranking_bool,b.ranking_review,
            b.change_num,b.employee_remark,b.review_remark,b.strengths,b.target,b.improve,b.tem_s_ist,b.review_sum,b.name_list,b.employee_id,c.name,c.code,c.phone,c.city,c.entry_time,d.name as company_name,e.name as dept_name,b.status_type,b.year,b.year_type,b.id,docman$suffix.countdoc('REVIEW',b.id) as reviewdoc")
            //->select("c.department,b.review_type,b.change_num,b.employee_remark,b.review_remark,b.strengths,b.target,b.improve,b.tem_s_ist,b.review_sum,b.name_list,,b.employee_id,c.name,c.code,c.phone,c.city,c.entry_time,d.name as company_name,e.name as dept_name,b.status_type,b.year,b.year_type,b.id,docman$suffix.countdoc('REVIEW',b.id) as reviewdoc")
            ->from("hr_review b")
            ->leftJoin("hr_employee c","c.id = b.employee_id")
            ->leftJoin("hr_company d","c.company_id = d.id")
            ->leftJoin("hr_dept e","c.position = e.id")
            ->where("b.id=:id and b.status_type in (1,2,3) $expr_sql",array(":id"=>$index))->queryRow();
		if ($row) {
            $this->id = $row['id'];
            $this->no_of_attm['review'] = $row['reviewdoc'];
            $this->status_type = $row['status_type'];
            //$this->status_type = ReviewAllotList::getReviewStatuts($review['status_type'])["status"];
            $this->name_list = $row['name_list'];
            $this->tem_s_ist = json_decode($row['tem_s_ist'],true);
            $this->year = $row['year'];
            $this->year_type = $row['year_type'];
            $this->employee_id = $row['employee_id'];
            $this->name = $row['name'];
            $this->city = $row["city"];
            $this->entry_time = $row['entry_time'];
            $this->company_name = $row['company_name'];
            $this->dept_name = $row['dept_name'];
            $this->code = $row['code'];
            $this->department = $row['department'];
            $this->phone = $row['phone'];
            $this->review_type = $row['review_type'];
            $this->change_num = $row['change_num'];
            $this->review_sum = $row['review_sum'];
//&#10;
            $this->employee_remark = $row['employee_remark'];
            $this->review_remark = '';
            $this->strengths = '';
            $this->target = '';
            $this->improve = '';
            /*固定差異性排名*/
            $this->ranking_bool = empty($row['ranking_bool'])?false:true;
            $this->ranking_sum = $row['ranking_sum'];
            $this->ranking_review = empty($row['ranking_review'])?"''":$row['ranking_review'];
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

    public function getShowBool($row){
	    return $row['status_type']==3&&($row['handle_id']==$this->login_id||$this->employee_id==$this->login_id||$this->status_type == 3);
    }

//員工評語
    public function resetRemarkList($row){
        $bool = $row['status_type']==3&&($row['handle_id']==$this->login_id||$this->employee_id==$this->login_id||$this->status_type == 3);
        if($bool&&!empty($row["review_remark"])){
            $this->review_remark .=$row["handle_name"].":\r\n".$row["review_remark"]."\r\n";
        }
        if($bool&&!empty($row["strengths"])){
            $this->strengths .=$row["handle_name"].":\r\n".$row["strengths"]."\r\n";
        }
        if($bool&&!empty($row["target"])){
            $this->target .=$row["handle_name"].":\r\n".$row["target"]."\r\n";
        }
        if($bool&&!empty($row["improve"])){
            $this->improve .=$row["handle_name"].":\r\n".$row["improve"]."\r\n";
        }
    }

    protected function getTableHeaderHtml($rows,$bool = false){
        if($bool&&$this->review_type == 3){
            $rows[] = array(
                "handle_per"=>"70",
                "handle_name"=>Yii::t("contract","Substantial sales performance")
            );
            $this->table_foot["sumNum"][] = 1;
            $this->table_foot["sumList"][] = $this->change_num;
            $this->table_foot["preList"][] = 70;
        }
        $colspan = count($rows)+1;
        $width = intval(50/$colspan);
        $handleNameHtml = '';
        $handleHtml="<div class='form-group'><div class='col-sm-5 col-sm-offset-2'><table class='table table-bordered'>";
        $handleHtml.="<thead><tr><th colspan='2' width='50%' class='text-center'>".Yii::t("contract","Performance factors")."</th>";
        foreach ($rows as $row){
            $handleHtml.="<th width='$width%'>".$row['handle_per']."(%)</th>";
            $handleNameHtml.="<th>".$row['handle_name']."</th>";
        }
        $handleHtml.="<th width='$width%'>&nbsp;</th></tr>";
        $handleHtml.="<tr><th colspan='2' class='text-center'>".Yii::t("contract","Employee evaluated")."</th><th colspan='$colspan'>".$this->name."</th></tr>";
        $handleHtml.="<tr><th colspan='2' class='text-center'>".Yii::t("contract","Assessment person")."</th>$handleNameHtml<th>".Yii::t("contract","review number")."</th></tr>";

        return $handleHtml;
    }

    public function getTabList(){
        $tabs = array();
        $rows = Yii::app()->db->createCommand()->select("*")->from("hr_review_h")
            ->where("review_id=:review_id",array(":review_id"=>$this->id))->queryAll();
        if(is_array($this->tem_s_ist)&&$rows){
            $this->table_foot = array( //表格底部統計
                'sumNum'=>array(),
                'sumList'=>array(),
                'preList'=>array()
            );
            $handleHtml=$this->getTableHeaderHtml($rows);
            foreach ($rows as &$row){
                $this->resetRemarkList($row);//員工評語
                $row['list'] = json_decode($row["tem_s_ist"],true);
                $this->table_foot["sumNum"][] = 0;
                $this->table_foot["sumList"][] = 0;
                $this->table_foot["preList"][] = $row["handle_per"];
            }
            $this->with_foot = $this->table_foot;
            foreach ($this->tem_s_ist as $set_id => $setList) {
                $this->pro_str = empty($this->pro_str)?"（".$setList['code']."-":$this->pro_str;
                $content = $this->reviewSearchDiv($set_id,$setList,$rows,$handleHtml);
                $tabs[] = array(
                    'label'=>$setList['code']."（".$setList['name']."）",
                    'content'=>"<p>&nbsp;</p>".$content,
                    'active'=>false,
                );
            }
            if (isset($setList['code'])){
                $this->pro_str .= $setList['code']."）";
            }
            $content = "<p>&nbsp;</p>".$this->getCountTable($this->getTableHeaderHtml($rows,$this->status_type == 3));
            $tabs[] = array(
                'label'=>Yii::t("contract","review sum"),
                'content'=>&$content,
                'active'=>true,
            );
            $reviewGrade = Yii::t("contract","review grade");

            //評分排名
            if($this->ranking_bool){
                $reviewGrade = Yii::t("contract","review sum Grade");
                $rankingHtml =$this->getRankingHtml();
                //隱藏排名信息
                if(Yii::app()->user->validFunction('ZR20')){
                    $tabs[] = array(
                        'label'=>Yii::t("contract","review sum ranking"),
                        'content'=>"<p>&nbsp;</p>".$rankingHtml,
                        'active'=>false,
                    );
                }
            }
            $content = str_replace(":RANKINGLEAVE",$this->leave_bool,$content);
            $content = str_replace(":REVIEWGRADE",$reviewGrade,$content);
        }
        return $tabs;
    }

    protected function reviewSearchDiv($set_id,$setList,$rows,$handleHtml){
        $sum = (count($setList['list'])*10)*intval($setList['num_ratio']);
        $footArr = array( //表格底部統計
            'sumNum'=>array(),
            'sumList'=>array(),
            'preList'=>array()
        );
        //表格頭部顯示
        $html=$handleHtml;
        $html.="<tr><th width='1%'>".$setList['code']."</th><th>".$setList['name']."</th>";
        for ($i=0;$i<count($rows);$i++){
            if(!isset($footArr["sumList"][$i])){
                $footArr["sumNum"][$i] = count($setList['list'])*intval($setList['num_ratio']);
                $footArr["sumList"][$i] = 0;
                $footArr["preList"][$i] = $rows[$i]["handle_per"];
            }
            $html.="<th>$sum</th>";
        }
        $html.="<th>".($sum*count($rows))."</th></tr>";
        $html.="</thead><tbody>";
        $num =0;
        //表格內容
        $width = intval(50/(count($rows)+1));
        foreach ($setList["list"] as $proList) {
            $num++;
            if($num%2 == 0){
                $html.="<tr><td>$num</td>";
            }else{
                $html.="<tr class='active'><td>$num</td>";
            }
            $html.="<td class='showNum'>".$proList["name"]."</td>";
            $proSum = 0;
            $proArr= array();
            for ($i=0;$i<count($rows);$i++){
                $this->table_foot["sumNum"][$i]+=intval($setList['num_ratio']);
                if($setList["four_with"]==1){
                    $this->with_foot["sumNum"][$i]+=intval($setList['num_ratio']);
                }
                $bool = ($rows[$i]['handle_id']!=$this->login_id&&$this->employee_id!=$this->login_id);
                $bool = in_array($rows[$i]['status_type'],array(1,4))||($bool&&$this->status_type != 3);
                if(!isset($rows[$i]["list"][$set_id]["list"][$proList["id"]]["value"])||$bool){
                    $proSum = '-';
                    $proValue = "-";
                }else{
                    $proValue = $rows[$i]["list"][$set_id]["list"][$proList["id"]]["value"];
                    $proValue *= intval($setList['num_ratio']);
                    $footArr["sumList"][$i]+=$proValue;
                    $this->table_foot["sumList"][$i]+=$proValue;
                    if($setList["four_with"]==1){
                        $this->with_foot["sumList"][$i]+=$proValue;
                    }
                    $proSum = $proSum+$proValue;
                }
                //if((!ReviewHandleForm::scoringOk($proValue)||isset($rows[$i]["list"][$set_id]["list"][$proList["id"]]["remark"]))&&$proValue!="-"){
                if($proValue!="-"&&key_exists("remark",$rows[$i]["list"][$set_id]["list"][$proList["id"]])){
                    $remark = $rows[$i]["list"][$set_id]["list"][$proList["id"]]["remark"];
                    if(!empty($remark)){
                        $colorList = array("text-danger","text-info","text-warning","text-primary");
                        $key = in_array($i,array(0,1,2,3))?$i:0;
                        $proArr[]=array('color'=>$colorList[$key],'name'=>$rows[$i]['handle_name'],'remark'=>htmlspecialchars($remark));
                    }
                }
                $html.="<td class='showNum' width='$width%'>$proValue</td>";
            }
            $html.="<td class='showNum' width='$width%'>$proSum</td>";
            if(!empty($proArr)){
                $html.="<td class='remark'>";
                if(count($proArr)>1){
                    foreach ($proArr as $remarkArr){
                        $html.="<span class='".$remarkArr['color']."'>".$remarkArr["name"]."：".$remarkArr["remark"]."</span><br>";
                    }
                }else{
                    $html.="<span class='".$proArr[0]['color']."'>".$proArr[0]["remark"]."</span>";
                }
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

    protected function getRankingHtml(){
        // table-striped
        $html="<div class='form-group'><div class='col-sm-8 col-sm-offset-2'><table class='table table-bordered'>";

        $html.="<thead><tr>";
        $html.="<th>".Yii::t("contract","Employee Code")."</th>";
        $html.="<th>".Yii::t("contract","Employee Name")."</th>";
        $html.="<th>".Yii::t("contract","City")."</th>";
        $html.="<th>".Yii::t("contract","Department")."</th>";
        $html.="<th>".Yii::t("contract","Position")."</th>";
        $html.="<th>".Yii::t("contract","review sum")."</th>";

        $show_ranking = true;
        $reviewArr = array();
        $reviewId = array();
        $rankingArr = array(
            array("maxNum"=>round($this->ranking_sum*0.2),"list"=>array(),"leave"=>"I","class"=>"success"),
            array("maxNum"=>round($this->ranking_sum*0.2),"list"=>array(),"leave"=>"II","class"=>"info"),
            array("maxNum"=>round($this->ranking_sum*0.3),"list"=>array(),"leave"=>"III","class"=>"warning"),
            array("maxNum"=>round($this->ranking_sum*0.2),"list"=>array(),"leave"=>"IV","class"=>"active"),
            array("maxNum"=>round($this->ranking_sum*0.1),"list"=>array(),"leave"=>"V","class"=>"danger"),
        );
        $reviewRows = Yii::app()->db->createCommand()->select("b.employee_id,b.review_sum,b.status_type as review_status_type,c.*,e.name as dept_name")->from("hr_review b")
            ->leftJoin("hr_employee c","c.id = b.employee_id")
            ->leftJoin("hr_dept e","c.position = e.id")
            ->where("b.id in ({$this->ranking_review})")
            ->order("b.review_sum desc")->queryAll();
        if($reviewRows){//組別內超過10個人並且需要差異性排名
            foreach ($reviewRows as $row){
                if($row['review_status_type']==4){
                    continue;
                }
                $reviewArr[$row["employee_id"]]=$this->resetRanking($rankingArr,$row);
                //$reviewArr[$row["employee_id"]]=array("name"=>$row["review_sum"],"ranking"=>'I',"class"=>'');
                $reviewId[] = $row["employee_id"];
                if($row['review_status_type']!=3){
                    $show_ranking = false;
                }
            }
        }
        if($show_ranking){
            $html.="<th>".Yii::t("contract","review grade")."</th>";
        }
        $html.="</tr></thead><tbody>";
        if($reviewRows){
            foreach ($reviewRows as $row){
                $employee_list = $this->getArrValueToKey($row["employee_id"],$reviewArr);
                $html.="<tr class='";
                if($row['id'] == $this->employee_id){
                    $html.=" text-weight ";
                }
                $html.=$employee_list["class"];
                $html.="'>";
                $html.="<td>".$row['code']."</td>";
                $html.="<td>".$row['name']."</td>";
                $html.="<td>".CGeneral::getCityName($row['city'])."</td>";
                $html.="<td>".DeptForm::getDeptToId($row['department'])."</td>";
                $html.="<td>".$row['dept_name']."</td>";
                $html.="<td>".$employee_list['name']."</td>";
                if($show_ranking){
                    if($row['id'] == $this->employee_id){
                        $this->leave_bool = $employee_list['ranking'];
                    }
                    $html.="<td>".$employee_list['ranking']."</td>";
                }
                $html.="</tr>";
            }
        }
        $html.="</tbody></table></div></div>";
        return $html;
    }

    protected function resetRanking(&$rankingArr,&$row){
        foreach ($rankingArr as $key =>&$arr){
            if(count($arr["list"])<$arr["maxNum"]){
                if($key!==0){
                    if(end($rankingArr[($key-1)]["list"]) == $row["review_sum"]){
                        return array("name"=>$row["review_sum"],"ranking"=>$rankingArr[($key-1)]["leave"],"class"=>$rankingArr[($key-1)]["class"]);
                    }
                }
                if($key === 4){
                    if($row["review_sum"]>=50){
                        return array("name"=>$row["review_sum"],"ranking"=>$rankingArr[3]["leave"],"class"=>$rankingArr[3]["class"]);
                    }
                }
                $arr["list"][] = $row["review_sum"];
                return array("name"=>$row["review_sum"],"ranking"=>$arr["leave"],"class"=>$arr["class"]);
            }
        }

        if($row["review_sum"]>=50){
            return array("name"=>$row["review_sum"],"ranking"=>$rankingArr[3]["leave"],"class"=>$rankingArr[3]["class"]);
        }else{
            return array("name"=>$row["review_sum"],"ranking"=>$rankingArr[4]["leave"],"class"=>$rankingArr[4]["class"]);
        }
    }

    protected function getArrValueToKey(&$key,&$arr){
        if (key_exists($key,$arr)){
            if($arr[$key]['name'] === null){
                return array("name"=>Yii::t("contract","in review"),"ranking"=>0,"class"=>'');
            }else{
                return $arr[$key];
            }
        }else{
            return array("name"=>Yii::t("contract","none review"),"ranking"=>0,"class"=>'');
        }
    }

    public function validateWithFoot(){
        if(empty($this->with_foot["sumNum"][0])){
            return false;
        }else{
            return true;
        }
    }

    public function returnTableFoot($footArr,$str="",$bool=false){
        $withFoot = $this->with_foot;
        $nameStr = (in_array($this->review_type,array(2,4))&&$this->status_type==3&&$bool)?Yii::t("contract","employee total score"):Yii::t("contract","Percentage Sum");
        $footList = array(
            array("code"=>"A","name"=>Yii::t("contract","Project total score").$str,"list"=>array()),
            array("code"=>"B","name"=>Yii::t("contract","assessed total score"),"list"=>array()),
            array("code"=>"C","name"=>Yii::t("contract","Percentage score")."（B/A*100）","list"=>array()),
            array("code"=>"D","name"=>Yii::t("contract","Scoring ratio"),"list"=>array()),
            array("code"=>"E","name"=>$nameStr."（C*D）","list"=>array()),
        );
        if($this->status_type==3&&$bool){
            switch ($this->review_type){
                case 2:
                    $footList[] = array("code"=>"F","pre"=>0.85,"name"=>Yii::t("contract","Percentage Sum")."（E*85%）","list"=>array());
                    break;
                case 4:
                    if(!empty($this->with_foot["sumNum"][0])){ //含有四有
                        $footList[] = array("code"=>"F","pre"=>0.9,"name"=>Yii::t("contract","Percentage Sum")."（E*90%）","list"=>array());
                    }
                    break;
            }
        }
        $html = '';
        foreach ($footArr["sumList"] as $key => $sum){
            $sumNum = $footArr["sumNum"][$key]*10;
            if($this->review_type == 4&&$bool){
                $sumNum -=$withFoot["sumNum"][$key]*10;
                $sum -=$withFoot["sumList"][$key];
            }
            $footList[0]['list'][$key] = $sumNum;
            $footList[1]['list'][$key] = $sum;
            $footList[2]['list'][$key] = sprintf("%.2f",($sum/$sumNum)*100);
            $footList[3]['list'][$key] = $footArr["preList"][$key]."%";
            $footList[4]['list'][$key] = sprintf("%.2f",($sum/$sumNum)*$footArr["preList"][$key]);
            if($this->status_type==3&&$bool){
                switch ($this->review_type){
                    case 2:
                        $footList[5]['list'][$key] = sprintf("%.2f",(($sum/$sumNum)*$footArr["preList"][$key]*0.85));
                        break;
                    case 4:
                        if(!empty($this->with_foot["sumNum"][0])){ //含有四有
                            $footList[5]['list'][$key] = sprintf("%.2f",(($sum/$sumNum)*$footArr["preList"][$key]*0.9));
                        }
                        break;
                }
            }
        }
        $sum = 0;
        foreach ($footList as $list){
            $html.="<tr>";
            $html.="<th>".$list["code"]."</th>";
            $html.="<th>".$list["name"]."</th>";
            if($list["code"]!=="F"){
                $sum = 0;
            }
            foreach($list["list"] as $item){
                $html.="<th>".$item."</th>";
                if($list["code"]!=="F"){
                    $sum+=floatval($item);
                }
            }
            if(strstr($item,"%")){
                $sum.="%";
            }
            if($list["code"]=="F"){
                $sum =round($sum*$list["pre"],2);
            }
            $html.="<th>".$sum."</th>";
            $html.="</tr>";
        }
        $num = count($footList[0]['list'])+2;
        if($this->status_type==3&&$bool){
            switch ($this->review_type){
                case 2://技術員的專屬表格
                    $html.=$this->reviewTwoHtml($num,$sum);
                    break;
                case 4:
                    if(!empty($this->with_foot["sumNum"][0])) { //含有四有
                        $html.=$this->reviewThreeHtml($num,$sum);
                    }
                    break;
            }
        }
        if(!empty($str)){
            if($this->status_type != 3){
                $reviewLevel = '';
            }else{
                if($this->ranking_bool){
                    $reviewLevel = ':RANKINGLEAVE';
                }else{
                    $reviewLevel =self::getReviewLevelToSum($sum);
                }
            }
            $html.="<tr><th class='text-right' colspan='$num'>:REVIEWGRADE</th><th>$reviewLevel</th></tr>";
        }

        return $html;
    }

    private function reviewTwoHtml($num,&$sum){
        $change_num = 15-($this->change_num*0.5);
        $change_num = $change_num<0?0:$change_num;
        $html="<tr><td colspan='".($num+1)."'>".Yii::t("contract","Attendance score")." (15%)</td></tr>";
        $html.="<tr><th>G</th><th>".Yii::t("contract","sick leave and personal leave")."</th><th colspan='".($num-2)."'></th>";
        $html.="<th>".$this->change_num."</th>";
        $html.="</tr>";
        $html.="<tr><th>H</th><th>".Yii::t("contract","Attendance score")."</th><th colspan='".($num-2)."'></th>";
        $html.="<th>$change_num</th>";
        $html.="</tr>";
        $html.="<tr><td colspan='".($num+1)."'>".Yii::t("contract","total score")." (100%)</td></tr>";
        $html.="<tr><th>J</th><th>".Yii::t("contract","Percentage score")."(".Yii::t("contract","Out of 100").") (F + H)</th><th colspan='".($num-2)."'>&nbsp;</th>";
        $sum+=$change_num;
        $html.="<th>$sum</th>";
        $html.="</tr>";

        return $html;
    }

    private function reviewThreeHtml($num,&$otherSum){
        $footArr = $this->with_foot;
        $footList = array(
            array("code"=>"G","name"=>Yii::t("contract","Project total score"),"list"=>array()),
            array("code"=>"H","name"=>Yii::t("contract","assessed total score"),"list"=>array()),
            array("code"=>"J","name"=>Yii::t("contract","Percentage score")."（H/G*100）","list"=>array()),
            array("code"=>"K","name"=>Yii::t("contract","Scoring ratio"),"list"=>array()),
            array("code"=>"L","name"=>Yii::t("contract","employee total score")."（J*K）","list"=>array()),
            array("code"=>"M","name"=>"“四用”总得分（L*10%）","list"=>array()),
        );
        foreach ($footArr["sumList"] as $key => $sum){
            $sumNum = $footArr["sumNum"][$key]*10;
            $footList[0]['list'][$key] = $sumNum;
            $footList[1]['list'][$key] = $sum;
            $footList[2]['list'][$key] = sprintf("%.2f",($sum/$sumNum)*100);
            $footList[3]['list'][$key] = $footArr["preList"][$key]."%";
            $footList[4]['list'][$key] = sprintf("%.2f",($sum/$sumNum)*$footArr["preList"][$key]);
            $footList[5]['list'][$key] = sprintf("%.2f",(($sum/$sumNum)*$footArr["preList"][$key]*0.1));
        }
        $html="<tr><td colspan='".($num+1)."'>“四用”之得分 (10%)</td></tr>";
        foreach ($footList as $list){
            $html.="<tr>";
            $html.="<th>".$list["code"]."</th>";
            $html.="<th>".$list["name"]."</th>";
            $sum = 0;
            foreach($list["list"] as $item){
                $html.="<th>".$item."</th>";
                $sum+=floatval($item);
            }
            if(strstr($item,"%")){
                $sum.="%";
            }
            $html.="<th>".$sum."</th>";
            $html.="</tr>";
        }
        $otherSum +=$sum;
        $html.="<tr><th colspan='$num' class='text-right'>".Yii::t("contract","review sum")."</th><th>$otherSum</th></tr>";

        return $html;
    }

    public static function getReviewLevelToSum($sum){
        if(!is_numeric($sum)){
            return '';
        }elseif ($sum<50){
            return 'V';
        }elseif ($sum<=59){
            return 'IV';
        }elseif ($sum<=69){
            return 'III';
        }elseif ($sum<=79){
            return 'II';
        }elseif ($sum<=100){
            return 'I';
        }else{
            return $sum;
        }
    }

    public function getReviewLeave(){
        if($this->ranking_bool){
            $str = "经高低差异化(拉curve)后评级";
            $reviewLevel = "待定";
            $rankingArr = array(
                array("maxNum"=>round($this->ranking_sum*0.2),"list"=>array(),"leave"=>"I","class"=>"success"),
                array("maxNum"=>round($this->ranking_sum*0.2),"list"=>array(),"leave"=>"II","class"=>"info"),
                array("maxNum"=>round($this->ranking_sum*0.3),"list"=>array(),"leave"=>"III","class"=>"warning"),
                array("maxNum"=>round($this->ranking_sum*0.2),"list"=>array(),"leave"=>"IV","class"=>"active"),
                array("maxNum"=>round($this->ranking_sum*0.1),"list"=>array(),"leave"=>"V","class"=>"danger"),
            );

            $reviewRows = Yii::app()->db->createCommand()->select("b.employee_id,b.review_sum,b.status_type as review_status_type,c.*,e.name as dept_name")->from("hr_review b")
                ->leftJoin("hr_employee c","c.id = b.employee_id")
                ->leftJoin("hr_dept e","c.position = e.id")
                ->where("b.id in ({$this->ranking_review})")
                ->order("b.review_sum desc")->queryAll();
            if($reviewRows){
                if($this->ranking_sum==count($reviewRows)){
                    foreach ($reviewRows as $row){
                        if($row['review_status_type']!=3){
                            $reviewLevel = "待定";
                            break;
                        }
                        $arr = $this->resetRanking($rankingArr,$row);
                        if($this->employee_id == $row["employee_id"]){
                            $reviewLevel = $arr["ranking"];
                        }
                    }
                }
            }
        }else{
            $str = "评分后级别";
            $reviewLevel =self::getReviewLevelToSum($this->review_sum);
        }
        return array(
            'str'=>$str,
            'leave'=>$reviewLevel,
        );
    }

    protected function getCountTable($html){
        //$this->table_foot["sumNum"] = $this->table_foot["sumNum"]*10;
        $sum = 3+count($this->table_foot["sumList"]);
        $html.="</thead><tbody>";
        $html.="<tr><td colspan='$sum'>";
        switch ($this->review_type){
            case 2:
                $html.=Yii::t("contract","Quarterly assessment score")." (85%)";
                break;
            case 4:
                if(!empty($this->with_foot['sumNum'][0])){
                    $html.=Yii::t("contract","Evaluate project score")." (90%)";
                }else{
                    $html.=Yii::t("contract","Evaluate project score")." (100%)";
                }
                break;
            default:
                $html.=Yii::t("contract","Quarterly assessment score")." (100%)";
        }
        $html.="</td></tr>";
        $html.="</tbody><tfoot>";
        $html.=$this->returnTableFoot($this->table_foot,$this->pro_str,true);
        $html.="</tfoot></table></div>";
        //評分級別規則
        $html.="<div class='col-sm-3 col-sm-offset-1'><table class='table table-bordered table-striped'>";
        $html.="<thead><tr><th class='text-center'>评分级别标准</th><th class='text-center'>排 名</th><th class='text-center'>评 级</th></tr></thead>";
        $html.="<tbody>";
        $html.="<tr><th class='text-center'>80 - 100</th><th class='text-center'>Top 20%</th><th class='text-center'>I</th></tr>";
        $html.="<tr><th class='text-center'>70 - 79</th><th class='text-center'>21 - 40%</th><th class='text-center'>II</th></tr>";
        $html.="<tr><th class='text-center'>60 - 69</th><th class='text-center'>41 - 70%</th><th class='text-center'>III</th></tr>";
        $html.="<tr><th class='text-center'>50 - 59</th><th class='text-center'>71 - 90%</th><th class='text-center'>IV</th></tr>";
        $html.="<tr><th class='text-center'>50分以下</th><th class='text-center'>Bottom 10%</th><th class='text-center'>V</th></tr>";
        $html.="<tr><th class='text-center' colspan='3'>不适用于差异性评分</th></tr>";
        $html.="</tbody></table></div>";
        $html.="</div>";
        return $html;
    }

    //刪除驗證
    public function deleteValidate(){
        return false;
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
        $connection->createCommand()->update('hr_review', array(
            'employee_remark'=>$this->employee_remark,
            'luu'=>$uid,
        ), 'id=:id', array(':id'=>$this->id));

		return true;
	}

    //判斷員工是否參與差異性排名
    public static function reviewBool($employee_id,$year,$year_type){
        $arr = array();
        $row = Yii::app()->db->createCommand()->select("a.city,a.department,a.position")
            ->from("hr_employee a")
            ->leftJoin("hr_dept b","a.position = b.id")
            ->where("a.id=:id and b.review_status = 1 and a.staff_leader = 'Nil'",array(":id"=>$employee_id))->queryRow();
        if($row){
            $ranking_review = Yii::app()->db->createCommand()->select("b.id")->from("hr_review b")
                ->leftJoin("hr_employee c","c.id = b.employee_id")
                ->leftJoin("hr_dept e","c.position = e.id")
                ->where("b.status_type!=4 and c.staff_leader = 'Nil' and e.review_status = 1 and c.city=:city and c.department=:department and b.year=:year and b.year_type=:year_type",
                    array(":city"=>$row["city"],":department"=>$row["department"],":year"=>$year,":year_type"=>$year_type)
                )->order("b.review_sum desc")->queryAll();//組別內超過5個人並且需要差異性排名
            $ranking_sum = $ranking_review?count($ranking_review):0;
            if($ranking_sum>=5){
                $arr["ranking_bool"]=1;
                $arr["ranking_sum"]=$ranking_sum;
                $arr["ranking_review"]=implode(",",array_column($ranking_review,"id"));
                Yii::app()->db->createCommand()->update('hr_review', $arr, "id in ({$arr['ranking_review']})");
            }
        }
        return $arr;
    }

    public static function setOldReviewRanking(){
        $year="";
        $insertSql="ALTER TABLE hr_review ADD COLUMN ranking_review varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '參與差異性的考核id' AFTER change_num;";
        self::searchMysqlColumns("hr_review","ranking_review",$insertSql);
        $insertSql="ALTER TABLE hr_review ADD COLUMN ranking_bool int(11) NOT NULL DEFAULT 0 COMMENT '是否參與差異性排名 0：不參與' AFTER change_num;";
        self::searchMysqlColumns("hr_review","ranking_bool",$insertSql);
        $insertSql="ALTER TABLE hr_review ADD COLUMN ranking_sum int(11) NOT NULL DEFAULT 0 COMMENT '差異性參與總人數' AFTER change_num;";
        self::searchMysqlColumns("hr_review","ranking_sum",$insertSql);
        $year_type="";
        $rows = Yii::app()->db->createCommand()->select("employee_id,year,year_type")
            ->from("hr_review")->where("status_type in (1,2,3)")
            ->order("year asc,year_type asc")->queryAll();
        if($rows){
            foreach ($rows as $row){
                if($year!=$row["year"]&&$year_type!=$row["year_type"]){
                    $year = $row["year"];
                    $year_type = $row["year_type"];
                    echo "year:{$year}   ||   year_type:{$year_type}<br/>";
                }
                self::reviewBool($row["employee_id"],$row["year"],$row["year_type"]);
            }
        }
    }

    public static function searchMysqlColumns($table_name,$column,$insertSql){
        $sql="SELECT count(*) FROM information_schema.COLUMNS WHERE table_name = '{$table_name}'";
        $sql.=" and column_name = '{$column}'";
        $count = Yii::app()->db->createCommand($sql)->queryScalar();
        if(empty($count)){
            Yii::app()->db->createCommand($insertSql)->execute();
        }
    }

    public static function searchMysqlTables($table_name,$insertSql){
        $sql="SELECT count(*) FROM information_schema.tables WHERE table_name = '{$table_name}'";
        $count = Yii::app()->db->createCommand($sql)->queryScalar();
        if(empty($count)){
            Yii::app()->db->createCommand($insertSql)->execute();
        }
    }
}
