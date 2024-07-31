
<tr class='clickable-row <?php echo $this->record['status']["style"]?>' data-href='<?php echo $this->getLink($this->record['acc'], 'auditHoliday/edit', 'auditHoliday/view',
    array('index'=>$this->record['id'],'type'=>$this->model->type));?>'>


    <td><?php echo $this->drawEditButton($this->record['acc'], 'auditHoliday/edit', 'auditHoliday/view', array('index'=>$this->record['id'],'type'=>$this->model->type)); ?></td>



    <td><?php echo $this->record['employee_name']; ?></td>
    <td><?php echo $this->record['city']; ?></td>
    <td><?php echo $this->record['holiday_name']; ?></td>
    <td><?php echo $this->record['start_time']; ?></td>
    <td><?php echo $this->record['end_time']; ?></td>
    <td><?php echo $this->record['status']["status"]; ?></td>
</tr>
