<?php

/**
 */
class SupportApplyController extends Controller
{
	public $function_id='AY01';

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
                'actions'=>array('new','edit','draft','delete','save','saveDraft','review','early'),//'renewal'續期功能不需要
                'expression'=>array('SupportApplyController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view'),
                'expression'=>array('SupportApplyController','allowReadOnly'),
            ),
            array('allow',
                'actions'=>array('ajaxEndDate'),
                'expression'=>array('SupportApplyController','allow'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('AY01');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('AY01');
    }

    public static function allow() {
        return true;
    }

    public function actionIndex($pageNum=0){
        $model = new SupportApplyList;
        if (isset($_POST['SupportApplyList'])) {
            $model->attributes = $_POST['SupportApplyList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['supportApply_01']) && !empty($session['supportApply_01'])) {
                $criteria = $session['supportApply_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }


    public function actionEdit($index)
    {
        $model = new SupportApplyForm('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionNew()
    {
        $model = new SupportApplyForm('new');
        $model->apply_city = Yii::app()->user->city;
        $this->render('form',array('model'=>$model,));
    }

    public function actionView($index)
    {
        $model = new SupportApplyForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }


    public function actionSave()
    {
        if (isset($_POST['SupportApplyForm'])) {
            $model = new SupportApplyForm($_POST['SupportApplyForm']['scenario']);
            $model->attributes = $_POST['SupportApplyForm'];
            $model->status_type = 2;
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('supportApply/edit',array('index'=>$model->id)));
            } else {
                $model->status_type = $_POST['SupportApplyForm']["status_type"];
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    public function actionDraft()
    {
        if (isset($_POST['SupportApplyForm'])) {
            $model = new SupportApplyForm($_POST['SupportApplyForm']['scenario']);
            $model->attributes = $_POST['SupportApplyForm'];
            $model->status_type = 1;
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('supportApply/edit',array('index'=>$model->id)));
            } else {
                $model->status_type = $_POST['SupportApplyForm']["status_type"];
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    public function actionReview()
    {
        if (isset($_POST['SupportApplyForm'])) {
            $model = new SupportApplyForm("review");
            $model->attributes = $_POST['SupportApplyForm'];
            $model->status_type = 6;
            if ($model->validate()) {
                $model->saveData();
                $model->setScenario($_POST['SupportApplyForm']['scenario']);
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('supportApply/edit',array('index'=>$model->id)));
            } else {
                $model->status_type = $_POST['SupportApplyForm']["status_type"];
                $model->setScenario($_POST['SupportApplyForm']['scenario']);
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    public function actionSaveDraft()
    {
        if (isset($_POST['SupportApplyForm'])) {
            $model = new SupportApplyForm("review");
            $model->attributes = $_POST['SupportApplyForm'];
            $model->status_type = 14;
            if ($model->validate()) {
                $model->saveData();
                $model->setScenario($_POST['SupportApplyForm']['scenario']);
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('supportApply/edit',array('index'=>$model->id)));
            } else {
                $model->status_type = $_POST['SupportApplyForm']["status_type"];
                $model->setScenario($_POST['SupportApplyForm']['scenario']);
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    public function actionEarly()
    {
        if (isset($_POST['SupportApplyForm'])) {
            $model = new SupportApplyForm("early");
            $model->attributes = $_POST['SupportApplyForm'];
            if ($model->validate()) {
                $model->status_type = 9;
                $model->saveData();
                $model->setScenario($_POST['SupportApplyForm']['scenario']);
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('supportApply/edit',array('index'=>$model->id)));
            } else {
                $model->setScenario($_POST['SupportApplyForm']['scenario']);
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    public function actionRenewal()
    {
        if (isset($_POST['SupportApplyForm'])) {
            $model = new SupportApplyForm("renewal");
            $model->attributes = $_POST['SupportApplyForm'];
            if ($model->validate()) {
                $model->status_type = 10;
                $model->saveData();
                $model->setScenario($_POST['SupportApplyForm']['scenario']);
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('supportApply/edit',array('index'=>$model->id)));
            } else {
                $model->setScenario($_POST['SupportApplyForm']['scenario']);
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    //刪除
    public function actionDelete(){
        $model = new SupportApplyForm('delete');
        if (isset($_POST['SupportApplyForm'])) {
            $model->attributes = $_POST['SupportApplyForm'];
            if($model->deleteValidate()){
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
                $this->redirect(Yii::app()->createUrl('supportApply/index'));
            }else{
                Dialog::message(Yii::t('dialog','Information'), "內容不存在");
                $this->render('form',array('model'=>$model));
            }
        }
    }

    //时间计算
    public function actionAjaxEndDate(){
        if(Yii::app()->request->isAjaxRequest) {//是否ajax请求
            $model = new SupportAuditForm();
            $support = isset($_POST['support'])?$_POST['support']:0;
            $privilege = isset($_POST['privilege'])?$_POST['privilege']:0;
            $apply_date = $_POST['apply_date'];
            $apply_length = $_POST['apply_length'];
            $length_type = $_POST['length_type'] == 1?"month":"day";
            $endDate = date("Y/m/d",strtotime("$apply_date + $apply_length $length_type"));
            $endDate = date("Y/m/d",strtotime("$endDate - 1 day"));
            if($privilege == 2){//后续添加，優先權的最後日期只能是本月的最後一天
                $date = date("Y/m/01",strtotime($apply_date));
                $endDate = date("Y/m/d",strtotime("$date + 1 month - 1 day"));
            }
            $html = '';
            $model->apply_date = $apply_date;
            $model->apply_end_date = $endDate;
            if(!empty($support)&&Yii::app()->user->validFunction('AY02')){
                $data = $model->getSupportEmployee();
                foreach ($data as $key=>$item){
                    $html.="<option value='$key'>$item</option>";
                }
            }
            echo CJSON::encode(array("status"=>1,"endDate"=>$endDate,"html"=>$html));
        }else{
            $this->redirect(Yii::app()->createUrl(''));
        }
    }
}