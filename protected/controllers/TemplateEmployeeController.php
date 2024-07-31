<?php

/**
 * Created by PhpStorm.
 * User: 考核模板
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class TemplateEmployeeController extends Controller
{
	public $function_id='RE06';

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
                'actions'=>array('edit','save','new','delete'),
                'expression'=>array('TemplateEmployeeController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view'),
                'expression'=>array('TemplateEmployeeController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('RE06');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('RE06');
    }

    public function actionIndex($pageNum=0){
        $model = new TemplateEmployeeList;
        if (isset($_POST['TemplateEmployeeList'])) {
            $model->attributes = $_POST['TemplateEmployeeList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['templateEmployee_01']) && !empty($session['templateEmployee_01'])) {
                $criteria = $session['templateEmployee_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }


    public function actionEdit($index)
    {
        $model = new TemplateEmployeeForm('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionView($index)
    {
        $model = new TemplateEmployeeForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }


    public function actionNew()
    {
        $model = new TemplateEmployeeForm('new');
        $model->city = Yii::app()->user->city_name();
        $this->render('form',array('model'=>$model,));
    }

    public function actionSave()
    {
        if (isset($_POST['TemplateEmployeeForm'])) {
            $model = new TemplateEmployeeForm($_POST['TemplateEmployeeForm']['scenario']);
            $model->attributes = $_POST['TemplateEmployeeForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('templateEmployee/edit',array('index'=>$model->employee_id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }
}