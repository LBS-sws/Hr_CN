<tr>
	<th></th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('appoint_code').$this->drawOrderArrow('a.appoint_code'),'#',$this->createOrderLink('appointTripSet-list','a.appoint_code'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('employee_name').$this->drawOrderArrow('b.name'),'#',$this->createOrderLink('appointTripSet-list','b.name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('city_name').$this->drawOrderArrow('f.name'),'#',$this->createOrderLink('appointTripSet-list','f.name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('audit_user_str').$this->drawOrderArrow('a.audit_user_str'),'#',$this->createOrderLink('appointTripSet-list','a.audit_user_str'))
			;
		?>
	</th>
</tr>
