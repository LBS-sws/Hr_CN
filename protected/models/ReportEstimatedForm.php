<?php
/* Reimbursement Form */

class ReportEstimatedForm extends CReportForm
{

    protected function labelsEx() {
        return array(
            'year'=>Yii::t('contract',' year'),
        );
    }

    protected function rulesEx() {
        return array(
            array('year','safe'),
        );
    }

    protected function queueItemEx() {
        return array(
            'YEAR'=>$this->year,
            'CITY'=>$this->city,
        );
    }

    public function init() {
        $this->id = 'RptEstimatedList';
        $this->name = Yii::t('app','Estimated statement report');
        $this->format = 'EXCEL';
        $this->city = Yii::app()->user->city();
        $this->fields = 'year';
        $this->year = date("Y");
    }

    public static function getYearList() {
        $list = array();
        $rows = Yii::app()->db->createCommand()->select("audit_year")->from("hr_boss_audit")
            ->group("audit_year")->queryAll();
        if ($rows) {
            foreach ($rows as $row) {
                $list[$row['audit_year']] = $row['audit_year'].Yii::t('contract',' year');
            }
        }
        return $list;
    }
}
