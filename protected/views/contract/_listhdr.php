<tr>
	<th></th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('name').$this->drawOrderArrow('name'),'#',$this->createOrderLink('contract-list','name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('city').$this->drawOrderArrow('city'),'#',$this->createOrderLink('contract-list','city'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('retire').$this->drawOrderArrow('retire'),'#',$this->createOrderLink('contract-list','retire'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('local_type').$this->drawOrderArrow('local_type'),'#',$this->createOrderLink('contract-list','local_type'))
			;
		?>
	</th>
</tr>
