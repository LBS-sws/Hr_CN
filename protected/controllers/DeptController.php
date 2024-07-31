<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class DeptController extends Controller
{

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
                'actions'=>array('edit','new','save','delete','copy','copyCity'),
                'expression'=>array('DeptController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view'),
                'expression'=>array('DeptController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        if(array_key_exists("type",$_GET) && $_GET["type"] == 1){
            return Yii::app()->user->validRWFunction('ZC02');
        }else{
            return Yii::app()->user->validRWFunction('ZC01');
        }
    }

    public static function allowReadOnly() {
        if(array_key_exists("type",$_GET) && $_GET["type"] == 1){
            return Yii::app()->user->validFunction('ZC02');
        }else{
            return Yii::app()->user->validFunction('ZC01');
        }
    }

    public function actionCopyCity($toCity,$formCity="ZH")
    {
        $model = new DeptForm('new');
        $arr = $model->copyCity($toCity,$formCity);
        var_dump($arr);
        Yii::app()->end();
    }

    public function actionIndex($pageNum=0,$type=0){
		$this->function_id = $type==0 ? 'ZC01' : 'ZC02';
		Yii::app()->session['active_func'] = $this->function_id;
        $model = new DeptList;
        $model->type=$type;
        if (isset($_POST['DeptList'])) {
            $model->attributes = $_POST['DeptList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['dept_01']) && !empty($session['dept_01'])) {
                $criteria = $session['dept_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }


    public function actionNew($type=0)
    {
		$this->function_id = $type==0 ? 'ZC01' : 'ZC02';
		Yii::app()->session['active_func'] = $this->function_id;
        $model = new DeptForm('new');
        $model->type = $type;
        $this->render('form',array('model'=>$model,));
    }


    public function actionCopy($index=0)
    {
        $model = new DeptForm('new');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->function_id = $model->type==0 ? 'ZC01' : 'ZC02';
            Yii::app()->session['active_func'] = $this->function_id;
            $model->id=null;
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionEdit($index)
    {
        $model = new DeptForm('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
			$this->function_id = $model->type==0 ? 'ZC01' : 'ZC02';
			Yii::app()->session['active_func'] = $this->function_id;
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionView($index)
    {
        $model = new DeptForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
			$this->function_id = $model->type==0 ? 'ZC01' : 'ZC02';
			Yii::app()->session['active_func'] = $this->function_id;
            $this->render('form',array('model'=>$model,));
        }
    }


    public function actionSave()
    {
        if (isset($_POST['DeptForm'])) {
            $model = new DeptForm($_POST['DeptForm']['scenario']);
            $model->attributes = $_POST['DeptForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('dept/edit',array('index'=>$model->id,'type'=>$model->type)));
            } else {
                $this->function_id = $model->type==0 ? 'ZC01' : 'ZC02';
                Yii::app()->session['active_func'] = $this->function_id;
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }


    //刪除職位
    public function actionDelete(){
        $model = new DeptForm('delete');
        if (isset($_POST['DeptForm'])) {
            $model->attributes = $_POST['DeptForm'];
            if($model->validateDelete()){
                $model->saveData();
                $this->redirect(Yii::app()->createUrl('dept/index',array("type"=>$model->type)));
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
            }else{
                Dialog::message(Yii::t('dialog','Information'), Yii::t('contract','The dept has staff being used, please delete the staff first'));
                $this->redirect(Yii::app()->createUrl('dept/edit',array('index'=>$model->id,'type'=>$model->type)));
            }
        }
    }
}