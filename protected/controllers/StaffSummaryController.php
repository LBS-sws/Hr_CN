<?php

class StaffSummaryController extends Controller
{
	public $function_id='ZP05';
	
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
				'actions'=>array('index','ajaxDetail'),
				'expression'=>array('StaffSummaryController','allowReadOnly'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionIndex($pageNum=0) 
	{
		$model = new StaffSummaryList();
		if (isset($_POST['StaffSummaryList'])) {
			$model->attributes = $_POST['StaffSummaryList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['staffSummary_c01']) && !empty($session['staffSummary_c01'])) {
				$criteria = $session['staffSummary_c01'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}

	public function actionAjaxDetail(){
        if(Yii::app()->request->isAjaxRequest) {//是否ajax请求
            $model = new StaffSummaryList();
            $html = $model->getStaffTableDetail();
            echo CJSON::encode(array("html"=>$html));
        }else{
            $this->redirect(Yii::app()->createUrl('staffSummary/index'));
        }
	}
	
	public static function allowReadWrite() {
		return Yii::app()->user->validRWFunction('ZP05');
	}
	
	public static function allowReadOnly() {
		return Yii::app()->user->validFunction('ZP05');
	}
}
