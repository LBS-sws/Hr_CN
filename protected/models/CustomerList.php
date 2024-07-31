<?php

class CustomerList extends CListPageModel
{

    public $search_code = "";
    public $search_name = "";
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
			'name'=>Yii::t('fete','customer name'),
			'code'=>Yii::t('fete','customer code'),
			'cont_name'=>Yii::t('fete','contact'),
			'cont_phone'=>Yii::t('fete','contact phone'),
		);
	}

	public function retrieveDataByPage($pageNum=1)
	{
        $suffix = Yii::app()->params['envSuffix'];
        $city_allow = Yii::app()->user->city_allow();
		$sql1 = "select * 
                from swoper$suffix.swo_company 
                where city in ($city_allow) 
			";
		$sql2 = "select count(*)
                from swoper$suffix.swo_company 
                where city in ($city_allow) 
			";
		$clause = "";
		if(!empty($this->search_code)){
            $svalue = str_replace("'","\'",$this->search_code);
            $clause .= General::getSqlConditionClause('code',$svalue);
        }
		if(!empty($this->search_name)){
            $svalue = str_replace("'","\'",$this->search_name);
            $clause .= General::getSqlConditionClause('name',$svalue);
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
					'name'=>$record['name'],
					'code'=>$record['code'],
					'cont_name'=>$record['cont_name'],
					'cont_phone'=>$record['cont_phone'],
				);
			}
		}
/*		$session = Yii::app()->session;
		$session['prize_01'] = $this->getCriteria();*/
		return true;
	}

	//獲取分頁列表
    public function getPageList(){
        $items = array();
	    $total = $this->totalRow;//總數據行數
	    $num = $this->noOfItem;//每頁顯示多少行
        $current = $this->pageNum;//當前頁碼
	    $pageNum = ceil($total/$num);//總共多少頁
        $min = $current-2;
        $min = $min<1?1:$min;
        $max = $min+4;
        $max = $max>$pageNum?$pageNum:$max;
        for ($i=$min;$i<=$max;$i++){
            $url = "javascript:resetTable($i);";
            $items[] = array('label'=>$i,'url'=>$url,'active'=>($current == $i));
        }
        return $items;
    }
}
