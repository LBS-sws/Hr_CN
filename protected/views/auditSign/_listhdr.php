<tr>
	<th></th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('code').$this->drawOrderArrow('b.code'),'#',$this->createOrderLink('auditSign-list','b.code'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('name').$this->drawOrderArrow('b.name'),'#',$this->createOrderLink('auditSign-list','b.name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('city').$this->drawOrderArrow('b.city'),'#',$this->createOrderLink('auditSign-list','b.city'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('sign_type').$this->drawOrderArrow('a.sign_type'),'#',$this->createOrderLink('auditSign-list','a.sign_type'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('position').$this->drawOrderArrow('b.position'),'#',$this->createOrderLink('auditSign-list','b.position'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('company_id').$this->drawOrderArrow('b.company_id'),'#',$this->createOrderLink('auditSign-list','b.company_id'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('entry_time').$this->drawOrderArrow('b.entry_time'),'#',$this->createOrderLink('auditSign-list','b.entry_time'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('courier_str').$this->drawOrderArrow('a.courier_str'),'#',$this->createOrderLink('auditSign-list','a.courier_str'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('courier_code').$this->drawOrderArrow('a.courier_code'),'#',$this->createOrderLink('auditSign-list','a.courier_code'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('status_type').$this->drawOrderArrow('a.status_type'),'#',$this->createOrderLink('auditSign-list','a.status_type'))
			;
		?>
	</th>
</tr>
