<tr>
	<th></th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('name').$this->drawOrderArrow('a.name'),'#',$this->createOrderLink('pinName-list','a.name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('class_id').$this->drawOrderArrow('b.name'),'#',$this->createOrderLink('pinName-list','b.name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('pin_type').$this->drawOrderArrow('a.pin_type'),'#',$this->createOrderLink('pinName-list','a.pin_type'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('z_index').$this->drawOrderArrow('a.z_index'),'#',$this->createOrderLink('pinName-list','a.z_index'))
			;
		?>
	</th>
</tr>
