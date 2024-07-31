<tr>
	<th></th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('code').$this->drawOrderArrow('b.code'),'#',$this->createOrderLink('bossAudit-list','b.code'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('name').$this->drawOrderArrow('b.name'),'#',$this->createOrderLink('bossAudit-list','a.name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('city_name').$this->drawOrderArrow('d.name'),'#',$this->createOrderLink('bossAudit-list','d.name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('audit_year').$this->drawOrderArrow('a.audit_year'),'#',$this->createOrderLink('bossAudit-list','a.audit_year'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('results_a').$this->drawOrderArrow('a.results_a'),'#',$this->createOrderLink('bossAudit-list','a.results_a'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('results_b').$this->drawOrderArrow('a.results_b'),'#',$this->createOrderLink('bossAudit-list','a.results_b'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('results_c').$this->drawOrderArrow('a.results_c'),'#',$this->createOrderLink('bossAudit-list','a.results_c'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('results_sum').$this->drawOrderArrow('a.results_sum'),'#',$this->createOrderLink('bossAudit-list','a.results_sum'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('status_type').$this->drawOrderArrow('a.status_type'),'#',$this->createOrderLink('bossAudit-list','a.status_type'))
			;
		?>
	</th>
</tr>
