<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class PlusCityController extends Controller
{
	public $function_id='ZC13';

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
                'actions'=>array('new','edit','save','delete','ajaxPlusCity'),
                'expression'=>array('PlusCityController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view'),
                'expression'=>array('PlusCityController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('ZC13');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('ZC13');
    }
    public function actionIndex($pageNum=0){
        $model = new PlusCityList();
        if (isset($_POST['PlusCityList'])) {
            $model->attributes = $_POST['PlusCityList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['plusCity_01']) && !empty($session['plusCity_01'])) {
                $criteria = $session['plusCity_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }


    public function actionNew()
    {
        $model = new PlusCityForm('new');
        $this->render('form',array('model'=>$model,));
    }

    public function actionEdit($index)
    {
        $model = new PlusCityForm('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionView($index)
    {
        $model = new PlusCityForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }


    public function actionSave()
    {
        if (isset($_POST['PlusCityForm'])) {
            $model = new PlusCityForm($_POST['PlusCityForm']['scenario']);
            $model->attributes = $_POST['PlusCityForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('plusCity/edit',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    //刪除
    public function actionDelete(){
        $model = new PlusCityForm('delete');
        if (isset($_POST['PlusCityForm'])) {
            $model->attributes = $_POST['PlusCityForm'];
            if($model->validateDelete()){
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
                $this->redirect(Yii::app()->createUrl('plusCity/index'));
            }else{
                Dialog::message(Yii::t('dialog','Information'), Yii::t('contract','error not find'));
                $this->redirect(Yii::app()->createUrl('plusCity/edit',array('index'=>$model->id)));
            }
        }else{
            $this->redirect(Yii::app()->createUrl('plusCity/index'));
        }
    }

    public function actionAjaxPlusCity(){
        if(Yii::app()->request->isAjaxRequest) {//是否ajax请求
            $city = $_POST['city'];
            $department = isset($_POST['department'])?$_POST['department']:'';
            $position = isset($_POST['position'])?$_POST['position']:'';
            $model = new PlusCityForm();
            $rs = $model->getAjaxPlusCity($city,$department,$position);
            echo CJSON::encode(array('status'=>1,'data'=>$rs));//Yii 的方法将数组处理成json数据
        }else{
            $this->redirect(Yii::app()->createUrl('plusCity/index'));
        }
    }
}