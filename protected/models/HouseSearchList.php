<?php

class HouseSearchList extends CListPageModel
{
	public $address;//戶籍地址
	public $department;//部門
	public $show;

	
	public function attributeLabels()
	{
		return array(
            'code'=>Yii::t('contract','Employee Code'),
            'name'=>Yii::t('contract','Employee Name'),
            'address'=>Yii::t('contract','Old Address'),
            'department'=>Yii::t('contract','Department'),
            'position'=>Yii::t('contract','Position'),
            'entry_time'=>Yii::t('contract','Entry Time'),
            'city_name'=>Yii::t('misc','City'),
            'phone'=>Yii::t('contract','Employee Phone'),
		);
	}
	
	public function rules()
	{	$rtn1 = parent::rules();
		$rtn2 =  array(
			array('address,department','safe',),
			);
		return array_merge($rtn1, $rtn2);
	}

	public function retrieveDataByPage($pageNum=1)
	{
		$suffix = Yii::app()->params['envSuffix'];
		$city_allow = Yii::app()->user->city_allow();
		if(Yii::app()->user->validFunction('ZE10')){
            $whereSql = "a.id>0 ";
        }else{
            $whereSql = "a.city in ({$city_allow}) ";
        }
		$sql1 = "select a.id,a.code,a.name,a.address,a.entry_time,a.phone,f.name as city_name,
                  b.name as dept_name,c.name as pos_name
				from hr_employee a
				left join hr_dept b on a.department=b.id
				left join hr_dept c on a.position=c.id
				left join security$suffix.sec_city f on a.city=f.code
				where {$whereSql} 
			";
		$sql2 = "select count(a.id)
				from hr_employee a
				left join hr_dept b on a.department=b.id
				left join hr_dept c on a.position=c.id
				left join security$suffix.sec_city f on a.city=f.code
				where {$whereSql} 
			";
		$clause = "";
		if (!empty($this->address)) {
			$svalue = str_replace("'","\'",$this->address);
			$clause .= (empty($clause) ? '' : ' and ')."a.address like '%$svalue%'";
		}
		if (!empty($this->department)) {
			$svalue = str_replace("'","\'",$this->department);
			$clause .= (empty($clause) ? '' : ' and ')."b.name like '%$svalue%'";
		}

		if ($clause!='') $clause = ' and ('.$clause.')'; 
		
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
					'city_name'=>$record['city_name'],
					'code'=>$record['code'],
					'name'=>$record['name'],
					'phone'=>$record['phone'],
					'address'=>$record['address'],
					'entry_time'=>CGeneral::toDate($record['entry_time']),
					'dept_name'=>$record['dept_name'],
					'pos_name'=>$record['pos_name'],
				);
			}
		}
		$session = Yii::app()->session;
		$session[$this->criteriaName()] = $this->getCriteria();
		return true;
	}
	
	public function getCriteria() {
		$rtn1 = parent::getCriteria();
		$rtn2 = array(
					'address'=>$this->address,
					'department'=>$this->department,
				);
		return array_merge($rtn1, $rtn2);
	}
}
