<?php

class TreatyStopController extends Controller
{
	public $function_id='TH02';
	
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
				'actions'=>array('edit','black'),
				'expression'=>array('TreatyStopController','allowReadWrite'),
			),
			array('allow', 
				'actions'=>array('index','view'),
				'expression'=>array('TreatyStopController','allowReadOnly'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionIndex($pageNum=0) 
	{
		$model = new TreatyStopList();
		if (isset($_POST['TreatyStopList'])) {
			$model->attributes = $_POST['TreatyStopList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['treatyStop_c01']) && !empty($session['treatyStop_c01'])) {
				$criteria = $session['treatyStop_c01'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}

	public function actionView($index)
	{
		$model = new TreatyStopForm('view');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}
	
	public function actionEdit($index)
	{
		$model = new TreatyStopForm('edit');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}

	public function actionBlack()
	{
		$model = new TreatyStopForm('stop');
		if (isset($_POST['TreatyStopForm'])) {
			$model->attributes = $_POST['TreatyStopForm'];
			if($model->retrieveData($model->id)){
                $model->blackData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('treaty','Record Black'));
                $update = Yii::app()->user->validRWFunction('TH01')?"edit":"view";
                $this->redirect(Yii::app()->createUrl('treatyService/'.$update,array("index"=>$model->id)));
            }
		}
	}
	
	public static function allowReadWrite() {
		return Yii::app()->user->validRWFunction('TH02');
	}
	
	public static function allowReadOnly() {
		return Yii::app()->user->validFunction('TH02');
	}
}
