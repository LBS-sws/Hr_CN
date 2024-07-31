<tr>
	<th></th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('code').$this->drawOrderArrow('a.code'),'#',$this->createOrderLink('audit-list','a.code'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('table_type').$this->drawOrderArrow('a.table_type'),'#',$this->createOrderLink('audit-list','a.table_type'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('name').$this->drawOrderArrow('a.name'),'#',$this->createOrderLink('audit-list','a.name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('city').$this->drawOrderArrow('a.city'),'#',$this->createOrderLink('audit-list','a.city'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('phone').$this->drawOrderArrow('a.phone'),'#',$this->createOrderLink('audit-list','a.phone'))
			;
		?>
	</th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('department').$this->drawOrderArrow('g.name'),'#',$this->createOrderLink('audit-list','g.name'))
        ;
        ?>
    </th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('position').$this->drawOrderArrow('a.position'),'#',$this->createOrderLink('audit-list','a.position'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('entry_time').$this->drawOrderArrow('a.entry_time'),'#',$this->createOrderLink('audit-list','a.entry_time'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('company_id').$this->drawOrderArrow('a.company_id'),'#',$this->createOrderLink('audit-list','a.company_id'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('staff_status').$this->drawOrderArrow('a.staff_status'),'#',$this->createOrderLink('audit-list','a.staff_status'))
			;
		?>
	</th>
</tr>
