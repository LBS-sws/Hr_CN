<?php

class SupportApplyList extends CListPageModel
{
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
            'support_code'=>Yii::t('contract','support code'),
            'apply_city'=>Yii::t('contract','City'),
            'apply_date'=>Yii::t('contract','Start Time'),
            'apply_end_date'=>Yii::t('contract','End Time'),
            'employee_id'=>Yii::t('contract','support employee'),
            'review_sum'=>Yii::t('contract','review sum'),
            'status_type'=>Yii::t('contract','Status'),
            'service_type'=>Yii::t('contract','service type'),
            'apply_type'=>Yii::t('queue','Type'),
            'privilege'=>Yii::t('contract','privilege'),
            'privilege_user'=>Yii::t('contract','privilege user'),
            'dept_name'=>Yii::t('contract','Position'),
		);
	}

	public function retrieveDataByPage($pageNum=1)
	{
        $suffix = Yii::app()->params['envSuffix'];
        $city = Yii::app()->user->city;
        $city_allow = Yii::app()->user->city_allow();
		$sql1 = "select a.*,b.name,e.name as dept_name from hr_apply_support a 
                LEFT JOIN hr_employee b ON a.employee_id = b.id
                LEFT JOIN hr_dept e ON b.position = e.id
                where a.apply_city='$city' 
			";
		$sql2 = "select count(*) from hr_apply_support a 
                LEFT JOIN hr_employee b ON a.employee_id = b.id
                LEFT JOIN hr_dept e ON b.position = e.id
                where a.apply_city='$city' 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'support_code':
					$clause .= General::getSqlConditionClause('a.support_code',$svalue);
					break;
				case 'employee_id':
					$clause .= General::getSqlConditionClause('b.name',$svalue);
					break;
				case 'dept_name':
					$clause .= General::getSqlConditionClause('e.name',$svalue);
					break;
			}
		}
		
		$order = "";
		if (!empty($this->orderField)) {
			$order .= " order by ".$this->orderField." ";
			if ($this->orderType=='D') $order .= "desc ";
		}else{
            $order = " order by id desc";
        }

		$sql = $sql2.$clause;
		$this->totalRow = Yii::app()->db->createCommand($sql)->queryScalar();
		
		$sql = $sql1.$clause.$order;
		$sql = $this->sqlWithPageCriteria($sql, $this->pageNum);
		$records = Yii::app()->db->createCommand($sql)->queryAll();

		$this->attr = array();
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
                $arr = $this->getStatus($record);
				$this->attr[] = array(
					'id'=>$record['id'],
					'support_code'=>$record['support_code'],
					'apply_city'=>$record['apply_city'],
					'apply_date'=>$record['apply_date'],
					'apply_end_date'=>$record['apply_end_date'],
					'name'=>$record['name'],
					'dept_name'=>$record['dept_name'],
					'review_sum'=>$record['review_sum'],
					'service_type'=>$this->getServiceList($record['service_type'],true),
                    'apply_type'=>$this->getApplyTypeList($record['apply_type'],true),
                    'privilege'=>$this->getPrivilegeList($record['privilege'],true),
					'status'=>$arr['status'],
					'style'=>$arr['style'],
				);
			}
		}
		$session = Yii::app()->session;
		$session['supportApply_01'] = $this->getCriteria();
		return true;
	}

	public function getStatusList(){
	    return array(
	        1=>array("status"=>Yii::t("contract","Draft"),"style"=>""),//草稿
	        2=>array("status"=>Yii::t("contract","pending approval"),"style"=>"text-primary"),//待審核
	        3=>array("status"=>Yii::t("contract","Have to see"),"style"=>"text-primary"),//已查看
	        4=>array("status"=>Yii::t("contract","Wait in line"),"style"=>"text-primary"),//排隊等候
	        5=>array("status"=>Yii::t("contract","To score"),"style"=>"text-warning"),//待評分
	        6=>array("status"=>Yii::t("contract","finish score"),"style"=>"text-success"),//已評分
	        7=>array("status"=>Yii::t("contract","finish support"),"style"=>"text-success"),//已完成
	        8=>array("status"=>Yii::t("contract","reject early"),"style"=>"text-danger"),//拒绝提前結束
	        9=>array("status"=>Yii::t("contract","apply early end"),"style"=>"text-primary"),//申請提前結束
	        10=>array("status"=>Yii::t("contract","renewal"),"style"=>"text-primary"),//续期
            11=>array("status"=>Yii::t("contract","reject renewal"),"style"=>"text-danger"),//拒绝续期
            12=>array("status"=>Yii::t("contract","finish and custom"),"style"=>"text-danger"),//已完成,自定义
            13=>array("status"=>Yii::t("contract","send back"),"style"=>"text-danger"),//退回
            14=>array("status"=>Yii::t("contract","To score"),"style"=>"text-warning"),//待評分,草稿
        );
    }

    public function getStatus($arr){
	    $list = $this->getStatusList();
	    if(key_exists($arr["status_type"],$list)){
            return $list[$arr["status_type"]];
        }else{
            return array(
                "status"=>Yii::t("contract","not sent"),
                "style"=>"text-danger"
            );//未發送
        }
    }

    public function getServiceList($id='',$bool=false){
        $arr = array(
            1=>Yii::t("contract","service support"),//服务支援
            2=>Yii::t("contract","service guide"),//技術支援
        );
        if($bool){
            if(key_exists($id,$arr)){
                return $arr[$id];
            }else{
                return $id;
            }
        }
        return $arr;
    }

    public function getApplyTypeList($id='',$bool=false){
        $arr = array(
            1=>Yii::t("contract","support apply"),//申请支援
            2=>Yii::t("contract","stationary point"),//駐點
        );
        if($bool){
            if(key_exists($id,$arr)){
                return $arr[$id];
            }else{
                return $id;
            }
        }
        return $arr;
    }

    public function getPrivilegeList($id='',$bool=false){
        $arr = array(
            0=>Yii::t("contract","not use"),//不使用
            1=>Yii::t("contract","Personnel replacement"),//人員置換
            2=>Yii::t("contract","priority"),//優先權
        );
        if($bool){
            if(key_exists($id,$arr)){
                return $arr[$id];
            }else{
                return $id;
            }
        }
        return $arr;
    }
}
