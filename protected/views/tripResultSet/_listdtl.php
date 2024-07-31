<tr class='clickable-row' data-href='<?php echo $this->getLink('ZC17', 'tripResultSet/edit', 'tripResultSet/view', array('index'=>$this->record['id']));?>'>
	<td><?php echo $this->drawEditButton('ZC17', 'tripResultSet/edit', 'tripResultSet/view', array('index'=>$this->record['id'])); ?></td>
	<td><?php echo $this->record['pro_name']; ?></td>
	<td><?php echo $this->record['pro_num']; ?></td>
	<td><?php echo $this->record['z_display']; ?></td>
	<td><?php echo $this->record['z_index']; ?></td>
</tr>
