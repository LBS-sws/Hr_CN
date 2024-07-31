<?php
/* Reimbursement Form */

class ReportY01Form extends CReportForm
{
	public $region;
	public $search_start;
	public $search_end;

	protected function labelsEx() {
        return array(
            'region'=>Yii::t('report','Region'),
            'search_start'=>Yii::t('report','Start Date'),
            'search_end'=>Yii::t('report','End Date'),
        );
	}
	
	protected function rulesEx() {
        return array(
            array('region,search_start,search_end','safe'),
            array('search_start','validateDate'),
        );
	}

    public function validateDate($attribute, $params){
	    $startDate = $this->search_start;
	    $endDate = $this->search_end;
	    if($startDate>$endDate){
            $message = "开始日期不能大于结束日期";
            $this->addError($attribute,$message);
        }
    }
	
	protected function queueItemEx() {
        return array(
            'REGION'=>$this->region,
            'SEARCHS'=>$this->search_start,
            'SEARCHE'=>$this->search_end,
        );
	}
	
	public function init() {
		$this->id = 'RptStaffExList';
		$this->name = Yii::t('report','Staff Rpt List');
		$this->format = 'EXCEL';
		$this->city = Yii::app()->user->city();
		$this->fields = 'city';
		$this->search_start = date("Y/01");
		$this->search_end = date("Y/m");
	}
}
