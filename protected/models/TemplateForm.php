<?php

class TemplateForm extends CFormModel
{
	public $id;
	public $city;
	public $tem_name;
	public $tem_str;
	public $tem_list=array();

	public function attributeLabels()
	{
        return array(
            'id'=>Yii::t('contract','ID'),
            'tem_name'=>Yii::t('contract','template name'),
            'city'=>Yii::t('contract','City'),
            'tem_str'=>Yii::t('fete','template str'),
        );
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id, city, tem_name,tem_str,tem_list','safe'),
            array('tem_name','required'),
            array('tem_list','required'),
            array('tem_name','validateName'),
            array('tem_list','validateList'),
		);
	}

    public function validateName($attribute, $params){
        $id = -1;
        $city = Yii::app()->user->city();
        if(!empty($this->id)){
            $id = $this->id;
        }
        $rows = Yii::app()->db->createCommand()->select("id")->from("hr_template")
            ->where('tem_name=:tem_name and id!=:id and city=:city',
                array(':tem_name'=>$this->tem_name,':id'=>$id,':city'=>$city))->queryAll();
        if(count($rows)>0){
            $message = Yii::t('contract','template name'). Yii::t('contract',' can not repeat');
            $this->addError($attribute,$message);
        }
    }

    public function validateList($attribute, $params){
        if(!empty($this->tem_list)){
            $arr = array();
            foreach ($this->tem_list as $key => $list){
                if($list=='on'){
                    $rows = Yii::app()->db->createCommand()->select("id")->from("hr_set_pro")
                        ->where('id=:id',
                            array(':id'=>$key))->queryRow();
                    if(!$rows){
                        $message = Yii::t('contract','template name'). Yii::t('contract',' not exist');
                        $this->addError($attribute,$message);
                        break;
                    }else{
                        $arr[] = $key;
                    }
                }
            }
            if (empty($arr)){
                $message = Yii::t('contract','template name'). Yii::t('contract',' can not be empty');
                $this->addError($attribute,$message);
                return false;
            }
            $this->tem_str = implode(",",$arr);
        }
    }

    public function getSetNameToId($id){
        $rows = Yii::app()->db->createCommand()->select("*")->from("hr_template")
            ->where('id=:id',array(':id'=>$id))->queryRow();
        if($rows){
            return $rows;
        }else{
            return false;
        }
    }

    public function parentTemStrDiv($model){
        $key = "";
        $html = "";
        $list = explode(",",$model->tem_str);
        $rows = Yii::app()->db->createCommand()->select("a.id,a.pro_name,b.set_name")->from("hr_set_pro a")
            ->leftJoin("hr_set b",'a.set_id = b.id')
            ->order("b.z_index desc,b.id desc,a.z_index desc")->queryAll();
        $className = get_class($model);
        $downList = array(
            ''=>Yii::t("contract","Off"),//关闭
            'on'=>Yii::t("contract","On")//开启
        );
        $html.="";
        $divBool = false;//div閉合判斷
        if($rows){
            $num = 0;
            foreach ($rows as $row){
                if($key != $row["set_name"]){
                    $key = $row["set_name"];
                    if($divBool){
                        $html.="</div>";
                        $divBool = false;
                    }
                    $html.="<legend>".$row["set_name"]."</legend>";
                    $num =0;
                }
                if($num%3==0){
                    if($divBool){
                        $html.="</div>";
                    }
                    $html.="<div class='form-group'>";
                    $divBool = true;
                }
                $id = $row['id'];
                $html.=TbHtml::label($row["pro_name"],$className.'_tem_'.$id,array('class'=>'col-sm-2 control-label'));
                $bool = in_array($row['id'],$list)?'on':'';
                $html.="<div class='col-sm-2'>".TbHtml::dropDownList($className."[tem_list][$id]",$bool,$downList,array("class"=>"form-control",'id'=>$className.'_tem_'.$id,'readonly'=>$model->getReadonly()))."</div>";
                $num++;
            }
        }
        if($divBool){
            $html.="</div>";
        }
        return $html;
    }

    public function getReadonly(){
        return $this->getScenario() == 'view';
    }

	public function retrieveData($index) {
        $suffix = Yii::app()->params['envSuffix'];
        $city = Yii::app()->user->city();
        $row = Yii::app()->db->createCommand()->select("a.*,b.name as city_name")->from("hr_template a")
            ->leftJoin("security$suffix.sec_city b",'a.city = b.code')
            ->where("a.id=:id and a.city = :city",array(":id"=>$index,":city"=>$city))->queryRow();
		if ($row) {
            $this->id = $row['id'];
            $this->city = $row['city_name'];
            $this->tem_name = $row['tem_name'];
            $this->tem_str = $row['tem_str'];
		}
		return true;
	}

    //刪除驗證
    public function deleteValidate(){
        $city = Yii::app()->user->city();
        $row = Yii::app()->db->createCommand()->select("*")->from("hr_template")
            ->where("id=:id and city=:city",array(":id"=>$this->id,":city"=>$city))->queryRow();
        if ($row) {
            $row = Yii::app()->db->createCommand()->select("*")->from("hr_template_employee")
                ->where("tem_id=:id",array(":id"=>$this->id))->queryRow();
            if($row){
                return false;
            }else{
                return true;
            }
        }
        return false;
    }

	public function saveData()
	{
		$connection = Yii::app()->db;
		$transaction=$connection->beginTransaction();
		try {
			$this->saveGoods($connection);
			$transaction->commit();
		}
		catch(Exception $e) {
			$transaction->rollback();
			throw new CHttpException(404,'Cannot update. ('.$e->getMessage().')');
		}
	}

	protected function saveGoods(&$connection) {
		$sql = '';
        switch ($this->scenario) {
            case 'delete':
                $sql = "delete from hr_template where id = :id";
                break;
            case 'new':
                $sql = "insert into hr_template(
							city,tem_name,tem_str, lcu
						) values (
							:city,:tem_name,:tem_str, :lcu
						)";
                break;
            case 'edit':
                $sql = "update hr_template set
							tem_name = :tem_name, 
							tem_str = :tem_str, 
							luu = :luu
						where id = :id
						";
                break;
        }
		if (empty($sql)) return false;

        $city = Yii::app()->user->city();
        $uid = Yii::app()->user->id;

        $command=$connection->createCommand($sql);
        if (strpos($sql,':id')!==false)
            $command->bindParam(':id',$this->id,PDO::PARAM_INT);
        //log_bool,max_log,sub_bool,sub_multiple
        if (strpos($sql,':city')!==false)
            $command->bindParam(':city',$city,PDO::PARAM_STR);
        if (strpos($sql,':tem_name')!==false)
            $command->bindParam(':tem_name',$this->tem_name,PDO::PARAM_INT);
        if (strpos($sql,':tem_str')!==false)
            $command->bindParam(':tem_str',$this->tem_str,PDO::PARAM_STR);

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
