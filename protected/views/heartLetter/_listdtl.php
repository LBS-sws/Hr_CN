<tr class='clickable-row<?php echo $this->record['style']; ?>' data-href='<?php echo $this->getLink('HL01', 'heartLetter/edit', 'heartLetter/view', array('index'=>$this->record['id']));?>'>


	<td><?php echo $this->drawEditButton('HL01', 'heartLetter/edit', 'heartLetter/view', array('index'=>$this->record['id'])); ?></td>



    <td><?php echo $this->record['employee_code']; ?></td>
    <td><?php echo $this->record['employee_name']; ?></td>
    <td><?php echo $this->record['letter_title']; ?></td>
    <td><?php echo $this->record['letter_type']; ?></td>
    <td><?php echo $this->record['lcd']; ?></td>
    <td><?php echo $this->record['state']; ?></td>
</tr>
