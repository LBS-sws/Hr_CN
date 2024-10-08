<tr>
	<th></th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('group_code').$this->drawOrderArrow('group_code'),'#',$this->createOrderLink('groupName-list','group_code'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('group_name').$this->drawOrderArrow('group_name'),'#',$this->createOrderLink('groupName-list','group_name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('group_remark').$this->drawOrderArrow('group_remark'),'#',$this->createOrderLink('groupName-list','group_remark'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('group_sum').$this->drawOrderArrow('group_sum'),'#',$this->createOrderLink('groupName-list','group_sum'))
			;
		?>
	</th>

    <?php if (Yii::app()->user->validFunction('ZC24')): ?>
        <th width="1%"></th>
    <?php endif; ?>
</tr>
