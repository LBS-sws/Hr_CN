<?php

class ConfigOfficeController extends Controller
{
	public $function_id='ZC15';
	
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
                'expression'=>array('ConfigOfficeController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view','ajaxOffice'),
                'expression'=>array('ConfigOfficeController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('ZC15');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('ZC15');
    }

	public function actionIndex($pageNum=0) 
	{
		$model = new ConfigOfficeList;
		if (isset($_POST['ConfigOfficeList'])) {
			$model->attributes = $_POST['ConfigOfficeList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['configOffice_ya01']) && !empty($session['configOffice_ya01'])) {
				$criteria = $session['configOffice_ya01'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}


	public function actionSave()
	{
		if (isset($_POST['ConfigOfficeForm'])) {
			$model = new ConfigOfficeForm($_POST['ConfigOfficeForm']['scenario']);
			$model->attributes = $_POST['ConfigOfficeForm'];
			if ($model->validate()) {
				$model->saveData();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
				$this->redirect(Yii::app()->createUrl('configOffice/edit',array('index'=>$model->id)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,));
			}
		}
	}

	public function actionView($index)
	{
		$model = new ConfigOfficeForm('view');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}

    public function actionNew()
    {
        $model = new ConfigOfficeForm('new');
        $model->city = Yii::app()->user->city();
        $this->render('form',array('model'=>$model,));
    }

	public function actionEdit($index)
	{
		$model = new ConfigOfficeForm('edit');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}

    public function actionDelete()
    {
        $model = new ConfigOfficeForm('delete');
        if (isset($_POST['ConfigOfficeForm'])) {
            $model->attributes = $_POST['ConfigOfficeForm'];
            if($model->deleteValidate()){
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
                $this->redirect(Yii::app()->createUrl('configOffice/index'));
            }else{
                $model->scenario = "edit";
                Dialog::message(Yii::t('dialog','Validation Message'), "办事处存在员工无法删除，请先修改员工");
                $this->render('form',array('model'=>$model,));
            }
        }else{
            $this->redirect(Yii::app()->createUrl('configOffice/index'));
        }
    }

    public function actionAjaxOffice(){
        if(Yii::app()->request->isAjaxRequest) {//是否ajax请求
            $id = key_exists("id",$_GET)?$_GET["id"]:0;
            $html = ConfigOfficeForm::getOfficeStaffToHtml($id);
            echo CJSON::encode(array("html"=>$html));
        }else{
            $this->redirect(Yii::app()->createUrl('staffSummary/index'));
        }
    }
}
