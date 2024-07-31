<?php
	$content = "<p>".Yii::t('treaty','Are you sure to black?')."</p>";
	$this->widget('bootstrap.widgets.TbModal', array(
					'id'=>'blackDialog',
					'header'=>Yii::t('treaty','black'),
					'content'=>$content,
					'footer'=>array(
						TbHtml::button(Yii::t('dialog','OK'), array('id'=>'btnBlackData','data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_PRIMARY)),
						TbHtml::button(Yii::t('dialog','Cancel'), array('data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_PRIMARY)),
					),
					'show'=>false,
				));
?>