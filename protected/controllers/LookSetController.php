<?php

class LookSetController extends Controller
{
	public $function_id='ZC22';
	
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
                'actions'=>array('new','edit','delete','save','searchEmployee'),
                'expression'=>array('LookSetController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view'),
                'expression'=>array('LookSetController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('ZC22');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('ZC22');
    }

	public function actionIndex($pageNum=0) 
	{
		$model = new LookSetList;
		if (isset($_POST['LookSetList'])) {
			$model->attributes = $_POST['LookSetList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['lookSet_ya01']) && !empty($session['lookSet_ya01'])) {
				$criteria = $session['lookSet_ya01'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}


	public function actionSave()
	{
		if (isset($_POST['LookSetForm'])) {
			$model = new LookSetForm($_POST['LookSetForm']['scenario']);
			$model->attributes = $_POST['LookSetForm'];
			if ($model->validate()) {
				$model->saveData();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
				$this->redirect(Yii::app()->createUrl('lookSet/edit',array('index'=>$model->id)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,));
			}
		}
	}

	public function actionView($index)
	{
		$model = new LookSetForm('view');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}

	public function actionSearchEmployee()
	{
        if(Yii::app()->request->isAjaxRequest) {//是否ajax请求
            $model = new LookSetForm('view');
            $name = Yii::app()->request->getPost('name',"");
            $city = Yii::app()->request->getPost('city',"");
            $position = Yii::app()->request->getPost('position',"");
            $json = array("data"=>"","status"=>1);
            $json["data"] = $model->searchEmployee($city,$name,$position);
            echo CJSON::encode($json);
        }else{
            $this->redirect(Yii::app()->createUrl('lookSet/index'));
        }
	}

    public function actionNew()
    {
        $model = new LookSetForm('new');
        $this->render('form',array('model'=>$model,));
    }

	public function actionEdit($index)
	{
		$model = new LookSetForm('edit');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}

    public function actionDelete()
    {
        $model = new LookSetForm('delete');
        if (isset($_POST['LookSetForm'])) {
            $model->attributes = $_POST['LookSetForm'];
            if($model->deleteValidate()){
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
                $this->redirect(Yii::app()->createUrl('lookSet/index'));
            }else{
                $model->scenario = "edit";
                Dialog::message(Yii::t('dialog','Validation Message'), Yii::t("contract","The reward has staff being used, please delete the staff first"));
                $this->render('form',array('model'=>$model,));
            }
        }else{
            $this->redirect(Yii::app()->createUrl('lookSet/index'));
        }
    }

}
