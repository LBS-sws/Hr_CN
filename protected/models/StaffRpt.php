 <?php
class StaffRpt {
	protected $rptId;
	protected $rptName;
	protected $reqUser;
	protected $format;
	protected $data = array();
	protected $multiuser = false;
	protected $users = array();

	public function actionRptStaffList($city='',$date="") {
        $city = empty($city)?Yii::app()->user->city():$city;
        $name = CGeneral::getCityName($city);
		$tdate = empty($date)?date("Y/m/d"):date("Y/m/d",strtotime($date));
		$this->rptId = 'RptStaffList';
		$this->rptName = Yii::t('report','Staff List');
		$this->reqUser = 'admin';
		$this->format = 'EMAIL';
        $this->data = array(
            'RPT_ID'=>$this->rptId,
            'RPT_NAME'=>$this->rptName,
            'CITY'=>$city,
            'TARGET_DT'=>General::toMyDate($tdate),
            'LANGUAGE'=>'zh_cn',
            'CITY_NAME'=>$name,
        );
        $this->addQueueItem();
	}

	protected function addQueueItem() {
		$connection = Yii::app()->db;
		$transaction=$connection->beginTransaction();
		try {
			$sql = "insert into hr_queue (rpt_desc, req_dt, username, status, rpt_type)
						values(:rpt_desc, :req_dt, :username, 'P', :rpt_type)
					";
			$now = date("Y-m-d H:i:s");
			$command=$connection->createCommand($sql);
			if (strpos($sql,':rpt_desc')!==false)
				$command->bindParam(':rpt_desc',$this->rptName,PDO::PARAM_STR);
			if (strpos($sql,':req_dt')!==false)
				$command->bindParam(':req_dt',$now,PDO::PARAM_STR);
			if (strpos($sql,':username')!==false)
				$command->bindParam(':username',$this->reqUser,PDO::PARAM_STR);
			if (strpos($sql,':rpt_type')!==false)
				$command->bindParam(':rpt_type',$this->format,PDO::PARAM_STR);
			$command->execute();
			$qid = Yii::app()->db->getLastInsertID();
	
			$sql = "insert into hr_queue_param (queue_id, param_field, param_value)
						values(:queue_id, :param_field, :param_value)
					";
			foreach ($this->data as $key=>$value) {
				$command=$connection->createCommand($sql);
				if (strpos($sql,':queue_id')!==false)
					$command->bindParam(':queue_id',$qid,PDO::PARAM_INT);
				if (strpos($sql,':param_field')!==false)
					$command->bindParam(':param_field',$key,PDO::PARAM_STR);
				if (strpos($sql,':param_value')!==false)
					$command->bindParam(':param_value',$value,PDO::PARAM_STR);
				$command->execute();
			}

			if ($this->multiuser) {
				$sql = "insert into hr_queue_user (queue_id, username)
						values(:queue_id, :username)
					";
				if (!empty($this->users)) {
					foreach ($this->users as $user) {
						$command=$connection->createCommand($sql);
						if (strpos($sql,':queue_id')!==false)
							$command->bindParam(':queue_id',$qid,PDO::PARAM_INT);
						if (strpos($sql,':username')!==false)
							$command->bindParam(':username',$user,PDO::PARAM_STR);
						$command->execute();
					}
				}
			}
			$transaction->commit();
		}
		catch(Exception $e) {
			$transaction->rollback();
			throw new CHttpException(404,'Cannot update.'.$e->getMessage());
		}
	}
}
?>