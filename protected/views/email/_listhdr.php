<tr>
	<th></th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('subject').$this->drawOrderArrow('subject'),'#',$this->createOrderLink('email-list','subject'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('city_str').$this->drawOrderArrow('city_str'),'#',$this->createOrderLink('email-list','city_str'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('staff_str').$this->drawOrderArrow('staff_str'),'#',$this->createOrderLink('email-list','staff_str'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('status_type').$this->drawOrderArrow('status_type'),'#',$this->createOrderLink('email-list','status_type'))
			;
		?>
	</th>
</tr>
