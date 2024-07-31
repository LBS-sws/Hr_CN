<?php

class PrizeList extends CListPageModel
{
    public $employee_id;//員工id
    public $searchTimeStart;//開始日期
    public $searchTimeEnd;//結束日期
    public $test;//
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
			'prize_date'=>Yii::t('fete','prize date'),
			'prize_num'=>Yii::t('fete','prize num'),
			'prize_pro'=>Yii::t('fete','prize pro'),
			'contact'=>Yii::t('fete','contact'),
			'phone'=>Yii::t('fete','contact phone'),
			'position'=>Yii::t('contract','Leader'),
			'employee_name'=>Yii::t('contract','Employee Name'),
			'employee_code'=>Yii::t('contract','Employee Code'),
			'city'=>Yii::t('contract','City'),
			'city_name'=>Yii::t('contract','City'),
			'status'=>Yii::t('contract','Status'),
			'lcd'=>Yii::t('contract','Apply Date'),
		);
	}

    public function rules()
    {
        return array(
            array('attr, pageNum, noOfItem, totalRow, searchField, searchValue, orderField, orderType, searchTimeStart, searchTimeEnd, checkBoxSent','safe',),
        );
    }

	public function retrieveDataByPage($pageNum=1)
	{
        $city_allow = Yii::app()->user->city_allow();
		$sql1 = "select a.*,b.name AS employee_name,b.code AS employee_code,b.position,b.city AS s_city 
                from hr_prize a LEFT JOIN hr_employee b ON a.employee_id = b.id
                where a.id!=0 AND b.city IN ($city_allow) 
			";
		$sql2 = "select count(a.id)
                from hr_prize a LEFT JOIN hr_employee b ON a.employee_id = b.id
                where a.id!=0 AND b.city IN ($city_allow) 
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
		if (!empty($this->searchTimeStart)) {
			$svalue = str_replace("'","\'",$this->searchTimeStart);
            $clause .= " and a.lcd >='$svalue 00:00:00' ";
		}
		if (!empty($this->searchTimeEnd)) {
			$svalue = str_replace("'","\'",$this->searchTimeEnd);
            $clause .= " and a.lcd <='$svalue 23:59:59' ";
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

		$this->attr = array();
		$prizeList = self::getPrizeList();
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
			    $colorList = $this->statusToColor($record['status']);
				$this->attr[] = array(
					'id'=>$record['id'],
					'employee_name'=>$record['employee_name'],
					'employee_code'=>$record['employee_code'],
					'prize_pro'=>$prizeList[$record['prize_pro']],
					'prize_num'=>$record['prize_num'],
					'customer_name'=>$record['customer_name'],
					'contact'=>$record['contact'],
					'position'=>DeptForm::getDeptToId($record['position']),
					'phone'=>$record['phone'],
					'prize_date'=>date("Y-m-d",strtotime($record['prize_date'])),
					'lcd'=>date("Y-m-d",strtotime($record['lcd'])),
					'status'=>$colorList["status"],
                    'city'=>CGeneral::getCityName($record["s_city"]),
					'style'=>$colorList["style"],
				);
			}
		}
		$session = Yii::app()->session;
		$session['prize_01'] = $this->getCriteria();
		return true;
	}

	public function getCriteria(){
	    $arr = parent::getCriteria();
        $arr["searchTimeStart"] = $this->searchTimeStart;
        $arr["searchTimeEnd"] = $this->searchTimeEnd;
        return $arr;
    }

	public static function getPrizeList(){
	    return array(
	        ''=>'',
	        1=>Yii::t("fete","cleaner"), //清潔
	        2=>Yii::t("fete","exterminators"), //滅蟲
	        3=>Yii::t("fete","cleaner and exterminators"), //清潔滅蟲
        );
    }

    //根據狀態獲取顏色
    public function statusToColor($status){
        switch ($status){
            // text-danger
            case 0:
                return array(
                    "status"=>Yii::t("contract","Draft"),
                    "style"=>""
                );
            case 1:
                return array(
                    "status"=>Yii::t("contract","Sent, pending approval"),//已發送，等待審核
                    "style"=>" text-primary"
                );
            case 2:
                return array(
                    "status"=>Yii::t("contract","Rejected"),//拒絕
                    "style"=>" text-danger"
                );
            case 3:
                return array(
                    "status"=>Yii::t("contract","audit"),//審核通過
                    "style"=>" text-success"
                );
        }
        return array(
            "status"=>"",
            "style"=>""
        );
    }

}
