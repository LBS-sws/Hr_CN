<tr>
    <td>
        <?php echo TbHtml::dropDownList($this->getFieldName('support_city'), $this->record['support_city'], SupportEmailForm::getCityList(),
            array('disabled'=>!Yii::app()->user->validRWFunction('AY05'))
        ); ?>
    </td>
    <td>
        <?php echo TbHtml::dropDownList($this->getFieldName('wage_city'), $this->record['wage_city'], SupportEmailForm::getCityList(),
            array('disabled'=>!Yii::app()->user->validRWFunction('AY05'))
        ); ?>
    </td>
	<td>
		<?php echo TbHtml::textField($this->getFieldName('start_date'), $this->record['start_date'],
			array('readonly'=>!Yii::app()->user->validRWFunction('AY05'),
				'size'=>'10', 'maxlength'=>'10','class'=>'deadline',
				'prepend'=>'<i class="fa fa-calendar"></i>',
		)); ?>
	</td>
	<td>
		<?php echo TbHtml::textField($this->getFieldName('end_date'), $this->record['end_date'],
			array('readonly'=>!Yii::app()->user->validRWFunction('AY05'),
				'size'=>'10', 'maxlength'=>'10','class'=>'deadline',
				'prepend'=>'<i class="fa fa-calendar"></i>',
		)); ?>
	</td>
	<td>
		<?php 
			echo Yii::app()->user->validRWFunction('AY05')
				? TbHtml::Button('-',array('id'=>'btnDelRow','title'=>Yii::t('misc','Delete'),'size'=>TbHtml::BUTTON_SIZE_SMALL))
				: '&nbsp;';
		?>
		<?php echo CHtml::hiddenField($this->getFieldName('uflag'),$this->record['uflag']); ?>
		<?php echo CHtml::hiddenField($this->getFieldName('id'),$this->record['id']); ?>
	</td>
</tr>
