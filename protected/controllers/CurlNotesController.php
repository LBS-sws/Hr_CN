<?php

class CurlNotesController extends Controller
{
	public $function_id='ZC20';
	
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
				'actions'=>array('send','employee','binding'),
				'expression'=>array('CurlNotesController','allowReadWrite'),
			),
			array('allow', 
				'actions'=>array('index'),
				'expression'=>array('CurlNotesController','allowReadOnly'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionIndex($pageNum=0) 
	{
		$model = new CurlNotesList();
		if (isset($_POST['CurlNotesList'])) {
			$model->attributes = $_POST['CurlNotesList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['hr_curlNotes_c01']) && !empty($session['hr_curlNotes_c01'])) {
				$criteria = $session['hr_curlNotes_c01'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}

	public function actionSend($index)
	{
        $model = new CurlNotesList();
        if($model->sendID($index)){
            Dialog::message(Yii::t('dialog','Information'), "已重新发送");
        }else{
            Dialog::message(Yii::t('dialog','Validation Message'), "数据异常");
        }
        $this->redirect(Yii::app()->createUrl('curlNotes/index'));
	}

	public function actionEmployee($id,$type="edit")
	{
        $model = new CurlNotesList();
        $model->sendCurlForIDAndType($id,$type,"employee");
        Yii::app()->end();
	}

	public function actionBinding($id,$type="edit")
	{
        $model = new CurlNotesList();
        $model->sendCurlForIDAndType($id,$type,"binding");
        Yii::app()->end();
	}
	
	public static function allowReadWrite() {
		return Yii::app()->user->validRWFunction('ZC20');
	}
	
	public static function allowReadOnly() {
		return Yii::app()->user->validFunction('ZC20');
	}
}
