<tr class='clickable-row' data-href='<?php echo $this->getLink('ZC21', 'bankAbbrSet/edit', 'bankAbbrSet/view', array('index'=>$this->record['id']));?>'>
	<td><?php echo $this->drawEditButton('ZC21', 'bankAbbrSet/edit', 'bankAbbrSet/view', array('index'=>$this->record['id'])); ?></td>
	<td><?php echo $this->record['name']; ?></td>
	<td><?php echo $this->record['display']; ?></td>
	<td><?php echo $this->record['z_index']; ?></td>
</tr>
