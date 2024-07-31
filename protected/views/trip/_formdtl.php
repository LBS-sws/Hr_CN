<tr>
	<td>
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
            <?php echo TbHtml::textField($this->getFieldName('start_time'), $this->record['start_time'],
                array('readonly'=>$this->model->ready(),'autocomplete'=>'off','class'=>'start_time'
                )); ?>
            <div class="input-group-btn">
                <?php echo TbHtml::dropDownList($this->getFieldName('start_time_lg'),  $this->record['start_time_lg'], LeaveForm::getAMPMList(),
                    array('readonly'=>$this->model->ready(),'class'=>'start_time_lg')
                ); ?>
            </div>
        </div>
	</td>
	<td>
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
            <?php echo TbHtml::textField($this->getFieldName('end_time'), $this->record['end_time'],
                array('readonly'=>$this->model->ready(),'autocomplete'=>'off','class'=>'end_time'
                )); ?>
            <div class="input-group-btn">
                <?php echo TbHtml::dropDownList($this->getFieldName('end_time_lg'),  $this->record['end_time_lg'], LeaveForm::getAMPMList(),
                    array('readonly'=>$this->model->ready(),'class'=>'end_time_lg')
                ); ?>
            </div>
        </div>
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
