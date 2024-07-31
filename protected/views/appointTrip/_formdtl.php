<tr>
	<td>
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
            <?php echo TbHtml::textField($this->getFieldName('start_time'), $this->record['start_time'],
                array('readonly'=>true,'class'=>'start_time'
                )); ?>
            <div class="input-group-btn">
                <?php echo TbHtml::dropDownList($this->getFieldName('start_time_lg'),  $this->record['start_time_lg'], LeaveForm::getAMPMList(),
                    array('readonly'=>true,'class'=>'start_time_lg')
                ); ?>
            </div>
        </div>
	</td>
	<td>
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
            <?php echo TbHtml::textField($this->getFieldName('end_time'), $this->record['end_time'],
                array('readonly'=>true,'class'=>'end_time'
                )); ?>
            <div class="input-group-btn">
                <?php echo TbHtml::dropDownList($this->getFieldName('end_time_lg'),  $this->record['end_time_lg'], LeaveForm::getAMPMList(),
                    array('readonly'=>true,'class'=>'end_time_lg')
                ); ?>
            </div>
        </div>
	</td>
</tr>
