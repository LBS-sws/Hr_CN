<tr>
	<th></th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('name').$this->drawOrderArrow('name'),'#',$this->createOrderLink('fete-list','name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('city').$this->drawOrderArrow('city'),'#',$this->createOrderLink('fete-list','city'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('start_time').$this->drawOrderArrow('start_time'),'#',$this->createOrderLink('fete-list','start_time'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('end_time').$this->drawOrderArrow('end_time'),'#',$this->createOrderLink('fete-list','end_time'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('only').$this->drawOrderArrow('only'),'#',$this->createOrderLink('fete-list','only'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('cost_num').$this->drawOrderArrow('cost_num'),'#',$this->createOrderLink('fete-list','cost_num'))
			;
		?>
	</th>
</tr>
