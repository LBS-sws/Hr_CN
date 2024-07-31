<?php

class HouseSearchController extends Controller
{
	public $function_id='ZE10';

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
/*		
			array('allow', 
				'actions'=>array('new','edit','delete','save'),
				'expression'=>array('CustomerController','allowReadWrite'),
			),
*/
			array('allow', 
				'actions'=>array('index'),
				'expression'=>array('HouseSearchController','allowReadOnly'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionIndex($pageNum=1,$show=1) 
	{
		$model = new HouseSearchList;
		if (isset($_POST['HouseSearchList'])) {
			$model->attributes = $_POST['HouseSearchList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session[$model->criteriaName()]) && !empty($session[$model->criteriaName()])) {
				$criteria = $session[$model->criteriaName()];
				$model->setCriteria($criteria);
			}
		}
		$model->show = $show;
		if ($show!=0) {
			$model->determinePageNum($pageNum);
			$model->retrieveDataByPage($model->pageNum);
		}
		$this->render('index',array('model'=>$model));
	}

	public static function allowReadWrite() {
		return Yii::app()->user->validRWFunction('ZE10');
	}
	
	public static function allowReadOnly() {
		return Yii::app()->user->validFunction('ZE10');
	}
}
