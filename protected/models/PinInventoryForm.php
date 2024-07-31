<?php

class PinInventoryForm extends CFormModel
{
	public $id;
	public $inventory;
	public $safe_stock=0;
	public $city;
	public $class_id;
	public $pin_name_id;
	public $display=1;
	public $z_index=0;
	public $historyList;
	public $residue_num;

	public function attributeLabels()
	{
        return array(
            'city'=>Yii::t('contract','City'),
            'class_id'=>Yii::t('app','Pin Class'),
            'pin_name_id'=>Yii::t('app','Pin Name'),
            'z_index'=>Yii::t('contract','Level'),
            'inventory'=>Yii::t('contract','inventory num'),
            'safe_stock'=>Yii::t('contract','safe stock'),
        );
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id,inventory,z_index,pin_name_id,safe_stock,city,display','safe'),
            array('pin_name_id,inventory,city,safe_stock','required'),
            array('id','validateId','on'=>array('delete')),
		);
	}

    public function validateId($attribute, $params){
        $row = Yii::app()->db->createCommand()->select("id")->from("hr_pin")
            ->where("inventory_id=:inventory_id",array(":inventory_id"=>$this->id))->queryRow();
        if($row){
            $message = "該襟章已被申請，无法删除";
            $this->addError($attribute,$message);
        }
    }

	public function retrieveData($index,$city="") {
        $city=empty($city)?Yii::app()->user->city():$city;
		$row = Yii::app()->db->createCommand()->select("*")->from("hr_pin_inventory")
            ->where("pin_name_id=:id and city=:city",array(":id"=>$index,":city"=>$city))->queryRow();
		if ($row) {
            $this->id = $row['id'];
            $this->pin_name_id = $row['pin_name_id'];
            $this->inventory = $row['inventory'];
            $this->safe_stock = $row['safe_stock'];
            $this->city = $row['city'];
            $this->z_index = $row['z_index'];
            $this->residue_num = $row['residue_num'];
		}else{
		    $bool = Yii::app()->db->createCommand()->select("id")->from("hr_pin_name")
                ->where("id=:id",array(":id"=>$index))->queryRow();
		    if($bool){
                Yii::app()->db->createCommand()->insert('hr_pin_inventory',array(
                    'pin_name_id'=>$index,
                    'city'=>$city,
                    'lcu'=>Yii::app()->user->id,
                ));
                self::retrieveData($index,$city);
            }else{
		        return false;
            }
        }
        return true;
	}

	//获取库存记录HTML
	public static function getHistoryList($id){
        $statusList = array(//info,danger,success,warning
            1=>array("name"=>Yii::t("contract","Inventory Update"),'style'=>"danger","id"=>1),
            2=>array("name"=>Yii::t("contract","Pin new"),'style'=>"hidden","id"=>2),
            3=>array("name"=>Yii::t("contract","Pin Update"),'style'=>"hidden","id"=>3),
            4=>array("name"=>Yii::t("contract","Pin Delete"),'style'=>"warning hidden","id"=>4)
        );
        $html="<div style='margin: 10px 0px;width: 33%'>".PinApplyForm::getSelectForDataEx("changeStatus",1,$statusList)."</div>";
        $html.='<table id="tblFlow" class="table table-bordered table-striped table-hover">';
        $html.="<thead><tr>";
        $html.="<th>".Yii::t("contract","Apply Date")."</th>";
        $html.="<th>".Yii::t("app","Pin Name")."</th>";
        $html.="<th>".Yii::t("contract","Status")."</th>";
        $html.="<th>".Yii::t("contract","Operator User")."</th>";
        $html.="<th>".Yii::t("contract","old num")."</th>";
        $html.="<th>".Yii::t("contract","now num")."</th>";
        $html.="</tr></thead><tbody>";
        $historyList = Yii::app()->db->createCommand()
            ->select("a.apply_date,a.old_sum,a.now_sum,a.apply_name,a.status_type,b.name,a.pin_code")
            ->from("hr_pin_inventory_history a")
            ->leftJoin("hr_pin_name b","a.pin_name_id=b.id")
            ->where("a.inventory_id=:id",array(":id"=>$id))->order("a.apply_date desc")->queryAll();
        if($historyList){
            foreach ($historyList as $list){
                $status_type=key_exists($list["status_type"],$statusList)?$statusList[$list["status_type"]]["name"]:"";
                $style=key_exists($list["status_type"],$statusList)?$statusList[$list["status_type"]]["style"]:"";
                $html.="<tr class='{$style}' data-type='{$list['status_type']}'>";
                $html.="<td>".$list["apply_date"]."</td>";
                $html.="<td>".$list["name"]."</td>";
                $html.="<td>".$status_type.TbHtml::hiddenField("test",$list["pin_code"])."</td>";
                $html.="<td>".$list["apply_name"]."</td>";
                $html.="<td>".$list["old_sum"]."</td>";
                $html.="<td>".$list["now_sum"]."</td>";
                $html.="</tr>";

            }
        }
        return $html."</tbody></table>";
    }

	//获取所有库存选项框
    public static function getPinInventorySelect($model,$str,$htmlArr){
    }

    //刪除驗證
    public function deleteValidate(){
        return false;
    }

	public function saveData()
	{
		$connection = Yii::app()->db;
		$transaction=$connection->beginTransaction();
		try {
            $this->saveHistory($connection);
			$this->saveGoods($connection);
			$transaction->commit();
		}
		catch(Exception $e) {
			$transaction->rollback();
			throw new CHttpException(404,'Cannot update. ('.$e->getMessage().')');
		}
	}

	protected function saveHistory(&$connection) {
        switch ($this->scenario) {
            case 'delete':
                $connection->createCommand()->delete('hr_pin_inventory_history',"inventory_id={$this->id}");
                break;
            case 'edit':
                $oldModel = new PinInventoryForm();
                $oldModel->retrieveData($this->pin_name_id,$this->city);
                $this->residue_num=$oldModel->residue_num;
                if($oldModel->inventory!=$this->inventory){
                    $this->residue_num+=$this->inventory-$oldModel->inventory;
                    $connection->createCommand()->insert('hr_pin_inventory_history',array(
                        'apply_date'=>date("Y-m-d H:i:s"),
                        'inventory_id'=>$this->id,
                        'pin_name_id'=>$this->pin_name_id,
                        'old_sum'=>$oldModel->inventory,
                        'now_sum'=>$this->inventory,
                        'apply_name'=>Yii::app()->user->user_display_name(),
                        'status_type'=>1,
                        'lcu'=>Yii::app()->user->id,
                    ));
                }
                break;
        }
    }

	protected function saveGoods(&$connection) {
		$sql = '';
        switch ($this->scenario) {
            case 'delete':
                $sql = "delete from hr_pin_inventory where id = :id";
                break;
            case 'new':
                $sql = "insert into hr_pin_inventory(
							pin_name_id,inventory,safe_stock,city,z_index, lcu
						) values (
							:pin_name_id,:inventory,:safe_stock,:city,:z_index, :lcu
						)";
                break;
            case 'edit':
                $sql = "update hr_pin_inventory set 
							inventory = :inventory, 
							safe_stock = :safe_stock, 
							residue_num = :residue_num, 
							display = :display, 
							z_index = :z_index, 
							luu = :luu
						where pin_name_id = :pin_name_id AND city=:city
						";
                break;
        }
		if (empty($sql)) return false;

        $city = Yii::app()->user->city();
        $uid = Yii::app()->user->id;

        $command=$connection->createCommand($sql);
        if (strpos($sql,':id')!==false)
            $command->bindParam(':id',$this->id,PDO::PARAM_INT);

        if (strpos($sql,':pin_name_id')!==false)
            $command->bindParam(':pin_name_id',$this->pin_name_id,PDO::PARAM_INT);
        if (strpos($sql,':inventory')!==false)
            $command->bindParam(':inventory',$this->inventory,PDO::PARAM_INT);
        if (strpos($sql,':safe_stock')!==false)
            $command->bindParam(':safe_stock',$this->safe_stock,PDO::PARAM_INT);
        if (strpos($sql,':residue_num')!==false)
            $command->bindParam(':residue_num',$this->residue_num,PDO::PARAM_INT);
        if (strpos($sql,':display')!==false)
            $command->bindParam(':display',$this->display,PDO::PARAM_INT);
        if (strpos($sql,':z_index')!==false)
            $command->bindParam(':z_index',$this->z_index,PDO::PARAM_INT);

        if (strpos($sql,':city')!==false)
            $command->bindParam(':city',$this->city,PDO::PARAM_STR);
        if (strpos($sql,':luu')!==false)
            $command->bindParam(':luu',$uid,PDO::PARAM_STR);
        if (strpos($sql,':lcu')!==false)
            $command->bindParam(':lcu',$uid,PDO::PARAM_STR);
        $command->execute();

        if ($this->scenario=='new'){
            $this->id = Yii::app()->db->getLastInsertID();
            $this->scenario = "edit";
        }
		return true;
	}
}
