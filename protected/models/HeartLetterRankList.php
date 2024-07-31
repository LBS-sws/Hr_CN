<?php

class HeartLetterRankList extends CListPageModel
{
    public $year;//年
    /**
     * Declares customized attribute labels.
     * If not declared here, an attribute would have a label that is
     * the same as its name with the first letter in upper case.
     */
    public function attributeLabels()
    {
        return array(
            'letter_type'=>Yii::t('contract','type for director'),
            'letter_title'=>Yii::t('queue','Subject'),
            'employee_name'=>Yii::t('contract','Employee Name'),
            'employee_code'=>Yii::t('contract','Employee Code'),

            'state'=>Yii::t('contract','Status'),
            'city'=>Yii::t('contract','City'),
            'lcd'=>Yii::t('fete','apply for time'),
            'user_num'=>Yii::t('contract','review number'),
        );
    }

    public function rules()
    {
        return array(
            array('attr, pageNum, noOfItem, totalRow, searchField, searchValue, orderField, orderType, year','safe',),
        );
    }
    public function retrieveDataByPage($pageNum=1)
    {
        $suffix = Yii::app()->params['envSuffix'];
        $lcuId = Yii::app()->user->id;
        $city_allow = Yii::app()->user->city_allow();
        //,docman$suffix.countdoc('LEAVE',a.id) as leavedoc
        $sql1 = "select sum(a.letter_num) as user_num,b.id,b.name AS employee_name,b.code AS employee_code,b.city AS s_city 
              from hr_letter a 
              LEFT JOIN hr_employee b ON a.employee_id = b.id 
              where b.city in ($city_allow) and a.state = 4";
        $sql2 = "select b.id,b.name AS employee_name,b.code AS employee_code,b.city AS s_city from hr_letter a 
              LEFT JOIN hr_employee b ON a.employee_id = b.id 
              where b.city in ($city_allow) and a.state = 4";
        $clause = "";
        if (!empty($this->searchField) && !empty($this->searchValue)) {
            $svalue = str_replace("'","\'",$this->searchValue);
            switch ($this->searchField) {
                case 'employee_name':
                    $clause .= General::getSqlConditionClause('b.name',$svalue);
                    break;
                case 'employee_code':
                    $clause .= General::getSqlConditionClause('b.code',$svalue);
                    break;
                case 'city':
                    $clause .= ' and b.city in '.WordForm::getCityCodeSqlLikeName($svalue);
                    break;
            }
        }
        if (!empty($this->year)) {
            $svalue = str_replace("'","\'",$this->year);
            $clause .= " and date_format(a.lcd,'%Y') ='$svalue' ";
        }

        $order = "";
        if (!empty($this->orderField)) {
            $order .= " order by ".$this->orderField." ";
            if ($this->orderType=='D') $order .= "desc ";
        }else{
            $order .= " order by user_num desc ";
        }

        $sql = $sql2.$clause." GROUP BY b.id,b.name,b.code,b.city";
        $sql = "select count(test.s_city) from ($sql) test";
        $this->totalRow = Yii::app()->db->createCommand($sql)->queryScalar();

        $sql = $sql1.$clause." GROUP BY b.id,b.name,b.code,b.city ".$order;
        $sql = $this->sqlWithPageCriteria($sql, $this->pageNum);
        $records = Yii::app()->db->createCommand($sql)->queryAll();

        $this->attr = array();
        if (count($records) > 0) {
            foreach ($records as $k=>$record) {
                $this->attr[] = array(
                    'id'=>$record['id'],
                    'employee_name'=>$record['employee_name'],
                    'employee_code'=>$record['employee_code'],
                    'city'=>CGeneral::getCityName($record["s_city"]),
                    'user_num'=>$record['user_num'],
                );
            }
        }
        $session = Yii::app()->session;
        $session['heartLetterRank_op01'] = $this->getCriteria();
        return true;
    }
/*
    public function getYearList(){
        $sql = "select year from gr_credit_point_ex GROUP BY year ORDER by year asc";
        $rows = Yii::app()->db->createCommand($sql)->queryAll();

        $arr[]=array('value'=>"",'name'=>"所有","color"=>"#555");
        $maxYear = intval(date("Y"))-5;
        foreach ($rows as $row){
            $arr[]=array('value'=>$row["year"],'name'=>$row["year"].Yii::t("integral","year"),"color"=>intval($row['year'])<=$maxYear?"#a94442":"#555");
        }
        return $arr;
    }*/

    public function getYearList(){
/*        $sql = "select date_format(lcd,'%Y') as apply_year from hr_letter GROUP BY apply_year ORDER by apply_year asc";
        $rows = Yii::app()->db->createCommand($sql)->queryAll();*/

        $arr[0]="所有";
        $year = intval(date("Y"));
        for ($i=$year-3;$i<$year+5;$i++){
            $arr[$i]=$i.Yii::t('contract',' year');
        }
        return $arr;
    }
}
