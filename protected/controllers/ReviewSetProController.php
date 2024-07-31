<?php

/**
 * Created by PhpStorm.
 * User: 配置考核選項
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class ReviewSetProController extends Controller
{
	public $function_id='RE04';

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
                'actions'=>array('edit','save','new','delete'),
                'expression'=>array('ReviewSetProController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view'),
                'expression'=>array('ReviewSetProController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('RE04');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('RE04');
    }

    public function actionIndex($type,$pageNum=0){
        $model = new ReviewSetProList;
        if (isset($_POST['ReviewSetProList'])) {
            $model->attributes = $_POST['ReviewSetProList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['reviewSetPro_01']) && !empty($session['reviewSetPro_01'])) {
                $criteria = $session['reviewSetPro_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($type,$model->pageNum);
        $this->render('index',array('model'=>$model));
    }


    public function actionEdit($index)
    {
        $model = new ReviewSetProForm('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionView($index)
    {
        $model = new ReviewSetProForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }


    public function actionNew($type)
    {
        $model = new ReviewSetProForm('new');
        $model->set_id = $type;
        $model->set_name = ReviewSetForm::getSetNameToId($type);
        $this->render('form',array('model'=>$model,));
    }

    public function actionSave()
    {
        if (isset($_POST['ReviewSetProForm'])) {
            $model = new ReviewSetProForm($_POST['ReviewSetProForm']['scenario']);
            $model->attributes = $_POST['ReviewSetProForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('reviewSetPro/edit',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    public function actionDelete()
    {
        $model = new ReviewSetProForm('delete');
        if (isset($_POST['ReviewSetProForm'])) {
            $model->attributes = $_POST['ReviewSetProForm'];
            if($model->deleteValidate()){
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
                $this->redirect(Yii::app()->createUrl('reviewSetPro/index',array("type"=>$model->set_id)));
            }else{
                $model->scenario = "edit";
                Dialog::message(Yii::t('dialog','Validation Message'), "项目不存在");
                $this->render('form',array('model'=>$model));
            }
        }else{
            $this->redirect(Yii::app()->createUrl('reviewSet/index'));
        }
    }
}