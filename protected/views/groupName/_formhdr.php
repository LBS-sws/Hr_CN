<tr>
    <th width="90%">
        <?php echo TbHtml::label($this->getLabelName('employeeID'), false); ?>
    </th>
	<th>
		<?php echo Yii::app()->user->validRWFunction('ZC24') ?
				TbHtml::Button('+',array('id'=>'btnAddRow','title'=>Yii::t('misc','Add'),'size'=>TbHtml::BUTTON_SIZE_SMALL))
				: '&nbsp;';
		?>
	</th>
</tr>
