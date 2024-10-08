<?php

class GroupStaffList extends CListPageModel
{
    public $index=0;
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */

    public function rules()
    {
        return array(
            array('index,attr, pageNum, noOfItem, totalRow, searchField, searchValue, orderField, orderType, filter, dateRangeValue','safe',),
        );
    }
	public function attributeLabels()
	{
		return array(
			'employee_code'=>Yii::t('group','employee code'),
			'employee_name'=>Yii::t('group','employee name'),
			'branch_text'=>Yii::t('group','branch text'),
		);
	}
	
	public function retrieveDataByPage($index,$pageNum=1)
	{
        $index = empty($index)||!is_numeric($index)?0:intval($index);
	    $this->index = $index;
		$suffix = Yii::app()->params['envSuffix'];
		$sql1 = "select a.*,b.code as employee_code,b.name as employee_name
				from hr_group_staff a  
				LEFT JOIN hr_employee b ON a.employee_id=b.id
				where a.group_id='{$index}'  
			";
		$sql2 = "select count(a.id)
				from hr_group_staff a 
				LEFT JOIN hr_employee b ON a.employee_id=b.id
				where a.group_id='{$index}'  
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'employee_code':
					$clause .= General::getSqlConditionClause('b.code',$svalue);
					break;
				case 'employee_name':
					$clause .= General::getSqlConditionClause('b.name',$svalue);
					break;
				case 'branch_text':
					$clause .= General::getSqlConditionClause('a.branch_text',$svalue);
					break;
			}
		}
		
		$order = "";
		if (!empty($this->orderField)) {
            $order .= " order by {$this->orderField} ";
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
					$this->attr[] = array(
						'id'=>$record['id'],
						'employee_code'=>$record['employee_code'],
						'employee_name'=>$record['employee_name'],
                        'branch_text'=>$record['branch_text'],
					);
			}
		}
		$session = Yii::app()->session;
		$session['groupStaff_01'] = $this->getCriteria();
		return true;
	}


    public function getCriteria() {
        return array(
            'index'=>$this->index,
            'searchField'=>$this->searchField,
            'searchValue'=>$this->searchValue,
            'orderField'=>$this->orderField,
            'orderType'=>$this->orderType,
            'noOfItem'=>$this->noOfItem,
            'pageNum'=>$this->pageNum,
            'filter'=>$this->filter,
            'dateRangeValue'=>$this->dateRangeValue,
        );
    }
}
