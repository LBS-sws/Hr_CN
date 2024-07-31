<tr>
	<th width="50%">
		<?php echo TbHtml::label($this->getLabelName('start_time'), false); ?>
	</th>
	<th width="50%">
		<?php echo TbHtml::label($this->getLabelName('end_time'), false); ?>
	</th>
	<th>
		<?php echo !$this->model->ready() ?
				TbHtml::Button('+',array('id'=>'btnAddRow','class'=>'btnAddRow','title'=>Yii::t('misc','Add'),'size'=>TbHtml::BUTTON_SIZE_SMALL))
				: '&nbsp;';
		?>
	</th>
</tr>
