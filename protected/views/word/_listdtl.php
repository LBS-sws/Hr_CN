<tr class='clickable-row' data-href='<?php echo $this->getLink('ZD01', 'word/edit', 'word/view', array('index'=>$this->record['id']));?>'>


	<td><?php echo $this->drawEditButton('ZD01', 'word/edit','word/view', array('index'=>$this->record['id'])); ?></td>



    <td><?php echo $this->record['name']; ?></td>
   <td><?php echo $this->record['city']; ?></td>
	<td><?php echo $this->record['type']; ?></td>
</tr>
