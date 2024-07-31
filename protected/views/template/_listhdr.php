<tr>
	<th></th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('tem_name').$this->drawOrderArrow('a.tem_name'),'#',$this->createOrderLink('template-list','a.tem_name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('city').$this->drawOrderArrow('b.name'),'#',$this->createOrderLink('template-list','b.name'))
			;
		?>
	</th>
</tr>
