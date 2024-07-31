<?php

/**
 * Created by PhpStorm.
 * User: 考核模板
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class TemplateController extends Controller
{
	public $function_id='RE05';

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
                'actions'=>array('edit','save','new','delete','copy'),
                'expression'=>array('TemplateController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view'),
                'expression'=>array('TemplateController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('RE05');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('RE05');
    }

    public function actionIndex($pageNum=0){
        $model = new TemplateList;
        if (isset($_POST['TemplateList'])) {
            $model->attributes = $_POST['TemplateList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['template_01']) && !empty($session['template_01'])) {
                $criteria = $session['template_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }


    public function actionEdit($index)
    {
        $model = new TemplateForm('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionView($index)
    {
        $model = new TemplateForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }


    public function actionNew()
    {
        $model = new TemplateForm('new');
        $model->city = Yii::app()->user->city_name();
        $this->render('form',array('model'=>$model,));
    }

    public function actionSave()
    {
        if (isset($_POST['TemplateForm'])) {
            $model = new TemplateForm($_POST['TemplateForm']['scenario']);
            $model->attributes = $_POST['TemplateForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('template/edit',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    public function actionDelete()
    {
        $model = new TemplateForm('delete');
        if (isset($_POST['TemplateForm'])) {
            $model->attributes = $_POST['TemplateForm'];
            if($model->deleteValidate()){
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
                $this->redirect(Yii::app()->createUrl('template/index'));
            }else{
                $model->scenario = "edit";
                Dialog::message(Yii::t('dialog','Validation Message'), "范本不存在无法删除");
                $this->render('form',array('model'=>$model));
            }
        }else{
            $this->redirect(Yii::app()->createUrl('template/index'));
        }
    }

    public function actionCopy()
    {
        $copyCity = "CD";//複製成都的模板數據
        $suffix = Yii::app()->params['envSuffix'];
        $rows = Yii::app()->db->createCommand()->select("*")->from("hr_template")
            ->where("city = '$copyCity'")->queryAll();
        $cityRows = Yii::app()->db->createCommand()->select("code,name")->from("security$suffix.sec_city")
            ->where("code != '$copyCity'")->queryAll();
        echo "start:<br>------------------------<br>";
        if($rows&&$cityRows){
            foreach ($rows as $row){
                foreach ($cityRows as $city){
                    $bool = Yii::app()->db->createCommand()->select("*")->from("hr_template")
                        ->where("city = :city and tem_name = :tem_name",array(":city"=>$city["code"],":tem_name"=>$row["tem_name"]))->queryRow();
                    if(!$bool){
                        echo "<br>".$city['name']."---".$row["tem_name"]." ------- finish";
                        $arr = array(
                            "tem_name"=>$row["tem_name"],
                            "city"=>$city["code"],
                            "tem_str"=>$row["tem_str"],
                            "lcu"=>'lbs',
                        );
                        Yii::app()->db->createCommand()->insert('hr_template',$arr);
                    }
                }
            }
        }
        echo "<br>------------------------<br>copy complete";
    }
}