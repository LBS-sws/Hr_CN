<?php

class TreatyStopList extends CListPageModel
{
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
			'treaty_code'=>Yii::t('treaty','treaty code'),
			'treaty_name'=>Yii::t('treaty','treaty name'),
			'month_num'=>Yii::t('treaty','month num'),
			'treaty_num'=>Yii::t('treaty','treaty num'),
			'city'=>Yii::t('treaty','city'),
			'apply_date'=>Yii::t('treaty','apply date'),
			'start_date'=>Yii::t('treaty','start date'),
			'end_date'=>Yii::t('treaty','end date'),
			'state_type'=>Yii::t('treaty','treaty state'),
            'lcu'=>Yii::t('treaty','treaty lcu'),
		);
	}
	
	public function retrieveDataByPage($pageNum=1)
	{
		$suffix = Yii::app()->params['envSuffix'];
        $city_allow = Yii::app()->user->city_allow();
        $uid = Yii::app()->user->id;
        if(Yii::app()->user->validFunction('ZR21')){ //允許查看管轄內的所有項目
            $whereSql = " and a.city in ({$city_allow}) ";
        }else{
            $whereSql = " and a.lcu='{$uid}' ";
        }
		$sql1 = "select a.*,b.name as city_name 
				from hr_treaty a
				LEFT JOIN security{$suffix}.sec_city b on a.city=b.code
				where a.state_type=3 {$whereSql}
			";
		$sql2 = "select count(a.id)
				from hr_treaty a
				LEFT JOIN security{$suffix}.sec_city b on a.city=b.code
				where a.state_type=3 {$whereSql}
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'treaty_code':
					$clause .= General::getSqlConditionClause('a.treaty_code',$svalue);
					break;
				case 'treaty_name':
					$clause .= General::getSqlConditionClause('a.treaty_name',$svalue);
					break;
				case 'city':
					$clause .= General::getSqlConditionClause('b.name',$svalue);
					break;
                case 'lcu':
                    $clause .= General::getSqlConditionClause('a.lcu',$svalue);
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
                    'treaty_code'=>$record['treaty_code'],
                    'treaty_name'=>$record['treaty_name'],
                    'city'=>$record['city_name'],
                    'lcu'=>$record['lcu'],
                    'treaty_num'=>empty($record['treaty_num'])?"":$record['treaty_num'],
                    'apply_date'=>empty($record['apply_date'])?"":CGeneral::toDate($record['apply_date']),
                    'start_date'=>empty($record['start_date'])?"":CGeneral::toDate($record['start_date']),
                    'end_date'=>empty($record['end_date'])?"":CGeneral::toDate($record['end_date']),
                    'state_type'=>Yii::t("treaty","treaty stop"),
                    'color'=>"",
                );
			}
		}
		$session = Yii::app()->session;
		$session['treatyStop_c01'] = $this->getCriteria();
		return true;
	}

}
