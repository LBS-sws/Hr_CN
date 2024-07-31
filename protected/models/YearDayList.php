<?php

class YearDayList extends CListPageModel
{
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(	
			'employee_code'=>Yii::t('contract','Employee Code'),
			'employee_name'=>Yii::t('contract','Employee Name'),
			'year'=>Yii::t('fete','Year'),
            'add_num'=>Yii::t('fete','Cumulative Day'),
		);
	}

	public function retrieveDataByPage($pageNum=1)
	{
        $city_allow = Yii::app()->user->city_allow();
		$sql1 = "select a.*,b.code AS employee_code,b.name AS employee_name from hr_staff_year a 
                LEFT JOIN hr_employee b ON a.employee_id = b.id 
                where a.id>0 AND b.city IN ($city_allow) 
			";
		$sql2 = "select count(a.id)
				from hr_staff_year a 
                LEFT JOIN hr_employee b ON a.employee_id = b.id 
                where a.id>0 AND b.city IN ($city_allow) 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'year':
					$clause .= General::getSqlConditionClause('a.year',$svalue);
					break;
				case 'add_num':
					$clause .= General::getSqlConditionClause('a.add_num',$svalue);
					break;
				case 'employee_code':
                    $clause .= General::getSqlConditionClause('b.code',$svalue);
					break;
				case 'employee_name':
                    $clause .= General::getSqlConditionClause('b.name',$svalue);
					break;
			}
		}
		
		$order = "";
		if (!empty($this->orderField)) {
			$order .= " order by ".$this->orderField." ";
			if ($this->orderType=='D') $order .= "desc ";
		}

		$sql = $sql2.$clause;
		$this->totalRow = Yii::app()->db->createCommand($sql)->queryScalar();
		
		$sql = $sql1.$clause.$order;
		$sql = $this->sqlWithPageCriteria($sql, $this->pageNum);
		$records = Yii::app()->db->createCommand($sql)->queryAll();

		$this->attr = array();
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
				$this->attr[] = array(
					'id'=>$record['id'],
					'employee_code'=>$record['employee_code'],
					'employee_name'=>$record['employee_name'],
					'year'=>$record['year'],
					'add_num'=>$record['add_num'],
				);
			}
		}
		$session = Yii::app()->session;
		$session['yearday_01'] = $this->getCriteria();
		return true;
	}


//員工名字（模糊查詢）
    public function getEmployeeNameSqlLikeName($name)
    {
        $rows = Yii::app()->db->createCommand()->select("id")->from("hr_employee")->where(array('like', 'name', "%$name%"))->queryAll();
        $arr = array();
        foreach ($rows as $row){
            array_push($arr,"'".$row["id"]."'");
        }
        if(empty($arr)){
            return "('')";
        }else{
            $arr = implode(",",$arr);
            return "($arr)";
        }
    }

    //根據id獲取員工名字
    public static function getEmployeeNameToId($id){
        $rows = Yii::app()->db->createCommand()->select("name")->from("hr_employee")
            ->where('id=:id', array(':id'=>$id))->queryRow();
        if($rows){
            return $rows["name"];
        }
        return $id;
    }
}
