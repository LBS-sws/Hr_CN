<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class PrizeController extends Controller
{
	public $function_id='ZE08';

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
                'actions'=>array('new','edit','delete','save','audit','uploadImg','AjaxCustomer'),
                'expression'=>array('PrizeController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view'),
                'expression'=>array('PrizeController','allowReadOnly'),
            ),
            array('allow',
                'actions'=>array('AjaxCity','printImage'),
                'expression'=>array('PrizeController','allowWrite'),
            ),
            array('allow',
                'actions'=>array('back'),
                'expression'=>array('PrizeController','allowBack'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('ZE08');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('ZE08');
    }

    public static function allowBack() {
        return Yii::app()->user->validFunction('ZR18');
    }

    public static function allowWrite() {
        return !empty(Yii::app()->user->id);
    }

    public function actionIndex($pageNum=0){
        $model = new PrizeList;
        if (isset($_POST['PrizeList'])) {
            $model->attributes = $_POST['PrizeList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['prize_01']) && !empty($session['prize_01'])) {
                $criteria = $session['prize_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }


    public function actionNew()
    {
        $model = new PrizeForm('new');
        $model->prize_date = date("Y-m-d");
        $model->prize_num = 1;
        $this->render('form',array('model'=>$model,));
    }

    public function actionEdit($index)
    {
        $model = new PrizeForm('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionView($index)
    {
        $model = new PrizeForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }


    public function actionSave()
    {
        if (isset($_POST['PrizeForm'])) {
            $model = new PrizeForm($_POST['PrizeForm']['scenario']);
            $model->attributes = $_POST['PrizeForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('prize/edit',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

/*    public function actionCopy()
    {
        $model = new PrizeForm('new');
        if (isset($_POST['PrizeForm'])) {
            $model->attributes = $_POST['PrizeForm'];
            $this->render('form',array('model'=>$model,));
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }*/

    public function actionAudit()
    {
        if (isset($_POST['PrizeForm'])) {
            $model = new PrizeForm($_POST['PrizeForm']['scenario']);
            $model->attributes = $_POST['PrizeForm'];
            $model->audit = true;
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('prize/edit',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $model->audit = false;
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    //退回
    public function actionBack()
    {
        $model = new PrizeForm('back');
        $model->attributes = $_POST['PrizeForm'];
        if ($model->validateID()) {
            $model->saveData();
            Dialog::message(Yii::t('dialog','Information'), Yii::t('contract','finish to send back'));
            $this->redirect(Yii::app()->createUrl('prize/edit',array('index'=>$model->id)));
        } else {
            $message = CHtml::errorSummary($model);
            Dialog::message(Yii::t('dialog','Validation Message'), $message);
            $this->redirect(Yii::app()->createUrl('prize/edit',array('index'=>$model->id)));
        }
    }

    //刪除
    public function actionDelete(){
        $model = new PrizeForm('delete');
        if (isset($_POST['PrizeForm'])) {
            $model->attributes = $_POST['PrizeForm'];
            if($model->validate()){
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
                $this->redirect(Yii::app()->createUrl('assess/index'));
            }else{
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','This record is already in use'));
                $this->redirect(Yii::app()->createUrl('assess/edit',array('index'=>$model->id)));
            }
        }
    }

    //上傳圖片
    public function actionUploadImg(){
        if(Yii::app()->request->isAjaxRequest) {//是否ajax请求
            $model = new UploadImgForm();
            $img = CUploadedFile::getInstance($model,'file');
            $city = Yii::app()->user->city();
            $path =Yii::app()->basePath."/../upload/images/";
            if (!file_exists($path)){
                mkdir($path);
                $myfile = fopen($path."index.php", "w");
                fclose($myfile);
            }
            $path.=$city."/";
            if (!file_exists($path)){
                mkdir($path);
                $myfile = fopen($path."index.php", "w");
                fclose($myfile);
            }
            $url = "upload/images/".$city."/".date("YmdHsi").".".$img->getExtensionName();
            $model->file = $img->getName();
            if ($model->file && $model->validate()) {
                $img->saveAs($url);
                //$url = "/".Yii::app()->params['systemId']."/".$url;
                $url = "../../".$url;
                echo CJSON::encode(array('status'=>1,'data'=>$url));
            }else{
                echo CJSON::encode(array('status'=>0,'error'=>$model->getErrors()));
            }
        }else{
            $this->redirect(Yii::app()->createUrl('prize/index'));
        }
    }

    public function actionAjaxCity() {
        if(Yii::app()->request->isAjaxRequest) {//是否ajax请求
            $city = $_POST['city'];
            $staffList = AssessForm::getEmployeeList($city);
            $customerList = "";
            $staffNum = AssessForm::getPrizeStaffNum($city);;//參與人數
            //$customerList = PrizeForm::getCustomerList($city);
            unset($staffList[""]);
            //unset($customerList[""]);
            echo CJSON::encode(array("status"=>1,"staffList"=>$staffList,"customerList"=>$customerList,"staffNum"=>$staffNum));
        }else{
            $this->redirect(Yii::app()->createUrl(''));
        }
    }

    public function actionAjaxCustomer() {
        if(Yii::app()->request->isAjaxRequest) {//是否ajax请求
            $model = new CustomerList;
            $model->search_code = $_POST['search_code'];
            $model->search_name = $_POST['search_name'];
            $pageNum=$_POST['pageNum'];
            $model->noOfItem = 5;
            $model->determinePageNum($pageNum);
            $model->retrieveDataByPage($model->pageNum);
            $pageHtml =  TbHtml::pagination($model->getPageList(), array('class'=>'pagination pagination-sm no-margin'));
            $pageHtml.= "<div class='pull-right'>".Yii::t('misc','Record')."：<span>".$model->totalRow."</span></div>";
            echo CJSON::encode(array("status"=>1,"attr"=>$model->attr,"pageHtml"=>$pageHtml));
        }else{
            $this->redirect(Yii::app()->createUrl(''));
        }
    }

    public function actionPrintImage($id = 0,$str="") {
        $rows = Yii::app()->db->createCommand()->select("$str")
            ->from("hr_prize")->where("id=:id",array(":id"=>$id))->queryRow();
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
                    echo "地址不存在".$path;
                    return false;
                }
            }
        }else{
            echo "沒找到圖片";
            return false;
        }
    }
}