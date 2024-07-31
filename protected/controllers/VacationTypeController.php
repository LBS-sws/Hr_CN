<?php

/**
 * Created by PhpStorm.
 * User: 請假配置
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class VacationTypeController extends Controller
{
	public $function_id='ZC12';
	
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
                'expression'=>array('VacationTypeController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view'),
                'expression'=>array('VacationTypeController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('ZC12');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('ZC12');
    }

    public function actionIndex($pageNum=0){
        $model = new VacationTypeList;
        if (isset($_POST['VacationTypeList'])) {
            $model->attributes = $_POST['VacationTypeList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['vacationType_01']) && !empty($session['vacationType_01'])) {
                $criteria = $session['vacationType_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }


    public function actionNew()
    {
        $model = new VacationTypeForm('new');
        $this->render('form',array('model'=>$model,));
    }

    public function actionEdit($index)
    {
        $model = new VacationTypeForm('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionView($index)
    {
        $model = new VacationTypeForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }


    public function actionSave()
    {
        if (isset($_POST['VacationTypeForm'])) {
            $model = new VacationTypeForm($_POST['VacationTypeForm']['scenario']);
            $model->attributes = $_POST['VacationTypeForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('vacationType/edit',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    //刪除
    public function actionDelete(){
        $model = new VacationTypeForm('delete');
        if (isset($_POST['VacationTypeForm'])) {
            $model->attributes = $_POST['VacationTypeForm'];
            if($model->validate()){
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
                $this->redirect(Yii::app()->createUrl('vacationType/index'));
            }else{
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','This record is already in use'));
                $this->redirect(Yii::app()->createUrl('vacationType/edit',array('index'=>$model->id)));
            }
        }
    }
}