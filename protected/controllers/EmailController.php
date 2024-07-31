<?php

/**
 */
class EmailController extends Controller
{
	public $function_id='RE07';

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
                'actions'=>array('new','edit','draft','delete','save'),
                'expression'=>array('EmailController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view'),
                'expression'=>array('EmailController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('RE07');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('RE07');
    }

    public function actionIndex($pageNum=0){
        $model = new EmailList;
        if (isset($_POST['EmailList'])) {
            $model->attributes = $_POST['EmailList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['email_01']) && !empty($session['email_01'])) {
                $criteria = $session['email_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }


    public function actionEdit($index)
    {
        $model = new EmailForm('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionNew()
    {
        $model = new EmailForm('new');
        $this->render('form',array('model'=>$model,));
    }

    public function actionView($index)
    {
        $model = new EmailForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }


    public function actionSave()
    {
        if (isset($_POST['EmailForm'])) {
            $model = new EmailForm($_POST['EmailForm']['scenario']);
            $model->attributes = $_POST['EmailForm'];
            $model->status_type = 3;
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('contract','Send Email Finish'));
                $this->redirect(Yii::app()->createUrl('email/edit',array('index'=>$model->id)));
            } else {
                $model->status_type = $_POST['EmailForm']["status_type"];
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    public function actionDraft()
    {
        if (isset($_POST['EmailForm'])) {
            $model = new EmailForm($_POST['EmailForm']['scenario']);
            $model->attributes = $_POST['EmailForm'];
            $model->status_type = 4;
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('email/edit',array('index'=>$model->id)));
            } else {
                $model->status_type = $_POST['EmailForm']["status_type"];
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    //刪除
    public function actionDelete(){
        $model = new EmailForm('delete');
        if (isset($_POST['EmailForm'])) {
            $model->attributes = $_POST['EmailForm'];
            if($model->deleteValidate()){
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
                $this->redirect(Yii::app()->createUrl('email/index'));
            }else{
                Dialog::message(Yii::t('dialog','Information'), "內容不存在");
                $this->render('form',array('model'=>$model));
            }
        }
    }
}