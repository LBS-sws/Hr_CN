<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class ReviewHandleController extends Controller
{
	public $function_id='RE02';

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
                'actions'=>array('new','edit','draft','save','copy'),
                'expression'=>array('ReviewHandleController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view','fileDownload'),
                'expression'=>array('ReviewHandleController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }
    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('RE02');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('RE02');
    }

    public function actionIndex($pageNum=0){
        $model = new ReviewHandleList();
        if($model->validateEmployee()){
            if (isset($_POST['ReviewHandleList'])) {
                $model->attributes = $_POST['ReviewHandleList'];
            } else {
                $session = Yii::app()->session;
                if (isset($session['reviewHandle_01']) && !empty($session['reviewHandle_01'])) {
                    $criteria = $session['reviewHandle_01'];
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

    public function actionEdit($index)
    {
        $model = new ReviewHandleForm('edit');
        if($model->validateEmployee()){
            if (!$model->retrieveData($index)) {
                throw new CHttpException(404,'The requested page does not exist.');
            } else {
                $this->render('form',array('model'=>$model,));
            }
        }else{
            throw new CHttpException(404,Yii::t("contract",'The account has no binding staff, please contact the administrator'));
        }
    }

    public function actionView($index)
    {
        $model = new ReviewHandleForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }


    public function actionSave()
    {
        if (isset($_POST['ReviewHandleForm'])) {
            $model = new ReviewHandleForm($_POST['ReviewHandleForm']['scenario']);
            $model->attributes = $_POST['ReviewHandleForm'];
            $model->status_type = 3;
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('reviewHandle/index'));
            } else {
                $model->status_type = 0;
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    public function actionDraft()
    {
        if (isset($_POST['ReviewHandleForm'])) {
            $model = new ReviewHandleForm("draft");
            $model->attributes = $_POST['ReviewHandleForm'];
            $model->status_type = 4;
            if ($model->validate()) {
                $model->setScenario($_POST['ReviewHandleForm']['scenario']);
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('reviewHandle/edit',array('index'=>$model->id)));
            } else {
                $model->setScenario($_POST['ReviewHandleForm']['scenario']);
                $model->status_type = 0;
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    public function actionCopy()
    {
        if(Yii::app()->request->isAjaxRequest) {//是否ajax请求
            $model = new ReviewHandleForm();
            $model->attributes = $_POST['ReviewHandleForm'];
            if($model->validateEmployee()){
                echo CJSON::encode(array("status"=>1,"list"=>$model->getLastTemList()));
            }else{
                echo CJSON::encode(array("status"=>0));
            }
        }else{
            $this->redirect(Yii::app()->createUrl(''));
        }
    }


    //下載附件
    public function actionFileDownload($mastId, $docId, $fileId, $doctype) {
        $sql = "select b.city from hr_review a LEFT JOIN hr_employee b ON a.employee_id = b.id where a.id = $docId";
        $row = Yii::app()->db->createCommand($sql)->queryRow();
        if ($row!==false) {
            $citylist = Yii::app()->user->city_allow();
            if (strpos($citylist, $row['city']) !== false) {
                $docman = new DocMan($doctype,$docId,'ReviewAllotForm');
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