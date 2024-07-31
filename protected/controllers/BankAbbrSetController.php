<?php

class BankAbbrSetController extends Controller
{
	public $function_id='ZC21';
	
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
                'actions'=>array('new','edit','delete','save'),
                'expression'=>array('BankAbbrSetController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view'),
                'expression'=>array('BankAbbrSetController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('ZC21');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('ZC21');
    }

	public function actionIndex($pageNum=0) 
	{
		$model = new BankAbbrSetList;
		if (isset($_POST['BankAbbrSetList'])) {
			$model->attributes = $_POST['BankAbbrSetList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['bankAbbrSet_ya01']) && !empty($session['bankAbbrSet_ya01'])) {
				$criteria = $session['bankAbbrSet_ya01'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}


	public function actionSave()
	{
		if (isset($_POST['BankAbbrSetForm'])) {
			$model = new BankAbbrSetForm($_POST['BankAbbrSetForm']['scenario']);
			$model->attributes = $_POST['BankAbbrSetForm'];
			if ($model->validate()) {
				$model->saveData();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
				$this->redirect(Yii::app()->createUrl('bankAbbrSet/edit',array('index'=>$model->id)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,));
			}
		}
	}

	public function actionView($index)
	{
		$model = new BankAbbrSetForm('view');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}

    public function actionNew()
    {
        $model = new BankAbbrSetForm('new');
        $this->render('form',array('model'=>$model,));
    }

	public function actionEdit($index)
	{
		$model = new BankAbbrSetForm('edit');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}

    public function actionDelete()
    {
        $model = new BankAbbrSetForm('delete');
        if (isset($_POST['BankAbbrSetForm'])) {
            $model->attributes = $_POST['BankAbbrSetForm'];
            if($model->deleteValidate()){
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
                $this->redirect(Yii::app()->createUrl('bankAbbrSet/index'));
            }else{
                $model->scenario = "edit";
                Dialog::message(Yii::t('dialog','Validation Message'), Yii::t("contract","The reward has staff being used, please delete the staff first"));
                $this->render('form',array('model'=>$model,));
            }
        }else{
            $this->redirect(Yii::app()->createUrl('bankAbbrSet/index'));
        }
    }

}
