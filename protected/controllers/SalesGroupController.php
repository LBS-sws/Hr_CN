<?php

/**
 * Created by PhpStorm. 
 * User: 銷售分組管理
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class SalesGroupController extends Controller
{
	public $function_id='SR01';

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
                'actions'=>array('staff','saveStaff','delStaff','StaffAdd','StaffView','StaffEdit'),
                'expression'=>array('SalesGroupController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view'),
                'expression'=>array('SalesGroupController','allowReadOnly'),
            ),
            array('allow',
                'actions'=>array('save','delete','new','edit'),
                'expression'=>array('SalesGroupController','allowEditOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('SR01');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('SR01');
    }

    public static function allowEditOnly() {
        return Yii::app()->user->validFunction('ZR14');
    }

    public function actionIndex($pageNum=0){
        $model = new SalesGroupList;
        if (isset($_POST['SalesGroupList'])) {
            $model->attributes = $_POST['SalesGroupList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['salesGroup_01']) && !empty($session['salesGroup_01'])) {
                $criteria = $session['salesGroup_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }


    public function actionEdit($index)
    {
        $model = new SalesGroupForm('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionView($index)
    {
        $model = new SalesGroupForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }


    public function actionNew()
    {
        $model = new SalesGroupForm('new');
        $this->render('form',array('model'=>$model,));
    }

    public function actionSave()
    {
        if (isset($_POST['SalesGroupForm'])) {
            $model = new SalesGroupForm($_POST['SalesGroupForm']['scenario']);
            $model->attributes = $_POST['SalesGroupForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('salesGroup/edit',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    public function actionDelete()
    {
        $model = new SalesGroupForm('delete');
        if (isset($_POST['SalesGroupForm'])) {
            $model->attributes = $_POST['SalesGroupForm'];
            if($model->deleteValidate()){
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
                $this->redirect(Yii::app()->createUrl('salesGroup/index'));
            }else{
                $model->scenario = "edit";
                Dialog::message(Yii::t('dialog','Validation Message'), "该项目含有子项目，请先删除子项目");
                $this->render('form',array('model'=>$model));
            }
        }else{
            $this->redirect(Yii::app()->createUrl('salesGroup/index'));
        }
    }

    public function actionStaff($index,$pageNum=0)
    {
        $model = new SalesStaffList;
        if (isset($_POST['SalesStaffList'])) {
            $model->attributes = $_POST['SalesStaffList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['salesGroup_01']) && !empty($session['salesStaff_01'])) {
                $criteria = $session['salesStaff_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($index,$model->pageNum);
        $this->render('staff_index',array('model'=>$model));
    }

    public function actionStaffAdd($index)
    {
        $model = new SalesStaffList('add');
        $model->index = $index;
        $this->render('staff_form',array('model'=>$model));
    }

    public function actionStaffView($index)
    {
        $model = new SalesStaffList('view');
        $model->retrieveForm($index);
        $this->render('staff_form',array('model'=>$model));
    }

    public function actionStaffEdit($index)
    {
        $model = new SalesStaffList('edit');
        $model->retrieveForm($index);
        $this->render('staff_form',array('model'=>$model));
    }

    public function actionSaveStaff()
    {
        if (isset($_POST['SalesStaffList'])) {
            $model = new SalesStaffList($_POST['SalesStaffList']['scenario']);
            $model->attributes = $_POST['SalesStaffList'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('salesGroup/staffEdit',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('staff_form',array('model'=>$model,));
            }
        }
    }

    public function actionDelStaff($index)
    {
        $model = new SalesStaffList("del");
        $model->id = $index;
        if ($model->validate()) {
            $model->saveData();
            Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
            $this->redirect(Yii::app()->createUrl('salesGroup/staff',array('index'=>$model->index)));
        } else {
            $model->setScenario($_POST['SalesStaffList']['scenario']);
            $message = CHtml::errorSummary($model);
            Dialog::message(Yii::t('dialog','Validation Message'), $message);
            $this->render('staff_form',array('model'=>$model));
        }
    }
}