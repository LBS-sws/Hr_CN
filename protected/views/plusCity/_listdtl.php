<tr class='clickable-row' data-href='<?php echo $this->getLink('ZC13', 'plusCity/edit', 'plusCity/view', array('index'=>$this->record['id']));?>'>


    <td><?php echo $this->drawEditButton('ZC13', 'plusCity/edit', 'plusCity/view', array('index'=>$this->record['id'])); ?></td>



    <td><?php echo $this->record['employee_code']; ?></td>
    <td><?php echo $this->record['employee_name']; ?></td>
    <td><?php echo $this->record['original_city']; ?></td>
    <td><?php echo $this->record['city']; ?></td>
    <td><?php echo $this->record['plus_department']; ?></td>
    <td><?php echo $this->record['plus_position']; ?></td>
</tr>
