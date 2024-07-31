<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class ExternalController extends Controller
{
	public $function_id='EL01';

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
                'actions'=>array('new','edit','save','delete','audit','uploadImg','fileupload','fileRemove','testBS'),
                'expression'=>array('ExternalController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view','fileDownload','printImage'),
                'expression'=>array('ExternalController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }
    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('EL01');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('EL01');
    }

    public static function allowWrite() {
        return !empty(Yii::app()->user->id);
    }

    public function actionTestBS(){
        $selectData=array(
            "empStatus"=>null,//人员状态：默认null，示例：[1,2,3,7]（待入职、试用、正式、返聘）。
            "employType"=>array(0,1,2),//雇佣类型：默认查询内部员工。示例：[0,2]，表示内部员工、实习生。
            "serviceType"=>array(0,1),//任职类型：默认查询主职。示例：[0]，表示主职。
            "withDisabled"=>true,//是否包含离职的记录
            "isGetOfferRecord"=>true,//是否获取任职记录对应的offer的记录
            "startTime"=>"2024-04-01T00:00:00",//时间范围开始时间，格式：2021-01-01T00:00:00
            "stopTime"=>"2024-07-01T00:00:00",//时间范围结束时间，格式：2021-01-01T00:00:00
            "timeWindowQueryType"=>"1",//时间窗查询类型，1修改时间、2业务修改时间
            "scrollId"=>null,//本批次的ScrollId，第一次查询为空
            "enableTranslate"=>true,//是否开启动态翻译，默认否
        );
        $url = "/TenantBaseExternal/api/v5/Employee/GetByTimeWindow";
        $curlModel =new CurlForStaff();
        $list = $curlModel->getHistoryData($selectData,$url);
        var_dump($list);
    }

    public function actionIndex($pageNum=0){
        $model = new ExternalList;
        if (isset($_POST['ExternalList'])) {
            $model->attributes = $_POST['ExternalList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['external_01']) && !empty($session['external_01'])) {
                $criteria = $session['external_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }

    public function actionNew()
    {
        $model = new ExternalForm('new');
        $model->entry_time = $model->test_start_time = date("Y/m/d");
        $this->render('form',array('model'=>$model,));
    }

    public function actionEdit($index)
    {
        $model = new ExternalForm('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionView($index)
    {
        $model = new ExternalForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionSave(){ //草稿
        if (isset($_POST['ExternalForm'])) {
            $model = new ExternalForm($_POST['ExternalForm']['scenario']);
            $model->attributes = $_POST['ExternalForm'];
            if ($model->validate()) {
                $model->staff_status=9;
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('external/edit',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    public function actionAudit(){//要求审核
        if (isset($_POST['ExternalForm'])) {
            $model = new ExternalForm($_POST['ExternalForm']['scenario']);
            $model->attributes = $_POST['ExternalForm'];
            if ($model->validate()) {
                $model->staff_status=2;
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('external/edit',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $model->setScenario($_POST['ExternalForm']['scenario']);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    //刪除草稿
    public function actionDelete(){
        $model = new ExternalForm('delete');
        if (isset($_POST['ExternalForm'])) {
            $model->attributes = $_POST['ExternalForm'];
            if($model->validateDelete()){
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
                $this->redirect(Yii::app()->createUrl('external/index'));
            }else{
                Dialog::message(Yii::t('dialog','Information'), Yii::t('contract','The dept has staff being used, please delete the staff first'));
                $this->redirect(Yii::app()->createUrl('external/edit',array('index'=>$model->id)));
            }
        }
    }

    //上傳附件
    public function actionFileupload($doctype) {
        $model = new ExternalForm();
        if (isset($_POST['ExternalForm'])) {
            $model->attributes = $_POST['ExternalForm'];

            $id = ($_POST['ExternalForm']['scenario']=='new') ? 0 : $model->id;
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
        $model = new ExternalForm();
        if (isset($_POST['ExternalForm'])) {
            $model->attributes = $_POST['ExternalForm'];

            $docman = new DocMan($model->docType,$model->id,'ExternalForm');
            $docman->masterId = $model->docMasterId[strtolower($doctype)];
            $docman->fileRemove($model->removeFileId[strtolower($doctype)]);
            echo $docman->genTableFileList(false);
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
                $docman = new DocMan($doctype,$docId,'ExternalForm');
                $docman->masterId = $mastId;
                $docman->fileDownload($fileId);
            } else {
                throw new CHttpException(404,'Access right not match.');
            }
        } else {
            throw new CHttpException(404,'Record not found.');
        }
    }


    public function actionPrintImage($id = 0,$staff = 0,$str="") {
        $id = empty($id)?$staff:$id;
        $rows = Yii::app()->db->createCommand()->select("$str")
            ->from("hr_external")->where("id=:id",array(":id"=>$id))->queryRow();
        if($rows){
            if(empty($rows[$str])){
                echo "圖片不存在";
                return false;
            }else{
                $n = new imgdata;
                $path = "protected/controllers/".$rows[$str];
                if (file_exists($path)) {
                    $n -> getdir($path);
                    $n -> img2data();
                    $n -> data2img();
                } else {
                    echo "地址不存在";
                    return false;
                }
            }
        }else{
            echo "沒找到圖片";
            return false;
        }
    }
}