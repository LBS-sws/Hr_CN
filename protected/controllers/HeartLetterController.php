<?php

/**
 * Created by PhpStorm.
 * User: 心意信active
 * Date: 2021/3/23 0007
 * Time: 上午 11:30
 */
class HeartLetterController extends Controller
{
	public $function_id='HL01';

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
                'actions'=>array('new','edit','delete','save','audit','fileupload','fileRemove'),
                'expression'=>array('HeartLetterController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view','fileDownload','PdfDownload'),
                'expression'=>array('HeartLetterController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('HL01');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('HL01');
    }

    public function actionIndex($pageNum=0){
        $model = new HeartLetterList();
        if($model->validateEmployee()){
            if (isset($_POST['HeartLetterList'])) {
                $model->attributes = $_POST['HeartLetterList'];
            } else {
                $session = Yii::app()->session;
                if (isset($session['heartLetter_01']) && !empty($session['heartLetter_01'])) {
                    $criteria = $session['heartLetter_01'];
                    $model->setCriteria($criteria);
                }
            }
            $model->determinePageNum($pageNum);
            $model->retrieveDataByPage($model->pageNum);
            $this->render('index',array('model'=>$model));
        }else{
            throw new CHttpException(404,Yii::t("contract",'The account has no binding staff, please contact the administrator'));
        }
    }


    public function actionNew()
    {
        $model = new HeartLetterForm('new');
        $employeeId = WorkList::getEmployeeId();
        if(empty($employeeId)){
            throw new CHttpException(404,Yii::t("contract",'The account has no binding staff, please contact the administrator'));
        }else{
            $model->employee_id = $employeeId;
            $model->goOnLetter();
        }
        $this->render('form',array('model'=>$model,));
    }

    public function actionEdit($index)
    {
        $model = new HeartLetterForm('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionView($index)
    {
        $model = new HeartLetterForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }


    public function actionSave()
    {
        if (isset($_POST['HeartLetterForm'])) {
            $model = new HeartLetterForm($_POST['HeartLetterForm']['scenario']);
            $model->attributes = $_POST['HeartLetterForm'];
            $model->state = 0;
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('heartLetter/edit',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    public function actionAudit()
    {
        if (isset($_POST['HeartLetterForm'])) {
            $model = new HeartLetterForm($_POST['HeartLetterForm']['scenario']);
            $model->attributes = $_POST['HeartLetterForm'];
            $model->state = 0;
            if ($model->validate()) {
                $model->state = 1;
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('heartLetter/edit',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    //刪除
    public function actionDelete(){
        $model = new HeartLetterForm('delete');
        if (isset($_POST['HeartLetterForm'])) {
            $model->attributes = $_POST['HeartLetterForm'];
            if($model->validate()){
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
                $this->redirect(Yii::app()->createUrl('heartLetter/index'));
            }else{
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','This record is already in use'));
                $this->redirect(Yii::app()->createUrl('heartLetter/edit',array('index'=>$model->id)));
            }
        }
    }

    public function actionFileupload($doctype) {
        $model = new HeartLetterForm();
        if (isset($_POST['HeartLetterForm'])) {
            $model->attributes = $_POST['HeartLetterForm'];

            $id = ($_POST['HeartLetterForm']['scenario']=='new') ? 0 : $model->id;
            $docman = new DocMan($model->docType,$id,get_class($model));
            $docman->masterId = $model->docMasterId[strtolower($doctype)];
            if (isset($_FILES[$docman->inputName])) $docman->files = $_FILES[$docman->inputName];
            $docman->fileUpload();
            if($_POST['HeartLetterForm']['scenario']=='new'||$model->state == 0){
                echo $docman->genTableFileList(false);
            }else{
                echo $docman->genTableFileList(false,false);
            }
        } else {
            echo "NIL";
        }
    }

    public function actionFileRemove($doctype) {
        $model = new HeartLetterForm();
        if (isset($_POST['HeartLetterForm'])) {
            $model->attributes = $_POST['HeartLetterForm'];

            $docman = new DocMan($model->docType,$model->id,'HeartLetterForm');
            $docman->masterId = $model->docMasterId[strtolower($doctype)];
            $docman->fileRemove($model->removeFileId[strtolower($doctype)]);
            if($_POST['HeartLetterForm']['scenario']=='new'||$model->state == 0){
                echo $docman->genTableFileList(false);
            }else{
                echo $docman->genTableFileList(false,false);
            }
        } else {
            echo "NIL";
        }
    }

    public function actionFileDownload($mastId, $docId, $fileId, $doctype) {
        $sql = "select city from hr_letter where id = $docId";
        $row = Yii::app()->db->createCommand($sql)->queryRow();
        if ($row!==false) {
            $citylist = Yii::app()->user->city_allow();
            if (strpos($citylist, $row['city']) !== false) {
                $docman = new DocMan($doctype,$docId,'HeartLetterForm');
                $docman->masterId = $mastId;
                $docman->fileDownload($fileId);
            } else {
                throw new CHttpException(404,'Access right not match.');
            }
        } else {
            throw new CHttpException(404,'Record not found.');
        }
    }
}