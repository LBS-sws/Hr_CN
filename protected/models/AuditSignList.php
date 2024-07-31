<?php

class AuditSignList extends CListPageModel
{
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
            'name'=>Yii::t('contract','Employee Name'),
            'code'=>Yii::t('contract','Employee Code'),
            'city'=>Yii::t('contract','City'),
            'position'=>Yii::t('contract','Position'),
            'contract_id'=>Yii::t('contract','Contract Name'),
            'company_id'=>Yii::t('contract','Company Name'),
            'entry_time'=>Yii::t('contract','Entry Time'),
            'status_type'=>Yii::t('contract','Status'),
            'courier_code'=>Yii::t('contract','courier code'),
            'courier_str'=>Yii::t('contract','courier name'),
            'sign_type'=>Yii::t('contract','contract type'),
		);
	}

	public function retrieveDataByPage($pageNum=1)
	{
        $suffix = Yii::app()->params['envSuffix'];
        $city = Yii::app()->user->city;
        $city_allow = Yii::app()->user->city_allow();
		$sql1 = "select a.id,a.sign_type,b.code,b.name,b.city,b.position,b.company_id,b.entry_time,a.status_type,a.courier_str,a.courier_code from hr_sign_contract a 
                LEFT JOIN hr_employee b ON a.employee_id = b.id
                where a.status_type=2 
			";
		$sql2 = "select count(a.id) from hr_sign_contract a 
                LEFT JOIN hr_employee b ON a.employee_id = b.id
                where a.status_type=2 
			";

		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'courier_str':
					$clause .= General::getSqlConditionClause('a.courier_str',$svalue);
					break;
				case 'courier_code':
					$clause .= General::getSqlConditionClause('a.courier_code',$svalue);
					break;
				case 'name':
					$clause .= General::getSqlConditionClause('b.name',$svalue);
					break;
				case 'code':
					$clause .= General::getSqlConditionClause('b.code',$svalue);
					break;
			}
		}
		
		$order = "";
		if (!empty($this->orderField)) {
			$order .= " order by ".$this->orderField." ";
			if ($this->orderType=='D') $order .= "desc ";
		}else{
            $order = " order by a.id desc";
        }

		$sql = $sql2.$clause;
		$this->totalRow = Yii::app()->db->createCommand($sql)->queryScalar();
		
		$sql = $sql1.$clause.$order;
		$sql = $this->sqlWithPageCriteria($sql, $this->pageNum);
		$records = Yii::app()->db->createCommand($sql)->queryAll();

		$this->attr = array();
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
                $arr = $this->getEmailStatus($record["status_type"]);
				$this->attr[] = array(
				    //b.code,b.name,b.city,b.position,b.company_id,b.entry_time,a.status_type,a.courier_str,a.courier_code
					'id'=>$record['id'],
                    'city'=>CGeneral::getCityName($record["city"]),
					'code'=>$record['code'],
					'name'=>$record['name'],
                    'position'=>DeptForm::getDeptToid($record['position']),
                    'company_id'=>CompanyForm::getCompanyToId($record['company_id'])["name"],
					'entry_time'=>$record['entry_time'],
					'courier_str'=>$record['courier_str'],
					'courier_code'=>$record['courier_code'],
					'status_type'=>$arr['status'],
                    'sign_type'=>SignContractList::getSignTypeListOrId($record["sign_type"],true),
					'style'=>$arr['style'],
				);
			}
		}
		$session = Yii::app()->session;
		$session['auditSign'] = $this->getCriteria();
		return true;
	}

    public function getEmailStatus($str){
        switch ($str){
            case 0:
                return array(
                    "status"=>Yii::t("contract","none sign"),
                    "style"=>"text-danger"
                );//未填寫
                break;
            case 1:
                return array(
                    "status"=>Yii::t("contract","Draft"),
                    "style"=>""
                );//草稿
                break;
            case 2:
                return array(
                    "status"=>Yii::t("contract","for sign"),
                    "style"=>"text-primary"
                );//已發送
                break;
            case 3:
                return array(
                    "status"=>Yii::t("contract","Finish approval"),
                    "style"=>"text-success"
                );//審核通過
                break;
            case 4:
                return array(
                    "status"=>Yii::t("contract","Rejected"),
                    "style"=>"text-error"
                );//已拒絕
                break;
            default:
                return array(
                    "status"=>Yii::t("contract","not sent"),
                    "style"=>"text-danger"
                );//未發送
        }
    }
}
