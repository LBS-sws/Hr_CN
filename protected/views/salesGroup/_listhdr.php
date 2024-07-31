
<tr>
	<th></th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('group_name').$this->drawOrderArrow('a.group_name'),'#',$this->createOrderLink('salesGroup-list','a.group_name'))
			;
		?>
	</th>
	<th>
		<?php
        echo TbHtml::link($this->getLabelName('staff_num'),'javascript:void(0);');
		?>
	</th>
    <?php if (Yii::app()->user->validFunction('ZR14')): ?>
	<th width="1%"></th>
    <?php endif; ?>
</tr>
