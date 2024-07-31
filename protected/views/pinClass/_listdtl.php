<tr class='clickable-row' data-href='<?php echo $this->getLink('PI03', 'pinClass/edit', 'pinClass/view', array('index'=>$this->record['id']));?>'>


	<td><?php echo $this->drawEditButton('PI03', 'pinClass/edit', 'pinClass/view', array('index'=>$this->record['id'])); ?></td>



    <td><?php echo $this->record['name']; ?></td>
    <td><?php echo $this->record['z_index']; ?></td>
</tr>
