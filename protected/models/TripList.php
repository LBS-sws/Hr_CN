<?php

class TripList extends CListPageModel
{

    public $employee_id;
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
			'trip_code'=>Yii::t('fete','trip code'),
			'lcd'=>Yii::t('fete','apply for time'),
            'employee_name'=>Yii::t('contract','Employee Name'),
            'employee_code'=>Yii::t('contract','Employee Code'),
			'city'=>Yii::t('contract','City'),
            'city_name'=>Yii::t('contract','City'),
			'start_time'=>Yii::t('contract','Start Time'),
			'end_time'=>Yii::t('contract','End Time'),
            'status'=>Yii::t('contract','Status'),
            'tripdoc'=>Yii::t('contract','Attachment'),
            'trip_cost'=>Yii::t('fete','trip cost'),
            'trip_address'=>Yii::t('fete','trip address'),
		);
	}

    public function rules()
    {
        return array(
            array('attr, pageNum, noOfItem, totalRow, searchField, searchValue, orderField, orderType, searchTimeStart, searchTimeEnd','safe',),
        );
    }

	public function retrieveDataByPage($pageNum=1)
	{
        $suffix = Yii::app()->params['envSuffix'];
        $lcuId = Yii::app()->user->id;
        $city_allow = Yii::app()->user->city_allow();
        $employee_id = empty($this->employee_id)?0:$this->employee_id;
        $auditSql = "";
        foreach (AppointTripSetForm::getZIndexForUser() as $key=>$item){
            $auditSql.= empty($auditSql)?"":" or ";
            $auditSql.= "a.{$item}='$lcuId'";
        }
        $auditSql.=LookSetForm::getLookSqlForStr("a.employee_id");
        $whereSql="";
        if(Yii::app()->user->validFunction('ZR24')){//所有出差記錄
            $whereSql.=" and ((b.id={$employee_id}) or {$auditSql} or (b.city in ({$city_allow}) and a.status!=0))";
        }else{
            $whereSql=" and (b.id={$employee_id} or {$auditSql})";
        }
		$sql1 = "select a.*,f.name as city_name,b.code,b.name from hr_employee_trip a
                LEFT JOIN hr_employee b ON a.employee_id=b.id
                LEFT JOIN security{$suffix}.sec_city f ON b.city=f.code
                where a.id>0 $whereSql 
			";
		$sql2 = "select count(a.id) from hr_employee_trip a
                LEFT JOIN hr_employee b ON a.employee_id=b.id
                LEFT JOIN security{$suffix}.sec_city f ON b.city=f.code
                where a.id>0 $whereSql 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'trip_code':
					$clause .= General::getSqlConditionClause('a.trip_code',$svalue);
					break;
				case 'trip_address':
					$clause .= General::getSqlConditionClause('a.trip_address',$svalue);
					break;
				case 'employee_name':
					$clause .= General::getSqlConditionClause('b.name',$svalue);
					break;
				case 'city_name':
					$clause .= General::getSqlConditionClause('f.name',$svalue);
					break;
			}
		}
        if (!empty($this->searchTimeStart) && !empty($this->searchTimeStart)) {
            $svalue = str_replace("'","\'",$this->searchTimeStart);
            $clause .= " and a.start_time >='$svalue 00:00:00' ";
        }
        if (!empty($this->searchTimeEnd) && !empty($this->searchTimeEnd)) {
            $svalue = str_replace("'","\'",$this->searchTimeEnd);
            $clause .= " and a.start_time <='$svalue 23:59:59' ";
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
                $colorList = self::statusToColor($record['status']);
				$this->attr[] = array(
					'id'=>$record['id'],
					'trip_code'=>$record['trip_code'],
					'lcd'=>$record['lcd'],
					'employee_name'=>$record['name'],
					'log_time'=>$record['log_time'],
					'end_time'=>CGeneral::toDate($record['end_time']),
					'start_time'=>CGeneral::toDate($record['start_time']),
					'city_name'=>$record['city_name'],
					'trip_address'=>$record['trip_address'],
					'trip_cost'=>floatval($record['trip_cost']),
                    'status'=>$colorList["status"],
                    'style'=>$colorList["style"],
					'tripdoc'=>0
					//'tripdoc'=>$record['tripdoc']
				);
			}
		}
		$session = Yii::app()->session;
		$session['trip_01'] = $this->getCriteria();
		return true;
	}

    //根據狀態獲取顏色
    public static function statusToColor($status){
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
                    "status"=>Yii::t("fete","audited, pending result"),//已审核，等待出差结果
                    "style"=>" text-yellow"
                );
            case 3:
                return array(
                    "status"=>Yii::t("contract","Rejected"),//拒絕
                    "style"=>" text-danger"
                );
            case 4:
                return array(
                    "status"=>Yii::t("contract","finish support"),//完成
                    "style"=>" text-green"
                );
            case 5:
                return array(
                    "status"=>Yii::t("contract","cancel"),//取消
                    "style"=>" text-aqua"
                );
        }
        return array(
            "status"=>"",
            "style"=>""
        );
    }

    public function getCriteria() {
        return array(
            'searchField'=>$this->searchField,
            'searchValue'=>$this->searchValue,
            'orderField'=>$this->orderField,
            'orderType'=>$this->orderType,
            'noOfItem'=>$this->noOfItem,
            'pageNum'=>$this->pageNum,
            'filter'=>$this->filter,
            'searchTimeStart'=>$this->searchTimeStart,
            'searchTimeEnd'=>$this->searchTimeEnd,
        );
    }

    //驗證賬號是否綁定員工
    public static function validateEmployee($model){
        $uid = Yii::app()->user->id;
        $rows = Yii::app()->db->createCommand()
            ->select("b.id,b.code,b.name,b.city")
            ->from("hr_binding a")
            ->leftJoin("hr_employee b","a.employee_id=b.id")
            ->where('a.user_id=:user_id',array(':user_id'=>$uid))->queryRow();
        if ($rows){
            $model->employee_id = $rows["id"];
            if(isset($model->employee_code)){
                $model->employee_code = $rows["code"];
            }
            if(isset($model->employee_name)){
                $model->employee_name = $rows["name"];
            }
            if(isset($model->city)){
                $model->city = $rows["city"];
            }
            return true;
        }else{
            $model->employee_id = 0;
        }
        return false;
    }
}
