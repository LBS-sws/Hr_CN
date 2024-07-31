<?php

/**
 */
class SignContractController extends Controller
{
	public $function_id='ZE09';

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
                'actions'=>array('edit','draft','save','down','fileupload','fileRemove'),
                'expression'=>array('SignContractController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('delete'),
                'expression'=>array('SignContractController','allowDelete'),
            ),
            array('allow',
                'actions'=>array('index','view','fileDownload'),
                'expression'=>array('SignContractController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('ZE09');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('ZE09');
    }

    public static function allowDelete() {
        return Yii::app()->user->validFunction('ZR17');
    }

    public function actionIndex($pageNum=0){
        $model = new SignContractList;
        if (isset($_POST['SignContractList'])) {
            $model->attributes = $_POST['SignContractList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['signContract_01']) && !empty($session['signContract_01'])) {
                $criteria = $session['signContract_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }


    public function actionEdit($index)
    {
        $model = new SignContractForm('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionView($index)
    {
        $model = new SignContractForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }


    public function actionSave()
    {
        if (isset($_POST['SignContractForm'])) {
            $model = new SignContractForm($_POST['SignContractForm']['scenario']);
            $model->attributes = $_POST['SignContractForm'];
            $model->status_type = 2;
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('signContract/edit',array('index'=>$model->id)));
            } else {
                $model->status_type = $_POST['SignContractForm']["status_type"];
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }


    public function actionDown($index = 0)
    {
        if (isset($_POST['SignContractForm'])) {
            $model = new SignContractForm($_POST['SignContractForm']['scenario']);
            $model->attributes = $_POST['SignContractForm'];
            $model->his_id = $index;
            if ($model->validateDown()) {
                $url = EmployeeForm::updateEmployeeWord($model->downList);
                if($url){
                    $file = Yii::app()->basePath."/../".$url["word_url"];
                    // To prevent corrupted zip - Percy
                    ob_clean();
                    ob_end_flush();
                    //
                    header("Content-type: application/octet-stream");
                    header('Content-Disposition: attachment; filename='.$url["name"].'.docx');
                    header("Content-Length: ". filesize($file));
                    readfile($file);
                }else{
                    $this->redirect(Yii::app()->createUrl('signContract/edit',array('index'=>$model->id)));
                }
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->redirect(Yii::app()->createUrl('signContract/edit',array('index'=>$model->id)));
            }
        }
    }

    public function actionDraft()
    {
        if (isset($_POST['SignContractForm'])) {
            $model = new SignContractForm($_POST['SignContractForm']['scenario']);
            $model->attributes = $_POST['SignContractForm'];
            $model->status_type = 1;
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('signContract/edit',array('index'=>$model->id)));
            } else {
                $model->status_type = $_POST['SignContractForm']["status_type"];
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    //刪除
    public function actionDelete(){
        $model = new SignContractForm('delete');
        if (isset($_POST['SignContractForm'])) {
            $model->attributes = $_POST['SignContractForm'];
            if($model->deleteValidate()){
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
                $this->redirect(Yii::app()->createUrl('signContract/index'));
            }else{
                Dialog::message(Yii::t('dialog','Information'), "该员工未离职无法删除");
                $this->redirect(Yii::app()->createUrl('signContract/edit',array('index'=>$model->id)));
            }
        }
    }

    //上傳附件
    public function actionFileupload($doctype) {
        $model = new SignContractForm();
        if (isset($_POST['SignContractForm'])) {
            $model->attributes = $_POST['SignContractForm'];
            $model->validateId('name','');
            if(empty($model->employee_id)){
                echo "NIL";
            }else{
                $model->updateStatusType();
                $id = $model->employee_id;
                $docman = new DocMan($model->docType,$id,get_class($model));
                $docman->masterId = $model->docMasterId[strtolower($doctype)];
                if (isset($_FILES[$docman->inputName])) $docman->files = $_FILES[$docman->inputName];
                $docman->fileUpload();
                echo $docman->genTableFileList(false);
            }
        } else {
            echo "NIL";
        }
    }

    //刪除附件
    public function actionFileRemove($doctype) {
        $model = new SignContractForm();
        if (isset($_POST['SignContractForm'])) {
            $model->attributes = $_POST['SignContractForm'];
            $model->validateId('name','');
            if(empty($model->employee_id)){
                echo "NIL";
            }else{
                $docman = new DocMan($model->docType,$model->employee_id,'SignContractForm');
                $docman->masterId = $model->docMasterId[strtolower($doctype)];
                $docman->fileRemove($model->removeFileId[strtolower($doctype)]);
                echo $docman->genTableFileList(false);
            }
        } else {
            echo "NIL";
        }
    }

    //下載附件
    public function actionFileDownload($mastId, $docId, $fileId, $doctype) {
        $sql = "select city from hr_employee where id = $docId";
        $row = Yii::app()->db->createCommand($sql)->queryRow();
        if ($row!==false) {
            $citylist = Yii::app()->user->city_allow();
            if (strpos($citylist, $row['city']) !== false) {
                $docman = new DocMan($doctype,$docId,'SignContractForm');
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