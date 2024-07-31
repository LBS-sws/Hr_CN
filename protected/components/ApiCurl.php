<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2023/12/14 0014
 * Time: 11:58
 */
class ApiCurl{
    public $curlData=array();

    public $infoType;//接口
    protected $infoData=array(//所有接口
        "employeeFull"=>"/api/hr.DataSync/employees_batchSync",//员工(批量)
        "employee"=>"/api/hr.DataSync/employee_dataSync",//员工
        "binding"=>"/api/hr.DataSync/hrBinding_dataSync",//绑定员工
        "cross"=>"/index.php/api/lbs.CrossAudit/getAuditInfo",//交叉派单
    );

    public function __construct($infoType,$curlData){
        $this->infoType = $infoType;
        $this->curlData = $curlData;
    }

    protected $errorMessage;

    protected function validateCurl(){
        if(!key_exists($this->infoType,$this->infoData)){
            $this->errorMessage='infoType error:'.$this->infoType;
            return false;
        }
        if(empty($this->curlData)){
            $this->errorMessage='curlData not empty.';
            return false;
        }
        return true;
    }

    public function sendCurl(){
        if($this->validateCurl()){
            $this->curl();
        }else{
            throw new CHttpException(400,$this->errorMessage);
        }
    }

    public function sendCurlAndAdd(){
        if($this->validateCurl()){
            $rtn = $this->curl();
            $rtn = $this->resetSQLData($rtn);
            Yii::app()->db->createCommand()->insert('hr_api_curl',$rtn);
        }else{
            throw new CHttpException(400,$this->errorMessage);
        }
    }

    public function sendCurlAndUpdate($id){
        if($this->validateCurl()){
            $rtn = $this->curl();
            $rtn = $this->resetSQLData($rtn);
            Yii::app()->db->createCommand()->update('hr_api_curl',$rtn, 'id=:id', array(':id'=>$id));
        }else{
            throw new CHttpException(400,$this->errorMessage);
        }
    }

    protected function resetSQLData($data){
        $rtn = $data;
        $rtn["lcu"]=Yii::app()->getComponent('user')===null?"bsAdmin":Yii::app()->user->id;
        $rtn["status_type"]=$rtn["message"]=="Success"?"C":"E";
        //{"code":400,"msg":"phone不能为空","data":[]}
        if($rtn["status_type"]=="C"&&isset($rtn["out_content"]["code"])&&$rtn["out_content"]["code"]!=200){
            $rtn["status_type"]="E";
            $rtn["message"]=isset($rtn["out_content"]["msg"])?$rtn["out_content"]["msg"]:$rtn["message"];
        }
        $rtn["data_content"]=json_encode($rtn["data_content"]);
        $rtn["out_content"]=json_encode($rtn["out_content"]);
        return $rtn;
    }

    protected function curl(){
        $rtn = array(
            'message'=>'',
            'status_type'=>'P',
            'info_type'=>$this->infoType,
            'info_url'=>'',
            'lcd'=>date("Y-m-d H:i:s"),
            'data_content'=>array(),
            'out_content'=>array()
        );

        $key = Yii::app()->params['uCurlKey'];
        $root = Yii::app()->params['uCurlRootURL'];
        $url = $root.$this->infoData[$this->infoType];
        $rtn["data_content"] = $this->curlData;
        $rtn["data_content"]["key"] = self::generate_key();
        $rtn["info_url"] = $url;
        $data_string = json_encode($rtn["data_content"]);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type:application/json',
            //'Content-Type:application/json;charset=UTF-8',
            //'Content-Length:'.strlen($data_string),
            'Content-Length:'.mb_strlen($data_string, 'UTF-8'),
        ));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $out = curl_exec($ch);
        if ($out===false) {
            $rtn['message'] = curl_error($ch);
        } else {
            $rtn['out_content'] = json_decode($out, true);
            $rtn['message'] = self::getJsonError(json_last_error());
        }

        return $rtn;
    }

    public static function getJsonError($error) {
        switch ($error) {
            case JSON_ERROR_NONE:
                return 'Success';
            case JSON_ERROR_DEPTH:
                return ' - Maximum stack depth exceeded';
            case JSON_ERROR_STATE_MISMATCH:
                return ' - Underflow or the modes mismatch';
            case JSON_ERROR_CTRL_CHAR:
                return ' - Unexpected control character found';
            case JSON_ERROR_SYNTAX:
                return ' - Syntax error, malformed JSON';
            case JSON_ERROR_UTF8:
                return ' - Malformed UTF-8 characters, possibly incorrectly encoded';
            default:
                return' - Unknown error ('.$error.')';
        }
    }


    //生成key,每10分钟一变
    public static function generate_key(){
        $ip = self::getCurlIP();
        $interval = 600; // 10分钟的秒数
        $secret_key = '5dd6f4b8ea2eda324a5629325e8868a8'; // 加密密钥

        //生成key
        $salt = floor(time() / $interval) * $interval; // 使用10分钟为间隔的时间戳作为盐

        $ip_split = explode('.', $ip);
        $hexip = sprintf('%02x%02x%02x%02x', $ip_split[0], $ip_split[1], $ip_split[2], $ip_split[3]);
        $key = hash('sha256', $ip . $salt . $hexip);

        //加密发送时间戳
        $encryptedData = openssl_encrypt($salt, 'AES-128-ECB', $secret_key, OPENSSL_RAW_DATA);
        $encrypted = base64_encode($encryptedData);

        return $key.'.'.$encrypted;
    }

    public static function getCurlIP(){
        return Yii::app()->params['uCurlIP'];
    }
}