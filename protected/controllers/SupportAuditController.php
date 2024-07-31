<?php

/**
 */
class SupportAuditController extends Controller
{
	public $function_id='AY02';

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
                'actions'=>array('new','edit','support','delete','save','audit','wait','undo','finish','reject','early','renewal','ajaxChangeCity','endReply','batchFinish'),
                'expression'=>array('SupportAuditController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view'),
                'expression'=>array('SupportAuditController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('AY02');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('AY02');
    }

    public function actionIndex($pageNum=0){
        $model = new SupportAuditList;
        if (isset($_POST['SupportAuditList'])) {
            $model->attributes = $_POST['SupportAuditList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['supportAudit_01']) && !empty($session['supportAudit_01'])) {
                $criteria = $session['supportAudit_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }


    public function actionEdit($index)
    {
        $model = new SupportAuditForm('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionNew()
    {
        $model = new SupportAuditForm('new');
        $this->render('form',array('model'=>$model,));
    }

    public function actionView($index)
    {
        $model = new SupportAuditForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }


    public function actionSave()
    {
        if (isset($_POST['SupportAuditForm'])) {
            $model = new SupportAuditForm("save");
            $model->attributes = $_POST['SupportAuditForm'];
            $model->status_type = 3;
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('supportAudit/edit',array('index'=>$model->id)));
            } else {
                $model->status_type = $_POST['SupportAuditForm']["status_type"];
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }
    public function actionBatchFinish()
    {
        if (isset($_POST['checkBox'])) {
            $model = new SupportAuditForm();
            $idList = $_POST['checkBox'];
            if ($model->validateBatchFinish($idList)) {
                $model->batchFinish();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('supportAudit/index'));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->redirect(Yii::app()->createUrl('supportAudit/index'));
            }
        }else{
            Dialog::message(Yii::t('dialog','Validation Message'), Yii::t("contract","Please select the support list you want to complete"));
            $this->redirect(Yii::app()->createUrl('supportAudit/index'));
        }
    }


    public function actionEndReply()
    {
        if (isset($_POST['SupportAuditForm'])) {
            $model = new SupportAuditForm("endReply");
            $model->attributes = $_POST['SupportAuditForm'];
            $model->status_type = 12;
            if ($model->validate()) {
                $model->saveData('endReply');
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('supportAudit/edit',array('index'=>$model->id)));
            } else {
                $model->status_type = $_POST['SupportAuditForm']["status_type"];
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }


    public function actionWait()
    {
        if (isset($_POST['SupportAuditForm'])) {
            $model = new SupportAuditForm("wait");
            $model->attributes = $_POST['SupportAuditForm'];
            $model->status_type = 4;
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('supportAudit/edit',array('index'=>$model->id)));
            } else {
                $model->status_type = $_POST['SupportAuditForm']["status_type"];
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    public function actionUndo()
    {
        if (isset($_POST['SupportAuditForm'])) {
            $model = new SupportAuditForm("undo");
            $model->attributes = $_POST['SupportAuditForm'];
            if ($model->validate()) {
                $model->saveData('undo');
                Dialog::message(Yii::t('dialog','Information'), "支援单已撤回待评分状态");
                $this->redirect(Yii::app()->createUrl('supportAudit/index'));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    public function actionFinish()
    {
        if (isset($_POST['SupportAuditForm'])) {
            $model = new SupportAuditForm("finish");
            $model->attributes = $_POST['SupportAuditForm'];
            if ($model->validate()) {
                $model->status_type = 7;
                $model->saveData('finish');
                Dialog::message(Yii::t('dialog','Information'), "支援单已完成");
                $this->redirect(Yii::app()->createUrl('supportAudit/index'));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    public function actionEarly()
    {
        if (isset($_POST['SupportAuditForm'])) {
            $model = new SupportAuditForm("early");
            $model->attributes = $_POST['SupportAuditForm'];
            if ($model->validate()) {
                $model->status_type = 7;
                $model->saveData('early');
                Dialog::message(Yii::t('dialog','Information'), "支援单已提前結束");
                $this->redirect(Yii::app()->createUrl('supportAudit/index'));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    public function actionRenewal()
    {
        if (isset($_POST['SupportAuditForm'])) {
            $model = new SupportAuditForm("renewal");
            $model->attributes = $_POST['SupportAuditForm'];
            if ($model->validate()) {
                $model->status_type = 5;
                $model->saveData('renewal');
                Dialog::message(Yii::t('dialog','Information'), "支援单续期成功");
                $this->redirect(Yii::app()->createUrl('supportAudit/index'));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    public function actionReject()
    {
        if (isset($_POST['SupportAuditForm'])) {
            $model = new SupportAuditForm("reject");
            $model->attributes = $_POST['SupportAuditForm'];
            if ($model->validate()) {
                $model->saveData('reject');
                Dialog::message(Yii::t('dialog','Information'), "支援单已拒绝提前結束");
                $this->redirect(Yii::app()->createUrl('supportAudit/index'));
            } else {
                $model->status_type = $_POST['SupportAuditForm']["status_type"];
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    public function actionAudit()
    {
        if (isset($_POST['SupportAuditForm'])) {
            $model = new SupportAuditForm("audit");
            $model->attributes = $_POST['SupportAuditForm'];
            $model->status_type = 5;
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('contract','Finish approval'));
                $this->redirect(Yii::app()->createUrl('supportAudit/index'));
            } else {
                $model->status_type = $_POST['SupportAuditForm']["status_type"];
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    public function actionSupport()
    {
        if (isset($_POST['SupportAuditForm'])) {
            $model = new SupportAuditForm("support");
            $model->attributes = $_POST['SupportAuditForm'];
            $model->status_type = 5;
            if ($model->validate()) {
                $model->saveData('new');
                Dialog::message(Yii::t('dialog','Information'), Yii::t('contract','audit support finish'));
                $this->redirect(Yii::app()->createUrl('supportAudit/index'));
            } else {
                $model->status_type = $_POST['SupportAuditForm']["status_type"];
                $model->setScenario("new");
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    //刪除
    public function actionDelete(){
        $model = new SupportAuditForm('delete');
        if (isset($_POST['SupportAuditForm'])) {
            $model->attributes = $_POST['SupportAuditForm'];
            if($model->deleteValidate()){
                $model->saveData('delete');
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
                $this->redirect(Yii::app()->createUrl('supportAudit/index'));
            }else{
                Dialog::message(Yii::t('dialog','Information'), "內容不存在");
                $this->render('form',array('model'=>$model));
            }
        }
    }

    //时间计算
    public function actionAjaxChangeCity(){
        if(Yii::app()->request->isAjaxRequest) {//是否ajax请求
            $city = key_exists("city",$_POST)?$_POST['city']:0;
            $arr = SupportApplyForm::getPrivilegeUserList($city);
            $html ='';
            foreach ($arr as $key =>$item){
                $html.="<option value='$key'>".$item."</option>";
            }
            echo CJSON::encode(array("status"=>1,"html"=>$html));
        }else{
            $this->redirect(Yii::app()->createUrl(''));
        }
    }
}