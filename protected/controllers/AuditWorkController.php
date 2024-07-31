<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class AuditWorkController extends Controller
{
    protected static $assList=array(
        1=>"ZA08",
        2=>"ZE05",
        3=>"ZG04",
        4=>"ZC10",
        5=>"ZP01",
    );

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
                'expression'=>array('AuditWorkController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view'),
                'expression'=>array('AuditWorkController','allowReadOnly'),
            ),
            array('allow',
                'actions'=>array('fileDownload'),
                'expression'=>array('AuditWorkController','allowRead'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        if(array_key_exists("only",$_GET)){
            $only = $_GET["only"];
            if(!key_exists($only,self::$assList)){
                $only = 1;
            }
        }else{
            $only = 1;
        }
        return Yii::app()->user->validRWFunction(self::$assList[$only]);
    }

    public static function allowReadOnly() {
        if(array_key_exists("only",$_GET)){
            $only = $_GET["only"];
            if(!key_exists($only,self::$assList)){
                $only = 1;
            }
        }else{
            $only = 1;
        }
        return Yii::app()->user->validFunction(self::$assList[$only]);
    }

    public static function allowRead() {
        return true;
    }

    public function actionIndex($pageNum=0,$only = 1){
		$this->function_id = self::$assList[$only];
		Yii::app()->session['active_func'] = $this->function_id;
        $model = new AuditWorkList;
        $model->only = $only;
        if (isset($_POST['AuditWorkList'])) {
            $model->attributes = $_POST['AuditWorkList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['auditwork_01']) && !empty($session['auditwork_01'])) {
                $criteria = $session['auditwork_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }

    public function actionEdit($index,$only = 1)
    {
 		$this->function_id = self::$assList[$only];
		Yii::app()->session['active_func'] = $this->function_id;
       $model = new AuditWorkForm('edit');
        if(!key_exists($only,self::$assList)){
            $only = 1;
        }
        $model->only = $only;
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionView($index,$only = 1)
    {
		$this->function_id = self::$assList[$only];
		Yii::app()->session['active_func'] = $this->function_id;
        $model = new AuditWorkForm('view');
        if(!key_exists($only,self::$assList)){
            $only = 1;
        }
        $model->only = $only;
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    //審核通過
    public function actionAudit()
    {
        if (isset($_POST['AuditWorkForm'])) {
            $model = new AuditWorkForm('audit');
            $model->attributes = $_POST['AuditWorkForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('auditWork/index',array('only'=>$model->only)));
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
        if (isset($_POST['AuditWorkForm'])) {
            $model = new AuditWorkForm('reject');
            $model->attributes = $_POST['AuditWorkForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('auditWork/edit',array('index'=>$model->id,'only'=>$model->only)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->redirect(Yii::app()->createUrl('auditWork/edit',array('index'=>$model->id,'only'=>$model->only)));
            }
        }
    }

    public function actionFileupload($doctype) {
        $model = new AuditWorkForm();
        if (isset($_POST['AuditWorkForm'])) {
            $model->attributes = $_POST['AuditWorkForm'];

            $id = ($_POST['AuditWorkForm']['scenario']=='new') ? 0 : $model->id;
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
        $model = new AuditWorkForm();
        if (isset($_POST['AuditWorkForm'])) {
            $model->attributes = $_POST['AuditWorkForm'];

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