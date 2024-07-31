<?php

/**
 * UserForm class.
 * UserForm is the data structure for keeping
 * user form data. It is used by the 'user' action of 'SiteController'.
 */
class DepartureForm extends StaffForm
{
    public $leave_time;
    public $leave_reason;
    //
    public function validateBack(){
        $row = Yii::app()->db->createCommand()->select()->from("hr_employee")
            ->where('id=:id and staff_status = -1', array(':id'=>$this->id))->queryRow();
        if($row){
            return true;
        }else{
            return false;
        }
    }
    //
    public function saveData(){
        $uid = Yii::app()->user->id;
        Yii::app()->db->createCommand()->update('hr_employee', array(
            'staff_status'=>0,
        ), 'id=:id', array(':id'=>$this->id));
        //記錄
        Yii::app()->db->createCommand()->insert('hr_employee_history', array(
            "employee_id"=>$this->id,
            "status"=>"send back",
            "lcu"=>$uid,
            "lcd"=>date('Y-m-d H:i:s'),
        ));

        //U系统同步
        StaffForm::sendCurl($this->id,"edit");
    }

    public function retrieveData($index){
        $suffix = Yii::app()->params['envSuffix'];
        $allow_city = Yii::app()->user->city_allow();
        $row = Yii::app()->db->createCommand()->select("*,docman$suffix.countdoc('SIGNC',id) as signcdoc,docman$suffix.countdoc('EMPLOY',id) as employdoc")->from("hr_employee")
            ->where("id=:id and staff_status = -1 and city in($allow_city) ", array(':id'=>$index))->queryRow();
        if ($row){
            $this->no_of_attm['employ'] = $row['employdoc'];
            $this->no_of_attm['signc'] = $row['signcdoc'];
            $arr = $this->getMyAttr();
            $arr["leave_time"] = 2;
            $arr["leave_reason"] = 1;
            foreach ($arr as $key => $type){
                switch ($type){
                    case 1://原值
                        $value = $row[$key];
                        $value = $value===""?null:$value;
                        $this->$key = $value;
                        break;
                    case 2://日期
                        $this->$key = empty($row[$key])?null:General::toDate($row[$key]);
                        break;
                    case 3://数字
                        $this->$key = $row[$key]===null?null:floatval($row[$key]);
                        break;
                    case "birth_time"://年龄
                        $this->$key = isset($row["birth_time"])?StaffFun::getAgeForBirthDate($row["birth_time"]):floatval($row[$key]);
                        break;
                    default:
                }
            }
            return true;
        }
        return false;
    }
}
