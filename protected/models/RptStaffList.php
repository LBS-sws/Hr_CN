<?php
class RptStaffList extends CReport {
	protected $mergeCities = array(
			'ZY' => array('KA'),
		);
	
	protected function fields() {
		return array(
			'code'=>array('label'=>Yii::t('report','Staff No.'),'width'=>15,'align'=>'L'),
			'name'=>array('label'=>Yii::t('report','Name'),'width'=>25,'align'=>'L'),
			'gender'=>array('label'=>Yii::t('report','Gender'),'width'=>10,'align'=>'C'),
			'position'=>array('label'=>Yii::t('report','Position'),'width'=>25,'align'=>'L'),
			'user_card'=>array('label'=>Yii::t('report','ID No.'),'width'=>20,'align'=>'L'),
			'user_card_date'=>array('label'=>Yii::t('report','ID Valid Date'),'width'=>15,'align'=>'C'),
			'dob'=>array('label'=>Yii::t('report','DOB'),'width'=>15,'align'=>'C'),
			'address'=>array('label'=>Yii::t('report','Original Address'),'width'=>40,'align'=>'L'),
			'contact_address'=>array('label'=>Yii::t('report','Contact Address'),'width'=>40,'align'=>'L'),
			'phone'=>array('label'=>Yii::t('report','Contact Type'),'width'=>20,'align'=>'L'),
			'fix_time'=>array('label'=>Yii::t('report','Contract Type'),'width'=>15,'align'=>'L'),
			'join_dt'=>array('label'=>Yii::t('report','Join Date'),'width'=>15,'align'=>'C'),
			'start_dt'=>array('label'=>Yii::t('report','Contract Start Date'),'width'=>15,'align'=>'C'),
			'end_dt'=>array('label'=>Yii::t('report','Contract End Date'),'width'=>15,'align'=>'C'),
			'social_code'=>array('label'=>Yii::t('report','Social Security Code'),'width'=>20,'align'=>'L'),
			'emergency_user'=>array('label'=>Yii::t('report','Emergency Contact'),'width'=>30,'align'=>'L'),
			'emergency_phone'=>array('label'=>Yii::t('report','Emergency Phone'),'width'=>20,'align'=>'L'),
			'change_dt'=>array('label'=>Yii::t('report','Leave/Change Date'),'width'=>15,'align'=>'C'),
			'reason'=>array('label'=>Yii::t('report','Leave/Change Reason'),'width'=>40,'align'=>'L'),
			'remarks'=>array('label'=>Yii::t('report','Remarks'),'width'=>40,'align'=>'L'),
		);
	}
	
	public function genReport() {
		$this->retrieveData();
		$this->title = $this->getReportName();
		$month = date('Y/m', strtotime($this->criteria['TARGET_DT'].' -1 month'));
		$this->subtitle = Yii::t('report','Date of Month').': '.$month;
		$output = $this->exportExcel();
		$emailBool = key_exists("EMAILBOOL",$this->criteria)?$this->criteria["EMAILBOOL"]:true;
		if($emailBool){
            $this->submitEmail($output);
        }
		return $output;
	}
	
	public function retrieveData() {
		$temp_dt = strtotime($this->criteria['TARGET_DT'].' -1 month');
		$start_dt = date('Y',$temp_dt).'-'.date('m',$temp_dt).'-01 00:00:00';
		
		$temp_dt = strtotime($this->criteria['TARGET_DT']);
		$end_dt = date('Y',$temp_dt).'-'.date('m',$temp_dt).'-01';

		$city = $this->criteria['CITY'];
		$city_allow = isset($this->criteria['REGION'])?$this->criteria['REGION']:$city;

		$suffix = Yii::app()->params['envSuffix'];

		$tolist = $this->getTransferOutList($city_allow, $start_dt);
		$tilist = $this->getTransferInList($city_allow, $end_dt.' 00:00:00');
		$exist = $this->getExistList($city_allow, $start_dt);
		
		$idlist = '';
		if (!empty($exist)) {
			foreach ($exist as $id) {
				if (!in_array($id, $tilist)) $idlist .= ($idlist=='' ? '' : ',').$id;
			}
		}
		if (!empty($tolist)) {
			foreach ($tolist as $id=>$items) {
				$idlist .= ($idlist=='' ? '' : ',').$id;
			}
		}
		if ($idlist=='') $idlist = '0';
				
		$sql = "select a.*, b.name as job_title 
				from hr_employee a left outer join hr_dept b on a.position = b.id
				where a.id in ($idlist)
				order by a.city,a.name
			";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
				$temp = array();
				$temp['code'] = $row['code'];
				$temp['name'] = $row['name'];
				$temp['gender'] = Yii::t('contract',$row['sex']);
				$temp['position'] = $row['job_title'];
				$temp['user_card'] = is_numeric($row['user_card']) ? $row['user_card'].' ' : $row['user_card'];
				$temp['user_card_date'] = General::toDate($row['user_card_date']);
				$temp['dob'] = General::toDate($row['birth_time']);
				$temp['address'] = $row['address'];
				$temp['contact_address'] = $row['contact_address'];
				$temp['phone'] = is_numeric($row['phone']) ? $row['phone'].' ' : $row['phone'];
				$temp['fix_time'] = Yii::t('contract',$row['fix_time']);
				$temp['join_dt'] = General::toDate($row['entry_time']);
				$temp['start_dt'] = General::toDate($row['start_time']);
				$temp['end_dt'] = General::toDate($row['end_time']);
				$temp['social_code'] = is_numeric($row['social_code']) ? $row['social_code'].' ' : $row['social_code'];
				$temp['emergency_user'] = $row['emergency_user'];
				$temp['emergency_phone'] = is_numeric($row['emergency_phone']) ? $row['emergency_phone'].' ' : $row['emergency_phone'];
				$temp['change_dt'] = ($row['city']!=$city ? $tolist[$row['id']]['change_dt'] :
										($row['staff_status']=-1 && strtotime($row['leave_time']) < strtotime($end_dt)
										? General::toDate($row['leave_time']) : '')
									);
				$temp['reason'] = ($row['city']!=$city ? $tolist[$row['id']]['remarks'] :
										($row['staff_status']=-1 && strtotime($row['leave_time']) < strtotime($end_dt)
										? $row['leave_reason'] : '')
									);
				$temp['remarks'] = '';
				$this->data[] = $temp;
			}
		}
		
		return true;
	}

	protected function getExistList($city, $dt) {
		$rtn = array();
		$citystr = $this->getCityQueryString($city);
		
		$sql = "select a.id 
				from hr_employee a 
				where a.city in ($citystr) and (convert(a.leave_time,datetime) >= '$dt' or a.leave_time is null)
				order by a.id
			";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (!empty($rows)) {
			foreach ($rows as $row) {
				$rtn[] = $row['id'];
			}
		}
		
		return $rtn;
	}
	
	protected function getTransferOutList($city, $dt) {
		$rtn = array();
		$citystr = $this->getCityQueryString($city);
		
		$sql = "select a.employee_id, a.id, a.remark, c.start_time, a.lcd   
				from hr_employee_history a, hr_employee_operate b, hr_employee c 
				where a.history_id=b.id and a.status='transfer' and a.lcd >= '$dt'
				and a.employee_id = c.id
				and b.city in ($citystr) and b.city <> b.change_city 
				and (b.change_city <> '' or b.change_city is not null)
			";
		$trfout = Yii::app()->db->createCommand($sql)->queryAll();
		if (!empty($trfout)) {
			foreach ($trfout as $row) {
				$accept = false;
				$eid = $row['employee_id'];
				$hid = $row['id'];
				
				$sql = "select status from hr_employee_history 
						where employee_id=$eid and id > $hid
						order by id
					";
				$hist = Yii::app()->db->createCommand($sql)->queryAll();
				if (!empty($hist)) {
					foreach ($hist as $rec) {
						$stop = '/update/change/departure/promotion/contract/transfer/';
						if (strpos($stop,'/'.$rec['status'].'/')!==false) break;
						if ($rec['status']=='finish') {
							$accept = true;
							break;
						}
					}
				}
				
				if ($accept) {
					$rtn[$row['employee_id']] = array('change_dt'=>$row['lcd'], 'remarks'=>$row['remark']);
				}
			}
		}

		return $rtn;
	}
	
	protected function getTransferInList($city, $dt) {
		$rtn = array();
		$citystr = $this->getCityQueryString($city);
		
		$sql = "select a.employee_id, a.id, c.start_time 
				from hr_employee_history a, hr_employee_operate b, hr_employee c 
				where a.history_id=b.id and a.status='transfer' and a.lcd > '$dt'
				and a.employee_id = c.id
				and b.change_city in ($citystr) and b.city <> b.change_city 
				and (b.change_city <> '' or b.change_city is not null)
			";
		$trfout = Yii::app()->db->createCommand($sql)->queryAll();
		if (!empty($trfout)) {
			foreach ($trfout as $row) {
				$accept = false;
				$eid = $row['employee_id'];
				$hid = $row['id'];
				
				$sql = "select status from hr_employee_history 
						where employee_id=$eid and id > $hid
						order by id
					";
				$hist = Yii::app()->db->createCommand($sql)->queryAll();
				if (!empty($hist)) {
					foreach ($hist as $rec) {
						$stop = '/update/change/departure/promotion/contract/transfer/';
						if (strpos($stop,'/'.$rec['status'].'/')!==false) break;
						if ($rec['status']=='finish') {
							$accept = true;
							break;
						}
					}
				}
				
				if ($accept) $rtn[] = $row['employee_id'];
			}
		}

		return $rtn;
	}

	public function getReportName() {
		$city_name = isset($this->criteria) ? ' - '.General::getCityName($this->criteria['CITY']) : '';
		return (isset($this->criteria) ? Yii::t('report',$this->criteria['RPT_NAME']) : Yii::t('report','Nil')).$city_name;
	}
	
	public function submitEmail($output) {
		$city = $this->criteria['CITY'];
		$date = $this->criteria['TARGET_DT'];
		
		$users = $this->getUsersWithRight($city,'YB04');

		$to = General::getEmailByUserIdArray($users);
		$to = General::dedupToEmailList($to);
		$cc = array();
		
		$subject = Yii::t('report','Staff List').' ('.General::getCityName($city).') - '.General::toDate($date);
		$desc = Yii::t('report','Staff List').' ('.General::getCityName($city).') - '.General::toDate($date);
		
		$param = array(
				'from_addr'=>Yii::app()->params['systemEmail'],
				'to_addr'=>json_encode($to),
				'cc_addr'=>json_encode($cc),
				'subject'=>$subject,
				'description'=>$desc,
				'message'=>Yii::t('report','Please find the attached report for your reference.'),
			);
		$fn = Yii::t('report','Staff List').'_'.General::getCityName($city).'.xlsx';
		$connection = Yii::app()->db;
		$this->sendEmailWithAttachment($connection, $param, array($fn=>$output));
	}
	
	protected function getUsersWithRight($city, $right) {
		$rtn = array();
		$suffix = Yii::app()->params['envSuffix'];
		$sql = "select a.username, b.city from security$suffix.sec_user_access a, security$suffix.sec_user b
				where (a.a_read_only like '%$right%' or a.a_read_write like '%$right%')
				and a.username=b.username and b.status='A' and a.system_id='hr' and FIND_IN_SET('{$city}',b.look_city)
			";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (!empty($rows)) {
            /*$cityo = new City;
            $citylist = $cityo->getAncestorList($city).','.$city;
            foreach ($rows as $row) {
                if (strpos($citylist,$row['city'])!==false) $rtn[] = $row['username'];
            }*/
            foreach ($rows as $row) {
				$rtn[] = $row['username'];
			}
		}
		return $rtn;
	}
	
	protected function getCityQueryString($city) {
        if (strpos($city,"'")!==false){//如果查询多个城市，直接返回
            return $city;
        }
		return isset($this->mergeCities[$city]) ? "'$city','".implode("','",$this->mergeCities[$city])."'" : "'$city'";
	}
}
?>