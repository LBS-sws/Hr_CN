<tr>
	<th></th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('code').$this->drawOrderArrow('b.code'),'#',$this->createOrderLink('makeWages-list','b.code'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('name').$this->drawOrderArrow('b.name'),'#',$this->createOrderLink('makeWages-list','b.name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('city').$this->drawOrderArrow('b.city'),'#',$this->createOrderLink('makeWages-list','b.city'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('position').$this->drawOrderArrow('b.position'),'#',$this->createOrderLink('makeWages-list','b.position'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('wages_date').$this->drawOrderArrow('a.wages_date'),'#',$this->createOrderLink('makeWages-list','a.wages_date'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('staff_status').$this->drawOrderArrow('a.wages_status'),'#',$this->createOrderLink('makeWages-list','a.wages_status'))
			;
		?>
	</th>
</tr>
