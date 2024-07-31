<?php

class CurlBsNotesList extends CListPageModel
{
    public $info_type;

    public function rules()
    {
        return array(
            array('info_type,attr, pageNum, noOfItem, totalRow, searchField, searchValue, orderField, orderType, filter, dateRangeValue','safe',),
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
			'cmd_content'=>Yii::t('curl','cmd content'),
			'message'=>Yii::t('curl','message'),
			'lcu'=>Yii::t('curl','lcu'),
			'lcd'=>Yii::t('curl','lcd'),
			'lud'=>Yii::t('curl','lud'),
		);
	}

	public function retrieveDataByPage($pageNum=1)
	{
		$suffix = Yii::app()->params['envSuffix'];
		$sql1 = "select id,source_time,status_type,info_type,message,lcu,luu,lcd ,lud 
				from hr{$suffix}.hr_bs_api_curl 
				where 1=1 
			";
		$sql2 = "select count(id)
				from  hr{$suffix}.hr_bs_api_curl  
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
				case 'id':
					$clause .= General::getSqlConditionClause('id',$svalue);
					break;
				case 'status_type':
					$clause .= General::getSqlConditionClause('status_type',$svalue);
					break;
				case 'info_type':
					$clause .= General::getSqlConditionClause('info_type',$svalue);
					break;
				case 'data_content':
					$clause .= General::getSqlConditionClause('data_content',$svalue);
					break;
				case 'out_content':
					$clause .= General::getSqlConditionClause('out_content',$svalue);
					break;
				case 'cmd_content':
					$clause .= General::getSqlConditionClause('cmd_content',$svalue);
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
						'info_type'=>self::getInfoTypeList($record['info_type'],true),
						'status_type'=>$record['status_type'],
						'data_content'=>"",
						'out_content'=>"",
						'cmd_content'=>"",
						//'data_content'=>urldecode($record['data_content']),
						//'out_content'=>urldecode($record['out_content']),
						'message'=>$record['message'],
						'lcu'=>$record['lcu'],
						'lcd'=>$record['lcd'],
						'lud'=>$record['lud'],
					);
			}
		}
		$session = Yii::app()->session;
		$session['curlBsNotes_c01'] = $this->getCriteria();
		return true;
	}

	//翻译curl的类型
	public static function getInfoTypeList($key="",$bool=false){
        $list = array(
            "BsStaff"=>"北森同步",
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
        $uid = Yii::app()->user->id;
        $suffix = Yii::app()->params['envSuffix'];
        $row = Yii::app()->db->createCommand()->select("*")->from("hr{$suffix}.hr_bs_api_curl")
            ->where("id=:id and status_type!='P'", array(':id'=>$index))->queryRow();
        if($row){
            Yii::app()->db->createCommand()->update("hr{$suffix}.hr_bs_api_curl",array(
                "status_type"=>"P",
                "cmd_content"=>null,
                "message"=>null,
                "lcu"=>$uid,
            ),"id={$index}");
            return true;
        }else{
            return false;
        }
    }

	public function getCurlTextForID($id,$type=0){
        $type = "".$type;
	    $list = array(
	        0=>"data_content",//请求内容
	        1=>"out_content",//响应的内容
	        2=>"cmd_content",//执行结果
        );
	    $selectStr = key_exists($type,$list)?$list[$type]:$list[0];
        $suffix = Yii::app()->params['envSuffix'];
        $row = Yii::app()->db->createCommand()->select($selectStr)->from("hr{$suffix}.hr_bs_api_curl")
            ->where("id=:id", array(':id'=>$id))->queryRow();
        if($row){
            return $row[$selectStr];
        }else{
            return "";
        }
    }

    private function sendCurl($url,$data){
        $data = json_encode($data);
        $url = Yii::app()->params['curlLink'].$url;
        $svrkey = Yii::app()->params['SvrKey'];
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type:application/json',
            'Content-Length:'.strlen($data),
            'Authorization: SvrKey '.$svrkey,
        ));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $out = curl_exec($ch);
        if ($out===false) {
            echo 'Error: '.curl_error($ch);
        } else {
            var_dump($out);
        }
        curl_close($ch);
    }

    public function getBsData($startDate,$endDate,$bool=true){
        $selectData=array(
            "empStatus"=>null,//人员状态：默认null，示例：[1,2,3,7]（待入职、试用、正式、返聘）。
            "employType"=>array(0,1,2),//雇佣类型：默认查询内部员工。示例：[0,2]，表示内部员工、实习生。
            "serviceType"=>array(0,1),//任职类型：默认查询主职。示例：[0]，表示主职。
            "withDisabled"=>true,//是否包含离职的记录
            "isGetOfferRecord"=>true,//是否获取任职记录对应的offer的记录
            "startTime"=>$startDate,//时间范围开始时间，格式：2021-01-01T00:00:00
            "stopTime"=>$endDate,//时间范围结束时间，格式：2021-01-01T00:00:00
            "timeWindowQueryType"=>"1",//时间窗查询类型，1修改时间、2业务修改时间
            "scrollId"=>null,//本批次的ScrollId，第一次查询为空
            "enableTranslate"=>true,//是否开启动态翻译，默认否
            "capacity"=>20,//每批次查询个数，默认100个
        );
        $url = "/TenantBaseExternal/api/v5/Employee/GetByTimeWindow";
        $list = $this->foreachScrollData($selectData,$url);
        if($bool){
            if($list["code"]==200){
                if(!empty($list["forNum"])||(key_exists("dataJson",$list)&&!empty($list["dataJson"]["data"]))){
                    Dialog::message(Yii::t('dialog','Information'), "已获取北森变更员工，等待执行");
                }else{
                    Dialog::message(Yii::t('dialog','Information'), "没有北森变更员工，无需执行");
                }
            }else{
                Dialog::message(Yii::t('dialog','Validation Message'), $list["message"]);
            }
        }
        return $list;
    }

    private function foreachScrollData($selectData,$url,$num=0){
        $curlModel =new CurlForStaff();
        $list = $curlModel->getHistoryData($selectData,$url);
        $curlModel->saveTableForBsArr($list);
        if(key_exists("dataJson",$list)){
            if(!empty($list["dataJson"]["data"])){
                $num++;
                $selectData["scrollId"] = $list["dataJson"]["scrollId"];
                return $this->foreachScrollData($selectData,$url,$num);
            }
        }
        $list["forNum"]=$num;
        return $list;
    }
}
