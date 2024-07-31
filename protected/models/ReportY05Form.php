<?php
/* Reimbursement Form */

class ReportY05Form extends CReportForm
{
    public $staffs;
    public $staffs_desc;
	
	protected function labelsEx() {
		return array(
            'staffs'=>Yii::t('report','Staffs'),
        );
	}
	
	protected function rulesEx() {
        return array(
            array('staffs, staffs_desc','safe'),
        );
	}
	
	protected function queueItemEx() {
		return array(
            'STAFFS'=>$this->staffs,
            'STAFFSDESC'=>$this->staffs_desc,
        );
	}
	
	public function init() {
		$this->id = 'RptPennantExList';
		$this->name = Yii::t('app','Pennants ex List');
		$this->format = 'EXCEL';
		$this->city = Yii::app()->user->city();
        $this->fields = 'city,year,month,staffs,staffs_desc';
        $this->year = date("Y");
        $this->month = date("m");
        $this->staffs = '';
        $this->staffs_desc = Yii::t('misc','All');
	}

	public function getYearList(){
        $arr = array();
        for ($i = 2015;$i<=2025;$i++){
            $arr[$i] = $i;
        }
        return $arr;
    }
	public function getMonthList(){
        $arr = array();
        for ($i = 1;$i<=12;$i++){
            $arr[$i] = $i;
        }
        return $arr;
    }
}
