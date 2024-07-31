<?php

class ReviewSearchList extends CListPageModel
{
    public $year = 3;
    public $year_type = 3;

    public $employee_id;


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
            'year'=>Yii::t('contract','what year'),
            'year_type'=>Yii::t('contract','monthly'),
            'name_list'=>Yii::t('contract','reviewAllot manager'),
            'review_sum'=>Yii::t('contract','review sum'),
            'review_type'=>Yii::t('contract','review type'),
            'department'=>Yii::t("contract","Department"),
		);
	}

    public function __construct($scenario='')
    {
        if($this->year === 3){
            $this->year = date("Y");
        }
        if($this->year_type===3){
            $month = intval(date("m"));
            if($month>=6&&$month<=11){
                $this->year_type = 1;
            }else{
                $this->year = $month!=12?$this->year-1:$this->year;
                $this->year_type = 2;
            }
        }
        parent::__construct();
    }

    public function rules()
    {
        return array(
            array('attr, pageNum, noOfItem, totalRow, searchField, searchValue, orderField, orderType, filter, year, year_type','safe',),
        );
    }

    //驗證賬號是否綁定員工
    public function validateEmployee(){
        $uid = Yii::app()->user->id;
        $rows = Yii::app()->db->createCommand()->select("employee_id,employee_name")->from("hr_binding")
            ->where('user_id=:user_id',
                array(':user_id'=>$uid))->queryRow();
        if ($rows||Yii::app()->user->validFunction('ZR09')){
            $this->employee_id = isset($rows["employee_id"])?$rows["employee_id"]:"";
            return true;
        }
        return false;
    }

	public function retrieveDataByPage($pageNum=1)
	{
		$suffix = Yii::app()->params['envSuffix'];
		$city = Yii::app()->user->city();
        $city_allow = Yii::app()->user->city_allow();
        //FIND_IN_SET
        $expr_sql = " and b.status_type in (1,2,3)";
        if(Yii::app()->user->validFunction('ZR15')){//所有權限（全部）
            $expr_sql.=" ";
        }elseif(!Yii::app()->user->validFunction('ZR09')){//沒有所有權限
            $expr_sql.=" and (FIND_IN_SET('$this->employee_id',b.id_s_list) or b.employee_id = '$this->employee_id' or b.lcu = '$this->employee_id')";
        }else{//所有權限（本地）
            $expr_sql.=" and c.city IN ($city_allow) ";
        }
        if(!empty($this->year)){
            $expr_sql.=" and b.year='".$this->year."'";
        }
        if(!empty($this->year_type)&&$this->year!=2020){
            $expr_sql.=" and b.year_type='".$this->year_type."'";
        }
		$sql1 = "select docman$suffix.countdoc('REVIEW',b.id) as reviewdoc,c.name,f.name as ment_name,c.code,c.phone,c.city,c.entry_time,d.name as company_name,e.name as dept_name,b.status_type,b.year,b.year_type,b.id,b.name_list,b.review_sum,b.review_type 
                from hr_review b 
                LEFT JOIN hr_employee c ON c.id = b.employee_id
                LEFT JOIN hr_company d ON c.company_id = d.id
                LEFT JOIN hr_dept e ON c.position = e.id
                LEFT JOIN hr_dept f ON c.department = f.id
                where b.id > 0 $expr_sql
			";
		$sql2 = "select count(*)  
                from hr_review b 
                LEFT JOIN hr_employee c ON c.id = b.employee_id
                LEFT JOIN hr_company d ON c.company_id = d.id
                LEFT JOIN hr_dept e ON c.position = e.id
                LEFT JOIN hr_dept f ON c.department = f.id
                where b.id > 0 $expr_sql
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'name':
					$clause .= General::getSqlConditionClause('c.name',$svalue);
					break;
				case 'code':
					$clause .= General::getSqlConditionClause('c.code',$svalue);
					break;
				case 'phone':
					$clause .= General::getSqlConditionClause('c.phone',$svalue);
					break;
                case 'department':
                    $clause .= General::getSqlConditionClause('f.name',$svalue);
                    break;
                case 'position':
                    $clause .= General::getSqlConditionClause('e.name',$svalue);
                    break;
                case 'year':
                    $clause .= General::getSqlConditionClause('b.year',$svalue);
                    break;
                case 'status':
                    $clause .= $this->getSearchValueToStatus($svalue);
                    break;
                case 'city_name':
                    $clause .= ' and c.city in '.WordForm::getCityCodeSqlLikeName($svalue);
                    break;
			}
		}
		
		$order = "";
		if (!empty($this->orderField)) {
            $order .= " order by ".$this->orderField." ";
            if ($this->orderType=='D') $order .= "desc ";
		}else{
            $order .= " order by b.status_type asc,b.lcd desc ";
        }

		$sql = $sql2.$clause;
		$this->totalRow = Yii::app()->db->createCommand($sql)->queryScalar();
		
		$sql = $sql1.$clause.$order;
		$sql = $this->sqlWithPageCriteria($sql, $this->pageNum);
		$records = Yii::app()->db->createCommand($sql)->queryAll();

		$this->attr = array();
		if (count($records) > 0) {
            $reviewTypeList = DeptForm::getReviewType();
			foreach ($records as $k=>$record) {
                $arr = ReviewAllotList::getReviewStatuts($record["status_type"]);
				$this->attr[] = array(
					'id'=>$record['id'],
					'name'=>$record['name'],
					'year'=>$record['year'],
					'year_type'=>ReviewAllotList::getYearTypeList($record['year_type'],$record['year']),
                    'review_type'=>key_exists($record["review_type"],$reviewTypeList)?$reviewTypeList[$record["review_type"]]:$record["review_type"],
					'code'=>$record['code'],
					'position'=>$record['dept_name'],
					'company_id'=>$record['company_name'],
                    'department'=>$record['ment_name'],
					'phone'=>$record['phone'],
					'reviewdoc'=>$record['reviewdoc'],
					'status'=>$arr["status"],
					'style'=>$arr["style"],
                    'city'=>CGeneral::getCityName($record["city"]),
                    'entry_time'=>$record["entry_time"],
                    'name_list'=>$record["name_list"],
                    'review_sum'=>$record["review_sum"],
				);
			}
		}
		$session = Yii::app()->session;
		$session['reviewSearch_01'] = $this->getCriteria();
		return true;
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
            'year'=>$this->year,
            'year_type'=>$this->year_type,
        );
    }

    private function getSearchValueToStatus($status){
        if($status === ""){
            return "";
        }
        $arr = array(
            //0=>Yii::t("contract","none review"),
            1=>Yii::t("contract","in review"),
            2=>Yii::t("contract","more review"),
            3=>Yii::t("contract","success review"),
            4=>Yii::t("contract","Draft"),
        );
        $statusList = array();
        foreach ($arr as $key =>$item){
            if (strpos($item,$status)!==false){
                $statusList[] = $key;
            }
        }
        if(!empty($statusList)){
            return " and b.status_type in (".implode(",",$statusList).")";
        }
        return " and b.status_type = -1";
    }


    public function getYearTypeList($year = 2000){
        if(Yii::app()->params['retire']||!isset(Yii::app()->params['retire'])){
            $arr = array(
                0=>Yii::t("misc","All"),
                1=>Yii::t("contract","first half year"),
                2=>Yii::t("contract","last half year")
            );
            if($year == 2020){
                $arr[1] = Yii::t("contract","first more half year");
            }elseif ($year>2020){
                $arr = array(
                    0=>Yii::t("misc","All"),
                    1=>Yii::t("fete","first half year"),
                    2=>Yii::t("fete","last half year")
                );
            }
        }else{
            $arr = array(
                0=>Yii::t("misc","All"),
                1=>Yii::t("fete","first half year"),
                2=>Yii::t("fete","last half year")
            );
        }
        return $arr;
    }

    public function getYearList(){
        $year = date("Y");
        $arr = array(0=>Yii::t("misc","All"));
        for ($i = $year-5;$i<$year+5;$i++){
            if($i<=2018){
                continue;
            }
            $arr[$i] = $i.Yii::t("contract"," year");
        }
        return $arr;
    }
}
