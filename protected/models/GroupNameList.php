<?php

class GroupNameList extends CListPageModel
{
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
			'group_code'=>Yii::t('group','GroupCode'),
			'group_name'=>Yii::t('group','GroupName'),
			'group_remark'=>Yii::t('group','GroupRemark'),
			'group_sum'=>Yii::t('group','GroupSum'),
		);
	}
	
	public function retrieveDataByPage($pageNum=1)
	{
		$suffix = Yii::app()->params['envSuffix'];
		$sql1 = "select * 
				from hr_group 
				where 1=1 
			";
		$sql2 = "select count(id)
				from hr_group 
				where 1=1 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'group_code':
					$clause .= General::getSqlConditionClause('group_code',$svalue);
					break;
				case 'group_name':
					$clause .= General::getSqlConditionClause('group_name',$svalue);
					break;
				case 'group_remark':
					$clause .= General::getSqlConditionClause('group_remark',$svalue);
					break;
			}
		}
		
		$order = "";
		if (!empty($this->orderField)) {
            $order .= " order by {$this->orderField} ";
			if ($this->orderType=='D') $order .= "desc ";
		}else{
            $order .= " order by id desc ";
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
						'group_code'=>$record['group_code'],
						'group_name'=>$record['group_name'],
						'group_remark'=>$record['group_remark'],
                        'group_sum'=>$record['group_sum'],
					);
			}
		}
		$session = Yii::app()->session;
		$session['groupName_c01'] = $this->getCriteria();
		return true;
	}

}
