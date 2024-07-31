<tr>
	<th></th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('leave_code').$this->drawOrderArrow('a.leave_code'),'#',$this->createOrderLink('auditLeave-list','a.leave_code'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('lcd').$this->drawOrderArrow('a.lcd'),'#',$this->createOrderLink('auditLeave-list','a.lcd'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('employee_name').$this->drawOrderArrow('b.name'),'#',$this->createOrderLink('auditLeave-list','b.name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('city').$this->drawOrderArrow('b.city'),'#',$this->createOrderLink('auditLeave-list','b.city'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('vacation_id').$this->drawOrderArrow('a.vacation_id'),'#',$this->createOrderLink('auditLeave-list','a.vacation_id'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('start_time').$this->drawOrderArrow('a.start_time'),'#',$this->createOrderLink('auditLeave-list','a.start_time'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('end_time').$this->drawOrderArrow('a.end_time'),'#',$this->createOrderLink('auditLeave-list','a.end_time'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('log_time').$this->drawOrderArrow('a.log_time'),'#',$this->createOrderLink('auditLeave-list','a.log_time'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('status').$this->drawOrderArrow('a.status'),'#',$this->createOrderLink('auditLeave-list','a.status'))
			;
		?>
	</th>
</tr>
