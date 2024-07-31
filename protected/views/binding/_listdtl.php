<tr class='clickable-row' data-href='<?php echo $this->getLink('ZC05', 'binding/edit', 'binding/view', array('index'=>$this->record['id']));?>'>


    <td><?php echo $this->drawEditButton('ZC05', 'binding/edit', 'binding/view', array('index'=>$this->record['id'])); ?></td>



    <td><?php echo $this->record['employee_name']; ?></td>
    <td><?php echo $this->record['employee_city']; ?></td>
    <td><?php echo $this->record['user_name']; ?></td>
    <td><?php echo $this->record['user_city']; ?></td>
</tr>
