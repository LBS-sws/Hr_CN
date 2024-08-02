<?php

class CurlNotesList extends CListPageModel
{
    public $info_type;

    public function rules()
    {
        return array(
            array('info_type,attr, pageNum, noOfItem, totalRow,city, searchField, searchValue, orderField, orderType, filter, dateRangeValue','safe',),
        );
    }

    public function getCriteria() {
        return array(
            'info_type'=>$this->info_type,
            'searchField'=>$this->searchField,
            'searchValue'=>$this->searchValue,
            'orderField'=>$this->orderField,
            'orderType'=>$this->orderType,
            'noOfItem'=>$this->noOfItem,
            'pageNum'=>$this->pageNum,
            'filter'=>$this->filter,
            'dateRangeValue'=>$this->dateRangeValue,
        );
    }
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
			'status_type'=>Yii::t('curl','status type'),
			'info_type'=>Yii::t('curl','info type'),
			'info_url'=>Yii::t('curl','info url'),
			'data_content'=>Yii::t('curl','data content'),
			'out_content'=>Yii::t('curl','out content'),
			'message'=>Yii::t('curl','message'),
			'lcu'=>Yii::t('curl','lcu'),
			'lcd'=>Yii::t('curl','lcd'),
			'lud'=>Yii::t('curl','lud'),
		);
	}
	
	public function retrieveDataByPage($pageNum=1)
	{
		$suffix = Yii::app()->params['envSuffix'];
		$sql1 = "select * 
				from hr_api_curl 
				where 1=1 
			";
		$sql2 = "select count(id)
				from hr_api_curl 
				where 1=1 
			";
		$clause = "";
        if(!empty($this->info_type)){
            $svalue = str_replace("'","\'",$this->info_type);
            $clause.=" and info_type='$svalue' ";
        }
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'status_type':
					$clause .= General::getSqlConditionClause('status_type',$svalue);
					break;
				case 'info_type':
					$clause .= General::getSqlConditionClause('info_type',$svalue);
					break;
				case 'info_url':
					$clause .= General::getSqlConditionClause('info_url',$svalue);
					break;
				case 'data_content':
					$clause .= General::getSqlConditionClause('data_content',$svalue);
					break;
				case 'out_content':
					$clause .= General::getSqlConditionClause('out_content',$svalue);
					break;
				case 'message':
					$clause .= General::getSqlConditionClause('message',$svalue);
					break;
			}
		}
		
		$order = "";
		if (!empty($this->orderField)) {
            $order .= " order by {$this->orderField} ";
			if ($this->orderType=='D') $order .= "desc ";
		}else{
            $order .= " order by id desc ";
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
						'status_type'=>StaffFun::getCurlStatusNameToID($record['status_type']),
						'info_type'=>self::getInfoTypeList($record['info_type'],true),
						'info_url'=>$record['info_url'],
						'data_content'=>$record['data_content'],
						'out_content'=>$record['out_content'],
						'message'=>$record['message'],
						'lcu'=>$record['lcu'],
						'lcd'=>$record['lcd'],
						'lud'=>$record['lud'],
					);
			}
		}
		$session = Yii::app()->session;
		$session['hr_curlNotes_c01'] = $this->getCriteria();
		return true;
	}

    //翻译curl的类型
    public static function getInfoTypeList($key="",$bool=false){
        $list = array(
            "employeeFull"=>"批量变更员工",
            "employee"=>"员工资料",
            "binding"=>"员工绑定",
            "cross"=>"交叉派单",
        );
        if($bool){
            if(key_exists($key,$list)){
                return $list[$key];
            }else{
                return $key;
            }
        }else{
            return $list;
        }
    }

	public function sendID($index){
        $row = Yii::app()->db->createCommand()->select("*")->from("hr_api_curl")
            ->where("id=:id", array(':id'=>$index))->queryRow();
        if($row){
            $info_type = $row["info_type"];
            $curlData = $row["data_content"];
            $curlData = empty($curlData)?array():json_decode($curlData,true);
            $curlModel = new ApiCurl($info_type,$curlData);
            $curlModel->sendCurlAndUpdate($index);
            return true;
        }else{
            return false;
        }
    }

	public function sendCurlForIDAndType($id,$type,$info_type){
	    switch ($info_type){
            case "employee":
                $bool = StaffForm::sendCurl($id,$type);
                if($bool){
                    echo "employee success ! id:{$id},Scenario:{$type}";
                }else{
                    echo "employee error ! id:{$id},Scenario:{$type}";
                }
                break;
            case "binding":
                $model = new BindingForm($type);
                $model->retrieveData($id);
                if(!empty($model->id)){
                    $model->sendCurl();
                    echo "binding success ! id:{$id},Scenario:{$type}";
                }else{
                    echo "binding error ! id:{$id},Scenario:{$type}";
                }
                break;
        }
    }
}
