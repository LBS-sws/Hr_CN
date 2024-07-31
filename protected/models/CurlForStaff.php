<?php
//员工的 curl
class CurlForStaff{
    protected $info_type="staff";
    protected $saveArr=array();

    /*
     * https://open.italent.cn/?_qrt=html&quark_s=9f6b3ec955dd7a35c0f676ef9a20358d50d25cfb5e2efba079210f3d1288b8fc#/open-document?menu=document-center&id=d2405033-0bb3-4574-ad82-b959799e8882
     *  查询北森员工历史审核数据(说明文档)
     */
    public function getHistoryData($data,$url) {
        $root = Yii::app()->params['BSCurlRootURL'];
        $endUrl = $root.$url;
        $rtn = array('message'=>'', 'code'=>400,'outData'=>'');//成功时code=200；
        $tokenModel = new BSToken();
        $tokenList = $tokenModel->getToken();
        $sendDate = date_format(date_create(),"Y/m/d H:i:s");
        if($tokenList["status"]===true){
            $data_string = json_encode($data);

            $ch = curl_init($endUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type:application/json',
                'Content-Length:'.strlen($data_string),
                'Authorization:Bearer '.$tokenList["token"],
            ));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            $out = curl_exec($ch);
            if ($out===false) {
                $rtn['message'] = curl_error($ch);
                $rtn['outData'] = $rtn['message'];
            } else {
                $rtn['outData'] = $out;
                $json = json_decode($out, true);
                if(is_array($json)&&key_exists("code",$json)&&$json["code"]==200){
                    $rtn['code'] = 200;
                    $rtn['dataJson'] = $json;
                }else{
                    $rtn['message'] = isset($json["message"])?$json["message"]:"";
                }
            }
        }else{
            $rtn['outData'] = $tokenList["message"];
            $rtn["message"] = "token获取失败:".$tokenList["message"];//token获取失败
        }

        $this->saveArr = array(
            "source_time"=>key_exists("startTime",$data)?$data["startTime"]:null,
            "status_type"=>$rtn['code']==200?"C":"E",
            "info_type"=>$this->info_type,
            "data_content"=>json_encode($data),
            "out_content"=>$rtn['outData'],
            "message"=>$rtn['message'],
            "lcu"=>Yii::app()->getComponent('user')===null?"bsAdmin":Yii::app()->user->id,
            "lcd"=>$sendDate,
        );

        /*
        echo "请求地址:";
        echo "<br/>";
        var_dump($endUrl);
        echo "<br/>";
        echo "请求内容:";
        echo "<br/>";
        var_dump($data);
        echo "<br/>";
        echo "<br/>";
        echo "响应内容:";
        echo "<br/>";
        var_dump($out);
        die();
        */
        return $rtn;
    }

    public function saveTableForBsArr($list){
        if(!empty($this->saveArr)){
            if(!empty($list)&&key_exists("dataJson",$list)&&!empty($list["dataJson"]["data"])){
                $suffix = Yii::app()->params['envSuffix'];
                $this->saveArr["status_type"]="P";
                $this->saveArr["info_type"]="BsStaff";
                Yii::app()->db->createCommand()->insert("hr{$suffix}.hr_bs_api_curl",$this->saveArr);
            }
        }
    }

    public function saveTableForArr(){
        if(!empty($this->saveArr)){
            $suffix = Yii::app()->params['envSuffix'];
            Yii::app()->db->createCommand()->insert("hr{$suffix}.hr_bs_api_curl",$this->saveArr);
        }
    }
}
