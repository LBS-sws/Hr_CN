<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class OldContractController extends Controller
{
	public $function_id='ZG09';

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
                'actions'=>array('checked','edit','unsigned','recall','delete','have'),
                'expression'=>array('OldContractController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view'),
                'expression'=>array('OldContractController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }
    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('ZG09');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('ZG09');
    }

    public function actionIndex($pageNum=0){
        $model = new OldContractList;
        if (isset($_POST['OldContractList'])) {
            $model->attributes = $_POST['OldContractList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['oldContract_01']) && !empty($session['oldContract_01'])) {
                $criteria = $session['oldContract_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }


    public function actionChecked()
    {
        if (isset($_POST['OldContractForm'])) {
            $model = new OldContractForm($_POST['OldContractForm']['scenario']);
            $model->attributes = $_POST['OldContractForm'];
            if ($model->validate()) {
                $model->saveStaff();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('OldContract/edit',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->redirect(Yii::app()->createUrl('OldContract/edit',array('index'=>$model->id)));
            }
        }
    }

    public function actionDelete()
    {
        if (isset($_POST['OldContractForm'])) {
            $model = new OldContractForm($_POST['OldContractForm']['scenario']);
            $model->attributes = $_POST['OldContractForm'];
            if ($model->validate()) {
                $model->old_type = 3;
                $model->saveStaff();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
                $this->redirect(Yii::app()->createUrl('OldContract/index'));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->redirect(Yii::app()->createUrl('OldContract/edit',array('index'=>$model->id)));
            }
        }
    }

    public function actionEdit($index)
    {
        $model = new OldContractForm('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionView($index)
    {
        $model = new OldContractForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionUnsigned($index)
    {
        if (isset($_POST['OldContractForm'])) {
            $model = new OldContractForm($_POST['OldContractForm']['scenario']);
            $model->attributes = $_POST['OldContractForm'];
            if ($model->validateUnsigned($index)) {
                $model->saveUnsigned();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('OldContract/edit',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->redirect(Yii::app()->createUrl('OldContract/edit',array('index'=>$model->id)));
            }
        }
    }

    public function actionHave($index)
    {
        if (isset($_POST['OldContractForm'])) {
            $model = new OldContractForm($_POST['OldContractForm']['scenario']);
            $model->attributes = $_POST['OldContractForm'];
            if ($model->validateUnsigned($index)) {
                $model->saveHasContract();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('OldContract/edit',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->redirect(Yii::app()->createUrl('OldContract/edit',array('index'=>$model->id)));
            }
        }
    }

    public function actionRecall($index)
    {
        if (isset($_POST['OldContractForm'])) {
            $model = new OldContractForm($_POST['OldContractForm']['scenario']);
            $model->attributes = $_POST['OldContractForm'];
            if ($model->validateRecall($index)) {
                $model->saveRecall();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('OldContract/edit',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->redirect(Yii::app()->createUrl('OldContract/edit',array('index'=>$model->id)));
            }
        }
    }

}