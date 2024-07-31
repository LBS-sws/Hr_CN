<?php

/**
 * Created by PhpStorm.
 * User: 請假審核
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class AppointLeaveController extends Controller
{
    public $function_id='ZG11';

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
                'actions'=>array('reject','audit','Fileupload','FileRemove'),
                'expression'=>array('AppointLeaveController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view','edit'),
                'expression'=>array('AppointLeaveController','allowReadOnly'),
            ),
            array('allow',
                'actions'=>array('fileDownload'),
                'expression'=>array('AppointLeaveController','allowRead'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction("ZG11");
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction("ZG11");
    }

    public static function allowRead() {
        return true;
    }

    public function actionIndex($pageNum=0){
        $model = new AppointLeaveList;
        if (isset($_POST['AppointLeaveList'])) {
            $model->attributes = $_POST['AppointLeaveList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['appointleave_01']) && !empty($session['appointleave_01'])) {
                $criteria = $session['appointleave_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }

    public function actionEdit($index)
    {
        $model = new AppointLeaveForm('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionView($index)
    {
        $model = new AppointLeaveForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    //審核通過
    public function actionAudit()
    {
        if (isset($_POST['AppointLeaveForm'])) {
            $model = new AppointLeaveForm('audit');
            $model->attributes = $_POST['AppointLeaveForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('appointLeave/index'));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }
    //審核不通過
    public function actionReject()
    {
        if (isset($_POST['AppointLeaveForm'])) {
            $model = new AppointLeaveForm('reject');
            $model->attributes = $_POST['AppointLeaveForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('appointLeave/edit',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->redirect(Yii::app()->createUrl('appointLeave/edit',array('index'=>$model->id)));
            }
        }
    }

    public function actionFileupload($doctype) {
        $model = new AppointLeaveForm();
        if (isset($_POST['AppointLeaveForm'])) {
            $model->attributes = $_POST['AppointLeaveForm'];

            $id = ($_POST['AppointLeaveForm']['scenario']=='new') ? 0 : $model->id;
            $docman = new DocMan($model->docType,$id,get_class($model));
            $docman->masterId = $model->docMasterId[strtolower($doctype)];
            if (isset($_FILES[$docman->inputName])) $docman->files = $_FILES[$docman->inputName];
            $docman->fileUpload();
            echo $docman->genTableFileList(false);
        } else {
            echo "NIL";
        }
    }

    public function actionFileRemove($doctype) {
        $model = new AppointLeaveForm();
        if (isset($_POST['AppointLeaveForm'])) {
            $model->attributes = $_POST['AppointLeaveForm'];

            $docman = new DocMan($model->docType,$model->id,'LeaveForm');
            $docman->masterId = $model->docMasterId[strtolower($doctype)];
            $docman->fileRemove($model->removeFileId[strtolower($doctype)]);
            echo $docman->genTableFileList(false);
        } else {
            echo "NIL";
        }
    }

    //下載附件
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
}