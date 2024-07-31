<?php

/**
 */
class SupportEmailController extends Controller
{
	public $function_id='AY05';

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
                'actions'=>array('new','edit','delete','save','test'),
                'expression'=>array('SupportEmailController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view'),
                'expression'=>array('SupportEmailController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('AY05');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('AY05');
    }

    public function actionTest($id="",$city="",$pre="AY01",$type=1){
        $emailModel = new Email();
        switch ($type){
            case 1:
                echo "employee_id:{$id}<br/>";
                $emailModel->addSupportPreEmailToEmployeeId($id);
                break;
            case 2:
                echo "city:{$city}  ,pre:{$pre}<br/>";
                $emailModel->addEmailToPrefixAndOnlyCity($pre,$city);
                break;
        }
        echo "email:<br/>";
        var_dump($emailModel->getToAddr());
        echo "<br/>user:<br/>";
        var_dump($emailModel->getToUser());
    }

    public function actionIndex($pageNum=0){
        $model = new SupportEmailList;
        if (isset($_POST['SupportEmailList'])) {
            $model->attributes = $_POST['SupportEmailList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['supportEmail_01']) && !empty($session['supportEmail_01'])) {
                $criteria = $session['supportEmail_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }


    public function actionEdit($index)
    {
        $model = new SupportEmailForm('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionNew()
    {
        $model = new SupportEmailForm('new');
        $model->apply_city = Yii::app()->user->city;
        $this->render('form',array('model'=>$model,));
    }

    public function actionView($index)
    {
        $model = new SupportEmailForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }


    public function actionSave()
    {
        if (isset($_POST['SupportEmailForm'])) {
            $model = new SupportEmailForm($_POST['SupportEmailForm']['scenario']);
            $model->attributes = $_POST['SupportEmailForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('supportEmail/edit',array('index'=>$model->employee_id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    //刪除
    public function actionDelete(){
        $model = new SupportEmailForm('delete');
        if (isset($_POST['SupportEmailForm'])) {
            $model->attributes = $_POST['SupportEmailForm'];
            if($model->deleteValidate()){
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
                $this->redirect(Yii::app()->createUrl('supportEmail/index'));
            }else{
                Dialog::message(Yii::t('dialog','Information'), "內容不存在");
                $this->render('form',array('model'=>$model));
            }
        }
    }
}