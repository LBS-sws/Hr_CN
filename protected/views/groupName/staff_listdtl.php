<tr class='clickable-row' data-href='<?php echo $this->getLink('ZC24', 'groupName/staffEdit', 'groupName/staffView', array('index'=>$this->record['id']));?>'>
	<td><?php echo $this->drawEditButton('ZC24', 'groupName/staffEdit', 'groupName/staffView', array('index'=>$this->record['id'])); ?></td>
	<td><?php echo $this->record['employee_code']; ?></td>
	<td><?php echo $this->record['employee_name']; ?></td>
	<td><?php echo $this->record['branch_text']; ?></td>
</tr>
