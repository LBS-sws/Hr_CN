<?php

class StaffSummaryList extends CListPageModel
{
    public $year;
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
			'year'=>Yii::t('recruit','year'),
			'city'=>Yii::t('recruit','city'),
			'dept_name'=>Yii::t('recruit','dept name'),
			'staff_sum'=>Yii::t('recruit','staff num'),
			'now_sum'=>Yii::t('recruit','job num'),
			'leave_sum'=>Yii::t('recruit','leave num'),
			'leave_rate'=>Yii::t('recruit','leave rate'),
		);
	}
    public function rules()
    {
        return array(
            array('attr, year, pageNum, noOfItem, totalRow, searchField, searchValue, orderField, orderType, filter','safe',),
        );
    }
	
	public function retrieveDataByPage($pageNum=1)
	{
		$suffix = Yii::app()->params['envSuffix'];
        $city = Yii::app()->user->city();
        $city_allow = Yii::app()->user->city_allow();
        $this->year = empty($this->year)||!is_numeric($this->year)?date("Y"):$this->year;
        $yearSql = "((CAST(entry_time as SIGNED)<={$this->year} and staff_status not in (-1,1))or(staff_status=-1 and CAST(leave_time as SIGNED)={$this->year}))";
        if(Yii::app()->user->validFunction('ZR22')){ //显示所有招聘登记
            $sqlCity="";//ZR22
        }else{
            $sqlCity=" AND city in ({$city_allow})";
        }
		$sql1 = "select a.city,a.staff_sum,a.leave_sum,b.name as city_name from (
                  select count(id) as staff_sum,sum(CASE WHEN staff_status=-1 THEN 1 ELSE 0 END) as leave_sum,city from hr_employee WHERE {$yearSql}{$sqlCity} GROUP BY city
                ) a
				LEFT JOIN security{$suffix}.sec_city b ON a.city=b.code 
				WHERE 1>0 
			";
		$sql2 = "select count(a.city) from (
                  select city from hr_employee WHERE {$yearSql}{$sqlCity} GROUP BY city
                ) a
				LEFT JOIN security{$suffix}.sec_city b ON a.city=b.code 
				WHERE 1>0 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'city':
					$clause .= General::getSqlConditionClause('b.name',$svalue);
					break;
			}
		}
		
		$order = "";
		if (!empty($this->orderField)) {
            $order .= " order by {$this->orderField} ";
			if ($this->orderType=='D') $order .= "desc ";
		}else{
            $order .= " order by a.city desc ";
        }

		$sql = $sql2.$clause;
		$this->totalRow = Yii::app()->db->createCommand($sql)->queryScalar();
		
		$sql = $sql1.$clause.$order;
		$sql = $this->sqlWithPageCriteria($sql, $this->pageNum);
		$records = Yii::app()->db->createCommand($sql)->queryAll();

		$this->attr = array();
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
                $attrList = array(
                    'year'=>$this->year,
                    'city_name'=>$record['city_name'],
                    'city'=>$record['city'],
                    'staff_sum'=>$record['staff_sum'],
                    'leave_sum'=>$record['leave_sum'],
                    'leave_rate'=>self::leaveRate($record["leave_sum"],$record["staff_sum"]),
                    'now_sum'=>$record['staff_sum']-$record['leave_sum']
                );
                $detail = $this->getDetailList($record);
                $attrList['detail']=$detail;
                $this->attr[] = $attrList;
			}
		}
		$session = Yii::app()->session;
		$session['staffSummary_c01'] = $this->getCriteria();
		return true;
	}

	private function leaveRate($leaveSum,$staffSum){
	    if(empty($staffSum)){
            $leave_rate="";
        }else{
            $leave_rate = round($leaveSum/$staffSum,2)*100;
            $leave_rate.="%";
        }
        return $leave_rate;
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
        );
    }

	protected function getDetailList($record){
	    $detail = array();
        $yearSql = "((CAST(a.entry_time as SIGNED)<={$this->year} and a.staff_status not in (-1,1))or(a.staff_status=-1 and CAST(a.leave_time as SIGNED)={$this->year}))";
        $rows = Yii::app()->db->createCommand()
            ->select("count(a.id) as staff_sum,sum(CASE WHEN a.staff_status=-1 THEN 1 ELSE 0 END) as leave_sum,a.department,b.name as leader_name")
            ->from("hr_employee a")
            ->leftJoin("hr_dept b","a.department=b.id")
            ->where("$yearSql and a.city=:city",array(":city"=>$record["city"]))
            ->group("a.department,b.name")
            ->order("b.name")
            ->queryAll();
        if($rows){
            foreach ($rows as $row){
                $detail[]=array(
                    'id'=>$row['department'],
                    'leader_name'=>$row['leader_name'],
                    'staff_sum'=>$row['staff_sum'],
                    'leave_sum'=>$row['leave_sum'],
                    'leave_rate'=>self::leaveRate($row["leave_sum"],$row["staff_sum"]),
                    'now_sum'=>$row['staff_sum']-$row['leave_sum']
                );
            }
        }
        return $detail;
    }


    public static function getYearList(){
        $year = date("Y");
        $arr = array();
        for ($i = 2015;$i<$year+1;$i++){
            $arr[$i] = $i.Yii::t("contract"," year");
        }
        return $arr;
    }

    public function getStaffTableDetail(){
        $city = key_exists("city",$_POST)?$_POST["city"]:"";
        $id = key_exists("id",$_POST)?$_POST["id"]:"";
        $year = key_exists("year",$_POST)?$_POST["year"]:0;
        $yearSql = "((CAST(a.entry_time as SIGNED)<={$year} and a.staff_status not in (-1,1))or(a.staff_status=-1 and CAST(a.leave_time as SIGNED)={$year}))";

        $rows = Yii::app()->db->createCommand()
            ->select("a.code,a.name,a.staff_status,a.entry_time,a.leave_time,b.name as dept_name,f.name as leader_name")
            ->from("hr_employee a")
            ->leftJoin("hr_dept b","a.position=b.id")
            ->leftJoin("hr_dept f","a.department=f.id")
            ->where("$yearSql and a.city=:city and a.department=:department",array(":department"=>$id,":city"=>$city))
            ->order("a.staff_status asc,b.name")
            ->queryAll();
        $html="";
        if($rows){
            foreach ($rows as $row){
                $html.="<tr>";
                $html.="<td>".$row["code"]."</td>";
                $html.="<td>".$row["name"]."</td>";
                $html.="<td>".$row["leader_name"]."</td>";
                $html.="<td>".$row["dept_name"]."</td>";
                $html.="<td>".CGeneral::toMyDate($row["entry_time"])."</td>";
                if($row["staff_status"]==-1){
                    $html.="<td>".CGeneral::toMyDate($row["leave_time"])."</td>";
                    $html.="<td class='text-danger'>".Yii::t("contract","departure")."</td>";
                }else{
                    $html.="<td>&nbsp;</td>";
                    $html.="<td>".Yii::t("contract","normal")."</td>";
                }
                $html.="</tr>";
            }
        }
        return $html;
    }
}
