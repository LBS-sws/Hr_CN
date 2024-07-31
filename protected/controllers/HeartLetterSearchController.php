<?php

/**
 * Created by PhpStorm.
 * User: 心意信active
 * Date: 2021/3/23 0007
 * Time: 上午 11:30
 */
class HeartLetterSearchController extends Controller
{
	public $function_id='HL03';

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
                'actions'=>array('edit','back'),
                'expression'=>array('HeartLetterSearchController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view','fileDownload','PdfDownload'),
                'expression'=>array('HeartLetterSearchController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('HL03');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('HL03');
    }

    public function actionIndex($pageNum=0){
        $model = new HeartLetterSearchList();
        if (isset($_POST['HeartLetterSearchList'])) {
            $model->attributes = $_POST['HeartLetterSearchList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['heartLetterSearch_01']) && !empty($session['heartLetterSearch_01'])) {
                $criteria = $session['heartLetterSearch_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }

    public function actionEdit($index)
    {
        $model = new HeartLetterSearchForm('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionView($index)
    {
        $model = new HeartLetterSearchForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionBack($index=0)
    {
        if (isset($_POST['HeartLetterSearchForm'])) {
            $model = new HeartLetterSearchForm("back");
            $model->attributes = $_POST['HeartLetterSearchForm'];
            $model->id = $index;
            if ($model->validate()) {
                $model->state = 0;//草稿
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('contract','finish to send back'));
                $this->redirect(Yii::app()->createUrl('HeartLetterSearch/edit',array('index'=>$model->employee_id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->redirect(Yii::app()->createUrl('HeartLetterSearch/edit',array('index'=>$model->employee_id)));
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