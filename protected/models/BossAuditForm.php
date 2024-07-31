<?php

class BossAuditForm extends CFormModel
{
	public $id;
	public $employee_id;
	public $lcu;
	public $code;
	public $name;
	public $city;
	public $audit_year;
	public $apply_date;
	public $status_type=0;
	public $boss_type;
	public $reject_remark;
	public $json_text=array();
	public $results_sum;
	public $results_a;
	public $results_b;
	public $results_c;
    public $ratio_a=50;//占比
    public $ratio_b=35;//占比
    public $ratio_c=15;//占比
    public $json_listX;

	public function attributeLabels()
	{
		return array(
            'name'=>Yii::t('contract','Employee Name'),
            'code'=>Yii::t('contract','Employee Code'),
            'audit_year'=>Yii::t('contract','audit year'),
            'results_sum'=>Yii::t('contract','Sum Results'),
            'status_type'=>Yii::t('contract','Status'),
            'reject_remark'=>Yii::t('contract','Rejected Remark'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id,employee_id,json_text,audit_year,reject_remark','safe'),
            array('id','validateID','on'=>array("audit","reject","finish","save")),
            //array('employee_id','validateStaff','on'=>array("audit","finish","save")),
            array('json_text','validateJson','on'=>array("audit","finish","save")),
            array('reject_remark','required','on'=>array("reject")),
		);
	}

    public function validateStaff($attribute, $params){
        $uid = Yii::app()->user->id;
        $rows = Yii::app()->db->createCommand()->select("a.employee_id,b.code,b.name,b.city")->from("hr_binding a")
            ->leftJoin("hr_employee b","a.employee_id = b.id")
            ->where('a.user_id=:user_id',
                array(':user_id'=>$uid))->queryRow();
        if ($rows){
            $this->employee_id = $rows["employee_id"];
            $this->code = $rows["code"];
            $this->name = $rows["name"];
            $this->city = Yii::app()->user->city();
            $this->lcu = $uid;
        }else{
            $message = "員工不存在，請於管理員聯繫";
            $this->addError($attribute,$message);
        }
    }

    public function validateID($attribute, $params){
        $city_allow = Yii::app()->user->city_allow();
        $row = Yii::app()->db->createCommand()->select("a.*,b.code as employee_code,b.name as employee_name")->from("hr_boss_audit a")
            ->leftJoin("hr_employee b","a.employee_id = b.id")
            ->where("a.id=:id and b.city in ($city_allow) and a.status_type in (0,1,5) and boss_type=:boss_type",
                array(':id'=>$this->id,':boss_type'=>$this->boss_type)
            )->queryRow();
        if (!$row){
            $message = "該考核不存在，無法修改";
            $this->addError($attribute,$message);
            return false;
        }else{
            $this->employee_id = $row['employee_id'];
            $this->code = $row['employee_code'];
            $this->ratio_a = $row["ratio_a"];
            $this->ratio_b = $row["ratio_b"];
            $this->ratio_c = $row["ratio_c"];
            $this->name = $row['employee_name'];
            $this->lcu = $row['lcu'];
            $this->city = $row['city'];
            $this->status_type = $row['status_type'];
            $this->audit_year = $row['audit_year'];
            $this->json_listX = empty($row['json_listX'])?array():json_decode($row['json_listX'],true);
            $this->json_text = $this->status_type==5?json_decode($row["json_text"],true):$this->json_text;

        }
    }

    public function validateJson($attribute, $params){
        if(!empty($this->json_text)){
            $bool = in_array($this->status_type,array(0,1));
            //A類驗證
            $bossReviewA = new BossReviewA($this);
            if(!empty($this->json_listX)){
                $bossReviewA->resetListX($this->json_listX);
            }
            $bossReviewA->validateJson($this,$bool);
            $this->json_text = $bossReviewA->json_text;
            $this->results_a = $bossReviewA->scoreSum;
            //B類驗證
            $bossReviewB = new BossReviewB($this);
            if(!empty($this->json_listX)){
                $bossReviewB->resetListX($this->json_listX);
            }
            $bossReviewB->validateJson($this,$bool);
            $this->json_text = $bossReviewB->json_text;
            $this->results_b = $bossReviewB->scoreSum;
            //C類驗證
            $bossRewardType = BossApplyForm::getBossRewardType($this->city);
            $ratio_a = $this->ratio_a*0.01;
            $ratio_b = $this->ratio_b*0.01;
            $this->ratio_c = 100-($this->ratio_a+$this->ratio_b);
            if($bossRewardType == 1){
                $this->results_c = 0;
                $this->results_sum = $this->results_a*$ratio_a+$this->results_b*$ratio_b;
            }else{
                $bossReviewC = new BossReviewC($this);
                $bossReviewC->validateJson($this,$bool);
                $this->json_text = $bossReviewC->json_text;
                $this->results_c = $bossReviewC->scoreSum;
                $this->results_sum = $this->results_a*$ratio_a+$this->results_b*$ratio_b+$this->results_c;
            }
        }
    }

	public function retrieveData($index) {
        $city_allow = Yii::app()->user->city_allow();
        $suffix = Yii::app()->params['envSuffix'];
        $row = Yii::app()->db->createCommand()->select("a.*,b.code as employee_code,b.name as employee_name")
            ->from("hr_boss_audit a")
            ->leftJoin("hr_employee b","a.employee_id = b.id")
            ->where("a.id=:id and b.city in ($city_allow) and a.status_type !=2",array(":id"=>$index))->queryRow();
		if ($row) {
            $this->id = $row['id'];
            $this->employee_id = $row['employee_id'];
            $this->lcu = $row['lcu'];
            $this->code = $row['employee_code'];
            $this->name = $row['employee_name'];
            $this->city = $row['city'];
            $this->apply_date = $row['apply_date'];
            $this->audit_year = $row['audit_year'];
            $this->json_text = json_decode($row['json_text'],true);
            $this->reject_remark = $row['reject_remark'];
            $this->status_type = $row['status_type'];
            $this->results_sum = $row['results_sum'];
            $this->results_a = $row['results_a'];
            $this->results_b = $row['results_b'];
            $this->results_c = $row['results_c'];
            $this->boss_type = $row['boss_type'];
            $this->ratio_a = $row['ratio_a'];
            $this->ratio_b = $row['ratio_b'];
            $this->ratio_c = $row['ratio_c'];
            $this->json_listX = empty($row['json_listX'])?array():json_decode($row['json_listX'],true);
		}
		return true;
	}

    //刪除驗證
    public function deleteValidate(){
        return false;
    }

	public function saveData($str='')
	{
		$connection = Yii::app()->db;
		$transaction=$connection->beginTransaction();
		try {
		    if(in_array($this->boss_type,array(1,2))&&in_array($this->getScenario(),array("finish","audit"))){ //副總監考核後需要總監考核
                $this->deputyAudit();
            }else{
                $this->saveGoods($connection);
            }
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
            case 'finish':
                $sql = "update hr_boss_audit set
							results_a = :results_a, 
							results_b = :results_b, 
							results_c = :results_c, 
							results_sum = :results_sum, 
							status_type = 2, 
							json_text = :json_text, 
							luu = :luu
						where id = :id AND status_type = 5
						";
                break;
            case 'audit':
                $sql = "update hr_boss_audit set
							results_a = :results_a, 
							results_b = :results_b, 
							results_c = :results_c, 
							results_sum = :results_sum, 
							status_type = 4, 
							json_text = :json_text, 
							luu = :luu
						where id = :id AND status_type = 1
						";
                break;
            case 'save':
                $sql = "update hr_boss_audit set
							results_a = :results_a, 
							results_b = :results_b, 
							results_c = :results_c, 
							results_sum = :results_sum, 
							json_text = :json_text, 
							luu = :luu
						where id = :id AND status_type = 0
						";
                break;
            case 'reject':
                $sql = "update hr_boss_audit set
							status_type = :status_type, 
							reject_remark = :reject_remark, 
							luu = :luu
						where id = :id AND status_type in (1,5)
						";
                break;
            case 'delete':
                $sql = "delete from hr_boss_audit where id = :id";
                break;
        }
		if (empty($sql)) return false;

        $city = Yii::app()->user->city();
        $uid = Yii::app()->user->id;

        $command=$connection->createCommand($sql);
        if (strpos($sql,':id')!==false)
            $command->bindParam(':id',$this->id,PDO::PARAM_INT);
        if (strpos($sql,':results_a')!==false)
            $command->bindParam(':results_a',$this->results_a,PDO::PARAM_INT);
        if (strpos($sql,':results_b')!==false)
            $command->bindParam(':results_b',$this->results_b,PDO::PARAM_INT);
        if (strpos($sql,':results_c')!==false)
            $command->bindParam(':results_c',$this->results_c,PDO::PARAM_INT);
        if (strpos($sql,':results_sum')!==false)
            $command->bindParam(':results_sum',$this->results_sum,PDO::PARAM_INT);
        if (strpos($sql,':status_type')!==false){
            $this->status_type = $this->status_type==5?4:3;
            $command->bindParam(':status_type',$this->status_type,PDO::PARAM_INT);
        }
        if (strpos($sql,':reject_remark')!==false)
            $command->bindParam(':reject_remark',$this->reject_remark,PDO::PARAM_STR);
        if (strpos($sql,':json_text')!==false){
            $json_text = json_encode($this->json_text);
            $command->bindParam(':json_text',$json_text,PDO::PARAM_LOB);
        }

        if (strpos($sql,':luu')!==false)
            $command->bindParam(':luu',$uid,PDO::PARAM_STR);
        $command->execute();

        if ($this->scenario=='new'){
            $this->id = Yii::app()->db->getLastInsertID();
        }

        $this->saveBossFlow();
        $this->sendEmail();//發送郵件
		return true;
	}

    protected function saveBossFlow(){
        switch ($this->getScenario()){
            case "audit":
                $state_type="Finish approval";
                break;
            case "finish":
                $state_type="finish";
                break;
            case "reject":
                $state_type="reject";
                break;
            case "delete":
                Yii::app()->db->createCommand()->delete('hr_boss_flow', 'boss_id=:id', array(':id'=>$this->id));
                return;
            default:
                return;
        }
        Yii::app()->db->createCommand()->insert('hr_boss_flow',array(
            'boss_id'=>$this->id,
            'state_type'=>$state_type,
            'state_remark'=>$this->getScenario() == "reject"?$this->reject_remark:"",
            'none_info'=>0,
            'lcu'=>Yii::app()->user->id,
        ));
    }

    protected function sendEmail($send_type = 1){
        $email = new Email();
        $cityName = CGeneral::getCityName($this->city);
        switch ($this->getScenario()){
            case "audit":
                $description="老总年度考核通过 - ".$this->name;
                break;
            case "finish":
                $description="老总年度考核已完成 - ".$this->name;
                break;
            case "reject":
                $description="老总年度考核拒绝 - ".$this->name;
                break;
            default:
                return;
        }
        $subject=$description;
        $message="<p>员工编号：".$this->code."</p>";
        $message.="<p>员工姓名：".$this->name."</p>";
        $message.="<p>员工城市：".$cityName."</p>";
        $message.="<p>考核年份：".$this->audit_year."年</p>";
        $ratio_a = $this->ratio_a*0.01;
        $ratio_b = $this->ratio_b*0.01;
        if($this->getScenario() == "reject"){
            $message.="<p>拒绝原因：".$this->reject_remark."</p>";
        }elseif($this->getScenario() == "finish"){
            $message.="<p>得分（A）项：".($this->results_a*$ratio_a)."</p>";
            $message.="<p>得分（B）项：".($this->results_b*$ratio_b)."</p>";
            $message.="<p>得分（C）项：".$this->results_c."</p>";
            $message.="<p>总得分：".$this->results_sum."</p>";
        }
        $email->setDescription($description);
        $email->setMessage($message);
        $email->setSubject($subject);
        if($send_type==1){
            $email->addEmailToStaffId($this->employee_id);
        }else{
            if ($this->boss_type == 3){//繞生的郵件
                $email->addEmailToPrefixAndCity('BA06',$this->city);
            }else if ($this->boss_type == 1){//總監的郵件
                $email->addEmailToPrefixAndCity('BA03',$this->city);
            }
        }
        $email->sent();
    }

    //副總監修改考核狀態
    protected function deputyAudit(){
        $state_type = "Finish approval";
        switch ($this->boss_type){
            case 1:
                $this->boss_type = 3;//跳轉給繞生
                break;
            case 2:
                $this->boss_type = 1;//跳轉給總監
                break;
        }
        Yii::app()->db->createCommand()->update('hr_boss_audit', array(
            'boss_type'=>$this->boss_type,
            'results_a'=>$this->results_a,
            'results_b'=>$this->results_b,
            'results_c'=>$this->results_c,
            'results_sum'=>$this->results_sum,
            'json_text'=>json_encode($this->json_text),
            'lcu'=>"test",
        ), 'id=:id', array(':id'=>$this->id));
        Yii::app()->db->createCommand()->insert('hr_boss_flow',array(
            'boss_id'=>$this->id,
            'state_type'=>$state_type,
            'none_info'=>0,
            'lcu'=>Yii::app()->user->id,
        ));
        $this->sendEmail(2);
    }

	//判斷輸入框能否修改
	public function getInputBool(){
        return $this->status_type==5||!in_array($this->getScenario(),array("audit","finish","edit","save"));
    }
}
