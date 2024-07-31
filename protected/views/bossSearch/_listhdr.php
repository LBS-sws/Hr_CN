<tr>
	<th></th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('code').$this->drawOrderArrow('b.code'),'#',$this->createOrderLink('bossSearch-list','b.code'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('name').$this->drawOrderArrow('b.name'),'#',$this->createOrderLink('bossSearch-list','a.name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('city_name').$this->drawOrderArrow('d.name'),'#',$this->createOrderLink('bossSearch-list','d.name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('audit_year').$this->drawOrderArrow('a.audit_year'),'#',$this->createOrderLink('bossSearch-list','a.audit_year'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('results_a').$this->drawOrderArrow('a.results_a'),'#',$this->createOrderLink('bossSearch-list','a.results_a'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('results_b').$this->drawOrderArrow('a.results_b'),'#',$this->createOrderLink('bossSearch-list','a.results_b'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('results_c').$this->drawOrderArrow('a.results_c'),'#',$this->createOrderLink('bossSearch-list','a.results_c'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('results_sum').$this->drawOrderArrow('a.results_sum'),'#',$this->createOrderLink('bossSearch-list','a.results_sum'))
			;
		?>
	</th>
</tr>
