<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class EmployeeController extends Controller
{
	public $function_id='ZE03';

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
                'actions'=>array('new','edit','save','test','displace'),
                'expression'=>array('EmployeeController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view'),
                'expression'=>array('EmployeeController','allowReadOnly'),
            ),
            array('allow',
                'actions'=>array('FileDownload','DownAgreement','DownOnlyContract','Downfile','Generate'),
                'expression'=>array('EmployeeController','allowWrite'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }
    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('ZE03');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('ZE03');
    }

    public static function allowWrite() {
        return !empty(Yii::app()->user->id);
    }

    public function actionTest($id=0){
        AuditHistoryForm::resetOnlyHistory($id);
    }

    public function actionIndex($pageNum=0){
        $model = new EmployeeList;
        if (isset($_POST['EmployeeList'])) {
            $model->attributes = $_POST['EmployeeList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['employee_01']) && !empty($session['employee_01'])) {
                $criteria = $session['employee_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }


    public function actionNew()
    {
        $model = new EmployeeForm('new');
        $this->render('form',array('model'=>$model,));
    }

    public function actionEdit($index)
    {
        $model = new EmployeeForm('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionDisplace($index)
    {
        $model = new EmployeeForm('edit');
        if ($model->validateDisplace($index)) {
            $model->saveDisplace();
            Dialog::message(Yii::t('dialog','Information'), Yii::t('contract','displace finish'));
            $this->redirect(Yii::app()->createUrl('employee/index'));
        } else {
            $message = CHtml::errorSummary($model);
            Dialog::message(Yii::t('dialog','Validation Message'), $message);
            $this->redirect(Yii::app()->createUrl('employee/edit',array('index'=>$index)));
        }
    }

    public function actionView($index)
    {
        $model = new EmployeeForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionSave()
    {
        if (isset($_POST['EmployeeForm'])) {
            $model = new EmployeeForm($_POST['EmployeeForm']['scenario']);
            $model->attributes = $_POST['EmployeeForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('employee/edit',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                $model->historyList = AuditHistoryForm::getStaffHistoryList($model->id);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    //生成合同
    public function actionGenerate($index=0){
        if (empty($index) || !is_numeric($index)){
            $this->redirect(Yii::app()->createUrl('employee/index'));
        }else{
            $bool = EmployeeForm::updateEmployeeWord($index);
            if (!$bool){
                $this->redirect(Yii::app()->createUrl('employee/index'));
            }else{
                Dialog::message(Yii::t('dialog','Information'), Yii::t('contract','Contract formation success'));
                $this->redirect(Yii::app()->createUrl('employee/edit',array('index'=>$index)));
            }
        }
    }

    //下載合同

    public function actionDownfile($index)
    {
        $url = EmployeeForm::updateEmployeeWord($index);
        if($url){
            Dialog::message(Yii::t('dialog','Information'), Yii::t("contract","Please handle the documents of the contract salary"));
            $file = Yii::app()->basePath."/../".$url["word_url"];
            // To prevent corrupted zip - Percy
            ob_clean();
            ob_end_flush();
            //
            header("Content-type: application/octet-stream");
            header('Content-Disposition: attachment; filename='.$url["name"].'.docx');
            header("Content-Length: ". filesize($file));
            readfile($file);
        }else{
            $this->render('index');
        }

    }

    //下載合同(選擇部分文檔下載）

    public function actionDownOnlyContract()
    {
        $index = $_POST["id"];
        if(empty($index) || empty($_POST["word"])){
            Dialog::message(Yii::t('dialog','Information'), "請選擇需要的合同文檔");
            $this->redirect(Yii::app()->createUrl('employee/edit',array('index'=>$index)));
            return false;
        }
        $url = EmployeeForm::updateEmployeeWord($index,$_POST["word"]);
        if($url){
            $file = Yii::app()->basePath."/../".$url["word_url"];
            // To prevent corrupted zip - Percy
            ob_clean();
            ob_end_flush();
            //
            header("Content-type: application/octet-stream");
            header('Content-Disposition: attachment; filename='.$url["name"].'.docx');
            header("Content-Length: ". filesize($file));
            readfile($file);
        }else{
            $this->render('index');
        }

    }


    //下載補充協議
    public function actionDownAgreement($index,$staff){
        $model = new EmployeeForm();
        if (!$model->retrieveData($staff)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $url = $model->downAgreement($index);
            // To prevent corrupted zip - Percy
            $file = Yii::app()->basePath."/../".$url;
            ob_clean();
            ob_end_flush();
            //
            header("Content-type: application/octet-stream");
            header('Content-Disposition: attachment; filename="补充协议('.$model->name.').docx"');
            header("Content-Length: ". filesize($file));
            readfile($file);
        }
    }

/*    //刪除員工
    public function actionDelete(){
        $model = new EmployeeForm('delete');
        if (isset($_POST['EmployeeForm'])) {
            $model->attributes = $_POST['EmployeeForm'];
            $model->saveData();
            Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
        }
        $this->redirect(Yii::app()->createUrl('employee/index'));
    }*/
    //下載附件
    public function actionFileDownload($mastId, $docId, $fileId, $doctype) {
        $sql = "select city from hr_employee where id = $docId";
        $row = Yii::app()->db->createCommand($sql)->queryRow();
        if ($row!==false) {
            $citylist = Yii::app()->user->city_allow();
            if (strpos($citylist, $row['city']) !== false) {
                $docman = new DocMan($doctype,$docId,'EmployForm');
                $docman->masterId = $mastId;
                $docman->fileDownload($fileId);
            } else {
                throw new CHttpException(404,'Access right not match.');
            }
        } else {
            throw new CHttpException(404,'Record not found.');
        }
    }
}