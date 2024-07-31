<?php

class BossApplyList extends CListPageModel
{
    public $id =1;
    public $employee_id;//員工id
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
    public $no_of_attm = array(
        'bosskpi'=>0
    );
    public $docType = 'BOSSKPI';
    public $docMasterId = array(
        'bosskpi'=>0
    );
    public $files;
    public $removeFileId = array(
        'bosskpi'=>0
    );
	public function attributeLabels()
	{
		return array(
            'name'=>Yii::t('contract','Employee Name'),
            'code'=>Yii::t('contract','Employee Code'),
            'audit_year'=>Yii::t('contract','audit year'),
            'results_a'=>Yii::t('contract','Results (A)'),
            'results_b'=>Yii::t('contract','Results (B)'),
            'results_c'=>Yii::t('contract','Results (C)'),
            'results_sum'=>Yii::t('contract','Sum Results'),
            'status_type'=>Yii::t('contract','Status'),
		);
	}
    public function rules(){
        return array(
            array('attr, pageNum, noOfItem, totalRow, searchField, searchValue, orderField, orderType, filter','safe',),
            array('files, removeFileId, docMasterId, no_of_attm','safe'),
        );
    }

    //驗證賬號是否綁定員工
    public function validateEmployee(){
        $uid = Yii::app()->user->id;
        $rows = Yii::app()->db->createCommand()->select("employee_id,employee_name")->from("hr_binding")
            ->where('user_id=:user_id',
                array(':user_id'=>$uid))->queryRow();
        if ($rows){
            $this->employee_id = $rows["employee_id"];
            return true;
        }
        return false;
    }

	public function retrieveDataByPage($pageNum=1)
	{
        $city = Yii::app()->user->city();
        $suffix = Yii::app()->params['envSuffix'];
        $city_allow = Yii::app()->user->city_allow();
		$sql1 = "select b.name,b.code,a.* from hr_boss_audit a 
                LEFT JOIN hr_employee b ON a.employee_id = b.id 
                where a.employee_id = '$this->employee_id' and a.city='$city' 
			";
		$sql2 = "select count(a.id)
				from hr_boss_audit a 
                LEFT JOIN hr_employee b ON a.employee_id = b.id 
                where a.employee_id = '$this->employee_id' and a.city='$city' 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'audit_year':
					$clause .= General::getSqlConditionClause('a.audit_year',$svalue);
					break;
				case 'code':
					$clause .= General::getSqlConditionClause('b.code',$svalue);
					break;
				case 'name':
					$clause .= General::getSqlConditionClause('b.name',$svalue);
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

		$this->attr = array();
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
                $ratio_a = $record['ratio_a']*0.01;
                $ratio_b = $record['ratio_b']*0.01;
                $bossRewardType = BossApplyForm::getBossRewardType($record['city']);
                $arrList = $this->statusToColor($record);
                $record["results_a"]=empty($record['results_a'])?0:floatval($record['results_a'])*$ratio_a;
                if($bossRewardType == 1){
                    $record['results_c'] = "-";
                    $record["results_b"]=empty($record['results_b'])?0:floatval($record['results_b'])*$ratio_b;
                    $record['results_sum'] = $record["results_a"]+$record["results_b"];
                }else{
                    $record['results_c'] = $record['results_c']."%";
                    $record["results_b"]=empty($record['results_b'])?0:floatval($record['results_b'])*$ratio_b;
                    $record['results_sum'] = $record["results_a"]+$record["results_b"]+$record['results_c'];
                }
                $record['results_sum'] = sprintf("%.2f",$record['results_sum']);
				$this->attr[] = array(
					'id'=>$record['id'],
					'code'=>$record['code'],
					'name'=>$record['name'],
                    'results_a'=>$record['results_a'],
                    'results_b'=>$record['results_b'],
					'results_c'=>$record['results_c'],
					'results_sum'=>$record['results_sum'],
					'audit_year'=>$record['audit_year'],
					'status_type'=>$arrList['status'],
					'style'=>$arrList['style'],
				);
			}
		}
        $this->no_of_attm['bosskpi'] = Yii::app()->db->createCommand("select docman$suffix.countdoc('BOSSKPI',$this->id)")->queryScalar();
		$session = Yii::app()->session;
		$session['bossApply_01'] = $this->getCriteria();
		return true;
	}
    //根據狀態獲取顏色
    public function statusToColor($row){
        switch ($row["status_type"]){
            case 0:
                return array(
                    "status"=>Yii::t("contract","Draft"),
                    "style"=>""
                );
            case 1:
                if($row['boss_type']==1){
                    $status =Yii::t("contract","Pending review by the director");
                }elseif($row['boss_type']==3){
                    $status =Yii::t("contract","Pending review by the joe");
                }else{
                    $status =Yii::t("contract","Pending review by the Deputy Director");
                }
                return array(
                    "status"=>$status,//已發送，等待審核
                    "style"=>" text-primary"
                );
            case 2:
                return array(
                    "status"=>Yii::t("contract","Finish"),//已完成
                    "style"=>" text-success"
                );
            case 3:
                return array(
                    "status"=>Yii::t("contract","Rejected"),//拒絕
                    "style"=>" text-danger"
                );
            case 4:
                return array(
                    "status"=>Yii::t("contract","Reviewed and to be completed"),//已审核，待完成
                    "style"=>" text-warning"
                );
            case 5:
                if($row['boss_type']==1){
                    $status =Yii::t("contract","Pending confirmation by the director");
                }elseif($row['boss_type']==3){
                    $status =Yii::t("contract","Pending confirmation by the joe");
                }else{
                    $status =Yii::t("contract","Pending confirmation by the Deputy Director");
                }
                return array(
                    "status"=>$status,//等待二次審核
                    "style"=>" text-primary"
                );
        }
        return array(
            "status"=>$row["status_type"],
            "style"=>""
        );
    }

}
