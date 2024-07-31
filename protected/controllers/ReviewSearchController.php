<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class ReviewSearchController extends Controller
{
	public $function_id='RE03';

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
                'actions'=>array('index','view','test','save','downExcel','fileDownload'),
                'expression'=>array('ReviewSearchController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }
    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('RE03');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('RE03');
    }

    public function actionTest(){
        echo "start:<br/>";
        ReviewSearchForm::setOldReviewRanking();
        echo "end<br/>";
        Yii::app()->end();
    }

    public function actionIndex($pageNum=0){
        $model = new ReviewSearchList();
        if($model->validateEmployee()){
            if (isset($_POST['ReviewSearchList'])) {
                $model->attributes = $_POST['ReviewSearchList'];
            } else {
                $session = Yii::app()->session;
                if (isset($session['reviewSearch_01']) && !empty($session['reviewSearch_01'])) {
                    $criteria = $session['reviewSearch_01'];
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

    public function actionView($index)
    {
        $model = new ReviewSearchForm('view');
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

    public function actionSave()
    {
        if (isset($_POST['ReviewSearchForm'])) {
            $model = new ReviewSearchForm($_POST['ReviewSearchForm']['scenario']);
            $model->attributes = $_POST['ReviewSearchForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('reviewSearch/view',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->redirect(Yii::app()->createUrl('reviewSearch/view',array('index'=>$model->id)));
            }
        }
    }

    public function actionDownExcel()
    {
        if (isset($_POST['ReviewSearchForm'])) {
            $model = new ReviewSearchForm();
            if($model->validateEmployee()){
                if (!$model->retrieveData($_POST['ReviewSearchForm']['id'])) {
                    throw new CHttpException(404,'The requested page does not exist.');
                } else {
                    $downExcel = new DownReviewForm();
                    $downExcel->setRowExcel($model);
                    $downExcel->outDownExcel("text.xls");
                }
            }else{
                throw new CHttpException(404,Yii::t("contract",'The account has no binding staff, please contact the administrator'));
            }
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