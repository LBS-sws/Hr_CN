<?php

/**
 * Created by PhpStorm.
 * User: 請假active 
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class LeaveController extends Controller
{
	public $function_id='ZA06';

    public function filters()
    {
        return array(
            'enforceSessionExpiration',
            'enforceNoConcurrentLogin',
            'accessControl', // perform access control for CRUD operations
            'postOnly + delete', // we only allow deletion via POST request
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(
            array('allow',
                'actions'=>array('new','edit','delete','save','audit','fileupload','fileRemove','ajaxYearDay','test','test2'),
                'expression'=>array('LeaveController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view','testManger','fileDownload','PdfDownload'),
                'expression'=>array('LeaveController','allowReadOnly'),
            ),
            array('allow',
                'actions'=>array('addDate'),
                'expression'=>array('LeaveController','allowWrite'),
            ),
            array('allow',
                'actions'=>array('back'),
                'expression'=>array('LeaveController','allowBack'),
            ),
            array('allow',
                'actions'=>array('cancel'),
                'expression'=>array('LeaveController','allowCancelled'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowBack() {
        return Yii::app()->user->validFunction('ZR13');
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('ZA06');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('ZA06');
    }

    public static function allowWrite() {
        return !empty(Yii::app()->user->id);
    }

    public static function allowCancelled() {
        return Yii::app()->user->validFunction('ZR05');
    }

    public function actionIndex($pageNum=0){
        $model = new LeaveList;
        if($model->validateEmployee()){
            if (isset($_POST['LeaveList'])) {
                $model->attributes = $_POST['LeaveList'];
            } else {
                $session = Yii::app()->session;
                if (isset($session['leave_01']) && !empty($session['leave_01'])) {
                    $criteria = $session['leave_01'];
                    $model->setCriteria($criteria);
                }
            }
            $model->determinePageNum($pageNum);
            $model->retrieveDataByPage($model->pageNum);
            $this->render('index',array('model'=>$model));
        }else{
            throw new CHttpException(404,Yii::t("contract",'The account has no binding staff, please contact the administrator'));
        }
    }


    public function actionNew()
    {
        $model = new LeaveForm('new');
        //$employeeId = WorkList::getEmployeeId();
        if($model->validateEmployee()){
            $this->render('form',array('model'=>$model,));
         }else{
            throw new CHttpException(404,Yii::t("contract",'The account has no binding staff, please contact the administrator'));
        }
    }

    public function actionEdit($index)
    {
        $model = new LeaveForm('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionView($index)
    {
        $model = new LeaveForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }


    public function actionSave()
    {
        if (isset($_POST['LeaveForm'])) {
            $model = new LeaveForm($_POST['LeaveForm']['scenario']);
            $model->attributes = $_POST['LeaveForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('leave/edit',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    public function actionAudit()
    {
        if (isset($_POST['LeaveForm'])) {
            $model = new LeaveForm($_POST['LeaveForm']['scenario']);
            $model->attributes = $_POST['LeaveForm'];
            $model->audit = true;
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('leave/edit',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $model->audit = false;
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    //刪除
    public function actionDelete(){
        $model = new LeaveForm('delete');
        if (isset($_POST['LeaveForm'])) {
            $model->attributes = $_POST['LeaveForm'];
            if($model->validate()){
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
                $this->redirect(Yii::app()->createUrl('leave/index'));
            }else{
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','This record is already in use'));
                $this->redirect(Yii::app()->createUrl('leave/edit',array('index'=>$model->id)));
            }
        }
    }

    //取消
    public function actionCancel(){
        $model = new LeaveForm('cancel');
        if (isset($_POST['LeaveForm'])) {
            $model->attributes = $_POST['LeaveForm'];
            if($model->validate()){
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Cancel Done'));
                $this->redirect(Yii::app()->createUrl('leave/index'));
            }else{
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->redirect(Yii::app()->createUrl('leave/edit',array('index'=>$model->id)));
            }
        }
    }

    //退回
    public function actionBack($index){
        $model = new LeaveForm();
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            if($model->status == 1){
                Yii::app()->db->createCommand()->update('hr_employee_leave', array(
                    'status'=>0
                ), 'id=:id', array(':id'=>$model->id));
                Dialog::message(Yii::t('dialog','Information'), Yii::t('contract','finish to send back'));
                $this->redirect(Yii::app()->createUrl('leave/edit',array('index'=>$model->id)));
            }else{
                Dialog::message(Yii::t('dialog','Information'), "请假单异常，请刷新重试");
                $this->redirect(Yii::app()->createUrl('leave/edit',array('index'=>$model->id)));
            }
        }
    }


    //時間運算
    public function actionAddDate(){
        if(Yii::app()->request->isAjaxRequest) {//是否ajax请求
            $startDate = $_POST['startDate'];
            $day = $_POST['day'];
            if(empty($startDate)||empty($day)){
                echo CJSON::encode(array("status"=>0,"message"=>"時間不能為空"));
                return true;
            }
            if(!is_numeric($day)){
                echo CJSON::encode(array("status"=>0,"message"=>"时间周期只能為數字"));
                return true;
            }
            if($day < 2){
                echo CJSON::encode(array("status"=>0,"message"=>"时间周期必須大於1"));
                return true;
            }
            $day--;
            $lastDate = date('Y/m/d', strtotime("$startDate +$day day"));
            echo CJSON::encode(array("status"=>1,"lastDate"=>$lastDate));
        }else{
            $this->redirect(Yii::app()->createUrl(''));
        }
    }



    public function actionFileupload($doctype) {
        $model = new LeaveForm();
        if (isset($_POST['LeaveForm'])) {
            $model->attributes = $_POST['LeaveForm'];

            $id = ($_POST['LeaveForm']['scenario']=='new') ? 0 : $model->id;
            $docman = new DocMan($model->docType,$id,get_class($model));
            $docman->masterId = $model->docMasterId[strtolower($doctype)];
            if (isset($_FILES[$docman->inputName])) $docman->files = $_FILES[$docman->inputName];
            $docman->fileUpload();
            if($_POST['LeaveForm']['scenario']=='new'||$model->status == 0||$model->status == 3||Yii::app()->user->validFunction('ZR05')){
                echo $docman->genTableFileList(false);
            }else{
                echo $docman->genTableFileList(false,false);
            }
        } else {
            echo "NIL";
        }
    }

    public function actionFileRemove($doctype) {
        $model = new LeaveForm();
        if (isset($_POST['LeaveForm'])) {
            $model->attributes = $_POST['LeaveForm'];

            $docman = new DocMan($model->docType,$model->id,'LeaveForm');
            $docman->masterId = $model->docMasterId[strtolower($doctype)];
            $docman->fileRemove($model->removeFileId[strtolower($doctype)]);
            if($_POST['LeaveForm']['scenario']=='new'||$model->status == 0||$model->status == 3||Yii::app()->user->validFunction('ZR05')){
                echo $docman->genTableFileList(false);
            }else{
                echo $docman->genTableFileList(false,false);
            }
        } else {
            echo "NIL";
        }
    }

    public function actionFileDownload($mastId, $docId, $fileId, $doctype) {
        $sql = "select city from hr_employee_leave where id = $docId";
        $row = Yii::app()->db->createCommand($sql)->queryRow();
        if ($row!==false) {
            $citylist = Yii::app()->user->city_allow();
            if (strpos($citylist, $row['city']) !== false) {
                $docman = new DocMan($doctype,$docId,'LeaveForm');
                $docman->masterId = $mastId;
                $docman->fileDownload($fileId);
            } else {
                throw new CHttpException(404,'Access right not match.');
            }
        } else {
            throw new CHttpException(404,'Record not found.');
        }
    }

    //PDF下載
    public function actionPdfDownload($index = 0){
        $model = new LeaveForm('edit');
        $arr = $model->getLeaveListToLeaveId($index);
        if (!$arr) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $pdf = new MyPDFTwo();
            $pdf->setPageToLeave($arr);
            $pdf->getOutput($arr["leave_code"]);
        }
    }

    //计算年假
    public function actionAjaxYearDay(){
        if(Yii::app()->request->isAjaxRequest) {//是否ajax请求
            $index = $_POST["index"];
            $time = $_POST["time"];
            $leave_type = $_POST["leave_type"];
            $work_id = key_exists("work_id",$_POST)?$_POST["work_id"]:array();
            $model = new VacationDayForm($index,$leave_type,$time);
            $useDay = $model->getVacationSum();
            $vacationList = $model->vacation_list;
            $remark = key_exists("remark",$vacationList)?$vacationList["remark"]:"";
            $workHtml = LeaveForm::getWorkSelectDiv($vacationList,$index,$work_id);
            if($model->remain_bool){
                $entry_time = $model->getEndTime();
                $html = "<p class='form-control-static text-success'>".Yii::t("contract","remaining days")."：".$useDay."</p>";
            }else{
                $entry_time = date("Y/m/d",strtotime(date("Y/m/d")."+2 year"));
                $html = "";
            }
            echo CJSON::encode(array("status"=>1,"work_html"=>$workHtml,"html"=>$html,"remark"=>$remark,"entry_time"=>$entry_time));
        }else{
            $this->redirect(Yii::app()->createUrl(''));
        }
    }

    //id:员工id  type：1请假 0：加班
    public function actionTestManger($id,$type=1){
        $z_index = AuditConfigForm::getCityAuditToCodeTest($id,$type);
        var_dump($z_index);
        Yii::app()->end();
    }

    //
    public function actionTest($index,$type=0){
        //type：1请假 0：加班
        $type = empty($type)?"ZA08":"ZA09";
        $index = is_numeric($index)?$index:0;
        $systemId = Yii::app()->params['systemId'];
        $suffix = Yii::app()->params['envSuffix'];
        $command = Yii::app()->db->createCommand();
        $workOne = $command->select("a.user_id,a.employee_id,d.name,b.city")->from("hr_binding a")
            ->leftJoin("hr_employee d","d.id = a.employee_id")
            ->leftJoin("security$suffix.sec_user b","b.username = a.user_id")
            ->leftJoin("security$suffix.sec_user_access c","c.username = a.user_id")
            ->where("b.status='A' and b.city=d.city and c.system_id='$systemId' and c.a_read_write like '%{$type}%' and d.department='$index'")
            ->queryAll();
        var_dump($workOne);
    }

    //
    public function actionTest2($city,$type=0){
        //type：1请假 0：加班
        $type = empty($type)?"ZE05":"ZE06";
        $systemId = Yii::app()->params['systemId'];
        $suffix = Yii::app()->params['envSuffix'];
        $command = Yii::app()->db->createCommand();
        $workTwo = $command->select("a.username")->from("security$suffix.sec_user a")
            ->leftJoin("security$suffix.sec_user_access b","b.username = a.username")
            ->where("a.status='A' and b.system_id='$systemId' and b.a_read_write like '%{$type}%' and a.city=:city",
                array(':city'=>$city))->queryAll();
        var_dump($workTwo);
    }
}