<?php

class ExtUpdateList extends CListPageModel
{
    public $table_type="";

    public function rules()
    {
        return array(
            array('table_type,attr, pageNum, noOfItem, totalRow, searchField, searchValue, orderField, orderType, filter, dateRangeValue','safe',),
        );
    }

    public function getCriteria() {
        return array(
            'table_type'=>$this->table_type,
            'searchField'=>$this->searchField,
            'searchValue'=>$this->searchValue,
            'orderField'=>$this->orderField,
            'orderType'=>$this->orderType,
            'noOfItem'=>$this->noOfItem,
            'pageNum'=>$this->pageNum,
            'filter'=>$this->filter,
            'dateRangeValue'=>$this->dateRangeValue,
        );
    }
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
			'phone'=>Yii::t('contract','Employee Phone'),
            'department'=>Yii::t('contract','Department'),
			'position'=>Yii::t('contract','Position'),
			'company_id'=>Yii::t('contract','Company Name'),
			'contract_id'=>Yii::t('contract','Contract Name'),
			'staff_status'=>Yii::t('contract','Status'),
            'entry_time'=>Yii::t('contract','Entry Time'),
            'city'=>Yii::t('contract','City'),
            'city_name'=>Yii::t('contract','City'),
            'table_type'=>Yii::t('contract','Employee Type'),
            'office_name'=>Yii::t('contract','staff office'),
            'status'=>Yii::t('contract','Status'),
		);
	}

	public function retrieveDataByPage($pageNum=1)
	{
		$suffix = Yii::app()->params['envSuffix'];
        $from =  'security'.Yii::app()->params['envSuffix'].'.sec_city';
		$city = Yii::app()->user->city();
		$allow_city = Yii::app()->user->city_allow();
		$localOffice = Yii::t("contract","local office");
		$sql1 = "select a.*,de.name as department_name,
                  b.name as position_name,g.name as city_name,
                  if(a.office_id=0,'{$localOffice}',f.name) as office_name,
                  docman$suffix.countdoc('EMPLOY',a.employee_id) as extUpdatedoc
                from hr_employee_operate a
                LEFT JOIN hr_office f ON f.id=a.office_id
                LEFT JOIN hr_dept b ON b.id=a.position
                LEFT JOIN hr_dept de ON de.id=a.department
                LEFT JOIN security{$suffix}.sec_city g ON g.code=a.city
                where a.city in ({$allow_city}) AND a.finish != 1 AND a.table_type != 1
			";
		$sql2 = "select count(a.id)
				from hr_employee_operate a
                LEFT JOIN hr_office f ON f.id=a.office_id
                LEFT JOIN hr_dept b ON b.id=a.position
                LEFT JOIN hr_dept de ON de.id=a.department
                LEFT JOIN security{$suffix}.sec_city g ON g.code=a.city
                where a.city in ({$allow_city}) AND a.finish != 1 AND a.table_type != 1
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'name':
					$clause .= General::getSqlConditionClause('a.name',$svalue);
					break;
				case 'code':
					$clause .= General::getSqlConditionClause('a.code',$svalue);
					break;
				case 'phone':
					$clause .= General::getSqlConditionClause('a.phone',$svalue);
					break;
                case 'department':
                    $clause .= General::getSqlConditionClause('de.name',$svalue);
                    break;
                case 'position':
                    $clause .= General::getSqlConditionClause('b.name',$svalue);
                    break;
				case 'city_name':
                    $clause .= General::getSqlConditionClause('g.name',$svalue);
                    break;
                case 'office_name':
                    $clause .= General::getSqlConditionClause("if(a.office_id=0,'{$localOffice}',f.name)",$svalue);
                    break;
			}
		}
        if($this->table_type!==""){//
            $list = StaffFun::getTableTypeList();
            $this->table_type="".$this->table_type;
            if(key_exists($this->table_type,$list)){
                $clause.=" and a.table_type='{$this->table_type}'";
            }
        }
		
		$order = "";
		if (!empty($this->orderField)) {
			$order .= " order by ".$this->orderField." ";
			if ($this->orderType=='D') $order .= "desc ";
		}else{
            $order .= " order by a.id desc ";
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
                $arr = $this->translateEmploy($record['staff_status']);
				$this->attr[] = array(
					'id'=>$record['id'],
					'name'=>$record['name'],
					'extUpdatedoc'=>$record['extUpdatedoc'],
					'office_name'=>$record['office_name'],
					'code'=>$record['code'],
					'position'=>$record['position_name'],
					'department'=>$record['department_name'],
					'city'=>$record['city_name'],
					'company_id'=>CompanyForm::getCompanyToId($record['company_id'])["name"],
					//'contract_id'=>ContractForm::getContractNameToId($record['contract_id']),
					'phone'=>$record['phone'],
                    'entry_time'=>$record["entry_time"],
                    'table_type'=>StaffFun::getTableTypeNameForID($record["table_type"]),
                    'status'=>$arr["status"],
                    'style'=>$arr["style"],
				);
			}
		}
		$session = Yii::app()->session;
		$session['extUpdate_01'] = $this->getCriteria();
		return true;
	}

    public static function translateEmploy($status){
        switch ($status){
            // text-danger
            case 1://审核通过
                return array(
                    "status"=>Yii::t("contract","audited"),
                    "style"=>" text-primary"
                );
            case 2:
                return array(
                    "status"=>Yii::t("contract","Sent, pending approval"),//已發送，等待審核
                    "style"=>" text-yellow"
                );
            case 3:
                return array(
                    "status"=>Yii::t("contract","Rejected"),//拒絕
                    "style"=>" text-danger"
                );
            case 9://草稿
                return array(
                    "status"=>Yii::t("contract","Draft"),
                    "style"=>""
                );
        }
        return array(
            "status"=>"",
            "style"=>""
        );
    }
}
