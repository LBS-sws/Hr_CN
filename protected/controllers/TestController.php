<?php

class TestController extends Controller
{
	public $interactive = false;
	
    public function actionTest(){
        $command = Yii::app()->db->createCommand();
        $firstday = date("Y/m/d");
        $lastday = date("Y/m/d",strtotime("$firstday + 1 month"));
        $command->reset();
        $aaa = $command->update('hr_employee', array("z_index"=>3),"staff_status=0 and test_type=1 and replace(test_start_time,'-', '/') <= '$firstday' and replace(test_end_time,'-', '/') >='$firstday'");//試用期
        $command->reset();
        echo "試用期:$aaa<br>";
        $aaa = $command->update('hr_employee', array("z_index"=>2),"staff_status=0 and test_type=1 and replace(test_start_time,'-', '/') >= '$firstday'");//未入職
        $command->reset();
        echo "未入職:$aaa<br>";
        $aaa = $command->update('hr_employee', array("z_index"=>1),"staff_status=0 and (test_type=0 or replace(test_end_time,'-', '/') <='$firstday')");//正式員工
        $command->reset();
        echo "正式員工:$aaa<br>";
        $aaa = $command->update('hr_employee', array("z_index"=>4),"staff_status=0 and fix_time='fixation' and replace(end_time,'-', '/') >='$firstday' and replace(end_time,'-', '/') <='$lastday'");//合同即將過期
        $command->reset();
        echo "合同即將過期:$aaa<br>";
        $aaa = $command->update('hr_employee', array("z_index"=>5),"staff_status=0 and fix_time='fixation' and replace(end_time,'-', '/') <'$firstday'");//合同過期

        echo "合同過期:$aaa<br>";
    }

    public function actionAaa($city="SH"){
        $email = new Email();
        $email->addEmailToPrefixAndCity("ZE01",$city);
        var_dump($email->getToAddr());
    }
}