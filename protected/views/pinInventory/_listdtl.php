<tr class='clickable-row <?php echo $this->record['style'];?>' data-href='<?php echo $this->getLink('PI02', 'pinInventory/edit', 'pinInventory/view', array('index'=>$this->record['id']));?>'>


	<td><?php echo $this->drawEditButton('PI02', 'pinInventory/edit', 'pinInventory/view', array('index'=>$this->record['id'])); ?></td>

    <td><?php echo $this->record['pin_name_id']; ?></td>
    <td><?php echo $this->record['class_id']; ?></td>
    <td><?php echo $this->record['city']; ?></td>
    <td><?php echo $this->record['inventory']; ?></td>
    <td>
        <?php echo $this->record['residue_num']; ?>
    </td>
    <td><?php echo $this->record['z_index']; ?></td>
</tr>
