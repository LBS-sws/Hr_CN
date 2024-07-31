<?php


class TripController extends Controller
{
    public $function_id='ZA10';

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
                'actions'=>array('new','edit','delete','save','audit','result','fileupload','fileRemove'),
                'expression'=>array('TripController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view','fileDownload'),
                'expression'=>array('TripController','allowReadOnly'),
            ),
            array('allow',
                'actions'=>array('cancel'),
                'expression'=>array('TripController','allowCancelled'),
            ),
            array('allow',
                'actions'=>array('reply'),
                'expression'=>array('TripController','allowReply'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }
    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('ZA10');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('ZA10');
    }

    public static function allowCancelled() {
        return Yii::app()->user->validFunction('ZR05');
    }

    public static function allowReply() {
        return Yii::app()->user->validFunction('ZG10');
    }

    //退回
    public function actionReply(){
        $model = new TripForm('reply');
        if (isset($_POST['TripForm'])) {
            $model->attributes = $_POST['TripForm'];
            if($model->validate()){
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('contract','finish to send back'));
                $this->redirect(Yii::app()->createUrl('trip/index'));
            }else{
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->redirect(Yii::app()->createUrl('trip/edit',array('index'=>$model->id)));
            }
        }
    }

    //取消
    public function actionCancel(){
        $model = new TripForm('cancel');
        if (isset($_POST['TripForm'])) {
            $model->attributes = $_POST['TripForm'];
            if($model->validate()){
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Cancel Done'));
                $this->redirect(Yii::app()->createUrl('trip/index'));
            }else{
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->redirect(Yii::app()->createUrl('trip/edit',array('index'=>$model->id)));
            }
        }
    }

    //出差結果
    public function actionResult(){
        $model = new TripForm('result');
        if (isset($_POST['TripForm'])) {
            $model->attributes = $_POST['TripForm'];
            if($model->validateResult()){
                $model->saveResult();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('trip/edit',array('index'=>$model->id)));
            }else{
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->redirect(Yii::app()->createUrl('trip/edit',array('index'=>$model->id)));
            }
        }
    }

    public function actionAudit()
    {
        if (isset($_POST['TripForm'])) {
            $model = new TripForm($_POST['TripForm']['scenario']);
            $model->attributes = $_POST['TripForm'];
            $model->audit = true;
            if ($model->validate()) {
                $model->status=1;
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('trip/edit',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $model->audit = false;
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    public function actionIndex($pageNum=0){
        $model = new TripList;
        TripList::validateEmployee($model);
        if (isset($_POST['TripList'])) {
            $model->attributes = $_POST['TripList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['trip_01']) && !empty($session['trip_01'])) {
                $criteria = $session['trip_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }


    public function actionNew()
    {
        $model = new TripForm('new');
        if(TripList::validateEmployee($model)){
            if($model->validateCount("id","id")){
                $this->render('form',array('model'=>$model,));
            }else{
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->redirect(Yii::app()->createUrl('trip/index'));
            }
        }else{
            throw new CHttpException(404,'该账号未绑定员工，请与管理员联系');
        }
    }

    public function actionEdit($index)
    {
        $model = new TripForm('edit');
        TripList::validateEmployee($model);
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionView($index)
    {
        $model = new TripForm('view');
        TripList::validateEmployee($model);
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }


    public function actionSave()
    {
        if (isset($_POST['TripForm'])) {
            $model = new TripForm($_POST['TripForm']['scenario']);
            $model->attributes = $_POST['TripForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('trip/edit',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    //刪除
    public function actionDelete(){
        $model = new TripForm('delete');
        if (isset($_POST['TripForm'])) {
            $model->attributes = $_POST['TripForm'];
            if($model->validate()){
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
                $this->redirect(Yii::app()->createUrl('trip/index'));
            }else{
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','This record is already in use'));
                $this->redirect(Yii::app()->createUrl('trip/edit',array('index'=>$model->id)));
            }
        }
    }

    public function actionFileupload($doctype) {
        $model = new TripForm();
        if (isset($_POST['TripForm'])) {
            $model->attributes = $_POST['TripForm'];

            $id = ($_POST['TripForm']['scenario']=='new') ? 0 : $model->id;
            $docman = new DocMan($model->docType,$id,get_class($model));
            $docman->masterId = $model->docMasterId[strtolower($doctype)];
            if (isset($_FILES[$docman->inputName])) $docman->files = $_FILES[$docman->inputName];
            $docman->fileUpload();
            if($_POST['TripForm']['scenario']=='new'||$model->status == 0||$model->status == 3||Yii::app()->user->validFunction('ZR05')){
                echo $docman->genTableFileList(false);
            }else{
                echo $docman->genTableFileList(false,false);
            }
        } else {
            echo "NIL";
        }
    }

    public function actionFileRemove($doctype) {
        $model = new TripForm();
        if (isset($_POST['TripForm'])) {
            $model->attributes = $_POST['TripForm'];

            $docman = new DocMan($model->docType,$model->id,'TripForm');
            $docman->masterId = $model->docMasterId[strtolower($doctype)];
            $docman->fileRemove($model->removeFileId[strtolower($doctype)]);
            if($_POST['TripForm']['scenario']=='new'||$model->status == 0||$model->status == 3||Yii::app()->user->validFunction('ZR10')){
                echo $docman->genTableFileList(false);
            }else{
                echo $docman->genTableFileList(false,false);
            }
        } else {
            echo "NIL";
        }
    }

    public function actionFileDownload($mastId, $docId, $fileId, $doctype) {
        $sql = "select city from hr_employee_trip where id = $docId";
        $row = Yii::app()->db->createCommand($sql)->queryRow();
        if ($row!==false) {
            $citylist = Yii::app()->user->city_allow();
            if (strpos($citylist, $row['city']) !== false) {
                $docman = new DocMan($doctype,$docId,'TripForm');
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