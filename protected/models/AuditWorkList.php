<?php

class AuditWorkList extends CListPageModel
{
    protected static $assList=array(
        1=>"ZA08",
        2=>"ZE05",
        3=>"ZG04",
        4=>"ZC10",
        5=>"ZP01",
    );

    public $only = 1;//1：地區審核  2：總部審核
    /**
     * Declares customized attribute labels.
     * If not declared here, an attribute would have a label that is
     * the same as its name with the first letter in upper case.
     */
    public function attributeLabels()
    {
        return array(
            'work_code'=>Yii::t('fete','Work Code'),
            'work_type'=>Yii::t('fete','Work Type'),
            'employee_name'=>Yii::t('contract','Employee Name'),
            'employee_code'=>Yii::t('contract','Employee Code'),
            'start_time'=>Yii::t('contract','Start Time'),
            'end_time'=>Yii::t('contract','End Time'),
            'log_time'=>Yii::t('fete','Log Date'),
            'status'=>Yii::t('contract','Status'),
            'city'=>Yii::t('contract','City'),
            'city_name'=>Yii::t('contract','City'),
            'lcd'=>Yii::t('fete','apply for time'),
        );
    }

    public function getAcc(){
        if(!key_exists($this->only,self::$assList)){
            $this->only = 1;
        }
        return self::$assList[$this->only];
    }

    public function retrieveDataByPage($pageNum=1)
    {
        $staffList = BindingForm::getEmployeeListToUsername();
        if(!empty($staffList)){
            $staff_id = $staffList["id"];
            $department = $staffList["department"];//部門
        }else{
            $staff_id = 0;
            $department = 0;
        }
        $only = $this->only;
        $city_allow = Yii::app()->user->city_allow();
        $city = Yii::app()->user->city();
        $sql1 = "select a.*,b.name AS employee_name,b.code AS employee_code,b.city AS s_city
                from hr_employee_work a LEFT JOIN hr_employee b ON a.employee_id = b.id
                where a.status in (1,3) AND a.z_index =$only 
			";
        $sql2 = "select count(a.id)
                from hr_employee_work a LEFT JOIN hr_employee b ON a.employee_id = b.id
				where a.status in (1,3) AND a.z_index =$only 
			";
        if($only!=4){ //“你”权限可以审核自己
            $sql1.=" and a.employee_id !='$staff_id' ";
            $sql2.=" and a.employee_id !='$staff_id' ";
        }
        switch ($only){
            case 1: //部門審核
                $sql1.=" AND b.department = '$department' ";
                $sql2.=" AND b.department = '$department' ";
                break;
            case 2: //主管
                $sql1.=" AND b.city = '$city' ";
                $sql2.=" AND b.city = '$city' ";
                break;
            case 5: //人事
                $sql1.=" AND b.city = '$city' ";
                $sql2.=" AND b.city = '$city' ";
                break;
            case 3: //總監
                $sql1.=" AND b.city in ($city_allow) ";
                $sql2.=" AND b.city in ($city_allow) ";
                break;
        }
        $clause = "";
        if (!empty($this->searchField) && !empty($this->searchValue)) {
            $svalue = str_replace("'","\'",$this->searchValue);
            switch ($this->searchField) {
                case 'work_code':
                    $clause .= General::getSqlConditionClause('a.work_code',$svalue);
                    break;
                case 'employee_name':
                    $clause .= General::getSqlConditionClause('b.name',$svalue);
                    break;
                case 'employee_code':
                    $clause .= General::getSqlConditionClause('b.code',$svalue);
                    break;
                case 'city_name':
                    $clause .= ' and b.city in '.WordForm::getCityCodeSqlLikeName($svalue);
                    break;
            }
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

        $costNumList = WorkList::getWorkTypeList();
        $this->attr = array();
        if (count($records) > 0) {
            foreach ($records as $k=>$record) {
                WorkList::resetWorkDate($record);
                $colorList = $this->statusToColor($record['status']);
                $record['start_time'] = date("Y/m/d H:i:s",strtotime($record['start_time']));
                $record['end_time'] = date("Y/m/d H:i:s",strtotime($record['end_time']));
                $dayStr =Yii::t("contract","Hour");
                $this->attr[] = array(
                    'id'=>$record['id'],
                    'work_code'=>$record['work_code'],
                    'employee_code'=>$record['employee_code'],
                    'employee_name'=>$record['employee_name'],
                    'start_time'=>$record['start_time'],
                    'end_time'=>$record['end_time'],
                    'lcd'=>CGeneral::toDateTime($record['lcd']),
                    'log_time'=>$record['log_time'].$dayStr,
                    'work_type'=>$costNumList[$record['work_type']],
                    'status'=>$colorList["status"],
                    'city'=>CGeneral::getCityName($record["s_city"]),
                    'style'=>$colorList["style"],
                );
            }
        }
        $session = Yii::app()->session;
        $session['auditwork_01'] = $this->getCriteria();
        return true;
    }

    //根據狀態獲取顏色
    public function statusToColor($status){
        switch ($status){
            case 1:
                return array(
                    "status"=>Yii::t("contract","pending approval"),//等待審核
                    "style"=>" text-primary"
                );
            case 2:
                return array(
                    "status"=>Yii::t("contract","Finish approval"),//審核通過
                    "style"=>" text-yellow"
                );
            case 3:
                return array(
                    "status"=>Yii::t("contract","Rejected"),//拒絕
                    "style"=>" text-danger"
                );
            case 4:
                return array(
                    "status"=>Yii::t("contract","Finish"),//完成
                    "style"=>" text-green"
                );
        }
        return array(
            "status"=>"",
            "style"=>""
        );
    }
}
