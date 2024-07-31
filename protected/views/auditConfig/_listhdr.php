<tr>
	<th></th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('city_name').$this->drawOrderArrow('b.code'),'#',$this->createOrderLink('auditConfig-list','b.code'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('audit_index').$this->drawOrderArrow('a.audit_index'),'#',$this->createOrderLink('auditConfig-list','a.audit_index'))
			;
		?>
	</th>
</tr>
