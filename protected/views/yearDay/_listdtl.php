<tr class='clickable-row' data-href='<?php echo $this->getLink('ZC07', 'YearDay/edit', 'YearDay/view', array('index'=>$this->record['id']));?>'>


	<td><?php echo $this->drawEditButton('ZC07', 'YearDay/edit', 'YearDay/view', array('index'=>$this->record['id'])); ?></td>



    <td><?php echo $this->record['employee_code']; ?></td>
    <td><?php echo $this->record['employee_name']; ?></td>
    <td><?php echo $this->record['year']; ?></td>
    <td><?php echo $this->record['add_num']; ?></td>
</tr>
