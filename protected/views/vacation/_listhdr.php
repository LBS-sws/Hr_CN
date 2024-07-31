<tr>
	<th></th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('name').$this->drawOrderArrow('name'),'#',$this->createOrderLink('vacation-list','name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('city').$this->drawOrderArrow('city'),'#',$this->createOrderLink('vacation-list','city'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('ass_bool').$this->drawOrderArrow('ass_bool'),'#',$this->createOrderLink('vacation-list','ass_bool'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('ass_id_name').$this->drawOrderArrow('ass_id_name'),'#',$this->createOrderLink('vacation-list','ass_id_name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('only').$this->drawOrderArrow('only'),'#',$this->createOrderLink('vacation-list','only'))
			;
		?>
	</th>
</tr>
