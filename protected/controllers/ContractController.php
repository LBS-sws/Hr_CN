<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class ContractController extends Controller
{
	public $function_id='ZD02';


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
                'actions'=>array('new','edit','save','delete','wordDelete','copyCity'),
                'expression'=>array('ContractController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view'),
                'expression'=>array('ContractController','allowReadOnly'),
            ),
            array('allow',
                'actions'=>array('orderGoodsDelete'),
                'expression'=>array('ContractController','allowWrite'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }
    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('ZD02');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('ZD02');
    }

    public static function allowWrite() {
        return !empty(Yii::app()->user->id);
    }

    public function actionCopyCity($toCity,$formCity="ZH")
    {
        $model = new ContractForm('new');
        $arr = $model->copyCity($toCity,$formCity);
        var_dump($arr);
        Yii::app()->end();
    }

    public function actionIndex($pageNum=0){
        $model = new ContractList;
        if (isset($_POST['ContractList'])) {
            $model->attributes = $_POST['ContractList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['contract_01']) && !empty($session['contract_01'])) {
                $criteria = $session['contract_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }


    public function actionNew()
    {
        $model = new ContractForm('new');
        $this->render('form',array('model'=>$model,));
    }

    public function actionEdit($index)
    {
        $model = new ContractForm('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionView($index)
    {
        $model = new ContractForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }


    public function actionSave()
    {
        if (isset($_POST['ContractForm'])) {
            $model = new ContractForm($_POST['ContractForm']['scenario']);
            $model->attributes = $_POST['ContractForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('contract/edit',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    //刪除合同下的某個文檔
    public function actionWordDelete(){
        if(Yii::app()->request->isAjaxRequest) {//是否ajax请求
            $id = $_POST['id'];
            $rs = ContractForm::delContractWordToId($id);
            echo CJSON::encode(array('status'=>$rs));//Yii 的方法将数组处理成json数据
        }else{
            $this->redirect(Yii::app()->createUrl('contract/index'));
        }
    }

    public function actionOrderGoodsDelete(){
        if(Yii::app()->request->isAjaxRequest) {//是否ajax请求
            $id = $_POST['id'];
            $rs = OrderGoods::model()->deleteByPk($id);
            echo CJSON::encode(array('status'=>$rs));//Yii 的方法将数组处理成json数据
        }else{
            $this->redirect(Yii::app()->createUrl('order/index'));
        }
    }

    //刪除合同
    public function actionDelete(){
        $model = new ContractForm('delete');
        if (isset($_POST['ContractForm'])) {
            $model->attributes = $_POST['ContractForm'];
            if($model->validateDelete()){
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
            }else{
                Dialog::message(Yii::t('dialog','Information'), Yii::t('contract','The contract has staff being used, please delete the staff first'));
                $this->redirect(Yii::app()->createUrl('contract/edit',array('index'=>$model->id)));
            }
        }
        $this->redirect(Yii::app()->createUrl('contract/index'));
    }
}