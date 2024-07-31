<tr>
	<th></th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('code').$this->drawOrderArrow('a.code'),'#',$this->createOrderLink('employee-list','a.code'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('name').$this->drawOrderArrow('a.name'),'#',$this->createOrderLink('employee-list','a.name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('city').$this->drawOrderArrow('a.city'),'#',$this->createOrderLink('employee-list','a.city'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('office_name').$this->drawOrderArrow('f.name'),'#',$this->createOrderLink('employee-list','f.name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('phone').$this->drawOrderArrow('a.phone'),'#',$this->createOrderLink('employee-list','a.phone'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('department').$this->drawOrderArrow('g.name'),'#',$this->createOrderLink('employee-list','g.name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('position').$this->drawOrderArrow('a.position'),'#',$this->createOrderLink('employee-list','a.position'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('entry_time').$this->drawOrderArrow('a.entry_time'),'#',$this->createOrderLink('employee-list','a.entry_time'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('year_day').$this->drawOrderArrow('a.year_day'),'#',$this->createOrderLink('employee-list','a.year_day'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('remain_year_day').$this->drawOrderArrow('a.year_day'),'#',$this->createOrderLink('employee-list','a.year_day'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('company_id').$this->drawOrderArrow('a.company_id'),'#',$this->createOrderLink('employee-list','a.company_id'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('status').$this->drawOrderArrow('a.z_index'),'#',$this->createOrderLink('employee-list','a.z_index'))
			;
		?>
	</th>
    <th width="1%">
    </th>
</tr>
