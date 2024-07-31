<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class WordController extends Controller
{

 	public $function_id='ZD01';
	
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
                'expression'=>array('WordController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view','downfile'),
                'expression'=>array('WordController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('ZD01');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('ZD01');
    }

    public function actionIndex($pageNum=0){
        $model = new WordList;
        if (isset($_POST['WordList'])) {
            $model->attributes = $_POST['WordList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['word_01']) && !empty($session['word_01'])) {
                $criteria = $session['word_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }


    public function actionNew()
    {
        $model = new WordForm('new');
        $this->render('form',array('model'=>$model,));
    }

    public function actionDownfile($index)
    {
        $url =WordForm::getDocxUrlToId($index);
        if($url){
            $file = Yii::app()->basePath."/../".$url["docx_url"];
            header("Content-type: application/octet-stream");
            header('Content-Disposition: attachment; filename="'.$url["name"].'.docx"');
            header("Content-Length: ". filesize($file));
            readfile($file);
        }else{
            $this->render('index');
        }

    }

    public function actionEdit($index)
    {
        $model = new WordForm('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionView($index)
    {
        $model = new WordForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }


    public function actionSave()
    {
        if (isset($_POST['WordForm'])) {
            $model = new WordForm($_POST['WordForm']['scenario']);
            $model->attributes = $_POST['WordForm'];
            if ($model->validate()) {
                if(empty($model->docx_url)){
                    $docx = CUploadedFile::getInstance($model,'file');
                    $path = Yii::app()->basePath."/../upload/contract/";
                    if (!file_exists($path)){
                        mkdir ($path);
                        $myfile = fopen($path."index.php", "w");
                        fclose($myfile);
                    }
                    $model->docx_url = 'upload/contract/'.date("YmdHis").".docx";
                    $docx->saveAs($model->docx_url);
                }

                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('word/edit',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    //刪除文檔
    public function actionDelete(){
        $model = new WordForm('delete');
        if (isset($_POST['WordForm'])) {
            $model->attributes = $_POST['WordForm'];
            if($model->validateDelete()){
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
            }else{
                Dialog::message(Yii::t('dialog','Information'), Yii::t('contract','This word is under contract. Please delete the contract first'));
                $this->redirect(Yii::app()->createUrl('word/edit',array('index'=>$model->id)));
            }
        }
        $this->redirect(Yii::app()->createUrl('word/index'));
    }
}