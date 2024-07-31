<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 * Date: 2020/6/15
 * Time: 13:42
 */
class BossReviewB extends BossReview
{

    protected function setListX(){
        $this->listX = array(
            array('value'=>'two_one','name'=>Yii::t("contract","two_one"),'percent'=>'20','show'=>'1'),//优化人才评核
            array('value'=>'two_two','name'=>Yii::t("contract","two_two"),'percent'=>'35','show'=>'1'),//月报表分数
            array('value'=>'two_three','name'=>Yii::t("contract","two_three"),'percent'=>'10','show'=>'1'),//质检拜访量
            array('value'=>'two_eight','name'=>Yii::t("contract","two_eight"),'percent'=>'10','show'=>'1'),//洗地易销售桶数
            array('value'=>'two_four','name'=>Yii::t("contract","two_four"),'pro_str'=>"%",'percent'=>'5','show'=>'1'),//高效客诉解决效率
            array('value'=>'two_five','name'=>Yii::t("contract","two_five"),'percent'=>'5','show'=>'1'),//总经理回馈次数
            array('value'=>'two_six','name'=>Yii::t("contract","two_six"),'pro_str'=>"%",'percent'=>'5','show'=>'1'),//提交销售5步曲数量培训销售部分
            array('value'=>'two_nine','name'=>Yii::t("contract","two_nine"),'pro_str'=>"%",'percent'=>'5','show'=>'1'),//IA物料使用率
            array('value'=>'two_ten','name'=>Yii::t("contract","two_ten"),'pro_str'=>"%",'percent'=>'5','show'=>'1'),//IB物料使用率
            //array('value'=>'two_service','name'=>Yii::t("contract","two_service"),'percent'=>'0','show'=>'1'),//蔚诺租赁服务机器台数
            //array('value'=>'two_seven','name'=>Yii::t("contract","two_seven"),'pro_str'=>"%")//提交销售5步曲数量培训销售经理部分
        );
    }

    //自由設置B項目
    public function resetListX($list){
        if(is_array($list)&&key_exists("bossB",$list)){
            $this->listX = $list['bossB'];
        }
    }

    //自由設置B項目(城市默認)
    public function cityListX(){
        $row = Yii::app()->db->createCommand()->select("json_text,num_ratio")->from("hr_boss_set_b")
            ->where('tacitly=1 or city=:city ', array(':city'=>$this->city))->order("tacitly asc")->queryRow();
        if($row){
            $this->listX = array();
            $this->ratio_b = $row["num_ratio"];
            if(!empty($this->model)){
                $this->model->ratio_b=$this->ratio_b;
            }
            $jsonList = json_decode($row["json_text"],true);
            foreach ($jsonList as $list){
                if($list["show"] == 1){
                    $list["name"] = Yii::t("contract",$list['value']);
                    $this->listX[] = $list;
                }
            }
        }
    }

    protected function setListY(){
        $this->listY = array(
            array('value'=>'two_1','name'=>($this->audit_year-1).Yii::t("contract","one_1"),'function'=>"getOldYear","width"=>"120px",'pro_str'=>"%"),//2018年度数据
            array('value'=>'two_2','name'=>Yii::t("contract","one_12").$this->audit_year.Yii::t("contract","one_3"),'function'=>"getPlanYear",'validate'=>true,"width"=>"160px",'pro_str'=>"%","emailBool"=>true),//预计2019年目标数据
            array('value'=>'two_3','name'=>Yii::t("contract","one_5"),'function'=>"getPlanYearCof","width"=>"100px"),//系数
            array('value'=>'two_4','name'=>$this->audit_year.Yii::t("contract","one_6"),'function'=>"getNowYear","width"=>"160px",'pro_str'=>"%","emailBool"=>true),//2019年实际达成数据
            array('value'=>'two_5','name'=>Yii::t("contract","one_8"),'function'=>"getLadderDiffer","width"=>"100px"),//阶梯落差
            array('value'=>'two_6','name'=>Yii::t("contract","one_9"),'function'=>"getLadderCof","width"=>"100px"),//落差系数
            array('value'=>'two_7','name'=>Yii::t("contract","one_10"),'function'=>"getNowCof","width"=>"100px"),//实际系数
            array('value'=>'two_8','name'=>Yii::t("contract","one_11"),'function'=>"getSumRate","width"=>"100px",'static_str'=>"%"),//占比（%）
            array('value'=>'two_9','name'=>Yii::t("contract","three_four"),'function'=>"getSumNumber","width"=>"100px",'static_str'=>"%"),//得分
            array('value'=>'two_10','name'=>Yii::t("contract","Remark"),'function'=>"getRemark","width"=>"160px")//备注
        );
    }

    //2018年度数据 - two_1
    public function getOldYear($type,$str,$list=array()){
        switch ($type){
            case "two_one"://优化人才评核
                $this->json_text[$type][$str] = $this->valueStaffReview($this->employee_id,$this->audit_year-1);
                return array('value'=>$this->json_text[$type][$str],'name'=>$this->json_text[$type][$str]);
            case "two_two"://月报表分数
                //$this->json_text[$type][$str] = MonthList::getSumAverageByYear($this->audit_year-1,$this->city);
                $this->json_text[$type][$str] = $this->valueHdr($this->city,$this->audit_year-1);
                return array('value'=>$this->json_text[$type][$str],'name'=>$this->json_text[$type][$str]);
            case "two_three"://质检拜访量
                $this->json_text[$type][$str] = $this->value($this->city,$this->audit_year-1,"00042");
                return array('value'=>$this->json_text[$type][$str],'name'=>$this->json_text[$type][$str]);
            case "two_eight"://洗地易销售桶数
                $this->json_text[$type][$str] = $this->value($this->city,$this->audit_year-1,"00069");
                return array('value'=>$this->json_text[$type][$str],'name'=>$this->json_text[$type][$str]);
            case "two_four"://高效客诉解决效率 00038  00036
                $this->json_text[$type][$str] = $this->valueStopToRate($this->city,$this->audit_year-1,array("00038","00036"));
                return array('value'=>$this->json_text[$type][$str],'name'=>$this->json_text[$type][$str]."%");
                break;
            case "two_five"://总经理回馈次数
                $this->json_text[$type][$str] = $this->valueFeedback($this->city,$this->audit_year-1);
                return array('value'=>$this->json_text[$type][$str],'name'=>$this->json_text[$type][$str]);
                break;
            case "two_six"://提交销售5步曲数量培训销售部分
                $this->json_text[$type][$str] = $this->valueSalesOne($this->audit_year-1,$this->city);
                return array('value'=>$this->json_text[$type][$str],'name'=>$this->json_text[$type][$str]."%");
            case "two_seven"://提交销售5步曲数量培训销售经理部分
                $this->json_text[$type][$str] = $this->valueSalesTwo($this->audit_year-1,$this->city);
                return array('value'=>$this->json_text[$type][$str],'name'=>$this->json_text[$type][$str]."%");
            case "two_nine"://IA物料使用率
                $this->json_text[$type][$str] = $this->valueStopToRate($this->city,$this->audit_year-1,array("00023","00003"));
                return array('value'=>$this->json_text[$type][$str],'name'=>$this->json_text[$type][$str]."%");
            case "two_ten"://IB物料使用率
                $this->json_text[$type][$str] = $this->valueStopToRate($this->city,$this->audit_year-1,array("00024","00004"));
                return array('value'=>$this->json_text[$type][$str],'name'=>$this->json_text[$type][$str]."%");
            case "two_service"://蔚诺租赁服务机器台数
                $this->json_text[$type][$str] = $this->valueServiceNum($this->city,$this->audit_year-1);
                return array('value'=>$this->json_text[$type][$str],'name'=>$this->json_text[$type][$str]);
        }
        $this->json_text[$type][$str] = 0;
        return array('value'=>$this->json_text[$type][$str],'name'=>$this->json_text[$type][$str]);
    }
    //预计2019年目标数据 - two_2
    public function getPlanYear($type,$str,$list=array()){
        $value = isset($this->json_text[$type][$str])?$this->json_text[$type][$str]:"";
        $name = $this->className."[json_text][".$type."]"."[".$str."]";
        $ready = $this->ready?"readonly":"";
        if($value === ""){
            $html ="<input type='number' name='$name' value='' data-name='$type' $ready class='form-control planYearB'/>";
        }else{
            $html ="<input type='number' name='$name' value='$value' data-name='$type' $ready class='form-control planYearB'/>";
        }

        if(in_array($type,array("two_four","two_six","two_seven","two_nine","two_ten"))){
            $html="<div class='input-group'>$html<span class='input-group-addon'>%</span></div>";
        }
        $this->json_text[$type][$str] = $value;
        return array('value'=>$this->json_text[$type][$str],'name'=>$html);
    }
    //系数 - two_3
    public function getPlanYearCof($type,$str,$list=array()){
        $value = $this->json_text[$type]["two_2"];
        $value = $this->cofModel->getClassCof($value,$this->countPrice,$type);//系數
        $kpiData = $this->cofModel->getKPIListStr();
        $kpiType = $this->cofModel->size_type;
        $name = $this->className."[json_text][".$type."]"."[".$str."]";
        $html ="<input readonly type='text' name='$name' value='$value' data-name='$type' data-kpi='$kpiData' data-size='$kpiType' class='form-control planYearBCof changeCofWindow'/>";


        $this->json_text[$type][$str] = $value;
        return array('value'=>$this->json_text[$type][$str],'name'=>$html);
    }
    //2019年实际达成数据 - two_4
    public function getNowYear($type,$str,$list=array()){
        switch ($type){
            case "two_one"://优化人才评核
                $this->json_text[$type][$str] = $this->valueStaffReview($this->employee_id,$this->audit_year);
                return array('value'=>$this->json_text[$type][$str],'name'=>$this->json_text[$type][$str]);
            case "two_two"://月报表分数
                //$this->json_text[$type][$str] = MonthList::getSumAverageByYear($this->audit_year,$this->city);
                $this->json_text[$type][$str] = $this->valueHdr($this->city,$this->audit_year);
                return array('value'=>$this->json_text[$type][$str],'name'=>$this->json_text[$type][$str]);
            case "two_three"://质检拜访量
                $this->json_text[$type][$str] = $this->value($this->city,$this->audit_year,"00042");
                return array('value'=>$this->json_text[$type][$str],'name'=>$this->json_text[$type][$str]);
            case "two_eight"://洗地易销售桶数
                $this->json_text[$type][$str] = $this->value($this->city,$this->audit_year,"00069");
                return array('value'=>$this->json_text[$type][$str],'name'=>$this->json_text[$type][$str]);
            case "two_four"://高效客诉解决效率 00038  00036
                $this->json_text[$type][$str] = $this->valueStopToRate($this->city,$this->audit_year,array("00038","00036"));
                return array('value'=>$this->json_text[$type][$str],'name'=>$this->json_text[$type][$str]."%");
                break;
            case "two_five"://总经理回馈次数
                $this->json_text[$type][$str] = $this->valueFeedback($this->city,$this->audit_year);
                return array('value'=>$this->json_text[$type][$str],'name'=>$this->json_text[$type][$str]);
                break;
            case "two_six"://提交销售5步曲数量培训销售部分
                $this->json_text[$type][$str] = $this->valueSalesOne($this->audit_year,$this->city);
                return array('value'=>$this->json_text[$type][$str],'name'=>$this->json_text[$type][$str]."%");
            case "two_seven"://提交销售5步曲数量培训销售经理部分
                $this->json_text[$type][$str] = $this->valueSalesTwo($this->audit_year,$this->city);
                return array('value'=>$this->json_text[$type][$str],'name'=>$this->json_text[$type][$str]."%");
            case "two_nine"://IA物料使用率
                $this->json_text[$type][$str] = $this->valueStopToRate($this->city,$this->audit_year,array("00023","00003"));
                return array('value'=>$this->json_text[$type][$str],'name'=>$this->json_text[$type][$str]."%");
            case "two_ten"://IB物料使用率
                $this->json_text[$type][$str] = $this->valueStopToRate($this->city,$this->audit_year,array("00024","00004"));
                return array('value'=>$this->json_text[$type][$str],'name'=>$this->json_text[$type][$str]."%");
            case "two_service"://蔚诺租赁服务机器台数
                $this->json_text[$type][$str] = $this->valueServiceNum($this->city,$this->audit_year);
                return array('value'=>$this->json_text[$type][$str],'name'=>$this->json_text[$type][$str]);
        }
        $this->json_text[$type][$str] = 0;
        return array('value'=>$this->json_text[$type][$str],'name'=>$this->json_text[$type][$str]);
    }
    //阶梯落差 - two_5
    public function getLadderDiffer($type,$str,$list=array()){
        $cofNow = $this->cofModel->getClassCof($this->json_text[$type]["two_4"],$this->countPrice,$type);
        $value = $this->cofModel->getClassLadder($this->json_text[$type]["two_3"],$cofNow,$type,$this->countPrice);
        $name1 = $this->className."[json_text][".$type."]"."[".$str."]";
        $name2 = $this->className."[json_text][".$type."]"."[cofNow]";
        $html = "<input type='hidden' name='$name1' value='$value'><input type='hidden' name='$name2' value='$cofNow'>"."<span>".abs($value)."</span>";
        $this->json_text[$type]["cofNow"] = $cofNow;
        $this->json_text[$type][$str] = $value;
        return array('value'=>$this->json_text[$type][$str],'name'=>$html);
    }
    //落差系数 - two_6
    public function getLadderCof($type,$str,$list=array()){
        $value = $this->json_text[$type]["two_5"];
        $value = $value>0?$value*0.03:$value*0.08;
        //$value += $this->json_text[$type]["two_3"];
        $this->json_text[$type][$str] = $value;
        return array('value'=>$this->json_text[$type][$str],'name'=>$value);
    }
    //实际系数 - two_7
    public function getNowCof($type,$str,$list=array()){
        $value = $this->json_text[$type]["two_6"]+$this->json_text[$type]["two_3"];
        $value = $value<=0?0:$value;
        $this->json_text[$type][$str] = $value;
        return array('value'=>$this->json_text[$type][$str],'name'=>$value);
    }
    //占比（%） - two_8
    public function getSumRate($type,$str,$list=array()){
        $value = $list["percent"];
        $this->json_text[$type][$str] = $value;
        return array('value'=>$this->json_text[$type][$str],'name'=>$this->json_text[$type][$str]."%");
    }
    //得分 - two_9
    public function getSumNumber($type,$str,$list=array()){
        $value = $this->json_text[$type]["two_7"]*$this->json_text[$type]["two_8"];
        $value = floatval(sprintf("%.3f",$value));
        $this->json_text[$type][$str] = $value;
        $this->scoreSum +=$value;
        return array('value'=>$this->json_text[$type][$str],'name'=>$value."%");
    }
    //邮件专用
    public function getEveryOrNowNumber($type,$str="every"){
        $value = $this->json_text[$type]["two_2"];
        switch ($str){
            case "complete"://目标实际完成 = XX年实际达成数据÷预计XX年目标数据
                $value=empty($value)?0:$this->json_text[$type]["two_4"]/$value;
                $value*=100;
                $value = round($value,2);
                break;
            case "every"://每月应达成平均数
                $value=$value/12;
                $value = round($value);
                break;
            case "now"://累计到当月应达成数据
                $value=$value/12;
                $value = round($value);
                $value*=$this->search_month;
                break;
        }
        return $value;
    }
    //备注 - two_10
    public function getRemark($type,$str,$list=array()){
        $value = isset($this->json_text[$type][$str])?$this->json_text[$type][$str]:"";
        $name = $this->className."[json_text][".$type."]"."[".$str."]";
        $ready = $this->ready?"readonly":"";
        $html ="<textarea name='$name' class='form-control' $ready >$value</textarea><input type='hidden'>";

        $this->json_text[$type][$str] = $value;
        return array('value'=>$this->json_text[$type][$str],'name'=>$html);
    }
}