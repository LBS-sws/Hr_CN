<tr>
	<th></th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('employee_code').$this->drawOrderArrow('b.code'),'#',$this->createOrderLink('groupStaff-list','b.code'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('employee_name').$this->drawOrderArrow('b.name'),'#',$this->createOrderLink('groupStaff-list','b.name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('branch_text').$this->drawOrderArrow('a.branch_text'),'#',$this->createOrderLink('groupStaff-list','a.branch_text'))
			;
		?>
	</th>
</tr>
