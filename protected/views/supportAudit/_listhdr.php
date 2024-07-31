<tr>
	<th><input type="checkbox" class="selectAll" id="selectAll"></th>
	<th></th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('support_code').$this->drawOrderArrow('a.support_code'),'#',$this->createOrderLink('supportAudit-list','a.support_code'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('apply_type').$this->drawOrderArrow('a.apply_type'),'#',$this->createOrderLink('supportAudit-list','a.apply_type'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('service_type').$this->drawOrderArrow('a.service_type'),'#',$this->createOrderLink('supportAudit-list','a.service_type'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('privilege').$this->drawOrderArrow('a.privilege'),'#',$this->createOrderLink('supportAudit-list','a.privilege'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('apply_city').$this->drawOrderArrow('c.name'),'#',$this->createOrderLink('supportAudit-list','c.name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('apply_date').$this->drawOrderArrow('a.apply_date'),'#',$this->createOrderLink('supportAudit-list','a.apply_date'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('apply_end_date').$this->drawOrderArrow('a.apply_end_date'),'#',$this->createOrderLink('supportAudit-list','a.apply_end_date'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('employee_id').$this->drawOrderArrow('b.name'),'#',$this->createOrderLink('supportAudit-list','b.name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('dept_name').$this->drawOrderArrow('e.name'),'#',$this->createOrderLink('supportAudit-list','e.name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('review_sum').$this->drawOrderArrow('a.review_sum'),'#',$this->createOrderLink('supportAudit-list','a.review_sum'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('status_type').$this->drawOrderArrow('a.status_type'),'#',$this->createOrderLink('supportAudit-list','a.status_type'))
			;
		?>
	</th>
</tr>
