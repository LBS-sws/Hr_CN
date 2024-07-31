<?php

class AuditHistoryList extends CListPageModel
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
            'phone'=>Yii::t('contract','Employee Phone'),
            'department'=>Yii::t('contract','Department'),
            'position'=>Yii::t('contract','Position'),
            'company_id'=>Yii::t('contract','Company Name'),
            'contract_id'=>Yii::t('contract','Contract Name'),
            'staff_status'=>Yii::t('contract','Status'),
            'operation'=>Yii::t('contract','Operation Status'),
            'entry_time'=>Yii::t('contract','Entry Time'),
            'city'=>Yii::t('contract','City'),
            'city_name'=>Yii::t('contract','City'),
            'table_type'=>Yii::t('contract','Employee Type'),
        );
    }

    public function retrieveDataByPage($pageNum=1)
    {
        $suffix = Yii::app()->params['envSuffix'];
        $city = Yii::app()->user->city();
        $city_allow = Yii::app()->user->city_allow();
        $sql1 = "select a.*,g.name as department_name from hr_employee_operate a
                LEFT JOIN hr_dept g ON g.id=a.department
                where a.finish != 1 AND a.staff_status = 2 AND a.city IN ($city_allow) 
			";
        $sql2 = "select count(a.id)
				from hr_employee_operate a 
                LEFT JOIN hr_dept g ON g.id=a.department
				where a.finish != 1 AND a.staff_status = 2 AND a.city IN ($city_allow) 
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
                    $clause .= General::getSqlConditionClause('g.name',$svalue);
                    break;
                case 'position':
                    $clause .= ' and a.position in '.DeptForm::getDeptSqlLikeName($svalue);
                    break;
                case 'city_name':
                    $clause .= ' and a.city in '.WordForm::getCityCodeSqlLikeName($svalue);
                    break;
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
                    'code'=>$record['code'],
                    'operation'=>$record['operation'],
                    'department'=>$record['department_name'],
                    'position'=>DeptForm::getDeptToid($record['position']),
                    'company_id'=>CompanyForm::getCompanyToId($record['company_id'])["name"],
                    //'contract_id'=>ContractForm::getContractNameToId($record['contract_id']),
                    'phone'=>$record['phone'],
                    'city'=>CGeneral::getCityName($record["city"]),
                    'staff_status'=>$arr["status"],
                    'style'=>$arr["style"],
                    'entry_time'=>$record["entry_time"],
                    'table_type'=>StaffFun::getTableTypeNameForID($record["table_type"]),
                );
            }
        }
        $session = Yii::app()->session;
        $session['audithistory_01'] = $this->getCriteria();
        return true;
    }

    public function translateEmploy($status){
        switch ($status){
            case 2:
                return array(
                    "status"=>Yii::t("contract","pending approval"),//等待審核
                    "style"=>" text-primary"
                );
            case 3:
                return array(
                    "status"=>Yii::t("contract","Rejected"),//拒絕
                    "style"=>" text-danger"
                );
            case 4:
                return array(
                    "status"=>Yii::t("contract","Finish approval"),//審核通過
                    "style"=>" text-yellow"
                );
        }
        return array(
            "status"=>"",
            "style"=>""
        );
    }

    public function historyOperation($status){
        return Yii::t("contract",$status);
    }
}
