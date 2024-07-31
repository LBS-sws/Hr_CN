<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class AuditController extends Controller
{

	public $function_id='ZG01';


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
                'expression'=>array('AuditController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view','fileDownload'),
                'expression'=>array('AuditController','allowReadOnly'),
            ),
            array('allow',
                'actions'=>array('Generate'),
                'expression'=>array('AuditController','allowWrite'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('ZG01');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('ZG01');
    }

    public static function allowWrite() {
        return !empty(Yii::app()->user->id);
    }

    public function actionIndex($pageNum=0){
        $model = new AuditList;
        if (isset($_POST['AuditList'])) {
            $model->attributes = $_POST['AuditList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['audit_01']) && !empty($session['audit_01'])) {
                $criteria = $session['audit_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }

    public function actionEdit($index)
    {
        $model = new AuditForm('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }
    public function actionView($index)
    {
        $model = new AuditForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionReject()
    {
        if (isset($_POST['AuditForm'])) {
            $model = new AuditForm('reject');
            $model->attributes = $_POST['AuditForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Request Denied'));
                $this->redirect(Yii::app()->createUrl('audit/edit',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    public function actionAudit()
    {
        if (isset($_POST['AuditForm'])) {
            $model = new AuditForm('audit');
            $model->attributes = $_POST['AuditForm'];
            if ($model->validate()) {
                $model->audit = true;
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('audit/edit',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    //生成合同
    public function actionGenerate($index=0){
        if (empty($index) || !is_numeric($index)){
            $this->redirect(Yii::app()->createUrl('audit/index'));
        }else{
            $bool = StaffForm::updateEmployeeWord($index);
            if (!$bool){
                $this->redirect(Yii::app()->createUrl('audit/index'));
            }else{
                Dialog::message(Yii::t('dialog','Information'), Yii::t('contract','Contract formation success'));
                $this->redirect(Yii::app()->createUrl('audit/edit',array('index'=>$index)));
            }
        }
    }
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