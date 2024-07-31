<?php

class BossSetBForm extends CFormModel
{
	public $id;
	public $list_text='';
	public $tacitly;
    public $num_ratio;
	public $city;
	public $json_text;
    protected $listX = array(
        array('value'=>'two_one','percent'=>'20','show'=>'1'),//优化人才评核
        array('value'=>'two_two','percent'=>'35','show'=>'1'),//月报表分数
        array('value'=>'two_three','percent'=>'10','show'=>'1'),//质检拜访量
        array('value'=>'two_eight','percent'=>'10','show'=>'1'),//洗地易销售桶数
        array('value'=>'two_four','pro_str'=>"%",'percent'=>'5','show'=>'1'),//高效客诉解决效率
        array('value'=>'two_five','percent'=>'5','show'=>'1'),//总经理回馈次数
        array('value'=>'two_six','pro_str'=>"%",'percent'=>'5','show'=>'1'),//提交销售5步曲数量培训销售部分
        array('value'=>'two_nine','pro_str'=>"%",'percent'=>'5','show'=>'1'),//IA物料使用率
        array('value'=>'two_ten','pro_str'=>"%",'percent'=>'5','show'=>'1'),//IB物料使用率
        array('value'=>'two_service','percent'=>'0','show'=>'1'),//蔚诺租赁服务机器台数
    );
/*    protected $listX = array(
        array('value'=>'two_one','name'=>Yii::t("contract","two_one")),//优化人才评核
        array('value'=>'two_two','name'=>Yii::t("contract","two_two")),//月报表分数
        array('value'=>'two_three','name'=>Yii::t("contract","two_three")),//质检拜访量
        array('value'=>'two_eight','name'=>Yii::t("contract","two_eight")),//洗地易销售桶数
        array('value'=>'two_four','name'=>Yii::t("contract","two_four"),'pro_str'=>"%"),//高效客诉解决效率
        array('value'=>'two_five','name'=>Yii::t("contract","two_five")),//总经理回馈次数
        array('value'=>'two_six','name'=>Yii::t("contract","two_six"),'pro_str'=>"%"),//提交销售5步曲数量培训销售部分
        array('value'=>'two_nine','name'=>Yii::t("contract","two_nine"),'pro_str'=>"%"),//IA物料使用率
        array('value'=>'two_ten','name'=>Yii::t("contract","two_ten"),'pro_str'=>"%"),//IB物料使用率
    );*/

	public function attributeLabels()
	{
		return array(
            'list_text'=>Yii::t("contract","matters"),
            'json_text'=>Yii::t("contract","matters"),
            'num_ratio'=>Yii::t("contract","one_11"),
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
			array('id, json_text, city,tacitly,num_ratio','safe'),
            array('tacitly','required'),
            array('num_ratio','required'),
            array('city','required'),
            array('json_text','required'),
            array('city','validateCity'),
            array('json_text','validateName'),
		);
	}

    public function validateCity($attribute, $params){
        $city = $this->city;
        $rows = Yii::app()->db->createCommand()->select()->from("hr_boss_set_b")
            ->where('id!=:id and city=:city ', array(':id'=>$this->id,':city'=>$city))->queryAll();
        if (count($rows) > 0){
            $message = "该城市已设置A项，不需要重复设置";
            $this->addError($attribute,$message);
        }
    }

	public function validateName($attribute, $params){
	    if(!empty($this->json_text)){
	        $this->list_text = array();
	        $arr = array();
	        $percent = 0;
            foreach ($this->listX as $key=>$item){
                if(key_exists($key,$this->json_text)){
                    if($this->json_text[$key]['show']==1){
                        //$item["name"]=Yii::t("contract",$item["value"]);
                        $this->list_text[] = Yii::t("contract",$item["value"]);
                        $item["show"]=1;
                        $item["percent"]=$this->json_text[$key]['percent'];
                        $percent+=$item["percent"];
                        $arr[]=$item;
                    }else{
                        $item["show"]=2;
                        $item["percent"]=0;
                        $arr[]=$item;
                    }
                }else{
                    $item["show"]=2;
                    $item["percent"]=0;
                    $arr[]=$item;
                }
            }

            if(empty($this->list_text)){
                $message = "请至少开启一项";
                $this->addError($attribute,$message);
                return false;
            }
            if($percent!=100){
                $message = "所有占比之和需要100%";
                $this->addError($attribute,$message);
                return false;
            }
            $this->json_text = $arr;
            $this->list_text = implode("，",$this->list_text);
        }
	}

	public function retrieveData($index) {
        $city_allow = Yii::app()->user->city_allow();
		$rows = Yii::app()->db->createCommand()->select("id,city,list_text,tacitly,json_text,num_ratio")
            ->from("hr_boss_set_b")->where("id=:id",array(":id"=>$index))->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
                $this->id = $row['id'];
                $this->json_text = json_decode($row['json_text'],true);
                $this->tacitly = $row['tacitly'];
                $this->num_ratio = $row['num_ratio'];
                $this->city = $row['city'];
                break;
			}
		}
		return true;
	}

	public function printTable(){
	    $html = "<table class='table table-bordered table-striped'><thead><tr>";
	    $html.="<th width='60%'>".Yii::t("contract","matters")."</th>";//事項
	    $html.="<th width='20%'>".Yii::t("contract","Status")."</th>";//狀態
	    $html.="<th width='20%'>".Yii::t("contract","one_11")."</th>";//佔比
	    $html.="</tr></thead><tbody>";
	    $openList = array(1=>Yii::t("contract","On"),2=>Yii::t("contract","Off"));
	    foreach ($this->listX as $key=>$list){
            $show = 1;
            $percent = $list["percent"];
            if(is_array($this->json_text)&&key_exists($key,$this->json_text)){
                $show = $this->json_text[$key]["show"];
                $percent = $this->json_text[$key]["percent"];
            }
            if($show ==1){
                $html.="<tr>";
            }else{
                $html.="<tr class='danger'>";
            }
	        $html.="<td>".TbHtml::textField("BossSetBForm[json_text][$key][value]",Yii::t("contract",$list["value"]),array('readonly'=>true))."</td>";
	        $html.="<td>".TbHtml::dropDownList("BossSetBForm[json_text][$key][show]",$show,$openList,array('readonly'=>$this->getReadonly(),'class'=>'tr_show'))."</td>";
	        $html.="<td><div class='input-group'>";
            $html.=TbHtml::textField("BossSetBForm[json_text][$key][percent]",$percent,array('readonly'=>$this->getReadonly(),'class'=>'tr_percent'));
            $html.="<span class='input-group-addon'>%</span></div></td>";
	        $html.="</tr>";
        }
	    $html.="</tbody></table>";

	    return $html;
    }

    public function getReadonly(){
        return $this->scenario=='view';
    }

    //刪除驗證
    public function deleteValidate(){
        $row = Yii::app()->db->createCommand()->select("id")->from("hr_boss_set_b")
            ->where("id!=:id and tacitly=1",array(":id"=>$this->id))->queryRow();
        if($row){
            return true;
        }else{
            return false;//沒有A項配置不允許刪除
        }
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
        if($this->tacitly == 1){
            Yii::app()->db->createCommand()->update('hr_boss_set_b', array(
                'tacitly'=>0,
            ));
        }
		$sql = '';
        switch ($this->scenario) {
            case 'delete':
                $sql = "delete from hr_boss_set_b where id = :id";
                break;
            case 'new':
                $sql = "insert into hr_boss_set_b(
							city,list_text,tacitly,num_ratio,json_text
						) values (
							:city,:list_text,:tacitly,:num_ratio,:json_text
						)";
                break;
            case 'edit':
                $sql = "update hr_boss_set_b set
							json_text = :json_text, 
							tacitly = :tacitly, 
							num_ratio = :num_ratio, 
							list_text = :list_text, 
							city = :city
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
        //id,city,list_text,tacitly,json_text
        if (strpos($sql,':city')!==false)
            $command->bindParam(':city',$this->city,PDO::PARAM_STR);
        if (strpos($sql,':num_ratio')!==false)
            $command->bindParam(':num_ratio',$this->num_ratio,PDO::PARAM_INT);
        if (strpos($sql,':list_text')!==false)
            $command->bindParam(':list_text',$this->list_text,PDO::PARAM_STR);
        if (strpos($sql,':tacitly')!==false)
            $command->bindParam(':tacitly',$this->tacitly,PDO::PARAM_STR);
        if (strpos($sql,':json_text')!==false){
            $json_text = json_encode($this->json_text);
            $command->bindParam(':json_text',$json_text,PDO::PARAM_STR);
        }
        $command->execute();

        if ($this->scenario=='new'){
            $this->id = Yii::app()->db->getLastInsertID();
            $this->scenario = "edit";
        }
		return true;
	}
}
