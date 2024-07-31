<tr class='clickable-row' data-href='<?php echo $this->getLink('PI04', 'pinName/edit', 'pinName/view', array('index'=>$this->record['id']));?>'>


	<td><?php echo $this->drawEditButton('PI04', 'pinName/edit', 'pinName/view', array('index'=>$this->record['id'])); ?></td>



    <td><?php echo $this->record['name']; ?></td>
    <td><?php echo $this->record['class_id']; ?></td>
    <td><?php echo $this->record['pin_type']; ?></td>
    <td><?php echo $this->record['z_index']; ?></td>
</tr>
