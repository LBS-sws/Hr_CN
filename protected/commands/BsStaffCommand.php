<?php
class BsStaffCommand extends CConsoleCommand {
	
	public function run($args) {
        $suffix = Yii::app()->params['envSuffix'];
		$row = Yii::app()->db->createCommand()->select()->from("hr{$suffix}.hr_bs_api_curl")
            ->where("status_type='P'")->order("id asc")->queryRow();
		if($row){
            Yii::app()->db->createCommand()->update("hr{$suffix}.hr_bs_api_curl", array(
                'status_type'=>"I",
            ), 'id=:id', array(':id'=>$row["id"]));

            $this->computeRow($row);//执行获取的数据
        }else{
		    $this->getBsData();//获取北森数据
        }
	}

    protected function computeRow($row){
        $suffix = Yii::app()->params['envSuffix'];
        $infoType = $row["info_type"];
        $result = array('code'=>400,'msg'=>"类型异常，info_type：{$infoType}");//200:成功
        switch ($infoType){
            case "BsStaff"://北森同步
                $data = json_decode($row["out_content"],true);
                $data = $data["data"];
                $bsStaffModel = new BsStaffModel();
                $result = $bsStaffModel->syncChangeFull($data);
                break;
        }

        $status_type = $result["code"]==400?"E":"C";
        Yii::app()->db->createCommand()->update("hr{$suffix}.hr_bs_api_curl",array(
            "status_type"=>$status_type,
            "cmd_content"=>isset($result["html"])?$result["html"]:"",
            "message"=>isset($result["msg"])?$result["msg"]:"",
        ),"id=".$row["id"]);
    }

    protected function getBsData(){
        $model = new CurlBsNotesList();
        $interval = 60*60*1; // 1个小时
        $endTime = floor(time() / $interval) * $interval; //结束时间戳
        $startTime = $endTime-$interval; //起始时间戳
        $datetime = new DateTime();
        $startDate = $datetime->setTimestamp($startTime)->format('Y-m-d\TH:i:s');
        $endDate = $datetime->setTimestamp($endTime)->format('Y-m-d\TH:i:s');
        $suffix = Yii::app()->params['envSuffix'];
        $row = Yii::app()->db->createCommand()->select()->from("hr{$suffix}.hr_bs_api_curl")
            ->where("source_time='{$startDate}' and lcu='admin'")->queryRow();
        if($row){
            //已执行，等待下一个1小时
        }else{
            $model->getBsData($startDate,$endDate,false);
        }
    }
}
?>