<?php

class BossKPIForm extends CFormModel
{
	public $id;
	public $kpi_name;
	public $rate_type;
    public $tacitly;
    public $city;
	public $size_type=0;//大小判斷  0：小於等於  1：大於等於
	public $sum_bool=0;//是否開啟金額分類

	public $json_one;//不開啟金額分類
	public $json_two;//開啟金額分類
	protected $kpi_value;//開啟金額分類
    protected $kpi_str;//開啟金額分類

	public function attributeLabels()
	{
		return array(
            'kpi_name'=>Yii::t('contract','kpi name'),
            'size_type'=>Yii::t('contract','kpi type'),
            'sum_bool'=>Yii::t('contract','kpi sum bool'),
            'tacitly'=>Yii::t("contract","tacitly"),
            'city'=>Yii::t('contract','City')
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id,size_type, sum_bool,json_one,json_two,city,tacitly','safe'),
            array('size_type','required'),
            array('sum_bool','required'),
            array('id','validateID'),
            array('city','validateCity'),
            array('tacitly','validateTacitly'),
            array('json_one','validateJsonOne'),
            array('json_two','validateJsonTwo'),
		);
	}

    public function validateCity($attribute, $params){
        $city = $this->city;
        $rows = Yii::app()->db->createCommand()->select()->from("hr_kpi")
            ->where('id!=:id and city=:city and kpi_name=:kpi_name ', array(':id'=>$this->id,':city'=>$city,':kpi_name'=>$this->kpi_value))->queryAll();
        if (count($rows) > 0){
            $message = "该城市已设置".$this->kpi_name."，不需要重复设置";
            $this->addError($attribute,$message);
        }
    }

	public function validateTacitly($attribute, $params){
	    if($this->tacitly!=1){
	        $id = $this->getScenario()=="copy"?"":$this->id;
            $row = Yii::app()->db->createCommand()->select("id")->from("hr_kpi")
                ->where("id!=:id and tacitly = 1 and kpi_name=:kpi_name",array(":id"=>$id,":kpi_name"=>$this->kpi_value))->queryRow();
            if(!$row){
                $message = $this->kpi_name."必须要设定一个默认值";
                $this->addError('kpi_name',$message);
            }
        }
	}

	public function validateJsonOne($attribute, $params){
	    if($this->sum_bool != 1){
	        $arr =array();
            $bool = $this->validateAndSetList($this->json_one,$arr);
            if($bool){
                $this->json_one = $arr;
            }
        }
	}

	public function validateJsonTwo($attribute, $params){
	    if($this->sum_bool == 1){
            $arr =array();
            $key = 0;
            foreach ($this->json_two as $item){
                $key++;
                if(key_exists("other_bool",$item)&&$item["other_bool"] == 1){
                    $arr[$key]["min_sum"] = 0;
                    $arr[$key]["other_bool"] = 1;
                    $str = "(".Yii::t("contract","Other").")";
                }else{
                    //验证年生意额
                    if(!isset($item["min_sum"])||$item["min_sum"]===""||!is_numeric($item["min_sum"])){
                        $message = Yii::t('contract','kpi number'). Yii::t('contract',' Must be Numbers');
                        $this->addError($attribute,$message);
                        return false;
                    }else{
                        $arr[$key]["other_bool"] = 0;
                        $arr[$key]["min_sum"] = $item["min_sum"];
                        $str = "(".$item["min_sum"].")";
                    }
                }
                //验证kpi詳情
                if(key_exists("list",$item)){
                    $arr[$key]["list"] = array();
                    $bool = $this->validateAndSetList($item["list"],$arr[$key]["list"],$str);
                    if(!$bool){
                        return false;
                    }
                }
            }

            $this->json_two = $arr;
        }
	}

	protected function validateAndSetList($forList,&$arr,$str=''){
	    if(is_array($forList)){
	        $i = 0;
	        foreach ($forList as $list){
	            $i++;
                if(key_exists("other_bool",$list)&&$list["other_bool"] == 1){
                    $arr[$i]['min_num'] = 0;
                    $arr[$i]["other_bool"] = 1;
                }else{
                    if(!isset($list['min_num'])||$list['min_num']===""||!is_numeric($list['min_num'])){
                        $message = Yii::t('contract','Scope'). Yii::t('contract',' Must be Numbers').$str;
                        $this->addError('sum_bool',$message);
                        return false;
                    }else{
                        $arr[$i]['min_num'] = $list['min_num'];
                        $arr[$i]['other_bool'] = 0;
                    }
                }

                if(!isset($list["kpi_value"])||$list["kpi_value"]===""||!is_numeric($list["kpi_value"])){
                    $message = Yii::t('contract','kpi value'). Yii::t('contract',' Must be Numbers').$str;
                    $this->addError('sum_bool',$message);
                    return false;
                }else{
                    $arr[$i]["kpi_value"] = $list["kpi_value"];
                }
            }
        }
        return true;
    }


	public function validateID($attribute, $params){
        $city_allow = Yii::app()->user->city_allow();
        $rows = Yii::app()->db->createCommand()->select("kpi_name,kpi_str,rate_type")->from("hr_kpi")
            ->where("id=:id",array(":id"=>$this->id))->queryRow();
        if($rows){
            $this->kpi_name = Yii::t("contract",$rows['kpi_name']);;
            $this->kpi_value = $rows["kpi_name"];
            $this->kpi_str = $rows["kpi_str"];
            $this->rate_type = $rows["rate_type"];
        }else{
            $message = Yii::t('contract','kpi name'). Yii::t('contract',' not exist');
            $this->addError($attribute,$message);
        }
	}

	public function retrieveData($index) {
        $row = Yii::app()->db->createCommand()->select("id,kpi_name,kpi_str,rate_type,size_type,sum_bool,city,tacitly")->from("hr_kpi")
            ->where("id=:id",array(":id"=>$index))->queryRow();
		if ($row) {
            $this->id = $row['id'];
            $this->kpi_name = Yii::t("contract",$row['kpi_name']);
            $this->rate_type = $row['rate_type'];
            $this->tacitly = $row['tacitly'];
            $this->city = $row['city'];
            $this->size_type = $row['size_type'];
            $this->sum_bool = $row['sum_bool'];
            $orderType = $this->size_type==1?"desc":"asc";
            $this->json_two = array();
            $this->json_one = array();
            if($this->sum_bool == 1){
                $sumList = Yii::app()->db->createCommand()->select("id,min_sum,other_bool")->from("hr_kpi_sum")
                    ->where("kpi_id=:kpi_id",array(":kpi_id"=>$row["id"]))->order("other_bool asc,min_sum asc")->queryAll();
                if($sumList){
                    foreach ($sumList as $list){
                        if(!key_exists($list["id"],$this->json_two)){
                            $this->json_two[$list["id"]] = array();
                        }
                        $this->json_two[$list["id"]]["other_bool"] = $list["other_bool"];
                        $this->json_two[$list["id"]]["min_sum"] = $list["min_sum"];
                        $rows = Yii::app()->db->createCommand()->select("min_num,kpi_value,other_bool")->from("hr_kpi_min")
                            ->where("kpi_id=:kpi_id and sum_id=:sum_id",array(":kpi_id"=>$row["id"],":sum_id"=>$list["id"]))
                            ->order("other_bool asc,min_num $orderType")->queryAll();
                        $this->json_two[$list["id"]]["list"] = $rows?$rows:array();
                    }
                }
            }else{
                $rows = Yii::app()->db->createCommand()->select("min_num,kpi_value,other_bool")->from("hr_kpi_min")
                    ->where("kpi_id=:kpi_id",array(":kpi_id"=>$row["id"]))->order("other_bool asc,min_num $orderType")->queryAll();
                $this->json_one = $rows?$rows:array();
            }
            return true;
		}else{
		    return false;
        }
	}

	protected function getJsonOneConstRow($name,$list=array()){
	    $list = empty($list)?array("min_num"=>"","kpi_value"=>""):$list;
        $list["kpi_value"] = $list["kpi_value"]!==""?floatval($list["kpi_value"]):"";
        $list["min_num"] = (isset($list["min_num"])&&$list["min_num"]!=="")?floatval($list["min_num"]):"";
        $html='<tr class="otherValue">';
        $html.="<td class='text-center'>";
        $html.=Yii::t("contract","Other");
        $html.="</td>";
        $html.="<td>";
        $html.=TbHtml::hiddenField($name."[other_bool]",1);
        $html.=TbHtml::numberField($name."[kpi_value]",$list["kpi_value"],array("readonly"=>$this->getReadonly(),'class'=>'form-control'));
        $html.="</td>";

        if(!$this->getReadonly()){
            $html.="<td>&nbsp;</td>";
        }
        $html.="</tr>";
        return $html;
    }

	protected function getJsonOneRow($name,$list=array()){
	    $list = empty($list)?array("min_num"=>"","kpi_value"=>""):$list;
        $list["kpi_value"] = $list["kpi_value"]!==""?floatval($list["kpi_value"]):"";
        $list["min_num"] = $list["min_num"]!==""?floatval($list["min_num"]):"";
	    $sizeType = $this->size_type == 1?">=":"<=";
	    if($name === ":name:"){
            $html='<tr class="addReadyRow" style="display: none">';
        }else{
            $html='<tr>';
        }
        $html.="<td><div class='input-group'><span class='input-group-addon changeSize'>$sizeType</span>";
        $html.=TbHtml::numberField($name.'[min_num]',$list["min_num"],array("readonly"=>$this->getReadonly()));
        if($this->rate_type == 1){
            $html.="<span class='input-group-addon'>%</span>";
        }
        $html.="</div></td>";
        $html.="<td>";
        $html.=TbHtml::numberField($name."[kpi_value]",$list["kpi_value"],array("readonly"=>$this->getReadonly(),'class'=>'form-control'));
        $html.="</td>";

        if(!$this->getReadonly()){
            $html.="<td>".TbHtml::button(Yii::t('misc','Delete'), array('class'=>"btn btn-danger tableDel"))."</td>";
        }
        $html.="</tr>";
        return $html;
    }

    public function getJsonOneTable(){
	    return $this->getJsonTable($this->json_one,"json_one");
    }

    protected function getJsonTable($jsonList,$jsonStr){
	    $num = count($jsonList)+2;
        $html = "<div class='form-group'><div class='col-lg-6 col-lg-offset-2'><table class='table table-bordered table-striped' data-str='$jsonStr' data-num='$num'>";

        $html.="<thead><tr>";
        $html.="<th width='50%'>".Yii::t("contract","Scope")."</th>";
        $html.="<th width='50%'>".Yii::t("contract","kpi value")."</th>";
        if(!$this->getReadonly()){
            $html.="<th width='1%'>&nbsp;</th>";
        }
        $html.="</tr></thead><tbody>";
        $otherList = array();
        $key = 0;
        foreach ($jsonList as $list){
            if(key_exists("other_bool",$list)&&$list["other_bool"] ==1){
                $otherList = $list;
            }else{
                $html.=$this->getJsonOneRow("BossKPIForm[$jsonStr][$key]",$list);
            }
            $key++;
        }

        $html.=$this->getJsonOneConstRow("BossKPIForm[$jsonStr][$key]",$otherList);
        $html.=$this->getJsonOneRow(":name:");
        $html.="</tbody>";
        if(!$this->getReadonly()){
            $html.="<tfoot>";
            $html.="<tr><td colspan='2'>&nbsp;</td>";
            $html.="<td>".TbHtml::button(Yii::t('misc','Add'), array('class'=>"btn btn-primary tableAdd"))."</td>";
            $html.="</tfoot>";
        }
        $html.="</table></div></div>";
        return $html;
    }

    protected function getJsonTwoTableConstList($name,$num,$list=array()){
        $list = empty($list)?array('min_sum'=>'','other_bool'=>0,'list'=>array()):$list;
        $html = "<p>&nbsp;</p>";
        $html.=TbHtml::hiddenField("BossKPIForm[$name]"."[other_bool]",1);

        $html.=$this->getJsonTable($list['list'],"json_two][$num][list");
        return array(
            'label'=>Yii::t("contract","kpi number")." - ".Yii::t("contract","Other"),
            'content'=>$html,
            'active'=>false,
        );
    }

    protected function getJsonTwoTableList($name,$num,$label,$list=array(),$active=false){
        $list = empty($list)?array('min_sum'=>'','other_bool'=>0,'list'=>array()):$list;
        $html = "<p class='text-right'>";
        $html.=TbHtml::button(Yii::t('misc','Delete'), array('class'=>"btn btn-danger jsonTwoDel"));
        $html.="</p>";
        $html.="<div class='form-group'>";
        $html.=TbHtml::label(Yii::t("contract","kpi number"),'',array('class'=>"col-sm-2 control-label"));
        $html.="<div class='col-sm-2'>";
        $html.=TbHtml::numberField("BossKPIForm[".$name."][min_sum]",$list['min_sum'],array('readonly'=>$this->getReadonly(),'class'=>'changeSumKey'));
        $html.="</div>";
        $html.="</div>";

        $html.=$this->getJsonTable($list['list'],$name."][list");
        $html.="<div></div>";
        return array(
            'label'=>$label,
            'content'=>$html,
            'active'=>$active,
        );
    }

    public function getJsonTwoTable(){
        $tabs = array();
        $key = 0;
        $otherList=array();
	    foreach ($this->json_two as $item){
	        if(key_exists("other_bool",$item)&&$item["other_bool"] == 1){
	            $otherList = $item;
            }else{
                $key++;
                $item["min_sum"] = isset($item["min_sum"])?$item["min_sum"]:"";
                $label = Yii::t("contract","kpi number")." <= <span class='changeSum'>".$item["min_sum"]."</span>";
                $active = $key == 1?true:false;
                $name = "json_two][$key";
                $tabs[] = $this->getJsonTwoTableList($name,$key,$label,$item,$active);
            }
        }
        $key++;
        $name = "json_two][$key";
        $template = $this->getJsonTwoTableConstList($name,$key,$otherList);//其它
        $template["htmlOptions"]=array("id"=>"beforeLi");
        $tabs[] = $template;//其它
        $label = Yii::t("contract","kpi number")." <= <span class='changeSum'></span>";
        $template = $this->getJsonTwoTableList(":nameTwo:",":num:",$label);//模板
        $template["id"]="templateTable";
        $template["htmlOptions"]=array("class"=>"hide",'id'=>'templateTwo');
        $tabs[] = $template;//模板
        $tabs[] = array('label'=>Yii::t("misc","Add"),'id'=>'addJsonTwo','htmlOptions'=>array('class'=>'addJsonTwo'));//增加
        return $tabs;
    }

    public function getSizeTypeList(){
        return array("<=",">=");
    }

    public function getSumBoolList(){
        return array(
            Yii::t("misc","No"),
            Yii::t("misc","Yes"),
        );
    }

    //刪除驗證
    public function deleteValidate(){
        $kpiName = Yii::app()->db->createCommand()->select("kpi_name")->from("hr_kpi")
            ->where("id=:id and tacitly!=1",array(":id"=>$this->id))->queryScalar();
        if($kpiName){
            $row = Yii::app()->db->createCommand()->select("id")->from("hr_kpi")
                ->where("id!=:id and kpi_name=:kpi_name and tacitly=1",array(":id"=>$this->id,":kpi_name"=>$kpiName))->queryRow();
            if($row){
                return true;
            }
        }
        return false;//沒有配置不允許刪除
    }

	public function getReadonly(){
        if ($this->getScenario()=='view'){
            return true;//只读
        }else{
            return false;
        }
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
        if($this->tacitly == 1){
            Yii::app()->db->createCommand()->update('hr_kpi', array(
                'tacitly'=>0,
            ),'kpi_name=:kpi_name',array(":kpi_name"=>$this->kpi_value));
        }
        $uid = Yii::app()->user->id;
        switch ($this->scenario) {
            case 'edit':
                $connection->createCommand()->update('hr_kpi', array(
                    'tacitly'=>$this->tacitly,
                    'city'=>$this->city,
                    'size_type'=>$this->size_type,
                    'sum_bool'=>$this->sum_bool,
                    'luu'=>$uid
                ), 'id=:id', array(':id'=>$this->id));
                $connection->createCommand()->delete('hr_kpi_sum', 'kpi_id=:id', array(':id'=>$this->id));
                $connection->createCommand()->delete('hr_kpi_min', 'kpi_id=:id', array(':id'=>$this->id));
                if($this->sum_bool ==1){
                    foreach ($this->json_two as $item){
                        $connection->createCommand()->insert("hr_kpi_sum", array(
                            'kpi_id'=>$this->id,
                            'min_sum'=>$item['min_sum'],
                            'other_bool'=>$item['other_bool'],
                            'lcu'=>$uid
                        ));
                        $id =Yii::app()->db->getLastInsertID();
                        foreach ($item["list"] as $list){
                            $connection->createCommand()->insert("hr_kpi_min", array(
                                'kpi_id'=>$this->id,
                                'sum_id'=>$id,
                                'min_num'=>$list['min_num'],
                                'kpi_value'=>$list['kpi_value'],
                                'other_bool'=>$list['other_bool'],
                                'lcu'=>$uid
                            ));
                        }
                    }
                }else{
                    foreach ($this->json_one as $list){
                        $connection->createCommand()->insert("hr_kpi_min", array(
                            'kpi_id'=>$this->id,
                            'min_num'=>$list['min_num'],
                            'kpi_value'=>$list['kpi_value'],
                            'other_bool'=>$list['other_bool'],
                            'lcu'=>$uid
                        ));
                    }
                }
                break;
            case 'copy':
                $connection->createCommand()->insert('hr_kpi', array(
                    'kpi_name'=>$this->kpi_value,
                    'kpi_str'=>$this->kpi_str,
                    'rate_type'=>$this->rate_type,
                    'tacitly'=>$this->tacitly,
                    'city'=>$this->city,
                    'size_type'=>$this->size_type,
                    'sum_bool'=>$this->sum_bool,
                    'lcu'=>$uid
                ));
                $this->id = Yii::app()->db->getLastInsertID();
                $connection->createCommand()->delete('hr_kpi_sum', 'kpi_id=:id', array(':id'=>$this->id));
                $connection->createCommand()->delete('hr_kpi_min', 'kpi_id=:id', array(':id'=>$this->id));
                if($this->sum_bool ==1){
                    foreach ($this->json_two as $item){
                        $connection->createCommand()->insert("hr_kpi_sum", array(
                            'kpi_id'=>$this->id,
                            'min_sum'=>$item['min_sum'],
                            'other_bool'=>$item['other_bool'],
                            'lcu'=>$uid
                        ));
                        $id =Yii::app()->db->getLastInsertID();
                        foreach ($item["list"] as $list){
                            $connection->createCommand()->insert("hr_kpi_min", array(
                                'kpi_id'=>$this->id,
                                'sum_id'=>$id,
                                'min_num'=>$list['min_num'],
                                'kpi_value'=>$list['kpi_value'],
                                'other_bool'=>$list['other_bool'],
                                'lcu'=>$uid
                            ));
                        }
                    }
                }else{
                    foreach ($this->json_one as $list){
                        $connection->createCommand()->insert("hr_kpi_min", array(
                            'kpi_id'=>$this->id,
                            'min_num'=>$list['min_num'],
                            'kpi_value'=>$list['kpi_value'],
                            'other_bool'=>$list['other_bool'],
                            'lcu'=>$uid
                        ));
                    }
                }
                break;
            case 'delete':
                Yii::app()->db->createCommand()->delete('hr_kpi_sum', 'kpi_id=:id', array(':id'=>$this->id));
                Yii::app()->db->createCommand()->delete('hr_kpi_min', 'kpi_id=:id', array(':id'=>$this->id));
                Yii::app()->db->createCommand()->delete('hr_kpi', 'id=:id', array(':id'=>$this->id));
                break;
        }

		return true;
	}

}
