<?php

/**
 * Created by PhpStorm.
 * User: 請假配置
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class VacationController extends Controller
{
	public $function_id='ZC04';
	
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
                'actions'=>array('new','edit','delete','save'),
                'expression'=>array('VacationController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view'),
                'expression'=>array('VacationController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('ZC04');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('ZC04');
    }

    public function actionIndex($pageNum=0){
        $model = new VacationList;
        if (isset($_POST['VacationList'])) {
            $model->attributes = $_POST['VacationList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['vacation_01']) && !empty($session['vacation_01'])) {
                $criteria = $session['vacation_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }


    public function actionNew($index=0)
    {
        $model = new VacationForm('new');
        if(!empty($index)){
            $model->retrieveData($index);
            $model->id = null;
        }
        $this->render('form',array('model'=>$model,));
    }

    public function actionEdit($index)
    {
        $model = new VacationForm('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionView($index)
    {
        $model = new VacationForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }


    public function actionSave()
    {
        if (isset($_POST['VacationForm'])) {
            $model = new VacationForm($_POST['VacationForm']['scenario']);
            $model->attributes = $_POST['VacationForm'];
            if ($model->validate()||!$model->rwFunction()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('vacation/edit',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    //刪除
    public function actionDelete(){
        $model = new VacationForm('delete');
        if (isset($_POST['VacationForm'])) {
            $model->attributes = $_POST['VacationForm'];
            if($model->validate()){
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
                $this->redirect(Yii::app()->createUrl('vacation/index'));
            }else{
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','This record is already in use'));
                $this->redirect(Yii::app()->createUrl('vacation/edit',array('index'=>$model->id)));
            }
        }
    }
}