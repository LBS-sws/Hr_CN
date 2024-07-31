<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 * Date: 2020/6/15
 * Time: 13:42
 */
class BossReviewA extends BossReview
{

    protected function setListX(){
        $this->listX = array(
            array('value'=>'one_one','name'=>Yii::t("contract","one_one"),'percent'=>'25','show'=>'1'),//年生意额增长目标
            array('value'=>'one_two','name'=>Yii::t("contract","one_two"),'percent'=>'20','show'=>'1'),//年利润额增长目标
            array('value'=>'one_three','name'=>Yii::t("contract","one_three"),'percent'=>'10','show'=>'1'),//年新业务生意额目标
            array('value'=>'one_four','name'=>Yii::t("contract","one_four"),'percent'=>'10','show'=>'1'),//IA服务生意年金额
            array('value'=>'one_five','name'=>Yii::t("contract","one_five"),'percent'=>'10','show'=>'1'),//IB服务生意年金额
            array('value'=>'one_nine','name'=>Yii::t("contract","one_nine"),'percent'=>'5','show'=>'1'),//新（IA+IB）服务年金额
            array('value'=>'one_six','name'=>Yii::t("contract","one_six"),'pro_str'=>"%",'percent'=>'10','show'=>'1'),//收款率(%)
            array('value'=>'one_seven','name'=>Yii::t("contract","one_seven"),'pro_str'=>"%",'percent'=>'5','show'=>'1'),//服务单的停单比例(%)
            array('value'=>'one_eight','name'=>Yii::t("contract","one_eight"),'percent'=>'5','show'=>'1')//技术员每月平均生产力
        );
    }

    //自由配置A項
    public function resetListX($list){
        if(is_array($list)&&key_exists("bossA",$list)){
            $this->listX = $list['bossA'];
        }
    }

    //自由配置A項(城市默認)
    public function cityListX(){
        $row = Yii::app()->db->createCommand()->select("json_text,num_ratio")->from("hr_boss_set_a")
            ->where('tacitly=1 or city=:city ', array(':city'=>$this->city))->order("tacitly asc")->queryRow();
        if($row){
            $this->listX = array();
            $this->ratio_a = $row["num_ratio"];
            if(!empty($this->model)){
                $this->model->ratio_a=$this->ratio_a;
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
            array('value'=>'one_1','name'=>($this->audit_year-1).Yii::t("contract","one_1"),'function'=>"getOldYear","width"=>"120px",'pro_str'=>"%","emailBool"=>false),//2018年度数据
            array('value'=>'one_2','name'=>($this->audit_year-1).Yii::t("contract","one_2"),'function'=>"getOldAgoYear","width"=>"160px",'static_str'=>"%"),//2018年度增长百分比
            array('value'=>'one_3','name'=>Yii::t("contract","one_12").$this->audit_year.Yii::t("contract","one_3"),'function'=>"getPlanYear",'validate'=>true,"width"=>"160px",'pro_str'=>"%","emailBool"=>true),//预计2019年目标数据
            array('value'=>'one_4','name'=>Yii::t("contract","one_4"),'function'=>"getPlanYearRate","width"=>"120px",'static_str'=>"%","emailBool"=>true),//预计增长百分比
            array('value'=>'one_5','name'=>Yii::t("contract","one_5"),'function'=>"getPlanYearCof","width"=>"100px"),//系数
            array('value'=>'one_6','name'=>$this->audit_year.Yii::t("contract","one_6"),'function'=>"getNowYear","width"=>"160px",'pro_str'=>"%","emailBool"=>true),//2019年实际达成数据
            array('value'=>'one_7','name'=>Yii::t("contract","one_7"),'function'=>"getNowYearRate","width"=>"160px",'static_str'=>"%","emailBool"=>true),//实际达成百分比
            array('value'=>'one_8','name'=>Yii::t("contract","one_8"),'function'=>"getLadderDiffer","width"=>"100px"),//阶梯落差
            array('value'=>'one_9','name'=>Yii::t("contract","one_9"),'function'=>"getLadderCof","width"=>"100px"),//落差系数
            array('value'=>'one_10','name'=>Yii::t("contract","one_10"),'function'=>"getNowCof","width"=>"100px"),//实际系数
            array('value'=>'one_11','name'=>Yii::t("contract","one_11"),'function'=>"getSumRate","width"=>"100px",'static_str'=>"%"),//占比（%）
            array('value'=>'one_12','name'=>Yii::t("contract","three_four"),'function'=>"getSumNumber","width"=>"100px",'static_str'=>"%"),//得分
            array('value'=>'one_13','name'=>Yii::t("contract","Remark"),'function'=>"getRemark","width"=>"160px")//备注
        );
    }

    //2018年度数据 - one_1
    public function getOldYear($type,$str,$list=array()){
        switch ($type){
            case "one_one"://年生意额增长目标
                $this->json_text[$type][$str] = $this->value($this->city,$this->audit_year-1,"00002");
                return array('value'=>$this->json_text[$type][$str],'name'=>$this->json_text[$type][$str]);
            case "one_two"://年利润额增长目标
                $this->json_text[$type][$str] = $this->value($this->city,$this->audit_year-1,"00067");
                return array('value'=>$this->json_text[$type][$str],'name'=>$this->json_text[$type][$str]);
            case "one_three"://年新业务生意额目标
                $this->json_text[$type][$str] = $this->valueToOp($this->city,$this->audit_year-1);
                return array('value'=>$this->json_text[$type][$str],'name'=>$this->json_text[$type][$str]);
            case "one_four"://IA服务生意年金额
                $this->json_text[$type][$str] = $this->value($this->city,$this->audit_year-1,"00003");
                return array('value'=>$this->json_text[$type][$str],'name'=>$this->json_text[$type][$str]);
            case "one_five"://IB服务生意年金额
                $this->json_text[$type][$str] = $this->value($this->city,$this->audit_year-1,"00004");
                return array('value'=>$this->json_text[$type][$str],'name'=>$this->json_text[$type][$str]);
            case "one_nine"://新（IA+IB）服務年金額
                $this->json_text[$type][$str] = $this->value($this->city,$this->audit_year-1,"00006");
                return array('value'=>$this->json_text[$type][$str],'name'=>$this->json_text[$type][$str]);
            case "one_six"://收款率(%)
                $this->json_text[$type][$str] = $this->valueOnToRate($this->city,$this->audit_year-1);;//今月收款額/上個月的生意額
                return array('value'=>$this->json_text[$type][$str],'name'=>$this->json_text[$type][$str]."%");
            case "one_seven"://服务单的停单比例(%)
                $this->json_text[$type][$str] = $this->valueStopToRate($this->city,$this->audit_year-1);//今月停單/今月的生意額
                return array('value'=>$this->json_text[$type][$str],'name'=>$this->json_text[$type][$str]."%");
            case "one_eight"://技术员每月平均生产力
                $this->json_text[$type][$str] = $this->valueAverage($this->city,$this->audit_year-1);
                return array('value'=>$this->json_text[$type][$str],'name'=>$this->json_text[$type][$str]);
        }
    }
    //2018年度增长百分比 - one_2
    public function getOldAgoYear($type,$str,$list=array()){
        switch ($type){
            case "one_one"://年生意额增长目标
                $value = $this->value($this->city,$this->audit_year-2,"00002");
                $value = floatval($value);
                $value = empty($value)?0:($this->json_text[$type]["one_1"]-$value)/$value;
                $this->json_text[$type][$str] = floatval(sprintf("%.4f",$value))*100;
                return array('value'=>$this->json_text[$type][$str],'name'=>$this->json_text[$type][$str]."%");
            case "one_two"://年利润额增长目标
                $value = $this->value($this->city,$this->audit_year-2,"00067");
                $value = floatval($value);
                $value = empty($value)?0:($this->json_text[$type]["one_1"]-$value)/abs($value);
                $this->json_text[$type][$str] = floatval(sprintf("%.4f",$value))*100;
                return array('value'=>$this->json_text[$type][$str],'name'=>$this->json_text[$type][$str]."%");
            case "one_three"://年新业务生意额目标
                $value = $this->valueToOp($this->city,$this->audit_year-2);
                $value = floatval($value);
                $value = empty($value)?0:($this->json_text[$type]["one_1"]-$value)/$value;
                $this->json_text[$type][$str] = floatval(sprintf("%.4f",$value))*100;
                return array('value'=>$this->json_text[$type][$str],'name'=>$this->json_text[$type][$str]."%");
            case "one_four"://IA服务生意年金额
                $value = $this->value($this->city,$this->audit_year-2,"00003");
                $value = floatval($value);
                $value = empty($value)?0:($this->json_text[$type]["one_1"]-$value)/$value;
                $this->json_text[$type][$str] = floatval(sprintf("%.4f",$value))*100;
                return array('value'=>$this->json_text[$type][$str],'name'=>$this->json_text[$type][$str]."%");
            case "one_five"://IB服务生意年金额
                $value = $this->value($this->city,$this->audit_year-2,"00004");
                $value = floatval($value);
                $value = empty($value)?0:($this->json_text[$type]["one_1"]-$value)/$value;
                $this->json_text[$type][$str] = floatval(sprintf("%.4f",$value))*100;
                return array('value'=>$this->json_text[$type][$str],'name'=>$this->json_text[$type][$str]."%");
            case "one_nine"://新（IA+IB）服務年金額
                $value = $this->value($this->city,$this->audit_year-2,"00006");
                $value = floatval($value);
                $value = empty($value)?0:($this->json_text[$type]["one_1"]-$value)/$value;
                $this->json_text[$type][$str] = floatval(sprintf("%.4f",$value))*100;
                return array('value'=>$this->json_text[$type][$str],'name'=>$this->json_text[$type][$str]."%");
            case "one_six"://收款率(%)
                $this->json_text[$type][$str] = "\\";
                return array('value'=>$this->json_text[$type][$str],'name'=>"\\");
            case "one_seven"://服务单的停单比例(%)
                $this->json_text[$type][$str] = "\\";
                return array('value'=>$this->json_text[$type][$str],'name'=>"\\");
            case "one_eight"://技术员每月平均生产力
                $this->json_text[$type][$str] = "\\";
                return array('value'=>$this->json_text[$type][$str],'name'=>"\\");
        }
    }
    //预计2019年目标数据 - one_3
    public function getPlanYear($type,$str,$list=array()){
        $value = isset($this->json_text[$type][$str])?$this->json_text[$type][$str]:"";
        $name = $this->className."[json_text][".$type."]"."[".$str."]";
        $ready = $this->ready?"readonly":"";
        if($value === ""){
            $html ="<input type='number' name='$name' value='' data-name='$type' $ready class='form-control planYearA'/>";
        }else{
            $html ="<input type='number' name='$name' value='$value' data-name='$type' $ready class='form-control planYearA'/>";
        }
        if(in_array($type,array("one_six","one_seven"))){
            $html="<div class='input-group'>$html<span class='input-group-addon'>%</span></div>";
        }

        $this->json_text[$type][$str] = $value;
        return array('value'=>$this->json_text[$type][$str],'name'=>$html);
    }
    //预计增长百分比 - one_4
    public function getPlanYearRate($type,$str,$list=array()){
        if(in_array($type,array("one_one","one_two","one_three","one_four","one_five","one_nine"))){
            $value = floatval($this->json_text[$type]["one_1"]);
            if($type == "one_two"){
                $value = empty($value)?0:($this->json_text[$type]["one_3"]-$value)/abs($value);
            }else{
                $value = empty($value)?0:($this->json_text[$type]["one_3"]-$value)/$value;
            }
            $value = round($value*100);
            $name = $this->className."[json_text][".$type."]"."[".$str."]";
            $html ="<input readonly type='text' name='$name' value='$value%' class='form-control planYearRate'/>";

            $this->json_text[$type][$str] = $value;
            return array('value'=>$this->json_text[$type][$str],'name'=>$html);
        }else{
            $this->json_text[$type][$str] = "\\";
            return array('value'=>$this->json_text[$type][$str],'name'=>"\\");
        }
    }
    //系数 - one_5
    public function getPlanYearCof($type,$str,$list=array()){
        if(in_array($type,array("one_six","one_seven","one_eight","one_nine"))){
            $value = $this->json_text[$type]["one_3"];
        }else{
            $value = $this->json_text[$type]["one_4"];
        }
        $value = $this->cofModel->getClassCof($value,$this->countPrice,$type);//系數
        $kpiData = $this->cofModel->getKPIListStr();
        $kpiType = $this->cofModel->size_type;
        $name = $this->className."[json_text][".$type."]"."[".$str."]";
        $html ="<input readonly type='text' name='$name' value='$value' data-name='$type' data-kpi='$kpiData' data-size='$kpiType' class='form-control planYearCof changeCofWindow'/>";

        $this->json_text[$type][$str] = $value;
        return array('value'=>$this->json_text[$type][$str],'name'=>$html);
    }
    //2019年实际达成数据 - one_6
    public function getNowYear($type,$str,$list=array()){
        switch ($type){
            case "one_one"://年生意额增长目标
                $this->json_text[$type][$str] = $this->value($this->city,$this->audit_year,"00002");
                return array('value'=>$this->json_text[$type][$str],'name'=>$this->json_text[$type][$str]);
            case "one_two"://年利润额增长目标
                $this->json_text[$type][$str] = $this->value($this->city,$this->audit_year,"00067");
                return array('value'=>$this->json_text[$type][$str],'name'=>$this->json_text[$type][$str]);
            case "one_three"://年新业务生意额目标
                $this->json_text[$type][$str] = $this->valueToOp($this->city,$this->audit_year);
                return array('value'=>$this->json_text[$type][$str],'name'=>$this->json_text[$type][$str]);
            case "one_four"://IA服务生意年金额
                $this->json_text[$type][$str] = $this->value($this->city,$this->audit_year,"00003");
                return array('value'=>$this->json_text[$type][$str],'name'=>$this->json_text[$type][$str]);
            case "one_five"://IB服务生意年金额
                $this->json_text[$type][$str] = $this->value($this->city,$this->audit_year,"00004");
                return array('value'=>$this->json_text[$type][$str],'name'=>$this->json_text[$type][$str]);
            case "one_nine"://新（IA+IB）服務年金額
                $this->json_text[$type][$str] = $this->value($this->city,$this->audit_year,"00006");
                return array('value'=>$this->json_text[$type][$str],'name'=>$this->json_text[$type][$str]);
            case "one_six"://收款率(%)
                $this->json_text[$type][$str] = $this->valueOnToRate($this->city,$this->audit_year);;//今月收款額/上個月的生意額
                return array('value'=>$this->json_text[$type][$str],'name'=>$this->json_text[$type][$str]."%");
            case "one_seven"://服务单的停单比例(%)
                $this->json_text[$type][$str] = $this->valueStopToRate($this->city,$this->audit_year);//今月停單/今月的生意額
                return array('value'=>$this->json_text[$type][$str],'name'=>$this->json_text[$type][$str]."%");
            case "one_eight"://技术员每月平均生产力
                $this->json_text[$type][$str] = $this->valueAverage($this->city,$this->audit_year,"00018");
                return array('value'=>$this->json_text[$type][$str],'name'=>$this->json_text[$type][$str]);
        }
    }
    //实际达成百分比 - one_7
    public function getNowYearRate($type,$str,$list=array()){
        if(in_array($type,array("one_one","one_two","one_three","one_four","one_five"))){
            //var_dump($this->json_text[$type]["one_1"]);die();
            $rate = floatval($this->json_text[$type]["one_1"]);
            if($type == "one_two"){
                $rate = empty($rate)?0:($this->json_text[$type]["one_6"]-$rate)/abs($rate);
            }else{
                $rate = empty($rate)?0:($this->json_text[$type]["one_6"]-$rate)/$rate;
            }
            $this->json_text[$type][$str] = floatval(sprintf("%.2f",$rate))*100;
            return array('value'=>$this->json_text[$type][$str],'name'=>$this->json_text[$type][$str]."%");
        }else{
            $this->json_text[$type][$str] = "\\";
            return array('value'=>$this->json_text[$type][$str],'name'=>"\\");
        }
    }
    //阶梯落差 - one_8
    public function getLadderDiffer($type,$str,$list=array()){
        if(in_array($type,array("one_one","one_two","one_three","one_four","one_five"))){
            $cofNow = $this->cofModel->getClassCof($this->json_text[$type]["one_7"],$this->countPrice,$type);
            $value = $this->cofModel->getClassLadder($this->json_text[$type]["one_5"],$cofNow,$type,$this->countPrice);
        }else{
            $cofNow = $this->cofModel->getClassCof($this->json_text[$type]["one_6"],$this->countPrice,$type);
            $value = $this->cofModel->getClassLadder($this->json_text[$type]["one_5"],$cofNow,$type,$this->countPrice);
        }
        $name1 = $this->className."[json_text][".$type."]"."[".$str."]";
        $name2 = $this->className."[json_text][".$type."]"."[cofNow]";
        $html = "<input type='hidden' name='$name1' value='$value'><input type='hidden' name='$name2' value='$cofNow'>"."<span>".abs($value)."</span>";
        $this->json_text[$type]["cofNow"] = $cofNow;
        $this->json_text[$type][$str] = $value;
        return array('value'=>$this->json_text[$type][$str],'name'=>$html);
    }
    //落差系数 - one_9
    public function getLadderCof($type,$str,$list=array()){
        $value = $this->json_text[$type]["one_8"];
        $value = $value>0?$value*0.03:$value*0.08;
        //$value += $this->json_text[$type]["one_5"];
        $this->json_text[$type][$str] = $value;
        return array('value'=>$this->json_text[$type][$str],'name'=>$value);
    }
    //实际系数 - one_10
    public function getNowCof($type,$str,$list=array()){
        $value = $this->json_text[$type]["one_5"]+$this->json_text[$type]["one_9"];
        $value = $value<=0?0:$value;
        $this->json_text[$type][$str] = $value;
        return array('value'=>$this->json_text[$type][$str],'name'=>$value);
    }
    //占比（%） - one_11
    public function getSumRate($type,$str,$list=array()){
        $value = $list["percent"];
        $this->json_text[$type][$str] = $value;
        return array('value'=>$this->json_text[$type][$str],'name'=>$value."%");
    }
    //得分 - one_12
    public function getSumNumber($type,$str,$list=array()){
        $value = $this->json_text[$type]["one_10"]*$this->json_text[$type]["one_11"];
        $value = floatval(sprintf("%.3f",$value));
        $this->json_text[$type][$str] = $value;
        $this->scoreSum +=$value;
        return array('value'=>$this->json_text[$type][$str],'name'=>$value."%");
    }

    //邮件专用
    public function getEveryOrNowNumber($type,$str="every"){
        $value = $this->json_text[$type]["one_3"];
        switch ($str){
            case "complete"://目标实际完成 = XX年实际达成数据÷预计XX年目标数据
                $value=empty($value)?0:$this->json_text[$type]["one_6"]/$value;
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

    //备注
    public function getRemark($type,$str,$list=array()){
        $value = isset($this->json_text[$type][$str])?$this->json_text[$type][$str]:"";
        $name = $this->className."[json_text][".$type."]"."[".$str."]";
        $ready = $this->ready?"readonly":"";
        $html ="<textarea name='$name' class='form-control' $ready >$value</textarea><input type='hidden'>";

        $this->json_text[$type][$str] = $value;
        return array('value'=>$this->json_text[$type][$str],'name'=>$html);
    }
}