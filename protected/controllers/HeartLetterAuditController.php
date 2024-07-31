<?php

/**
 * Created by PhpStorm.
 * User: 心意信active
 * Date: 2021/3/23 0007
 * Time: 上午 11:30
 */
class HeartLetterAuditController extends Controller
{
	public $function_id='HL02';

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
                'actions'=>array('edit','save','audit','end'),
                'expression'=>array('HeartLetterAuditController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view','fileDownload','PdfDownload'),
                'expression'=>array('HeartLetterAuditController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('HL02');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('HL02');
    }

    public function actionIndex($pageNum=0){
        $model = new HeartLetterAuditList();
        if($model->validateEmployee()){
            if (isset($_POST['HeartLetterAuditList'])) {
                $model->attributes = $_POST['HeartLetterAuditList'];
            } else {
                $session = Yii::app()->session;
                if (isset($session['heartLetterAudit_01']) && !empty($session['heartLetterAudit_01'])) {
                    $criteria = $session['heartLetterAudit_01'];
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
        $model = new HeartLetterAuditForm('new');
        $employeeId = WorkList::getEmployeeId();
        if(empty($employeeId)){
            throw new CHttpException(404,Yii::t("contract",'The account has no binding staff, please contact the administrator'));
        }else{
            $model->employee_id = $employeeId;
        }
        $this->render('form',array('model'=>$model,));
    }

    public function actionEdit($index)
    {
        $model = new HeartLetterAuditForm('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionView($index)
    {
        $model = new HeartLetterAuditForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionSave()
    {
        if (isset($_POST['HeartLetterAuditForm'])) {
            $model = new HeartLetterAuditForm("save");
            $model->attributes = $_POST['HeartLetterAuditForm'];
            if ($model->validate()) {
                $model->state = 3;//待处理
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('heartLetterAudit/edit',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    public function actionEnd()
    {
        if (isset($_POST['HeartLetterAuditForm'])) {
            $model = new HeartLetterAuditForm("end");
            $model->attributes = $_POST['HeartLetterAuditForm'];
            if ($model->validate()) {
                $model->state = 4;//完成
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('heartLetterAudit/edit',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    public function actionAudit()
    {
        if (isset($_POST['HeartLetterAuditForm'])) {
            $model = new HeartLetterAuditForm("audit");
            $model->attributes = $_POST['HeartLetterAuditForm'];
            if ($model->validate()) {
                $model->state = 4;//完成
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('heartLetterAudit/edit',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    public function actionFileDownload($mastId, $docId, $fileId, $doctype) {
        $sql = "select city from hr_letter where id = $docId";
        $row = Yii::app()->db->createCommand($sql)->queryRow();
        if ($row!==false) {
            $citylist = Yii::app()->user->city_allow();
            if (strpos($citylist, $row['city']) !== false) {
                $docman = new DocMan($doctype,$docId,'HeartLetterForm');
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