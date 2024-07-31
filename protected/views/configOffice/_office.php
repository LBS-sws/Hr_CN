<?php
$name = isset($name)?"（{$name}）":"";
	$ftrbtn = array();
	$ftrbtn[] = TbHtml::button(Yii::t('dialog','Close'), array('data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_PRIMARY));
	$this->beginWidget('bootstrap.widgets.TbModal', array(
					'id'=>'officeModel',
					'header'=>Yii::t('contract','office employee list')."<small id='office_title'>{$name}</small>",
					'footer'=>$ftrbtn,
					'size'=>TbHtml::MODAL_SIZE_LARGE,
					'show'=>false,
				));
?>

<div class="box" style="max-height: 300px; overflow-y: auto;">
	<table class="table table-bordered table-striped table-hover">
		<thead>
			<tr>
                <th><?php echo Yii::t("contract","Employee Code"); ?></th>
                <th><?php echo Yii::t("contract","Employee Name"); ?></th>
                <th><?php echo Yii::t("contract","City"); ?></th>
                <th><?php echo Yii::t("contract","Employee Phone"); ?></th>
                <th><?php echo Yii::t("contract","Position"); ?></th>
                <th><?php echo Yii::t("contract","Entry Time"); ?></th>
			</tr>
		</thead>
		<tbody id="office_body">
        <?php
        $id = isset($id)?$id:0;
        echo ConfigOfficeForm::getOfficeStaffToHtml($id);
        ?>
		</tbody>
	</table>
</div>

<?php
	$this->endWidget();
?>
