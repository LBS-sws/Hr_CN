<?php

class HeartLetterRankController extends Controller
{
	public $function_id='HL04';

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
                'expression'=>array('HeartLetterRankController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }
    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('HL04');
    }

	public function actionIndex($pageNum=0) 
	{
		$model = new HeartLetterRankList();
        $model->year = date("Y");
		if (isset($_POST['HeartLetterRankList'])) {
			$model->attributes = $_POST['HeartLetterRankList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['heartLetterRank_op01']) && !empty($session['heartLetterRank_op01'])) {
				$criteria = $session['heartLetterRank_op01'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}
}
