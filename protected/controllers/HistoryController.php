<?php
/**
 * Created by PhpStorm.
 * User: 沈超
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class HistoryController extends Controller
{
	public $function_id='ZE04';

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
                'actions'=>array('new','edit','delete','save','audit','finish','fileupload','fileRemove'),
                'expression'=>array('HistoryController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view','form','detail','fileDownload'),
                'expression'=>array('HistoryController','allowReadOnly'),
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

    public function actionIndex($pageNum=0){
        $model = new HistoryList;
        if (isset($_POST['HistoryList'])) {
            $model->attributes = $_POST['HistoryList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['history_01']) && !empty($session['history_01'])) {
                $criteria = $session['history_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }

    public function actionForm($index,$type = "")
    {
        $model = new HistoryForm($type);
        if(!$model->validateStaff($index,$type)){
            Dialog::message(Yii::t('dialog','Validation Message'), Yii::t('contract','The employee has changed the information, please complete the change first'));
            $this->redirect(Yii::app()->createUrl('employee/edit',array('index'=>$index)));
        }
        if (!$model->newData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,'back_type'=>$type));
        }
    }

    public function actionDetail($index)
    {
        $model = new HistoryForm("view");
        $oldModel = new EmployeeForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $model->staff_status = 2;
            $model->setScenario('view');
            $oldModel->retrieveData($model->employee_id);
            $this->render('detail',array('model'=>$model,'oldModel'=>$oldModel));
        }
    }

    public function actionEdit($index)
    {
        $model = new HistoryForm('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionView($index)
    {
        $model = new HistoryForm('view');
        if(!$model->validateStaff($index,'view')){
            Dialog::message(Yii::t('dialog','Validation Message'), Yii::t('contract','The employee has changed the information, please complete the change first'));
            $this->redirect(Yii::app()->createUrl('employee/edit',array('index'=>$index)));
        }
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }


    //刪除草稿
    public function actionDelete(){
        $model = new HistoryForm('delete');
        if (isset($_POST['HistoryForm'])) {
            $model->attributes = $_POST['HistoryForm'];
            if($model->validateDelete()){
                $model->deleteHistory();
                $this->redirect(Yii::app()->createUrl('history/index'));
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
            }else{
                Dialog::message(Yii::t('dialog','Information'), "無法刪除,該變更不是草稿");
                $this->redirect(Yii::app()->createUrl('history/edit',array('index'=>$model->id)));
            }
        }
    }
    public function actionSave()
    {
        if (isset($_POST['HistoryForm'])) {
            $model = new HistoryForm($_POST['HistoryForm']['scenario']);
            $model->attributes = $_POST['HistoryForm'];
            if ($model->validate()) {
                $model->audit = false;
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('history/edit',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    //變更要求審核
    public function actionAudit()
    {
        if (isset($_POST['HistoryForm'])) {
            $model = new HistoryForm($_POST['HistoryForm']['scenario']);
            $model->attributes = $_POST['HistoryForm'];
            if ($model->validate()) {
                $model->audit = true;
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('history/edit',array('index'=>$model->id,"type"=>"view")));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    //上傳附件
    public function actionFileupload($doctype) {
        $model = new HistoryForm();
        if (isset($_POST['HistoryForm'])) {
            $model->attributes = $_POST['HistoryForm'];

            $id = (empty($_POST['HistoryForm']['id'])) ? 0 : $model->id;
            $docman = new DocMan($model->docType,$id,get_class($model));
            $docman->masterId = $model->docMasterId[strtolower($doctype)];
            if (isset($_FILES[$docman->inputName])) $docman->files = $_FILES[$docman->inputName];
            $docman->fileUpload();
            echo $docman->genTableFileList(false);
        } else {
            echo "NIL";
        }
    }

    //刪除附件
    public function actionFileRemove($doctype) {
        $model = new HistoryForm();
        if (isset($_POST['HistoryForm'])) {
            $model->attributes = $_POST['HistoryForm'];

            $docman = new DocMan($model->docType,$model->id,'HistoryForm');
            $docman->masterId = $model->docMasterId[strtolower($doctype)];
            $docman->fileRemove($model->removeFileId[strtolower($doctype)]);
            echo $docman->genTableFileList(false);
        } else {
            echo "NIL";
        }
    }

    //下載附件
    public function actionFileDownload($mastId, $docId, $fileId, $doctype) {
        if($doctype == "EMPLOYEE"){
            $form = "HistoryForm";
            $sql = "select city from hr_employee_operate where id = $docId";
        }else{
            $form = "EmployForm";
            $sql = "select city from hr_employee where id = $docId";
        }
        $row = Yii::app()->db->createCommand($sql)->queryRow();
        if ($row!==false) {
            $citylist = Yii::app()->user->city_allow();
            if (strpos($citylist, $row['city']) !== false) {
                $docman = new DocMan($doctype,$docId,$form);
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