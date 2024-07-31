<tr>
	<th></th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('employee_code').$this->drawOrderArrow('b.code'),'#',$this->createOrderLink('plusCity-list','b.code'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('employee_name').$this->drawOrderArrow('b.name'),'#',$this->createOrderLink('plusCity-list','b.name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('original_city').$this->drawOrderArrow('b.city'),'#',$this->createOrderLink('plusCity-list','b.city'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('city').$this->drawOrderArrow('a.city'),'#',$this->createOrderLink('plusCity-list','a.city'))
			;
		?>
	</th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('plus_department').$this->drawOrderArrow('d.name'),'#',$this->createOrderLink('plusCity-list','d.name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('plus_position').$this->drawOrderArrow('e.name'),'#',$this->createOrderLink('plusCity-list','e.name'))
        ;
        ?>
    </th>
</tr>
