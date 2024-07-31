<?php

class EmailList extends CListPageModel
{
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
            'city'=>Yii::t('contract','City'),
            'subject'=>Yii::t('queue','Subject'),
            'city_name'=>Yii::t('contract','City'),
            'city_str'=>Yii::t('contract','send city'),
            'staff_str'=>Yii::t('contract','send staff'),
            'status_type'=>Yii::t('contract','Status'),
		);
	}

	public function retrieveDataByPage($pageNum=1)
	{
        $suffix = Yii::app()->params['envSuffix'];
        $city = Yii::app()->user->city;
        $city_allow = Yii::app()->user->city_allow();
		$sql1 = "select * from hr_email 
                where city='$city' 
			";
		$sql2 = "select count(*) from hr_email 
                where city='$city' 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'subject':
					$clause .= General::getSqlConditionClause('subject',$svalue);
					break;
				case 'city_str':
					$clause .= General::getSqlConditionClause('city_str',$svalue);
					break;
				case 'staff_str':
					$clause .= General::getSqlConditionClause('staff_str',$svalue);
					break;
			}
		}
		
		$order = "";
		if (!empty($this->orderField)) {
			$order .= " order by ".$this->orderField." ";
			if ($this->orderType=='D') $order .= "desc ";
		}else{
            $order = " order by id desc";
        }

		$sql = $sql2.$clause;
		$this->totalRow = Yii::app()->db->createCommand($sql)->queryScalar();
		
		$sql = $sql1.$clause.$order;
		$sql = $this->sqlWithPageCriteria($sql, $this->pageNum);
		$records = Yii::app()->db->createCommand($sql)->queryAll();

		$this->attr = array();
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
                $arr = $this->getEmailStatus($record["status_type"]);
				$this->attr[] = array(
					'id'=>$record['id'],
					'city'=>$record['city'],
					'subject'=>$record['subject'],
					'city_str'=>$record['city_str'],
					'staff_str'=>$record['staff_str'],
					'status_type'=>$arr['status'],
					'style'=>$arr['style'],
				);
			}
		}
		$session = Yii::app()->session;
		$session['email_01'] = $this->getCriteria();
		return true;
	}

    public function getEmailStatus($str){
        switch ($str){
            case 3:
                return array(
                    "status"=>Yii::t("fete","been sent"),
                    "style"=>"text-success"
                );//已發送
                break;
            case 4:
                return array(
                    "status"=>Yii::t("contract","Draft"),
                    "style"=>""
                );//草稿
                break;
            default:
                return array(
                    "status"=>Yii::t("contract","not sent"),
                    "style"=>"text-danger"
                );//未發送
        }
    }
}
