<?php

class AuditWagesList extends CListPageModel
{
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(	
			'id'=>Yii::t('contract','ID'),
			'name'=>Yii::t('contract','Employee Name'),
			'code'=>Yii::t('contract','Employee Code'),
			'wages_arr'=>Yii::t('contract','Wages Detail'),
			'position'=>Yii::t('contract','Position'),
			'city'=>Yii::t('contract','City'),
			'city_name'=>Yii::t('contract','City'),
			'company_id'=>Yii::t('contract','Company Name'),
			'contract_id'=>Yii::t('contract','Contract Name'),
			'staff_status'=>Yii::t('contract','Wages Status'),
            'wages_date'=>Yii::t('contract','Wages Time'),
		);
	}

	public function retrieveDataByPage($pageNum=1)
	{
		$suffix = Yii::app()->params['envSuffix'];
        $city_allow = Yii::app()->user->city_allow();
        $city = Yii::app()->user->city();
/*        $fastDate = date('Y-m-01', strtotime(date("Y-m-d")));
        $lastDate = date('Y-m-d', strtotime("$fastDate +1 month -1 day"));*/
		$sql1 = "select a.*,b.code,b.city AS s_city,b.name,b.position from hr_employee_wages a
                LEFT JOIN hr_employee b ON a.employee_id = b.id 
                where b.city IN ($city_allow) AND a.wages_status = 1 
			";
		$sql2 = "select count(*)
				from hr_employee_wages a
                LEFT JOIN hr_employee b ON a.employee_id = b.id 
                where b.city IN ($city_allow) AND a.wages_status = 1 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'code':
					$clause .= General::getSqlConditionClause('b.code',$svalue);
					break;
				case 'name':
					$clause .= General::getSqlConditionClause('b.name',$svalue);
					break;
				case 'position':
					$clause .= General::getSqlConditionClause('b.position',$svalue);
					break;
				case 'city':
					$clause .= General::getSqlConditionClause('b.city',$svalue);
					break;
                case 'city_name':
                    $clause .= ' and b.city in '.WordForm::getCityCodeSqlLikeName($svalue);
                    break;
			}
		}
		
		$order = "";
		if (!empty($this->orderField)) {
			$order .= " order by ".$this->orderField." ";
			if ($this->orderType=='D') $order .= "desc ";
		}

		$sql = $sql2.$clause;
		$this->totalRow = Yii::app()->db->createCommand($sql)->queryScalar();
		
		$sql = $sql1.$clause.$order;
		$sql = $this->sqlWithPageCriteria($sql, $this->pageNum);
		$records = Yii::app()->db->createCommand($sql)->queryAll();
		
		$list = array();
		$this->attr = array();
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
			    $arr = $this->returnStaffStatus($record['wages_status']);
				$this->attr[] = array(
					'id'=>$record['id'],
					'name'=>$record['name'],
					'wages_date'=>date("Y-m",strtotime($record['wages_date'])),
					'code'=>$record['code'],
					'position'=>DeptForm::getDeptToid($record['position']),
					'wages_arr'=>$this->getWagesList($record),
                    'city'=>CGeneral::getCityName($record["s_city"]),
					'staff_status'=>$arr["status"],
					'style'=>$arr["style"],
				);
			}
		}
		$session = Yii::app()->session;
		$session['auditwages_01'] = $this->getCriteria();
		return true;
	}

	private function getWagesList($row){
	    $html = Yii::t("contract","Wages Sum")."：".$row["sum"]."<br>";
	    if(!empty($row["wages_arr"])){
	        $wagesArr = unserialize($row["wages_arr"]);
	        $key = 0;
	        foreach ($wagesArr as $wage){
                $key++;
                $html .= $wage[0]."：".$wage[1];
                if($key == 2){
                    break;
                }
                $html .= "<br>";
            }
	        if(count($wagesArr)>3){
                $html.="<br>...";
            }
        }
        return $html;
    }

	public function returnStaffStatus($wages_status){
        switch ($wages_status){
            case 1:
                return array(
                    "status"=>Yii::t("contract","pending approval"),
                    "style"=>" text-yellow"
                );//已提交，待審核
/*            case 3:
                return array(
                    "status"=>Yii::t("contract","Finish approval"),
                    "style"=>" text-success"
                );//已審核，待確認
            case 4:
                return array(
                    "status"=>Yii::t("contract","Rejected"),
                    "style"=>" text-red"
                );//已拒絕*/
            default:
                return array(
                    "status"=>Yii::t("contract","Error"),
                    "style"=>" "
                );//已拒絕

        }
    }
}
