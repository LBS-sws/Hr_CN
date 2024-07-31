<tr>
	<th></th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('pin_code').$this->drawOrderArrow('a.pin_code'),'#',$this->createOrderLink('pinApply-list','a.pin_code'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('apply_date').$this->drawOrderArrow('a.apply_date'),'#',$this->createOrderLink('pinApply-list','a.apply_date'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('employee_id').$this->drawOrderArrow('b.name'),'#',$this->createOrderLink('pinApply-list','b.name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('position').$this->drawOrderArrow('p.name'),'#',$this->createOrderLink('pinApply-list','p.name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('city').$this->drawOrderArrow('h.name'),'#',$this->createOrderLink('pinApply-list','h.name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('name_id').$this->drawOrderArrow('g.name'),'#',$this->createOrderLink('pinApply-list','g.name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('pin_num').$this->drawOrderArrow('a.pin_num'),'#',$this->createOrderLink('pinApply-list','a.pin_num'))
			;
		?>
	</th>
</tr>
