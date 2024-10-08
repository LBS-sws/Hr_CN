<tr>
	<td>
        <?php
        echo TbHtml::textField($this->getFieldName('employeeName'), $this->record['employeeName'],
            array('readonly'=>true,'class'=>'employeeName',
                'append'=>TbHtml::button('<span class="fa fa-search"></span> '.Yii::t('contract','Employee'),array('class'=>'searchUser','disabled'=>(!Yii::app()->user->validRWFunction('ZC24')))),
            ));
        ?>
        <?php echo TbHtml::hiddenField($this->getFieldName('employeeID'), $this->record['employeeID'],array("class"=>"employeeID")); ?>
	</td>
	<td>
		<?php 
			echo Yii::app()->user->validRWFunction('ZC24')
				? TbHtml::Button('-',array('id'=>'btnDelRow','title'=>Yii::t('misc','Delete'),'size'=>TbHtml::BUTTON_SIZE_SMALL))
				: '&nbsp;';
		?>
		<?php echo CHtml::hiddenField($this->getFieldName('uflag'),$this->record['uflag']); ?>
		<?php echo CHtml::hiddenField($this->getFieldName('id'),$this->record['id']); ?>
	</td>
</tr>
