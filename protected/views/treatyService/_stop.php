<?php
	$content = "<p>".Yii::t('treaty','Are you sure to stop?')."</p>";
	$this->widget('bootstrap.widgets.TbModal', array(
					'id'=>'stopDialog',
					'header'=>Yii::t('treaty','stop'),
					'content'=>$content,
					'footer'=>array(
						TbHtml::button(Yii::t('dialog','OK'), array('id'=>'btnStopData','data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_PRIMARY)),
						TbHtml::button(Yii::t('dialog','Cancel'), array('data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_PRIMARY)),
					),
					'show'=>false,
				));
?>