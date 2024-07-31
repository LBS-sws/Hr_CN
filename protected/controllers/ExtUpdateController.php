<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class ExtUpdateController extends Controller
{
	public $function_id='EL02';

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
                'actions'=>array('update','departure','edit','save','delete','audit','fileupload','fileRemove'),
                'expression'=>array('ExtUpdateController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view','fileDownload'),
                'expression'=>array('ExtUpdateController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }
    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('EL02');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('EL02');
    }

    public static function allowWrite() {
        return !empty(Yii::app()->user->id);
    }

    public function actionIndex($pageNum=0){
        $model = new ExtUpdateList;
        if (isset($_POST['ExtUpdateList'])) {
            $model->attributes = $_POST['ExtUpdateList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['extUpdate_01']) && !empty($session['extUpdate_01'])) {
                $criteria = $session['extUpdate_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }

    public function actionUpdate($index)
    {//修改
        $model = new ExtUpdateForm('new');
        if(!$model->validateStaff($index)){
            Dialog::message(Yii::t('dialog','Validation Message'), Yii::t('contract','The employee has changed the information, please complete the change first'));
            $this->redirect(Yii::app()->createUrl('external/edit',array('index'=>$index)));
        }else{
            $model->retrieveDataForOld($index);
            $model->operation="update";
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionDeparture($index)
    {//离职
        $model = new ExtUpdateForm('new');
        if(!$model->validateStaff($index)){
            Dialog::message(Yii::t('dialog','Validation Message'), Yii::t('contract','The employee has changed the information, please complete the change first'));
            $this->redirect(Yii::app()->createUrl('external/edit',array('index'=>$index)));
        }else{
            $model->retrieveDataForOld($index);
            $model->operation="departure";
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionEdit($index)
    {
        $model = new ExtUpdateForm('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionView($index)
    {
        $model = new ExtUpdateForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionSave(){ //草稿
        if (isset($_POST['ExtUpdateForm'])) {
            $model = new ExtUpdateForm($_POST['ExtUpdateForm']['scenario']);
            $model->attributes = $_POST['ExtUpdateForm'];
            if ($model->validate()) {
                $model->staff_status=9;
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('extUpdate/edit',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    public function actionAudit(){//要求审核
        if (isset($_POST['ExtUpdateForm'])) {
            $model = new ExtUpdateForm($_POST['ExtUpdateForm']['scenario']);
            $model->attributes = $_POST['ExtUpdateForm'];
            if ($model->validate()) {
                $model->staff_status=2;
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('extUpdate/edit',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $model->setScenario($_POST['ExtUpdateForm']['scenario']);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    //刪除草稿
    public function actionDelete(){
        $model = new ExtUpdateForm('delete');
        if (isset($_POST['ExtUpdateForm'])) {
            $model->attributes = $_POST['ExtUpdateForm'];
            if($model->validateDelete()){
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
                $this->redirect(Yii::app()->createUrl('extUpdate/index'));
            }else{
                Dialog::message(Yii::t('dialog','Information'), Yii::t('contract','The dept has staff being used, please delete the staff first'));
                $this->redirect(Yii::app()->createUrl('extUpdate/edit',array('index'=>$model->id)));
            }
        }
    }

    //上傳附件
    public function actionFileupload($doctype) {
        $model = new ExtUpdateForm();
        if (isset($_POST['ExtUpdateForm'])) {
            $model->attributes = $_POST['ExtUpdateForm'];

            $id = $model->employee_id;
            $docman = new DocMan($model->docType,$id,get_class($model));
            $docman->masterId = $model->docMasterId[strtolower($doctype)];
            if (isset($_FILES[$docman->inputName])) $docman->files = $_FILES[$docman->inputName];
            $docman->fileUpload();
            echo $docman->genTableFileList(false);
        } else {
            echo "NIL";
        }
    }

    //刪除附件
    public function actionFileRemove($doctype) {
        $model = new ExtUpdateForm();
        if (isset($_POST['ExtUpdateForm'])) {
            $model->attributes = $_POST['ExtUpdateForm'];

            $docman = new DocMan($model->docType,$model->employee_id,'ExtUpdateForm');
            $docman->masterId = $model->docMasterId[strtolower($doctype)];
            $docman->fileRemove($model->removeFileId[strtolower($doctype)]);
            echo $docman->genTableFileList(false);
        } else {
            echo "NIL";
        }
    }

    //下載附件
    public function actionFileDownload($mastId, $docId, $fileId, $doctype) {
        $sql = "select city from hr_employee where table_type!=1 and id = $docId";
        $row = Yii::app()->db->createCommand($sql)->queryRow();
        if ($row!==false) {
            $citylist = Yii::app()->user->city_allow();
            if (strpos($citylist, $row['city']) !== false) {
                $docman = new DocMan($doctype,$docId,'ExtUpdateForm');
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