<?php

/**
 */
class SupportSearchController extends Controller
{
	public $function_id='AY03';

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
                'actions'=>array('early','delete','downExcel','back'),
                'expression'=>array('SupportSearchController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view'),
                'expression'=>array('SupportSearchController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('AY02');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('AY03')||Yii::app()->user->validFunction('AY04');
    }

    public function actionIndex($pageNum=0){
        $model = new SupportSearchList;
        if (isset($_POST['SupportSearchList'])) {
            $model->attributes = $_POST['SupportSearchList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['supportSearch_01']) && !empty($session['supportSearch_01'])) {
                $criteria = $session['supportSearch_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }

    public function actionView($index)
    {
        $model = new SupportSearchForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionEarly()
    {
        if (isset($_POST['SupportSearchForm'])) {
            $model = new SupportSearchForm("early");
            $model->attributes = $_POST['SupportSearchForm'];
            if ($model->validate()) {
                $model->status_type = 7;
                $model->saveData('early');
                Dialog::message(Yii::t('dialog','Information'), "支援单已提前結束");
                $this->redirect(Yii::app()->createUrl('supportSearch/view',array("index"=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->redirect(Yii::app()->createUrl('supportSearch/view',array("index"=>$model->id)));
            }
        }
    }

    public function actionBack()
    {
        $model = new SupportSearchForm("back");
        if (isset($_POST['SupportSearchForm'])) {
            $model->attributes = $_POST['SupportSearchForm'];
            if ($model->backValidate()) {
                $model->saveData('back');
                Dialog::message(Yii::t('dialog','Information'), Yii::t("contract","finish to send back"));
                $this->redirect(Yii::app()->createUrl('supportAudit/edit',array("index"=>$model->id)));
            } else {
                Dialog::message(Yii::t('dialog','Validation Message'), "支援單不存在，請於管理員聯繫");
                $this->redirect(Yii::app()->createUrl('supportSearch/view',array("index"=>$model->id)));
            }
        }
    }

    //刪除
    public function actionDelete(){
        $model = new SupportSearchForm('delete');
        if (isset($_POST['SupportSearchForm'])) {
            $model->attributes = $_POST['SupportSearchForm'];
            if($model->deleteValidate()){
                $model->deleteData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
                $this->redirect(Yii::app()->createUrl('supportSearch/index'));
            }else{
                Dialog::message(Yii::t('dialog','Information'), "內容不存在");
                $this->render('form',array('model'=>$model));
            }
        }
    }

    public function actionDownExcel()
    {
        if (isset($_POST['SupportSearchForm'])) {
            $model = new SupportSearchForm();
            if (!$model->retrieveData($_POST['SupportSearchForm']['id'])) {
                throw new CHttpException(404,'The requested page does not exist.');
            } else {
                $downExcel = new DownReviewForm();
                $downExcel->setRowExcelToSupport($model);
                $downExcel->outDownExcel("中央技术支援-".$model->employee_name.".xls");
            }
        }
    }
}