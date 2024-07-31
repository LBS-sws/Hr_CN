<tr>
	<th></th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('code').$this->drawOrderArrow('b.code'),'#',$this->createOrderLink('auditWages-list','b.code'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('name').$this->drawOrderArrow('b.name'),'#',$this->createOrderLink('auditWages-list','b.name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('city').$this->drawOrderArrow('b.city'),'#',$this->createOrderLink('auditWages-list','b.city'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('position').$this->drawOrderArrow('b.position'),'#',$this->createOrderLink('auditWages-list','b.position'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('wages_date').$this->drawOrderArrow('a.wages_date'),'#',$this->createOrderLink('auditWages-list','a.wages_date'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('wages_arr').$this->drawOrderArrow('a.wages_arr'),'#',$this->createOrderLink('auditWages-list','a.wages_arr'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('staff_status').$this->drawOrderArrow('staff_status'),'#',$this->createOrderLink('auditWages-list','staff_status'))
			;
		?>
	</th>
</tr>
