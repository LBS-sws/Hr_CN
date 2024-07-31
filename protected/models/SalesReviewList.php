<?php


class SalesReviewList extends CListPageModel
{

    public $city;
    public $year;
    public $year_type;


	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
            'id'=>Yii::t('contract','ID'),
            'city'=>Yii::t('contract','City'),
            'group_name'=>Yii::t('contract','group name'),
            'staff_num'=>Yii::t('contract','staff num'),
		);
	}
    public function __construct($scenario='')
    {
        if(empty($this->city)){
            $this->city = Yii::app()->user->city();
        }
        if(empty($this->year)){
            $this->year = date("Y");
        }
        if(empty($this->year_type)){
            $this->year_type = intval(date("m"))<7?1:2;
        }
        parent::__construct();
    }

    public function rules()
    {
        return array(
            array('attr, pageNum, noOfItem, totalRow, searchField, searchValue, orderField, orderType, filter, city, year, year_type','safe',),
        );
    }

    public function retrieveDataByPage($pageNum=1)
    {
        if($this->year == 2020){
            $this->year_type = 1;
        }
        $suffix = Yii::app()->params['envSuffix'];
        if(empty($this->city)){
            $this->city = Yii::app()->user->city();
        }
        $city = $this->city;
        $city_allow = Yii::app()->user->city_allow();
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
                case 'group_name':
                    $clause .= General::getSqlConditionClause('a.group_name',$svalue);
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

        $sql = $sql1.$clause." group by a.id ".$order;
        $sql = $this->sqlWithPageCriteria($sql, $this->pageNum);
        $records = Yii::app()->db->createCommand($sql)->queryAll();

        $this->attr = array();
        if (count($records) > 0) {
            $city_name = CGeneral::getCityName($city);
            foreach ($records as $k=>$record) {
                $this->attr[] = array(
                    'id'=>$record['id'],
                    'city_name'=>$city_name,
                    'group_name'=>$record['group_name'],
                    'staff_num'=>SalesGroupList::getGroupStaffNum($record['id'],$city)
                );
            }
        }
        $session = Yii::app()->session;
        $session['salesReview_01'] = $this->getCriteria();
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
            'year'=>$this->year,
            'city'=>$this->city,
            'year_type'=>$this->year_type,
        );
    }
}
