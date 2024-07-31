<tr class='clickable-row' data-href='<?php echo $this->getLink('RE04', 'reviewSetPro/edit', 'reviewSetPro/view', array('index'=>$this->record['id']));?>'>


	<td><?php echo $this->drawEditButton('RE04', 'reviewSetPro/edit', 'reviewSetPro/view', array('index'=>$this->record['id'])); ?></td>



    <td><?php echo $this->record['id']; ?></td>
    <td><?php echo $this->record['set_name']; ?></td>
    <td><?php echo $this->record['pro_name']; ?></td>
    <td><?php echo $this->record['z_index']; ?></td>
</tr>
