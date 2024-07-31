<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 * Date: 2020/6/15
 * Time: 13:42
 */
class BossReviewC
{
    public $ready=true;//禁止用戶修改
    public $className="";//表單的name前綴
    public $audit_year;//考核年限
    public $search_month=0;//查詢的月份（0:所有月份）
    public $status_type;//表單狀態
    public $listX=array();
    public $listY=array();
    public $json_text=array();
    public $scoreSum=0;
    public $ratio_c=15;//占比
    protected $searchBool = false;
    private $detailList=array();//报表专用

    public function __construct($model='',$searchBool=false)
    {
        if(!empty($model)){
            $this->ratio_c = $model->ratio_c;
            $this->json_text = $model->json_text;
            $this->status_type = isset($model->status_type)?$model->status_type:0;//表單狀態
            $this->audit_year = $model->audit_year;
            $this->ready = $model->getInputBool();
            $this->className = get_class($model);
        }
        $this->searchBool = $searchBool;
        $this->setListX();
        $this->setListY();
        if(!key_exists("three",$this->json_text)){
            $this->json_text["three"] = array("list"=>array(),'count'=>'','sum'=>'');
        }
    }

    protected function setListX(){
        $this->listX = array(
            array('value'=>'three_one','name'=>Yii::t("contract","three_one")),
            array('value'=>'three_two','name'=>Yii::t("contract","three_two")),
            array('value'=>'three_three','name'=>Yii::t("contract","three_three")),
            array('value'=>'three_four','name'=>Yii::t("contract","three_four"))
        );
    }

    protected function setListY(){
        $this->listY = array();
    }

    public function resetListX($list){
        return;
    }

    public function validateJson(&$model,$bool=true){
        $sum = 0;
        if(!key_exists("three",$this->json_text)){
            $message = Yii::t('contract',"(C)Optional project section")." - ".Yii::t('contract',' can not be empty');
            $model->addError('json_text',$message);
            return false;
        }else{
            if($bool&&(empty($this->json_text["three"]['count'])||!is_numeric($this->json_text["three"]['count']))){
                $message = Yii::t('contract',"three_three")." - ".Yii::t('contract',' can not be empty');
                $model->addError('json_text',$message);
                return false;
            }
            if(isset($this->json_text["three"]['list'])){
                if($bool&&count($this->json_text["three"]['list'])<3){
                    $message = Yii::t('contract',"(C)Optional project section")." - 至少填写三条";
                    $model->addError('json_text',$message);
                    return false;
                }
                foreach ($this->json_text["three"]['list'] as $row){
                    if($bool&&(!isset($row['three_four'])||$row['three_four']===""||!is_numeric($row['three_four']))){
                        $message = Yii::t('contract',"three_four")." - ".Yii::t('contract',' can not be empty');
                        $model->addError('json_text',$message);
                        return false;
                    }else{
                        $sum+=empty($row['three_four'])?0:floatval($row['three_four']);
                    }
                }
            }else{
                if($bool){
                    $message = Yii::t('contract',"(C)Optional project section")." - ".Yii::t('contract',' can not be empty');
                    $model->addError('json_text',$message);
                    return false;
                }
            }
            $sum = empty($this->json_text["three"]['count'])?0:($sum/$this->json_text["three"]['count'])*$this->ratio_c;
            $sum = floatval(sprintf("%.2f",$sum));
            $this->json_text["three"]['sum'] = $sum;
            if($this->json_text["three"]['sum']>$this->ratio_c){
                $message = Yii::t('contract',"(C)Optional project section")." - 不能大于$this->ratio_c";
                $model->addError('json_text',$message);
                return false;
            }
        }
        $this->scoreSum = $sum;
    }

    public function getTableHtmlToEmail(){
        $width="170px";
        $html="";
        $html.="<thead><tr>";
        foreach ($this->listX as $item){
            $html.="<th width='$width'>".$item["name"]."</th>";
            $this->detailList["title"][]=$item["name"];
        }
        $html.="</tr></thead>";
        if(isset($this->json_text["three"]["list"])){
            $html.="<tbody>";
            foreach ($this->json_text["three"]["list"] as $key =>$list){
                $html.="<tr>";
                foreach ($this->listX as $item){
                    $html.="<td width='$width'>".$list[$item["value"]]."</td>";
                    $this->detailList["{$key}_01"][]=$list[$item["value"]];
                }
                $html.="</tr>";
            }
            $html.="</tbody>";
            $html.="<tfoot><tr><td colspan='3' style='text-align: right'>";
            $html.=$this->json_text["three"]["count"]."</td>";
            $html.="<td>".$this->json_text["three"]["sum"]."</td>";
            $html.="</tr></tfoot>";
            $this->detailList["footer"][]=$this->json_text["three"]["count"];
            $this->detailList["footer"][]=$this->json_text["three"]["sum"];
        }
        return $html;
    }

    public function getTableHtml(){
        $html="<p>&nbsp;</p><div class='col-lg-12'>";
        $html.="<p><b>".Yii::t("contract","table_remark_0")."</b></p>";
        $html.="<p>".Yii::t("contract","table_remark_1")."</p>";
        $html.="<p>".Yii::t("contract","table_remark_2")."</p>";
        $html.="<p>".Yii::t("contract","table_remark_3")."</p>";
        $html.="<p>".Yii::t("contract","table_remark_4")."</p>";
        $html.="<p>".Yii::t("contract","table_remark_5")."</p>";
        $html.="</div>";
        $html.="<div class='form-group'><div class='col-lg-12'><table id='table_three' class='table table-bordered table-striped'>";
        $html.="<thead><tr>";
        $html.="<th width='28%'>".Yii::t("contract","three_one")."</th>";
        $html.="<th width='28%'>".Yii::t("contract","three_two")."</th>";
        $html.="<th width='28%'>".Yii::t("contract","three_three")."</th>";
        $html.="<th width='16%'>".Yii::t("contract","three_four");
        $html.="<input type='hidden' name='down[BossReviewC][title][]' value='".Yii::t("contract","three_one")."'>";
        $html.="<input type='hidden' name='down[BossReviewC][title][]' value='".Yii::t("contract","three_two")."'>";
        $html.="<input type='hidden' name='down[BossReviewC][title][]' value='".Yii::t("contract","three_three")."'>";
        $html.="<input type='hidden' name='down[BossReviewC][title][]' value='".Yii::t("contract","three_four")."'>";
        $html.="</th>";
        if(!$this->ready){
            $html.="<th width='1%'>".Yii::t("contract","Operation")."</th>";
        }
        $html.="</tr></thead><tbody data-num=':rowCount:'>";

        if(isset($this->json_text["three"]["list"])){
            foreach ($this->json_text["three"]["list"] as $key =>$list){
                $html.=$this->getRowHtml($this->ready,$list,$key);
            }
            $key = isset($key)?$key:1;
            $html = str_replace(":rowCount:",$key,$html);
        }

        $html.=$this->getRowHtml($this->ready);
        $html.="</tbody>";

        $html.="<tfoot><tr><td colspan='2'>&nbsp;</td>";
        $html.="<td><div class='input-group'>";
        $html.=TbHtml::numberField($this->className."[json_text][three][count]",$this->json_text["three"]["count"],array('readonly'=>$this->ready,'id'=>"three_count"));
        $html.="<span class='input-group-addon'>%</span></div></td>";
        $html.="<td><div class='input-group'>";
        $html.=TbHtml::numberField($this->className."[json_text][three][sum]",$this->json_text["three"]["sum"],array('readonly'=>true,'id'=>"three_sum"));
        $html.="<input type='hidden' name='down[BossReviewC][count][]' value='{$this->json_text["three"]["count"]}%'>";
        $html.="<input type='hidden' name='down[BossReviewC][count][]' value='{$this->json_text["three"]["sum"]}%'>";
        $html.="<span class='input-group-addon'>%</span></div></td>";
        if(!$this->ready){
            $html.="<td>".TbHtml::button(Yii::t("misc","Add"),array("class"=>"btn btn-primary","id"=>"addRow"))."</td>";
        }
        $html.="</tr></tfoot>";
        if(!$this->ready){
        }
        $html.="</table></div></div>";
        return $html;
    }


    protected function getRowHtml($ready=false,$list=array(),$num=''){
        if(empty($list)){
            $list = array('three_one'=>'','three_two'=>'','three_three'=>'','three_four'=>0);
        }
        if($num===''||!is_numeric($num)){
            $html="<tr id='trTemplate' style='display: none'>";
            $name = ":inputName:";
        }else{
            $html="<tr>";
            $name = $this->className."[json_text][three][list][$num]";
        }

        $html.="<td>".TbHtml::textArea($name."[three_one]",$list["three_one"],array('readonly'=>$ready))."</td>";
        if($this->status_type == 4&&$this->className == "BossApplyForm"){
            $html.="<td>".TbHtml::textArea($name."[three_two]",$list["three_two"],array('readonly'=>false))."</td>";
        }else{
            $html.="<td>".TbHtml::textArea($name."[three_two]",$list["three_two"],array('readonly'=>$ready))."</td>";
        }
        $html.="<td>".TbHtml::textArea($name."[three_three]",$list["three_three"],array('readonly'=>$ready))."</td>";
        $html.="<td><div class='input-group'>";
        if($this->status_type == 4&&$this->className == "BossApplyForm"){
            $html.=TbHtml::numberField($name."[three_four]",$list["three_four"],array('readonly'=>false,'class'=>'changeThreeFour'));
        }else{
            $html.=TbHtml::numberField($name."[three_four]",$list["three_four"],array('readonly'=>$ready,'class'=>'changeThreeFour'));
        }
        $html.="<span class='input-group-addon'>%</span></div>";
        if($name != ":inputName:"){
            $html.="<input type='hidden' name='down[BossReviewC][$num][]' value='{$list["three_one"]} '>";
            $html.="<input type='hidden' name='down[BossReviewC][$num][]' value='{$list["three_two"]} '>";
            $html.="<input type='hidden' name='down[BossReviewC][$num][]' value='{$list["three_three"]} '>";
            $html.="<input type='hidden' name='down[BossReviewC][$num][]' value='{$list["three_four"]}%'>";
        }
        $html.="</td>";

        if(!$this->ready){
            $html.="<td>".TbHtml::button(Yii::t("misc","Delete"),array("class"=>"btn btn-default deleteRow"))."</td>";
        }
        $html.="</tr>";

        return $html;
    }

    public function getDetailList(){
        return $this->detailList;
    }
}