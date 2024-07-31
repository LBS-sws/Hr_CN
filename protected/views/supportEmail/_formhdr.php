<tr>
	<th>
		<?php echo TbHtml::label($this->getLabelName('support_city'), false); ?>
	</th>
	<th>
		<?php echo TbHtml::label($this->getLabelName('wage_city'), false); ?>
	</th>
	<th>
		<?php echo TbHtml::label($this->getLabelName('start_date'), false); ?>
	</th>
	<th>
		<?php echo TbHtml::label($this->getLabelName('end_date'), false); ?>
	</th>
    <th>
        <?php echo Yii::app()->user->validRWFunction('AY05') ?
            TbHtml::Button('+',array('id'=>'btnAddRow','title'=>Yii::t('misc','Add'),'size'=>TbHtml::BUTTON_SIZE_SMALL))
            : '&nbsp;';
        ?>
    </th>
</tr>
