<?php

class ReviewSetProList extends CListPageModel
{
    public $type;//
    public $name;//
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
            'id'=>Yii::t('contract','ID'),
            'set_name'=>Yii::t('contract','set name'),
            'pro_name'=>Yii::t('contract','pro name'),
            'z_index'=>Yii::t('fete','level'),
		);
	}

    public function rules()
    {
        return array(
            array('attr, pageNum, noOfItem, totalRow, searchField, searchValue, orderField, orderType, filter, type','safe',),
        );
    }

	public function retrieveDataByPage($type,$pageNum=1)
	{
	    if(!is_numeric($type)){
            $type = 1;
        }
	    $this->type = $type;
	    $this->name = ReviewSetForm::getSetNameToId($this->type);
        $suffix = Yii::app()->params['envSuffix'];
        $city_allow = Yii::app()->user->city_allow();
		$sql1 = "select a.*,b.set_name from hr_set_pro a
                LEFT JOIN hr_set b on a.set_id = b.id 
                where a.set_id ='$type' 
			";
		$sql2 = "select count(*) from hr_set_pro a
                LEFT JOIN hr_set b on a.set_id = b.id 
                where a.set_id ='$type' 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'a.id':
					$clause .= General::getSqlConditionClause('a.id',$svalue);
					break;
				case 'pro_name':
					$clause .= General::getSqlConditionClause('a.pro_name',$svalue);
					break;
                case 'city_name':
                    $clause .= ' and a.code in '.WordForm::getCityCodeSqlLikeName($svalue);
                    break;
			}
		}
		
		$order = "";
		if (!empty($this->orderField)) {
			$order .= " order by ".$this->orderField." ";
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
					'set_name'=>$record['set_name'],
					'pro_name'=>$record['pro_name'],
					'z_index'=>$record['z_index']
				);
			}
		}
		$session = Yii::app()->session;
		$session['reviewSetPro_01'] = $this->getCriteria();
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
            'type'=>$this->type,
        );
    }
}
