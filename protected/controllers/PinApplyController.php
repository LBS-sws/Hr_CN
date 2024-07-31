<?php

/**
 * Created by PhpStorm.
 * User: 城市等級設置
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class PinApplyController extends Controller
{
	public $function_id='PI01';

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
                'expression'=>array('PinApplyController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view'),
                'expression'=>array('PinApplyController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('PI01');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('PI01');
    }

    public function actionIndex($pageNum=0){
        $model = new PinApplyList;
        if (isset($_POST['PinApplyList'])) {
            $model->attributes = $_POST['PinApplyList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['pinApply_01']) && !empty($session['pinApply_01'])) {
                $criteria = $session['pinApply_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }


    public function actionEdit($index)
    {
        $model = new PinApplyForm('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionNew()
    {
        $model = new PinApplyForm('new');
        $this->render('form',array('model'=>$model,));
    }

    public function actionView($index)
    {
        $model = new PinApplyForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }


    public function actionSave()
    {
        if (isset($_POST['PinApplyForm'])) {
            $model = new PinApplyForm($_POST['PinApplyForm']['scenario']);
            $model->attributes = $_POST['PinApplyForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('PinApply/edit',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    //刪除
    public function actionDelete(){
        $model = new PinApplyForm('delete');
        if (isset($_POST['PinApplyForm'])) {
            $model->attributes = $_POST['PinApplyForm'];
            if($model->validate()){
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
                $this->redirect(Yii::app()->createUrl('PinApply/index'));
            }else{
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','This record is already in use'));
                $this->redirect(Yii::app()->createUrl('PinApply/edit',array('index'=>$model->id)));
            }
        }
    }
}