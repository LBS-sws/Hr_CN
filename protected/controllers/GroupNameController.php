<?php

class GroupNameController extends Controller
{
	public $function_id='ZC24';
	
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
				'actions'=>array('new','edit','delete','save','saveStaff','delStaff','StaffAdd','StaffCopy','StaffEdit'),
				'expression'=>array('GroupNameController','allowReadWrite'),
			),
			array('allow', 
				'actions'=>array('index','view','staff','StaffView'),
				'expression'=>array('GroupNameController','allowReadOnly'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionIndex($pageNum=0) 
	{
		$model = new GroupNameList();
		if (isset($_POST['GroupNameList'])) {
			$model->attributes = $_POST['GroupNameList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['groupName_c01']) && !empty($session['groupName_c01'])) {
				$criteria = $session['groupName_c01'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}


	public function actionSave()
	{
		if (isset($_POST['GroupNameForm'])) {
			$model = new GroupNameForm($_POST['GroupNameForm']['scenario']);
			$model->attributes = $_POST['GroupNameForm'];
			if ($model->validate()) {
				$model->saveData();
				$model->scenario = 'edit';
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
				$this->redirect(Yii::app()->createUrl('groupName/edit',array('index'=>$model->id)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,));
			}
		}
	}

	public function actionView($index)
	{
		$model = new GroupNameForm('view');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}
	
	public function actionNew()
	{
		$model = new GroupNameForm('new');
		$this->render('form',array('model'=>$model,));
	}
	
	public function actionEdit($index)
	{
		$model = new GroupNameForm('edit');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}
	
	public function actionDelete()
	{
		$model = new GroupNameForm('delete');
		if (isset($_POST['GroupNameForm'])) {
			$model->attributes = $_POST['GroupNameForm'];
			if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
                $this->redirect(Yii::app()->createUrl('groupName/index'));
			} else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model));
			}
		}
	}
	
	public static function allowReadWrite() {
		return Yii::app()->user->validRWFunction('ZC24');
	}
	
	public static function allowReadOnly() {
		return Yii::app()->user->validFunction('ZC24');
	}

    public function actionStaff($index,$pageNum=0)
    {
        $model = new GroupStaffList;
        if (isset($_POST['GroupStaffList'])) {
            $model->attributes = $_POST['GroupStaffList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['groupStaff_01']) && !empty($session['groupStaff_01'])) {
                $criteria = $session['groupStaff_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($index,$model->pageNum);
        $this->render('staff_index',array('model'=>$model));
    }

    public function actionStaffAdd($index)
    {
        $model = new GroupStaffForm('new');
        $model->group_id = $index;
        $this->render('staff_form',array('model'=>$model));
    }

    public function actionStaffCopy($index)
    {
        $model = new GroupStaffForm('new');
        $model->retrieveCopy($index);
        $this->render('staff_form',array('model'=>$model));
    }

    public function actionStaffView($index)
    {
        $model = new GroupStaffForm('view');
        $model->retrieveData($index);
        $this->render('staff_form',array('model'=>$model));
    }

    public function actionStaffEdit($index)
    {
        $model = new GroupStaffForm('edit');
        $model->retrieveData($index);
        $this->render('staff_form',array('model'=>$model));
    }

    public function actionSaveStaff()
    {
        if (isset($_POST['GroupStaffForm'])) {
            $model = new GroupStaffForm($_POST['GroupStaffForm']['scenario']);
            $model->attributes = $_POST['GroupStaffForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('groupName/staffEdit',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('staff_form',array('model'=>$model,));
            }
        }
    }

    public function actionDelStaff()
    {
        $model = new GroupStaffForm('delete');
        if (isset($_POST['GroupStaffForm'])) {
            $model->attributes = $_POST['GroupStaffForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
                $this->redirect(Yii::app()->createUrl('groupName/staff',array("index"=>$model->group_id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model));
            }
        }
    }
}
