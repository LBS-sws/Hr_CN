<?php

/**
 * Created by PhpStorm.
 * User: 老總年度考核
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class BossAuditController extends Controller
{
    public $boss_type=1;//1:總監  2：副總監  3：饒生
	public $function_id='BA03';

    public function init()
    {
        parent::init();
        //$this->function_id = key_exists("type",$_GET)&&$_GET["type"] == 2?"BA05":"BA03";
        if(key_exists("type",$_GET)&&$_GET["type"] == 2){
            $this->function_id = "BA05";
            $this->boss_type = 2;
        }elseif(key_exists("type",$_GET)&&$_GET["type"] == 3){
            $this->function_id = "BA06";
            $this->boss_type = 3;
        }else{
            $this->function_id = "BA03";
            $this->boss_type = 1;
        }
    }

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
                'actions'=>array('edit','reject','audit','finish','save'),
                'expression'=>array('BossAuditController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view'),
                'expression'=>array('BossAuditController','allowReadOnly'),
            ),
            array('allow',
                'actions'=>array('delete'),
                'expression'=>array('BossAuditController','allowDelete'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowDelete() {
        return Yii::app()->user->validFunction('ZR16');
    }

    public static function allowReadWrite() {
        if(key_exists("type",$_GET)&&$_GET["type"] == 2){
            $typeStr = "BA05";
        }elseif(key_exists("type",$_GET)&&$_GET["type"] == 3){
            $typeStr = "BA06";
        }else{
            $typeStr = "BA03";
        }
        return Yii::app()->user->validRWFunction($typeStr);
    }

    public static function allowReadOnly() {
        if(key_exists("type",$_GET)&&$_GET["type"] == 2){
            $typeStr = "BA05";
        }elseif(key_exists("type",$_GET)&&$_GET["type"] == 3){
            $typeStr = "BA06";
        }else{
            $typeStr = "BA03";
        }
        return Yii::app()->user->validFunction($typeStr);
    }

    public function actionIndex($pageNum=0){
        $model = new BossAuditList;
        if (isset($_POST['BossAuditList'])) {
            $model->attributes = $_POST['BossAuditList'];
        } else {
            $session = Yii::app()->session;
            $sessionStr = "bossAudit_0".$this->boss_type;
            if (isset($session[$sessionStr]) && !empty($session[$sessionStr])) {
                $criteria = $session[$sessionStr];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum,$this->boss_type);
        $this->render('index',array('model'=>$model));
    }

    public function actionEdit($index)
    {
        $model = new BossAuditForm('edit');
        $bossType = key_exists("type",$_GET)?$_GET["type"]:1;
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,'bossType'=>$bossType));
        }
    }

    public function actionView($index)
    {
        $model = new BossAuditForm('view');
        $bossType = key_exists("type",$_GET)?$_GET["type"]:1;
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,'bossType'=>$bossType));
        }
    }

    public function actionAudit()
    {
        if (isset($_POST['BossAuditForm'])) {
            $model = new BossAuditForm('audit');
            $model->attributes = $_POST['BossAuditForm'];
            $model->boss_type = $this->boss_type;
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('bossAudit/index',array('type'=>$this->boss_type)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->redirect(Yii::app()->createUrl('bossAudit/edit',array('index'=>$model->id,'type'=>$this->boss_type)));
            }
        }
    }

    public function actionSave()
    {
        if (isset($_POST['BossAuditForm'])) {
            $model = new BossAuditForm('save');
            $model->attributes = $_POST['BossAuditForm'];
            $model->boss_type = $this->boss_type;
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('bossAudit/edit',array('index'=>$model->id,'type'=>$this->boss_type)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->redirect(Yii::app()->createUrl('bossAudit/edit',array('index'=>$model->id,'type'=>$this->boss_type)));
            }
        }
    }

    public function actionFinish()
    {
        if (isset($_POST['BossAuditForm'])) {
            $model = new BossAuditForm('finish');
            $model->attributes = $_POST['BossAuditForm'];
            $model->boss_type = $this->boss_type;
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('bossAudit/index',array('type'=>$this->boss_type)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->redirect(Yii::app()->createUrl('bossAudit/edit',array('index'=>$model->id,'type'=>$this->boss_type)));
            }
        }
    }

    public function actionReject()
    {
        if (isset($_POST['BossAuditForm'])) {
            $model = new BossAuditForm('reject');
            $model->attributes = $_POST['BossAuditForm'];
            $model->boss_type = $this->boss_type;
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Request Denied'));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
            }
            $this->redirect(Yii::app()->createUrl('bossAudit/edit',array('index'=>$model->id,'type'=>$this->boss_type)));
        }
    }

    public function actionDelete()
    {
        if (isset($_POST['BossAuditForm'])) {
            $model = new BossAuditForm('delete');
            $model->attributes = $_POST['BossAuditForm'];
            $model->boss_type = $this->boss_type;
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
            }
            $this->redirect(Yii::app()->createUrl('bossAudit/index',array('type'=>$this->boss_type)));
        }
    }
}