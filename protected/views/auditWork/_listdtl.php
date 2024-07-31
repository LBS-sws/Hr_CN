<tr class='clickable-row<?php echo $this->record['style']; ?>' data-href='<?php echo $this->getLink($this->model->getAcc(), 'auditWork/edit', 'auditWork/view', array('index'=>$this->record['id'],'only'=>$this->model->only));?>'>


	<td><?php echo $this->drawEditButton($this->model->getAcc(), 'auditWork/edit', 'auditWork/view', array('index'=>$this->record['id'],'only'=>$this->model->only)); ?></td>



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
