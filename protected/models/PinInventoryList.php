<?php

class PinInventoryList extends CListPageModel
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
            'residue_num'=>Yii::t('contract','remaining inventory'),
            'class_id'=>Yii::t('app','Pin Class'),
            'pin_name_id'=>Yii::t('app','Pin Name'),
            'z_index'=>Yii::t('contract','Level'),
            'inventory'=>Yii::t('contract','inventory num'),
		);
	}

	public function retrieveDataByPage($pageNum=1)
	{
        $suffix = Yii::app()->params['envSuffix'];
        $city = Yii::app()->user->city();
        $city_name = Yii::app()->user->city_name();
		$sql1 = "select a.residue_num,a.safe_stock,b.id,g.name as city_name,b.name as pin_name,f.name as class_name,ifnull(a.inventory,'null') as inventory,a.z_index
                from (select *,'$city' as cityPx from hr_pin_name) b 
                LEFT JOIN hr_pin_inventory a ON a.pin_name_id=b.id AND a.city=b.cityPx
                LEFT JOIN hr_pin_class f ON b.class_id=f.id
                LEFT JOIN security$suffix.sec_city g ON a.city=g.code
                where (b.cityPx='$city') 
			";
		$sql2 = "select count(b.id) from (select *,'$city' as cityPx from hr_pin_name) b 
                LEFT JOIN hr_pin_inventory a ON a.pin_name_id=b.id AND a.city=b.cityPx
                LEFT JOIN hr_pin_class f ON b.class_id=f.id
                LEFT JOIN security$suffix.sec_city g ON a.city=g.code
                where (b.cityPx='$city') 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'class_id':
					$clause .= General::getSqlConditionClause('f.name',$svalue);
					break;
				case 'pin_name_id':
					$clause .= General::getSqlConditionClause('b.name',$svalue);
					break;
				case 'inventory':
					$clause .= General::getSqlConditionClause('a.inventory',$svalue);
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
					'style'=>$record['residue_num']>$record['safe_stock']||$record['inventory']=="null"?"":"danger",
					'class_id'=>$record['class_name'],
					'pin_name_id'=>$record['pin_name'],
					'inventory'=>$record['inventory']=="null"?"未设置":$record['inventory'],
					'residue_num'=>$record['inventory']=="null"?"":$record['residue_num'],
					'city'=>$record['inventory']=="null"?$city_name:$record['city_name'],
					'z_index'=>$record['z_index'],
				);
			}
		}
		$session = Yii::app()->session;
		$session['pinInventory_01'] = $this->getCriteria();
		return true;
	}
}
