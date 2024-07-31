<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class AppointWorkController extends Controller
{
    public $function_id='ZG12';

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
                'actions'=>array('edit','reject','audit','Fileupload','FileRemove'),
                'expression'=>array('AppointWorkController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view'),
                'expression'=>array('AppointWorkController','allowReadOnly'),
            ),
            array('allow',
                'actions'=>array('fileDownload'),
                'expression'=>array('AppointWorkController','allowRead'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction("ZG12");
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction("ZG12");
    }

    public static function allowRead() {
        return true;
    }

    public function actionIndex($pageNum=0){
        $model = new AppointWorkList;
        if (isset($_POST['AppointWorkList'])) {
            $model->attributes = $_POST['AppointWorkList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['appointwork_01']) && !empty($session['appointwork_01'])) {
                $criteria = $session['appointwork_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }

    public function actionEdit($index)
    {
       $model = new AppointWorkForm('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionView($index)
    {
        $model = new AppointWorkForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    //審核通過
    public function actionAudit()
    {
        if (isset($_POST['AppointWorkForm'])) {
            $model = new AppointWorkForm('audit');
            $model->attributes = $_POST['AppointWorkForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('appointWork/index'));
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
        if (isset($_POST['AppointWorkForm'])) {
            $model = new AppointWorkForm('reject');
            $model->attributes = $_POST['AppointWorkForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('appointWork/edit',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->redirect(Yii::app()->createUrl('appointWork/edit',array('index'=>$model->id)));
            }
        }
    }

    public function actionFileupload($doctype) {
        $model = new AppointWorkForm();
        if (isset($_POST['AppointWorkForm'])) {
            $model->attributes = $_POST['AppointWorkForm'];

            $id = ($_POST['AppointWorkForm']['scenario']=='new') ? 0 : $model->id;
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
        $model = new AppointWorkForm();
        if (isset($_POST['AppointWorkForm'])) {
            $model->attributes = $_POST['AppointWorkForm'];

            $docman = new DocMan($model->docType,$model->id,'WorkForm');
            $docman->masterId = $model->docMasterId[strtolower($doctype)];
            $docman->fileRemove($model->removeFileId[strtolower($doctype)]);
            echo $docman->genTableFileList(false);
        } else {
            echo "NIL";
        }
    }

    //下載附件
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
}