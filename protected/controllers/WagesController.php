<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class WagesController extends Controller
{

	public $function_id='ZA03';
	
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
                'actions'=>array('new','edit','delete','save','ajaxGetWageType','wagesTypeDelete'),
                'expression'=>array('WagesController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view'),
                'expression'=>array('WagesController','allowReadOnly'),
            ),
            array('allow',
                'actions'=>array('downFinish'),
                'expression'=>array('WagesController','allowWrite'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('ZA03');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('ZA03');
    }
    public static function allowWrite() {
        return !empty(Yii::app()->user->id);
    }

    public function actionIndex($pageNum=0){
        $model = new WagesList;
        if (isset($_POST['WagesList'])) {
            $model->attributes = $_POST['WagesList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['wages_01']) && !empty($session['wages_01'])) {
                $criteria = $session['wages_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }


    public function actionNew()
    {
        $model = new WagesForm('new');
        $this->render('form',array('model'=>$model,));
    }

    public function actionEdit($index)
    {
        $model = new WagesForm('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionView($index)
    {
        $model = new WagesForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }


    public function actionSave()
    {
        if (isset($_POST['WagesForm'])) {
            $model = new WagesForm($_POST['WagesForm']['scenario']);
            $model->attributes = $_POST['WagesForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('wages/edit',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    //刪除工資單下的某個屬性
    public function actionWagesTypeDelete(){
        if(Yii::app()->request->isAjaxRequest) {//是否ajax请求
            $id = $_POST['id'];
            $rs = WagesForm::delWagesConfigToWagesId($id);
            if($rs){
                echo CJSON::encode(array('status'=>1));//Yii 的方法将数组处理成json数据
            }else{
                echo CJSON::encode(array('status'=>0));
            }
        }else{
            $this->redirect(Yii::app()->createUrl('wages/index'));
        }
    }

    //刪除工資單
    public function actionDelete(){
        $model = new WagesForm('delete');
        if (isset($_POST['WagesForm'])) {
            $model->attributes = $_POST['WagesForm'];
            if($model->validate()){
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
                $this->redirect(Yii::app()->createUrl('wages/index'));
            }else{
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','This record is already in use'));
                $this->redirect(Yii::app()->createUrl('wages/edit',array('index'=>$model->id)));
            }
        }
    }
/*
    //下載工資單列表
    public function actionDown(){
        $model = new EmployeeDown();
        $model->getEmployeeAll();
        $this->render('down',array('model'=>$model));
    }*/

    //下載工資單列表(下載)
    public function actionDownFinish(){
        if (isset($_POST['EmployeeDown'])) {
            $model = new EmployeeDown();
            $model->attributes = $_POST['EmployeeDown'];
            if ($model->validate()) {
                $model->downExcel();
            } else {
                $message = CHtml::errorSummary($model);
                $model->getEmployeeAll();
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('down',array('model'=>$model,));
            }
        }
    }

    //根據id獲取工資單
    public function actionAjaxGetWageType(){
        if(Yii::app()->request->isAjaxRequest) {//是否ajax请求
            $id = $_POST['id'];
            $rs = WagesForm::getWagesTypeList($id);
            if (empty($rs)){
                echo CJSON::encode(array('status'=>0,'error'=>Yii::t("dialog",'No Record Found')));//Yii 的方法将数组处理成json数据
            }else{
                echo CJSON::encode(array('status'=>1,'data'=>$rs));//Yii 的方法将数组处理成json数据
            }
        }else{
            $this->redirect(Yii::app()->createUrl('wages/index'));
        }
    }
}