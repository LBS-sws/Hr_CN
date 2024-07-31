<tr>
	<th></th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('pin_name_id').$this->drawOrderArrow('b.name'),'#',$this->createOrderLink('pinInventory-list','b.name'))
        ;
        ?>
    </th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('class_id').$this->drawOrderArrow('f.name'),'#',$this->createOrderLink('pinInventory-list','f.name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('city').$this->drawOrderArrow('g.name'),'#',$this->createOrderLink('pinInventory-list','g.name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('inventory').$this->drawOrderArrow('a.inventory'),'#',$this->createOrderLink('pinInventory-list','a.inventory'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('residue_num').$this->drawOrderArrow('a.residue_num'),'#',$this->createOrderLink('pinInventory-list','a.residue_num'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('z_index').$this->drawOrderArrow('a.z_index'),'#',$this->createOrderLink('pinInventory-list','a.z_index'))
			;
		?>
	</th>
</tr>
