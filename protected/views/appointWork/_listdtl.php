<tr class='clickable-row<?php echo $this->record['style']; ?>' data-href='<?php echo $this->getLink("ZG12", 'appointWork/edit', 'appointWork/view', array('index'=>$this->record['id']));?>'>


	<td><?php echo $this->drawEditButton("ZG12", 'appointWork/edit', 'appointWork/view', array('index'=>$this->record['id'])); ?></td>



    <td><?php echo $this->record['work_code']; ?></td>
    <td><?php echo $this->record['lcd']; ?></td>
    <td><?php echo $this->record['employee_name']; ?></td>
    <td><?php echo $this->record['city']; ?></td>
    <td><?php echo $this->record['work_type']; ?></td>
    <td><?php echo $this->record['start_time']; ?></td>
    <td><?php echo $this->record['end_time']; ?></td>
    <td><?php echo $this->record['log_time']; ?></td>
    <td><?php echo $this->record['status']; ?></td>
</tr>
