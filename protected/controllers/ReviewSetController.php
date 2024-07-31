<?php

/**
 * Created by PhpStorm.
 * User: 配置考核選項
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class ReviewSetController extends Controller
{
	public $function_id='RE04';

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
                'expression'=>array('ReviewSetController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view'),
                'expression'=>array('ReviewSetController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('RE04');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('RE04');
    }

    public function actionIndex($pageNum=0){
        $model = new ReviewSetList;
        if (isset($_POST['ReviewSetList'])) {
            $model->attributes = $_POST['ReviewSetList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['reviewSet_01']) && !empty($session['reviewSet_01'])) {
                $criteria = $session['reviewSet_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }


    public function actionEdit($index)
    {
        $model = new ReviewSetForm('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionView($index)
    {
        $model = new ReviewSetForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }


    public function actionNew()
    {
        $model = new ReviewSetForm('new');
        $this->render('form',array('model'=>$model,));
    }

    public function actionSave()
    {
        if (isset($_POST['ReviewSetForm'])) {
            $model = new ReviewSetForm($_POST['ReviewSetForm']['scenario']);
            $model->attributes = $_POST['ReviewSetForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('reviewSet/edit',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    public function actionDelete()
    {
        $model = new ReviewSetForm('delete');
        if (isset($_POST['ReviewSetForm'])) {
            $model->attributes = $_POST['ReviewSetForm'];
            if($model->deleteValidate()){
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
                $this->redirect(Yii::app()->createUrl('reviewSet/index'));
            }else{
                $model->scenario = "edit";
                Dialog::message(Yii::t('dialog','Validation Message'), "该项目含有子项目，请先删除子项目");
                $this->render('form',array('model'=>$model));
            }
        }else{
            $this->redirect(Yii::app()->createUrl('reviewSet/index'));
        }
    }
}