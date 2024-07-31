<?php

class RecruitApplyController extends Controller
{
	public $function_id='ZP03';
	
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
				'actions'=>array('new','edit','delete','save'),
				'expression'=>array('RecruitApplyController','allowReadWrite'),
			),
			array('allow', 
				'actions'=>array('index','view'),
				'expression'=>array('RecruitApplyController','allowReadOnly'),
			),
			array('allow',
				'actions'=>array('ajaxDetail'),
				'expression'=>array('RecruitApplyController','allowAll'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

    //详情列表的異步請求
    public function actionAjaxDetail(){
        if(Yii::app()->request->isAjaxRequest) {//是否ajax请求
            $model = new RecruitApplyList();
            $arr =$model->ajaxDetailForHtml();
            echo CJSON::encode(array('status'=>1,'html'=>$arr['html'],'title'=>$arr["title"]));//Yii 的方法将数组处理成json数据
        }else{
            $this->redirect(Yii::app()->createUrl('RankingMonth/index'));
        }
    }

	public function actionIndex($pageNum=0) 
	{
		$model = new RecruitApplyList();
		if (isset($_POST['RecruitApplyList'])) {
			$model->attributes = $_POST['RecruitApplyList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['recruitApply_c01']) && !empty($session['recruitApply_c01'])) {
				$criteria = $session['recruitApply_c01'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}


	public function actionSave()
	{
		if (isset($_POST['RecruitApplyForm'])) {
			$model = new RecruitApplyForm($_POST['RecruitApplyForm']['scenario']);
			$model->attributes = $_POST['RecruitApplyForm'];
			if ($model->validate()) {
				$model->saveData();
				$model->scenario = 'edit';
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
				$this->redirect(Yii::app()->createUrl('recruitApply/edit',array('index'=>$model->id)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,));
			}
		}
	}

	public function actionView($index)
	{
		$model = new RecruitApplyForm('view');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}
	
	public function actionNew()
	{
		$model = new RecruitApplyForm('new');
        $model->year = date("Y");
        $model->city = Yii::app()->user->city();
		$this->render('form',array('model'=>$model,));
	}
	
	public function actionEdit($index)
	{
		$model = new RecruitApplyForm('edit');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}
	
	public function actionDelete()
	{
		$model = new RecruitApplyForm('delete');
		if (isset($_POST['RecruitApplyForm'])) {
			$model->attributes = $_POST['RecruitApplyForm'];
			if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
                $this->redirect(Yii::app()->createUrl('recruitApply/index'));
			} else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model));
			}
		}
	}
	
	public static function allowReadWrite() {
		return Yii::app()->user->validRWFunction('ZP03');
	}
	
	public static function allowReadOnly() {
		return Yii::app()->user->validFunction('ZP03');
	}

	public static function allowAll() {
		return Yii::app()->user->validFunction('ZP03')||Yii::app()->user->validFunction('ZP04');
	}
}
