<?php

class TreatyServiceController extends Controller
{
	public $function_id='TH01';
	
	public function filters()
	{
		return array(
			'enforceRegisteredStation',
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
				'actions'=>array('new','edit','delete','save','stop','shift'),
				'expression'=>array('TreatyServiceController','allowReadWrite'),
			),
			array('allow', 
				'actions'=>array('index','view','resetEmail'),
				'expression'=>array('TreatyServiceController','allowReadOnly'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionResetEmail()
	{
		$model = new TreatyInfoForm();
        $model->resetEmailForOld();
		Yii::app()->end();
	}

	public function actionIndex($pageNum=0)
	{
		$model = new TreatyServiceList();
		if (isset($_POST['TreatyServiceList'])) {
			$model->attributes = $_POST['TreatyServiceList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['treatyService_c01']) && !empty($session['treatyService_c01'])) {
				$criteria = $session['treatyService_c01'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}


	public function actionSave()
	{
		if (isset($_POST['TreatyServiceForm'])) {
			$model = new TreatyServiceForm($_POST['TreatyServiceForm']['scenario']);
			$model->attributes = $_POST['TreatyServiceForm'];
			if ($model->validate()) {
				$model->saveData();
				$model->scenario = 'edit';
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
				$this->redirect(Yii::app()->createUrl('treatyService/edit',array('index'=>$model->id)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,));
			}
		}
	}

	public function actionView($index)
	{
		$model = new TreatyServiceForm('view');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}
	
	public function actionNew()
	{
		$model = new TreatyServiceForm('new');
		$this->render('form',array('model'=>$model,));
	}
	
	public function actionEdit($index)
	{
		$model = new TreatyServiceForm('edit');
		if (!$model->retrieveData($index)) {
            $this->redirect(Yii::app()->createUrl('treatyService/index'));
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}
	
	public function actionDelete()
	{
		$model = new TreatyServiceForm('delete');
		if (isset($_POST['TreatyServiceForm'])) {
			$model->attributes = $_POST['TreatyServiceForm'];
			if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
                $this->redirect(Yii::app()->createUrl('treatyService/index'));
			} else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model));
			}
		}
	}

	public function actionStop()
	{
		$model = new TreatyServiceForm('stop');
		if (isset($_POST['TreatyServiceForm'])) {
			$model->attributes = $_POST['TreatyServiceForm'];
			if($model->retrieveData($model->id)){
                $model->stopData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('treaty','Record Stop'));
                $update = Yii::app()->user->validRWFunction('TH02')?"edit":"view";
                $this->redirect(Yii::app()->createUrl('treatyStop/'.$update,array("index"=>$model->id)));
            }
		}
	}

	public function actionShift()
	{
        $model = new TreatyServiceForm('shift');
        if (isset($_POST['TreatyServiceForm'])) {
            $model->attributes = $_POST['TreatyServiceForm'];
            $treaty_lcu = key_exists("treaty_lcu",$_POST)?$_POST["treaty_lcu"]:"";
            if($model->retrieveData($model->id)&&!empty($treaty_lcu)){
                $model->shiftData($treaty_lcu);
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('treatyService/edit',array('index'=>$model->id)));
            }
        }
	}
	
	public static function allowReadWrite() {
		return Yii::app()->user->validRWFunction('TH01');
	}
	
	public static function allowReadOnly() {
		return Yii::app()->user->validFunction('TH01');
	}
}
