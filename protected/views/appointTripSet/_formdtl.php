<tr>
	<td>
		<?php echo TbHtml::dropDownList($this->getFieldName('audit_user'),  $this->record['audit_user'],AppointTripSetForm::getAppointAuditUserList($this->record['audit_user']),
								array('disabled'=>$this->model->scenario=='view','empty'=>'')
		); ?>
	</td>
	<td>
		<?php echo TbHtml::numberField($this->getFieldName('z_index'),  $this->record['z_index'],
								array('disabled'=>$this->model->scenario=='view')
		); ?>
	</td>
	<td>
		<?php 
			echo Yii::app()->user->validRWFunction('ZC19')
				? TbHtml::Button('-',array('id'=>'btnDelRow','title'=>Yii::t('misc','Delete'),'size'=>TbHtml::BUTTON_SIZE_SMALL))
				: '&nbsp;';
		?>
		<?php echo CHtml::hiddenField($this->getFieldName('uflag'),$this->record['uflag']); ?>
		<?php echo CHtml::hiddenField($this->getFieldName('id'),$this->record['id']); ?>
		<?php echo CHtml::hiddenField($this->getFieldName('appoint_id'),$this->record['appoint_id']); ?>
	</td>
</tr>
