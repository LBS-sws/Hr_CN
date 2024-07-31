<?php

/**
 * Created by PhpStorm.
 * User: 老總年度考核
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class BossSearchController extends Controller
{
	public $function_id='BA02';

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
                'actions'=>array('edit'),
                'expression'=>array('BossSearchController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view','resetSearch'),
                'expression'=>array('BossSearchController','allowReadOnly'),
            ),
            array('allow',
                'actions'=>array('back'),
                'expression'=>array('BossSearchController','allowBack'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('BA02');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('BA02');
    }

    public static function allowBack() {
        return Yii::app()->user->validFunction('ZR16');
    }

    public function actionResetSearch($year=2022){
        $year = is_numeric($year)?intval($year):2022;
        echo "Year:{$year}<br/>";
        $rows = Yii::app()->db->createCommand()->select("a.id,a.json_listX,a.ratio_a,a.ratio_b,a.ratio_c,a.id,a.results_a,a.results_b,a.results_c,a.status_type,a.city,a.audit_year,a.employee_id,a.lcu,a.json_text,b.code as employee_code,b.name as employee_name")
            ->from("hr_boss_audit a")
            ->leftJoin("hr_employee b","a.employee_id = b.id")
            ->where("a.status_type =2 and a.audit_year={$year}")->queryAll();
        if($rows){
            $model = new BossSearchForm();
            foreach ($rows as $row){
                echo "audit_id:{$row["id"]} city:{$row["city"]}  employee_id:{$row["employee_id"]} employee_name:{$row["employee_name"]}<br/>";
                $model->json_text = json_decode($row['json_text'],true);
                $model->lcu = $row['lcu'];
                $model->employee_id = $row['employee_id'];
                $model->audit_year = $row['audit_year'];
                $model->city = $row['city'];
                $model->ratio_a = $row['ratio_a'];
                $model->ratio_b = $row['ratio_b'];
                $model->ratio_c = $row['ratio_c'];
                $model->status_type = $row['status_type'];
                $model->results_c = floatval($row["results_c"]);
                //A類驗證
                $bossReviewA = new BossReviewA($model);
                if(!empty($row["json_listX"])){
                    $bossReviewA->resetListX(json_decode($row["json_listX"],true));
                }
                $bossReviewA->validateJson($model);
                $model->json_text = $bossReviewA->json_text;
                $model->results_a = $bossReviewA->scoreSum;
                //B類驗證
                $bossReviewB = new BossReviewB($model);
                if(!empty($row["json_listX"])){
                    $bossReviewB->resetListX(json_decode($row["json_listX"],true));
                }
                $bossReviewB->validateJson($model);
                $model->json_text = $bossReviewB->json_text;
                $model->results_b = $bossReviewB->scoreSum;

                $bossRewardType = BossApplyForm::getBossRewardType($row['city']);
                $ratio_a = $model->ratio_a*0.01;
                $ratio_b = $model->ratio_b*0.01;
                if($bossRewardType == 1){
                    $model->results_sum = $model->results_a*$ratio_a+$model->results_b*$ratio_b;
                }else{
                    $model->results_sum = $model->results_a*$ratio_a+$model->results_b*$ratio_b+$model->results_c;
                }

                Yii::app()->db->createCommand()->update('hr_boss_audit', array(
                    'results_a'=>$model->results_a,
                    'results_b'=>$model->results_b,
                    'results_sum'=>$model->results_sum,
                    'json_text'=>json_encode($model->json_text),
                ), 'id=:id', array(':id'=>$row['id']));
            }
        }
        Yii::app()->end();
    }

    public function actionIndex($pageNum=0){
        $model = new BossSearchList;
        if (isset($_POST['BossSearchList'])) {
            $model->attributes = $_POST['BossSearchList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['bossSearch_01']) && !empty($session['bossSearch_01'])) {
                $criteria = $session['bossSearch_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }

    public function actionEdit($index)
    {
        $model = new BossSearchForm('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionView($index)
    {
        $model = new BossSearchForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionBack()
    {
        $model = new BossSearchForm('back');
        $model->attributes = $_POST['BossSearchForm'];
        if ($model->validate()) {
            $model->saveData();
            Dialog::message(Yii::t('dialog','Information'), Yii::t('contract','finish to send back'));
            $this->redirect(Yii::app()->createUrl('bossSearch/index'));
        } else {
            $message = CHtml::errorSummary($model);
            Dialog::message(Yii::t('dialog','Validation Message'), $message);
            $this->redirect(Yii::app()->createUrl('bossSearch/edit',array('index'=>$model->id)));
        }
    }
}