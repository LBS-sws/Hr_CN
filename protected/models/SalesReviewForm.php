<?php


class SalesReviewForm extends CFormModel
{
	public $id;//组名id
	public $city;
	public $year;
	public $year_type;
    public $year_list;
    public $staff_list=array();
    public $form_list;
	protected $group_list;
	protected $group_staff=array();//员工分组
	protected $show_staff=array();//需要显示的员工

	protected $auto_staff=array();
	protected $auto_model=array();

	public function attributeLabels()
	{
        return array(
            'id'=>Yii::t('contract','ID'),
            'year'=>Yii::t('contract','Time'),
        );
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id, city','safe'),
		);
	}

	public function retrieveData($index,$year,$year_type,$city='') {
        $suffix = Yii::app()->params['envSuffix'];
        $this->id = $index;
        $this->form_list=array();
	    $this->year = !is_numeric($year)?2020:$year;
	    $this->year_type = (!is_numeric($year_type)||$this->year == 2020)?1:$year_type;
        $this->group_list = SalesGroupForm::getGroupListToId($index);
        $this->resetYearList();//重置年份區間 设置year_list
        $this->foreachGroupStaff($index);//獲取組內的員工 设置staff_list、group_list

        $this->getSalesDataForStaffList();//获取销售系统的数据(不分组) 设置form_list
		return true;
	}

    //获取什么是签单的查询字符串
    public static function getDealString($field) {
        $suffix = Yii::app()->params['envSuffix'];
        $rtn = '';
        $sql = "select id from sales{$suffix}.sal_visit_obj where rpt_type='DEAL'";
        $rows = Yii::app()->db->createCommand($sql)->queryAll();
        foreach ($rows as $row) {
            $rtn .= ($rtn=='' ? '' : ' or ').$field." like '%\"".$row['id']."\"%'";
        }
        return ($rtn=='' ? "$field='0'" : $rtn);
    }

	private function getSalesDataForStaffList(){
        $suffix = Yii::app()->params['envSuffix'];
        $staffKeyList = array_keys($this->staff_list);
        $staffSql = " and b.username = ''";
        if(!empty($this->staff_list)){
            $staffSql = " and b.username in ('".implode("','",$staffKeyList)."') ";
        }
        $minYear = $this->year_list[0];
        $maxYear = end($this->year_list);
        $svcList = array("svc_A7","svc_B6","svc_C7","svc_D6","svc_E7","svc_F4","svc_G3");//查詢該屬性的所有金額
        $notList = array("svc_F4","svc_G3");//只計算次數，不計算金額
        $svcSql = implode("','",$svcList);
        $dealSQL = self::getDealString("b.visit_obj");//签单的sql
        $visitObjSql = "  and ({$dealSQL})";
        $rows = Yii::app()->db->createCommand()->select("a.field_value,a.field_id,b.visit_dt,b.username")->from("sales$suffix.sal_visit_info a")
            ->leftJoin("sales$suffix.sal_visit b","b.id=a.visit_id")
            ->where("b.id is not null and a.field_id in('$svcSql') and (a.field_value+0)>0 and date_format(b.visit_dt,'%Y/%m')>='$minYear' and date_format(b.visit_dt,'%Y/%m')<='$maxYear' $staffSql $visitObjSql")->queryAll();
        if ($rows) {
            foreach ($rows as $row){
                $year = date("Y/m",strtotime($row["visit_dt"]));
                $username = $row["username"];
                if(!in_array($username,$staffKeyList)){//員工不存在，不計算
                    continue;
                }
                if(!key_exists($year,$this->form_list)){
                    $this->form_list[$year] = array('item'=>array());
                }
                if(!key_exists($username,$this->form_list[$year]['item'])){
                    $this->form_list[$year]['item'][$username] = array(
                        'sales_sum'=>0,//开单额
                        'sales_count'=>0//开单次数
                    );
                }
                $this->form_list[$year]['item'][$username]['sales_count']++;
                if(!in_array($row["field_id"],$notList)){
                    $this->form_list[$year]['item'][$username]['sales_sum']+=floatval($row["field_value"]);
                }
            }
        }
    }

	public function getTableHeader($year){
	    $html = "";
        $html.="<legend>".$year."</legend>";
        $html.="<div class='form-group'><div class='col-sm-12'><table class='table table-bordered table-striped showTable'>";
        $html.="<thead><tr>";
        $html.="<th>".Yii::t("contract","Employee Code")."</th>";
        $html.="<th>".Yii::t("contract","Employee Name")."</th>";
        $html.="<th>".Yii::t("contract","bill sum")."</th>";
        $html.="<th>".Yii::t("contract","average num")."</th>";
        $html.="<th>".Yii::t("contract","deviation")."</th>";
        $html.="<th class='text-danger'>".Yii::t("contract","review score")."</th>";
        $html.="<th>".Yii::t("contract","bill count")."</th>";
        $html.="<th>".Yii::t("contract","average num")."</th>";
        $html.="<th>".Yii::t("contract","deviation")."</th>";
        $html.="<th class='text-danger'>".Yii::t("contract","review score")."</th>";
        $html.="<th class='text-danger'>".Yii::t("contract","review number")."</th>";
        $html.="</tr></thead>";
        //$html.="</table></div></div>";

        return $html;
    }

    public function getInstructionsList(){
	    $html="";
        $arr = array(
            array('deviation'=>Yii::t("fete","30% Below"),
                'instruction'=>Yii::t("fete","Performance is not worth any score (dereliction of duty)"),
                'score'=>"0"
            ),
            array('deviation'=>"31%-45%",
                'instruction'=>Yii::t("fete","Extremely poor performance (extremely disappointing performance)"),
                'score'=>"1"
            ),
            array('deviation'=>"46%-60%",
                'instruction'=>Yii::t("fete","Poor performance (disappointing performance)"),
                'score'=>"2"
            ),
            array('deviation'=>"61%-75%",
                'instruction'=>Yii::t("fete","Poor performance (poor performance, far from the company's standards)"),
                'score'=>"3"
            ),
            array('deviation'=>"76%-90%",
                'instruction'=>Yii::t("fete","Poor performance (poor performance, unsatisfactory overall performance)"),
                'score'=>"4"
            ),
            array('deviation'=>"91%-99%",
                'instruction'=>Yii::t("fete","Fair performance (effort, but not up to company standards)"),
                'score'=>"5"
            ),
            array('deviation'=>"100%-110%",
                'instruction'=>Yii::t("fete","Standard performance (performance up to company standards only)"),
                'score'=>"6"
            ),
            array('deviation'=>"111%-130%",
                'instruction'=>Yii::t("fete","Stable performance (excellent performance, overall performance is satisfactory)"),
                'score'=>"7"
            ),
            array('deviation'=>"131%-150%",
                'instruction'=>Yii::t("fete","Perform well (perform competently and meet the company's expectations)"),
                'score'=>"8"
            ),
            array('deviation'=>"151%-200%",
                'instruction'=>Yii::t("fete","Perform well (perform well, exceed the company's expectations)"),
                'score'=>"9"
            ),
            array('deviation'=>Yii::t("fete","Over 200%"),
                'instruction'=>Yii::t("fete","Exceptional performance (performance that is beyond the company's expectations)"),
                'score'=>"10"
            ),
        );
        foreach ($arr as $item){
            $html.="<tr>";
            $html.="<td>".$item["deviation"]."</td>";
            $html.="<td>".$item["instruction"]."</td>";
            $html.="<td>".$item["score"]."</td>";
            $html.="</tr>";
        }
        return $html;
    }

    private function getGroupForBody($groupRow,$year,&$showStaff){
        $html = "";
        $count = 0;//本月有多少员工（不包含跨区)
        $allSum = 0;//本月总金额
        $allNum = 0;//本月总单数
        foreach ($groupRow as $staffRow){//計算員工总分及总单数
            if($year>=$staffRow["start_time"]&&$year<=$staffRow["end_time"]){
                $count++;
                $sum = isset($this->form_list[$year]["item"][$staffRow["user_id"]])?$this->form_list[$year]["item"][$staffRow["user_id"]]["sales_sum"]:0;
                $num = isset($this->form_list[$year]["item"][$staffRow["user_id"]])?$this->form_list[$year]["item"][$staffRow["user_id"]]["sales_count"]:0;
                $allNum+=$num;
                $allSum+=$sum;
            }
        }
        $allSum = empty($allSum)?0:round($allSum/$count,1);
        $allNum = empty($allNum)?0:round($allNum/$count,1);
        foreach ($groupRow as $staffRow){//生成表格
            if($year>=$staffRow["start_time"]&&$year<=$staffRow["end_time"]){
                $sum = isset($this->form_list[$year]["item"][$staffRow["user_id"]])?$this->form_list[$year]["item"][$staffRow["user_id"]]["sales_sum"]:0;
                $num = isset($this->form_list[$year]["item"][$staffRow["user_id"]])?$this->form_list[$year]["item"][$staffRow["user_id"]]["sales_count"]:0;

                $trHtml="<tr data-code='{$staffRow['code']}'>";
                $trHtml.="<td>".$staffRow["code"]."</td>";
                $trHtml.="<td>".$staffRow["name"]."</td>";
                $trHtml.="<td>".$sum."</td>";//开单额
                $trHtml.="<td>$allSum</td>";//开单额平均数
                $rankingOne = empty($allSum)?0:($sum/$allSum)*100;
                $rankingOne = round($rankingOne);
                $trHtml.="<td>".$rankingOne."%</td>";//开单额所占比
                $rankingOne = $this->getRankingToNum($rankingOne);
                $trHtml.="<td class='text-danger'><b>".$rankingOne."</b></td>";//开单额评分
                //$trHtml.="<td>&nbsp;</td>";
                $trHtml.="<td>".$num."</td>";//开单数量
                $trHtml.="<td>$allNum</td>";//开单数量平均数
                $rankingTwo = empty($allNum)?0:($num/$allNum)*100;
                $rankingTwo = round($rankingTwo);
                $trHtml.="<td>".$rankingTwo."%</td>";//开单数量所占比
                $rankingTwo = $this->getRankingToNum($rankingTwo);
                $trHtml.="<td class='text-danger'><b>".$rankingTwo."</b></td>";//开单数量评分
                $rankingSum = ($rankingTwo+$rankingOne)/2;
                $trHtml.="<td class='text-danger'><b>$rankingSum</b></td>";//当月总评分
                $trHtml.="</tr>";
                $bool = key_exists($staffRow["user_id"],$showStaff);
                if($staffRow["group_id"] == $this->id||$bool){
                    unset($showStaff[$staffRow["user_id"]]);//不需要重复显示
                    $html.=$trHtml;
                    $this->staff_list[$staffRow["user_id"]]["rankingCount"]++;
                    $this->staff_list[$staffRow["user_id"]]["ranking"]+=$rankingSum;
                }
            }
        }
        return $html;
    }

	public function getTableBody($year){
        $groupList = $this->group_staff;
	    $html = "<tbody>";
        $showStaff=$this->show_staff;
        if(key_exists($this->id,$groupList)){
            $html.= $this->getGroupForBody($groupList[$this->id],$year,$showStaff);
            unset($groupList[$this->id]);
        }
        if(!empty($groupList)){
            foreach ($groupList as $groupRow){
                $autoHtml = $this->getGroupForBody($groupRow,$year,$showStaff);
                if(!empty($autoHtml)){
                    $html.="</tbody></table></div></div>";
                    $html.=$this->getTableHeader("跨区");
                    $html.="<tbody>";
                    $html.=$autoHtml;
                }
            }
        }
        return $html."</tbody>";
    }

    public function getRankingToNum($num){
        $num = floatval($num);
        if($num>200){
            return 10;
        }elseif($num>150){
            return 9;
        }elseif($num>130){
            return 8;
        }elseif($num>110){
            return 7;
        }elseif($num>=100){
            return 6;
        }elseif($num>90){
            return 5;
        }elseif($num>75){
            return 4;
        }elseif($num>60){
            return 3;
        }elseif($num>45){
            return 2;
        }elseif($num>30){
            return 1;
        }else{
            return 0;
        }
    }

	public function getTabList(){
        $tabs = array();
        foreach ($this->year_list as $year){
            $content = $this->getTableHeader($year);
            $content.=$this->getTableBody($year);
            $content.="</table></div></div>";
            //查询跨地区员工
            //$this->getAutoStaff();
            $tabs[] = array(
                'label'=>$year,
                'content'=>"<p>&nbsp;</p>".$content,
                'active'=>false,
            );
        }
        $tabs[] = array(
            'label'=>Yii::t("contract","review number"),
            'content'=>"<p>&nbsp;</p>".$this->getAllSumTable(),
            'active'=>true,
        );
        return $tabs;
    }

    public function getAllSumTable(){
        $html = "";
        $html.="<div class='form-group'><div class='col-sm-5 col-sm-offset-2'><table class='table table-bordered table-striped'>";
        $html.="<thead><tr>";
        $html.="<th width='1%'>".TbHtml::checkBox("all",true,array("id"=>"allCheck"))."</th>";
        $html.="<th>".Yii::t("contract","Employee Code")."</th>";
        $html.="<th>".Yii::t("contract","Employee Name")."</th>";
        $html.="<th>".Yii::t("contract","review number")."</th>";
        $html.="</tr></thead><tbody>";
        foreach ($this->staff_list as $staff){
            if(key_exists($staff["user_id"],$this->show_staff)){
                $html.="<tr>";
                $html.="<th>".TbHtml::checkBox("only[]",true,array("class"=>"onlyCheck","data-code"=>$staff["code"]))."</th>";
                $html.="<td>".$staff["code"]."</td>";
                $html.="<td>".$staff["name"]."</td>";
                $ranking = empty($staff["rankingCount"])?0:$staff["ranking"]/$staff["rankingCount"];
                $html.="<td>".sprintf("%.2f",$ranking)."</td>";
                $html.="</tr>";
            }
        }
        $html.="</tbody></table></div></div>";

	    return $html;
    }

    protected function resetYearList(){
	    $year = $this->year;
	    if($this->year_type == 1){
            if(Yii::app()->params['retire']||!isset(Yii::app()->params['retire'])){//非台灣版
                if($year<2020){
                    $this->year_list = array("$year"."/04","$year"."/05","$year"."/06","$year"."/07","$year"."/08","$year"."/09");
                }elseif ($year==2020){
                    $this->year_list = array("$year"."/04","$year"."/05","$year"."/06","$year"."/07","$year"."/08","$year"."/09","$year"."/10","$year"."/11","$year"."/12");
                }else{
                    $this->year_list = array("$year"."/01","$year"."/02","$year"."/03","$year"."/04","$year"."/05","$year"."/06");
                }
            }else{
                $this->year_list = array("$year"."/01","$year"."/02","$year"."/03","$year"."/04","$year"."/05","$year"."/06");
            }
        }else{
            if(Yii::app()->params['retire']||!isset(Yii::app()->params['retire'])){//非台灣版
                if($year<2020){
                    $this->year_list = array("$year"."/10","$year"."/11","$year"."/12");
                    $year++;
                    $this->year_list = array_merge($this->year_list,array("$year"."/01","$year"."/02","$year"."/03"));
                }else{
                    $this->year_list = array("$year"."/07","$year"."/08","$year"."/09","$year"."/10","$year"."/11","$year"."/12");
                }
            }else{
                $this->year_list = array("$year"."/07","$year"."/08","$year"."/09","$year"."/10","$year"."/11","$year"."/12");
            }
        }
    }

    /*
     * @$index 组名id
     * @$num 最多循环1次
     */
    protected function foreachGroupStaff($index,$num=0){
        $num++;
        $minYear = $this->year_list[0];
        $maxYear = end($this->year_list);
        $rows = Yii::app()->db->createCommand()->select("a.group_id,b.code,b.name,b.id,c.user_id,a.start_time,a.end_time")->from("hr_sales_staff a")
            ->leftJoin("hr_employee b","a.employee_id = b.id")
            ->leftJoin("hr_binding c","c.employee_id = b.id")
            ->where('a.group_id=:group_id',array(':group_id'=>$index))->queryAll();
        $key = 0;
        foreach ($rows as $row){
            if(!key_exists($index,$this->group_staff)){
                $this->group_staff[$index] = array();
            }
            $startTime = $row['start_time'];
            $endTime = $row['end_time'];
            $startTime = empty($startTime) ? "0000/00" : date("Y/m", strtotime($startTime));
            $endTime = empty($endTime) ? "9999/99" : date("Y/m", strtotime($endTime));
            $key++;
            //生成user_id (解决有些员工未绑定账户)
            $row["user_id"] = empty($row["user_id"])?"null".$key:$row["user_id"];
            $staffArr=array(
                "group_id"=>$row["group_id"],//分组id
                "user_id"=>$row["user_id"],//账号id
                "id"=>$row["id"],//员工id
                "code"=>$row["code"],
                "name"=>$row["name"],
                "start_time"=>$startTime,
                "end_time"=>$endTime,
                "ranking"=>0,//总分
                "rankingCount"=>0//计算总分用的
            );
            if(!key_exists($row["user_id"],$this->staff_list)){ //防止循环覆盖问题
                $this->staff_list[$row["user_id"]]=$staffArr;
            }
            $this->group_staff[$index][]=$staffArr;
            if($index==$this->id&&!($startTime>$maxYear||$endTime<$minYear)){ //当前查询分组(需要显示的员工分数)
                $this->show_staff[$row["user_id"]] = $row["id"];
            }
            if($num<=1&&$startTime>$minYear&&$startTime<$maxYear){ //判断是否跨区
                $autoRow = Yii::app()->db->createCommand()->select("group_id")->from("hr_sales_staff")
                    ->where('group_id!=:group_id and time_off=1 and end_time<=:end_time and employee_id=:employee_id',
                        array(':group_id'=>$index,':end_time'=>$row['start_time'],':employee_id'=>$row['id']))->queryRow();
                if($autoRow&&!key_exists($autoRow["group_id"],$this->group_staff)){
                    $this->foreachGroupStaff($autoRow["group_id"],$num);
                }
            }
        }
    }

    public function getGroupListStr($str){
        if(key_exists($str,$this->group_list)){
            return $this->group_list[$str];
        }else{
            return $str;
        }
    }

    //由於平均分計算異常，統一修改所有的銷售考核單
    public function errorCompany($year){
        set_time_limit(0);
        if(date("Y-m-d")>"2022-01-12"){
            echo "long time";
            return false;
        }
        $reviewRows = Yii::app()->db->createCommand()
            ->select("a.id,a.status_type,a.change_num,a.employee_id,a.year,a.year_type,b.city,b.name")
            ->from("hr_review a")
            ->leftJoin("hr_employee b","a.employee_id=b.id")
            ->where("a.review_type=3 and a.year='{$year}'")->queryAll();
        echo "start<br/>";
        if($reviewRows){
            foreach ($reviewRows as $reviewRow){
                echo "id:{$reviewRow['id']},staff:{$reviewRow['name']}({$reviewRow['employee_id']}) - ({$reviewRow['year']}/{$reviewRow['year_type']}) - ";
                $row = Yii::app()->db->createCommand()->select("a.group_id")->from("hr_sales_staff a")
                    ->leftJoin("hr_sales_group b","a.group_id=b.id")
                    ->where("a.employee_id=:id and b.city=:city",
                        array(":id"=>$reviewRow['employee_id'],":city"=>$reviewRow['city'])
                    )->queryRow();
                if($row){
                    $change_num=0;
                    $model = new SalesReviewForm();
                    $model->retrieveData($row["group_id"],$reviewRow["year"],$reviewRow["year_type"]);
                    $model->getTabList();
                    foreach ($model->staff_list as $staff){
                        if($staff["id"] == $reviewRow['employee_id']){
                            echo " rankingCount:{$staff["rankingCount"]},ranking:{$staff["ranking"]} ";
                            $ranking = empty($staff["rankingCount"])?0:$staff["ranking"]/$staff["rankingCount"];
                            $change_num = sprintf("%.2f",$ranking);
                        }
                    }
                    unset($model);
                    //修改總分
                    $detailRows = Yii::app()->db->createCommand()->select("*")->from("hr_review_h")
                        ->where('review_id=:review_id',
                            array(':review_id'=>$reviewRow["id"]))->queryAll();
                    $review_sum=0;
                    if($detailRows){
                        foreach ($detailRows as $detail){
                            $detail["review_sum"] = empty($detail["review_sum"])?0:intval($detail["review_sum"]);
                            //$review_sum+=$row["review_sum"];
                            $review_sum+=$this->sumReview($detail);
                        }
                    }
                    $review_sum+=$change_num*7;
                    Yii::app()->db->createCommand()->update('hr_review', array(
                        'change_num'=>$change_num,
                        'review_sum'=>$reviewRow["status_type"]==3?$review_sum:null,
                    ), 'id=:id', array(':id'=>$reviewRow['id']));
                    echo " success ({$reviewRow['change_num']}->{$change_num})";
                }else{
                    echo "error group_id";
                }
                echo "<br/>";
            }
        }
        echo "end";
    }

    private function sumReview($arr){
        $sum = intval($arr["review_sum"]);
        $pro = intval($arr["handle_per"]);
        $num = intval($arr["tem_sum"])*10;
        return sprintf("%.2f",($sum/$num)*$pro);
    }
}
