<?php

class HeartLetterAuditList extends CListPageModel
{
    public $employee_id;//員工id
    public $searchTimeStart;//開始日期
    public $searchTimeEnd;//結束日期
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
            'letter_type'=>Yii::t('contract','type for director'),
			'letter_title'=>Yii::t('queue','Subject'),
			'employee_name'=>Yii::t('contract','Employee Name'),
            'employee_code'=>Yii::t('contract','Employee Code'),

			'state'=>Yii::t('contract','Status'),
            'city'=>Yii::t('contract','City'),
            'lcd'=>Yii::t('contract','send date'),
		);
	}

    public function rules()
    {
        return array(
            array('attr, pageNum, noOfItem, totalRow, searchField, searchValue, orderField, orderType, searchTimeStart, searchTimeEnd','safe',),
        );
    }
	//驗證賬號是否綁定員工
    public function validateEmployee(){
        $uid = Yii::app()->user->id;
        $rows = Yii::app()->db->createCommand()->select("employee_id,employee_name")->from("hr_binding")
            ->where('user_id=:user_id',
                array(':user_id'=>$uid))->queryRow();
        if ($rows){
            $this->employee_id = $rows["employee_id"];
            return true;
        }
        return false;
    }

	public function retrieveDataByPage($pageNum=1)
	{
        $suffix = Yii::app()->params['envSuffix'];
        $lcuId = Yii::app()->user->id;
        $city_allow = Yii::app()->user->city_allow();
        $employee_id = $this->employee_id;
        //,docman$suffix.countdoc('LEAVE',a.id) as leavedoc
		$sql1 = "select  a.*,b.name AS employee_name,b.code AS employee_code,b.city AS s_city 
              from hr_letter a 
              LEFT JOIN hr_employee b ON a.employee_id = b.id 
              where b.city in ($city_allow) and a.state in (1,3,4)";
        $sql2 = "select count(a.id) from hr_letter a 
              LEFT JOIN hr_employee b ON a.employee_id = b.id 
              where b.city in ($city_allow) and a.state in (1,3,4)";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'letter_type':
					$clause .= General::getSqlConditionClause('a.letter_type',$svalue);
					break;
				case 'letter_title':
					$clause .= General::getSqlConditionClause('letter_title',$svalue);
					break;
                case 'employee_name':
                    $clause .= General::getSqlConditionClause('b.name',$svalue);
                    break;
                case 'employee_code':
                    $clause .= General::getSqlConditionClause('b.code',$svalue);
                    break;
                case 'city':
                    $clause .= ' and b.city in '.WordForm::getCityCodeSqlLikeName($svalue);
                    break;
                case 'state':
                    $clause .= $this->searchToStatus($svalue);
                    break;
			}
		}
        if (!empty($this->searchTimeStart) && !empty($this->searchTimeStart)) {
            $svalue = str_replace("'","\'",$this->searchTimeStart);
            $clause .= " and a.lcd >='$svalue 00:00:00' ";
        }
        if (!empty($this->searchTimeEnd) && !empty($this->searchTimeEnd)) {
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
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
			    $colorList = $this->statusToColor($record);
				$this->attr[] = array(
					'id'=>$record['id'],
					'employee_name'=>$record['employee_name'],
					'employee_code'=>$record['employee_code'],
					'letter_type'=>HeartLetterForm::getLetterTypeList($record['letter_type'],true),
					'letter_title'=>$record['letter_title'],
					'lcd'=>CGeneral::toDateTime($record['lcd']),


					'state'=>$colorList["status"],
                    'city'=>CGeneral::getCityName($record["s_city"]),
					'style'=>$colorList["style"],
				);
			}
		}
		$session = Yii::app()->session;
		$session['heartLetterAudit_01'] = $this->getCriteria();
		return true;
	}

    //根據狀態獲取顏色
    public function statusToColor($list){
        switch ($list["state"]){
            // text-danger
            case 0:
                return array(
                    "status"=>Yii::t("contract","Draft"),
                    "style"=>""
                );
            case 1:
                return array(
                    "status"=>Yii::t("contract","pending approval"),//已發送，等待審核
                    "style"=>" text-primary"
                );
            case 3:
                return array(
                    "status"=>Yii::t("contract","To be processed"),//待处理
                    "style"=>" text-danger"
                );
            case 4:
                return array(
                    "status"=>Yii::t("contract","finish support"),//已完成
                    "style"=>" text-success"
                );
        }
        return array(
            "status"=>"",
            "style"=>""
        );
    }

    private function searchToStatus($search){
        $arr = array(10);
        $list = array(
            Yii::t("contract","Draft"),
            Yii::t("contract","pending approval"),
            Yii::t("contract","To be processed"),
            Yii::t("contract","finish support")
        );
        foreach ($list as $key=>$status){
            if (strpos($status,$search)!==false){
                $arr[] = $key;
            }
        }
        if($search!=""){
            return " and a.state in (".implode(",",$arr).")";
        }else{
            return "";
        }
    }
}
