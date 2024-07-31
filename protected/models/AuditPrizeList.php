<?php

class AuditPrizeList extends CListPageModel
{
	public function attributeLabels()
	{
        return array(
            'prize_date'=>Yii::t('fete','prize date'),
            'prize_num'=>Yii::t('fete','prize num'),
            'prize_pro'=>Yii::t('fete','prize pro'),
            'contact'=>Yii::t('fete','contact'),
            'phone'=>Yii::t('fete','contact phone'),
            'position'=>Yii::t('fete','contact position'),
            'employee_name'=>Yii::t('contract','Employee Name'),
            'employee_code'=>Yii::t('contract','Employee Code'),
            'city'=>Yii::t('contract','City'),
            'city_name'=>Yii::t('contract','City'),
            'status'=>Yii::t('contract','Status'),
        );
	}
	
	public function retrieveDataByPage($pageNum=1)
	{
        $city_allow = Yii::app()->user->city_allow();
        $sql1 = "select a.*,b.name AS employee_name,b.code AS employee_code,b.city AS s_city 
                from hr_prize a LEFT JOIN hr_employee b ON a.employee_id = b.id
                where a.status =1 AND b.city IN ($city_allow) 
			";
        $sql2 = "select count(a.id)
                from hr_prize a LEFT JOIN hr_employee b ON a.employee_id = b.id
                where a.status =1 AND b.city IN ($city_allow) 
			";
		$clause = "";
        if (!empty($this->searchField) && !empty($this->searchValue)) {
            $svalue = str_replace("'","\'",$this->searchValue);
            switch ($this->searchField) {
                case 'employee_name':
                    $clause .= General::getSqlConditionClause('b.name',$svalue);
                    break;
                case 'employee_code':
                    $clause .= General::getSqlConditionClause('b.code',$svalue);
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
		} else
			$order = " order by id desc";

		$sql = $sql2.$clause;
		$this->totalRow = Yii::app()->db->createCommand($sql)->queryScalar();
		
		$sql = $sql1.$clause.$order;
		$sql = $this->sqlWithPageCriteria($sql, $this->pageNum);
		$records = Yii::app()->db->createCommand($sql)->queryAll();
		
		$this->attr = array();
        $prizeList = PrizeList::getPrizeList();
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
                $colorList = $this->getListStatus($record['status']);
                $this->attr[] = array(
                    'id'=>$record['id'],
                    'employee_name'=>$record['employee_name'],
                    'employee_code'=>$record['employee_code'],
                    'prize_pro'=>$prizeList[$record['prize_pro']],
                    'prize_num'=>$record['prize_num'],
                    'customer_name'=>$record['customer_name'],
                    'contact'=>$record['contact'],
                    'phone'=>$record['phone'],
                    'prize_date'=>date("Y-m-d",strtotime($record['prize_date'])),
                    'status'=>$colorList["status"],
                    'city'=>CGeneral::getCityName($record["s_city"]),
                    'style'=>$colorList["style"],
                );
			}
		}
		$session = Yii::app()->session;
		$session['AuditPrize_ya01'] = $this->getCriteria();
		return true;
	}


    public function getListStatus($status){
        switch ($status){
            case 1:
                return array(
                    "status"=>Yii::t("contract","pending approval"),
                    "style"=>" text-yellow"
                );//已提交，待審核
            case 2:
                return array(
                    "status"=>Yii::t("contract","Rejected"),
                    "style"=>" text-red"
                );//已拒絕
            case 3:
                return array(
                    "status"=>Yii::t("contract","Finish approval"),
                    "style"=>" text-success"
                );//審核通過
            default:
                return array(
                    "status"=>Yii::t("contract","Error"),
                    "style"=>" "
                );//已拒絕
        }
    }
}
