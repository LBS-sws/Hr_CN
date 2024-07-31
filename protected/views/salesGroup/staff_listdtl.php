
<tr class='clickable-row' data-href='<?php echo $this->getLink('SR01', 'salesGroup/staffEdit', 'salesGroup/staffView', array('index'=>$this->record['id']));?>'>


    <td><?php echo $this->drawEditButton('SR01', 'salesGroup/staffEdit', 'salesGroup/staffView', array('index'=>$this->record['id'])); ?></td>
    <td><?php echo $this->record['code']; ?></td>
    <td><?php echo $this->record['name']; ?></td>
    <td><?php echo $this->record['department_name']; ?></td>
    <td><?php echo $this->record['position_name']; ?></td>
    <td><?php echo $this->record['start_time']; ?></td>
    <td><?php echo $this->record['end_time']; ?></td>
</tr>
