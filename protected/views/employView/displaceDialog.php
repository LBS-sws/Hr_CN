<?php
	$ftrbtn = array();
	$ftrbtn[] = TbHtml::button(Yii::t('dialog','Close'), array('data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_DEFAULT));
	$ftrbtn[] = TbHtml::button(Yii::t('contract','displace'), array('color'=>TbHtml::BUTTON_COLOR_PRIMARY,
        'submit'=>Yii::app()->createUrl('employee/displace',array("index"=>$model->id))));
	$this->beginWidget('bootstrap.widgets.TbModal', array(
					'id'=>'displaceDialog',
					'header'=>Yii::t('contract','displace'),
					'footer'=>$ftrbtn,
					'show'=>false,
				));
?>

<div class="form-group">
    <?php echo TbHtml::label(Yii::t("contract","Employee Type"),"",array('class'=>"col-sm-4 control-label"));?>
    <!--分割-->
    <div class="col-sm-5">
        <?php
        echo TbHtml::dropDownList("table_type","",StaffFun::getTableTypeList());
        ?>
    </div>
</div>
<?php
	$this->endWidget();
?>
