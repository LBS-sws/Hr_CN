<?php

class TreatyServiceForm extends CFormModel
{
	/* User Fields */
	public $id;
	public $treaty_code;
	public $treaty_name;
	public $month_num;
	public $treaty_num;
	public $city;
	public $city_name;
	public $apply_date;
	public $start_date;
	public $end_date;
	public $state_type;
	public $lcu;

	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
        return array(
            'treaty_code'=>Yii::t('treaty','treaty code'),
            'treaty_name'=>Yii::t('treaty','treaty name'),
            'month_num'=>Yii::t('treaty','month num'),
            'treaty_num'=>Yii::t('treaty','treaty num'),
            'city'=>Yii::t('treaty','city'),
            'city_name'=>Yii::t('treaty','city'),
            'apply_date'=>Yii::t('treaty','apply date'),
            'start_date'=>Yii::t('treaty','start date'),
            'end_date'=>Yii::t('treaty','end date'),
            'state_type'=>Yii::t('treaty','treaty state'),
            'lcu'=>Yii::t('treaty','treaty lcu'),
        );
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
            array('id,treaty_code,treaty_name,month_num,treaty_num,city,city_name,apply_date,start_date,end_date,state_type','safe'),
			array('treaty_name','required'),
            array('id','validateID','on'=>array("delete")),
		);
	}

    public function validateID($attribute, $params) {
        $id = $this->$attribute;
        $row = Yii::app()->db->createCommand()->select("id")->from("hr_treaty_info")
            ->where("treaty_id=:id",array(":id"=>$id))->queryRow();
        if($row){
            $this->addError($attribute, "这条记录已被使用无法删除");
            return false;
        }
    }

	public function retrieveData($index,$bool=true)
	{
        $suffix = Yii::app()->params['envSuffix'];
        $sqlCity = "";
        if($bool){ //由於定時刷新不需要城市，所以需要判斷
            $city_allow = Yii::app()->user->city_allow();
            $uid = Yii::app()->user->id;
            if(Yii::app()->user->validFunction('ZR21')){ //允許查看管轄內的所有項目
                $sqlCity = " and a.city in ({$city_allow}) ";
            }else{
                $sqlCity = " and a.lcu='{$uid}' ";
            }
        }
        $sql = "select a.* 
				from hr_treaty a
				where a.state_type!=3 {$sqlCity} and a.id='$index'
			";
		$row = Yii::app()->db->createCommand($sql)->queryRow();
		if ($row!==false) {
		    //id,treaty_code,treaty_name,month_num,treaty_num,city,apply_date,start_date,end_date,state_type
			$this->id = $row['id'];
			$this->treaty_code = $row['treaty_code'];
			$this->treaty_name = $row['treaty_name'];
			$this->month_num = $row['month_num'];
			$this->treaty_num = empty($row["treaty_num"])?"":$row['treaty_num'];
			$this->city = $row['city'];
			$this->apply_date = empty($row["apply_date"])?"":CGeneral::toDate($row["apply_date"]);
			$this->start_date = empty($row["start_date"])?"":CGeneral::toDate($row["start_date"]);
			$this->end_date = empty($row["end_date"])?"":CGeneral::toDate($row["end_date"]);
			$this->state_type = $row['state_type'];
            $this->lcu = $row['lcu'];
            $this->resetTreatyStatus();
            $this->changeTreatyEmail();
            return true;
		}else{
		    return false;
        }
	}

    //合約的郵件收件人需要根據權限隨時變化
	protected function changeTreatyEmail(){
        $suffix = Yii::app()->params['envSuffix'];
        $rows = Yii::app()->db->createCommand()
            ->select("a.email_id,b.to_addr")
            ->from("hr_treaty_info a")
            ->leftJoin("swoper$suffix.swo_email_queue b","b.id=a.email_id")
            ->where("a.treaty_id=:id and b.status='P'",array(":id"=>$this->id))
            ->order("a.start_date asc")->queryAll();
        if($rows){
            $emailModel = new Email();
            $emailModel->addEmailToPrefixAndCity("ZR21",$this->city,array(),3);//所有权限的人收到邮件
            $emailModel->addEmailToLcu($this->lcu);//项目负责人收到邮件
            $emailModel->addEmailToCity($this->city);
            //$emailModel->addEmailToPrefixAndCity("TH01",$this->city);
            $to_addr = $emailModel->getToAddr();
            $to_addr = empty($to_addr)?json_encode(array("it@lbsgroup.com.hk")):json_encode($to_addr);
            foreach ($rows as $row){
                if($to_addr!=$row["to_addr"]){
                    Yii::app()->db->createCommand()->update("swoper$suffix.swo_email_queue", array(
                        'to_addr'=>$to_addr,
                        'lcd'=>date('Y-m-d H:i:s'),
                    ), 'id=:id', array(':id'=>$row["email_id"]));
                }
            }
            unset($emailModel);
        }
    }
	protected function resetTreatyStatus(){
        $this->apply_date=null;
        $this->start_date=null;
        $this->end_date=null;
        $this->state_type=0;
        $this->treaty_num=0;
        $count = Yii::app()->db->createCommand()->select("count(a.id)")
            ->from("hr_treaty_info a")
            ->where("a.treaty_id=:id",array(":id"=>$this->id))
            ->queryScalar();
        $this->treaty_num = empty($count)?0:$count-1;
	    //起始时间
        $minRow = Yii::app()->db->createCommand()->select("a.id,a.start_date,a.month_num,a.end_date")
            ->from("hr_treaty_info a")
            ->where("a.treaty_id=:id",array(":id"=>$this->id))
            ->order("a.start_date asc")->queryRow();
        if($minRow){
            $this->state_type=1;
            $this->apply_date = $minRow["start_date"];
        }
        //结束时间
        $maxRow = Yii::app()->db->createCommand()->select("a.id,a.start_date,a.month_num,a.end_date")
            ->from("hr_treaty_info a")
            ->where("a.treaty_id=:id",array(":id"=>$this->id))
            ->order("a.end_date desc")->queryRow();
        if($maxRow){
            $this->start_date = $maxRow["start_date"];
            $this->end_date = $maxRow["end_date"];
        }
        if(!empty($this->end_date)&&strtotime($this->end_date)<strtotime(date("Y/m/d"))){//合约已过期
            $this->state_type=2;
        }
        Yii::app()->db->createCommand()->update("hr_treaty", array(
            'apply_date'=>$this->apply_date,
            'start_date'=>$this->start_date,
            'end_date'=>$this->end_date,
            'state_type'=>$this->state_type,
            'treaty_num'=>$this->treaty_num,
        ), "id=:id", array(':id'=>$this->id));
    }

    public static function getTreatyAllUser($city){
	    $arr = array(""=>"");
        $suffix = Yii::app()->params['envSuffix'];
        $systemId = Yii::app()->params['systemId'];
        $rows = Yii::app()->db->createCommand()->select("b.email, b.username")
            ->from("security$suffix.sec_user_access a")
            ->leftJoin("security$suffix.sec_user b","a.username=b.username")
            ->where("a.system_id='$systemId' and b.city='{$city}' and a.a_read_write like '%TH01%' and b.status='A'")
            ->queryAll();
        if($rows){
            foreach ($rows as $row){
                $arr[$row["username"]] = $row["username"];
            }
        }
        return $arr;
    }

	public static function getHistoryTable($treaty_id,$ready=true){
        $rows = Yii::app()->db->createCommand()->select("*")->from("hr_treaty_info")
            ->where("treaty_id=:id",array(":id"=>$treaty_id))->order("start_date asc")->queryAll();
        $html = "<table class='table table-hover table-striped table-bordered'>";
        $html.="<thead><tr>";
        $html.="<th width='13%'>".Yii::t("treaty","history code")."</th>";
        $html.="<th width='15%'>".Yii::t("treaty","start date")."</th>";
        $html.="<th width='15%'>".Yii::t("treaty","end date")."</th>";
        $html.="<th width='15%'>".Yii::t("treaty","email date")."</th>";
        $html.="<th>".Yii::t("treaty","remark")."</th>";
        $colspan = 5;
        if(!$ready){
            $html.="<th width='1%'>&nbsp;</th>";
        }
        $html.="</tr></thead><tbody>";
        if($rows){
            foreach ($rows as $row){
                $email_date = empty($row["email_hint"])?Yii::t("treaty","not email"):CGeneral::toDate($row["email_date"]);
                $html.="<tr>";
                $html.="<td>".$row["contract_code"]."</td>";
                $html.="<td>".CGeneral::toDate($row["start_date"])."</td>";
                $html.="<td>".CGeneral::toDate($row["end_date"])."</td>";
                $html.="<td>".$email_date."</td>";
                $html.="<td>".$row["remark"]."</td>";
                if(!$ready){
                    $label = "<span class='glyphicon glyphicon-pencil'></span>";
                    $link = Yii::app()->createUrl('treatyInfo/edit',array('index'=>$row["id"],'treaty_id'=>$treaty_id));
                    $html.="<td>";
                    $html.=TbHtml::link($label,$link);
                    $html.="</td>";
                }
                $html.="</tr>";
            }
        }else{
            $html.="<tr><td colspan='{$colspan}'>无记录</td></tr>";
        }
        $html.="</tbody></table>";
        return $html;
    }

	
	public function stopData(){
        $uid = Yii::app()->user->id;
        Yii::app()->db->createCommand()->update("hr_treaty", array(
            'state_type'=>3,
            'luu'=>$uid
        ), "id=:id and state_type=2", array(':id'=>$this->id));
	}

	public function shiftData($treaty_lcu){
        $uid = Yii::app()->user->id;
        Yii::app()->db->createCommand()->update("hr_treaty", array(
            'lcu'=>$treaty_lcu,
            'luu'=>$uid
        ), "id=:id", array(':id'=>$this->id));
        $this->retrieveData($this->id,false);//刷新邮件收件人
	}


	public function saveData()
	{
		$connection = Yii::app()->db;
		$transaction=$connection->beginTransaction();
		try {
			$this->saveDataForSql($connection);
			$transaction->commit();
		}
		catch(Exception $e) {
		    var_dump($e);
			$transaction->rollback();
			throw new CHttpException(404,'Cannot update.');
		}
	}

	protected function saveDataForSql(&$connection)
	{
		$suffix = Yii::app()->params['envSuffix'];
		$sql = '';
		switch ($this->scenario) {
			case 'delete':
				$sql = "delete from hr_treaty where id = :id";
				break;
			case 'new':
				$sql = "insert into hr_treaty(
						treaty_name, city, lcu) values (
						:treaty_name, :city, :lcu)";
				break;
			case 'edit':
				$sql = "update hr_treaty set 
					treaty_name = :treaty_name, 
					luu = :luu
					where id = :id";
				break;
		}

		$uid = Yii::app()->user->id;
        $city = Yii::app()->user->city();

		$command=$connection->createCommand($sql);
		if (strpos($sql,':id')!==false)
			$command->bindParam(':id',$this->id,PDO::PARAM_INT);
		if (strpos($sql,':treaty_name')!==false)
			$command->bindParam(':treaty_name',$this->treaty_name,PDO::PARAM_STR);
		if (strpos($sql,':city')!==false)
			$command->bindParam(':city',$city,PDO::PARAM_STR);

		if (strpos($sql,':lcu')!==false)
			$command->bindParam(':lcu',$uid,PDO::PARAM_STR);
		if (strpos($sql,':luu')!==false)
			$command->bindParam(':luu',$uid,PDO::PARAM_STR);
		$command->execute();

        if ($this->scenario=='new'){
            $this->id = Yii::app()->db->getLastInsertID();
            $this->treaty_code="T{$city}".(100000+$this->id);
            Yii::app()->db->createCommand()->update('hr_treaty', array(
                'treaty_code'=>$this->treaty_code,
            ), 'id=:id', array(':id'=>$this->id));
        }

		return true;
	}
}