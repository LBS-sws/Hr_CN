<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class CompanyController extends Controller
{
	public $function_id='CL01';

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
                'actions'=>array('new','edit','save','copy','delete','fileupload','fileRemove','test'),
                'expression'=>array('CompanyController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view','fileDownload','test'),
                'expression'=>array('CompanyController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }
    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('CL01');
    }

    public static function allowReadOnly() {//ZR25
        return Yii::app()->user->validFunction('CL01')||Yii::app()->user->validFunction('ZR25');
    }

    public function actionTest()
    {
        // 调用方法并传递静态资源路径
        $resourcePath = '/docman/upload/sal/uat/57/0/4731ca7a290485a311e4e05f8d55ff3f.jpg';
        $resourceContent = $this->readStaticResource($resourcePath);

        // 输出图片内容
        header('Content-Type: image/jpeg');
        echo $resourceContent;
    }

    public function readStaticResource($resourcePath)
    {
        // 读取静态资源文件内容
        $content = @file_get_contents($resourcePath);

        // 返回文件内容
        return $content;
    }
    public function actionIndex($pageNum=0){
        $model = new CompanyList;
        if (isset($_POST['CompanyList'])) {
            $model->attributes = $_POST['CompanyList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['company_01']) && !empty($session['company_01'])) {
                $criteria = $session['company_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }


    public function actionNew()
    {
        $model = new CompanyForm('new');
        $this->render('form',array('model'=>$model,));
    }

    public function actionCopy($index)
    {
        $model = new CompanyForm('new');
        $model->copyData($index);
        $this->render('form',array('model'=>$model,));
    }

    public function actionEdit($index)
    {
        $model = new CompanyForm('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionView($index)
    {
        $model = new CompanyForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionSave()
    {
        if (isset($_POST['CompanyForm'])) {
            $model = new CompanyForm($_POST['CompanyForm']['scenario']);
            $model->attributes = $_POST['CompanyForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('company/edit',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    //刪除公司
    public function actionDelete(){
        $model = new CompanyForm('delete');
        if (isset($_POST['CompanyForm'])) {
            $model->attributes = $_POST['CompanyForm'];
            if($model->validateDelete()){
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
            }else{
                Dialog::message(Yii::t('dialog','Information'), Yii::t('contract','The company has employees, please delete employees first'));
                $this->redirect(Yii::app()->createUrl('company/edit',array('index'=>$model->id)));
            }
        }
        $this->redirect(Yii::app()->createUrl('company/index'));
    }


    public function actionFileupload($doctype) {
        $model = new CompanyForm();
        if (isset($_POST['CompanyForm'])) {
            $model->attributes = $_POST['CompanyForm'];
            if($model->validateID("id","")){
                $id = ($_POST['CompanyForm']['scenario']=='new') ? 0 : $model->id;
                $docman = new DocMan($doctype,$id,get_class($model));
                $docman->masterId = $model->docMasterId[strtolower($doctype)];
                if (isset($_FILES[$docman->inputName])) $docman->files = $_FILES[$docman->inputName];
                $docman->fileUpload();
            }
            echo $docman->genTableFileList(false);
        } else {
            echo "NIL";
        }
    }

    public function actionFileRemove($doctype) {
        $model = new CompanyForm();
        if (isset($_POST['CompanyForm'])) {
            $model->attributes = $_POST['CompanyForm'];

            if($model->validateID("id","")){
                $docman = new DocMan($doctype,$model->id,'CompanyForm');
                $docman->masterId = $model->docMasterId[strtolower($doctype)];
                $docman->fileRemove($model->removeFileId[strtolower($doctype)]);
            }
            echo $docman->genTableFileList(false);
        } else {
            echo "NIL";
        }
    }

    public function actionFileDownload($mastId, $docId, $fileId, $doctype) {
        $sql = "select city from hr_company where id = $docId";
        $row = Yii::app()->db->createCommand($sql)->queryRow();
        if ($row!==false) {
            $citylist = Yii::app()->user->city_allow();
            $bool = Yii::app()->user->validFunction('ZR25')&&"COMPANY2"==$doctype;
            if ($bool||strpos($citylist, $row['city']) !== false) {
                $docman = new DocMan($doctype,$docId,'CompanyForm');
                $docman->masterId = $mastId;
                $docman->fileDownload($fileId);
            } else {
                throw new CHttpException(404,'Access right not match.');
            }
        } else {
            throw new CHttpException(404,'Record not found.');
        }
    }
    /**
     * Performs the AJAX validation.
     * @param CModel the model to be validated
     */
    protected function performAjaxValidation($model)
    {
        if(isset($_POST['ajax']) && $_POST['ajax']==='company-form')
        {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

}
