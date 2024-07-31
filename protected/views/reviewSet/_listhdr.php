<tr>
	<th></th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('id').$this->drawOrderArrow('a.id'),'#',$this->createOrderLink('reviewSet-list','a.id'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('set_name').$this->drawOrderArrow('a.set_name'),'#',$this->createOrderLink('reviewSet-list','a.set_name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('four_with').$this->drawOrderArrow('a.four_with'),'#',$this->createOrderLink('reviewSet-list','a.four_with'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('num_ratio').$this->drawOrderArrow('a.num_ratio'),'#',$this->createOrderLink('reviewSet-list','a.num_ratio'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('z_index').$this->drawOrderArrow('a.z_index'),'#',$this->createOrderLink('reviewSet-list','a.z_index'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('pro_num').$this->drawOrderArrow('pro_num'),'#',$this->createOrderLink('reviewSet-list','pro_num'))
			;
		?>
	</th>
	<th width="1%"></th>
</tr>
