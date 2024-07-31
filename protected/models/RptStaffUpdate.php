<?php
class RptStaffUpdate extends CReport {

    protected $_contractList=array();
    protected $_deptList=array();
    protected $_companyList=array();
    protected $_cityList=array();

    public function __construct(){
        $suffix = Yii::app()->params['envSuffix'];
        $rows = Yii::app()->db->createCommand()->select("code,name")
            ->from("security{$suffix}.sec_city")->queryAll();
        if($rows){
            foreach ($rows as $row){
                $this->_cityList[$row["code"]]=$row["name"];
            }
        }
        $rows = Yii::app()->db->createCommand()->select("id,name")
            ->from("hr_company")->queryAll();
        if($rows){
            foreach ($rows as $row){
                $this->_companyList[$row["id"]]=$row["name"];
            }
        }
        $rows = Yii::app()->db->createCommand()->select("id,name")
            ->from("hr_dept")->queryAll();
        if($rows){
            foreach ($rows as $row){
                $this->_deptList[$row["id"]]=$row["name"];
            }
        }
        $rows = Yii::app()->db->createCommand()->select("id,name")
            ->from("hr_contract")->queryAll();
        if($rows){
            foreach ($rows as $row){
                $this->_contractList[$row["id"]]=$row["name"];
            }
        }
    }

    protected $dataList=array(
        "contList"=>array(),//合同协议(合同变更)
        "deptList"=>array(),//工作履历(职位变更)
    );

    protected function addDataForContract($row,$nextRow,$bool=false){
        if($row["fix_time"]=="fixation"){
            $fix_time = "有固定期限";
        }else{
            $fix_time = "无固定期限";
        }
        $bool = $bool||$row["company_id"]!=$nextRow["company_id"];//公司不同
        $bool = $bool||$row["contract_id"]!=$nextRow["contract_id"];//合同不同
        $bool = $bool||$row["fix_time"]!=$nextRow["fix_time"];//合同期限不同
        $bool = $bool||$row["start_time"]!=$nextRow["start_time"];//合同起始时间不同
        $bool = $bool||$row["end_time"]!=$nextRow["end_time"];//合同起始时间不同
        if($bool){
            $key = count($this->dataList["contList"]);
            if($key>=1){
                $key--;
                if($this->dataList["contList"][$key]["employee_id"]==$row["employee_id"]){
                    //unset($this->dataList["contList"][$key]);
                    array_pop($this->dataList["contList"]);
                }
            }
            $this->dataList["contList"][]=array(
                "city_name"=>$this->getListStrForKey($row["city"],$this->_cityList),
                "employee_id"=>$row["employee_id"],
                "employee_name"=>$row["name"],
                "employee_code"=>$row["code"],
                "company_name"=>$this->getListStrForKey($row["company_id"],$this->_companyList),
                "contract_name"=>$this->getListStrForKey($row["contract_id"],$this->_contractList),
                "fix_time"=>$fix_time,
                "start_time"=>$row["start_time"],
                "end_time"=>$row["end_time"],
                "lcd"=>$row["lcd"],
                "lud"=>$row["lud"],
            );
            $this->dataList["contList"][]=array(
                "city_name"=>$this->getListStrForKey($nextRow["city"],$this->_cityList),
                "employee_id"=>$nextRow["employee_id"],
                "employee_name"=>$nextRow["name"],
                "employee_code"=>$nextRow["code"],
                "company_name"=>$this->getListStrForKey($nextRow["company_id"],$this->_companyList),
                "contract_name"=>$this->getListStrForKey($nextRow["contract_id"],$this->_contractList),
                "fix_time"=>$fix_time,
                "start_time"=>$nextRow["start_time"],
                "end_time"=>$nextRow["end_time"],
                "lcd"=>$nextRow["lcd"],
                "lud"=>$nextRow["lud"],
            );
        }
    }

    protected function addDataForDept($row,$nextRow,$bool=false){
        $bool = $bool||$row["department"]!=$nextRow["department"];//部门不同
        $bool = $bool||$row["position"]!=$nextRow["position"];//职位不同
        if($bool){
            $key = count($this->dataList["deptList"]);
            if($key>=1){
                $key--;
                if($this->dataList["deptList"][$key]["employee_id"]==$row["employee_id"]){
                    array_pop($this->dataList["deptList"]);
                    $dateObj = new DateTime($row["lcd"]);
                    $end_time=$dateObj->modify("-1 day");
                    $end_time = $end_time->format('Y/m/d');
                    if(isset($this->dataList["deptList"][$key-1])){
                        $this->dataList["deptList"][$key-1]["end_date"]=$end_time;
                    }
                }
            }
            $dateObj = new DateTime($nextRow["lcd"]);
            $end_time=$dateObj->modify("-1 day");
            $end_time = $end_time->format('Y/m/d');

            $this->dataList["deptList"][]=array(
                "city_name"=>$this->getListStrForKey($row["city"],$this->_cityList),
                "employee_id"=>$row["employee_id"],
                "employee_name"=>$row["name"],
                "employee_code"=>$row["code"],
                "email"=>$row["email"],
                "company_name"=>$this->getListStrForKey($row["company_id"],$this->_companyList),
                "department_name"=>$this->getListStrForKey($row["department"],$this->_deptList),
                "position_name"=>$this->getListStrForKey($row["position"],$this->_deptList),
                "start_date"=>CGeneral::toDate($row["lcd"]),
                "end_date"=>$end_time,
                "lcd"=>$row["lcd"],
                "lud"=>$row["lud"],
            );
            $this->dataList["deptList"][]=array(
                "city_name"=>$this->getListStrForKey($nextRow["city"],$this->_cityList),
                "employee_id"=>$nextRow["employee_id"],
                "employee_name"=>$nextRow["name"],
                "employee_code"=>$nextRow["code"],
                "email"=>$nextRow["email"],
                "company_name"=>$this->getListStrForKey($nextRow["company_id"],$this->_companyList),
                "department_name"=>$this->getListStrForKey($nextRow["department"],$this->_deptList),
                "position_name"=>$this->getListStrForKey($nextRow["position"],$this->_deptList),
                "start_date"=>CGeneral::toDate($nextRow["lcd"]),
                "end_date"=>"",
                "lcd"=>$nextRow["lcd"],
                "lud"=>$nextRow["lud"],
            );
        }
    }

    protected function getListStrForKey($key,$list){
        $key="".$key;
        if(key_exists($key,$list)){
            return $list[$key];
        }else{
            return $key;
        }
    }

    protected function getSheetExpr(){//额外的sheet
        $sheetList=array(
            array(//合同协议
                "header_title"=>"合同协议",
                "data"=>$this->dataList["contList"],
                "line_def"=>array(//页头
                    'city_name'=>array('label'=>Yii::t('app','City'),'width'=>13,'align'=>'C'),//城市
                    'employee_name'=>array('label'=>Yii::t('report','Name'),'width'=>20,'align'=>'C'),//姓名
                    'employee_code'=>array('label'=>Yii::t('code','Code'),'width'=>20,'align'=>'C'),//编号
                    'company_name'=>array('label'=>Yii::t('contract','Company Name'),'width'=>32,'align'=>'C'),//法人公司
                    'contract_name'=>array('label'=>Yii::t('contract','contract type'),'width'=>25,'align'=>'C'),//合同类型
                    'fix_time'=>array('label'=>Yii::t('contract','contract deadline'),'width'=>25,'align'=>'C'),//合同期限类型
                    'start_time'=>array('label'=>Yii::t('report','Contract Start Date'),'width'=>25,'align'=>'C'),//合同生效日期
                    'end_time'=>array('label'=>Yii::t('report','Contract End Date'),'width'=>25,'align'=>'C'),//合同终止日期
                    'lcd'=>array('label'=>Yii::t('report','update date'),'width'=>25,'align'=>'C'),//修改日期
                    'lud'=>array('label'=>Yii::t('report','audit date'),'width'=>25,'align'=>'C'),//审批日期
                ),
            ),
            array(//工作履历
                "header_title"=>"工作履历",
                "data"=>$this->dataList["deptList"],
                "line_def"=>array(//页头
                    'city_name'=>array('label'=>Yii::t('app','City'),'width'=>13,'align'=>'C'),//城市
                    'employee_name'=>array('label'=>Yii::t('report','Name'),'width'=>20,'align'=>'C'),//姓名
                    'employee_code'=>array('label'=>Yii::t('code','Code'),'width'=>20,'align'=>'C'),//编号
                    'email'=>array('label'=>Yii::t('report','Email'),'width'=>32,'align'=>'C'),//电子邮箱
                    'company_name'=>array('label'=>Yii::t('contract','Company Name'),'width'=>32,'align'=>'C'),//法人公司
                    'department_name'=>array('label'=>Yii::t('report','Department'),'width'=>25,'align'=>'C'),//部门
                    'position_name'=>array('label'=>Yii::t('report','Position'),'width'=>25,'align'=>'C'),//职位
                    'start_date'=>array('label'=>Yii::t('report','Start Date'),'width'=>25,'align'=>'C'),//开始日期
                    'end_date'=>array('label'=>Yii::t('report','End Date'),'width'=>25,'align'=>'C'),//结束日期
                    'lcd'=>array('label'=>Yii::t('report','update date'),'width'=>25,'align'=>'C'),//修改日期
                    'lud'=>array('label'=>Yii::t('report','audit date'),'width'=>25,'align'=>'C'),//审批日期
                ),
            ),
        );
        return $sheetList;
    }

    protected function fields() {
        return array(
            'employee_code'=>array('label'=>Yii::t('contract','Employee Code'),'width'=>12,'align'=>'L'),
            'employee_name'=>array('label'=>Yii::t('contract','Employee Name'),'width'=>12,'align'=>'L'),
            'city_name'=>array('label'=>Yii::t('contract','City'),'width'=>12,'align'=>'C'),
            'user_card'=>array('label'=>Yii::t('contract','ID Card'),'width'=>25,'align'=>'R'),
            'company_name'=>array('label'=>Yii::t('contract','Affiliated company'),'width'=>20,'align'=>'L'),
            'department_name'=>array('label'=>Yii::t('contract','Department'),'width'=>15,'align'=>'L'),
            'position_name'=>array('label'=>Yii::t('contract','Position'),'width'=>15,'align'=>'L'),
            'entry_time'=>array('label'=>Yii::t('contract','Entry Time'),'width'=>20,'align'=>'L'),
        );
    }

    public function genReport() {
        $this->retrieveData();
        $this->title = $this->getReportName();
        $this->subtitle = Yii::t('report','Year').':'.$this->criteria['START_DT'].' ~ '.$this->criteria['END_DT'];
        if (isset($this->criteria['CITY'])&&!empty($this->criteria['CITY'])) {
            $this->subtitle.= empty($this->subtitle)?"":" ；";
            $this->subtitle.= Yii::t('report','City').': ';
            $this->subtitle.= General::getCityNameForList($this->criteria['CITY']);
        }
        return $this->exportExcel();
    }

    protected function exportExcel() {
        $this->excel = new ExcelToolEx();
        $this->excel->start();

        $this->rpt_fields = $this->fields();

        $this->excel->newFile();
        $this->excel->setReportDefaultFormat();
        $sheetLists = $this->getSheetExpr();
        foreach ($sheetLists as $sheetId=>$sheetList){
            $this->current_row =0;
            if($sheetId!==0){
                $this->excel->createSheet($sheetId);
                $this->excel->setActiveSheet($sheetId);
            }
            $this->excel->getActiveSheet()->setTitle($sheetList["header_title"]);
            $this->title = $sheetList["header_title"];
            $this->rpt_fields = $sheetList["line_def"];
            $this->data = $sheetList["data"];
            $this->printHeader();
            $this->printDetail();
        }
        $outstring = $this->excel->getOutput();

        $this->excel->end();
        return $outstring;
    }

    public function retrieveData() {
        $start_dt = $this->criteria['START_DT'];
        $end_dt = $this->criteria['END_DT'];
        $end_dt = CGeneral::toMyDate($end_dt);
        $city = $this->criteria['CITY'];

        if(!General::isJSON($city)){
            $citylist = strpos($city,"'")!==false?$city:"'{$city}'";
        }else{
            $citylist = json_decode($city,true);
            $citylist = "'".implode("','",$citylist)."'";
        }

        $sql = "select *,CONCAT('old_',id) as tab_id from hr_employee_operate 
                where city in($citylist) and lcd BETWEEN '{$start_dt}' and '{$end_dt}'
				order by employee_id asc,lcd asc
			";
        $rows = Yii::app()->db->createCommand($sql)->queryAll();
        if (count($rows) > 0) {
            foreach ($rows as $row) {
                $row["start_time"] = CGeneral::toDate($row["start_time"]);
                $row["end_time"] = $row["fix_time"]=="fixation"?CGeneral::toDate($row["end_time"]):"";
                $nextRow = $this->getNextRow($row);
                if(!empty($nextRow)){
                    $this->addDataForContract($row,$nextRow);
                    $this->addDataForDept($row,$nextRow);
                }
            }
        }
        return true;
    }

    protected function getNextRow($row){
        $sql = "select *,CONCAT('old_',id) as tab_id from hr_employee_operate 
                where employee_id='{$row['employee_id']}' and lcd>='{$row['lcd']}' and id!='{$row['id']}'
				order by lcd asc
			";
        $row = Yii::app()->db->createCommand($sql)->queryRow();
        if($row){
            $row["start_time"] = CGeneral::toDate($row["start_time"]);
            $row["end_time"] = $row["fix_time"]=="fixation"?CGeneral::toDate($row["end_time"]):"";
            $row["now_bool"]=false;
            return $row;
        }else{
            $sql = "select *,id as employee_id,CONCAT('now_',id) as tab_id from hr_employee where id='{$row['employee_id']}'";
            $row = Yii::app()->db->createCommand($sql)->queryRow();
            if($row){
                $row["start_time"] = CGeneral::toDate($row["start_time"]);
                $row["end_time"] = $row["fix_time"]=="fixation"?CGeneral::toDate($row["end_time"]):"";
                $row["now_bool"]=true;
                return $row;
            }else{
                return array();
            }
        }
    }

    public function getReportName() {
        //$city_name = isset($this->criteria) ? ' - '.General::getCityName($this->criteria['CITY']) : '';
        return (isset($this->criteria) ? Yii::t('report',$this->criteria['RPT_NAME']) : Yii::t('report','Nil'));
    }
}
?>