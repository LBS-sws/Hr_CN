<tr>
	<th>
		<?php echo TbHtml::link($this->getLabelName('city_name').$this->drawOrderArrow('f.name'),'#',$this->createOrderLink('customer-enq','f.name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('code').$this->drawOrderArrow('a.code'),'#',$this->createOrderLink('customer-enq','a.code'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('name').$this->drawOrderArrow('a.name'),'#',$this->createOrderLink('customer-enq','a.name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('phone').$this->drawOrderArrow('a.phone'),'#',$this->createOrderLink('customer-enq','a.phone'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('address').$this->drawOrderArrow('a.address'),'#',$this->createOrderLink('customer-enq','a.address'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('entry_time').$this->drawOrderArrow('a.entry_time'),'#',$this->createOrderLink('customer-enq','a.entry_time'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('department').$this->drawOrderArrow('b.name'),'#',$this->createOrderLink('customer-enq','b.name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('position').$this->drawOrderArrow('c.name'),'#',$this->createOrderLink('customer-enq','c.name'))
			;
		?>
	</th>
</tr>
