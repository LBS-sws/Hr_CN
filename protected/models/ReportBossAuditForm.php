<?php
/* Reimbursement Form */

class ReportBossAuditForm extends CReportForm
{

    protected function labelsEx() {
        return array(
            'year'=>Yii::t('contract',' year'),
            'month'=>Yii::t('report','Month'),
        );
    }

    protected function rulesEx() {
        return array(
            array('year,month','safe'),
        );
    }

    protected function queueItemEx() {
        return array(
            'MONTH'=>$this->month,
            'YEAR'=>$this->year,
            'CITY'=>$this->city,
        );
    }

    public function init() {
        $this->id = 'RptBossPlanList';
        $this->name = Yii::t('app','Boss Audit Plan Report');
        $this->format = 'EXCEL';
        $this->city = Yii::app()->user->city();
        $this->fields = 'year,month';
        $this->year = date("Y");
        $this->month = date("n");
    }

    public static function getMonthList() {
        $list = array();
        for($i=1;$i<=12;$i++){
            $list[$i] = $i.Yii::t('report','Month');
        }
        return $list;
    }
}
