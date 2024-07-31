<?php

class SignContractList extends CListPageModel
{
    public $city;//查詢的城市
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

    public function rules(){
        return array(
            array('attr, pageNum, noOfItem, totalRow, searchField, searchValue, orderField, orderType, city, filter, dateRangeValue','safe',),
        );
    }

	public function retrieveDataByPage($pageNum=1)
	{
        $suffix = Yii::app()->params['envSuffix'];
        $city = Yii::app()->user->city;
        $city_allow = Yii::app()->user->city_allow();
		$sql1 = "select a.id,a.sign_type,b.code,b.name,b.city,b.position,b.company_id,b.entry_time,a.status_type,a.courier_str,a.courier_code from hr_sign_contract a 
                LEFT JOIN hr_employee b ON a.employee_id = b.id
                where b.city IN ($city_allow) AND a.status_type IN (-1,0,1,2,3,4) 
			";
		$sql2 = "select count(a.id) from hr_sign_contract a 
                LEFT JOIN hr_employee b ON a.employee_id = b.id
                where b.city IN ($city_allow) AND a.status_type IN (-1,0,1,2,3,4) 
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
        if (!empty($this->city)) {
            $svalue = str_replace("'","\'",$this->city);
            $clause .= " and b.city ='$svalue' ";
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
					'sign_type'=>SignContractList::getSignTypeListOrId($record["sign_type"],true),
					'status_type'=>$arr['status'],
					'style'=>$arr['style'],
				);
			}
		}
		$session = Yii::app()->session;
		$session['signContract_01'] = $this->getCriteria();
		return true;
	}

    public function getEmailStatus($str){
        switch ($str){
            case -1:
                return array(
                    "status"=>Yii::t("contract","To be sent under contract"),
                    "style"=>"text-danger"
                );//未填寫
                break;
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
                    "status"=>Yii::t("contract","contract has been sent"),
                    "style"=>"text-primary"
                );//已發送
                break;
            case 3:
                return array(
                    "status"=>Yii::t("contract","contract has been signed"),
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

    function getSignTypeListOrId($sign_type,$bool=false){
        $arr = array(
            ''=>'',
            0=>Yii::t("contract","new contract"),
            1=>Yii::t("contract","contract renewal"),
            2=>Yii::t("contract","retirement contract"),
        );
        if($bool){
            if(key_exists($sign_type,$arr)){
                return $arr[$sign_type];
            }else{
                return $sign_type;
            }
        }else{
            return $arr;
        }
    }

//獲取城市列表
    public function getCityAllList()
    {
        $city_allow = Yii::app()->user->city_allow();
        $from =  'security'.Yii::app()->params['envSuffix'].'.sec_city';
        $rows = Yii::app()->db->createCommand()->select("code,name")->from($from)->where("code in ($city_allow)")->queryAll();
        $arr = array(""=>" -- ".Yii::t("user","City")." -- ");
        foreach ($rows as $row){
            $arr[$row["code"]] = $row["name"];
        }
        return $arr;
    }
}
