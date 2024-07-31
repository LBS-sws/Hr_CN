<tr>
	<td>
        <?php echo TbHtml::dropDownList($this->getFieldName('money_set_id'), $this->record['money_set_id'],
            TripMoneySetForm::getTripMoneySetList($this->record['money_set_id']),
            array('readonly'=>$this->model->ready(),'autocomplete'=>'off','class'=>'money_set_id'
            )); ?>
	</td>
	<td>
        <?php echo TbHtml::numberField($this->getFieldName('trip_money'), $this->record['trip_money'],
            array('readonly'=>$this->model->ready(),'min'=>0,'autocomplete'=>'off','class'=>'trip_money'
            )); ?>
	</td>
	<td>
		<?php 
			echo !$this->model->ready()
				? TbHtml::Button('-',array('id'=>'btnDelRow','class'=>'btnDelRow','title'=>Yii::t('misc','Delete'),'size'=>TbHtml::BUTTON_SIZE_SMALL))
				: '&nbsp;';
		?>
		<?php echo CHtml::hiddenField($this->getFieldName('uflag'),$this->record['uflag']); ?>
		<?php echo CHtml::hiddenField($this->getFieldName('trip_id'),$this->record['trip_id']); ?>
		<?php echo CHtml::hiddenField($this->getFieldName('id'),$this->record['id']); ?>
	</td>
</tr>
