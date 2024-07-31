<?php

class PinTableForm extends CFormModel
{
	public $id;//
	public $nameList=array();//徽章數組
	public $staffList=array();//員工數組
	public $storeList=array();//庫存數組
	public $searchStaff=array();//庫存數組
    public $city;

	public function attributeLabels()
	{
        return array(
            'name'=>Yii::t('app','Pin Class'),
            'city'=>Yii::t('contract','City'),
            'z_index'=>Yii::t('contract','Level'),
        );
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id,city','safe'),
            array('z_index,name','required'),
		);
	}

    //所有城市
    public static function getAllowCity(){
        $city_allow = Yii::app()->user->city_allow();
        $suffix = Yii::app()->params['envSuffix'];
        $arr = array(""=>"");
        $rows = Yii::app()->db->createCommand()->select("code,name")
            ->from("security$suffix.sec_city")->where("code in ($city_allow)")->queryAll();
        if($rows){
            foreach ($rows as $row){
                $arr[$row['code']] = $row['name'];
            }
        }
        return $arr;
    }

    public function retrieveData($city="") {
	    if(empty($city)){
            $city = Yii::app()->user->city();
        }
        $this->city = $city;
        $rows = Yii::app()->db->createCommand()->select("a.id,a.image_url,a.name,b.name as class_name,a.class_id")
            ->from("hr_pin_name a")
            ->leftJoin("hr_pin_class b",'a.class_id=b.id')
            ->order("b.z_index asc,a.z_index asc")->queryAll();
        if($rows){
            $this->nameList =array();
            $this->staffList=array();
            $this->storeList=array();
            foreach ($rows as $row){
                $row["inventory"]="";
                $row["residue_num"]="";
                $row["sum_num"]=0;
                $this->nameList[$row["id"]] = $row;
            }
            //查詢庫存數量
            $rows = Yii::app()->db->createCommand()->select("inventory,residue_num,pin_name_id,id")
                ->from("hr_pin_inventory")
                ->where("city='{$city}'")->queryAll();
            if($rows){
                foreach ($rows as $row){
                    $this->storeList[$row["pin_name_id"]]=$row;
                    if(key_exists($row["pin_name_id"],$this->nameList)){
                        $this->nameList[$row["pin_name_id"]]["inventory"]=$row["inventory"];
                        $this->nameList[$row["pin_name_id"]]["residue_num"]=$row["residue_num"];
                    }
                }
            }
            //查詢哪些員工登記過徽章
            $staffRows = Yii::app()->db->createCommand()
                ->select("b.id,b.name,b.entry_time,p.name as dept_name")
                ->from("hr_pin a")
                ->leftJoin("hr_employee b",'a.employee_id=b.id')
                ->leftJoin("hr_dept p","b.position=p.id")
                ->where("a.city='{$city}'")->group("b.id,b.name,b.entry_time,p.name")->queryAll();
            if($staffRows){
                foreach ($staffRows as $staff){
                    $this->staffList[$staff["id"]]=$staff;
                }
            }
            //員工的統計徽章
            $sumRows = Yii::app()->db->createCommand()->select("a.employee_id,b.pin_name_id,sum(a.pin_num) as sumPin")
                ->from("hr_pin a")
                ->leftJoin("hr_pin_inventory b",'a.inventory_id=b.id')
                ->where("a.city='{$city}'")
                ->group("a.employee_id,b.pin_name_id")->queryAll();
            if($sumRows){
                foreach ($sumRows as $row){
                    if(key_exists($row["pin_name_id"],$this->nameList)){
                        $this->nameList[$row["pin_name_id"]]["sum_num"]+=$row["sumPin"];
                    }
                    if(key_exists($row["employee_id"],$this->staffList)){
                        //$this->staffList[員工id][徽章id]
                        $this->staffList[$row["employee_id"]][$row["pin_name_id"]]=$row["sumPin"];
                    }
                }
            }
            return true;
        }
        return false;
    }

	private function tableHead(){
        $thImage = "";
        $thPinName = "";
        $thStore = "";
        if(!empty($this->nameList)){
            foreach ($this->nameList as $row){
                $image = empty($row["image_url"])?"":"<img width='70px' src='".Yii::app()->createUrl('pinName/printImage',array("id"=>$row["id"]))."'/>";
                $thImage.="<th width='85px' class='text-center'>".$image."</th>";
                $thPinName.="<th class='text-center'>".$row['name']."</th>";
                $thStore.="<th class='text-center'>".$row['inventory']."</th>";
            }
        }
        $html="<thead>";
        $html.="<tr>";
        $html.="<th rowspan='2' width='300px' colspan='3' style='vertical-align: middle' class='text-center'>".Yii::t("app","Pin Name")."</th>".$thImage;
        $html.="</tr>";
        $html.="<tr>";
        $html.=$thPinName;
        $html.="</tr>";
        $html.="<tr>";
        $html.="<th colspan='3' class='text-center'>".Yii::t("contract","inventory num")."</th>".$thStore;
        $html.="</tr>";
        $html.="</thead>";
        return $html;
    }

	private function tableBody(){
        $html="";
        if(!empty($this->staffList)){
            foreach ($this->staffList as $staffRow){
                $html.="<tr>";
                $html.="<td width='100px'>".$staffRow["name"]."</td>";
                $html.="<td width='110px'>".$staffRow["dept_name"]."</td>";
                $html.="<td width='90px'>".$staffRow["entry_time"]."</td>";
                foreach ($this->nameList as $pinRow){
                    $num="";
                    if(key_exists($pinRow["id"],$staffRow)){
                        $num = $staffRow[$pinRow["id"]];
                    }
                    $html.="<td class='text-center'>".$num."</td>";
                }
                $html.="</tr>";
            }
        }
        return $html;
    }

	private function tableFoot(){
        $tableFootSum="";//總計數量html
        $tableFoot="";//剩餘數量html
        if(!empty($this->nameList)){
            foreach ($this->nameList as $row){
                $tableFootSum.="<th class='text-center'>".$row['sum_num']."</th>";
                $tableFoot.="<th class='text-center'>".$row['residue_num']."</th>";
            }
        }
        $html="<tfoot>";
        $html.="<tr>";
        $html.="<th colspan='3' class='text-center'>".Yii::t("contract","sum num")."</th>";
        $html.=$tableFootSum;
        $html.="</tr>";
        $html.="<tr>";
        $html.="<th colspan='3' class='text-center'>".Yii::t("contract","remaining num")."</th>";
        $html.=$tableFoot;
        $html.="</tr>";
        $html.="</tfoot>";
        return $html;
    }

	public function printTable() {
	    $html="<div class='table-responsive'>";
	    $html.="<table class='table table-bordered table-hover table-condensed'>";
        $html.=$this->tableHead();
	    $html.="<tbody>";
        $html.=$this->tableBody();
	    $html.="</tbody>";
        $html.=$this->tableFoot();
	    $html.="</table></div>";
	    return $html;
	}
}
