<tr>
	<th></th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('id').$this->drawOrderArrow('a.id'),'#',$this->createOrderLink('reviewSetPro-list','a.id'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('set_name').$this->drawOrderArrow('b.set_name'),'#',$this->createOrderLink('reviewSetPro-list','b.set_name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('pro_name').$this->drawOrderArrow('a.pro_name'),'#',$this->createOrderLink('reviewSetPro-list','a.pro_name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('z_index').$this->drawOrderArrow('a.z_index'),'#',$this->createOrderLink('reviewSetPro-list','a.z_index'))
			;
		?>
	</th>
</tr>
