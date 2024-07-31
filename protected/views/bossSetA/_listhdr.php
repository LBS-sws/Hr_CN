<tr>
	<th></th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('city').$this->drawOrderArrow('b.name'),'#',$this->createOrderLink('bossSetA-list','b.name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('list_text').$this->drawOrderArrow('a.list_text'),'#',$this->createOrderLink('bossSetA-list','a.list_text'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('num_ratio').$this->drawOrderArrow('a.num_ratio'),'#',$this->createOrderLink('bossSetA-list','a.num_ratio'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('tacitly').$this->drawOrderArrow('a.tacitly'),'#',$this->createOrderLink('bossSetA-list','a.tacitly'))
			;
		?>
	</th>
</tr>
