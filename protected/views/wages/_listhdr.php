<tr>
	<th></th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('wages_name').$this->drawOrderArrow('wages_name'),'#',$this->createOrderLink('wages-list','wages_name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('city').$this->drawOrderArrow('city'),'#',$this->createOrderLink('wages-list','city'))
			;
		?>
	</th>
</tr>
