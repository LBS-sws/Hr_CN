<tr>
	<th></th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('kpi_name').$this->drawOrderArrow('a.kpi_name'),'#',$this->createOrderLink('bossKPI-list','a.kpi_name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('city').$this->drawOrderArrow('b.name'),'#',$this->createOrderLink('bossKPI-list','b.name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('tacitly').$this->drawOrderArrow('a.tacitly'),'#',$this->createOrderLink('bossKPI-list','a.tacitly'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('sum_bool').$this->drawOrderArrow('a.sum_bool'),'#',$this->createOrderLink('bossKPI-list','a.sum_bool'))
			;
		?>
	</th>
</tr>
