<tr>
	<th></th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('code').$this->drawOrderArrow('a.code'),'#',$this->createOrderLink('oldContract-list','a.code'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('name').$this->drawOrderArrow('a.name'),'#',$this->createOrderLink('oldContract-list','a.name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('city').$this->drawOrderArrow('a.city'),'#',$this->createOrderLink('oldContract-list','a.city'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('phone').$this->drawOrderArrow('a.phone'),'#',$this->createOrderLink('oldContract-list','a.phone'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('position').$this->drawOrderArrow('a.position'),'#',$this->createOrderLink('oldContract-list','a.position'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('entry_time').$this->drawOrderArrow('a.entry_time'),'#',$this->createOrderLink('oldContract-list','a.entry_time'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('company_id').$this->drawOrderArrow('a.company_id'),'#',$this->createOrderLink('oldContract-list','a.company_id'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('status').$this->drawOrderArrow('b.old_type'),'#',$this->createOrderLink('oldContract-list','b.old_type'))
			;
		?>
	</th>
</tr>
