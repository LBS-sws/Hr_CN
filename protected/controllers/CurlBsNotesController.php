<?php

class CurlBsNotesController extends Controller
{
	public $function_id='ZC23';
	
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
				'actions'=>array('send','getNow'),
				'expression'=>array('CurlBsNotesController','allowReadWrite'),
			),
			array('allow', 
				'actions'=>array('index','getAjaxStr'),
				'expression'=>array('CurlBsNotesController','allowReadOnly'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionGetAjaxStr()
	{
        if(Yii::app()->request->isAjaxRequest) {//是否ajax请求
            $model = new CurlBsNotesList();
            $id = key_exists("id",$_POST)?$_POST["id"]:0;
            $type = key_exists("type",$_POST)?$_POST["type"]:0;
            $content = $model->getCurlTextForID($id,$type);
            echo CJSON::encode(array("content"=>$content));
        }else{
            $this->redirect(Yii::app()->createUrl('curlBsNotes/index'));
        }
	}

	public function actionIndex($pageNum=0)
	{
		$model = new CurlBsNotesList();
		if (isset($_POST['CurlBsNotesList'])) {
			$model->attributes = $_POST['CurlBsNotesList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['curlBsNotes_c01']) && !empty($session['curlBsNotes_c01'])) {
				$criteria = $session['curlBsNotes_c01'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}

	public function actionSend($index)
	{
        $model = new CurlBsNotesList();
        if($model->sendID($index)){
            Dialog::message(Yii::t('dialog','Information'), "已重新发送");
        }else{
            Dialog::message(Yii::t('dialog','Validation Message'), "数据异常");
        }
        $this->redirect(Yii::app()->createUrl('curlBsNotes/index'));
	}

	public function actionGetNow($startDate="",$endDate="")
	{
        $model = new CurlBsNotesList();
        $interval = 600; // 10分钟的秒数
        $startTime = floor(time() / $interval) * $interval; //起始时间戳
        $endTime = $startTime+$interval; //结束时间戳
        $datetime = new DateTime();
        $startDateEmpty = $datetime->setTimestamp($startTime)->format('Y-m-d\TH:i:s');
        $endDateEmpty = $datetime->setTimestamp($endTime)->format('Y-m-d\TH:i:s');
        $startDate = !empty($startDate)?date_format(date_create($startDate),'Y-m-d\TH:i:s'):$startDateEmpty;
        $endDate = !empty($endDate)?date_format(date_create($endDate),'Y-m-d\TH:i:s'):$endDateEmpty;
        $model->getBsData($startDate,$endDate);
        $this->redirect(Yii::app()->createUrl('curlBsNotes/index'));
	}
	
	public static function allowReadWrite() {
		return Yii::app()->user->validRWFunction('ZC23');
	}
	
	public static function allowReadOnly() {
		return Yii::app()->user->validFunction('ZC23');
	}
}
