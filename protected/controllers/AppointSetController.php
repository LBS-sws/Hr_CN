<?php

class AppointSetController extends Controller 
{
	public $function_id='ZC18';

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
				'actions'=>array('new','edit','delete','save','copyTripSet'),
				'expression'=>array('AppointSetController','allowReadWrite'),
			),
			array('allow', 
				'actions'=>array('index','view'),
				'expression'=>array('AppointSetController','allowReadOnly'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionIndex($pageNum=0) 
	{
		$model = new AppointSetList;
		if (isset($_POST['AppointSetList'])) {
			$model->attributes = $_POST['AppointSetList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['appointSet_c01']) && !empty($session['appointSet_c01'])) {
				$criteria = $session['appointSet_c01'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}


	public function actionSave()
	{
		if (isset($_POST['AppointSetForm'])) {
			$model = new AppointSetForm($_POST['AppointSetForm']['scenario']);
			$model->attributes = $_POST['AppointSetForm'];
			if ($model->validate()) {
				$model->saveData();
//				$model->scenario = 'edit';
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
				$this->redirect(Yii::app()->createUrl('appointSet/edit',array('index'=>$model->id)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,));
			}
		}
	}

	public function actionView($index)
	{
		$model = new AppointSetForm('view');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}
	
	public function actionNew()
	{
		$model = new AppointSetForm('new');
		$this->render('form',array('model'=>$model,));
	}
	
	public function actionEdit($index)
	{
		$model = new AppointSetForm('edit');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}
	
	public function actionDelete()
	{
		$model = new AppointSetForm('delete');
		if (isset($_POST['AppointSetForm'])) {
			$model->attributes = $_POST['AppointSetForm'];
			if ($model->isOccupied($model->id)) {
				Dialog::message(Yii::t('dialog','Warning'), "该账户存在待审核的加班、请假单，无法删除");
				$this->redirect(Yii::app()->createUrl('appointSet/edit',array('index'=>$model->id)));
			} else {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
                $this->redirect(Yii::app()->createUrl('appointSet/index'));
			}
		}
	}

	public function actionCopyTripSet()
	{
		$model = new AppointTripSetForm('new');
		if (isset($_POST['AppointSetForm'])) {
            $model->setAttrForCopy($_POST['AppointSetForm']);
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), "已复制到出差配置");
                $this->redirect(Yii::app()->createUrl('appointSet/edit',array('index'=>$_POST['AppointSetForm']["id"])));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->redirect(Yii::app()->createUrl('appointSet/edit',array('index'=>$_POST['AppointSetForm']["id"])));
            }
		}
	}
	
	public static function allowReadWrite() {
		return Yii::app()->user->validRWFunction('ZC18');
	}
	
	public static function allowReadOnly() {
		return Yii::app()->user->validFunction('ZC18');
	}
}
