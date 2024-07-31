<?php

/**
 * Created by PhpStorm.
 * User: 城市等級設置
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class PinNameController extends Controller
{
	public $function_id='PI04';

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
                'actions'=>array('edit','save','new','delete','uploadImg'),
                'expression'=>array('PinNameController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view'),
                'expression'=>array('PinNameController','allowReadOnly'),
            ),
            array('allow',
                'actions'=>array('PrintImage'),
                'expression'=>array('PinNameController','allowAll'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('PI04');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('PI04');
    }

    public static function allowAll() {
        return true;
    }

    public function actionIndex($pageNum=0){
        $model = new PinNameList;
        if (isset($_POST['PinNameList'])) {
            $model->attributes = $_POST['PinNameList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['pinName_01']) && !empty($session['pinName_01'])) {
                $criteria = $session['pinName_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }


    public function actionEdit($index)
    {
        $model = new PinNameForm('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionNew()
    {
        $model = new PinNameForm('new');
        $this->render('form',array('model'=>$model,));
    }

    public function actionView($index)
    {
        $model = new PinNameForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }


    public function actionSave()
    {
        if (isset($_POST['PinNameForm'])) {
            $model = new PinNameForm($_POST['PinNameForm']['scenario']);
            $model->attributes = $_POST['PinNameForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('PinName/edit',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    //刪除
    public function actionDelete(){
        $model = new PinNameForm('delete');
        if (isset($_POST['PinNameForm'])) {
            $model->attributes = $_POST['PinNameForm'];
            if($model->validate()){
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
                $this->redirect(Yii::app()->createUrl('PinName/index'));
            }else{
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','This record is already in use'));
                $this->redirect(Yii::app()->createUrl('PinName/edit',array('index'=>$model->id)));
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
            $this->redirect(Yii::app()->createUrl('pinName/index'));
        }
    }

    public function actionPrintImage($id = 0) {
        $str="image_url";
        $rows = Yii::app()->db->createCommand()->select($str)
            ->from("hr_pin_name")->where("id=:id",array(":id"=>$id))->queryRow();
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