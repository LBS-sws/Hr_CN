<?php


class SalesGroupList extends CListPageModel
{
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
            'id'=>Yii::t('contract','ID'),
            'group_name'=>Yii::t('contract','group name'),
            'staff_num'=>Yii::t('contract','staff num'),
            'local'=>Yii::t('contract','group restrict'),
            'city'=>Yii::t('user','City'),
		);
	}

	public function retrieveDataByPage($pageNum=1)
	{
        $suffix = Yii::app()->params['envSuffix'];
        $city = Yii::app()->user->city();
		$sql1 = "select a.* from hr_sales_group a
                where a.city='$city'  
			";
		$sql2 = "select count(a.id) from hr_sales_group a
                where a.city='$city'  
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'id':
					$clause .= General::getSqlConditionClause('id',$svalue);
					break;
				case 'set_name':
					$clause .= General::getSqlConditionClause('a.set_name',$svalue);
					break;
                case 'city':
                    $clause .= ' and a.city in '.WordForm::getCityCodeSqlLikeName($svalue);
                    break;
			}
		}
		
		$order = "";
		if (!empty($this->orderField)) {
		    if($this->orderField == 'staff_num'){
                $order .= " order by a.id ";
            }else{
                $order .= " order by ".$this->orderField." ";
            }
			if ($this->orderType=='D') $order .= "desc ";
		}else{
            $order .= " order by a.id desc ";
        }

		$sql = $sql2.$clause;
		$this->totalRow = Yii::app()->db->createCommand($sql)->queryScalar();

        $sql = $sql1.$clause." group by a.id ".$order;
		$sql = $this->sqlWithPageCriteria($sql, $this->pageNum);
		$records = Yii::app()->db->createCommand($sql)->queryAll();

		$this->attr = array();
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
				$this->attr[] = array(
					'id'=>$record['id'],
					'group_name'=>$record['group_name'],
					'city'=>CGeneral::getCityName($record['city']),
					'local'=>empty($record['local'])?Yii::t("contract","default"):Yii::t("contract","local"),
					'staff_num'=>SalesGroupList::getGroupStaffNum($record['id'],$city)
				);
			}
		}
		$session = Yii::app()->session;
		$session['salesGroup_01'] = $this->getCriteria();
		return true;
	}

	public function getGroupStaffNum($group_id,$city){
        $staff_num = Yii::app()->db->createCommand()->select("count(a.id)")->from("hr_sales_staff a")
            ->leftJoin("hr_employee b","a.employee_id = b.id")
            ->where('a.group_id=:group_id',array(':group_id'=>$group_id))->queryScalar();
        return $staff_num;
    }
}
