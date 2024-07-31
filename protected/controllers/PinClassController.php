<?php

/**
 * Created by PhpStorm.
 * User: 城市等級設置
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class PinClassController extends Controller
{
	public $function_id='PI03';

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
                'expression'=>array('PinClassController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view'),
                'expression'=>array('PinClassController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('PI03');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('PI03');
    }

    public function actionIndex($pageNum=0){
        $model = new PinClassList;
        if (isset($_POST['PinClassList'])) {
            $model->attributes = $_POST['PinClassList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['pinClass_01']) && !empty($session['pinClass_01'])) {
                $criteria = $session['pinClass_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }


    public function actionEdit($index)
    {
        $model = new PinClassForm('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionNew()
    {
        $model = new PinClassForm('new');
        $this->render('form',array('model'=>$model,));
    }

    public function actionView($index)
    {
        $model = new PinClassForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }


    public function actionSave()
    {
        if (isset($_POST['PinClassForm'])) {
            $model = new PinClassForm($_POST['PinClassForm']['scenario']);
            $model->attributes = $_POST['PinClassForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('PinClass/edit',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    //刪除
    public function actionDelete(){
        $model = new PinClassForm('delete');
        if (isset($_POST['PinClassForm'])) {
            $model->attributes = $_POST['PinClassForm'];
            if($model->validate()){
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
                $this->redirect(Yii::app()->createUrl('PinClass/index'));
            }else{
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','This record is already in use'));
                $this->redirect(Yii::app()->createUrl('PinClass/edit',array('index'=>$model->id)));
            }
        }
    }
}