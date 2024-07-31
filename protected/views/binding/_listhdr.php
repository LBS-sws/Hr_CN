<tr>
	<th></th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('employee_name').$this->drawOrderArrow('b.name'),'#',$this->createOrderLink('binding-list','b.name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('employee_city').$this->drawOrderArrow('b.city'),'#',$this->createOrderLink('binding-list','b.city'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('user_name').$this->drawOrderArrow('d.disp_name'),'#',$this->createOrderLink('binding-list','d.disp_name'))
			;
		?>
	</th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('user_city').$this->drawOrderArrow('d.city'),'#',$this->createOrderLink('binding-list','d.city'))
        ;
        ?>
    </th>
</tr>
