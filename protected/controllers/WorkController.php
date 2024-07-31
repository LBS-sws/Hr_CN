<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class WorkController extends Controller
{

	public $function_id='ZA05';
	
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
                'actions'=>array('new','edit','delete','save','audit','fileupload','fileRemove'),
                'expression'=>array('WorkController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view','fileDownload','PdfDownload'),
                'expression'=>array('WorkController','allowReadOnly'),
            ),
            array('allow',
                'actions'=>array('addDate','ajaxWorkType','resetWorkTime'),
                'expression'=>array('WorkController','allowWrite'),
            ),
            array('allow',
                'actions'=>array('back'),
                'expression'=>array('WorkController','allowBack'),
            ),
            array('allow',
                'actions'=>array('cancel'),
                'expression'=>array('WorkController','allowCancelled'),
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
        return Yii::app()->user->validRWFunction('ZA05');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('ZA05');
    }

    public static function allowWrite() {
        return !empty(Yii::app()->user->id);
    }

    public static function allowCancelled() {
        return Yii::app()->user->validFunction('ZR05');
    }

    public function actionIndex($pageNum=0){
        $model = new WorkList;
        if($model->validateEmployee()){
            if (isset($_POST['WorkList'])) {
                $model->attributes = $_POST['WorkList'];
            } else {
                $session = Yii::app()->session;
                if (isset($session['work_01']) && !empty($session['work_01'])) {
                    $criteria = $session['work_01'];
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
        $model = new WorkForm('new');
        $employeeName = WorkList::getEmployeeName();
        if(empty($employeeName)){
            throw new CHttpException(404,Yii::t("contract",'The account has no binding staff, please contact the administrator'));
        }else{
            $model->employee_id = $employeeName;
        }
        $this->render('form',array('model'=>$model,));
    }

    public function actionEdit($index)
    {
        $model = new WorkForm('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionView($index)
    {
        $model = new WorkForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }


    public function actionSave()
    {
        if (isset($_POST['WorkForm'])) {
            $model = new WorkForm($_POST['WorkForm']['scenario']);
            $model->attributes = $_POST['WorkForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('work/edit',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    public function actionAudit()
    {
        if (isset($_POST['WorkForm'])) {
            $model = new WorkForm($_POST['WorkForm']['scenario']);
            $model->attributes = $_POST['WorkForm'];
            $model->audit = true;
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('work/edit',array('index'=>$model->id)));
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
        $model = new WorkForm('delete');
        if (isset($_POST['WorkForm'])) {
            $model->attributes = $_POST['WorkForm'];
            if($model->validate()){
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
                $this->redirect(Yii::app()->createUrl('work/index'));
            }else{
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','This record is already in use'));
                $this->redirect(Yii::app()->createUrl('work/edit',array('index'=>$model->id)));
            }
        }
    }

    //取消
    public function actionCancel(){
        $model = new WorkForm('cancel');
        if (isset($_POST['WorkForm'])) {
            $model->attributes = $_POST['WorkForm'];
            if($model->validate()){
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Cancel Done'));
                $this->redirect(Yii::app()->createUrl('work/index'));
            }else{
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                //Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','This record is already in use'));
                $this->redirect(Yii::app()->createUrl('work/edit',array('index'=>$model->id)));
            }
        }
    }

    //退回
    public function actionBack($index){
        $model = new WorkForm();
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            if($model->status == 1){
                Yii::app()->db->createCommand()->update('hr_employee_work', array(
                    'status'=>0
                ), 'id=:id', array(':id'=>$model->id));
                Dialog::message(Yii::t('dialog','Information'), Yii::t('contract','finish to send back'));
                $this->redirect(Yii::app()->createUrl('work/edit',array('index'=>$model->id)));
            }else{
                Dialog::message(Yii::t('dialog','Information'), "请假单异常，请刷新重试");
                $this->redirect(Yii::app()->createUrl('work/edit',array('index'=>$model->id)));
            }
        }
    }

    //時間運算
    public function actionAddDate(){
        if(Yii::app()->request->isAjaxRequest) {//是否ajax请求
            $startDate = $_POST['startDate'];
            $day = $_POST['day'];
            $type = $_POST['type'];
            $str = $type == 2?"天数":"小時";
            if(empty($startDate)||empty($day)){
                echo CJSON::encode(array("status"=>0,"message"=>"時間不能為空"));
                return true;
            }
            if(!is_numeric($day)){
                echo CJSON::encode(array("status"=>0,"message"=>$str."只能為數字"));
                return true;
            }
            if(intval($day) != $day){
                echo CJSON::encode(array("status"=>0,"message"=>$str."只能為正整數"));
                return true;
            }
            if($day < 2){
                echo CJSON::encode(array("status"=>0,"message"=>$str."必須大於1"));
                return true;
            }
            if($type == 2){
                $day--;
                $lastDate = date('Y/m/d', strtotime("$startDate +$day day"));
            }else{
                $lastDate = date('Y/m/d H:i', strtotime("$startDate +$day hours"));
            }
            echo CJSON::encode(array("status"=>1,"lastDate"=>$lastDate));
        }else{
            $this->redirect(Yii::app()->createUrl(''));
        }
    }

    //加班類型變換
    public function actionAjaxWorkType(){
        if(Yii::app()->request->isAjaxRequest) {//是否ajax请求
            $model = new WorkForm();
            $modelStr = $_POST['modelStr'];
            $work_type = $_POST['work_type'];
            $index = $_POST['index'];
            $only = $_POST['only'];
            $arr = $model->getWorkTimeHtmlToType($modelStr,$work_type,$index,$only);
            echo CJSON::encode($arr);
        }else{
            $this->redirect(Yii::app()->createUrl(''));
        }
    }



    public function actionFileupload($doctype) {
        $model = new WorkForm();
        if (isset($_POST['WorkForm'])) {
            $model->attributes = $_POST['WorkForm'];
            $id = ($_POST['WorkForm']['scenario']=='new') ? 0 : $model->id;
            $docman = new DocMan($model->docType,$id,get_class($model));
            $docman->masterId = $model->docMasterId[strtolower($doctype)];
            if (isset($_FILES[$docman->inputName])) $docman->files = $_FILES[$docman->inputName];
            $docman->fileUpload();
            if($_POST['WorkForm']['scenario']=='new'||$model->status == 0||$model->status == 3||Yii::app()->user->validFunction('ZR05')){
                echo $docman->genTableFileList(false);
            }else{
                echo $docman->genTableFileList(false,false);
            }
        } else {
            echo "NIL";
        }
    }

    public function actionFileRemove($doctype) {
        $model = new WorkForm();
        if (isset($_POST['WorkForm'])) {
            $model->attributes = $_POST['WorkForm'];
            $docman = new DocMan($model->docType,$model->id,'WorkForm');
            $docman->masterId = $model->docMasterId[strtolower($doctype)];
            $docman->fileRemove($model->removeFileId[strtolower($doctype)]);
            if($_POST['WorkForm']['scenario']=='new'||$model->status == 0||$model->status == 3||Yii::app()->user->validFunction('ZR05')){
                echo $docman->genTableFileList(false);
            }else{
                echo $docman->genTableFileList(false,false);
            }
        } else {
            echo "NIL";
        }
    }

    public function actionFileDownload($mastId, $docId, $fileId, $doctype) {
        $sql = "select city from hr_employee_work where id = $docId";
        $row = Yii::app()->db->createCommand($sql)->queryRow();
        if ($row!==false) {
            $citylist = Yii::app()->user->city_allow();
            if (strpos($citylist, $row['city']) !== false) {
                $docman = new DocMan($doctype,$docId,'WorkForm');
                $docman->masterId = $mastId;
                $docman->fileDownload($fileId);
            } else {
                throw new CHttpException(404,'Access right not match.');
            }
        } else {
            throw new CHttpException(404,'Record not found.');
        }
    }

    //測試PDF下載
    public function actionPdfDownload($index = 0){
        $model = new WorkForm('edit');
        $arr = $model->getWorkListToWorkId($index);
        if (!$arr) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $pdf = new MyPDFTwo();
            $pdf->setPageToWork($arr);
            $pdf->getOutput($arr["employee_name"]."".$arr["work_code"]);
        }
    }

    public function actionResetWorkTime(){
        $endTime = "2018-11-15 23:36:24";
        $num = Yii::app()->db->createCommand("update hr_employee_work set log_time=log_time*8,luu = 'resetWorkTime' where (luu!='resetWorkTime' OR luu IS NULL ) and work_type='2' and lcd<='$endTime'")->execute();
        //var_dump($num);
        echo "<br>update Num:".$num;
        Yii::app()->end();
    }
}