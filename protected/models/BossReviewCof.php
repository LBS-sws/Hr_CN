<?php

/**
 * 等級系數
 * User: 沈超
 * Date: 2020/6/17
 * Time: 13:42
 */
class BossReviewCof
{
    public $class_id='';//
    public $city='';//
    public $size_type=0;//
    public $kpi_list=array();

    public function __construct($class_id='')
    {
        $this->class_id=$class_id;
    }

    public function getClassLadder($cofOld,$cofNow,$class_id,$price=''){
        if(!is_numeric($cofOld)||!is_numeric($cofNow)){
            return 0;
        }
        $kpiRow = Yii::app()->db->createCommand()->select("id,kpi_name,size_type,sum_bool")->from("hr_kpi")
            ->where("kpi_name=:kpi_name and (city=:city or tacitly = 1)",array(":kpi_name"=>$class_id,":city"=>$this->city))
            ->order("tacitly asc")->queryRow();
        if($kpiRow){
            $kpiList = $this->getKPIList($kpiRow,$price);
            $keyOld = 0;
            $keyNow = 0;
            if(is_array($kpiList)){
                $kpiList = $this->sortList($kpiList);
                foreach ($kpiList as $key =>$list){
                    if(floatval($list["kpi_value"])==$cofOld){
                        $keyOld = $key;
                    }
                    if(floatval($list["kpi_value"])==$cofNow){
                        $keyNow = $key;
                    }
                }
            }
            return $keyNow-$keyOld;
        }
        return 0;
    }

    private function sortList($kpiList){
        $arr = array();
        $valueList = array_column($kpiList,"kpi_value");
        asort($valueList);
        foreach ($valueList as $key =>$value){
            $arr[] = $kpiList[$key];
        }
        return $arr;
    }

    public function getClassCof($value,$price='',$class_id=''){
        if(!is_numeric($value)||$value ===""){
            $value = 0;
        }
        $kpiRow = Yii::app()->db->createCommand()->select("id,kpi_name,size_type,sum_bool")->from("hr_kpi")
            ->where("kpi_name=:kpi_name and (city=:city or tacitly = 1)",array(":kpi_name"=>$class_id,":city"=>$this->city))
            ->order("tacitly asc")->queryRow();
        if($kpiRow){
            $kpiList = $this->getKPIList($kpiRow,$price);
            $this->size_type = $kpiRow["size_type"];
            $this->kpi_list = $kpiList;
            if($kpiRow["size_type"]==1){
                return $this->foreachListToMax($kpiList,$value,'min_num','kpi_value');
            }else{
                return $this->foreachListToMin($kpiList,$value,'min_num','kpi_value');
            }
        }
        return 0;
    }

    public function getKPIListStr(){
        $str = "";
        if(is_array($this->kpi_list)){
            //$kpiList = $this->sortList($this->kpi_list);
            foreach ($this->kpi_list as $list){
                $str.=empty($str)?"":",";
                $str.=floatval($list["min_num"]).":".floatval($list["kpi_value"]);
            }
        }
        return $str;
    }

    protected function getKPIList($row,$price=''){
        $orderType = $row["size_type"]==1?"desc":"asc";
        if($row["sum_bool"] == 1){//複雜（需要生意額)
            $sumRow = Yii::app()->db->createCommand()->select("id,min_sum,other_bool")->from("hr_kpi_sum")
                ->where("kpi_id=:kpi_id",array(":kpi_id"=>$row['id']))->order("other_bool asc,min_sum asc")->queryAll();
            $sum_id = $this->foreachListToMin($sumRow,$price,'min_sum','id');
            return Yii::app()->db->createCommand()->select("id,min_num,kpi_value,other_bool")->from("hr_kpi_min")
                ->where("kpi_id=:kpi_id and sum_id=:sum_id",array(":kpi_id"=>$row['id'],":sum_id"=>$sum_id))
                ->order("other_bool asc,min_num $orderType")->queryAll();
        }else{
            return Yii::app()->db->createCommand()->select("id,min_num,kpi_value,other_bool")->from("hr_kpi_min")
                ->where("kpi_id=:kpi_id",array(":kpi_id"=>$row['id']))
                ->order("other_bool asc,min_num $orderType")->queryAll();
        }
    }

    protected function foreachListToMin($list,$min,$searchStr,$returnStr){
        $min = (!is_numeric($min)||$min === "")?0:floatval($min);
        $value = 0;
        if(is_array($list)){
            foreach ($list as $row){
                if($row["other_bool"]==1){
                    $value = floatval($row[$returnStr]);
                }else{
                    if($min<=floatval($row[$searchStr])){
                        return floatval($row[$returnStr]);
                    }
                }
            }
        }
        return $value;
    }

    protected function foreachListToMax($list,$min,$searchStr,$returnStr){
        $min = (!is_numeric($min)||$min === "")?0:floatval($min);
        $value = 0;
        if(is_array($list)){
            foreach ($list as $row){
                if($row["other_bool"]==1){
                    $value = floatval($row[$returnStr]);
                }else{
                    if($min>=floatval($row[$searchStr])){
                        return floatval($row[$returnStr]);
                    }
                }
            }
        }
        return $value;
    }

}