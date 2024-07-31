<?php
$ftrbtn = array();
$ftrbtn[] = TbHtml::button(Yii::t('dialog','Close'), array('id'=>'btnWFClose','data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_PRIMARY));
$this->beginWidget('bootstrap.widgets.TbModal', array(
    'id'=>'workList',
    'header'=>$tableName,
    'footer'=>$ftrbtn,
    'show'=>false,
));
?>

<div class="box" id="flow-list" style="max-height: 300px; overflow-y: auto;">
    <table id="tblFlow" class="table table-bordered table-striped table-hover">
        <thead>
        <tr>
            <th width="15%"><?php echo Yii::t("contract","Employee Code"); ?></th>
            <th width="15%"><?php echo Yii::t("contract","Employee Name"); ?></th>
            <th width="22%"><?php echo Yii::t("contract","Start Time"); ?></th>
            <th width="22%"><?php echo Yii::t("contract","End Time"); ?></th>
            <th width="15%"><?php echo Yii::t("fete","Log Date"); ?></th>
            <th width="10%"><?php echo Yii::t("contract","Status"); ?></th>
        </tr>
        </thead>
        <tbody>

        <?php
        $historyList = $model->getHistoryList();
        if(!empty($historyList)){
            foreach ($historyList as $list){
                echo "<tr>";
                echo "<td>".$list['employee_code']."</td>";
                echo "<td>".$list['employee_name']."</td>";
                echo "<td>".$list['start_time']."</td>";
                echo "<td>".$list['end_time']."</td>";
                echo "<td>".$list['log_time']."</td>";
                echo "<td>".Yii::t("fete","approve")."</td>";
                echo "</tr>";
            }
        }
        ?>
        </tbody>
    </table>
</div>

<?php
$this->endWidget();
?>
