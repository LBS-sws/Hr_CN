<?php

/**
 * Created by PhpStorm.
 * User: 老總年度考核
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class BossKPIController extends Controller
{
	public $function_id='BA04';

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
                'actions'=>array('edit','save','Delete','copy'),
                'expression'=>array('BossKPIController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view'),
                'expression'=>array('BossKPIController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('BA04');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('BA04');
    }

    public function actionIndex($pageNum=0){
        $model = new BossKPIList;
        if (isset($_POST['BossKPIList'])) {
            $model->attributes = $_POST['BossKPIList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['bossKPI_01']) && !empty($session['bossKPI_01'])) {
                $criteria = $session['bossKPI_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }

    public function actionEdit($index)
    {
        $model = new BossKPIForm('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionCopy($index)
    {
        $model = new BossKPIForm('copy');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionView($index)
    {
        $model = new BossKPIForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }


    public function actionSave()
    { //
        if (isset($_POST['BossKPIForm'])) {
            $model = new BossKPIForm($_POST['BossKPIForm']['scenario']);
            $model->attributes = $_POST['BossKPIForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('bossKPI/edit',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    //刪除
    public function actionDelete(){
        $model = new BossKPIForm('delete');
        if (isset($_POST['BossKPIForm'])) {
            $model->attributes = $_POST['BossKPIForm'];
            if($model->deleteValidate()){
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
                $this->redirect(Yii::app()->createUrl('bossKPI/index'));
            }else{
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','This record is already in use'));
                $this->redirect(Yii::app()->createUrl('bossKPI/edit',array('index'=>$model->id)));
            }
        }
    }
}