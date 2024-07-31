<tr>
	<th></th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('code').$this->drawOrderArrow('a.code'),'#',$this->createOrderLink('departure-list','a.code'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('table_type').$this->drawOrderArrow('a.table_type'),'#',$this->createOrderLink('departure-list','a.table_type'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('name').$this->drawOrderArrow('a.name'),'#',$this->createOrderLink('departure-list','a.name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('city').$this->drawOrderArrow('a.city'),'#',$this->createOrderLink('departure-list','a.city'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('office_name').$this->drawOrderArrow('f.name'),'#',$this->createOrderLink('departure-list','f.name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('phone').$this->drawOrderArrow('a.phone'),'#',$this->createOrderLink('departure-list','a.phone'))
			;
		?>
	</th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('department').$this->drawOrderArrow('g.name'),'#',$this->createOrderLink('departure-list','g.name'))
        ;
        ?>
    </th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('position').$this->drawOrderArrow('a.position'),'#',$this->createOrderLink('departure-list','a.position'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('entry_time').$this->drawOrderArrow('a.entry_time'),'#',$this->createOrderLink('departure-list','a.entry_time'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('company_id').$this->drawOrderArrow('a.company_id'),'#',$this->createOrderLink('departure-list','a.company_id'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('leave_time').$this->drawOrderArrow('a.leave_time'),'#',$this->createOrderLink('departure-list','a.leave_time'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('status').$this->drawOrderArrow('a.test_end_time'),'#',$this->createOrderLink('departure-list','a.test_end_time'))
			;
		?>
	</th>
</tr>
