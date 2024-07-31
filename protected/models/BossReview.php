<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 * Date: 2020/6/15
 * Time: 13:42
 */
class BossReview
{
    public $ready=true;//禁止用戶修改
    public $className="";//表單的name前綴
    public $audit_year=0;//考核年限
    public $search_month=0;//查詢的月份（0:所有月份）
    public $city='';//城市
    public $ratio_a=50;//占比
    public $ratio_b=35;//占比
    public $ratio_c=15;//占比
    public $employee_id='';//員工
    public $username='';//賬號
    public $listX=array();
    public $listY=array();
    public $json_text=array();
    public $validate_text=array();//需要驗證的json
    public $cofModel;
    public $scoreSum=0;// 總分數

    protected $detailList=array();//每项的详情（报表专用）
    protected $model;
    protected $countPrice;//年生意額

    protected $searchBool = false;


    public function __construct(&$model='',$searchBool=false)
    {
        if(!empty($model)){
            $this->model = $model;
            $this->username = $model->lcu;
            $this->employee_id = $model->employee_id;
            $this->json_text = $model->json_text;
            $this->audit_year = $model->audit_year;
            $this->city = $model->city;
            $this->ratio_a = $model->ratio_a;
            $this->ratio_b = $model->ratio_b;
            $this->ratio_c = $model->ratio_c;
            $this->ready = $model->getInputBool();
            $this->className = get_class($model);
        }
        $this->searchBool = $searchBool;
        $this->cofModel = new BossReviewCof();
        $this->cofModel->city = $this->city;
        $this->countPrice = $this->value($this->city,$this->audit_year-1,"00002");
        $this->setListX();
        $this->setListY();
    }

    protected function setListX(){
        //array('value'=>'','name'=>'')
        $this->listX = array();
    }

    public function getDetailList(){
        return $this->detailList;
    }

    public function getListX(){
        return $this->listX;
    }

    protected function setListY(){
        $this->listY = array();
    }

    public function validateJson(&$model,$bool=true){
        foreach ($this->listX as $listX) {
            $valueX = $listX["value"];
            foreach ($this->listY as $key => $listY) {
                $valueY = $listY["value"];
                if($bool&&key_exists("validate",$listY)&&$listY["validate"]){
                    if(!isset($model->json_text[$valueX][$valueY])||!is_numeric($model->json_text[$valueX][$valueY])){
                        $message = Yii::t('contract',$valueX)." - ".Yii::t('contract',' can not be empty');
                        $model->addError('json_text',$message);
                        return false;
                    }
                }

                if(key_exists("function",$listY)){
                    call_user_func(array($this,$listY["function"]),$listX["value"],$listY["value"],$listX);
                }
            }
        }
    }

    //主內容橫向
    public function getTableHtml(){
        $className = get_class($this);
        $width="170px";
        $html="<p>&nbsp;</p><div class='form-group'><div class='col-lg-12'><div class='table-responsive'><table class='table table-bordered table-hover'>";
        $html.="<thead><tr>";
        $html.="<th width='$width'>";
        $html.=Yii::t("contract","matters");
        $html.="<input type='hidden' name='down[{$className}][title][]' value='".Yii::t("contract","matters")."'>";
        $html.="</th>";
        foreach ($this->listY as $key => $listY){
            $downTitle = "<input type='hidden' name='down[{$className}][title][]' value='{$listY["name"]}'>";
            if(key_exists("width",$listY)){
                $html.="<th width='".$listY["width"]."'>".$downTitle.$listY["name"]."</th>";
            }else{
                $html.="<th width='170px'>".$downTitle.$listY["name"]."</th>";
            }
        }
        $html.="</tr></thead><tbody>";
        $title=Yii::t("bossHint","title");

        foreach ($this->listX as $listX){
            $content = Yii::t("bossHint",$listX["value"]);
            $html.="<tr>";
            $html.="<td>";
            $html.="<input type='hidden' name='down[{$className}][{$listX['value']}][]' value='".Yii::t("contract",$listX["value"])."'>";
            $html.="<a class='bossHintTitle' role='button' tabindex='0' data-toggle='popover' data-trigger='focus' title='$title' data-html='true' data-content='$content'><b>".Yii::t("contract",$listX["value"])."</b></a></td>";
            foreach ($this->listY as $key => $listY){
                $downText = "";//文檔下載需要顯示的內容
                if($this->searchBool){
                    $searchText = !isset($this->json_text[$listX["value"]][$listY["value"]])?0:$this->json_text[$listX["value"]][$listY["value"]];
                    if(isset($listY["static_str"])&&$searchText!=="\\"){
                        $searchText.=$listY["static_str"];
                    }elseif(isset($listY["pro_str"])&&isset($listX["pro_str"])&&$listX["pro_str"]==$listY["pro_str"]){
                        $searchText.=$listY["pro_str"];
                    }
                    $html.="<td>";
                    $html.=$searchText;
                    $html.="<input type='hidden' name='down[{$className}][{$listX['value']}][]' value='{$searchText} '>";
                    $html.="</td>";
                    continue;
                }

                $name = $this->className."[json_text][".$listX["value"]."]"."[".$listY["value"]."]";
                $html.="<td class='".$listY["value"]."'>";
                if(key_exists("function",$listY)){
                    $value = call_user_func(array($this,$listY["function"]),$listX["value"],$listY["value"],$listX);
                    if(is_array($value)){
                        if (strpos($value['name'],'<input')===false){
                            $html.="<input type='hidden' name='$name' value='".$value['value']."'/>";
                            $html.="<span>".$value['name']."</span>";
                        }else{
                            $html.=$value['name'];
                        }
                        $downText = $value['value'];
                    }else{
                        $downText = $value;
                        $html.="<input type='hidden' name='$name' value='$value'><span>$value</span>";
                    }
                }elseif(key_exists("text",$listY)){
                    if($this->ready){
                        $html.="<textarea type='text' name='$name' class='form-control' readonly></textarea>";
                    }else{
                        $html.="<textarea type='text' name='$name' class='form-control'></textarea>";
                    }
                }elseif(key_exists("input",$listY)){
                    if(key_exists("ready",$listY)||$this->ready){
                        $html.="<input type='text' name='$name' value='' class='form-control' readonly>";
                    }else{
                        $html.="<input type='text' name='$name' value='' class='form-control'>";
                    }
                }
                if(isset($listY["static_str"])&&$downText!=="\\"){
                    $downText.=$listY["static_str"];
                }elseif(isset($listY["pro_str"])&&isset($listX["pro_str"])&&$listX["pro_str"]==$listY["pro_str"]){
                    $downText.=$listY["pro_str"];
                }
                $html.="<input type='hidden' name='down[{$className}][{$listX['value']}][]' value='{$downText} '>";
                $html.="</td>";
            }
            $html.="</tr>";
        }

        $html.="</tbody></table></div></div></div>";
        return $html;
    }

    //邮件专用
    public function getEveryOrNowNumber($type,$str="every"){
        return "/";
    }

    //主內容橫向
    public function getTableHtmlToEmail(){
        $this->detailList=array();
        $width="170px";
        $html="";
        $html.="<thead><tr>";
        $html.="<th width='$width'>".Yii::t("contract","matters")."</th>";
        $this->detailList["title"]["list"][]=Yii::t("contract","matters");
        $colorInfo = array("one_6","two_4");//需要添加颜色判断的列
        $tdInfo = array("one_4","two_2");//在某列之后添加“累计”及“本月”
        $tdPro = array("one_one","one_two","one_three","one_four","one_five","one_nine","two_three","two_eight","two_five");
        foreach ($this->listY as $key => $listY){
            if(key_exists("emailBool",$listY)&&$listY["emailBool"]){
                if(key_exists("width",$listY)){
                    $html.="<th width='".$listY["width"]."'>".$listY["name"]."</th>";
                }else{
                    $html.="<th width='170px'>".$listY["name"]."</th>";
                }
                $this->detailList["title"]["list"][]=$listY["name"];
            }
            if(in_array($listY["value"],$tdInfo)){
                $html.="<th width='170px'>".Yii::t("contract","every monthly average")."</th>";
                $html.="<th width='170px'>".Yii::t("contract","now monthly average")."</th>";
                $this->detailList["title"]["list"][]=Yii::t("contract","every monthly average");
                $this->detailList["title"]["list"][]=Yii::t("contract","now monthly average");
            }
        }
        $html.="<th width='170px'>{$this->audit_year}".Yii::t("contract"," monthly complete")."</th>";
        $this->detailList["title"]["list"][]=$this->audit_year.Yii::t("contract"," monthly complete");
        $html.="</tr></thead><tbody>";

        for($i=1;$i<=$this->search_month;$i++){
            $this->detailList["title"]["info"][$i]=array('num'=>$i,'text'=>$i.Yii::t("report","Month"),'len'=>0);
        }

        foreach ($this->listX as $rowKey=>$listX){
            $tableTr="<tr>";
            $tableTr.="<td><b>".$listX["name"]."</b></td>";
            $this->detailList[$rowKey]["list"][]=$listX["name"];
            $nowNum = 0;
            $userNum = 0;
            foreach ($this->listY as $key => $listY){
                if(key_exists("emailBool",$listY)){
                    if(key_exists("function",$listY)){
                        call_user_func(array($this,$listY["function"]),$listX["value"],$listY["value"],$listX);
                    }
                    if($listY["emailBool"]){
                        $searchText = !isset($this->json_text[$listX["value"]][$listY["value"]])?0:$this->json_text[$listX["value"]][$listY["value"]];
                        $searchText = empty($searchText)?0:$searchText;
                        if(in_array($listY["value"],array("one_6","two_4"))){
                            $userNum = is_numeric($searchText)?$searchText:0;
                        }
                        if(in_array($listY["value"],array("one_3","two_2"))){
                            $nowNum = is_numeric($searchText)?$searchText:0;
                        }
                        if(isset($listY["static_str"])&&$searchText!=="\\"){
                            $searchText.=$listY["static_str"];
                        }elseif(isset($listY["pro_str"])&&isset($listX["pro_str"])&&$listX["pro_str"]==$listY["pro_str"]){
                            $searchText.=$listY["pro_str"];
                        }
                        if(in_array($listY["value"],$colorInfo)){
                            $tableTr.="<td style='border-color:#000;color::COLORSTR:;'>".$searchText."</td>";
                        }else{
                            $tableTr.="<td>".$searchText."</td>";
                        }
                        $this->detailList[$rowKey]["list"][]=$searchText;
                    }
                }
                if(in_array($listY["value"],$tdInfo)){
                    if(in_array($listX["value"],$tdPro)){
                        $eveyNum = $this->getEveryOrNowNumber($listX["value"],"every");
                        $nowNum = $this->getEveryOrNowNumber($listX["value"],"now");
                        $tableTr.="<td>".$eveyNum."</td>";
                        $tableTr.="<td>".$nowNum."</td>";
                        $this->detailList[$rowKey]["list"][]=$eveyNum;
                        $this->detailList[$rowKey]["list"][]=$nowNum;
                    }else{
                        $eveyNum = "/";
                        $tableTr.="<td>".$eveyNum."</td>";
                        $tableTr.="<td>".$eveyNum."</td>";
                        $this->detailList[$rowKey]["list"][]=$eveyNum;
                        $this->detailList[$rowKey]["list"][]=$eveyNum;
                    }
                }
            }
            if($listX["value"]=="two_one"||(key_exists("pro_str",$listX)&&$listX["pro_str"]=="%")){
                $completeNum="/";
            }else{
                $completeNum = $this->getEveryOrNowNumber($listX["value"],"complete");
                $completeNum.="%";
            }
            $tableTr.="<td>".$completeNum."</td>";
            $this->detailList[$rowKey]["list"][]=$completeNum;
            $this->detailList[$rowKey]["info"]=$this->getDetailInfo($listX);
            $tableTr.="</tr>";
            if(in_array($listX["value"],array("two_nine","two_ten","one_seven"))){
                $nowNum*=-1;
                $userNum*=-1;
            }
            if($nowNum<=$userNum){
                $tableTr = str_replace(":COLORSTR:","blue",$tableTr);
            }else{
                $tableTr = str_replace(":COLORSTR:","red",$tableTr);
            }
            $html.=$tableTr;
        }

        $html.="</tbody>";
        return $html;
    }

    //由于不能直接在A里面添加函数，所以后续需要判断
    protected function getDetailInfo($listX){
        $detailList = array();
        $functionList = array(
            //A部分
            'one_one'=>array("function"=>"valueForDetail","data_field"=>"00002"),//年生意额增长目标
            'one_two'=>array("function"=>"valueForDetail","data_field"=>"00067"),//年利润额增长目标
            'one_three'=>array("function"=>"valueToOpForDetail"),//年新业务生意额目标
            'one_four'=>array("function"=>"valueForDetail","data_field"=>"00003"),//IA服务生意年金额
            'one_five'=>array("function"=>"valueForDetail","data_field"=>"00004"),//IB服务生意年金额
            'one_nine'=>array("function"=>"valueForDetail","data_field"=>"00006"),//新（IA+IB）服务年金额
            'one_six'=>array("function"=>"valueOnToRateForDetail","data_field"=>"00067"),//收款率(%)
            'one_seven'=>array("function"=>"valueStopToRateForDetail","arr"=>array("00017","00002")),//服务单的停单比例(%)
            'one_eight'=>array("function"=>"valueForDetail","data_field"=>"00018"),//技术员每月平均生产力
            //B部分
            'two_one'=>array("function"=>"valueStaffReviewForDetail"),//优化人才评核
            'two_two'=>array("function"=>"valueHdrForDetail"),//月报表分数
            'two_three'=>array("function"=>"valueForDetail","data_field"=>"00042"),//质检拜访量
            'two_eight'=>array("function"=>"valueForDetail","data_field"=>"00069"),//洗地易销售桶数
            'two_four'=>array("function"=>"valueStopToRateForDetail","arr"=>array("00038","00036")),//高效客诉解决效率
            'two_five'=>array("function"=>"valueFeedbackForDetail"),//总经理回馈次数
            //'two_six'=>array("function"=>"valueSalesOneForDetail"),//提交销售5步曲数量培训销售部分(太麻烦了，不想实现)
            'two_nine'=>array("function"=>"valueStopToRateForDetail","arr"=>array("00023","00003")),//IA物料使用率
            'two_ten'=>array("function"=>"valueStopToRateForDetail","arr"=>array("00024","00004")),//IB物料使用率
            'two_service'=>array("function"=>"valueServiceNumForDetail"),//蔚诺租赁服务机器台数
        );
        if(key_exists($listX["value"],$functionList)){
            $func = $functionList[$listX["value"]]["function"];
            $detailList = $this->$func($listX,$functionList[$listX["value"]]);
        }

        return $detailList;
    }

    //主內容豎向（不使用）
    public function getTableHtmlOld(){
        $html="<p>&nbsp;</p><div class='form-group'><div class='col-lg-12'><table class='table table-bordered'>";
        $html.="<thead><tr>";
        $html.="<th>".Yii::t("contract","matters")."</th>";
        foreach ($this->listX as $key => $listX){
            if($key%2 == 0){
                $html.="<th class='info'>".$listX["name"]."</th>";
            }else{
                $html.="<th>".$listX["name"]."</th>";
            }
        }
        $html.="</tr></thead><tbody>";

        foreach ($this->listY as $listY){
            $html.="<tr>";
            $html.="<td><b>".$listY["name"]."</b></td>";
            foreach ($this->listX as $key => $listX){
                $name = $this->className."[json_text][".$listX["value"]."]"."[".$listY["value"]."]";
                if($key%2 == 0){
                    $html.="<td class='info'>";
                }else{
                    $html.="<td>";
                }
                if(key_exists("function",$listY)){
                    $value = call_user_func(array($this,$listY["function"]),$listX["value"],$listY["value"],$listX);
                    if(is_array($value)){
                        if (strpos($value['name'],'<input')===false){
                            $html.="<input type='hidden' name='$name' value='".$value['value']."'>";
                        }
                        $html.="<span>".$value['name']."</span>";
                    }else{
                        $html.="<input type='hidden' name='$name' value='$value'><span>$value</span>";
                    }
                }elseif(key_exists("text",$listY)){
                    if($this->ready){
                        $html.="<textarea type='text' name='$name' class='form-control' readonly></textarea>";
                    }else{
                        $html.="<textarea type='text' name='$name' class='form-control'></textarea>";
                    }
                }elseif(key_exists("input",$listY)){
                    if(key_exists("ready",$listY)||$this->ready){
                        $html.="<input type='text' name='$name' value='' class='form-control' readonly>";
                    }else{
                        $html.="<input type='text' name='$name' value='' class='form-control'>";
                    }
                }

                $html.="</td>";
            }
            $html.="</tr>";
        }

        $html.="</tbody></table></div></div>";
        return $html;
    }

    //提取月报表数据（日報表系統）
    public function value($city,$year,$data_field){
        $sqlExpr = "";
        if(!empty($this->search_month)&&$this->audit_year==$year){
            $sqlExpr=" and b.month_no<=$this->search_month ";
        }
        //$data_field 00002:生意額增長 00067:利潤增長 00021:收款率 00017:停單比例 00018:技术员每月平均生产力
        $suffix = Yii::app()->params['envSuffix'];
        $sum = Yii::app()->db->createCommand()->select("SUM(convert(a.data_value,decimal(18,2)))")
            ->from("swoper$suffix.swo_monthly_dtl a")
            ->leftJoin("swoper$suffix.swo_monthly_hdr b","b.id = a.hdr_id")
            ->where("b.city = :city $sqlExpr AND b.year_no = :year AND a.data_field=:field",
                array(":city"=>$city,":year"=>$year,":field"=>$data_field)
            )->queryScalar();
        return empty($sum)||$sum==null?0:$sum;
    }

    //提取月报表数据（營運系統）
    public function valueForOpr($city,$year,$data_field){
        $sqlExpr = "";
        if(!empty($this->search_month)&&$this->audit_year==$year){
            $sqlExpr=" and b.month_no<=$this->search_month ";
        }
        //$data_field 100055：空气净化机租赁
        $suffix = Yii::app()->params['envSuffix'];
        $sum = Yii::app()->db->createCommand()->select("SUM(convert(a.data_value,decimal(18,2)))")
            ->from("operation$suffix.opr_monthly_dtl a")
            ->leftJoin("operation$suffix.opr_monthly_hdr b","b.id = a.hdr_id")
            ->where("b.city = :city $sqlExpr AND b.year_no = :year AND a.data_field=:field",
                array(":city"=>$city,":year"=>$year,":field"=>$data_field)
            )->queryScalar();
        return empty($sum)||$sum==null?0:$sum;
    }

    //提取月报表数据
    public function valueHdr($city,$year){
        $sqlExpr = "";
        if(!empty($this->search_month)&&$this->audit_year==$year){
            $sqlExpr=" and month_no<=$this->search_month ";
        }
        $suffix = Yii::app()->params['envSuffix'];
        $sum = Yii::app()->db->createCommand()->select("AVG(f73)")
            ->from("swoper$suffix.swo_monthly_hdr")
            ->where("city = :city AND year_no = :year $sqlExpr",
                array(":city"=>$city,":year"=>$year)
            )->queryScalar();
        return empty($sum)||$sum==null?0:round($sum,2);
    }

    //提取月报表数据
    protected function valueHdrForDetail($listX,$funcList){
        $sqlExpr = "";
        if(!empty($this->search_month)){
            $sqlExpr=" and month_no<=$this->search_month ";
        }
        $suffix = Yii::app()->params['envSuffix'];
        $list = array();
        $rows = Yii::app()->db->createCommand()->select("f73,month_no")
            ->from("swoper$suffix.swo_monthly_hdr")
            ->where("city = :city AND year_no = :year $sqlExpr",
                array(":city"=>$this->city,":year"=>$this->audit_year)
            )->queryAll();
        if($rows){
            foreach ($rows as $row){
                $list[]=array('num'=>$row["month_no"],"text"=>floatval($row["f73"]),"len"=>0);
            }
        }
        return $list;
    }

    //提取月报表数据(详情)
    protected function valueForDetail($listX,$funcList){
        $sqlExpr = "";
        if(!empty($this->search_month)){
            $sqlExpr=" and b.month_no<=$this->search_month ";
        }
        $data_field =$funcList["data_field"];
        //$data_field 00002:生意額增長 00067:利潤增長 00021:收款率 00017:停單比例 00018:技术员每月平均生产力
        $suffix = Yii::app()->params['envSuffix'];
        $rows = Yii::app()->db->createCommand()->select("a.data_value,b.month_no")
            ->from("swoper$suffix.swo_monthly_dtl a")
            ->leftJoin("swoper$suffix.swo_monthly_hdr b","b.id = a.hdr_id")
            ->where("b.city = :city $sqlExpr AND b.year_no = :year AND a.data_field=:field",
                array(":city"=>$this->city,":year"=>$this->audit_year,":field"=>$data_field)
            )->order("b.month_no asc")->queryAll();
        $list=array();
        foreach ($rows as $row){
            $key=$row["month_no"];
            $list[]=array('num'=>$key,'text'=>$row["data_value"],'len'=>0);
        }
        return $list;
    }

    //提取营业报告表数据
    public function valueToOp($city,$year){
        //
        $sqlExpr = "";
        if(!empty($this->search_month)&&$this->audit_year==$year){
            $sqlExpr=" and b.month_no<=$this->search_month ";
        }
        if($year>=2022){//2022年需要计算隔油池及ID服务
            $suffix = Yii::app()->params['envSuffix'];
            $sum = Yii::app()->db->createCommand()->select("SUM(convert(a.data_value,decimal(18,2)))")
                ->from("operation$suffix.opr_monthly_dtl a")
                ->leftJoin("operation$suffix.opr_monthly_hdr b","b.id = a.hdr_id")
                ->where("b.city = :city $sqlExpr 
            AND b.year_no = :year 
            AND (
                (a.data_field in ('10005','100055','10004') AND workflow$suffix.RequestStatus('OPRPT',b.id,b.lcd)='ED')
                or
                (a.data_field in ('20001','20002') AND workflow$suffix.RequestStatus('OPRPT2',b.id,b.lcd)='ED')
            ) ",array(":city"=>$city,":year"=>$year))->queryScalar();
            $sum = $sum?$sum:0;
        }else{
            $suffix = Yii::app()->params['envSuffix'];
            $sum = Yii::app()->db->createCommand()->select("SUM(convert(a.data_value,decimal(18,2)))")
                ->from("operation$suffix.opr_monthly_dtl a")
                ->leftJoin("operation$suffix.opr_monthly_hdr b","b.id = a.hdr_id")
                ->where("b.city = :city $sqlExpr 
            AND b.year_no = :year 
            AND a.data_field in ('10005','10004') 
            AND workflow$suffix.RequestStatus('OPRPT',b.id,b.lcd)='ED'",
                    array(":city"=>$city,":year"=>$year)
                )->queryScalar();
        }
        return empty($sum)||$sum==null?0:$sum;
    }

    //提取营业报告表数据(详情)
    protected function valueToOpForDetail($listX,$funcList){
        //
        $sqlExpr = "";
        if(!empty($this->search_month)){
            $sqlExpr=" and b.month_no<=$this->search_month ";
        }
        $suffix = Yii::app()->params['envSuffix'];
        if($this->audit_year>=2022){//2022年需要计算隔油池及ID服务
            $rows = Yii::app()->db->createCommand()->select("a.data_value,b.month_no")
                ->from("operation$suffix.opr_monthly_dtl a")
                ->leftJoin("operation$suffix.opr_monthly_hdr b","b.id = a.hdr_id")
                ->where("b.city = :city $sqlExpr 
            AND b.year_no = :year 
            AND (
                (a.data_field in ('10005','100055','10004') AND workflow$suffix.RequestStatus('OPRPT',b.id,b.lcd)='ED')
                or
                (a.data_field in ('20001','20002') AND workflow$suffix.RequestStatus('OPRPT2',b.id,b.lcd)='ED')
            ) ",
                    array(":city"=>$this->city,":year"=>$this->audit_year)
                )->order("b.month_no asc")->queryAll();
        }else{
            $rows = Yii::app()->db->createCommand()->select("a.data_value,b.month_no")
                ->from("operation$suffix.opr_monthly_dtl a")
                ->leftJoin("operation$suffix.opr_monthly_hdr b","b.id = a.hdr_id")
                ->where("b.city = :city $sqlExpr 
            AND b.year_no = :year 
            AND a.data_field in ('10005','10004') 
            AND workflow$suffix.RequestStatus('OPRPT',b.id,b.lcd)='ED'",
                    array(":city"=>$this->city,":year"=>$this->audit_year)
                )->order("b.month_no asc")->queryAll();
        }
        $list=array();
        foreach ($rows as $row){
            $key=intval($row["month_no"]);
            if(!key_exists($key,$list)){
                $list[$key]=array('num'=>$key,'text'=>0,'len'=>0);
            }
            $number = is_numeric($row["data_value"])?round($row["data_value"],2):0;
            $list[$key]["text"]+=$number;
        }
        //$list[14]=array('num'=>14,'text'=>$sum,'len'=>0);//測試bug
        return $list;
    }

    //平均值
    public function valueAverage($city,$year,$data_field='00018'){
        //00018:技术员每月平均生产力
        $sqlExpr = "";
        if(!empty($this->search_month)&&$this->audit_year==$year){
            $sqlExpr=" and b.month_no<=$this->search_month ";
        }
        $suffix = Yii::app()->params['envSuffix'];
        $sum = 0;
        $rows = Yii::app()->db->createCommand()->select("a.data_value")
            ->from("swoper$suffix.swo_monthly_dtl a")
            ->leftJoin("swoper$suffix.swo_monthly_hdr b","b.id = a.hdr_id")
            ->where("b.city = :city $sqlExpr AND b.year_no = :year AND a.data_field=:field",
                array(":city"=>$city,":year"=>$year,":field"=>$data_field)
            )->queryAll();
        if($rows){
            foreach ($rows as $row){
                $sum+=floatval($row["data_value"]);
            }
            $sum=$sum/count($rows);
        }
        return floatval(sprintf("%.2f",$sum));
    }

    //提取月报表数据 (精確到月份)
    public function valueAndMonth($city,$year,$month,$data_field){
        //$data_field 00002:生意額增長 00067:利潤增長 00021:收款率 00017:停單比例 00018:技术员每月平均生产力
        $suffix = Yii::app()->params['envSuffix'];
        $sum = Yii::app()->db->createCommand()->select("SUM(convert(a.data_value,decimal(18,2)))")
            ->from("swoper$suffix.swo_monthly_dtl a")
            ->leftJoin("swoper$suffix.swo_monthly_hdr b","b.id = a.hdr_id")
            ->where("b.city = :city AND b.year_no = :year AND b.month_no = :month AND a.data_field=:field",
                array(":city"=>$city,":year"=>$year,":month"=>$month,":field"=>$data_field)
            )->queryScalar();
        return empty($sum)?0:$sum;
    }

    //服务单的停单比例
    public function valueStopToRate($city,$year,$arr = array("00017","00002")){
        //00017:今月停單生意額   00002:今月生意額  rate = 今月停單/今月的生意額 * 100
        $rows = $this->getValueListToArr($arr,$city,$year);
        $sum = 0;
        if($rows){
            foreach ($rows as $row){
                $row["valueOne"] = floatval($row["valueOne"]);
                $row["valueTwo"] = floatval($row["valueTwo"]);
                $count = empty($row["valueTwo"])||$row["valueTwo"]==0?0:$row["valueOne"]/$row["valueTwo"];
                $count*=100;
                $sum+=$count;
            }
            $sum = $sum/count($rows);
        }
        return floatval(sprintf("%.2f",$sum));
    }

    //服务单的停单比例(详情)
    protected function valueStopToRateForDetail($listX,$funcList){
        //$listX,$funcList
        $city = $this->city;
        $year = $this->audit_year;
        $arr = $funcList["arr"];//
        //00017:今月停單生意額   00002:今月生意額  rate = 今月停單/今月的生意額 * 100
        $rows = $this->getValueListToArr($arr,$city,$year);
        $list = array();//month_no
        if($rows){
            foreach ($rows as $row){
                $row["valueOne"] = floatval($row["valueOne"]);
                $row["valueTwo"] = floatval($row["valueTwo"]);
                $count = empty($row["valueTwo"])||$row["valueTwo"]==0?0:$row["valueOne"]/$row["valueTwo"];
                $count*=100;
                $count=sprintf("%.2f",$count)."%";
                $list[]=array('num'=>$row["month_no"],"text"=>$count,"len"=>0);
            }
        }
        return $list;
    }

    //蔚诺租赁服务机器台数
    public function valueServiceNum($city,$year){
        $sqlExpr1 = "";
        $sqlExpr2 = "";
        if(!empty($this->search_month)&&$this->audit_year==$year){
            $sqlExpr1=" and date_format(a.status_dt,'%c')<=$this->search_month ";
            $sqlExpr2=" and date_format(a.first_dt,'%c')<=$this->search_month ";
        }
        $suffix = Yii::app()->params['envSuffix'];
        //服務類型的第二欄是非一次性服務(ID服務)
        $row = Yii::app()->db->createCommand()->select("sum(a.amt_money)")->from("swoper$suffix.swo_serviceid a")
            ->leftJoin("swoper$suffix.swo_customer_type_info b","b.id = a.cust_type_name")
            ->where("b.single=0 $sqlExpr1 and date_format(a.status_dt,'%Y')=:year AND a.status='N' AND a.city=:city",
                array(":year"=>$year,":city"=>$city)
            )->queryScalar();
        $row = empty($row)?0:$row;
        //隔油池金額 (非ID服務)
        $serviceMoney =Yii::app()->db->createCommand()
            ->select("sum(CASE WHEN a.paid_type = 'M' THEN a.amt_paid*a.ctrt_period ELSE a.amt_paid END)")
            ->from("swoper$suffix.swo_service a")
            ->leftJoin("swoper$suffix.swo_customer_type_twoname b","a.cust_type_name = b.id")
            ->leftJoin("swoper$suffix.swo_customer_type c","a.cust_type = c.id")
            ->where("b.bring = 1 and a.status = 'N' $sqlExpr2 and date_format(a.first_dt,'%Y')=:year AND a.city=:city",
                array(":year"=>$year,":city"=>$city)
            )->queryScalar();
        $serviceMoney = empty($serviceMoney)?0:$serviceMoney;
        return $serviceMoney+$row;
    }

    //蔚诺租赁服务机器台数(详情)
    protected function valueServiceNumForDetail($listX,$funcList){
        $sqlExpr1 = "";
        $sqlExpr2 = "";
        if(!empty($this->search_month)){
            $sqlExpr1=" and date_format(a.status_dt,'%c')<=$this->search_month ";
            $sqlExpr2=" and date_format(a.first_dt,'%c')<=$this->search_month ";
        }
        $suffix = Yii::app()->params['envSuffix'];
        $list = array();
        //服務類型的第二欄是非一次性服務(ID服務)
        $serviceIDRows = Yii::app()->db->createCommand()->select("date_format(a.status_dt,'%c') as month_no,sum(a.amt_money) as sum_num")->from("swoper$suffix.swo_serviceid a")
            ->leftJoin("swoper$suffix.swo_customer_type_info b","b.id = a.cust_type_name")
            ->where("b.single=0 $sqlExpr1 and date_format(a.status_dt,'%Y')=:year AND a.status='N' AND a.city=:city",
                array(":year"=>$this->audit_year,":city"=>$this->city)
            )->group("date_format(a.status_dt,'%c')")->queryAll();
        if($serviceIDRows){
            foreach ($serviceIDRows as $row){
                if(!key_exists($row["month_no"],$list)){
                    $list[$row["month_no"]] = array('num'=>$row["month_no"],'text'=>0,'len'=>0);
                }
                $list[$row["month_no"]]['text']+=floatval($row["sum_num"]);
            }
        }
        //隔油池金額 (非ID服務)
        $serviceOtherRows =Yii::app()->db->createCommand()
            ->select("date_format(a.first_dt,'%c') as month_no,sum(CASE WHEN a.paid_type = 'M' THEN a.amt_paid*a.ctrt_period ELSE a.amt_paid END) as sum_num")
            ->from("swoper$suffix.swo_service a")
            ->leftJoin("swoper$suffix.swo_customer_type_twoname b","a.cust_type_name = b.id")
            ->leftJoin("swoper$suffix.swo_customer_type c","a.cust_type = c.id")
            ->where("b.bring = 1 and a.status = 'N' $sqlExpr2 and date_format(a.first_dt,'%Y')=:year AND a.city=:city",
                array(":year"=>$this->audit_year,":city"=>$this->city)
            )->group("date_format(a.first_dt,'%c')")->queryAll();
        if($serviceOtherRows){
            foreach ($serviceOtherRows as $row){
                if(!key_exists($row["month_no"],$list)){
                    $list[$row["month_no"]] = array('num'=>$row["month_no"],'text'=>0,'len'=>0);
                }
                $list[$row["month_no"]]['text']+=floatval($row["sum_num"]);
            }
        }
        return $list;
    }

    //收款比例
    public function valueOnToRate($city,$year){
        //00021:今月收款额   00002:今月生意額  rate = 今月收款额/上月的生意額 * 100
        $rows = $this->getValueListToArr(array("00021","00002"),$city,$year);
        $sum = 0;
        if($rows){
            if($rows[0]["month_no"] == 1){
                $valueTwo = $this->valueAndMonth($city,$year-1,12,"00002"); //上一年12月份的生意額
            }else{
                $valueTwo = 0; //上月份的生意額
            }
            foreach ($rows as $key =>$row){
                $row["valueOne"] = floatval($row["valueOne"]);
                $valueTwo = $key==0?$valueTwo:floatval($rows[$key-1]["valueTwo"]);
                $count = empty($valueTwo)||$valueTwo==0?0:$row["valueOne"]/$valueTwo;
                $count*=100;
                $sum+=$count;
            }
            $sum = $sum/count($rows);
        }
        return floatval(sprintf("%.2f",$sum));
    }

    //收款比例(详情)
    protected function valueOnToRateForDetail($listX,$funcList){
        $city = $this->city;
        $year = $this->audit_year;
        //00021:今月收款额   00002:今月生意額  rate = 今月收款额/上月的生意額 * 100
        $rows = $this->getValueListToArr(array("00021","00002"),$city,$year);
        $list = array();
        if($rows){
            if($rows[0]["month_no"] == 1){
                $valueTwo = $this->valueAndMonth($city,$year-1,12,"00002"); //上一年12月份的生意額
            }else{
                $valueTwo = 0; //上月份的生意額
            }
            foreach ($rows as $key =>$row){
                $row["valueOne"] = floatval($row["valueOne"]);
                $valueTwo = $key==0?$valueTwo:floatval($rows[$key-1]["valueTwo"]);
                $count = empty($valueTwo)||$valueTwo==0?0:$row["valueOne"]/$valueTwo;
                $count*=100;
                $count = sprintf("%.2f",$count)."%";
                //month_no
                $list[]=array('num'=>$row["month_no"],"text"=>$count,"len"=>0);
            }
        }
        return $list;
    }

    //員工考核分數
    public function valueStaffReview($employee_id,$year){
        $sqlExpr = "";
        if(!empty($this->search_month)&&$this->search_month<=6&&$this->audit_year==$year){
            $sqlExpr=" and year_type=1";
        }
        $sum = 0;
        $rows = Yii::app()->db->createCommand()->select("review_sum")->from("hr_review")
            ->where("status_type=3 and employee_id=:employee_id and year=:year $sqlExpr",array(":year"=>$year,":employee_id"=>$employee_id))
            ->queryAll();
        if($rows){
            foreach ($rows as $row){
                $sum+=floatval($row["review_sum"]);
            }
            $sum = $sum/count($rows);
        }
        return $sum;
    }

    //員工考核分數(详情)
    protected function valueStaffReviewForDetail($listX,$funcList){
        $sqlExpr = "";
        if(!empty($this->search_month)&&$this->search_month<=6){
            $sqlExpr=" and year_type=1";
        }
        $employee_id = $this->employee_id;
        $year = $this->audit_year;
        $list = array();//month_no
        $rows = Yii::app()->db->createCommand()->select("review_sum,year_type")->from("hr_review")
            ->where("status_type=3 and employee_id=:employee_id and year=:year $sqlExpr",array(":year"=>$year,":employee_id"=>$employee_id))
            ->queryAll();
        if($rows){
            foreach ($rows as $key=>$row){
                $len = !empty($this->search_month)&&$this->search_month<=6?$this->search_month-1:($key==0?5:$this->search_month-7);
                $num = $row["year_type"]==1?1:7;
                $list[]=array('num'=>$num,"text"=>floatval($row["review_sum"]),"len"=>$len);
            }
        }
        return $list;
    }

    //总经理回馈次数
    public function valueFeedback($city,$year){
        $sqlExpr = "";
        if(!empty($this->search_month)&&$this->audit_year==$year){
            $sqlExpr=" and date_format(request_dt,'%c')<=$this->search_month ";
        }
        $suffix = Yii::app()->params['envSuffix'];
        $row = Yii::app()->db->createCommand()->select("count(*)")->from("swoper$suffix.swo_mgr_feedback")
            ->where("date_format(request_dt,'%Y')=:year $sqlExpr AND city=:city AND status='Y' AND (DATEDIFF(feedback_dt,request_dt)=0 OR DATEDIFF(feedback_dt,request_dt)=1)",
                array(":year"=>$year,":city"=>$city)
            )
            ->queryScalar();
        return empty($row)?0:$row;
    }

    //总经理回馈次数(详情)
    protected function valueFeedbackForDetail($listX,$funcList){
        $sqlExpr = "";
        if(!empty($this->search_month)){
            $sqlExpr=" and date_format(request_dt,'%c')<=$this->search_month ";
        }
        $suffix = Yii::app()->params['envSuffix'];
        $rows = Yii::app()->db->createCommand()->select("date_format(request_dt,'%c') as month_no,count(id) as count_num")->from("swoper$suffix.swo_mgr_feedback")
            ->where("date_format(request_dt,'%Y')=:year $sqlExpr AND city=:city AND status='Y' AND (DATEDIFF(feedback_dt,request_dt)=0 OR DATEDIFF(feedback_dt,request_dt)=1)",
                array(":year"=>$this->audit_year,":city"=>$this->city)
            )->group("date_format(request_dt,'%c')")
            ->queryAll();
        $list=array();
        if($rows){
            foreach ($rows as $row){
                $list[]=array('num'=>$row["month_no"],"text"=>$row["count_num"],"len"=>0);
            }
        }
        return $list;
    }

    //销售5步曲 - 销售部分
    public function valueSalesOne($year,$city){
        $suffix = Yii::app()->params['envSuffix'];
        $sum = 0;
        $count = 0;
        $whereSql = "IFNULL(TIMESTAMPDIFF(MONTH,a.entry_time,a.leave_time),3)>=2";//入职大于两个月(离职时间-入职时间>2)
        $rows = Yii::app()->db->createCommand()->select("d.user_id,a.entry_time,a.id,a.lud,a.position")
            ->from("hr_binding d")
            ->leftJoin("hr_employee a","d.employee_id=a.id")
            ->leftJoin("hr_dept b","a.position=b.id")
            ->leftJoin("security$suffix.sec_user f","f.username=d.user_id")
            ->where("(a.staff_status=0 or (a.staff_status=-1 and {$whereSql})) AND b.manager_type=1 AND f.city=:city",
                array(":city"=>$city)
            )->queryAll();
        if($rows){
            foreach ($rows as $row){
                $entry_time = $row["entry_time"];
                $historyRow = Yii::app()->db->createCommand()->select("a.effect_time,a.lcd,a.operation")->from("hr_employee_operate a")
                    ->leftJoin("hr_dept b","a.position=b.id")
                    ->where("a.employee_id=:id and b.manager_type!=1 and a.lcd<=:lud",
                        array(":id"=>$row["id"],":lud"=>$row["lud"])
                    )->order("lcd desc")->queryRow();
                if($historyRow){//如果職位從非銷售轉成了銷售，入職時間需要改成職位變更時間
                    if($historyRow["operation"]=="update"){//變更
                        $entry_time = date("Y/m/d",strtotime($historyRow["lcd"]));
                    }else{
                        $entry_time = date("Y/m/d",strtotime($historyRow["effect_time"]));
                    }
                }
                if(intval($entry_time)==$year){ //入職或者變更等於老總年度考核的時間
                    //echo "<div class='hide' data-sales='sales-user' data-year='{$year}'>user_id:{$row["user_id"]},employee_id:{$row["id"]}</div>";
                    $count++;
                    /*  查询月份不影响销售5步曲分数*/
                    $datetime = date("Y/m/d",strtotime($entry_time." + 2 month"));
                    $bool = Yii::app()->db->createCommand()->select("username")->from("sales$suffix.sal_fivestep")
                        ->where("step in ('1','2','3') and username=:username and date_format(rec_dt,'%Y/%m/%d') <=:datetime",
                            array(":username"=>$row["user_id"],":datetime"=>$datetime)
                        )->queryRow();
                    if($bool){
                        $sum++;
                    }
                }
            }
        }
        $sum = empty($count)?100:($sum/$count)*100;
        return floatval(sprintf("%.2f",$sum));
    }

    //销售5步曲 - 销售经理部分(已弃置)
    public function valueSalesTwo($year,$city){
        $suffix = Yii::app()->params['envSuffix'];
        $count = 0;
        $rows = Yii::app()->db->createCommand()->select("d.user_id,a.entry_time")->from("hr_binding d")
            ->leftJoin("hr_employee a","d.employee_id=a.id")
            ->leftJoin("hr_dept b","a.position=b.id")
            ->leftJoin("security$suffix.sec_user f","f.username=d.user_id")
            ->where("CONVERT(a.entry_time, SIGNED)=:year AND b.manager_type in (2,3) AND f.city=:city",
                array(":year"=>$year,":city"=>$city)
            )->queryAll();
        if($rows){
            foreach ($rows as $row){
                $datetime = date("Y/m/d",strtotime($row["entry_time"]." + 2 month"));
                $bool = Yii::app()->db->createCommand()->select("username")->from("sales$suffix.sal_fivestep")
                    ->where("step in ('4','5') and username=:username and date_format(rec_dt,'%Y/%m/%d') <=:datetime",
                        array(":username"=>$row["user_id"],":datetime"=>$datetime)
                    )->queryRow();
                if($bool){
                    $count++;
                }
            }
            $count = ($count/count($rows))*100;
        }
        return floatval(sprintf("%.2f",$count));
    }

    //是否有銷售經理  true：是  false：否
    public function validateSalesBoos($year,$city){
        $suffix = Yii::app()->params['envSuffix'];
        $row = Yii::app()->db->createCommand()->select("d.user_id")->from("hr_binding d")
            ->leftJoin("hr_employee a","d.employee_id=a.id")
            ->leftJoin("hr_dept b","a.position=b.id")
            ->leftJoin("security$suffix.sec_user f","f.username=d.user_id")
            ->where("CONVERT(a.entry_time, SIGNED)=:year AND b.manager_type in (2,3) AND f.city=:city",
                array(":year"=>$year,":city"=>$city)
            )->queryRow();
        if($row){
            return true;
        }else{
            return false;
        }
    }

    //將某兩行轉換成兩列 （用於當月的兩個值相除）
    private function getValueListToArr($arr,$city,$year){
        $sqlExpr = "";
        if(!empty($this->search_month)&&$this->audit_year==$year){
            $sqlExpr=" and b.month_no<=$this->search_month ";
        }
        $suffix = Yii::app()->params['envSuffix'];
        $sql = "SELECT b.year_no,b.month_no,
                    SUM(CASE a.data_field WHEN '".$arr[0]."' THEN convert(a.data_value,decimal(18,2)) ELSE 0 END) as valueOne,
                    SUM(CASE a.data_field WHEN '".$arr[1]."' THEN convert(a.data_value,decimal(18,2)) ELSE 0 END) as valueTwo 
                FROM swoper$suffix.swo_monthly_dtl a
                LEFT JOIN swoper$suffix.swo_monthly_hdr b ON a.hdr_id = b.id
                WHERE b.city='$city' AND a.data_field in('".implode("','",$arr)."') AND b.year_no = '$year' $sqlExpr  
                GROUP BY b.year_no,b.month_no ORDER BY b.month_no ASC";
        $rows = Yii::app()->db->createCommand($sql)->queryAll();
        return $rows;
    }

    //年生意額淨增長(不需要)
    public static function sumAmountNetGrowth($year, $city) {
        $suffix = Yii::app()->params['envSuffix'];
        $rtn = 0;
        $sql = "select a.city, a.status, 
					sum(case a.paid_type
							when 'Y' then a.amt_paid
							when 'M' then a.amt_paid * 
								(case when a.ctrt_period < 12 then a.ctrt_period else 12 end)
							else a.amt_paid
						end
					) as sum_amount,
					sum(case a.b4_paid_type
							when 'Y' then a.b4_amt_paid
							when 'M' then a.b4_amt_paid * 
								(case when a.ctrt_period < 12 then a.ctrt_period else 12 end)
							else a.b4_amt_paid
						end
					) as b4_sum_amount
				from swoper$suffix.swo_service a, swoper$suffix.swo_customer_type b 
				where ((year(a.first_dt)=$year and a.status in ('N'))  
				or (year(a.status_dt)=$year and a.status in ('T','A')))
				and a.cust_type=b.id and b.rpt_cat <> 'INV' 
				AND a.city='$city' 
				group by a.city, a.status
			";
        $rows = Yii::app()->db->createCommand($sql)->queryAll();
        if (count($rows) > 0) {
            $amt_n = 0;
            $amt_a = 0;
            $amt_r = 0;
            $amt_s = 0;
            $amt_t = 0;
            foreach ($rows as $row) {
                switch ($row['status']) {
                    case 'N': $amt_n = $row['sum_amount']; break;
                    case 'A': $amt_a = $row['sum_amount']-$row['b4_sum_amount']; break;
                    case 'R': $amt_r = $row['sum_amount']; break;
                    case 'S': $amt_s = $row['sum_amount']; break;
                    case 'T': $amt_t = $row['sum_amount']; break;
                }
            }
            $rtn = number_format($amt_n+$amt_a+$amt_r-$amt_s-$amt_t,2,'.','');
        }
        return $rtn;
    }

}
