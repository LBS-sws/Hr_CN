<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class DepartureController extends Controller
{
	public $function_id='ZE02';

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
                'actions'=>array('fileupload'),
                'expression'=>array('DepartureController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view','edit','fileDownload'),
                'expression'=>array('DepartureController','allowReadOnly'),
            ),
            array('allow',
                'actions'=>array('back'),
                'expression'=>array('DepartureController','allowBack'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('ZE02');
    }
    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('ZE01');
    }
    public static function allowBack() {
        return Yii::app()->user->validFunction('ZR19');
    }

    public function actionIndex($pageNum=0){
        $model = new DepartureList;
        if (isset($_POST['DepartureList'])) {
            $model->attributes = $_POST['DepartureList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['departure_01']) && !empty($session['departure_01'])) {
                $criteria = $session['departure_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }

    public function actionEdit($index)
    {
        $model = new DepartureForm('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionBack()
    {
        $model = new DepartureForm('back');
        $model->attributes = $_POST['DepartureForm'];
        if ($model->validateBack()) {
            $model->saveData();
            Dialog::message(Yii::t('dialog','Information'), Yii::t('contract','finish to send back'));
            $this->redirect(Yii::app()->createUrl('Departure/index'));
        } else {
            $message = CHtml::errorSummary($model);
            Dialog::message(Yii::t('dialog','Validation Message'), $message);
            $this->redirect(Yii::app()->createUrl('Departure/edit',array('index'=>$model->id)));
        }
    }

    public function actionView($index)
    {
        $model = new DepartureForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
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

    //上傳附件
    public function actionFileupload($doctype) {
        $model = new DepartureForm();
        if (isset($_POST['DepartureForm'])) {
            $model->attributes = $_POST['DepartureForm'];

            $id = ($_POST['DepartureForm']['scenario']=='new') ? 0 : $model->id;
            $docman = new DocMan($model->docType,$id,get_class($model));
            $docman->masterId = $model->docMasterId[strtolower($doctype)];
            if (isset($_FILES[$docman->inputName])) $docman->files = $_FILES[$docman->inputName];
            $docman->fileUpload();
            echo $docman->genTableFileList(false);
        } else {
            echo "NIL";
        }
    }
}