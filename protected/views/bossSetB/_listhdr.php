<tr>
	<th></th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('city').$this->drawOrderArrow('b.name'),'#',$this->createOrderLink('bossSetB-list','b.name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('list_text').$this->drawOrderArrow('a.list_text'),'#',$this->createOrderLink('bossSetB-list','a.list_text'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('num_ratio').$this->drawOrderArrow('a.num_ratio'),'#',$this->createOrderLink('bossSetB-list','a.num_ratio'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('tacitly').$this->drawOrderArrow('a.tacitly'),'#',$this->createOrderLink('bossSetB-list','a.tacitly'))
			;
		?>
	</th>
</tr>
