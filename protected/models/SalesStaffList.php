<?php


class SalesStaffList extends CListPageModel
{
    public $id =0;
    public $index =0;
    public $employee_id =0;
    public $time_off =0;
    public $start_time;
    public $end_time;

    protected $group_list=array();
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */

    public function rules()
    {
        return array(
            array('attr,start_time,end_time, employee_id, index, pageNum, noOfItem, totalRow, searchField, searchValue, orderField, orderType, filter','safe',),
            array('id','validateDel','on'=>array("del","edit")),
            array('start_time','validateTime','on'=>array("add","edit")),
            array('index','validateIndex','on'=>array("add")),
            array('employee_id','validateStaff','on'=>array("add")),
        );
    }

    public function validateTime($attribute, $params){
        $this->time_off = 0;
        if(!empty($this->start_time)){
            $this->start_time.=strpos($this->start_time,'/')!==false?"/05":"-05";
            $this->time_off = 1;
        }
        if(!empty($this->end_time)){
            $this->end_time.=strpos($this->end_time,'/')!==false?"/05":"-05";
            if($this->time_off == 1){
                if(date("Y-m",strtotime($this->start_time))>date("Y-m",strtotime($this->end_time))){
                    $message = "开始时间不能大于结束时间";
                    $this->addError($attribute,$message);
                }
            }
            $this->time_off = 1;
        }
    }

    public function validateDel($attribute, $params){
        $bool = Yii::app()->db->createCommand()->select("group_id")->from("hr_sales_staff")
            ->where('id=:id',array(':id'=>$this->id))->queryRow();
        if(!$bool){
            $message = "記錄不存在，請刷新重試";
            $this->addError($attribute,$message);
        }else{
            $this->index = $bool["group_id"];
        }
    }

    public function validateIndex($attribute, $params){
        $bool = Yii::app()->db->createCommand()->select("*")->from("hr_sales_group")
            ->where('id=:id',array(':id'=>$this->index))->queryRow();
        if(!$bool){
            $message = "分组不存在，请刷新重试";
            $this->addError($attribute,$message);
        }
    }

    public function validateStaff($attribute, $params){
        $city = Yii::app()->user->city();
        $bool = Yii::app()->db->createCommand()->select("a.id")->from("hr_sales_staff a")
            ->leftJoin("hr_sales_group b","a.group_id=b.id")
            ->where("a.employee_id=:id and b.city='$city'",array(":id"=>$this->employee_id))->queryRow();
        if($bool){
            $message = "该员工已分组，请刷新重试";
            $this->addError($attribute,$message);
        }else{
            $row = Yii::app()->db->createCommand()->select("id")->from("hr_employee")
                ->where("id=:id and city='$city'",array(":id"=>$this->employee_id))->queryRow();
            if(!$row){
                $message = "員工不存在，請於管理員聯繫";
                $this->addError($attribute,$message);
            }
        }
    }

    public function retrieveForm($index){
        $row = Yii::app()->db->createCommand()->select("*")->from("hr_sales_staff")
            ->where("id=:id",array(":id"=>$index))->queryRow();
        if($row){
            $this->id = $row["id"];
            $this->index = $row["group_id"];
            $this->employee_id = $row["employee_id"];
            $this->time_off = $row["time_off"];
            $this->start_time = empty($row['start_time'])?"":date("Y-m",strtotime($row['start_time']));
            $this->end_time = empty($row['end_time'])?"":date("Y-m",strtotime($row['end_time']));
        }
    }

	public function attributeLabels()
	{
		return array(
            'id'=>Yii::t('contract','ID'),
            'code'=>Yii::t('contract','Employee Code'),
            'name'=>Yii::t('contract','Employee Name'),
            'employee_id'=>Yii::t('contract','Employee Name'),
            'department_name'=>Yii::t('contract','Department'),
            'position_name'=>Yii::t('contract','Position'),
            'start_time'=>Yii::t('contract','Start Time'),
            'end_time'=>Yii::t('contract','End Time'),
		);
	}

	public function retrieveDataByPage($index,$pageNum=1)
	{
        $index = is_numeric($index)?$index:0;
        $this->index = $index;
	    $this->group_list = SalesGroupForm::getGroupListToId($index);

        $suffix = Yii::app()->params['envSuffix'];
        $city = Yii::app()->user->city();
        $city_allow = Yii::app()->user->city_allow();
		$sql1 = "select a.id,a.start_time,a.end_time,b.code,b.name,c.name as position_name,d.name as department_name from hr_sales_staff a
                LEFT JOIN hr_employee b ON a.employee_id=b.id 
                LEFT JOIN hr_dept c ON b.position=c.id 
                LEFT JOIN hr_dept d ON b.department=d.id 
                LEFT JOIN hr_sales_group f ON a.group_id=f.id 
                where a.group_id = '$index' AND f.city = '$city' 
			";
		$sql2 = "select count(a.id) from hr_sales_staff a
                LEFT JOIN hr_employee b ON a.employee_id=b.id 
                LEFT JOIN hr_dept c ON b.position=c.id 
                LEFT JOIN hr_dept d ON b.department=d.id 
                LEFT JOIN hr_sales_group f ON a.group_id=f.id 
                where a.group_id = '$index' AND f.city = '$city' 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'code':
					$clause .= General::getSqlConditionClause('b.code',$svalue);
					break;
				case 'name':
					$clause .= General::getSqlConditionClause('b.name',$svalue);
					break;
				case 'department_name':
					$clause .= General::getSqlConditionClause('d.name',$svalue);
					break;
				case 'position_name':
					$clause .= General::getSqlConditionClause('c.name',$svalue);
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
					'code'=>$record['code'],
					'name'=>$record['name'],
					'start_time'=>empty($record['start_time'])?Yii::t("contract","unlimited"):date("Y-m",strtotime($record['start_time'])),
					'end_time'=>empty($record['end_time'])?Yii::t("contract","unlimited"):date("Y-m",strtotime($record['end_time'])),
					'department_name'=>$record['department_name'],
					'position_name'=>$record['position_name']
				);
			}
		}
		$session = Yii::app()->session;
		$session['salesStaff_01'] = $this->getCriteria();
		return true;
	}

	public function getGroupListStr($str){
	    if(key_exists($str,$this->group_list)){
	        return $this->group_list[$str];
        }else{
	        return $str;
        }
    }

    public function getSalesList(){
	    $arr = array("list"=>array(),"option"=>array());
        $city = Yii::app()->user->city();
        $rows = Yii::app()->db->createCommand()->select("a.id,a.code,a.name,a.entry_time,a.leave_time,a.name,a.staff_status")->from("hr_employee a")
            ->leftJoin("hr_dept d","a.position = d.id")
            ->where("(a.city='$city' AND a.table_type=1 AND a.staff_status = 0 AND d.review_type = 3) or a.id='".$this->employee_id."'")->order("a.id desc")->queryAll();
        if($rows){
            foreach ($rows as $row){
                $bool = Yii::app()->db->createCommand()->select("a.id")->from("hr_sales_staff a")
                    ->leftJoin("hr_sales_group b","a.group_id = b.id")
                    ->where("a.employee_id=:id and b.city=:city",
                        array(":id"=>$row["id"],":city"=>$city)
                    )->queryRow();
                if(!$bool||$row["id"] == $this->employee_id){
                    $leave_time = $row["staff_status"]!=-1?"":$row["leave_time"];
                    $arr["list"][$row["id"]] = $row["code"]." -- ".$row["name"];
                    $arr["option"][$row["id"]] = array("data-entry"=>$row["entry_time"],"data-staff"=>$row["staff_status"],"data-leave"=>$leave_time);
                }
            }
        }

        return $arr;
    }

    public function saveData(){
        switch ($this->getScenario()){
            case "add":
                $addArr = array(
                    'group_id'=>$this->index,
                    'employee_id'=>$this->employee_id,
                    'time_off'=>$this->time_off,
                    'start_time'=>empty($this->start_time)?null:$this->start_time,
                    'end_time'=>empty($this->end_time)?null:$this->end_time,
                    'lcu'=>Yii::app()->user->id,
                );
                Yii::app()->db->createCommand()->insert("hr_sales_staff",$addArr);
                $this->id = Yii::app()->db->getLastInsertID();
                break;
            case "del":
                $rows = Yii::app()->db->createCommand()->delete('hr_sales_staff', 'id=:id', array(':id'=>$this->id));
                break;
            case "edit":
                Yii::app()->db->createCommand()->update('hr_sales_staff', array(
                    'time_off'=>$this->time_off,
                    'start_time'=>empty($this->start_time)?null:$this->start_time,
                    'end_time'=>empty($this->end_time)?null:$this->end_time,
                ), 'id=:id', array(':id'=>$this->id));
                break;
        }
    }
}
