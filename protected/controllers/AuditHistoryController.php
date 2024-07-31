<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class AuditHistoryController extends Controller
{
	public $function_id='ZG02';


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
                'actions'=>array('edit','reject','audit'),
                'expression'=>array('AuditHistoryController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view','fileDownload'),
                'expression'=>array('AuditHistoryController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('ZG02');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('ZG02');
    }

    public function actionIndex($pageNum=0){
        $model = new AuditHistoryList;
        if (isset($_POST['AuditHistoryList'])) {
            $model->attributes = $_POST['AuditHistoryList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['audithistory_01']) && !empty($session['audithistory_01'])) {
                $criteria = $session['audithistory_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }

    public function actionEdit($index)
    {
        $model = new AuditHistoryForm('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            if($model->table_type==1){
                $oldModel = new EmployeeForm('view');
            }else{
                $oldModel = new ExternalForm('view');
            }
            $oldModel->retrieveData($model->employee_id);
            $this->render('form',array('model'=>$model,'oldModel'=>$oldModel));
        }
    }

    public function actionView($index)
    {
        $model = new AuditHistoryForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            if($model->table_type==1){
                $oldModel = new EmployeeForm('view');
            }else{
                $oldModel = new ExternalForm('view');
            }
            $oldModel->retrieveData($model->employee_id);
            $this->render('form',array('model'=>$model,'oldModel'=>$oldModel));
        }
    }
    //審核通過
    public function actionAudit()
    {
        if (isset($_POST['AuditHistoryForm'])) {
            $model = new AuditHistoryForm('audit');
            $model->attributes = $_POST['AuditHistoryForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('AuditHistory/index'));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->redirect(Yii::app()->createUrl('AuditHistory/edit',array('index'=>$model->id)));
            }
        }
    }
    //審核不通過
    public function actionReject()
    {
        if (isset($_POST['AuditHistoryForm'])) {
            $model = new AuditHistoryForm('reject');
            $model->attributes = $_POST['AuditHistoryForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Request Denied'));
                $this->redirect(Yii::app()->createUrl('AuditHistory/index'));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->redirect(Yii::app()->createUrl('AuditHistory/edit',array('index'=>$model->id)));
            }
        }
    }
    //下載附件
    public function actionFileDownload($mastId, $docId, $fileId, $doctype) {
        $sql = "select city from hr_employee_operate where id = $docId";
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