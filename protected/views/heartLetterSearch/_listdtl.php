<tr class='clickable-row' data-href='<?php echo $this->getLink('HL03', 'heartLetterSearch/edit', 'heartLetterSearch/view', array('index'=>$this->record['id']));?>'>


	<td><?php echo $this->drawEditButton('HL03', 'heartLetterSearch/edit', 'heartLetterSearch/view', array('index'=>$this->record['id'])); ?></td>



    <td><?php echo $this->record['employee_code']; ?></td>
    <td><?php echo $this->record['employee_name']; ?></td>
    <td><?php echo $this->record['city']; ?></td>
    <td><?php echo $this->record['user_num']; ?></td>
</tr>
