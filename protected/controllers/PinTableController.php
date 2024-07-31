<?php

/**
 * Created by PhpStorm.
 * User: 城市等級設置
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class PinTableController extends Controller
{
	public $function_id='PI05';

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
                'actions'=>array('index'),
                'expression'=>array('PinTableController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index'),
                'expression'=>array('PinTableController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('PI05');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('PI05');
    }

    public function actionIndex($city='')
    {
        $model = new PinTableForm('edit');
        if (!$model->retrieveData($city)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

}