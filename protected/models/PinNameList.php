<?php

class PinNameList extends CListPageModel
{
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
            'name'=>Yii::t('app','Pin Name'),
            'class_id'=>Yii::t('app','Pin Class'),
            'z_index'=>Yii::t('contract','Level'),
            'pin_type'=>Yii::t('contract','pin type'),
		);
	}

	public function retrieveDataByPage($pageNum=1)
	{
        $suffix = Yii::app()->params['envSuffix'];
        $city = Yii::app()->user->city();
		$sql1 = "select a.id,a.name,a.pin_type,a.z_index,b.name as class_name from hr_pin_name a 
                LEFT JOIN  hr_pin_class b ON a.class_id=b.id
                where a.id>0 
			";
		$sql2 = "select count(a.id) from hr_pin_name a 
                LEFT JOIN  hr_pin_class b ON a.class_id=b.id
                where a.id>0 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'name':
					$clause .= General::getSqlConditionClause('a.name',$svalue);
					break;
				case 'class_id':
					$clause .= General::getSqlConditionClause('b.name',$svalue);
					break;
				case 'z_index':
					$clause .= General::getSqlConditionClause('a.z_index',$svalue);
					break;
			}
		}
		
		$order = "";
		if (!empty($this->orderField)) {
			$order .= " order by ".$this->orderField." ";
			if ($this->orderType=='D') $order .= "desc ";
		}else{
            $order .= " order by z_index asc ";
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
					'name'=>$record['name'],
					'class_id'=>$record['class_name'],
					'pin_type'=>self::getPinType($record['pin_type'],true),
					'z_index'=>$record['z_index'],
				);
			}
		}
		$session = Yii::app()->session;
		$session['pinName_01'] = $this->getCriteria();
		return true;
	}

    public static function getPinType($key=0,$bool=false){
        $list = array(
            0=>Yii::t("contract","Ordinary Pin"),
            1=>Yii::t("contract","Two Pin"),
        );
        if($bool){
            if(key_exists($key,$list)){
                return $list[$key];
            }else{
                return $key;
            }
        }else{
            return $list;
        }
    }
}
