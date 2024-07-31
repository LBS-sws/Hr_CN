<tr>
	<th></th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('name').$this->drawOrderArrow('name'),'#',$this->createOrderLink('company-list','name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('city').$this->drawOrderArrow('city'),'#',$this->createOrderLink('company-list','city'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('head').$this->drawOrderArrow('head'),'#',$this->createOrderLink('company-list','head'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('agent').$this->drawOrderArrow('agent'),'#',$this->createOrderLink('company-list','agent'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('tacitly').$this->drawOrderArrow('tacitly'),'#',$this->createOrderLink('company-list','tacitly'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('share_bool').$this->drawOrderArrow('share_bool'),'#',$this->createOrderLink('company-list','share_bool'))
			;
		?>
	</th>
</tr>
