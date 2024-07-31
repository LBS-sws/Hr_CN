<?php

class RecruitSummaryList extends CListPageModel
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
			'recruit_num'=>Yii::t('recruit','recruit num'),
			'now_num'=>Yii::t('recruit','now num'),
			'leave_num'=>Yii::t('recruit','leave num'),
			'lack_num'=>Yii::t('recruit','lack num'),
			'completion_rate'=>Yii::t('recruit','completion rate'),
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
        if(Yii::app()->user->validFunction('ZR22')){ //显示所有招聘登记
            $sqlCity="";//ZR22
        }else{
            $sqlCity=" AND city in ({$city_allow})";
        }
        $this->year = empty($this->year)?date("Y"):$this->year;
		$sql1 = "select a.year,a.city,a.recruit_sum,b.name as city_name from (
                  select SUM(recruit_num) as recruit_sum,year,city from hr_recruit WHERE year={$this->year} {$sqlCity} GROUP BY year,city
                ) a
				LEFT JOIN security{$suffix}.sec_city b ON a.city=b.code 
				WHERE a.year>0 
			";
		$sql2 = "select count(a.city) from (
                  select city,year from hr_recruit WHERE year={$this->year} {$sqlCity} GROUP BY year,city
                ) a
				LEFT JOIN security{$suffix}.sec_city b ON a.city=b.code 
				WHERE a.year>0 
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
                    'year'=>$record['year'],
                    'city_name'=>$record['city_name'],
                    'city'=>$record['city'],
                    'recruit_sum'=>$record['recruit_sum'],
                    'now_sum'=>0,
                    'leave_sum'=>0,
                    'lack_sum'=>0,
                    'sum_rate'=>'',
                );
                $detail = $this->getDetailList($record,$attrList);
                $attrList['detail']=$detail;
                $attrList['sum_rate']=empty($attrList["recruit_sum"])?"error":round(($attrList["now_sum"]-$attrList["leave_sum"])/$attrList["recruit_sum"],2)*100;
                $attrList['sum_rate'].=empty($attrList["recruit_sum"])?"":"%";
                $this->attr[] = $attrList;
			}
		}
		$session = Yii::app()->session;
		$session['recruitSummary_c01'] = $this->getCriteria();
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
        );
    }

	protected function getDetailList($record,&$attrList){
	    $detail = array();
        $rows = Yii::app()->db->createCommand()
            ->select("a.id,a.dept_id,a.city,a.recruit_num,a.year,b.name as dept_name,g.name as leader_name")
            ->from("hr_recruit a")
            ->leftJoin("hr_dept b","a.dept_id=b.id")
            ->leftJoin("hr_dept g","b.dept_id=g.id")
            ->where("a.year=:year and a.city=:city",array(":year"=>$record["year"],":city"=>$record["city"]))
            ->queryAll();
        if($rows){
            foreach ($rows as $row){
                $arr = RecruitApplyList::recruitLoading($row);
                $attrList["now_sum"]+=$arr['now_num'];
                $attrList["leave_sum"]+=$arr['leave_num'];
                $attrList["lack_sum"]+=$arr['lack_num'];
                $detail[]=array(
                    'id'=>$row['id'],
                    'recruit_num'=>$row['recruit_num'],
                    'dept_name'=>$row['dept_name'],
                    'leader_name'=>$row['leader_name'],
                    'now_num'=>$arr['now_num'],
                    'leave_num'=>$arr['leave_num'],
                    'lack_num'=>$arr['lack_num'],
                    'completion_rate'=>$arr['completion_rate'],
                );
            }
        }
        return $detail;
    }


    public function getYearList(){
        $year = date("Y");
        $arr = array();
        for ($i = $year-5;$i<$year+5;$i++){
            if($i<=2021){
                continue;
            }
            $arr[$i] = $i.Yii::t("contract"," year");
        }
        return $arr;
    }
}
