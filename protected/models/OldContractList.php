<?php

class OldContractList extends CListPageModel
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
            'position'=>Yii::t('contract','Position'),
            'company_id'=>Yii::t('contract','Company Name'),
            'contract_id'=>Yii::t('contract','Contract Name'),
            'status'=>Yii::t('contract','Status'),
            'city'=>Yii::t('contract','City'),
            'city_name'=>Yii::t('contract','City'),
            'entry_time'=>Yii::t('contract','Entry Time'),
        );
    }

    public function retrieveDataByPage($pageNum=1)
    {
        $sql1 = "select a.*,b.old_type from hr_employee a 
                LEFT JOIN hr_check_staff b ON a.id = b.employee_id 
                where a.staff_status = 0 AND (b.old_type != 3 or b.old_type is NULL ) 
			";
        $sql2 = "select count(a.id)
				from hr_employee a 
                LEFT JOIN hr_check_staff b ON a.id = b.employee_id 
                where a.staff_status = 0 AND (b.old_type != 3 or b.old_type is NULL ) 
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
            if ($this->orderField != "a.city"){
                $order .= ",a.city desc ";
            }
        }else{
            $order .= " order by a.city asc,a.id asc ";
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
                $arr = $this->returnStaffStatus($record);
                $this->attr[] = array(
                    'id'=>$record['id'],
                    'name'=>$record['name'],
                    'code'=>$record['code'],
                    'position'=>DeptForm::getDeptToid($record['position']),
                    'company_id'=>CompanyForm::getCompanyToId($record['company_id'])["name"],
                    //'contract_id'=>ContractForm::getContractNameToId($record['contract_id']),
                    'phone'=>$record['phone'],
                    'status'=>$arr["status"],
                    'style'=>$arr["style"],
                    'city'=>CGeneral::getCityName($record["city"]),
                    'entry_time'=>$record["entry_time"],
                );
            }
        }
        $session = Yii::app()->session;
        $session['oldContract_01'] = $this->getCriteria();
        return true;
    }

    public function returnStaffStatus($arr){
        switch ($arr["old_type"]){
            case 1:
                return array(
                    "status"=>Yii::t("contract","No contract"),
                    "style"=>"text-danger"
                );//未有合同
            case 2:
                return array(
                    "status"=>Yii::t("contract","Existing contract"),
                    "style"=>"text-success"
                );//已有合同
            default:
                return array(
                    "status"=>Yii::t("contract","unchecked"),
                    "style"=>"text-primary"
                );//未檢查
        }
    }
}
