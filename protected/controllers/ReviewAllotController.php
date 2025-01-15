<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class ReviewAllotController extends Controller
{
	public $function_id='RE01';

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
                'actions'=>array('new','edit','draft','save','undo','back','fileupload','fileRemove','fileRemove','bulkAllot'),
                'expression'=>array('ReviewAllotController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view','fileDownload'),
                'expression'=>array('ReviewAllotController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }
    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('RE01');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('RE01');
    }

    public function actionIndex($pageNum=0){
        $model = new ReviewAllotList();
        if (isset($_POST['ReviewAllotList'])) {
            $model->attributes = $_POST['ReviewAllotList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['reviewAllot_01']) && !empty($session['reviewAllot_01'])) {
                $criteria = $session['reviewAllot_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }

    public function actionEdit($index,$year,$year_type)
    {
        $model = new ReviewAllotForm('edit');
        if (!$model->retrieveData($index,$year,$year_type)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionView($index,$year,$year_type)
    {
        $model = new ReviewAllotForm('view');
        if (!$model->retrieveData($index,$year,$year_type)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }


    public function actionSave()
    {
        if (isset($_POST['ReviewAllotForm'])) {
            $model = new ReviewAllotForm($_POST['ReviewAllotForm']['scenario']);
            $model->attributes = $_POST['ReviewAllotForm'];
            $model->status_type = 1;
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('reviewAllot/edit',array('index'=>$model->employee_id,'year'=>$model->year,'year_type'=>$model->year_type)));
            } else {
                $model->status_type = 0;
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    public function actionDraft()
    {
        if (isset($_POST['ReviewAllotForm'])) {
            $model = new ReviewAllotForm($_POST['ReviewAllotForm']['scenario']);
            $model->attributes = $_POST['ReviewAllotForm'];
            $model->status_type = 4;
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('reviewAllot/edit',array('index'=>$model->employee_id,'year'=>$model->year,'year_type'=>$model->year_type)));
            } else {
                $model->status_type = 0;
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    //批量分配
    public function actionBulkAllot()
    {
        $model = new ReviewAllotForm;
        if(isset($_POST['ReviewAllotList']['attr'])){
            $year = isset($_POST['ReviewAllotList']['year'])?$_POST['ReviewAllotList']['year']:2022;
            $month = isset($_POST['ReviewAllotList']['year_type'])?$_POST['ReviewAllotList']['year_type']:1;
            $idList = $_POST['ReviewAllotList']['attr'];
            $model->bulkAllot($idList,$year,$month);
            $this->redirect(Yii::app()->createUrl('reviewAllot/index'));
        }else{
            Dialog::message(Yii::t('dialog','Warning'), Yii::t('dialog','No Record Found'));
            $this->redirect(Yii::app()->createUrl('reviewAllot/index'));
        }

    }

    //退回單個考核
    public function actionBack($index)
    {
        $model = new ReviewAllotForm();
        $model->reviewBack($index);
        $this->redirect(Yii::app()->createUrl('reviewAllot/edit',array('index'=>$model->employee_id,'year'=>$model->year,'year_type'=>$model->year_type)));
    }

    //退回草稿
    public function actionUndo(){
        $model = new ReviewAllotForm('undo');
        if (isset($_POST['ReviewAllotForm'])) {
            $model->attributes = $_POST['ReviewAllotForm'];
            if($model->validateUndo()){
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('contract','Draft status returned'));
                $this->redirect(Yii::app()->createUrl('reviewAllot/edit',array('index'=>$model->employee_id,'year'=>$model->year,'year_type'=>$model->year_type)));
            }else{
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','This record is already in use'));
                $this->redirect(Yii::app()->createUrl('reviewAllot/edit',array('index'=>$model->employee_id,'year'=>$model->year,'year_type'=>$model->year_type)));
            }
        }
    }

    //上傳附件
    public function actionFileupload($doctype='REVIEW') {
        $model = new ReviewAllotForm();
        if (isset($_POST['ReviewAllotForm'])) {
            $model->attributes = $_POST['ReviewAllotForm'];
            $id = ($_POST['ReviewAllotForm']['scenario']=='new') ? 0 : $model->review_id;
            $id = empty($id)?0:$id;
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
        $model = new ReviewAllotForm();
        if (isset($_POST['ReviewAllotForm'])) {
            $model->attributes = $_POST['ReviewAllotForm'];

            $docman = new DocMan($model->docType,$model->review_id,'ReviewAllotForm');
            $docman->masterId = $model->docMasterId[strtolower($doctype)];
            $docman->fileRemove($model->removeFileId[strtolower($doctype)]);
            echo $docman->genTableFileList(false);
        } else {
            echo "NIL";
        }
    }

    //下載附件
    public function actionFileDownload($mastId, $docId, $fileId, $doctype) {
        $sql = "select b.city from hr_review a LEFT JOIN hr_employee b ON a.employee_id = b.id where a.id = $docId";
        $row = Yii::app()->db->createCommand($sql)->queryRow();
        if ($row!==false) {
            $citylist = Yii::app()->user->city_allow();
            if (strpos($citylist, $row['city']) !== false) {
                $docman = new DocMan($doctype,$docId,'ReviewAllotForm');
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